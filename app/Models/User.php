<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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

    /**
     * Setlisturile create de utilizator.
     */
    public function setlists(): HasMany
    {
        return $this->hasMany(Setlist::class);
    }

    /**
     * Verifică dacă utilizatorul este administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}