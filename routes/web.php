<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
 * ทุก route ครอบด้วย cache.headers กันกด Back ของ browser แล้วเห็นหน้าเก่าหลัง logout
 * route ของแต่ละคนแยกไฟล์ แล้ว require มารวมที่นี่ → แยกกันทำได้โดยไม่แก้ไฟล์เดียวกัน
 * (ไฟล์นี้เป็นของคนที่ 3 — ถ้าต้องแก้ ให้บอกก่อน)
 */
Route::middleware([
    'cache.headers:no_store;no_cache;must_revalidate;max_age=0',
])->group(static function (): void {
    // หน้าแรก (public)
    Route::get('/', [HomeController::class, 'index'])->name('home');

    require __DIR__ . '/auth.php';           // คนที่ 3: login / logout (+ สมัครสมาชิกในขั้นที่ 2)
    require __DIR__ . '/activities.php';     // คนที่ 1: กิจกรรม + สมัคร/ยกเลิก
    require __DIR__ . '/categories.php';     // คนที่ 2: หมวดหมู่
    require __DIR__ . '/tickets.php';        // คนที่ 2: บัตรเข้าร่วม PDF
    require __DIR__ . '/organizers.php';     // คนที่ 3: ผู้จัด
    require __DIR__ . '/my-activities.php';  // คนที่ 3: กิจกรรมของฉัน
});
