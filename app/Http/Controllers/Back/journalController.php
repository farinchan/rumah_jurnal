<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class journalController extends Controller
{
    public function index($journal_path)
    {
        $journal = Journal::where('url_path', $journal_path)->with('issues.submissions')->first();
        if (!$journal) {
            return abort(404);
        }
        $data = [
            'title' => $journal->name,
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

    public function issueUpdate(Request $request, $journal_path, $id)
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

        $issue = $journal->issues()->find($id);
        if (!$issue) {
            return abort(404);
        }

        $issue->update($request->all());
        Alert::success('Success', 'Issue has been updated');
        return redirect()->back();
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
            'issue' => $issue
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-article', $data);
    }
}
