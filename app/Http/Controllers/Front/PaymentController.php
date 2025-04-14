<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $journal_id = $request->journal_id;
        $q = $request->q;

        $data = [
            'title' => __('front.payment'),
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
}
