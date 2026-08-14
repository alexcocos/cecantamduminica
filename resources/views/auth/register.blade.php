@extends('layouts.app')

@section('title', 'Creează cont | Ce cântăm duminică')

@section('content')
    <section class="auth-page">
        <div class="auth-card">
            <div class="auth-introduction">
                <p class="auth-eyebrow">
                    Setlisturile tale
                </p>

                <h1>Creează un cont</h1>

                <p>
                    Salvează setlisturi, stabilește ordinea
                    pieselor și generează materialele pentru
                    repetiții sau evenimente.
                </p>

                <div class="auth-benefits">
                    <div>
                        <span>✓</span>
                        <p>Setlisturi salvate în contul tău</p>
                    </div>

                    <div>
                        <span>✓</span>
                        <p>Reordonarea pieselor în timp real</p>
                    </div>

                    <div>
                        <span>✓</span>
                        <p>Export PDF și PowerPoint</p>
                    </div>
                </div>
            </div>

            <div class="auth-form-container">
                <div class="auth-form-heading">
                    <h2>Înregistrare</h2>

                    <p>
                        Completează datele pentru a continua.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="auth-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    action="{{ route('register.attempt') }}"
                    method="POST"
                    class="auth-form"
                >
                    @csrf

                    <div class="auth-field">
                        <label for="name">
                            Nume
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            placeholder="Numele tău"
                            required
                            autofocus
                        >
                    </div>

                    <div class="auth-field">
                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="nume@exemplu.ro"
                            required
                        >
                    </div>

                    <div class="auth-field">
                        <label for="password">
                            Parolă
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            placeholder="Minimum 8 caractere"
                            minlength="8"
                            required
                        >
                    </div>

                    <div class="auth-field">
                        <label for="password_confirmation">
                            Confirmă parola
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Scrie parola din nou"
                            minlength="8"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="auth-submit"
                    >
                        Creează contul
                    </button>
                </form>

                <p class="auth-switch">
                    Ai deja un cont?

                    <a href="{{ route('login') }}">
                        Autentifică-te
                    </a>
                </p>
            </div>
        </div>
    </section>

    <style>
        .auth-page {
            min-height: calc(100vh - 250px);
            display: grid;
            place-items: center;
        }

        .auth-card {
            width: min(940px, 100%);
            display: grid;
            grid-template-columns:
                minmax(0, 0.95fr)
                minmax(0, 1.05fr);
            overflow: hidden;
            background: white;
            border: 1px solid #cbd8e5;
            border-radius: 20px;
            box-shadow:
                0 22px 60px rgba(7, 25, 47, 0.15);
        }

        .auth-introduction {
            position: relative;
            padding: 48px 42px;
            overflow: hidden;
            background:
                linear-gradient(
                    145deg,
                    #07192f 0%,
                    #0b2c55 65%,
                    #15538c 100%
                );
            color: white;
        }

        .auth-introduction::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -100px;
            width: 260px;
            height: 260px;
            background: rgba(99, 179, 237, 0.14);
            border-radius: 50%;
        }

        .auth-eyebrow {
            position: relative;
            z-index: 1;
            margin: 0 0 12px;
            color: #81c5f5;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .auth-introduction h1 {
            position: relative;
            z-index: 1;
            margin: 0;
            font-family: "DM Sans", sans-serif;
            font-size: clamp(2.1rem, 5vw, 3.35rem);
            line-height: 1.05;
            letter-spacing: -0.05em;
        }

        .auth-introduction > p:not(.auth-eyebrow) {
            position: relative;
            z-index: 1;
            margin: 18px 0 0;
            color: #c5d8ea;
            font-size: 0.96rem;
        }

        .auth-benefits {
            position: relative;
            z-index: 1;
            margin-top: 36px;
            display: flex;
            flex-direction: column;
            gap: 13px;
        }

        .auth-benefits > div {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .auth-benefits span {
            width: 27px;
            height: 27px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: rgba(129, 197, 245, 0.16);
            border: 1px solid rgba(129, 197, 245, 0.25);
            border-radius: 50%;
            color: #92d1fb;
            font-size: 0.76rem;
            font-weight: 900;
        }

        .auth-benefits p {
            margin: 0;
            color: #dfebf5;
            font-size: 0.86rem;
        }

        .auth-form-container {
            padding: 45px 44px;
        }

        .auth-form-heading h2 {
            margin: 0;
            color: #07192f;
            font-family: "DM Sans", sans-serif;
            font-size: 1.7rem;
            letter-spacing: -0.035em;
        }

        .auth-form-heading p {
            margin: 5px 0 0;
            color: #718096;
            font-size: 0.88rem;
        }

        .auth-errors {
            margin-top: 20px;
            padding: 13px 16px;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-left: 4px solid #be123c;
            border-radius: 8px;
            color: #881337;
            font-size: 0.83rem;
        }

        .auth-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .auth-form {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            gap: 17px;
        }

        .auth-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .auth-field label {
            color: #111820;
            font-size: 0.84rem;
            font-weight: 750;
        }

        .auth-field input {
            width: 100%;
            min-height: 47px;
            padding: 11px 13px;
            background: #f5f7fa;
            border: 1px solid #c5d0dc;
            border-radius: 8px;
            color: #080d14;
            font-family: "DM Sans", sans-serif;
            font-size: 0.92rem;
            outline: none;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .auth-field input:focus {
            background: white;
            border-color: #2878c8;
            box-shadow:
                0 0 0 4px rgba(40, 120, 200, 0.11);
        }

        .auth-field input::placeholder {
            color: #8a97a5;
        }

        .auth-submit {
            min-height: 49px;
            margin-top: 4px;
            background: #0b2c55;
            border: 0;
            border-radius: 8px;
            color: white;
            font-family: "DM Sans", sans-serif;
            font-size: 0.92rem;
            font-weight: 750;
            cursor: pointer;
            transition:
                background 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .auth-submit:hover {
            background: #174f86;
            box-shadow:
                0 8px 20px rgba(23, 79, 134, 0.22);
            transform: translateY(-1px);
        }

        .auth-switch {
            margin: 22px 0 0;
            color: #718096;
            font-size: 0.85rem;
            text-align: center;
        }

        .auth-switch a {
            color: #155d9c;
            font-weight: 750;
        }

        .auth-switch a:hover {
            color: #0b2c55;
            text-decoration: underline;
        }

        @media (max-width: 760px) {
            .auth-card {
                grid-template-columns: 1fr;
            }

            .auth-introduction {
                padding: 35px 26px;
            }

            .auth-benefits {
                display: none;
            }

            .auth-form-container {
                padding: 34px 24px;
            }
        }
    </style>
@endsection