<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    private $testUser;
    private string $token;
    private array $defaultParkingSpacesAvailabilityDates = [];

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->testUser = User::create([
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => Hash::make('password')
        ]);
        $this->token = $this->testUser->createToken('TestToken')->plainTextToken;
        $this->defaultParkingSpacesAvailabilityDates = [
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

    public function test_can_get_all_availability_of_parking_spaces_when_with_no_bookings()
    {
        $response = $this->get('/api/v1/availability', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment(['Parking Space A' => $this->defaultParkingSpacesAvailabilityDates])
            ->assertJsonFragment(['Parking Space B' => $this->defaultParkingSpacesAvailabilityDates]);
    }

    public function test_can_get_availability_of_parking_spaces_when_with_one_booking()
    {
        Booking::insert([
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-03-01',
                'end' => '2024-03-14',
                'parking_space' => 'A'
            ]
        ]);
        $response = $this->get('/api/v1/availability', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['Parking Space A' => [
            ['start' => '2024-01-01', 'end' => '2024-02-29'],
            ['start' => '2024-03-15','end' => '2024-12-31'],
            ['start' => '2025-01-01', 'end' => '2025-12-31']
        ]]);
    }

    public function test_can_get_availability_of_parking_spaces_when_with_two_bookings()
    {
        Booking::insert([
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-03-01',
                'end' => '2024-03-14',
                'parking_space' => 'B'
            ],
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-04-01',
                'end' => '2024-04-14',
                'parking_space' => 'B'
            ]
        ]);
        $response = $this->get('/api/v1/availability', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['Parking Space B' => [
            ['start' => '2024-01-01', 'end' => '2024-02-29'],
            ['start' => '2024-03-15', 'end' => '2024-03-31'],
            ['start' => '2024-04-15', 'end' => '2024-12-31'],
            ['start' => '2025-01-01', 'end' => '2025-12-31']
        ]]);
    }

    public function test_can_get_availability_of_parking_spaces_when_with_three_bookings()
    {
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
        $response = $this->get('/api/v1/availability', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['Parking Space C' => [
            ['start' => '2024-01-01', 'end' => '2024-02-29'],
            ['start' => '2024-03-15', 'end' => '2024-03-31'],
            ['start' => '2024-04-15', 'end' => '2024-04-30'],
            ['start' => '2024-05-15', 'end' => '2024-12-31'],
            ['start' => '2025-01-01', 'end' => '2025-12-31']
        ]]);
    }

    public function test_can_get_availability_of_parking_spaces_when_with_four_bookings()
    {
        Booking::insert([
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-03-01',
                'end' => '2024-03-14',
                'parking_space' => 'D'
            ],
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-04-01',
                'end' => '2024-04-14',
                'parking_space' => 'D'
            ],
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-05-01',
                'end' => '2024-05-14',
                'parking_space' => 'D'
            ],
            [
                'user_id' => $this->testUser->id,
                'start' => '2024-06-01',
                'end' => '2024-06-14',
                'parking_space' => 'D'
            ]
        ]);
        $response = $this->get('/api/v1/availability', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['Parking Space D' => [
            ['start' => '2024-01-01', 'end' => '2024-02-29'],
            ['start' => '2024-03-15', 'end' => '2024-03-31'],
            ['start' => '2024-04-15', 'end' => '2024-04-30'],
            ['start' => '2024-05-15', 'end' => '2024-05-31'],
            ['start' => '2024-06-15', 'end' => '2024-12-31'],
            ['start' => '2025-01-01', 'end' => '2025-12-31']
        ]]);
    }

    public function test_can_check_parking_space_availability_of_date_booked_is_unavailable(){
        $response = $this->post('/api/v1/availability', [
            'start' => '2024-03-01',
            'end' => '2024-03-14',
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['total_available_spaces' => 6]);  // Parking space A, B, C, D has been booked for the date based on the initial tests
    }

    public function test_can_check_parking_space_availability_date_not_booked_is_available(){
        $response = $this->post('/api/v1/availability', [
            'start' => '2024-08-01',
            'end' => '2024-08-05',
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['total_available_spaces' => 10]);   // All parking spaces available as date has never been booked
    }

    public function test_can_get_availability_pricing_of_dates(){
        $response = $this->post('/api/v1/availability/pricing', [
            'start' => '2024-08-01',
            'end' => '2024-08-05',
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('total_price', $response['data']);
        $this->assertArrayHasKey('pricing_breakdown', $response['data']);
        $this->assertArrayHasKey('season', $response['data']['pricing_breakdown'][0]);
        $this->assertArrayHasKey('price', $response['data']['pricing_breakdown'][0]);
    }
}
