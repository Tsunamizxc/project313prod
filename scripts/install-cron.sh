#!/usr/bin/env bash
# One-time setup on Timeweb (SSH): chmod + register cron for auto-pull.
# Usage:
#   cd /path/to/theme
#   bash scripts/install-cron.sh
#   bash scripts/install-cron.sh --minutes 2
#   bash scripts/install-cron.sh --uninstall

set -euo pipefail

MINUTES=1
UNINSTALL=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --minutes)
      MINUTES="${2:-1}"
      shift 2
      ;;
    --uninstall)
      UNINSTALL=1
      shift
      ;;
    *)
      echo "Unknown option: $1" >&2
      exit 1
      ;;
  esac
done

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PULL_SCRIPT="$SCRIPT_DIR/auto-pull.sh"
MARKER="# project313-auto-pull"
CRON_LINE="*/${MINUTES} * * * * /bin/bash ${PULL_SCRIPT} >/dev/null 2>&1 ${MARKER}"

chmod +x "$PULL_SCRIPT"

if [[ "$UNINSTALL" -eq 1 ]]; then
  if crontab -l 2>/dev/null | grep -qF "$MARKER"; then
    crontab -l 2>/dev/null | grep -vF "$MARKER" | crontab -
    echo "Removed cron job ($MARKER)."
  else
    echo "Cron job not found."
  fi
  exit 0
fi

if crontab -l 2>/dev/null | grep -qF "$MARKER"; then
  crontab -l 2>/dev/null | grep -vF "$MARKER" | crontab -
fi

(crontab -l 2>/dev/null; echo "$CRON_LINE") | crontab -

echo "Installed cron (every ${MINUTES} min):"
echo "  $CRON_LINE"
echo ""
echo "Repo: $REPO_ROOT"
echo "Test: bash $PULL_SCRIPT"
echo "Log:  $SCRIPT_DIR/logs/auto-pull.log"
echo "Remove: bash $SCRIPT_DIR/install-cron.sh --uninstall"
