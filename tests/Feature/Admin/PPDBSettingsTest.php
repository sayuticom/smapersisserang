<?php

namespace Tests\Feature\Admin;

use App\Models\AdmissionYear;
use App\Models\AdmissionProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\HasAdminUser;
use Tests\TestCase;

class PPDBSettingsTest extends TestCase
{
    use RefreshDatabase, HasAdminUser;

    private User $admin;
    private AdmissionYear $admissionYear;
    private AdmissionProgram $admissionProgram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdminUser();

        $this->admissionYear = AdmissionYear::factory()->create([
            'status' => 'open',
        ]);

        $this->admissionProgram = AdmissionProgram::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'status' => 'open',
        ]);
    }

    public function test_guest_cannot_access_settings_page(): void
    {
        $this->get(route('admin.ppdb.settings.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_open_settings_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.settings.edit'))
            ->assertOk()
            ->assertSee('Pengaturan SPMB')
            ->assertSee($this->admissionYear->name)
            ->assertSee($this->admissionProgram->name);
    }

    public function test_admin_can_update_settings(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.ppdb.settings.update'), [
                'name' => 'Angkatan Kedua',
                'academic_year' => '2027/2028',
                'quota' => 50,
                'status' => 'closed',
                'start_date' => '2027-01-01',
                'end_date' => '2027-06-30',
                'description' => 'Program angkatan kedua.',

                'program_name' => 'Program Reguler',
                'program_type' => 'regular_paid',
                'program_quota' => 50,
                'program_status' => 'closed',
                'tuition_fee' => 500000,
                'boarding_fee' => 1000000,
                'meal_fee' => 300000,
                'registration_fee' => 200000,
                'other_fee' => 100000,
                'is_free_program' => 0,
                'program_description' => 'Program reguler berbayar.',
            ])
            ->assertRedirect(route('admin.ppdb.settings.edit'))
            ->assertSessionHas('success');

        $this->admissionYear->refresh();
        $this->admissionProgram->refresh();

        $this->assertEquals('Angkatan Kedua', $this->admissionYear->name);
        $this->assertEquals('closed', $this->admissionYear->status);
        $this->assertEquals('closed', $this->admissionProgram->status);
        $this->assertEquals('Program Reguler', $this->admissionProgram->name);
    }

    public function test_update_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.ppdb.settings.update'), [
                'name' => '',
                'academic_year' => '',
                'quota' => '',
                'status' => '',
                'start_date' => '',
                'end_date' => '',
                'program_name' => '',
                'program_type' => '',
                'program_quota' => '',
                'program_status' => '',
                'tuition_fee' => '',
                'boarding_fee' => '',
                'meal_fee' => '',
                'registration_fee' => '',
                'other_fee' => '',
            ])
            ->assertSessionHasErrors([
                'name', 'academic_year', 'quota', 'status', 'start_date', 'end_date',
                'program_name', 'program_type', 'program_quota', 'program_status',
                'tuition_fee', 'boarding_fee', 'meal_fee', 'registration_fee', 'other_fee',
            ]);
    }
}
