@extends('layouts.app')

@section('title', 'Exportă ' . $setlist->name)

@section('content')
<section class="export-page">
    <header class="export-header">
        <a
            href="{{ route('setlists.show', $setlist) }}"
            class="export-back-link"
        >
            ← Înapoi la setlist
        </a>

        <span class="export-eyebrow">
            Export setlist
        </span>

        <h1>{{ $setlist->name }}</h1>

        <p>
            Alege formatul în care vrei să descarci setlistul.
            Piesele originale și setlistul nu vor fi modificate.
        </p>

        <div class="export-header-details">
            <span>
                {{ $setlist->songs->count() }}

                {{ $setlist->songs->count() === 1
                    ? 'piesă'
                    : 'piese'
                }}
            </span>

            <span>
                Creat de {{ $setlist->user->name }}
            </span>
        </div>
    </header>

    @if ($errors->any())
        <div class="export-errors">
            <strong>
                Exportul nu a putut fi generat.
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="format-selection" id="format-selection">
        <div class="format-selection-heading">
            <span>Pasul 1</span>

            <h2>Alege formatul exportului</h2>

            <p>
                Poți reveni oricând pentru a genera și celălalt format.
            </p>
        </div>

        <div class="format-cards">
            <button
                type="button"
                class="format-card format-card-pdf"
                data-select-format="pdf"
            >
                <span class="format-icon">
                    PDF
                </span>

                <span class="format-card-content">
                    <strong>Document PDF</strong>

                    <small>
                        Pentru imprimare, distribuire și utilizare
                        pe tabletă.
                    </small>
                </span>

                <span class="format-arrow">→</span>
            </button>

            <button
                type="button"
                class="format-card format-card-powerpoint"
                data-select-format="powerpoint"
            >
                <span class="format-icon">
                    PPT
                </span>

                <span class="format-card-content">
                    <strong>Prezentare PowerPoint</strong>

                    <small>
                        Pentru proiectarea versurilor pe ecran
                        în timpul programului.
                    </small>
                </span>

                <span class="format-arrow">→</span>
            </button>
        </div>
    </div>

    <div
        class="export-layout"
        id="export-layout"
        hidden
    >
        <div class="export-forms">
            <form
                action="{{ route('setlists.export.pdf', $setlist) }}"
                method="POST"
                class="export-form"
                id="pdf-export-form"
                hidden
            >
                @csrf

                <section class="export-card">
                    <div class="export-card-heading">
                        <span class="export-format-icon pdf-icon">
                            PDF
                        </span>

                        <div>
                            <span class="form-step">
                                Pasul 2
                            </span>

                            <h2>Opțiuni PDF</h2>

                            <p>
                                Configurează documentul pentru
                                imprimare sau distribuire.
                            </p>
                        </div>
                    </div>

                    <div class="export-options">
                        <label class="export-option">
                            <input
                                type="checkbox"
                                name="include_chords"
                                value="1"
                                checked
                            >

                            <span class="option-control"></span>

                            <span class="option-content">
                                <strong>
                                    Include acordurile
                                </strong>

                                <small>
                                    Acordurile vor folosi transpunerea
                                    salvată în setlist.
                                </small>
                            </span>
                        </label>

                        <label class="export-option">
                            <input
                                type="checkbox"
                                name="include_details"
                                value="1"
                                checked
                            >

                            <span class="option-control"></span>

                            <span class="option-content">
                                <strong>
                                    Include detaliile pieselor
                                </strong>

                                <small>
                                    Autor, tonalitate, transpunere și capo.
                                </small>
                            </span>
                        </label>

                        <label class="export-option">
                            <input
                                type="checkbox"
                                name="include_section_labels"
                                value="1"
                                checked
                            >

                            <span class="option-control"></span>

                            <span class="option-content">
                                <strong>
                                    Include denumirile secțiunilor
                                </strong>

                                <small>
                                    Strofă, pre-refren, refren,
                                    bridge și coda.
                                </small>
                            </span>
                        </label>

                        <label class="export-option">
                            <input
                                type="checkbox"
                                name="one_song_per_page"
                                value="1"
                            >

                            <span class="option-control"></span>

                            <span class="option-content">
                                <strong>
                                    Fiecare piesă pe pagină nouă
                                </strong>

                                <small>
                                    Util dacă vrei să separi piesele
                                    în document.
                                </small>
                            </span>
                        </label>
                    </div>

                    <div class="export-submit-area">
                        <button
                            type="button"
                            class="change-format-button"
                            data-change-format
                        >
                            ← Schimbă formatul
                        </button>

                        <button
                            type="submit"
                            class="export-button pdf-button"
                        >
                            Generează PDF
                            <span>↓</span>
                        </button>
                    </div>
                </section>
            </form>

            <form
                action="{{ route('setlists.export.powerpoint', $setlist) }}"
                method="POST"
                class="export-form"
                id="powerpoint-export-form"
                hidden
            >
                @csrf

                <section class="export-card">
                    <div class="export-card-heading">
                        <span class="export-format-icon powerpoint-icon">
                            PPT
                        </span>

                        <div>
                            <span class="form-step">
                                Pasul 2
                            </span>

                            <h2>Opțiuni PowerPoint</h2>

                            <p>
                                Configurează slide-urile pentru
                                proiectarea versurilor.
                            </p>
                        </div>
                    </div>

                    <div class="export-options">
                        <label class="export-option">
                            <input
                                type="checkbox"
                                name="ppt_include_chords"
                                value="1"
                            >

                            <span class="option-control"></span>

                            <span class="option-content">
                                <strong>
                                    Include acordurile
                                </strong>

                                <small>
                                    Acordurile vor apărea deasupra
                                    versurilor în slide-uri.
                                </small>
                            </span>
                        </label>

                        <input
                            type="hidden"
                            name="ppt_include_section_labels"
                            value="0"
                        >

                        <label class="export-option">
                            <input
                                type="checkbox"
                                name="ppt_include_section_labels"
                                value="1"
                                checked
                            >

                            <span class="option-control"></span>

                            <span class="option-content">
                                <strong>
                                    Afișează tipul secțiunii
                                </strong>

                                <small>
                                    Strofă, refren, pre-refren,
                                    bridge sau coda.
                                </small>
                            </span>
                        </label>

                        <div class="export-field">
                            <label for="lines_per_slide">
                                Rânduri de versuri pe slide
                            </label>

                            <select
                                id="lines_per_slide"
                                name="lines_per_slide"
                            >
                                <option value="2">
                                    2 rânduri
                                </option>

                                <option value="3">
                                    3 rânduri
                                </option>

                                <option
                                    value="4"
                                    selected
                                >
                                    4 rânduri
                                </option>

                                <option value="5">
                                    5 rânduri
                                </option>

                                <option value="6">
                                    6 rânduri
                                </option>
                            </select>

                            <small>
                                Pentru proiecție recomandăm între
                                3 și 4 rânduri pe slide.
                            </small>
                        </div>

                        <div class="color-options">
                            <label class="color-option">
                                <span>
                                    Culoarea fundalului
                                </span>

                                <span class="color-input-wrapper">
                                    <input
                                        type="color"
                                        name="background_color"
                                        id="background_color"
                                        value="#081e34"
                                    >

                                    <strong id="background-color-value">
                                        #081E34
                                    </strong>
                                </span>
                            </label>

                            <label class="color-option">
                                <span>
                                    Culoarea textului
                                </span>

                                <span class="color-input-wrapper">
                                    <input
                                        type="color"
                                        name="text_color"
                                        id="text_color"
                                        value="#ffffff"
                                    >

                                    <strong id="text-color-value">
                                        #FFFFFF
                                    </strong>
                                </span>
                            </label>
                        </div>

                        <div
                            class="slide-preview"
                            id="slide-preview"
                        >
                            <span class="slide-preview-label">
                                REFREN
                            </span>

                            <strong>
                                Bunătatea Ta mă înconjoară
                            </strong>

                            <strong>
                                Harul Tău mă va purta
                            </strong>

                            <small>
                                Exemplu de slide
                            </small>
                        </div>
                    </div>

                    <div class="export-submit-area">
                        <button
                            type="button"
                            class="change-format-button"
                            data-change-format
                        >
                            ← Schimbă formatul
                        </button>

                        <button
                            type="submit"
                            class="export-button powerpoint-button"
                        >
                            Generează PowerPoint
                            <span>↓</span>
                        </button>
                    </div>
                </section>
            </form>
        </div>

        <aside class="export-preview">
            <span class="preview-eyebrow">
                Ordinea exportului
            </span>

            <h2>Piesele din setlist</h2>

            <div class="preview-songs">
                @foreach ($setlist->songs as $song)
                    <div class="preview-song">
                        <span class="preview-number">
                            {{ $loop->iteration }}
                        </span>

                        <span class="preview-song-text">
                            <strong>
                                {{ $song->title }}
                            </strong>

                            <small>
                                {{ $song->author ?: 'Autor necunoscut' }}

                                @if (
                                    (int)
                                    $song->pivot->transpose_steps
                                    !== 0
                                )
                                    ·

                                    {{ (int)
                                        $song->pivot->transpose_steps
                                        > 0
                                            ? '+'
                                            : ''
                                    }}

                                    {{ $song->pivot->transpose_steps }}

                                    semitonuri
                                @endif
                            </small>
                        </span>
                    </div>
                @endforeach
            </div>

            <p class="preview-note">
                Ordinea din export va fi aceeași cu
                ordinea salvată în setlist.
            </p>
        </aside>
    </div>
</section>

<style>
    .export-page {
        width: min(1180px, 100%);
        margin: 0 auto;
        font-family: "DM Sans", sans-serif;
    }

    .export-header {
        padding: clamp(30px, 5vw, 55px);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 90% 10%,
                rgba(50, 139, 216, 0.32),
                transparent 38%
            ),
            linear-gradient(
                135deg,
                #061a30 0%,
                #0d345e 100%
            );
        box-shadow:
            0 20px 55px rgba(5, 29, 53, 0.18);
        color: #ffffff;
    }

    .export-back-link {
        display: inline-flex;
        margin-bottom: 30px;
        color: #9ad6ff;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
    }

    .export-back-link:hover {
        color: #ffffff;
    }

    .export-eyebrow,
    .preview-eyebrow,
    .form-step {
        display: block;
        color: #82cafa;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .export-header h1 {
        margin: 8px 0 0;
        color: #ffffff;
        font-size: clamp(2.4rem, 6vw, 4.6rem);
        line-height: 1;
        letter-spacing: -0.055em;
    }

    .export-header > p {
        max-width: 690px;
        margin: 18px 0 0;
        color: #c7d8e8;
        line-height: 1.65;
    }

    .export-header-details {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 27px;
    }

    .export-header-details span {
        padding: 8px 11px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.07);
        color: #d2e0ed;
        font-size: 0.72rem;
        font-weight: 750;
    }

    .export-errors {
        margin-top: 20px;
        padding: 17px 20px;
        border: 1px solid #fecaca;
        border-radius: 13px;
        background: #fff1f2;
        color: #9f1239;
    }

    .export-errors ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }

    .format-selection {
        margin-top: 24px;
        padding: 28px;
        border: 1px solid #cbd8e4;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow:
            0 12px 36px rgba(8, 38, 67, 0.07);
    }

    .format-selection-heading {
        text-align: center;
    }

    .format-selection-heading > span {
        color: #1971ad;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .format-selection-heading h2 {
        margin: 6px 0;
        color: #0b2946;
        font-size: 1.55rem;
    }

    .format-selection-heading p {
        margin: 0;
        color: #718096;
        font-size: 0.84rem;
    }

    .format-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 25px;
    }

    .format-card {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 16px;
        padding: 22px;
        border: 1px solid #d8e2eb;
        border-radius: 16px;
        background: #f9fbfd;
        font-family: inherit;
        text-align: left;
        cursor: pointer;
        transition:
            transform 0.2s ease,
            border-color 0.2s ease,
            background 0.2s ease,
            box-shadow 0.2s ease;
    }

    .format-card:hover {
        transform: translateY(-3px);
        border-color: #8db9d8;
        background: #f2f8fc;
        box-shadow:
            0 12px 28px rgba(8, 45, 76, 0.1);
    }

    .format-icon,
    .export-format-icon {
        display: inline-flex;
        width: 56px;
        height: 56px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 0.74rem;
        font-weight: 900;
    }

    .format-card-pdf .format-icon,
    .pdf-icon {
        background: #fee2e2;
        color: #b91c1c;
    }

    .format-card-powerpoint .format-icon,
    .powerpoint-icon {
        background: #ffedd5;
        color: #b45309;
    }

    .format-card-content {
        display: flex;
        min-width: 0;
        flex: 1;
        flex-direction: column;
    }

    .format-card-content strong {
        color: #12314d;
        font-size: 1rem;
    }

    .format-card-content small {
        margin-top: 5px;
        color: #748598;
        font-size: 0.74rem;
        line-height: 1.45;
    }

    .format-arrow {
        color: #1a6fa8;
        font-size: 1.2rem;
        font-weight: 900;
    }

    .export-layout {
        display: grid;
        grid-template-columns:
            minmax(0, 1.25fr)
            minmax(280px, 0.75fr);
        align-items: start;
        gap: 20px;
        margin-top: 24px;
    }

    .export-card,
    .export-preview {
        border: 1px solid #cbd8e4;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow:
            0 12px 36px rgba(8, 38, 67, 0.07);
    }

    .export-card {
        overflow: hidden;
    }

    .export-card-heading {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 24px;
        border-bottom: 1px solid #e3e9ef;
        background: #f8fafc;
    }

    .export-card-heading h2,
    .export-preview h2 {
        margin: 4px 0 0;
        color: #0b2946;
        font-size: 1.25rem;
    }

    .export-card-heading p {
        margin: 6px 0 0;
        color: #718096;
        font-size: 0.82rem;
        line-height: 1.5;
    }

    .form-step {
        color: #2078b5;
    }

    .export-options {
        display: grid;
        gap: 10px;
        padding: 22px 24px;
    }

    .export-option {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        padding: 15px;
        border: 1px solid #dce4ec;
        border-radius: 12px;
        background: #fbfcfe;
        cursor: pointer;
    }

    .export-option:hover {
        border-color: #95b9d7;
        background: #f2f8fc;
    }

    .export-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .option-control {
        position: relative;
        display: inline-flex;
        width: 21px;
        height: 21px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 2px solid #aab9c8;
        border-radius: 6px;
        background: #ffffff;
    }

    .export-option input:checked + .option-control {
        border-color: #1672ad;
        background: #1672ad;
    }

    .export-option input:checked + .option-control::after {
        content: "✓";
        color: #ffffff;
        font-size: 0.76rem;
        font-weight: 900;
    }

    .option-content {
        display: flex;
        flex-direction: column;
    }

    .option-content strong {
        color: #183651;
        font-size: 0.83rem;
    }

    .option-content small {
        margin-top: 4px;
        color: #7b8b9b;
        font-size: 0.72rem;
        line-height: 1.45;
    }

    .export-field {
        display: flex;
        flex-direction: column;
        padding: 15px;
        border: 1px solid #dce4ec;
        border-radius: 12px;
        background: #fbfcfe;
    }

    .export-field label {
        color: #183651;
        font-size: 0.83rem;
        font-weight: 800;
    }

    .export-field select {
        width: 100%;
        margin-top: 10px;
        padding: 11px 12px;
        border: 1px solid #cbd8e4;
        border-radius: 9px;
        background: #ffffff;
        color: #173a59;
        font-family: inherit;
    }

    .export-field small {
        margin-top: 7px;
        color: #7b8b9b;
        font-size: 0.7rem;
    }

    .color-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .color-option {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 15px;
        border: 1px solid #dce4ec;
        border-radius: 12px;
        background: #fbfcfe;
        color: #183651;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .color-input-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .color-input-wrapper input {
        width: 45px;
        height: 35px;
        padding: 2px;
        border: 1px solid #cbd8e4;
        border-radius: 7px;
        background: #ffffff;
        cursor: pointer;
    }

    .color-input-wrapper strong {
        color: #63778a;
        font-size: 0.7rem;
    }

    .slide-preview {
        display: flex;
        min-height: 230px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 28px;
        border-radius: 14px;
        background: #081e34;
        color: #ffffff;
        text-align: center;
        transition:
            background 0.2s ease,
            color 0.2s ease;
    }

    .slide-preview-label {
        color: #7dd3fc;
        font-size: 0.65rem;
        font-weight: 900;
        letter-spacing: 0.13em;
    }

    .slide-preview > strong {
        font-size: 1.05rem;
    }

    .slide-preview small {
        margin-top: 12px;
        opacity: 0.55;
    }

    .export-submit-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 20px 24px;
        border-top: 1px solid #e3e9ef;
        background: #f8fafc;
    }

    .change-format-button {
        border: 0;
        background: transparent;
        color: #176ca7;
        font-family: inherit;
        font-size: 0.76rem;
        font-weight: 850;
        cursor: pointer;
    }

    .export-button {
        display: inline-flex;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 0 20px;
        border: 0;
        border-radius: 11px;
        color: #ffffff;
        font-family: inherit;
        font-size: 0.8rem;
        font-weight: 900;
        cursor: pointer;
        box-shadow:
            0 9px 22px rgba(8, 50, 83, 0.18);
    }

    .pdf-button {
        background:
            linear-gradient(
                135deg,
                #7f1d1d,
                #dc2626
            );
    }

    .powerpoint-button {
        background:
            linear-gradient(
                135deg,
                #9a3412,
                #ea580c
            );
    }

    .export-button:hover {
        transform: translateY(-1px);
    }

    .export-preview {
        padding: 23px;
    }

    .preview-eyebrow {
        color: #2078b5;
    }

    .export-preview h2 {
        margin-top: 5px;
    }

    .preview-songs {
        display: grid;
        gap: 8px;
        margin-top: 18px;
    }

    .preview-song {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
        padding: 11px;
        border: 1px solid #e0e7ee;
        border-radius: 10px;
        background: #f9fbfd;
    }

    .preview-number {
        display: inline-flex;
        width: 27px;
        height: 27px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        background: #0b2c55;
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 900;
    }

    .preview-song-text {
        display: flex;
        min-width: 0;
        flex-direction: column;
    }

    .preview-song-text strong,
    .preview-song-text small {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .preview-song-text strong {
        color: #183550;
        font-size: 0.78rem;
    }

    .preview-song-text small {
        margin-top: 3px;
        color: #7c8b9b;
        font-size: 0.67rem;
    }

    .preview-note {
        margin: 17px 0 0;
        color: #7b8a99;
        font-size: 0.72rem;
        line-height: 1.5;
    }

    [hidden] {
        display: none !important;
    }

    @media (max-width: 820px) {
        .format-cards,
        .export-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .export-header,
        .format-selection {
            padding: 28px 21px;
        }

        .export-card-heading,
        .export-options,
        .export-submit-area {
            padding-right: 17px;
            padding-left: 17px;
        }

        .format-card {
            align-items: flex-start;
        }

        .color-options {
            grid-template-columns: 1fr;
        }

        .export-submit-area {
            align-items: stretch;
            flex-direction: column;
        }

        .change-format-button,
        .export-button {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formatSelection =
            document.getElementById('format-selection');

        const exportLayout =
            document.getElementById('export-layout');

        const pdfForm =
            document.getElementById('pdf-export-form');

        const powerpointForm =
            document.getElementById(
                'powerpoint-export-form'
            );

        const formatButtons =
            document.querySelectorAll(
                '[data-select-format]'
            );

        const changeFormatButtons =
            document.querySelectorAll(
                '[data-change-format]'
            );

        function showFormat(format) {
            formatSelection.hidden = true;
            exportLayout.hidden = false;

            pdfForm.hidden =
                format !== 'pdf';

            powerpointForm.hidden =
                format !== 'powerpoint';

            window.scrollTo({
                top:
                    exportLayout.offsetTop - 30,
                behavior: 'smooth'
            });
        }

        function returnToSelection() {
            pdfForm.hidden = true;
            powerpointForm.hidden = true;
            exportLayout.hidden = true;
            formatSelection.hidden = false;

            window.scrollTo({
                top:
                    formatSelection.offsetTop - 30,
                behavior: 'smooth'
            });
        }

        formatButtons.forEach(button => {
            button.addEventListener(
                'click',
                () => {
                    showFormat(
                        button.dataset.selectFormat
                    );
                }
            );
        });

        changeFormatButtons.forEach(button => {
            button.addEventListener(
                'click',
                returnToSelection
            );
        });

        const backgroundInput =
            document.getElementById(
                'background_color'
            );

        const textInput =
            document.getElementById(
                'text_color'
            );

        const backgroundValue =
            document.getElementById(
                'background-color-value'
            );

        const textValue =
            document.getElementById(
                'text-color-value'
            );

        const slidePreview =
            document.getElementById(
                'slide-preview'
            );

        function updateSlidePreview() {
            const background =
                backgroundInput.value;

            const textColor =
                textInput.value;

            slidePreview.style.background =
                background;

            slidePreview.style.color =
                textColor;

            backgroundValue.textContent =
                background.toUpperCase();

            textValue.textContent =
                textColor.toUpperCase();
        }

        backgroundInput.addEventListener(
            'input',
            updateSlidePreview
        );

        textInput.addEventListener(
            'input',
            updateSlidePreview
        );

        updateSlidePreview();
    });
</script>
@endsection