<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ActivityController extends SearchableController
{
    public function getQuery(): Builder
    {
        return Activity::with(['category', 'organizer'])
            ->withCount('participants')
            ->orderBy('code');
    }
}
