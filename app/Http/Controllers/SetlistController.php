<?php

namespace App\Http\Controllers;

use App\Models\Setlist;
use App\Models\Song;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetlistController extends Controller
{
    /**
     * Afișează toate setlisturile personale.
     */
    /**
 * Afișează setlisturile utilizatorului autentificat.
 */
public function index(Request $request)
{
    $setlists = $request
        ->user()
        ->setlists()
        ->with('team')
        ->withCount('songs')
        ->latest('updated_at')
        ->get();

    $teams = $request
        ->user()
        ->teams()
        ->orderBy('name')
        ->get();

    return view(
        'setlists.index',
        compact(
            'setlists',
            'teams'
        )
    );
}

    /**
     * Afișează formularul pentru un setlist nou.
     */
    public function create(Request $request)
    {
        $songs = Song::query()
            ->orderBy('title')
            ->get();

        $teams = $request
            ->user()
            ->teams()
            ->orderBy('name')
            ->get();

        /*
         * Dacă venim de pe pagina unei echipe,
         * selectăm automat echipa respectivă.
         */
        $selectedTeamId = null;

        if ($request->filled('team')) {
            $requestedTeamId = (int) $request->query('team');

            if (
                $teams->contains(
                    fn ($team) =>
                        $team->id === $requestedTeamId
                )
            ) {
                $selectedTeamId = $requestedTeamId;
            }
        }

        return view(
            'setlists.create',
            compact(
                'songs',
                'teams',
                'selectedTeamId'
            )
        );
    }

    /**
     * Salvează un setlist nou.
     */
    public function store(Request $request)
    {
        $validated =
            $this->validateSetlist($request);

        $teamId = $this->validatedTeamId(
            $request,
            $validated['team_id'] ?? null
        );

        $setlist = DB::transaction(
            function () use (
                $request,
                $validated,
                $teamId
            ) {
                $setlist = new Setlist();

                $setlist->user_id =
                    $request->user()->id;

                $setlist->team_id =
                    $teamId;

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
     * Afișează un setlist.
     */
   /**
 * Afișează un setlist.
 */
public function show(
    Request $request,
    Setlist $setlist
) {
    $setlist->load([
        'user',
        'team',
        'songs',
    ]);

    $teams = collect();

    if (
        $request->user() &&
        $request->user()->id === $setlist->user_id
    ) {
        $teams = $request
            ->user()
            ->teams()
            ->orderBy('name')
            ->get();
    }

    return view(
        'setlists.show',
        compact(
            'setlist',
            'teams'
        )
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

        $setlist->load([
            'songs',
            'team',
        ]);

        $songs = Song::query()
            ->orderBy('title')
            ->get();

        $teams = $request
            ->user()
            ->teams()
            ->orderBy('name')
            ->get();

        $selectedTeamId =
            $setlist->team_id;

        return view(
            'setlists.edit',
            compact(
                'setlist',
                'songs',
                'teams',
                'selectedTeamId'
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

        $teamId = $this->validatedTeamId(
            $request,
            $validated['team_id'] ?? null
        );

        /*
         * Un setlist live trebuie să rămână
         * asociat unei echipe.
         */
        if (
            $setlist->is_live &&
            $teamId === null
        ) {
            return back()
                ->withErrors([
                    'team_id' =>
                        'Un setlist live trebuie să aparțină unei echipe.',
                ])
                ->withInput();
        }

        DB::transaction(
            function () use (
                $setlist,
                $validated,
                $teamId
            ) {
                /*
                 * Dacă mutăm un setlist live în altă echipă,
                 * dezactivăm setlistul live existent acolo.
                 */
                if (
                    $setlist->is_live &&
                    $teamId !== $setlist->team_id
                ) {
                    Setlist::query()
                        ->where('team_id', $teamId)
                        ->where('is_live', true)
                        ->whereKeyNot($setlist->id)
                        ->update([
                            'is_live' => false,
                        ]);
                }

                $setlist->team_id =
                    $teamId;

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
     * Salvează transpunerea unei piese.
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
     * Salvează notița unei piese.
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
 * Asociază setlistul unei echipe
 * sau elimină asocierea existentă.
 */
public function assignTeam(
    Request $request,
    Setlist $setlist
) {
    $this->ensureOwner(
        $request,
        $setlist
    );

    $validated = $request->validate([
        'team_id' => [
            'nullable',
            'integer',
            'exists:teams,id',
        ],
    ]);

    $teamId = $this->validatedTeamId(
        $request,
        $validated['team_id'] ?? null
    );

    /*
     * Un setlist live nu poate fi scos din echipă.
     */
    if (
        $setlist->is_live &&
        $teamId === null
    ) {
        return back()->withErrors([
            'team_id' =>
                'Oprește modul live înainte de a elimina echipa.',
        ]);
    }

    DB::transaction(
        function () use (
            $setlist,
            $teamId
        ) {
            /*
             * Dacă mutăm un setlist live, în echipa
             * nouă poate exista un singur setlist live.
             */
            if (
                $setlist->is_live &&
                $teamId !== $setlist->team_id
            ) {
                Setlist::query()
                    ->where('team_id', $teamId)
                    ->where('is_live', true)
                    ->whereKeyNot($setlist->id)
                    ->update([
                        'is_live' => false,
                    ]);
            }

            $setlist->team_id = $teamId;
            $setlist->save();
        }
    );

    return back()->with(
        'success',
        $teamId
            ? 'Setlistul a fost asociat echipei.'
            : 'Setlistul nu mai este asociat unei echipe.'
    );
}
    /**
     * Activează sau dezactivează setlistul live.
     *
     * Fiecare echipă poate avea un singur
     * setlist live.
     */
    public function toggleLive(
        Request $request,
        Setlist $setlist
    ) {
        $this->ensureOwner(
            $request,
            $setlist
        );

        /*
         * Dezactivarea nu necesită selectarea echipei.
         */
       if ($setlist->is_live) {
    $setlist->is_live = false;
    $setlist->save();

    return back()->with(
        'success',
        'Setlistul nu mai este live.'
    );
}

        /*
         * Pentru activare, echipa este obligatorie.
         */
        $validated = $request->validate([
            'team_id' => [
                'required',
                'integer',
                'exists:teams,id',
            ],
        ]);

        $teamId = $this->validatedTeamId(
            $request,
            $validated['team_id']
        );

        DB::transaction(
            function () use (
                $setlist,
                $teamId
            ) {
                /*
                 * Dezactivăm numai setlistul live
                 * al echipei selectate.
                 */
                Setlist::query()
                    ->where('team_id', $teamId)
                    ->where('is_live', true)
                    ->whereKeyNot($setlist->id)
                    ->update([
                        'is_live' => false,
                    ]);

                $setlist->team_id =
                    $teamId;

                $setlist->is_live = true;

                $setlist->save();
            }
        );

        return back()->with(
            'success',
            'Setlistul este acum live pentru echipa selectată.'
        );
    }

    /**
     * Adaugă o piesă la finalul unui setlist.
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
     * Validează formularul unui setlist.
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

            /*
             * Asocierea cu o echipă este opțională.
             */
            'team_id' => [
                'nullable',
                'integer',
                'exists:teams,id',
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
     * Verifică dacă echipa selectată aparține
     * utilizatorului și întoarce ID-ul ei.
     */
    private function validatedTeamId(
        Request $request,
        mixed $teamId
    ): ?int {
        if (
            $teamId === null ||
            $teamId === ''
        ) {
            return null;
        }

        $team = Team::query()
            ->whereKey((int) $teamId)
            ->whereHas(
                'users',
                function ($query) use ($request) {
                    $query->where(
                        'users.id',
                        $request->user()->id
                    );
                }
            )
            ->first();

        abort_unless(
            $team !== null,
            403,
            'Nu faci parte din echipa selectată.'
        );

        return $team->id;
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
     * Verifică dacă piesa aparține setlistului.
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