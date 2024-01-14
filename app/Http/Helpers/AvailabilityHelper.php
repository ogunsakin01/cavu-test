<?php

namespace App\Http\Helpers;

use App\Models\Booking;

trait AvailabilityHelper
{
    public array $freePeriods = [];

    public function getParkingSpaceFreePeriods(){
        $parkingSpaceBookings = Booking::orderBy('start', 'asc')->get()->groupBy('parking_space');
        foreach(config('app.parking_spaces') as $parkingSpace){
            $bookings = $parkingSpaceBookings[$parkingSpace] ?? [];
            $parkingSpaceName = 'Parking Space '.$parkingSpace;
            if(count($bookings) < 1) $this->freePeriods[$parkingSpaceName] = $this->buildEmptyBookingFreePeriods();
            if(count($bookings) > 0) $this->freePeriods[$parkingSpaceName] = $this->findFreeDateRanges($bookings);
        }
    }

    private function findFreeDateRanges($bookings)
    {
        $freePeriods = [];
        foreach ($bookings as $key => $booking) {
            $startDate = strtotime($booking['start']);
            $endDate = strtotime($booking['end']);
            if ($key == 0 && (date("Y-m-d", $startDate) !== date("Y-01-01", $startDate))) {
                $start = date('Y-m-d', strtotime(date("Y-01-01", $startDate)));
                $end = date('Y-m-d', strtotime("-1 day", $startDate));
                $freePeriods[] = ['start' => $start, 'end' => $end];
            } elseif ($key != 0 && $key != (count($bookings) - 1)) {
                $start = date('Y-m-d', strtotime("+1 day", strtotime($bookings[$key - 1]['end'])) );
                $end = date('Y-m-d', strtotime("-1 day", strtotime($booking['start'])));
                $freePeriods[] = ['start' => $start, 'end' => $end];
                if($key == (count($bookings) - 2)){
                    $start = date('Y-m-d', strtotime("+1 day", strtotime($booking['end'])) );
                    $end = date('Y-m-d', strtotime("-1 day", strtotime($bookings[$key + 1]['start'])));
                    $freePeriods[] = ['start' => $start, 'end' => $end];
                }
            } elseif ($key == (count($bookings) - 1)) {
                $start = date('Y-m-d', strtotime("+1 day", $endDate));
                $end = date('Y-m-d', strtotime(date("Y-12-31", $endDate)));
                $freePeriods[] = ['start' => $start, 'end' => $end];
            }else{
                $start = date('Y-m-d', strtotime("+1 day", $endDate));
                $end = date('Y-m-d', strtotime($bookings[$key + 1]['start']));
                $freePeriods[] = ['start' => $start, 'end' => $end];
            }
        }
        $endDate = $bookings[count($bookings) - 1]['end'];
        $freePeriods[] = [
            'start' => date('Y-m-d', strtotime('+1 year', strtotime(date('Y-01-01', strtotime($endDate))))),
            'end' => date('Y-m-d', strtotime('+1 year', strtotime(date('Y-12-31', strtotime($endDate))))),
        ];
        return $freePeriods;
    }

    private function buildEmptyBookingFreePeriods()
    {
        return [
            [
                'start' => date('Y-01-01'),
                'end' => date('Y-12-31'),
            ],
            [
                'start' => date('Y-m-d', strtotime('+1 year', strtotime(date('Y-01-01')))),
                'end' => date('Y-m-d', strtotime('+1 year', strtotime(date('Y-12-31')))),
            ],
        ];
    }
}
