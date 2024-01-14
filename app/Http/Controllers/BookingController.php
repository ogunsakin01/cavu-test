<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\AvailabilityPricingRequest;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\CreateBookingRequest;
use App\Http\Services\CreateBooking;
use App\Http\Services\GetAvailability;
use App\Http\Services\GetAvailabilityPricing;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    use ResponseHelper;



    public function createBooking(CreateBookingRequest $request): JsonResponse
    {
        $response = (new CreateBooking($request->start, $request->end, $request->parking_space))->handle();
        return $this->formattedResponse($response);
    }

    public function cancelBooking(){

    }

    public function updateBooking(){

    }

    public function deleteBooking(){

    }
}
