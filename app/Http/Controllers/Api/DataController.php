<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\News;
use App\Models\SettingBanner;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DataController extends Controller
{
    public function dataBanner(Request $request)
    {
        $banner = SettingBanner::where('status', 1)->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'image' => Storage::url($item->image),
                'url' => $item->url,
            ];
        });
        if ($banner->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $banner], 200);
    }
    public function dataWebsite(Request $request)
    {
        $setting_web = SettingWebsite::first();
        $allIndexing = Journal::pluck('indexing')->flatMap(fn($item) => $item);
        $data = [
            "website" => $setting_web,
            "indexing" => $allIndexing->countBy(),
        ];
        return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $data], 200);
    }

    public function dataJournal(Request $request)
    {
        $journals = Journal::orderBy('context_id')->get()->map(function ($journal) {
            $iconIndexing = [];
            $indexingIcons = [
                "Sinta 1" => ['icon' => url('/icons/sinta1.png'), 'label' => 'SINTA-1'],
                "Sinta 2" => ['icon' => url('/icons/sinta2.png'), 'label' => 'SINTA-2'],
                "Sinta 3" => ['icon' => url('/icons/sinta3.png'), 'label' => 'SINTA-3'],
                "Sinta 4" => ['icon' => url('/icons/sinta4.png'), 'label' => 'SINTA-4'],
                "Sinta 5" => ['icon' => url('/icons/sinta5.png'), 'label' => 'SINTA-5'],
                "Sinta 6" => ['icon' => url('/icons/sinta6.png'), 'label' => 'SINTA-6'],
                "Scopus" => ['icon' => url('/icons/scopus.png'), 'label' => 'SCOPUS'],
                "Garuda" => ['icon' => url('/icons/garuda.png'), 'label' => 'GARUDA'],
                "Scholar" => ['icon' => url('/icons/scholar.png'), 'label' => 'SCHOLAR'],
                "Moraref" => ['icon' => url('/icons/moraref.png'), 'label' => 'MORAREF'],
                "DOAJ" => ['icon' => url('/icons/doaj.png'), 'label' => 'DOAJ'],
                "Crossref" => ['icon' => url('/icons/crossref.png'), 'label' => 'CROSSREF'],
                "Copernicus" => ['icon' => url('/icons/copernicus.png'), 'label' => 'COPERNICUS'],
                "WOS" => ['icon' => url('/icons/wos.png'), 'label' => 'WOS'],
                "EBSCO" => ['icon' => url('/icons/ebsco.png'), 'label' => 'EBSCO'],
            ];

            if (is_array($journal->indexing)) {
                foreach ($journal->indexing as $index) {
                    if (isset($indexingIcons[$index])) {
                        $iconIndexing[] = $indexingIcons[$index];
                    }
                }
            }

            $journal->indexing = $iconIndexing ?? [];
            $journal->api_key = null; // Remove api_key from the response
            $journal->ojs_version = null; // Remove ojs_version from the response
            return $journal;
        });

        if ($journals->isNotEmpty()) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $journals], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }

    public function dataJournal2(Request $request)
    {
        $journals = Journal::orderBy('context_id')->get()->makeHidden(['api_key', 'ojs_version', 'created_at', 'updated_at', 'deleted_at', 'url_path', 'context_id', 'id', 'last_sync', 'thumbnail', 'url', 'editor_chief_signature', 'indexing_others', 'subject', 'faculty']);

        if ($journals->isNotEmpty()) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $journals], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }

    public function dataPayment(Request $request)
    {
        $journals = Journal::orderBy('context_id')->where('author_fee', '>', '0')->get()->map(function ($journal) {
            return [
                'jurnal_name' => $journal->name,
                'jurnal_title' => $journal->title,
                'payment_info' => $journal->issues()->where('status', 'unpublished')->get()->map(function ($issue) {
                    return [
                        'issue' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' (' . $issue->year . ')',
                        'submission' => $issue->submissions()->get()->map(function ($submission) use ( $issue) {
                            return [
                               'submission_id' => $submission->submission_id,
                               'article_id' => $submission->submission_id,
                               'authors' => $submission->authorsString,
                               'invoice' => $submission->paymentInvoices()->get()->map(function ($invoice) use ($submission, $issue) {
                                    return [
                                        'invoice' => $invoice->invoice_number . '/JRNL/UINSMDD/' . date('Y', strtotime($invoice->created_at)),
                                        'amount' => 'Rp ' . number_format($invoice->payment_amount, 0, ',', '.'),
                                        'percentage' => $invoice->payment_percent . '%',
                                        'status' => $invoice->is_paid ? 'sudah bayar' : 'belum bayar',
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ];
        });

        if ($journals->isNotEmpty()) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $journals], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }

    public function dataJournalContext(Request $request, $context_id)
    {
        $journal = Journal::where('context_id', $context_id)->orWhere('url_path', $context_id)->first();
        $iconIndexing = [];

        if ($journal && is_array($journal->indexing)) {
            $indexingIcons = [
                "Sinta 1" => ['icon' => url('/icons/sinta1.png'), 'label' => 'SINTA-1'],
                "Sinta 2" => ['icon' => url('/icons/sinta2.png'), 'label' => 'SINTA-2'],
                "Sinta 3" => ['icon' => url('/icons/sinta3.png'), 'label' => 'SINTA-3'],
                "Sinta 4" => ['icon' => url('/icons/sinta4.png'), 'label' => 'SINTA-4'],
                "Sinta 5" => ['icon' => url('/icons/sinta5.png'), 'label' => 'SINTA-5'],
                "Sinta 6" => ['icon' => url('/icons/sinta6.png'), 'label' => 'SINTA-6'],
                "Scopus" => ['icon' => url('/icons/scopus.png'), 'label' => 'SCOPUS'],
                "Garuda" => ['icon' => url('/icons/garuda.png'), 'label' => 'GARUDA'],
                "Scholar" => ['icon' => url('/icons/scholar.png'), 'label' => 'SCHOLAR'],
                "Moraref" => ['icon' => url('/icons/moraref.png'), 'label' => 'MORAREF'],
                "DOAJ" => ['icon' => url('/icons/doaj.png'), 'label' => 'DOAJ'],
                "Crossref" => ['icon' => url('/icons/crossref.png'), 'label' => 'CROSSREF'],
                "Copernicus" => ['icon' => url('/icons/copernicus.png'), 'label' => 'COPERNICUS'],
                "WOS" => ['icon' => url('/icons/wos.png'), 'label' => 'WOS'],
            ];

            foreach ($journal->indexing as $index) {
                if (isset($indexingIcons[$index])) {
                    $iconIndexing[] = $indexingIcons[$index];
                }
            }
        }
        $journal->indexing = $iconIndexing ?? [];


        if ($journal) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $journal,], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }

    public function dataIssue(Request $request, $journal_id)
    {
        $journal = Journal::find($journal_id);
        if (!$journal) {
            return response()->json(['status' => false, 'message' => 'Journal not found'], 404);
        }
        $issues = $journal->issues()->orderBy('year', 'desc')->orderBy('volume', 'desc')->orderBy('number', 'desc')->get();
        if ($issues->isNotEmpty()) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $issues], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }

    public function dataNews(Request $request)
    {
        $news = News::with(['category', 'user'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'image' => $item->getThumbnail(),
                'excerpt' => Str::limit(strip_tags($item->content), 100, '...'),
                'category' => $item->category ? $item->category->name : null,
                'comment_count' => $item->comments()->count(),
                'viewers_count' => $item->viewers()->count(),
                'author' => $item->user ? $item->user->name : 'Unknown',
                'created_at' => $item->created_at ? $item->created_at->format('d M Y') : null,
                'url' => route('news.detail', $item->slug),
            ];
            });

        if ($news->isNotEmpty()) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $news], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }
}
