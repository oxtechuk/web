<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Models\Car;
use App\Models\Partner;
use App\Models\Testimonial;
use App\Services\Cache\AboutCacheService;

final class AboutApiService
{
    public function __construct(
        private readonly AboutCacheService $cache,
    ) {}

    public function about(): array
    {
        $testimonials = Testimonial::where('is_visible', true)->get();
        $partners = Partner::orderBy('sort_order')->get();

        $featuredDesigns = Car::where('is_active', true)
            ->where(function ($q) {
                $q->where('is_featured', true)->orWhereHas('offers');
            })
            ->with('brand')
            ->latest()
            ->take(5)
            ->get();

        $mainGallery = $this->cache->rememberMainGallery();

        return [
            'testimonials' => $testimonials->toArray(),
            'partners' => $partners->toArray(),
            'featured_designs' => $featuredDesigns->toArray(),
            'main_gallery' => $mainGallery,
        ];
    }
}
