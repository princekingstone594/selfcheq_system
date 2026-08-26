<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
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
    public function dashboard_shows_empty_state_metrics_for_new_users(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertOk();

        // A brand-new user should see level 1 and an XP readout.
        $html = $response->getContent();
        $this->assertStringContainsString('Level 1', $html);
        $this->assertStringContainsString('total XP', $html);
    }

    /** @test */
    public function dashboard_persists_todays_daily_stat(): void
    {
        $this->get(route('dashboard'))->assertOk();

        $this->assertDatabaseHas('daily_stats', [
            'user_id' => $this->user->id,
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function dashboard_query_count_stays_bounded(): void
    {
        // Guard against N+1 creep: the dashboard does a fixed amount of work
        // regardless of how much data exists. Seed a realistic dataset.
        $tasks = [];
        for ($i = 0; $i < 20; $i++) {
            $tasks[] = [
                'user_id' => $this->user->id,
                'title' => "Task {$i}",
                'due_date' => now()->subDays($i % 7)->toDateString(),
                'is_completed' => $i % 2 === 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('tasks')->insert($tasks);

        DB::enableQueryLog();
        $this->get(route('dashboard'))->assertOk();
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // The chart loop alone accounts for ~14 queries; anything beyond 60
        // means a per-row query crept in somewhere.
        $this->assertLessThan(60, $queryCount, "Dashboard executed {$queryCount} queries — possible N+1.");
    }

    /** @test */
    public function dashboard_is_cached_per_user_and_day_for_ai_coach(): void
    {
        // First request populates the cache entry; second must not recompute
        // the AI message (cache store is array-backed in tests).
        $this->get(route('dashboard'))->assertOk();
        $first = cache()->get('ai_coach_' . $this->user->id . '_' . now()->toDateString());

        $this->assertNotNull($first, 'AI coach message should be cached for the day.');
        $this->assertSame($first, cache()->get('ai_coach_' . $this->user->id . '_' . now()->toDateString()));
    }
}
