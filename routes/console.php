<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('notifications:remind-teachers')->dailyAt('20:00');
