<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class, 'id_category', 'id');
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Icon::class, 'id_icon', 'id');
    }
}
