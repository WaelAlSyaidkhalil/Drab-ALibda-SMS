# Deployment

## Flow

```
push to main
  └─> GitHub Actions (.github/workflows/deploy.yml)
        └─> SSH to the production server
              └─> forced command on the deploy key runs /var/www/Drab-ALibda-SMS/deploy.sh
                    ├─ git fetch --prune origin && git reset --hard origin/main
                    └─ exec bash scripts/deploy.sh   (build steps, version-controlled)
```

The SSH key on the server is pinned to a forced command, so whatever the workflow
sends as a command is ignored — the server always runs its own `/var/www/Drab-ALibda-SMS/deploy.sh`.

- `deploy.sh` at the repo root on the server is **untracked** (see `.gitignore`) so the
  `git reset --hard` never rewrites the script while it is running.
- `scripts/deploy-bootstrap.sh` is the version-controlled template for that untracked
  bootstrap copy. It does the git work, then hands off to `scripts/deploy.sh`.
- `scripts/deploy.sh` holds the actual build steps and is safe to change in git —
  it is only reached via `exec` after the reset has already completed.

## Required GitHub secrets

| Secret | Value |
| --- | --- |
| `SSH_HOST` | Production server hostname or IP |
| `SSH_USER` | SSH user the deploy key belongs to |
| `SSH_PRIVATE_KEY` | Private key whose public half is pinned to the forced command |

## One-time server activation

Run once, after the first deploy has pulled the `scripts/` directory onto the server,
to switch the server's bootstrap over to the git-managed script:

```bash
sudo cp /var/www/Drab-ALibda-SMS/scripts/deploy-bootstrap.sh /var/www/Drab-ALibda-SMS/deploy.sh && sudo chmod 755 /var/www/Drab-ALibda-SMS/deploy.sh
```

## API documentation

Published with the app on every deploy — no extra build step:

- Swagger UI: `https://<host>/docs`
- OpenAPI 3.1 spec: `https://<host>/openapi.yaml`

The spec is hand-maintained at `darb-alibda-sms/public/openapi.yaml`. When you add or
change a route in `routes/api.php`, update the spec in the same commit.

Note: `routes/web.php` must stay free of Closure-based routes — `php artisan route:cache`
in `scripts/deploy.sh` cannot serialize Closures and will abort the deploy. Use controller
actions instead (see `app/Http/Controllers/PageController.php`).

## Server facts

- Repo: `/var/www/Drab-ALibda-SMS` (Laravel app in `darb-alibda-sms/`)
- PHP: `/usr/bin/php8.4`
- Composer: `/usr/local/bin/composer`
- FPM service: `php8.4-fpm`
- Deploy runs as `root`

## Manual deploy

```bash
sudo /var/www/Drab-ALibda-SMS/deploy.sh
```
