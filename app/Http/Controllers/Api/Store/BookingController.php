<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Api\Store\BookingRequest;
use App\Models\Car;
use App\Services\Api\Store\BookingApiService;
use App\Services\Api\Store\Data\BookingData;

final class BookingController extends ApiBaseController
{
    public function __construct(
        private readonly BookingApiService $bookingService,
    ) {
        parent::__construct(app(\App\Http\Api\Response\Builder\ApiResponseBuilder::class));
    }

    public function store(BookingRequest $request)
    {
        $car = Car::findOrFail($request->input('car_id'));

        $data = BookingData::fromRequest(
            $request->validated(),
            $car->cash_price,
        );

        $booking = $this->bookingService->create($data);

        return $this->respondCreated($booking->toArray(), 'Booking created successfully');
    }
}
