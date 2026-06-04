<?php

use App\Jobs\EnviarLembrete1h;
use App\Jobs\EnviarLembrete24h;
use App\Jobs\LembrarRetornoCliente;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new EnviarLembrete24h)->dailyAt('08:00');
Schedule::job(new EnviarLembrete1h)->hourly();
Schedule::job(new LembrarRetornoCliente)->weekly()->sundays()->at('09:00');
