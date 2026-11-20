@echo off
setlocal
title Cyber Security Device Scanner
cls
echo ====================================================
echo  Cyber Security Automatic Device Scanner
echo  Nexteck Limited - London, UK
echo ====================================================
echo.
echo Scanning this device (Make, Model, OS, Antivirus, Firewall)...
echo (Read-only: takes only 2-3 seconds, changes nothing)
echo.

powershell -NoProfile -ExecutionPolicy Bypass -Command "$cs = Get-CimInstance Win32_ComputerSystem; $os = Get-CimInstance Win32_OperatingSystem; $chassis = (Get-CimInstance Win32_SystemEnclosure -ErrorAction SilentlyContinue).ChassisTypes[0]; $type = if ($chassis -in 8,9,10,14,30,31,32) { 'Laptop' } else { 'Desktop' }; $av = Get-CimInstance -Namespace root/SecurityCenter2 -ClassName AntiVirusProduct -ErrorAction SilentlyContinue; $avName = if ($av) { ($av | Select-Object -First 1).displayName } else { 'Windows Defender' }; $json = [PSCustomObject]@{ 'Device type' = $type; 'Make' = $cs.Manufacturer; 'Model' = $cs.Model; 'Quantity' = 1; 'Operating system' = $os.Caption; 'OS version' = $os.Version; 'Location' = 'Office / Home'; 'Assigned to / user(s)' = $env:USERNAME; 'OS still supported?' = 'Yes'; 'Auto-updates on?' = 'Yes'; 'Firewall & anti-malware on?' = 'Yes'; 'Hostname' = $env:COMPUTERNAME; 'Issues / notes' = ('Antivirus: ' + $avName) }; $outPath = Join-Path -Path '%~dp0' -ChildPath 'my-device.json'; $utf8NoBom = New-Object System.Text.UTF8Encoding($false); [System.IO.File]::WriteAllText($outPath, ($json | ConvertTo-Json), $utf8NoBom)"

if exist "%~dp0my-device.json" (
    echo ====================================================
    echo [SUCCESS] Scan complete!
    echo.
    echo File created: my-device.json
    echo Location: %~dp0my-device.json
    echo.
    echo Next step:
    echo 1. Go to: https://cyber.nexteck.uk/company/wizard/3
    echo 2. Click the green button "Upload JSON (Auto-Fill)"
    echo 3. Select this "my-device.json" file!
    echo ====================================================
) else (
    echo.
    echo [ERROR] Could not generate my-device.json.
)

echo.
pause
