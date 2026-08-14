#!/usr/bin/env bash
set -euo pipefail

REPO=/var/www/Drab-ALibda-SMS

cd "$REPO"
git fetch --prune origin
git reset --hard origin/main

exec bash "$REPO/scripts/deploy.sh"
