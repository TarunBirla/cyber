@echo off
setlocal
title Cyber Essentials Software Inventory Scanner
cls
echo ====================================================
echo  Cyber Essentials Software Inventory Scanner
echo  Nexteck Limited - London, UK
echo ====================================================
echo.
echo Scanning installed software and applications...
echo (Read-only: takes only 2-4 seconds, changes nothing)
echo.

powershell -NoProfile -ExecutionPolicy Bypass -Command "$paths = @('HKLM:\Software\Microsoft\Windows\CurrentVersion\Uninstall\*', 'HKLM:\Software\Wow6432Node\Microsoft\Windows\CurrentVersion\Uninstall\*', 'HKCU:\Software\Microsoft\Windows\CurrentVersion\Uninstall\*'); $items = Get-ItemProperty -Path $paths -ErrorAction SilentlyContinue | Where-Object { $_.DisplayName -and $_.SystemComponent -ne 1 -and $_.ParentKeyName -eq $null } | Select-Object @{N='name';E={$_.DisplayName}}, @{N='version';E={if($_.DisplayVersion){$_.DisplayVersion}else{'Latest'}}}, @{N='publisher';E={$_.Publisher}} | Sort-Object name -Unique; $out = [PSCustomObject]@{ machine = $env:COMPUTERNAME; os = (Get-CimInstance Win32_OperatingSystem).Caption; count = $items.Count; software = $items }; $outPath = Join-Path -Path '%~dp0' -ChildPath 'my-software.json'; $utf8NoBom = New-Object System.Text.UTF8Encoding($false); [System.IO.File]::WriteAllText($outPath, ($out | ConvertTo-Json -Depth 4), $utf8NoBom)"

if exist "%~dp0my-software.json" (
    echo ====================================================
    echo [SUCCESS] Software scan complete!
    echo.
    echo File created: my-software.json
    echo Location: %~dp0my-software.json
    echo.
    echo Next step:
    echo 1. Go to: https://cyber.nexteck.uk/company/wizard/8
    echo 2. Click the green button "Upload JSON (Auto-Fill)"
    echo 3. Select this "my-software.json" file!
    echo ====================================================
) else (
    echo.
    echo [ERROR] Could not generate my-software.json.
)

echo.
pause
