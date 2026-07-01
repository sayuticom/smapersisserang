<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionProgram;
use App\Models\AdmissionYear;
use App\Models\AiFaq;
use App\Models\SchoolSetting;
use App\Services\OpenAIChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function send(Request $request, OpenAIChatService $chatService)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = trim($validated['message']);

        if (empty($message)) {
            return response()->json([
                'reply' => 'Silakan ketik pertanyaan Anda.',
            ]);
        }

        $reply = null;

        if ($chatService->isConfigured()) {
            $context = $this->buildContext();
            $reply = $chatService->chat($message, $context);
        }

        if ($reply === null) {
            Log::info('AI Chat fallback to FAQ', ['message_length' => strlen($message)]);
            $reply = $this->searchFaq($message);
        }

        if ($reply === null) {
            $reply = 'Maaf, saya tidak dapat menemukan jawaban untuk pertanyaan Anda. Silakan hubungi panitia SPMB melalui WhatsApp 089661234569 untuk informasi lebih lanjut.';
        }

        return response()->json([
            'reply' => $reply,
        ]);
    }

    protected function searchFaq(string $message): ?string
    {
        $faqs = AiFaq::active()->orderBy('sort_order')->orderBy('id')->get();

        if ($faqs->isEmpty()) {
            return null;
        }

        $keywords = preg_split('/[\s,?.\-!]+/', strtolower($message));
        $keywords = array_filter($keywords, fn($w) => strlen($w) > 2);
        $keywords = array_values($keywords);

        if (empty($keywords)) {
            return null;
        }

        $bestMatch = null;
        $bestScore = 0;

        foreach ($faqs as $faq) {
            $text = strtolower($faq->question . ' ' . $faq->answer);
            $score = 0;
            foreach ($keywords as $word) {
                if (str_contains($text, $word)) {
                    $score++;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $faq;
            }
        }

        if ($bestMatch && $bestScore > 0) {
            return $bestMatch->answer;
        }

        return null;
    }

    protected function buildContext(): array
    {
        $schoolSetting = SchoolSetting::current();

        $school = [];
        if ($schoolSetting) {
            $school = [
                'school_name' => $schoolSetting->school_name,
                'tagline' => $schoolSetting->tagline,
                'description' => $schoolSetting->description,
                'address' => $schoolSetting->address,
                'city' => $schoolSetting->city,
                'province' => $schoolSetting->province,
                'whatsapp_number' => $schoolSetting->whatsapp_number,
                'email' => $schoolSetting->email,
            ];
        }

        $currentYear = AdmissionYear::where('is_current', true)->first();
        $year = [];
        if ($currentYear) {
            $year = [
                'name' => $currentYear->name,
                'academic_year' => $currentYear->academic_year,
                'quota' => $currentYear->quota,
                'status' => $currentYear->status,
                'start_date' => $currentYear->start_date?->format('d/m/Y'),
                'end_date' => $currentYear->end_date?->format('d/m/Y'),
            ];
        }

        $currentProgram = $currentYear?->programs()->first();
        $program = [];
        if ($currentProgram) {
            $program = [
                'name' => $currentProgram->name,
                'type' => $currentProgram->type,
                'quota' => $currentProgram->quota,
                'description' => $currentProgram->description,
                'benefits' => $currentProgram->benefits,
                'requirements' => $currentProgram->requirements,
                'is_free_program' => $currentProgram->is_free_program,
                'registration_fee' => $currentProgram->registration_fee,
                'tuition_fee' => $currentProgram->tuition_fee,
                'boarding_fee' => $currentProgram->boarding_fee,
                'meal_fee' => $currentProgram->meal_fee,
                'status' => $currentProgram->status,
            ];
        }

        return [
            'school' => $school,
            'admission_year' => $year,
            'admission_program' => $program,
        ];
    }
}
