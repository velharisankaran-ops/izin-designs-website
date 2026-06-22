[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

$sshDir = Join-Path $HOME '.ssh'
$configPath = Join-Path $sshDir 'config'
$aliasBlock = @"
Host izin-hostinger
    HostName 82.25.120.135
    User u658377134
    Port 65002
    IdentityFile ~/.ssh/id_ed25519
"@

if (-not (Test-Path $sshDir)) {
    New-Item -ItemType Directory -Path $sshDir | Out-Null
}

if (-not (Test-Path $configPath)) {
    New-Item -ItemType File -Path $configPath | Out-Null
}

$existing = Get-Content -Path $configPath -Raw

if ($existing -match '(?m)^Host\s+izin-hostinger\s*$') {
    Write-Host 'SSH alias already exists: izin-hostinger'
    return
}

if ($existing -and -not $existing.EndsWith("`n")) {
    Add-Content -Path $configPath -Value ''
}

Add-Content -Path $configPath -Value $aliasBlock
Write-Host "Added SSH alias to $configPath"
