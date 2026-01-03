<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private $url_wa;
    private $session_wa;
    private $secret_key;

    public function __construct()
    {
        $this->url_wa = env('WHATSAPP_API_URL');
        $this->session_wa = env('WHATSAPP_API_SESSION');
        $this->secret_key = env('WHATSAPP_API_SECRET');
    }

    /**
     * Send a WhatsApp message
     *
     * @param string $phone
     * @param string $message
     * @return array
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            // Format phone number (remove leading 0 and add country code if needed)
            $phone = $this->formatPhoneNumber($phone);

            $response = Http::timeout(60)->post($this->url_wa . "/send-message", [
                'session' => $this->session_wa,
                'to' => $phone,
                'text' => $message
            ]);

            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Message sent successfully'
                ];
            }

            Log::error('WhatsApp API Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send 2FA code via WhatsApp
     *
     * @param string $phone
     * @param string $code
     * @param string $userName
     * @return array
     */
    public function send2FACode(string $phone, string $code, string $userName): array
    {
        $message = "🔐 *Kode Verifikasi 2FA*\n\n"
            . "Halo {$userName},\n\n"
            . "Kode verifikasi Anda adalah:\n\n"
            . "*{$code}*\n\n"
            . "Kode ini berlaku selama *10 menit*.\n\n"
            . "Jangan bagikan kode ini kepada siapapun.\n\n"
            . "Jika Anda tidak meminta kode ini, abaikan pesan ini.\n\n"
            . "---\n"
            . "_Pesan ini dikirim otomatis oleh sistem Rumah Jurnal_";

        return $this->sendMessage($phone, $message);
    }

    /**
     * Format phone number to international format
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62 (Indonesia)
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with country code, add 62
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Check if WhatsApp session is active
     *
     * @return bool
     */
    public function isSessionActive(): bool
    {
        try {
            $response = Http::timeout(60)->get($this->url_wa . "/sessions?key=" . $this->secret_key);

            if ($response->status() === 200) {
                $sessions = $response->json()['data'] ?? [];
                return in_array($this->session_wa, $sessions);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp Session Check Error', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }
}
