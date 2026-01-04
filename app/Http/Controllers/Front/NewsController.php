<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsViewer;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Jenssegers\Agent\Facades\Agent;
use Stevebauman\Location\Facades\Location;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $setting_web = SettingWebsite::first();

        $search = $request->q;
        $news = News::where('title', 'like', "%$search%")
            ->with(['category', 'comments', 'user', 'viewers'])
            ->latest()
            ->paginate(6);
        $news->appends(['q' => $search]);
        $data = [
            'title' => __('front.news') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.news') . ' | ' . $setting_web->name,
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
                    'name' => __('front.news'),
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
        $setting_web = SettingWebsite::first();
        $news = News::where('slug', $slug)->firstOrFail();
        $data = [
            'title' => $news->title,
            'meta' => [
                'title' => $news->title . ' | ' . $setting_web->name,
                'description' => strip_tags($news->content),
                'keywords' => $setting_web->name . ', ' . $news->title . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $news->thumbnail ?? $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.news'),
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
        $setting_web = SettingWebsite::first();
        $category = NewsCategory::where('slug', $slug)->firstOrFail();
        $news = $category->news()->latest()->paginate(6);
        $data = [
            'title' => $category->name,
            'meta' => [
                'title' => $category->name . ' | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', ' . $category->name . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.news'),
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
            'agree' => 'required|accepted',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'news_id.required' => 'News ID is required',
            'news_id.exists' => 'News not found',
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'comment.required' => 'Comment is required',
            'agree.required' => 'You must agree to the terms',
            'agree.accepted' => 'You must agree to the terms',
            'g-recaptcha-response.required' => 'Captcha must be filled',
            'g-recaptcha-response.captcha' => 'Invalid captcha',
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

    public function visit(Request $request)
    {
        $news_id = $request->news_id;
        // dd($news_id);
        try {
            $currentUserInfo = Location::get(request()->ip());
            $news_visitor = new NewsViewer();
            $news_visitor->news_id = $news_id;
            $news_visitor->ip = request()->ip();
            if ($currentUserInfo) {
                $news_visitor->country = $currentUserInfo->countryName;
                $news_visitor->city = $currentUserInfo->cityName;
                $news_visitor->region = $currentUserInfo->regionName;
                $news_visitor->postal_code = $currentUserInfo->postalCode;
                $news_visitor->latitude = $currentUserInfo->latitude;
                $news_visitor->longitude = $currentUserInfo->longitude;
                $news_visitor->timezone = $currentUserInfo->timezone;
            }
            $news_visitor->user_agent = Agent::getUserAgent();
            $news_visitor->platform = Agent::platform();
            $news_visitor->browser = Agent::browser();
            $news_visitor->device = Agent::device();
            $news_visitor->save();

            return response()->json(['status' => 'success', 'message' => 'Visitor has been saved'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }
}
