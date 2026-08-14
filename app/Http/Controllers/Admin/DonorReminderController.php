<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationRegularDonor;
use App\Services\DonorReminderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DonorReminderController extends Controller
{
    public function __construct(
        private readonly DonorReminderService $reminderService,
    ) {}

    public function index(Request $request): View
    {
        $query = DonationRegularDonor::query()
            ->orderByDesc('reminder_enabled')
            ->orderByRaw('COALESCE(next_reminder_at, last_donation_at, created_at) asc')
            ->orderBy('name');

        if ($request->filled('q')) {
            $keyword = trim($request->string('q')->toString());
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('whatsapp_number', 'like', '%'.$keyword.'%');
            });
        }

        if ($request->input('status') === 'enabled') {
            $query->where('reminder_enabled', true);
        } elseif ($request->input('status') === 'disabled') {
            $query->where('reminder_enabled', false);
        }

        $donors = $query->paginate(20)->withQueryString();

        return view('admin.donor-reminders.index', [
            'donors' => $donors,
            'reminderService' => $this->reminderService,
            'dueCount' => $this->reminderService->dueCount(),
            'upcomingCount' => $this->reminderService->upcomingCount(),
            'activeCount' => $this->reminderService->activeCount(),
        ]);
    }

    public function update(Request $request, DonationRegularDonor $donor): RedirectResponse
    {
        $enabled = $request->boolean('reminder_enabled');

        $rules = [
            'reminder_enabled' => ['nullable', 'boolean'],
            'reminder_frequency' => [$enabled ? 'required' : 'nullable', Rule::in([DonorReminderService::FREQUENCY_MONTHLY])],
            'reminder_day' => [$enabled ? 'required' : 'nullable', 'integer', 'min:1', 'max:28'],
        ];

        $data = $request->validate($rules);

        if (! $enabled) {
            $donor->update([
                'reminder_enabled' => false,
                'reminder_frequency' => null,
                'reminder_day' => null,
                'next_reminder_at' => null,
            ]);

            return back()->with('success', 'Pengingat WhatsApp dinonaktifkan untuk '.$donor->name.'.');
        }

        $donor->fill([
            'reminder_enabled' => true,
            'reminder_frequency' => DonorReminderService::FREQUENCY_MONTHLY,
            'reminder_day' => (int) $data['reminder_day'],
        ]);
        $donor->next_reminder_at = $this->reminderService->nextReminderFor($donor);
        $donor->save();

        return back()->with('success', 'Pengaturan pengingat WhatsApp diperbarui untuk '.$donor->name.'.');
    }

    public function markReminded(DonationRegularDonor $donor, Request $request): RedirectResponse
    {
        if (! $donor->reminder_enabled) {
            return back()->with('error', 'Pengingat WhatsApp belum aktif untuk donatur ini.');
        }

        $message = $request->input('message_snapshot') ?: $this->reminderService->defaultMessage($donor);
        $this->reminderService->markReminded($donor, $request->user(), $message);

        return back()->with('success', 'Donatur ditandai sudah diingatkan.');
    }
}
