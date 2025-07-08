<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventUser;
use App\Models\SettingWebsite;
use App\Models\User;
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

            'list_event' => Event::latest()->paginate(10),
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
}
