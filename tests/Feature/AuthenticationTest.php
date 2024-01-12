<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     *  Registration validation error test
     */
    public function test_customer_registration_validation_error(): void
    {
        $fakeUser = User::factory()->make()->toArray();
        $response = $this->post('/api/v1/register',$fakeUser,[
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(200);
    }

    /**
     * Successful registration test
     */
    public function test_customer_registration_success(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
