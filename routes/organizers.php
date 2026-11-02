<?php

use App\Http\Controllers\OrganizerController;
use Illuminate\Routing\Router;   // ใช้ใน group(static function (Router $router) ...) แบบในคาบ
use Illuminate\Support\Arr;      // ใช้ใน Arr::last(...) แบบในคาบ
use Illuminate\Support\Facades\Route;

/*
 * เจ้าของ: คนที่ 3 — ผู้จัด (โครงเดียวกับ categories.php)
 * ทุกหน้าต้อง login · สิทธิ์ ADMIN ตรวจใน Policy · /create ต้องอยู่ก่อน /{organizer}
 */
Route::controller(OrganizerController::class)
    ->middleware('auth')
    ->prefix('/organizers')
    ->name('organizers.')
    ->group(static function (Router $router): void {
        $prefixName = Arr::last($router->getGroupStack())['as'];

        Route::get('/list', 'list')->name('list');
        Route::any('', static fn() => redirect()->route("{$prefixName}list"))->name('index');
        Route::post('', 'create')->name('create');
        Route::get('/create', 'showCreateForm')->name('create-form');

        Route::prefix('/{organizer}')->group(static function (): void {
            Route::get('', 'view')->name('view');
            Route::post('', 'update')->name('update');
            Route::get('/update', 'showUpdateForm')->name('update-form');
            Route::post('/delete', 'delete')->name('delete');
            Route::get('/activities', 'viewActivities')->name('view-activities');
        });
    });
