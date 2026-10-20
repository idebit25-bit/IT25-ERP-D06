<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupInduction extends Command
{
    protected $signature = 'induccion:respaldo';
    protected $description = 'Respalda la base MySQL y los archivos subidos de la instalación local';

    public function handle(): int
    {
        $connection = config('database.connections.'.config('database.default'));
        if (!app()->environment('local') || ($connection['driver'] ?? '') !== 'mysql'
            || !in_array($connection['host'] ?? '', ['127.0.0.1', 'localhost', '::1'], true)
            || !empty($connection['url']) || !empty($connection['unix_socket'])) {
            $this->error('Este comando solo permite MySQL local, sin URL ni socket alternativo.');
            return self::FAILURE;
        }
        $directory = storage_path('app/private/backups');
        File::ensureDirectoryExists($directory);
        $base = $directory.'/induccion-'.now()->format('Ymd-His').'-'.bin2hex(random_bytes(3));
        $temporary = $base.'.sql.part';
        $zipTemporary = $base.'.zip.part';
        $output = fopen($temporary, 'wb');
        if (!$output) {
            $this->error('No se pudo crear el archivo de respaldo.');
            return self::FAILURE;
        }
        try {
            $process = new Process([
                config('induction.mysqldump_path'), '--protocol=TCP', '--host='.$connection['host'],
                '--port='.$connection['port'], '--user='.$connection['username'],
                '--default-character-set=utf8mb4', '--single-transaction', '--quick', '--skip-lock-tables',
                '--routines', '--triggers', '--events', $connection['database'],
            ], null, ['MYSQL_PWD' => $connection['password'] ?? '']);
            $process->setTimeout(300);
            $process->mustRun(function ($type, $text) use ($output) {
                if ($type === Process::OUT && fwrite($output, $text) !== strlen($text)) {
                    throw new \RuntimeException('No se pudo escribir todo el respaldo.');
                }
            });
            fclose($output);
            $output = null;
            if (!rename($temporary, $base.'.sql')) throw new \RuntimeException('No se pudo terminar el archivo SQL.');

            if (!class_exists(ZipArchive::class)) throw new \RuntimeException('Se requiere la extensión ZIP de PHP.');
            $zip = new ZipArchive();
            if ($zip->open($zipTemporary, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
                throw new \RuntimeException('No se pudo crear el respaldo de archivos.');
            }
            if (!$zip->addFile($base.'.sql', 'base_de_datos.sql')) throw new \RuntimeException('No se pudo incluir el SQL.');
            $uploads = storage_path('app/public');
            if (is_dir($uploads)) {
                $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($uploads, \FilesystemIterator::SKIP_DOTS));
                foreach ($iterator as $file) {
                    if ($file->isFile() && !$file->isLink()) {
                        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($uploads) + 1));
                        if (!$zip->addFile($file->getPathname(), 'archivos/'.$relative)) throw new \RuntimeException('No se pudo incluir un archivo.');
                    }
                }
            }
            if (!$zip->close()) throw new \RuntimeException('No se pudo terminar el archivo ZIP.');
            if (!rename($zipTemporary, $base.'.zip')) throw new \RuntimeException('No se pudo terminar el respaldo ZIP.');
            $this->info('Respaldo creado: '.$base.'.zip');
            $this->line('También se conserva el SQL. Los respaldos no se borran automáticamente.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            if (is_resource($output)) fclose($output);
            if (is_file($temporary)) unlink($temporary);
            if (is_file($zipTemporary)) unlink($zipTemporary);
            $this->error('Respaldo incompleto: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
