<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * หน้าแรก (public)
 * ตอนนี้เป็น "หน้าตรวจฐานร่วม" ชั่วคราว — คนที่ 3 จะเปลี่ยนเป็นหน้าแรกจริงในขั้นที่ 2
 */
class HomeController extends Controller
{
    function index(): View
    {
        return view('home', [
            'counts' => [
                'users' => User::count(),
                'categories' => Category::count(),
                'organizers' => Organizer::count(),
                'activities' => Activity::count(),
                'activity_user' => DB::table('activity_user')->count(),
            ],
            // ทดสอบ relationship ทุกตัวว่าใช้ได้
            'activities' => Activity::with(['category', 'organizer'])
                ->withCount('participants')
                ->orderBy('code')
                ->get(),
        ]);
    }
}
