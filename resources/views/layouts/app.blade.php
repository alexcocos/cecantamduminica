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
        @yield('title', 'CE CÂNTĂM DUMINICĂ')
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

        @media (max-width: 900px) {
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

        @media (max-width: 600px) {
            .brand-logo-link {
    width: 170px;
    height: 56px;
}

.brand-logo {
    top: -60px;
    left: -4px;
    width: 180px;
    height: 180px;
}

            .navigation nav {
                align-items: stretch;
                flex-direction: column;
            }

            .navigation nav > a,
            .user-menu,
            .user-menu-trigger {
                width: 100%;
            }

            .user-menu-trigger {
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
        }
    </style>
</head>

<body>
    <header class="site-header">
        <div class="container navigation">
            <a
                class="brand-logo-link"
                href="{{ route('songs.index') }}"
                aria-label="Ce cântăm duminică - Pagina principală"
            >
                <img
                    class="brand-logo"
                    src="{{ asset('images/logo.png') }}"
                    alt="Ce cântăm duminică"
                >
            </a>

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
                        Setlisturile mele
                    </a>

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
            Ce cântăm duminică
        </div>
    </footer>
</body>
</html</html>