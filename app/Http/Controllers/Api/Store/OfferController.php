<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Api\ApiBaseController;
use App\Services\Api\Store\OfferApiService;
use Illuminate\Http\Request;

final class OfferController extends ApiBaseController
{
    public function __construct(
        private readonly OfferApiService $offerService,
    ) {
        parent::__construct(app(\App\Http\Api\Response\Builder\ApiResponseBuilder::class));
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 12);

        return $this->respondPaginated(
            $this->offerService->list($perPage),
            'Offers retrieved successfully'
        );
    }
}
