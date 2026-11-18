@echo off
setlocal
echo ====================================================
echo  Cyber Essentials Software Inventory Scanner
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

if not exist "%~dp0list-installed-software.js" (
    echo Downloading latest software scanner engine from server...
    powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; (New-Object Net.WebClient).DownloadFile('https://cyber.nexteck.uk/scripts/list-installed-software.js', '%~dp0list-installed-software.js')"
)

echo Scanning installed applications and software inventory...
echo (Read-only: collects name, version, vendor and security status)
echo.

node "%~dp0list-installed-software.js" --json --out "%~dp0my-software.json"

if exist "%~dp0my-software.json" (
    echo.
    echo ====================================================
    echo [SUCCESS] Software scan complete!
    echo File created: "%~dp0my-software.json"
    echo.
    echo Next step: Go to https://cyber.nexteck.uk/company/wizard/8
    echo and click "Upload JSON (Auto-Fill)" to submit!
    echo ====================================================
) else (
    echo.
    echo [ERROR] Scan failed. Please check permissions or run manual registration.
)

echo.
pause
