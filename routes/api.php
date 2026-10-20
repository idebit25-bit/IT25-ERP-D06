<?php

use App\Http\Controllers\Integration\InductionResultController;
use App\Http\Middleware\AuthenticateA11;
use Illuminate\Support\Facades\Route;

Route::get('/v1/induccion', InductionResultController::class)
    ->middleware(['throttle:60,1', AuthenticateA11::class]);

