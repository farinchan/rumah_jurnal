<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Journal;
use App\Models\News;
use App\Models\SettingWebsite;
use App\Models\Visitor;
use App\Models\WelcomeSpeech;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;
use Stevebauman\Location\Facades\Location;

class HomeController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => 'Home',
            'meta' => [
                'title' => 'Home | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'list_news' => News::latest()->where('status', 'published')->limit(10)->get(),
            'list_journal' => Journal::all(),
            'welcome_speech' => WelcomeSpeech::first(),
            'list_announcement' => Announcement::latest()->where('is_active', true)->limit(8)->get(),
            'list_event' => Event::latest()->where('is_active', true)->where('access', 'terbuka')->limit(8)->get(),
        ];
        return view('front.pages.home.index', $data);
    }

    public function vistWebsite()
    {
        try {
            $currentUserInfo = Location::get(request()->ip());
            $visitor = new Visitor();
            $visitor->ip = request()->ip();
            if ($currentUserInfo) {
                $visitor->country = $currentUserInfo->countryName;
                $visitor->city = $currentUserInfo->cityName;
                $visitor->region = $currentUserInfo->regionName;
                $visitor->postal_code = $currentUserInfo->postalCode;
                $visitor->latitude = $currentUserInfo->latitude;
                $visitor->longitude = $currentUserInfo->longitude;
                $visitor->timezone = $currentUserInfo->timezone;
            }
            $visitor->user_agent = Agent::getUserAgent();
            $visitor->platform = Agent::platform();
            $visitor->browser = Agent::browser();
            $visitor->device = Agent::device();
            $visitor->save();

            return response()->json(['status' => 'success', 'message' => 'Visitor has been saved'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function privacyPolicy()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => 'Kebijakan Privasi',
            'meta' => [
                'title' => 'Kebijakan Privasi | ' . $setting_web->name,
                'description' => 'Kebijakan Privasi ' . $setting_web->name . ' - Perlindungan data dan privasi pengguna dalam sistem jurnal online.',
                'keywords' => $setting_web->name . ', Kebijakan Privasi, Privacy Policy, Data Protection, Journal, Research, OJS System',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Kebijakan Privasi',
                    'link' => route('privacy.policy')
                ]
            ],
            'setting_web' => $setting_web,
        ];
        return view('front.pages.home.privacy_policy', $data);
    }

    public function termsOfService()
    {
        $setting_web = SettingWebsite::first();

        $data = [
            'title' => 'Syarat dan Ketentuan',
            'meta' => [
                'title' => 'Syarat dan Ketentuan | ' . $setting_web->name,
                'description' => 'Syarat dan Ketentuan penggunaan layanan ' . $setting_web->name . ' - Aturan dan regulasi sistem jurnal online.',
                'keywords' => $setting_web->name . ', Syarat dan Ketentuan, Terms of Service, Journal Rules, OJS System, Academic Journal',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Syarat dan Ketentuan',
                    'link' => route('terms.service')
                ]
            ],
            'setting_web' => $setting_web,
        ];
        return view('front.pages.home.terms_of_service', $data);
    }
}
