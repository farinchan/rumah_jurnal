<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Controller;
use App\Models\Editor;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeamController extends Controller
{
    public function editor(Request $request)
    {
        $path = $request->journal;
        if (!$path) {
            $journal_first = Journal::first()->url_path;
            return redirect()->route("team.editor", ['journal' => $journal_first]);
        }
        $setting_web = SettingWebsite::first();

         $journals = Journal::get();
        $journalData = [];

        foreach ($journals as $journal) {
            $journalData[] = $this->editorCache($request, $journal->url_path);
        }

        $editorMap = [];

        foreach ($journalData as $journalEntry) {
            $journalName = $journalEntry['journal'];
            $urlPath = $journalEntry['url_path'];
            foreach ($journalEntry['editor'] as $editor) {
                $id = $editor['id'];

                if (!isset($editorMap[$id])) {
                    // Salin data editor dan buat array jurnal
                    $editorMap[$id] = $editor;
                    $editorMap[$id]['jurnal'] = [];
                }

                $editorMap[$id]['jurnal'][] = [
                    'name' => $journalName,
                    'path' => $urlPath
                ];
            }
        }

        $targetEditors = [];
        foreach ($journalData as $journalEntry) {
            if ($journalEntry['url_path'] === $path) {
                foreach ($journalEntry['editor'] as $editor) {
                    $id = $editor['id'];
                    $targetEditors[] = $editorMap[$id];
                }

                $finalOutput = [
                    'journal' => $journalEntry['journal'],
                    'url_path' => $journalEntry['url_path'],
                    'editor' => $targetEditors,
                ];
                break;
            }
        }

        $data = [
            'title' =>  'Editor | ' . $setting_web->name,
            'meta' => [
                'title' => 'Editor | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Contact Us, Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => 'Editor',
                    'link' => route('team.editor')
                ]
            ],
            'setting_web' => SettingWebsite::first(),
            'journals' => Journal::all(),
            'editors' => $targetEditors
            // 'issues' => Issue::whereHas('journal', function ($query) use ($path) {
            //     $query->where('url_path', $path);
            // })->with(['editors' => function ($query) {
            //     $query->orderBy('name', 'asc');
            // }])->get(),
        ];

        // return response()->json($data);
        return view('front.pages.team.editor', $data);
    }

    private function editorCache(Request $request, $url_path)
    {
        $jurnal = Journal::where('url_path', $url_path)->first();
        // cache()->forget($url_path . '_reviewer_list_cache');

        try {
            $cacheKey =  $url_path . '_editor_list_cache';
            $cachedData = cache()->get($cacheKey);

            if ($cachedData) {
                return $cachedData;
            }

           $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/users', [
                'roleIds' => '16,17',
                'orderBy' => 'id',
                'count' => 100,
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {
                $data = [
                    'journal' => $jurnal->title,
                    'url_path' => $jurnal->url_path,
                    'message' => 'Success get editor list',
                    'editor' => collect($response->json()["items"] ?? [])->map(function ($item) {
                        return [
                            'id' => $item['id'] ?? null,
                            'fullName' => $item['fullName'] ?? null,
                            'email' => $item['email'] ?? null,
                            'userName' => $item['userName'] ?? null,
                            'affiliation' => $item['affiliation']['en_US'] ?? null,
                        ];
                    })->all(),
                ];

                cache()->put($cacheKey, $data, now()->addMinutes(120));


                return $data;
            } else {
                return [
                    'message' => 'Error: ' . $response->status(),
                ];
            }
        } catch (\Throwable $th) {
            return [
                'message' => 'Error: ' . $th->getMessage(),
            ];
        }
    }


    public function reviewer(Request $request)
    {
        $path = $request->journal;
        if (!$path) {
            $journal_first = Journal::first()->url_path;
            return redirect()->route("team.reviewer", ['journal' => $journal_first]);
        }
        $setting_web = SettingWebsite::first();

        $journals = Journal::get();
        $journalData = [];

        foreach ($journals as $journal) {
            $journalData[] = $this->reviewerCache($request, $journal->url_path);
        }

        $reviewerMap = [];

        foreach ($journalData as $journalEntry) {
            $journalName = $journalEntry['journal'];
            $urlPath = $journalEntry['url_path'];
            foreach ($journalEntry['reviewer'] as $reviewer) {
                $id = $reviewer['id'];

                if (!isset($reviewerMap[$id])) {
                    // Salin data reviewer dan buat array jurnal
                    $reviewerMap[$id] = $reviewer;
                    $reviewerMap[$id]['jurnal'] = [];
                }

                $reviewerMap[$id]['jurnal'][] = [
                    'name' => $journalName,
                    'path' => $urlPath
                ];
            }
        }

        $targetReviewers = [];
        foreach ($journalData as $journalEntry) {
            if ($journalEntry['url_path'] === $path) {
                foreach ($journalEntry['reviewer'] as $reviewer) {
                    $id = $reviewer['id'];
                    $targetReviewers[] = $reviewerMap[$id];
                }

                $finalOutput = [
                    'journal' => $journalEntry['journal'],
                    'url_path' => $journalEntry['url_path'],
                    'reviewer' => $targetReviewers,
                ];
                break;
            }
        }


        $data = [
            'title' =>  'Reviewer | ' . $setting_web->name,
            'meta' => [
                'title' => 'Reviewer | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Contact Us, Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => 'Reviewer',
                    'link' => route('team.reviewer')
                ]
            ],
            'setting_web' => SettingWebsite::first(),
            'journals' => Journal::all(),
            'reviewers' => $targetReviewers,
            // 'issues' => Issue::whereHas('journal', function ($query) use ($path) {
            //     $query->where('url_path', $path);
            // })->with(['reviewers' => function ($query) {
            //     $query->orderBy('name', 'asc');
            // }])->get(),
        ];
        // return response()->json($data);
        return view('front.pages.team.reviewer', $data);
    }

    private function reviewerCache(Request $request, $url_path)
    {
        $jurnal = Journal::where('url_path', $url_path)->first();
        // cache()->forget($url_path . '_reviewer_list_cache');

        try {
            $cacheKey =  $url_path . '_reviewer_list_cache';
            $cachedData = cache()->get($cacheKey);

            if ($cachedData) {
                return $cachedData;
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/users/reviewers', [
                'orderBy' => 'id',
                'count' => 100,
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {
                $data = [
                    'journal' => $jurnal->title,
                    'url_path' => $jurnal->url_path,
                    'message' => 'Success get reviewer list',
                    'reviewer' => collect($response->json()["items"] ?? [])->map(function ($item) {
                        return [
                            'id' => $item['id'] ?? null,
                            'fullName' => $item['fullName'] ?? null,
                            'email' => $item['email'] ?? null,
                            'userName' => $item['userName'] ?? null,
                            'affiliation' => $item['affiliation']['en_US'] ?? null,
                        ];
                    })->all(),
                ];

                cache()->put($cacheKey, $data, now()->addMinutes(120));

                return $data;
            } else {
                return [
                    'message' => 'Error: ' . $response->status(),
                ];
            }
        } catch (\Throwable $th) {
            return [
                'message' => 'Error: ' . $th->getMessage(),
            ];
        }
    }
}
