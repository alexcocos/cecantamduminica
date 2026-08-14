<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Song extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'chords' => 'array',
            'capo' => 'integer',
        ];
    }

    /**
     * Setlisturile în care apare piesa.
     */
    public function setlists(): BelongsToMany
    {
        return $this
            ->belongsToMany(Setlist::class)
            ->withPivot([
                'position',
                'transpose_steps',
            ])
            ->withTimestamps();
    }
}