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
MAX_TRIES="${MAX_TRIES:-5}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
LOG_DIR="$SCRIPT_DIR/logs"
LOG_FILE="$LOG_DIR/auto-pull.log"

# Shared hosting (Timeweb) often fails HTTPS DNS with:
#   getaddrinfo() thread failed to start
# Prefer a clean env + sync DNS hints + HTTP/1.1 + retries.
export HOME="${HOME:-$(getent passwd "$(id -un)" 2>/dev/null | cut -d: -f6)}"
export HOME="${HOME:-/home/$(id -un)}"
export PATH="/usr/local/bin:/usr/bin:/bin:${PATH:-}"
export LANG="${LANG:-C.UTF-8}"
export LC_ALL="${LC_ALL:-C.UTF-8}"
export GIT_TERMINAL_PROMPT=0
export RES_OPTIONS="${RES_OPTIONS:-single-request-reopen}"
unset LD_LIBRARY_PATH LD_PRELOAD 2>/dev/null || true

mkdir -p "$LOG_DIR"

log() {
  local level="${2:-INFO}"
  printf '%s [%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$level" "$1" | tee -a "$LOG_FILE"
}

# Run git with hosting-friendly options; retry transient DNS/thread errors.
git_cmd() {
  local attempt=1
  local out rc
  local err_file
  err_file="$(mktemp)"

  while [[ $attempt -le $MAX_TRIES ]]; do
    set +e
    out="$(
      git -c http.version=HTTP/1.1 \
          -c http.postBuffer=524288000 \
          -c http.lowSpeedLimit=0 \
          -c http.lowSpeedTime=999999 \
          "$@" 2>"$err_file"
    )"
    rc=$?
    set -e

    if [[ $rc -eq 0 ]]; then
      rm -f "$err_file"
      printf '%s' "$out"
      return 0
    fi

    if grep -Eqi 'getaddrinfo\(\) thread failed|Could not resolve host|Failed to connect|TLS|SSL|timed out|Temporary failure' "$err_file"; then
      log "git $* failed (try ${attempt}/${MAX_TRIES}): $(tr '\n' ' ' <"$err_file")" "WARN"
      sleep $((attempt * 2))
      attempt=$((attempt + 1))
      continue
    fi

    log "git $* failed: $(tr '\n' ' ' <"$err_file")" "ERROR"
    cat "$err_file" >&2 || true
    rm -f "$err_file"
    return "$rc"
  done

  log "git $* failed after ${MAX_TRIES} tries. On Timeweb prefer SSH remote (see scripts/setup-host.sh --ssh)." "ERROR"
  cat "$err_file" >&2 || true
  rm -f "$err_file"
  return 1
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

ORIGIN_URL="$(git remote get-url "$REMOTE" 2>/dev/null || true)"
log "Checking ${REMOTE}/${BRANCH} in $REPO_ROOT (remote=${ORIGIN_URL})"

git_cmd fetch "$REMOTE" "$BRANCH" --quiet >/dev/null

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

# Log file changes must not block auto-pull.
DIRTY="$(git status --porcelain | grep -Ev '^.. scripts/logs/' || true)"
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
git_cmd pull --ff-only "$REMOTE" "$BRANCH" >/dev/null
NEW_HEAD="$(git rev-parse --short HEAD)"
log "Pull OK -> ${NEW_HEAD}"
