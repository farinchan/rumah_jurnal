<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'journals' => Journal::latest()->get(),
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
                'favicon' => $journal->getJournalThumbnail() ?? Storage::url($setting_web->favicon)
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

    public function loaValidate(Request $request)
    {
        $setting_web = SettingWebsite::first();

        $submissionId = $request->query('submission_id');
        $authorId = $request->query('author_id');

        $submission = null;
        $author = null;
        $isValid = false;
        $hasSearched = !empty($submissionId);

        if ($submissionId) {
            $submission = \App\Models\Submission::with('issue.journal')
                ->where('id', $submissionId)
                ->orWhere('submission_id', $submissionId)
                ->first();

            if ($submission) {
                $authors = $submission->authors ?? [];

                if ($authorId) {
                    foreach ($authors as $auth) {
                        if (isset($auth['id']) && (string) $auth['id'] === (string) $authorId) {
                            $author = $auth;
                            $isValid = true;
                            break;
                        }
                    }
                } else {
                    $isValid = true;
                    $author = !empty($authors) ? $authors[0] : null;
                }
            }
        }

        $data = [
            'title' => 'Validasi Letter of Acceptance (LoA) | ' . ($setting_web->name ?? 'Rumah Jurnal'),
            'meta' => [
                'title' => 'Validasi Letter of Acceptance (LoA) | ' . ($setting_web->name ?? 'Rumah Jurnal'),
                'description' => 'Verifikasi keaslian Letter of Acceptance (LoA) dari ' . ($setting_web->name ?? 'Rumah Jurnal') . '.',
                'keywords' => ($setting_web->name ?? 'Rumah Jurnal') . ', Validasi LoA, Letter of Acceptance, Verification, Authenticity',
                'favicon' => $setting_web?->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => 'Beranda',
                    'link' => route('home')
                ],
                [
                    'name' => 'Validasi LoA',
                    'link' => route('loa.validate')
                ]
            ],
            'setting_web' => $setting_web,
            'submission' => $submission,
            'author' => $author,
            'isValid' => $isValid,
            'hasSearched' => $hasSearched,
            'searchedSubmissionId' => $submissionId,
        ];

        return view('front.pages.journal.loa_validate', $data);
    }
}
