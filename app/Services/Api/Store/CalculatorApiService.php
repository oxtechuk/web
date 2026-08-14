<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Models\CalculatorLead;
use App\Services\TwilioOtpService;

final class CalculatorApiService
{
    public function __construct(
        private readonly TwilioOtpService $otpService,
    ) {}

    public function saveLead(array $data): CalculatorLead
    {
        return CalculatorLead::create($data);
    }

    public function sendOtp(string $phone): array
    {
        $result = $this->otpService->sendOtp($phone);

        return [
            'success' => $result['success'] ?? false,
            'message' => $result['message'] ?? 'OTP sent',
        ];
    }

    public function verifyOtp(string $phone, string $code): bool
    {
        $result = $this->otpService->verifyOtp($phone, $code);

        return $result['success'] ?? false;
    }

    public function createLeadFromVerified(string $name, string $phone): CalculatorLead
    {
        return CalculatorLead::create([
            'name' => $name,
            'phone' => $phone,
            'details' => [
                'page' => 'calculator_page',
                'otp_verified_at' => now()->toISOString(),
            ],
        ]);
    }
}
