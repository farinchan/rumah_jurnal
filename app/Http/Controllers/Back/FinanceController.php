<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmPaymentMail;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\PaymentInvoice;
use App\Models\SettingWebsite;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class FinanceController extends Controller
{
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
            'journals' => Journal::all()

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

        // $payment = Submission::all();
        // dd($payment);
        $payment = Payment::with(['paymentInvoice.submission'])
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
                if ($payment->payment_status == 'accepted') {
                    return '
                    <a href="' . route("back.finance.verification.detail", $payment->id) . '" class="btn btn-sm btn-light-primary my-1">
                        <i class="ki-duotone ki-eye fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i> Detail
                    </a>
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
                    </a>';
                }
            })
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
}
