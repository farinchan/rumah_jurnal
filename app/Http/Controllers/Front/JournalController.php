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
            'title' => 'Journal',
            'breadcrumbs' => [
                [
                    'name' => 'Home',
                    'link' => route('home')
                ],
                [
                    'name' => 'Journal',
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
                    'name' => 'Home',
                    'link' => route('home')
                ],
                [
                    'name' => 'Journal',
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
