<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:notify-missing-expenses')
    ->everyMinute()
    ->runInBackground();
