<?php

// use App\Http\Controllers\ActivityController;   ← เปิดเมื่อสร้าง controller แล้ว
use Illuminate\Routing\Router;   // ใช้ใน group(static function (Router $router) ...) แบบในคาบ
use Illuminate\Support\Arr;      // ใช้ใน Arr::last(...) แบบในคาบ
use Illuminate\Support\Facades\Route;

/*
 * เจ้าของ: คนที่ 1 — กิจกรรม
 * ชื่อ route ให้ตรงกับตารางใน docs/team/README.md (คนอื่นลิงก์มาด้วยชื่อนี้)
 *
 * ขั้นที่ 2: เขียนแบบเดียวกับ /products ใน web.php ของ my-db ต่างกันตรงที่
 *   list / index / view เป็นหน้า public (อยู่นอก middleware auth)
 *
 * ⚠️ ลำดับสำคัญ 2 ข้อ (ลำดับด้านล่างถูกแล้ว):
 *   1) ANY '' (index) ต้องอยู่ "ก่อน" POST '' (create) เหมือนในคาบ
 *      ถ้าสลับกัน ANY จะทับ POST → กดสร้างกิจกรรมแล้วโดน redirect แทนการบันทึก
 *   2) กลุ่ม auth (มี GET /create) ต้องอยู่ "ก่อน" GET /{activity}
 *      ไม่งั้นคำว่า "create" ใน /activities/create จะถูกจับเป็นรหัสกิจกรรม
 *
 * Route::controller(ActivityController::class)
 *     ->prefix('/activities')
 *     ->name('activities.')
 *     ->group(static function (Router $router): void {
 *         $prefixName = Arr::last($router->getGroupStack())['as'];
 *
 *         // public (ไม่ต้อง login)
 *         Route::get('/list', 'list')->name('list');
 *         Route::any('', static fn() => redirect()->route("{$prefixName}list"))->name('index');
 *
 *         Route::middleware('auth')->group(static function (): void {
 *             Route::post('', 'create')->name('create');
 *             Route::get('/create', 'showCreateForm')->name('create-form');
 *             Route::prefix('/{activity}')->group(static function (): void {
 *                 Route::post('', 'update')->name('update');
 *                 Route::get('/update', 'showUpdateForm')->name('update-form');
 *                 Route::post('/delete', 'delete')->name('delete');
 *                 Route::get('/participants', 'viewParticipants')->name('view-participants');
 *                 Route::post('/register', 'register')->name('register');
 *                 Route::post('/cancel', 'cancel')->name('cancel');
 *             });
 *         });
 *
 *         // public (ไม่ต้อง login) — ต้องอยู่หลังกลุ่ม auth
 *         Route::get('/{activity}', 'view')->name('view');
 *     });
 */
