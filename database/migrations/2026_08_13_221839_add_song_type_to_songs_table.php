<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adaugă tipul piesei și numele evenimentului.
     */
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table
                ->string('song_type')
                ->nullable()
                ->after('capo');

            $table
                ->string('event_name')
                ->nullable()
                ->after('song_type');
        });
    }

    /**
     * Elimină coloanele dacă migrarea este anulată.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn([
                'song_type',
                'event_name',
            ]);
        });
    }
};