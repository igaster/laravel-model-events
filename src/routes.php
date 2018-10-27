<?php

use Illuminate\Support\Facades\Route;

use Igaster\ModelEvents\Controllers\ModelEventController;

Route::middleware(['auth'])->group(function () {
    Route::get('user-events', ModelEventController::class.'@userEvents')->name('modelEvents.authUserEvents');
});
