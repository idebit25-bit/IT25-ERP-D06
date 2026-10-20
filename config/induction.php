<?php

return [
    // Plazo para las alertas; se puede ajustar con INDUCTION_DELAY_DAYS.
    'delay_days' => max(1, (int) env('INDUCTION_DELAY_DAYS', 7)),
    'mysqldump_path' => env('MYSQLDUMP_PATH', 'C:/xampp/mysql/bin/mysqldump.exe'),
];
