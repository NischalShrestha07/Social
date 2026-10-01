<?php

namespace App\Models;

use Database\Factories\JobPostingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $company
 * @property string $location
 * @property string $type full_time|part_time|contract|internship
 * @property string $work_mode onsite|remote|hybrid
 * @property int|null $salary_min
 * @property int|null $salary_max
 * @property string $salary_currency
 * @property Carbon|null $expires_at
 * @property bool $is_open
 */
#[Fillable([
    'user_id', 'title', 'slug', 'description', 'company', 'location',
    'type', 'work_mode', 'salary_min', 'salary_max', 'salary_currency',
    'expires_at', 'is_open',
])]
class JobPosting extends Model
{
    /** @use HasFactory<JobPostingFactory> */
    use HasFactory;

    public const TYPES = ['full_time', 'part_time', 'contract', 'internship'];

    public const WORK_MODES = ['onsite', 'remote', 'hybrid'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_open' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<JobApplication, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /** Expired postings drop out of the listings and cannot receive applications. */
    public function isAcceptingApplications(): bool
    {
        return $this->is_open && (! $this->expires_at || $this->expires_at->endOfDay()->isFuture());
    }

    /** @param Builder<JobPosting> $query */
    public function scopeOpen(Builder $query): void
    {
        $query->where('is_open', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()));
    }

    /** @param Builder<JobPosting> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function salaryLabel(): ?string
    {
        if (! $this->salary_min && ! $this->salary_max) {
            return null;
        }

        $format = fn (int $amount) => $amount >= 1000
            ? rtrim(rtrim(number_format($amount / 1000, 0), '0'), '.').'k'
            : (string) $amount;

        return match (true) {
            $this->salary_min && $this->salary_max => $format($this->salary_min).' - '.$format($this->salary_max).' '.$this->salary_currency,
            $this->salary_min => 'from '.$format($this->salary_min).' '.$this->salary_currency,
            default => 'up to '.$format($this->salary_max).' '.$this->salary_currency,
        };
    }

    public static function typeLabel(?string $type): string
    {
        return match ($type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            default => 'Full Time',
        };
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'job';
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
