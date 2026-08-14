<?php

namespace App\Services\Cache;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

 class BaseCacheService
{
    protected const TTL_DEFAULT = 3600;

    protected const TTL_LONG = 86400;

    protected function remember(string $key, callable $callback, int $ttl = self::TTL_DEFAULT): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function rememberSettings(): mixed
    {
        return $this->remember('settings.all', function () {
            return Setting::all()->pluck('value', 'key');
        }, self::TTL_LONG);
    }

    public function rememberSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->rememberSettings();

        return $settings[$key] ?? $default;
    }

    public function rememberHeroSetting(string $key): array
    {
        return $this->remember("settings.hero.{$key}", function () use ($key) {
            $heroSetting = Setting::where('key', $key)->first();

            return $heroSetting ? $heroSetting->value : [
                'title' => '',
                'subtitle' => '',
                'image' => null,
            ];
        }, self::TTL_LONG);
    }

    public function rememberHeroSlides(): array
    {
        return $this->remember('settings.hero_slides', function () {
            $setting = Setting::where('key', 'hero_slides')->first();

            if (! $setting || empty($setting->value)) {
                return [];
            }

            return is_array($setting->value)
                ? $setting->value
                : (json_decode($setting->value, true) ?: []);
        }, self::TTL_LONG);
    }

    public function forgetSettings(): void
    {
        Cache::forget('settings.all');
    }

    public function flushAll(): void
    {
        Cache::flush();
    }
}
