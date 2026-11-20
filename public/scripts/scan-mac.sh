#!/bin/bash
echo "===================================================="
echo " Cyber Security Automatic Device Scanner (macOS)"
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

echo "Scanning your Mac hardware, macOS version, and security settings..."
echo "(Read-only: changes nothing)"
echo ""

node "$DIR/device-register.js" --json --out "$DIR/my-device.json"

if [ -f "$DIR/my-device.json" ]; then
    echo ""
    echo "===================================================="
    echo "[SUCCESS] Scan complete! File created: my-device.json"
    echo "Go to https://cyber.nexteck.uk/company/wizard/3"
    echo "and click 'Upload my-device.json' to auto-populate all fields!"
    echo "===================================================="
else
    echo "[ERROR] Scan failed."
fi
