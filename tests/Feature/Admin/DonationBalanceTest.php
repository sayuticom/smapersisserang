<?php

namespace Tests\Feature\Admin;

use App\Models\DonationOutflow;
use App\Models\DonationTransaction;
use App\Models\FinanceIncome;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\DonationBalanceService;
use App\Services\DonationOutflowApprovalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DonationBalanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }

    protected function tearDown(): void
    {
        foreach ([
            'finance_expenses',
            'finance_incomes',
            'donation_item_commitments',
            'donation_item_receipts',
            'donation_outflow_status_histories',
            'donation_outflows',
            'donation_transactions',
            'permission_role',
            'permissions',
            'role_user',
            'roles',
            'school_settings',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_valid_money_transaction_adds_to_total_incoming(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 500000]);
        $this->createDonationTransaction(['status' => 'settlement', 'amount' => 300000]);
        $this->createDonationTransaction(['status' => 'capture', 'amount' => 200000]);

        $service = app(DonationBalanceService::class);
        $this->assertSame(1000000, $service->totalIncoming());
        $this->assertSame(1000000, $service->recordedBalance());
    }

    public function test_pending_transaction_does_not_add_to_total_incoming(): void
    {
        $this->createDonationTransaction(['status' => 'pending', 'amount' => 500000]);

        $this->assertSame(0, app(DonationBalanceService::class)->totalIncoming());
    }

    public function test_cancelled_and_failed_transactions_do_not_add(): void
    {
        $this->createDonationTransaction(['status' => 'cancelled', 'amount' => 500000]);
        $this->createDonationTransaction(['status' => 'expired', 'amount' => 300000]);
        $this->createDonationTransaction(['status' => 'failure', 'amount' => 200000]);

        $this->assertSame(0, app(DonationBalanceService::class)->totalIncoming());
    }

    public function test_pending_outflow_does_not_reduce_recorded_balance(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $this->createOutflow(['status' => 'pending', 'amount' => 400000]);

        $service = app(DonationBalanceService::class);
        $this->assertSame(1000000, $service->recordedBalance());
    }

    public function test_pending_outflow_reduces_available_balance(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $this->createOutflow(['status' => 'pending', 'amount' => 400000]);

        $service = app(DonationBalanceService::class);
        $this->assertSame(1000000, $service->recordedBalance());
        $this->assertSame(600000, $service->availableBalance());
    }

    public function test_approved_outflow_reduces_recorded_balance(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $this->createOutflow(['status' => 'approved', 'amount' => 400000]);

        $service = app(DonationBalanceService::class);
        $this->assertSame(600000, $service->recordedBalance());
        $this->assertSame(600000, $service->availableBalance());
    }

    public function test_rejected_outflow_does_not_reduce_balance(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $this->createOutflow(['status' => 'rejected', 'amount' => 400000]);

        $service = app(DonationBalanceService::class);
        $this->assertSame(1000000, $service->recordedBalance());
        $this->assertSame(1000000, $service->availableBalance());
    }

    public function test_item_donation_is_not_counted(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $creator = User::factory()->create(['role' => 'admin']);
        DB::table('donation_item_receipts')->insert([
            'receipt_number' => 'BK-0001',
            'received_date' => '2026-07-31',
            'donor_name' => 'Pak Barang',
            'item_type' => 'Peralatan',
            'status' => 'received',
            'user_id' => $creator->id,
        ]);
        DB::table('donation_item_commitments')->insert([
            'reference_number' => 'CM-0001',
            'received_at' => now(),
            'donor_name' => 'Pak Komitmen',
            'item_type' => 'Sembako',
            'status' => 'confirmed',
            'created_by' => $creator->id,
        ]);

        $service = app(DonationBalanceService::class);
        $this->assertSame(1000000, $service->totalIncoming());
        $this->assertSame(1000000, $service->recordedBalance());
    }

    public function test_user_cannot_create_outflow_exceeding_available_balance(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $this->createOutflow(['status' => 'pending', 'amount' => 400000]);
        $user = $this->userWithPermissions(['donation.outflows.create']);

        $this->actingAs($user)
            ->post(route('admin.donation-outflows.store'), $this->outflowPayload(['amount' => 700000]))
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('donation_outflows', 1);
    }

    public function test_rejection_returns_amount_to_available_balance(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $outflow = $this->createOutflow(['status' => 'pending', 'amount' => 400000]);

        $service = app(DonationBalanceService::class);
        $this->assertSame(600000, $service->availableBalance());

        $outflow->update([
            'status' => DonationOutflow::STATUS_REJECTED,
            'rejected_by' => User::factory()->create(['role' => 'admin'])->id,
            'rejected_at' => now(),
        ]);

        $this->assertSame(1000000, $service->availableBalance());
    }

    public function test_approval_cannot_make_balance_negative(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $approver = $this->userWithPermissions(['donation.outflows.approve']);
        $first = $this->createOutflow(['status' => 'pending', 'amount' => 1000000]);

        app(DonationOutflowApprovalService::class)->approve($first, $approver);

        $this->assertSame(0, app(DonationBalanceService::class)->recordedBalance());

        $second = $this->createOutflow(['status' => 'pending', 'amount' => 1000000]);
        try {
            app(DonationOutflowApprovalService::class)->approve($second, $approver);
            $this->fail('Approval seharusnya gagal karena saldo tidak mencukupi.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('status', $e->errors());
            $this->assertDatabaseCount('finance_incomes', 1);
        }
    }

    public function test_concurrent_requests_cannot_use_same_available_balance(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $user = $this->userWithPermissions(['donation.outflows.create']);

        $this->actingAs($user)
            ->post(route('admin.donation-outflows.store'), $this->outflowPayload(['amount' => 1000000]))
            ->assertRedirect();

        $this->actingAs($user)
            ->post(route('admin.donation-outflows.store'), $this->outflowPayload(['amount' => 1000000]))
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('donation_outflows', 1);
    }

    public function test_summary_reports_correct_values(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 20000000]);
        $this->createDonationTransaction(['status' => 'pending', 'amount' => 5000000]);
        $this->createOutflow(['status' => 'approved', 'amount' => 7500000]);
        $this->createOutflow(['status' => 'pending', 'amount' => 1250000]);
        $this->createOutflow(['status' => 'rejected', 'amount' => 999000]);

        $summary = app(DonationBalanceService::class)->summary();

        $this->assertSame(20000000, $summary['total_incoming']);
        $this->assertSame(7500000, $summary['total_approved_outflow']);
        $this->assertSame(12500000, $summary['recorded_balance']);
        $this->assertSame(11250000, $summary['available_balance']);
        $this->assertSame(1, $summary['pending_count']);
    }

    public function test_finance_income_from_approval_is_not_recounted_as_incoming(): void
    {
        $this->createDonationTransaction(['status' => 'paid', 'amount' => 1000000]);
        $approver = $this->userWithPermissions(['donation.outflows.approve']);
        $outflow = $this->createOutflow(['status' => 'pending', 'amount' => 400000]);

        app(DonationOutflowApprovalService::class)->approve($outflow, $approver);

        $this->assertDatabaseCount('finance_incomes', 1);
        $service = app(DonationBalanceService::class);
        $this->assertSame(1000000, $service->totalIncoming());
        $this->assertSame(600000, $service->recordedBalance());
    }

    public function test_user_without_balance_permission_cannot_access_dashboard(): void
    {
        $viewOnly = $this->userWithPermissions(['donation.outflows.view']);

        $this->actingAs($viewOnly)
            ->get(route('admin.donation.dashboard'))
            ->assertForbidden();
    }

    public function test_user_with_balance_permission_can_see_dashboard(): void
    {
        $user = $this->userWithPermissions([
            'donation.outflows.view',
            'donation.balance.view',
        ]);

        $this->actingAs($user)
            ->get(route('admin.donation.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Donasi')
            ->assertSee('Saldo Donasi');
    }

    public function test_dashboard_lists_pending_outflows(): void
    {
        $user = $this->userWithPermissions(['donation.balance.view']);
        $this->createOutflow(['status' => 'pending', 'amount' => 500000]);
        $pending = $this->createOutflow(['status' => 'pending', 'amount' => 750000]);
        $this->createOutflow(['status' => 'approved', 'amount' => 200000]);

        $this->actingAs($user)
            ->get(route('admin.donation.dashboard'))
            ->assertOk()
            ->assertSee($pending->transaction_number)
            ->assertSee('Donasi Keluar Menunggu Verifikasi');
    }

    public function test_dashboard_lists_latest_valid_income(): void
    {
        $user = $this->userWithPermissions(['donation.balance.view']);
        $paid = $this->createDonationTransaction(['status' => 'paid', 'amount' => 500000, 'donor_name' => 'Donatur Masuk']);
        $this->createDonationTransaction(['status' => 'pending', 'amount' => 300000, 'donor_name' => 'Donatur Pending']);

        $this->actingAs($user)
            ->get(route('admin.donation.dashboard'))
            ->assertOk()
            ->assertSee('Donasi Masuk Terbaru')
            ->assertSee('Donatur Masuk')
            ->assertDontSee('Donatur Pending');
    }

    public function test_dashboard_limits_lists_to_five_rows(): void
    {
        $user = $this->userWithPermissions(['donation.balance.view']);

        $transactions = [];
        foreach (range(1, 7) as $i) {
            $tx = $this->createDonationTransaction([
                'status' => 'paid',
                'amount' => 100000,
                'donor_name' => 'Donatur ke-' . $i,
            ]);
            $tx->forceFill(['created_at' => now()->addMinutes($i)])->save();
            $transactions[] = $tx;
        }

        $response = $this->actingAs($user)
            ->get(route('admin.donation.dashboard'))
            ->assertOk();

        foreach ($transactions as $index => $transaction) {
            $included = $index >= 2;
            $this->assertSame($included, str_contains($response->getContent(), $transaction->order_id));
        }
    }

    // ========================================================================
    // HELPERS
    // ========================================================================

    private function createDonationTransaction(array $overrides = []): DonationTransaction
    {
        return DonationTransaction::create(array_merge([
            'order_id' => 'DON-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
            'donor_name' => 'Donatur Uji',
            'donor_whatsapp' => '-',
            'support_type' => 'Donasi Pendidikan & Makan Santri',
            'amount' => 100000,
            'payment_gateway' => 'midtrans',
            'status' => 'paid',
            'paid_at' => now(),
        ], $overrides));
    }

    private function createOutflow(array $overrides = []): DonationOutflow
    {
        $creator = User::factory()->create(['role' => 'admin']);

        return DonationOutflow::create(array_merge([
            'transaction_number' => 'DK-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
            'handover_date' => '2026-07-31',
            'donation_source' => 'Donasi Pendidikan',
            'description' => 'Periode Juli 2026',
            'amount' => 1250000,
            'handover_method' => 'transfer',
            'destination_account' => 'BSI Operasional',
            'status' => 'pending',
            'created_by' => $creator->id,
        ], $overrides));
    }

    private function outflowPayload(array $overrides = []): array
    {
        return array_merge([
            'handover_date' => '2026-07-31',
            'donation_source' => 'Donasi Pendidikan',
            'description' => 'Periode Juli 2026',
            'amount' => 1250000,
            'handover_method' => 'transfer',
            'destination_account' => 'BSI Operasional',
        ], $overrides);
    }

    private function userWithPermissions(array $permissionNames, string $roleName = 'staf_keuangan'): User
    {
        $role = Role::firstOrCreate([
            'name' => $roleName,
        ], [
            'display_name' => ucfirst($roleName),
            'guard_name' => 'web',
            'is_active' => true,
        ]);
        $user = User::factory()->create(['role' => $roleName]);
        $user->roles()->attach($role);

        foreach ([
            'donation.outflows.view',
            'donation.outflows.create',
            'donation.outflows.approve',
            'donation.outflows.reject',
            'donation.balance.view',
        ] as $name) {
            $permission = Permission::firstOrCreate(
                ['name' => $name],
                [
                    'module' => 'donation',
                    'action' => str($name)->afterLast('.')->value(),
                    'display_name' => $name,
                    'group_name' => 'DONASI',
                    'is_active' => true,
                ]
            );

            if (in_array($name, $permissionNames, true)) {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }

        return $user;
    }

    private function createTables(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('role', 50)->nullable();
            $table->timestamps();
        });
        Schema::create('school_settings', function ($table) {
            $table->id();
            $table->string('school_name')->nullable();
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('roles', function ($table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('guard_name')->default('web');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('role_user', function ($table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });
        Schema::create('permissions', function ($table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('module');
            $table->string('action');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('group_name');
            $table->string('guard_name')->default('web');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('permission_role', function ($table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['permission_id', 'role_id']);
        });
        Schema::create('donation_transactions', function ($table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('donor_name');
            $table->string('donor_whatsapp');
            $table->string('support_type');
            $table->unsignedBigInteger('amount');
            $table->text('note')->nullable();
            $table->string('payment_gateway')->default('midtrans');
            $table->text('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->string('midtrans_fraud_status')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_notification')->nullable();
            $table->timestamps();
        });
        Schema::create('donation_outflows', function ($table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('handover_date');
            $table->string('donation_source');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('handover_method');
            $table->string('destination_account');
            $table->string('proof_file')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
        Schema::create('donation_outflow_status_histories', function ($table) {
            $table->id();
            $table->foreignId('donation_outflow_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('donation_item_receipts', function ($table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->date('received_date');
            $table->string('donor_name')->nullable();
            $table->string('item_type');
            $table->string('status')->default('received');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('donation_item_commitments', function ($table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->dateTime('received_at');
            $table->string('donor_name')->nullable();
            $table->string('item_type');
            $table->string('status')->default('pending');
            $table->foreignId('received_receipt_id')->nullable()->constrained('donation_item_receipts')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('finance_incomes', function ($table) {
            $table->id();
            $table->foreignId('donation_outflow_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->date('date');
            $table->string('income_type');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->string('source_name')->nullable();
            $table->text('description')->nullable();
            $table->string('proof_file')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
        Schema::create('finance_expenses', function ($table) {
            $table->id();
            $table->date('date');
            $table->string('expense_category');
            $table->decimal('amount', 15, 2);
            $table->string('paid_to')->nullable();
            $table->string('payment_method');
            $table->text('description')->nullable();
            $table->string('proof_file')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
}
