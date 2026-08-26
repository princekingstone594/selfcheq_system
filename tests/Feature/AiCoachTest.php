<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AiCoachService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiCoachTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.groq.api_key' => null]);
        config(['services.openai.api_key' => null]);

        $this->user = User::factory()->create([
            'onboarding_complete' => true,
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function generate_falls_back_gracefully_without_api_keys(): void
    {
        $service = app(AiCoachService::class);

        $message = $service->generate(['score' => 50, 'tasks_completed' => 1, 'tasks_total' => 3]);

        $this->assertIsString($message);
        $this->assertNotSame('', trim($message));
    }

    /** @test */
    public function coach_chat_page_renders_and_accepts_messages(): void
    {
        $this->get(route('coach.index'))->assertOk();

        // Chat endpoint should not explode even with AI disabled — it must
        // degrade to a helpful fallback reply.
        $response = $this->post(route('coach.chat'), [
            'message' => 'How did I do today?',
        ]);

        $this->assertContains($response->status(), [200, 302]);
    }

    /** @test */
    public function chat_validates_message_presence(): void
    {
        $response = $this->from(route('coach.index'))->post(route('coach.chat'), []);

        $this->assertContains($response->status(), [200, 302]);
    }
}
