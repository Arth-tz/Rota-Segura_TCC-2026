<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Encerra automaticamente rotas em_andamento esquecidas pelo motorista.
// Em produção (Railway), configure um cron: * * * * * php artisan schedule:run
Schedule::command('rotas:encerrar-esquecidas')->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();
