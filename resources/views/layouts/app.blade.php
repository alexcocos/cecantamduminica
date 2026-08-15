<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Ce Cântăm Duminică')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        /*
         * Logo
         */
        .brand-logo-link {
            position: relative;
            display: block;
            width: 190px;
            height: 62px;
            flex-shrink: 0;
            overflow: hidden;
            text-decoration: none;
        }

        .brand-logo {
            position: absolute;
            top: -67px;
            left: -5px;
            display: block;
            width: 200px;
            max-width: none;
            height: 200px;
            object-fit: contain;
            transition:
                transform 0.2s ease,
                opacity 0.2s ease;
        }

        .brand-logo-link:hover .brand-logo {
            transform: scale(1.035);
            opacity: 0.92;
        }

        /*
         * Navigare
         */
        .navigation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .navigation nav {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        /*
         * Setlist live
         */
        .navigation-live {
            position: relative;
            z-index: 60;
        }

        .navigation-live-link,
        .navigation-live-trigger {
            display: inline-flex;
            min-height: 42px;
            box-sizing: border-box;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 14px;
            border: 1px solid rgba(49, 214, 118, 0.72);
            border-radius: 11px;
            background:
                linear-gradient(
                    135deg,
                    rgba(42, 48, 48, 0.96),
                    rgba(12, 18, 18, 0.98)
                );
            color: #ffffff;
            font-family: inherit;
            font-size: 0.36rem;
            font-weight: 300;
            letter-spacing: 0em;
            text-decoration: none;
            cursor: pointer;
            box-shadow:
                0 8px 22px rgba(1, 11, 7, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            transition:
                border-color 0.2s ease,
                background 0.2s ease,
                transform 0.2s ease;
        }

        .navigation-live-link:hover,
        .navigation-live-trigger:hover {
            border-color: #4fe08b;
            background:
                linear-gradient(
                    135deg,
                    rgba(47, 61, 56, 0.98),
                    rgba(13, 24, 20, 0.98)
                );
            transform: translateY(-1px);
        }

        .navigation-live-dot {
            display: block;
            width: 7px;
            height: 7px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #48dd87;
            box-shadow:
                0 0 0 4px rgba(72, 221, 135, 0.12);
            animation:
                navigation-live-pulse 1.8s ease-in-out infinite;
        }

        .navigation-live-label {
            color: #91efb8;
            text-transform: uppercase;
            font-family: "DM Sans", sans-serif;
    font-weight: 400;
    letter-spacing: 0.09em;
    text-transform: uppercase;
        }

        .navigation-live-name {
            display: block;
            max-width: 125px;
            overflow: hidden;
            color: #ffffff;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @keyframes navigation-live-pulse {
            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.65;
                transform: scale(0.83);
            }
        }

        /*
         * Dropdown pentru mai multe setlisturi live
         */
        .navigation-live-menu {
            position: relative;
        }

        .navigation-live-menu > summary {
            list-style: none;
        }

        .navigation-live-menu > summary::-webkit-details-marker {
            display: none;
        }

        .navigation-live-trigger::after {
            content: "⌄";
            position: relative;
            top: -2px;
            margin-left: 2px;
            color: #8eeab5;
            font-size: 0.85rem;
        }

        .navigation-live-dropdown {
            position: absolute;
            top: calc(100% + 9px);
            right: 0;
            display: grid;
            width: 280px;
            gap: 6px;
            padding: 8px;
            border: 1px solid rgba(52, 208, 117, 0.28);
            border-radius: 13px;
            background: #101817;
            box-shadow:
                0 18px 45px rgba(3, 16, 11, 0.32);
        }

        .navigation-live-dropdown a {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 11px;
            padding: 12px;
            border-radius: 9px;
            color: #ffffff;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .navigation-live-dropdown a:hover {
            background: rgba(72, 221, 135, 0.1);
        }

        .navigation-live-dropdown-information {
            display: grid;
            min-width: 0;
            gap: 3px;
        }

        .navigation-live-dropdown-information strong {
            overflow: hidden;
            color: #ffffff;
            font-size: 0.3rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .navigation-live-dropdown-information small {
            overflow: hidden;
            color: #8dc3a5;
            font-size: 0.3rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .navigation-live-dropdown-arrow {
            color: #76e6a5;
            font-size: 0.8rem;
        }

        /*
         * Meniul utilizatorului
         */
        .user-menu {
            position: relative;
            z-index: 50;
        }

        .user-menu-trigger {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            gap: 9px;
            padding: 0 15px;
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 11px;
            background:
                linear-gradient(
                    135deg,
                    #071d34,
                    #0c416f
                );
            color: #ffffff;
            font-family: inherit;
            font-size: 0.78rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow:
                0 7px 18px rgba(6, 31, 55, 0.15);
        }

        .user-menu-trigger::after {
            content: "⌄";
            position: relative;
            top: -2px;
            color: #9ed8ff;
            font-size: 0.9rem;
            transition: transform 0.2s ease;
        }

        .user-menu:hover
        .user-menu-trigger::after,
        .user-menu:focus-within
        .user-menu-trigger::after {
            transform: rotate(180deg);
        }

        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 150px;
            padding: 7px;
            border: 1px solid rgba(8, 39, 68, 0.12);
            border-radius: 11px;
            background: #ffffff;
            box-shadow:
                0 15px 38px rgba(5, 30, 54, 0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-7px);
            transition:
                opacity 0.18s ease,
                visibility 0.18s ease,
                transform 0.18s ease;
        }

        .user-menu:hover
        .user-menu-dropdown,
        .user-menu:focus-within
        .user-menu-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-menu-dropdown::before {
            content: "";
            position: absolute;
            right: 0;
            bottom: 100%;
            width: 100%;
            height: 10px;
        }

        .logout-form {
            display: block;
            margin: 0;
        }

        .logout-button {
            width: 100%;
            padding: 10px 12px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #a31f36;
            font-family: inherit;
            font-size: 0.78rem;
            font-weight: 900;
            text-align: left;
            cursor: pointer;
            transition:
                color 0.2s ease,
                background 0.2s ease;
        }

        .logout-button:hover {
            background: #fff0f2;
            color: #83152a;
        }

        .mobile-menu-toggle {
            display: none;
            width: 44px;
            height: 44px;
            padding: 0;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            background: rgba(4, 27, 50, 0.72);
            cursor: pointer;
        }

        .mobile-menu-toggle span {
            display: block;
            width: 19px;
            height: 2px;
            margin: 4px auto;
            border-radius: 999px;
            background: #ffffff;
            transition:
                transform 0.2s ease,
                opacity 0.2s ease;
        }

        .mobile-menu-toggle.is-open span:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }

        .mobile-menu-toggle.is-open span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.is-open span:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }

        @media (max-width: 1050px) {
            .navigation {
                align-items: flex-start;
                flex-direction: column;
                gap: 18px;
            }

            .navigation nav {
                width: 100%;
                flex-wrap: wrap;
                gap: 12px;
            }
        }

        @media (max-width: 700px) {
            .site-header {
                padding: 8px 0;
            }

            .navigation {
                display: grid;
                grid-template-columns: 1fr auto;
                align-items: center;
                gap: 10px 14px;
            }

            .brand-logo-link {
                width: 145px;
                height: 50px;
            }

            .brand-logo {
                top: -53px;
                left: -4px;
                width: 160px;
                height: 160px;
            }

            .mobile-menu-toggle {
                display: block;
                grid-column: 2;
                grid-row: 1;
            }

            .navigation nav {
                display: flex;
                grid-column: 1 / -1;
                width: 100%;
                align-items: stretch;
                flex-direction: column;
                gap: 8px;
            }

            .navigation nav > a,
            .navigation nav > .user-menu {
                display: none;
            }

            .navigation nav > .navigation-live {
                display: block;
                width: 100%;
                order: -1;
            }

            .navigation-live-link,
            .navigation-live-menu,
            .navigation-live-trigger {
                width: 100%;
                box-sizing: border-box;
            }

            .navigation-live-link,
            .navigation-live-trigger {
                min-height: 44px;
                justify-content: flex-start;
                padding: 0 14px;
                border-radius: 12px;
            }

            .navigation-live-name {
                max-width: none;
                font-family: "DM Sans", sans-serif !important;
                font-size: 0.76rem;
                font-weight: 800;
            }

            .navigation-live-dropdown {
                position: static;
                width: 100%;
                box-sizing: border-box;
                margin-top: 7px;
            }

            .navigation-live,
            .navigation-live * {
                font-family: "DM Sans", sans-serif;
            }

            .navigation nav.is-open > a {
                display: flex;
                min-height: 43px;
                box-sizing: border-box;
                align-items: center;
                padding: 0 14px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                background: rgba(7, 31, 55, 0.72);
                color: #ffffff;
                text-decoration: none;
            }

            .navigation nav.is-open > .user-menu {
                display: block;
                width: 100%;
            }

            .user-menu-trigger {
                width: 100%;
                min-height: 43px;
                justify-content: space-between;
            }

            .user-menu-dropdown {
                position: static;
                display: none;
                width: 100%;
                margin-top: 7px;
                opacity: 1;
                visibility: visible;
                transform: none;
            }

            .user-menu:hover
            .user-menu-dropdown,
            .user-menu:focus-within
            .user-menu-dropdown {
                display: block;
            }

            .main-content {
                width: 100%;
                box-sizing: border-box;
                padding-right: 16px;
                padding-left: 16px;
            }

            .alert-success,
            .form-errors {
                margin: 12px 0;
                padding: 13px 15px;
                border-radius: 12px;
            }

            .site-footer {
                padding-right: 16px;
                padding-left: 16px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .navigation-live-dot {
                animation: none;
            }
        }
    </style>
</head>

<body>
    @php
        $navigationLiveSetlists = collect();

        if (auth()->check()) {
            $navigationLiveSetlists = \App\Models\Setlist::query()
                ->where('is_live', true)
                ->whereHas(
                    'team.users',
                    function ($query) {
                        $query->where(
                            'users.id',
                            auth()->id()
                        );
                    }
                )
                ->with([
                    'team',
                ])
                ->withCount('songs')
                ->latest('updated_at')
                ->get();
        }
    @endphp

    <header class="site-header">
        <div class="container navigation">
            <a
                class="brand-logo-link"
                href="{{ route('home') }}"
                aria-label="Ce cântăm duminică - Pagina principală"
            >
                <img
                    class="brand-logo"
                    src="{{ asset('images/logo.png') }}"
                    alt="Ce cântăm duminică"
                >
            </a>

            <button
                type="button"
                class="mobile-menu-toggle"
                id="mobile-menu-toggle"
                aria-label="Deschide meniul"
                aria-expanded="false"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav>
                <a href="{{ route('songs.index') }}">
                    Piese
                </a>

                @guest
                    <a href="{{ route('login') }}">
                        Autentificare
                    </a>
                @endguest

                @auth
                    <a href="{{ route('setlists.index') }}">
                        Setlist
                    </a>

                    <a href="{{ route('teams.index') }}">
                        Echipe
                    </a>

                    @if ($navigationLiveSetlists->count() === 1)
                        @php
                            $navigationLiveSetlist =
                                $navigationLiveSetlists->first();
                        @endphp

                        <div class="navigation-live">
                            <a
                                class="navigation-live-link"
                                href="{{ route(
                                    'setlists.show',
                                    $navigationLiveSetlist
                                ) }}"
                            >
                                <i class="navigation-live-dot"></i>

                                <span class="navigation-live-label">
                                    Live
                                </span>

                                <span class="navigation-live-name">
                                    {{ $navigationLiveSetlist->name }}
                                </span>
                            </a>
                        </div>
                    @elseif ($navigationLiveSetlists->count() > 1)
                        <div class="navigation-live">
                            <details class="navigation-live-menu">
                                <summary class="navigation-live-trigger">
                                    <i class="navigation-live-dot"></i>

                                    <span class="navigation-live-label">
                                        Live
                                    </span>

                                    <span>
                                        {{ $navigationLiveSetlists->count() }}
                                        setlisturi
                                    </span>
                                </summary>

                                <div class="navigation-live-dropdown">
                                    @foreach (
                                        $navigationLiveSetlists
                                        as $navigationLiveSetlist
                                    )
                                        <a
                                            href="{{ route(
                                                'setlists.show',
                                                $navigationLiveSetlist
                                            ) }}"
                                        >
                                            <i class="navigation-live-dot"></i>

                                            <span
                                                class="navigation-live-dropdown-information"
                                            >
                                                <strong>
                                                    {{ $navigationLiveSetlist->name }}
                                                </strong>

                                                <small>
                                                    {{ $navigationLiveSetlist->team->name }}

                                                    ·

                                                    {{ $navigationLiveSetlist->songs_count }}

                                                    {{ $navigationLiveSetlist->songs_count === 1
                                                        ? 'piesă'
                                                        : 'piese'
                                                    }}
                                                </small>
                                            </span>

                                            <span
                                                class="navigation-live-dropdown-arrow"
                                            >
                                                →
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    @endif

                    <div class="user-menu">
                        <button
                            type="button"
                            class="user-menu-trigger"
                            aria-label="Deschide meniul utilizatorului"
                        >
                            {{ auth()->user()->name }}
                        </button>

                        <div class="user-menu-dropdown">
                            <form
                                class="logout-form"
                                action="{{ route('logout') }}"
                                method="POST"
                            >
                                @csrf

                                <button
                                    class="logout-button"
                                    type="submit"
                                >
                                    Delogare
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container main-content">
        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="form-errors">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
    <div class="container">
        <span>
             © Website Dezvoltat de Biserica Casa Pâinii Bahnea
        </span>
    </div>
</footer>

    <script>
        (() => {
            const button =
                document.getElementById(
                    'mobile-menu-toggle'
                );

            const navigation =
                document.querySelector(
                    '.navigation nav'
                );

            if (!button || !navigation) {
                return;
            }

            button.addEventListener(
                'click',
                function () {
                    const isOpen =
                        navigation.classList.toggle(
                            'is-open'
                        );

                    button.classList.toggle(
                        'is-open',
                        isOpen
                    );

                    button.setAttribute(
                        'aria-expanded',
                        isOpen ? 'true' : 'false'
                    );
                }
            );
        })();
    </script>
</body>
</html>