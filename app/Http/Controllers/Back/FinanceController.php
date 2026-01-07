<?php

namespace App\Http\Controllers\Back;

use App\Exports\CashflowExport;
use App\Exports\FinanceReportExport;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmPaymentMail;
use App\Models\Finance;
use App\Models\FinanceYear;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\PaymentInvoice;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use illuminate\Support\Str;


class FinanceController extends Controller
{

    protected $cookie_control_panel;

    public function __construct()
    {
        $this->cookie_control_panel = Cookie::get('control_panel');

        if (!$this->cookie_control_panel) {
            redirect()->route('back.dashboard')->with('permission_denied', 'Anda tidak memiliki akses modul ini')->send();
        }
    }

    public function verificationIndex()
    {

        $data = [
            'title' => 'Verifikasi Pembayaran',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Finance',
                    'link' => route('back.finance.verification.index')
                ]
            ],
            'payment' => Payment::with(['paymentInvoice'])
                ->orderBy('created_at', 'desc')
                ->get(),
            'journals' => Journal::where('type', $this->cookie_control_panel)->get(),

        ];
        return view('back.pages.finance.verification', $data);
    }

    public function verificationDatatable(Request $request)
    {
        $journal_id = $request->journal_id;
        $submission_search = $request->submission_search;
        $payment_status = $request->payment_status;
        $payment_timestamp_start = $request->payment_timestamp_start;
        $payment_timestamp_end = $request->payment_timestamp_end;

        $type = $this->cookie_control_panel;

        // $payment = Submission::all();
        // dd($payment);
        $payment = Payment::with(['paymentInvoice.submission'])
            ->when($type, function ($query) use ($type) {
                return $query->whereHas('paymentInvoice.submission.issue.journal', function ($q) use ($type) {
                    $q->where('type', $type);
                });
            })
            ->when($journal_id, function ($query) use ($journal_id) {
                return $query->whereHas('paymentInvoice.submission.issue', function ($q) use ($journal_id) {
                    $q->where('journal_id', $journal_id);
                });
            })
            ->when($submission_search, function ($query) use ($submission_search) {
                return $query->whereHas('paymentInvoice.submission', function ($q) use ($submission_search) {
                    $q->where('submission_id', 'like', '%' . $submission_search . '%')
                        ->orWhere('fullTitle', 'like', '%' . $submission_search . '%')
                        ->orWhere('authorsString', 'like', '%' . $submission_search . '%');
                });
            })
            ->when($payment_status, function ($query) use ($payment_status) {
                return $query->where('payment_status', $payment_status);
            })
            ->when($payment_timestamp_start, function ($query) use ($payment_timestamp_start) {
                return $query->whereDate('payment_timestamp', '>=', date('Y-m-d H:i:s', strtotime($payment_timestamp_start)));
            })
            ->when($payment_timestamp_end, function ($query) use ($payment_timestamp_end) {
                return $query->whereDate('payment_timestamp', '<=', date('Y-m-d H:i:s', strtotime($payment_timestamp_end)));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $payment_pending = $payment->where('payment_status', 'pending')->count();
        $payment_accepted = $payment->where('payment_status', 'accepted')->count();
        $payment_rejected = $payment->where('payment_status', 'rejected')->count();
        $payment_total = $payment->count();

        // return response()->json([
        //     'data' => $payment,
        // ]);

        return datatables()
            ->of($payment)
            ->addColumn('payment', function ($payment) {
                return '
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 mb-1">' . $payment->payment_timestamp->format('d M Y H:i:s') . '</span>
                            <span>Nama: ' . $payment->name . '</span>
                            <span>Email:  ' . $payment->email . '</span>
                            <span>phone: ' . $payment->phone . '</span>
                        </div>
                ';
            })
            ->addColumn('invoice', function ($payment) {
                return '
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 mb-1">INVOICE ' . $payment->paymentInvoice->invoice_number . '/JRNL/UINSMDD/' . $payment->paymentInvoice->created_at->format('Y') . '</span>
                            <span>Persentase: ' . $payment->paymentInvoice->payment_percent . '%</span>
                            <span>Jumlah: Rp ' . number_format($payment->paymentInvoice->payment_amount, 0, ',', '.') . '</span>
                        </div>
                ';
            })
            ->addColumn('submission', function ($payment) {
                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary"> Submission ID: ' . $payment->paymentInvoice->submission->submission_id . '</a>
                                <span class="text-gray-800 ">' . $payment->paymentInvoice->submission->fullTitle . '</span>
                            <span >' . $payment->paymentInvoice->submission->authorsString . '</span>
                        </div>
                ';
            })
            ->addColumn('journal', function ($payment) {

                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary mb-1">' . $payment->paymentInvoice->submission->issue->journal->title . '</a>
                            <span> Vol. ' . $payment->paymentInvoice->submission->issue->volume . ' No. ' . $payment->paymentInvoice->submission->issue->number . ' (' . $payment->paymentInvoice->submission->issue->year . '): ' . $payment->paymentInvoice->submission->issue->title .  '</span>
                        </div>
                ';
            })
            ->addColumn('status', function ($payment) {
                $status_temp = '';
                if ($payment->payment_status == 'pending') {
                    $status_temp = '<span class="badge badge-light-warning text-center">Pending</span>';
                } elseif ($payment->payment_status == 'accepted') {
                    $status_temp = '<span class="badge badge-light-success">Accepted</span>';
                } elseif ($payment->payment_status == 'rejected') {
                    $status_temp = '<span class="badge badge-light-danger">Rejected</span>';
                } else {
                    $status_temp = '<span class="badge badge-light-primary">' . $payment->payment_status . '</span>';
                }
                return $status_temp;
            })
            ->addColumn('action', function ($payment) {
                $deleteButton = '';
                if (Auth()->user()->hasRole('super-admin')) {
                    $deleteButton = '
                        <a href="' . route("back.finance.verification.delete", $payment->id) . '" class="btn btn-sm btn-light-danger my-1">
                            <i class="ki-duotone ki-trash fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Hapus
                        </a>
                    ';
                }
                if ($payment->payment_status == 'accepted') {
                    return '
                    <a href="' . route("back.finance.verification.detail", $payment->id) . '" class="btn btn-sm btn-light-primary my-1">
                        <i class="ki-duotone ki-eye fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i> Detail
                    </a>
                    ' . $deleteButton . '
                    <br>
                    <a href="' . route("back.finance.confirm-payment.generate", $payment->id) . '" class="btn btn-sm btn-light-info my-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Download Konfirmasi Pembayaran">
                        <i class="ki-duotone ki-file-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <a href="' . route("back.finance.confirm-payment.mail-send", $payment->id) . '" class="btn btn-sm btn-light-warning my-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kirim konfirmasi pembayaran melalui email">
                        <i class="ki-duotone ki-send fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                ';
                } else {
                    return '
                    <a href="' . route("back.finance.verification.detail", $payment->id) . '" class="btn btn-sm btn-light-primary my-1">
                        <i class="ki-duotone ki-eye fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i> Detail
                    </a>
                    ' . $deleteButton . '
                    ';
                }
            })
            ->with([
                'payment_pending' => $payment_pending,
                'payment_accepted' => $payment_accepted,
                'payment_rejected' => $payment_rejected,
                'payment_total' => $payment_total,
            ])
            ->rawColumns([
                'payment',
                'invoice',
                'submission',
                'journal',
                'status',
                'action'
            ])
            ->make(true);
    }

    public function verificationDetail($id)
    {
        $data = [
            'title' => 'Detail Pembayaran',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Finance',
                    'link' => route('back.finance.verification.index')
                ],
                [
                    'name' => 'Detail Pembayaran',
                    'link' => route('back.finance.verification.detail', $id)
                ]
            ],
            'payment' => Payment::with(['paymentInvoice.submission.issue.journal'])->findOrFail($id),
        ];
        return view('back.pages.finance.verification-show', $data);
    }

    public function verificationUpdate(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'payment_status' => 'required|in:pending,accepted,rejected',
                'payment_note' => 'nullable|string|max:255',
            ],
            [
                'payment_status.required' => 'Status Pembayaran harus diisi',
                'payment_status.in' => 'Status Pembayaran tidak valid',
                'payment_note.string' => 'Catatan harus berupa teks',
                'payment_note.max' => 'Catatan maksimal 255 karakter',
            ]
        );
        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $payment = Payment::findOrFail($id);
        $payment->update([
            'payment_status' => $request->payment_status,
            'payment_note' => $request->payment_note,
        ]);

        if ($request->payment_status == 'accepted') {

            $payment->paymentInvoice->update([
                'is_paid' => 1,
            ]);

            $mailData = [
                'subject' => 'Confirmation Payment Accepted',
                'number' => $payment->paymentInvoice->invoice_number ?? "0000",
                'year' => $payment->paymentInvoice->created_at->format('Y') ?? Carbon::now()->format('Y'),
                'authorString' => $payment->paymentInvoice->submission->authorsString,
                'name' => $payment->paymentInvoice->submission->authors[0]['name'],
                'affiliation' => $payment->paymentInvoice->submission->authors[0]['affiliation'],
                'title' => $payment->paymentInvoice->submission->fullTitle,
                'journal' => $payment->paymentInvoice->submission->issue->journal->title,
                'edition' => 'Vol. ' . $payment->paymentInvoice->submission->issue->volume . ' No. ' . $payment->paymentInvoice->submission->issue->number . ' Tahun ' . $payment->paymentInvoice->submission->issue->year,
                'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
                'id' => $payment->paymentInvoice->submission->submission_id,
                'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($payment->paymentInvoice->submission->issue->journal->getJournalThumbnail())),
                'payment_account_name' => $payment->payment_account_name,
                'payment_amount' => $payment->paymentInvoice->payment_amount,
                'payment_timestamp' => $payment->payment_timestamp->translatedFormat('d F Y H:i:s'),
                'setting_web' => SettingWebsite::first(),
            ];

            $pdf = Pdf::loadView('back.pages.journal.pdf.confirm-payment', $mailData)->setPaper('A4', 'portrait');
            $path = 'arsip/payment/' . $payment->paymentInvoice->created_at->format('Y') . '/' . $payment->paymentInvoice->invoice_number . '/confirm-payment-' . $payment->paymentInvoice->submission->submission_id . '.pdf';

            Storage::disk('public')->put($path, $pdf->output());
            $mailData['attachments'] = storage_path('app/public/' . $path);

            $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
            if ($mailEnvirontment == 'production') {
                Mail::to($payment->email)->send(new ConfirmPaymentMail($mailData));
            } else {
                Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new ConfirmPaymentMail($mailData));
            }
        }

        $paymentCompleteCheck = PaymentInvoice::where('submission_id', $payment->paymentInvoice->submission_id)->where('is_paid', 1)->pluck('payment_percent')->toArray();
        $paymentCompleteCheck = array_sum($paymentCompleteCheck);

        if ($paymentCompleteCheck >= 100) {
            $submission = Submission::findOrFail($payment->paymentInvoice->submission_id);
            $submission->update([
                'payment_status' => 'paid',
            ]);
        }

        Alert::success('Berhasil', 'Pembayaran berhasil diperbarui dan email konfirmasi berhasil dikirim');
        return redirect()->route('back.finance.verification.index')->with('success', 'Pembayaran berhasil diperbarui');
    }

    public function verificationDelete(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        if (!$payment) {
            Alert::error('Error', 'Payment not found');
            return redirect()->back()->with('error', 'Payment not found');
        }
        if ($payment->paymentInvoice->is_paid) {
            Alert::error('Error', 'Payment cannot be deleted because it has been paid');
            return redirect()->back()->with('error', 'Payment cannot be deleted because it has been paid');
        }
        if ($payment->payment_file && Storage::exists($payment->payment_file)) {
            try {
                Storage::delete($payment->payment_file);
            } catch (\Exception $e) {
            }
        }
        $payment->delete();
        Alert::success('Berhasil', 'Pembayaran berhasil dihapus');
        return redirect()->route('back.finance.verification.index')->with('success', 'Pembayaran berhasil dihapus');
    }

    public function confirmPaymentGenerate(Request $request, $id)
    {
        $payment = Payment::with(['paymentInvoice.submission.issue.journal'])->findOrFail($id);
        if (!$payment) {
            Alert::error('Error', 'Submission not found');
            return redirect()->back()->with('error', 'Submission not found');
        }

        $data = [
            'number' => $payment->paymentInvoice->invoice_number ?? "0000",
            'year' => $payment->paymentInvoice->created_at->format('Y') ?? Carbon::now()->format('Y'),
            'name' => $payment->paymentInvoice->submission->authors[0]['name'],
            'affiliation' => $payment->paymentInvoice->submission->authors[0]['affiliation'],
            'title' => $payment->paymentInvoice->submission->fullTitle,
            'journal' => $payment->paymentInvoice->submission->issue->journal->title,
            'edition' => 'Vol. ' . $payment->paymentInvoice->submission->issue->volume . ' No. ' . $payment->paymentInvoice->submission->issue->number . ' Tahun ' . $payment->paymentInvoice->submission->issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'id' => $payment->paymentInvoice->submission->submission_id,
            'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($payment->paymentInvoice->submission->issue->journal->getJournalThumbnail())),
            'payment_account_name' => $payment->payment_account_name,
            'payment_amount' => $payment->paymentInvoice->payment_amount,
            'payment_timestamp' => $payment->payment_timestamp->translatedFormat('d F Y H:i:s'),
        ];


        // dd($data);
        $pdf = Pdf::loadView('back.pages.journal.pdf.confirm-payment', $data)->setPaper('A4', 'portrait');
        return $pdf->stream('Confirm-Payment-' . $payment->paymentInvoice->submission->submission_id . '.pdf');
    }

    public function confirmPaymentMailSend(Request $request, $id)
    {
        $payment = Payment::with(['paymentInvoice.submission.issue.journal'])->findOrFail($id);
        if (!$payment) {
            Alert::error('Error', 'Submission not found');
            return redirect()->back()->with('error', 'Submission not found');
        }

        $data = [
            'subject' => 'Confirmation Payment Accepted',
            'number' => $payment->paymentInvoice->invoice_number ?? "0000",
            'year' => $payment->paymentInvoice->created_at->format('Y') ?? Carbon::now()->format('Y'),
            'authorString' => $payment->paymentInvoice->submission->authorsString,
            'name' => $payment->paymentInvoice->submission->authors[0]['name'],
            'affiliation' => $payment->paymentInvoice->submission->authors[0]['affiliation'],
            'title' => $payment->paymentInvoice->submission->fullTitle,
            'journal' => $payment->paymentInvoice->submission->issue->journal->title,
            'edition' => 'Vol. ' . $payment->paymentInvoice->submission->issue->volume . ' No. ' . $payment->paymentInvoice->submission->issue->number . ' Tahun ' . $payment->paymentInvoice->submission->issue->year,
            'date' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'id' => $payment->paymentInvoice->submission->submission_id,
            'journal_thumbnail' => 'data:image/png;base64,' . base64_encode(file_get_contents($payment->paymentInvoice->submission->issue->journal->getJournalThumbnail())),
            'payment_account_name' => $payment->payment_account_name,
            'payment_amount' => $payment->paymentInvoice->payment_amount,
            'payment_timestamp' => $payment->payment_timestamp->translatedFormat('d F Y H:i:s'),
            'setting_web' => SettingWebsite::first(),
        ];

        if (Storage::exists('arsip/payment/' . $payment->paymentInvoice->created_at->format('Y') . '/' . $payment->paymentInvoice->invoice_number . '/confirm-payment-' . $payment->paymentInvoice->submission->submission_id . '.pdf')) {
            $data['attachments'] = storage_path('app/public/arsip/payment/' . $payment->paymentInvoice->created_at->format('Y') . '/' . $payment->paymentInvoice->invoice_number . '/confirm-payment-' . $payment->paymentInvoice->submission->submission_id . '.pdf');
        } else {
            $pdf = Pdf::loadView('back.pages.journal.pdf.invoice', $data)->setPaper('A4', 'portrait');
            $path = 'arsip/payment/' . $payment->paymentInvoice->created_at->format('Y') . '/' . $payment->paymentInvoice->invoice_number . '/confirm-payment-' . $payment->paymentInvoice->submission->submission_id . '.pdf';

            Storage::disk('public')->put($path, $pdf->output());
            $data['attachments'] = storage_path('app/public/' . $path);
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'production') {
            Mail::to($payment->email)->send(new ConfirmPaymentMail($data));
        } else {
            Mail::to(env('MAIL_LOCAL_ADDRESS'))->send(new ConfirmPaymentMail($data));
        }

        Alert::success('Berhasil', 'Email konfirmasi pembayaran berhasil dikirim');
        return redirect()->route('back.finance.verification.index')->with('success', 'Email konfirmasi pembayaran berhasil dikirim');
    }

    public function reportIndex()
    {
        $data = [
            'title' => 'Laporan Jurnal',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Jurnal',
                    'link' => route('back.finance.report.index')
                ]
            ],
            'journals' => Journal::where('type', $this->cookie_control_panel)->get(),
        ];
        return view('back.pages.finance.report', $data);
    }

    public function reportDatatable(Request $request)
    {
        $journal_id = $request->journal_id;
        $date_end = $request->date_end ?? now()->toDateString();
        $date_start = $request->date_start ?? now()->subMonth()->toDateString();
        $issue_id = $request->issue_id;

        $type = $this->cookie_control_panel;


        $submission = Submission::with(['paymentInvoices.submission.issue.journal'])
            ->when($type, function ($query) use ($type) {
                return $query->whereHas('issue.journal', function ($q) use ($type) {
                    $q->where('type', $type);
                });
            })
            ->when($journal_id, function ($query) use ($journal_id) {
                return $query->whereHas('issue.journal', function ($q) use ($journal_id) {
                    $q->where('id', $journal_id);
                });
            })
            ->when($issue_id, function ($query) use ($issue_id) {
                return $query->where('issue_id', $issue_id);
            })
            ->when($date_start, function ($query) use ($date_start) {
                return $query->whereHas('paymentInvoices', function ($q) use ($date_start) {
                    $q->whereDate('created_at', '>=', date('Y-m-d H:i:s', strtotime($date_start)));
                });
            })
            ->when($date_end, function ($query) use ($date_end) {
                return $query->whereHas('paymentInvoices', function ($q) use ($date_end) {
                    $q->whereDate('created_at', '<=', date('Y-m-d H:i:s', strtotime($date_end)));
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();




        $total_income = $submission->sum(function ($item) {
            return $item->paymentInvoices->where('is_paid', 1)->sum('payment_amount');
        });
        // dd($total_income);
        $total_expense = 0;
        $total_balance = $total_income - $total_expense;

        return datatables()
            ->of($submission)
            ->addColumn('journal', function ($submission) {
                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary mb-1">' . $submission->issue->journal->title . '</a>
                        </div>
                ';
            })
            ->addColumn('author', function ($submission) {
                $author = "";
                $authorList = '';
                foreach ($submission->authors as $key => $authorData) {
                    $authorList .= '<li> <b>' . $authorData['name'] . ' </b> <br>' . $authorData['affiliation'] . '</li>';
                }
                $author = '<ul>' . $authorList . '</ul>';

                return $author;
            })
            ->addColumn('submission', function ($submission) {
                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary"> Submission ID: ' . $submission->submission_id . '</a>
                                <span class="text-gray-800 ">' . $submission->fullTitle . '</span>
                        </div>
                ';
            })
            ->addColumn('edition', function ($submission) {
                return '
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 mb-1">Vol. ' . $submission->issue->volume . ' No. ' . $submission->issue->number . ' (' . $submission->issue->year . '): ' . $submission->issue->title . '</span>
                        </div>
                ';
            })
            ->addColumn('payment_info', function ($submission) {
                $paymentInfo = '';
                foreach ($submission->paymentInvoices as $paymentInvoice) {
                    $paymentInfo .= '
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 mb-1">INVOICE ' . $paymentInvoice->invoice_number . '/JRNL/UINSMDD/' . $paymentInvoice->created_at->format('Y') . '</span>
                            <span>pembayaran: ' . $paymentInvoice->payment_percent . '% - Rp ' . number_format($paymentInvoice->payment_amount, 0, ',', '.') .  ($paymentInvoice->is_paid ? ' <span class="badge badge-light-success">Sudah Dibayar</span>' : ' <span class="badge badge-light-warning">Belum Dibayar</span>') . '</span>
                        </div>
                    ';
                }
                if ($paymentInfo == '') {
                    $paymentInfo = '<span class="text-muted">Tidak ada informasi pembayaran</span>';
                }
                return $paymentInfo;
            })
            ->addColumn('loa', function ($submission) {
                $authorId = $submission->authors[0]['id'] ?? null;
                if (Storage::exists('arsip/loa/' . 'LoA-'  . $submission->submission_id  . '-' . $submission->id . '-' . $authorId . '.pdf')) {
                    return '
                        <span class="text-success">LoA Sudah Dikirim</span>
                        ';
                } else {
                    return '<span class="text-muted">LoA Belum Terbit</span>';
                }
            })
            ->with([
                'total_income' => $total_income,
                'total_expense' => $total_expense,
                'total_balance' => $total_balance,
            ])
            ->rawColumns([
                'journal',
                'author',
                'submission',
                'edition',
                'payment_info',
                'loa'
            ])
            ->make(true);
    }

    public function reportExport(Request $request)
    {
        $journal_id = $request->journal_id;
        $issue_id = $request->issue_id;
        $date_end = $request->date_end ?? now()->toDateString();
        $date_start = $request->date_start ?? now()->subMonth()->toDateString();
        $type = $this->cookie_control_panel;

        return Excel::download(new FinanceReportExport($journal_id, $issue_id, $date_start, $date_end, $type), 'laporan-journal-' . date('Y-m-d') . '.xlsx');
    }

    public function cashflowYearStore(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255|unique:finance_years,name',
                'start_date' => 'required|date',
                'distribution_percentage' => 'required|integer|min:0|max:100',
            ],
            [
                'name.required' => 'Nama Tahun Keuangan harus diisi',
                'name.string' => 'Nama Tahun Keuangan harus berupa teks',
                'name.max' => 'Nama Tahun Keuangan maksimal 255 karakter',
                'name.unique' => 'Nama Tahun Keuangan sudah ada',
                'start_date.required' => 'Tanggal Mulai harus diisi',
                'start_date.date' => 'Tanggal Mulai tidak valid',
                'distribution_percentage.required' => 'Persentase Distribusi harus diisi',
                'distribution_percentage.integer' => 'Persentase Distribusi harus berupa angka',
                'distribution_percentage.min' => 'Persentase Distribusi minimal 0',
                'distribution_percentage.max' => 'Persentase Distribusi maksimal 100',
            ]
        );
        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        FinanceYear::latest()->first()?->update([
            'end_date' => Carbon::parse($request->start_date)->subDay()->toDateString(),
            'is_active' => 0,
        ]);
        FinanceYear::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => Auth::user()->name,
        ]);

        Alert::success('Berhasil', 'Tahun Keuangan berhasil ditambahkan');
        return redirect()->back()->with('success', 'Tahun Keuangan berhasil ditambahkan');
    }

    public function cashflowYearEdit(Request $request)
    {
        $finance_year = FinanceYear::latest()->first();
        if (!$finance_year) {
            Alert::error('Gagal', 'Tahun Keuangan tidak ditemukan');
            return redirect()->back()->with('error', 'Tahun Keuangan tidak ditemukan');
        }
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255|unique:finance_years,name,' . $finance_year->id,
                'start_date' => 'required|date',
                'distribution_percentage' => 'required|integer|min:0|max:100',
            ],
            [
                'name.required' => 'Nama Tahun Keuangan harus diisi',
                'name.string' => 'Nama Tahun Keuangan harus berupa teks',
                'name.max' => 'Nama Tahun Keuangan maksimal 255 karakter',
                'name.unique' => 'Nama Tahun Keuangan sudah ada',
                'start_date.required' => 'Tanggal Mulai harus diisi',
                'start_date.date' => 'Tanggal Mulai tidak valid',
                'distribution_percentage.required' => 'Persentase Distribusi harus diisi',
                'distribution_percentage.integer' => 'Persentase Distribusi harus berupa angka',
                'distribution_percentage.min' => 'Persentase Distribusi minimal 0',
                'distribution_percentage.max' => 'Persentase Distribusi maksimal 100',
            ]
        );
        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $finance_year->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'distribution_percentage' => $request->distribution_percentage,
            ]);
            Alert::success('Berhasil', 'Tahun Keuangan berhasil diperbarui');
            return redirect()->back()->with('success', 'Tahun Keuangan berhasil diperbarui');
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Tahun Keuangan gagal diperbarui');
            return redirect()->back()->with('error', 'Tahun Keuangan gagal diperbarui');
        }
    }

    public function cashflowIndex(Request $request)
    {
        $id = $request->id;
        $type = $this->cookie_control_panel;

        if ($id) {
            $finance_year_now = FinanceYear::where('type_control', $type)->findOrFail($id);
            $start_date = $finance_year_now->start_date;
            $end_date = $finance_year_now->end_date ?? now()->addDay()->toDateString();
        } else {
            $finance_year_now = FinanceYear::latest()->where('type_control', $type)->first();
            $start_date = $finance_year_now ? $finance_year_now->start_date : now()->startOfYear()->toDateString();
            $end_date = $finance_year_now && $finance_year_now->end_date ? $finance_year_now->end_date : now()->addDay()->toDateString();
        }

        $finance_now = Finance::where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)->where('type_control', $type);

        $outcome = (clone $finance_now)->where('type', 'expense')->sum('amount');
        $income_temp = (clone $finance_now)->where('type', 'income')->sum('amount');

        $payment = Payment::with(['paymentInvoice'])
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->where('payment_status', 'accepted')
            ->whereHas('paymentInvoice.submission.issue.journal', function ($query) use ($type) {
                $query->where('type', $type);
            })
            // ->where('type_control', $type)
            ->get()
            ->map(function ($item) {
                return $item->paymentInvoice->payment_amount ?? 0;
            })->sum();

        $income = $income_temp + $payment;
        $balance = $income - $outcome;
        $data = [
            'title' => 'Laporan Keuangan',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Keuangan',
                    'link' => route('back.finance.cashflow.index')
                ]
            ],
            'finance_year' => $finance_year_now,
            'list_finance_year' => FinanceYear::latest()->where('type_control', $type)->get(),
            'total_outcome_now' => $outcome,
            'total_income_now' => $income,
            'total_balance_now' => $balance,
            'total_distribution' => $finance_year_now ? ($income * ($finance_year_now->distribution_percentage / 100)) : 0,
            'total_distributtion_other' => $finance_year_now ? ($income * ((100 - $finance_year_now->distribution_percentage) / 100)) : 0,

        ];
        // return response()->json($data);
        return view('back.pages.finance.cashflow', $data);
    }

    public function cashflowDatatables(Request $request)
    {
        $type = $request->type ?? "all";
        $date_end = $request->date_end ?? now()->toDateString();
        $date_start = $request->date_start ?? now()->subMonth()->toDateString();

        $type_control = $this->cookie_control_panel;

        $finance = Finance::where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->where('type_control', $type_control)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'type' => $item->type,
                    'amount' => $item->amount,
                    'date' => $item->date,
                    'payment_method' => $item->payment_method,
                    'payment_reference' => $item->payment_reference,
                    'payment_note' => $item->payment_note,
                    'attachment' => $item->attachment,
                    'editable' => true,
                    'created_at' => $item->created_at,
                    'created_by' => $item->created_by,
                    'updated_at' => $item->updated_at,
                    'updated_by' => $item->updated_by,
                ];
            })->collect();

        $billing = Payment::with(['paymentInvoice.submission.issue.journal'])
            ->whereHas('paymentInvoice.submission.issue.journal', function ($query) use ($type_control) {
                $query->where('type', $type_control);
            })
            ->whereBetween('created_at', [$date_start, $date_end])
            ->where('payment_status', 'accepted')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => null,
                    'name' => 'Pembayaran Invoice ' . ($item->paymentInvoice->invoice_number ?? 'Unknown Invoice')  . "/JRNL/UINSMDD/" . ($item->paymentInvoice->created_at ? $item->paymentInvoice->created_at->format('Y') : '-'),
                    'description' => 'Pembayaran Invoice ' . ($item->paymentInvoice->invoice_number ?? 'Unknown Invoice') . "/JRNL/UINSMDD/" . ($item->paymentInvoice->created_at ? $item->paymentInvoice->created_at->format('Y') : '-') . ' Yang Telah Dibayarkan Oleh ' . ($item->name ?? 'Unknown Payer'),
                    'type' => 'income',
                    'amount' => $item->paymentInvoice->payment_amount ?? 0,
                    'date' => $item->payment_timestamp,
                    'payment_method' => ($item->payment_method ?? "-") . ' a/n ' . ($item->payment_account_name ?? "-"),
                    'payment_reference' => "-",
                    'payment_note' => $item->payment_note,
                    'attachment' => $item->payment_file,
                    'editable' => false,
                    'created_at' => $item->created_at,
                    'created_by' => $item->created_by ?? '-',
                    'updated_at' => $item->updated_at,
                    'updated_by' => $item->updated_by ?? '-',
                ];
            })->collect();


        $data = $finance->merge($billing)->when($type != 'all', function ($query) use ($type) {
            return $query->where('type', $type);
        })->sortByDesc('date')->values();

        $total_income = $data->where('type', 'income')->sum('amount');
        $total_expense = $data->where('type', 'expense')->sum('amount');
        $total_balance = $total_income - $total_expense;

        return datatables()->of($data)
            ->addColumn('transaction', function ($row) {
                return '<div class="d-flex flex-column">
                            <a href="#"
                            class="text-gray-800 text-hover-primary mb-1">' . $row->name . '</a>
                            <span class="text-muted">' . $row->description . '</span>
                        </div>';
            })
            ->addColumn('date', function ($row) {
                return '<span class="fw-bold">' . Carbon::parse($row->date)->format('d M Y') . '</span>';
            })
            ->addColumn('amount', function ($row) {
                if ($row->type == 'income') {
                    return '<span class="text-success">+' . number_format($row->amount, 0, ',', '.') . '</span>';
                } else {
                    return '<span class="text-danger">-' . number_format($row->amount, 0, ',', '.') . '</span>';
                }
            })
            ->addColumn('type', function ($row) {
                return '<span class="badge badge-' . ($row->type == 'income' ? 'success' : 'danger') . '">' . $row->type . '</span>';
            })
            ->addColumn('payment_info', function ($row) {
                return '<ul>
                            <li>
                                <span class="fw-bold">Metode Pembayaran:</span>
                                <span>' . ($row->payment_method ?? '-') . '</span>
                            </li>
                            <li>
                                <span class="fw-bold">No Ref:</span>
                                <span>' . ($row->payment_reference ?? '-') . '</span>
                            </li>
                            <li>
                                <span class="fw-bold">Note:</span>
                                <span>' . ($row->payment_note ?? '-') . '</span>
                            </li>
                        </ul>';
            })
            ->addColumn('attachment', function ($row) {
                if ($row->attachment) {
                    return '<a href="' . asset('storage/' . $row->attachment) . '" target="_blank">
                        <i class="ki-duotone ki-file-added text-primary fs-3x" data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat File">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>';
                } else {
                    return '<i class="ki-duotone ki-file-deleted text-danger fs-3x" data-bs-toggle="tooltip" data-bs-placement="right" title="File Tidak Ada">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>';
                }
            })
            ->addColumn('log', function ($row) {
                return '<ul>
                    <li>
                        <span class="fw-bold">Created At:</span>
                        <span>' . Carbon::parse($row->created_at)->format('d M Y H:i') . '</span>
                    </li>
                    <li>
                        <span class="fw-bold">Created By:</span>
                        <span>' . ($row->created_by ? (User::find($row->created_by)->name ?? "-") : '-') . '</span>
                    </li>

                    <br>

                    <li>
                        <span class="fw-bold">Update At:</span>
                        <span>' . Carbon::parse($row->updated_at)->format('d M Y H:i')  . '</span>
                    </li>
                    <li>
                        <span class="fw-bold">Update By:</span>
                        <span>' . ($row->updated_by ? (User::find($row->updated_by)->name ?? "-") : '-') . '</span>
                    </li>
                </ul>';
            })
            ->addColumn('action', function ($row) {
                if ($row->editable) {
                    return ' <div class="d-flex justify-content-end">
                        <a href="#" class="btn btn-icon btn-light-warning me-3" data-bs-toggle="modal" data-bs-target="#edit_' . $row->id . '"><i class="fa-solid fa-pen-to-square fs-4"></i></a>
                        <a href="#" class="btn btn-icon btn-light-danger" data-bs-toggle="modal" data-bs-target="#delete_' . $row->id . '"><i class="fa-solid fa-trash fs-4"></i></a>
                    </div>
                    <!-- Modal Edit -->
                    <div class="modal fade" tabindex="-1" id="edit_' . $row->id . '" aria-labelledby="editLabel_' . $row->id . '" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editLabel_' . $row->id . '">Edit Transaksi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="' . route('back.finance.cashflow.update', $row->id) . '" method="POST" enctype="multipart/form-data">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <div class="modal-body">
                                        <div class="mb-5">
                                            <label for="name_' . $row->id . '" class="form-label required">Nama Transaksi</label>
                                            <input type="text" class="form-control" id="name_' . $row->id . '" name="name" value="' . $row->name . '" required>
                                        </div>
                                        <div class="mb-5">
                                            <label for="description_' . $row->id . '" class="form-label">Deskripsi</label>
                                            <textarea class="form-control" id="description_' . $row->id . '" name="description">' . $row->description . '</textarea>
                                        </div>
                                        <div class="mb-5">
                                            <label for="amount_' . $row->id . '" class="form-label required">Jumlah</label>
                                            <div class="input-group mb-5">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control" id="amount_' . $row->id . '" name="amount" value="' . $row->amount . '" required>
                                            </div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="date_' . $row->id . '" class="form-label required">Tanggal</label>
                                            <input type="date" class="form-control" id="date_' . $row->id . '" name="date" value="' . Carbon::parse($row->date)->format('Y-m-d') . '" required>
                                        </div>
                                        <div class="mb-5">
                                            <label for="type_' . $row->id . '" class="form-label required">Type</label>
                                            <select class="form-select" id="type_' . $row->id . '" name="type" required>
                                                <option value="income" ' . ($row->type == 'income' ? 'selected' : '') . '>Income</option>
                                                <option value="expense" ' . ($row->type == 'expense' ? 'selected' : '') . '>Expense</option>
                                            </select>
                                        </div>
                                        <div class="mb-5">
                                            <div class="row mb-5">
                                                <div class="col">
                                                    <label for="payment_method_' . $row->id . '" class="form-label">Metode Pembayaran</label>
                                                    <input type="text" class="form-control" id="payment_method_' . $row->id . '" name="payment_method" value="' . $row->payment_method . '">
                                                </div>
                                                <div class="col">
                                                    <label for="payment_reference_' . $row->id . '" class="form-label">No Referensi</label>
                                                    <input type="text" class="form-control" id="payment_reference_' . $row->id . '" name="payment_reference" value="' . $row->payment_reference . '">
                                                </div>
                                            </div>
                                            <div class="mb-5">
                                                <label for="payment_note_' . $row->id . '" class="form-label">Note</label>
                                                <textarea class="form-control" id="payment_note_' . $row->id . '" name="payment_note">' . $row->payment_note . '</textarea>
                                            </div>
                                            <div class="mb-5">
                                                <label for="attachment_' . $row->id . '" class="form-label">Lampiran</label>
                                                <input type="file" class="form-control" id="attachment_' . $row->id . '" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                                                <div class="mt-2">
                                                    File saat ini:
                                                    <a href="' . ($row->attachment ? asset('storage/' . $row->attachment) : '#') . '" target="_blank">
                                                        ' . ($row->attachment ? basename($row->attachment) : 'Tidak ada file yang diunggah') . '
                                                    </a>
                                                </div>
                                                <small class="form-text text-muted">Format: jpg, jpeg, png, pdf. Maksimal 10MB.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Batal
                                        </button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Delete -->
                    <div class="modal fade" tabindex="-1" id="delete_' . $row->id . '" aria-labelledby="deleteLabel_' . $row->id . '" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="deleteLabel_' . $row->id . '">Hapus Transaksi</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body mb-5">
                                    <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
                                    <p class="text-danger">
                                        <strong>Peringatan: </strong> Seluruh data yang terkait dengan transaksi ini
                                        akan dihapus dan tidak dapat dikembalikan.
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <form action="' . route('back.finance.cashflow.destroy', $row->id) . '" method="POST" style="display:inline;">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                } else {
                    return '<div class="d-flex justify-content-end">
                        <span class="badge badge-secondary">Tidak Dapat Diedit</span>
                    </div>';
                }
            })
            ->with([
                'total_income' => $total_income,
                'total_expense' => $total_expense,
                'total_balance' => $total_balance,
            ])
            ->rawColumns(['transaction', 'date', 'amount', 'type', 'payment_info', 'attachment', 'log', 'action'])
            ->make(true);
    }

    public function cashFlowExport(Request $request)
    {
        $type = $request->type;
        $date_end = $request->date_end ?? now()->toDateString();
        $date_start = $request->date_start ?? now()->subMonth()->toDateString();

        return Excel::download(new CashflowExport($date_start, $date_end, $type), 'cashflow_' . now()->format('Y_m_d') . '.xlsx');
    }

    public function CashflowStore(Request $request)
    {
        $type_control = $this->cookie_control_panel;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'payment_note' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', 'Validation failed: ' . implode(', ', $validator->errors()->all()));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $finance = new Finance();
        $finance->name = $request->name;
        $finance->description = $request->description;
        $finance->type = $request->type;
        $finance->amount = $request->amount;
        $finance->date = Carbon::parse($request->date);
        $finance->payment_method = $request->payment_method;
        $finance->payment_reference = $request->payment_reference;
        $finance->payment_note = $request->payment_note;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $filename = Str::slug($request->name) . '_' . time() . '.' . $attachment->getClientOriginalExtension();
            $path = $attachment->storeAs('attachments/finances', $filename, 'public');
            $finance->attachment = $path;
        }
        $finance->created_by = Auth::user()->id;
        $finance->type_control = $type_control;
        $finance->save();

        Alert::success('Success', 'Finance record created successfully.');
        return redirect()->back();
    }

    public function cashflowDestroy(Request $request)
    {
        $finance = Finance::find($request->id);
        if (!$finance) {
            Alert::error('Error', 'Finance record not found.');
            return redirect()->back();
        }

        // Hapus file lampiran jika ada
        if ($finance->attachment) {
            Storage::disk('public')->delete($finance->attachment);
        }
        $finance->delete();

        Alert::success('Success', 'Finance record deleted successfully.');
        return redirect()->back();
    }

    public function cashflowUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'payment_note' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', 'Validation failed: ' . implode(', ', $validator->errors()->all()));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $finance = Finance::findOrFail($request->id);
        if (!$finance) {
            Alert::error('Error', 'Finance record not found.');
            return redirect()->back();
        }

        $finance->name = $request->name;
        $finance->description = $request->description;
        $finance->type = $request->type;
        $finance->amount = $request->amount;
        $finance->date = Carbon::parse($request->date);
        $finance->payment_method = $request->payment_method;
        $finance->payment_reference = $request->payment_reference;
        $finance->payment_note = $request->payment_note;

        if ($request->hasFile('attachment')) {
            if ($finance->attachment) {
                Storage::disk('public')->delete($finance->attachment);
            }
            $attachment = $request->file('attachment');
            $filename = Str::slug($request->name) . '_' . time() . '.' . $attachment->getClientOriginalExtension();
            $path = $attachment->storeAs('attachments/finances', $filename, 'public');
            $finance->attachment = $path;
        }
        $finance->updated_by = Auth::user()->id;
        $finance->save();

        Alert::success('Success', 'Finance record updated successfully.');
        return redirect()->back();
    }
}
