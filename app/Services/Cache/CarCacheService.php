<?php

namespace App\Services\Cache;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Support\Facades\Cache;

class CarCacheService extends BaseCacheService
{
    public function rememberCarFilters(): array
    {
        return $this->remember('cars.filters', function () {
            $brands = Brand::where('is_active', true)->withCount(['cars' => function ($query) {
                $query->where('is_active', true)->where('is_highlighted', '!=', 'coming_soon');
            }])->get();
            $years = Car::where('is_highlighted', '!=', 'coming_soon')->distinct()->orderByDesc('year')->pluck('year');

            return compact('brands', 'years');
        }, self::TTL_LONG);
    }

    public function forgetCars(): void
    {
        Cache::forget('cars.filters');
    }
}
