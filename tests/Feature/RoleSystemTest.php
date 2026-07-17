<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();
        $this->createRolesTable();
        $this->createRoleUserTable();
        $this->seedDefaultRoles();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    private function createUsersTable(): void
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
    }

    private function createRolesTable(): void
    {
        Schema::create('roles', function ($table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->string('guard_name', 30)->default('web');
            $table->timestamps();
        });
    }

    private function createRoleUserTable(): void
    {
        Schema::create('role_user', function ($table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });
    }

    private function seedDefaultRoles(): void
    {
        $roleNames = [
            'superadmin' => 'Superadmin',
            'admin' => 'Admin',
            'kepala_sekolah' => 'Kepala Sekolah',
            'guru' => 'Guru',
            'staf_tata_usaha' => 'Staf Tata Usaha',
            'staf_keuangan' => 'Staf Keuangan',
            'staf_kesiswaan' => 'Staf Kesiswaan',
            'staf_sarpras' => 'Staf Sarpras',
        ];

        foreach ($roleNames as $name => $displayName) {
            Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName, 'guard_name' => 'web']
            );
        }
    }

    public function test_admin_user_with_old_column_is_still_recognized_as_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isSuperadmin());
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_superadmin_user_with_old_column_is_still_recognized_as_superadmin(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->assertTrue($user->isSuperadmin());
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->hasRole('superadmin'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create(['role' => 'guru']);
        $guruRole = Role::where('name', 'guru')->first();
        $tuRole = Role::where('name', 'staf_tata_usaha')->first();

        $user->roles()->attach([$guruRole->id, $tuRole->id]);

        $user->load('roles');
        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->hasRole('guru'));
        $this->assertTrue($user->hasRole('staf_tata_usaha'));
        $this->assertTrue($user->hasAnyRole(['guru', 'staf_tata_usaha']));
    }

    public function test_guru_is_not_considered_admin(): void
    {
        $user = User::factory()->create(['role' => 'guru']);
        $guruRole = Role::where('name', 'guru')->first();
        $user->roles()->attach($guruRole->id);

        $user->load('roles');
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isSuperadmin());
    }

    public function test_has_any_role_works_correctly(): void
    {
        $user = User::factory()->create(['role' => 'kepala_sekolah']);
        $role = Role::where('name', 'kepala_sekolah')->first();
        $user->roles()->attach($role->id);

        $user->load('roles');
        $this->assertTrue($user->hasAnyRole(['kepala_sekolah', 'guru']));
        $this->assertFalse($user->hasAnyRole(['superadmin', 'admin']));
    }

    public function test_roles_fall_back_to_column_when_no_relation_exists(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->assertTrue($user->isSuperadmin());
        $this->assertTrue($user->isAdmin());
    }

    public function test_superadmin_can_assign_multiple_roles_via_controller(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $superadmin->roles()->attach(Role::where('name', 'superadmin')->first()->id);

        $guruRole = Role::where('name', 'guru')->first();
        $tuRole = Role::where('name', 'staf_tata_usaha')->first();

        $this->actingAs($superadmin)
            ->post(route('admin.users.store'), [
                'name' => 'Multi Role User',
                'email' => 'multi@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => [$guruRole->id, $tuRole->id],
            ])
            ->assertSessionHas('success');

        $user = User::where('email', 'multi@test.com')->first();
        $this->assertNotNull($user);
        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->hasRole('guru'));
        $this->assertTrue($user->hasRole('staf_tata_usaha'));
    }

    public function test_regular_admin_cannot_assign_superadmin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->roles()->attach(Role::where('name', 'admin')->first()->id);
        $superadminRole = Role::where('name', 'superadmin')->first();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => [$superadminRole->id],
            ])
            ->assertStatus(403);
    }

    public function test_regular_admin_cannot_edit_superadmin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->roles()->attach(Role::where('name', 'admin')->first()->id);

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $superadmin->roles()->attach(Role::where('name', 'superadmin')->first()->id);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $superadmin), [
                'name' => 'Hacked Name',
                'email' => $superadmin->email,
                'roles' => [$superadmin->roles->first()->id],
            ])
            ->assertStatus(403);
    }

    public function test_sync_roles_does_not_duplicate(): void
    {
        $user = User::factory()->create(['role' => 'guru']);
        $guruRole = Role::where('name', 'guru')->first();

        $user->roles()->syncWithoutDetaching([$guruRole->id]);
        $user->roles()->syncWithoutDetaching([$guruRole->id]);

        $this->assertCount(1, $user->roles()->get());
    }

    public function test_migration_does_not_delete_existing_users(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->syncWithoutDetaching([$adminRole->id]);

        $user->refresh();
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue($user->isAdmin());
    }
}
