<?php

namespace App\Http\Controllers;

use App\Models\StoreAnnouncement;
use App\Models\Staff;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreAnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements for the storekeeper.
     */
    public function index()
    {
        $announcements = StoreAnnouncement::with('creator')
            ->latest()
            ->paginate(10);

        if (request()->routeIs('super_admin.announcements.index')) {
            return view('dashboard.super-admin.announcements', compact('announcements'));
        }

        return view('dashboard.storekeeper.announcements', compact('announcements'));
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'target_role' => 'required|in:head_chef,bar_keeper,housekeeper,both,all,reception,cashier,accountant,manager,storekeeper,waiter',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $announcement = StoreAnnouncement::create([
            'message' => $request->message,
            'target_role' => $request->target_role,
            'created_by' => Auth::guard('staff')->id(),
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        $message = $request->routeIs('super_admin.announcements.store')
            ? 'Notice published. It is scrolling under the header.'
            : 'Announcement broadcasted successfully.';

        if ($request->boolean('send_sms') && $request->routeIs('super_admin.announcements.store')) {
            $message .= ' ' . $this->sendAnnouncementSms($announcement);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle the active status of an announcement.
     */
    public function toggleStatus(StoreAnnouncement $announcement)
    {
        $announcement->update([
            'is_active' => !$announcement->is_active,
        ]);

        $status = $announcement->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Announcement $status successfully.");
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(StoreAnnouncement $announcement)
    {
        $announcement->delete();
        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Text the notice to active staff in the selected audience.
     */
    private function sendAnnouncementSms(StoreAnnouncement $announcement): string
    {
        $staff = $this->staffForAnnouncement($announcement->target_role);
        $withPhone = $staff->filter(fn ($person) => filled($person->phone));
        $missingPhone = $staff->count() - $withPhone->count();

        if ($withPhone->isEmpty()) {
            return 'No SMS was sent. None of the selected staff have a phone number.';
        }

        $sms = app(SmsService::class);
        $text = 'Notice: ' . $announcement->message;
        $sent = 0;
        $failed = 0;

        foreach ($withPhone->unique('phone') as $person) {
            $result = $sms->sendSms($person->phone, $text);
            if (!empty($result['success'])) {
                $sent++;
            } else {
                $failed++;
            }
        }

        $summary = "SMS sent to {$sent} staff.";
        if ($failed > 0) {
            $summary .= " {$failed} failed.";
        }
        if ($missingPhone > 0) {
            $summary .= " {$missingPhone} had no phone number.";
        }

        return $summary;
    }

    private function staffForAnnouncement(string $role)
    {
        $query = Staff::query()
            ->where(function ($q) {
                $q->where('is_active', 1)->orWhereNull('is_active');
            });

        if ($role === 'both') {
            $query->whereIn('role', ['head_chef', 'bar_keeper']);
        } elseif ($role === 'bar_keeper') {
            $query->where(function ($q) {
                $q->where('role', 'bar_keeper')
                    ->orWhere('role', 'like', '%bar%')
                    ->orWhere('role', 'like', '%counter%');
            });
        } elseif ($role !== 'all') {
            $query->where('role', $role);
        }

        return $query->get(['id', 'phone', 'role']);
    }
}
