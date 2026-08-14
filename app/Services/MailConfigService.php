<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class MailConfigService
{
    /**
     * Load SMTP settings from the DB and apply them to Laravel's mail config at runtime.
     * Call this before sending any email so the correct SMTP is used.
     */
    public function applyDynamicMailConfig(): void
    {
        $settings = Setting::whereIn('key', [
            'mail_driver', 'mail_host', 'mail_port', 'mail_username',
            'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name',
        ])->pluck('value', 'key');

        // If no mail settings stored, just use the existing .env values
        if ($settings->isEmpty()) {
            return;
        }

        $driver     = $this->clean($settings['mail_driver'] ?? 'smtp');
        $host       = $this->clean($settings['mail_host'] ?? '');
        $port       = (int) $this->clean($settings['mail_port'] ?? '587');
        $username   = $this->clean($settings['mail_username'] ?? '');
        $password   = $this->clean($settings['mail_password'] ?? '');
        $encryption = $this->clean($settings['mail_encryption'] ?? 'tls');
        $fromAddr   = $this->clean($settings['mail_from_address'] ?? config('mail.from.address'));
        $fromName   = $this->clean($settings['mail_from_name'] ?? config('mail.from.name', 'GR Motors'));

        // Only apply if we have real values
        if (empty($host) || empty($username)) {
            return;
        }

        Config::set('mail.default', $driver);
        Config::set("mail.mailers.{$driver}", [
            'transport'  => $driver,
            'host'       => $host,
            'port'       => $port,
            'username'   => $username,
            'password'   => $password,
            'encryption' => $encryption === 'none' ? null : $encryption,
            'timeout'    => null,
        ]);
        Config::set('mail.from.address', $fromAddr);
        Config::set('mail.from.name', $fromName);
    }

    /**
     * Test the stored SMTP config by sending a test email.
     */
    public function sendTestEmail(string $toAddress): array
    {
        $this->applyDynamicMailConfig();

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "✅ اتصال SMTP يعمل بنجاح!\n\nتم إرسال هذا الإيميل الاختباري من GR Motors لتأكيد إعدادات البريد.",
                function ($message) use ($toAddress) {
                    $message->to($toAddress)
                        ->subject('✅ اختبار إعدادات البريد — GR Motors');
                }
            );

            return ['success' => true, 'message' => 'تم إرسال إيميل الاختبار بنجاح إلى ' . $toAddress];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'فشل الإرسال: ' . $e->getMessage()];
        }
    }

    /**
     * Clean value — handle JSON-decoded arrays or plain strings.
     */
    private function clean(mixed $value): string
    {
        if (is_array($value)) {
            return (string) ($value['value'] ?? array_values($value)[0] ?? '');
        }
        return (string) $value;
    }
}
