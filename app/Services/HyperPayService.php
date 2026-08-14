<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HyperPayService
{
    // =====================================================
    // HyperPay Result Code Patterns
    // =====================================================

    /**
     * Result codes من HyperPay التي تعتبر "ناجحة"
     * المصدر: https://hyperpay.docs.oppwa.com/reference/resultCodes
     */
    const SUCCESS_CODES = [
        '/^000\.000\./',           // نجاح كامل
        '/^000\.100\.1/',          // نجاح Test Mode
        '/^000\.396\.0/',          // نجاح مع رسالة إضافية
        '/^000\.400\.0[^3]/',      // نجاح مع 3DS
        '/^000\.400\.100/',        // نجاح مع 3DS
    ];

    /**
     * Result codes التي تعتبر "معلقة" (Pending - تحتاج Webhook)
     */
    const PENDING_CODES = [
        '/^000\.200\./',           // معاملة معلقة
        '/^800\.400\.5/',          // انتظار External
    ];

    /**
     * رسائل خطأ مخصصة للعميل حسب كود الخطأ
     */
    const ERROR_MESSAGES = [
        '800.100.151' => 'تم رفض البطاقة. تحقق من بيانات البطاقة وحاول مرة أخرى.',
        '800.100.153' => 'البطاقة منتهية الصلاحية. يرجى استخدام بطاقة أخرى.',
        '800.100.155' => 'الرصيد غير كافٍ. يرجى استخدام بطاقة أخرى.',
        '800.100.157' => 'رقم CVV غير صحيح. تحقق من الرقم الموجود خلف بطاقتك.',
        '800.100.162' => 'تم تجاوز حد الاستخدام اليومي للبطاقة.',
        '800.100.163' => 'مبلغ المعاملة تجاوز الحد المسموح به.',
        '800.100.170' => 'البطاقة مقيدة. تواصل مع البنك.',
        '800.300.101' => 'تعذر الاتصال ببوابة الدفع. حاول لاحقاً.',
        '800.300.401' => 'خطأ في إعداد بوابة الدفع. تواصل مع الدعم.',
        '100.396.104' => 'هذه المعاملة تمت معالجتها مسبقاً.',
        'default'     => 'حدث خطأ أثناء معالجة الدفع. حاول مرة أخرى أو تواصل معنا.',
    ];

    // =====================================================
    // API URLs
    // =====================================================

    const TEST_URL = 'https://eu-test.oppwa.com/v1/payments';
    const LIVE_URL = 'https://eu-prod.oppwa.com/v1/payments';

    // =====================================================
    // Private Helpers
    // =====================================================

    private function getSettings(): array
    {
        $keys = [
            'hyperpay_mode',
            'hyperpay_test_entity_id',
            'hyperpay_test_access_token',
            'hyperpay_live_entity_id',
            'hyperpay_live_access_token',
            'hyperpay_currency',
        ];

        return Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
    }

    private function isTestMode(): bool
    {
        $settings = $this->getSettings();
        return ($settings['hyperpay_mode'] ?? 'test') === 'test';
    }

    private function getApiUrl(): string
    {
        return $this->isTestMode() ? self::TEST_URL : self::LIVE_URL;
    }

    private function getEntityId(): string
    {
        $settings = $this->getSettings();
        if ($this->isTestMode()) {
            return $settings['hyperpay_test_entity_id'] ?? '';
        }
        return $settings['hyperpay_live_entity_id'] ?? '';
    }

    private function getAccessToken(): string
    {
        $settings = $this->getSettings();
        if ($this->isTestMode()) {
            return $settings['hyperpay_test_access_token'] ?? '';
        }
        return $settings['hyperpay_live_access_token'] ?? '';
    }

    private function getCurrency(): string
    {
        $settings = $this->getSettings();
        return $settings['hyperpay_currency'] ?? 'SAR';
    }

    // =====================================================
    // Public Methods
    // =====================================================

    /**
     * توليد مفتاح Idempotency فريد لمنع الدفع المزدوج
     */
    public function generateIdempotencyKey(): string
    {
        return (string) Str::uuid();
    }

    /**
     * التحقق من صحة إعدادات HyperPay
     */
    public function isConfigured(): bool
    {
        $entityId     = $this->getEntityId();
        $accessToken  = $this->getAccessToken();

        return !empty($entityId) && !empty($accessToken);
    }

    /**
     * تحليل result.code من HyperPay
     * @return string: 'success' | 'pending' | 'failed'
     */
    public function parseResultCode(string $code): string
    {
        foreach (self::SUCCESS_CODES as $pattern) {
            if (preg_match($pattern, $code)) {
                return 'success';
            }
        }

        foreach (self::PENDING_CODES as $pattern) {
            if (preg_match($pattern, $code)) {
                return 'pending';
            }
        }

        return 'failed';
    }

    /**
     * الحصول على رسالة خطأ مفهومة للعميل بناءً على كود النتيجة
     */
    public function getErrorMessage(string $code): string
    {
        return self::ERROR_MESSAGES[$code] ?? self::ERROR_MESSAGES['default'];
    }

    /**
     * إرسال طلب الدفع إلى HyperPay (Server-to-Server)
     *
     * @param array $paymentData بيانات العميل والبطاقة
     * @param string $idempotencyKey مفتاح فريد لمنع التكرار
     * @return array ['status' => 'success|pending|failed', 'code' => '', 'description' => '', 'transaction_id' => '']
     */
    public function initiatePayment(array $paymentData, string $idempotencyKey): array
    {
        if (!$this->isConfigured()) {
            Log::error('HyperPay: بوابة الدفع غير مهيأة - تحقق من الإعدادات');
            return [
                'status'         => 'failed',
                'code'           => '000.000.000',
                'description'    => 'بوابة الدفع غير مهيأة. تواصل مع الدعم.',
                'transaction_id' => null,
            ];
        }

        $url      = $this->getApiUrl();
        $entityId = $this->getEntityId();
        $token    = $this->getAccessToken();
        $currency = $this->getCurrency();

        // بناء بيانات الطلب
        $postData = http_build_query([
            'entityId'            => $entityId,
            'amount'              => number_format((float) $paymentData['amount'], 2, '.', ''),
            'currency'            => $currency,
            'paymentType'         => 'DB', // Debit (خصم فوري)
            'paymentBrand'        => $paymentData['card_brand'] ?? 'VISA', // VISA, MASTER

            // بيانات البطاقة
            'card.number'         => $paymentData['card_number'],
            'card.holder'         => $paymentData['card_holder'],
            'card.expiryMonth'    => $paymentData['card_expiry_month'],
            'card.expiryYear'     => $paymentData['card_expiry_year'],
            'card.cvv'            => $paymentData['card_cvv'],

            // بيانات العميل
            'customer.email'      => $paymentData['customer_email'] ?? '',
            'customer.givenName'  => $paymentData['customer_name'] ?? '',
            'customer.mobile'     => $paymentData['customer_phone'] ?? '',

            // وصف العملية
            'merchantTransactionId' => $idempotencyKey,
            'descriptor'            => 'رسوم حجز سيارة',
        ]);

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $token,
                    'Content-Type: application/x-www-form-urlencoded',
                ],
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $postData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30, // 30 ثانية timeout
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $response    = curl_exec($ch);
            $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError   = curl_error($ch);
            curl_close($ch);

            // خطأ في cURL (انقطاع اتصال، timeout...)
            if ($curlError) {
                Log::error('HyperPay cURL Error: ' . $curlError, [
                    'idempotency_key' => $idempotencyKey,
                    'http_code'       => $httpCode,
                ]);
                return [
                    'status'         => 'failed',
                    'code'           => 'CURL_ERROR',
                    'description'    => 'تعذر الاتصال ببوابة الدفع. تحقق من اتصالك بالإنترنت وحاول مرة أخرى.',
                    'transaction_id' => null,
                ];
            }

            // تحليل الرد
            $result = json_decode($response, true);

            if (!$result || !isset($result['result']['code'])) {
                Log::error('HyperPay: رد غير متوقع من الخادم', [
                    'response'        => $response,
                    'http_code'       => $httpCode,
                    'idempotency_key' => $idempotencyKey,
                ]);
                return [
                    'status'         => 'failed',
                    'code'           => 'INVALID_RESPONSE',
                    'description'    => 'رد غير متوقع من بوابة الدفع. حاول مرة أخرى.',
                    'transaction_id' => null,
                ];
            }

            $resultCode        = $result['result']['code'];
            $resultDescription = $result['result']['description'] ?? '';
            $transactionId     = $result['id'] ?? null;
            $parsedStatus      = $this->parseResultCode($resultCode);

            Log::info('HyperPay Response', [
                'code'            => $resultCode,
                'status'          => $parsedStatus,
                'transaction_id'  => $transactionId,
                'idempotency_key' => $idempotencyKey,
            ]);

            return [
                'status'         => $parsedStatus,
                'code'           => $resultCode,
                'description'    => $parsedStatus === 'failed'
                    ? $this->getErrorMessage($resultCode)
                    : $resultDescription,
                'transaction_id' => $transactionId,
                'raw'            => $result, // للـ debugging
            ];

        } catch (\Exception $e) {
            Log::error('HyperPay Exception: ' . $e->getMessage(), [
                'trace'           => $e->getTraceAsString(),
                'idempotency_key' => $idempotencyKey,
            ]);

            return [
                'status'         => 'failed',
                'code'           => 'EXCEPTION',
                'description'    => 'حدث خطأ غير متوقع. حاول مرة أخرى أو تواصل معنا.',
                'transaction_id' => null,
            ];
        }
    }

    /**
     * اختبار الاتصال ببوابة الدفع (بدون خصم فعلي)
     * يُستخدم من صفحة الإعدادات
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'أدخل بيانات API أولاً'];
        }

        // نرسل طلباً بمبلغ صغير مع بيانات بطاقة اختبار
        $testData = [
            'amount'             => '1.00',
            'card_brand'         => 'VISA',
            'card_number'        => '4200000000000000',
            'card_holder'        => 'Test User',
            'card_expiry_month'  => '05',
            'card_expiry_year'   => '2030',
            'card_cvv'           => '123',
            'customer_email'     => 'test@test.com',
            'customer_name'      => 'Test',
            'customer_phone'     => '0500000000',
        ];

        $result = $this->initiatePayment($testData, 'test-connection-' . time());

        if (in_array($result['status'], ['success', 'pending'])) {
            return ['success' => true, 'message' => 'الاتصال بـ HyperPay يعمل بشكل صحيح ✓'];
        }

        return [
            'success' => false,
            'message' => 'فشل الاتصال: ' . ($result['code'] ?? 'خطأ غير معروف'),
        ];
    }
}
