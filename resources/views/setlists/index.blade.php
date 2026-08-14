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
                        <div class="setlist-card-labels">
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

                            @if ($setlist->team)
                                <span class="team-badge">
                                    {{ $setlist->team->name }}
                                </span>
                            @endif
                        </div>

                        <span class="setlist-date">
                            Actualizat
                            {{ $setlist->updated_at->diffForHumans() }}
                        </span>
                    </div>

                    <div class="setlist-card-body">
                        <div class="setlist-card-content">
                            <h2>
                                <a
                                    href="{{ route(
                                        'setlists.show',
                                        $setlist
                                    ) }}"
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

                                @if ($setlist->team)
                                    <span>
                                        Echipa:
                                        <strong>
                                            {{ $setlist->team->name }}
                                        </strong>
                                    </span>
                                @else
                                    <span class="personal-setlist-label">
                                        Setlist personal
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="setlist-card-actions">
                            <div class="setlist-primary-actions">
                                <a
                                    class="setlist-open-button"
                                    href="{{ route(
                                        'setlists.show',
                                        $setlist
                                    ) }}"
                                >
                                    Deschide setlistul
                                    <span>→</span>
                                </a>
                            </div>

                            <div class="setlist-secondary-actions">
                                <button
                                    type="button"
                                    class="setlist-team-button"
                                    data-team-modal-button
                                    data-setlist-name="{{ $setlist->name }}"
                                    data-team-id="{{ $setlist->team_id ?? '' }}"
                                    data-action="{{ route(
                                        'setlists.team',
                                        $setlist
                                    ) }}"
                                    title="{{ $setlist->team
                                        ? 'Schimbă echipa'
                                        : 'Asociază unei echipe'
                                    }}"
                                >
                                    Echipă
                                </button>

                                <a
                                    class="setlist-edit-button"
                                    href="{{ route(
                                        'setlists.edit',
                                        $setlist
                                    ) }}"
                                >
                                    Editează
                                </a>

                                <form
                                    class="setlist-delete-form"
                                    action="{{ route(
                                        'setlists.destroy',
                                        $setlist
                                    ) }}"
                                    method="POST"
                                    onsubmit="return confirm(
                                        'Sigur vrei să ștergi acest setlist?'
                                    )"
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

@if ($setlists->isNotEmpty())
    <div
        class="team-assignment-modal"
        id="team-assignment-modal"
        hidden
    >
        <div
            class="team-assignment-backdrop"
            id="team-assignment-backdrop"
        ></div>

        <div
            class="team-assignment-card"
            role="dialog"
            aria-modal="true"
            aria-labelledby="team-assignment-title"
        >
            <button
                type="button"
                class="team-assignment-close"
                id="team-assignment-close"
                aria-label="Închide"
            >
                ×
            </button>

            <p class="team-assignment-eyebrow">
                Organizare
            </p>

            <h2 id="team-assignment-title">
                Asociază unei echipe
            </h2>

            <p
                class="team-assignment-description"
                id="team-assignment-description"
            ></p>

            @if ($teams->isNotEmpty())
                <form
                    method="POST"
                    id="team-assignment-form"
                    class="team-assignment-form"
                >
                    @csrf
                    @method('PATCH')

                    <label for="assignment-team-id">
                        Echipa
                    </label>

                    <select
                        id="assignment-team-id"
                        name="team_id"
                    >
                        <option value="">
                            Fără echipă
                        </option>

                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>

                    <p class="team-assignment-help">
                        Setlistul va apărea în pagina echipei
                        selectate.
                    </p>

                    <button
                        type="submit"
                        class="team-assignment-save"
                    >
                        Salvează asocierea
                    </button>
                </form>
            @else
                <div class="team-assignment-empty">
                    <p>
                        Nu faci parte încă din nicio echipă.
                        Creează o echipă sau înscrie-te folosind
                        un cod.
                    </p>

                    <a href="{{ route('teams.index') }}">
                        Mergi la echipe
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif

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

    .setlist-card-labels {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .live-badge,
    .saved-badge,
    .team-badge {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        gap: 7px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.64rem;
        font-weight: 800;
        letter-spacing: 0.06em;
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

    .team-badge {
        background: #e3f1fb;
        color: #17628f;
    }

    .setlist-date {
        color: #8491a0;
        font-size: 0.67rem;
    }

    .setlist-card-body {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            310px;
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
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: auto;
        padding: 9px 0 0;
        border-top: 1px solid #e0e6ed;
        color: #687789;
        font-size: 0.8rem;
    }

    .setlist-card-information strong {
        color: #0b2c55;
        font-size: 0.9rem;
    }

    .personal-setlist-label {
        color: #8996a4;
        font-style: italic;
    }

    .setlist-card-actions {
        display: flex;
        width: 310px;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
    }

    .setlist-primary-actions {
        display: grid;
    }

    .setlist-secondary-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 7px;
    }

    .setlist-open-button,
    .setlist-edit-button,
    .setlist-delete-button,
    .setlist-team-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-family: inherit;
        font-size: 0.7rem;
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
    .setlist-delete-button:hover,
    .setlist-team-button:hover {
        transform: translateY(-1px);
    }

    .setlist-open-button {
        width: 100%;
        min-height: 45px;
        justify-content: space-between;
        padding: 9px 14px;
        border: 1px solid #0b2c55;
        background: #0b2c55;
        color: #ffffff;
        font-size: 0.76rem;
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

    .setlist-team-button,
    .setlist-edit-button,
    .setlist-delete-button {
        width: 100%;
        min-height: 35px;
        padding: 6px 7px;
    }

    .setlist-team-button {
        border: 1px solid #b9d9c5;
        background: #eef8f2;
        color: #246443;
    }

    .setlist-team-button:hover {
        border-color: #78bc92;
        background: #dcf4e6;
        color: #105332;
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

    .team-assignment-modal[hidden] {
        display: none;
    }

    .team-assignment-modal {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: grid;
        place-items: center;
        padding: 20px;
    }

    .team-assignment-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(3, 18, 32, 0.72);
        backdrop-filter: blur(6px);
    }

    .team-assignment-card {
        position: relative;
        z-index: 1;
        width: min(450px, 100%);
        padding: 32px;
        border: 1px solid rgba(8, 44, 77, 0.12);
        border-radius: 22px;
        background: #ffffff;
        box-shadow:
            0 28px 80px rgba(2, 20, 37, 0.3);
    }

    .team-assignment-close {
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

    .team-assignment-eyebrow {
        margin: 0 0 7px;
        color: #177146;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .team-assignment-card h2 {
        margin: 0;
        color: #071f37;
        font-size: 1.8rem;
        letter-spacing: -0.04em;
    }

    .team-assignment-description {
        margin: 10px 0 22px;
        color: #758497;
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .team-assignment-form {
        display: grid;
        gap: 11px;
    }

    .team-assignment-form label {
        color: #183650;
        font-size: 0.77rem;
        font-weight: 900;
    }

    .team-assignment-form select {
        width: 100%;
        min-height: 51px;
        padding: 0 14px;
        border: 1px solid #c9d7e3;
        border-radius: 11px;
        outline: none;
        background: #f7fafc;
        color: #09243d;
        font: inherit;
        cursor: pointer;
    }

    .team-assignment-form select:focus {
        border-color: #2679b0;
        box-shadow:
            0 0 0 4px rgba(38, 121, 176, 0.12);
    }

    .team-assignment-help {
        margin: 0;
        color: #82909f;
        font-size: 0.72rem;
        line-height: 1.5;
    }

    .team-assignment-save {
        min-height: 51px;
        border: 0;
        border-radius: 11px;
        background: #0a3b65;
        color: #ffffff;
        font: inherit;
        font-weight: 900;
        cursor: pointer;
    }

    .team-assignment-save:hover {
        background: #115381;
    }

    .team-assignment-empty p {
        margin: 0;
        color: #718195;
        line-height: 1.6;
    }

    .team-assignment-empty a {
        display: inline-flex;
        margin-top: 14px;
        color: #17699f;
        font-weight: 900;
    }

    body.team-assignment-open {
        overflow: hidden;
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
    }

    @media (max-width: 430px) {
        .setlist-card-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .setlist-secondary-actions {
            gap: 5px;
        }

        .setlist-team-button,
        .setlist-edit-button,
        .setlist-delete-button {
            padding: 6px 4px;
            font-size: 0.64rem;
        }
    }
</style>

@if ($setlists->isNotEmpty())
    <script>
        const teamAssignmentModal =
            document.getElementById(
                'team-assignment-modal'
            );

        const teamAssignmentForm =
            document.getElementById(
                'team-assignment-form'
            );

        const teamAssignmentSelect =
            document.getElementById(
                'assignment-team-id'
            );

        const teamAssignmentDescription =
            document.getElementById(
                'team-assignment-description'
            );

        function openTeamAssignmentModal(
            button
        ) {
            const setlistName =
                button.dataset.setlistName;

            teamAssignmentDescription.textContent =
                `Alege echipa pentru setlistul „${setlistName}”.`;

            if (teamAssignmentForm) {
                teamAssignmentForm.action =
                    button.dataset.action;

                teamAssignmentSelect.value =
                    button.dataset.teamId;
            }

            teamAssignmentModal.hidden = false;

            document.body.classList.add(
                'team-assignment-open'
            );

            teamAssignmentSelect?.focus();
        }

        function closeTeamAssignmentModal() {
            teamAssignmentModal.hidden = true;

            document.body.classList.remove(
                'team-assignment-open'
            );
        }

        document
            .querySelectorAll(
                '[data-team-modal-button]'
            )
            .forEach(function (button) {
                button.addEventListener(
                    'click',
                    function () {
                        openTeamAssignmentModal(
                            button
                        );
                    }
                );
            });

        document
            .getElementById(
                'team-assignment-close'
            )
            ?.addEventListener(
                'click',
                closeTeamAssignmentModal
            );

        document
            .getElementById(
                'team-assignment-backdrop'
            )
            ?.addEventListener(
                'click',
                closeTeamAssignmentModal
            );

        document.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key === 'Escape' &&
                    !teamAssignmentModal.hidden
                ) {
                    closeTeamAssignmentModal();
                }
            }
        );
    </script>
@endif
@endsection