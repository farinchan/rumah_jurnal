<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('front.journal') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.journal') . ' | ' . $setting_web->name,
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
                    'name' => __('front.journal'),
                    'link' => route('journal.index')
                ]
                ],
            'journals' => Journal::latest()->paginate(6),
        ];
        return view('front.pages.journal.index', $data);
    }

    public function detail($journal_path)
    {
        $setting_web = SettingWebsite::first();
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            abort(404);
        }
        $data = [
            'title' => $journal->title,
            'meta' => [
                'title' => $journal->title . ' | ' . $setting_web->name,
                'description' => strip_tags($journal->description),
                'keywords' => $setting_web->name . ', ' . $journal->title . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $journal->getJournalThumbnail() ?? $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.journal'),
                    'link' => route('journal.index')
                ],
                [
                    'name' => $journal->url_path,
                    'link' => route('journal.detail', $journal->url_path)
                ]
            ],
            'journal' => $journal,
            'issues' => $journal->issues()->latest()->paginate(6),
        ];
        return view('front.pages.journal.detail', $data);
    }
}
