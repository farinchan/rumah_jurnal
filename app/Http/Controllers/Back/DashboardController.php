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

    protected array $dashboardScopes = [
        'all' => [
            'id' => 'all',
            'name' => 'Semua Jurnal',
            'label' => 'Semua Jurnal (Keseluruhan)',
            'type' => null,
        ],
        'all_journal' => [
            'id' => 'all_journal',
            'name' => 'Semua E-Journal',
            'label' => 'Semua E-Journal',
            'type' => 'journal',
        ],
        'all_proceeding' => [
            'id' => 'all_proceeding',
            'name' => 'Semua Proceeding',
            'label' => 'Semua Proceeding',
            'type' => 'proceeding',
        ],
        'all_student_research_hub' => [
            'id' => 'all_student_research_hub',
            'name' => 'Semua Student Research Hub',
            'label' => 'Semua Student Research Hub',
            'type' => 'student_research_hub',
        ],
    ];

    private function canUserAccessScopeType($user, ?string $type): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($type === null) {
            return $user->hasAnyRole($this->allowedDashboardJournalRoles);
        }

        return match ($type) {
            'journal' => $user->hasRole('admin-ejournal') || $user->hasRole('editor'),
            'proceeding' => $user->hasRole('admin-proceeding') || $user->hasRole('editor-proceeding'),
            'student_research_hub' => $user->hasRole('admin-student-research-hub') || $user->hasRole('editor-student-research-hub'),
            default => false,
        };
    }

    private function getAccessibleScopes($user, $accessibleJournals): array
    {
        $scopes = [];
        $isSuperAdmin = $user->hasRole('super-admin');

        foreach ($this->dashboardScopes as $key => $scopeDef) {
            $type = $scopeDef['type'];
            if ($isSuperAdmin) {
                $scopes[$key] = $scopeDef;
            } elseif ($type === null) {
                if ($accessibleJournals->isNotEmpty()) {
                    $scopes[$key] = $scopeDef;
                }
            } else {
                if ($this->canUserAccessScopeType($user, $type) && $accessibleJournals->where('type', $type)->isNotEmpty()) {
                    $scopes[$key] = $scopeDef;
                }
            }
        }

        return $scopes;
    }

    private function resolveSelectedJournals($user, ?string $journalId, $accessibleJournals): array
    {
        if (array_key_exists($journalId, $this->dashboardScopes)) {
            $scopeDef = $this->dashboardScopes[$journalId];
            $type = $scopeDef['type'];

            if ($type !== null && !$user->hasRole('super-admin') && !$this->canUserAccessScopeType($user, $type) && $accessibleJournals->where('type', $type)->isEmpty()) {
                return [
                    'authorized' => false,
                    'error_code' => 403,
                    'message' => 'Anda tidak memiliki akses ke cakupan jurnal ini',
                ];
            }

            $selectedJournals = ($type === null)
                ? $accessibleJournals
                : $accessibleJournals->where('type', $type)->values();

            if ($selectedJournals->isEmpty() && !$user->hasRole('super-admin')) {
                return [
                    'authorized' => false,
                    'error_code' => 403,
                    'message' => 'Anda tidak memiliki akses ke jurnal dalam cakupan ini',
                ];
            }

            return [
                'authorized' => true,
                'is_scope' => true,
                'scope_id' => $journalId,
                'scope_name' => $scopeDef['name'],
                'journal' => null,
                'journals' => $selectedJournals,
            ];
        }

        if ($journalId) {
            $journal = Journal::find($journalId);
        } else {
            $journal = null;
        }

        if (!$journal) {
            return [
                'authorized' => false,
                'error_code' => 404,
                'message' => 'Jurnal tidak ditemukan atau Anda belum memiliki jurnal yang ditugaskan',
            ];
        }

        if (!$this->canUserAccessJournal($user, $journal)) {
            return [
                'authorized' => false,
                'error_code' => 403,
                'message' => 'Anda tidak memiliki akses ke jurnal ini',
            ];
        }

        return [
            'authorized' => true,
            'is_scope' => false,
            'scope_id' => null,
            'scope_name' => null,
            'journal' => $journal,
            'journals' => collect([$journal]),
        ];
    }

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

        $accessibleScopes = $this->getAccessibleScopes($user, $journals);

        // Group journals by publication type
        $typeLabels = [
            'journal' => 'Jurnal / E-Journal',
            'proceeding' => 'Proceeding',
            'student_research_hub' => 'Student Research Hub',
        ];

        $groupedJournals = $journals->groupBy(function ($item) use ($typeLabels) {
            return $typeLabels[$item->type] ?? ucfirst(str_replace('_', ' ', $item->type));
        });

        // Determine initially selected journal or scope
        $selectedJournalId = $request->query('journal_id');
        $isScope = array_key_exists($selectedJournalId, $accessibleScopes);

        if (!$selectedJournalId || (!$journals->contains('id', $selectedJournalId) && !$isScope)) {
            $matchingControlPanelJournal = $journals->firstWhere('type', $controlPanel);
            $selectedJournalId = $matchingControlPanelJournal ? $matchingControlPanelJournal->id : $journals->first()?->id;
            $isScope = false;
        }

        $selectedIssueId = $request->query('issue_id');
        $selectedYear = $request->query('year');

        $initialIssues = collect();
        $initialYears = collect();

        if ($isScope) {
            $scopeJournals = match ($selectedJournalId) {
                'all' => $journals,
                'all_journal' => $journals->where('type', 'journal'),
                'all_proceeding' => $journals->where('type', 'proceeding'),
                'all_student_research_hub' => $journals->where('type', 'student_research_hub'),
                default => $journals,
            };
            $scopeJournalIds = $scopeJournals->pluck('id')->all();

            $initialYears = Issue::whereIn('journal_id', $scopeJournalIds)
                ->whereNotNull('year')
                ->where('year', '!=', '')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
        } elseif ($selectedJournalId) {
            $initialYears = Issue::where('journal_id', $selectedJournalId)
                ->whereNotNull('year')
                ->where('year', '!=', '')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            $issuesQuery = Issue::where('journal_id', $selectedJournalId)
                ->orderBy('year', 'desc')
                ->orderBy('volume', 'desc')
                ->orderBy('number', 'desc');

            if ($selectedYear && $selectedYear !== 'all') {
                $issuesQuery->where('year', $selectedYear);
            }

            $initialIssues = $issuesQuery->get();
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
            'accessible_scopes' => $accessibleScopes,
            'selected_journal_id' => $selectedJournalId,
            'is_scope' => $isScope,
            'initial_issues' => $initialIssues,
            'selected_issue_id' => $selectedIssueId,
            'initial_years' => $initialYears,
            'selected_year' => $selectedYear,
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
            $filterYear = $request->get('year');

            // Get journals accessible to this user based on their role and permissions
            $journals = Journal::orderBy('type')
                ->orderBy('name')
                ->get()
                ->filter(function ($journal) use ($user) {
                    return $this->canUserAccessJournal($user, $journal);
                })
                ->values();

            if (!$journalId) {
                $matchingControlPanelJournal = $journals->firstWhere('type', $controlPanel);
                $journalId = $matchingControlPanelJournal ? $matchingControlPanelJournal->id : $journals->first()?->id;
            }

            $resolved = $this->resolveSelectedJournals($user, (string)$journalId, $journals);
            if (!$resolved['authorized']) {
                return response()->json([
                    'success' => false,
                    'message' => $resolved['message'],
                ], $resolved['error_code']);
            }

            $isScope = $resolved['is_scope'];
            $selectedJournals = $resolved['journals'];
            $selectedJournalIds = $selectedJournals->pluck('id')->all();
            $journalsById = $selectedJournals->keyBy('id');
            $journal = $resolved['journal'];

            // Retrieve all available years of these journals for the year filter options
            $availableYears = Issue::whereIn('journal_id', $selectedJournalIds)
                ->whereNotNull('year')
                ->where('year', '!=', '')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->values();

            // Retrieve all issues of these journals for filter options & count
            $allIssues = Issue::whereIn('journal_id', $selectedJournalIds)
                ->orderBy('year', 'desc')
                ->orderBy('volume', 'desc')
                ->orderBy('number', 'desc')
                ->get();

            $selectedIssue = null;
            if (!$isScope && !empty($issueId) && $issueId !== 'all') {
                $selectedIssue = $allIssues->firstWhere('id', $issueId);
                if (!$selectedIssue) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Issue tidak ditemukan pada jurnal ini',
                    ], 404);
                }
            }

            // Retrieve issues with submissions, payment invoices, and payments
            $issuesQuery = Issue::whereIn('journal_id', $selectedJournalIds)
                ->with(['submissions.paymentInvoices.payments'])
                ->orderBy('year', 'asc')
                ->orderBy('volume', 'asc')
                ->orderBy('number', 'asc');

            if ($selectedIssue) {
                $issuesQuery->where('id', $selectedIssue->id);
            } elseif (!empty($filterYear) && $filterYear !== 'all') {
                $issuesQuery->where('year', $filterYear);
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

            $journalChartData = [];
            if ($isScope) {
                foreach ($selectedJournals as $j) {
                    $journalChartData[$j->id] = [
                        'name' => Str::limit($j->name, 25),
                        'published' => 0,
                        'unpublished' => 0,
                    ];
                }
            }

            $yearData = [];

            foreach ($issues as $issue) {
                $issueJournal = $journalsById[$issue->journal_id] ?? ($journal ?: Journal::find($issue->journal_id));
                $issueFee = $issue->author_fee ?? ($issueJournal?->author_fee ?? 0);
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

                $issueYear = $issue->year ?: ($issue->created_at ? $issue->created_at->format('Y') : 'Unknown');
                if (!isset($yearData[$issueYear])) {
                    $yearData[$issueYear] = [
                        'published' => 0,
                        'unpublished' => 0,
                    ];
                }
                $yearData[$issueYear]['published'] += $issuePublished;
                $yearData[$issueYear]['unpublished'] += $issueUnpublished;

                $issueLabel = 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ($issue->year ? ' (' . $issue->year . ')' : '');

                if ($isScope) {
                    if (isset($journalChartData[$issue->journal_id])) {
                        $journalChartData[$issue->journal_id]['published'] += $issuePublished;
                        $journalChartData[$issue->journal_id]['unpublished'] += $issueUnpublished;
                    }
                } else {
                    $issueChartCategories[] = $issueLabel;
                    $issueChartPublished[] = $issuePublished;
                    $issueChartUnpublished[] = $issueUnpublished;
                }

                $issuesTableData[] = [
                    'id' => $issue->id,
                    'journal_id' => $issue->journal_id,
                    'journal_name' => $issueJournal?->name ?? '-',
                    'journal_url_path' => $issueJournal?->url_path ?? '',
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
                    'action_url' => $issueJournal ? route('back.journal.article.index', [$issueJournal->url_path, $issue->id]) : '#',
                ];
            }

            $journalsTableData = [];
            if ($isScope) {
                $issuesByJournal = collect($issuesTableData)->groupBy('journal_id');

                foreach ($selectedJournals as $j) {
                    $jIssues = $issuesByJournal->get($j->id, collect())->values()->all();

                    $journalsTableData[] = [
                        'journal_id' => $j->id,
                        'journal_name' => $j->name,
                        'journal_title' => $j->title,
                        'journal_url_path' => $j->url_path,
                        'journal_type' => $j->type,
                        'journal_author_fee' => (int)($j->author_fee ?? 0),
                        'total_issues' => count($jIssues),
                        'total_articles' => (int)array_sum(array_column($jIssues, 'total_articles')),
                        'published_count' => (int)array_sum(array_column($jIssues, 'published_count')),
                        'unpublished_count' => (int)array_sum(array_column($jIssues, 'unpublished_count')),
                        'lunas_count' => (int)array_sum(array_column($jIssues, 'lunas_count')),
                        'belum_lunas_count' => (int)array_sum(array_column($jIssues, 'belum_lunas_count')),
                        'belum_bayar_count' => (int)array_sum(array_column($jIssues, 'belum_bayar_count')),
                        'free_count' => (int)array_sum(array_column($jIssues, 'free_count')),
                        'total_income' => (int)array_sum(array_column($jIssues, 'total_income')),
                        'issues' => array_reverse($jIssues),
                    ];
                }
            }

            if ($isScope) {
                $issueChartCategories = array_column(array_values($journalChartData), 'name');
                $issueChartPublished = array_column(array_values($journalChartData), 'published');
                $issueChartUnpublished = array_column(array_values($journalChartData), 'unpublished');
                $issueChartMode = 'by_journal';
                $issueChartTitle = 'Statistik Artikel Publish vs Belum Publish per Jurnal';
                $issueChartDesc = 'Perbandingan jumlah artikel terbit dan dalam proses per jurnal';
            } else {
                $issueChartMode = 'by_issue';
                $issueChartTitle = 'Statistik Artikel Publish vs Belum Publish per Edisi';
                $issueChartDesc = 'Perbandingan jumlah artikel terbit dan dalam proses per edisi/issue';
            }

            // Waiting submissions for selected journals
            $waitingSubmissionsQuery = WaitingSubmission::whereIn('target_journal_id', $selectedJournalIds);
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

            $issuesOptions = $isScope ? [] : $allIssues->map(function ($iss) {
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

            if ($isScope) {
                $journalPayload = [
                    'id' => $resolved['scope_id'],
                    'name' => $resolved['scope_name'],
                    'title' => $resolved['scope_name'],
                    'url_path' => null,
                    'is_scope' => true,
                    'scope_id' => $resolved['scope_id'],
                    'total_journals' => $selectedJournals->count(),
                    'author_fee' => 0,
                    'journal_author_fee' => 0,
                    'total_issues' => $allIssues->count(),
                    'filtered_issues_count' => $issues->count(),
                    'selected_year' => (!empty($filterYear) && $filterYear !== 'all') ? $filterYear : null,
                    'selected_issue' => null,
                ];
            } else {
                $journalPayload = [
                    'id' => $journal->id,
                    'name' => $journal->name,
                    'title' => $journal->title,
                    'url_path' => $journal->url_path,
                    'is_scope' => false,
                    'scope_id' => null,
                    'total_journals' => 1,
                    'author_fee' => (int)($selectedIssue ? ($selectedIssue->author_fee ?? ($journal->author_fee ?? 0)) : ($journal->author_fee ?? 0)),
                    'journal_author_fee' => (int)($journal->author_fee ?? 0),
                    'total_issues' => $allIssues->count(),
                    'filtered_issues_count' => $issues->count(),
                    'selected_year' => (!empty($filterYear) && $filterYear !== 'all') ? $filterYear : null,
                    'selected_issue' => $selectedIssue ? [
                        'id' => $selectedIssue->id,
                        'label' => 'Vol. ' . $selectedIssue->volume . ' No. ' . $selectedIssue->number . ($selectedIssue->year ? ' (' . $selectedIssue->year . ')' : ''),
                        'author_fee' => (int)($selectedIssue->author_fee ?? ($journal->author_fee ?? 0)),
                    ] : null,
                ];
            }

            return response()->json([
                'success' => true,
                'journal' => $journalPayload,
                'years_options' => $availableYears,
                'issues_options' => $issuesOptions,
                'summary' => [
                    'is_scope' => $isScope,
                    'is_issue_filtered' => !empty($selectedIssue),
                    'is_year_filtered' => (!empty($filterYear) && $filterYear !== 'all'),
                    'selected_year' => (!empty($filterYear) && $filterYear !== 'all') ? $filterYear : null,
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
                        'mode' => $issueChartMode,
                        'title' => $issueChartTitle,
                        'description' => $issueChartDesc,
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
                'journals_table' => $journalsTableData,
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

    public function journalSubmissions(Request $request)
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
            $year = $request->get('year');
            $type = $request->get('type', 'belum_lunas');

            if (!in_array($type, ['belum_lunas', 'belum_bayar'])) {
                $type = 'belum_lunas';
            }

            // Get journals accessible to this user
            $journals = Journal::orderBy('type')->orderBy('name')->get()
                ->filter(fn($j) => $this->canUserAccessJournal($user, $j))
                ->values();

            if (!$journalId) {
                $matchingControlPanelJournal = $journals->firstWhere('type', $controlPanel);
                $journalId = $matchingControlPanelJournal ? $matchingControlPanelJournal->id : $journals->first()?->id;
            }

            $resolved = $this->resolveSelectedJournals($user, (string)$journalId, $journals);
            if (!$resolved['authorized']) {
                return response()->json([
                    'success' => false,
                    'message' => $resolved['message'],
                ], $resolved['error_code']);
            }

            $isScope = $resolved['is_scope'];
            $selectedJournals = $resolved['journals'];
            $selectedJournalIds = $selectedJournals->pluck('id')->all();
            $journalsById = $selectedJournals->keyBy('id');
            $journal = $resolved['journal'];

            $selectedIssue = null;
            if (!$isScope && !empty($issueId) && $issueId !== 'all') {
                $selectedIssue = Issue::where('journal_id', $journal->id)->where('id', $issueId)->first();
                if (!$selectedIssue) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Issue tidak ditemukan pada jurnal ini',
                    ], 404);
                }
            }

            $issuesQuery = Issue::whereIn('journal_id', $selectedJournalIds)
                ->with(['submissions.paymentInvoices.payments'])
                ->orderBy('year', 'desc')
                ->orderBy('volume', 'desc')
                ->orderBy('number', 'desc');

            if ($selectedIssue) {
                $issuesQuery->where('id', $selectedIssue->id);
            } elseif (!empty($year) && $year !== 'all') {
                $issuesQuery->where('year', $year);
            }

            $issues = $issuesQuery->get();

            $submissionsList = [];
            $totalFee = 0;
            $totalPaid = 0;
            $totalRemaining = 0;

            foreach ($issues as $issue) {
                $issueJournal = $journalsById[$issue->journal_id] ?? ($journal ?: Journal::find($issue->journal_id));
                $issueFee = (int)($issue->author_fee ?? ($issueJournal?->author_fee ?? 0));
                $issueLabel = 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ($issue->year ? ' (' . $issue->year . ')' : '');

                foreach ($issue->submissions as $submission) {
                    $subFee = $issueFee;
                    $isFree = ($submission->free_charge == 1) || ($subFee <= 0);

                    if ($isFree) {
                        continue;
                    }

                    $paidInvoices = $submission->paymentInvoices->where('is_paid', 1);
                    $paidPercent = (int)$paidInvoices->sum('payment_percent');
                    $paidAmount = (int)$paidInvoices->sum('payment_amount');

                    if ($paidAmount == 0) {
                        $acceptedPayments = $submission->paymentInvoices->flatMap->payments->where('payment_status', 'accepted');
                        $paidAmount = (int)$acceptedPayments->sum('payment_amount');
                    }

                    $isLunas = ($submission->payment_status === 'paid')
                        || ($paidPercent >= 100)
                        || ($subFee > 0 && $paidAmount >= $subFee);

                    if ($isLunas) {
                        continue;
                    }

                    $isBelumLunas = ($paidPercent > 0 || $paidAmount > 0);
                    $isBelumBayar = (!$isBelumLunas);

                    if ($type === 'belum_lunas' && !$isBelumLunas) {
                        continue;
                    }

                    if ($type === 'belum_bayar' && !$isBelumBayar) {
                        continue;
                    }

                    $remaining = max(0, $subFee - $paidAmount);
                    $totalFee += $subFee;
                    $totalPaid += $paidAmount;
                    $totalRemaining += $remaining;

                    $effectivePercent = $paidPercent;
                    if ($effectivePercent == 0 && $subFee > 0 && $paidAmount > 0) {
                        $effectivePercent = (int)round(($paidAmount / $subFee) * 100);
                    }

                    // Author formatting
                    $authorDisplay = '-';
                    if (!empty($submission->authorsString)) {
                        $authorDisplay = $submission->authorsString;
                    } elseif (is_array($submission->authors)) {
                        $names = collect($submission->authors)->pluck('name')->filter()->implode(', ');
                        if (!empty($names)) {
                            $authorDisplay = $names;
                        }
                    }

                    // Invoices formatting
                    $invoicesSummary = [];
                    foreach ($submission->paymentInvoices as $inv) {
                        $invoicesSummary[] = [
                            'id' => $inv->id,
                            'invoice_number' => $inv->invoice_number ?: '-',
                            'payment_percent' => $inv->payment_percent,
                            'payment_amount' => (int)$inv->payment_amount,
                            'is_paid' => (bool)$inv->is_paid,
                            'due_date' => $inv->payment_due_date ? date('d M Y', strtotime($inv->payment_due_date)) : null,
                        ];
                    }

                    $submissionsList[] = [
                        'id' => $submission->id,
                        'submission_id' => $submission->submission_id,
                        'title' => $submission->fullTitle ?: 'Tanpa Judul',
                        'authors' => $authorDisplay,
                        'issue_id' => $issue->id,
                        'issue_label' => $issueLabel,
                        'journal_id' => $issue->journal_id,
                        'journal_name' => $issueJournal?->name ?? '-',
                        'status' => $submission->status,
                        'status_label' => $submission->status_label ?: ($submission->status == '3' ? 'Published' : 'Belum Publish'),
                        'is_published' => ($submission->status == '3'),
                        'payment_status' => $submission->payment_status,
                        'author_fee' => $subFee,
                        'paid_amount' => $paidAmount,
                        'remaining_amount' => $remaining,
                        'paid_percent' => $effectivePercent,
                        'invoices' => $invoicesSummary,
                        'action_url' => $issueJournal ? route('back.journal.article.index', [$issueJournal->url_path, $issue->id]) : '#',
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'meta' => [
                    'type' => $type,
                    'type_label' => $type === 'belum_lunas' ? 'Belum Lunas (DP/Cicil)' : 'Belum Bayar (0%)',
                    'journal' => [
                        'id' => $isScope ? $resolved['scope_id'] : $journal->id,
                        'name' => $isScope ? $resolved['scope_name'] : $journal->name,
                        'url_path' => $isScope ? null : $journal->url_path,
                        'is_scope' => $isScope,
                    ],
                    'issue' => $selectedIssue ? [
                        'id' => $selectedIssue->id,
                        'label' => 'Vol. ' . $selectedIssue->volume . ' No. ' . $selectedIssue->number . ($selectedIssue->year ? ' (' . $selectedIssue->year . ')' : ''),
                    ] : null,
                    'year' => (!empty($year) && $year !== 'all') ? $year : null,
                    'total_count' => count($submissionsList),
                    'total_fee' => $totalFee,
                    'total_paid' => $totalPaid,
                    'total_remaining' => $totalRemaining,
                ],
                'submissions' => $submissionsList,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat data submission',
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
