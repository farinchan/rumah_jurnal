<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => __('front.announcement') . ' | ' . $setting_web->name,
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.announcement'),
                    'link' => route('announcement.index')
                ]
            ],
            'setting_web' => $setting_web,

            'list_announcement' => Announcement::latest()->paginate(10),
        ];

        return view('front.pages.announcement.index', $data);
    }

    public function show($slug)
    {
        $setting_web = SettingWebsite::first();
        $announcement = Announcement::where('slug', $slug)->first();
        $data = [
            'title' => $announcement->title . ' | ' . $setting_web->name,
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.announcement'),
                    'link' => route('announcement.index')
                ],
                [
                    'name' => $announcement->title,
                    'link' => route('announcement.show', $announcement->slug)
                ]
            ],
            'setting_web' => $setting_web,

            'announcement' => $announcement,
        ];

        return view('front.pages.announcement.show', $data);
    }
}
