<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Organizer;
use Illuminate\View\View;

/**
 * หน้าแรก (public) — กิจกรรมที่ใกล้จะจัด, ตัวเลขสรุป, ปุ่มหมวดหมู่
 */
class HomeController extends Controller
{
    const int MAX_UPCOMING = 6;

    function index(): View
    {
        return view('home', [
            'counts' => [
                'activities' => Activity::count(),
                'upcoming' => Activity::whereDate('date', '>=', today())->count(),
                'categories' => Category::count(),
                'organizers' => Organizer::count(),
            ],
            'categories' => Category::orderBy('code')->get(),
            'activities' => Activity::with(['category', 'organizer'])
                ->withCount('participants')
                ->whereDate('date', '>=', today())
                ->orderBy('date')
                ->orderBy('code')
                ->limit(self::MAX_UPCOMING)
                ->get(),
        ]);
    }
}
