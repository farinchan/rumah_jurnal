<?php

namespace App\Http\Controllers\Back;

use App\Exports\articleIssueExport;
use App\Http\Controllers\Controller;
use App\Mail\CertificateEditorMail;
use App\Mail\CertificateReviewerMail;
use App\Mail\FeeEditorMail;
use App\Mail\FeeReviewerMail;
use App\Mail\InvoiceMail;
use App\Mail\LoaMail;
use App\Mail\SkEditorMail;
use App\Mail\SkReviewerMail;
use App\Models\Editor;
use App\Models\EditorFileIssue;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\PaymentAccount;
use App\Models\PaymentInvoice;
use App\Models\Reviewer;
use App\Models\ReviewerFileIssue;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\SubmissionEditor;
use App\Models\SubmissionReviewer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
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

    //TODO: ARTCILE SECTION

    public function articleIndex($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with(['submissions.paymentInvoices'])->find($issue_id);
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
            'editors' => Editor::where('issue_id', $issue_id)->get(),
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
            'reviewer' => 'nullable|array',
            'editor' => 'nullable|array',
        ], [
            'reviewer.required' => 'Reviewer harus dipilih',
            'reviewer.array' => 'Reviewer harus dipilih',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $submission->update([
            'free_charge' => $request->free_charge ? 1 : 0,
        ]);


        SubmissionReviewer::where('submission_id', $submission->id)->delete();

        if ($request->reviewer) {

            foreach ($request->reviewer as $reviewer) {
                SubmissionReviewer::create([
                    'submission_id' => $submission->id,
                    'reviewer_id' => $reviewer,
                ]);
            }
        }

        SubmissionEditor::where('submission_id', $submission->id)->delete();

        if ($request->editor) {
            foreach ($request->editor as $editor) {
                SubmissionEditor::create([
                    'submission_id' => $submission->id,
                    'editor_id' => $editor,
                ]);
            }
        }

        Alert::success('Success', 'Artcle has been updated');
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

    public function articleExport($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        return Excel::download(new articleIssueExport($issue_id), 'Article-' . $issue->volume . '-' . $issue->number . '-' . $issue->year . '.xlsx');
        // $issue = Issue::with(['submissions'])
        //     ->where('id', $issue_id)
        //     ->first()->submissions
        //     ->map(function ($submissions) {
        //         return [
        //             'submission_id' => $submissions->id,
        //             'authors' => $submissions->authorsString,
        //             'title' => $submissions->FullTitle,
        //             'status' => $submissions->status_label,
        //             'url_published' => $submissions->urlPublished,
        //             'editors' => $submissions->editors->map(function ($editor) {
        //                 return [
        //                     'name' => $editor->name,
        //                     'email' => $editor->email,
        //                 ];
        //             }),
        //             'reviewers' => $submissions->reviewers->map(function ($reviewer) {
        //                 return [
        //                     'name' => $reviewer->name,
        //                     'email' => $reviewer->email,
        //                 ];
        //             }),
        //         ];
        //     });

        // return response()->json($issue);
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
                'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                'chief_editor' => $issue->journal->editor_chief_name,
                'chief_editor_signature' => $issue->journal->editor_chief_signature ? 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $issue->journal->editor_chief_signature))) : null,
            ];
            $datas[] = $data;

            // if (Storage::exists('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
            //     $files[] = storage_path('app/public/arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
            // } else {
            //     $pdf = Pdf::loadView('back.pages.journal.pdf.loa', $data)->setPaper('A4', 'portrait');
            //     $path = 'arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

            //     Storage::disk('public')->put($path, $pdf->output());
            //     $files[] = $data['attachments'] = storage_path('app/public/' . $path);
            // }

            $pdf = Pdf::loadView('back.pages.journal.pdf.loa', $data)->setPaper('A4', 'portrait');
            $path = 'arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

            // Cek apakah file sudah ada di storage
            if (Storage::exists('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
                // Jika maka hapus dari storage
                Storage::disk('public')->delete('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
            }
            Storage::disk('public')->put($path, $pdf->output());
            $files[] = $data['attachments'] = storage_path('app/public/' . $path);
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

                // if (Storage::exists('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
                //     $data['attachments'] = storage_path('app/public/arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
                // } else {
                //     $pdf = Pdf::loadView('back.pages.journal.pdf.loa', $data)->setPaper('A4', 'portrait');
                //     $path = 'arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

                //     Storage::disk('public')->put($path, $pdf->output());
                //     $data['attachments'] = $data['attachments'] = storage_path('app/public/' . $path);
                // }

                $pdf = Pdf::loadView('back.pages.journal.pdf.loa', $data)->setPaper('A4', 'portrait');
                $path = 'arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf';

                // Cek apakah file sudah ada di storage
                if (Storage::exists('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf')) {
                    // Jika maka hapus dari storage
                    Storage::disk('public')->delete('arsip/loa/' . 'LoA-' . $submission->submission_id . '-' . $submission->id . '-' . $author['id'] . '.pdf');
                }
                Storage::disk('public')->put($path, $pdf->output());
                $files[] = $data['attachments'] = storage_path('app/public/' . $path);

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

    public function invoiceGenerate1($submission)
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

        $invoice = $submission->paymentInvoices()->where('payment_percent', '60')->first();
        if (!$invoice) {
            $year = Carbon::now()->year;
            $last = PaymentInvoice::whereYear('created_at', $year)
                ->orderBy('invoice_number', 'desc')
                ->first();
            $newNumber = $last ? $last->invoice_number + 1 : 1;

            // Format jadi 4 digit
            $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $invoice = PaymentInvoice::create([
                'invoice_number' => $formattedNumber,
                'payment_percent' => 60,
                'payment_amount' => $issue->journal->author_fee * 0.6,
                'payment_due_date' => Carbon::now()->addDays(3),
                'submission_id' => $submission->id,
            ]);
        }

        $files = [];
        foreach ($submission->authors as $author) {
            $data = [
                'number' => $invoice->invoice_number ?? "0000",
                'year' => $invoice->created_at->format('Y') ?? Carbon::now()->format('Y'),
                'name' => $author['name'],
                'affiliation' => $author['affiliation'],
                'title' => $submission->fullTitle,
                'journal' => $issue->journal->title,
                'payment_percent' => $invoice->payment_percent,
                'payment_amount' => $invoice->payment_amount,
                'payment_due_date' => \Carbon\Carbon::parse($invoice->payment_due_date)->translatedFormat('d F Y'),
                'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                'id' => $submission->submission_id,
                'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                'payment_account' => PaymentAccount::first(),
            ];

            if (Storage::exists('arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf')) {
                $files[] = storage_path('app/public/arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf');
            } else {
                $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                $path = 'arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf';

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

    public function invoiceMailSend1($submission)
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

        $invoice = $submission->paymentInvoices()->where('payment_percent', '60')->first();
        if (!$invoice) {
            $year = Carbon::now()->year;
            $last = PaymentInvoice::whereYear('created_at', $year)
                ->orderBy('invoice_number', 'desc')
                ->first();
            $newNumber = $last ? $last->invoice_number + 1 : 1;

            // Format jadi 4 digit
            $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $invoice = PaymentInvoice::create([
                'invoice_number' => $formattedNumber,
                'payment_percent' => 60,
                'payment_amount' => $issue->journal->author_fee * 0.6,
                'payment_due_date' => Carbon::now()->addDays(3),
                'submission_id' => $submission->id,
            ]);
        }

        foreach ($submission->authors as $author) {
            try {
                if ($author['email']) {
                    $data = [
                        'subject' => 'Invoice for ' . $author['name'],
                        'number' => $invoice->invoice_number ?? "0000",
                        'year' => $submission->created_at->format('Y') ?? Carbon::now()->format('Y'),
                        'authorString' => $submission->authorsString,
                        'name' => $author['name'],
                        'email' => $author['email'],
                        'affiliation' => $author['affiliation'],
                        'title' => $submission->fullTitle,
                        'journal' => $issue->journal->title,
                        'journal_path' => $issue->journal->url_path,
                        'payment_percent' => $invoice->payment_percent,
                        'payment_amount' => $invoice->payment_amount,
                        'payment_due_date' => \Carbon\Carbon::parse($invoice->payment_due_date)->translatedFormat('d F Y'),
                        'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                        'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                        'id' => $submission->submission_id,
                        'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                        'payment_account' => PaymentAccount::first(),
                        'setting_web' => SettingWebsite::first(),
                    ];

                    if (Storage::exists('arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf')) {
                        $data['attachments'] = storage_path('app/public/arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf');
                    } else {
                        $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                        $path = 'arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf';

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
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        Alert::success('Success', 'Email has been sent');
        return redirect()->back();
    }

    public function invoiceGenerate2($submission)
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

        $invoice = $submission->paymentInvoices()->where('payment_percent', '40')->first();
        if (!$invoice) {
            $year = Carbon::now()->year;
            $last = PaymentInvoice::whereYear('created_at', $year)
                ->orderBy('invoice_number', 'desc')
                ->first();
            $newNumber = $last ? $last->invoice_number + 1 : 1;

            // Format jadi 4 digit
            $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $invoice = PaymentInvoice::create([
                'invoice_number' => $formattedNumber,
                'payment_percent' => 40,
                'payment_amount' => $issue->journal->author_fee * 0.4,
                'payment_due_date' => Carbon::now()->addDays(3),
                'submission_id' => $submission->id,
            ]);
        }

        $files = [];
        foreach ($submission->authors as $author) {
            $data = [
                'number' => $invoice->invoice_number ?? "0000",
                'year' => $invoice->created_at->format('Y') ?? Carbon::now()->format('Y'),
                'name' => $author['name'],
                'affiliation' => $author['affiliation'],
                'title' => $submission->fullTitle,
                'journal' => $issue->journal->title,
                'payment_percent' => $invoice->payment_percent,
                'payment_amount' => $invoice->payment_amount,
                'payment_due_date' => \Carbon\Carbon::parse($invoice->payment_due_date)->translatedFormat('d F Y'),
                'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                'id' => $submission->submission_id,
                'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                'payment_account' => PaymentAccount::first(),
            ];

            if (Storage::exists('arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf')) {
                $files[] = storage_path('app/public/arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf');
            } else {
                $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                $path = 'arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf';

                Storage::disk('public')->put($path, $pdf->output());
                $files[] = storage_path('app/public/' . $path);
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

    public function invoiceMailSend2($submission)
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

        $invoice = $submission->paymentInvoices()->where('payment_percent', '40')->first();
        if (!$invoice) {
            $year = Carbon::now()->year;
            $last = PaymentInvoice::whereYear('created_at', $year)
                ->orderBy('invoice_number', 'desc')
                ->first();
            $newNumber = $last ? $last->invoice_number + 1 : 1;

            // Format jadi 4 digit
            $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $invoice = PaymentInvoice::create([
                'invoice_number' => $formattedNumber,
                'payment_percent' => 40,
                'payment_amount' => $issue->journal->author_fee * 0.4,
                'payment_due_date' => Carbon::now()->addDays(3),
                'submission_id' => $submission->id,
            ]);
        }

        foreach ($submission->authors as $author) {
            try {
                if ($author['email']) {
                    $data = [
                        'subject' => 'Invoice for ' . $author['name'],
                        'number' => $invoice->invoice_number ?? "0000",
                        'year' => $submission->created_at->format('Y') ?? Carbon::now()->format('Y'),
                        'authorString' => $submission->authorsString,
                        'name' => $author['name'],
                        'email' => $author['email'],
                        'affiliation' => $author['affiliation'],
                        'title' => $submission->fullTitle,
                        'journal' => $issue->journal->title,
                        'journal_path' => $issue->journal->url_path,
                        'payment_percent' => $invoice->payment_percent,
                        'payment_amount' => $invoice->payment_amount,
                        'payment_due_date' => \Carbon\Carbon::parse($invoice->payment_due_date)->translatedFormat('d F Y'),
                        'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                        'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                        'id' => $submission->submission_id,
                        'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                        'payment_account' => PaymentAccount::first(),
                        'setting_web' => SettingWebsite::first(),
                    ];
                    if (Storage::exists('arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf')) {
                        $data['attachments'] = storage_path('app/public/arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf');
                    } else {
                        $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                        $path = 'arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf';

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
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        Alert::success('Success', 'Email has been sent');
        return redirect()->back();
    }

    public function invoiceGenerate3($submission)
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

        $invoice = $submission->paymentInvoices()->where('payment_percent', '100')->first();
        if (!$invoice) {
            $year = Carbon::now()->year;
            $last = PaymentInvoice::whereYear('created_at', $year)
                ->orderBy('invoice_number', 'desc')
                ->first();
            $newNumber = $last ? $last->invoice_number + 1 : 1;

            // Format jadi 4 digit
            $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $invoice = PaymentInvoice::create([
                'invoice_number' => $formattedNumber,
                'payment_percent' => 100,
                'payment_amount' => $issue->journal->author_fee,
                'payment_due_date' => Carbon::now()->addDays(3),
                'submission_id' => $submission->id,
            ]);
        }

        $files = [];
        foreach ($submission->authors as $author) {
            $data = [
                'number' => $invoice->invoice_number ?? "0000",
                'year' => $invoice->created_at->format('Y') ?? Carbon::now()->format('Y'),
                'name' => $author['name'],
                'affiliation' => $author['affiliation'],
                'title' => $submission->fullTitle,
                'journal' => $issue->journal->title,
                'payment_percent' => $invoice->payment_percent,
                'payment_amount' => $invoice->payment_amount,
                'payment_due_date' => \Carbon\Carbon::parse($invoice->payment_due_date)->translatedFormat('d F Y'),
                'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                'id' => $submission->submission_id,
                'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                'payment_account' => PaymentAccount::first(),
            ];

            if (Storage::exists('arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf')) {
                $files[] = storage_path('app/public/arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf');
            } else {
                $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                $path = 'arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf';

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

    public function invoiceMailSend3($submission)
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

        $invoice = $submission->paymentInvoices()->where('payment_percent', '100')->first();
        if (!$invoice) {
            $year = Carbon::now()->year;
            $last = PaymentInvoice::whereYear('created_at', $year)
                ->orderBy('invoice_number', 'desc')
                ->first();
            $newNumber = $last ? $last->invoice_number + 1 : 1;

            // Format jadi 4 digit
            $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $invoice = PaymentInvoice::create([
                'invoice_number' => $formattedNumber,
                'payment_percent' => 100,
                'payment_amount' => $issue->journal->author_fee,
                'payment_due_date' => Carbon::now()->addDays(3),
                'submission_id' => $submission->id,
            ]);
        }

        foreach ($submission->authors as $author) {
            try {
                if ($author['email']) {
                    $data = [
                        'subject' => 'Invoice for ' . $author['name'],
                        'number' => $invoice->invoice_number ?? "0000",
                        'year' => $submission->created_at->format('Y') ?? Carbon::now()->format('Y'),
                        'authorString' => $submission->authorsString,
                        'name' => $author['name'],
                        'email' => $author['email'],
                        'affiliation' => $author['affiliation'],
                        'title' => $submission->fullTitle,
                        'journal' => $issue->journal->title,
                        'journal_path' => $issue->journal->url_path,
                        'payment_percent' => $invoice->payment_percent,
                        'payment_amount' => $invoice->payment_amount,
                        'payment_due_date' => \Carbon\Carbon::parse($invoice->payment_due_date)->translatedFormat('d F Y'),
                        'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                        'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                        'id' => $submission->submission_id,
                        'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($issue->journal->getJournalThumbnail())),
                        'payment_account' => PaymentAccount::first(),
                        'setting_web' => SettingWebsite::first(),
                    ];
                    if (Storage::exists('arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf')) {
                        $data['attachments'] = storage_path('app/public/arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf');
                    } else {
                        $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
                        $path = 'arsip/invoice/' . $invoice->created_at->format('Y') . '/' . $invoice->invoice_number . '/invoice-' . $submission->submission_id .  '-' . $author['id'] . '.pdf';

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
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        Alert::success('Success', 'Email has been sent');
        return redirect()->back();
    }

    //TODO: EDITOR SECTION

    public function editorIndex($journal_path, $issue_id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with(['submissions', 'editors', 'reviewers'])->find($issue_id);
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
            'file_sk' => EditorFileIssue::where('issue_id', $issue_id)->where('file_type', 'sk')->first(),
            'file_certificate' => EditorFileIssue::where('issue_id', $issue_id)->where('file_type', 'certificate')->first(),
            'file_fee' => EditorFileIssue::where('issue_id', $issue_id)->where('file_type', 'fee')->first(),
            'setting_web' => SettingWebsite::first(),
            // 'submissions' => $issue->submissions->pluck('submission_id'),
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-editor', $data);
    }

    public function editorCertificateDownload(Request $request, $journal_path, $issue_id, ?int $id = null)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        if ($id) {
            $editor = Editor::where('issue_id', $issue_id)->find($id);
            if (!$editor) {
                Alert::error('Error', 'Editor not found');
                return redirect()->back();
            }

            if (!$editor->number) {
                // Generate number if not exists
                $year = Carbon::now()->year;
                $last = Editor::whereYear('created_at', $year)
                    ->orderBy('number', 'desc')
                    ->first();
                $newNumber = $last ? $last->number + 1 : 1;

                // Format jadi 4 digit
                $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                $editor->number = $formattedNumber;
                $editor->save();
            }

            $data = [
                'number' =>  $editor->number ?? "0000",
                'month' => strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMMM')),
                'month_roman' => strtoupper(\Carbon\Carbon::now()->format('n')) ? [
                    1 => 'I',
                    2 => 'II',
                    3 => 'III',
                    4 => 'IV',
                    5 => 'V',
                    6 => 'VI',
                    7 => 'VII',
                    8 => 'VIII',
                    9 => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII'
                ][(int)\Carbon\Carbon::now()->format('n')] : '',
                'year' => \Carbon\Carbon::now()->format('Y'),
                'name' => $editor->name,
                'affiliation' => $editor->affiliation,
                'journal' => $issue->journal->title,
                'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            ];

            $pdf = Pdf::loadView('back.pages.journal.pdf.certificate-editor', $data)->setPaper('A4', 'landscape');
            $path = 'arsip/certificate/editor/' . $issue->journal->url_path . '/' . $issue->year . '/' . $issue->volume . '-' . $issue->number . '/certificate-editor-' . $editor->editor_id . '-' . $editor->id . '.pdf';

            return $pdf->stream();
            // // Cek apakah file sudah ada di storage
            if (Storage::exists('public/' . $path)) {
                // Jika ada, kembalikan file tersebut
                return response()->download(storage_path('app/public/' . $path));
            } else {
                // Jika tidak ada, simpan file baru
                Storage::disk('public')->put($path, $pdf->output());
                return response()->download(storage_path('app/public/' . $path));
            }
        } else {
            $editors = Editor::where('issue_id', $issue_id)->get();
            if (!$editors) {
                Alert::error('Error', 'Editor not found');
                return redirect()->back();
            }
            $files = [];
            foreach ($editors as $editor) {
                if (!$editor->number) {
                    // Generate number if not exists
                    $year = Carbon::now()->year;
                    $last = Editor::whereYear('created_at', $year)
                        ->orderBy('number', 'desc')
                        ->first();
                    $newNumber = $last ? $last->number + 1 : 1;

                    // Format jadi 4 digit
                    $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                    $editor->number = $formattedNumber;
                    $editor->save();
                }
                $data = [
                    'number' =>  $editor->number ?? "0000",
                    'month' => strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMMM')),
                    'month_roman' => strtoupper(\Carbon\Carbon::now()->format('n')) ? [
                        1 => 'I',
                        2 => 'II',
                        3 => 'III',
                        4 => 'IV',
                        5 => 'V',
                        6 => 'VI',
                        7 => 'VII',
                        8 => 'VIII',
                        9 => 'IX',
                        10 => 'X',
                        11 => 'XI',
                        12 => 'XII'
                    ][(int)\Carbon\Carbon::now()->format('n')] : '',
                    'year' => \Carbon\Carbon::now()->format('Y'),
                    'name' => $editor->name,
                    'affiliation' => $editor->affiliation,
                    'journal' => $issue->journal->title,
                    'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                ];

                $pdf = Pdf::loadView('back.pages.journal.pdf.certificate-editor', $data)->setPaper('A4', 'landscape');
                $path = 'arsip/certificate/editor/' . $issue->journal->url_path . '/' . $issue->year . '/' . $issue->volume . '-' . $issue->number . '/certificate-editor-' . $editor->editor_id . '-' . $editor->id . '.pdf';

                // Cek apakah file sudah ada di storage
                if (Storage::exists('public/' . $path)) {
                    // Jika ada, kembalikan file tersebut
                    $files[] = storage_path('app/public/' . $path);
                } else {
                    // Jika tidak ada, simpan file baru
                    Storage::disk('public')->put($path, $pdf->output());
                    $files[] = storage_path('app/public/' . $path);
                }
            }

            $zipFileName = 'CERTIFICATE-EDITOR-' . $issue->journal->url_path . '-' . $issue->year . '-' . $issue->volume . '-' . $issue->number . '.zip';
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
    }

    public function editorCertificateSendmail(Request $request, $journal_path, $issue_id, ?int $id = null)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        if ($id) {
            $editor = Editor::where('issue_id', $issue_id)->find($id);
            if (!$editor) {
                Alert::error('Error', 'Editor not found');
                return redirect()->back();
            }

            if (!$editor->number) {
                // Generate number if not exists
                $year = Carbon::now()->year;
                $last = Editor::whereYear('created_at', $year)
                    ->orderBy('number', 'desc')
                    ->first();
                $newNumber = $last ? $last->number + 1 : 1;

                // Format jadi 4 digit
                $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                $editor->number = $formattedNumber;
                $editor->save();
            }

            $data = [
                'subject' => 'Certificate Editor - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
                'number' =>  $editor->number ?? "0000",
                'month' => strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMMM')),
                'month_roman' => strtoupper(\Carbon\Carbon::now()->format('n')) ? [
                    1 => 'I',
                    2 => 'II',
                    3 => 'III',
                    4 => 'IV',
                    5 => 'V',
                    6 => 'VI',
                    7 => 'VII',
                    8 => 'VIII',
                    9 => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII'
                ][(int)\Carbon\Carbon::now()->format('n')] : '',
                'year' => \Carbon\Carbon::now()->format('Y'),
                'name' => $editor->name,
                'affiliation' => $editor->affiliation,
                'journal' => $issue->journal->title,
                'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                'setting_web' => SettingWebsite::first(),
                'email' => $editor->email,
            ];

            $pdf = Pdf::loadView('back.pages.journal.pdf.certificate-editor', $data)->setPaper('A4', 'landscape');

            // Cek apakah file sudah ada di storage
            $path = 'arsip/certificate/editor/' . $issue->journal->url_path . '/' . $issue->year . '/' . $issue->volume . '-' . $issue->number . '/certificate-editor-' . $editor->editor_id . '-' . $editor->id . '.pdf';
            if (Storage::exists('public/' . $path)) {
                // Jika ada, kembalikan file tersebut
                $data['attachments'] = storage_path('app/public/' . $path);
            } else {
                // Jika tidak ada, simpan file baru
                Storage::disk('public')->put($path, $pdf->output());
                $data['attachments'] = storage_path('app/public/' . $path);
            }

            $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
            if ($mailEnvirontment == 'production') {
                Mail::to($data['email'])->send(new CertificateEditorMail($data));
            } else {
                // For testing purpose
                Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new CertificateEditorMail($data));
            }

            Alert::success('Success', 'email has been sent');
            return redirect()->back();
        }else{
            $editors = Editor::where('issue_id', $issue_id)->get();
            if (!$editors) {
                Alert::error('Error', 'Editor not found');
                return redirect()->back();
            }

            foreach ($editors as $editor) {
                if (!$editor->number) {
                    // Generate number if not exists
                    $year = Carbon::now()->year;
                    $last = Editor::whereYear('created_at', $year)
                        ->orderBy('number', 'desc')
                        ->first();
                    $newNumber = $last ? $last->number + 1 : 1;

                    // Format jadi 4 digit
                    $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                    $editor->number = $formattedNumber;
                    $editor->save();
                }
                $data = [
                    'subject' => 'Certificate Editor - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
                    'number' =>  $editor->number ?? "0000",
                    'month' => strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMMM')),
                    'month_roman' => strtoupper(\Carbon\Carbon::now()->format('n')) ? [
                        1 => 'I',
                        2 => 'II',
                        3 => 'III',
                        4 => 'IV',
                        5 => 'V',
                        6 => 'VI',
                        7 => 'VII',
                        8 => 'VIII',
                        9 => 'IX',
                        10 => 'X',
                        11 => 'XI',
                        12 => 'XII'
                    ][(int)\Carbon\Carbon::now()->format('n')] : '',
                    'year' => \Carbon\Carbon::now()->format('Y'),
                    'name' => $editor->name,
                    'affiliation' => $editor->affiliation,
                    'journal' => $issue->journal->title,
                    'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
                    'setting_web' => SettingWebsite::first(),
                    'email' => $editor->email,
                ];

                $pdf = Pdf::loadView('back.pages.journal.pdf.certificate-editor', $data)->setPaper('A4', 'landscape');

                // Cek apakah file sudah ada di storage
                $path = 'arsip/certificate/editor/' . $issue->journal->url_path . '/' . $issue->year . '/' . $issue->volume . '-' . $issue->number . '/certificate-editor-' . $editor->editor_id . '-' . $editor->id . '.pdf';
                if (Storage::exists('public/' . $path)) {
                    // Jika ada, kembalikan file tersebut
                    $data['attachments'] = storage_path('app/public/' . $path);
                } else {
                    // Jika tidak ada, simpan file baru
                    Storage::disk('public')->put($path, $pdf->output());
                    $data['attachments'] = storage_path('app/public/' . $path);
                }

                $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
                if ($mailEnvirontment == 'production') {
                    Mail::to($data['email'])->send(new CertificateEditorMail($data));
                } else {
                    // For testing purpose
                    Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new CertificateEditorMail($data));
                }
            }

            Alert::success('Success', 'email has been sent');
            return redirect()->back();
        }
    }



    public function editorFileCertificateSendMail(Request $request, $journal_path, $issue_id, ?string $email = null)
    {

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $editor = Editor::where('issue_id', $issue_id)->get();
        if (!$editor) {
            Alert::error('Error', 'Editor not found');
            return redirect()->back();
        }

        $file = EditorFileIssue::where('issue_id', $issue_id)->where('file_type', 'certificate')->first();
        if (!$file) {
            Alert::error('Error', 'File not found');
            return redirect()->back();
        }

        $data = [
            'subject' => 'Certificate Editor - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
            'journal' => $issue->journal->title,
            'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'attachments' => storage_path('app/public/' . $file->file),
            'setting_web' => SettingWebsite::first(),
        ];

        $emailAddress = [];
        if ($request->email) {
            $emailAddress = $email;
        } else {
            foreach ($editor as $editors) {
                if ($editors->email) {
                    $emailAddress[] = $editors->email;
                }
            }
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'production') {
            Mail::to($emailAddress)->send(new CertificateEditorMail($data));
        } else {
            // For testing purpose
            Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new CertificateEditorMail($data));
        }

        Alert::success('Success', 'email has been sent');
        return redirect()->back();
    }

    public function editorFileSkStore(Request $request, $journal_path, $issue_id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:10240',
        ], [
            'file.required' => 'File harus diisi',
            'file.mimes' => 'File harus berupa pdf',
            'file.max' => 'File tidak boleh lebih dari 10 MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $file = $request->file('file');
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        EditorFileIssue::updateOrCreate(
            ['issue_id' => $issue_id, 'file_type' => 'sk'],
            ['file' => $file->storeAs('editor_file/sk', $filename, 'public')]
        );

        Alert::success('Success', 'File has been uploaded');
        return redirect()->back();
    }

    public function editorFileSkSendMail(Request $request, $journal_path, $issue_id, ?string $email = null)
    {

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $editor = Editor::where('issue_id', $issue_id)->get();
        if (!$editor) {
            Alert::error('Error', 'Editor not found');
            return redirect()->back();
        }

        $file = EditorFileIssue::where('issue_id', $issue_id)->where('file_type', 'sk')->first();
        if (!$file) {
            Alert::error('Error', 'File not found');
            return redirect()->back();
        }

        $data = [
            'subject' => 'SK Editor - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
            'journal' => $issue->journal->title,
            'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'attachments' => storage_path('app/public/' . $file->file),
            'setting_web' => SettingWebsite::first(),
        ];

        $emailAddress = [];
        if ($request->email) {
            $emailAddress = $email;
        } else {
            foreach ($editor as $editors) {
                if ($editors->email) {
                    $emailAddress[] = $editors->email;
                }
            }
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'production') {
            Mail::to($emailAddress)->send(new SkEditorMail($data));
        } else {
            // For testing purpose
            Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new SkEditorMail($data));
        }

        Alert::success('Success', 'email has been sent');
        return redirect()->back();
    }



    public function editorFileFeeStore(Request $request, $journal_path, $issue_id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:10240',
        ], [
            'file.required' => 'File harus diisi',
            'file.mimes' => 'File harus berupa pdf',
            'file.max' => 'File tidak boleh lebih dari 10 MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $file = $request->file('file');
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        EditorFileIssue::updateOrCreate(
            ['issue_id' => $issue_id, 'file_type' => 'fee'],
            ['file' => $file->storeAs('editor_file/fee', $filename, 'public')]
        );

        Alert::success('Success', 'File has been uploaded');
        return redirect()->back();
    }

    public function editorFileFeeSendMail(Request $request, $journal_path, $issue_id, ?string $email = null)
    {

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $editor = Editor::where('issue_id', $issue_id)->get();
        if (!$editor) {
            Alert::error('Error', 'Editor not found');
            return redirect()->back();
        }

        $file = EditorFileIssue::where('issue_id', $issue_id)->where('file_type', 'fee')->first();
        if (!$file) {
            Alert::error('Error', 'File not found');
            return redirect()->back();
        }

        $data = [
            'subject' => 'Fee Editor - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
            'journal' => $issue->journal->title,
            'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'attachments' => storage_path('app/public/' . $file->file),
            'setting_web' => SettingWebsite::first(),
        ];

        $emailAddress = [];
        if ($request->email) {
            $emailAddress = $email;
        } else {
            foreach ($editor as $editors) {
                if ($editors->email) {
                    $emailAddress[] = $editors->email;
                }
            }
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'production') {
            Mail::to($emailAddress)->send(new FeeEditorMail($data));
        } else {
            // For testing purpose
            Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new FeeEditorMail($data));
        }

        Alert::success('Success', 'email has been sent');
        return redirect()->back();
    }

    public function editorUpdate($journal_path, $issue_id, $id)
    {
        $validator = Validator::make(request()->all(), [
            'account_bank' => 'required|string',
            'account_number' => 'required|string',
        ], [
            'account_bank.required' => 'Bank harus diisi',
            'account_number.required' => 'Nomor Rekening harus diisi',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $editor = Editor::where('issue_id', $issue_id)->find($id);
        if (!$editor) {
            Alert::error('Error', 'Editor not found');
            return redirect()->back();
        }

        $editor->update([
            'account_bank' => request()->account_bank,
            'account_number' => request()->account_number,
        ]);
        Alert::success('Success', 'Editor has been updated');
        return redirect()->back();
    }

    public function editorDestroy($journal_path, $issue_id, $id)
    {
        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            Alert::error('Error', 'Journal not found');
            return back()->with('error', 'Journal not found');
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            Alert::error('Error', 'Issue not found');
            return back()->with('error', 'Issue not found');
        }

        $editor = Editor::where('issue_id', $issue_id)->find($id);
        if (!$editor) {
            Alert::error('Error', 'Editor not found');
            return back()->with('error', 'Editor not found');
        }

        $editor->delete();
        Alert::success('Success', 'Editor has been deleted');
        return redirect()->back();
    }

    //TODO: REVIEWER SECTION

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
            'file_sk' => ReviewerFileIssue::where('issue_id', $issue_id)->where('file_type', 'sk')->first(),
            'file_certificate' => ReviewerFileIssue::where('issue_id', $issue_id)->where('file_type', 'certificate')->first(),
            'file_fee' => ReviewerFileIssue::where('issue_id', $issue_id)->where('file_type', 'fee')->first(),
            'setting_web' => SettingWebsite::first(),
            // 'submissions' => $issue->submissions->pluck('submission_id'),
        ];
        // return response()->json($data);
        return view('back.pages.journal.detail-reviewer', $data);
    }

    public function reviewerFileSkStore(Request $request, $journal_path, $issue_id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:10240',
        ], [
            'file.required' => 'File harus diisi',
            'file.mimes' => 'File harus berupa pdf',
            'file.max' => 'File tidak boleh lebih dari 10 MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $file = $request->file('file');
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        ReviewerFileIssue::updateOrCreate(
            ['issue_id' => $issue_id, 'file_type' => 'sk'],
            ['file' => $file->storeAs('reviewer_file/sk', $filename, 'public')]
        );

        Alert::success('Success', 'File has been uploaded');
        return redirect()->back();
    }

    public function reviewerFileSkSendMail(Request $request, $journal_path, $issue_id, ?string $email = null)
    {

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $reviwer = Reviewer::where('issue_id', $issue_id)->get();
        if (!$reviwer) {
            Alert::error('Error', 'Reviewer not found');
            return redirect()->back();
        }

        $file = ReviewerFileIssue::where('issue_id', $issue_id)->where('file_type', 'sk')->first();
        if (!$file) {
            Alert::error('Error', 'File not found');
            return redirect()->back();
        }

        $data = [
            'subject' => 'SK Reviewer - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
            'journal' => $issue->journal->title,
            'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'attachments' => storage_path('app/public/' . $file->file),
            'setting_web' => SettingWebsite::first(),
        ];

        $emailAddress = [];
        if ($request->email) {
            $emailAddress = $email;
        } else {
            foreach ($reviwer as $reviewer) {
                if ($reviewer->email) {
                    $emailAddress[] = $reviewer->email;
                }
            }
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'production') {
            Mail::to($emailAddress)->send(new SkReviewerMail($data));
        } else {
            // For testing purpose
            Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new SkReviewerMail($data));
        }


        Alert::success('Success', 'email has been sent');
        return redirect()->back();
    }

    public function reviewerFileCertificateStore(Request $request, $journal_path, $issue_id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:10240',
        ], [
            'file.required' => 'File harus diisi',
            'file.mimes' => 'File harus berupa pdf',
            'file.max' => 'File tidak boleh lebih dari 10 MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $file = $request->file('file');
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        ReviewerFileIssue::updateOrCreate(
            ['issue_id' => $issue_id, 'file_type' => 'certificate'],
            ['file' => $file->storeAs('reviewer_file/certificate', $filename, 'public')]
        );

        Alert::success('Success', 'File has been uploaded');
        return redirect()->back();
    }

    public function reviewerFileCertificateSendMail(Request $request, $journal_path, $issue_id, ?string $email = null)
    {

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $reviwer = Reviewer::where('issue_id', $issue_id)->get();
        if (!$reviwer) {
            Alert::error('Error', 'Reviewer not found');
            return redirect()->back();
        }

        $file = ReviewerFileIssue::where('issue_id', $issue_id)->where('file_type', 'certificate')->first();
        if (!$file) {
            Alert::error('Error', 'File not found');
            return redirect()->back();
        }

        $data = [
            'subject' => 'Certificate Reviewer - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
            'journal' => $issue->journal->title,
            'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'attachments' => storage_path('app/public/' . $file->file),
            'setting_web' => SettingWebsite::first(),
        ];

        $emailAddress = [];
        if ($request->email) {
            $emailAddress = $email;
        } else {
            foreach ($reviwer as $reviewer) {
                if ($reviewer->email) {
                    $emailAddress[] = $reviewer->email;
                }
            }
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'production') {
            Mail::to($emailAddress)->send(new CertificateReviewerMail($data));
        } else {
            // For testing purpose
            Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new CertificateReviewerMail($data));
        }

        Alert::success('Success', 'email has been sent');
        return redirect()->back();
    }

    public function reviewerFileFeeStore(Request $request, $journal_path, $issue_id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:10240',
        ], [
            'file.required' => 'File harus diisi',
            'file.mimes' => 'File harus berupa pdf',
            'file.max' => 'File tidak boleh lebih dari 10 MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $file = $request->file('file');
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        ReviewerFileIssue::updateOrCreate(
            ['issue_id' => $issue_id, 'file_type' => 'fee'],
            ['file' => $file->storeAs('reviewer_file/fee', $filename, 'public')]
        );

        Alert::success('Success', 'File has been uploaded');
        return redirect()->back();
    }

    public function reviewerFileFeeSendMail(Request $request, $journal_path, $issue_id, ?string $email = null)
    {

        $journal = Journal::where('url_path', $journal_path)->first();
        if (!$journal) {
            return abort(404);
        }

        $issue = Issue::with('submissions')->find($issue_id);
        if (!$issue) {
            return abort(404);
        }

        $reviwer = Reviewer::where('issue_id', $issue_id)->get();
        if (!$reviwer) {
            Alert::error('Error', 'Reviewer not found');
            return redirect()->back();
        }

        $file = ReviewerFileIssue::where('issue_id', $issue_id)->where('file_type', 'fee')->first();
        if (!$file) {
            Alert::error('Error', 'File not found');
            return redirect()->back();
        }

        $data = [
            'subject' => 'Fee Reviewer - ' . $issue->journal->title . ' Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year . ': ' . $issue->title,
            'journal' => $issue->journal->title,
            'edition' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' Tahun ' . $issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'attachments' => storage_path('app/public/' . $file->file),
            'setting_web' => SettingWebsite::first(),
        ];

        $emailAddress = [];
        if ($request->email) {
            $emailAddress = $email;
        } else {
            foreach ($reviwer as $reviewer) {
                if ($reviewer->email) {
                    $emailAddress[] = $reviewer->email;
                }
            }
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'production') {
            Mail::to($emailAddress)->send(new FeeReviewerMail($data));
        } else {
            // For testing purpose
            Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new FeeReviewerMail($data));
        }

        Alert::success('Success', 'email has been sent');
        return redirect()->back();
    }

    public function reviewerUpdate($journal_path, $issue_id, $id)
    {
        $validator = Validator::make(request()->all(), [
            'nik' => 'required|string|max:100',
            'account_bank' => 'required|string',
            'account_number' => 'required|string',
            'npwp' => 'nullable|string|max:100',
        ], [
            'nik.required' => 'NIK harus diisi',
            'nik.string' => 'NIK harus berupa string',
            'nik.max' => 'NIK tidak boleh lebih dari 100 karakter',
            'account_bank.string' => 'Bank harus berupa string',
            'account_bank.required' => 'Bank harus diisi',
            'account_number.required' => 'Nomor Rekening harus diisi',
            'account_number.string' => 'Nomor Rekening harus berupa string',
            'npwp.string' => 'NPWP harus berupa string',
            'npwp.max' => 'NPWP tidak boleh lebih dari 100 karakter',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $reviewer = Reviewer::where('issue_id', $issue_id)->find($id);
        if (!$reviewer) {
            Alert::error('Error', 'Reviewer not found');
            return redirect()->back();
        }

        $reviewer->update([
            'nik' => request()->nik,
            'account_bank' => request()->account_bank,
            'account_number' => request()->account_number,
            'npwp' => request()->npwp,
        ]);
        Alert::success('Success', 'Reviewer has been updated');
        return redirect()->back();
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
