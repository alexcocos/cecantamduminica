<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setlist extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
        ];
    }

    /**
     * Utilizatorul care a creat setlistul.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Echipa căreia îi aparține setlistul.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Piesele setlistului, în ordinea stabilită.
     */
    public function songs(): BelongsToMany
    {
        return $this
            ->belongsToMany(Song::class)
            ->withPivot([
                'position',
                'transpose_steps',
                'notes',
            ])
            ->withTimestamps()
            ->orderByPivot('position');
    }
}