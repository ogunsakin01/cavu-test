<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public array $fakeUser;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->fakeUser = [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Successful registration test
     */
    public function test_customer_registration_success(): void
    {
        $response = $this->post('/api/v1/register', $this->fakeUser, [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $this->fakeUser['name'],
                'email' => $this->fakeUser['email'],
            ]
        ]);

    }

    /**
     *  Registration validation error test
     */
    public function test_customer_registration_validation_error(): void
    {
        $this->fakeUser['password'] = uniqid();
        $response = $this->post('/api/v1/register', $this->fakeUser, [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment([0 => 'The password field confirmation does not match.']);
    }

    /**
     *  Login validation error test
     */
    public function test_customer_login_validation_error(): void
    {
        $this->fakeUser['password'] = uniqid();
        $response = $this->post('/api/v1/login',  [
            'email' => $this->fakeUser['email'],
            'password' => uniqid()
        ],[
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(422);
        $response->assertJsonFragment([0 => 'Invalid password']);
    }


    /**
     *  Login success test
     */
    public function test_customer_login_success(): void
    {
        $this->fakeUser['password'] = uniqid();
        $response = $this->post('/api/v1/login', [
            'email' => $this->fakeUser['email'],
            'password' => 'password'
        ], [
            'Accept' => 'application/json'
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment(['data' => 'The password field confirmation does not match.']);
    }
}
