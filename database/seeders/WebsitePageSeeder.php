<?php

namespace Database\Seeders;

use App\Models\WebsitePage;
use Illuminate\Database\Seeder;

class WebsitePageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'page_key' => 'home',
                'title' => 'SMA Persis Serang',
                'subtitle' => 'Islamic Boarding School Berbasis Akhlak dan Teknologi',
                'content' => 'SMA Persis Serang menghadirkan pendidikan Islam berasrama yang menguatkan akhlak, ilmu, kemandirian, dan literasi teknologi dalam lingkungan yang aman dan terarah.',
                'button_primary_text' => 'Daftar SPMB',
                'button_primary_url' => '/spmb/daftar',
                'button_secondary_text' => 'Cek Status',
                'button_secondary_url' => '/spmb/cek-status',
                'meta_description' => 'SMA Persis Serang, Islamic Boarding School berbasis akhlak dan teknologi di Serang, Banten.',
                'is_active' => true,
            ],
            [
                'page_key' => 'profile',
                'title' => 'Profil Sekolah',
                'subtitle' => 'Mengenal lebih dekat SMA Persis Serang',
                'content' => 'SMA Persis Serang adalah sekolah Islam berasrama yang membina siswa agar berilmu, beradab, mandiri, dan siap menghadapi perkembangan zaman.',
                'meta_description' => 'Profil SMA Persis Serang sebagai sekolah Islam berasrama di Serang, Banten.',
                'is_active' => true,
            ],
            [
                'page_key' => 'program',
                'title' => 'Program Pendidikan',
                'subtitle' => 'Kurikulum dan pembinaan yang memadukan akhlak, keislaman, kemandirian, dan teknologi.',
                'content' => 'Program pendidikan SMA Persis Serang memadukan kurikulum formal, pembinaan diniyah, pembiasaan ibadah, penguatan karakter, dan pembelajaran modern.',
                'button_primary_text' => 'Daftar SPMB',
                'button_primary_url' => '/spmb/daftar',
                'meta_description' => 'Program pendidikan SMA Persis Serang dengan kurikulum terpadu dan pembinaan boarding school.',
                'is_active' => true,
            ],
            [
                'page_key' => 'boarding',
                'title' => 'Islamic Boarding School',
                'subtitle' => 'Lingkungan pendidikan berasrama untuk membentuk akhlak, kemandirian, ibadah, dan kedisiplinan siswa.',
                'content' => 'Sistem boarding school membantu siswa membangun kebiasaan baik melalui pembinaan harian, ibadah berjamaah, adab, belajar mandiri, dan kehidupan sosial yang terarah.',
                'button_primary_text' => 'Daftar SPMB',
                'button_primary_url' => '/spmb/daftar',
                'meta_description' => 'Boarding school SMA Persis Serang untuk pembinaan akhlak, ibadah, kemandirian, dan kedisiplinan.',
                'is_active' => true,
            ],
            [
                'page_key' => 'gallery',
                'title' => 'Galeri Sekolah',
                'subtitle' => 'Dokumentasi lingkungan, kegiatan, dan pembinaan siswa.',
                'content' => 'Lihat dokumentasi lingkungan sekolah, kegiatan pembelajaran, aktivitas asrama, dan pembinaan siswa SMA Persis Serang.',
                'meta_description' => 'Galeri kegiatan dan lingkungan SMA Persis Serang.',
                'is_active' => true,
            ],
            [
                'page_key' => 'figures',
                'title' => 'Tokoh & Pembina',
                'subtitle' => 'Orang-orang yang membersamai pembinaan dan pengembangan SMA Persis Serang.',
                'content' => 'Tokoh, pembina, dan pengelola sekolah berperan dalam menjaga arah pendidikan, pembinaan, dan budaya Islami di SMA Persis Serang.',
                'meta_description' => 'Tokoh dan pembina SMA Persis Serang.',
                'is_active' => true,
            ],
            [
                'page_key' => 'teachers',
                'title' => 'Profil Guru',
                'subtitle' => 'Tenaga pendidik profesional yang berdedikasi tinggi',
                'content' => 'Guru SMA Persis Serang mendampingi proses belajar siswa dengan pendekatan akademik, pembinaan karakter, dan keteladanan.',
                'meta_description' => 'Profil guru dan tenaga pendidik SMA Persis Serang.',
                'is_active' => true,
            ],
            [
                'page_key' => 'contact',
                'title' => 'Kontak',
                'subtitle' => 'Hubungi kami untuk informasi lebih lanjut',
                'content' => 'Hubungi SMA Persis Serang untuk informasi SPMB, program boarding school, kunjungan sekolah, dan kerja sama.',
                'button_primary_text' => 'Daftar SPMB',
                'button_primary_url' => '/ppdb/daftar',
                'meta_description' => 'Kontak SMA Persis Serang untuk informasi sekolah dan SPMB.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            WebsitePage::updateOrCreate(
                ['page_key' => $page['page_key']],
                $page
            );
        }
    }
}
