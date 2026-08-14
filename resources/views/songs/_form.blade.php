@php
    $initialSections = [];

    if (old('sections')) {
        $initialSections =
            json_decode(old('sections'), true) ?? [];
    } elseif ($song && !empty($song->sections)) {
        $initialSections = $song->sections;
    } elseif (
        $song &&
        trim($song->lyrics ?? '') !== ''
    ) {
        $initialSections = [
            [
                'id' => 'legacy-section-' . $song->id,
                'type' => 'stanza',
                'number' => '',
                'custom_label' => '',
                'lyrics' => $song->lyrics,
            ],
        ];
    }

    $initialChords = [];

    if (old('chords')) {
        $initialChords =
            json_decode(old('chords'), true) ?? [];
    } elseif ($song && !empty($song->chords)) {
        $initialChords = $song->chords;
    }

    if (
        count($initialSections) === 1 &&
        isset($initialSections[0]['id'])
    ) {
        $legacySectionId =
            $initialSections[0]['id'];

        $initialChords = collect($initialChords)
            ->map(function ($chord) use ($legacySectionId) {
                if (empty($chord['section'])) {
                    $chord['section'] =
                        $legacySectionId;
                }

                return $chord;
            })
            ->values()
            ->all();
    }

    $selectedSongType = old(
        'song_type',
        $song?->song_type
    );

    $selectedEventName = old(
        'event_name',
        $song?->event_name
    );
@endphp

<section class="form-page">
    <div class="form-heading">
        <p class="eyebrow-dark">
            Administrare
        </p>

        <h1>{{ $pageTitle }}</h1>

        <p>{{ $pageDescription }}</p>
    </div>

    @if ($errors->any())
        <div class="form-errors">
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
        class="song-form"
        action="{{ $formAction }}"
        method="POST"
        id="song-form"
    >
        @csrf

        @if ($formMethod !== 'POST')
            @method($formMethod)
        @endif

        <section class="song-information">
            <div class="form-section-title">
                <span>1</span>

                <div>
                    <h2>Informațiile piesei</h2>

                    <p>
                        Titlul, categoria și configurația
                        muzicală de bază.
                    </p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group form-group-wide">
                    <label for="title">
                        Titlu
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $song?->title) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="author">
                        Autor
                    </label>

                    <input
                        type="text"
                        id="author"
                        name="author"
                        value="{{ old('author', $song?->author) }}"
                    >
                </div>

                <div class="form-group">
                    <label for="song_type">
                        Tipul piesei
                    </label>

                    <select
                        id="song_type"
                        name="song_type"
                        required
                    >
                        <option value="">
                            Alege tipul piesei
                        </option>

                        <option
                            value="praise"
                            @selected($selectedSongType === 'praise')
                        >
                            Laudă
                        </option>

                        <option
                            value="interlude"
                            @selected($selectedSongType === 'interlude')
                        >
                            Intermediar
                        </option>

                        <option
                            value="worship"
                            @selected($selectedSongType === 'worship')
                        >
                            Închinare
                        </option>

                        <option
                            value="event"
                            @selected($selectedSongType === 'event')
                        >
                            Eveniment
                        </option>
                    </select>
                </div>

                <div
                    class="form-group"
                    id="event-name-group"
                    hidden
                >
                    <label for="event_name">
                        Denumirea evenimentului
                    </label>

                    <input
                        type="text"
                        id="event_name"
                        name="event_name"
                        value="{{ $selectedEventName }}"
                        placeholder="Exemplu: Crăciun, Paște, Nuntă"
                        maxlength="255"
                    >

                    <small class="field-help">
                        Scrie evenimentul pentru care este
                        potrivită piesa.
                    </small>
                </div>

                <div class="form-group">
                    <label for="key">
                        Tonalitate originală
                    </label>

                    <input
                        type="text"
                        id="key"
                        name="key"
                        value="{{ old('key', $song?->key) }}"
                        placeholder="Exemplu: G"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="capo">
                        Capo
                    </label>

                    <input
                        type="number"
                        id="capo"
                        name="capo"
                        value="{{ old('capo', $song?->capo ?? 0) }}"
                        min="0"
                        max="12"
                        required
                    >
                </div>
            </div>
        </section>

        <input
            type="hidden"
            id="sections"
            name="sections"
        >

        <input
            type="hidden"
            id="chords"
            name="chords"
        >

        <section class="section-builder">
            <div class="form-section-title">
                <span>2</span>

                <div>
                    <h2>Structura piesei</h2>

                    <p>
                        Adaugă pe rând fiecare strofă, refren
                        sau altă parte a piesei.
                    </p>
                </div>
            </div>

            <div
                id="section-feedback"
                class="section-feedback"
                hidden
            ></div>

            <div id="sections-list"></div>

            <button
                type="button"
                id="add-section"
                class="add-section-card"
            >
                <span class="add-section-icon">
                    +
                </span>

                <span>
                    <strong id="add-section-title">
                        Adaugă prima secțiune
                    </strong>

                    <small>
                        Strofă, refren, bridge, coda
                        sau alt tip
                    </small>
                </span>
            </button>
        </section>

        <section
            class="global-chords-step"
            id="global-chords-step"
            hidden
        >
            <div class="form-section-title">
                <span>3</span>

                <div>
                    <h2>Acordurile piesei</h2>

                    <p>
                        Adaugă și poziționează acordurile după
                        ce ai finalizat toate secțiunile.
                    </p>
                </div>
            </div>

            <div class="global-chords-toolbar">
                <button
                    type="button"
                    id="back-to-sections"
                    class="button button-secondary"
                >
                    ← Înapoi la secțiuni
                </button>
            </div>

            <div
                id="global-chords-editor"
                class="global-chords-editor"
            ></div>
        </section>

        <div class="form-actions">
            <button
                class="button button-primary"
                type="button"
                id="continue-to-chords"
            >
                Continuă cu acordurile
            </button>

            <button
                class="button button-primary"
                type="submit"
                id="save-song"
                hidden
            >
                {{ $submitLabel }}
            </button>

            <a
                class="button button-secondary"
                href="{{ $cancelUrl }}"
            >
                Renunță
            </a>
        </div>
    </form>
</section>

<style>
    .song-form select {
        width: 100%;
        min-height: 52px;
        padding: 0 42px 0 15px;
        border: 1px solid #bdccdb;
        border-radius: 11px;
        outline: none;
        background-color: #ffffff;
        color: #111827;
        font-family: "DM Sans", sans-serif;
        font-size: 0.94rem;
        cursor: pointer;
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .song-form select:focus {
        border-color: #2878c8;
        box-shadow:
            0 0 0 4px rgba(40, 120, 200, 0.11);
    }

    .field-help {
        display: block;
        margin-top: 6px;
        color: #748496;
        font-size: 0.72rem;
        line-height: 1.45;
    }

    #event-name-group {
        padding: 16px;
        border: 1px solid #b9d7ed;
        border-radius: 12px;
        background: #f0f8fd;
    }

    #event-name-group[hidden] {
        display: none;
    }

    .form-actions [hidden] {
    display: none !important;
}
</style>

<script>
    window.songEditorInitialData = {
        sections: @json($initialSections),
        chords: @json($initialChords)
    };

    document.addEventListener(
        'DOMContentLoaded',
        function () {
            const songType =
                document.getElementById('song_type');

            const eventNameGroup =
                document.getElementById(
                    'event-name-group'
                );

            const eventName =
                document.getElementById('event_name');

            function updateEventField() {
                const isEvent =
                    songType.value === 'event';

                eventNameGroup.hidden = !isEvent;
                eventName.required = isEvent;

                if (!isEvent) {
                    eventName.value = '';
                }
            }

            songType.addEventListener(
                'change',
                updateEventField
            );

            updateEventField();
        }
    );
</script>