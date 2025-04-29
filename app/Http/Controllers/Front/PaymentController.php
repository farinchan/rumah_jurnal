<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\SettingWebsite;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $journal_id = $request->journal_id;
        $q = $request->q;
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('front.payment') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.payment') . ' | ' . $setting_web->name,
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
                    'name' => __('front.payment'),
                    'link' => route('payment.index')
                ]
            ],
            'journals' => Journal::all(),
            'submissions' => Submission::with('issue.journal')
                ->when($journal_id, function ($query) use ($journal_id) {
                    return $query->whereHas('issue.journal', function ($query) use ($journal_id) {
                        $query->where('id', $journal_id);
                    });
                })
                ->when($q, function ($query) use ($q) {
                    return $query->where(function ($query) use ($q) {
                        $query->where('submission_id', 'like', "%$q%")
                            ->orWhere('fullTitle', 'like', "%$q%");
                    });
                })
                ->latest()
                ->paginate(6)
        ];
        return view('front.pages.payment.index', $data);
    }

    public function submission(Request $request, $journal_path, $submission_id)
    {
        $submission = Submission::with('issue.journal')
            ->where('submission_id', $submission_id)
            ->whereHas('issue.journal', function ($query) use ($journal_path) {
                $query->where('url_path', $journal_path);
            })->firstOrFail();
        $journal = Journal::where('url_path', $journal_path)->firstOrFail();

        if ($submission->issue->journal->url_path != $journal->url_path) {
            abort(404);
        }
        $setting_web = SettingWebsite::first();
        $data = [
            'title' =>  $submission->fullTitle,
            'meta' => [
                'title' => $submission->fullTitle . ' | ' . $setting_web->name,
                'description' => strip_tags($submission->abstract),
                'keywords' => $setting_web->name . ', ' . $submission->fullTitle . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $submission?->issue?->journal?->getJournalThumbnail() ?? Storage::url($setting_web->favicon)
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.payment'),
                    'link' => route('payment.index')
                ],
                [
                    'name' => $journal_path . ' - ' . $submission_id,
                    'link' => route('payment.submission', ['journal_path' => $journal_path, 'submission_id' => $submission_id])
                ]
            ],
            'submission' => $submission,
            'journal' => $journal
        ];

        return view('front.pages.payment.submission', $data);
    }

    public function pay(Request $request, $journal_path, $submission_id)
    {
        $submission = Submission::with('issue.journal')
            ->where('submission_id', $submission_id)
            ->whereHas('issue.journal', function ($query) use ($journal_path) {
                $query->where('url_path', $journal_path);
            })->firstOrFail();
        $journal = Journal::where('url_path', $journal_path)->firstOrFail();

        if ($submission->issue->journal->url_path != $journal->url_path) {
            abort(404);
        }
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('front.payment') . ' - ' . $submission->fullTitle,
            'meta' => [
                'title' => __('front.payment') . ' - ' . $submission->fullTitle . ' | ' . $setting_web->name,
                'description' => strip_tags($submission->abstract),
                'keywords' => $setting_web->name . ', ' . $submission->fullTitle . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $submission?->issue?->journal?->getJournalThumbnail() ?? Storage::url($setting_web->favicon)
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.payment'),
                    'link' => route('payment.index')
                ],
                [
                    'name' => $journal_path . ' - ' . $submission_id,
                    'link' => route('payment.submission', ['journal_path' => $journal_path, 'submission_id' => $submission_id])
                ],
                [
                    'name' => __('front.pay_now_btn'),
                    'link' => route('payment.pay', ['journal_path' => $journal_path, 'submission_id' => $submission_id])
                ]

            ],
            'submission' => $submission,
            'journal' => $journal,
            'payment_accounts' => PaymentAccount::all(),
        ];

        return view('front.pages.payment.pay', $data);
    }

    public function payStore(Request $request, $journal_path, $submission_id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'submission_id' => 'required|exists:submissions,id',
            'invoice_id' => 'nullable',
            'payment_timestamp' => 'required|date',
            'payment_method' => 'required',
            'payment_amount' => 'required',
            'payment_amount_int' => 'required|numeric',
            'payment_account_number' => 'required|max:255',
            'payment_account_name' => 'required|max:255',
            'payment_file' => 'required|mimes:jpg,jpeg,png,pdf|max:10240', // 10 MB
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:255',
        ],
            [
                'submission_id.required' => 'Submission ID is required',
                'submission_id.exists' => 'Submission ID not found',
                'payment_timestamp.required' => 'Payment timestamp is required',
                'payment_timestamp.datetime' => 'Payment timestamp must be a valid date and time',
                'payment_method.required' => 'Payment method is required',
                'payment_amount.required' => 'Payment amount is required',
                'payment_amount_int.required' => 'Payment amount integer is required',
                'payment_amount_int.numeric' => 'Payment amount integer must be a number',
                'payment_account_number.required' => 'Account number is required',
                'payment_account_name.required' => 'Account name is required',
                'payment_file.required' => 'Payment file is required',
                'payment_file.mimes' => 'Payment file must be a jpg, jpeg, png, or pdf file',
                'payment_file.max' => 'Payment file size must not exceed 10 MB',
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'phone.required' => 'Phone number is required'
            ]
        );
        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $paayment = new Payment();
        $paayment->submission_id = $request->submission_id;
        $paayment->payment_timestamp = $request->payment_timestamp;
        $paayment->payment_method = $request->payment_method;
        $paayment->payment_amount = $request->payment_amount;
        $paayment->payment_amount_int = $request->payment_amount_int;
        $paayment->payment_account_number = $request->payment_account_number;
        $paayment->payment_account_name = $request->payment_account_name;
        $paayment->name = $request->name;
        $paayment->email = $request->email;
        $paayment->phone = $request->phone;
        $paayment->submission_id = $request->submission_id;
        if ($request->hasFile('payment_file')) {
            $file = $request->file('payment_file');
            $filename = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $paayment->payment_file = $file->storeAs('payment', $filename, 'public');
        }
        $paayment->save();

        Alert::success('Success', 'Payment has been created');
        return redirect()->route('payment.submission', ['journal_path' => $journal_path, 'submission_id' => $submission_id]);
    }
}
