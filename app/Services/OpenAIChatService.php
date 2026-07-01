<?php

namespace App\Services;

use App\Models\AiFaq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIChatService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function chat(string $message, array $context): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        try {
            $response = Http::timeout(30)
                ->withToken($this->apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $message],
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::warning('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    protected function buildSystemPrompt(array $context): string
    {
        $school = $context['school'] ?? [];
        $year = $context['admission_year'] ?? [];
        $program = $context['admission_program'] ?? [];

        $info = "Informasi SMA Persis Serang:\n";

        if (!empty($school['school_name'])) {
            $info .= "- Nama Sekolah: {$school['school_name']}\n";
        }
        if (!empty($school['tagline'])) {
            $info .= "- Tagline: {$school['tagline']}\n";
        }
        if (!empty($school['description'])) {
            $info .= "- Deskripsi: {$school['description']}\n";
        }
        if (!empty($school['address'])) {
            $info .= "- Alamat: {$school['address']}\n";
        }
        if (!empty($school['city'])) {
            $info .= "- Kota: {$school['city']}\n";
        }
        if (!empty($school['province'])) {
            $info .= "- Provinsi: {$school['province']}\n";
        }
        if (!empty($school['whatsapp_number'])) {
            $info .= "- WhatsApp: {$school['whatsapp_number']}\n";
        }
        if (!empty($school['email'])) {
            $info .= "- Email: {$school['email']}\n";
        }

        if (!empty($year)) {
            $info .= "\nTahun Ajaran Pendaftaran:\n";
            if (!empty($year['academic_year'])) {
                $info .= "- Tahun: {$year['academic_year']}\n";
            }
            if (!empty($year['name'])) {
                $info .= "- Nama: {$year['name']}\n";
            }
            if (!empty($year['quota'])) {
                $info .= "- Kuota: {$year['quota']} siswa\n";
            }
            if (!empty($year['start_date'])) {
                $info .= "- Pendaftaran dibuka: {$year['start_date']}\n";
            }
            if (!empty($year['end_date'])) {
                $info .= "- Pendaftaran ditutup: {$year['end_date']}\n";
            }
            if (!empty($year['status'])) {
                $statusLabel = [
                    'draft' => 'Belum dibuka',
                    'open' => 'Dibuka',
                    'almost_full' => 'Hampir penuh',
                    'quota_full' => 'Kuota penuh',
                    'closed' => 'Ditutup',
                    'announcement' => 'Pengumuman',
                ];
                $info .= "- Status: " . ($statusLabel[$year['status']] ?? $year['status']) . "\n";
            }
        }

        if (!empty($program)) {
            $info .= "\nProgram Pendaftaran:\n";
            if (!empty($program['name'])) {
                $info .= "- Nama Program: {$program['name']}\n";
            }
            if (!empty($program['type'])) {
                $typeLabel = [
                    'first_batch_free' => 'Gratis Angkatan Pertama',
                    'regular_paid' => 'Reguler Berbayar',
                    'scholarship' => 'Beasiswa',
                    'subsidy' => 'Subsidi',
                ];
                $info .= "- Tipe: " . ($typeLabel[$program['type']] ?? $program['type']) . "\n";
            }
            if (!empty($program['quota'])) {
                $info .= "- Kuota Program: {$program['quota']} siswa\n";
            }
            if (!empty($program['description'])) {
                $info .= "- Deskripsi: {$program['description']}\n";
            }
            if (!empty($program['benefits'])) {
                $info .= "- Benefit: {$program['benefits']}\n";
            }
            if (!empty($program['requirements'])) {
                $info .= "- Syarat: {$program['requirements']}\n";
            }
            if (isset($program['is_free_program'])) {
                $info .= "- Program Gratis: " . ($program['is_free_program'] ? 'Ya' : 'Tidak') . "\n";
            }
            if (!empty($program['registration_fee'])) {
                $info .= "- Biaya Pendaftaran: Rp " . number_format($program['registration_fee'], 0, ',', '.') . "\n";
            }
            if (!empty($program['tuition_fee'])) {
                $info .= "- SPP: Rp " . number_format($program['tuition_fee'], 0, ',', '.') . "\n";
            }
            if (!empty($program['boarding_fee'])) {
                $info .= "- Biaya Asrama: Rp " . number_format($program['boarding_fee'], 0, ',', '.') . "\n";
            }
        }

        $info .= "\nLink pendaftaran: " . route('spmb.create');

        $faqs = AiFaq::active()->orderBy('sort_order')->orderBy('id')->limit(20)->get();
        if ($faqs->isNotEmpty()) {
            $info .= "\n\nFAQ Resmi:\n";
            foreach ($faqs as $j => $faq) {
                $info .= ($j + 1) . ". Q: {$faq->question}\n   A: {$faq->answer}\n";
            }
        }

        return <<<PROMPT
Kamu adalah Asisten SPMB SMA Persis Serang.
Jawab dalam Bahasa Indonesia.
Gunakan gaya ramah, sopan, singkat, dan jelas.
Jawab hanya berdasarkan informasi resmi yang diberikan di konteks.
Jangan mengarang data.
Jika pertanyaan pengguna cocok dengan FAQ Resmi, prioritaskan jawaban dari FAQ tersebut. Jawaban boleh diringkas, tapi jangan mengubah makna jawaban resmi.
Jika informasi tidak tersedia, arahkan pengguna untuk menghubungi panitia SPMB.
Jika pertanyaan di luar konteks SMA Persis Serang atau SPMB, jawab singkat bahwa kamu hanya membantu konsultasi SPMB.

KONTEKS INFORMASI RESMI:
{$info}
PROMPT;
    }
}
