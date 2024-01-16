<?php

namespace App\Http\Helpers;

use App\Http\Services\CheckAvailability;
use App\Models\Booking;

trait AvailabilityHelper
{
    public array $freePeriods = [];

    public array $parkingSpaceBookingsFreePeriods = [];

    public $availability;

    public $parkingSpace;

    public function getParkingSpaceFreePeriods(): void
    {
        $parkingSpaceBookings = Booking::orderBy('start', 'asc')->get()->groupBy('parking_space');
        foreach (config('app.parking_spaces') as $parkingSpace) {
            $bookings = $parkingSpaceBookings[$parkingSpace] ?? [];
            $parkingSpaceName = 'Parking Space ' . $parkingSpace;
            if (count($bookings) < 1) $this->freePeriods[$parkingSpaceName] = $this->buildEmptyBookingFreePeriods();
            if (count($bookings) > 0) $this->freePeriods[$parkingSpaceName] = $this->findFreeDateRanges($bookings);
        }
    }

    private function buildEmptyBookingFreePeriods(): array
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

    private function findFreeDateRanges($bookings): array
    {
        $this->parkingSpaceBookingsFreePeriods = [];
        $lastBookingEndDate = $bookings[count($bookings) - 1]['end'];
        foreach ($bookings as $key => $booking) {
            $startDate = strtotime($booking['start']);
            $endDate = strtotime($booking['end']);
            $nextStartDate = isset($bookings[$key + 1]) ? strtotime($bookings[$key + 1]['start']) : null;
            $lastEndDate = isset($bookings[$key - 1]) ? strtotime($bookings[$key - 1]['end']) : null;
            if ($key == 0 && (date("Y-m-d", $startDate) !== date("Y-01-01", $startDate))) $this->buildFreePeriodsBeforeFirstBooking($startDate);
            if ($key != 0 && $key != (count($bookings) - 1)) $this->buildFreePeriodsBeforeBooking($startDate, $lastEndDate);
            if (isset($bookings[$key + 1])) $this->buildFreePeriodsAfterBooking($endDate, $nextStartDate);
            if ($key == (count($bookings) - 1)) $this->buildLastBookingDateToEndingOfYearFreePeriods($endDate);
        }
        $this->buildNextYearFreePeriods($lastBookingEndDate);
        return array_values(array_unique($this->parkingSpaceBookingsFreePeriods, SORT_REGULAR));
    }

    private function buildFreePeriodsBeforeFirstBooking($startDate): void
    {
        $start = date('Y-m-d', strtotime(date("Y-01-01", $startDate)));
        $end = date('Y-m-d', strtotime("-1 day", $startDate));
        $this->parkingSpaceBookingsFreePeriods[] = ['start' => $start, 'end' => $end];
    }

    private function buildFreePeriodsBeforeBooking($startDate, $lastEndDate): void
    {
        $start = strtotime("+1 day", $lastEndDate);
        $end = strtotime("-1 day", $startDate);
        if($start > $end) return;
        $this->parkingSpaceBookingsFreePeriods[] = ['start' => date('Y-m-d', $start), 'end' => date('Y-m-d', $end)];
    }

    private function buildFreePeriodsAfterBooking($endDate, $nextStartDate): void
    {
        $start = strtotime("+1 day", $endDate);
        $end = strtotime("-1 day", $nextStartDate);
        if($start > $end) return;
        $this->parkingSpaceBookingsFreePeriods[] = ['start' => date('Y-m-d', $start), 'end' => date('Y-m-d', $end)];
    }

    private function buildLastBookingDateToEndingOfYearFreePeriods($endDate): void
    {
        $start = date('Y-m-d', strtotime("+1 day", $endDate));
        $end = date('Y-m-d', strtotime(date("Y-12-31", $endDate)));
        $this->parkingSpaceBookingsFreePeriods[] = ['start' => $start, 'end' => $end];
    }

    private function buildNextYearFreePeriods($lastBookingEndDate): void
    {
        $this->parkingSpaceBookingsFreePeriods[] = [
            'start' => date('Y-m-d', strtotime('+1 year', strtotime(date('Y-01-01', strtotime($lastBookingEndDate))))),
            'end' => date('Y-m-d', strtotime('+1 year', strtotime(date('Y-12-31', strtotime($lastBookingEndDate))))),
        ];
    }

    private function checkAvailability(): void
    {
        $this->availability = (new CheckAvailability($this->start, $this->end))->handle();
        if ($this->availability['data']['total_available_spaces'] < 1) abort(422, 'No parking space available for this dates');
        if (is_null($this->parkingSpace)) {
            $availableSpaces = $this->availability['data']['available_parking_space'];
            $originalString = $availableSpaces[array_rand($availableSpaces)];
            $this->parkingSpace = str_replace('Parking Space ', '', $originalString);
        }
    }
}
