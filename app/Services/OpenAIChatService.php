<?php

namespace App\Services;

use App\Models\AiFaq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIChatService
{
    protected string $apiKey;
    protected string $model;

    protected array $stopwords = [
        'apa', 'itu', 'siapa', 'dimana', 'kapan', 'mengapa', 'bagaimana',
        'yang', 'ini', 'itu', 'di', 'ke', 'dari', 'dan', 'atau', 'dengan',
        'tidak', 'ada', 'bisa', 'akan', 'sudah', 'belum', 'apakah', 'saya',
        'kami', 'kita', 'anda', 'dia', 'mereka', 'untuk', 'dalam', 'pada',
        'sebagai', 'oleh', 'secara', 'ya', 'indonesia', 'sma',
        'serang', 'sekolah',
    ];

    protected array $suffixPatterns = [
        '/^(.*)nya$/u',
        '/^(.*)kan$/u',
        '/^(.*)kah$/u',
        '/^(.*)lah$/u',
        '/^(.*)ku$/u',
        '/^(.*)mu$/u',
    ];

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

        $faqs = $this->searchRelevantFaqs($message);

        $systemPrompt = $this->buildSystemPrompt($context, $faqs);

        Log::info('AI Chat context', [
            'message' => $message,
            'faq_count' => $faqs->count(),
            'faq_questions' => $faqs->pluck('question')->values(),
        ]);

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

    public function searchRelevantFaqs(string $message, int $limit = 5)
    {
        $faqs = AiFaq::active()->orderBy('sort_order')->orderBy('id')->get();

        if ($faqs->isEmpty()) {
            return collect();
        }

        $queryTokens = $this->tokenize($message);

        if (empty($queryTokens)) {
            return collect();
        }

        $scored = [];
        foreach ($faqs as $faq) {
            $faqTokens = $this->tokenize($faq->question . ' ' . $faq->category . ' ' . $faq->answer);
            $score = $this->calculateScore($queryTokens, $faqTokens, $faq);
            $scored[] = ['faq' => $faq, 'score' => $score];
        }

        usort($scored, fn($a, $b) => 
            $b['score'] <=> $a['score'] ?: 
            strlen($a['faq']->question) <=> strlen($b['faq']->question)
        );

        $filtered = array_filter($scored, fn($s) => $s['score'] > 0.3);

        return collect(array_slice(array_map(fn($s) => $s['faq'], $filtered), 0, $limit));
    }

    protected function tokenize(string $text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, fn($w) => strlen($w) >= 2);

        $result = [];
        foreach ($words as $word) {
            $normalized = $this->normalize($word);
            if ($normalized !== null) {
                $result[] = $normalized;
            }
        }

        return array_values($result);
    }

    protected function normalize(string $word): ?string
    {
        $word = trim($word);
        if (in_array($word, $this->stopwords, true)) {
            return null;
        }
        foreach ($this->suffixPatterns as $pattern) {
            if (preg_match($pattern, $word, $matches)) {
                $word = $matches[1];
                break;
            }
        }
        if (strlen($word) <= 1) {
            return null;
        }
        return $word;
    }

    protected function calculateScore(array $queryTokens, array $faqTokens, $faq): float
    {
        $queryCount = count($queryTokens);
        if ($queryCount === 0) {
            return 0;
        }

        $exactMatches = 0;
        $stemMatches = 0;

        foreach ($queryTokens as $qToken) {
            $foundExact = false;
            $foundStem = false;
            foreach ($faqTokens as $fToken) {
                if ($qToken === $fToken) {
                    $foundExact = true;
                } elseif (!$foundExact && !$foundStem && strlen($fToken) >= 4 && strlen($qToken) > strlen($fToken) && str_starts_with($qToken, $fToken)) {
                    $foundStem = true;
                }
            }
            if ($foundExact) {
                $exactMatches++;
            } elseif ($foundStem) {
                $stemMatches++;
            }
        }

        $score = ($exactMatches * 2) + ($stemMatches * 1);

        $score /= ($queryCount * 2);

        $faqQuestionTokens = $this->tokenize($faq->question);
        $questionTokenCount = count($faqQuestionTokens);
        if ($questionTokenCount > 0 && $exactMatches > 0) {
            $coverage = $exactMatches / $questionTokenCount;
            $score += $coverage * 0.3;
        }

        $priorityKeywords = [
            'syarat', 'biaya', 'kuota', 'asrama', 'whatsapp', 'wa', 'kontak',
            'daftar', 'gratis', 'free', 'spmb', 'keunggulan', 'program',
            'berkembang', 'informasi', 'daftar', 'pendaftaran',
            'persis', 'persatuan',
        ];

        foreach ($queryTokens as $qToken) {
            if (in_array($qToken, $priorityKeywords, true)) {
                $question = strtolower($faq->question);
                $cleanQuestion = preg_replace('/[^a-z0-9\s]/', ' ', $question);
                $questionWords = preg_split('/\s+/', trim($cleanQuestion));
                foreach ($questionWords as $word) {
                    if ($qToken === $word || str_starts_with($word, $qToken)) {
                        $score += 0.5;
                        break;
                    }
                }
            }
        }

        return $score;
    }

    protected function buildSystemPrompt(array $context, $faqs): string
    {
        $school = $context['school'] ?? [];
        $year = $context['admission_year'] ?? [];
        $program = $context['admission_program'] ?? [];

        $info = "INFORMASI SEKOLAH:\n";

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
        if (!empty($school['whatsapp_number'])) {
            $info .= "- WhatsApp Panitia: {$school['whatsapp_number']}\n";
        }
        if (!empty($school['email'])) {
            $info .= "- Email: {$school['email']}\n";
        }

        if (!empty($year)) {
            $info .= "\nPENDAFTARAN:\n";
            if (!empty($year['academic_year'])) {
                $info .= "- Tahun Ajaran: {$year['academic_year']}\n";
            }
            if (!empty($year['quota'])) {
                $info .= "- Kuota: {$year['quota']} siswa\n";
            }
            if (!empty($year['start_date'])) {
                $info .= "- Dibuka: {$year['start_date']}\n";
            }
            if (!empty($year['end_date'])) {
                $info .= "- Ditutup: {$year['end_date']}\n";
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
            $info .= "\nPROGRAM:\n";
            if (!empty($program['name'])) {
                $info .= "- Nama: {$program['name']}\n";
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
                $info .= "- Kuota: {$program['quota']} siswa\n";
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

        $faqSection = '';
        if ($faqs->isNotEmpty()) {
            $faqSection = "\nKONTEKS FAQ RESMI:\n";
            foreach ($faqs as $j => $faq) {
                $faqSection .= ($j + 1) . ". P: {$faq->question}\n   J: {$faq->answer}\n";
            }
        }

        return <<<PROMPT
Kamu adalah asisten resmi SMA Persis Serang untuk layanan SPMB (Sistem Penerimaan Murid Baru).

Tugasmu:
- Jawab pertanyaan calon siswa atau orang tua dengan ramah, singkat, jelas, dan akurat.
- Gunakan bahasa Indonesia yang alami dan mudah dipahami.
- Prioritaskan jawaban berdasarkan konteks informasi resmi dan FAQ yang diberikan.
- Jangan mengarang informasi yang tidak ada dalam konteks.
- Jika data kurang lengkap atau ragu, arahkan pengguna untuk menghubungi panitia SPMB di WhatsApp 089661234569.
- Gunakan istilah "SPMB", bukan "PPDB".
- Jika pertanyaan di luar konteks pendaftaran SMA Persis Serang, tolak dengan sopan dan ajak kembali ke topik SPMB.

{$info}
{$faqSection}
PROMPT;
    }
}
