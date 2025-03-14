<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:notify-missing-expenses')
    ->dailyAt("7:00")
    ->runInBackground();

Schedule::command('app:process-recurring-expenses')
    ->everyMinute()
    ->runInBackground();
