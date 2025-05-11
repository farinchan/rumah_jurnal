<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\SettingBanner;
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

    public function dataJournal(Request $request)
    {
        $journal = Journal::orderBy('context_id')->get();
        if ($journal) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $journal], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }

    public function dataJournalContext(Request $request, $context_id)
    {
        $journal = Journal::where('context_id', $context_id)->first();
        if ($journal) {
            return response()->json(['status' => true, 'message' => 'Data retrieved successfully', 'data' => $journal], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found'], 404);
        }
    }
}
