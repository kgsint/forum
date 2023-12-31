<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    const PAGINATION_COUNT = 10;

    protected $guarded = [];

    public function scopeSearchByNameAndSlug($query)
    {
        $query->when(
            request('s'),
            fn($query) => $query->where('name', 'LIKE', '%' . request('s') . '%')
                                ->orWhere('slug', 'LIKE', '%' . request('s') . '%')
        );
    }
}
