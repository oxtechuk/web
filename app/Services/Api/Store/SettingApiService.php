<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Services\Cache\BaseCacheService;
use Illuminate\Support\Facades\Storage;

final class SettingApiService
{
    private const SECRET_KEYS = [
        'twilio_sid',
        'twilio_auth_token',
        'twilio_whatsapp_number',
        'twilio_sms_number',
        'whatsapp_template_new_lead',
        'whatsapp_template_status_update',
        'gemini_api_key',
    ];

    private const IMAGE_KEYS = [
        'site_logo', 'site_favicon', 'breadcrumb_bg', 'hero_video',
        'hero_ad_1_image', 'hero_ad_2_image', 'page_loader_image',
        'store_img_hero_mask', 'store_img_hero_card_1', 'store_img_hero_card_3',
        'store_img_business_1', 'store_img_offer_banner_left',
    ];

    private const ARRAY_IMAGE_KEYS = [
        'main_gallery',
    ];

    private const NESTED_IMAGE_KEYS = [
        'hero_slides',
        'promo_popup',
    ];

    public function __construct(
        private readonly BaseCacheService $cache,
    ) {}

    public function list(?array $keys = null): array
    {
        $settings = $this->cache->rememberSettings();

        if ($keys) {
            $keys = array_map(fn ($key) => trim((string) $key), $keys);
            $result = [];

            foreach ($keys as $key) {
                if ($settings->has($key)) {
                    $result[$key] = $settings->get($key);
                }
            }

            return $this->resolveImages($this->stripSecrets($result));
        }

        return $this->resolveImages($this->stripSecrets($settings->toArray()));
    }

    private function stripSecrets(array $data): array
    {
        foreach (self::SECRET_KEYS as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    private function resolveImages(array $data): array
    {
        foreach (self::IMAGE_KEYS as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $data[$key] = $this->resolveUrl($data[$key]);
            }
        }

        foreach (self::ARRAY_IMAGE_KEYS as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                $data[$key] = array_map(fn ($path) => $this->resolveUrl($path), $data[$key]);
            }
        }

        foreach (self::NESTED_IMAGE_KEYS as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                $data[$key] = $this->resolveNestedImages($data[$key]);
            }
        }

        return $data;
    }

    private function resolveNestedImages(array $items): array
    {
        // promo_popup is a single object
        if (array_is_list($items)) {
            return array_map(fn (array $item) => $this->resolveItemImage($item), $items);
        }

        return $this->resolveItemImage($items);
    }

    private function resolveItemImage(array $item): array
    {
        if (isset($item['image']) && is_string($item['image'])) {
            $item['image'] = $this->resolveUrl($item['image']);
        }

        return $item;
    }

    private function resolveUrl(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
