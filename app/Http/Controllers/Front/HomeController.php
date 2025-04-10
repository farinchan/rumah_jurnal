<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Journal;
use App\Models\News;
use App\Models\WelcomeSpeech;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'list_news' => News::latest()->where('status', 'published')->limit(5)->get(),
            'list_journal' => Journal::all(),
            'welcome_speech' => WelcomeSpeech::first(),
            'list_announcement' => Announcement::latest()->where('is_active', true)->limit(5)->get(),
            'list_event' => Event::latest()->where('is_active', true)->limit(5)->get(),
        ];
        return view('front.pages.home.index', $data);
    }
}
