<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
    'content',
    'user_id',   // ✅ GANZ WICHTIG
];


    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\PostLike::class, 'post_id');
        
    }
    
}
