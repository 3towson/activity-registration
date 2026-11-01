<?php

namespace App\Policies;

use App\Models\Organizer;
use App\Models\User;

class OrganizerPolicy
{
    function list(): bool
    {
        return true;
    }

    function view(): bool
    {
        return $this->list();
    }

    function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    function update(User $user): bool
    {
        return $this->create($user);
    }

    function delete(User $user, Organizer $organizer): bool
    {
        // ให้มี activities_count เสมอ
        $organizer->loadCount('activities');

        return $this->update($user) && ($organizer->activities_count === 0);
    }
}
