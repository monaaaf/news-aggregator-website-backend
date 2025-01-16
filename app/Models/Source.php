<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model {
    protected $fillable
        = [
            'name',
            'url',
            'api_provider_id',
        ];

    public function apiProvider(): BelongsTo {
        return $this->belongsTo(ApiProvider::class);
    }

    public function articles(): HasMany {
        return $this->hasMany(Article::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_source');
    }
}
