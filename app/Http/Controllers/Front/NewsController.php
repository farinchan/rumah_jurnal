<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $news = News::where('title', 'like', "%$search%")
            ->with(['category', 'comments', 'user', 'viewers'])
            ->latest()
            ->paginate(6);
        $news->appends(['q' => $search]);
        $data = [
            'title' => 'News',
            'breadcrumbs' => [
                [
                    'name' => 'Home',
                    'link' => route('home')
                ],
                [
                    'name' => 'News',
                    'link' => route('news.index')
                ]
            ],
            'news' => $news,
            'news_trending' => News::withCount('viewers')->orderByDesc('viewers_count')->take(5)->get(),
            'categories' => NewsCategory::with('news')->get(),

        ];
        return view('front.pages.news.index', $data);
    }

    public function detail($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        $data = [
            'title' => $news->title,
            'breadcrumbs' => [
                [
                    'name' => 'Home',
                    'link' => route('home')
                ],
                [
                    'name' => 'News',
                    'link' => route('news.index')
                ],
                [
                    'name' => $news->title,
                    'link' => route('news.detail', $news->slug)
                ]
            ],
            'news' => $news,
            'prev_news' => News::where('id', '<', $news->id)->latest()->first(),
            'next_news' => News::where('id', '>', $news->id)->latest()->first(),
            'news_trending' => News::withCount('viewers')->orderByDesc('viewers_count')->take(5)->get(),
            'categories' => NewsCategory::with('news')->get(),
        ];
        return view('front.pages.news.detail', $data);
    }

    public function category($slug)
    {
        $category = NewsCategory::where('slug', $slug)->firstOrFail();
        $news = $category->news()->latest()->paginate(6);
        $data = [
            'title' => $category->name,
            'breadcrumbs' => [
                [
                    'name' => 'Home',
                    'link' => route('home')
                ],
                [
                    'name' => 'News',
                    'link' => route('news.index')
                ],
                [
                    'name' => $category->name,
                    'link' => route('news.category', $category->slug)
                ]
            ],
            'category' => $category,
            'news' => $news,
            'news_trending' => News::withCount('viewers')->orderByDesc('viewers_count')->take(5)->get(),
            'categories' => NewsCategory::with('news')->get(),
        ];
        return view('front.pages.news.category', $data);
    }

    public function comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'news_id' => 'required|exists:news,id',
            'name' => 'required',
            'email' => 'required|email',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', 'Please fill all the form');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $news = News::find($request->news_id);
        $news->comments()->create($request->all());
        Alert::success('Success', 'Comment has been added');
        return redirect()->back();
    }
}
