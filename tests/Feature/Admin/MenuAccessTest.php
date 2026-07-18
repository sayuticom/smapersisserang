<?php

namespace Tests\Feature\Admin;

use App\Models\MenuRoleOverride;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\AdminMenuService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MenuAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
        $this->seedRoles();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('menu_role_overrides');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('school_settings');
        parent::tearDown();
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
            $table->string('role', 50)->default('admin');
            $table->timestamps();
        });

        Schema::create('roles', function ($table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->string('guard_name', 30)->default('web');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('role_user', function ($table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('school_settings', function ($table) {
            $table->id();
            $table->string('school_name')->nullable();
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_role_overrides', function ($table) {
            $table->id();
            $table->string('menu_key', 100)->unique();
            $table->json('roles');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function ($table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('module', 100)->default('');
            $table->string('action', 50)->default('');
            $table->string('display_name', 200)->default('');
            $table->text('description')->nullable();
            $table->string('group_name', 100)->default('');
            $table->string('guard_name', 30)->default('web');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('permission_role', function ($table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->unsignedBigInteger('granted_by')->nullable();
            $table->timestamps();
            $table->unique(['permission_id', 'role_id']);
        });
    }

    private function seedRoles(): void
    {
        $systemRoles = [
            'superadmin' => ['display_name' => 'Superadmin', 'sort_order' => 1],
            'admin' => ['display_name' => 'Admin', 'sort_order' => 2],
            'kepala_sekolah' => ['display_name' => 'Kepala Sekolah', 'sort_order' => 3],
            'guru' => ['display_name' => 'Guru', 'sort_order' => 4],
            'staf_tata_usaha' => ['display_name' => 'Staf Tata Usaha', 'sort_order' => 5],
            'staf_keuangan' => ['display_name' => 'Staf Keuangan', 'sort_order' => 6],
            'staf_kesiswaan' => ['display_name' => 'Staf Kesiswaan', 'sort_order' => 7],
            'staf_sarpras' => ['display_name' => 'Staf Sarpras', 'sort_order' => 8],
        ];

        foreach ($systemRoles as $name => $config) {
            Role::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $config['display_name'],
                    'guard_name' => 'web',
                    'is_system' => true,
                    'is_active' => true,
                    'sort_order' => $config['sort_order'],
                ]
            );
        }
    }

    private function createUser(string $roleName): User
    {
        $user = User::factory()->create(['role' => $roleName]);
        $role = Role::where('name', $roleName)->first();
        $user->roles()->attach($role->id);
        return $user;
    }

    private function createPermission(string $name, string $roleName): Permission
    {
        return Permission::create([
            'name' => $name,
            'module' => 'test',
            'action' => 'view',
            'display_name' => $name,
            'group_name' => 'test',
        ]);
    }

    private function registerTestSidebarRoute(string $name): void
    {
        Route::middleware('web')->group(function () use ($name) {
            Route::get('/_test/menu-' . $name, function () {
                return view('components.admin-layout', ['slot' => '']);
            })->middleware('auth')->name('_test.menu-' . $name);
        });
    }

    // ── Access Control ──

    public function test_superadmin_can_access_menu_access_page(): void
    {
        $user = $this->createUser('superadmin');
        $response = $this->actingAs($user)->get(route('admin.menu-access.index'));
        $response->assertOk();
        $response->assertSee('Pengaturan Menu Akses');
        $response->assertSee('ppdb.dashboard');
        $response->assertSee('finance.incomes');
        $response->assertSee('sarpras.assets');
    }

    public function test_non_superadmin_cannot_access_menu_access_page(): void
    {
        $roles = ['admin', 'kepala_sekolah', 'guru', 'staf_tata_usaha', 'staf_keuangan', 'staf_kesiswaan', 'staf_sarpras'];
        foreach ($roles as $roleName) {
            $user = $this->createUser($roleName);
            $response = $this->actingAs($user)->get(route('admin.menu-access.index'));
            $response->assertForbidden();
        }
    }

    public function test_admin_and_other_roles_get_403_on_menu_access_page(): void
    {
        $roles = ['admin', 'kepala_sekolah', 'guru', 'staf_tata_usaha', 'staf_keuangan', 'staf_kesiswaan', 'staf_sarpras'];
        foreach ($roles as $roleName) {
            $user = $this->createUser($roleName);
            $this->actingAs($user)->get(route('admin.menu-access.index'))->assertForbidden();
            $this->actingAs($user)->put(route('admin.menu-access.update'), [
                'overrides' => [['key' => 'ppdb.dashboard', 'roles' => ['admin']]],
            ])->assertForbidden();
        }
    }

    // ── CRUD Overrides ──

    public function test_superadmin_can_update_menu_role_overrides(): void
    {
        $user = $this->createUser('superadmin');

        $response = $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [
                [
                    'key' => 'ppdb.dashboard',
                    'roles' => ['superadmin', 'admin'],
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('menu_role_overrides', [
            'menu_key' => 'ppdb.dashboard',
        ]);

        $override = MenuRoleOverride::where('menu_key', 'ppdb.dashboard')->first();
        $this->assertEquals(['superadmin', 'admin'], $override->roles);
    }

    public function test_clearing_all_roles_deletes_override(): void
    {
        MenuRoleOverride::create([
            'menu_key' => 'sarpras.dashboard',
            'roles' => ['superadmin', 'admin'],
        ]);

        $user = $this->createUser('superadmin');

        $response = $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [
                [
                    'key' => 'sarpras.dashboard',
                    'roles' => [],
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('menu_role_overrides', [
            'menu_key' => 'sarpras.dashboard',
        ]);
    }

    // ── Locked Items ──

    public function test_locked_dashboard_cannot_be_overridden(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'dashboard', 'roles' => ['admin']]],
        ]);

        $this->assertDatabaseMissing('menu_role_overrides', ['menu_key' => 'dashboard']);
    }

    public function test_locked_profile_cannot_be_overridden(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'account.profile', 'roles' => ['admin']]],
        ]);

        $this->assertDatabaseMissing('menu_role_overrides', ['menu_key' => 'account.profile']);
    }

    public function test_locked_kelola_user_cannot_be_expanded(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'system.users', 'roles' => ['superadmin', 'admin']]],
        ]);

        $this->assertDatabaseMissing('menu_role_overrides', ['menu_key' => 'system.users']);
    }

    // ── Reset ──

    public function test_reset_removes_all_overrides(): void
    {
        MenuRoleOverride::create(['menu_key' => 'ppdb.dashboard', 'roles' => ['superadmin']]);
        MenuRoleOverride::create(['menu_key' => 'finance.incomes', 'roles' => ['superadmin']]);

        $user = $this->createUser('superadmin');

        $this->assertDatabaseCount('menu_role_overrides', 2);

        $this->actingAs($user)->put(route('admin.menu-access.update'), ['reset' => '1']);

        $this->assertDatabaseCount('menu_role_overrides', 0);
    }

    public function test_reset_clears_cache(): void
    {
        MenuRoleOverride::create(['menu_key' => 'ppdb.dashboard', 'roles' => ['superadmin', 'admin']]);
        $service = app(AdminMenuService::class);
        $service->flushCache();

        $this->assertNotNull($service->getOverrideFor('ppdb.dashboard'));

        $user = $this->createUser('superadmin');
        $this->actingAs($user)->put(route('admin.menu-access.update'), ['reset' => '1']);

        $service->flushCache();
        $this->assertNull($service->getOverrideFor('ppdb.dashboard'));
    }

    // ── Validation ──

    public function test_foreign_key_submission_is_ignored(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [
                ['key' => 'nonexistent.menu.key', 'roles' => ['superadmin', 'admin']],
            ],
        ]);

        $this->assertDatabaseMissing('menu_role_overrides', ['menu_key' => 'nonexistent.menu.key']);
    }

    public function test_invalid_role_submission_is_filtered_out(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [
                [
                    'key' => 'ppdb.dashboard',
                    'roles' => ['superadmin', 'admin', 'invalid_role_xyz'],
                ],
            ],
        ]);

        $override = MenuRoleOverride::where('menu_key', 'ppdb.dashboard')->first();
        $this->assertNotNull($override);
        $this->assertContains('superadmin', $override->roles);
        $this->assertContains('admin', $override->roles);
        $this->assertNotContains('invalid_role_xyz', $override->roles);
    }

    public function test_superadmin_always_forced_in_override(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [
                [
                    'key' => 'ppdb.dashboard',
                    'roles' => ['admin', 'staf_tata_usaha'],
                ],
            ],
        ]);

        $override = MenuRoleOverride::where('menu_key', 'ppdb.dashboard')->first();
        $this->assertNotNull($override);
        $this->assertContains('superadmin', $override->roles);
        $this->assertContains('admin', $override->roles);
    }

    public function test_roles_not_in_route_allowed_are_filtered_out(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'sarpras.assets', 'roles' => ['superadmin', 'admin', 'guru']]],
        ]);

        $override = MenuRoleOverride::where('menu_key', 'sarpras.assets')->first();
        $this->assertNotNull($override);
        $this->assertContains('superadmin', $override->roles);
        $this->assertContains('admin', $override->roles);
        $this->assertNotContains('guru', $override->roles);
    }

    // ── created_by / updated_by ──

    public function test_created_by_recorded_on_create(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'ppdb.dashboard', 'roles' => ['superadmin', 'admin']]],
        ]);

        $override = MenuRoleOverride::where('menu_key', 'ppdb.dashboard')->first();
        $this->assertEquals($user->id, $override->created_by);
        $this->assertEquals($user->id, $override->updated_by);
    }

    public function test_updated_by_changes_on_update_by_different_user(): void
    {
        $creator = $this->createUser('superadmin');

        $this->actingAs($creator)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'ppdb.dashboard', 'roles' => ['superadmin', 'admin']]],
        ]);

        $override = MenuRoleOverride::where('menu_key', 'ppdb.dashboard')->first();
        $this->assertEquals($creator->id, $override->created_by);

        $updater = User::factory()->create(['role' => 'superadmin']);
        $role = Role::where('name', 'superadmin')->first();
        $updater->roles()->attach($role->id);

        $this->actingAs($updater)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'ppdb.dashboard', 'roles' => ['superadmin', 'admin', 'staf_tata_usaha']]],
        ]);

        $override->refresh();
        $this->assertEquals($creator->id, $override->created_by);
        $this->assertEquals($updater->id, $override->updated_by);
    }

    // ── Sidebar ──

    public function test_sidebar_reflects_role_overrides(): void
    {
        $this->registerTestSidebarRoute('override-test');

        MenuRoleOverride::create([
            'menu_key' => 'ppdb.dashboard',
            'roles' => ['superadmin', 'admin'],
        ]);

        $tuUser = $this->createUser('staf_tata_usaha');
        $response = $this->actingAs($tuUser)->get('/_test/menu-override-test');
        $html = $response->getContent();

        $this->assertStringNotContainsString('Dashboard SPMB', $html);
    }

    public function test_sidebar_filters_by_override(): void
    {
        $this->registerTestSidebarRoute('sidebar-filter');

        MenuRoleOverride::create([
            'menu_key' => 'ppdb.applications',
            'roles' => ['superadmin'],
        ]);

        $adminUser = $this->createUser('admin');
        $response = $this->actingAs($adminUser)->get('/_test/menu-sidebar-filter');
        $html = $response->getContent();

        $this->assertStringNotContainsString('Data Pendaftaran', $html);
    }

    public function test_sidebar_changes_after_override_then_reset(): void
    {
        $this->registerTestSidebarRoute('sidebar-change');

        $tuUser = $this->createUser('staf_tata_usaha');
        $response = $this->actingAs($tuUser)->get('/_test/menu-sidebar-change');
        $html = $response->getContent();
        $this->assertStringContainsString('Dashboard SPMB', $html);

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $role = Role::where('name', 'superadmin')->first();
        $superadmin->roles()->attach($role->id);

        $this->actingAs($superadmin)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'ppdb.dashboard', 'roles' => ['superadmin', 'admin']]],
        ]);

        $response2 = $this->actingAs($tuUser)->get('/_test/menu-sidebar-change');
        $html2 = $response2->getContent();
        $this->assertStringNotContainsString('Dashboard SPMB', $html2);

        $this->actingAs($superadmin)->put(route('admin.menu-access.update'), ['reset' => '1']);

        $response3 = $this->actingAs($tuUser)->get('/_test/menu-sidebar-change');
        $html3 = $response3->getContent();
        $this->assertStringContainsString('Dashboard SPMB', $html3);
    }

    public function test_sidebar_shows_all_for_superadmin(): void
    {
        $user = $this->createUser('superadmin');

        $service = app(AdminMenuService::class);
        $sidebar = $service->getSidebar($user);

        $this->assertArrayHasKey('dashboard', $sidebar);
        $this->assertArrayHasKey('sections', $sidebar);
        $sectionLabels = array_map(fn($s) => $s['label'], $sidebar['sections']);
        $this->assertContains('SPMB', $sectionLabels);
        $this->assertContains('SISTEM', $sectionLabels);
    }

    // ── Service ──

    public function test_service_computes_effective_roles(): void
    {
        MenuRoleOverride::create([
            'menu_key' => 'ppdb.applications',
            'roles' => ['superadmin', 'admin', 'staf_tata_usaha'],
        ]);

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $item = [
            'key' => 'ppdb.applications',
            'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
        ];

        $effective = $service->getEffectiveRoles($item);

        $this->assertContains('superadmin', $effective);
        $this->assertContains('admin', $effective);
        $this->assertContains('staf_tata_usaha', $effective);
    }

    public function test_get_all_menu_items_returns_all_keys(): void
    {
        $service = app(AdminMenuService::class);
        $items = $service->getAllMenuItems();

        $keys = array_column($items, 'key');
        $this->assertContains('ppdb.dashboard', $keys);
        $this->assertContains('finance.incomes', $keys);
        $this->assertContains('letters.outgoings', $keys);
        $this->assertContains('sarpras.assets', $keys);
        $this->assertContains('system.menu-access', $keys);
        $this->assertContains('account.profile', $keys);

        $this->assertNotContains('Kalender dan Jadwal', array_column($items, 'label'));
    }

    public function test_parent_visibility_computed_from_children(): void
    {
        MenuRoleOverride::create([
            'menu_key' => 'academic.calendar',
            'roles' => ['superadmin', 'admin'],
        ]);

        MenuRoleOverride::create([
            'menu_key' => 'academic.schedule',
            'roles' => ['superadmin', 'admin'],
        ]);

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $guru = $this->createUser('guru');

        $parentItem = [
            'label' => 'Kalender dan Jadwal',
            'route' => '#',
            'route_active' => 'admin.akademik.kalender.*|admin.akademik.jadwal-pelajaran.*',
            'icon' => 'calendar',
            'children' => [
                ['key' => 'academic.calendar', 'label' => 'Kalender Pendidikan', 'route' => '', 'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah', 'guru']],
                ['key' => 'academic.schedule', 'label' => 'Jadwal Pelajaran', 'route' => '', 'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah', 'guru']],
            ],
            'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah', 'guru'],
        ];

        $visible = $service->isVisibleToUser($parentItem, $guru);
        $this->assertFalse($visible, 'Parent should be hidden when all children are hidden by override');
    }

    public function test_fallback_when_table_not_available(): void
    {
        Schema::dropIfExists('menu_role_overrides');

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $overrides = $service->getOverrides();
        $this->assertNotNull($overrides);
        $this->assertTrue($overrides->isEmpty());

        $item = [
            'key' => 'ppdb.dashboard',
            'route' => 'admin.ppdb.dashboard',
            'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
        ];

        $effective = $service->getEffectiveRoles($item);
        $this->assertContains('superadmin', $effective);
        $this->assertContains('admin', $effective);
    }

    public function test_empty_override_returns_to_config_default(): void
    {
        $user = $this->createUser('superadmin');

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'ppdb.dashboard', 'roles' => ['superadmin', 'admin']]],
        ]);

        $this->assertDatabaseHas('menu_role_overrides', ['menu_key' => 'ppdb.dashboard']);

        $this->actingAs($user)->put(route('admin.menu-access.update'), [
            'overrides' => [['key' => 'ppdb.dashboard', 'roles' => []]],
        ]);

        $this->assertDatabaseMissing('menu_role_overrides', ['menu_key' => 'ppdb.dashboard']);
    }

    // ── Permission-based visibility (Fase 1) ──

    public function test_superadmin_sees_all_menu_via_permission(): void
    {
        $service = app(AdminMenuService::class);
        $service->flushCache();

        $user = $this->createUser('superadmin');
        $sidebar = $service->getSidebar($user);

        $this->assertArrayHasKey('dashboard', $sidebar);
        $sectionLabels = array_map(fn($s) => $s['label'], $sidebar['sections']);
        $this->assertContains('SISTEM', $sectionLabels);
        $this->assertContains('SPMB', $sectionLabels);
    }

    public function test_user_with_permission_can_see_menu(): void
    {
        $perm = $this->createPermission('ppdb.dashboard.view', 'staf_tata_usaha');
        $role = Role::where('name', 'staf_tata_usaha')->first();
        DB::table('permission_role')->insert([
            'permission_id' => $perm->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $user = $this->createUser('staf_tata_usaha');
        $this->assertTrue(
            $service->isVisibleToUser([
                'key' => 'ppdb.dashboard',
                'permission' => 'ppdb.dashboard.view',
                'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
            ], $user)
        );
    }

    public function test_user_without_permission_cannot_see_menu_even_if_role_allows(): void
    {
        $this->createPermission('ppdb.dashboard.view', 'admin');

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $user = $this->createUser('staf_tata_usaha');
        $this->assertFalse(
            $service->isVisibleToUser([
                'key' => 'ppdb.dashboard',
                'permission' => 'ppdb.dashboard.view',
                'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
            ], $user)
        );
    }

    public function test_permission_not_available_falls_back_to_role(): void
    {
        $service = app(AdminMenuService::class);
        $service->flushCache();

        $user = $this->createUser('staf_sarpras');

        $permName = 'nonexistent.permission.not.seeded';
        $this->assertTrue(
            $service->isVisibleToUser([
                'key' => 'sarpras.dashboard',
                'permission' => $permName,
                'roles' => ['superadmin', 'admin', 'staf_sarpras', 'kepala_sekolah'],
            ], $user)
        );
    }

    public function test_item_without_permission_uses_role_only(): void
    {
        $service = app(AdminMenuService::class);
        $service->flushCache();

        $guru = $this->createUser('guru');
        $this->assertFalse(
            $service->isVisibleToUser([
                'key' => 'ppdb.dashboard',
                'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
            ], $guru)
        );

        $tu = $this->createUser('staf_tata_usaha');
        $this->assertTrue(
            $service->isVisibleToUser([
                'key' => 'ppdb.dashboard',
                'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
            ], $tu)
        );

        $stafSarpras = $this->createUser('staf_sarpras');
        $this->assertFalse(
            $service->isVisibleToUser([
                'key' => 'ppdb.dashboard',
                'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
            ], $stafSarpras)
        );
    }

    public function test_parent_visible_when_child_permitted(): void
    {
        $this->createPermission('academic.calendar.view', 'guru');
        $this->createPermission('academic.schedule.view', 'admin');
        $role = Role::where('name', 'guru')->first();
        DB::table('permission_role')->insert([
            'permission_id' => Permission::where('name', 'academic.calendar.view')->first()->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $user = $this->createUser('guru');
        $sidebar = $service->getSidebar($user);

        $sectionLabels = array_map(fn($s) => $s['label'], $sidebar['sections']);
        $this->assertContains('AKADEMIK', $sectionLabels);

        $akademik = collect($sidebar['sections'])->firstWhere('label', 'AKADEMIK');
        $this->assertNotEmpty($akademik['items']);

        $parentLabels = array_map(fn($i) => $i['label'] ?? '', $akademik['items']);
        $this->assertContains('Kalender dan Jadwal', $parentLabels);

        $jitem = collect($akademik['items'])->firstWhere('label', 'Kalender dan Jadwal');
        $childLabels = array_map(fn($c) => $c['label'], $jitem['children']);
        $this->assertContains('Kalender Pendidikan', $childLabels);
        $this->assertNotContains('Jadwal Pelajaran', $childLabels);
    }

    public function test_parent_hidden_when_all_children_denied(): void
    {
        $this->createPermission('academic.calendar.view', 'admin');
        $this->createPermission('academic.schedule.view', 'admin');

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $user = $this->createUser('guru');
        $sidebar = $service->getSidebar($user);

        $akademik = collect($sidebar['sections'] ?? [])->firstWhere('label', 'AKADEMIK');
        $this->assertNotEmpty($akademik['items']);

        $parentLabels = array_map(fn($i) => $i['label'] ?? '', $akademik['items']);
        $this->assertNotContains('Kalender dan Jadwal', $parentLabels);
    }

    public function test_denied_child_excluded_from_sidebar(): void
    {
        $this->createPermission('academic.calendar.view', 'guru');
        $this->createPermission('academic.schedule.view', 'admin');
        $role = Role::where('name', 'guru')->first();
        DB::table('permission_role')->insert([
            'permission_id' => Permission::where('name', 'academic.calendar.view')->first()->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(AdminMenuService::class);
        $service->flushCache();

        $user = $this->createUser('guru');
        $sidebar = $service->getSidebar($user);

        $akademik = collect($sidebar['sections'])->firstWhere('label', 'AKADEMIK');
        $jitem = collect($akademik['items'])->firstWhere('label', 'Kalender dan Jadwal');
        $childLabels = array_map(fn($c) => $c['label'], $jitem['children']);

        $this->assertContains('Kalender Pendidikan', $childLabels);
        $this->assertNotContains('Jadwal Pelajaran', $childLabels);
    }

    public function test_override_still_works_on_role_fallback(): void
    {
        $service = app(AdminMenuService::class);
        $service->flushCache();

        MenuRoleOverride::create([
            'menu_key' => 'sarpras.dashboard',
            'roles' => ['superadmin', 'admin'],
        ]);
        $service->flushCache();

        $tu = $this->createUser('staf_tata_usaha');
        $this->assertFalse(
            $service->isVisibleToUser([
                'key' => 'sarpras.dashboard',
                'roles' => ['superadmin', 'admin', 'staf_sarpras', 'kepala_sekolah'],
            ], $tu)
        );
    }

    public function test_override_cannot_bypass_permission_denied(): void
    {
        $perm = $this->createPermission('ppdb.dashboard.view', 'admin');
        $role = Role::where('name', 'admin')->first();
        DB::table('permission_role')->insert([
            'permission_id' => $perm->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(AdminMenuService::class);
        $service->flushCache();

        MenuRoleOverride::create([
            'menu_key' => 'ppdb.dashboard',
            'roles' => ['superadmin', 'admin', 'staf_tata_usaha'],
        ]);
        $service->flushCache();

        $tu = $this->createUser('staf_tata_usaha');
        $this->assertFalse(
            $service->isVisibleToUser([
                'key' => 'ppdb.dashboard',
                'permission' => 'ppdb.dashboard.view',
                'roles' => ['superadmin', 'admin', 'staf_tata_usaha', 'staf_kesiswaan', 'kepala_sekolah'],
            ], $tu)
        );
    }
}
