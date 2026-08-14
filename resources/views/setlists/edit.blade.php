@extends('layouts.app')

@section('content')
@php
    $selectedSongIds = old(
        'song_ids',
        $setlist->songs->pluck('id')->map(fn ($id) => (string) $id)->values()->all()
    );

    $savedTransposeSteps = $setlist->songs
        ->mapWithKeys(function ($song) {
            return [
                (string) $song->id => (int) $song->pivot->transpose_steps
            ];
        })
        ->all();

    $transposeSteps = old(
        'transpose_steps',
        $savedTransposeSteps
    );

    $songsForJavascript = $songs
        ->map(function ($song) {
            return [
                'id' => (string) $song->id,
                'title' => $song->title,
                'author' => $song->author,
                'key' => $song->key,
            ];
        })
        ->values();

    $selectedIdsForJavascript = collect($selectedSongIds)
        ->map(function ($id) {
            return (string) $id;
        })
        ->values();
@endphp

<div class="setlist-editor-page">
    <header class="editor-header">
        <div>
            <a
                href="{{ route('setlists.show', $setlist) }}"
                class="back-link"
            >
                ← Înapoi la setlist
            </a>

            <span class="editor-eyebrow">Editare setlist</span>

            <h1>{{ $setlist->name }}</h1>

            <p>
                Modifică informațiile, piesele, ordinea sau tonalitatea
                fiecărei piese.
            </p>
        </div>
    </header>

    <main class="editor-content">
        @if ($errors->any())
            <div class="error-card">
                <strong>Verifică următoarele informații:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('setlists.update', $setlist) }}"
            method="POST"
            id="setlist-form"
        >
            @csrf
            @method('PUT')

            <section class="form-card information-card">
                <div class="card-heading">
                    <span class="step-number">01</span>

                    <div>
                        <h2>Informațiile setlistului</h2>
                        <p>Schimbă numele sau descrierea setlistului.</p>
                    </div>
                </div>

                <div class="form-fields">
                    <div class="form-group">
                        <label for="name">Numele setlistului</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $setlist->name) }}"
                            placeholder="De exemplu: Duminică dimineața"
                            required
                            maxlength="150"
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">
                            Descriere
                            <span>opțional</span>
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Poți adăuga o scurtă notă despre acest setlist."
                        >{{ old('description', $setlist->description) }}</textarea>
                    </div>

<div class="form-group team-form-group">
    <label for="team_id">
        Echipa
        <span>opțional</span>
    </label>

    <select
        id="team_id"
        name="team_id"
    >
        <option value="">
            Setlist personal — fără echipă
        </option>

        @foreach ($teams as $team)
            <option
                value="{{ $team->id }}"
                @selected(
                    (string) old(
                        'team_id',
                        $selectedTeamId
                    ) === (string) $team->id
                )
            >
                {{ $team->name }}
            </option>
        @endforeach
    </select>

    <small class="team-field-help">
        @if ($setlist->is_live)
            Acest setlist este live și trebuie să rămână
            asociat unei echipe.
        @else
            Poți păstra setlistul personal sau îl poți
            asocia unei echipe.
        @endif
    </small>
</div>

                </div>
            </section>

            <section class="form-card songs-card">
                <div class="card-heading">
                    <span class="step-number">02</span>

                    <div>
                        <h2>Alege și ordonează piesele</h2>
                        <p>
                            Adaugă piesele din stânga, apoi trage-le în ordinea
                            în care vor fi cântate.
                        </p>
                    </div>
                </div>

                <div class="song-editor-grid">
                    <div class="song-library-panel">
                        <div class="panel-heading">
                            <div>
                                <span class="panel-eyebrow">
                                    Biblioteca de piese
                                </span>

                                <h3>Adaugă piese</h3>
                            </div>

                            <span
                                id="available-count"
                                class="song-count"
                            ></span>
                        </div>

                        <div class="search-wrapper">
                            <svg
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                                class="search-icon"
                            >
                                <path
                                    d="m21 21-4.3-4.3m2.3-5.2a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"
                                />
                            </svg>

                            <input
                                type="search"
                                id="song-search"
                                placeholder="Caută după titlu sau autor..."
                                autocomplete="off"
                            >
                        </div>

                        <div
                            id="available-songs"
                            class="song-list available-songs"
                        ></div>

                        <div
                            id="no-search-results"
                            class="empty-message"
                            hidden
                        >
                            Nu am găsit nicio piesă.
                        </div>
                    </div>

                    <div class="selected-songs-panel">
                        <div class="panel-heading">
                            <div>
                                <span class="panel-eyebrow">
                                    Ordinea setlistului
                                </span>

                                <h3>Piese selectate</h3>
                            </div>

                            <span
                                id="selected-count"
                                class="song-count"
                            ></span>
                        </div>

                        <div
                            id="selected-songs"
                            class="song-list selected-songs"
                        ></div>

                        <div
                            id="selected-empty"
                            class="selected-empty"
                        >
                            <div class="empty-icon">♪</div>
                            <strong>Nu ai selectat nicio piesă</strong>
                            <span>
                                Apasă „Adaugă” pentru a începe setlistul.
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <div id="hidden-fields"></div>

            <div class="form-footer">
                <a
                    href="{{ route('setlists.show', $setlist) }}"
                    class="button button-secondary"
                >
                    Renunță
                </a>

                <button
                    type="submit"
                    class="button button-primary"
                    id="save-button"
                >
                    Salvează modificările
                </button>
            </div>
        </form>

        <section class="danger-zone">
            <div>
                <span class="danger-eyebrow">Zonă periculoasă</span>
                <h2>Șterge setlistul</h2>
                <p>
                    Setlistul și ordinea pieselor sale vor fi șterse definitiv.
                    Piesele nu vor fi șterse.
                </p>
            </div>

            <form
                action="{{ route('setlists.destroy', $setlist) }}"
                method="POST"
                onsubmit="return confirm('Sigur vrei să ștergi acest setlist? Acțiunea nu poate fi anulată.')"
            >
                @csrf
                @method('DELETE')

                <button type="submit" class="button button-delete">
                    Șterge setlistul
                </button>
            </form>
        </section>
    </main>
</div>

<style>
    .setlist-editor-page {
        min-height: 100vh;
        padding: 32px;
        background:
            radial-gradient(
                circle at top right,
                rgba(37, 128, 199, 0.18),
                transparent 32%
            ),
            linear-gradient(145deg, #eaf2fa 0%, #f8fafc 55%, #e8eef5 100%);
        color: #071a2e;
        font-family: "DM Sans", sans-serif;
    }

    .editor-header,
    .editor-content {
        width: min(1380px, 100%);
        margin: 0 auto;
    }

    .editor-header {
        padding: clamp(34px, 5vw, 68px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        background:
            radial-gradient(
                circle at 90% 10%,
                rgba(48, 137, 216, 0.35),
                transparent 38%
            ),
            linear-gradient(135deg, #061a30 0%, #0d345e 100%);
        box-shadow: 0 24px 70px rgba(5, 28, 52, 0.18);
    }

    .back-link {
        display: inline-flex;
        margin-bottom: 34px;
        color: #9ad6ff;
        font-size: 0.88rem;
        font-weight: 800;
        text-decoration: none;
    }

    .back-link:hover {
        color: #ffffff;
    }

    .editor-eyebrow {
        display: block;
        margin-bottom: 12px;
        color: #8dd1ff;
        font-size: 0.75rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
    }

    .editor-header h1 {
        margin: 0;
        color: #ffffff;
        font-size: clamp(2.6rem, 6vw, 5.3rem);
        line-height: 0.98;
        letter-spacing: -0.055em;
    }

    .editor-header p {
        max-width: 680px;
        margin: 20px 0 0;
        color: #c4d6e7;
        font-size: 1rem;
        line-height: 1.7;
    }

    .editor-content {
        padding: 32px 0 60px;
    }

    .error-card {
        margin-bottom: 22px;
        padding: 20px 24px;
        border: 1px solid #fecaca;
        border-radius: 16px;
        background: #fff1f2;
        color: #9f1239;
    }

    .error-card ul {
        margin: 10px 0 0;
        padding-left: 20px;
    }

    .form-card {
        margin-bottom: 24px;
        overflow: hidden;
        border: 1px solid rgba(8, 42, 74, 0.1);
        border-radius: 23px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 16px 45px rgba(7, 36, 65, 0.07);
    }

    .card-heading {
        display: flex;
        align-items: flex-start;
        gap: 17px;
        padding: 27px 30px;
        border-bottom: 1px solid #e5ebf1;
    }

    .step-number {
        display: inline-flex;
        width: 43px;
        height: 43px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #092b4e;
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 900;
    }

    .card-heading h2 {
        margin: 0 0 5px;
        color: #09233e;
        font-size: 1.2rem;
    }

    .card-heading p {
        margin: 0;
        color: #718296;
        font-size: 0.88rem;
        line-height: 1.5;
    }

    .form-fields {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 22px;
        padding: 30px;
    }

    .form-group {
        display: grid;
        align-content: start;
        gap: 9px;
    }

    .form-group label {
        color: #183651;
        font-size: 0.8rem;
        font-weight: 900;
    }

    .form-group label span {
        margin-left: 5px;
        color: #93a0ad;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .form-group input,
.form-group textarea,
.form-group select,
.search-wrapper input {
        width: 100%;
        border: 1px solid #ccd8e3;
        outline: none;
        background: #f9fbfd;
        color: #101828;
        font: inherit;
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease,
            background 0.2s ease;
    }

    .form-group input {
        min-height: 53px;
        padding: 0 16px;
        border-radius: 12px;
    }

    .form-group textarea {
        min-height: 118px;
        resize: vertical;
        padding: 15px 16px;
        border-radius: 12px;
        line-height: 1.55;
    }

.team-form-group {
    grid-column: 1 / -1;
}

.form-group select {
    width: 100%;
    min-height: 53px;
    padding: 0 16px;
    border-radius: 12px;
    cursor: pointer;
}

.team-field-help {
    color: #7b8998;
    font-size: 0.72rem;
    line-height: 1.5;
}

    .form-group input:focus,
.form-group textarea:focus,
.form-group select:focus,
.search-wrapper input:focus {
        border-color: #207ab9;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(32, 122, 185, 0.12);
    }

    .song-editor-grid {
        display: grid;
        grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.15fr);
        min-height: 560px;
    }

    .song-library-panel,
    .selected-songs-panel {
        min-width: 0;
        padding: 28px;
    }

    .song-library-panel {
        border-right: 1px solid #e3eaf0;
        background: #f7f9fc;
    }

    .selected-songs-panel {
        background: #ffffff;
    }

    .panel-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }

    .panel-eyebrow {
        display: block;
        margin-bottom: 4px;
        color: #2179b8;
        font-size: 0.63rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .panel-heading h3 {
        margin: 0;
        color: #102a44;
        font-size: 1.15rem;
    }

    .song-count {
        display: inline-flex;
        min-width: 32px;
        height: 32px;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
        border-radius: 9px;
        background: #e1edf7;
        color: #155f96;
        font-size: 0.75rem;
        font-weight: 900;
    }

    .search-wrapper {
        position: relative;
        margin-bottom: 18px;
    }

    .search-wrapper input {
        height: 49px;
        padding: 0 15px 0 44px;
        border-radius: 12px;
    }

    .search-icon {
        position: absolute;
        top: 50%;
        left: 16px;
        z-index: 1;
        width: 18px;
        height: 18px;
        fill: none;
        stroke: #6e8092;
        stroke-linecap: round;
        stroke-width: 2;
        transform: translateY(-50%);
    }

    .song-list {
        display: grid;
        align-content: start;
        gap: 10px;
    }

    .available-song,
    .selected-song {
        border: 1px solid #dde5ed;
        border-radius: 14px;
        background: #ffffff;
    }

    .available-song {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px;
    }

    .song-information {
        min-width: 0;
    }

    .song-information strong {
        display: block;
        overflow: hidden;
        color: #102b45;
        font-size: 0.9rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .song-information span {
        display: block;
        margin-top: 4px;
        overflow: hidden;
        color: #77889a;
        font-size: 0.75rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .add-song-button,
    .remove-song-button {
        flex-shrink: 0;
        border: 0;
        font-family: inherit;
        font-weight: 900;
        cursor: pointer;
    }

    .add-song-button {
        padding: 9px 12px;
        border-radius: 9px;
        background: #dcecf8;
        color: #105f99;
        font-size: 0.72rem;
    }

    .add-song-button:hover {
        background: #c9e2f5;
    }

    .selected-song {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 13px;
        padding: 14px;
        cursor: grab;
        transition:
            opacity 0.2s ease,
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .selected-song:active {
        cursor: grabbing;
    }

    .selected-song.is-dragging {
        opacity: 0.42;
    }

    .selected-song.drag-over {
        border-color: #2680bd;
        box-shadow: 0 0 0 3px rgba(38, 128, 189, 0.1);
    }

    .drag-handle {
        color: #8191a1;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -0.18em;
        user-select: none;
    }

    .selected-song-content {
        display: grid;
        min-width: 0;
        gap: 12px;
    }

    .selected-song-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .remove-song-button {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #fff0f1;
        color: #be293d;
        font-size: 1rem;
    }

    .remove-song-button:hover {
        background: #ffe0e4;
    }

    .transpose-control {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .transpose-label {
        margin-right: 3px;
        color: #728397;
        font-size: 0.7rem;
        font-weight: 800;
    }

    .transpose-button {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        border: 1px solid #ccd8e3;
        border-radius: 8px;
        background: #ffffff;
        color: #153b5c;
        font-family: inherit;
        font-size: 1rem;
        font-weight: 900;
        cursor: pointer;
    }

    .transpose-button:hover {
        border-color: #2380bf;
        background: #edf6fc;
    }

    .transpose-value {
        min-width: 88px;
        color: #0b3357;
        font-size: 0.75rem;
        font-weight: 900;
        text-align: center;
    }

    .reset-transpose {
        border: 0;
        background: transparent;
        color: #2877ae;
        font-family: inherit;
        font-size: 0.7rem;
        font-weight: 800;
        cursor: pointer;
    }

    .selected-empty,
    .empty-message {
        padding: 50px 20px;
        color: #8190a0;
        text-align: center;
    }

    .selected-empty {
        display: grid;
        justify-items: center;
        gap: 7px;
        border: 1px dashed #ccd8e3;
        border-radius: 15px;
        background: #fafcfe;
    }

    .selected-empty[hidden] {
        display: none;
    }

    .empty-icon {
        display: flex;
        width: 47px;
        height: 47px;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
        border-radius: 50%;
        background: #e5f0f8;
        color: #1d72aa;
        font-size: 1.25rem;
        font-weight: 900;
    }

    .selected-empty strong {
        color: #3e5368;
        font-size: 0.85rem;
    }

    .selected-empty span {
        font-size: 0.76rem;
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 8px 0 28px;
    }

    .button {
        display: inline-flex;
        min-height: 50px;
        align-items: center;
        justify-content: center;
        padding: 0 22px;
        border: 1px solid transparent;
        border-radius: 13px;
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
        transition:
            transform 0.2s ease,
            background 0.2s ease,
            border-color 0.2s ease;
    }

    .button:hover {
        transform: translateY(-2px);
    }

    .button-primary {
        background: linear-gradient(135deg, #071f38, #0c4e82);
        color: #ffffff;
        box-shadow: 0 10px 24px rgba(7, 42, 74, 0.18);
    }

    .button-secondary {
        border-color: #cbd6e0;
        background: #ffffff;
        color: #304b64;
    }

    .button-delete {
        border-color: #fecaca;
        background: #fff1f2;
        color: #b42334;
    }

    .danger-zone {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
        padding: 27px 30px;
        border: 1px solid #fecdd3;
        border-radius: 20px;
        background: rgba(255, 247, 248, 0.9);
    }

    .danger-eyebrow {
        color: #be123c;
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .danger-zone h2 {
        margin: 5px 0 6px;
        color: #881337;
        font-size: 1.1rem;
    }

    .danger-zone p {
        margin: 0;
        color: #8c5364;
        font-size: 0.82rem;
        line-height: 1.55;
    }

    @media (max-width: 900px) {
        .setlist-editor-page {
            padding: 14px;
        }

        .form-fields,
        .song-editor-grid {
            grid-template-columns: 1fr;
        }

        .song-library-panel {
            border-right: 0;
            border-bottom: 1px solid #e3eaf0;
        }
    }

    @media (max-width: 600px) {
        .editor-header {
            padding: 30px 22px;
        }

        .editor-header h1 {
            font-size: 2.6rem;
        }

        .card-heading,
        .song-library-panel,
        .selected-songs-panel {
            padding: 22px 18px;
        }

        .form-fields {
            padding: 22px 18px;
        }

        .selected-song {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .form-footer,
        .danger-zone {
            align-items: stretch;
            flex-direction: column;
        }

        .form-footer .button,
        .danger-zone .button {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const allSongs = @json($songsForJavascript);

        let selectedIds = @json($selectedIdsForJavascript);

        const initialTransposeSteps = @json($transposeSteps);

        const transposeSteps = {};

        Object.entries(initialTransposeSteps).forEach(([songId, steps]) => {
            transposeSteps[String(songId)] = Number(steps) || 0;
        });

        selectedIds.forEach(songId => {
            if (transposeSteps[songId] === undefined) {
                transposeSteps[songId] = 0;
            }
        });

        const searchInput = document.getElementById('song-search');
        const availableContainer =
            document.getElementById('available-songs');
        const selectedContainer =
            document.getElementById('selected-songs');
        const selectedEmpty =
            document.getElementById('selected-empty');
        const noSearchResults =
            document.getElementById('no-search-results');
        const availableCount =
            document.getElementById('available-count');
        const selectedCount =
            document.getElementById('selected-count');
        const hiddenFields =
            document.getElementById('hidden-fields');
        const form =
            document.getElementById('setlist-form');

        let draggedSongId = null;

        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value ?? '';

            return element.innerHTML;
        }

        function getSong(songId) {
            return allSongs.find(
                song => String(song.id) === String(songId)
            );
        }

        function formatTranspose(steps) {
            if (steps === 0) {
                return 'Tonalitate originală';
            }

            const sign = steps > 0 ? '+' : '';
            const word = Math.abs(steps) === 1
                ? 'semiton'
                : 'semitonuri';

            return `${sign}${steps} ${word}`;
        }

        function renderAvailableSongs() {
            const query = searchInput.value
                .trim()
                .toLocaleLowerCase('ro');

            const availableSongs = allSongs.filter(song => {
                if (selectedIds.includes(String(song.id))) {
                    return false;
                }

                const searchableText =
                    `${song.title} ${song.author}`.toLocaleLowerCase('ro');

                return searchableText.includes(query);
            });

            availableContainer.innerHTML = availableSongs
                .map(song => `
                    <div class="available-song">
                        <div class="song-information">
                            <strong>${escapeHtml(song.title)}</strong>
                            <span>
                                ${escapeHtml(song.author)}
                                · Tonalitate ${escapeHtml(song.key)}
                            </span>
                        </div>

                        <button
                            type="button"
                            class="add-song-button"
                            data-song-id="${song.id}"
                        >
                            + Adaugă
                        </button>
                    </div>
                `)
                .join('');

            availableCount.textContent = availableSongs.length;
            noSearchResults.hidden = availableSongs.length !== 0;

            availableContainer
                .querySelectorAll('.add-song-button')
                .forEach(button => {
                    button.addEventListener('click', () => {
                        addSong(button.dataset.songId);
                    });
                });
        }

        function renderSelectedSongs() {
            selectedContainer.innerHTML = selectedIds
                .map((songId, index) => {
                    const song = getSong(songId);

                    if (!song) {
                        return '';
                    }

                    const steps = transposeSteps[songId] ?? 0;

                    return `
                        <div
                            class="selected-song"
                            draggable="true"
                            data-song-id="${song.id}"
                        >
                            <span
                                class="drag-handle"
                                title="Trage pentru reordonare"
                            >
                                ⋮⋮
                            </span>

                            <div class="selected-song-content">
                                <div class="selected-song-top">
                                    <div class="song-information">
                                        <strong>
                                            ${index + 1}. ${escapeHtml(song.title)}
                                        </strong>

                                        <span>
                                            ${escapeHtml(song.author)}
                                            · Tonalitate ${escapeHtml(song.key)}
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        class="remove-song-button"
                                        data-song-id="${song.id}"
                                        title="Elimină piesa"
                                    >
                                        ×
                                    </button>
                                </div>

                                <div class="transpose-control">
                                    <span class="transpose-label">
                                        Transpunere:
                                    </span>

                                    <button
                                        type="button"
                                        class="transpose-button"
                                        data-action="decrease"
                                        data-song-id="${song.id}"
                                        aria-label="Coboară un semiton"
                                    >
                                        −
                                    </button>

                                    <span class="transpose-value">
                                        ${formatTranspose(steps)}
                                    </span>

                                    <button
                                        type="button"
                                        class="transpose-button"
                                        data-action="increase"
                                        data-song-id="${song.id}"
                                        aria-label="Urcă un semiton"
                                    >
                                        +
                                    </button>

                                    ${
                                        steps !== 0
                                            ? `
                                                <button
                                                    type="button"
                                                    class="reset-transpose"
                                                    data-action="reset"
                                                    data-song-id="${song.id}"
                                                >
                                                    Revino la original
                                                </button>
                                            `
                                            : ''
                                    }
                                </div>
                            </div>
                        </div>
                    `;
                })
                .join('');

            selectedCount.textContent = selectedIds.length;
            selectedEmpty.hidden = selectedIds.length !== 0;

            selectedContainer
                .querySelectorAll('.remove-song-button')
                .forEach(button => {
                    button.addEventListener('click', () => {
                        removeSong(button.dataset.songId);
                    });
                });

            selectedContainer
                .querySelectorAll('.transpose-button, .reset-transpose')
                .forEach(button => {
                    button.addEventListener('click', () => {
                        changeTranspose(
                            button.dataset.songId,
                            button.dataset.action
                        );
                    });
                });

            attachDragEvents();
            renderHiddenFields();
        }

        function renderHiddenFields() {
            hiddenFields.innerHTML = selectedIds
                .map(songId => `
                    <input
                        type="hidden"
                        name="song_ids[]"
                        value="${songId}"
                    >

                    <input
                        type="hidden"
                        name="transpose_steps[${songId}]"
                        value="${transposeSteps[songId] ?? 0}"
                    >
                `)
                .join('');
        }

        function addSong(songId) {
            songId = String(songId);

            if (selectedIds.includes(songId)) {
                return;
            }

            selectedIds.push(songId);
            transposeSteps[songId] = 0;

            renderAll();
        }

        function removeSong(songId) {
            songId = String(songId);

            selectedIds = selectedIds.filter(
                selectedId => selectedId !== songId
            );

            delete transposeSteps[songId];

            renderAll();
        }

        function changeTranspose(songId, action) {
            songId = String(songId);

            let steps = Number(transposeSteps[songId] ?? 0);

            if (action === 'increase' && steps < 11) {
                steps += 1;
            }

            if (action === 'decrease' && steps > -11) {
                steps -= 1;
            }

            if (action === 'reset') {
                steps = 0;
            }

            transposeSteps[songId] = steps;

            renderSelectedSongs();
        }

        function attachDragEvents() {
            selectedContainer
                .querySelectorAll('.selected-song')
                .forEach(songElement => {
                    songElement.addEventListener(
                        'dragstart',
                        event => {
                            draggedSongId =
                                songElement.dataset.songId;

                            songElement.classList.add(
                                'is-dragging'
                            );

                            event.dataTransfer.effectAllowed = 'move';
                            event.dataTransfer.setData(
                                'text/plain',
                                draggedSongId
                            );
                        }
                    );

                    songElement.addEventListener(
                        'dragend',
                        () => {
                            draggedSongId = null;

                            selectedContainer
                                .querySelectorAll('.selected-song')
                                .forEach(element => {
                                    element.classList.remove(
                                        'is-dragging',
                                        'drag-over'
                                    );
                                });
                        }
                    );

                    songElement.addEventListener(
                        'dragover',
                        event => {
                            event.preventDefault();

                            if (
                                !draggedSongId ||
                                draggedSongId ===
                                    songElement.dataset.songId
                            ) {
                                return;
                            }

                            songElement.classList.add('drag-over');
                            event.dataTransfer.dropEffect = 'move';
                        }
                    );

                    songElement.addEventListener(
                        'dragleave',
                        () => {
                            songElement.classList.remove(
                                'drag-over'
                            );
                        }
                    );

                    songElement.addEventListener(
                        'drop',
                        event => {
                            event.preventDefault();

                            songElement.classList.remove(
                                'drag-over'
                            );

                            const targetSongId =
                                songElement.dataset.songId;

                            if (
                                !draggedSongId ||
                                draggedSongId === targetSongId
                            ) {
                                return;
                            }

                            const draggedIndex =
                                selectedIds.indexOf(draggedSongId);

                            const targetIndex =
                                selectedIds.indexOf(targetSongId);

                            selectedIds.splice(draggedIndex, 1);
                            selectedIds.splice(
                                targetIndex,
                                0,
                                draggedSongId
                            );

                            renderSelectedSongs();
                        }
                    );
                });
        }

        function renderAll() {
            renderAvailableSongs();
            renderSelectedSongs();
        }

        searchInput.addEventListener(
            'input',
            renderAvailableSongs
        );

        form.addEventListener('submit', event => {
            if (selectedIds.length === 0) {
                event.preventDefault();

                alert(
                    'Setlistul trebuie să conțină cel puțin o piesă.'
                );

                return;
            }

            renderHiddenFields();
        });

        renderAll();
    });
</script>
@endsection