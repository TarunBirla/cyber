@echo off
setlocal
echo ====================================================
echo  Cyber Essentials Automatic Device Scanner
echo  Nexteck Limited - London, UK
echo ====================================================
echo.

node -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Node.js is required to run this scanner.
    echo Please install Node.js from https://nodejs.org/ (LTS version)
    echo Once installed, double-click this file again.
    pause
    exit /b 1
)

if not exist "%~dp0device-register.js" (
    echo Downloading latest scanner engine from server...
    powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; (New-Object Net.WebClient).DownloadFile('https://cyber.nexteck.uk/scripts/device-register.js', '%~dp0device-register.js')"
)

echo Scanning your device hardware, operating system, and security settings...
echo (This takes only a few seconds, read-only: changes nothing)
echo.

node "%~dp0device-register.js" --json --out "%~dp0my-device.json"

if exist "%~dp0my-device.json" (
    echo.
    echo ====================================================
    echo [SUCCESS] Scan complete!
    echo File created: "%~dp0my-device.json"
    echo.
    echo Next step: Go to https://cyber.nexteck.uk/company/wizard/3
    echo and click "Upload JSON (Auto-Fill)" to submit!
    echo ====================================================
) else (
    echo.
    echo [ERROR] Scan failed. Please check permissions or run manual registration.
)

echo.
pause
