<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'join_code',
    ];

    /**
     * Utilizatorul care a creat echipa.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_id'
        );
    }

    /**
     * Toți membrii echipei.
     */
    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Setlisturile care aparțin echipei.
     */
    public function setlists(): HasMany
    {
        return $this->hasMany(Setlist::class);
    }

    /**
     * Verifică dacă utilizatorul este membru.
     */
    public function hasMember(User $user): bool
    {
        return $this
            ->users()
            ->whereKey($user->id)
            ->exists();
    }

    /**
     * Verifică dacă utilizatorul este proprietarul echipei.
     */
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }
}