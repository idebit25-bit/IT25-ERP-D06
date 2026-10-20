$ErrorActionPreference = 'Stop'
$projectPath = [IO.Path]::GetFullPath((Split-Path $PSScriptRoot -Parent))
Set-Location -LiteralPath $projectPath
$configPath = Join-Path $projectPath 'storage\app\mysql-local\instancia-local.ini'
$listener = Get-NetTCPConnection -LocalPort 3307 -State Listen -ErrorAction SilentlyContinue
if ($listener) {
    $owner = Get-CimInstance Win32_Process -Filter "ProcessId = $($listener[0].OwningProcess)"
    if (!$owner.CommandLine -or !$owner.CommandLine.Contains($configPath)) {
        throw 'El puerto 3307 pertenece a otra instancia. No se modifico ese servicio.'
    }
    Write-Output 'MySQL de este proyecto ya esta iniciado.'
} else {
    & (Join-Path $PSScriptRoot 'iniciar-mysql-local.ps1')
}
if (Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue) {
    Write-Output 'Ya hay un servidor en http://127.0.0.1:8000. No se inicio otro.'
    return
}
Write-Output 'Abre http://127.0.0.1:8000. Ctrl+C detiene Laravel; MySQL se detiene con detener-mysql-local.ps1.'
& php artisan serve --host=127.0.0.1 --port=8000
if ($LASTEXITCODE -ne 0) { throw 'No se pudo iniciar Laravel.' }
