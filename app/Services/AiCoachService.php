<?php

namespace App\Services;

use OpenAI;

class AiCoachService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.openai.key'));
    }

    /**
     * Generate daily coaching message
     */
    public function generate($data)
    {
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

        return $response->choices[0]->message->content;
    }

    /**
     * Chat (used for voice AI or manual input)
     */
    public function chat($message)
    {
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

        return $response->choices[0]->message->content;
    }

    /**
     * Build main coaching prompt
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
- Detect patterns in behavior
- If journaling is missing → emphasize reflection
- If focus is low → recommend deep work
- If tasks are completed → reinforce discipline
- Give 1 clear improvement action

Return only a short coaching message.
";
    }

    /**
     * Personality system 🎭
     */
    private function getPersonality($mode)
    {
        return match ($mode) {
            'calm' => "You are a calm, wise mentor. Speak gently, encourage reflection.",
            'aggressive' => "You are an intense, no-excuses drill sergeant. Be direct, tough, and push hard.",
            default => "You are a strict discipline coach. Be firm, structured, and no-nonsense.",
        };
    }
}