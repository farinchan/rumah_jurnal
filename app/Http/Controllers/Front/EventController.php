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

            'list_event' => Event::orderBy('start', 'desc')->get(),
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
                'keywords' => $setting_web->name . ', ' . $event->title .', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
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
            'event_latest' => Event::where('start', '>=', now())->orderBy('start', 'asc')->take(6)->get(),

            'event' => $event,
        ];

        return view('front.pages.event.show', $data);
    }
}
