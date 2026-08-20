# Registers a Windows Scheduled Task that runs auto-pull every N minutes.
# Run once (as Administrator if you want "SYSTEM" / all-users task):
#   powershell -ExecutionPolicy Bypass -File .\scripts\install-auto-pull-task.ps1
#   powershell -ExecutionPolicy Bypass -File .\scripts\install-auto-pull-task.ps1 -Minutes 2
#   powershell -ExecutionPolicy Bypass -File .\scripts\install-auto-pull-task.ps1 -Uninstall

param(
    [int]$Minutes = 1,
    [switch]$Uninstall,
    [string]$TaskName = "project313-auto-pull"
)

$ErrorActionPreference = "Stop"

$ScriptPath = Join-Path $PSScriptRoot "auto-pull.ps1"
if (-not (Test-Path $ScriptPath)) {
    throw "Missing script: $ScriptPath"
}

$existing = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
if ($existing) {
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
    Write-Host "Removed existing task: $TaskName"
}

if ($Uninstall) {
    Write-Host "Uninstalled $TaskName"
    exit 0
}

if ($Minutes -lt 1) { $Minutes = 1 }

$action = New-ScheduledTaskAction `
    -Execute "powershell.exe" `
    -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$ScriptPath`""

# RepetitionDuration must be a finite ISO duration (MaxValue is rejected on some Windows builds).
$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(1) `
    -RepetitionInterval (New-TimeSpan -Minutes $Minutes) `
    -RepetitionDuration (New-TimeSpan -Days 3650)

$settings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -MultipleInstances IgnoreNew `
    -ExecutionTimeLimit (New-TimeSpan -Minutes 10)

$principal = New-ScheduledTaskPrincipal `
    -UserId $env:USERNAME `
    -LogonType Interactive `
    -RunLevel Limited

Register-ScheduledTask `
    -TaskName $TaskName `
    -Action $action `
    -Trigger $trigger `
    -Settings $settings `
    -Principal $principal `
    -Description "Auto git pull for project313 when origin/main has new commits" `
    | Out-Null

Write-Host "Installed scheduled task '$TaskName' (every $Minutes min)."
Write-Host "Manual run:  powershell -ExecutionPolicy Bypass -File `"$ScriptPath`""
Write-Host "Watch mode:  powershell -ExecutionPolicy Bypass -File `"$ScriptPath`" -Watch"
Write-Host "Uninstall:   powershell -ExecutionPolicy Bypass -File `"$PSCommandPath`" -Uninstall"
Write-Host "Log file:    $(Join-Path $PSScriptRoot 'logs\auto-pull.log')"
