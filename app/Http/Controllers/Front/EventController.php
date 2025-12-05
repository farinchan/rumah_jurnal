<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventAttendanceUser;
use App\Models\EventUser;
use App\Models\SettingWebsite;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class EventController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => __('front.agenda') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.agenda') . ' | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.agenda'),
                    'link' => route('event.index')
                ]
            ],
            'setting_web' => $setting_web,

            'list_event' => Event::latest()->where('is_active', true)->where('access', 'terbuka')->paginate(10),
        ];

        return view('front.pages.event.index', $data);
    }

    public function show($slug)
    {
        $setting_web = SettingWebsite::first();
        $event = Event::where('slug', $slug)->first();
        $data = [
            'title' => $event->title,
            'meta' => [
                'title' => $event->title . ' | ' . $setting_web->name,
                'description' => strip_tags($event->content),
                'keywords' => $setting_web->name . ', ' . $event->title . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $event->image ?? $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.agenda'),
                    'link' => route('event.index')
                ],
                [
                    'name' => 'Detail',
                    'link' => route('event.show', $event->slug)
                ]
            ],
            'setting_web' => $setting_web,
            'event_latest' => Event::latest()->take(6)->get(),
            'check_registered' => Auth::check() ? $event->users()->where('user_id', Auth::id())->exists() : false,
            'eticket' => Auth::check() ? $event->users()->where('user_id', Auth::id())->first() : null,

            'event' => $event,
        ];

        return view('front.pages.event.show', $data);
    }

    public function register(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return redirect()->back()->with('error', 'Event not found');
        }

        if (!Auth::check()) {
            Alert::warning('Login Required', 'Please login to register for this event.');
            return redirect()->route('login');
        }

        if ($event->is_active == false) {
            Alert::error('Error', 'Event is not active');
            return redirect()->route('event.show', $event->slug);
        }

        if ($event->access == 'tertutup') {
            Alert::error('Error', 'Event is closed');
            return redirect()->route('event.show', $event->slug);
        }

        if ($event->limit) {
            $count_registered = $event->users()->count();
            if ($count_registered >= $event->limit) {
                Alert::error('Error', 'Event registration is full');
                return redirect()->route('event.show', $event->slug);
            }
        }


        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^\+[1-9][0-9]{0,18}$/'
                ],
            ],
            [
                'name.required' => 'Nama lengkap harus diisi.',
                'email.required' => 'Email harus diisi.',
                'email.email' => 'Format email tidak valid.',
                'phone.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            ]
        );

        if ($validator->fails()) {
            Alert::error('Registration Failed', 'Please check the form for errors.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create or update the event user registration
        $event->users()->updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]
        );

        $user = User::find(Auth::id());
        if (!$user->phone) {
            $user->phone = $request->phone;
            $user->save();
        }
        Alert::success('Registration Successful', 'You have successfully registered for the event ' . $event->name . ' Please check your email/WhatsApp for further information.');
        return redirect()->route('event.show', $event->slug)->with('success', 'You have successfully registered for the event ' . $event->name . '. Please check your email/WhatsApp for further information.');
    }

    public function eticket($uuid)
    {
        $eventUser = EventUser::with(['event', 'user'])->find($uuid);
        if (!$eventUser) {
            Alert::error('Error', 'Event user not found');
            return redirect()->route('event.index');
        }

        // return response()->json($eventUser);

        return view('front.pages.event.e_ticket', ['eventUser' => $eventUser]);
    }

    public function certificate($uuid)
    {
        $eventUser = EventUser::find($uuid);
        $data = [
            'event_name' => $eventUser->event->name ?? '',
            'participant_name' => $eventUser->name ?? '',
            'event_date' => $eventUser->event->datetime ?? '',
            'certificate_number' => str_pad($eventUser->event->id, 4, '0', STR_PAD_LEFT),

        ];
        $pdf = Pdf::loadView('front.pages.event.certificate', $data)->setPaper('A4', 'landscape');
        // return $pdf->download('sertifikat_' . $eventUser->id . '.pdf');
        return $pdf->stream('sertifikat_' . $eventUser->id . '.pdf');

    }

    public function presence($code)
    {
        if (!Auth::check()) {
            Alert::warning('Login Required', 'Please login to mark your attendance.');
            return redirect()->route('login');
        }
        $event_attendance = EventAttendance::where('code', $code)->with(['event'])->first();
        if (!$event_attendance) {
            Alert::error('Error', 'Attendance code not found');
            return redirect()->route('home');
        }
        if ($event_attendance->event->is_active == false) {
            Alert::error('Error', 'Event is not active');
            return redirect()->route('home');
        }

        $start = \Carbon\Carbon::parse($event_attendance->start_datetime);
        $end   = \Carbon\Carbon::parse($event_attendance->end_datetime);

        // Absensi belum dibuka
        if ($start->isFuture()) {
            Alert::error('Error', 'Absence for this event is not yet open');
            return redirect()->route('home');
        }

        // Absensi sudah ditutup
        if ($end->isPast()) {
            Alert::error('Error', 'Absence for this event has been closed');
            return redirect()->route('home');
        }

        $user = Auth::user();
        $event_user = EventUser::where('event_id', $event_attendance->event_id)
            ->where('user_id', $user->id)
            ->first();
        if (!$event_user) {
            Alert::error('Error', 'You are not registered for this event');
            return redirect()->route('home');
        }

        $setting_web = SettingWebsite::first();
        $data = [
            'title' => 'Event Attendance | ' . $event_attendance->event->name,
            'meta' => [
                'title' => 'Event Attendance | ' . $event_attendance->event->name,
                'description' => strip_tags($event_attendance->description),
                'keywords' => $event_attendance->event->name . ', Event, Attendance',
                'favicon' => $event_attendance->event->thumbnail
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => 'Event Attendance',
                    'link' => route('event.presence', $code)
                ]
            ],
            'setting_web' => $setting_web,
            'event_attendance' => $event_attendance,
            'attendance_check' => $event_user->Attendances()->where('event_attendance_id', $event_attendance->id)->first(),
        ];
        return view('front.pages.event.presence', $data);
    }

    public function presenceStore(Request $request, $code)
    {
        if (!Auth::check()) {
            Alert::warning('Login Required', 'Please login to mark your attendance.');
            return redirect()->route('login');
        }
        $event_attendance = EventAttendance::where('code', $code)->with(['event'])->first();
        if (!$event_attendance) {
            Alert::error('Error', 'Attendance code not found');
            return redirect()->route('home');
        }
        if ($event_attendance->event->is_active == false) {
            Alert::error('Error', 'Event is not active');
            return redirect()->route('home');
        }
        $start = \Carbon\Carbon::parse($event_attendance->start_datetime);
        $end   = \Carbon\Carbon::parse($event_attendance->end_datetime);

        // Absensi belum dibuka
        if ($start->isFuture()) {
            Alert::error('Error', 'Absence for this event is not yet open');
            return redirect()->route('home');
        }

        // Absensi sudah ditutup
        if ($end->isPast()) {
            Alert::error('Error', 'Absence for this event has been closed');
            return redirect()->route('home');
        }

        $user = Auth::user();
        $event_user = EventUser::where('event_id', $event_attendance->event_id)
            ->where('user_id', $user->id)
            ->first();
        if (!$event_user) {
            Alert::error('Error', 'You are not registered for this event');
            return redirect()->route('home');
        }


        $validator = Validator::make(
            $request->all(),
            [
                'notes' => 'nullable|string',
            ],
            [
                'notes.string' => 'Notes must be a string.',
            ]
        );

        if ($validator->fails()) {
            Alert::error('Attendance Failed', 'Please check the form for errors.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create or update the event user registration
        EventAttendanceUser::updateOrCreate(
            [
                'event_attendance_id' => $event_attendance->id,
                'event_user_id' => $event_user->id
            ],
            [
                'attendance_datetime' => now(),
                'notes' => $request->notes,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),

            ]
        );

        Alert::success('Attendance Successful', 'You have successfully registered your presence for the event ' . $event_attendance->event->name . '.');
        return redirect()->route('event.presence', $code)->with('success', 'You have successfully registered your presence for the event ' . $event_attendance->event->name . '.');
    }
}
