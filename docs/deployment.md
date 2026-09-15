# Production deployment (cPanel)

## Architecture

```
push/merge to master
        |
GitHub Actions (.github/workflows/deploy-production.yml)
        |
checkout exact commit -> test -> build frontend
        |
SSH + rsync
        |
/home/avenuebr/repositories/avenue   (application code, composer/artisan run here)
        |
allowlisted static asset sync
        |
/home/avenuebr/public_html           (actual web root served by Apache)
        |
health check -> https://avenuebrand.online
```

GitHub Actions owns the deployed commit. The server never runs `git pull` —
it only receives what the workflow rsyncs to it, which is always the exact
commit that triggered the run. This keeps "what's live" deterministic and
avoids the server independently deciding what `origin/master` currently is.

Two separate directories exist on the server for a reason:

- `/home/avenuebr/repositories/avenue` — the Laravel application (`app/`,
  `vendor/`, `artisan`, etc.). Not web-accessible.
- `/home/avenuebr/public_html` — the actual Apache document root. Only a
  subset of `public/` (the Laravel-owned static assets) is mirrored here;
  everything else already in `public_html` (`.htaccess`, `.well-known/`,
  domain-verification files, etc.) is left alone.

## Required GitHub Actions secrets

Already configured on the repository (not stored here):

| Secret | Purpose |
|---|---|
| `CPANEL_HOST` | SSH hostname/IP for the cPanel server |
| `CPANEL_PORT` | SSH port |
| `CPANEL_USER` | SSH/cPanel username (`avenuebr`) |
| `CPANEL_SSH_KEY` | Private key for SSH auth (public key must be in the server's `~/.ssh/authorized_keys`) |
| `DEPLOY_PATH` | Absolute path to the application checkout, `/home/avenuebr/repositories/avenue` |

`public_html` itself is never a secret — it's derived in the workflows as
`/home/$CPANEL_USER/public_html` so the username isn't duplicated anywhere.

## Workflows

### `.github/workflows/test-cpanel-ssh.yml`

Manual (`workflow_dispatch`) only. Verifies SSH connectivity, that
`DEPLOY_PATH` exists, that `php`/`composer`/`rsync` are available on the
server, checks `.env` vs. a stray `env` file (existence only), inspects
`public_html/index.php` for a `repositories/avenue` path reference, and
reports whether the `public_html/storage` symlink exists. It never prints
secret values and never modifies anything.

Run it once before trusting the production workflow, and again any time the
server, key, or paths change:

```bash
gh workflow run test-cpanel-ssh.yml --ref <branch>
gh run watch
```

### `.github/workflows/deploy-production.yml`

Triggers on push to `master` (this repo's actual default branch — not `main`)
and manually via `workflow_dispatch`. Runs with
`concurrency: { group: avenue-production, cancel-in-progress: false }` so two
deploys can never race, and `permissions: contents: read` (no broader token
scope than needed).

Stages, in order (fails fast — a failed step stops the job, nothing after it
runs, and "Deployment successful" only prints if everything before it
succeeded):

1. Checkout the exact triggering commit.
2. PHP dependency install (with dev) + `php artisan test` against the
   in-memory SQLite config already defined in `phpunit.xml`.
3. `npm ci` + `npm run build` (Vite), and verify `public/build/manifest.json`
   exists.
4. SSH key setup with strict host key checking (`ssh-keyscan` populates
   `known_hosts` — `StrictHostHostKeyChecking=no` is never used).
5. Pre-flight `.env` check (see below) — deploy stops here if it's wrong.
6. `rsync` the application code to `DEPLOY_PATH`, excluding `.git/`,
   `.github/`, `node_modules/`, `vendor/`, `storage/`, `tests/`, and any
   `.env*` file. No `--delete` is used for this sync — it's additive only,
   so nothing on the server is ever removed by this step.
7. On the server: `composer install --no-dev --optimize-autoloader`
   (never `composer update`), wrapped in `php artisan down` /
   `php artisan up` maintenance mode (a `trap` guarantees `up` runs even if
   a step in between fails), `php artisan migrate --force`, then
   `optimize:clear` + `config:cache` + `view:cache`.
8. Storage/bootstrap-cache permissions set narrowly (`chmod 775`/`664` on
   just those two directories — never `777`, never recursive elsewhere).
9. `public_html/storage` symlink created only if missing — an existing
   symlink is never touched.
10. The deployed commit SHA is written to
    `DEPLOY_PATH/.deployed_commit` for rollback reference.
11. Public asset sync — see allowlist below.
12. `curl --fail` against `https://avenuebrand.online/`.

### Why `route:cache` is not run

`routes/platform.php` registers Orchid's dashboard screens/menus, which are
built through Orchid's own routing layer rather than plain closures. That
*may* be cache-compatible, but it hasn't been proven against this specific
screen set, and a failed `route:cache` on a production box is a bad place to
find out. It's deliberately left out of the automated pipeline for now.
Verify manually (`php artisan route:cache` on the server, check for errors,
`php artisan route:clear` if it breaks anything) before adding it back in.

## Public asset sync (public_html)

`public_html` is **not** wiped and re-synced from `public/`. Instead:

- `public/build/` → `public_html/build/` with `rsync -a --delete`, because
  this directory is entirely Vite-generated — nothing hand-placed ever lives
  there, so mirroring it exactly (including deletions) is safe.
- `brand/`, `css/`, `js/`, `images/`, `vendor/` → additive `rsync -a` (no
  `--delete`) into the matching `public_html` directory. These are
  application-owned but the sync is deliberately conservative.
- `favicon.ico`, `robots.txt` → copied individually.
- Everything else already in `public_html` — `.htaccess`,
  `.well-known/`, and any other file not in the list above — is never
  touched by these workflows.

## `.env` handling

The deploy workflow checks (via SSH, existence only, contents never read or
logged) whether `DEPLOY_PATH/.env` exists. If it's missing but a file
literally named `env` exists instead, the deploy **stops** with:

> Production .env configuration requires correction.

Nothing is auto-renamed. Fix it manually on the server (confirm which file
is actually correct before renaming) and re-run.

## `public_html/index.php`

Because the web root (`public_html`) and the application (`repositories/avenue`)
are different directories, `public_html/index.php` must load Laravel from the
repository path rather than the default relative `__DIR__.'/../vendor/autoload.php'`
that ships in `public/index.php`. The smoke-test workflow checks this
structurally (`grep`s for a `repositories/avenue` path reference) but never
modifies the file. If the smoke test reports it's missing that reference,
fix `public_html/index.php` by hand once — the deploy workflow will not
overwrite it automatically.

## Storage symlink

Laravel's usual `php artisan storage:link` creates
`repositories/avenue/public/storage`, which is useless here since that
`public/` is never served directly. The deploy workflow instead ensures
`public_html/storage -> repositories/avenue/storage/app/public` exists,
creating it only if nothing is there yet.

## Queues and scheduler

- No `->withSchedule()` / cron-driven scheduled tasks are currently defined
  in this application (`routes/console.php` only registers the `inspire`
  demo command), so no cPanel cron entry is required today. If a scheduled
  task is added later, add this cron job manually in cPanel (not something
  these workflows can create):
  ```
  * * * * * php /home/avenuebr/repositories/avenue/artisan schedule:run >> /dev/null 2>&1
  ```
- `QUEUE_CONNECTION` is `redis` in production. Shared cPanel hosting
  generally can't run a persistent `queue:work` daemon (no Supervisor
  access). Practical options once queued jobs are actually dispatched:
  1. A cPanel cron entry running
     `php artisan queue:work --stop-when-empty --max-time=50` every minute, or
  2. Switch to `QUEUE_CONNECTION=sync` if jobs are cheap enough to run inline.
  Neither is configured automatically — pick one when queues are actually in use.

## Rollback

Code and database rollback are treated as separate concerns:

- **Code rollback**: re-run `deploy-production.yml` via `workflow_dispatch`
  from a previous known-good commit/tag (`gh workflow run deploy-production.yml --ref <sha-or-tag>`).
  `DEPLOY_PATH/.deployed_commit` on the server records what's currently live.
- **Database rollback**: never automatic. `migrate:rollback` is not run by
  these workflows because not all migrations are safely reversible under
  production data. Handle a bad migration manually, informed by the specific
  migration involved.

## Troubleshooting

- **Smoke test fails at `ssh-keyscan`**: host/port unreachable, or a
  firewall is blocking GitHub Actions' IP ranges — check with your hosting
  provider.
- **`.env` check fails**: see the `.env` handling section above.
- **Health check fails after a deploy**: the app went live but is returning
  a non-2xx/3xx response. Check `storage/logs/laravel.log` on the server —
  maintenance mode will have already been lifted (the `trap cleanup EXIT` in
  the workflow guarantees `php artisan up` runs even on failure), so the
  broken state is visible, not hidden.
- **`route:cache` wanted**: see "Why `route:cache` is not run" above.

## One-time manual steps

These are not automated by the workflows above and need to be done once,
by hand, before (or shortly after) the first automated deploy:

1. Confirm `public_html/index.php` correctly references the
   `repositories/avenue` path (checked, not fixed, by the smoke test).
2. Confirm the public key matching `CPANEL_SSH_KEY` is in
   `~/.ssh/authorized_keys` on the server for `CPANEL_USER`.
3. Run `.github/workflows/test-cpanel-ssh.yml` and review its output before
   ever relying on `deploy-production.yml`.
