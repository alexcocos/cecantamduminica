<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'setlist_song',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('setlist_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table
                    ->foreignId('song_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table
                    ->unsignedInteger('position')
                    ->default(0);

                $table
                    ->smallInteger('transpose_steps')
                    ->default(0);

                $table->timestamps();

                $table->unique([
                    'setlist_id',
                    'song_id',
                ]);

                $table->index([
                    'setlist_id',
                    'position',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('setlist_song');
    }
};