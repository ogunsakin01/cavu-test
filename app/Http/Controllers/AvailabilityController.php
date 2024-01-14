<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\AvailabilityPricingRequest;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Services\CheckAvailability;
use App\Http\Services\GetAvailability;
use App\Http\Services\GetAvailabilityPricing;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    use ResponseHelper;

    public function availability(): JsonResponse
    {
        $response = (new GetAvailability())->handle();
        return $this->formattedResponse($response);
    }

    public function checkAvailability(CheckAvailabilityRequest $request): JsonResponse
    {
        $response = (new CheckAvailability($request->start, $request->end))->handle();
        return $this->formattedResponse($response);
    }

    public function availabilityPricing(AvailabilityPricingRequest $request): JsonResponse
    {
        $response = (new GetAvailabilityPricing($request->start, $request->end))->handle();
        return $this->formattedResponse($response);
    }

    public function getDailyAvailability(){

    }
}
