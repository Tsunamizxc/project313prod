#!/usr/bin/env bash
# Setup cron for auto-pull.
# On Timeweb shared hosting crontab is blocked — script prints a line for Control Panel.
#
# Usage:
#   bash scripts/install-cron.sh
#   bash scripts/install-cron.sh --minutes 2

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

HOME_DIR="${HOME:-$(getent passwd "$(id -un)" 2>/dev/null | cut -d: -f6)}"
HOME_DIR="${HOME_DIR:-/home/$(id -un)}"
USER_NAME="$(id -un)"

# Command for Timeweb Control Panel cron (no crontab headers needed there).
PANEL_CMD="cd ${REPO_ROOT} && /usr/bin/env -i HOME=${HOME_DIR} USER=${USER_NAME} PATH=/usr/local/bin:/usr/bin:/bin RES_OPTIONS=single-request-reopen LANG=C.UTF-8 /bin/bash ${PULL_SCRIPT}"
CRON_LINE="*/${MINUTES} * * * * ${PANEL_CMD} >/dev/null 2>&1 ${MARKER}"

chmod +x "$PULL_SCRIPT"

print_panel_help() {
  echo ""
  echo "Timeweb: crontab via SSH is disabled."
  echo "Add the job in Control Panel → Cron / Планировщик задач:"
  echo ""
  echo "  Schedule: every ${MINUTES} minute(s)"
  echo "  Command:"
  echo "  ${PANEL_CMD}"
  echo ""
  echo "Test now: bash ${PULL_SCRIPT}"
  echo "Log file: ${SCRIPT_DIR}/logs/auto-pull.log"
}

if [[ "$UNINSTALL" -eq 1 ]]; then
  if crontab -l 2>/dev/null | grep -qF "$MARKER"; then
    crontab -l 2>/dev/null | grep -vF "$MARKER" | crontab - || true
    echo "Removed crontab entry ($MARKER) if present."
  fi
  print_panel_help
  echo "In Control Panel: delete the cron job with this command manually."
  exit 0
fi

set +e
CRON_ERR="$( (crontab -l 2>/dev/null | grep -vF "$MARKER"; echo "$CRON_LINE") | crontab - 2>&1 )"
CRON_RC=$?
set -e

if [[ $CRON_RC -eq 0 ]]; then
  echo "Installed crontab (every ${MINUTES} min):"
  echo "  $CRON_LINE"
else
  echo "$CRON_ERR"
  print_panel_help
fi
