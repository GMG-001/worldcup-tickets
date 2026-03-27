<?php

use App\Jobs\ExpireReservationsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ExpireReservationsJob())->everyMinute();
