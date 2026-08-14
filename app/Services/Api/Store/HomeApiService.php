<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Services\Cache\HomeCacheService;
use Illuminate\Support\Facades\Storage;

final class HomeApiService
{
    public function __construct(
        private readonly HomeCacheService $cache,
    ) {}

    public function home(): array
    {
        $data = $this->cache->rememberHomeData();

        $heroSetting = $this->cache->rememberSetting('store_home_hero');
        $hero = $heroSetting ? [
            'title' => $heroSetting['title'] ?? '',
            'subtitle' => $heroSetting['subtitle'] ?? '',
            'image' => $this->resolveImage($heroSetting['image'] ?? null),
        ] : [
            'title' => __('store-api.home.hero.title', [], 'ar'),
            'subtitle' => __('store-api.home.hero.subtitle', [], 'ar'),
            'image' => null,
        ];

        $heroSlides = array_map(function (array $slide): array {
            $slide['image'] = $this->resolveImage($slide['image'] ?? null);
            $locale = app()->getLocale();
            $slide['link'] = ($locale === 'en' && !empty($slide['link_en']))
                ? $slide['link_en']
                : ($slide['link_ar'] ?? $slide['link'] ?? '');
            $slide['button_text'] = ($locale === 'en' && !empty($slide['button_text_en']))
                ? $slide['button_text_en']
                : ($slide['button_text_ar'] ?? $slide['button_text'] ?? __('اكتشف السيارات'));

            return $slide;
        }, $data['heroSlides'] ?? []);

        return [
            'featured_cars' => ($data['featuredCars'] ?? collect())->values(),
            'active_offers' => ($data['activeOffers'] ?? collect())->values(),
            'brands' => ($data['brands'] ?? collect())->values(),
            'latest_posts' => ($data['latestPosts'] ?? collect())->values(),
            'stats' => $data['stats'] ?? [],
            'testimonials' => ($data['testimonials'] ?? collect())->values(),
            'partners' => ($data['partners'] ?? collect())->values(),
            'hero' => $hero,
            'featured_design' => $data['featuredDesign'],
            'social_designs' => ($data['socialDesigns'] ?? collect())->values(),
            'filter_brands' => ($data['filterBrands'] ?? collect())->values(),
            'filter_categories' => ($data['filterCategories'] ?? collect())->values(),
            'filter_years' => ($data['filterYears'] ?? collect())->values(),
            'bento_cars' => ($data['bentoCars'] ?? collect())->values(),
            'featured_offers' => ($data['featuredOffers'] ?? collect())->values(),
            'highlighted_cars' => ($data['highlightedCars'] ?? collect())->values(),
            'hero_slides' => $heroSlides,
        ];
    }

    private function resolveImage(?string $path): ?string
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
