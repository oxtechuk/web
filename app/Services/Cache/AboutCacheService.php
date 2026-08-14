<?php

namespace App\Services\Cache;

class AboutCacheService extends BaseCacheService
{
    public function rememberMainGallery(): array
    {
        $settings = $this->rememberSettings();

        if (! isset($settings['main_gallery'])) {
            return [];
        }

        return is_array($settings['main_gallery'])
            ? $settings['main_gallery']
            : (json_decode($settings['main_gallery'], true) ?: []);
    }
}
