<?php

use Illuminate\Support\Facades\Schedule;


// Backup the app everyday at 12am
Schedule::command('app:backup')->dailyAt('00:10');
