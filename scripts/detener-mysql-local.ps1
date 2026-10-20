param([string]$XamppPath = 'C:\xampp', [int]$Port = 3307)
$ErrorActionPreference = 'Stop'
$projectPath = [IO.Path]::GetFullPath((Split-Path $PSScriptRoot -Parent))
$configPath = Join-Path $projectPath 'storage\app\mysql-local\instancia-local.ini'
$listener = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
if (!$listener) { Write-Output 'La instancia local ya esta detenida.'; return }
$process = Get-CimInstance Win32_Process -Filter "ProcessId = $($listener[0].OwningProcess)"
if (!$process.CommandLine -or !$process.CommandLine.Contains($configPath)) {
    throw 'El puerto pertenece a otra instancia. No se detuvo ningun servicio.'
}
& (Join-Path $XamppPath 'mysql\bin\mysqladmin.exe') --host=127.0.0.1 "--port=$Port" --user=root shutdown
if ($LASTEXITCODE -ne 0) { throw 'No se pudo detener la instancia local.' }
$ownedProcess = Get-Process -Id $process.ProcessId -ErrorAction SilentlyContinue
if ($ownedProcess -and !$ownedProcess.WaitForExit(30000)) {
    throw 'MySQL sigue cerrando sus archivos. No lo reinicies hasta que termine el proceso.'
}
Write-Output 'MySQL local detenido.'
