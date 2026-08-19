<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'birthday',
        'bio',
        'profile_photo_path',
        'password',
        'onboarding_complete',
        'theme',
        'notifications_enabled',
        'contacts_enabled',
        'reminders_enabled',
        'xp',
        'level',
        'streak',
        'last_completed_date',
        'coach_mode',
        'settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_complete' => 'boolean',
            'birthday' => 'date',
            'last_completed_date' => 'date',
            'settings' => 'array',
        ];
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        try {
            if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
                $url = Storage::disk('public')->url($this->profile_photo_path);
                // Add cache buster to ensure fresh image loads
                return $url . '?v=' . filemtime(storage_path('app/public/' . $this->profile_photo_path));
            }
        } catch (\Exception $e) {
            // If file check fails, fall back to initials
        }

        // Fallback: generated avatar from initials
        $name = trim(collect(explode(' ', $this->name))->map(fn ($n) => mb_substr($n, 0, 1))->join(''));
        return 'https://ui-avatars.com/api/?name=' . urlencode($name ?: 'U') . '&color=FFFFFF&background=6366F1&bold=true';
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function focusSessions()
    {
        return $this->hasMany(FocusSession::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function financials()
    {
        return $this->hasMany(Financial::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function dailyStats()
    {
        return $this->hasMany(DailyStat::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function morningDevotion()
    {
        return $this->hasOne(MorningDevotion::class);
    }
}
