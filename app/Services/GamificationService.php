<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use Carbon\Carbon;

class GamificationService
{
    /**
     * XP thresholds for badge awards.
     */
    const BADGE_THRESHOLDS = [
        50   => 'Getting Started',
        100  => 'First 100 XP',
        500  => 'Dedicated',
        1000 => 'Master',
        5000 => 'Legend',
    ];

    /**
     * XP needed per level (level N requires XP >= N * XP_PER_LEVEL)
     */
    const XP_PER_LEVEL = 100;

    /**
     * Streak milestones for badges.
     */
    const STREAK_MILESTONES = [3, 7, 14, 30, 60, 100];

    /**
     * Badge icon map for known badge names.
     */
    const BADGE_ICONS = [
        '🔥 First Day' => '🎯',
        '🔥 Three Day Streak' => '🔥',
        '🔥🔥 Seven Day Streak' => '🔥🔥',
        '🔥🔥🔥 Two Week Streak' => '🔥🔥🔥',
        '🔥🔥🔥🔥 Month Streak' => '🔥🔥🔥🔥',
        '🔥🔥🔥🔥🔥 Two Month Streak' => '🔥🔥🔥🔥🔥',
        '🔥🔥🔥🔥🔥🔥 Century Streak' => '🔥🔥🔥🔥🔥🔥',
        'Getting Started' => '🌱',
        'First 100 XP' => '💎',
        'Dedicated' => '🛡️',
        'Master' => '⚡',
        'Legend' => '👑',
        'First Task Completed' => '✅',
        'Financial Discipline Week' => '💰',
        '1000 XP Earned' => '💰',
    ];

    /**
     * Award XP to a user and handle level-ups and badges.
     */
    public static function awardXp(User $user, int $amount, string $reason = null): void
    {
        $user->increment('xp', $amount);
        $user->touch(); // update updated_at

        self::checkLevelUp($user);
        self::checkXpBadges($user);

        // Log for debugging
        \Log::info("🎮 Awarded {$amount} XP to user {$user->id} ({$reason}). Total: {$user->fresh()->xp} XP");
    }

    /**
     * Deduct XP from a user (e.g., when uncompleting a task).
     */
    public static function deductXp(User $user, int $amount, string $reason = null): void
    {
        $user->decrement('xp', $amount);
        if ($user->xp < 0) {
            $user->update(['xp' => 0]);
        }

        self::checkLevelUp($user);
    }

    /**
     * Update the user's level based on current XP.
     */
    public static function checkLevelUp(User $user): void
    {
        $newLevel = floor($user->xp / self::XP_PER_LEVEL) + 1;

        if ($newLevel > $user->level) {
            $oldLevel = $user->level;
            $user->update(['level' => $newLevel]);

            // Award a badge for reaching a new level milestone
            $levelBadges = [10, 20, 50, 100];
            foreach ($levelBadges as $milestone) {
                if ($newLevel >= $milestone && $oldLevel < $milestone) {
                    self::awardBadge($user, "Level {$milestone}");
                }
            }

            \Log::info("🎉 User {$user->id} leveled up from {$oldLevel} to {$newLevel}!");
        }
    }

    /**
     * Check and award badges based on XP thresholds.
     */
    public static function checkXpBadges(User $user): void
    {
        foreach (self::BADGE_THRESHOLDS as $threshold => $badgeName) {
            if ($user->xp >= $threshold) {
                self::awardBadge($user, $badgeName);
            }
        }
    }

    /**
     * Award a badge to a user if they don't already have it.
     */
    public static function awardBadge(User $user, string $badgeName, string $description = null): void
    {
        $icon = self::BADGE_ICONS[$badgeName] ?? '🏅';

        $badge = Badge::firstOrCreate(
            ['name' => $badgeName],
            [
                'description' => $description ?? "Awarded for {$badgeName}",
                'icon' => $icon,
            ]
        );

        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id);
            \Log::info("🏅 User {$user->id} earned badge: {$badge->name}");
        }
    }

    /**
     * Update streak tracking when the user has activity on a given day.
     * Streak increments if yesterday was the last activity day.
     * Resets to 1 if there was a gap.
     */
    public static function recordDailyActivity(User $user, string $activityDate = null): void
    {
        $activityDate = $activityDate ?? Carbon::today()->toDateString();
        $today = Carbon::today();

        if ($user->last_completed_date === null) {
            // First time — no streak history
            $user->update([
                'streak' => 1,
                'last_completed_date' => $activityDate,
            ]);
            self::awardBadge($user, '🔥 First Day', 'Awarded for starting your discipline journey');
        } elseif ($user->last_completed_date === $today->copy()->subDay()->toDateString()) {
            // Consecutive day — increment streak
            $user->increment('streak');
            $user->update(['last_completed_date' => $activityDate]);
            self::checkStreakBadges($user);
        } elseif ($user->last_completed_date !== $activityDate) {
            // Gap in activity — reset streak to 1
            $user->update([
                'streak' => 1,
                'last_completed_date' => $activityDate,
            ]);
        }
    }

    /**
     * Check and award badges for streak milestones.
     */
    public static function checkStreakBadges(User $user): void
    {
        foreach (self::STREAK_MILESTONES as $milestone) {
            if ($user->streak >= $milestone) {
                $badgeName = match ($milestone) {
                    3  => '🔥 Three Day Streak',
                    7  => '🔥🔥 Seven Day Streak',
                    14 => '🔥🔥🔥 Two Week Streak',
                    30 => '🔥🔥🔥🔥 Month Streak',
                    60 => '🔥🔥🔥🔥🔥 Two Month Streak',
                    100 => '🔥🔥🔥🔥🔥🔥 Century Streak',
                    default => "Streak: {$milestone} days",
                };
                self::awardBadge($user, $badgeName, "Awarded for a {$milestone}-day streak");
            }
        }
    }

    /**
     * Award XP for focus session completion (1 XP per minute, capped at 60 per session).
     */
    public static function awardFocusXp(User $user, int $durationMinutes): void
    {
        $xp = min($durationMinutes, 60); // cap at 60 XP per focus session
        self::awardXp($user, $xp, "Focus session: {$durationMinutes} min");
    }

    /**
     * Award XP for journaling (fixed amount for creating/editing an entry).
     */
    public static function awardJournalXp(User $user): void
    {
        self::awardXp($user, 20, 'Journal entry saved');
    }

    /**
     * Award XP for completing a routine.
     */
    public static function awardRoutineXp(User $user): void
    {
        self::awardXp($user, 15, 'Routine completed');
    }
}
