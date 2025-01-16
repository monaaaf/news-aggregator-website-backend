<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model {
    protected $fillable
        = [
            'name',
            'slug',
        ];

    public function articles(): HasMany {
        return $this->hasMany(Article::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_category');
    }
}
