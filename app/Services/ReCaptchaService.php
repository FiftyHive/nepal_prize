<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReCaptchaService
{
    private string $secretKey;
    private string $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key', '');
    }

    /**
     * Verify a reCAPTCHA v2 token server-side.
     *
     * Returns true if verification passes (or if keys are not configured,
     * to allow local development without CAPTCHA keys).
     */
    public function verify(?string $token, ?string $ip = null): bool
    {
        $token = (string) ($token ?? '');

        // Allow bypass in local/testing environment when keys not set
        if (empty($this->secretKey)) {
            Log::warning('ReCaptcha secret key not configured — bypassing in dev mode');
            return app()->environment('local', 'testing');
        }

        try {
            $response = Http::asForm()->post($this->verifyUrl, [
                'secret'   => $this->secretKey,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if (!$response->successful()) {
                Log::error('reCAPTCHA verification HTTP error', ['status' => $response->status()]);
                return false;
            }

            $data = $response->json();

            return (bool) ($data['success'] ?? false);
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA verification exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
