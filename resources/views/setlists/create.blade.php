@extends('layouts.app')

@section('title', 'Setlist nou | Ce cântăm duminică')

@section('content')
    <section class="setlist-builder-page">
        <header class="builder-header">
            <div>
                <p class="builder-eyebrow">
                    Setlist nou
                </p>

                <h1>Construiește setlistul</h1>

                <p>
                    Alege piesele, stabilește tonalitatea
                    fiecăreia și aranjează-le în ordinea dorită.
                </p>
            </div>

            <a href="{{ route('setlists.index') }}">
                ← Setlisturile mele
            </a>
        </header>

        @if ($errors->any())
            <div class="builder-errors">
                <strong>
                    Verifică următoarele informații:
                </strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('setlists.store') }}"
            method="POST"
            id="setlist-form"
        >
            @csrf

            <section class="setlist-details-card">
                <div class="builder-step-title">
                    <span>1</span>

                    <div>
                        <h2>Informațiile setlistului</h2>

                        <p>
                            Alege un nume ușor de recunoscut.
                        </p>
                    </div>
                </div>

                <div class="setlist-details-grid">
                    <div class="builder-field">
                        <label for="name">
                            Numele setlistului
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Exemplu: Duminică dimineața"
                            required
                        >
                    </div>

                    <div class="builder-field">
                        <label for="description">
                            Descriere
                            <small>opțional</small>
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Observații despre program..."
                        >{{ old('description') }}</textarea>
                    </div>
                </div>
            </section>

            <section class="songs-builder-card">
                <div class="builder-step-title">
                    <span>2</span>

                    <div>
                        <h2>Alege și ordonează piesele</h2>

                        <p>
                            Adaugă piesele din repertoriu,
                            apoi trage-le în ordinea dorită.
                        </p>
                    </div>
                </div>

                <div
                    id="builder-feedback"
                    class="builder-feedback"
                    hidden
                ></div>

                <div class="songs-builder-layout">
                    <div class="song-library-panel">
                        <div class="panel-heading">
                            <div>
                                <h3>Repertoriu</h3>

                                <p>
                                    Apasă pe o piesă pentru a o adăuga.
                                </p>
                            </div>

                            <span id="available-count">
                                {{ $songs->count() }}
                            </span>
                        </div>

                        <div class="builder-search">
                            <span aria-hidden="true">⌕</span>

                            <input
                                type="search"
                                id="library-search"
                                placeholder="Caută o piesă..."
                                autocomplete="off"
                            >
                        </div>

                        <div
                            id="song-library"
                            class="song-library"
                        ></div>

                        <div
                            id="library-empty"
                            class="library-empty"
                            hidden
                        >
                            Nu am găsit nicio piesă.
                        </div>
                    </div>

                    <div class="selected-songs-panel">
                        <div class="panel-heading">
                            <div>
                                <h3>Ordinea setlistului</h3>

                                <p>
                                    Trage piesele pentru a le reordona.
                                </p>
                            </div>

                            <span id="selected-count">
                                0
                            </span>
                        </div>

                        <div
                            id="selected-songs"
                            class="selected-songs"
                        ></div>

                        <div
                            id="selected-empty"
                            class="selected-empty"
                        >
                            <span>♪</span>

                            <strong>
                                Setlistul este gol
                            </strong>

                            <p>
                                Alege prima piesă din repertoriu.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <div id="hidden-song-fields"></div>

            <div class="builder-actions">
                <button
                    type="submit"
                    class="button button-primary"
                >
                    Salvează setlistul
                </button>

                <a
                    class="button button-secondary"
                    href="{{ route('setlists.index') }}"
                >
                    Renunță
                </a>
            </div>
        </form>
    </section>

    <style>
        .setlist-builder-page {
            max-width: 1180px;
            margin: 0 auto;
        }

        .builder-header {
            margin-bottom: 26px;
            padding: 38px 42px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 30px;
            background:
                linear-gradient(
                    135deg,
                    #07192f 0%,
                    #0b2c55 63%,
                    #15538c 100%
                );
            border-radius: 20px;
            box-shadow:
                0 18px 45px rgba(7, 25, 47, 0.17);
            color: white;
        }

        .builder-eyebrow {
            margin: 0 0 8px;
            color: #83c8f7;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .builder-header h1 {
            margin: 0;
            font-family: "DM Sans", sans-serif;
            font-size: clamp(2.1rem, 5vw, 3.5rem);
            line-height: 1.05;
            letter-spacing: -0.05em;
        }

        .builder-header > div > p:last-child {
            max-width: 620px;
            margin: 13px 0 0;
            color: #c7daeb;
            font-size: 0.94rem;
        }

        .builder-header > a {
            flex-shrink: 0;
            color: #dceaf6;
            font-size: 0.83rem;
            font-weight: 700;
        }

        .builder-header > a:hover {
            color: white;
        }

        .builder-errors,
        .builder-feedback {
            margin-bottom: 20px;
            padding: 14px 17px;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .builder-errors {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-left: 4px solid #be123c;
            color: #881337;
        }

        .builder-errors ul {
            margin: 7px 0 0;
            padding-left: 19px;
        }

        .builder-feedback {
            background: #fff7e6;
            border: 1px solid #f4ce82;
            border-left: 4px solid #d97706;
            color: #824707;
            font-weight: 650;
        }

        .builder-feedback[hidden] {
            display: none;
        }

        .setlist-details-card,
        .songs-builder-card {
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #cbd8e5;
            border-radius: 16px;
            box-shadow:
                0 10px 30px rgba(11, 44, 85, 0.07);
        }

        .songs-builder-card {
            margin-top: 20px;
        }

        .builder-step-title {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .builder-step-title > span {
            width: 37px;
            height: 37px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: #0b2c55;
            border-radius: 50%;
            color: white;
            font-size: 0.85rem;
            font-weight: 800;
        }

        .builder-step-title h2 {
            margin: 0;
            color: #07192f;
            font-family: "DM Sans", sans-serif;
            font-size: 1.35rem;
            letter-spacing: -0.03em;
        }

        .builder-step-title p {
            margin: 3px 0 0;
            color: #718096;
            font-size: 0.83rem;
        }

        .setlist-details-grid {
            display: grid;
            grid-template-columns:
                minmax(0, 0.85fr)
                minmax(0, 1.15fr);
            gap: 20px;
        }

        .builder-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .builder-field label {
            color: #111820;
            font-size: 0.84rem;
            font-weight: 750;
        }

        .builder-field label small {
            color: #83909e;
            font-size: 0.72rem;
            font-weight: 500;
        }

        .builder-field input,
        .builder-field textarea {
            width: 100%;
            padding: 12px 13px;
            background: #f5f7fa;
            border: 1px solid #c5d0dc;
            border-radius: 8px;
            color: #080d14;
            font-family: "DM Sans", sans-serif;
            font-size: 0.9rem;
            outline: none;
            resize: vertical;
        }

        .builder-field input:focus,
        .builder-field textarea:focus {
            background: white;
            border-color: #2878c8;
            box-shadow:
                0 0 0 4px rgba(40, 120, 200, 0.1);
        }

        .songs-builder-layout {
            display: grid;
            grid-template-columns:
                minmax(0, 0.9fr)
                minmax(0, 1.1fr);
            gap: 18px;
        }

        .song-library-panel,
        .selected-songs-panel {
            min-width: 0;
            padding: 18px;
            background: #f6f8fb;
            border: 1px solid #d4dee8;
            border-radius: 12px;
        }

        .selected-songs-panel {
            background: #edf4fa;
            border-color: #b9ccde;
        }

        .panel-heading {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
        }

        .panel-heading h3 {
            margin: 0;
            color: #07192f;
            font-family: "DM Sans", sans-serif;
            font-size: 1.05rem;
        }

        .panel-heading p {
            margin: 2px 0 0;
            color: #758395;
            font-size: 0.76rem;
        }

        .panel-heading > span {
            min-width: 29px;
            height: 29px;
            padding: 0 8px;
            display: grid;
            place-items: center;
            background: #dce8f3;
            border-radius: 8px;
            color: #0b2c55;
            font-size: 0.76rem;
            font-weight: 800;
        }

        .builder-search {
            position: relative;
            margin-bottom: 12px;
        }

        .builder-search > span {
            position: absolute;
            top: 50%;
            left: 13px;
            color: #266da8;
            font-size: 1.15rem;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .builder-search input {
            width: 100%;
            min-height: 43px;
            padding: 10px 12px 10px 39px;
            background: white;
            border: 1px solid #c5d0dc;
            border-radius: 8px;
            color: #111820;
            font-family: "DM Sans", sans-serif;
            font-size: 0.84rem;
            outline: none;
        }

        .builder-search input:focus {
            border-color: #2878c8;
            box-shadow:
                0 0 0 3px rgba(40, 120, 200, 0.09);
        }

        .song-library,
        .selected-songs {
            max-height: 510px;
            overflow-y: auto;
        }

        .library-song {
            width: 100%;
            margin-bottom: 7px;
            padding: 11px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: white;
            border: 1px solid #d4dde6;
            border-radius: 8px;
            color: #172231;
            text-align: left;
            cursor: pointer;
        }

        .library-song:hover {
            background: #e8f2fb;
            border-color: #91b7d9;
        }

        .library-song.is-selected {
            opacity: 0.48;
            cursor: not-allowed;
        }

        .library-song-text {
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .library-song-text strong {
            overflow: hidden;
            font-size: 0.84rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .library-song-text small {
            overflow: hidden;
            margin-top: 1px;
            color: #7a8797;
            font-size: 0.7rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .library-add-icon {
            width: 26px;
            height: 26px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: #e1edf8;
            border-radius: 7px;
            color: #155d9c;
            font-size: 1rem;
            font-weight: 800;
        }

        .selected-song {
            margin-bottom: 8px;
            padding: 12px;
            display: grid;
            grid-template-columns:
                30px minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            background: white;
            border: 1px solid #c6d5e3;
            border-radius: 9px;
            box-shadow:
                0 3px 10px rgba(11, 44, 85, 0.05);
            cursor: grab;
        }

        .selected-song.is-dragging {
            opacity: 0.55;
            border-color: #2878c8;
        }

        .drag-handle {
            color: #8291a2;
            font-size: 1.05rem;
            text-align: center;
            cursor: grab;
        }

        .selected-song-main {
            min-width: 0;
        }

        .selected-song-main strong {
            display: block;
            overflow: hidden;
            color: #0b213b;
            font-size: 0.85rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .selected-song-meta {
            margin-top: 5px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
        }

        .original-key {
            color: #748295;
            font-size: 0.7rem;
        }

        .transpose-picker {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .transpose-picker button {
            width: 24px;
            height: 24px;
            padding: 0;
            display: grid;
            place-items: center;
            background: #e8eef5;
            border: 0;
            border-radius: 5px;
            color: #0b2c55;
            font-weight: 800;
            cursor: pointer;
        }

        .transpose-picker button:hover {
            background: #d5e5f3;
        }

        .transpose-value {
            min-width: 54px;
            color: #42627f;
            font-size: 0.67rem;
            font-weight: 700;
            text-align: center;
        }

        .remove-selected-song {
            width: 29px;
            height: 29px;
            padding: 0;
            display: grid;
            place-items: center;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 7px;
            color: #9f1239;
            font-size: 1rem;
            cursor: pointer;
        }

        .remove-selected-song:hover {
            background: #ffe4e6;
            border-color: #fda4af;
        }

        .selected-empty,
        .library-empty {
            padding: 40px 18px;
            color: #718096;
            text-align: center;
        }

        .selected-empty > span {
            display: block;
            color: #2878c8;
            font-size: 1.8rem;
        }

        .selected-empty strong {
            display: block;
            margin-top: 7px;
            color: #0b2c55;
            font-size: 0.9rem;
        }

        .selected-empty p {
            margin: 3px 0 0;
            font-size: 0.75rem;
        }

        .library-empty {
            font-size: 0.8rem;
        }

        .builder-actions {
            margin-top: 22px;
            padding: 21px 0 0;
            display: flex;
            align-items: center;
            gap: 11px;
            border-top: 1px solid #d6e0e9;
        }

        @media (max-width: 800px) {
            .builder-header {
                padding: 31px 24px;
                align-items: flex-start;
                flex-direction: column;
            }

            .setlist-details-grid,
            .songs-builder-layout {
                grid-template-columns: 1fr;
            }

            .setlist-details-card,
            .songs-builder-card {
                padding: 23px 18px;
            }

            .song-library,
            .selected-songs {
                max-height: 390px;
            }
        }

        @media (max-width: 520px) {
            .builder-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .builder-actions .button {
                width: 100%;
            }

            .selected-song {
                grid-template-columns:
                    25px minmax(0, 1fr) auto;
            }
        }
    </style>

    @php
        $songsForJavascript = $songs
            ->map(function ($song) {
                return [
                    'id' => $song->id,
                    'title' => $song->title,
                    'author' => $song->author,
                    'key' => $song->key,
                ];
            })
            ->values();
    @endphp

    <script>
        const songs = @json($songsForJavascript);

        const libraryElement =
            document.getElementById('song-library');

        const selectedElement =
            document.getElementById('selected-songs');

        const selectedEmpty =
            document.getElementById('selected-empty');

        const libraryEmpty =
            document.getElementById('library-empty');

        const searchInput =
            document.getElementById('library-search');

        const selectedCount =
            document.getElementById('selected-count');

        const availableCount =
            document.getElementById('available-count');

        const hiddenFields =
            document.getElementById('hidden-song-fields');

        const feedback =
            document.getElementById('builder-feedback');

        const setlistForm =
            document.getElementById('setlist-form');

        let selectedSongs = [];
        let draggedSongId = null;

        function normalizeText(text) {
            return String(text || '')
                .toLocaleLowerCase('ro-RO')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function isSelected(songId) {
            return selectedSongs.some(
                function (item) {
                    return item.id === songId;
                }
            );
        }

        function addSong(songId) {
            if (isSelected(songId)) {
                return;
            }

            const song = songs.find(
                function (item) {
                    return item.id === songId;
                }
            );

            if (!song) {
                return;
            }

            selectedSongs.push({
                ...song,
                transposeSteps: 0,
            });

            feedback.hidden = true;

            renderEverything();
        }

        function removeSong(songId) {
            selectedSongs = selectedSongs.filter(
                function (song) {
                    return song.id !== songId;
                }
            );

            renderEverything();
        }

        function changeTranspose(songId, amount) {
            const song = selectedSongs.find(
                function (item) {
                    return item.id === songId;
                }
            );

            if (!song) {
                return;
            }

            song.transposeSteps = Math.max(
                -11,
                Math.min(
                    11,
                    song.transposeSteps + amount
                )
            );

            renderSelectedSongs();
            renderHiddenFields();
        }

        function transposeLabel(steps) {
            if (steps === 0) {
                return 'Original';
            }

            if (steps > 0) {
                return `+${steps} semit.`;
            }

            return `${steps} semit.`;
        }

        function renderLibrary() {
            const searchTerm =
                normalizeText(searchInput.value);

            libraryElement.innerHTML = '';

            let visibleCount = 0;

            songs.forEach(function (song) {
                const searchableText = normalizeText(
                    `${song.title} ${song.author || ''}`
                );

                if (!searchableText.includes(searchTerm)) {
                    return;
                }

                visibleCount += 1;

                const button =
                    document.createElement('button');

                button.type = 'button';
                button.className = 'library-song';

                if (isSelected(song.id)) {
                    button.classList.add('is-selected');
                }

                const text =
                    document.createElement('span');

                text.className = 'library-song-text';

                const title =
                    document.createElement('strong');

                title.textContent = song.title;

                const author =
                    document.createElement('small');

                author.textContent =
                    song.author || 'Autor necunoscut';

                text.append(title, author);

                const icon =
                    document.createElement('span');

                icon.className = 'library-add-icon';
                icon.textContent = isSelected(song.id)
                    ? '✓'
                    : '+';

                button.append(text, icon);

                button.addEventListener(
                    'click',
                    function () {
                        addSong(song.id);
                    }
                );

                libraryElement.appendChild(button);
            });

            libraryEmpty.hidden =
                visibleCount !== 0;

            availableCount.textContent =
                songs.length - selectedSongs.length;
        }

        function createTransposePicker(song) {
            const picker =
                document.createElement('span');

            picker.className = 'transpose-picker';

            const downButton =
                document.createElement('button');

            downButton.type = 'button';
            downButton.textContent = '−';
            downButton.title = 'Un semiton mai jos';

            downButton.addEventListener(
                'click',
                function () {
                    changeTranspose(song.id, -1);
                }
            );

            const value =
                document.createElement('span');

            value.className = 'transpose-value';
            value.textContent =
                transposeLabel(song.transposeSteps);

            const upButton =
                document.createElement('button');

            upButton.type = 'button';
            upButton.textContent = '+';
            upButton.title = 'Un semiton mai sus';

            upButton.addEventListener(
                'click',
                function () {
                    changeTranspose(song.id, 1);
                }
            );

            picker.append(
                downButton,
                value,
                upButton
            );

            return picker;
        }

        function renderSelectedSongs() {
            selectedElement.innerHTML = '';

            selectedEmpty.hidden =
                selectedSongs.length !== 0;

            selectedCount.textContent =
                selectedSongs.length;

            selectedSongs.forEach(
                function (song, index) {
                    const card =
                        document.createElement('article');

                    card.className = 'selected-song';
                    card.draggable = true;
                    card.dataset.songId = song.id;

                    const handle =
                        document.createElement('span');

                    handle.className = 'drag-handle';
                    handle.textContent = '⋮⋮';
                    handle.title = 'Trage pentru reordonare';

                    const main =
                        document.createElement('div');

                    main.className = 'selected-song-main';

                    const title =
                        document.createElement('strong');

                    title.textContent =
                        `${index + 1}. ${song.title}`;

                    const meta =
                        document.createElement('div');

                    meta.className =
                        'selected-song-meta';

                    const key =
                        document.createElement('span');

                    key.className = 'original-key';
                    key.textContent =
                        `Original: ${song.key}`;

                    meta.append(
                        key,
                        createTransposePicker(song)
                    );

                    main.append(title, meta);

                    const removeButton =
                        document.createElement('button');

                    removeButton.type = 'button';

                    removeButton.className =
                        'remove-selected-song';

                    removeButton.textContent = '×';
                    removeButton.title =
                        'Elimină din setlist';

                    removeButton.addEventListener(
                        'click',
                        function () {
                            removeSong(song.id);
                        }
                    );

                    card.addEventListener(
                        'dragstart',
                        function () {
                            draggedSongId = song.id;

                            card.classList.add(
                                'is-dragging'
                            );
                        }
                    );

                    card.addEventListener(
                        'dragend',
                        function () {
                            draggedSongId = null;

                            card.classList.remove(
                                'is-dragging'
                            );
                        }
                    );

                    card.addEventListener(
                        'dragover',
                        function (event) {
                            event.preventDefault();
                        }
                    );

                    card.addEventListener(
                        'drop',
                        function (event) {
                            event.preventDefault();

                            if (
                                draggedSongId === null ||
                                draggedSongId === song.id
                            ) {
                                return;
                            }

                            const fromIndex =
                                selectedSongs.findIndex(
                                    function (item) {
                                        return (
                                            item.id ===
                                            draggedSongId
                                        );
                                    }
                                );

                            const toIndex =
                                selectedSongs.findIndex(
                                    function (item) {
                                        return (
                                            item.id ===
                                            song.id
                                        );
                                    }
                                );

                            const moved =
                                selectedSongs.splice(
                                    fromIndex,
                                    1
                                )[0];

                            selectedSongs.splice(
                                toIndex,
                                0,
                                moved
                            );

                            renderEverything();
                        }
                    );

                    card.append(
                        handle,
                        main,
                        removeButton
                    );

                    selectedElement.appendChild(card);
                }
            );
        }

        function renderHiddenFields() {
            hiddenFields.innerHTML = '';

            selectedSongs.forEach(function (song) {
                const songInput =
                    document.createElement('input');

                songInput.type = 'hidden';
                songInput.name = 'song_ids[]';
                songInput.value = song.id;

                const transposeInput =
                    document.createElement('input');

                transposeInput.type = 'hidden';

                transposeInput.name =
                    `transpose_steps[${song.id}]`;

                transposeInput.value =
                    song.transposeSteps;

                hiddenFields.append(
                    songInput,
                    transposeInput
                );
            });
        }

        function renderEverything() {
            renderLibrary();
            renderSelectedSongs();
            renderHiddenFields();
        }

        searchInput.addEventListener(
            'input',
            renderLibrary
        );

        setlistForm.addEventListener(
            'submit',
            function (event) {
                if (selectedSongs.length === 0) {
                    event.preventDefault();

                    feedback.textContent =
                        'Adaugă cel puțin o piesă în setlist.';

                    feedback.hidden = false;

                    document
                        .querySelector('.songs-builder-card')
                        .scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                        });
                }
            }
        );

        renderEverything();
    </script>
@endsection