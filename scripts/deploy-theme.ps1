[CmdletBinding()]
param(
    [string[]]$Path,
    [switch]$DryRun,
    [switch]$NoCachePurge
)

$ErrorActionPreference = 'Stop'

$repoRoot = Split-Path -Parent $PSScriptRoot
$themeName = 'izin-designs-theme'
$hostAlias = 'izin-hostinger'
$serverHome = '/home/u658377134'
$webRoot = "$serverHome/domains/izindesigns.com/public_html"
$remoteThemeDir = "$webRoot/wp-content/themes/$themeName"
$remoteTmpArchive = "$serverHome/$themeName-deploy.tar"
$localArchive = Join-Path $env:TEMP "$themeName-deploy.tar"

$allowedRootFiles = @(
    'archive.php',
    'author.php',
    'category.php',
    'footer.php',
    'front-page.php',
    'functions.php',
    'header.php',
    'home.php',
    'index.php',
    'izin-designs-landing.php',
    'page-3bhk-interior-package-kochi-aluva.php',
    'page-bid-project.php',
    'page-career.php',
    'page-project-status.php',
    'page.php',
    'search.php',
    'single.php',
    'style.css'
)

$allowedDirs = @(
    'frontend',
    'includes',
    'template-parts'
)

function Test-CommandAvailable {
    param([string]$Name)

    return [bool](Get-Command $Name -ErrorAction SilentlyContinue)
}

function Assert-CommandAvailable {
    param([string]$Name)

    if (-not (Test-CommandAvailable -Name $Name)) {
        throw "Required command not found: $Name"
    }
}

function Resolve-DeployItems {
    param([string[]]$RequestedPaths)

    if (-not $RequestedPaths -or $RequestedPaths.Count -eq 0) {
        return $allowedRootFiles + $allowedDirs
    }

    $resolved = New-Object System.Collections.Generic.List[string]

    foreach ($item in $RequestedPaths) {
        $normalized = $item.Replace('/', '\').TrimStart('.\').Trim()
        if ([string]::IsNullOrWhiteSpace($normalized)) {
            continue
        }

        $relative = $normalized.Replace('\', '/')
        $topLevel = ($relative -split '/')[0]

        if ($allowedDirs -contains $topLevel) {
            if (-not $resolved.Contains($relative)) {
                $resolved.Add($relative)
            }
            continue
        }

        if ($allowedRootFiles -contains $relative) {
            if (-not $resolved.Contains($relative)) {
                $resolved.Add($relative)
            }
            continue
        }

        throw "Path '$item' is outside the allowed theme deploy set."
    }

    if ($resolved.Count -eq 0) {
        throw 'No deployable paths were resolved.'
    }

    return $resolved.ToArray()
}

function New-DeployArchive {
    param([string[]]$Items)

    if (Test-Path $localArchive) {
        Remove-Item -LiteralPath $localArchive -Force
    }

    Push-Location $repoRoot
    try {
        $tarArgs = @(
            '-cf',
            $localArchive,
            '--force-local'
        ) + $Items

        & tar.exe @tarArgs

        if ($LASTEXITCODE -ne 0 -or -not (Test-Path $localArchive)) {
            throw 'Failed to create deploy archive.'
        }
    }
    finally {
        Pop-Location
    }
}

function Invoke-Remote {
    param([string]$Command)

    & ssh.exe $hostAlias $Command
    if ($LASTEXITCODE -ne 0) {
        throw "Remote command failed: $Command"
    }
}

Assert-CommandAvailable -Name 'ssh.exe'
Assert-CommandAvailable -Name 'scp.exe'
Assert-CommandAvailable -Name 'tar.exe'

$items = Resolve-DeployItems -RequestedPaths $Path

Write-Host "Deploy target: $remoteThemeDir"
Write-Host "Using SSH host alias: $hostAlias"
Write-Host 'Items to deploy:'
$items | ForEach-Object { Write-Host " - $_" }

if ($DryRun) {
    Write-Host ''
    Write-Host 'Dry run only. No archive created and no files uploaded.'
    return
}

New-DeployArchive -Items $items

try {
    Write-Host ''
    Write-Host 'Uploading deploy archive...'
    & scp.exe $localArchive "${hostAlias}:$remoteTmpArchive"
    if ($LASTEXITCODE -ne 0) {
        throw 'Archive upload failed.'
    }

    Write-Host 'Extracting into live theme directory...'
    Invoke-Remote "mkdir -p '$remoteThemeDir' && tar -xf '$remoteTmpArchive' -C '$remoteThemeDir' && rm -f '$remoteTmpArchive'"

    if (-not $NoCachePurge) {
        Write-Host 'Purging live cache...'
        Invoke-Remote "cd '$webRoot' && wp litespeed-purge all && wp cache flush"
    }

    Write-Host ''
    Write-Host 'Deploy complete.'
    Write-Host "Live theme: $remoteThemeDir"
    Write-Host 'Verify these routes:'
    Write-Host ' - https://izindesigns.com/'
    Write-Host ' - https://izindesigns.com/bid-project/'
    Write-Host ' - https://izindesigns.com/project-status/'
}
finally {
    if (Test-Path $localArchive) {
        Remove-Item -LiteralPath $localArchive -Force
    }
}
