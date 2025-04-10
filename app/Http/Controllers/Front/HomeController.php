<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'list_news' => News::latest()->where('status', 'published')->limit(5)->get(),
            'list_journal' => Journal::all(),
        ];
        return view('front.pages.home.index', $data);
    }
}
