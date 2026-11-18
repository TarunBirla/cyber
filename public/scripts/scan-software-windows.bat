@echo off
setlocal
echo ====================================================
echo  Cyber Essentials Software Inventory Scanner
echo  Nexteck Limited - London, UK
echo ====================================================
echo.

node -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Node.js is required to run this script.
    echo Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)

echo Scanning installed applications and software inventory...
echo (Read-only: collects name, version, vendor and security status)
echo.

node "%~dp0list-installed-software.js" --json --out "%~dp0my-software.json"

if exist "%~dp0my-software.json" (
    echo.
    echo ====================================================
    echo [SUCCESS] Software scan complete!
    echo File created: my-software.json
    echo.
    echo Next step: Go to https://cyber.nexteck.uk/company/wizard/8
    echo and click "Upload my-software.json" to auto-register all apps!
    echo ====================================================
) else (
    echo.
    echo [ERROR] Scan failed.
)

echo.
pause
