<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiProvider extends Model {
    protected $fillable
        = [
            'name',
            'url',
        ];

    public function sources(): HasMany {
        return $this->hasMany(Source::class);
    }
}
