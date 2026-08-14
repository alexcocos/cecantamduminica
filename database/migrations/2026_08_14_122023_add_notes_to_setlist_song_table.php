<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adaugă notițele pentru fiecare piesă
     * dintr-un setlist.
     */
    public function up(): void
    {
        Schema::table(
            'setlist_song',
            function (Blueprint $table) {
                $table
                    ->text('notes')
                    ->nullable();
            }
        );
    }

    /**
     * Elimină coloana dacă migrarea
     * este anulată.
     */
    public function down(): void
    {
        Schema::table(
            'setlist_song',
            function (Blueprint $table) {
                $table->dropColumn('notes');
            }
        );
    }
};