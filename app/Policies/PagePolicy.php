<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

// v1: single owner, no page admins/editors.
class PagePolicy
{
    public function post(User $user, Page $page): bool
    {
        return $user->id === $page->owner_id;
    }

    public function update(User $user, Page $page): bool
    {
        return $user->id === $page->owner_id;
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->id === $page->owner_id;
    }
}
