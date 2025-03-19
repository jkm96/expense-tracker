<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:notify-missing-expenses')
    ->everyFourHours()
    ->runInBackground();

Schedule::command('app:process-recurring-expenses')
    ->everyMinute()
    ->runInBackground();
