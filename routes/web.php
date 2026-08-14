<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SetlistController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SetlistExportController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('songs.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/setlists', [SetlistController::class, 'index'])
        ->name('setlists.index');
    Route::get('/setlists/create', [SetlistController::class, 'create'])
        ->name('setlists.create');
    Route::post('/setlists', [SetlistController::class, 'store'])
        ->name('setlists.store');
    Route::get('/setlists/{setlist}/edit', [SetlistController::class, 'edit'])
        ->name('setlists.edit');
    Route::put('/setlists/{setlist}', [SetlistController::class, 'update'])
        ->name('setlists.update');
    Route::patch('/setlists/{setlist}/reorder', [SetlistController::class, 'reorder'])
        ->name('setlists.reorder');
    Route::patch(
        '/setlists/{setlist}/songs/{song}/transpose',
        [SetlistController::class, 'transposeSong']
    )->name('setlists.songs.transpose');
    Route::patch(
    '/setlists/{setlist}/songs/{song}/notes',
    [SetlistController::class, 'updateSongNotes']
)->name('setlists.songs.notes');
    Route::patch('/setlists/{setlist}/live', [SetlistController::class, 'toggleLive'])
        ->name('setlists.live');
    Route::delete('/setlists/{setlist}', [SetlistController::class, 'destroy'])
        ->name('setlists.destroy');
        Route::post(
    '/songs/{song}/add-to-setlist',
    [SetlistController::class, 'addSong']
)->name('songs.add-to-setlist');
});

/*
|--------------------------------------------------------------------------
| Exporturile setlistului
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

Route::get('/setlists/{setlist}', [SetlistController::class, 'show'])
    ->name('setlists.show');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/songs/create', [SongController::class, 'create'])->name('songs.create');
    Route::post('/songs', [SongController::class, 'store'])->name('songs.store');
    Route::get('/songs/{song}/edit', [SongController::class, 'edit'])->name('songs.edit');
    Route::put('/songs/{song}', [SongController::class, 'update'])->name('songs.update');
    Route::delete('/songs/{song}', [SongController::class, 'destroy'])->name('songs.destroy');
});

Route::get('/songs', [SongController::class, 'index'])->name('songs.index');
Route::get('/songs/{song}', [SongController::class, 'show'])->name('songs.show');