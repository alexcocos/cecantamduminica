<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
     * Echipele create de utilizator.
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(
            Team::class,
            'owner_id'
        );
    }

    /**
     * Echipele din care utilizatorul face parte.
     */
    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Verifică dacă utilizatorul este administratorul site-ului.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}