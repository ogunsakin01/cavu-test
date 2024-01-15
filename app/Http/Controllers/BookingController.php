<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\BookingRequest;
use App\Http\Services\CreateBooking;
use App\Http\Services\UpdateBooking;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    use ResponseHelper;

    public function getBookings(): JsonResponse
    {
        $bookings = Booking::with('user')->get();
        return $this->formattedResponse([
            'data' => $bookings->toArray(),
            'message' => 'Bookings'
        ]);
    }

    public function createBooking(BookingRequest $request): JsonResponse
    {
        $response = (new CreateBooking($request->start, $request->end, $request->parking_space))->handle();
        return $this->formattedResponse($response);
    }

    public function getBooking(Booking $booking): JsonResponse
    {
        $booking['user'] = $booking->user;
        return $this->formattedResponse([
            'data' => $booking->toArray(),
            'message' => 'Bookings'
        ]);
    }

    public function updateBooking(Booking $booking, BookingRequest $request){
        $response = (new UpdateBooking($request->start, $request->end, $booking, $request->parking_space))->handle();
        return $this->formattedResponse($response);
    }

    public function deleteBooking(Booking $booking){
        $booking->delete();
        return $this->formattedResponse([
            'data' => [],
            'message' => 'Booking deleted'
        ]);
    }
}
