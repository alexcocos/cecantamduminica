@extends('layouts.app')

@section('title', $song->title . ' | Ce cântăm duminică')

@section('content')
    @php
        $sections = $song->sections ?? [];

        /*
         * Compatibilitate pentru piesele vechi, care aveau
         * doar câmpul lyrics și nu erau împărțite pe secțiuni.
         */
        if (empty($sections)) {
            $sections = [
                [
                    'id' => 'legacy-section-' . $song->id,
                    'type' => 'stanza',
                    'number' => null,
                    'custom_label' => null,
                    'lyrics' => $song->lyrics,
                ],
            ];
        }

        $allChords = $song->chords ?? [];

        /*
         * Acordurile pieselor vechi nu aveau section.
         * Le legăm de prima secțiune numai pentru afișare.
         */
        if (count($sections) === 1) {
            $firstSectionId = $sections[0]['id'];

            $allChords = collect($allChords)
                ->map(function ($chord) use ($firstSectionId) {
                    if (empty($chord['section'])) {
                        $chord['section'] = $firstSectionId;
                    }

                    return $chord;
                })
                ->all();
        }

        $sectionNames = [
            'verse' => 'Pre-refren',
            'pre_chorus' => 'Pre-refren',
            'stanza' => 'Strofă',
            'chorus' => 'Refren',
            'bridge' => 'Bridge',
            'coda' => 'Coda',
        ];

        $songTypeLabels = [
            'praise' => 'Laudă',
            'interlude' => 'Intermediar',
            'worship' => 'Închinare',
            'event' => $song->event_name ?: 'Eveniment',
        ];

        $songTypeLabel =
            $songTypeLabels[$song->song_type] ?? null;
    @endphp

    <section class="song-page">
        <div class="song-page-top">
            <a
                class="back-link"
                href="{{ route('songs.index') }}"
            >
                ← Înapoi la piese
            </a>

            @auth
                <div class="song-page-actions">
                    @if (auth()->user()->isAdmin())
                    <a
                        class="button button-secondary"
                        href="{{ route('songs.edit', $song) }}"
                    >
                        Editează piesa
                    </a>

                    <form
                        action="{{ route('songs.destroy', $song) }}"
                        method="POST"
                        onsubmit="return confirm('Sigur vrei să ștergi această piesă?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            class="button button-danger"
                            type="submit"
                        >
                            Șterge
                        </button>
                    </form>
                    @endif
                </div>
            @endauth
        </div>

        @if (session('error'))
            <div class="song-action-error">
                {{ session('error') }}
            </div>
        @endif

        <header class="song-header">
            @auth
                <details class="add-to-setlist-menu song-header-setlist">
                    <summary class="button add-to-setlist-button">
                        + Adaugă în setlist
                    </summary>

                    <div class="add-to-setlist-panel">
                        <strong>Adaugă piesa în:</strong>

                        @if ($userSetlists->isEmpty())
                            <p>Nu ai încă niciun setlist.</p>

                            <a
                                href="{{ route('setlists.create') }}"
                                class="create-setlist-link"
                            >
                                Creează primul setlist
                            </a>
                        @else
                            <form
                                action="{{ route('songs.add-to-setlist', $song) }}"
                                method="POST"
                            >
                                @csrf

                                <label for="setlist_id">
                                    Setlist
                                </label>

                                <select
                                    id="setlist_id"
                                    name="setlist_id"
                                    required
                                >
                                    <option value="">
                                        Alege un setlist
                                    </option>

                                    @foreach ($userSetlists as $setlist)
                                        @php
                                            $containsSong =
                                                $setlist->songs->isNotEmpty();
                                        @endphp

                                        <option
                                            value="{{ $setlist->id }}"
                                            @disabled($containsSong)
                                        >
                                            {{ $setlist->name }}
                                            {{ $containsSong ? '— piesa este deja adăugată' : '' }}
                                        </option>
                                    @endforeach
                                </select>

                                <button
                                    type="submit"
                                    class="button confirm-setlist-button"
                                >
                                    Adaugă piesa
                                </button>
                            </form>
                        @endif
                    </div>
                </details>
            @endauth

            <p class="song-author">
                {{ $song->author ?? 'Autor necunoscut' }}
            </p>

            <h1>{{ $song->title }}</h1>

            <div class="song-details">
                <div class="transpose-control">
                    <span class="transpose-label">
                        Tonalitate:
                    </span>

                    <button
                        type="button"
                        id="transpose-down"
                        class="transpose-button"
                        aria-label="Transpune cu un semiton mai jos"
                    >
                        −
                    </button>

                    <strong
                        id="current-key"
                        data-original-key="{{ $song->key }}"
                    >
                        {{ $song->key }}
                    </strong>

                    <button
                        type="button"
                        id="transpose-up"
                        class="transpose-button"
                        aria-label="Transpune cu un semiton mai sus"
                    >
                        +
                    </button>

                    <button
                        type="button"
                        id="transpose-reset"
                        class="transpose-reset"
                    >
                        Original
                    </button>

                    <span
                        id="transpose-steps"
                        class="transpose-steps"
                        hidden
                    ></span>
                </div>

                <span class="song-detail">
                    Capo:
                    <strong>{{ $song->capo }}</strong>
                </span>

                @if ($songTypeLabel)
                    <span class="song-type-tag song-type-{{ $song->song_type }}">
                        {{ $songTypeLabel }}
                    </span>
                @endif
            </div>
        </header>

        <div class="song-sections-grid">
            @foreach ($sections as $section)
                @php
                    $sectionId = $section['id'];

                    $sectionType =
                        $section['type'] ?? 'stanza';

                    if ($sectionType === 'custom') {
                        $sectionLabel =
                            trim($section['custom_label'] ?? '')
                            ?: 'Secțiune';
                    } else {
                        $sectionLabel =
                            $sectionNames[$sectionType]
                            ?? 'Secțiune';
                    }

                    if (!empty($section['number'])) {
                        $sectionLabel .=
                            ' ' . $section['number'];
                    }

                    $lines = preg_split(
                        '/\r\n|\r|\n/',
                        $section['lyrics'] ?? ''
                    );

                    $sectionChords = collect($allChords)
                        ->filter(function ($chord) use ($sectionId) {
                            return
                                ($chord['section'] ?? null)
                                === $sectionId;
                        });
                @endphp

                <article class="public-song-section">
                    <header class="public-section-heading">
                        <span class="public-section-type">
                            {{ $sectionLabel }}
                        </span>
                    </header>

                    <div class="public-section-content">
                        @foreach ($lines as $lineIndex => $line)
                            @php
                                $lineChords = $sectionChords
                                    ->filter(function ($chord) use ($lineIndex) {
                                        return
                                            (int) ($chord['line'] ?? -1)
                                            === $lineIndex;
                                    });
                            @endphp

                            <div class="public-song-line">
                                <div class="public-chord-lane">
                                    @foreach ($lineChords as $chord)
                                        <span
                                            class="public-chord"
                                            data-original-chord="{{ $chord['name'] }}"
                                            style="left: calc({{ $chord['position'] ?? 0 }}ch + {{ $chord['offset'] ?? 0 }}ch);"
                                        >{{ $chord['name'] }}</span>
                                    @endforeach
                                </div>

                                <div class="public-lyric-line">{{ $line ?: ' ' }}</div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <style>
        /*
         * Aceste stiluri aparțin numai paginii piesei.
         */

        .song-page {
            max-width: 1180px;
            margin: 0 auto;
        }

        .song-page-top {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .song-page-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
        }

        .song-page-actions form {
            margin: 0;
        }

        .add-to-setlist-menu {
            position: relative;
            z-index: 20;
        }

        .add-to-setlist-menu summary {
            list-style: none;
        }

        .add-to-setlist-menu summary::-webkit-details-marker {
            display: none;
        }

        .add-to-setlist-button {
            border: 1px solid #a7c8e3;
            background: #e8f3fb;
            color: #0b4f80;
            cursor: pointer;
        }

        .add-to-setlist-panel {
            position: absolute;
            top: calc(100% + 9px);
            right: 0;
            width: min(330px, calc(100vw - 30px));
            padding: 18px;
            border: 1px solid #cbd8e4;
            border-radius: 13px;
            background: #ffffff;
            box-shadow: 0 18px 45px rgba(7, 35, 62, 0.2);
        }

        .add-to-setlist-panel > strong {
            display: block;
            margin-bottom: 13px;
            color: #0a2845;
            font-size: 0.86rem;
        }

        .add-to-setlist-panel p {
            margin: 0 0 12px;
            color: #718096;
            font-size: 0.8rem;
        }

        .add-to-setlist-panel label {
            display: block;
            margin-bottom: 6px;
            color: #344e68;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .add-to-setlist-panel select {
            width: 100%;
            min-height: 45px;
            margin-bottom: 11px;
            padding: 0 12px;
            border: 1px solid #c4d1dd;
            border-radius: 9px;
            outline: none;
            background: #f8fafc;
            color: #102a43;
            font: inherit;
            font-size: 0.8rem;
        }

        .add-to-setlist-panel select:focus {
            border-color: #2878c8;
            box-shadow: 0 0 0 3px rgba(40, 120, 200, 0.1);
        }

        .confirm-setlist-button {
            width: 100%;
            border: 0;
            background: #0b2c55;
            color: #ffffff;
        }

        .create-setlist-link {
            color: #1768a8;
            font-size: 0.8rem;
            font-weight: 800;
            text-decoration: none;
        }

        .song-action-error {
            margin-bottom: 18px;
            padding: 12px 15px;
            border: 1px solid #fecaca;
            border-radius: 10px;
            background: #fff1f2;
            color: #a51d36;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .song-page .song-header {
            position: relative;
            padding: 42px 48px;
            background:
                linear-gradient(
                    135deg,
                    #07192f 0%,
                    #0b2c55 62%,
                    #154f87 100%
                );
            border-radius: 22px;
            box-shadow:
                0 18px 45px rgba(7, 25, 47, 0.18);
            color: white;
        }

        .song-header-setlist {
            position: absolute;
            top: 28px;
            right: 32px;
        }

         .song-header-setlist .add-to-setlist-button {
    border: 1px solid rgba(134, 239, 172, 0.35);
    background: rgba(22, 163, 74, 0.16);
    color: #fefffe;
    box-shadow: none;
    backdrop-filter: blur(6px);
}

        .song-page .song-author {
            margin: 0 0 10px;
            color: #82c5f4;
            font-family: "DM Sans", sans-serif;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .song-page .song-header h1 {
            margin: 0;
            color: white;
            font-family: "DM Sans", sans-serif;
            font-size: clamp(2.4rem, 6vw, 4.5rem);
            font-weight: 750;
            line-height: 1.05;
            letter-spacing: -0.055em;
        }

        .song-page .song-details {
            margin-top: 28px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .song-type-tag {
            display: inline-flex;
            min-height: 29px;
            align-items: center;
            padding: 5px 10px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-family: "DM Sans", sans-serif;
            font-size: 0.68rem;
            font-weight: 850;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .song-type-praise {
            border-color: #93c5fd;
            background: #dbeafe;
            color: #1d4f91;
        }

        .song-type-interlude {
            border-color: #fdba74;
            background: #ffedd5;
            color: #9a4511;
        }

        .song-type-worship {
            border-color: #c4b5fd;
            background: #ede9fe;
            color: #5b36a8;
        }

        .song-type-event {
            border-color: #86efac;
            background: #dcfce7;
            color: #166534;
        }

        /*
         * Secțiunile ocupă două coloane numai dacă există
         * cel puțin 430px disponibili pentru fiecare card.
         */

        .song-sections-grid {
            margin-top: 24px;
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(min(100%, 430px), 1fr));
            align-items: start;
            gap: 20px;
        }

        .public-song-section {
            min-width: 0;
            overflow: hidden;
            background:
                linear-gradient(
                    145deg,
                    rgba(255, 255, 255, 0.96),
                    rgba(239, 245, 251, 0.94)
                );
            border: 1px solid #cbd9e8;
            border-radius: 15px;
            box-shadow:
                0 8px 24px rgba(11, 44, 85, 0.07);
        }

        .public-section-heading {
            padding: 14px 20px 12px;
            background: rgba(11, 44, 85, 0.055);
            border-bottom: 1px solid #d7e1eb;
        }

        .public-section-type {
            display: inline-flex;
            align-items: center;
            color: #0b2c55;
            font-family: "DM Sans", sans-serif;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.11em;
            text-transform: uppercase;
        }

        .public-section-type::before {
            content: "";
            width: 7px;
            height: 7px;
            margin-right: 9px;
            background: #2878c8;
            border-radius: 50%;
        }

        .public-section-content {
            padding: 24px 20px 28px;
            overflow-x: auto;
        }

        /*
         * max-content păstrează relația exactă dintre acord
         * și caracterele versului. Cardul va avea scroll
         * numai dacă un rând este prea lung.
         */

        .public-song-line {
            width: max-content;
            min-width: 100%;
            margin-bottom: 14px;
        }

        .public-song-line:last-child {
            margin-bottom: 0;
        }

        .public-chord-lane {
            position: relative;
            min-height: 27px;
            font-family:
                "SFMono-Regular",
                Consolas,
                "Liberation Mono",
                monospace;
        }

        .public-chord {
            position: absolute;
            bottom: 2px;
            color: #1768a8;
            font-family:
                "SFMono-Regular",
                Consolas,
                "Liberation Mono",
                monospace;
            font-size: 0.94rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .public-lyric-line {
            min-height: 27px;
            color: #090e15;
            font-family:
                "SFMono-Regular",
                Consolas,
                "Liberation Mono",
                monospace;
            font-size: 0.98rem;
            font-weight: 500;
            white-space: pre;
        }

        /*
         * Indicatorul semitonurilor apare numai după
         * transpunerea piesei.
         */

        .transpose-steps[hidden] {
            display: none;
        }

        .transpose-steps {
            padding: 5px 9px;
            background: rgba(99, 179, 237, 0.16);
            border: 1px solid rgba(153, 205, 244, 0.25);
            border-radius: 6px;
            color: #a9d6f7;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        @media (max-width: 700px) {
            .song-page-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .song-page .song-header {
                padding: 31px 24px;
                border-radius: 17px;
            }

            .song-header-setlist {
                position: relative;
                top: auto;
                right: auto;
                display: inline-block;
                margin-bottom: 24px;
            }

            .song-page .song-header h1 {
                font-size: 2.45rem;
            }

            .song-page .song-details {
                align-items: flex-start;
                flex-direction: column;
            }

            .transpose-control {
                max-width: 100%;
                flex-wrap: wrap;
            }

            .song-sections-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .public-section-content {
                padding: 21px 17px 24px;
            }
        }
    </style>

    <script>
        const sharpNotes = [
            'C', 'C#', 'D', 'D#', 'E', 'F',
            'F#', 'G', 'G#', 'A', 'A#', 'B'
        ];

        const flatNotes = [
            'C', 'Db', 'D', 'Eb', 'E', 'F',
            'Gb', 'G', 'Ab', 'A', 'Bb', 'B'
        ];

        const currentKeyElement =
            document.getElementById('current-key');

        const stepsElement =
            document.getElementById('transpose-steps');

        const originalKey =
            currentKeyElement.dataset.originalKey;

        const chordElements =
            document.querySelectorAll('.public-chord');

        let transposeSteps = 0;

        function normalizeNote(note) {
            return note
                .replace('♯', '#')
                .replace('♭', 'b');
        }

        function findNoteIndex(note) {
            const normalizedNote =
                normalizeNote(note);

            let index =
                sharpNotes.indexOf(normalizedNote);

            if (index === -1) {
                index =
                    flatNotes.indexOf(normalizedNote);
            }

            return index;
        }

        function transposeNote(
            note,
            steps,
            preferFlats
        ) {
            const noteIndex =
                findNoteIndex(note);

            if (noteIndex === -1) {
                return note;
            }

            const newIndex =
                (noteIndex + steps + 120) % 12;

            return preferFlats
                ? flatNotes[newIndex]
                : sharpNotes[newIndex];
        }

        function transposeChordPart(
            part,
            steps,
            preferFlats
        ) {
            return part.replace(
                /^([A-G])([#b♯♭]?)/,
                function (
                    match,
                    letter,
                    accidental
                ) {
                    return transposeNote(
                        letter + accidental,
                        steps,
                        preferFlats
                    );
                }
            );
        }

        function transposeChord(
            chord,
            steps,
            preferFlats
        ) {
            return chord
                .split('/')
                .map(function (part) {
                    return transposeChordPart(
                        part,
                        steps,
                        preferFlats
                    );
                })
                .join('/');
        }

        function shouldPreferFlats() {
            if (
                originalKey.includes('b') ||
                originalKey.includes('♭')
            ) {
                return true;
            }

            return Array.from(chordElements)
                .some(function (element) {
                    const chord =
                        element.dataset.originalChord;

                    return (
                        chord.includes('b') ||
                        chord.includes('♭')
                    );
                });
        }

        function updateTransposeDisplay() {
            const preferFlats =
                shouldPreferFlats();

            currentKeyElement.textContent =
                transposeChord(
                    originalKey,
                    transposeSteps,
                    preferFlats
                );

            chordElements.forEach(
                function (element) {
                    element.textContent =
                        transposeChord(
                            element.dataset.originalChord,
                            transposeSteps,
                            preferFlats
                        );
                }
            );

            /*
             * În gama originală indicatorul dispare complet.
             */

            if (transposeSteps === 0) {
                stepsElement.hidden = true;
                stepsElement.textContent = '';

                return;
            }

            stepsElement.hidden = false;

            if (transposeSteps === 1) {
                stepsElement.textContent =
                    '+1 semiton';
            } else if (transposeSteps === -1) {
                stepsElement.textContent =
                    '−1 semiton';
            } else if (transposeSteps > 1) {
                stepsElement.textContent =
                    `+${transposeSteps} semitonuri`;
            } else {
                stepsElement.textContent =
                    `−${Math.abs(transposeSteps)} semitonuri`;
            }
        }

        document
            .getElementById('transpose-down')
            .addEventListener(
                'click',
                function () {
                    transposeSteps -= 1;
                    updateTransposeDisplay();
                }
            );

        document
            .getElementById('transpose-up')
            .addEventListener(
                'click',
                function () {
                    transposeSteps += 1;
                    updateTransposeDisplay();
                }
            );

        document
            .getElementById('transpose-reset')
            .addEventListener(
                'click',
                function () {
                    transposeSteps = 0;
                    updateTransposeDisplay();
                }
            );

        updateTransposeDisplay();
    </script>
@endsection