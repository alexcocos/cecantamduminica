@extends('layouts.app')

@section('title', 'Piese | Ce cântăm duminică')

@section('content')
    <section class="page-heading">
        <p class="eyebrow">Cântece</p>

        <h1>Repertoriul Nostru</h1>

        <p class="page-description">
            Găsește rapid versurile și acordurile
            pieselor din repertoriu.
        </p>
    </section>

    @if ($liveSetlist)
        <a
            href="{{ route('setlists.show', $liveSetlist) }}"
            class="live-setlist-card"
        >
            <span class="live-setlist-status">
                <span class="live-status-dot"></span>
                Setlist live
            </span>

            <span class="live-setlist-content">
                <span class="live-setlist-main">
                    <span class="live-setlist-icon">
                        ♪
                    </span>

                    <span class="live-setlist-text">
                        <strong>
                            {{ $liveSetlist->name }}
                        </strong>

                        <small>
                            {{ $liveSetlist->songs_count }}
                            {{ $liveSetlist->songs_count === 1
                                ? 'piesă'
                                : 'piese'
                            }}

                            <span aria-hidden="true">·</span>

                            Creat de
                            {{ $liveSetlist->user->name }}
                        </small>
                    </span>
                </span>

                <span class="live-setlist-action">
                    Deschide setlistul
                    <span aria-hidden="true">→</span>
                </span>
            </span>
        </a>
    @endif

    <section class="songs-section">
        <div class="section-heading">
            <div>
                <h2>Toate piesele</h2>

                <p class="songs-count">
                    <span id="visible-songs-count">
                        {{ $songs->count() }}
                    </span>

                    <span id="songs-count-label">
                        {{ $songs->count() === 1 ? 'piesă' : 'piese' }}
                    </span>
                </p>
            </div>

            @auth
                <div class="section-actions">
                    <a
                        class="button create-setlist-button"
                        href="{{ route('setlists.create') }}"
                    >
                        <span aria-hidden="true">＋</span>
                        Creează setlist
                    </a>

                    @if (auth()->user()->isAdmin())
                        <a
                            class="button button-primary"
                            href="{{ route('songs.create') }}"
                        >
                            Adaugă o piesă
                        </a>
                    @endif
                </div>
            @endauth
        </div>

        @if ($songs->isNotEmpty())
            <div class="songs-search">
                <label
                    class="sr-only"
                    for="song-search"
                >
                    Caută o piesă
                </label>

                <span
                    class="songs-search-icon"
                    aria-hidden="true"
                >
                    ⌕
                </span>

                <input
                    type="search"
                    id="song-search"
                    placeholder="Caută după titlu sau autor..."
                    autocomplete="off"
                >

                <button
                    type="button"
                    id="clear-song-search"
                    class="clear-song-search"
                    aria-label="Șterge căutarea"
                    hidden
                >
                    ×
                </button>
            </div>

            <div class="song-type-filters" aria-label="Filtrează piesele după tip">
                <button type="button" class="song-filter is-active" data-song-filter="all">
                    Toate
                </button>

                <button type="button" class="song-filter filter-praise" data-song-filter="praise">
                    Laudă
                </button>

                <button type="button" class="song-filter filter-interlude" data-song-filter="interlude">
                    Intermediar
                </button>

                <button type="button" class="song-filter filter-worship" data-song-filter="worship">
                    Închinare
                </button>

                <button type="button" class="song-filter filter-event" data-song-filter="event">
                    Eveniment
                </button>
            </div>
        @endif

        <div
            class="alphabetical-label"
            id="alphabetical-label"
        >
            Ordine alfabetică
        </div>

        <div class="songs-list" id="songs-list">
            @forelse ($songs as $song)
                @php
                    $songTypeLabels = [
                        'praise' => 'Laudă',
                        'interlude' => 'Intermediar',
                        'worship' => 'Închinare',
                        'event' => $song->event_name ?: 'Eveniment',
                    ];

                    $songTypeLabel =
                        $songTypeLabels[$song->song_type] ?? null;
                @endphp

                <a
                    class="song-card"
                    href="{{ route('songs.show', $song) }}"
                    data-song-title="{{ Str::lower($song->title) }}"
                    data-song-author="{{ Str::lower($song->author ?? '') }}"
                    data-song-type="{{ $song->song_type ?? '' }}"
                >
                    <span class="song-card-main">
                        <span class="song-initial">
                            {{ Str::upper(Str::substr($song->title, 0, 1)) }}
                        </span>

                        <span class="song-card-text">
                            <strong>
                                {{ $song->title }}
                            </strong>

                            @if ($song->author)
                                <small>
                                    {{ $song->author }}
                                </small>
                            @endif

                            @if ($songTypeLabel)
                                <span class="song-type-tag song-type-{{ $song->song_type }}">
                                    {{ $songTypeLabel }}
                                </span>
                            @endif
                        </span>
                    </span>

                    <span class="song-arrow">→</span>
                </a>
            @empty
                <div class="empty-state">
                    <h3>Nu există încă nicio piesă</h3>

                    <p>
                        Piesele adăugate vor apărea aici.
                    </p>
                </div>
            @endforelse
        </div>

        <div
            class="search-empty-state"
            id="search-empty-state"
            hidden
        >
            <span>⌕</span>

            <h3>Nu am găsit nicio piesă</h3>

            <p>
                Încearcă un alt titlu sau numele altui autor.
            </p>
        </div>
    </section>

    <style>
        .live-setlist-card {
            position: relative;
            display: block;
            overflow: hidden;
            margin-bottom: 28px;
            padding: 26px 28px;
           border: 3px solid #22c55e;
            border-radius: 20px;
            background:
                radial-gradient(
                    circle at 90% 20%,
                    rgba(11, 12, 12, 0.35),
                    transparent 38%
                ),
                linear-gradient(
                    135deg,
                    #35383a 0%,
                    #131516 100%
                );
            box-shadow: 0 18px 45px rgba(5, 32, 58, 0.18);
            color: #ffffff;
            text-decoration: none;
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease;
        }

        .live-setlist-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 24px 55px rgba(5, 32, 58, 0.24);
        }

        .live-setlist-card::after {
            content: "";
            position: absolute;
            top: -90px;
            right: -65px;
            width: 220px;
            height: 220px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .live-setlist-status {
            display: inline-flex;
            position: relative;
            z-index: 1;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            padding: 7px 11px;
            border: 1px solid rgba(134, 239, 172, 0.27);
            border-radius: 999px;
            background: rgba(22, 163, 74, 0.16);
            color: #a7f3c1;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.11em;
            text-transform: uppercase;
        }

        .live-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow:
                0 0 0 5px rgba(74, 222, 128, 0.12);
            animation: live-pulse 1.8s infinite;
        }

        @keyframes live-pulse {
            0%,
            100% {
                box-shadow:
                    0 0 0 4px rgba(74, 222, 128, 0.11);
            }

            50% {
                box-shadow:
                    0 0 0 8px rgba(74, 222, 128, 0.03);
            }
        }

        .live-setlist-content {
            display: flex;
            position: relative;
            z-index: 1;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .live-setlist-main {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 16px;
        }

        .live-setlist-icon {
            display: grid;
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.09);
            color: #91d5ff;
            font-size: 1.25rem;
            font-weight: 900;
        }

        .live-setlist-text {
            display: flex;
            min-width: 0;
            flex-direction: column;
        }

        .live-setlist-text strong {
            overflow: hidden;
            color: #ffffff;
            font-family: "DM Sans", sans-serif;
            font-size: clamp(1.25rem, 3vw, 1.8rem);
            font-weight: 850;
            letter-spacing: -0.025em;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .live-setlist-text small {
            margin-top: 5px;
            color: #bed1e4;
            font-size: 0.77rem;
            font-weight: 600;
        }

        .live-setlist-text small span {
            margin: 0 5px;
            color: #7197ba;
        }

        .live-setlist-action {
            display: inline-flex;
            flex-shrink: 0;
            align-items: center;
            gap: 9px;
            color: #a8dcff;
            font-size: 0.78rem;
            font-weight: 900;
        }

        .live-setlist-card:hover .live-setlist-action span {
            transform: translateX(4px);
        }

        .live-setlist-action span {
            transition: transform 0.2s ease;
        }

        .section-heading {
            align-items: center;
            gap: 24px;
        }

        .section-heading > div:first-child h2 {
            margin-bottom: 2px;
        }

        .section-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .section-actions .button {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 18px;
            border-radius: 11px;
            font-family: "DM Sans", sans-serif;
            font-size: 0.8rem;
            font-weight: 850;
            text-decoration: none;
            transition:
                transform 0.2s ease,
                border-color 0.2s ease,
                background 0.2s ease,
                color 0.2s ease;
        }

        .section-actions .button:hover {
            transform: translateY(-2px);
        }

        .create-setlist-button {
            border: 1px solid #b8cce0;
            background: #edf5fb;
            color: #0c4c7d;
            box-shadow: 0 7px 20px rgba(11, 55, 93, 0.07);
        }

        .create-setlist-button:hover {
            border-color: #8db6d8;
            background: #dceefa;
            color: #083a60;
        }

        .create-setlist-button span {
            font-size: 1.05rem;
            line-height: 1;
        }

        .songs-count {
            margin: 0;
            color: #718096;
            font-size: 0.85rem;
        }

        .songs-search {
            position: relative;
            max-width: 650px;
            margin-bottom: 13px;
        }

        .song-type-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
        }

        .song-filter {
            min-height: 34px;
            padding: 6px 12px;
            border: 1px solid #cbd8e4;
            border-radius: 999px;
            background: #ffffff;
            color: #526579;
            font-family: "DM Sans", sans-serif;
            font-size: 0.72rem;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .song-filter:hover {
            transform: translateY(-1px);
        }

        .song-filter.is-active {
            background: #0b2c55;
            border-color: #0b2c55;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(11, 44, 85, 0.15);
        }

        .song-filter.filter-praise:not(.is-active) {
            border-color: #93c5fd;
            background: #dbeafe;
            color: #1d4f91;
        }

        .song-filter.filter-interlude:not(.is-active) {
            border-color: #fdba74;
            background: #ffedd5;
            color: #9a4511;
        }

        .song-filter.filter-worship:not(.is-active) {
            border-color: #c4b5fd;
            background: #ede9fe;
            color: #5b36a8;
        }

        .song-filter.filter-event:not(.is-active) {
            border-color: #86efac;
            background: #dcfce7;
            color: #166534;
        }

        .songs-search input {
            width: 100%;
            min-height: 54px;
            padding: 13px 48px 13px 47px;
            border: 1px solid #bdccdb;
            border-radius: 11px;
            outline: none;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 6px 20px rgba(11, 44, 85, 0.06);
            color: #090e15;
            font-family: "DM Sans", sans-serif;
            font-size: 0.97rem;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .songs-search input:focus {
            border-color: #2878c8;
            background: white;
            box-shadow:
                0 0 0 4px rgba(40, 120, 200, 0.11),
                0 8px 22px rgba(11, 44, 85, 0.08);
        }

        .songs-search input::placeholder {
            color: #8795a5;
        }

        .songs-search-icon {
            position: absolute;
            top: 50%;
            left: 17px;
            color: #174f86;
            font-size: 1.35rem;
            line-height: 1;
            pointer-events: none;
            transform: translateY(-50%);
        }

        .clear-song-search {
            position: absolute;
            top: 50%;
            right: 12px;
            display: grid;
            width: 31px;
            height: 31px;
            place-items: center;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: #e8eef4;
            color: #34465a;
            font-size: 1.1rem;
            line-height: 1;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .clear-song-search:hover {
            background: #d5e4f2;
            color: #0b2c55;
        }

        .clear-song-search[hidden] {
            display: none;
        }

        .alphabetical-label {
            margin-bottom: 13px;
            color: #6b7b8d;
            font-size: 0.75rem;
            font-weight: 750;
            letter-spacing: 0.11em;
            text-transform: uppercase;
        }

        .song-card-main {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 13px;
        }

        .song-initial {
            display: grid;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            place-items: center;
            border-radius: 9px;
            background: #e0edf9;
            color: #0b2c55;
            font-family: "DM Sans", sans-serif;
            font-size: 0.91rem;
            font-weight: 800;
        }

        .song-card:hover .song-initial {
            background: #2878c8;
            color: white;
        }

        .song-card-text {
            display: flex;
            min-width: 0;
            flex-direction: column;
        }

        .song-card-text strong {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .song-card-text small {
            overflow: hidden;
            margin-top: 2px;
            color: #718096;
            font-size: 0.78rem;
            font-weight: 500;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .song-type-tag {
            display: inline-flex;
            width: fit-content;
            min-height: 21px;
            align-items: center;
            margin-top: 6px;
            padding: 3px 8px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-family: "DM Sans", sans-serif;
            font-size: 0.58rem;
            font-weight: 850;
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

        .song-card[hidden] {
            display: none;
        }

        .search-empty-state {
            padding: 48px 25px;
            border: 1px dashed #afc1d2;
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.75);
            color: #657588;
            text-align: center;
        }

        .search-empty-state[hidden] {
            display: none;
        }

        .search-empty-state > span {
            display: block;
            color: #2878c8;
            font-size: 2rem;
        }

        .search-empty-state h3 {
            margin: 10px 0 3px;
            color: #0b2c55;
            font-family: "DM Sans", sans-serif;
            font-size: 1.2rem;
        }

        .search-empty-state p {
            margin: 0;
            font-size: 0.88rem;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
            white-space: nowrap;
        }

        @media (max-width: 700px) {
            .live-setlist-content {
                align-items: flex-start;
                flex-direction: column;
            }

            .section-heading {
                align-items: stretch;
                flex-direction: column;
            }

            .section-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 480px) {
            .live-setlist-card {
                padding: 22px 18px;
            }

            .live-setlist-icon {
                width: 44px;
                height: 44px;
            }

            .live-setlist-text strong {
                white-space: normal;
            }

            .section-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .section-actions .button {
                width: 100%;
            }
        }
    </style>

    <script>
        const searchInput =
            document.getElementById('song-search');

        if (searchInput) {
            const songCards =
                Array.from(
                    document.querySelectorAll('.song-card')
                );

            const clearButton =
                document.getElementById(
                    'clear-song-search'
                );

            const countElement =
                document.getElementById(
                    'visible-songs-count'
                );

            const countLabel =
                document.getElementById(
                    'songs-count-label'
                );

            const emptyState =
                document.getElementById(
                    'search-empty-state'
                );

            const alphabeticalLabel =
                document.getElementById(
                    'alphabetical-label'
                );

            const filterButtons = Array.from(
                document.querySelectorAll('.song-filter')
            );

            let activeType = 'all';

            function normalizeText(text) {
                return text
                    .toLocaleLowerCase('ro-RO')
                    .normalize('NFD')
                    .replace(
                        /[\u0300-\u036f]/g,
                        ''
                    )
                    .trim();
            }

            function filterSongs() {
                const searchTerm =
                    normalizeText(searchInput.value);

                let visibleCount = 0;

                songCards.forEach(function (card) {
                    const title = normalizeText(
                        card.dataset.songTitle || ''
                    );

                    const author = normalizeText(
                        card.dataset.songAuthor || ''
                    );

                    const matchesSearch =
                        title.includes(searchTerm) ||
                        author.includes(searchTerm);

                    const matchesType =
                        activeType === 'all' ||
                        card.dataset.songType === activeType;

                    const isVisible =
                        matchesSearch && matchesType;

                    card.hidden = !isVisible;

                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                countElement.textContent =
                    visibleCount;

                countLabel.textContent =
                    visibleCount === 1
                        ? 'piesă'
                        : 'piese';

                clearButton.hidden =
                    searchTerm.length === 0;

                emptyState.hidden =
                    visibleCount !== 0;

                alphabeticalLabel.hidden =
                    visibleCount === 0;
            }

            searchInput.addEventListener(
                'input',
                filterSongs
            );

            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activeType = button.dataset.songFilter;

                    filterButtons.forEach(function (item) {
                        item.classList.toggle(
                            'is-active',
                            item === button
                        );
                    });

                    filterSongs();
                });
            });

            clearButton.addEventListener(
                'click',
                function () {
                    searchInput.value = '';
                    filterSongs();
                    searchInput.focus();
                }
            );

            filterSongs();
        }
    </script>
@endsection