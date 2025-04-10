<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => __('front.agenda') . ' | ' . $setting_web->name,
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

            'list_event' => Event::orderBy('start', 'desc')->paginate(12),
        ];

        return view('front.pages.event.index', $data);
    }

    public function show($slug)
    {
        $setting_web = SettingWebsite::first();
        $event = Event::where('slug', $slug)->first();
        $data = [
            'title' => $event->title . ' | ' . $setting_web->name,
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
                    'name' => $event->title,
                    'link' => route('event.show', $event->slug)
                ]
            ],
            'setting_web' => $setting_web,

            'event' => $event,
        ];

        return view('front.pages.event.show', $data);
    }
}
