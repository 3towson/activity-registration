<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ใช้ pagination template ที่ไม่พึ่ง Tailwind (แบบเดียวกับในคาบ) แล้วตกแต่งเองใน app.css
        Paginator::defaultView('pagination::semantic-ui');
        Paginator::defaultSimpleView('pagination::semantic-ui');

        // แสดงชื่อเดือนเป็นภาษาไทย เช่น $activity->date->translatedFormat('j F Y')
        Carbon::setLocale(config('app.locale'));
    }
}
