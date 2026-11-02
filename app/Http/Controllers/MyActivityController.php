<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class MyActivityController extends Controller
{
    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('viewMine', Activity::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $showPast = ($request->getQueryParams()['tab'] ?? '') === 'past';

        $query = $user->activities()->with(['category', 'organizer']);

        if ($showPast) {
            // ที่ผ่านมาแล้ว: ล่าสุดขึ้นก่อน
            $query->whereDate('date', '<', today())->orderBy('date', 'desc');
        } else {
            // กำลังจะมาถึง: ใกล้สุดขึ้นก่อน
            $query->whereDate('date', '>=', today())->orderBy('date', 'asc');
        }

        $query->orderBy('code');

        return view('my-activities.list', [
            'showPast' => $showPast,
            'activities' => $query->paginate(SearchableController::MAX_ITEMS),
        ]);
    }
}
