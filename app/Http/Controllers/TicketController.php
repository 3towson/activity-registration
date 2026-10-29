<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    function download(string $activityCode): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $activity = $user->activities()
            ->where('activities.code', $activityCode)
            ->with(['category', 'organizer'])
            ->firstOrFail();

        return Pdf::loadView('tickets.pdf', [
            'activity' => $activity,
            'user' => $user,
            'registeredAt' => $activity->pivot->created_at,
        ])->setPaper('a5', 'landscape')->download("ticket-{$activity->code}.pdf");
    }
}
