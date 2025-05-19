<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Editor;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function editor(Request $request)
    {
        $path = $request->journal;
        if (!$path) {
            $journal_first = Journal::first()->url_path;
            return redirect()->route("team.editor", ['journal' => $journal_first]);

        }
        $setting_web = SettingWebsite::first();
        $data = [
            'title' =>  'Editor | ' . $setting_web->name,
            'meta' => [
                'title' => 'Editor | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Contact Us, Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => 'Editor',
                    'link' => route('team.editor')
                ]
                ],
            'setting_web' => SettingWebsite::first(),
            'journals' => Journal::all(),
            'issues' => Issue::whereHas('journal', function ($query) use ($path) {
                $query->where('url_path', $path);
            })->with(['editors' => function ($query) {
                $query->orderBy('name', 'asc');
            }])->get(),
        ];

        return view('front.pages.team.editor', $data);
    }
    public function reviewer(Request $request)
    {
        $path = $request->journal;
        if (!$path) {
            $journal_first = Journal::first()->url_path;
            return redirect()->route("team.reviewer", ['journal' => $journal_first]);

        }
        $setting_web = SettingWebsite::first();
        $data = [
            'title' =>  'Reviewer | ' . $setting_web->name,
            'meta' => [
                'title' => 'Reviewer | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Contact Us, Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => 'Reviewer',
                    'link' => route('team.reviewer')
                ]
                ],
            'setting_web' => SettingWebsite::first(),
            'journals' => Journal::all(),
            'issues' => Issue::whereHas('journal', function ($query) use ($path) {
                $query->where('url_path', $path);
            })->with(['reviewers' => function ($query) {
                $query->orderBy('name', 'asc');
            }])->get(),
        ];
        return view('front.pages.team.reviewer', $data);
    }
}
