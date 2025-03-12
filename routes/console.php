<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:notify-missing-expenses')
    ->daily()
    ->runInBackground();

Schedule::command('app:process-recurring-expenses')
    ->daily()
    ->runInBackground();
