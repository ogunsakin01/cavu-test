<?php

namespace App\Http\Services;

use App\Models\Booking;

class CreateBooking
{
    public string $start;
    public string $end;
    public string $parkingSpace;
    public $booking;

    public function __construct($start, $end, $parkingSpace){
        $this->start = $start;
        $this->end = $end;
        $this->parkingSpace = $parkingSpace;
    }

    public function handle(){
        try{
            $this->makeBooking();
            return [
                'message' => 'Booking created  successfully',
                'code' => 200,
                'data' => $this->booking->toArray(),
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

    public function makeBooking(){
        $this->booking = Booking::create([
            'user_id' => Auth()->id(),
            'start' => $this->start,
            'end' => $this->end,
            'parking_space' => $this->parkingSpace
        ]);
    }
}
