<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke-renders every core page as an authenticated user with an empty account.
 * Locks in the mobile-first layout work against regressions (broken Blade,
 * missing view variables, controller errors).
 */
class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Never hit the real AI providers during tests.
        config(['services.groq.api_key' => null]);
        config(['services.openai.api_key' => null]);

        $this->user = User::factory()->create([
            'onboarding_complete' => true,
        ]);

        $this->actingAs($this->user);
    }

    public static function pageProvider(): array
    {
        return [
            'dashboard'      => ['dashboard'],
            'appointments'   => ['appointments.index'],
            'calendar'       => ['calendar.index'],
            'coach'          => ['coach.index'],
            'devotional'     => ['devotional.today'],
            'examen'         => ['examen.today'],
            'export'         => ['export.index'],
            'financials'     => ['financials.index'],
            'fitness'        => ['fitness.index'],
            'focus'          => ['focus.index'],
            'goals'          => ['goals.index'],
            'habits'         => ['habits.index'],
            'journal'        => ['journal.index'],
            'notes'          => ['notes.index'],
            'progress'       => ['progress.index'],
            'recap'          => ['recap.index'],
            'routines'       => ['routines.index'],
            'settings'       => ['settings.index'],
            'tasks'          => ['tasks.index'],
            'weekly-review'  => ['weekly-review.index'],
        ];
    }

    /**
     * @test
     * @dataProvider pageProvider
     */
    public function core_pages_render_for_authenticated_users(string $route): void
    {
        $response = $this->get(route($route));

        $response->assertOk();

        // Every page must render inside the authenticated app shell.
        $response->assertSee('SelfCheq', false);
    }

    /** @test */
    public function dashboard_renders_progress_and_daily_rhythm_sections(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Daily rhythm', false)
            ->assertSee('Your progress', false);

        // Progress section must appear after the Daily Rhythm section in the DOM.
        $html = $response->getContent();
        $this->assertGreaterThan(
            strpos($html, 'Daily rhythm'),
            strpos($html, 'Your progress'),
            'The "Your progress" section should be rendered below the Daily Rhythm section.'
        );
    }

    /** @test */
    public function guests_are_redirected_to_login(): void
    {
        $this->post('/logout');

        foreach (['dashboard', 'appointments.index', 'progress.index'] as $route) {
            $this->get(route($route))->assertRedirect(route('login', absolute: false));
        }
    }
}
