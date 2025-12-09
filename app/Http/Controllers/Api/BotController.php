<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SettingBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{


    public function handleBot(Request $request)
    {
        $settingBot = SettingBot::first();
        if (!$settingBot) {
            return response()->json(['error' => 'Bot settings not found.'], 404);
        }

        try {
            // $appConfig = config('app.env'); // or however you determine the environment
            $appConfig = "local"; // This should be set based on your environment configuration

            $apiUrl = '';

            if ($appConfig === 'production') {
                $apiUrl = $settingBot->api_production_url;
            } else {
                $apiUrl = $settingBot->api_sandbox_url;
            }

            $response = Http::post($apiUrl, [
                'data' => $request->all(),
                'name' => $settingBot->name,
                'system_message' => $settingBot->system_message,
                'additional_context' => $settingBot->additional_context,
                'signature' => $settingBot->signature,
                'is_active' => $settingBot->is_active,
                'is_whatsapp_active' => $settingBot->is_whatsapp_active,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Failed to communicate with bot API.'], $response->status());
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'An error occurred: ' . $th->getMessage()], 500);
        }
    }
}
