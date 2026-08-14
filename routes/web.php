<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SetlistController;
use App\Http\Controllers\SetlistExportController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pagina principală
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [HomeController::class, 'index']
)->name('home');
/*
|--------------------------------------------------------------------------
| Autentificare și înregistrare
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get(
        '/login',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->name('login.attempt');

    Route::get(
        '/register',
        [AuthController::class, 'showRegister']
    )->name('register');

    Route::post(
        '/register',
        [AuthController::class, 'register']
    )->name('register.attempt');
});

Route::post(
    '/logout',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Paginile care necesită autentificare
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Echipe
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/teams',
        [TeamController::class, 'index']
    )->name('teams.index');

    Route::post(
        '/teams',
        [TeamController::class, 'store']
    )->name('teams.store');

    Route::post(
        '/teams/join',
        [TeamController::class, 'join']
    )->name('teams.join');

    Route::get(
        '/teams/{team}',
        [TeamController::class, 'show']
    )->name('teams.show');

    Route::delete(
        '/teams/{team}/leave',
        [TeamController::class, 'leave']
    )->name('teams.leave');

    Route::delete(
    '/teams/{team}',
    [TeamController::class, 'destroy']
)->name('teams.destroy');
    /*
    |--------------------------------------------------------------------------
    | Setlisturi
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/setlists',
        [SetlistController::class, 'index']
    )->name('setlists.index');

    Route::get(
        '/setlists/create',
        [SetlistController::class, 'create']
    )->name('setlists.create');

    Route::post(
        '/setlists',
        [SetlistController::class, 'store']
    )->name('setlists.store');

    Route::get(
        '/setlists/{setlist}/edit',
        [SetlistController::class, 'edit']
    )->name('setlists.edit');

    Route::put(
        '/setlists/{setlist}',
        [SetlistController::class, 'update']
    )->name('setlists.update');

    Route::patch(
    '/setlists/{setlist}/team',
    [SetlistController::class, 'assignTeam']
)->name('setlists.team');

    Route::patch(
        '/setlists/{setlist}/reorder',
        [SetlistController::class, 'reorder']
    )->name('setlists.reorder');

    Route::patch(
        '/setlists/{setlist}/songs/{song}/transpose',
        [SetlistController::class, 'transposeSong']
    )->name('setlists.songs.transpose');

    Route::patch(
        '/setlists/{setlist}/songs/{song}/notes',
        [SetlistController::class, 'updateSongNotes']
    )->name('setlists.songs.notes');

    Route::patch(
        '/setlists/{setlist}/live',
        [SetlistController::class, 'toggleLive']
    )->name('setlists.live');

    Route::delete(
        '/setlists/{setlist}',
        [SetlistController::class, 'destroy']
    )->name('setlists.destroy');

    Route::post(
        '/songs/{song}/add-to-setlist',
        [SetlistController::class, 'addSong']
    )->name('songs.add-to-setlist');
});

/*
|--------------------------------------------------------------------------
| Exporturile setlisturilor
|--------------------------------------------------------------------------
*/

Route::get(
    '/setlists/{setlist}/export',
    [SetlistExportController::class, 'options']
)->name('setlists.export.options');

Route::post(
    '/setlists/{setlist}/export/pdf',
    [SetlistExportController::class, 'pdf']
)->name('setlists.export.pdf');

Route::post(
    '/setlists/{setlist}/export/powerpoint',
    [SetlistExportController::class, 'powerpoint']
)->name('setlists.export.powerpoint');

/*
|--------------------------------------------------------------------------
| Vizualizarea setlisturilor
|--------------------------------------------------------------------------
*/

Route::get(
    '/setlists/{setlist}',
    [SetlistController::class, 'show']
)->name('setlists.show');

/*
|--------------------------------------------------------------------------
| Administrarea pieselor
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'admin',
])->group(function () {
    Route::get(
        '/songs/create',
        [SongController::class, 'create']
    )->name('songs.create');

    Route::post(
        '/songs',
        [SongController::class, 'store']
    )->name('songs.store');

    Route::get(
        '/songs/{song}/edit',
        [SongController::class, 'edit']
    )->name('songs.edit');

    Route::put(
        '/songs/{song}',
        [SongController::class, 'update']
    )->name('songs.update');

    Route::delete(
        '/songs/{song}',
        [SongController::class, 'destroy']
    )->name('songs.destroy');
});

/*
|--------------------------------------------------------------------------
| Paginile publice ale pieselor
|--------------------------------------------------------------------------
*/

Route::get(
    '/songs',
    [SongController::class, 'index']
)->name('songs.index');

Route::get(
    '/songs/{song}',
    [SongController::class, 'show']
)->name('songs.show');