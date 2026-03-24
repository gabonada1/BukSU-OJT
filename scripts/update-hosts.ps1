$ErrorActionPreference = 'Stop'

$repoRoot = Split-Path -Parent $PSScriptRoot
$hostsPath = 'C:\Windows\System32\drivers\etc\hosts'

if (-not ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Host 'Re-launching with Administrator privileges...'
    Start-Process powershell.exe -Verb RunAs -ArgumentList @(
        '-ExecutionPolicy', 'Bypass',
        '-File', ('"' + $PSCommandPath + '"')
    )
    exit
}

Set-Location $repoRoot

$domains = @('buksu.test')

$phpScript = @'
<?php
require getcwd() . '/vendor/autoload.php';
$app = require getcwd() . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Tenant::query()->whereNotNull('domain')->pluck('domain') as $domain) {
    echo $domain, PHP_EOL;
}
'@

$tenantDomains = $phpScript | php

foreach ($domain in $tenantDomains) {
    if ($domain -and $domain.Trim()) {
        $domains += $domain.Trim()
    }
}

$domains = $domains | Sort-Object -Unique

$markerStart = '# BukSU Practicum domains start'
$markerEnd = '# BukSU Practicum domains end'
$existingHosts = Get-Content $hostsPath
$filteredHosts = @()
$insideBlock = $false

foreach ($line in $existingHosts) {
    if ($line -eq $markerStart) {
        $insideBlock = $true
        continue
    }

    if ($line -eq $markerEnd) {
        $insideBlock = $false
        continue
    }

    if (-not $insideBlock) {
        $filteredHosts += $line
    }
}

$filteredHosts += ''
$filteredHosts += $markerStart

foreach ($domain in $domains) {
    $filteredHosts += "127.0.0.1`t$domain"
}

$filteredHosts += $markerEnd

Set-Content -Path $hostsPath -Value $filteredHosts -Encoding ASCII

Write-Host 'Updated hosts file with these domains:'
$domains | ForEach-Object { Write-Host " - $_" }
