#!/usr/bin/env bash
# Auto-pull for hosting (Timeweb): fetch + ff-only pull when origin is ahead.
# Cron (every minute):
#   * * * * * /bin/bash /FULL/PATH/TO/theme/scripts/auto-pull.sh >/dev/null 2>&1
#
# Force pull with dirty tree:
#   FORCE=1 /bin/bash scripts/auto-pull.sh

set -euo pipefail

REMOTE="${REMOTE:-origin}"
BRANCH="${BRANCH:-main}"
FORCE="${FORCE:-0}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
LOG_DIR="$SCRIPT_DIR/logs"
LOG_FILE="$LOG_DIR/auto-pull.log"

mkdir -p "$LOG_DIR"

log() {
  local level="${2:-INFO}"
  printf '%s [%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$level" "$1" | tee -a "$LOG_FILE"
}

cd "$REPO_ROOT"

if [[ ! -d .git ]]; then
  log "Not a git repo: $REPO_ROOT" "ERROR"
  exit 1
fi

if ! command -v git >/dev/null 2>&1; then
  log "git not found in PATH" "ERROR"
  exit 1
fi

log "Checking ${REMOTE}/${BRANCH} in $REPO_ROOT"

git fetch "$REMOTE" "$BRANCH" --quiet

LOCAL="$(git rev-parse HEAD)"
REMOTE_REF="$(git rev-parse "${REMOTE}/${BRANCH}")"

if [[ "$LOCAL" == "$REMOTE_REF" ]]; then
  log "Already up to date (${LOCAL:0:7})."
  exit 0
fi

BEHIND="$(git rev-list --count "HEAD..${REMOTE}/${BRANCH}")"
AHEAD="$(git rev-list --count "${REMOTE}/${BRANCH}..HEAD")"
log "Local behind=${BEHIND} ahead=${AHEAD} (local=${LOCAL:0:7} remote=${REMOTE_REF:0:7})."

if [[ "$AHEAD" -gt 0 ]]; then
  log "Local has unpushed commits - skip pull." "WARN"
  exit 0
fi

DIRTY="$(git status --porcelain)"
STASHED=0
if [[ -n "$DIRTY" ]]; then
  if [[ "$FORCE" != "1" ]]; then
    log "Working tree dirty - skip pull. Set FORCE=1 to stash/pull/pop." "WARN"
    exit 0
  fi
  log "Stashing local changes before pull..."
  git stash push -u -m "auto-pull $(date '+%Y-%m-%d %H:%M:%S')"
  STASHED=1
fi

cleanup() {
  if [[ "$STASHED" -eq 1 ]]; then
    log "Restoring stashed changes..."
    if ! git stash pop; then
      log "stash pop failed - resolve manually (stash kept)." "ERROR"
    fi
  fi
}
trap cleanup EXIT

log "Pulling ${REMOTE}/${BRANCH} ..."
git pull --ff-only "$REMOTE" "$BRANCH"
NEW_HEAD="$(git rev-parse --short HEAD)"
log "Pull OK -> ${NEW_HEAD}"
