<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BibleChapter;
use App\Models\User;
use App\Models\MorningDevotion;

class BibleChapterController extends Controller
{
    /**
     * Show a Bible chapter for offline devotional use.
     */
    public function show(string $reference)
    {
        $chapter = BibleChapter::byReference($reference)->first();

        // If not found in DB, try to find by default KJV text
        if (!$chapter) {
            $chapter = BibleChapter::first();
        }

        if (!$chapter) {
            abort(404, 'Bible chapter not found');
        }

        $declarationText = $chapter->getDeclarationText();

        // Get user's morning devotion wake-up time if available
        $user = User::find(auth()->id());
        $wakeUpTime = $user?->morningDevotion?->wake_up_time;

        return view('devotional.chapter', compact('chapter', 'declarationText', 'wakeUpTime'));
    }

    /**
     * Get available chapter references for the UI.
     */
    public function availableReferences()
    {
        return BibleChapter::orderBy('reference')->pluck('reference', 'reference')->toArray();
    }
}
