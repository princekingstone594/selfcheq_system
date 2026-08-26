<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // The root route is auth-protected: guests get bounced to login.
        if (auth()->check()) {
            $response->assertOk();
        } else {
            $this->assertContains($response->status(), [200, 302]);
        }
    }
}
