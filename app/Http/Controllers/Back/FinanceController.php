<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\Submission;
use Illuminate\Http\Request;

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
            'payment' => Payment::with(['submission'])
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
        $payment = Payment::with(['submission'])
            ->when($journal_id, function ($query) use ($journal_id) {
                return $query->whereHas('submission.issue', function ($q) use ($journal_id) {
                    $q->where('journal_id', $journal_id);
                });
            })
            ->when($submission_search, function ($query) use ($submission_search) {
                return $query->whereHas('submission', function ($q) use ($submission_search) {
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

        return datatables()
            ->of($payment)
            ->addColumn('id', function ($payment) {
                return '<span class="fw-bold">' . $payment->submission->submission_id . '</span>';;
            })
            ->addColumn('submission', function ($payment) {
                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary mb-1">' . $payment->submission->authorsString . '</a>
                            <span>' . $payment->submission->fullTitle . '</span>
                        </div>
                ';
            })
            ->addColumn('journal', function ($payment) {

                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary mb-1">' . $payment->submission->issue->journal->title . '</a>
                            <span> Vol. ' . $payment->submission->issue->volume . ' No. ' . $payment->submission->issue->number . ' (' . $payment->submission->issue->year . '): ' . $payment->submission->issue->title .  '</span>
                        </div>
                ';
            })
            ->addColumn('author', function ($payment) {
                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary mb-1">' . $payment->name . '</a>
                            <span> Email:' . $payment->email . '</span>
                            <span> Phone:' . $payment->phone . '</span>
                        </div>
                ';
            })
            ->addColumn('status', function ($payment) {
                if ($payment->payment_status == 'pending') {
                    return '<span class="badge badge-light-warning">Pending</span>';
                } elseif ($payment->payment_status == 'accepted') {
                    return '<span class="badge badge-light-success">Accepted</span>';
                } elseif ($payment->payment_status == 'rejected') {
                    return '<span class="badge badge-light-danger">Rejected</span>';
                } else {
                    return '<span class="badge badge-light-primary">' . $payment->payment_status . '</span>';
                }
            })
            ->rawColumns([
                'id',
                'submission',
                'journal',
                'author',
                'status'
            ])
            ->make(true);
    }
}
