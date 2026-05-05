<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Send a WhatsApp message via Fonnte
     * 
     * @param string $target Phone number (e.g. 08123456789 or 628123456789)
     * @param string $message The message to send
     * @return array
     */
    public function sendMessage(string $target, string $message): array
    {
        Log::info("Attempting to send WA to {$target}");
        
        if (empty($this->token)) {
            Log::error('Fonnte API Token is not configured.');
            return [
                'status' => false,
                'message' => 'Fonnte API Token is not configured.',
            ];
        }

        // Normalize phone number
        $target = preg_replace('/[^0-9]/', '', $target); // Remove non-numeric
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        } elseif (!str_starts_with($target, '62')) {
            $target = '62' . $target;
        }

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->baseUrl, [
                'target' => $target,
                'message' => $message,
                // Removed countryCode because we normalize target to include it
            ]);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Fonnte API Error: ' . $response->body());
            return [
                'status' => false,
                'message' => $response->json('reason') ?? 'Unknown error from Fonnte.',
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte Service Exception: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
