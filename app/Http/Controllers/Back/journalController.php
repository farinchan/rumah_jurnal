<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Mail\LoaMail;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\PaymentAccount;
use App\Models\Reviewer;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\SubmissionReviewer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Writer\Pdf\DomPDF as PdfWriter;
use ZipArchive;

class journalController extends Controller
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
            'loa_template' => 'nullable|mimes:pptx,docx,doc,pdf|max:10240',
        ], [
            'volume.required' => 'Volume harus diisi',
            'number.required' => 'Number harus diisi',
            'year.required' => 'Year harus diisi',
            'title.required' => 'Title harus diisi',
            'loa_template.mimes' => 'File harus berupa pptx, docx, doc, pdf',
            'loa_template.max' => 'File tidak boleh lebih dari 10 MB',
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

        $issue->update(
            $request->except('loa_template')
        );
        if ($request->hasFile('loa_template')) {
            $file = $request->file('loa_template');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $issue->loa_template = $file->storeAs('loa_template', $filename, 'public');
            $issue->save();
        }
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
            'reviewers' => Reviewer::where('issue_id', $issue_id)->get(),
            // 'submissions' => $issue->submissions->pluck('submission_id'),
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-article', $data);
    }

    public function articleUpdate(Request $request, $journal_path, $issue_id, $id)
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

        $validator = Validator::make($request->all(), [
            'reviewer' => 'required|array',
        ], [
            'reviewer.required' => 'Reviewer harus dipilih',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }


        SubmissionReviewer::where('submission_id', $submission->id)->delete();

        foreach ($request->reviewer as $reviewer) {
            SubmissionReviewer::create([
                'submission_id' => $submission->id,
                'reviewer_id' => $reviewer,
            ]);
        }

        Alert::success('Success', 'Reviewer has been added');
        return redirect()->back();
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
            'setting_web' => SettingWebsite::first(),
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


    public function loaGenerate($submission)
    {
        $submission = Submission::find($submission);
        if (!$submission) {
            Alert::error('Error', 'Submission not found');
            return redirect()->back()->with('error', 'Submission not found');
        }

        $issue = Issue::find($submission->issue_id);
        if (!$issue) {
            Alert::error('Error', 'Issue not found');
            return redirect()->back()->with('error', 'Issue not found');
        }

        $files = [];

        foreach ($submission->authors as $author) {
            $data = [
                'number' => $submission->number ?? "0000",
                'year' => $submission->created_at->format('Y') ?? Carbon::now()->format('Y'),
                'name' => $author['name'],
                'affiliation' => $author['affiliation'],
                'title' => $submission->fullTitle,
                'journal' => $issue->journal->title,
                'editon' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                'chief_editor' => $issue->journal->editor_chief_name,
                'chief_editor_signature' => $issue->journal->editor_chief_signature ? 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $issue->journal->editor_chief_signature))) : null,
            ];
            $datas[] = $data;

            if (Storage::exists('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
                $files[] = storage_path('app/public/arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
            } else {
                $pdf = Pdf::loadView('back.pages.journal.pdf.loa', $data)->setPaper('A4', 'portrait');
                $path = 'arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

                Storage::disk('public')->put($path, $pdf->output());
                $files[] = $data['attachments'] = storage_path('app/public/' . $path);
            }
        }

        $zipFileName = 'LoA-' . $submission->submission_id . '.zip';
        $zip = new ZipArchive;

        // Temporary path buat zip-nya
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Pastikan folder temp ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                $filePath = $file;
                if (file_exists($filePath)) {
                    // Add file ke zip (hanya nama file saja di dalam zip)
                    $zip->addFile($filePath, basename($file));
                }
            }
            $zip->close();
        } else {
            Alert::error('Error', 'Failed to create zip file');
            return redirect()->back()->with('error', 'Failed to create zip file');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function loaMailSend($submission)
    {
        $submission = Submission::find($submission);
        if (!$submission) {
            Alert::error('Error', 'Submission not found');
            return redirect()->back()->with('error', 'Submission not found');
        }

        $issue = Issue::find($submission->issue_id);
        if (!$issue) {
            Alert::error('Error', 'Issue not found');
            return redirect()->back()->with('error', 'Issue not found');
        }

        foreach ($submission->authors as $author) {
            if ($author['email']) {
                $data = [
                    'subject' => 'Letter of Acceptance (LoA) for ' . $author['name'],
                    'number' => $submission->number ?? "0000",
                    'year' => $submission->created_at->format('Y') ?? Carbon::now()->format('Y'),
                    'name' => $author['name'],
                    'email' => $author['email'],
                    'affiliation' => $author['affiliation'],
                    'title' => $submission->fullTitle,
                    'journal' => $issue->journal->title,
                    'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                    'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                    'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                    'chief_editor' => $issue->journal->editor_chief_name,
                    'chief_editor_signature' => $issue->journal->editor_chief_signature ? 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $issue->journal->editor_chief_signature))) : null,
                    'setting_web' => SettingWebsite::first(),
                ];

                if (Storage::exists('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
                    $data['attachments'] = storage_path('app/public/arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
                } else {
                    $pdf = Pdf::loadView('back.pages.journal.pdf.loa', $data)->setPaper('A4', 'portrait');
                    $path = 'arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

                    Storage::disk('public')->put($path, $pdf->output());
                    $data['attachments'] = $data['attachments'] = storage_path('app/public/' . $path);
                }

                $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
                if ($mailEnvirontment == 'production') {
                    Mail::to($author['email'])->send(new LoaMail($data));
                } else {
                    // For testing purpose
                    Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new LoaMail($data));
                }
            }
        }

        Alert::success('Success', 'Email has been sent');
        return redirect()->back();
    }

    public function invoiceGenerate($submission)
    {
        $submission = Submission::find($submission);
        if (!$submission) {
            Alert::error('Error', 'Submission not found');
            return redirect()->back()->with('error', 'Submission not found');
        }

        // Load PPTX template
        $issue = Issue::find($submission->issue_id);
        if (!$issue) {
            Alert::error('Error', 'Issue not found');
            return redirect()->back()->with('error', 'Issue not found');
        }
        $files = [];
        foreach ($submission->authors as $author) {
            $data = [
                'number' => $submission->number ?? "0000",
                'year' => $submission->created_at->format('Y') ?? Carbon::now()->format('Y'),
                'name' => $author['name'],
                'affiliation' => $author['affiliation'],
                'title' => $submission->fullTitle,
                'journal' => $issue->journal->title,
                'journal_fee' => $issue->journal->author_fee,
                'editon' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                'id' => $submission->submission_id,
                'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                'payment_account' => PaymentAccount::first(),
            ];

            if (Storage::exists('arsip/invoice/' . 'invoice-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
                $files[] = storage_path('app/public/arsip/invoice/' . 'INVOICE-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
            } else {
                $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                $path = 'arsip/invoice/' . 'INVOICE-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

                Storage::disk('public')->put($path, $pdf->output());
                $files[] = $data['attachments'] = storage_path('app/public/' . $path);
            }
        }

        $zipFileName = 'INVOICE-' . $submission->submission_id . '.zip';
        $zip = new ZipArchive;

        // Temporary path buat zip-nya
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Pastikan folder temp ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                $filePath = $file;
                if (file_exists($filePath)) {
                    // Add file ke zip (hanya nama file saja di dalam zip)
                    $zip->addFile($filePath, basename($file));
                }
            }
            $zip->close();
        } else {
            Alert::error('Error', 'Failed to create zip file');
            return redirect()->back()->with('error', 'Failed to create zip file');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function invoiceMailSend($submission)
    {
        $submission = Submission::find($submission);
        if (!$submission) {
            Alert::error('Error', 'Submission not found');
            return redirect()->back()->with('error', 'Submission not found');
        }

        // Load PPTX template
        $issue = Issue::find($submission->issue_id);
        if (!$issue) {
            Alert::error('Error', 'Issue not found');
            return redirect()->back()->with('error', 'Issue not found');
        }

        foreach ($submission->authors as $author) {
            if ($author['email']) {
                $data = [
                    'subject' => 'Invoice for ' . $author['name'],
                    'number' => $submission->number ?? "0000",
                    'year' => $submission->created_at->format('Y') ?? Carbon::now()->format('Y'),
                    'authorString' => $submission->authorsString,
                    'name' => $author['name'],
                    'email' => $author['email'],
                    'affiliation' => $author['affiliation'],
                    'title' => $submission->fullTitle,
                    'journal' => $issue->journal->title,
                       'journal_path' => $issue->journal->url_path,
                    'journal_fee' => $issue->journal->author_fee,
                    'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                    'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                    'id' => $submission->submission_id,
                    'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                    'payment_account' => PaymentAccount::first(),
                    'setting_web' => SettingWebsite::first(),
                ];

                if (Storage::exists('arsip/invoice/' . 'INVOICE-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
                    $data['attachments'] = storage_path('app/public/arsip/invoice/' . 'INVOICE-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
                } else {
                    $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                    $path = 'arsip/invoice/' . 'INVOICE-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

                    Storage::disk('public')->put($path, $pdf->output());
                    $data['attachments'] = storage_path('app/public/' . $path);
                }
            }
            $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
            if ($mailEnvirontment == 'production') {
                Mail::to($author['email'])->send(new InvoiceMail($data));
            } else {
                // For testing purpose
                Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new InvoiceMail($data));
            }
        }
        Alert::success('Success', 'Email has been sent');
        return redirect()->back();
    }

    public function confirmPaymentGenerate($submission)
    {
        $submission = Submission::find($submission);
        if (!$submission) {
            Alert::error('Error', 'Submission not found');
            return redirect()->back()->with('error', 'Submission not found');
        }

        // Load PPTX template
        $issue = Issue::find($submission->issue_id);
        if (!$issue) {
            Alert::error('Error', 'Issue not found');
            return redirect()->back()->with('error', 'Issue not found');
        }

        $data = [
            'name' => $submission->authors[0]['name'],
            'affiliation' => $submission->authors[0]['affiliation'],
            'title' => $submission->fullTitle,
            'journal' => $issue->journal->title,
            'editon' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'id' => $submission->submission_id,
            'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
        ];

        // dd($data);
        $pdf = Pdf::loadView('back.pages.journal.pdf.confirm-payment', $data)->setPaper('A4', 'portrait');
        return $pdf->stream('Confirm-Payment-' . $submission->submission_id . '.pdf');
    }
}
