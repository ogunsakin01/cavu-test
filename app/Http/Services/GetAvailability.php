<?php

namespace App\Http\Services;

use App\Http\Helpers\AvailabilityHelper;

class GetAvailability
{
    use AvailabilityHelper;

    public array $dates = [];
    public array $freePeriods = [];

    public function handle(){
        try{
            $this->getParkingSpaceFreePeriods();
            return [
                'code' => 200,
                'message' => 'Free Periods',
                'data' => [
                    'free_periods' => $this->freePeriods
                ]
            ];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage(),
                'code' => in_array($e->getCode(), [500, 422]) ? $e->getCode() : 400,
                'data' => ['errors' => ($e->getCode() == 500) ? $e->getTrace() : []]
            ];
        }
    }
}
