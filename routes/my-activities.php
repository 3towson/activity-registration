<?php

use App\Http\Controllers\MyActivityController;
use Illuminate\Support\Facades\Route;

/*
 * เจ้าของ: คนที่ 3
 * ชื่อ route ให้ตรงกับตารางใน docs/team/README.md (คนอื่นลิงก์มาด้วยชื่อนี้)
 */
Route::get('/my-activities', [MyActivityController::class, 'list'])
    ->middleware('auth')
    ->name('my-activities.list');
