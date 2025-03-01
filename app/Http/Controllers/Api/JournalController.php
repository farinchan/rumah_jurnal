<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;

class JournalController extends Controller
{
    public function journalStore(Request $request)
    {
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
                'count' => 50,
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
        $jurnal_path = $request->url_path;
        $submission_id = $request->submission_id;

        if (!$jurnal_path || !$submission_id) {
            return response()->json([
                'success' => false,
                'message' => 'Error',
                'error' => 'url_path, submission_id is required'
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

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization'
                => 'Bearer ' . $jurnal->api_key
            ])->get($jurnal->url . '/api/v1/submissions/' . $submission_id, [
                'apiToken' => $jurnal->api_key
            ]);

            if ($response->status() === 200) {

                $publication_response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization
                    ' => 'Bearer ' . $jurnal->api_key
                ])->get($response->json()["publication"]["_href"], [
                    'apiToken' => $jurnal->api_key
                ]);

                if ($publication_response->status() === 200) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Success',
                        'data' => $publication_response->json()
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
}
