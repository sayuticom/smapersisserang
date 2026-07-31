<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\FinanceController;
use App\Models\DonationOutflow;
use App\Models\FinanceIncome;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\DonationOutflowApprovalService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DonationOutflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        foreach ([
            'donation_outflow_status_histories',
            'finance_incomes',
            'finance_expenses',
            'donation_outflows',
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

    public function test_user_with_view_permission_can_see_index(): void
    {
        $user = $this->userWithPermissions(['donation.outflows.view']);

        $this->actingAs($user)
            ->get(route('admin.donation-outflows.index'))
            ->assertOk()
            ->assertSee('Penyerahan Dana Donasi ke Keuangan');
    }

    public function test_user_without_view_permission_is_denied(): void
    {
        $user = $this->userWithPermissions([]);

        $this->actingAs($user)
            ->get(route('admin.donation-outflows.index'))
            ->assertForbidden();
    }

    public function test_transaction_is_created_pending_with_initial_history(): void
    {
        $user = $this->userWithPermissions(['donation.outflows.create']);

        $response = $this->actingAs($user)->post(route('admin.donation-outflows.store'), [
            'handover_date' => '2026-07-31',
            'donation_source' => 'Donasi Pendidikan',
            'description' => 'Periode Juli 2026',
            'amount' => 1500000,
            'handover_method' => 'transfer',
            'destination_account' => 'BSI Operasional',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
            'notes' => 'Diserahkan penuh',
        ]);

        $outflow = DonationOutflow::firstOrFail();

        $response->assertRedirect(route('admin.donation-outflows.show', $outflow));
        $this->assertMatchesRegularExpression('/^DK-20260731-[A-Z0-9]{4}$/', $outflow->transaction_number);
        $this->assertSame(DonationOutflow::STATUS_PENDING, $outflow->status);
        $this->assertDatabaseHas('donation_outflow_status_histories', [
            'donation_outflow_id' => $outflow->id,
            'from_status' => null,
            'to_status' => 'pending',
            'changed_by' => $user->id,
        ]);
        Storage::disk('public')->assertExists($outflow->proof_file);
    }

    public function test_approval_creates_one_correctly_mapped_finance_income(): void
    {
        $approver = $this->userWithPermissions(['donation.outflows.approve']);
        $outflow = $this->createOutflow(['proof_file' => 'donation/outflow-proofs/bukti.pdf']);

        app(DonationOutflowApprovalService::class)->approve($outflow, $approver);

        $income = FinanceIncome::sole();
        $this->assertSame($outflow->id, $income->donation_outflow_id);
        $this->assertSame('2026-07-31', $income->date->format('Y-m-d'));
        $this->assertSame('Transfer dari Donasi', $income->income_type);
        $this->assertSame('1250000.00', $income->amount);
        $this->assertSame('Transfer Bank', $income->payment_method);
        $this->assertSame('Donasi Pendidikan', $income->source_name);
        $this->assertSame('donation/outflow-proofs/bukti.pdf', $income->proof_file);
        $this->assertSame($approver->id, $income->created_by);
        $this->assertStringContainsString($outflow->transaction_number, $income->description);
        $this->assertStringContainsString('Periode Juli 2026', $income->description);
        $this->assertStringContainsString('BSI Operasional', $income->description);
        $this->assertStringContainsString('Catatan pengujian', $income->description);
        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'approved',
            'approved_by' => $approver->id,
        ]);
        $this->assertDatabaseHas('donation_outflow_status_histories', [
            'donation_outflow_id' => $outflow->id,
            'from_status' => 'pending',
            'to_status' => 'approved',
        ]);
    }

    public function test_second_approval_does_not_create_duplicate_income(): void
    {
        $approver = $this->userWithPermissions(['donation.outflows.approve']);
        $outflow = $this->createOutflow();
        $service = app(DonationOutflowApprovalService::class);
        $service->approve($outflow, $approver);

        try {
            $service->approve($outflow, $approver);
            $this->fail('Approval kedua seharusnya ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('finance_incomes', 1);
        }
    }

    public function test_rejection_requires_and_saves_reason_without_income(): void
    {
        $rejector = $this->userWithPermissions(['donation.outflows.reject']);
        $outflow = $this->createOutflow();

        $this->actingAs($rejector)
            ->post(route('admin.donation-outflows.reject', $outflow), [])
            ->assertSessionHasErrors('rejection_reason');

        $this->actingAs($rejector)
            ->post(route('admin.donation-outflows.reject', $outflow), [
                'rejection_reason' => 'Nominal belum sesuai bukti.',
            ])
            ->assertRedirect(route('admin.donation-outflows.show', $outflow));

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'rejected',
            'rejected_by' => $rejector->id,
            'rejection_reason' => 'Nominal belum sesuai bukti.',
        ]);
        $this->assertDatabaseHas('donation_outflow_status_histories', [
            'donation_outflow_id' => $outflow->id,
            'from_status' => 'pending',
            'to_status' => 'rejected',
            'reason' => 'Nominal belum sesuai bukti.',
        ]);
        $this->assertDatabaseCount('finance_incomes', 0);
    }

    public function test_approved_cannot_be_rejected_and_rejected_cannot_be_approved(): void
    {
        $user = $this->userWithPermissions([
            'donation.outflows.approve',
            'donation.outflows.reject',
        ]);

        $approved = $this->createOutflow();
        app(DonationOutflowApprovalService::class)->approve($approved, $user);

        $this->actingAs($user)
            ->post(route('admin.donation-outflows.reject', $approved), [
                'rejection_reason' => 'Tidak boleh diproses.',
            ])
            ->assertSessionHasErrors('status');

        $rejected = $this->createOutflow(['transaction_number' => 'DK-20260731-RJ01']);
        $this->actingAs($user)->post(route('admin.donation-outflows.reject', $rejected), [
            'rejection_reason' => 'Ditolak.',
        ]);

        try {
            app(DonationOutflowApprovalService::class)->approve($rejected->fresh(), $user);
            $this->fail('Transaksi rejected seharusnya tidak dapat disetujui.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('finance_incomes', 1);
        }
    }

    public function test_integrated_finance_income_cannot_be_edited_or_deleted(): void
    {
        $admin = $this->userWithPermissions([], 'admin');
        $outflow = $this->createOutflow();
        $income = $this->createIncome($outflow);

        $this->actingAs($admin)
            ->get(route('admin.finance.incomes.edit', $income))
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.finance.incomes.update', $income), $this->incomePayload())
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.finance.incomes.destroy', $income))
            ->assertForbidden();

        $this->assertDatabaseHas('finance_incomes', ['id' => $income->id]);
    }

    public function test_dashboard_pending_notification_only_counts_pending_transactions(): void
    {
        $this->createOutflow();
        $this->createOutflow([
            'transaction_number' => 'DK-20260731-AP01',
            'status' => 'approved',
        ]);
        $this->createOutflow([
            'transaction_number' => 'DK-20260731-RJ01',
            'status' => 'rejected',
        ]);

        $view = app(FinanceController::class)->dashboard();

        $this->assertSame(1, $view->getData()['pendingDonationOutflowCount']);
        $this->assertEquals(1250000, $view->getData()['pendingDonationOutflowTotal']);
    }

    public function test_report_only_reads_finance_income_created_from_approval(): void
    {
        $approver = $this->userWithPermissions(['donation.outflows.approve']);
        $pending = $this->createOutflow();
        $this->createOutflow([
            'transaction_number' => 'DK-20260731-RJ01',
            'status' => 'rejected',
        ]);

        app(DonationOutflowApprovalService::class)->approve($pending, $approver);

        $request = Request::create('/admin/finance/laporan', 'GET', [
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-31',
        ]);
        $view = app(FinanceController::class)->report($request);

        $this->assertCount(1, $view->getData()['incomes']);
        $this->assertEquals(1250000, $view->getData()['totalIncome']);
        $this->assertSame('Transfer dari Donasi', $view->getData()['incomes']->sole()->income_type);
    }

    private function userWithPermissions(array $permissionNames, string $roleName = 'guru'): User
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

    private function createOutflow(array $overrides = []): DonationOutflow
    {
        $creator = User::factory()->create(['role' => 'admin']);

        return DonationOutflow::create(array_merge([
            'transaction_number' => 'DK-20260731-PN01',
            'handover_date' => '2026-07-31',
            'donation_source' => 'Donasi Pendidikan',
            'description' => 'Periode Juli 2026',
            'amount' => 1250000,
            'handover_method' => 'transfer',
            'destination_account' => 'BSI Operasional',
            'notes' => 'Catatan pengujian',
            'status' => 'pending',
            'created_by' => $creator->id,
        ], $overrides));
    }

    private function createIncome(DonationOutflow $outflow): FinanceIncome
    {
        $creator = User::factory()->create(['role' => 'admin']);

        return FinanceIncome::create(array_merge($this->incomePayload(), [
            'donation_outflow_id' => $outflow->id,
            'created_by' => $creator->id,
        ]));
    }

    private function incomePayload(): array
    {
        return [
            'date' => '2026-07-31',
            'income_type' => 'Transfer dari Donasi',
            'amount' => 1250000,
            'payment_method' => 'Transfer Bank',
            'source_name' => 'Donasi Pendidikan',
            'description' => 'Pemasukan terintegrasi',
        ];
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
