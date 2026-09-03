<?php

use App\Jobs\SyncAllSignaturesJob;
use App\Jobs\SyncGoogleWorkspaceUsersJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(SyncGoogleWorkspaceUsersJob::class)->dailyAt('02:00');
Schedule::job(SyncAllSignaturesJob::class)->dailyAt('02:30');
