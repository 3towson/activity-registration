<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// เจ้าของ: คนที่ 3
Route::prefix('/auth')->group(static function (): void {
    // หน้าเหล่านี้สำหรับคนที่ยังไม่ login (guest) — ถ้า login แล้วจะถูกพาไปหน้าแรก
    Route::middleware('guest')->group(static function (): void {
        Route::controller(LoginController::class)->group(static function (): void {
            // ต้องชื่อ "login" เพราะ middleware auth จะ redirect มาที่ route ชื่อนี้
            Route::get('/login', 'showForm')->name('login');
            Route::post('/login', 'authenticate')->name('authentication');
        });

        Route::controller(RegisterController::class)->group(static function (): void {
            Route::get('/register', 'showForm')->name('register-form');
            Route::post('/register', 'register')->name('register');
        });
    });

    Route::post('/logout', [LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');
});
