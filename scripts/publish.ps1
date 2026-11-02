$ErrorActionPreference = 'Stop'
$repositoryRoot = Split-Path $PSScriptRoot -Parent
$repositoryUrl = 'https://github.com/smipigamer15-crypto/larevel-docker-booking.git'

function Invoke-ProjectGit {
    & git @args
    if ($LASTEXITCODE -ne 0) {
        throw "Git failed: $($args[0]). No force push was attempted."
    }
}

Push-Location $repositoryRoot
try {
    if (-not (Test-Path -LiteralPath '.git')) {
        Invoke-ProjectGit init -b main
    }

    $branch = & git branch --show-current
    if ($LASTEXITCODE -ne 0 -or $branch -ne 'main') {
        throw 'Run this publishing script from a repository on the main branch.'
    }

    $remotes = @(& git remote)
    if ($LASTEXITCODE -ne 0) { throw 'Unable to read Git remotes.' }
    if ($remotes -contains 'origin') {
        $origin = & git remote get-url origin
        if ($LASTEXITCODE -ne 0) { throw 'Unable to read the origin URL.' }
        if (($origin.TrimEnd('/') -replace '\.git$', '') -ne ($repositoryUrl -replace '\.git$', '')) {
            throw 'The origin remote points to a different repository. Review it before publishing.'
        }
    } else {
        Invoke-ProjectGit remote add origin $repositoryUrl
    }

    Invoke-ProjectGit add --all
    & git diff --cached --quiet
    $changesExitCode = $LASTEXITCODE
    if ($changesExitCode -eq 1) {
        Invoke-ProjectGit commit -m 'Initial commit: multilingual Laravel booking system'
    } elseif ($changesExitCode -ne 0) {
        throw 'Unable to inspect staged changes.'
    }

    Invoke-ProjectGit push --set-upstream origin main
} finally {
    Pop-Location
}
