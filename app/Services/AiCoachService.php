<?php

namespace App\Services;

use OpenAI;

class AiCoachService
{
    public function generate($data)
    {
        $client = OpenAI::client(config('services.openai.key'));

        $prompt = $this->buildPrompt($data);

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a strict but motivating discipline coach. Keep responses short and powerful.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    private function buildPrompt($data)
    {
        return 
        "User daily stats:
          - Discipline score: {$data['score']}/100
          - Tasks completed: {$data['tasks_completed']}/{$data['tasks_total']}
          - Focus minutes: {$data['focus']}
          - Journaled today: " . ($data['journal'] ? 'yes' : 'no') . "

        Extra context:
            - If journal is missing → emphasize reflection
            - If focus is low → suggest deep work
            - If all tasks are completed → praise and encourage consistency and discipline

        Give a short (1–2 sentences) coaching message to improve discipline.
        ";

        $historyText = "";

        foreach ($data['history'] as $day) {
            $historyText .="

        Date: {$day['date']} 
        Score: {$day['score']}
        Tasks: {$day['tasks_completed']}/{$day['tasks_total']} 
        Focus: {$day['focus_minutes']} 
        Journaled: " . ($day['journaled'] ? 'yes' : 'no') . "
        ";
        }

        return "
        You are an elite discipline coach.

        Today's stats:
        -Score: {$data['score']}/100
        - Tasks: {$data['tasks_completed']}/{$data['tasks_total']}
        - Focus: {$data['focus']}
        - Journaled: " . ($data['journal'] ? 'yes' : 'no') . "

        Past 7 days:
        $historyText

        Analyze patterns, consistency, and weaknesses.

        Give a short (2–3 sentences) coaching message that:
        - Identifies a pattern
        - Calls out a weakness
        - Gives a clear improvement action
     ";
    }
}