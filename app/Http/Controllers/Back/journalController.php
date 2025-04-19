<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Reviewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class JournalController extends Controller
{
    public function index($journal_path)
    {
        $journal = Journal::where('url_path', $journal_path)->with('issues.submissions')->first();
        if (!$journal) {
            return abort(404);
        }
        $data = [
            'title' => $journal->title,
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Journal',
                    'link' => route('back.journal.index', $journal_path)
                ]
            ],
            'journal_path' => $journal_path,
            'journal' => $journal
        ];
        // return response()->json($data);
        return view('back.pages.journal.index', $data);
    }

    public function issueStore(Request $request, $journal_path)
    {
        $validator = Validator::make($request->all(), [
            'volume' => 'required',
            'number' => 'required',
            'year' => 'required',
            'title' => 'required',
            'description' => 'nullable',
        ], [
            'volume.required' => 'Volume harus diisi',
            'number.required' => 'Number harus diisi',
            'year.required' => 'Year harus diisi',
            'title.required' => 'Title harus diisi',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $journal->issues()->create($request->all());
        Alert::success('Success', 'Issue has been created');
        return redirect()->back();
    }

    public function issueUpdate(Request $request, $journal_path, $issue_id)
    {
        $validator = Validator::make($request->all(), [
            'volume' => 'required',
            'number' => 'required',
            'year' => 'required',
            'title' => 'required',
            'description' => 'nullable',
        ], [
            'volume.required' => 'Volume harus diisi',
            'number.required' => 'Number harus diisi',
            'year.required' => 'Year harus diisi',
            'title.required' => 'Title harus diisi',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = $journal->issues()->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $issue->update($request->all());
        Alert::success('Success', 'Issue has been updated');
        return redirect()->back();
    }

    public function issueDestroy($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = $journal->issues()->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $issue->delete();
        Alert::success('Success', 'Issue has been deleted');
        return redirect()->route('back.journal.index', $journal_path);
    }

    public function dashboardIndex($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $data = [
            'title' => "Vol. " . $issue->volume . " No. " . $issue->number . " (" . $issue->year . "): " . $issue->title,
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => $journal->title,
                    'link' => route('back.journal.index', $journal_path)
                ],
                [
                    'name' => $issue->title,
                    'link' => route('back.journal.index', $journal_path)
                ]
            ],
            'journal_path' => $journal_path,
            'journal' => $journal,
            'issue' => $issue,
            // 'submissions' => $issue->submissions->pluck('submission_id'),
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-dashboard', $data);
    }

    public function articleIndex($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $data = [
            'title' => "Vol. " . $issue->volume . " No. " . $issue->number . " (" . $issue->year . "): " . $issue->title,
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => $journal->title,
                    'link' => route('back.journal.index', $journal_path)
                ],
                [
                    'name' => $issue->title,
                    'link' => route('back.journal.index', $journal_path)
                ]
            ],
            'journal_path' => $journal_path,
            'journal' => $journal,
            'issue' => $issue,
            // 'submissions' => $issue->submissions->pluck('submission_id'),
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-article', $data);
    }

    public function articleDestroy($journal_path, $issue_id, $id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $submission = $issue->submissions()->find($id);
        if (!$submission) {
            return abort(404);
        }

        $submission->delete();
        Alert::success('Success', 'Article has been deleted');
        return redirect()->back();
    }

    public function reviewerIndex($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $data = [
            'title' => "Vol. " . $issue->volume . " No. " . $issue->number . " (" . $issue->year . "): " . $issue->title,
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => $journal->title,
                    'link' => route('back.journal.index', $journal_path)
                ],
                [
                    'name' => $issue->title,
                    'link' => route('back.journal.index', $journal_path)
                ]
            ],
            'journal_path' => $journal_path,
            'journal' => $journal,
            'issue' => $issue,
            // 'submissions' => $issue->submissions->pluck('submission_id'),
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-reviewer', $data);
    }

    public function reviewerDestroy($journal_path, $issue_id, $id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $reviewer = Reviewer::find($id);
        if (!$reviewer) {
            return abort(404);
        }
        $reviewer->delete();
        Alert::success('Success', 'Reviewer has been deleted');
        return redirect()->back();
    }

    public function settingIndex($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $data = [
            'title' => "Vol. " . $issue->volume . " No. " . $issue->number . " (" . $issue->year . "): " . $issue->title,
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => $journal->title,
                    'link' => route('back.journal.index', $journal_path)
                ],
                [
                    'name' => $issue->title,
                    'link' => route('back.journal.index', $journal_path)
                ]
            ],
            'journal_path' => $journal_path,
            'journal' => $journal,
            'issue' => $issue,
            // 'submissions' => $issue->submissions->pluck('submission_id'),
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-setting', $data);
    }
}
