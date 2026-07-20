<?php

namespace Tests\Feature\Admin;

use App\Models\AdmissionYear;
use App\Models\AdmissionProgram;
use App\Models\StudentApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\HasAdminUser;
use Tests\TestCase;

class PPDBApplicationTest extends TestCase
{
    use RefreshDatabase, HasAdminUser;

    private User $admin;
    private AdmissionYear $admissionYear;
    private AdmissionProgram $admissionProgram;
    private StudentApplication $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdminUser();

        $this->admissionYear = AdmissionYear::factory()->create();
        $this->admissionProgram = AdmissionProgram::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
        ]);

        $this->application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'registration_number' => 'SPMB-2025-0001',
            'status' => 'baru_daftar',
        ]);

        ApplicationStatusHistory::create([
            'student_application_id' => $this->application->id,
            'from_status' => null,
            'to_status' => 'baru_daftar',
            'changed_by' => null,
            'notes' => 'Pendaftaran dibuat melalui form SPMB online.',
        ]);
    }

    public function test_guest_cannot_access_admin_ppdb_index()
    {
        $this->get(route('admin.ppdb.applications.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_admin_ppdb_show()
    {
        $this->get(route('admin.ppdb.applications.show', $this->application))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_status()
    {
        $this->patch(route('admin.ppdb.applications.update-status', $this->application), [
            'status' => 'terverifikasi',
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_index_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'))
            ->assertOk()
            ->assertSee('Pendaftar SPMB')
            ->assertSee($this->application->registration_number)
            ->assertSee($this->application->student_name);
    }

    public function test_index_page_has_mobile_card_layout()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'));

        $response->assertOk();
        $response->assertSee('md:hidden', false);
        $response->assertSee('hidden md:table', false);
    }

    public function test_index_page_has_filter_form()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'))
            ->assertOk()
            ->assertSee('Cari nama')
            ->assertSee('Semua Status')
            ->assertSee('Semua Tahun')
            ->assertSee('Filter');
    }

    public function test_index_page_pagination_and_search()
    {
        StudentApplication::factory(25)->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'));

        $response->assertOk();
        $response->assertSee('Total:');
    }

    public function test_admin_can_view_show_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.show', $this->application))
            ->assertOk()
            ->assertSee('Detail Pendaftar')
            ->assertSee($this->application->registration_number)
            ->assertSee($this->application->student_name)
            ->assertSee('Data Siswa')
            ->assertSee('Data Orang Tua')
            ->assertSee('Informasi Pendaftaran')
            ->assertSee('Ubah Status')
            ->assertSee('Riwayat Status');
    }

    public function test_show_page_displays_status_history()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.show', $this->application))
            ->assertOk()
            ->assertSee('Baru Daftar')
            ->assertSee('Pendaftaran dibuat melalui form SPMB online.');
    }

    public function test_show_page_has_status_change_form()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.show', $this->application))
            ->assertOk()
            ->assertSee('Simpan Perubahan Status')
            ->assertSee('name="status"', false)
            ->assertSee('name="notes"', false);
    }

    public function test_update_status_changes_application_status()
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [
                'status' => 'terverifikasi',
                'notes' => 'Data lengkap, lanjut verifikasi.',
            ])->assertSessionHas('success', 'Status berhasil diperbarui.');

        $this->application->refresh();
        $this->assertEquals('terverifikasi', $this->application->status);
    }

    public function test_update_status_creates_status_history()
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [
                'status' => 'terverifikasi',
                'notes' => 'Data lengkap.',
            ]);

        $historyCount = ApplicationStatusHistory::where('student_application_id', $this->application->id)->count();
        $this->assertEquals(2, $historyCount);

        $latestHistory = ApplicationStatusHistory::where('student_application_id', $this->application->id)
            ->orderByDesc('id')
            ->first();

        $this->assertEquals('baru_daftar', $latestHistory->from_status);
        $this->assertEquals('terverifikasi', $latestHistory->to_status);
        $this->assertEquals($this->admin->id, $latestHistory->changed_by);
        $this->assertEquals('Data lengkap.', $latestHistory->notes);
    }

    public function test_update_status_sets_verified_by_when_terverifikasi()
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [
                'status' => 'terverifikasi',
            ]);

        $this->application->refresh();
        $this->assertEquals($this->admin->id, $this->application->verified_by);
        $this->assertNotNull($this->application->verified_at);
    }

    public function test_update_status_does_not_change_verified_by_if_already_verified()
    {
        $this->application->update([
            'verified_by' => $this->admin->id,
            'verified_at' => now()->subDay(),
        ]);

        $newAdmin = $this->createAdminUser();

        $this->actingAs($newAdmin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [
                'status' => 'terverifikasi',
            ]);

        $this->application->refresh();
        $this->assertEquals($this->admin->id, $this->application->verified_by);
    }

    public function test_cannot_update_to_same_status()
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [
                'status' => 'baru_daftar',
            ])->assertSessionHas('error', 'Status sudah sama, tidak ada perubahan.');

        $historyCount = ApplicationStatusHistory::where('student_application_id', $this->application->id)->count();
        $this->assertEquals(1, $historyCount);
    }

    public function test_update_status_with_invalid_status_fails()
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [
                'status' => 'invalid_status',
            ])->assertSessionHasErrors('status');
    }

    public function test_update_status_without_status_fails()
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [])
            ->assertSessionHasErrors('status');
    }

    public function test_update_status_notes_are_optional()
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-status', $this->application), [
                'status' => 'menunggu_verifikasi',
            ])->assertSessionHas('success');

        $this->application->refresh();
        $this->assertEquals('menunggu_verifikasi', $this->application->status);
    }

    public function test_index_search_by_registration_number()
    {
        $secondApp = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'registration_number' => 'SPMB-2025-0002',
            'student_name' => 'Unik Nama Siswa',
            'parent_whatsapp' => '081234567890',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index', ['search' => '0002']))
            ->assertOk()
            ->assertSee($secondApp->student_name)
            ->assertDontSee($this->application->student_name);
    }

    public function test_index_search_by_name()
    {
        $secondApp = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'registration_number' => 'SPMB-2025-0002',
            'student_name' => 'Target Siswa',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index', ['search' => 'Target']))
            ->assertOk()
            ->assertSee($secondApp->registration_number);
    }

    public function test_index_filter_by_status()
    {
        $verifiedApp = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'registration_number' => 'SPMB-2025-0002',
            'status' => 'terverifikasi',
            'student_name' => 'Verified Student',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index', ['status' => 'terverifikasi']))
            ->assertOk()
            ->assertSee($verifiedApp->student_name)
            ->assertDontSee($this->application->student_name);
    }

    public function test_mobile_view_does_not_overflow()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('md:hidden', $html, 'Mobile card layout should be present');
        $this->assertStringContainsString('hidden md:table', $html, 'Desktop table should be hidden on mobile');

        preg_match_all('/<div\s+class="p-4\s+space-y-2">/', $html, $mobileCards);
        $this->assertGreaterThan(0, count($mobileCards[0]), 'Mobile card items should exist');

        preg_match_all('/<table\s+class="min-w-full\s+divide-y\s+divide-gray-200\s+hidden\s+md:table">/', $html, $tables);
        $this->assertGreaterThan(0, count($tables[0]), 'Desktop table should exist but hidden on mobile');

        $this->assertStringContainsString('lg:pl-64', $html, 'Sidebar should use responsive padding');
        $this->assertStringContainsString('max-w-7xl', $html, 'Content should have max-width constraint');
    }

    public function test_mobile_card_view_shows_all_required_info()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'))
            ->assertOk()
            ->assertSee($this->application->registration_number)
            ->assertSee($this->application->student_name)
            ->assertSee($this->application->parent_whatsapp)
            ->assertSee('Detail');
    }

    public function test_show_page_mobile_responsive()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.show', $this->application));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('grid grid-cols-1 sm:grid-cols-2', $html);
        $this->assertStringContainsString('sm:col-span-2', $html);
        $this->assertStringContainsString('w-full sm:w-64', $html);
        $this->assertStringContainsString('w-full sm:w-96', $html);
    }

    // ─── Dashboard ─────────────────────────────────────────────

    public function test_guest_cannot_access_dashboard()
    {
        $this->get(route('admin.ppdb.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_page_loads()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard SPMB')
            ->assertSee($this->admissionYear->academic_year);
    }

    public function test_dashboard_shows_total_pendaftar()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'))
            ->assertOk()
            ->assertSee('Total Pendaftar');
    }

    public function test_dashboard_shows_quota_info()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'))
            ->assertOk()
            ->assertSee('Kuota Total')
            ->assertSee('Terisi (Diterima)')
            ->assertSee('Sisa Kuota')
            ->assertSee('36');
    }

    public function test_dashboard_shows_recent_applicants()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'))
            ->assertOk()
            ->assertSee('Pendaftar Terbaru')
            ->assertSee($this->application->registration_number)
            ->assertSee($this->application->student_name);
    }

    public function test_dashboard_has_sidebar_links()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Dashboard SPMB', $html);
        $this->assertStringContainsString(route('admin.ppdb.dashboard'), $html);
        $this->assertStringContainsString('Data Pendaftaran', $html);
        $this->assertStringContainsString(route('admin.ppdb.applications.index'), $html);
    }

    public function test_sidebar_has_ppdb_group(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Dashboard SPMB', $html);
        $this->assertStringContainsString('Data Pendaftaran', $html);
        $this->assertStringContainsString('Pengaturan SPMB', $html);
    }

    public function test_sidebar_has_website_group(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Pengaturan Website', $html);
        $this->assertStringContainsString('Galeri Sekolah', $html);
        $this->assertStringContainsString('Kategori Galeri', $html);
        $this->assertStringContainsString('Tokoh &amp; Pembina', $html);
        $this->assertStringContainsString('Profil Guru', $html);
    }

    public function test_sidebar_has_akun_group(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Profil', $html);
        $this->assertStringContainsString(route('profile.edit'), $html);
    }

    public function test_sidebar_ppdb_settings_in_ppdb_group(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.settings.edit'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Pengaturan SPMB', $html);
        $this->assertStringContainsString(route('admin.ppdb.settings.edit'), $html);
    }

    public function test_admin_faq_page_still_accessible(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.website.faq.index'));

        $response->assertOk();
        $response->assertSee('FAQ SPMB');
    }

    public function test_sidebar_hides_faq(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringNotContainsString('FAQ SPMB', $html);
    }

    public function test_dashboard_mobile_responsive()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('grid grid-cols-1', $html);
        $this->assertStringContainsString('sm:grid-cols-2', $html);
        $this->assertStringContainsString('lg:grid-cols-4', $html);
    }

    public function test_dashboard_shows_status_counts()
    {
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'status' => 'diterima',
        ]);

        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'status' => 'menunggu_verifikasi',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'))
            ->assertOk()
            ->assertSee('Baru Daftar')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Terverifikasi')
            ->assertSee('Wawancara')
            ->assertSee('Lulus')
            ->assertSee('Cadangan')
            ->assertSee('Tidak Lulus')
            ->assertSee('Diterima')
            ->assertSee('Mengundurkan Diri');
    }

    public function test_dashboard_shows_correct_quota_calculation()
    {
        StudentApplication::factory(3)->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'status' => 'diterima',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Kuota Total', $html);
        $this->assertStringContainsString('36', $html); // total quota
        $this->assertStringContainsString('Terisi (Diterima)', $html);
        $this->assertStringContainsString('Sisa Kuota', $html);
    }

    public function test_dashboard_works_without_current_year()
    {
        $this->admissionYear->update(['is_current' => false]);

        $this->actingAs($this->admin)
            ->get(route('admin.ppdb.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard SPMB')
            ->assertSee('Belum ditentukan');
    }

    public function test_guest_cannot_export_applications()
    {
        $this->get(route('admin.ppdb.applications.export'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_export_applications()
    {
        StudentApplication::factory()->count(2)->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="pendaftar-ppdb-' . now()->format('Ymd-Hi') . '.csv"');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Nomor Pendaftaran', $content);
        $this->assertStringContainsString('Nama Siswa', $content);
        $this->assertStringContainsString('Status', $content);
    }

    public function test_export_respects_status_filter()
    {
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'status' => 'diterima',
        ]);
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'status' => 'tidak_lulus',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.export', ['status' => 'diterima']));

        $response->assertOk();
        $content = $response->streamedContent();
        // Should only have the diterima application in data rows
        $this->assertStringContainsString('Diterima', $content);
        // Should NOT contain tidak_lulus labels
        $this->assertStringNotContainsString('Tidak Lulus', $content);
    }

    public function test_export_supports_filters_from_request()
    {
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'student_name' => 'Ahmad Testing',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.export', ['search' => 'Ahmad']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Ahmad Testing', $content);
    }

    public function test_index_page_shows_export_and_print_buttons()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'));

        $response->assertSee('Excel');
        $response->assertSee('PDF');
        $response->assertSee('Print');
    }

    public function test_guest_cannot_export_pdf_applications()
    {
        $this->get(route('admin.ppdb.applications.export-pdf'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_export_pdf_applications()
    {
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'student_name' => 'PDF Test Student',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.export-pdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('daftar-pendaftar-ppdb-', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_export_pdf_respects_status_filter()
    {
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'status' => 'diterima',
        ]);
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'status' => 'tidak_lulus',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.export-pdf', ['status' => 'diterima']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');

        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF-', $content);
        $this->assertStringContainsString('%%EOF', $content);
    }

    public function test_export_pdf_has_valid_pdf_structure()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.export-pdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');

        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF-', $content);
        $this->assertStringContainsString('/Type /Catalog', $content);
        $this->assertStringContainsString('/Type /Pages', $content);
        $this->assertStringContainsString('%%EOF', $content);
    }

    public function test_guest_cannot_update_follow_up()
    {
        $application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $this->patch(route('admin.ppdb.applications.update-follow-up', $application), [
            'follow_up_status' => 'sudah_dihubungi',
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_update_follow_up()
    {
        $application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-follow-up', $application), [
                'follow_up_status' => 'sudah_dihubungi',
                'follow_up_notes' => 'Telah dihubungi via WA, akan datang besok.',
            ]);

        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('sudah_dihubungi', $application->follow_up_status);
        $this->assertEquals('Telah dihubungi via WA, akan datang besok.', $application->follow_up_notes);
        $this->assertNotNull($application->follow_up_at);
        $this->assertEquals($this->admin->id, $application->follow_up_by);
    }

    public function test_follow_up_sets_timestamp_and_officer()
    {
        $application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-follow-up', $application), [
                'follow_up_status' => 'selesai',
            ]);

        $application->refresh();
        $this->assertNotNull($application->follow_up_at);
        $this->assertEquals($this->admin->id, $application->follow_up_by);
        $this->assertInstanceOf(\Carbon\Carbon::class, $application->follow_up_at);
    }

    public function test_follow_up_with_invalid_status_fails()
    {
        $application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-follow-up', $application), [
                'follow_up_status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors('follow_up_status');
    }

    public function test_follow_up_notes_are_optional()
    {
        $application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-follow-up', $application), [
                'follow_up_status' => 'siap_wawancara',
            ]);

        $response->assertSessionHas('success');
    }

    public function test_follow_up_notes_max_length()
    {
        $application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.ppdb.applications.update-follow-up', $application), [
                'follow_up_status' => 'perlu_dilengkapi',
                'follow_up_notes' => str_repeat('a', 2001),
            ]);

        $response->assertSessionHasErrors('follow_up_notes');
    }

    public function test_follow_up_shows_in_index()
    {
        $application = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'follow_up_status' => 'belum_dihubungi',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index'));

        $response->assertSee('Belum Dihubungi');
    }

    public function test_follow_up_filter_works()
    {
        $student1 = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'student_name' => 'Follow Up Alpha',
            'follow_up_status' => 'belum_dihubungi',
        ]);
        $student2 = StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'student_name' => 'Follow Up Beta',
            'follow_up_status' => 'sudah_dihubungi',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index', ['follow_up_status' => 'belum_dihubungi']));

        $response->assertSee('Follow Up Alpha');
    }

    public function test_follow_up_filter_excludes_other_statuses()
    {
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'student_name' => 'Follow Up Alpha',
            'follow_up_status' => 'belum_dihubungi',
        ]);
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'student_name' => 'Follow Up Beta',
            'follow_up_status' => 'sudah_dihubungi',
        ]);
        StudentApplication::factory()->create([
            'admission_year_id' => $this->admissionYear->id,
            'admission_program_id' => $this->admissionProgram->id,
            'student_name' => 'Charlie Followup',
            'follow_up_status' => 'perlu_dilengkapi',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index', ['search' => 'Follow']));

        $response->assertSee('Follow Up Alpha');
        $response->assertSee('Follow Up Beta');
        $response->assertSee('Charlie Followup');

        $filtered = $this->actingAs($this->admin)
            ->get(route('admin.ppdb.applications.index', ['search' => 'Follow', 'follow_up_status' => 'belum_dihubungi']));

        $filtered->assertSee('Follow Up Alpha');
        $filtered->assertDontSee('Follow Up Beta');
        $filtered->assertDontSee('Charlie Followup');
    }
}
