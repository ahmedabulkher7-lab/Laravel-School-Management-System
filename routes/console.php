<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('notifications:remind-teachers')->dailyAt('20:00');
Schedule::command('notifications:remind-weekly-plans')->weeklyOn(5, '08:00');
