<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Models\Brand;
use App\Models\Car;
use App\Services\Api\Store\Helpers\SlugResolver;
use App\Services\Cache\CarCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class CarApiService
{
    public function __construct(
        private readonly CarCacheService $cache,
    ) {}

    public function list(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Car::with(['brand', 'activeOffers'])->where('is_active', true);

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort'] ?? 'latest');

        return $query->paginate($perPage)->withQueryString();
    }

    public function findBySlug(string $slug): array
    {
        $query = Car::with([
            'brand',
            'images',
            'specifications',
            'features_list',
            'offers' => fn ($q) => $q->active(),
        ])
            ->where('is_active', true);

        SlugResolver::applyCarSlug($query, $slug);

        $car = $query->first();

        if (! $car) {
            throw new ModelNotFoundException('Car not found');
        }

        $car->increment('views');

        $relatedCars = Car::with(['brand', 'activeOffers'])
            ->where('brand_id', $car->brand_id)
            ->where('id', '!=', $car->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return [
            'car' => $car->toArray(),
            'related_cars' => $relatedCars->toArray(),
        ];
    }

    public function search(string $query): array
    {
        return Car::with('brand')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name->ar', 'LIKE', "%{$query}%")
                    ->orWhere('name->en', 'LIKE', "%{$query}%")
                    ->orWhereHas('brand', fn ($bq) => $bq->where('name', 'LIKE', "%{$query}%"))
                    ->orWhere('model', 'LIKE', "%{$query}%")
                    ->orWhere('year', 'LIKE', "%{$query}%");
            })
            ->orderByDesc('is_featured')
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function brands(): array
    {
        return Brand::where('is_active', true)
            ->withCount('cars')
            ->get()
            ->toArray();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['brands']) && is_array($filters['brands'])) {
            $query->whereIn('brand_id', $filters['brands']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (! empty($filters['min_price'])) {
            $query->where('cash_price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('cash_price', '<=', $filters['max_price']);
        }

        $search = $filters['search'] ?? $filters['q'] ?? null;

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['offer_id'])) {
            $query->whereHas('offers', fn ($q) => $q->where('offers.id', $filters['offer_id']));
        }
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('cash_price'),
            'price_desc' => $query->orderByDesc('cash_price'),
            'year_desc' => $query->orderByDesc('year'),
            default => $query->latest('id'),
        };
    }
}
