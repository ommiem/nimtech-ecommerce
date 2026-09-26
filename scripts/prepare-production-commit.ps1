Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$repoRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
Set-Location $repoRoot

php artisan test
npm run build

git add -A public/build
git status --short --branch

Write-Host ''
Write-Host 'Production build is complete and public/build is staged.'
Write-Host 'Review any remaining changes, then commit and push main.'
