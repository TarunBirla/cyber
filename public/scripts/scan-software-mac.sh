#!/bin/bash
echo "===================================================="
echo " Cyber Essentials Software Inventory Scanner (macOS)"
echo " Nexteck Limited - London, UK"
echo "===================================================="
echo ""

if ! command -v node &> /dev/null
then
    echo "[ERROR] Node.js is required to run this script."
    echo "Please install Node.js from https://nodejs.org/"
    exit 1
fi

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

echo "Scanning installed applications and software on your Mac..."
echo ""

node "$DIR/list-installed-software.js" --json --out "$DIR/my-software.json"

if [ -f "$DIR/my-software.json" ]; then
    echo ""
    echo "===================================================="
    echo "[SUCCESS] Software scan complete! File created: my-software.json"
    echo "Go to https://cyber.nexteck.uk/company/wizard/8"
    echo "and click 'Upload my-software.json' to auto-register all apps!"
    echo "===================================================="
else
    echo "[ERROR] Scan failed."
fi
