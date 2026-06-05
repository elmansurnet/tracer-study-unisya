<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// OTP cleanup — setiap jam
Schedule::command('otp:cleanup')->hourly()->withoutOverlapping();

// Token cleanup — setiap jam
Schedule::command('tokens:cleanup')->hourly()->withoutOverlapping();

// Tracer Study reminders — setiap hari jam 08:00 WIB (UTC+7 = 01:00 UTC)
Schedule::command('tracer:send-reminders')->dailyAt('01:00')->withoutOverlapping();