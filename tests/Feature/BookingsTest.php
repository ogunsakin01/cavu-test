<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookingsTest extends TestCase
{
    private $testUser;
    private string $token;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->testUser = User::create([
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => Hash::make('password')
        ]);
        $this->token = $this->testUser->createToken('TestToken')->plainTextToken;
        Booking::insert([
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-03-01',
                'end' => '2024-03-14',
                'parking_space' => 'C'
            ],
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-04-01',
                'end' => '2024-04-14',
                'parking_space' => 'C'
            ],
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-05-01',
                'end' => '2024-05-14',
                'parking_space' => 'C'
            ]
        ]);
    }

    public function test_can_get_bookings()
    {
        $response = $this->get('/api/v1/booking', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $bookingsCount = Booking::all()->count();
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Bookings']);
        $this->assertCount($bookingsCount, $response['data']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('start', $response['data'][0]);
        $this->assertArrayHasKey('end', $response['data'][0]);
        $this->assertArrayHasKey('parking_space', $response['data'][0]);
        $this->assertArrayHasKey('user', $response['data'][0]);
    }

    public function test_can_create_booking()
    {
        $response = $this->post('/api/v1/booking', [
            'start' => '2024-03-01',
            'end' => '2024-03-14',
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('start', $response['data']);
        $this->assertArrayHasKey('end', $response['data']);
        $this->assertArrayHasKey('parking_space', $response['data']);
    }

    public function test_can_get_booking()
    {
        $booking = Booking::create([
                'user_id' => $this->testUser->id,
                'start' => '2024-03-01',
                'end' => '2024-03-14',
                'parking_space' => 'A'
            ]);
        $response = $this->get('/api/v1/booking/'.$booking->id, [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('start', $response['data']);
        $this->assertArrayHasKey('end', $response['data']);
        $this->assertArrayHasKey('parking_space', $response['data']);
        $this->assertEquals('2024-03-01', $response['data']['start']);
        $this->assertEquals('2024-03-14', $response['data']['end']);
    }

    public function test_can_not_find_deleted_booking()
    {
        $booking = Booking::create([
            'user_id' => $this->testUser->id,
            'start' => '2024-03-01',
            'end' => '2024-03-14',
            'parking_space' => 'F'
        ]);
        $bookingId = $booking->id;
        $booking->delete();
        $response = $this->get('/api/v1/booking/'.$bookingId,[
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(404);
    }

    public function test_can_update_booking()
    {
        $booking = Booking::create([
                'user_id' => $this->testUser->id,
                'start' => '2024-03-01',
                'end' => '2024-03-14',
                'parking_space' => 'E'
            ]);
        $response = $this->patch('/api/v1/booking/'.$booking->id, [
            'start' => '2024-03-07',
            'end' => '2024-03-14',
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('start', $response['data']);
        $this->assertArrayHasKey('end', $response['data']);
        $this->assertArrayHasKey('parking_space', $response['data']);
        $this->assertEquals('2024-03-07', $response['data']['start']);
        $this->assertEquals('2024-03-14', $response['data']['end']);
    }

    public function test_can_delete_booking()
    {
        $booking = Booking::create([
                'user_id' => $this->testUser->id,
                'start' => '2024-03-01',
                'end' => '2024-03-14',
                'parking_space' => 'F'
        ]);
        $response = $this->delete('/api/v1/booking/'.$booking->id, [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
    }

    public function test_can_not_delete_deleted_booking()
    {
        $booking = Booking::create([
            'user_id' => $this->testUser->id,
            'start' => '2024-03-01',
            'end' => '2024-03-14',
            'parking_space' => 'F'
        ]);
        $bookingId = $booking->id;
        $booking->delete();
        $response = $this->delete('/api/v1/booking/'.$bookingId, [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(404);
    }
}
