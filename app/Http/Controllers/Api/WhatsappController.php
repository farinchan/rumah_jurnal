<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    private $url_wa;
    private $session_wa;
    private $secret_key;

    private $chatery_url_wa;
    private $chatery_session_wa;
    private $chatery_secret_key;

    public function __construct()
    {
        $this->url_wa = env('WHATSAPP_API_URL');
        $this->session_wa = env('WHATSAPP_API_SESSION');
        $this->secret_key = env('WHATSAPP_API_SECRET');

        $this->chatery_url_wa = env('CHATERY_WHATSAPP_API_URL');
        $this->chatery_session_wa = env('CHATERY_WHATSAPP_API_SESSION');
        $this->chatery_secret_key = env('CHATERY_WHATSAPP_API_SECRET');
    }

    public function chateryServerStatus()
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
                'Content-Type' => 'application/json',
            ])->get($this->chatery_url_wa . '/api/health');

            if ($response->successful()) {
                return response()->json($response->json(), $response->status());
            } else {
                return response()->json(['status' => 'error', 'message' => 'Failed to fetch WAHA API server status', 'data' => null], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred: ' . $th->getMessage(), 'data' => null], 500);
        }
    }

    public function chateryGetMySession(Request $request)
    {
        try {
            $response_wa = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->get($this->chatery_url_wa  . "/api/whatsapp/sessions/"  . $this->chatery_session_wa . "/status");


            return response()->json($response_wa->json(), $response_wa->status());
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

    public function chaterySessionStart(Request $request)
    {

        try {

            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/sessions/"  . $this->chatery_session_wa . "/connect");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage(), 'data' => null], 500);
        }
    }

    public function chaterySessionLogout(Request $request)
    {

        try {

            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->delete($this->chatery_url_wa  . "/api/whatsapp/sessions/"  . $this->chatery_session_wa);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage(), 'data' => null], 500);
        }
    }

    public function chaterySessionAuthQrCode(Request $request)
    {
        try {

            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->get($this->chatery_url_wa  . "/api/whatsapp/sessions/"  . $this->chatery_session_wa . "/qr");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage(), 'data' => null], 500);
        }
    }



    public function getAllSessions(Request $request)
    {
        try {
            $response_wa = Http::timeout(60)->get($this->url_wa  . "/sessions?key=" . $this->secret_key);

            if ($response_wa->status() != 200) {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to get all sessions',
                    'error' => $response_wa->json(),
                ];

                return response()->json($response, $response_wa->status());
            } else {
                $response = [
                    'status' => 'success',
                    'data' => $response_wa->json()['data'],
                ];

                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

    public function getMySession(Request $request)
    {
        try {
            $response_wa = Http::timeout(60)->get($this->url_wa  . "/sessions?key=" . $this->secret_key);

            if ($response_wa->status() != 200) {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to get session',
                    'error' => $response_wa->json(),
                ];

                return response()->json($response, $response_wa->status());
            } else {
                if (in_array($this->session_wa, $response_wa->json()['data'])) {
                    $response = [
                        'status' => 'Terhubung',
                        'message' => 'Session found',
                        'data' => $response_wa->json()['data'],
                    ];

                    return response()->json($response);
                } else {
                    $response = [
                        'status' => 'Tidak Terhubung',
                        'message' => 'Session not found',
                    ];

                    return response()->json($response);
                }
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }


    public function deleteSession(Request $request)
    {
        try {
            $response_wa = Http::timeout(60)->get($this->url_wa  . "/delete-session", [
                'session' => $this->session_wa
            ]);

            if ($response_wa->status() != 200) {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to delete session',
                    'error' => $response_wa->json(),
                ];

                return response()->json($response, $response_wa->status());
            } else {
                $response = [
                    'status' => 'success',
                    'message' => 'Session deleted successfully',
                ];

                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            // $response_wa = Http::timeout(60)->post($this->url_wa  . "/send-message", [
            //     'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
            //     'to' => $request->phone,
            //     'text' => $request->message
            // ]);

            $response_wa = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/chats/send-text", [
                'sessionId' => env('CHATERY_WHATSAPP_API_SESSION'), // Use the session name from your environment variable
                'chatId' => $request->phone,
                'message' => $request->message,
                'typingTime' => rand(1000, 3000),
            ]);

            if ($response_wa->status() != 200) {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to send message',
                    'error' => $response_wa->json(),
                ];

                return response()->json($response, $response_wa->status());
            } else {
                $response = [
                    'status' => 'success',
                    'message' => 'Message sent successfully',
                ];

                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

    public function sendBulkMessage(Request $request)
    {
        // example of request data
        // {
        //     "sessionId": "mysession",
        //     "recipients": ["628123456789", "628987654321", "628111222333"],
        //     "message": "Hello! This is a bulk message.",
        //     "delayBetweenMessages": 1000,
        //     "typingTime": 0
        // }
        try {
            $response_wa = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/chats/send-bulk", [
                'sessionId' => env('CHATERY_WHATSAPP_API_SESSION'),
                'recipients' => $request->data,
                'message' => $request->message,
                'delayBetweenMessages' => $request->delayBetweenMessages ?? 1000,
                'typingTime' => $request->typingTime ?? rand(1000, 3000),
            ]);

            if ($response_wa->status() != 200) {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to send bulk message',
                ];

                return response()->json($response, $response_wa->status());
            } else {
                $response = [
                    'status' => 'success',
                    'message' => 'Bulk message sent successfully',
                ];

                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

    public function sendImage(Request $request)
    {
        try {
            // $response_wa = Http::timeout(60)->post($this->url_wa  . "/send-image", [
            //     'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
            //     'to' => $request->phone,
            //     'urlImage' => $request->image,
            //     'caption' => $request->message
            // ]);

            $response_wa = Http::timeout(40)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/chats/send-image", [
                'sessionId' => env('CHATERY_WHATSAPP_API_SESSION'), // Use the session name from your environment variable
                'chatId' => $request->phone,
                "imageUrl" => $request->image,
                "caption" => $request->message,
                "typingTime" => rand(1000, 3000),
            ]);

            if ($response_wa->status() != 200) {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to send image',
                ];

                return response()->json($response, $response_wa->status());
            } else {
                $response = [
                    'status' => 'success',
                    'message' => 'Image sent successfully',
                ];

                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }
}
