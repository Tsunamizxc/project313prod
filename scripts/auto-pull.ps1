# Auto-pull: fetches origin and pulls when the remote branch is ahead.
# Usage:
#   .\scripts\auto-pull.ps1              # one check (for Task Scheduler)
#   .\scripts\auto-pull.ps1 -Watch       # loop every -IntervalSeconds
#   .\scripts\auto-pull.ps1 -Force       # pull even with local changes (stash -> pull -> pop)

param(
    [switch]$Watch,
    [int]$IntervalSeconds = 60,
    [switch]$Force,
    [string]$Branch = "main",
    [string]$Remote = "origin"
)

$ErrorActionPreference = "Stop"

$Git = "C:\Program Files\Git\bin\git.exe"
if (-not (Test-Path $Git)) {
    $cmd = Get-Command git -ErrorAction SilentlyContinue
    if ($cmd) { $Git = $cmd.Source }
}
if (-not $Git -or -not (Test-Path $Git)) {
    throw "git.exe not found. Install Git for Windows or add it to PATH."
}

$RepoRoot = Split-Path -Parent $PSScriptRoot
if (-not (Test-Path (Join-Path $RepoRoot ".git"))) {
    throw "Not a git repo: $RepoRoot"
}

$LogDir = Join-Path $RepoRoot "scripts\logs"
if (-not (Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir | Out-Null
}
$LogFile = Join-Path $LogDir "auto-pull.log"

function Write-Log {
    param(
        [Parameter(Mandatory = $true)][string]$Message,
        [string]$Level = "INFO"
    )
    $line = "{0} [{1}] {2}" -f (Get-Date -Format "yyyy-MM-dd HH:mm:ss"), $Level, $Message
    Add-Content -Path $LogFile -Value $line -Encoding UTF8
    Write-Host $line
}

function Invoke-Git {
    param([Parameter(ValueFromRemainingArguments = $true)][string[]]$GitArgs)
    & $Git -C $RepoRoot @GitArgs
    if ($LASTEXITCODE -ne 0) {
        throw ("git {0} failed with exit code {1}" -f ($GitArgs -join " "), $LASTEXITCODE)
    }
}

function Get-GitOut {
    param([Parameter(ValueFromRemainingArguments = $true)][string[]]$GitArgs)
    $out = & $Git -C $RepoRoot @GitArgs 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw ("git {0} failed: {1}" -f ($GitArgs -join " "), $out)
    }
    return (($out | Out-String).Trim())
}

function Sync-Once {
    Write-Log -Message ("Checking {0}/{1} ..." -f $Remote, $Branch)

    Invoke-Git fetch $Remote $Branch --quiet

    $local = Get-GitOut rev-parse HEAD
    $remoteRef = Get-GitOut rev-parse ("{0}/{1}" -f $Remote, $Branch)

    if ($local -eq $remoteRef) {
        Write-Log -Message ("Already up to date ({0})." -f $local.Substring(0, 7))
        return
    }

    $behind = Get-GitOut rev-list --count ("HEAD..{0}/{1}" -f $Remote, $Branch)
    $ahead = Get-GitOut rev-list --count ("{0}/{1}..HEAD" -f $Remote, $Branch)
    Write-Log -Message ("Local behind={0} ahead={1} (local={2} remote={3})." -f $behind, $ahead, $local.Substring(0, 7), $remoteRef.Substring(0, 7))

    if ([int]$ahead -gt 0) {
        Write-Log -Message "Local has unpushed commits - skip pull to avoid conflicts." -Level "WARN"
        return
    }

    $dirty = Get-GitOut status --porcelain
    $stashed = $false
    if ($dirty) {
        if (-not $Force) {
            Write-Log -Message "Working tree dirty - skip pull. Re-run with -Force to stash/pull/pop." -Level "WARN"
            return
        }
        Write-Log -Message "Stashing local changes before pull..."
        $stashMsg = "auto-pull {0}" -f (Get-Date -Format "yyyy-MM-dd HH:mm:ss")
        Invoke-Git stash push -u -m $stashMsg
        $stashed = $true
    }

    try {
        Write-Log -Message ("Pulling {0}/{1} ..." -f $Remote, $Branch)
        Invoke-Git pull --ff-only $Remote $Branch
        $newHead = Get-GitOut rev-parse --short HEAD
        Write-Log -Message ("Pull OK -> {0}" -f $newHead)
    }
    finally {
        if ($stashed) {
            Write-Log -Message "Restoring stashed changes..."
            & $Git -C $RepoRoot stash pop
            if ($LASTEXITCODE -ne 0) {
                Write-Log -Message "stash pop failed - resolve manually (stash kept)." -Level "ERROR"
            }
        }
    }
}

Write-Log -Message ("auto-pull started (repo={0} watch={1} interval={2}s)" -f $RepoRoot, $Watch, $IntervalSeconds)

if ($Watch) {
    while ($true) {
        try {
            Sync-Once
        }
        catch {
            Write-Log -Message $_.Exception.Message -Level "ERROR"
        }
        Start-Sleep -Seconds $IntervalSeconds
    }
}
else {
    Sync-Once
}
