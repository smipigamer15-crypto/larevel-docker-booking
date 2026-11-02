param([ValidateSet('production', 'development')][string]$Mode = 'production')

$ErrorActionPreference = 'Stop'
$applicationRoot = Split-Path $PSScriptRoot -Parent
$compilerPath = Join-Path $applicationRoot 'node_modules/@esbuild/win32-x64/esbuild.exe'
if (-not (Test-Path -LiteralPath $compilerPath)) { throw 'Install npm dependencies before building assets.' }

$environmentPath = [IO.Path]::GetTempFileName()
$metadataPath = [IO.Path]::GetTempFileName()
Push-Location $applicationRoot
try {
    $environmentCode = @'
import { loadEnv } from 'vite';
import { writeFileSync } from 'node:fs';
const mode = process.argv[2];
writeFileSync(process.argv[1], JSON.stringify({...loadEnv(mode, process.cwd()), BASE_URL: '/', MODE: mode, DEV: mode === 'development', PROD: mode === 'production', SSR: false}));
'@
    & node --input-type=module -e $environmentCode $environmentPath $Mode
    if ($LASTEXITCODE -ne 0) { throw 'Unable to load public build configuration.' }

    $entryPoints = @(Get-ChildItem -LiteralPath 'resources/css' -Filter '*.css' | ForEach-Object { 'resources/css/' + $_.Name })
    $entryPoints += 'resources/js/app.js'
    $buildArguments = $entryPoints + @(
        '--bundle', '--minify', '--format=esm', '--platform=browser', '--target=es2022',
        '--outdir=public/build/assets', '--entry-names=[name]-[hash]', '--asset-names=[name]-[hash]',
        '--loader:.woff=file', '--loader:.woff2=file', '--loader:.ttf=file', '--loader:.svg=file',
        '--loader:.png=file', '--loader:.jpg=file', '--loader:.webp=file',
        ('--define:import.meta.env=' + [IO.File]::ReadAllText($environmentPath)),
        ('--define:process.env.NODE_ENV=' + (ConvertTo-Json $Mode -Compress)),
        ('--metafile=' + $metadataPath)
    )
    & $compilerPath @buildArguments
    if ($LASTEXITCODE -ne 0) { throw 'Asset compilation failed.' }

    $manifestCode = @'
import { readFileSync, writeFileSync } from 'node:fs';
import path from 'node:path';
const metadata = JSON.parse(readFileSync(process.argv[1], 'utf8'));
const manifest = {};
for (const [output, entry] of Object.entries(metadata.outputs)) {
    if (!entry.entryPoint) continue;
    const source = entry.entryPoint.replaceAll('\\', '/');
    manifest[source] = {file: path.relative('public/build', output).replaceAll('\\', '/'), name: path.basename(source, path.extname(source)), src: source, isEntry: true};
}
writeFileSync('public/build/manifest.json', JSON.stringify(manifest, null, 2) + '\n');
console.log(`Built ${Object.keys(manifest).length} asset entries.`);
'@
    & node --input-type=module -e $manifestCode $metadataPath
    if ($LASTEXITCODE -ne 0) { throw 'Unable to generate the asset manifest.' }
} finally {
    Pop-Location
    Remove-Item -LiteralPath $environmentPath, $metadataPath -ErrorAction SilentlyContinue
}
