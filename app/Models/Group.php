<?php

namespace App\Models;

use App\Notifications\GroupJoinRequested;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

#[Fillable(['owner_id', 'name', 'slug', 'description', 'avatar_path', 'cover_path', 'privacy'])]
class Group extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }

    public function approvedMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'approved');
    }

    public function pendingMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    public function admins(): BelongsToMany
    {
        return $this->approvedMembers()->wherePivot('role', 'admin');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->latest();
    }

    public function isPrivate(): bool
    {
        return $this->privacy === 'private';
    }

    // 'none' | 'pending' | 'member' | 'admin'
    public function membershipStatus(?User $user): string
    {
        if (! $user) {
            return 'none';
        }

        $pivot = $this->members()->where('users.id', $user->id)->first()?->pivot;

        if (! $pivot) {
            return 'none';
        }

        if ($pivot->status === 'pending') {
            return 'pending';
        }

        return $pivot->role === 'admin' ? 'admin' : 'member';
    }

    public function isMember(?User $user): bool
    {
        return in_array($this->membershipStatus($user), ['member', 'admin'], true);
    }

    public function isAdmin(?User $user): bool
    {
        return $this->membershipStatus($user) === 'admin';
    }

    // Public groups admit immediately; private ones create a pending request for the admins.
    public function join(User $user): void
    {
        if ($this->membershipStatus($user) !== 'none') {
            return;
        }

        $status = $this->isPrivate() ? 'pending' : 'approved';
        $this->members()->attach($user->id, ['role' => 'member', 'status' => $status]);

        if ($status === 'pending') {
            Notification::send($this->admins()->get(), new GroupJoinRequested($user, $this));
        }
    }

    // Also cancels a pending request. The owner can never leave.
    public function leave(User $user): void
    {
        if ($user->id === $this->owner_id) {
            return;
        }

        $this->members()->detach($user->id);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? asset('storage/'.$this->cover_path) : null;
    }

    public function avatarUrl(): string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : 'https://ui-avatars.com/api/?background=088dcd&color=fff&name='.urlencode($this->name);
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'group';
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
