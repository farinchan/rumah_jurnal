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
use App\Models\Journal;
use App\Models\Issue;
use App\Models\Submission;
use App\Models\PaymentInvoice;
use App\Models\WaitingSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

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

    public function stats()
    {
        $data = [
            'journals' => \App\Models\Journal::count(),
            'submissions' => \App\Models\Submission::count(),
            'events' => \App\Models\Event::count(),
            'users' => \App\Models\User::count(),
        ];
        return response()->json($data);
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

    public function cashflowStat(Request $request)
    {
        try {
            // Get control panel type from cookie
            $controlPanel = $request->cookie('control_panel', 'journal');

            $data = cache()->remember('cashflow_stats_' . $controlPanel, 60, function () use ($controlPanel) {
                // Get current finance year based on control panel type
                $financeYear = FinanceYear::where('type_control', $controlPanel)->latest()->first();
                $startDate = $financeYear ? $financeYear->start_date : now()->startOfYear()->toDateString();
                $endDate = $financeYear && $financeYear->end_date ? $financeYear->end_date : now()->addDay()->toDateString();

                // Monthly cashflow data filtered by control panel
                $monthlyData = Finance::select(
                    DB::raw('DATE(date) as date'),
                    DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income'),
                    DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
                )
                    ->where('type_control', $controlPanel)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->groupBy(DB::raw('DATE(date)'))
                    ->orderBy('date', 'desc')
                    ->limit(30)
                    ->get();

                // Payment income data filtered by control panel (through submission's journal type)
                $paymentIncome = Payment::with(['paymentInvoice.submission.issue.journal'])
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('payment_status', 'accepted')
                    ->get()
                    ->filter(function ($payment) use ($controlPanel) {
                        $journal = $payment->paymentInvoice?->submission?->issue?->journal;
                        return $journal && $journal->type === $controlPanel;
                    })
                    ->groupBy(function ($payment) {
                        return $payment->created_at->format('Y-m-d');
                    })
                    ->map(function ($payments) {
                        return $payments->sum(function ($payment) {
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

                // Transaction type distribution filtered by control panel
                $transactionTypes = Finance::select('type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                    ->where('type_control', $controlPanel)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->groupBy('type')
                    ->get();

                // Finance Years overview filtered by control panel
                $financeYears = FinanceYear::where('type_control', $controlPanel)
                    ->orderBy('start_date', 'desc')
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
                    $financeYears = $financeYears->map(function ($year) use ($controlPanel) {
                        $startDate = $year->start_date;
                        $endDate = $year->end_date ?? now()->addDay()->toDateString();

                        // Calculate income for this finance year filtered by control panel
                        $income = Finance::where('type', 'income')
                            ->where('type_control', $controlPanel)
                            ->where('date', '>=', $startDate)
                            ->where('date', '<=', $endDate)
                            ->sum('amount');

                        // Calculate payment income for this finance year filtered by control panel
                        $paymentIncome = Payment::with(['paymentInvoice.submission.issue.journal'])
                            ->where('created_at', '>=', $startDate)
                            ->where('created_at', '<=', $endDate)
                            ->where('payment_status', 'accepted')
                            ->get()
                            ->filter(function ($payment) use ($controlPanel) {
                                $journal = $payment->paymentInvoice?->submission?->issue?->journal;
                                return $journal && $journal->type === $controlPanel;
                            })
                            ->sum(function ($payment) {
                                return $payment->paymentInvoice->payment_amount ?? 0;
                            });

                        // Calculate outcome for this finance year filtered by control panel
                        $outcome = Finance::where('type', 'expense')
                            ->where('type_control', $controlPanel)
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

                // Recent transactions filtered by control panel
                $recentTransactions = Finance::where('type_control', $controlPanel)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();

                // Summary totals filtered by control panel
                $totalIncome = Finance::where('type', 'income')
                    ->where('type_control', $controlPanel)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->sum('amount');

                $totalPaymentIncome = Payment::with(['paymentInvoice.submission.issue.journal'])
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('payment_status', 'accepted')
                    ->get()
                    ->filter(function ($payment) use ($controlPanel) {
                        $journal = $payment->paymentInvoice?->submission?->issue?->journal;
                        return $journal && $journal->type === $controlPanel;
                    })
                    ->sum(function ($payment) {
                        return $payment->paymentInvoice->payment_amount ?? 0;
                    });

                $totalExpense = Finance::where('type', 'expense')
                    ->where('type_control', $controlPanel)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->sum('amount');

                // Calculate distribution based on finance year percentage
                $distributionPercentage = $financeYear ? $financeYear->distribution_percentage : 80;
                $totalGrossIncome = $totalIncome + $totalPaymentIncome;
                $totalBalance = $totalGrossIncome - $totalExpense;
                // Rumah Jurnal: persentase dari (pemasukan - pengeluaran)
                $distributionRumahJurnal = ($totalBalance * $distributionPercentage) / 100;
                // BLU: persentase dari pemasukan
                $distributionBLU = ($totalGrossIncome * (100 - $distributionPercentage)) / 100;

                // Transaction counts filtered by control panel
                $totalTransactionCount = Finance::where('type_control', $controlPanel)
                    ->where('date', '>=', $startDate)
                    ->where('date', '<=', $endDate)
                    ->count();

                $monthlyTransactionCount = Finance::where('type_control', $controlPanel)
                    ->where('date', '>=', now()->startOfMonth())
                    ->where('date', '<=', now()->endOfMonth())
                    ->count();

                return [
                    'monthly_cashflow' => $mergedMonthly->values()->toArray(),
                    'transaction_types' => $transactionTypes->toArray(),
                    'finance_years' => $financeYears->toArray(),
                    'recent_transactions' => $recentTransactions->toArray(),
                    'control_panel' => $controlPanel,
                    'summary' => [
                        'total_income' => (int)($totalIncome + $totalPaymentIncome),
                        'total_expense' => (int)$totalExpense,
                        'total_balance' => (int)(($totalIncome + $totalPaymentIncome) - $totalExpense),
                        'finance_income' => (int)$totalIncome,
                        'payment_income' => (int)$totalPaymentIncome,
                        'transaction_count' => $totalTransactionCount,
                        'monthly_transactions' => $monthlyTransactionCount,
                        'distribution_percentage' => $distributionPercentage,
                        'distribution_rumah_jurnal' => (int)$distributionRumahJurnal,
                        'distribution_blu' => (int)$distributionBLU,
                        'total_gross_income' => (int)$totalGrossIncome
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

    protected array $allowedDashboardJournalRoles = [
        'super-admin',
        'admin-ejournal',
        'admin-proceeding',
        'admin-student-research-hub',
        'editor',
        'editor-proceeding',
        'editor-student-research-hub',
    ];

    private function canUserAccessJournal($user, Journal $journal): bool
    {
        if (!$user || !$user->hasAnyRole($this->allowedDashboardJournalRoles)) {
            return false;
        }

        // super-admin can open everything across all types
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // admin-ejournal can open all journals, but only for type 'journal'
        if ($user->hasRole('admin-ejournal') && $journal->type === 'journal') {
            return true;
        }

        // admin-proceeding can open all journals, but only for type 'proceeding'
        if ($user->hasRole('admin-proceeding') && $journal->type === 'proceeding') {
            return true;
        }

        // admin-student-research-hub can open all journals, but only for type 'student_research_hub'
        if ($user->hasRole('admin-student-research-hub') && $journal->type === 'student_research_hub') {
            return true;
        }

        // editor roles can only open journals based on permission url_path assigned to them
        if ($user->hasAnyRole(['editor', 'editor-proceeding', 'editor-student-research-hub'])) {
            if ($user->can($journal->url_path)) {
                return true;
            }
        }

        return false;
    }

    public function journal(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasAnyRole($this->allowedDashboardJournalRoles)) {
            abort(403, 'Anda tidak memiliki akses ke Dashboard Jurnal');
        }

        $controlPanel = $request->cookie('control_panel', 'journal');

        // Get journals accessible to this user based on their role and permissions
        $journals = Journal::orderBy('type')
            ->orderBy('name')
            ->get()
            ->filter(function ($journal) use ($user) {
                return $this->canUserAccessJournal($user, $journal);
            })
            ->values();

        // Group journals by publication type
        $typeLabels = [
            'journal' => 'Jurnal / E-Journal',
            'proceeding' => 'Proceeding',
            'student_research_hub' => 'Student Research Hub',
        ];

        $groupedJournals = $journals->groupBy(function ($item) use ($typeLabels) {
            return $typeLabels[$item->type] ?? ucfirst(str_replace('_', ' ', $item->type));
        });

        // Determine initially selected journal
        $selectedJournalId = $request->query('journal_id');
        if (!$selectedJournalId || !$journals->contains('id', $selectedJournalId)) {
            $matchingControlPanelJournal = $journals->firstWhere('type', $controlPanel);
            $selectedJournalId = $matchingControlPanelJournal ? $matchingControlPanelJournal->id : $journals->first()?->id;
        }

        $selectedIssueId = $request->query('issue_id');

        $initialIssues = collect();
        if ($selectedJournalId) {
            $initialIssues = Issue::where('journal_id', $selectedJournalId)
                ->orderBy('year', 'desc')
                ->orderBy('volume', 'desc')
                ->orderBy('number', 'desc')
                ->get();
        }

        $data = [
            'title' => 'Dashboard Jurnal',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Jurnal',
                    'link' => route('back.dashboard.journal')
                ]
            ],
            'journals' => $journals,
            'grouped_journals' => $groupedJournals,
            'selected_journal_id' => $selectedJournalId,
            'initial_issues' => $initialIssues,
            'selected_issue_id' => $selectedIssueId,
            'control_panel' => $controlPanel,
        ];

        return view('back.pages.dashboard.journal', $data);
    }

    public function journalStat(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->hasAnyRole($this->allowedDashboardJournalRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke Dashboard Jurnal',
                ], 403);
            }

            $controlPanel = $request->cookie('control_panel', 'journal');
            $journalId = $request->get('journal_id');
            $issueId = $request->get('issue_id');

            if ($journalId) {
                $journal = Journal::find($journalId);
            } else {
                $journals = Journal::orderBy('type')->orderBy('name')->get();
                $journal = $journals->firstWhere('type', $controlPanel);
                if (!$journal || !$this->canUserAccessJournal($user, $journal)) {
                    $journal = $journals->first(fn($j) => $this->canUserAccessJournal($user, $j));
                }
            }

            if (!$journal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal tidak ditemukan atau Anda belum memiliki jurnal yang ditugaskan',
                ], 404);
            }

            // Check authorization specifically for this journal
            if (!$this->canUserAccessJournal($user, $journal)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke jurnal ini',
                ], 403);
            }

            // Retrieve all issues of this journal for the filter options & total count
            $allIssues = Issue::where('journal_id', $journal->id)
                ->orderBy('year', 'desc')
                ->orderBy('volume', 'desc')
                ->orderBy('number', 'desc')
                ->get();

            $selectedIssue = null;
            if (!empty($issueId) && $issueId !== 'all') {
                $selectedIssue = $allIssues->firstWhere('id', $issueId);
                if (!$selectedIssue) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Issue tidak ditemukan pada jurnal ini',
                    ], 404);
                }
            }

            // Retrieve issues with submissions, payment invoices, and payments
            $issuesQuery = Issue::where('journal_id', $journal->id)
                ->with(['submissions.paymentInvoices.payments'])
                ->orderBy('year', 'asc')
                ->orderBy('volume', 'asc')
                ->orderBy('number', 'asc');

            if ($selectedIssue) {
                $issuesQuery->where('id', $selectedIssue->id);
            }

            $issues = $issuesQuery->get();

            $totalSubmissions = 0;
            $publishedCount = 0;
            $unpublishedCount = 0;

            $lunasCount = 0;
            $lunasAmount = 0;

            $belumLunasCount = 0;
            $belumLunasPaid = 0;
            $belumLunasRemaining = 0;

            $belumBayarCount = 0;
            $belumBayarAmount = 0;

            $freeCount = 0;

            $issuesTableData = [];
            $issueChartCategories = [];
            $issueChartPublished = [];
            $issueChartUnpublished = [];

            $yearData = [];

            foreach ($issues as $issue) {
                $issueFee = $issue->author_fee ?? ($journal->author_fee ?? 0);
                $issueArticlesCount = $issue->submissions->count();
                $issuePublished = 0;
                $issueUnpublished = 0;
                $issueLunas = 0;
                $issueBelumLunas = 0;
                $issueBelumBayar = 0;
                $issueFree = 0;
                $issueIncome = 0;

                foreach ($issue->submissions as $submission) {
                    $totalSubmissions++;

                    // Published status check: hanya status == '3' yang publish, selain itu belum publish
                    $isPublished = ($submission->status == '3');

                    if ($isPublished) {
                        $publishedCount++;
                        $issuePublished++;
                    } else {
                        $unpublishedCount++;
                        $issueUnpublished++;
                    }

                    // Payment status check
                    $subFee = $issueFee;
                    $isFree = ($submission->free_charge == 1) || ($subFee <= 0);

                    if ($isFree) {
                        $freeCount++;
                        $issueFree++;
                    } else {
                        $paidInvoices = $submission->paymentInvoices->where('is_paid', 1);
                        $paidPercent = $paidInvoices->sum('payment_percent');
                        $paidAmount = $paidInvoices->sum('payment_amount');

                        if ($paidAmount == 0) {
                            $acceptedPayments = $submission->paymentInvoices->flatMap->payments->where('payment_status', 'accepted');
                            $paidAmount = $acceptedPayments->sum('payment_amount');
                        }

                        $isLunas = ($submission->payment_status === 'paid')
                            || ($paidPercent >= 100)
                            || ($subFee > 0 && $paidAmount >= $subFee);

                        if ($isLunas) {
                            $lunasCount++;
                            $issueLunas++;
                            $actualPaid = $paidAmount > 0 ? $paidAmount : $subFee;
                            $lunasAmount += $actualPaid;
                            $issueIncome += $actualPaid;
                        } elseif ($paidPercent > 0 || $paidAmount > 0) {
                            $belumLunasCount++;
                            $issueBelumLunas++;
                            $belumLunasPaid += $paidAmount;
                            $remaining = max(0, $subFee - $paidAmount);
                            $belumLunasRemaining += $remaining;
                            $issueIncome += $paidAmount;
                        } else {
                            $belumBayarCount++;
                            $issueBelumBayar++;
                            $belumBayarAmount += $subFee;
                        }
                    }
                }

                $year = $issue->year ?: ($issue->created_at ? $issue->created_at->format('Y') : 'Unknown');
                if (!isset($yearData[$year])) {
                    $yearData[$year] = [
                        'published' => 0,
                        'unpublished' => 0,
                    ];
                }
                $yearData[$year]['published'] += $issuePublished;
                $yearData[$year]['unpublished'] += $issueUnpublished;

                $issueLabel = 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ($issue->year ? ' (' . $issue->year . ')' : '');
                $issueChartCategories[] = $issueLabel;
                $issueChartPublished[] = $issuePublished;
                $issueChartUnpublished[] = $issueUnpublished;

                $issuesTableData[] = [
                    'id' => $issue->id,
                    'volume' => $issue->volume,
                    'number' => $issue->number,
                    'year' => $issue->year,
                    'title' => $issue->title ?: '-',
                    'issue_label' => $issueLabel,
                    'author_fee' => (int)$issueFee,
                    'total_articles' => $issueArticlesCount,
                    'published_count' => $issuePublished,
                    'unpublished_count' => $issueUnpublished,
                    'lunas_count' => $issueLunas,
                    'belum_lunas_count' => $issueBelumLunas,
                    'belum_bayar_count' => $issueBelumBayar,
                    'free_count' => $issueFree,
                    'total_income' => (int)$issueIncome,
                    'action_url' => route('back.journal.article.index', [$journal->url_path, $issue->id]),
                ];
            }

            // Waiting submissions for this journal
            $waitingSubmissionsQuery = WaitingSubmission::where('target_journal_id', $journal->id);
            $totalWaiting = (clone $waitingSubmissionsQuery)->count();
            $waitingWaiting = (clone $waitingSubmissionsQuery)->where('status', 'waiting')->count();
            $waitingUnderReview = (clone $waitingSubmissionsQuery)->where('status', 'under_review')->count();
            $waitingAccepted = (clone $waitingSubmissionsQuery)->where('status', 'accepted')->count();

            // Total revenue received vs outstanding
            $totalPaidReceived = $lunasAmount + $belumLunasPaid;
            $totalOutstanding = $belumLunasRemaining + $belumBayarAmount;
            $totalPotentialRevenue = $totalPaidReceived + $totalOutstanding;

            // Sort year data chronologically
            ksort($yearData);
            $yearCategories = array_keys($yearData);
            $yearPublishedSeries = array_column(array_values($yearData), 'published');
            $yearUnpublishedSeries = array_column(array_values($yearData), 'unpublished');

            $issuesOptions = $allIssues->map(function ($iss) {
                $label = 'Vol. ' . $iss->volume . ' No. ' . $iss->number . ($iss->year ? ' (' . $iss->year . ')' : '');
                if (!empty($iss->title) && $iss->title !== '-') {
                    $label .= ' - ' . Str::limit($iss->title, 40);
                }
                return [
                    'id' => $iss->id,
                    'label' => $label,
                    'volume' => $iss->volume,
                    'number' => $iss->number,
                    'year' => $iss->year,
                    'title' => $iss->title ?: '-',
                    'author_fee' => (int)($iss->author_fee ?? 0),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'journal' => [
                    'id' => $journal->id,
                    'name' => $journal->name,
                    'title' => $journal->title,
                    'url_path' => $journal->url_path,
                    'author_fee' => (int)($selectedIssue ? ($selectedIssue->author_fee ?? ($journal->author_fee ?? 0)) : ($journal->author_fee ?? 0)),
                    'journal_author_fee' => (int)($journal->author_fee ?? 0),
                    'total_issues' => $allIssues->count(),
                    'filtered_issues_count' => $issues->count(),
                    'selected_issue' => $selectedIssue ? [
                        'id' => $selectedIssue->id,
                        'label' => 'Vol. ' . $selectedIssue->volume . ' No. ' . $selectedIssue->number . ($selectedIssue->year ? ' (' . $selectedIssue->year . ')' : ''),
                        'author_fee' => (int)($selectedIssue->author_fee ?? ($journal->author_fee ?? 0)),
                    ] : null,
                ],
                'issues_options' => $issuesOptions,
                'summary' => [
                    'is_issue_filtered' => !empty($selectedIssue),
                    'total_submissions' => $totalSubmissions,
                    'total_published' => $publishedCount,
                    'total_unpublished' => $unpublishedCount,
                    'published_percentage' => $totalSubmissions > 0 ? round(($publishedCount / $totalSubmissions) * 100, 1) : 0,
                    'unpublished_percentage' => $totalSubmissions > 0 ? round(($unpublishedCount / $totalSubmissions) * 100, 1) : 0,

                    // Rekap data pembayaran
                    'lunas' => [
                        'count' => $lunasCount,
                        'amount' => (int)$lunasAmount,
                    ],
                    'belum_lunas' => [
                        'count' => $belumLunasCount,
                        'paid_amount' => (int)$belumLunasPaid,
                        'remaining_amount' => (int)$belumLunasRemaining,
                    ],
                    'belum_bayar' => [
                        'count' => $belumBayarCount,
                        'amount' => (int)$belumBayarAmount,
                    ],
                    'free' => [
                        'count' => $freeCount,
                    ],

                    // Finansial
                    'total_paid_received' => (int)$totalPaidReceived,
                    'total_outstanding' => (int)$totalOutstanding,
                    'total_potential_revenue' => (int)$totalPotentialRevenue,

                    // Naskah waiting
                    'waiting_submissions' => [
                        'total' => $totalWaiting,
                        'waiting' => $waitingWaiting,
                        'under_review' => $waitingUnderReview,
                        'accepted' => $waitingAccepted,
                    ],
                ],
                'charts' => [
                    'issue_chart' => [
                        'categories' => $issueChartCategories,
                        'published' => $issueChartPublished,
                        'unpublished' => $issueChartUnpublished,
                    ],
                    'year_chart' => [
                        'categories' => $yearCategories,
                        'published' => $yearPublishedSeries,
                        'unpublished' => $yearUnpublishedSeries,
                    ],
                    'payment_chart' => [
                        'labels' => ['Lunas', 'Belum Lunas', 'Belum Bayar', 'Free Charge'],
                        'series' => [$lunasCount, $belumLunasCount, $belumBayarCount, $freeCount],
                        'amounts' => [(int)$lunasAmount, (int)$belumLunasPaid, (int)$belumBayarAmount, 0],
                        'colors' => ['#50CD89', '#FFC700', '#F1416C', '#009EF7'],
                    ],
                    'article_status_chart' => [
                        'labels' => ['Published', 'Belum Publish', 'Naskah Menunggu'],
                        'series' => [$publishedCount, $unpublishedCount, $selectedIssue ? 0 : $totalWaiting],
                        'colors' => ['#50CD89', '#FFC700', '#7239EA'],
                    ],
                ],
                'issues_table' => array_reverse($issuesTableData),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat data statistik jurnal',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function switchControl($control)
    {

        if ($control == "journal") {
            if (Auth::user()->hasRole('admin-ejournal') || Auth::user()->hasRole('editor') || Auth::user()->hasRole('super-admin')|| Auth::user()->hasRole('keuangan')) {
                return redirect()->route('back.dashboard')->cookie('control_panel', $control, 60 * 24 * 30);
            }
        }
        if ($control == "proceeding") {
            if (Auth::user()->hasRole('admin-proceeding') || Auth::user()->hasRole('editor-proceeding') || Auth::user()->hasRole('super-admin')|| Auth::user()->hasRole('keuangan-proceeding')) {
                return redirect()->route('back.dashboard')->cookie('control_panel', $control, 60 * 24 * 30);
            }
        }
        if ($control == "student_research_hub") {
            if (Auth::user()->hasRole('admin-student-research-hub') || Auth::user()->hasRole('editor-student-research-hub') || Auth::user()->hasRole('super-admin')|| Auth::user()->hasRole('keuangan-student-research-hub')) {
                return redirect()->route('back.dashboard')->cookie('control_panel', $control, 60 * 24 * 30);
            }
        }

        cookie::forget('control_panel');
        Alert::error('Akses Ditolak', 'Anda tidak memiliki akses ke kontrol Student Research Hub');
        return redirect()->back();
    }
}
