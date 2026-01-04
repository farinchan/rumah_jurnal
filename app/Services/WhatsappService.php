<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
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

            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/chats/send-text", [
                'sessionId' => $this->chatery_session_wa, // Use the session name from your environment variable
                'chatId' => $phone,
                'message' => $message,
                'typingTime' => rand(1000, 3000),
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

    public function sendImage(string $phone, string $imageUrl, string $caption = ''): array
    {
        try {
            // Format phone number (remove leading 0 and add country code if needed)
            $phone = $this->formatPhoneNumber($phone);


            $response = Http::timeout(40)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/chats/send-image", [
                'sessionId' => $this->chatery_session_wa, // Use the session name from your environment variable
                'chatId' => $phone,
                "imageUrl" => $imageUrl,
                "caption" => $caption,
                "typingTime" => rand(1000, 3000),
            ]);

            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Image sent successfully'
                ];
            }

            Log::error('WhatsApp API Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send image',
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

    public function sendDocument(string $phone, string $documentUrl, string $fileName , string $mimetype): array
    {
        try {
            // Format phone number (remove leading 0 and add country code if needed)
            $phone = $this->formatPhoneNumber($phone);

            $response = Http::timeout(40)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/chats/send-document", [
                'sessionId' => $this->chatery_session_wa, // Use the session name from your environment variable
                'chatId' => $phone,
                "documentUrl" => $documentUrl,
                "fileName" => $fileName,
                "mimetype" => $mimetype,
                "typingTime" => rand(1000, 3000),
            ]);
            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Document sent successfully'
                ];
            }
            Log::error('WhatsApp API Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            return [
                'success' => false,
                'message' => 'Failed to send document',
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

    public function sendBulkMessage(array $phones, string $message, int $delay = 1000): array
    {
        try {
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatery_secret_key,
            ])->post($this->chatery_url_wa  . "/api/whatsapp/chats/send-bulk", [
                'sessionId' => $this->chatery_session_wa,
                'recipients' => $phones,
                'message' => $message,
                'delayBetweenMessages' => $delay,
                'typingTime' => rand(1000, 3000),
            ]);

            if ($response->status() === 200) {
                return [
                    'success' => true,
                    'message' => 'Bulk message sent successfully'
                ];
            }
            Log::error('WhatsApp Bulk API Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            return [
                'success' => false,
                'message' => 'Failed to send bulk message',
                'error' => $response->json()
            ];
        } catch (\Throwable $th) {
            Log::error('WhatsApp Bulk Service Exception', [
                'message' => $th->getMessage()
            ]);
            return [
                'success' => false,
                'message' => $th->getMessage()
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
            . "Kode verifikasi Anda adalah:\n"
            . "*{$code}*\n\n"
            . "Kode ini berlaku selama *10 menit*.\n"
            . "Jangan bagikan kode ini kepada siapapun.\n"
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
