<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BibleChapter;
use App\Models\User;
use App\Services\AiCoachService;

class BibleChapterController extends Controller
{
    protected $allowedReferences = [
        'Psalms 91', 'Deuteronomy 28', 'Psalms 23',
        'Psalms 27', 'Psalms 121', 'Psalms 118', 'Isaiah 61',
    ];

    protected $affirmationVerses = [
        'Romans 8:28' => 'And we know that all things work together for good to them that love God, to them who are the called according to his purpose.',
        '1 John 5:4' => 'For whatsoever is born of God overcometh the world: and this is the victory that overcometh the world, even our faith.',
        '2 Corinthians 5:17' => 'Therefore if any man be in Christ, he is a new creature: old things are passed away; behold, all things are become new.',
        'Philippians 4:13' => 'I can do all things through Christ which strengtheneth me.',
        'Philippians 2:13' => 'For it is God which worketh in you both to will and to do of his good pleasure.',
    ];

    public function show(string $reference)
    {
        $allowed = collect($this->allowedReferences)
            ->map(fn($r) => strtolower(str_replace(' ', '', $r)))
            ->contains(fn($r) => $r === strtolower(str_replace(' ', '', $reference)));

        if (!$allowed) {
            abort(404, 'Bible chapter not found');
        }

        $chapter = BibleChapter::byReference($reference)->first();

        if (!$chapter) {
            abort(404, 'Bible chapter not found');
        }

        // 📖 Track the read — powers the weekly recap's "chapters declared"
        \App\Models\DeclarationRead::create([
            'user_id' => auth()->id(),
            'reference' => $chapter->reference,
            'date' => now()->toDateString(),
        ]);

        $declarationText = $chapter->getDeclarationText();

        $user = User::find(auth()->id());
        $wakeUpTime = $user?->morningDevotion?->wake_up_time;

        $affirmations = $this->affirmationVerses;

        $chapters = BibleChapter::whereIn('reference', $this->allowedReferences)
            ->orderByRaw("FIELD(reference, 'Psalms 91', 'Deuteronomy 28', 'Psalms 23', 'Psalms 27', 'Psalms 121', 'Psalms 118', 'Isaiah 61')")
            ->get();

        return view('devotional.chapter', compact('chapter', 'declarationText', 'wakeUpTime', 'affirmations', 'chapters'));
    }

    public function personalize(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $chapter = BibleChapter::byReference($request->reference)->first();

        if (!$chapter) {
            return response()->json(['error' => 'Chapter not found'], 404);
        }

        $ai = new AiCoachService();

        $prompt = "Personalize this Bible chapter for personal morning declaration.\n";
        $prompt .= "ONLY change pronouns referring to the reader/believer to first-person (I, me, my, mine).\n";
        $prompt .= "NEVER change pronouns referring to God, the LORD, Jesus, or the Holy Spirit.\n";
        $prompt .= "Reference: {$chapter->reference}\n\n{$chapter->content}\n\n";
        $prompt .= "Output ONLY the personalized text.";

        return response()->json(['personalized' => $ai->chat($prompt)]);
    }

    public function availableReferences()
    {
        return collect($this->allowedReferences)
            ->mapWithKeys(fn($ref) => [$ref => $ref])
            ->toArray();
    }
}