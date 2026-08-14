<?php

namespace App\Http\Controllers;

use App\Models\Setlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetlistController extends Controller
{
    /**
     * Afișează setlisturile utilizatorului autentificat.
     */
    public function index(Request $request)
    {
        $setlists = $request
            ->user()
            ->setlists()
            ->withCount('songs')
            ->latest('updated_at')
            ->get();

        return view(
            'setlists.index',
            compact('setlists')
        );
    }

    /**
     * Afișează formularul pentru un setlist nou.
     */
    public function create()
    {
        $songs = Song::orderBy('title')->get();

        return view(
            'setlists.create',
            compact('songs')
        );
    }

    /**
     * Salvează un setlist nou.
     */
    public function store(Request $request)
    {
        $validated =
            $this->validateSetlist($request);

        $setlist = DB::transaction(
            function () use (
                $request,
                $validated
            ) {
                $setlist = new Setlist();

                $setlist->user_id =
                    $request->user()->id;

                $setlist->name =
                    $validated['name'];

                $setlist->description =
                    $validated['description']
                    ?? null;

                $setlist->is_live = false;

                $setlist->save();

                $setlist->songs()->sync(
                    $this->buildSongSyncData(
                        $validated['song_ids'],
                        $validated[
                            'transpose_steps'
                        ] ?? []
                    )
                );

                return $setlist;
            }
        );

        return redirect()
            ->route(
                'setlists.show',
                $setlist
            )
            ->with(
                'success',
                'Setlistul a fost creat.'
            );
    }

    /**
     * Afișează public un setlist.
     */
    public function show(Setlist $setlist)
    {
        $setlist->load([
            'user',
            'songs',
        ]);

        return view(
            'setlists.show',
            compact('setlist')
        );
    }

    /**
     * Afișează formularul de editare.
     */
    public function edit(
        Request $request,
        Setlist $setlist
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        $setlist->load('songs');

        $songs =
            Song::orderBy('title')->get();

        return view(
            'setlists.edit',
            compact(
                'setlist',
                'songs'
            )
        );
    }

    /**
     * Salvează modificările setlistului.
     */
    public function update(
        Request $request,
        Setlist $setlist
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        $validated =
            $this->validateSetlist($request);

        DB::transaction(
            function () use (
                $setlist,
                $validated
            ) {
                $setlist->name =
                    $validated['name'];

                $setlist->description =
                    $validated['description']
                    ?? null;

                $setlist->save();

                $setlist->songs()->sync(
                    $this->buildSongSyncData(
                        $validated['song_ids'],
                        $validated[
                            'transpose_steps'
                        ] ?? []
                    )
                );
            }
        );

        return redirect()
            ->route(
                'setlists.show',
                $setlist
            )
            ->with(
                'success',
                'Setlistul a fost modificat.'
            );
    }

    /**
     * Actualizează ordinea pieselor.
     */
    public function reorder(
        Request $request,
        Setlist $setlist
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        $validated = $request->validate([
            'song_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'song_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:songs,id',
            ],
        ]);

        $currentSongIds = $setlist
            ->songs()
            ->pluck('songs.id')
            ->map(
                fn ($id) => (int) $id
            )
            ->sort()
            ->values();

        $submittedSongIds = collect(
            $validated['song_ids']
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->sort()
            ->values();

        abort_unless(
            $currentSongIds->all()
                ===
                $submittedSongIds->all(),
            422,
            'Lista pieselor nu corespunde setlistului.'
        );

        DB::transaction(
            function () use (
                $setlist,
                $validated
            ) {
                foreach (
                    $validated['song_ids']
                    as $position => $songId
                ) {
                    $setlist
                        ->songs()
                        ->updateExistingPivot(
                            $songId,
                            [
                                'position' =>
                                    $position,
                            ]
                        );
                }
            }
        );

        return response()->json([
            'message' =>
                'Ordinea pieselor a fost salvată.',
        ]);
    }

    /**
     * Salvează transpunerea unei piese
     * în cadrul setlistului.
     */
    public function transposeSong(
        Request $request,
        Setlist $setlist,
        Song $song
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        $this->ensureSongBelongsToSetlist(
            $setlist,
            $song
        );

        $validated = $request->validate([
            'transpose_steps' => [
                'required',
                'integer',
                'min:-11',
                'max:11',
            ],
        ]);

        $setlist
            ->songs()
            ->updateExistingPivot(
                $song->id,
                [
                    'transpose_steps' =>
                        $validated[
                            'transpose_steps'
                        ],
                ]
            );

        $setlist->touch();

        return response()->json([
            'message' =>
                'Transpunerea a fost salvată.',

            'transpose_steps' =>
                (int) $validated[
                    'transpose_steps'
                ],
        ]);
    }

    /**
     * Salvează notița unei piese
     * în cadrul setlistului.
     */
    public function updateSongNotes(
        Request $request,
        Setlist $setlist,
        Song $song
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        $this->ensureSongBelongsToSetlist(
            $setlist,
            $song
        );

        $validated = $request->validate([
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $notes = trim(
            $validated['notes'] ?? ''
        );

        $setlist
            ->songs()
            ->updateExistingPivot(
                $song->id,
                [
                    'notes' =>
                        $notes !== ''
                            ? $notes
                            : null,
                ]
            );

        $setlist->touch();

        return response()->json([
            'message' =>
                'Notița a fost salvată.',

            'notes' =>
                $notes !== ''
                    ? $notes
                    : null,
        ]);
    }

    /**
     * Activează sau dezactivează setlistul live.
     *
     * Poate exista un singur setlist live.
     */
    public function toggleLive(
        Request $request,
        Setlist $setlist
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        DB::transaction(
            function () use ($setlist) {
                if ($setlist->is_live) {
                    $setlist->is_live = false;
                    $setlist->save();

                    return;
                }

                Setlist::query()
                    ->where('is_live', true)
                    ->update([
                        'is_live' => false,
                    ]);

                $setlist->is_live = true;
                $setlist->save();
            }
        );

        $isLive = $setlist
            ->fresh()
            ->is_live;

        return back()->with(
            'success',
            $isLive
                ? 'Setlistul este acum live.'
                : 'Setlistul nu mai este live.'
        );
    }

    /**
     * Adaugă o piesă la finalul
     * unui setlist.
     */
    public function addSong(
        Request $request,
        Song $song
    ) {
        $validated = $request->validate([
            'setlist_id' => [
                'required',
                'integer',
                'exists:setlists,id',
            ],
        ]);

        $setlist = Setlist::query()
            ->whereKey(
                $validated['setlist_id']
            )
            ->where(
                'user_id',
                $request->user()->id
            )
            ->firstOrFail();

        $songAlreadyExists = DB::table(
            'setlist_song'
        )
            ->where(
                'setlist_id',
                $setlist->id
            )
            ->where(
                'song_id',
                $song->id
            )
            ->exists();

        if ($songAlreadyExists) {
            return back()->with(
                'error',
                'Piesa există deja în setlistul selectat.'
            );
        }

        $lastPosition = DB::table(
            'setlist_song'
        )
            ->where(
                'setlist_id',
                $setlist->id
            )
            ->max('position');

        $nextPosition =
            $lastPosition === null
                ? 0
                : (int) $lastPosition + 1;

        $setlist
            ->songs()
            ->attach(
                $song->id,
                [
                    'position' =>
                        $nextPosition,

                    'transpose_steps' =>
                        0,

                    'notes' =>
                        null,
                ]
            );

        $setlist->touch();

        return back()->with(
            'success',
            'Piesa a fost adăugată în setlistul „'
                . $setlist->name
                . '”.'
        );
    }

    /**
     * Șterge un setlist.
     */
    public function destroy(
        Request $request,
        Setlist $setlist
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        $setlist->delete();

        return redirect()
            ->route('setlists.index')
            ->with(
                'success',
                'Setlistul a fost șters.'
            );
    }

    /**
     * Validează formularul setlistului.
     */
    private function validateSetlist(
        Request $request
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'song_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'song_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:songs,id',
            ],

            'transpose_steps' => [
                'nullable',
                'array',
            ],

            'transpose_steps.*' => [
                'nullable',
                'integer',
                'min:-11',
                'max:11',
            ],
        ]);
    }

    /**
     * Pregătește datele pieselor pentru
     * tabelul intermediar.
     */
    private function buildSongSyncData(
        array $songIds,
        array $transposeSteps
    ): array {
        $syncData = [];

        foreach (
            $songIds
            as $position => $songId
        ) {
            $syncData[$songId] = [
                'position' =>
                    $position,

                'transpose_steps' =>
                    (int) (
                        $transposeSteps[
                            $songId
                        ] ?? 0
                    ),
            ];
        }

        return $syncData;
    }

    /**
     * Verifică dacă piesa aparține
     * setlistului primit.
     */
    private function ensureSongBelongsToSetlist(
        Setlist $setlist,
        Song $song
    ): void {
        abort_unless(
            $setlist
                ->songs()
                ->whereKey($song->id)
                ->exists(),
            404,
            'Piesa nu aparține acestui setlist.'
        );
    }

    /**
     * Permite modificarea numai proprietarului.
     */
    private function ensureOwner(
        Request $request,
        Setlist $setlist
    ): void {
        abort_unless(
            $request->user()->id
                ===
                $setlist->user_id,
            403,
            'Nu poți modifica setlistul altui utilizator.'
        );
    }
}