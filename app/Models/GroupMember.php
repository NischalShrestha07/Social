<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Typed pivot for the `group_members` table so `$member->pivot->role` /
 * `->status` are statically known instead of a bare Pivot.
 *
 * @property int $group_id
 * @property int $user_id
 * @property string $role admin|member
 * @property string $status pending|approved
 */
#[Fillable(['role', 'status'])]
class GroupMember extends Pivot
{
    public const ROLES = ['admin', 'member'];

    public const STATUSES = ['pending', 'approved'];
}
