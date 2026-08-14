@extends('layouts.app')

@section('title', 'Echipele mele')

@section('content')
<section class="teams-page">
    <header class="teams-heading">
        <div>
            <p class="teams-eyebrow">
                Comunitatea ta
            </p>

            <h1>Echipele mele</h1>

            <p>
                Vezi echipele din care faci parte și
                setlisturile pregătite pentru fiecare.
            </p>
        </div>

        @if ($teams->isNotEmpty())
            <span class="teams-count">
                {{ $teams->count() }}

                {{ $teams->count() === 1
                    ? 'echipă'
                    : 'echipe'
                }}
            </span>
        @endif
    </header>

    @if ($errors->any())
        <div class="teams-errors">
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

    <section class="teams-list-section">
        @if ($teams->isEmpty())
            <div class="teams-empty-state">
                <span class="teams-empty-icon">
                    ✦
                </span>

                <h2>Nu faci parte încă dintr-o echipă</h2>

                <p>
                    Creează prima echipă sau introdu codul
                    primit de la un alt membru.
                </p>
            </div>
        @else
            <div class="teams-list">
                @foreach ($teams as $team)
                    <a
                        href="{{ route(
                            'teams.show',
                            $team
                        ) }}"
                        class="team-list-card"
                    >
                        <div class="team-list-icon">
                            {{ mb_strtoupper(
                                mb_substr(
                                    $team->name,
                                    0,
                                    1
                                )
                            ) }}
                        </div>

                        <div class="team-list-information">
                            <div class="team-list-title-row">
                                <h2>
                                    {{ $team->name }}
                                </h2>

                                @if (
                                    $team->owner_id ===
                                    auth()->id()
                                )
                                    <span class="team-owner-badge">
                                        Proprietar
                                    </span>
                                @else
                                    <span class="team-member-badge">
                                        Membru
                                    </span>
                                @endif
                            </div>

                            <p>
                                Creată de
                                <strong>
                                    {{ $team->owner->name }}
                                </strong>
                            </p>

                            <div class="team-list-meta">
                                <span>
                                    {{ $team->users_count }}

                                    {{ $team->users_count === 1
                                        ? 'membru'
                                        : 'membri'
                                    }}
                                </span>

                                <span>
                                    {{ $team->setlists_count }}

                                    {{ $team->setlists_count === 1
                                        ? 'setlist'
                                        : 'setlisturi'
                                    }}
                                </span>
                            </div>
                        </div>

                        <span class="team-list-arrow">
                            →
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="teams-actions-section">
        <div>
            <p>Administrare</p>

            <h2>
                Gestionează echipele
            </h2>
        </div>

        <div class="teams-actions">
            <button
                type="button"
                class="team-action-button team-create-button"
                data-open-team-modal="create"
            >
                <span>＋</span>
                Creează echipă
            </button>

            <button
                type="button"
                class="team-action-button team-join-button"
                data-open-team-modal="join"
            >
                <span>→</span>
                Înscrie-te într-o echipă
            </button>
        </div>
    </section>
</section>

<div
    class="team-form-modal"
    id="team-form-modal"
    hidden
>
    <div
        class="team-form-backdrop"
        id="team-form-backdrop"
    ></div>

    <div
        class="team-form-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="team-form-title"
    >
        <button
            type="button"
            class="team-form-close"
            id="team-form-close"
            aria-label="Închide"
        >
            ×
        </button>

        <div
            class="team-form-panel"
            id="create-team-panel"
            hidden
        >
            <p class="team-form-eyebrow">
                Echipă nouă
            </p>

            <h2 id="team-form-title">
                Creează o echipă
            </h2>

            <p class="team-form-description">
                Vei deveni proprietarul echipei și vei primi
                un cod pe care îl poți trimite membrilor.
            </p>

            <form
                action="{{ route('teams.store') }}"
                method="POST"
                class="team-modal-form"
            >
                @csrf

                <label for="team-name">
                    Numele echipei
                </label>

                <input
                    type="text"
                    id="team-name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Exemplu: Casa Pâinii"
                    maxlength="255"
                    required
                >

                <button type="submit">
                    Creează echipa
                </button>
            </form>
        </div>

        <div
            class="team-form-panel"
            id="join-team-panel"
            hidden
        >
            <p class="team-form-eyebrow">
                Invitație
            </p>

            <h2>
                Intră într-o echipă
            </h2>

            <p class="team-form-description">
                Introdu codul primit de la proprietarul
                echipei.
            </p>

            <form
                action="{{ route('teams.join') }}"
                method="POST"
                class="team-modal-form"
            >
                @csrf

                <label for="join-code">
                    Codul echipei
                </label>

                <input
                    type="text"
                    id="join-code"
                    name="join_code"
                    value="{{ old('join_code') }}"
                    placeholder="Exemplu: A7KD92BX"
                    maxlength="30"
                    autocomplete="off"
                    required
                >

                <button type="submit">
                    Înscrie-mă în echipă
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .teams-page {
        display: grid;
        gap: 25px;
        padding: 20px 0 55px;
    }

    .teams-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 25px;
        padding: clamp(29px, 5vw, 52px);
        border-radius: 25px;
        background:
            radial-gradient(
                circle at 90% 10%,
                rgba(79, 169, 226, 0.26),
                transparent 35%
            ),
            linear-gradient(
                140deg,
                #061b31,
                #0d4475
            );
        color: #ffffff;
        box-shadow:
            0 22px 60px rgba(4, 30, 53, 0.17);
    }

    .teams-eyebrow {
        margin: 0 0 9px;
        color: #91d4ff;
        font-size: 0.73rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
    }

    .teams-heading h1 {
        margin: 0;
        color: #ffffff;
        font-size: clamp(2.5rem, 7vw, 5.2rem);
        line-height: 0.93;
        letter-spacing: -0.06em;
    }

    .teams-heading > div > p:last-child {
        max-width: 620px;
        margin: 16px 0 0;
        color: #c6d9ea;
        line-height: 1.65;
    }

    .teams-count {
        flex-shrink: 0;
        padding: 11px 14px;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        font-size: 0.76rem;
        font-weight: 900;
    }

    .teams-errors {
        padding: 14px 17px;
        border: 1px solid #f2bdc6;
        border-left: 4px solid #bc2944;
        border-radius: 10px;
        background: #fff1f3;
        color: #8f1e34;
        font-size: 0.83rem;
    }

    .teams-errors ul {
        margin: 7px 0 0;
        padding-left: 20px;
    }

    .teams-list {
        display: grid;
        gap: 12px;
    }

    .team-list-card {
        display: grid;
        grid-template-columns:
            auto
            minmax(0, 1fr)
            auto;
        align-items: center;
        gap: 18px;
        padding: 19px;
        border: 1px solid rgba(7, 42, 72, 0.12);
        border-radius: 17px;
        background: rgba(255, 255, 255, 0.9);
        color: inherit;
        text-decoration: none;
        box-shadow:
            0 10px 32px rgba(6, 36, 61, 0.06);
        transition:
            transform 0.2s ease,
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .team-list-card:hover {
        transform: translateY(-2px);
        border-color: #82b3d6;
        box-shadow:
            0 16px 38px rgba(6, 36, 61, 0.1);
    }

    .team-list-icon {
        display: grid;
        width: 51px;
        height: 51px;
        place-items: center;
        border-radius: 14px;
        background:
            linear-gradient(
                145deg,
                #071f37,
                #125487
            );
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 950;
    }

    .team-list-title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
    }

    .team-list-title-row h2 {
        margin: 0;
        color: #071f37;
        font-size: 1.13rem;
    }

    .team-list-information > p {
        margin: 5px 0 0;
        color: #7b8998;
        font-size: 0.82rem;
    }

    .team-owner-badge,
    .team-member-badge {
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 0.6rem;
        font-weight: 900;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .team-owner-badge {
        background: #dcf6e7;
        color: #147044;
    }

    .team-member-badge {
        background: #e1effa;
        color: #17679d;
    }

    .team-list-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 11px;
        color: #51677b;
        font-size: 0.73rem;
        font-weight: 850;
    }

    .team-list-arrow {
        color: #1476b7;
        font-size: 1.35rem;
        font-weight: 900;
    }

    .teams-empty-state {
        display: grid;
        min-height: 280px;
        place-items: center;
        align-content: center;
        gap: 8px;
        padding: 35px;
        border: 1px dashed #b6c9d9;
        border-radius: 20px;
        background: rgba(247, 250, 252, 0.78);
        text-align: center;
    }

    .teams-empty-icon {
        color: #248bc9;
        font-size: 1.8rem;
    }

    .teams-empty-state h2,
    .teams-empty-state p {
        margin: 0;
    }

    .teams-empty-state h2 {
        color: #071f37;
    }

    .teams-empty-state p {
        max-width: 490px;
        color: #758597;
        line-height: 1.6;
    }

    .teams-actions-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding-top: 22px;
        border-top: 1px solid #d5e0e9;
    }

    .teams-actions-section p,
    .teams-actions-section h2 {
        margin: 0;
    }

    .teams-actions-section p {
        margin-bottom: 4px;
        color: #1871ac;
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .teams-actions-section h2 {
        color: #09233d;
        font-size: 1.2rem;
    }

    .teams-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
    }

    .team-action-button {
        display: inline-flex;
        min-height: 43px;
        align-items: center;
        gap: 8px;
        padding: 0 15px;
        border-radius: 10px;
        font: inherit;
        font-size: 0.75rem;
        font-weight: 900;
        cursor: pointer;
        transition:
            transform 0.2s ease,
            border-color 0.2s ease,
            background 0.2s ease;
    }

    .team-action-button:hover {
        transform: translateY(-1px);
    }

    .team-create-button {
        border: 1px solid #0a3a63;
        background: #0a3a63;
        color: #ffffff;
    }

    .team-create-button:hover {
        background: #125786;
    }

    .team-join-button {
        border: 1px solid #b8cbd9;
        background: #ffffff;
        color: #153c5d;
    }

    .team-join-button:hover {
        border-color: #82aac6;
        background: #f1f7fb;
    }

    .team-form-modal[hidden] {
        display: none;
    }

    .team-form-modal {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: grid;
        place-items: center;
        padding: 20px;
    }

    .team-form-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(2, 16, 29, 0.73);
        backdrop-filter: blur(6px);
    }

    .team-form-card {
        position: relative;
        z-index: 1;
        width: min(460px, 100%);
        padding: 32px;
        border: 1px solid rgba(8, 44, 77, 0.12);
        border-radius: 22px;
        background: #ffffff;
        box-shadow:
            0 28px 80px rgba(2, 20, 37, 0.32);
    }

    .team-form-close {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 9px;
        background: #edf3f7;
        color: #082743;
        font-size: 1.35rem;
        cursor: pointer;
    }

    .team-form-eyebrow {
        margin: 0 0 7px;
        color: #197349;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .team-form-panel h2 {
        margin: 0;
        color: #071f37;
        font-size: 1.9rem;
        letter-spacing: -0.045em;
    }

    .team-form-description {
        margin: 11px 0 23px;
        color: #748496;
        font-size: 0.86rem;
        line-height: 1.6;
    }

    .team-modal-form {
        display: grid;
        gap: 11px;
    }

    .team-modal-form label {
        color: #183650;
        font-size: 0.77rem;
        font-weight: 900;
    }

    .team-modal-form input {
        width: 100%;
        min-height: 51px;
        padding: 0 14px;
        border: 1px solid #c8d6e2;
        border-radius: 11px;
        outline: none;
        background: #f7fafc;
        color: #09243d;
        font: inherit;
        box-sizing: border-box;
    }

    .team-modal-form input:focus {
        border-color: #2679b0;
        box-shadow:
            0 0 0 4px rgba(38, 121, 176, 0.12);
    }

    .team-modal-form button {
        min-height: 51px;
        border: 0;
        border-radius: 11px;
        background: #0a3b65;
        color: #ffffff;
        font: inherit;
        font-weight: 900;
        cursor: pointer;
    }

    body.team-form-open {
        overflow: hidden;
    }

    @media (max-width: 680px) {
        .teams-heading,
        .teams-actions-section {
            align-items: flex-start;
            flex-direction: column;
        }

        .teams-actions {
            width: 100%;
        }

        .team-action-button {
            flex: 1;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .team-list-card {
            grid-template-columns:
                auto
                minmax(0, 1fr);
        }

        .team-list-arrow {
            display: none;
        }

        .teams-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .team-action-button {
            width: 100%;
        }

        .team-form-card {
            padding: 28px 22px;
        }
    }
</style>

<script>
    const teamFormModal =
        document.getElementById(
            'team-form-modal'
        );

    const createTeamPanel =
        document.getElementById(
            'create-team-panel'
        );

    const joinTeamPanel =
        document.getElementById(
            'join-team-panel'
        );

    function openTeamFormModal(type) {
        createTeamPanel.hidden =
            type !== 'create';

        joinTeamPanel.hidden =
            type !== 'join';

        teamFormModal.hidden = false;

        document.body.classList.add(
            'team-form-open'
        );

        if (type === 'create') {
            document
                .getElementById('team-name')
                ?.focus();
        } else {
            document
                .getElementById('join-code')
                ?.focus();
        }
    }

    function closeTeamFormModal() {
        teamFormModal.hidden = true;

        document.body.classList.remove(
            'team-form-open'
        );
    }

    document
        .querySelectorAll(
            '[data-open-team-modal]'
        )
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    openTeamFormModal(
                        button.dataset.openTeamModal
                    );
                }
            );
        });

    document
        .getElementById('team-form-close')
        ?.addEventListener(
            'click',
            closeTeamFormModal
        );

    document
        .getElementById('team-form-backdrop')
        ?.addEventListener(
            'click',
            closeTeamFormModal
        );

    document.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Escape' &&
                !teamFormModal.hidden
            ) {
                closeTeamFormModal();
            }
        }
    );

    @if ($errors->has('join_code'))
        openTeamFormModal('join');
    @elseif ($errors->has('name'))
        openTeamFormModal('create');
    @endif
</script>
@endsection