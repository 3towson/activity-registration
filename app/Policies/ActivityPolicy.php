<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function list(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Activity $activity): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->isAdministrator();
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->isAdministrator();
    }

    public function viewParticipants(User $user, Activity $activity): bool
    {
        return $user->isAdministrator();
    }

    public function register(User $user, Activity $activity): bool
    {
        return $user->isMember() && $activity->isOpen() && !$activity->isFull();
    }

    public function cancel(User $user, Activity $activity): bool
    {
        return $user->isMember() && $activity->isOpen()
            && $activity->participants()->whereKey($user->id)->exists();
    }
}
