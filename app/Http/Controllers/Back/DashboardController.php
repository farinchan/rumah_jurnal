<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsViewer;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
use App\Models\Announcement;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                     'link' => route('back.dashboard')
                ],
            ],


        ];
        return view('back.pages.dashboard.index', $data);
    }

    public function visistorStat()
    {


        $data = [
            'visitor_monthly' => Visitor::select(DB::raw('Date(created_at) as date'), DB::raw('count(*) as total'))
                ->orderBy('date', 'desc')
                ->limit(30)
                ->groupBy('date')
                ->get(),
            'visitor_platfrom' => Visitor::select('platform', DB::raw('count(*) as total'))
                ->groupBy('platform')
                ->get(),
            'visitor_browser' => Visitor::select('browser', DB::raw('count(*) as total'))
                ->groupBy('browser')
                ->get(),
        ];
        return response()->json($data);
    }

    public function news()
    {
        $data = [
            'title' => 'Dashboard Berita',
            'menu' => 'dashboard',
            'sub_menu' => '',
            'berita_count' => News::count(),
            'news_popular' => News::with('comments')->withCount('viewers')->orderBy('viewers_count', 'desc')->limit(5)->get(),
            'news_new' => News::with(['comments', 'viewers'])->latest()->limit(5)->get(),
            'news_writer' => news::select(
                DB::raw('count(*) as total'),
                'news.user_id',
            )
                ->groupBy('news.user_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];
        return view('back.pages.dashboard.news', $data);
    }

    public function stat()
    {


        $data = [
            'news_viewer_monthly' => NewsViewer::select(DB::raw('Date(created_at) as date'), DB::raw('count(*) as total'))
                ->limit(30)
                ->groupBy('date')
                ->get(),
            'news_viewer_platfrom' => NewsViewer::select('platform', DB::raw('count(*) as total'))
                ->groupBy('platform')
                ->get(),
            'news_viewer_browser' => NewsViewer::select('browser', DB::raw('count(*) as total'))
                ->groupBy('browser')
                ->get(),
        ];
        return response()->json($data);
    }
}
