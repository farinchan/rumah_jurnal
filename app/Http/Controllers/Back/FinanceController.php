<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\Submission;
use Illuminate\Http\Request;
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
            ->addColumn('submission', function ($payment) {
                return '
                        <div class="d-flex flex-column">
                            <a href="#"
                                class="text-gray-800 text-hover-primary"> Submission ID: ' . $payment->submission->submission_id . '</a>
                                <span class="text-gray-800 ">' . $payment->submission->fullTitle . '</span>
                            <span >' . $payment->submission->authorsString . '</span>
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
                return '
                    <a href="' . route("back.finance.verification.detail", $payment->id) . '" class="btn btn-sm btn-light-primary my-1">
                        <i class="ki-duotone ki-eye fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i> Detail
                    </a>
                ';
            })
            ->rawColumns([
                'payment',
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
            'payment' => Payment::with(['submission.issue.journal'])->findOrFail($id),
        ];
        return view('back.pages.finance.verification-show', $data);
    }
    public function verificationUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,accepted,rejected',
            'note' => 'nullable|string',
        ],
            [
                'payment_status.required' => 'Status Pembayaran harus diisi',
                'payment_status.in' => 'Status Pembayaran tidak valid',
                'note.string' => 'Catatan harus berupa teks',
                'note.max' => 'Catatan maksimal 255 karakter',
            ]
        );
        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $payment = Payment::findOrFail($id);
        $payment->update([
            'payment_status' => $request->payment_status,
            'note' => $request->note,
        ]);

        if ($request->payment_status == 'accepted') {
            $submission = Submission::findOrFail($payment->submission_id);
            $submission->update([
                'payment_status' => 'paid',
            ]);
        } else{
            $submission = Submission::findOrFail($payment->submission_id);
            $submission->update([
                'payment_status' => 'pending',
            ]);
        }
        Alert::success('Berhasil', 'Pembayaran berhasil diperbarui');
        return redirect()->route('back.finance.verification.index')->with('success', 'Pembayaran berhasil diperbarui');
    }
}
