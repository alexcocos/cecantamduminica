@extends('layouts.app')

@section('title', $team->name)

@section('content')
    @php
        $isOwner = $team->owner_id === auth()->id();

        $liveSetlist = $team->setlists
            ->firstWhere('is_live', true);
    @endphp

    <section class="team-page">
        <header class="team-hero">
            <a
                href="{{ route('teams.index') }}"
                class="team-back-link"
            >
                ← Înapoi la echipe
            </a>

            <div class="team-hero-content">
                <div>
                    <p class="team-hero-eyebrow">
                        {{ $isOwner ? 'Echipa ta' : 'Membru al echipei' }}
                    </p>

                    <h1>{{ $team->name }}</h1>

                    <p class="team-owner-line">
                        Creată de {{ $team->owner->name }}
                    </p>
                </div>

                <div class="team-hero-statistics">
                    <div>
                        <strong>{{ $team->users->count() }}</strong>
                        <span>Membri</span>
                    </div>

                    <div>
                        <strong>{{ $team->setlists->count() }}</strong>
                        <span>Setlisturi</span>
                    </div>
                </div>
            </div>

            @if ($isOwner)
                <div class="team-code-box">
                    <div>
                        <span>Cod de înscriere</span>

                        <strong id="team-join-code">
                            {{ $team->join_code }}
                        </strong>
                    </div>

                    <button
                        type="button"
                        id="copy-team-code"
                        class="copy-team-code"
                    >
                        Copiază codul
                    </button>
                </div>
            @endif
        </header>

        @if ($liveSetlist)
            <a
                href="{{ route('setlists.show', $liveSetlist) }}"
                class="team-live-setlist"
            >
                <div class="team-live-indicator">
                    <span></span>
                    Live acum
                </div>

                <div>
                    <p>Setlistul live al echipei</p>

                    <h2>{{ $liveSetlist->name }}</h2>

                    <span>
                        {{ $liveSetlist->songs_count }}
                        {{ $liveSetlist->songs_count === 1
                            ? 'piesă'
                            : 'piese' }}
                    </span>
                </div>

                <strong class="team-live-arrow">→</strong>
            </a>
        @endif

        <div class="team-content-grid">
            <section class="team-setlists-section">
                <div class="team-section-heading">
                    <div>
                        <p>Planificare</p>
                        <h2>Setlisturile echipei</h2>
                    </div>

                    <a
                        href="{{ route('setlists.create', [
                            'team' => $team->id,
                        ]) }}"
                        class="team-create-setlist"
                    >
                        Creează setlist
                    </a>
                </div>

                @if ($team->setlists->isEmpty())
                    <div class="team-empty-setlists">
                        <span>♪</span>

                        <h3>Niciun setlist momentan</h3>

                        <p>
                            Creează primul setlist pentru această
                            echipă și adaugă piesele dorite.
                        </p>
                    </div>
                @else
                    <div class="team-setlists-list">
                        @foreach ($team->setlists as $setlist)
                            <a
                                href="{{ route(
                                    'setlists.show',
                                    $setlist
                                ) }}"
                                class="team-setlist-card"
                            >
                                <div>
                                    <div class="team-setlist-labels">
                                        @if ($setlist->is_live)
                                            <span class="setlist-live-badge">
                                                Live
                                            </span>
                                        @else
                                            <span class="setlist-saved-badge">
                                                Salvat
                                            </span>
                                        @endif
                                    </div>

                                    <h3>{{ $setlist->name }}</h3>

                                    <p>
                                        {{ $setlist->songs_count }}
                                        {{ $setlist->songs_count === 1
                                            ? 'piesă'
                                            : 'piese' }}
                                    </p>
                                </div>

                                <span>→</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <aside class="team-members-section">
                <div class="team-section-heading">
                    <div>
                        <p>Comunitate</p>
                        <h2>Membri</h2>
                    </div>
                </div>

                <div class="team-members-list">
                    @foreach ($team->users as $member)
                        <div class="team-member-row">
                            <div class="team-member-avatar">
                                {{ mb_strtoupper(
                                    mb_substr(
                                        $member->name,
                                        0,
                                        1
                                    )
                                ) }}
                            </div>

                            <div>
                                <strong>{{ $member->name }}</strong>

                                <span>
                                    @if (
                                        $member->id ===
                                        $team->owner_id
                                    )
                                        Proprietar
                                    @else
                                        Membru
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

               @if ($isOwner)
    <form
        action="{{ route(
            'teams.destroy',
            $team
        ) }}"
        method="POST"
        onsubmit="return confirm(
            'Sigur vrei să ștergi această echipă? Setlisturile nu vor fi șterse.'
        )"
    >
        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="delete-team-button"
        >
            Șterge echipa
        </button>
    </form>
@else
    <form
        action="{{ route(
            'teams.leave',
            $team
        ) }}"
        method="POST"
        onsubmit="return confirm(
            'Sigur vrei să părăsești această echipă?'
        )"
    >
        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="leave-team-button"
        >
            Părăsește echipa
        </button>
    </form>
@endif
            </aside>
        </div>
    </section>

    <style>
        .team-page {
            display: grid;
            gap: 24px;
            padding: 20px 0 55px;
        }

        .team-hero {
            display: grid;
            gap: 30px;
            padding: clamp(28px, 5vw, 58px);
            border-radius: 28px;
            background:
                radial-gradient(
                    circle at 88% 10%,
                    rgba(66, 161, 222, 0.3),
                    transparent 34%
                ),
                linear-gradient(
                    140deg,
                    #061b31,
                    #0d4475
                );
            color: #ffffff;
            box-shadow: 0 24px 65px rgba(3, 29, 52, 0.18);
        }

        .team-back-link {
            width: fit-content;
            color: #99d6fb;
            font-size: 0.84rem;
            font-weight: 900;
            text-decoration: none;
        }

        .team-hero-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 30px;
        }

        .team-hero-eyebrow {
            margin: 0 0 10px;
            color: #91d4ff;
            font-size: 0.75rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        .team-hero h1 {
            max-width: 850px;
            margin: 0;
            color: #ffffff;
            font-size: clamp(2.5rem, 7vw, 6rem);
            line-height: 0.92;
            letter-spacing: -0.065em;
        }

        .team-owner-line {
            margin: 15px 0 0;
            color: rgba(255, 255, 255, 0.64);
            font-size: 0.9rem;
        }

        .team-hero-statistics {
            display: flex;
            gap: 10px;
        }

        .team-hero-statistics > div {
            display: grid;
            min-width: 92px;
            gap: 3px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.07);
        }

        .team-hero-statistics strong {
            font-size: 1.35rem;
        }

        .team-hero-statistics span {
            color: rgba(255, 255, 255, 0.63);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .team-code-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            background: rgba(2, 19, 35, 0.26);
        }

        .team-code-box > div {
            display: grid;
            gap: 4px;
        }

        .team-code-box span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .team-code-box strong {
            color: #ffffff;
            font-size: 1.35rem;
            letter-spacing: 0.12em;
        }

        .copy-team-code {
            min-height: 43px;
            padding: 0 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            font: inherit;
            font-size: 0.78rem;
            font-weight: 900;
            cursor: pointer;
        }

        .team-live-setlist {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 22px;
            padding: 24px;
            border: 1px solid rgba(39, 174, 99, 0.32);
            border-radius: 20px;
            background:
                linear-gradient(
                    135deg,
                    rgba(222, 249, 233, 0.96),
                    rgba(237, 252, 244, 0.92)
                );
            color: #0b452d;
            text-decoration: none;
            box-shadow: 0 14px 38px rgba(26, 119, 72, 0.1);
        }

        .team-live-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 999px;
            background: #ffffff;
            color: #16804b;
            font-size: 0.72rem;
            font-weight: 950;
            text-transform: uppercase;
        }

        .team-live-indicator > span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2ac875;
            box-shadow: 0 0 0 5px rgba(42, 200, 117, 0.15);
        }

        .team-live-setlist p,
        .team-live-setlist h2 {
            margin: 0;
        }

        .team-live-setlist p {
            margin-bottom: 5px;
            color: #4c7964;
            font-size: 0.74rem;
            font-weight: 850;
            text-transform: uppercase;
        }

        .team-live-setlist h2 {
            color: #0b452d;
            font-size: 1.35rem;
        }

        .team-live-setlist div > span {
            display: inline-block;
            margin-top: 5px;
            color: #668777;
            font-size: 0.78rem;
        }

        .team-live-arrow {
            font-size: 1.45rem;
        }

        .team-content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(260px, 0.7fr);
            gap: 20px;
        }

        .team-setlists-section,
        .team-members-section {
            display: grid;
            align-content: start;
            gap: 20px;
            padding: 24px;
            border: 1px solid rgba(7, 42, 72, 0.1);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.82);
            box-shadow: 0 14px 40px rgba(5, 35, 60, 0.06);
        }

        .team-section-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
        }

        .team-section-heading p,
        .team-section-heading h2 {
            margin: 0;
        }

        .team-section-heading p {
            margin-bottom: 5px;
            color: #1870aa;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .team-section-heading h2 {
            color: #071f37;
            font-size: 1.35rem;
        }

        .team-create-setlist {
            padding: 10px 13px;
            border-radius: 10px;
            background: #08355d;
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 900;
            text-decoration: none;
        }

        .team-setlists-list,
        .team-members-list {
            display: grid;
            gap: 10px;
        }

        .team-setlist-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            padding: 17px;
            border: 1px solid #dce7f0;
            border-radius: 14px;
            background: #f7fafc;
            color: inherit;
            text-decoration: none;
            transition:
                transform 0.2s ease,
                border-color 0.2s ease;
        }

        .team-setlist-card:hover {
            transform: translateY(-2px);
            border-color: #91bbd9;
        }

        .team-setlist-card h3,
        .team-setlist-card p {
            margin: 0;
        }

        .team-setlist-card h3 {
            margin-top: 7px;
            color: #071f37;
            font-size: 1rem;
        }

        .team-setlist-card p {
            margin-top: 4px;
            color: #8090a0;
            font-size: 0.76rem;
        }

        .team-setlist-labels {
            display: flex;
            gap: 6px;
        }

        .setlist-live-badge,
        .setlist-saved-badge {
            padding: 5px 7px;
            border-radius: 999px;
            font-size: 0.59rem;
            font-weight: 950;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .setlist-live-badge {
            background: #d8f6e5;
            color: #147347;
        }

        .setlist-saved-badge {
            background: #e5edf4;
            color: #617286;
        }

        .team-member-row {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid #e3ebf1;
        }

        .team-member-row:last-child {
            border-bottom: 0;
        }

        .team-member-avatar {
            display: grid;
            width: 39px;
            height: 39px;
            place-items: center;
            border-radius: 11px;
            background: #e3f0fa;
            color: #0e679f;
            font-weight: 950;
        }

        .team-member-row > div:last-child {
            display: grid;
            gap: 3px;
        }

        .team-member-row strong {
            color: #0a243c;
            font-size: 0.86rem;
        }

        .team-member-row span {
            color: #8794a1;
            font-size: 0.69rem;
            font-weight: 800;
        }

        .leave-team-button {
            width: 100%;
            min-height: 43px;
            border: 1px solid #f0b9c3;
            border-radius: 10px;
            background: #fff4f6;
            color: #a5253c;
            font: inherit;
            font-size: 0.76rem;
            font-weight: 900;
            cursor: pointer;
        }
.delete-team-button {
    width: 100%;
    min-height: 43px;
    border: 1px solid #efb5c0;
    border-radius: 10px;
    background: #fff1f3;
    color: #a51f39;
    font: inherit;
    font-size: 0.76rem;
    font-weight: 900;
    cursor: pointer;
}

.delete-team-button:hover {
    border-color: #e68da0;
    background: #ffe3e8;
    color: #84162b;
}
        .team-empty-setlists {
            display: grid;
            min-height: 210px;
            place-items: center;
            align-content: center;
            gap: 7px;
            border: 1px dashed #bed0df;
            border-radius: 16px;
            background: #f8fafc;
            text-align: center;
        }

        .team-empty-setlists > span {
            color: #2086c5;
            font-size: 1.7rem;
        }

        .team-empty-setlists h3,
        .team-empty-setlists p {
            margin: 0;
        }

        .team-empty-setlists h3 {
            color: #09223a;
        }

        .team-empty-setlists p {
            max-width: 430px;
            color: #8090a0;
            font-size: 0.84rem;
            line-height: 1.55;
        }

        @media (max-width: 880px) {
            .team-content-grid {
                grid-template-columns: 1fr;
            }

            .team-hero-content {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 560px) {
            .team-code-box,
            .team-section-heading {
                align-items: stretch;
                flex-direction: column;
            }

            .team-live-setlist {
                grid-template-columns: 1fr;
            }

            .team-live-arrow {
                display: none;
            }
        }
    </style>

    @if ($isOwner)
        <script>
            const copyTeamCodeButton =
                document.getElementById('copy-team-code');

            copyTeamCodeButton?.addEventListener(
                'click',
                async () => {
                    const code = document
                        .getElementById('team-join-code')
                        .textContent
                        .trim();

                    await navigator.clipboard.writeText(code);

                    copyTeamCodeButton.textContent =
                        'Cod copiat';

                    setTimeout(() => {
                        copyTeamCodeButton.textContent =
                            'Copiază codul';
                    }, 1800);
                }
            );
        </script>
    @endif
@endsection