<?php

namespace App\Services;

use OpenAI;

class AiCoachService
{
    protected $client;

    /** @var string Model used for chat completions */
    protected $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        // Prefer Groq (free tier) when configured; fall back to OpenAI.
        $groqKey = config('services.groq.api_key');

        if ($groqKey) {
            $this->client = OpenAI::factory()
                ->withApiKey($groqKey)
                ->withBaseUri(config('services.groq.base_uri'))
                ->make();
            $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
            return;
        }

        $apiKey = config('services.openai.api_key');

        // Prevent crash if no API key
        if (!$apiKey) {
            $this->client = null;
            return;
        }

        $this->client = OpenAI::client($apiKey);
    }

    /**
     * Generate daily coaching message
     */
    public function generate($data)
    {
        // Fallback if no AI configured
        if (!$this->client) {
            return "Stay consistent. Small daily wins build discipline 💪";
        }

        try {
            $prompt = $this->buildPrompt($data);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getPersonality($data['mode'] ?? 'strict') .
                            ' Keep responses short (1–2 sentences) and powerful.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
            ]);

            return $response->choices[0]->message->content ?? 'Stay focused and disciplined 💪';

        } catch (\Exception $e) {
            return "Stay disciplined. You're building momentum 💪";
        }
    }

    /**
     * Generate weekly insights for the Weekly Review page.
     *
     * @param array $history  Array of daily stat snapshots (7 days)
     * @param string $mode    Coach mode (strict, calm, aggressive)
     * @return string
     */
    public function generateWeeklyInsights(array $history, string $mode = 'strict'): string
    {
        if (!$this->client) {
            return $this->fallbackWeeklyInsights($history);
        }

        try {
            $historyText = '';
            foreach ($history as $day) {
                $historyText .= "
Date: {$day['date']}
Score: {$day['score']}
Tasks: {$day['tasks_completed']}/{$day['tasks_total']}
Focus: {$day['focus']} min
Journaled: " . (!empty($day['journaled']) ? 'yes' : 'no') . "
Mood: " . ($day['mood'] ?? 'n/a') . "
";
            }

            $prompt = "Here is the user's weekly history (past 7 days):
$historyText

Instructions:
- Summarize their week in 2–3 short paragraphs
- Highlight their biggest win and improvement area
- Point out any mood/focus patterns you detect
- End with one clear action to start the new week strong";

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getPersonality($mode) . ' You are a weekly review coach. Provide thoughtful, encouraging analysis.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ],
                ],
            ]);

            return $response->choices[0]->message->content ?? $this->fallbackWeeklyInsights($history);

        } catch (\Exception $e) {
            return $this->fallbackWeeklyInsights($history);
        }
    }

    /**
     * Heuristic fallback for weekly insights when AI is unavailable.
     */
    protected function fallbackWeeklyInsights(array $history): string
    {
        if (empty($history)) {
            return "Not enough data yet. Keep using SelfCheq daily and we'll build your first weekly review soon! 🌱";
        }

        $avgScore = collect($history)->avg('score');
        $totalTasks = collect($history)->sum('tasks_completed');
        $totalTasksPossible = collect($history)->sum('tasks_total');
        $totalFocus = collect($history)->sum('focus');
        $journaledDays = collect($history)->where('journaled', true)->count();

        $bestDay = collect($history)->sortByDesc('score')->first();
        $moodEntries = collect($history)->filter(fn($h) => $h['mood'] !== null);
        $avgMood = $moodEntries->isNotEmpty() ? $moodEntries->avg('mood') : null;

        $insights = "This week your average discipline score was " . round($avgScore) . "/100. ";
        $insights .= "You completed {$totalTasks} of {$totalTasksPossible} tasks and focused for {$totalFocus} minutes total. ";
        $insights .= "You journaled on {$journaledDays} of 7 days";

        if ($bestDay) {
            $insights .= ". Your best day was {$bestDay['date']} with a score of {$bestDay['score']}/100";
        }

        if ($avgMood !== null) {
            $insights .= ". Average mood this week was " . round($avgMood, 1) . "/5";
        }

        $insights .= ". ";

        // Detect patterns
        $lowFocusDays = collect($history)->filter(fn($h) => ($h['focus'] ?? 0) < 20)->count();
        if ($lowFocusDays > 0) {
            $insights .= "You had {$lowFocusDays} day(s) with less than 20 minutes of focus. Try scheduling a 25-minute focus block tomorrow. ";
        }

        $lowJournalDays = collect($history)->where('journaled', false)->count();
        if ($lowJournalDays > 2) {
            $insights .= "You missed journaling on {$lowJournalDays} days. Journaling boosts self-awareness — try adding it to your morning routine. ";
        }

        if ($lowFocusDays === 0 && $lowJournalDays <= 2) {
            $insights .= "You're building solid habits! Keep stacking wins and your scores will keep climbing. ";
        }

        $insights .= "Action: Pick your single most important task for tomorrow and complete it first thing — this will set the tone for the whole day. 💪";

        return $insights;
    }

    /**
     * Chat with conversation history.
     *
     * @param string $message
     * @param array $history Array of ['role' => 'user'|'assistant', 'content' => string]
     */
    public function chat($message, array $history = [])
    {
        if (!$this->client) {
            return "AI not configured yet.";
        }

        try {
            $mode = auth()->user()->coach_mode ?? 'strict';

            $messages = [
                [
                    'role' => 'system',
                    'content' => $this->getPersonality($mode)
                        . ' This is a live 1-on-1 chat. Reply conversationally like a real person texting — '
                        . 'plain text only, 1-3 short sentences. NEVER use headings, bold, labels, bullet points, '
                        . 'or sections like "Greeting:" or "Inquiry:". Just talk naturally. '
                        . 'You have access to the recent chat history — remember what the user told you.'
                ],
            ];

            foreach ($history as $entry) {
                if (in_array($entry['role'] ?? '', ['user', 'assistant']) && isset($entry['content']) && trim($entry['content']) !== '') {
                    $messages[] = [
                        'role' => $entry['role'],
                        'content' => $entry['content'],
                    ];
                }
            }

            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => $messages,
            ]);

            return trim($response->choices[0]->message->content ?? '') ?: 'Say more — I\'m listening.';

        } catch (\Exception $e) {
            report($e);
            return "I'm having trouble reaching my coaching brain right now — please try again in a moment. 🧠";
        }
    }

    /**
     * Generate an evening "daily nudge" — one short, contextual line to carry
     * into tomorrow. Weaves in the user's streak, today's activity and
     * (optionally) yesterday's Evening Examen reflection.
     *
     * Tone: universal and reflective, not preachy.
     *
     * Expected $context keys:
     *   - streak              (int)    current streak in days
     *   - tasks_completed     (int)
     *   - tasks_total         (int)
     *   - focus_minutes       (int)
     *   - journaled           (bool)
     *   - examen_reflection   (string|null) yesterday's Examen reflection
     *   - examen_gratitude    (string|null) yesterday's gratitude chip
     *   - tomorrow_chapter    (string)      e.g. "Psalms 27"
     *
     * @return string A single-sentence nudge.
     */
    public function generateDailyNudge(array $context): string
    {
        // Heuristic fallback when AI is not configured or fails
        $fallback = $this->fallbackDailyNudge($context);

        if (!$this->client) {
            return $fallback;
        }

        try {
            $reflectionText = !empty($context['examen_reflection'])
                ? "Yesterday's reflection: \"{$context['examen_reflection']}\"\n"
                : '';
            $gratitudeText = !empty($context['examen_gratitude'])
                ? "Yesterday's gratitude: {$context['examen_gratitude']}\n"
                : '';

            $prompt = "
User context:
- Current streak: {$context['streak']} day(s)
- Tasks today: {$context['tasks_completed']}/{$context['tasks_total']}
- Focus: {$context['focus_minutes']} min
- Journaled: " . (!empty($context['journaled']) ? 'yes' : 'no') . "
{$reflectionText}{$gratitudeText}Tomorrow's declaration chapter: {$context['tomorrow_chapter']}

Instructions:
- Write ONE short sentence (max 20 words) — a nudge to carry into tomorrow
- Reference their streak naturally if it's 3+ days
- If a reflection was given, gently build on it; otherwise reference the chapter
- Warm, grounded tone — a moment of reflection, never preachy
- Return ONLY the sentence, no quotes.
";

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You write warm, concise daily nudges for a discipline app. '
                            . 'Universal and reflective in tone — spiritual content is available but never forced. '
                            . 'Always exactly one sentence.',
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $nudge = trim($response->choices[0]->message->content ?? '');

            return $nudge !== '' ? $nudge : $fallback;

        } catch (\Exception $e) {
            return $fallback;
        }
    }

    /**
     * Heuristic daily nudge when AI is unavailable.
     */
    protected function fallbackDailyNudge(array $context): string
    {
        $streak = max((int) ($context['streak'] ?? 0), 0);
        $chapter = $context['tomorrow_chapter'] ?? 'Psalms 27';

        if ($streak >= 3) {
            return "{$streak}-day streak going strong — carry that momentum into {$chapter} tomorrow.";
        }

        if (!empty($context['examen_gratitude'])) {
            return "Hold onto yesterday's gratitude — then start fresh with {$chapter} tomorrow.";
        }

        return "A quiet moment with {$chapter} tomorrow could set the tone for your whole day.";
    }

    /**
     * Build coaching prompt
     */
    private function buildPrompt($data)
    {
        $historyText = '';

        if (!empty($data['history'])) {
            foreach ($data['history'] as $day) {
                $focus = $day['focus_minutes'] ?? $day['focus'] ?? 'n/a';

                $historyText .= "
Date: {$day['date']}
Score: {$day['score']}
Tasks: {$day['tasks_completed']}/{$day['tasks_total']}
Focus: {$focus}
Journaled: " . (!empty($day['journaled']) ? 'yes' : 'no') . "
";
            }
        }

        return "
User stats today:
- Score: {$data['score']}/100
- Tasks: {$data['tasks_completed']}/{$data['tasks_total']}
- Focus: {$data['focus']}
- Journaled: " . (($data['journal'] ?? false) ? 'yes' : 'no') . "

" . (!empty($historyText) ? "Past 7 days:
$historyText
" : '') . "

Instructions:
- Detect patterns
- Push improvement
- Give 1 clear action

Return only a short coaching message.
";
    }

    /**
     * Personality system 🎭
     */
    private function getPersonality($mode)
    {
        return match ($mode) {
            'calm' => "You are a calm, wise mentor. Speak gently.",
            'aggressive' => "You are intense, direct, no excuses.",
            default => "You are strict, structured, disciplined.",
        };
    }
}
