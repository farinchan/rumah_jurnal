<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Reviewer;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;

class JournalController extends Controller
{
    public function journalStore(Request $request)
    {
        $name = $request->name;
        $url = $request->url;
        $api_key = $request->api_key;
        $url_path = $request->url_path;
        $ojs_version = $request->ojs_version ?? "3.3";

        if (!$url || !$api_key || !$url_path || !$ojs_version) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'api_key, url, url_path, ojs_version is required'
            ], 400);
        }

        if (Journal::where('url_path', $url_path)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Journal already exists'
            ], 400);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key
            ])->get($url . '/api/v1/contexts', [
                'apiToken' => $api_key
            ]);

            if ($response->status() === 200) {
                $another = collect($response->json()["items"])->firstWhere('urlPath', $url_path);
                $response_another = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key
                ])->get(
                    $another['_href'],
                    ['apiToken' => $api_key]
                );

                if ($response_another->status() === 200) {

                    $jurnal = new Journal();
                    $jurnal->name = $name;
                    $jurnal->context_id = $response_another->json()["id"];
                    $jurnal->url = $response_another->json()["url"];
                    $jurnal->url_path = $url_path;
                    $jurnal->title = $response_another->json()["name"]["en_US"]?? $url_path;
                    $jurnal->description = $response_another->json()["about"]["en_US"]?? "No Description";
                    $jurnal->thumbnail = $response_another->json()["journalThumbnail"]['en_US']['uploadName']?? null;
                    $jurnal->onlineIssn = $response_another->json()["onlineIssn"]?? null;
                    $jurnal->printIssn = $response_another->json()["printIssn"]?? null;
                    $jurnal->api_key = $api_key;
                    $jurnal->ojs_version = $ojs_version;
                    $jurnal->last_sync = now();
                    $jurnal->save();

                    Permission::create(['name' =>  $url_path]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Success',
                        'data' => $jurnal
                    ], 200);

                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error',
                        'error' => $response_another->json()["errorMessage"] ?? "something went wrong"
                    ], $response_another->status());
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error',
                    'error' => $response->json()["errorMessage"] ?? "something went wrong"
                ], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function journalSync(Request $request)
    {
        $jurnal = Journal::where('url_path', $request->url_path)->first();

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Journal not found'
            ], 404);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/contexts/' . $jurnal->context_id, [
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {
                $jurnal->title = $response->json()["name"]["en_US"]?? $jurnal->title;
                $jurnal->description = $response->json()["about"]["en_US"]?? $jurnal->description;
                $jurnal->thumbnail = $response->json()["journalThumbnail"]['en_US']['uploadName']?? $jurnal->thumbnail;
                $jurnal->onlineIssn = $response->json()["onlineIssn"]?? $jurnal->onlineIssn;
                $jurnal->printIssn = $response->json()["printIssn"]?? $jurnal->printIssn;
                $jurnal->last_sync = now();
                $jurnal->save();

                Permission::create(['name' =>  $jurnal->url_path]);

                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $jurnal
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error',
                    'error' => $response->json()["errorMessage"] ?? "something went wrong"
                ], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function submissionsList(Request $request)
    {
        $jurnal = Journal::where('url_path', $request->url_path)->first();

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Journal not found'
            ], 404);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/submissions', [
                'orderBy' => 'dateSubmitted',
                'count' => 200,
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $response->json()["items"] ?? []
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error',
                    'error' => $response->json()["errorMessage"] ?? "something went wrong"
                ], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function submissionsSelect(Request $request)
    {
        $jurnal_path = $request->jurnal_path;
        $submission_id = $request->submission_id;
        $issue_id = $request->issue_id;

        if (!$jurnal_path || !$submission_id) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'jurnal_path' => $jurnal_path,
                'submission_id' => $submission_id,
                'error' => 'jurnal_path, submission_id is required'
            ], 400);
        }

        $jurnal = Journal::where('url_path', $jurnal_path)->first();

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Journal not found'
            ], 404);
        }

        $issue = Issue::where('id', $issue_id)->where('journal_id', $jurnal->id)->first();

        if (!$issue) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Issue not found'
            ], 404);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/submissions/' . $submission_id, [
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {

                $publication_response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $jurnal->api_key
                ])->get($response->json()["publications"][0]["_href"], [
                    'apiToken' => $jurnal->api_key
                ]);

                if ($publication_response->status() === 200) {

                    $submission = Submission::updateOrCreate(
                        [
                            'submission_id' => $submission_id,
                            'issue_id' => $issue->id,
                        ],
                        [
                            'publication_id' => $publication_response->json()["id"],
                            'locale' => $publication_response->json()["locale"],
                            'authors' => $publication_response->json()["authors"],
                            'authorsString' => $publication_response->json()["authorsString"],
                            'fullTitle' => $publication_response->json()["fullTitle"],
                            'abstract' => $publication_response->json()["abstract"],
                            'keywords' => $publication_response->json()["keywords"],
                            'citations' => $publication_response->json()["citations"],
                            'urlPublished' => $publication_response->json()["urlPublished"],
                            'datePublished' => $publication_response->json()["datePublished"],
                            'status' => $response->json()["status"],
                            'status_label' => $response->json()["statusLabel"],
                            'lastModified' => $publication_response->json()["lastModified"],
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Submission data has been updated',
                        'data' => $submission,
                        'data' => [
                            'submission' => $submission,
                            'publication' => $publication_response->json()
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error',
                        'error' => $publication_response->json()["errorMessage"] ?? "something went wrong"
                    ], $publication_response->status());
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error',
                    'error' => $response->json()["errorMessage"] ?? "something went wrong"
                ], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public function reviewerList(Request $request)
    {
        $jurnal = Journal::where('url_path', $request->url_path)->first();

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Journal not found'
            ], 404);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/users/reviewers', [
                'orderBy' => 'id',
                'count' => 100,
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'Success',
                    'data' => $response->json()["items"] ?? []
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error',
                    'error' => $response->json()["errorMessage"] ?? "something went wrong"
                ], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function reviewerSelect(Request $request)
    {
        $jurnal_path = $request->jurnal_path;
        $issue_id = $request->issue_id;
        $reviewer_id = $request->reviewer_id;

        if (!$jurnal_path || !$reviewer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'jurnal_path' => $jurnal_path,
                'reviewer_id' => $reviewer_id,
                'error' => 'jurnal_path, submission_id is required'
            ], 400);
        }

        $jurnal = Journal::where('url_path', $jurnal_path)->first();

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Journal not found'
            ], 404);
        }

        $issue = Issue::where('id', $issue_id)->where('journal_id', $jurnal->id)->first();

        if (!$issue) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'Issue not found'
            ], 404);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/users/' . $reviewer_id, [
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {

                $reviewer = Reviewer::updateOrCreate(
                    [
                        'reviewer_id' => $reviewer_id,
                        'issue_id' => $issue->id,
                    ],
                    [
                        'name' => $response->json()["fullName"],
                        'username' => $response->json()["userName"],
                        'email' => $response->json()["email"],
                        'phone' => $response->json()["phone"],
                        'affiliation' => $response->json()["affiliation"]['en_US'] ?? null,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Reviewer data has been updated',
                    'data' => $reviewer
                ], 200);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error',
                    'error' => $response->json()["errorMessage"] ?? "something went wrong"
                ], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }

    }
}
