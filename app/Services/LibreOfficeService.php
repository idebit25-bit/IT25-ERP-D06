<?php

namespace App\Services;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LibreOfficeService
{
    public function convertToPdf($inputFile, $outputFile)
    {
        // Verificar que el archivo de entrada existe
        if (!file_exists($inputFile)) {
            throw new \Exception('El archivo de entrada no existe.');
        }

        // Comando para convertir el archivo con LibreOffice
        $command = [
            'soffice', 
            '--headless', 
            '--convert-to', 
            'pdf', 
            '--outdir', 
            dirname($outputFile), 
            $inputFile
        ];

        // Ejecutar el comando
        $process = new Process($command);
        $process->run();

        // Verificar si el comando se ejecutó correctamente
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $outputFile;
    }
}
