<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\Api\Store\CarApiService;
use App\Services\Api\Store\CompareApiService;
use Illuminate\Http\Request;

final class CarController extends ApiBaseController
{
    public function __construct(
        private readonly CarApiService $carService,
        private readonly CompareApiService $compareService,
    ) {
        parent::__construct(app(\App\Http\Api\Response\Builder\ApiResponseBuilder::class));
    }

    public function index(Request $request)
    {
        $filters = $request->only(['brands', 'type', 'year', 'min_price', 'max_price', 'search', 'q', 'offer_id', 'sort']);

        return $this->respondPaginated(
            $this->carService->list($filters, (int) $request->get('per_page', 12)),
            'Cars retrieved successfully'
        );
    }

    public function show(string $slug)
    {
        $result = $this->carService->findBySlug($slug);

        return $this->respondSuccess($result, 'Car retrieved successfully');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        return $this->respondSuccess(
            $this->carService->search($query),
            $query ? 'Search results' : 'All cars'
        );
    }

    public function compare(Request $request)
    {
        $slugs = array_filter(array_slice((array) $request->get('cars', []), 0, 2));

        if (count($slugs) < 2) {
            return $this->respondError('Please provide exactly 2 car slugs to compare', 422);
        }

        return $this->respondSuccess(
            $this->compareService->compare($slugs[0], $slugs[1]),
            'Comparison retrieved successfully'
        );
    }

    public function brands()
    {
        return $this->respondSuccess(
            $this->carService->brands(),
            'Brands retrieved successfully'
        );
    }
}
