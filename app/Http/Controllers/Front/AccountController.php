<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\EventUser;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function profile(Request $request)
    {
        $setting_web = SettingWebsite::first();
        $me = Auth::user();
        if (!$me) {
            return redirect()->route('login')->with('error', "You must be logged in to view your profile.");
        }

        $data = [
            'title' => $me->name . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.profile'),
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
                    'name' => __('front.profile'),
                    'link' => route('account.profile')
                ]
            ],
            'setting_web' => $setting_web,
            'events' => EventUser::with(['event'])
                ->where('user_id', $me->id)
                ->latest()
                ->paginate(10),
            'me' => $me,
        ];
        // return response()->json($data);
        return view('front.pages.account.profile', $data);
    }
}
