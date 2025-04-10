<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index()
    {
        $data = [
            'title' => __('front.journal'),
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
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            abort(404);
        }
        $data = [
            'title' => $journal->title,
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
