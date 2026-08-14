<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    /**
     * Verify the submitted Google reCAPTCHA token against Google's siteverify endpoint.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.recaptcha.secret_key');

        if (! $secretKey) {
            // Captcha isn't configured yet - fail open so the site keeps working
            // until the keys are added, but log it so it doesn't go unnoticed.
            Log::warning('reCAPTCHA secret key is not configured; skipping verification.');

            return;
        }

        if (! $value) {
            $fail(__('يرجى تأكيد أنك لست روبوتاً.'));

            return;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $json = $response->json();

            if (! $response->successful() || empty($json['success'])) {
                $fail(__('يرجى تأكيد أنك لست روبوتاً.'));
                return;
            }

            // If v3 score is returned, check safety threshold
            if (isset($json['score']) && $json['score'] < 0.3) {
                Log::warning('reCAPTCHA v3 low score: ' . $json['score']);
                $fail(__('فشل التحقق من الأمان، يرجى المحاولة مرة أخرى.'));
            }
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA verification failed: '.$e->getMessage());
            $fail(__('تعذر التحقق من الكابتشا، حاول مرة أخرى.'));
        }
    }
}
