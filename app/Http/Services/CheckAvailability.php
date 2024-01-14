<?php

namespace App\Http\Services;

use App\Http\Helpers\AvailabilityHelper;
use DateTime;

class CheckAvailability
{
    use AvailabilityHelper;

    public string $start;
    public string $end;
    public array $availableParkingSpaces = [];

    public function __construct($start, $end){
        $this->start = $start;
        $this->end = $end;
    }

    public function handle(){
        try{
            $this->getParkingSpaceFreePeriods();
            $this->getAvailableParkingSpaces();
            return [
                'code' => 200,
                'message' => 'Free Periods',
                'data' => [
                    'available_parking_space' => $this->availableParkingSpaces,
                    'total_available_spaces' => count($this->availableParkingSpaces),
                    'date_range' => [
                        'start' => $this->start,
                        'end' => $this->end
                    ]
                ]
            ];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage(),
                'code' => in_array($e->getCode(), [500, 422]) ? $e->getCode() : 400,
                'data' => [
                    'errors' => $e->getTrace()
                ]
            ];
        }
    }

    private function getAvailableParkingSpaces(){
        foreach($this->freePeriods as $parkingSpace => $availabilities){
            if($this->isDateRangeAvailable($availabilities)){
                $this->availableParkingSpaces[] = $parkingSpace;
            }
        }
    }

    private function isDateRangeAvailable($availabilityDates)
    {
        $inputStart = new DateTime($this->start);
        $inputEnd = new DateTime($this->end);

        foreach ($availabilityDates as $availability) {
            $availabilityStart = new DateTime($availability['start']);
            $availabilityEnd = new DateTime($availability['end']);

            if (
                ($inputStart >= $availabilityStart && $inputStart <= $availabilityEnd) &&
                ($inputEnd >= $availabilityStart && $inputEnd <= $availabilityEnd)
            ) {
                return true; // Overlapping date range found
            }
        }

        return false; // No overlapping date range found
    }
}
