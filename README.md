# SelfCheq

A mobile-first, gamified self-discipline & Christian life-coaching PWA built on Laravel 12: daily tasks, routines, journaling, evening Examen, devotionals, focus timers, fitness plans, finances, an AI coach ("Coach Zoe") and weekly recaps.

## Local Setup

```bash
composer install
cp .env.example .env        # then fill in DB credentials + a Groq or OpenAI key
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve
```

Run everything at once (server, queue worker, logs, Vite):

```bash
composer dev
```

## Tests

```bash
php artisan test
```

Feature tests cover all core pages, the dashboard data pipeline, and AI-coach degradation paths. No real API calls are made in tests (AI keys are nulled in `setUp`).

## Scheduled Jobs (`routes/console.php`)

| Command | Schedule |
|---|---|
| `reminders:tasks` | every minute (deadline alerts) |
| `reminders:send` | daily 08:00 |
| `streak:remind` | daily 19:00 |
| `nudges:send` | daily 20:30 |
| `wins:send` | daily 21:00 |
| `recap:send` | Sundays 19:00 |

**Production requirements:**

1. **Scheduler** — a cron entry running Laravel's scheduler every minute:
   ```
   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
   ```
2. **Queue worker** — `QUEUE_CONNECTION=database`; run under supervisor/systemd:
   ```
   php artisan queue:work --tries=1 --timeout=0
   ```
   Ensure the `jobs` and `failed_jobs` tables exist (`php artisan queue:table && php artisan migrate` if missing).

## AI Coach Configuration

`AiCoachService` prefers **Groq** (free tier) and falls back to **OpenAI**; with neither configured it degrades gracefully to canned messages.

```
GROQ_API_KEY=...        # preferred — free tier
GROQ_MODEL=openai/gpt-oss-120b
OPENAI_API_KEY=...      # optional fallback
```

The dashboard coach message is cached per user per day (`ai_coach_{id}_{date}`); users can regenerate via the "🔄 New message" button. Transient provider errors (429/503/timeouts) are retried once after a 1s backoff.

## Deployment Checklist

- Set `APP_ENV=production`, `APP_DEBUG=false`, and a strong `APP_URL` in the prod `.env`
- Run `php artisan config:cache route:cache view:cache`
- Keep `.env` out of version control (`.env.example` documents required keys)
