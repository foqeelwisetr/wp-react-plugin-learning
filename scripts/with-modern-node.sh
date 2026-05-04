#!/usr/bin/env sh
# Optional nvm hook so builds use a recent Node when the system default is old.
set -e
export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
if [ -s "$NVM_DIR/nvm.sh" ]; then
	# shellcheck disable=SC1090
	. "$NVM_DIR/nvm.sh"
	if command -v nvm >/dev/null 2>&1; then
		nvm use default >/dev/null 2>&1 || nvm use >/dev/null 2>&1 || true
	fi
fi
exec "$@"
