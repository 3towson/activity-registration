<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::controller(TicketController::class)
    ->middleware('auth')
    ->group(static function (): void {
        Route::get('/activities/{activity}/ticket', 'download')->name('activities.ticket');
    });
