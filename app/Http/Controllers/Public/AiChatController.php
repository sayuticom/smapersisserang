<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionProgram;
use App\Models\AdmissionYear;
use App\Models\SchoolSetting;
use App\Services\OpenAIChatService;
use Illuminate\Http\Request;

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

        if (!$chatService->isConfigured()) {
            return response()->json([
                'reply' => 'Maaf, layanan chat AI sedang tidak tersedia. Silakan hubungi panitia SPMB melalui WhatsApp atau telepon.',
            ]);
        }

        $context = $this->buildContext();

        $reply = $chatService->chat($message, $context);

        if ($reply === null) {
            return response()->json([
                'reply' => 'Maaf, saya mengalami kendala teknis. Silakan hubungi panitia SPMB untuk informasi lebih lanjut.',
            ]);
        }

        return response()->json([
            'reply' => $reply,
        ]);
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
