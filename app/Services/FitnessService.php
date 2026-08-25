<?php

namespace App\Services;

use OpenAI;

class FitnessService
{
    protected $client;
    protected $model = 'openai/gpt-oss-120b';

    public function __construct()
    {
        // Same provider strategy as AiCoachService: Groq first, then OpenAI.
        $groqKey = config('services.groq.api_key');

        if ($groqKey) {
            $this->client = OpenAI::factory()
                ->withApiKey($groqKey)
                ->withBaseUri(config('services.groq.base_uri'))
                ->make();
            $this->model = config('services.groq.model', 'openai/gpt-oss-120b');
            return;
        }

        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            $this->client = null;
            return;
        }

        $this->client = OpenAI::client($apiKey);
    }

    /**
     * Generate a structured weekly fitness plan (workouts + diet).
     *
     * @return array{summary: string, diet: array<int, string>, days: array, source: string}
     */
    public function generateWeeklyPlan(string $goal, string $level): array
    {
        if ($this->client) {
            try {
                return $this->generateWithAi($goal, $level);
            } catch (\Exception $e) {
                report($e);
            }
        }

        return $this->fallbackPlan($goal, $level);
    }

    protected function generateWithAi(string $goal, string $level): array
    {
        $goalLabels = [
            'lose_weight' => 'lose weight / fat loss',
            'build_muscle' => 'build muscle',
            'endurance' => 'improve endurance & cardio',
            'general' => 'general health & fitness',
        ];

        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a certified personal trainer and nutrition coach. '
                        . 'Respond with ONLY valid JSON, no markdown fences, no commentary. Schema: '
                        . '{"summary":"one motivational sentence","diet":["5 short nutrition guidelines"],'
                        . '"days":[{"focus":"e.g. Push Day or Rest Day",'
                        . '"workout":["3-6 exercises with sets/reps like Squats 4x10"],"tip":"one form/recovery tip"}]} '
                        . '"days" must contain exactly 7 entries (Mon-Sun) with at least 2 rest/active-recovery days.',
                ],
                [
                    'role' => 'user',
                    'content' => "Create a 7-day workout + diet plan for someone at {$level} level whose goal is to "
                        . ($goalLabels[$goal] ?? $goalLabels['general']) . '. They train at home or a basic gym.',
                ],
            ],
        ]);

        $content = trim($response->choices[0]->message->content ?? '');
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $content);
        $decoded = json_decode($content, true);

        if (!is_array($decoded) || empty($decoded['days'])) {
            throw new \Exception('Invalid AI fitness plan response');
        }

        return [
            'summary' => trim($decoded['summary'] ?? ''),
            'diet' => array_values(array_slice((array) ($decoded['diet'] ?? []), 0, 6)),
            'days' => $this->normalizeDays($decoded['days']),
            'source' => 'ai',
        ];
    }

    protected function normalizeDays(array $days): array
    {
        return array_values(array_slice(array_map(function ($d) {
            return [
                'focus' => trim((string) ($d['focus'] ?? 'Training')),
                'workout' => array_values(array_filter(array_map('trim', (array) ($d['workout'] ?? [])))),
                'tip' => trim((string) ($d['tip'] ?? '')),
            ];
        }, $days), 0, 7));
    }

    /**
     * Solid preset plan when AI is unavailable.
     */
    protected function fallbackPlan(string $goal, string $level): array
    {
        $reps = $level === 'beginner' ? '3x10' : ($level === 'intermediate' ? '4x12' : '5x15');
        $cardioMin = $goal === 'endurance' ? 40 : ($goal === 'lose_weight' ? 30 : 20);

        $strength = ["Push-ups {$reps}", "Bodyweight squats {$reps}", "Lunges {$reps}", "Plank 3x45s"];
        $lower = ["Squats {$reps}", "Glute bridges {$reps}", "Calf raises {$reps}", "Wall sit 3x40s"];

        return [
            'summary' => "A balanced home-friendly week tuned for {$level}s — consistency beats intensity. 💪",
            'diet' => [
                'Protein with every meal (eggs, chicken, beans, Greek yogurt).',
                'Drink 2–3 liters of water daily.',
                'Fill half your plate with vegetables at lunch and dinner.',
                $goal === 'lose_weight'
                    ? 'Keep a slight calorie deficit — smaller portions, no late-night snacking.'
                    : 'Eat enough calories to support training — don\'t skip carbs on workout days.',
                'Limit sugary drinks and processed snacks.',
                'Prep meals ahead so hunger never picks your food.',
            ],
            'days' => [
                ['focus' => 'Full Body Strength', 'workout' => $strength, 'tip' => 'Warm up 5 minutes before starting.'],
                ['focus' => "Cardio ({$cardioMin} min)", 'workout' => ["Brisk walk / jog / cycle {$cardioMin} min", 'Stretching 10 min'], 'tip' => 'Keep a pace where you can still talk.'],
                ['focus' => 'Lower Body', 'workout' => $lower, 'tip' => 'Drive through your heels on every rep.'],
                ['focus' => 'Active Recovery', 'workout' => ['Walk 20–30 min', 'Full-body stretching 15 min'], 'tip' => 'Recovery days build the gains — don\'t skip them.'],
                ['focus' => 'Upper Body & Core', 'workout' => ["Push-ups {$reps}", "Pike push-ups {$reps}", "Superman holds 3x30s", "Bicycle crunches {$reps}"], 'tip' => 'Squeeze your core on every rep.'],
                ['focus' => "Cardio ({$cardioMin} min)", 'workout' => ["Intervals: 1 min fast / 2 min easy x8", 'Cool-down walk 5 min'], 'tip' => 'Intervals burn more than steady pace in less time.'],
                ['focus' => 'Rest', 'workout' => ['Complete rest or light walk', 'Plan meals for next week'], 'tip' => 'Sleep 7–9 hours tonight.'],
            ],
            'source' => 'preset',
        ];
    }
}
