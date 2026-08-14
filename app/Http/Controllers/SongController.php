<?php

namespace App\Http\Controllers;

use App\Models\Setlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SongController extends Controller
{
    /**
     * Afișează lista pieselor și setlistul live.
     */
    public function index()
    {
        $songs = Song::orderBy('title')->get();

        $liveSetlist = Setlist::query()
            ->where('is_live', true)
            ->with('user')
            ->withCount('songs')
            ->first();

        return view(
            'songs.index',
            compact(
                'songs',
                'liveSetlist'
            )
        );
    }

    /**
     * Afișează formularul pentru adăugarea unei piese.
     */
    public function create()
    {
        return view('songs.create');
    }

    /**
     * Salvează o piesă nouă.
     */
    public function store(Request $request)
    {
        $validated =
            $this->validateSong($request);

        $sections = json_decode(
            $validated['sections'],
            true
        );

        $chords = json_decode(
            $validated['chords'] ?? '[]',
            true
        );

        $sectionError =
            $this->validateSections($sections);

        if ($sectionError !== null) {
            return back()
                ->withErrors([
                    'sections' => $sectionError,
                ])
                ->withInput();
        }

        $lyrics =
            $this->buildLegacyLyrics($sections);

        $song = new Song();

        $this->fillSong(
            $song,
            $validated,
            $sections,
            $chords,
            $lyrics
        );

        $song->save();

        return redirect()
            ->route('songs.index')
            ->with(
                'success',
                'Piesa a fost adăugată.'
            );
    }

    /**
     * Afișează o piesă.
     */
    /**
 * Afișează o piesă.
 */
public function show(Song $song)
{
    $userSetlists = collect();

    if (auth()->check()) {
        $userSetlists = auth()
            ->user()
            ->setlists()
            ->with([
                'songs' => function ($query) use ($song) {
                    $query->where(
                        'songs.id',
                        $song->id
                    );
                },
            ])
            ->latest('updated_at')
            ->get();
    }

    return view(
        'songs.show',
        compact(
            'song',
            'userSetlists'
        )
    );
}

    /**
     * Afișează formularul de editare.
     */
    public function edit(Song $song)
    {
        return view(
            'songs.edit',
            compact('song')
        );
    }

    /**
     * Salvează modificările unei piese.
     */
    public function update(
        Request $request,
        Song $song
    ) {
        $validated =
            $this->validateSong($request);

        $sections = json_decode(
            $validated['sections'],
            true
        );

        $chords = json_decode(
            $validated['chords'] ?? '[]',
            true
        );

        $sectionError =
            $this->validateSections($sections);

        if ($sectionError !== null) {
            return back()
                ->withErrors([
                    'sections' => $sectionError,
                ])
                ->withInput();
        }

        $lyrics =
            $this->buildLegacyLyrics($sections);

        $this->fillSong(
            $song,
            $validated,
            $sections,
            $chords,
            $lyrics
        );

        $song->save();

        return redirect()
            ->route('songs.show', $song)
            ->with(
                'success',
                'Piesa a fost modificată.'
            );
    }

    /**
     * Șterge o piesă.
     */
    public function destroy(Song $song)
    {
        $song->delete();

        return redirect()
            ->route('songs.index')
            ->with(
                'success',
                'Piesa a fost ștearsă.'
            );
    }

    /**
     * Validează informațiile generale ale piesei.
     */
    private function validateSong(
        Request $request
    ): array {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'author' => [
                'nullable',
                'string',
                'max:255',
            ],

            'song_type' => [
                'required',
                Rule::in([
                    'praise',
                    'interlude',
                    'worship',
                    'event',
                ]),
            ],

            'event_name' => [
                'nullable',
                'string',
                'max:255',
                'required_if:song_type,event',
            ],

            'key' => [
                'required',
                'string',
                'max:20',
            ],

            'capo' => [
                'required',
                'integer',
                'min:0',
                'max:12',
            ],

            'sections' => [
                'required',
                'json',
            ],

            'chords' => [
                'nullable',
                'json',
            ],
        ], [
            'song_type.required' =>
                'Alege tipul piesei.',

            'song_type.in' =>
                'Tipul piesei selectat nu este valid.',

            'event_name.required_if' =>
                'Completează denumirea evenimentului.',
        ]);
    }

    /**
     * Completează modelul piesei.
     */
    private function fillSong(
        Song $song,
        array $validated,
        array $sections,
        mixed $chords,
        string $lyrics
    ): void {
        $song->title =
            $validated['title'];

        $song->author =
            $validated['author'] ?? null;

        $song->song_type =
            $validated['song_type'];

        /*
         * Numele evenimentului se păstrează numai
         * pentru piesele de tip eveniment.
         */
        $song->event_name =
            $validated['song_type'] === 'event'
                ? trim($validated['event_name'])
                : null;

        $song->key =
            $validated['key'];

        $song->capo =
            $validated['capo'];

        $song->lyrics =
            $lyrics;

        $song->sections =
            $sections;

        $song->chords =
            is_array($chords)
                ? $chords
                : [];
    }

    /**
     * Verifică structura secțiunilor.
     */
    private function validateSections(
        mixed $sections
    ): ?string {
        if (
            !is_array($sections) ||
            count($sections) === 0
        ) {
            return 'Adaugă cel puțin o secțiune.';
        }

        $allowedTypes = [
            'stanza',
            'pre_chorus',
            'chorus',
            'bridge',
            'coda',
            'custom',
        ];

        foreach ($sections as $section) {
            if (
                empty($section['id']) ||
                empty($section['type']) ||
                !in_array(
                    $section['type'],
                    $allowedTypes,
                    true
                )
            ) {
                return
                    'Una dintre secțiuni are un tip invalid.';
            }

            if (
                empty(
                    trim($section['lyrics'] ?? '')
                )
            ) {
                return
                    'Fiecare secțiune trebuie să conțină versuri.';
            }

            if (
                $section['type'] === 'custom' &&
                empty(
                    trim(
                        $section['custom_label']
                        ?? ''
                    )
                )
            ) {
                return
                    'Completează denumirea secțiunii personalizate.';
            }
        }

        return null;
    }

    /**
     * Păstrează și varianta simplă a versurilor.
     */
    private function buildLegacyLyrics(
        array $sections
    ): string {
        return collect($sections)
            ->pluck('lyrics')
            ->implode("\n\n");
    }
}