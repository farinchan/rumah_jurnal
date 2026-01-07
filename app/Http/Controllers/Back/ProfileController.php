<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\EventUser;
use App\Models\EventAttendanceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource (Overview).
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's event statistics
        $totalEvents = EventUser::where('user_id', $user->id)->count();

        // Get attendance count through event_users table
        $eventUserIds = EventUser::where('user_id', $user->id)->pluck('id');
        $totalAttendances = EventAttendanceUser::whereIn('event_user_id', $eventUserIds)->count();

        // Get recent events
        $recentEvents = EventUser::with('event')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $data = [
            'title' => 'Profil Saya',
            'breadcrumbs' => [
                [
                    'name' => 'Profil Saya',
                    'link' => route('back.profile.index')
                ]
            ],
            'user' => $user,
            'totalEvents' => $totalEvents,
            'recentEvents' => $recentEvents,
            'activeTab' => 'overview',
        ];

        return view('back.pages.profile.index', $data);
    }

    /**
     * Display the settings page.
     */
    public function settings()
    {
        $user = Auth::user();

        $data = [
            'title' => 'Pengaturan Profil',
            'breadcrumbs' => [
                [
                    'name' => 'Profil Saya',
                    'link' => route('back.profile.index')
                ],
                [
                    'name' => 'Pengaturan',
                    'link' => route('back.profile.settings')
                ]
            ],
            'user' => $user,
            'activeTab' => 'settings',
        ];

        return view('back.pages.profile.settings', $data);
    }

    /**
     * Display the events page.
     */
    public function events()
    {
        $user = Auth::user();

        // Get all user's events with pagination, include event attendances
        $events = EventUser::with(['event.attendances', 'Attendances.eventAttendance'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get event user IDs for this user
        $eventUserIds = EventUser::where('user_id', $user->id)->pluck('id');

        // Get attendance records through event_users
        $attendances = EventAttendanceUser::with(['eventAttendance.event', 'eventUser'])
            ->whereIn('event_user_id', $eventUserIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'title' => 'Event Saya',
            'breadcrumbs' => [
                [
                    'name' => 'Profil Saya',
                    'link' => route('back.profile.index')
                ],
                [
                    'name' => 'Event',
                    'link' => route('back.profile.events')
                ]
            ],
            'user' => $user,
            'events' => $events,
            'attendances' => $attendances,
            'activeTab' => 'events',
        ];

        return view('back.pages.profile.events', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:laki-laki,perempuan',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sinta_id' => 'nullable|string|max:255',
            'scopus_id' => 'nullable|string|max:255',
            'google_scholar' => 'nullable|string|max:255',
        ]);

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'sinta_id' => $request->sinta_id,
            'scopus_id' => $request->scopus_id,
            'google_scholar' => $request->google_scholar,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && !str_starts_with($user->photo, 'http')) {
                Storage::delete('public/' . $user->photo);
            }

            $photoPath = $request->file('photo')->store('photos/users', 'public');
            $userData['photo'] = $photoPath;
        }

        $user->update($userData);

        return redirect()->route('back.profile.settings')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('back.profile.settings')->with('success', 'Password berhasil diperbarui!');
    }
}
