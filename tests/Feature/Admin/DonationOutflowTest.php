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
use Illuminate\Support\Facades\DB;
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
        $rejector = $this->userWithRoleOutflowDefaults('staf_keuangan');
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
        $staf = $this->userWithRoleOutflowDefaults('staf_keuangan');

        $approved = $this->createOutflow();
        app(DonationOutflowApprovalService::class)->approve($approved, $user);

        $this->actingAs($staf)
            ->post(route('admin.donation-outflows.reject', $approved), [
                'rejection_reason' => 'Tidak boleh diproses.',
            ])
            ->assertSessionHasErrors('status');

        $rejected = $this->createOutflow(['transaction_number' => 'DK-20260731-RJ01']);
        $this->actingAs($staf)->post(route('admin.donation-outflows.reject', $rejected), [
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

    public function test_staf_keuangan_can_open_index_and_detail(): void
    {
        $staf = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $outflow = $this->createOutflow();

        $this->actingAs($staf)
            ->get(route('admin.donation-outflows.index'))
            ->assertOk()
            ->assertSee('Penyerahan Dana Donasi ke Keuangan');

        $this->actingAs($staf)
            ->get(route('admin.donation-outflows.show', $outflow))
            ->assertOk()
            ->assertSee($outflow->transaction_number);
    }

    public function test_staf_keuangan_can_approve(): void
    {
        $staf = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $outflow = $this->createOutflow();

        $this->actingAs($staf)
            ->post(route('admin.donation-outflows.approve', $outflow))
            ->assertRedirect(route('admin.donation-outflows.show', $outflow));

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'approved',
            'approved_by' => $staf->id,
        ]);
        $this->assertDatabaseCount('finance_incomes', 1);
    }

    public function test_staf_keuangan_can_reject(): void
    {
        $staf = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $outflow = $this->createOutflow();

        $this->actingAs($staf)
            ->post(route('admin.donation-outflows.reject', $outflow), [
                'rejection_reason' => 'Berkas belum lengkap.',
            ])
            ->assertRedirect(route('admin.donation-outflows.show', $outflow));

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'rejected',
            'rejected_by' => $staf->id,
        ]);
        $this->assertDatabaseCount('finance_incomes', 0);
    }

    public function test_staf_keuangan_cannot_create(): void
    {
        $staf = $this->userWithRoleOutflowDefaults('staf_keuangan');

        $this->actingAs($staf)
            ->get(route('admin.donation-outflows.create'))
            ->assertForbidden();

        $this->actingAs($staf)
            ->post(route('admin.donation-outflows.store'), [
                'handover_date' => '2026-07-31',
                'donation_source' => 'Donasi Pendidikan',
                'amount' => 1000,
                'handover_method' => 'cash',
                'destination_account' => 'Kas Sekolah',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('donation_outflows', 0);
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = $this->userWithPermissions([]);

        $this->actingAs($user)
            ->get(route('admin.donation-outflows.index'))
            ->assertForbidden();
    }

    public function test_dashboard_pending_card_links_to_outflow_index(): void
    {
        $staf = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $this->createOutflow();

        $this->actingAs($staf)
            ->get(route('admin.finance.dashboard'))
            ->assertOk()
            ->assertSee('Dana Donasi Menunggu Verifikasi')
            ->assertSee(route('admin.donation-outflows.index'));
    }

    public function test_creator_can_open_own_detail_but_does_not_see_verify_buttons(): void
    {
        $creator = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $outflow = $this->createOutflow(['created_by' => $creator->id]);

        $this->actingAs($creator)
            ->get(route('admin.donation-outflows.show', $outflow))
            ->assertOk()
            ->assertSee($outflow->transaction_number)
            ->assertSee('Menunggu verifikasi dari bagian Keuangan.')
            ->assertDontSee('Tindakan Verifikasi')
            ->assertDontSee('Setujui / ACC')
            ->assertDontSee('Tolak');
    }

    public function test_creator_cannot_approve_own_transaction(): void
    {
        $creator = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $outflow = $this->createOutflow(['created_by' => $creator->id]);

        $this->actingAs($creator)
            ->post(route('admin.donation-outflows.approve', $outflow))
            ->assertForbidden();

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('finance_incomes', 0);
    }

    public function test_creator_cannot_reject_own_transaction(): void
    {
        $creator = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $outflow = $this->createOutflow(['created_by' => $creator->id]);

        $this->actingAs($creator)
            ->post(route('admin.donation-outflows.reject', $outflow), [
                'rejection_reason' => 'Tidak bisa ditolak sendiri.',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'pending',
        ]);
    }

    public function test_non_finance_user_with_approve_permission_cannot_approve(): void
    {
        $user = $this->userWithPermissions(['donation.outflows.approve']);
        $outflow = $this->createOutflow();

        $this->actingAs($user)
            ->post(route('admin.donation-outflows.approve', $outflow))
            ->assertForbidden();

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('finance_incomes', 0);
    }

    public function test_superadmin_can_approve_transaction_not_created_by_self(): void
    {
        $superadmin = $this->userWithRoleOutflowDefaults('superadmin');
        $outflow = $this->createOutflow();

        $this->actingAs($superadmin)
            ->post(route('admin.donation-outflows.approve', $outflow))
            ->assertRedirect(route('admin.donation-outflows.show', $outflow));

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'approved',
            'approved_by' => $superadmin->id,
        ]);
        $this->assertDatabaseCount('finance_incomes', 1);
    }

    public function test_superadmin_cannot_approve_own_transaction(): void
    {
        $superadmin = $this->userWithRoleOutflowDefaults('superadmin');
        $outflow = $this->createOutflow(['created_by' => $superadmin->id]);

        $this->actingAs($superadmin)
            ->post(route('admin.donation-outflows.approve', $outflow))
            ->assertForbidden();

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('finance_incomes', 0);
    }

    public function test_superadmin_can_reject_transaction_not_created_by_self(): void
    {
        $superadmin = $this->userWithRoleOutflowDefaults('superadmin');
        $outflow = $this->createOutflow();

        $this->actingAs($superadmin)
            ->post(route('admin.donation-outflows.reject', $outflow), [
                'rejection_reason' => 'Dana tidak sesuai bukti.',
            ])
            ->assertRedirect(route('admin.donation-outflows.show', $outflow));

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'rejected',
            'rejected_by' => $superadmin->id,
        ]);
        $this->assertDatabaseCount('finance_incomes', 0);
    }

    public function test_superadmin_cannot_reject_own_transaction(): void
    {
        $superadmin = $this->userWithRoleOutflowDefaults('superadmin');
        $outflow = $this->createOutflow(['created_by' => $superadmin->id]);

        $this->actingAs($superadmin)
            ->post(route('admin.donation-outflows.reject', $outflow), [
                'rejection_reason' => 'Tidak boleh ditolak sendiri.',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('donation_outflows', [
            'id' => $outflow->id,
            'status' => 'pending',
        ]);
    }

    public function test_verification_default_roles_exclude_admin_in_manifest(): void
    {
        $manifest = collect(config('permissions'))
            ->keyBy('name')
            ->only(['donation.outflows.approve', 'donation.outflows.reject']);

        $this->assertSame(
            ['donation.outflows.approve', 'donation.outflows.reject'],
            $manifest->keys()->all(),
            'Manifest harus memuat kedua permission verifikasi.'
        );

        foreach ($manifest as $permission) {
            $this->assertNotContains('admin', $permission['default_roles'] ?? [], "{$permission['name']} tidak boleh berdefault admin.");
            $this->assertContains('staf_keuangan', $permission['default_roles'] ?? [], "{$permission['name']} harus berdefault staf_keuangan.");
        }
    }

    public function test_admin_with_stale_approve_reject_permission_still_forbidden(): void
    {
        $admin = $this->userWithPermissions([
            'donation.outflows.approve',
            'donation.outflows.reject',
        ], 'admin');

        $pending = $this->createOutflow();
        $this->actingAs($admin)
            ->post(route('admin.donation-outflows.approve', $pending))
            ->assertForbidden();
        $this->assertDatabaseHas('donation_outflows', [
            'id' => $pending->id,
            'status' => 'pending',
        ]);

        $pendingReject = $this->createOutflow(['transaction_number' => 'DK-20260731-AR01']);
        $this->actingAs($admin)
            ->post(route('admin.donation-outflows.reject', $pendingReject), [
                'rejection_reason' => 'Admin tidak boleh memverifikasi.',
            ])
            ->assertForbidden();
        $this->assertDatabaseHas('donation_outflows', [
            'id' => $pendingReject->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('finance_incomes', 0);
    }

    public function test_approve_reject_buttons_appear_per_permission_and_status(): void
    {
        $staf = $this->userWithRoleOutflowDefaults('staf_keuangan');

        $pending = $this->createOutflow();
        $this->actingAs($staf)
            ->get(route('admin.donation-outflows.show', $pending))
            ->assertOk()
            ->assertSee('Tindakan Verifikasi')
            ->assertSee('Setujui / ACC');

        $approved = $this->createOutflow([
            'transaction_number' => 'DK-20260731-AP2',
            'status' => 'approved',
        ]);
        $this->actingAs($staf)
            ->get(route('admin.donation-outflows.show', $approved))
            ->assertOk()
            ->assertDontSee('Tindakan Verifikasi')
            ->assertDontSee('Setujui / ACC');

        $viewOnly = $this->userWithPermissions(['donation.outflows.view']);
        $this->actingAs($viewOnly)
            ->get(route('admin.donation-outflows.show', $pending))
            ->assertOk()
            ->assertDontSee('Tindakan Verifikasi')
            ->assertDontSee('Setujui / ACC');
    }

    public function test_income_from_approval_shows_inputter_and_verifier(): void
    {
        $creator = User::factory()->create(['role' => 'admin', 'name' => 'Penginput Donasi']);
        $approver = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $approver->update(['name' => 'Verifikator Keuangan']);

        $outflow = $this->createOutflow(['created_by' => $creator->id]);
        app(DonationOutflowApprovalService::class)->approve($outflow, $approver);

        $this->actingAs($approver)
            ->get(route('admin.finance.incomes.index'))
            ->assertOk()
            ->assertSeeInOrder(['Diinput oleh', 'Penginput Donasi', 'Diverifikasi oleh', 'Verifikator Keuangan']);
    }

    public function test_manual_income_still_shows_recorder(): void
    {
        $recorder = $this->userWithRoleOutflowDefaults('admin');
        $recorder->update(['name' => 'Pencatat Manual']);

        FinanceIncome::create([
            'date' => '2026-07-31',
            'income_type' => 'Bantuan Sekolah',
            'amount' => 750000,
            'payment_method' => 'Transfer Bank',
            'source_name' => 'Manual',
            'description' => 'Pemasukan manual',
            'created_by' => $recorder->id,
        ]);

        $this->actingAs($recorder)
            ->get(route('admin.finance.incomes.index'))
            ->assertOk()
            ->assertSee('Pencatat Manual');
    }

    public function test_income_from_approval_handles_missing_inputter_user(): void
    {
        $approver = $this->userWithRoleOutflowDefaults('staf_keuangan');
        $creator = User::factory()->create(['role' => 'admin', 'name' => 'Penginput Lama']);

        $outflow = $this->createOutflow(['created_by' => $creator->id]);
        app(DonationOutflowApprovalService::class)->approve($outflow, $approver);

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('donation_outflows')->where('id', $outflow->id)->update(['created_by' => 999999]);
        DB::statement('PRAGMA foreign_keys = ON');

        $this->actingAs($approver)
            ->get(route('admin.finance.incomes.index'))
            ->assertOk()
            ->assertSee('Pengguna tidak tersedia');
    }

    private function userWithRoleOutflowDefaults(string $roleName): User
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

        $permissionIds = [];
        foreach (config('permissions') as $perm) {
            if (str_starts_with($perm['name'], 'donation.outflows.')
                && in_array($roleName, $perm['default_roles'] ?? [], true)) {
                $permissionIds[] = Permission::firstOrCreate(
                    ['name' => $perm['name']],
                    [
                        'module' => $perm['module'],
                        'action' => $perm['action'],
                        'display_name' => $perm['display_name'],
                        'group_name' => $perm['group_name'],
                        'is_active' => true,
                    ]
                )->id;
            }
        }

        $role->permissions()->syncWithoutDetaching($permissionIds);

        return $user;
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
