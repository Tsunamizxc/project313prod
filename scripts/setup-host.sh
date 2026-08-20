#!/usr/bin/env bash
# One-time Timeweb setup: GitHub auth + pull + cron.
#
# HTTPS + token (may hit getaddrinfo errors on shared hosting):
#   GITHUB_TOKEN=ghp_xxx bash scripts/setup-host.sh
#
# Preferred on Timeweb — SSH deploy key (more reliable):
#   bash scripts/setup-host.sh --ssh
# Then add the printed public key to:
#   GitHub repo → Settings → Deploy keys → Allow read access

set -euo pipefail

GITHUB_USER="${GITHUB_USER:-Tsunamizxc}"
GITHUB_REPO_HTTPS="${GITHUB_REPO_HTTPS:-https://github.com/Tsunamizxc/project313prod.git}"
GITHUB_REPO_SSH="${GITHUB_REPO_SSH:-git@github.com:Tsunamizxc/project313prod.git}"
BRANCH="${BRANCH:-main}"
MODE="https"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --ssh) MODE="ssh"; shift ;;
    --https) MODE="https"; shift ;;
    *)
      echo "Unknown option: $1" >&2
      exit 1
      ;;
  esac
done

cd "$REPO_ROOT"

export HOME="${HOME:-$(getent passwd "$(id -un)" 2>/dev/null | cut -d: -f6)}"
export HOME="${HOME:-/home/$(id -un)}"
export PATH="/usr/local/bin:/usr/bin:/bin:${PATH:-}"
export RES_OPTIONS="${RES_OPTIONS:-single-request-reopen}"
export GIT_TERMINAL_PROMPT=0
unset LD_LIBRARY_PATH LD_PRELOAD 2>/dev/null || true

chmod +x "$SCRIPT_DIR/auto-pull.sh" "$SCRIPT_DIR/install-cron.sh" "$SCRIPT_DIR/setup-host.sh"

git restore --worktree scripts/logs/auto-pull.log 2>/dev/null || true

git_try() {
  git -c http.version=HTTP/1.1 "$@"
}

if [[ "$MODE" == "ssh" ]]; then
  mkdir -p "$HOME/.ssh"
  chmod 700 "$HOME/.ssh"
  KEY="$HOME/.ssh/project313_github"
  if [[ ! -f "$KEY" ]]; then
    ssh-keygen -t ed25519 -f "$KEY" -N "" -C "project313-timeweb-deploy"
  fi
  chmod 600 "$KEY"
  chmod 644 "${KEY}.pub"

  cat > "$HOME/.ssh/config" <<EOF
Host github.com
  HostName github.com
  User git
  IdentityFile ${KEY}
  IdentitiesOnly yes
  StrictHostKeyChecking accept-new
EOF
  chmod 600 "$HOME/.ssh/config"

  echo ""
  echo "=== Add this PUBLIC key as a Deploy key on GitHub ==="
  echo "Repo → Settings → Deploy keys → Add deploy key (read-only is enough)"
  echo "------------------------------------------------------"
  cat "${KEY}.pub"
  echo "------------------------------------------------------"
  echo ""
  read -r -p "Press Enter after the deploy key is added on GitHub..."

  ssh -T git@github.com 2>&1 || true

  git remote set-url origin "$GITHUB_REPO_SSH"
  echo "Fetching and pulling ${BRANCH} via SSH ..."
  git_try fetch origin "$BRANCH"
  git_try pull --ff-only origin "$BRANCH"
else
  if [[ -z "${GITHUB_TOKEN:-}" ]]; then
    echo "Set GITHUB_TOKEN or use: bash scripts/setup-host.sh --ssh" >&2
    exit 1
  fi

  git config --local credential.helper store
  git config --local user.name "$GITHUB_USER"
  git config --local user.email "${GITHUB_USER}@users.noreply.github.com"

  AUTH_URL="https://${GITHUB_USER}:${GITHUB_TOKEN}@github.com/Tsunamizxc/project313prod.git"
  git remote set-url origin "$AUTH_URL"

  echo "Fetching and pulling ${BRANCH} via HTTPS ..."
  if ! git_try fetch origin "$BRANCH"; then
    echo ""
    echo "HTTPS failed (often Timeweb getaddrinfo). Prefer SSH:" >&2
    echo "  bash scripts/setup-host.sh --ssh" >&2
    exit 1
  fi
  git_try pull --ff-only origin "$BRANCH"

  git remote set-url origin "$GITHUB_REPO_HTTPS"
  CREDS_FILE="$HOME/.git-credentials"
  if [[ -f "$CREDS_FILE" ]]; then
    grep -v '@github.com' "$CREDS_FILE" > "${CREDS_FILE}.tmp" || true
    mv "${CREDS_FILE}.tmp" "$CREDS_FILE"
  fi
  printf 'https://%s:%s@github.com\n' "$GITHUB_USER" "$GITHUB_TOKEN" >> "$CREDS_FILE"
  chmod 600 "$CREDS_FILE"
fi

bash "$SCRIPT_DIR/install-cron.sh"

echo ""
echo "Done. Mode=${MODE}"
echo "Test: bash $SCRIPT_DIR/auto-pull.sh"
echo "Log:  $SCRIPT_DIR/logs/auto-pull.log"
