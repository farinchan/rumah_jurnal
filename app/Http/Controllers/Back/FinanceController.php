<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Payment;
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
            'payment' => Payment::with(['user', 'journal'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get()
        ];
        return view('back.pages.finance.verification.index', $data);
    }
}
