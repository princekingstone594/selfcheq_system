<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_complete_onboarding_and_have_it_persisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/onboarding/complete');

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertTrue($user->fresh()->onboarding_complete);
    }
}
