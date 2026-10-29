<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Routing\Router;   // ใช้ใน group(static function (Router $router) ...) แบบในคาบ
use Illuminate\Support\Arr;      // ใช้ใน Arr::last(...) แบบในคาบ
use Illuminate\Support\Facades\Route;

/*
 * เจ้าของ: คนที่ 2 — หมวดหมู่
 * ชื่อ route ให้ตรงกับตารางใน docs/team/README.md (คนอื่นลิงก์มาด้วยชื่อนี้)
 *
 * ขั้นที่ 2: เขียนแบบเดียวกับ /categories ใน web.php ของ my-db (ไม่ต้องใส่ Route::post('', 'list') ที่ซ้ำ)
 *
 * Route::controller(CategoryController::class)
 *     ->middleware('auth')                         // ทุกหน้าต้อง login · สิทธิ์ ADMIN ตรวจใน Policy
 *     ->prefix('/categories')
 *     ->name('categories.')
 *     ->group(static function (Router $router): void {
 *         $prefixName = Arr::last($router->getGroupStack())['as'];
 *         Route::get('/list', 'list')->name('list');
 *         Route::any('', static fn() => redirect()->route("{$prefixName}list"))->name('index');
 *         Route::post('', 'create')->name('create');
 *         Route::get('/create', 'showCreateForm')->name('create-form');   // ก่อน /{category}
 *         Route::prefix('/{category}')->group(static function (): void {
 *             Route::get('', 'view')->name('view');
 *             Route::post('', 'update')->name('update');
 *             Route::get('/update', 'showUpdateForm')->name('update-form');
 *             Route::post('/delete', 'delete')->name('delete');
 *             Route::get('/activities', 'viewActivities')->name('view-activities');
 *         });
 *     });
 */


Route::controller(CategoryController::class)
    ->middleware('auth')
    ->prefix('/categories')
    ->name('categories.')
    ->group(static function (Router $router): void {
        $prefixName = Arr::last($router->getGroupStack())['as'];

        Route::get('/list', 'list')->name('list');
        Route::any('', static fn() => redirect()->route("{$prefixName}list"))->name('index');
    });
