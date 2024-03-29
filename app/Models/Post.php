<?php

namespace App\Models;

use App\Concerns\HasLike;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory, HasLike;

    protected $guarded = [];

    const PAGINATION_COUNT = 10;

    public function scopeSearchByBody($query)
    {
        $query->when(
            request()->has('s'),
            fn($query) =>
                            $query->where('body', 'LIKE', "%" . request('s') . "%")
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    // a list of users being mentioned
    public function mentions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_mention', 'post_id')
                                                                    ->withTimestamps();
    }

    // nested relationship
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    // nested relationship
    public function replies(): HasMany
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    public function spamReports(): MorphMany
    {
        return $this->morphMany(SpamReport::class, 'reportable');
    }

    public function reportAsSpamBy(User $user): void
    {
        $this->spamReports()->create(['user_id' => $user->id]);
    }

    public function isAlreadyReportedAsSpamBy(User $user): bool
    {
        return $this->spamReports()->where('user_id', $user->id)->exists();
    }
}
