@extends('layouts.app')

@section('title', 'Setlisturile mele | Ce cântăm duminică')

@section('content')
<section class="setlists-page">
    <header class="setlists-header">
        <div>
            <p class="setlists-eyebrow">
                Spațiul tău
            </p>

            <h1>Setlisturile mele</h1>

            <p>
                Creează, organizează și pregătește piesele
                pentru repetiții sau evenimente.
            </p>
        </div>
    </header>

    @if ($setlists->isEmpty())
        <div class="setlists-empty">
            <div class="setlists-empty-icon">
                ♪
            </div>

            <h2>Nu ai încă niciun setlist</h2>

            <p>
                Alege piesele, stabilește ordinea lor și
                salvează primul tău setlist.
            </p>

            <a
                class="button button-primary"
                href="{{ route('setlists.create') }}"
            >
                Creează primul setlist
            </a>
        </div>
    @else
        <div class="setlists-summary">
            <span>
                {{ $setlists->count() }}

                {{ $setlists->count() === 1
                    ? 'setlist'
                    : 'setlisturi'
                }}
            </span>

            @if ($setlists->contains('is_live', true))
                <span class="setlists-live-summary">
                    <i></i>
                    Ai un setlist live
                </span>
            @endif
        </div>

        <div class="setlists-grid">
            @foreach ($setlists as $setlist)
                <article
                    class="setlist-card
                        {{ $setlist->is_live
                            ? 'is-live'
                            : ''
                        }}"
                >
                    <div class="setlist-card-top">
                        @if ($setlist->is_live)
                            <span class="live-badge">
                                <i></i>
                                Live
                            </span>
                        @else
                            <span class="saved-badge">
                                Salvat
                            </span>
                        @endif

                        <span class="setlist-date">
                            Actualizat
                            {{ $setlist->updated_at->diffForHumans() }}
                        </span>
                    </div>

                    <div class="setlist-card-body">
                        <div class="setlist-card-content">
                            <h2>
                                <a
                                    href="{{ route('setlists.show', $setlist) }}"
                                >
                                    {{ $setlist->name }}
                                </a>
                            </h2>

                            @if ($setlist->description)
                                <p class="setlist-description">
                                    {{ Str::limit(
                                        $setlist->description,
                                        120
                                    ) }}
                                </p>
                            @else
                                <p class="setlist-description is-empty">
                                    Fără descriere
                                </p>
                            @endif

                            <div class="setlist-card-information">
                                <span>
                                    <strong>
                                        {{ $setlist->songs_count }}
                                    </strong>

                                    {{ $setlist->songs_count === 1
                                        ? 'piesă'
                                        : 'piese'
                                    }}
                                </span>
                            </div>
                        </div>

                        <div class="setlist-card-actions">
                            <a
                                class="setlist-open-button"
                                href="{{ route('setlists.show', $setlist) }}"
                            >
                                Deschide setlistul
                                <span>→</span>
                            </a>

                            <div class="setlist-secondary-actions">
                                <a
                                    class="setlist-edit-button"
                                    href="{{ route('setlists.edit', $setlist) }}"
                                >
                                    Editează
                                </a>

                                <form
                                    class="setlist-delete-form"
                                    action="{{ route('setlists.destroy', $setlist) }}"
                                    method="POST"
                                    onsubmit="return confirm('Sigur vrei să ștergi acest setlist? Acțiunea nu poate fi anulată.')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="setlist-delete-button"
                                        aria-label="Șterge setlistul {{ $setlist->name }}"
                                        title="Șterge setlistul"
                                    >
                                        Șterge
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

<style>
    .setlists-page {
        width: min(1180px, 100%);
        margin: 0 auto;
        font-family: "DM Sans", sans-serif;
    }

    .setlists-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 30px;
        padding: 28px 32px;
        border-radius: 17px;
        background:
            linear-gradient(
                135deg,
                #07192f 0%,
                #0b2c55 62%,
                #15538c 100%
            );
        box-shadow:
            0 18px 45px rgba(7, 25, 47, 0.17);
        color: #ffffff;
    }

    .setlists-eyebrow {
        margin: 0 0 8px;
        color: #83c8f7;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .setlists-header h1 {
        margin: 0;
        font-size: clamp(2rem, 4vw, 2.9rem);
        line-height: 1.05;
        letter-spacing: -0.05em;
    }

    .setlists-header > div > p:last-child {
        max-width: 580px;
        margin: 10px 0 0;
        color: #c7d9ea;
        font-size: 0.88rem;
    }

    .setlists-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin: 20px 0 12px;
        color: #687789;
        font-size: 0.83rem;
        font-weight: 650;
    }

    .setlists-live-summary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #166534;
    }

    .setlists-live-summary i,
    .live-badge i {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow:
            0 0 0 4px rgba(34, 197, 94, 0.13);
    }

    .setlists-grid {
        display: grid;
        gap: 12px;
    }

    .setlist-card {
        padding: 17px;
        border: 1px solid #cbd8e4;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow:
            0 7px 24px rgba(11, 44, 85, 0.06);
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease,
            transform 0.2s ease;
    }

    .setlist-card:hover {
        transform: translateY(-2px);
        border-color: #8eb4d5;
        box-shadow:
            0 14px 34px rgba(11, 44, 85, 0.11);
    }

    .setlist-card.is-live {
        border-color: #7bc898;
        box-shadow:
            0 9px 28px rgba(22, 101, 52, 0.09);
    }

    .setlist-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .live-badge,
    .saved-badge {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        gap: 7px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.64rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .live-badge {
        background: #e9f8ef;
        color: #166534;
    }

    .saved-badge {
        background: #e8eef5;
        color: #506176;
    }

    .setlist-date {
        color: #8491a0;
        font-size: 0.67rem;
    }

    .setlist-card-body {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 220px;
        align-items: stretch;
        gap: 28px;
        margin-top: 14px;
    }

    .setlist-card-content {
        display: flex;
        min-width: 0;
        flex-direction: column;
    }

    .setlist-card h2 {
        margin: 0;
        color: #07192f;
        font-size: 1.3rem;
        line-height: 1.2;
        letter-spacing: -0.03em;
    }

    .setlist-card h2 a {
        color: inherit;
        text-decoration: none;
    }

    .setlist-card h2 a:hover {
        color: #1768a8;
    }

    .setlist-description {
        min-height: 36px;
        margin: 7px 0 0;
        color: #687789;
        font-size: 0.77rem;
        line-height: 1.5;
    }

    .setlist-description.is-empty {
        color: #9aa5b1;
        font-style: italic;
    }

    .setlist-card-information {
        margin-top: auto;
        padding: 9px 0 0;
        border-top: 1px solid #e0e6ed;
        color: #687789;
        font-size: 0.8rem;
    }

    .setlist-card-information strong {
        color: #0b2c55;
        font-size: 1rem;
    }

    .setlist-card-actions {
        display: flex;
        width: 220px;
        flex-direction: column;
        justify-content: center;
        gap: 9px;
    }

    .setlist-open-button,
    .setlist-edit-button,
    .setlist-delete-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-family: inherit;
        font-size: 0.74rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition:
            border-color 0.2s ease,
            background 0.2s ease,
            color 0.2s ease,
            transform 0.2s ease;
    }

    .setlist-open-button:hover,
    .setlist-edit-button:hover,
    .setlist-delete-button:hover {
        transform: translateY(-1px);
    }

    .setlist-open-button {
        width: 100%;
        min-height: 47px;
        justify-content: space-between;
        padding: 10px 15px;
        border: 1px solid #0b2c55;
        background: #0b2c55;
        color: #ffffff;
        font-size: 0.78rem;
    }

    .setlist-open-button:hover {
        border-color: #1768a8;
        background: #1768a8;
        color: #ffffff;
    }

    .setlist-open-button span {
        font-size: 1rem;
        transition: transform 0.2s ease;
    }

    .setlist-open-button:hover span {
        transform: translateX(3px);
    }

    .setlist-secondary-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 9px;
    }

    .setlist-edit-button,
    .setlist-delete-button {
        width: 100%;
        min-height: 37px;
        padding: 7px 10px;
    }

    .setlist-edit-button {
        border: 1px solid #d1dbe5;
        background: #edf2f7;
        color: #34465a;
    }

    .setlist-edit-button:hover {
        border-color: #b8c9d9;
        background: #dfeaf4;
        color: #0b2c55;
    }

    .setlist-delete-form {
        width: 100%;
        margin: 0;
    }

    .setlist-delete-button {
        border: 1px solid #fecdd3;
        background: #fff1f2;
        color: #b4233c;
    }

    .setlist-delete-button:hover {
        border-color: #fda4af;
        background: #ffe4e7;
        color: #8f1830;
    }

    .setlists-empty {
        margin-top: 22px;
        padding: 48px 26px;
        border: 1px dashed #aebfd0;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.85);
        text-align: center;
    }

    .setlists-empty-icon {
        display: grid;
        width: 60px;
        height: 60px;
        margin: 0 auto;
        place-items: center;
        border-radius: 16px;
        background: #e0edf9;
        color: #155d9c;
        font-size: 1.8rem;
    }

    .setlists-empty h2 {
        margin: 18px 0 6px;
        color: #07192f;
        font-size: 1.45rem;
    }

    .setlists-empty p {
        max-width: 480px;
        margin: 0 auto 22px;
        color: #718096;
        font-size: 0.88rem;
    }

    .setlists-empty .button {
        display: inline-flex;
        min-height: 45px;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 10px;
        text-decoration: none;
    }

    .setlists-empty .button-primary {
        background: #0b2c55;
        color: #ffffff;
    }

    @media (max-width: 700px) {
        .setlists-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 25px 21px;
        }

        .setlists-summary {
            align-items: flex-start;
            flex-direction: column;
        }

        .setlist-card-body {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .setlist-card-actions {
            width: 100%;
        }

        .setlist-open-button {
            width: 100%;
        }

        .setlist-secondary-actions {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 430px) {
        .setlist-card-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .setlist-secondary-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection