<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Models\Offer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class OfferApiService
{
    public function list(int $perPage = 12): LengthAwarePaginator
    {
        return Offer::active()
            ->with(['cars.brand'])
            ->latest()
            ->paginate($perPage);
    }
}
