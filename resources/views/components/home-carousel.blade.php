<section
    class="home-carousel"
    id="home-carousel"
    aria-label="Prezentarea aplicației"
>
    <div
        class="home-carousel-track"
        id="home-carousel-track"
    >
        {{-- Slide 1: homepage-ul actual --}}
        <article
            class="home-carousel-slide home-main-slide"
            
        >
            <div class="home-main-content">
                <p class="home-slide-eyebrow">
                    Repertoriu • Echipe • Setlisturi
                </p>

                <h1>
                    Pregătește programul.
                    <span>Cântați împreună.</span>
                </h1>

                <p class="home-main-description">
                    Piese, versuri, acorduri și setlisturi
                    într-un singur loc, pregătite pentru
                    repetiție și programul de duminică.
                </p>

                <div class="home-main-actions">
                    <a
                        href="{{ route('songs.index') }}"
                        class="home-main-primary"
                    >
                        Vezi repertoriul
                        <span>→</span>
                    </a>

                    @auth
                        <a
                            href="{{ route('setlists.index') }}"
                            class="home-main-secondary"
                        >
                            Setlisturile mele
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="home-main-secondary"
                        >
                            Autentificare
                        </a>
                    @endauth
                </div>
            </div>

            <div class="home-main-preview">
                <div class="home-preview-header">
                    <div>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <small>Program duminică</small>
                </div>

                <div class="home-preview-content">
                    <p>Setlist</p>

                    @foreach ([
                        ['number' => 1, 'title' => 'Eu de Tine am nevoie', 'key' => 'G'],
                        ['number' => 2, 'title' => 'Atât de bun', 'key' => 'D'],
                        ['number' => 3, 'title' => 'Rege al regilor', 'key' => 'C'],
                    ] as $previewSong)
                        <div class="home-preview-song">
                            <span>
                                {{ $previewSong['number'] }}
                            </span>

                            <div>
                                <strong>
                                    {{ $previewSong['title'] }}
                                </strong>

                                <small>
                                    Tonalitate
                                    {{ $previewSong['key'] }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        {{-- Slide 2: repertoriu --}}
        <article
            class="home-carousel-slide home-feature-slide"
            data-background="REPERTORIU"
        >
            <div class="home-feature-content">
                

                <h2>
                    Toate piesele, ușor de găsit
                </h2>

                <p>
                    Caută rapid după titlu sau autor și
                    filtrează repertoriul după tipul piesei.
                </p>

                <a href="{{ route('songs.index') }}">
                    Vezi toate piesele
                    <span>→</span>
                </a>
            </div>

            <div class="home-feature-visual library-visual">
                <div class="visual-search">
                    <span>⌕</span>
                    Caută după titlu sau autor...
                </div>

                <div class="visual-filter-list">
                    <span class="is-active">Toate</span>
                    <span>Laudă</span>
                    <span>Închinare</span>
                </div>

                @foreach ([
                    ['initial' => 'A', 'title' => 'Atât de bun', 'type' => 'Laudă'],
                    ['initial' => 'E', 'title' => 'Eu de Tine am nevoie', 'type' => 'Închinare'],
                    ['initial' => 'R', 'title' => 'Rege al regilor', 'type' => 'Laudă'],
                ] as $song)
                    <div class="visual-song">
                        <span>{{ $song['initial'] }}</span>

                        <div>
                            <strong>{{ $song['title'] }}</strong>
                            <small>{{ $song['type'] }}</small>
                        </div>

                        <b>→</b>
                    </div>
                @endforeach
            </div>
        </article>

        {{-- Slide 3: transpunere --}}
        <article
            class="home-carousel-slide home-feature-slide"
            data-background="ACORDURI"
        >
            <div class="home-feature-content">
                

                <h2>
                    Transpune în timp real
                </h2>

                <p>
                    Schimbă tonalitatea fără să modifici piesa
                    originală. Acordurile rămân poziționate
                    exact deasupra cuvintelor.
                </p>

                <span class="home-feature-note">
                    Tonalitatea aleasă poate fi salvată în setlist.
                </span>
            </div>

            <div class="home-feature-visual chords-visual">
                <div class="visual-key-control">
                    <button type="button">−</button>
                    <span>
                        <small>Tonalitate</small>
                        <strong>G</strong>
                    </span>
                    <button type="button">＋</button>
                </div>

                <div class="visual-lyric">
                    <div>
                        <span style="left: 0%">G</span>
                        <span style="left: 14%">C</span>
                        <span style="left: 25%">G</span>
                    </div>

                    <p>Doamne, vin și-ngenunchez</p>
                </div>

                <div class="visual-lyric">
                    <div>
                        <span style="left: 0%">Em</span>
                        <span style="left: 10%">D</span>
                        <span style="left: 25%">C</span>
                    </div>

                    <p>Ție Îți mărturisesc</p>
                </div>
            </div>
        </article>

        {{-- Slide 4: setlisturi --}}
        <article
            class="home-carousel-slide home-feature-slide"
            data-background="SETLISTURI"
        >
            <div class="home-feature-content">
                

                <h2>
                    Programul în ordinea dorită
                </h2>

                <p>
                    Selectează piesele, trage-le în ordinea
                    programului și salvează tonalitatea și
                    notițele pentru fiecare.
                </p>

                @auth
                    <a href="{{ route('setlists.index') }}">
                        Setlisturile mele
                        <span>→</span>
                    </a>
                @else
                    <a href="{{ route('login') }}">
                        Autentifică-te
                        <span>→</span>
                    </a>
                @endauth
            </div>

            <div class="home-feature-visual setlist-visual">
                @foreach ([
                    ['number' => 1, 'title' => 'Eu de Tine am nevoie', 'detail' => 'G • Notiță salvată'],
                    ['number' => 2, 'title' => 'Atât de bun', 'detail' => 'D • Original'],
                    ['number' => 3, 'title' => 'Rege al regilor', 'detail' => 'C • −2 semitonuri'],
                ] as $setlistSong)
                    <div class="visual-setlist-song">
                        <span class="visual-drag">
                            ⋮⋮
                        </span>

                        <strong>
                            {{ $setlistSong['number'] }}
                        </strong>

                        <div>
                            <b>{{ $setlistSong['title'] }}</b>
                            <small>{{ $setlistSong['detail'] }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        {{-- Slide 5: echipe --}}
        <article
            class="home-carousel-slide home-feature-slide"
            data-background="ECHIPE"
        >
            <div class="home-feature-content">
                
                <h2>
                    Distribuie setlistul
                </h2>

                <p>
                    Creează o echipă, invită membrii printr-un
                    cod și publică setlistul live numai pentru
                    persoanele înscrise.
                </p>

                @auth
                    <a href="{{ route('teams.index') }}">
                        Echipele mele
                        <span>→</span>
                    </a>
                @else
                    <a href="{{ route('login') }}">
                        Intră în cont
                        <span>→</span>
                    </a>
                @endauth
            </div>

            <div class="home-feature-visual team-visual">
                <div class="visual-live">
                    <i></i>
                    Live pentru Casa Pâinii
                </div>

                <h3>Program duminică</h3>

                <div class="visual-members">
                    <span>A</span>
                    <span>M</span>
                    <span>D</span>
                    <span>＋3</span>
                </div>

                <div class="visual-team-code">
                    <small>Cod de înscriere</small>
                    <strong>A7KD92BX</strong>
                </div>
            </div>
        </article>

        {{-- Slide 6: export --}}
        <article
            class="home-carousel-slide home-feature-slide"
            data-background="EXPORT"
        >
            <div class="home-feature-content">
               

                <h2>
                    PDF și PowerPoint
                </h2>

                <p>
                    Descarcă toate piesele într-un singur PDF
                    sau generează automat prezentarea cu versuri.
                </p>

                <span class="home-feature-note">
                    Setlisturile pot fi deschise și fullscreen.
                </span>
            </div>

            <div class="home-feature-visual export-visual">
                <div class="visual-export-file">
                    <span>PDF</span>

                    <div>
                        <strong>Acorduri și versuri</strong>
                        <small>Format compact pe coloane</small>
                    </div>
                </div>

                <div class="visual-export-file">
                    <span>PPT</span>

                    <div>
                        <strong>Prezentare automată</strong>
                        <small>Slide-uri pentru fiecare secțiune</small>
                    </div>
                </div>

                <div class="visual-fullscreen">
                    <i></i>
                    Mod fullscreen inclus
                </div>
            </div>
        </article>
    </div>

    <div class="home-carousel-navigation">
        <div
            class="home-carousel-dots"
            id="home-carousel-dots"
        ></div>

        <div class="home-carousel-buttons">
            <button
                type="button"
                id="home-carousel-previous"
                aria-label="Slide-ul anterior"
            >
                ←
            </button>

            <button
                type="button"
                id="home-carousel-next"
                aria-label="Slide-ul următor"
            >
                →
            </button>
        </div>
    </div>
</section>

<style>
    .home-carousel {
    position: relative;
    width: 100%;
    min-height: calc(100vh - 90px);
    overflow: hidden;
    border-radius: 0;
    background: #061b31;
    box-shadow: none;
}

    .home-carousel-track {
        display: flex;
        transition: transform 0.5s ease;
    }

    .home-carousel-slide {
        position: relative;
        min-width: 100%;
        min-height: calc(100vh - 90px);
        box-sizing: border-box;
        background:
            radial-gradient(
                circle at 90% 5%,
                rgba(67, 163, 225, 0.35),
                transparent 31%
            ),
            linear-gradient(
                140deg,
                #05182b,
                #0a335c 67%,
                #155487
            );
        color: #ffffff;
    }

    .home-carousel-slide::before {
    content: attr(data-background);
    position: absolute;
    top: 19%;
    left: 16%;
    z-index: 0;

    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0.48),
        rgba(175, 190, 203, 0.22)
    );
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;

    font-size: clamp(3.5rem, 8vw, 8rem);
    font-weight: 950;
    line-height: 0.85;
    letter-spacing: -0.06em;
    white-space: nowrap;
    pointer-events: none;
    transform: none;
    user-select: none;
}

    .home-main-slide,
    .home-feature-slide {
        display: grid;
        grid-template-columns:
            minmax(0, 560px)
            minmax(420px, 560px);
        align-items: center;
        justify-content: center;
        gap: clamp(45px, 6vw, 95px);
        padding: 70px clamp(30px, 6vw, 90px) 95px;
    }

    .home-main-content,
    .home-feature-content,
    .home-main-preview,
    .home-feature-visual {
        position: relative;
        z-index: 1;
    }

    .home-slide-eyebrow {
        margin: 0 0 15px;
        color: #8fd5ff;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
    }

    .home-main-content h1 {
        max-width: 800px;
        margin: 0;
        color: #ffffff;
        font-size: clamp(3rem, 7.5vw, 6.6rem);
        line-height: 0.88;
        letter-spacing: -0.07em;
    }

    .home-main-content h1 span {
        display: block;
        color: #92d8ff;
    }

    .home-main-description {
        max-width: 650px;
        margin: 24px 0 0;
        color: #c6d9e9;
        font-size: 0.96rem;
        line-height: 1.75;
    }

    .home-main-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 30px;
    }

    .home-main-primary,
    .home-main-secondary {
        display: inline-flex;
        min-height: 50px;
        align-items: center;
        justify-content: center;
        gap: 17px;
        padding: 0 18px;
        border-radius: 12px;
        font-size: 0.78rem;
        font-weight: 900;
        text-decoration: none;
    }

    .home-main-primary {
        background: #ffffff;
        color: #092847;
    }

    .home-main-secondary {
        border: 1px solid rgba(255, 255, 255, 0.18);
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    .home-main-preview {
        width: 100%;
        min-height: 390px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.09);
        box-shadow:
            0 28px 65px rgba(0, 0, 0, 0.22);
        backdrop-filter: blur(12px);
        transform: rotate(2deg);
    }

    .home-preview-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .home-preview-header > div {
        display: flex;
        gap: 6px;
    }

    .home-preview-header > div span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.34);
    }

    .home-preview-header small {
        color: #aac2d5;
        font-size: 0.64rem;
    }

    .home-preview-content {
        display: grid;
        gap: 13px;
        padding: 30px;
    }

    .home-preview-content > p {
        margin: 0 0 5px;
        color: #8fd5ff;
        font-size: 0.67rem;
        font-weight: 900;
        text-transform: uppercase;
    }

    .home-preview-song {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 12px;
        padding: 17px;
        border-radius: 11px;
        background: rgba(3, 21, 39, 0.34);
    }

    .home-preview-song > span {
        display: grid;
        width: 35px;
        height: 35px;
        place-items: center;
        border-radius: 9px;
        background: #dfeefa;
        color: #12679f;
        font-size: 0.72rem;
        font-weight: 900;
    }

    .home-preview-song div {
        display: grid;
        gap: 3px;
    }

    .home-preview-song strong {
        font-size: 0.77rem;
    }

    .home-preview-song small {
        color: #9eb8cc;
        font-size: 0.62rem;
    }

    .home-feature-content h2 {
        max-width: 720px;
        margin: 0;
        color: #ffffff;
        font-size: clamp(2.3rem, 5vw, 4.7rem);
        line-height: 0.95;
        letter-spacing: -0.06em;
    }

    .home-feature-content > p:not(.home-slide-eyebrow) {
        max-width: 600px;
        margin: 22px 0 0;
        color: #c6d9e9;
        line-height: 1.75;
    }

    .home-feature-content > a {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 27px;
        color: #ffffff;
        font-size: 0.78rem;
        font-weight: 900;
        text-decoration: none;
    }

    .home-feature-note {
        display: inline-flex;
        margin-top: 25px;
        padding: 10px 13px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.07);
        color: #bfd6e7;
        font-size: 0.72rem;
    }

    .home-feature-visual {
        width: 100%;
        min-height: 360px;
        box-sizing: border-box;
        padding: 32px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.09);
        box-shadow:
            0 28px 65px rgba(0, 0, 0, 0.21);
        backdrop-filter: blur(12px);
    }

    .library-visual,
    .chords-visual,
    .setlist-visual,
    .team-visual,
    .export-visual {
        align-self: center;
    }

    .visual-search {
        padding: 16px 18px;
        border-radius: 10px;
        background: #ffffff;
        color: #8493a1;
        font-size: 0.72rem;
    }

    .visual-search span {
        margin-right: 8px;
        color: #176fa8;
    }

    .visual-filter-list {
        display: flex;
        gap: 6px;
        margin: 11px 0;
    }

    .visual-filter-list span {
        padding: 6px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        color: #aac2d4;
        font-size: 0.61rem;
    }

    .visual-filter-list .is-active {
        background: #dceefa;
        color: #12689f;
    }

    .visual-song {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 11px;
        margin-top: 8px;
        padding: 15px;
        border-radius: 10px;
        background: rgba(4, 25, 45, 0.34);
    }

    .visual-song > span,
    .visual-setlist-song > strong {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 9px;
        background: #dceefa;
        color: #12689f;
        font-size: 0.7rem;
    }

    .visual-song div,
    .visual-setlist-song div {
        display: grid;
        gap: 2px;
    }

    .visual-song strong,
    .visual-setlist-song b {
        font-size: 0.76rem;
    }

    .visual-song small,
    .visual-setlist-song small {
        color: #a8bfd1;
        font-size: 0.61rem;
    }

    .visual-song b {
        color: #8fd5ff;
    }

    .visual-key-control {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 19px;
        margin-bottom: 55px;
    }

    .visual-key-control button {
        width: 50px;
        height: 50px;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 11px;
        background: rgba(255, 255, 255, 0.09);
        color: #ffffff;
    }

    .visual-key-control > span {
        display: grid;
        gap: 2px;
        text-align: center;
    }

    .visual-key-control small {
        color: #9eb7ca;
        font-size: 0.57rem;
        text-transform: uppercase;
    }

    .visual-key-control strong {
        font-size: 1.5rem;
    }

    .visual-lyric {
        margin-top: 42px;
    }

    .visual-lyric > div {
        position: relative;
        height: 21px;
    }

    .visual-lyric span {
        position: absolute;
        color: #7fd0ff;
        font-weight: 900;
    }

    .visual-lyric p {
        margin: 0;
        font-family: monospace;
        white-space: nowrap;
    }

    .setlist-visual {
        display: grid;
        gap: 9px;
    }

    .visual-setlist-song {
        display: grid;
        grid-template-columns: auto auto 1fr;
        align-items: center;
        gap: 11px;
        padding: 17px;
        border-radius: 10px;
        background: rgba(4, 25, 45, 0.34);
    }

    .visual-drag {
        color: #88a8c0;
    }

    .visual-live {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 9px;
        background: rgba(45, 201, 112, 0.14);
        color: #9df0bf;
        font-size: 0.66rem;
        font-weight: 900;
    }

    .visual-live i {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #4ade80;
    }

    .team-visual h3 {
        margin: 27px 0 15px;
    }

    .visual-members {
        display: flex;
    }

    .visual-members span {
        display: grid;
        width: 39px;
        height: 39px;
        margin-right: -7px;
        place-items: center;
        border: 3px solid #0b355e;
        border-radius: 50%;
        background: #e1eff9;
        color: #145f90;
        font-size: 0.66rem;
        font-weight: 900;
    }

    .visual-team-code {
        display: grid;
        gap: 4px;
        margin-top: 25px;
        padding: 12px;
        border-radius: 10px;
        background: rgba(4, 24, 43, 0.35);
    }

    .visual-team-code small {
        color: #9ab5ca;
        font-size: 0.6rem;
        text-transform: uppercase;
    }

    .visual-team-code strong {
        letter-spacing: 0.12em;
    }

    .export-visual {
        display: grid;
        gap: 11px;
    }

    .visual-export-file {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 12px;
        padding: 18px;
        border-radius: 11px;
        background: rgba(4, 25, 45, 0.34);
    }

    .visual-export-file > span {
        display: grid;
        width: 44px;
        height: 44px;
        place-items: center;
        border-radius: 10px;
        background: #ffffff;
        color: #0a4775;
        font-size: 0.64rem;
        font-weight: 950;
    }

    .visual-export-file div {
        display: grid;
        gap: 2px;
    }

    .visual-export-file strong {
        font-size: 0.77rem;
    }

    .visual-export-file small {
        color: #a4bdd0;
        font-size: 0.62rem;
    }

    .visual-fullscreen {
        margin-top: 8px;
        color: #b8d0e1;
        font-size: 0.67rem;
    }

    .visual-fullscreen i {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin-right: 7px;
        border-top: 2px solid #83d0ff;
        border-right: 2px solid #83d0ff;
    }

    .home-carousel-navigation {
        position: absolute;
        right: clamp(25px, 5vw, 55px);
        bottom: 24px;
        left: clamp(25px, 5vw, 55px);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .home-carousel-dots {
        display: flex;
        gap: 7px;
    }

    .home-carousel-dot {
        width: 8px;
        height: 8px;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.35);
        cursor: pointer;
        transition:
            width 0.25s ease,
            background 0.25s ease;
    }

    .home-carousel-dot.is-active {
        width: 28px;
        background: #ffffff;
    }

    .home-carousel-buttons {
        display: flex;
        gap: 7px;
    }

    .home-carousel-buttons button {
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        font: inherit;
        font-weight: 900;
        cursor: pointer;
    }

    @media (max-width: 850px) {
        .home-main-slide,
        .home-feature-slide {
            grid-template-columns: 1fr;
            min-height: 780px;
            justify-items: center;
            align-content: center;
            gap: 34px;
            padding: 55px 28px 100px;
        }

        .home-main-content,
        .home-feature-content {
            width: min(100%, 650px);
            text-align: center;
        }

        .home-main-description,
        .home-feature-content > p:not(.home-slide-eyebrow) {
            margin-right: auto;
            margin-left: auto;
        }

        .home-main-actions {
            justify-content: center;
        }

        .home-main-preview,
        .home-feature-visual {
            width: min(100%, 620px);
        }

        .home-main-preview {
            transform: none;
        }

        .home-carousel-slide::before {
            font-size: clamp(4.5rem, 18vw, 9rem);
        }
    }

    @media (max-width: 520px) {
        .home-main-slide,
        .home-feature-slide {
            min-height: 760px;
            padding: 35px 22px 90px;
        }

        .home-feature-visual {
            padding: 16px;
        }

        .visual-lyric p {
            font-size: 0.75rem;
            white-space: normal;
        }
    }

    /*
     * Aspect dedicat telefoanelor.
     * Aceste reguli suprascriu doar varianta mobilă.
     */
    @media (max-width: 700px) {
        .home-carousel,
        .home-carousel-slide {
            min-height: 940px;
        }

        .home-main-slide,
        .home-feature-slide {
            grid-template-columns: minmax(0, 1fr);
            grid-template-rows: auto auto;
            min-height: 940px;
            align-content: start;
            justify-items: stretch;
            gap: 42px;
            padding: 105px 18px 105px;
        }

        .home-carousel-slide::before {
            top: 38px;
            left: 18px;
            max-width: calc(100% - 36px);
            overflow: hidden;
            font-size: clamp(2.65rem, 13vw, 4.25rem);
            line-height: 0.9;
            letter-spacing: -0.05em;
            white-space: nowrap;
        }

        .home-main-content,
        .home-feature-content {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
            text-align: left;
        }

        .home-slide-eyebrow {
            margin-bottom: 12px;
            font-size: 0.64rem;
            line-height: 1.4;
        }

        .home-main-content h1 {
            max-width: 100%;
            font-size: clamp(2.55rem, 12vw, 4rem);
            line-height: 0.98;
            letter-spacing: -0.055em;
        }

        .home-feature-content h2 {
            max-width: 100%;
            font-size: clamp(2.25rem, 10vw, 3.45rem);
            line-height: 1;
            letter-spacing: -0.045em;
        }

        .home-main-description,
        .home-feature-content > p:not(.home-slide-eyebrow) {
            max-width: 100%;
            margin: 17px 0 0;
            font-size: 0.88rem;
            line-height: 1.65;
        }

        .home-main-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 9px;
            margin-top: 22px;
        }

        .home-main-primary,
        .home-main-secondary {
            width: 100%;
            min-height: 48px;
            box-sizing: border-box;
        }

        .home-feature-content > a {
            margin-top: 21px;
        }

        .home-feature-note {
            margin-top: 20px;
            line-height: 1.5;
        }

        .home-main-preview,
        .home-feature-visual {
            width: 100%;
            max-width: 520px;
            min-height: 0;
            margin: 0 auto;
            box-sizing: border-box;
            border-radius: 17px;
            transform: none;
        }

        .home-preview-content,
        .home-feature-visual {
            padding: 18px;
        }

        .home-preview-content {
            gap: 9px;
        }

        .home-preview-song,
        .visual-song,
        .visual-setlist-song,
        .visual-export-file {
            padding: 12px;
        }

        .visual-filter-list {
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
        }

        .visual-filter-list::-webkit-scrollbar {
            display: none;
        }

        .visual-filter-list span {
            flex-shrink: 0;
        }

        .visual-key-control {
            margin-bottom: 34px;
        }

        .visual-lyric {
            margin-top: 30px;
        }

        .visual-lyric p {
            overflow-wrap: anywhere;
            font-size: 0.78rem;
            line-height: 1.55;
            white-space: normal;
        }

        .team-visual h3 {
            margin-top: 20px;
        }

        .home-carousel-navigation {
            right: 18px;
            bottom: 22px;
            left: 18px;
        }

        .home-carousel-buttons button {
            width: 42px;
            height: 42px;
        }
    }

    @media (max-width: 390px) {
        .home-carousel,
        .home-carousel-slide,
        .home-main-slide,
        .home-feature-slide {
            min-height: 910px;
        }

        .home-main-slide,
        .home-feature-slide {
            gap: 34px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .home-carousel-slide::before {
            left: 15px;
            font-size: 2.5rem;
        }

        .home-carousel-dots {
            gap: 5px;
        }

        .home-carousel-dot.is-active {
            width: 21px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .home-carousel-track {
            transition: none;
        }
    }
</style>

<script>
    (() => {
        const carousel =
            document.getElementById('home-carousel');

        if (!carousel) {
            return;
        }

        const track =
            document.getElementById(
                'home-carousel-track'
            );

        const slides =
            Array.from(track.children);

        const dotsContainer =
            document.getElementById(
                'home-carousel-dots'
            );

        const previousButton =
            document.getElementById(
                'home-carousel-previous'
            );

        const nextButton =
            document.getElementById(
                'home-carousel-next'
            );

        let currentIndex = 0;
        let timer = null;

        function showSlide(index) {
            currentIndex =
                (index + slides.length) %
                slides.length;

            track.style.transform =
                `translateX(-${currentIndex * 100}%)`;

            dotsContainer
                .querySelectorAll(
                    '.home-carousel-dot'
                )
                .forEach(function (dot, dotIndex) {
                    dot.classList.toggle(
                        'is-active',
                        dotIndex === currentIndex
                    );
                });
        }

        function restartTimer() {
            window.clearInterval(timer);

            timer = window.setInterval(
                function () {
                    showSlide(currentIndex + 1);
                },
                7000
            );
        }

        slides.forEach(function (_, index) {
            const dot =
                document.createElement('button');

            dot.type = 'button';
            dot.className = 'home-carousel-dot';

            dot.setAttribute(
                'aria-label',
                `Deschide slide-ul ${index + 1}`
            );

            dot.addEventListener(
                'click',
                function () {
                    showSlide(index);
                    restartTimer();
                }
            );

            dotsContainer.appendChild(dot);
        });

        previousButton.addEventListener(
            'click',
            function () {
                showSlide(currentIndex - 1);
                restartTimer();
            }
        );

        nextButton.addEventListener(
            'click',
            function () {
                showSlide(currentIndex + 1);
                restartTimer();
            }
        );

        carousel.addEventListener(
            'mouseenter',
            function () {
                window.clearInterval(timer);
            }
        );

        carousel.addEventListener(
            'mouseleave',
            restartTimer
        );

        showSlide(0);
        restartTimer();
    })();
</script>