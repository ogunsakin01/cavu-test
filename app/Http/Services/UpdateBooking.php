<?php

namespace App\Http\Services;

use App\Http\Helpers\AvailabilityHelper;
use App\Models\Booking;

class UpdateBooking
{
    use AvailabilityHelper;

    public string $start;
    public string $end;
    public Booking $booking;

    public function __construct(string $start, string $end, Booking $booking, ?string $parkingSpace = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->parkingSpace = $parkingSpace;
        $this->booking = $booking;
    }

    public function handle(): array
    {
        try {
            $this->checkAvailability();
            $this->updateBooking();
            return [
                'message' => 'Booking Updated successfully',
                'code' => 200,
                'data' => $this->booking->refresh()->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'code' => in_array($e->getCode(), [500, 422]) ? $e->getCode() : 400,
                'data' => ['errors' => ($e->getCode() == 500) ? $e->getTrace() : []]
            ];
        }
    }

    private function updateBooking(): void
    {
        $this->booking->update([
                'start' => $this->start,
                'end' => $this->end,
                'parking_space' => $this->parkingSpace
            ]);
        $this->booking['user'] = $this->booking->user;
    }
}
