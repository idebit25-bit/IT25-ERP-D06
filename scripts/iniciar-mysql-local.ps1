param([string]$XamppPath = 'C:\xampp', [int]$Port = 3307)
$ErrorActionPreference = 'Stop'
$projectPath = [IO.Path]::GetFullPath((Split-Path $PSScriptRoot -Parent))
$dataPath = Join-Path $projectPath 'storage\app\mysql-local'
$configPath = Join-Path $dataPath 'instancia-local.ini'
$mysqlBinary = Join-Path $XamppPath 'mysql\bin\mysqld.exe'
$installer = Join-Path $XamppPath 'mysql\bin\mysql_install_db.exe'
if (!(Test-Path -LiteralPath $mysqlBinary) -or !(Test-Path -LiteralPath $installer)) {
    throw 'No se encontraron los programas MySQL de XAMPP. Ajusta -XamppPath.'
}
if (Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue) {
    throw "El puerto $Port ya esta ocupado. No se inicia ni se modifica otro servidor."
}
$existing = Get-CimInstance Win32_Process -Filter "name = 'mysqld.exe'" |
    Where-Object { $_.CommandLine -and $_.CommandLine.Contains($configPath) }
if ($existing) { throw 'La instancia de este proyecto aun tiene un proceso activo. Espera a que termine antes de iniciarla.' }
New-Item -ItemType Directory -Path $dataPath -Force | Out-Null
if (!(Test-Path -LiteralPath (Join-Path $dataPath 'mysql'))) {
    & $installer "--datadir=$dataPath" "--port=$Port"
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo preparar la instancia local.' }
}
$configText = @"
[mysqld]
basedir=$($XamppPath.Replace('\','/'))/mysql
datadir=$($dataPath.Replace('\','/'))
port=$Port
bind-address=127.0.0.1
skip-log-bin
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
"@
[IO.File]::WriteAllText($configPath, $configText, [Text.UTF8Encoding]::new($false))
$argument = '--defaults-file="' + $configPath + '" --console'
$process = Start-Process -FilePath $mysqlBinary -ArgumentList $argument -WindowStyle Hidden -PassThru `
    -RedirectStandardOutput (Join-Path $dataPath 'mysql.stdout.log') `
    -RedirectStandardError (Join-Path $dataPath 'mysql.stderr.log')
for ($attempt = 0; $attempt -lt 100; $attempt++) {
    if ($process.HasExited) { throw 'MySQL no inicio. Revisa mysql.stderr.log en storage/app/mysql-local.' }
    if (Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue) {
        Write-Output "MySQL local iniciado en 127.0.0.1:$Port. Datos dentro de este proyecto."
        Write-Output 'Para detenerlo ejecuta scripts/detener-mysql-local.ps1.'
        return
    }
    Start-Sleep -Milliseconds 200
}
throw 'MySQL tarda en iniciar. Revisa el registro antes de intentar otro inicio.'
