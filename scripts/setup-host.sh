#!/usr/bin/env bash
# One-time Timeweb setup: GitHub auth + pull + cron.
# Do NOT commit tokens. Pass via environment:
#   GITHUB_TOKEN=your_token bash scripts/setup-host.sh
#
# Optional:
#   GITHUB_USER=Tsunamizxc
#   GITHUB_REPO=https://github.com/Tsunamizxc/project313prod.git

set -euo pipefail

GITHUB_USER="${GITHUB_USER:-Tsunamizxc}"
GITHUB_REPO="${GITHUB_REPO:-https://github.com/Tsunamizxc/project313prod.git}"
BRANCH="${BRANCH:-main}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

cd "$REPO_ROOT"

if [[ -z "${GITHUB_TOKEN:-}" ]]; then
  echo "Set GITHUB_TOKEN (GitHub Personal Access Token)." >&2
  echo "Example: GITHUB_TOKEN=ghp_xxx bash scripts/setup-host.sh" >&2
  exit 1
fi

chmod +x "$SCRIPT_DIR/auto-pull.sh" "$SCRIPT_DIR/install-cron.sh"

# Drop local log changes so pull is not blocked on old commits.
git restore --worktree scripts/logs/auto-pull.log 2>/dev/null || true

git config --local credential.helper store
git config --local user.name "$GITHUB_USER"
git config --local user.email "${GITHUB_USER}@users.noreply.github.com"

AUTH_URL="https://${GITHUB_USER}:${GITHUB_TOKEN}@github.com/Tsunamizxc/project313prod.git"
git remote set-url origin "$AUTH_URL"

echo "Fetching and pulling ${BRANCH} ..."
git fetch origin "$BRANCH"
git pull --ff-only origin "$BRANCH"

# Remote without token in URL; credentials stay in ~/.git-credentials
git remote set-url origin "$GITHUB_REPO"
CREDS_FILE="$HOME/.git-credentials"
if [[ -f "$CREDS_FILE" ]]; then
  grep -v '@github.com' "$CREDS_FILE" > "${CREDS_FILE}.tmp" || true
  mv "${CREDS_FILE}.tmp" "$CREDS_FILE"
fi
printf 'https://%s:%s@github.com\n' "$GITHUB_USER" "$GITHUB_TOKEN" >> "$CREDS_FILE"
chmod 600 "$CREDS_FILE"

bash "$SCRIPT_DIR/install-cron.sh"

echo ""
echo "Done."
echo "Test: bash $SCRIPT_DIR/auto-pull.sh"
echo "Log:  $SCRIPT_DIR/logs/auto-pull.log"
