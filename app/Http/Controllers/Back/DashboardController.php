<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsViewer;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Payment;
use App\Models\FinanceYear;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
            ],


        ];
        return view('back.pages.dashboard.index', $data);
    }

    public function visistorStat()
    {


        $data = cache()->remember('visitor_stats', 60, function () {
            return [
                'visitor_monthly' => Visitor::select(DB::raw('Date(created_at) as date'), DB::raw('count(*) as total'))
                    ->orderBy('date', 'desc')
                    ->limit(30)
                    ->groupBy('date')
                    ->get(),
                'visitor_platfrom' => Visitor::select('platform', DB::raw('count(*) as total'))
                    ->groupBy('platform')
                    ->get(),
                'visitor_browser' => Visitor::select('browser', DB::raw('count(*) as total'))
                    ->groupBy('browser')
                    ->get(),
                'visitor_country' => Visitor::select('country', DB::raw('count(*) as total'))
                    ->whereNotNull('country')
                    ->groupBy('country')
                    ->orderBy('total', 'desc')
                    ->get()
                    ->map(function ($item) {
                        $countryName = $item->country;

                        $hash = substr(md5($countryName), 0, 6);
                        $item->color = "#{$hash}";
                        return $item;
                    }),
            ];
        });
        return response()->json($data);
    }

    public function news()
    {
        $data = [
            'title' => 'Dashboard Berita',
            'menu' => 'dashboard',
            'sub_menu' => '',
            'berita_count' => News::count(),
            'news_popular' => News::with('comments')->withCount('viewers')->orderBy('viewers_count', 'desc')->limit(5)->get(),
            'news_new' => News::with(['comments', 'viewers'])->latest()->limit(5)->get(),
            'news_writer' => news::select(
                DB::raw('count(*) as total'),
                'news.user_id',
            )
                ->groupBy('news.user_id')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];
        return view('back.pages.dashboard.news', $data);
    }

    public function stat()
    {


        $data = [
            'news_viewer_monthly' => NewsViewer::select(DB::raw('Date(created_at) as date'), DB::raw('count(*) as total'))
                ->limit(30)
                ->groupBy('date')
                ->get(),
            'news_viewer_platfrom' => NewsViewer::select('platform', DB::raw('count(*) as total'))
                ->groupBy('platform')
                ->get(),
            'news_viewer_browser' => NewsViewer::select('browser', DB::raw('count(*) as total'))
                ->groupBy('browser')
                ->get(),

        ];
        return response()->json($data);
    }

    public function cashFlow()
    {
        $data = [
            'title' => 'Dashboard Cashflow',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Cashflow',
                    'link' => route('back.dashboard.cashflow')
                ]
            ]
        ];
        return view('back.pages.dashboard.cashflow', $data);
    }

    public function cashflowStat()
    {
        try {
            $data = cache()->remember('cashflow_stats', 60, function () {
                // Get current finance year
                $financeYear = FinanceYear::latest()->first();
                $startDate = $financeYear ? $financeYear->start_date : now()->startOfYear()->toDateString();
                $endDate = $financeYear && $financeYear->end_date ? $financeYear->end_date : now()->toDateString();

                // Monthly cashflow data
                $monthlyData = Finance::select(
                    DB::raw('DATE(date) as date'),
                    DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income'),
                    DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
                )
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->groupBy(DB::raw('DATE(date)'))
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();

                // Payment income data
                $paymentIncome = Payment::with(['paymentInvoice'])
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('payment_status', 'accepted')
                    ->get()
                    ->groupBy(function($payment) {
                        return $payment->created_at->format('Y-m-d');
                    })
                    ->map(function($payments) {
                        return $payments->sum(function($payment) {
                            return $payment->paymentInvoice->payment_amount ?? 0;
                        });
                    });

                // Merge and process monthly data
                $mergedMonthly = $monthlyData->map(function ($item) use ($paymentIncome) {
                    $paymentForDate = $paymentIncome->get($item->date, 0);
                    $totalIncome = (int)($item->income + $paymentForDate);
                    $expense = (int)$item->expense;

                    return [
                        'date' => $item->date,
                        'income' => $totalIncome,
                        'expense' => $expense,
                        'balance' => $totalIncome - $expense
                    ];
                });

                // Transaction type distribution
                $transactionTypes = Finance::select('type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->groupBy('type')
                    ->get();

                // Finance Years overview
                $financeYears = FinanceYear::orderBy('start_date', 'desc')
                    ->limit(5)
                    ->get();

                if ($financeYears->isEmpty()) {
                    // If no finance years exist, create a default one for current year
                    $financeYears = collect([[
                        'name' => 'Current Year (' . now()->year . ')',
                        'income' => 0,
                        'outcome' => 0,
                        'balance' => 0,
                        'start_date' => now()->startOfYear()->toDateString(),
                        'end_date' => now()->endOfYear()->toDateString(),
                        'is_active' => true
                    ]]);
                } else {
                    $financeYears = $financeYears->map(function ($year) {
                        $startDate = $year->start_date;
                        $endDate = $year->end_date ?? now()->toDateString();

                        // Calculate income for this finance year
                        $income = Finance::where('type', 'income')
                            ->where('date', '>=', $startDate)
                            ->where('date', '<=', $endDate)
                            ->sum('amount');

                        // Calculate payment income for this finance year
                        $paymentIncome = Payment::with(['paymentInvoice'])
                            ->where('created_at', '>=', $startDate)
                            ->where('created_at', '<=', $endDate)
                            ->where('payment_status', 'accepted')
                            ->get()
                            ->sum(function ($payment) {
                                return $payment->paymentInvoice->payment_amount ?? 0;
                            });

                        // Calculate outcome for this finance year
                        $outcome = Finance::where('type', 'expense')
                            ->where('date', '>=', $startDate)
                            ->where('date', '<=', $endDate)
                            ->sum('amount');

                        $totalIncome = $income + $paymentIncome;
                        $balance = $totalIncome - $outcome;

                        return [
                            'name' => $year->name,
                            'income' => (int)$totalIncome,
                            'outcome' => (int)$outcome,
                            'balance' => (int)$balance,
                            'start_date' => $year->start_date,
                            'end_date' => $year->end_date,
                            'is_active' => $year->is_active
                        ];
                    });
                }

                // Recent transactions
                $recentTransactions = Finance::where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();

                // Summary totals
                $totalIncome = Finance::where('type', 'income')
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->sum('amount');

                $totalPaymentIncome = Payment::with(['paymentInvoice'])
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('payment_status', 'accepted')
                    ->get()
                    ->sum(function ($payment) {
                        return $payment->paymentInvoice->payment_amount ?? 0;
                    });

                $totalExpense = Finance::where('type', 'expense')
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->sum('amount');

                // Transaction counts
                $totalTransactionCount = Finance::where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->count();

                $monthlyTransactionCount = Finance::where('date', '>=', now()->startOfMonth())
                    ->where('date', '<=', now()->endOfMonth())
                    ->count();

                return [
                    'monthly_cashflow' => $mergedMonthly->values()->toArray(),
                    'transaction_types' => $transactionTypes->toArray(),
                    'finance_years' => $financeYears->toArray(),
                    'recent_transactions' => $recentTransactions->toArray(),
                    'summary' => [
                        'total_income' => (int)($totalIncome + $totalPaymentIncome),
                        'total_expense' => (int)$totalExpense,
                        'total_balance' => (int)(($totalIncome + $totalPaymentIncome) - $totalExpense),
                        'finance_income' => (int)$totalIncome,
                        'payment_income' => (int)$totalPaymentIncome,
                        'transaction_count' => $totalTransactionCount,
                        'monthly_transactions' => $monthlyTransactionCount
                    ]
                ];
            });

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load cashflow data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
