<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

/*
 * เจ้าของ: คนที่ 1 — กิจกรรม
 * ชื่อ route ให้ตรงกับตารางใน docs/team/README.md (คนอื่นลิงก์มาด้วยชื่อนี้)
 *
 * ⚠️ ลำดับสำคัญ 2 ข้อ:
 *   1) ANY '' (index) ต้องอยู่ "ก่อน" POST '' (create)
 *   2) กลุ่ม auth (มี GET /create) ต้องอยู่ "ก่อน" GET /{activity}
 */
Route::controller(ActivityController::class)
    ->prefix('/activities')
    ->name('activities.')
    ->group(static function (): void {
        Route::get('/list', 'list')->name('list');
        Route::any('', static fn () => redirect()->route('activities.list'))
            ->name('index');

        Route::middleware('auth')->group(static function (): void {
            Route::post('', 'create')->name('create');
            Route::get('/create', 'showCreateForm')->name('create-form');
            Route::prefix('/{activity}')->group(static function (): void {
                Route::post('', 'update')->name('update');
                Route::get('/update', 'showUpdateForm')->name('update-form');
                Route::post('/delete', 'delete')->name('delete');
                Route::get('/participants', 'viewParticipants')->name('view-participants');
                Route::post('/register', 'register')->name('register');
                Route::post('/cancel', 'cancel')->name('cancel');
            });
        });

        Route::get('/{activity}', 'view')->name('view');
    });
