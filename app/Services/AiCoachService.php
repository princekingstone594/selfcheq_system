<?php

namespace App\Services;

use OpenAI;

class AiCoachService
{
    protected $client;

    public function __construct()
    {
        $apiKey = config('services.openai.key');

        // ✅ Prevent crash if no API key
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
        // ✅ fallback if no AI configured
        if (!$this->client) {
            return "Stay consistent. Small daily wins build discipline 💪";
        }

        try {
            $prompt = $this->buildPrompt($data);

            $response = $this->client->chat()->create([
                'model' => 'gpt-4o-mini',
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
     * Chat (future feature)
     */
    public function chat($message)
    {
        if (!$this->client) {
            return "AI not configured yet.";
        }

        try {
            $mode = auth()->user()->coach_mode ?? 'strict';

            $response = $this->client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getPersonality($mode)
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
            ]);

            return $response->choices[0]->message->content ?? '';

        } catch (\Exception $e) {
            return "Stay focused.";
        }
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