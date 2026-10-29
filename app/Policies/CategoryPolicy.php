<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
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

    function delete(User $user, Category $category): bool
    {
        // ให้มี activities_count เสมอ
        $category->loadCount('activities');

        return $this->update($user) && ($category->activities_count === 0);
    }
}
