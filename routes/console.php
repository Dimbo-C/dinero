<?php

use Illuminate\Foundation\Inspiring;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('clearlog', function () {

    $logDir = __DIR__ . "/../storage/logs/laravel.log";
    unlink($logDir);
    touch($logDir);
    chmod($logDir, 0777);

})->describe('clear log (obliterate log file)');
