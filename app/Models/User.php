<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Beziehungen
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'benutzername',
        'email',
        'password',
        'profilbeschreibung',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================
    // Posts dieses Users
    // =========================
    public function posts(): HasMany
    {
         return $this->hasMany(\App\Models\Post::class, 'user_id');
    }

    // =========================
    // Posts die dieser User geliked hat
    // =========================
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')
            ->withTimestamps();
    }

    // =========================
    // Likes dieses Users (für Counts etc.)
    // =========================
    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    // =========================
    // WICHTIG: Route Model Binding über benutzername
    // =========================
    public function getRouteKeyName(): string
    {
        return 'benutzername';
    }
}
