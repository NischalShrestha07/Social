<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    // Seeing the group's posts/members; name + description are always public.
    public function view(User $user, Group $group): bool
    {
        return ! $group->isPrivate() || $group->isMember($user);
    }

    public function post(User $user, Group $group): bool
    {
        return $group->isMember($user);
    }

    public function update(User $user, Group $group): bool
    {
        return $group->isAdmin($user);
    }

    public function manageMembers(User $user, Group $group): bool
    {
        return $group->isAdmin($user);
    }
}
