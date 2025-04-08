<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:notify-missing-expenses')
    ->everyFourHours()
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:process-recurring-expenses')
    ->everyTwoMinutes()
    ->runInBackground()
    ->withoutOverlapping();
