@extends('layouts.app')

@section('content')
@php
    $isOwner = auth()->check() && auth()->id() === $setlist->user_id;
@endphp

<div class="setlist-page">
    <header class="setlist-header">
        <button
    type="button"
    class="fullscreen-toggle"
    id="fullscreen-toggle"
    aria-label="Deschide setlistul pe ecran complet"
    title="Ecran complet"
>
    <span class="fullscreen-corner"></span>
</button>
        <div class="setlist-header__content">
            <a href="{{ route('setlists.index') }}" class="back-link">
                ← Înapoi la setlisturi
            </a>

            <div class="setlist-title-row">
                <div>
                    <div class="setlist-eyebrow">
                        Setlist creat de {{ $setlist->user->name }}

                        @if ($setlist->is_live)
                            <span class="live-badge">
                                <span class="live-dot"></span>
                                Live
                            </span>
                        @endif
                    </div>

                    <h1>{{ $setlist->name }}</h1>

                    @if ($setlist->description)
                        <p class="setlist-description">
                            {{ $setlist->description }}
                        </p>
                    @endif
                </div>

                <div class="setlist-actions">
    @if ($isOwner)
        <a
            href="{{ route('setlists.edit', $setlist) }}"
            class="button button-secondary"
        >
            Editează
        </a>

        <form
            action="{{ route('setlists.live', $setlist) }}"
            method="POST"
        >
            @csrf
            @method('PATCH')

            <button
                type="submit"
                class="button {{ $setlist->is_live ? 'button-danger' : 'button-live' }}"
            >
                {{ $setlist->is_live
                    ? 'Oprește modul live'
                    : 'Declară setlist live'
                }}
            </button>
        </form>
    @endif

    <a
        href="{{ route('setlists.export.options', $setlist) }}"
        class="button button-export"
    >
        Exportă setlistul
        <span>→</span>
    </a>
</div>
            </div>

            
        </div>
    </header>

    <main class="setlist-content">
        @if ($setlist->songs->isEmpty())
            <div class="empty-state">
                <h2>Setlistul nu conține piese</h2>
                <p>Poți adăuga piese din pagina de editare.</p>

                @if ($isOwner)
                    <a
                        href="{{ route('setlists.edit', $setlist) }}"
                        class="button button-primary"
                    >
                        Adaugă piese
                    </a>
                @endif
            </div>
        @else
            <section class="setlist-order-panel">
                <div class="setlist-order-heading">
                    <div>
                        <span>Ordinea pieselor</span>
                        <h2>Setlist</h2>
                    </div>

                    @if ($isOwner)
                        <small>Trage doar titlurile pentru a schimba ordinea.</small>
                    @endif
                </div>

                <div id="song-order-list" class="song-order-list">
                    @foreach ($setlist->songs as $song)
                        <div
                            class="song-order-item"
                            data-song-id="{{ $song->id }}"
                            draggable="{{ $isOwner ? 'true' : 'false' }}"
                        >
                            @if ($isOwner)
                                <span class="order-drag-handle">⋮⋮</span>
                            @endif

                            <span class="order-number">{{ $loop->iteration }}</span>
                            <strong>{{ $song->title }}</strong>
                            <small>{{ $song->author }}</small>
                        </div>
                    @endforeach
                </div>
            </section>

            <div
                id="setlist-songs"
                class="setlist-songs"
                data-reorder-url="{{ route('setlists.reorder', $setlist) }}"
                data-owner="{{ $isOwner ? 'true' : 'false' }}"
            >
                @foreach ($setlist->songs as $song)
                    @php
                        $sections = $song->sections ?? [];

                        /*
                         * Compatibilitate cu piesele mai vechi,
                         * care aveau versurile într-un singur câmp.
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

                        $transposeSteps = (int) $song->pivot->transpose_steps;

                        $songTypeLabels = [
                            'praise' => 'Laudă',
                            'interlude' => 'Intermediar',
                            'worship' => 'Închinare',
                            'event' => $song->event_name ?: 'Eveniment',
                        ];

                        $songTypeLabel =
                            $songTypeLabels[$song->song_type] ?? null;
                    @endphp

                    <article
                        class="setlist-song"
                        data-song-id="{{ $song->id }}"
                        data-transpose-steps="{{ $transposeSteps }}"
                    >
                        <div class="song-toolbar">
                            <div class="song-order-area">
                                <span class="song-number">
                                    {{ $loop->iteration }}
                                </span>
                            </div>

                            <div class="song-toolbar-information">
                                <span>
                                    Tonalitate:
                                    <strong
                                        class="displayed-key"
                                        data-original-key="{{ $song->key }}"
                                    >
                                        {{ $song->key }}
                                    </strong>
                                </span>

                                <span>Capo: {{ $song->capo ?? 0 }}</span>

                                <span class="original-key-label" @if ($transposeSteps === 0) hidden @endif>
                                    Original: <strong>{{ $song->key }}</strong>
                                </span>

                                <span class="transpose-badge" @if ($transposeSteps === 0) hidden @endif></span>

                                @if ($isOwner)
                                    <div
                                        class="setlist-transpose-controls"
                                        data-transpose-url="{{ route('setlists.songs.transpose', [$setlist, $song]) }}"
                                    >
                                        <button type="button" data-transpose-action="decrease">−</button>
                                        <button type="button" data-transpose-action="increase">+</button>
                                        <button
                                            type="button"
                                            class="transpose-reset"
                                            data-transpose-action="reset"
                                            @if ($transposeSteps === 0) hidden @endif
                                        >
                                            Revino la original
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="song-heading">
                            <div>
                                <p class="song-author">
                                    {{ $song->author }}
                                </p>

                                <h2>{{ $song->title }}</h2>

                                @if ($songTypeLabel)
                                    <span class="song-type-tag song-type-{{ $song->song_type }}">
                                        {{ $songTypeLabel }}
                                    </span>
                                @endif
                            </div>

                            <div
    class="song-note-box"
    data-notes-url="{{ $isOwner
        ? route(
            'setlists.songs.notes',
            [$setlist, $song]
        )
        : ''
    }}"
>
    <div class="song-note-heading">
        <span>Notiță</span>

        @if ($isOwner)
            <small
                class="song-note-status"
                aria-live="polite"
            ></small>
        @endif
    </div>

    @if ($isOwner)
        <textarea
            class="song-note-input"
            maxlength="2000"
            placeholder="Adaugă o notiță pentru această piesă..."
        >{{ $song->pivot->notes }}</textarea>
    @elseif (trim($song->pivot->notes ?? '') !== '')
        <p class="song-note-text">
            {{ $song->pivot->notes }}
        </p>
    @else
        <p class="song-note-empty">
            Nu există nicio notiță.
        </p>
    @endif
</div>
                        </div>

                        <div class="song-sections">
                            @foreach ($sections as $section)
                                @php
                                    $sectionId = $section['id'];

                                    $sectionType = $section['type'] ?? 'stanza';

                                    $sectionLabels = [
                                        'verse' => 'Pre-refren',
                                        'pre_chorus' => 'Pre-refren',
                                        'stanza' => 'Strofă',
                                        'chorus' => 'Refren',
                                        'bridge' => 'Bridge',
                                        'coda' => 'Coda',
                                    ];

                                    if ($sectionType === 'custom') {
                                        $sectionLabel =
                                            trim($section['custom_label'] ?? '')
                                            ?: 'Secțiune';
                                    } else {
                                        $sectionLabel =
                                            $sectionLabels[$sectionType]
                                            ?? 'Secțiune';
                                    }

                                    if (!empty($section['number'])) {
                                        $sectionLabel .= ' ' . $section['number'];
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

                                <section class="lyrics-section">
                                    <div class="section-label">
                                        {{ $sectionLabel }}
                                    </div>

                                    <div class="lyrics-lines">
                                        @foreach ($lines as $lineIndex => $line)
                                            @php
                                                $lineChords = $sectionChords
                                                    ->filter(function ($chord) use ($lineIndex) {
                                                        return
                                                            (int) ($chord['line'] ?? -1)
                                                            === $lineIndex;
                                                    });
                                            @endphp

                                            <div class="lyrics-line">
                                                <div class="chord-line">
                                                    @foreach ($lineChords as $chord)
                                                        <span
                                                            class="song-chord"
                                                            data-original-chord="{{ $chord['name'] }}"
                                                            style="left: calc({{ $chord['position'] ?? 0 }}ch + {{ $chord['offset'] ?? 0 }}ch);"
                                                        >{{ $chord['name'] }}</span>
                                                    @endforeach
                                                </div>

                                                <div class="lyric-text">
                                                    {{ $line ?: ' ' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($isOwner)
                <div
                    id="reorder-status"
                    class="reorder-status"
                    aria-live="polite"
                ></div>
            @endif

            
        @endif
    </main>
</div>

<style>
    .setlist-page {
        min-height: 100vh;
        padding: 32px;
        background:
            radial-gradient(
                circle at top right,
                rgba(30, 109, 181, 0.16),
                transparent 34%
            ),
            linear-gradient(145deg, #edf4fb 0%, #f8fafc 52%, #e5edf7 100%);
        color: #081a2f;
        font-family: "DM Sans", sans-serif;
    }

    .setlist-header,
    .setlist-content {
        width: min(1480px, 100%);
        margin: 0 auto;
    }

    .setlist-header {
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        background:
            radial-gradient(
                circle at 90% 10%,
                rgba(49, 133, 211, 0.34),
                transparent 35%
            ),
            linear-gradient(135deg, #061a30 0%, #0c315a 100%);
        box-shadow: 0 24px 65px rgba(4, 24, 46, 0.2);
    }

    .setlist-header__content {
        padding: clamp(32px, 5vw, 72px);
    }

    .back-link {
        display: inline-flex;
        margin-bottom: 38px;
        color: #96d4ff;
        font-size: 0.92rem;
        font-weight: 800;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: #ffffff;
    }

    .setlist-title-row {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 32px;
    }

    .setlist-eyebrow {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        color: #96d4ff;
        font-size: 0.78rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .setlist-header h1 {
        max-width: 900px;
        margin: 0;
        color: #ffffff;
        font-size: clamp(2.6rem, 6vw, 5.8rem);
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .setlist-description {
        max-width: 700px;
        margin: 24px 0 0;
        color: #cadaeb;
        font-size: 1.05rem;
        line-height: 1.7;
    }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border: 1px solid rgba(134, 239, 172, 0.35);
        border-radius: 999px;
        background: rgba(22, 163, 74, 0.16);
        color: #a7f3c1;
        font-size: 0.7rem;
        letter-spacing: 0.08em;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ade80;
        box-shadow: 0 0 0 5px rgba(74, 222, 128, 0.13);
    }

    .setlist-actions {
    display: grid;
    width: min(440px, 100%);
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.setlist-header {
    position: relative;
}

.fullscreen-toggle {
    position: absolute;
    top: 25px;
    right: 25px;
    z-index: 10;
    display: flex;
    width: 45px;
    height: 45px;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.08);
    cursor: pointer;
    backdrop-filter: blur(8px);
    transition:
        background 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.fullscreen-toggle:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.45);
    background: rgba(255, 255, 255, 0.15);
}

.fullscreen-corner {
    display: block;
    width: 15px;
    height: 15px;
    border-top: 3px solid #ffffff;
    border-right: 3px solid #ffffff;
    transition:
        transform 0.25s ease,
        width 0.25s ease,
        height 0.25s ease;
}

.fullscreen-toggle:hover .fullscreen-corner {
    width: 18px;
    height: 18px;
    transform: translate(2px, -2px);
}

/*
 * Când pagina este în fullscreen, colțul
 * este rotit spre interior.
 */
body.setlist-fullscreen .fullscreen-corner {
    transform: rotate(180deg);
}

body.setlist-fullscreen
.fullscreen-toggle:hover
.fullscreen-corner {
    transform:
        rotate(180deg)
        translate(2px, -2px);
}

/*
 * Ascundem elementele site-ului care nu sunt
 * necesare în timpul vizualizării setlistului.
 */
body.setlist-fullscreen .site-header,
body.setlist-fullscreen .site-footer,
body.setlist-fullscreen .back-link,
body.setlist-fullscreen .setlist-actions {
    display: none !important;
}

body.setlist-fullscreen {
    overflow-y: auto;
    background: #eaf1f8;
}

body.setlist-fullscreen .main-content {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
}

body.setlist-fullscreen .setlist-page {
    min-height: 100vh;
    padding: 18px;
}

body.setlist-fullscreen .setlist-header,
body.setlist-fullscreen .setlist-content {
    width: 100%;
    max-width: none;
}

.setlist-actions form,
.setlist-actions form .button {
    width: 100%;
}

.setlist-actions .button {
    min-width: 0;
}

    .button {
        display: inline-flex;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
        border: 1px solid transparent;
        border-radius: 13px;
        font-family: inherit;
        font-size: 0.88rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition:
            transform 0.2s ease,
            border-color 0.2s ease,
            background 0.2s ease;
    }

    .button:hover:not(:disabled) {
        transform: translateY(-2px);
    }

    .button-secondary {
        border-color: rgba(255, 255, 255, 0.19);
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

.button-export {
    grid-column: 1 / -1;
    width: 100%;
    border-color: #ede4e4;
    background: #f8f3f3f8;
    color: #092847;
    box-shadow:
        0 10px 28px rgba(0, 0, 0, 0.14);
}

.button-export:hover {
    border-color: #d9efff;
    background: #d9efff;
    color: #092847;
}

.button-export span {
    margin-left: 9px;
    transition: transform 0.2s ease;
}

.button-export:hover span {
    transform: translateX(4px);
}

    .button-live {
        background: #55d98c;
        color: #052316;
    }

    .button-danger {
        border-color: rgba(252, 165, 165, 0.25);
        background: rgba(220, 38, 38, 0.16);
        color: #fecaca;
    }

    .button-primary {
        background: #0b3159;
        color: #ffffff;
    }

    .button-disabled {
        border-color: #d5dee9;
        background: #edf1f5;
        color: #8b99aa;
        cursor: not-allowed;
    }

    .setlist-information {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 42px;
    }

    .setlist-information span {
        padding: 10px 14px;
        border: 1px solid rgba(255, 255, 255, 0.13);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.06);
        color: #cadaeb;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .setlist-content {
        padding: 34px 0 60px;
    }

    .setlist-songs {
        display: grid;
        gap: 26px;
    }

    .setlist-song {
        overflow: hidden;
        border: 1px solid rgba(8, 40, 72, 0.11);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 16px 45px rgba(8, 36, 66, 0.08);
        transition:
            opacity 0.2s ease,
            transform 0.2s ease,
            border-color 0.2s ease;
    }

    .setlist-song[draggable="true"] {
        cursor: grab;
    }

    .setlist-song[draggable="true"]:active {
        cursor: grabbing;
    }

    .setlist-song.is-dragging {
        opacity: 0.42;
        transform: scale(0.985);
    }

    .setlist-song.drag-over {
        border-color: #2580c7;
        box-shadow: 0 0 0 4px rgba(37, 128, 199, 0.12);
    }

    .song-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 15px 22px;
        border-bottom: 1px solid #e7edf3;
        background: #f5f8fb;
    }

    .song-order-area,
    .song-toolbar-information {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .drag-handle {
        color: #688098;
        font-size: 1.35rem;
        font-weight: 900;
        letter-spacing: -0.2em;
        user-select: none;
    }

    .song-number {
        display: inline-flex;
        width: 32px;
        height: 32px;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #092847;
        color: #ffffff;
        font-size: 0.78rem;
        font-weight: 900;
    }

    .song-toolbar-information {
        flex-wrap: wrap;
        justify-content: flex-end;
        color: #62758a;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .song-toolbar-information strong {
        color: #082c51;
    }

    .transpose-badge {
        padding: 6px 9px;
        border-radius: 7px;
        background: #dcebfa;
        color: #0f609c;
    }

    .song-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        padding: 32px 38px 24px;
    }

    .song-author {
        margin: 0 0 7px;
        color: #1971ad;
        font-size: 0.74rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .song-heading h2 {
        margin: 0;
        color: #071b30;
        font-size: clamp(1.8rem, 4vw, 3.2rem);
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .song-type-tag {
        display: inline-flex;
        min-height: 22px;
        align-items: center;
        margin-top: 10px;
        padding: 3px 8px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 0.58rem;
        font-weight: 900;
        letter-spacing: 0.07em;
        line-height: 1;
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

    .song-note-box {
    width: min(340px, 100%);
    flex-shrink: 0;
    align-self: stretch;
    padding: 16px;
    border: 1px solid #d6e2ec;
    border-radius: 14px;
    background:
        linear-gradient(
            145deg,
            #f4f8fc,
            #ffffff
        );
}

.song-note-heading {
    display: flex;
    min-height: 20px;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 9px;
}

.song-note-heading > span {
    color: #176ca7;
    font-size: 0.65rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.song-note-status {
    color: #718296;
    font-size: 0.64rem;
    font-weight: 700;
}

.song-note-status.is-success {
    color: #15803d;
}

.song-note-status.is-error {
    color: #b42318;
}

.song-note-input {
    display: block;
    width: 100%;
    min-height: 88px;
    resize: vertical;
    padding: 11px 12px;
    border: 1px solid #cfdae5;
    border-radius: 10px;
    outline: none;
    background: rgba(255, 255, 255, 0.9);
    color: #14283c;
    font-family: "DM Sans", sans-serif;
    font-size: 0.78rem;
    line-height: 1.5;
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.song-note-input:focus {
    border-color: #2b83bf;
    box-shadow:
        0 0 0 4px rgba(43, 131, 191, 0.1);
}

.song-note-input::placeholder {
    color: #9aa8b5;
}

.song-note-text,
.song-note-empty {
    margin: 0;
    color: #334a60;
    font-size: 0.78rem;
    line-height: 1.55;
    white-space: pre-wrap;
}

.song-note-empty {
    color: #96a3af;
    font-style: italic;
}

    .song-sections {
        display: grid;
        grid-template-columns: repeat(
            auto-fit,
            minmax(min(100%, 420px), 1fr)
        );
        align-items: start;
        gap: 18px;
        padding: 10px 38px 40px;
    }

    .lyrics-section {
        min-width: 0;
        padding: 24px;
        border: 1px solid #e3eaf1;
        border-radius: 17px;
        background: linear-gradient(
            145deg,
            rgba(242, 247, 252, 0.92),
            rgba(255, 255, 255, 0.92)
        );
    }

    .section-label {
        display: inline-flex;
        margin-bottom: 24px;
        padding: 7px 11px;
        border-radius: 8px;
        background: #dcecf9;
        color: #0e609c;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .setlist-page .lyrics-lines {
        display: flex !important;
        width: 100% !important;
        flex-direction: column !important;
        align-items: stretch !important;
        justify-content: flex-start !important;
        gap: 10px !important;
    }

    .setlist-page .lyrics-line {
        position: relative !important;
        display: block !important;
        width: 100% !important;
        min-width: 0 !important;
        height: 50px !important;
        min-height: 0px !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        text-align: left !important;
        transform: none !important;
    }

    .setlist-page .chord-line {
        position: relative !important;
        display: block !important;
        width: 100% !important;
        min-width: 0 !important;
        height: 0px !important;
        min-height: 0px !important;
        margin: 0 0 -10px !important;
        padding: -10px !important;
        overflow: visible !important;
        font-family: "DM Sans", sans-serif;
        font-size: 0.92rem;
        font-weight: 900;
        text-align: left !important;
    }

    .setlist-page .song-chord {
        position: absolute !important;
        bottom: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #1268a8;
        white-space: nowrap;
        transform: none !important;
    }

    .setlist-page .lyric-text {
        position: static !important;
        display: block !important;
        width: 100% !important;
        min-width: 0 !important;
        height: auto !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #111827;
        font-family: "SFMono-Regular", Consolas, "Liberation Mono", monospace;
        font-size: 1rem;
        line-height: 0.6;
        text-align: left !important;
        white-space: pre-wrap !important;
        overflow-wrap: normal !important;
        transform: none !important;
    }

    .reorder-status {
        min-height: 24px;
        margin-top: 16px;
        color: #42617e;
        font-size: 0.82rem;
        font-weight: 700;
        text-align: center;
    }

    .reorder-status.is-success {
        color: #167044;
    }

    .reorder-status.is-error {
        color: #b42318;
    }

    .setlist-order-panel {
        margin-bottom: 22px;
        padding: 22px;
        border: 1px solid rgba(8, 40, 72, 0.11);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 12px 35px rgba(8, 36, 66, 0.07);
    }

    .setlist-order-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 16px;
    }

    .setlist-order-heading span {
        color: #1971ad;
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .setlist-order-heading h2 {
        margin: 3px 0 0;
        color: #092847;
        font-size: 1.2rem;
    }

    .setlist-order-heading small {
        color: #718296;
        font-size: 0.76rem;
    }

    .song-order-list {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
    }

    .song-order-item {
        display: inline-flex;
        min-width: 190px;
        flex: 1 1 220px;
        align-items: center;
        gap: 9px;
        padding: 10px 12px;
        border: 1px solid #dbe5ee;
        border-radius: 11px;
        background: #f7fafc;
        color: #102b45;
        transition: opacity 0.2s, border-color 0.2s, transform 0.2s;
    }

    .song-order-item[draggable="true"] {
        cursor: grab;
    }

    .song-order-item.is-dragging {
        opacity: 0.4;
    }

    .song-order-item.drag-over {
        border-color: #2580c7;
        transform: translateY(-2px);
    }

    .order-drag-handle {
        color: #75889b;
        font-weight: 900;
        letter-spacing: -0.2em;
    }

    .order-number {
        display: inline-flex;
        width: 25px;
        height: 25px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        background: #092847;
        color: white;
        font-size: 0.68rem;
        font-weight: 900;
    }

    .song-order-item strong {
        overflow: hidden;
        font-size: 0.8rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .song-order-item small {
        margin-left: auto;
        color: #7b8c9e;
        font-size: 0.68rem;
    }

    .setlist-transpose-controls {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .setlist-transpose-controls button {
        min-width: 31px;
        height: 31px;
        padding: 0 9px;
        border: 1px solid #cbd8e4;
        border-radius: 8px;
        background: white;
        color: #103b61;
        font: inherit;
        font-weight: 900;
        cursor: pointer;
    }

    .setlist-transpose-controls button:hover {
        border-color: #2580c7;
        background: #eaf5fc;
    }

    .setlist-transpose-controls .transpose-reset {
        color: #176ca7;
        font-size: 0.7rem;
    }

    .original-key-label {
        color: #6d7f91;
    }

    [hidden] {
        display: none !important;
    }

    .export-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
        margin-top: 28px;
        padding: 30px;
        border: 1px solid rgba(8, 40, 72, 0.11);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.78);
    }

    .export-card h2 {
        margin: 6px 0 8px;
        color: #0a2542;
    }

    .export-card p {
        max-width: 650px;
        margin: 0;
        color: #64778b;
        line-height: 1.6;
    }

    .export-eyebrow {
        color: #1971ad;
        font-size: 0.7rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .export-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .empty-state {
        padding: 70px 24px;
        border: 1px solid rgba(8, 40, 72, 0.11);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.88);
        text-align: center;
    }

    .empty-state h2 {
        margin-top: 0;
    }

    .empty-state p {
        margin-bottom: 24px;
        color: #687b8e;
    }

    @media (max-width: 850px) {
        .setlist-page {
            padding: 14px;
        }

        .setlist-title-row,
        .export-card {
            align-items: stretch;
            flex-direction: column;
        }

        .setlist-actions {
            justify-content: flex-start;
        }

        .song-heading {
            align-items: flex-start;
            flex-direction: column;
            padding: 26px 22px 20px;
        }

        .song-sections {
            grid-template-columns: 1fr;
            padding: 8px 16px 24px;
        }

        .song-note-box {
            width: 100%;
        }

        .song-toolbar {
            align-items: flex-start;
        }

        .song-toolbar-information {
            gap: 7px;
        }
    }

    @media (max-width: 540px) {
        .setlist-header__content {
            padding: 28px 22px;
        }

        .fullscreen-toggle {
    top: 17px;
    right: 17px;
    width: 41px;
    height: 41px;
}

        .setlist-header h1 {
            font-size: 2.55rem;
        }

        .song-toolbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .song-toolbar-information {
            justify-content: flex-start;
        }

        .lyrics-section {
            padding: 19px 16px;
        }

        .export-buttons,
        .export-buttons .button {
            width: 100%;
        }

        .export-pdf-button {
    border: 1px solid #0b2c55;
    background: #0b2c55;
    color: #ffffff;
}

.export-pdf-button:hover {
    border-color: #1768a8;
    background: #1768a8;
    color: #ffffff;
}

.export-pdf-button span {
    margin-left: 7px;
    transition: transform 0.2s ease;
}

.export-pdf-button:hover span {
    transform: translateX(3px);
}

    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const songContainer = document.getElementById('setlist-songs');

        if (!songContainer) {
            return;
        }

        /*
         * Transpunerea acordurilor conform valorii salvate
         * pentru fiecare piesă din setlist.
         */
        const sharpNotes = [
            'C', 'C#', 'D', 'D#', 'E', 'F',
            'F#', 'G', 'G#', 'A', 'A#', 'B'
        ];

        const flatNotes = [
            'C', 'Db', 'D', 'Eb', 'E', 'F',
            'Gb', 'G', 'Ab', 'A', 'Bb', 'B'
        ];

        const noteIndexes = {
            'C': 0,
            'B#': 0,
            'C#': 1,
            'Db': 1,
            'D': 2,
            'D#': 3,
            'Eb': 3,
            'E': 4,
            'Fb': 4,
            'E#': 5,
            'F': 5,
            'F#': 6,
            'Gb': 6,
            'G': 7,
            'G#': 8,
            'Ab': 8,
            'A': 9,
            'A#': 10,
            'Bb': 10,
            'B': 11,
            'Cb': 11
        };

        function transposeChord(chord, steps) {
            if (!chord || steps === 0) {
                return chord;
            }

            const match = chord.match(/^([A-G](?:#|b)?)(.*)$/);

            if (!match) {
                return chord;
            }

            const root = match[1];
            const suffix = match[2];
            const originalIndex = noteIndexes[root];

            if (originalIndex === undefined) {
                return chord;
            }

            const newIndex = (
                (originalIndex + steps) % 12 + 12
            ) % 12;

            const notes = root.includes('b')
                ? flatNotes
                : sharpNotes;

            return notes[newIndex] + suffix;
        }

        songContainer
            .querySelectorAll('.setlist-song')
            .forEach(songElement => {
                const steps = Number(
                    songElement.dataset.transposeSteps || 0
                );

                if (steps === 0) {
                    return;
                }

                const displayedKey =
                    songElement.querySelector('.displayed-key');

                if (displayedKey) {
                    displayedKey.textContent = transposeChord(
                        displayedKey.dataset.originalKey,
                        steps
                    );
                }

                songElement
                    .querySelectorAll('.song-chord')
                    .forEach(chordElement => {
                        chordElement.textContent = transposeChord(
                            chordElement.dataset.originalChord,
                            steps
                        );
                    });
            });

        /*
         * Reordonarea este disponibilă numai proprietarului.
         */
        if (songContainer.dataset.owner !== 'true') {
            return;
        }

        const statusElement =
            document.getElementById('reorder-status');

        let draggedSong = null;

        const songElements = () => [
            ...songContainer.querySelectorAll('.setlist-song')
        ];

        function updateSongNumbers() {
            songElements().forEach((song, index) => {
                const number = song.querySelector('.song-number');

                if (number) {
                    number.textContent = index + 1;
                }
            });
        }

        function setStatus(message, type = '') {
            if (!statusElement) {
                return;
            }

            statusElement.textContent = message;
            statusElement.className = 'reorder-status';

            if (type) {
                statusElement.classList.add(`is-${type}`);
            }
        }

        async function saveOrder() {
            const songIds = songElements().map(
                song => Number(song.dataset.songId)
            );

            setStatus('Se salvează noua ordine...');

            try {
                const response = await fetch(
                    songContainer.dataset.reorderUrl,
                    {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token())
                        },
                        body: JSON.stringify({
                            song_ids: songIds
                        })
                    }
                );

                if (!response.ok) {
                    throw new Error('Ordinea nu a putut fi salvată.');
                }

                setStatus('Ordinea a fost salvată.', 'success');

                window.setTimeout(() => {
                    setStatus('');
                }, 2200);
            } catch (error) {
                setStatus(
                    'A apărut o problemă la salvarea ordinii.',
                    'error'
                );
            }
        }

        songElements().forEach(song => {
            song.addEventListener('dragstart', event => {
                draggedSong = song;
                song.classList.add('is-dragging');

                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData(
                    'text/plain',
                    song.dataset.songId
                );
            });

            song.addEventListener('dragend', () => {
                song.classList.remove('is-dragging');

                songElements().forEach(element => {
                    element.classList.remove('drag-over');
                });

                draggedSong = null;
            });

            song.addEventListener('dragover', event => {
                event.preventDefault();

                if (!draggedSong || draggedSong === song) {
                    return;
                }

                song.classList.add('drag-over');
                event.dataTransfer.dropEffect = 'move';
            });

            song.addEventListener('dragleave', () => {
                song.classList.remove('drag-over');
            });

            song.addEventListener('drop', event => {
                event.preventDefault();
                song.classList.remove('drag-over');

                if (!draggedSong || draggedSong === song) {
                    return;
                }

                const elements = songElements();
                const draggedIndex = elements.indexOf(draggedSong);
                const targetIndex = elements.indexOf(song);

                if (draggedIndex < targetIndex) {
                    song.after(draggedSong);
                } else {
                    song.before(draggedSong);
                }

                updateSongNumbers();
                saveOrder();
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const orderList = document.getElementById('song-order-list');
        const songContainer = document.getElementById('setlist-songs');

        if (!orderList || !songContainer) {
            return;
        }

        const csrfToken = @json(csrf_token());
        const isOwner = songContainer.dataset.owner === 'true';
        const statusElement = document.getElementById('reorder-status');

        const sharpNotes = [
            'C', 'C#', 'D', 'D#', 'E', 'F',
            'F#', 'G', 'G#', 'A', 'A#', 'B'
        ];

        const flatNotes = [
            'C', 'Db', 'D', 'Eb', 'E', 'F',
            'Gb', 'G', 'Ab', 'A', 'Bb', 'B'
        ];

        const noteIndexes = {
            C: 0, 'B#': 0, 'C#': 1, Db: 1,
            D: 2, 'D#': 3, Eb: 3, E: 4,
            Fb: 4, 'E#': 5, F: 5, 'F#': 6,
            Gb: 6, G: 7, 'G#': 8, Ab: 8,
            A: 9, 'A#': 10, Bb: 10, B: 11, Cb: 11
        };

        function transposeChord(chord, steps) {
            const match = String(chord || '').match(/^([A-G](?:#|b)?)(.*)$/);

            if (!match || steps === 0) {
                return chord;
            }

            const index = noteIndexes[match[1]];

            if (index === undefined) {
                return chord;
            }

            const newIndex = ((index + steps) % 12 + 12) % 12;
            const notes = match[1].includes('b') ? flatNotes : sharpNotes;

            return notes[newIndex] + match[2];
        }

        function setStatus(message, type = '') {
            if (!statusElement) return;

            statusElement.textContent = message;
            statusElement.className = 'reorder-status';

            if (type) statusElement.classList.add(`is-${type}`);
        }

        function renderTranspose(songElement, steps) {
            songElement.dataset.transposeSteps = steps;

            const keyElement = songElement.querySelector('.displayed-key');
            const originalKey = keyElement.dataset.originalKey;

            keyElement.textContent = transposeChord(originalKey, steps);

            songElement.querySelectorAll('.song-chord').forEach(chord => {
                chord.textContent = transposeChord(
                    chord.dataset.originalChord,
                    steps
                );
            });

            const originalLabel = songElement.querySelector('.original-key-label');
            const badge = songElement.querySelector('.transpose-badge');
            const reset = songElement.querySelector('.transpose-reset');
            const changed = steps !== 0;

            originalLabel.hidden = !changed;
            badge.hidden = !changed;

            if (reset) reset.hidden = !changed;

            if (changed) {
                const word = Math.abs(steps) === 1 ? 'semiton' : 'semitonuri';
                badge.textContent = `${steps > 0 ? '+' : ''}${steps} ${word}`;
            }
        }

        songContainer.querySelectorAll('.setlist-song').forEach(song => {
            renderTranspose(song, Number(song.dataset.transposeSteps || 0));
        });

        if (!isOwner) return;

        songContainer.querySelectorAll('.setlist-transpose-controls').forEach(control => {
            control.addEventListener('click', async event => {
                const button = event.target.closest('[data-transpose-action]');

                if (!button) return;

                const songElement = control.closest('.setlist-song');
                const oldSteps = Number(songElement.dataset.transposeSteps || 0);
                let newSteps = oldSteps;

                if (button.dataset.transposeAction === 'increase') {
                    newSteps = Math.min(11, oldSteps + 1);
                } else if (button.dataset.transposeAction === 'decrease') {
                    newSteps = Math.max(-11, oldSteps - 1);
                } else {
                    newSteps = 0;
                }

                if (newSteps === oldSteps) return;

                control.querySelectorAll('button').forEach(item => item.disabled = true);
                renderTranspose(songElement, newSteps);
                setStatus('Se salvează transpunerea...');

                try {
                    const response = await fetch(control.dataset.transposeUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ transpose_steps: newSteps })
                    });

                    if (!response.ok) throw new Error();

                    setStatus('Transpunerea a fost salvată.', 'success');
                } catch (error) {
                    renderTranspose(songElement, oldSteps);
                    setStatus('Transpunerea nu a putut fi salvată.', 'error');
                } finally {
                    control.querySelectorAll('button').forEach(item => item.disabled = false);
                }
            });
        });

        let draggedItem = null;

        const orderItems = () => [...orderList.querySelectorAll('.song-order-item')];

        function synchronizeSongCards() {
            orderItems().forEach((item, index) => {
                item.querySelector('.order-number').textContent = index + 1;

                const songCard = songContainer.querySelector(
                    `.setlist-song[data-song-id="${item.dataset.songId}"]`
                );

                songCard.querySelector('.song-number').textContent = index + 1;
                songContainer.appendChild(songCard);
            });
        }

        async function saveOrder() {
            const songIds = orderItems().map(item => Number(item.dataset.songId));
            setStatus('Se salvează noua ordine...');

            try {
                const response = await fetch(songContainer.dataset.reorderUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ song_ids: songIds })
                });

                if (!response.ok) throw new Error();
                setStatus('Ordinea a fost salvată.', 'success');
            } catch (error) {
                setStatus('Ordinea nu a putut fi salvată.', 'error');
            }
        }

        orderItems().forEach(item => {
            item.addEventListener('dragstart', event => {
                draggedItem = item;
                item.classList.add('is-dragging');
                event.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragend', () => {
                orderItems().forEach(element => {
                    element.classList.remove('is-dragging', 'drag-over');
                });
                draggedItem = null;
            });

            item.addEventListener('dragover', event => {
                event.preventDefault();
                if (draggedItem && draggedItem !== item) {
                    item.classList.add('drag-over');
                }
            });

            item.addEventListener('dragleave', () => {
                item.classList.remove('drag-over');
            });

            item.addEventListener('drop', event => {
                event.preventDefault();
                item.classList.remove('drag-over');

                if (!draggedItem || draggedItem === item) return;

                const items = orderItems();
                const from = items.indexOf(draggedItem);
                const to = items.indexOf(item);

                if (from < to) item.after(draggedItem);
                else item.before(draggedItem);

                synchronizeSongCards();
                saveOrder();
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const noteBoxes =
            document.querySelectorAll(
                '.song-note-box[data-notes-url]'
            );

        const csrfToken =
            @json(csrf_token());

        noteBoxes.forEach(noteBox => {
            const notesUrl =
                noteBox.dataset.notesUrl;

            const textarea =
                noteBox.querySelector(
                    '.song-note-input'
                );

            const status =
                noteBox.querySelector(
                    '.song-note-status'
                );

            if (
                !notesUrl
                ||
                !textarea
                ||
                !status
            ) {
                return;
            }

            let saveTimer = null;
            let lastSavedValue =
                textarea.value;

            function setNoteStatus(
                message,
                type = ''
            ) {
                status.textContent = message;
                status.className =
                    'song-note-status';

                if (type) {
                    status.classList.add(
                        `is-${type}`
                    );
                }
            }

            async function saveNotes() {
                const currentValue =
                    textarea.value;

                if (
                    currentValue
                    ===
                    lastSavedValue
                ) {
                    setNoteStatus('');

                    return;
                }

                setNoteStatus(
                    'Se salvează...'
                );

                textarea.disabled = true;

                try {
                    const response = await fetch(
                        notesUrl,
                        {
                            method: 'PATCH',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    csrfToken
                            },

                            body: JSON.stringify({
                                notes:
                                    currentValue
                            })
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Notița nu a putut fi salvată.'
                        );
                    }

                    lastSavedValue =
                        currentValue;

                    setNoteStatus(
                        'Salvat',
                        'success'
                    );

                    window.setTimeout(() => {
                        if (
                            textarea.value
                            ===
                            lastSavedValue
                        ) {
                            setNoteStatus('');
                        }
                    }, 2000);
                } catch (error) {
                    setNoteStatus(
                        'Eroare la salvare',
                        'error'
                    );
                } finally {
                    textarea.disabled = false;
                }
            }

            textarea.addEventListener(
                'input',
                () => {
                    window.clearTimeout(
                        saveTimer
                    );

                    setNoteStatus(
                        'Modificări nesalvate'
                    );

                    saveTimer =
                        window.setTimeout(
                            saveNotes,
                            800
                        );
                }
            );

            textarea.addEventListener(
                'blur',
                () => {
                    window.clearTimeout(
                        saveTimer
                    );

                    saveNotes();
                }
            );
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fullscreenButton =
            document.getElementById(
                'fullscreen-toggle'
            );

        if (!fullscreenButton) {
            return;
        }

        const pageElement =
            document.documentElement;

        function isFullscreen() {
            return Boolean(
                document.fullscreenElement
                ||
                document.webkitFullscreenElement
            );
        }

        async function enterFullscreen() {
            if (pageElement.requestFullscreen) {
                await pageElement.requestFullscreen();

                return;
            }

            if (
                pageElement.webkitRequestFullscreen
            ) {
                pageElement.webkitRequestFullscreen();
            }
        }

        async function exitFullscreen() {
            if (document.exitFullscreen) {
                await document.exitFullscreen();

                return;
            }

            if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }

        function updateFullscreenState() {
            const active = isFullscreen();

            document.body.classList.toggle(
                'setlist-fullscreen',
                active
            );

            fullscreenButton.setAttribute(
                'aria-label',
                active
                    ? 'Ieși din ecran complet'
                    : 'Deschide setlistul pe ecran complet'
            );

            fullscreenButton.setAttribute(
                'title',
                active
                    ? 'Ieși din ecran complet'
                    : 'Ecran complet'
            );
        }

        fullscreenButton.addEventListener(
            'click',
            async () => {
                try {
                    if (isFullscreen()) {
                        await exitFullscreen();
                    } else {
                        await enterFullscreen();
                    }
                } catch (error) {
                    console.error(
                        'Modul fullscreen nu a putut fi activat.',
                        error
                    );
                }
            }
        );

        document.addEventListener(
            'fullscreenchange',
            updateFullscreenState
        );

        document.addEventListener(
            'webkitfullscreenchange',
            updateFullscreenState
        );
    });
</script>
@endsection