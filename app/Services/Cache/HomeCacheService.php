<?php

namespace App\Services\Cache;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\Offer;
use App\Models\Partner;
use App\Models\ProjectDesign;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

class HomeCacheService extends BaseCacheService
{
    public function rememberHomeData(): array
    {
        return $this->remember('home.data', function () {
            $featuredCars = Car::with('brand')
                ->where('is_featured', true)
                ->where('is_active', true)
                ->where('is_highlighted', '!=', 'coming_soon')
                ->latest()
                ->limit(8)
                ->get();

            $activeOffers = Offer::active()
                ->with('cars.brand')
                ->limit(4)
                ->get();

            $brands = Brand::where('is_active', true)
                ->withCount('cars')
                ->get();

            $latestPosts = BlogPost::published()
                ->latest('published_at')
                ->limit(3)
                ->get();

            $stats = [
                'cars' => Car::where('is_active', true)->where('is_highlighted', '!=', 'coming_soon')->count(),
                'brands' => Brand::where('is_active', true)->count(),
            ];

            $testimonials = Testimonial::where('is_visible', true)->get();
            $partners = Partner::orderBy('sort_order')->get();

            $featuredDesign = ProjectDesign::where('is_featured', true)->first();
            $otherDesigns = ProjectDesign::where(function ($query) use ($featuredDesign) {
                if ($featuredDesign) {
                    $query->where('id', '!=', $featuredDesign->id);
                }
            })->orderBy('sort_order')->take(4)->get();

            $filterBrands = Brand::whereHas('cars')->orderBy('name')->get();
            $filterCategories = CarCategory::orderBy('name')->get();
            $filterYears = Car::where('is_active', true)->where('is_highlighted', '!=', 'coming_soon')->distinct()->pluck('year')->sortDesc();

            $bentoCars = Car::with('brand')
                ->where('is_active', true)
                ->where('is_highlighted', '!=', 'coming_soon')
                ->latest()
                ->take(5)
                ->get();

            $featuredOffers = ProjectDesign::where('type', 'featured_offer')
                ->orderBy('sort_order')
                ->take(3)
                ->get();

            $socialDesigns = ProjectDesign::where('type', 'social')
                ->orderBy('sort_order')
                ->take(4)
                ->get();

            $highlightedCars = Car::with('brand')
                ->where('is_highlighted', '!=', 'none')
                ->where('is_highlighted', '!=', 'coming_soon')
                ->where('is_active', true)
                ->latest()
                ->get();

            $heroSlides = $this->rememberHeroSlides();

            return compact(
                'featuredCars', 'activeOffers', 'brands', 'latestPosts', 'stats',
                'testimonials', 'partners', 'featuredDesign', 'socialDesigns',
                'filterBrands', 'filterCategories', 'filterYears', 'bentoCars',
                'featuredOffers', 'highlightedCars', 'heroSlides',
            );
        });
    }

    public function forgetHome(): void
    {
        Cache::forget('home.data');
    }
}
