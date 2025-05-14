<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\SettingBanner;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            ];

            if (is_array($journal->indexing)) {
                foreach ($journal->indexing as $index) {
                    if (isset($indexingIcons[$index])) {
                        $iconIndexing[] = $indexingIcons[$index];
                    }
                }
            }

            $journal->indexing = $iconIndexing ?? [];
            return $journal;
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
}
