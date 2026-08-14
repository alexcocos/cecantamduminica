<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">

    <title>{{ $setlist->name }}</title>

    <style>
        @page {
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111827;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8pt;
            line-height: 1.25;
        }

        .setlist-cover {
            margin-bottom: 14px;
            padding: 14px 16px;
            border-left: 5px solid #1768a8;
            background: #eef4f9;
        }

        .cover-eyebrow {
            margin: 0 0 4px;
            color: #1768a8;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .setlist-cover h1 {
            margin: 0;
            color: #092847;
            font-size: 19pt;
            line-height: 1.05;
        }

        .cover-description {
            margin: 6px 0 0;
            color: #53687c;
            font-size: 7.5pt;
        }

        .cover-information {
            margin-top: 8px;
            color: #64778a;
            font-size: 6.5pt;
        }

        .cover-information span {
            margin-right: 12px;
        }

        .setlist-order {
            margin-bottom: 16px;
            padding: 10px 13px;
            border: 1px solid #d5dee7;
        }

        .setlist-order h2 {
            margin: 0 0 6px;
            color: #173a59;
            font-size: 9pt;
        }

        .setlist-order ol {
            margin: 0;
            padding-left: 18px;
            columns: 2;
            column-gap: 24px;
        }

        .setlist-order li {
            margin-bottom: 2px;
            color: #374b5f;
            font-size: 6.8pt;
        }

        .song {
            margin-bottom: 16px;
        }

        .song.start-new-page {
            page-break-before: always;
        }

        .song-header {
            margin-bottom: 6px;
            padding: 7px 10px;
            border-left: 4px solid #1768a8;
            background: #f0f5f9;
        }

        .song-number {
            margin: 0 0 2px;
            color: #1768a8;
            font-size: 5.8pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .song-header h2 {
            margin: 0;
            color: #081e34;
            font-size: 14pt;
            line-height: 1.05;
        }

        .song-meta {
            margin-top: 4px;
            color: #52677a;
            font-size: 6.7pt;
        }

        .song-meta span {
            margin-right: 11px;
        }

        .transpose-information {
            color: #1768a8;
            font-weight: bold;
        }

        /*
         * Folosim un tabel deoarece DomPDF îl redă
         * mai stabil decât grid sau flex.
         */

        .song-note {
    margin: 0 5px 5px;
    padding: 5px 7px;
    border-left: 3px solid #e0a526;
    background: #fff8e8;
    color: #594719;
    font-size: 6.8pt;
    line-height: 1.3;
}

.song-note strong {
    color: #8a6210;
    font-size: 5.8pt;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

        .sections-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            table-layout: fixed;
        }

        .sections-table td {
            width: 50%;
            padding: 0;
            vertical-align: top;
        }

        .section-card {
            padding: 7px 8px;
            border: 1px solid #d6e0e8;
            background: #ffffff;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .section-label {
            display: inline-block;
            margin-bottom: 5px;
            padding: 2px 5px;
            border: 1px solid #b9d5e9;
            background: #e5f1f9;
            color: #155f96;
            font-size: 5.6pt;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .song-line {
            display: block;
            width: 100%;
            margin: 0 0 4px;
            padding: 0;
            page-break-inside: avoid;
        }

        .song-line:last-child {
            margin-bottom: 0;
        }

        /*
         * Acordurile și versurile folosesc exact
         * același font monospace. Astfel, fiecare
         * caracter ocupă aceeași lățime.
         */
        .chord-lane {
            display: block;
            width: 100%;
            min-height: 8px;
            margin: 0;
            padding: 0;
            overflow: hidden;
            color: #1768a8;
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 6.8pt;
            font-weight: bold;
            line-height: 1;
            white-space: pre;
        }

        .lyric {
            display: block;
            width: 100%;
            min-height: 9px;
            margin: 0;
            padding: 0;
            overflow: hidden;
            color: #111827;
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 7.2pt;
            line-height: 1.15;
            white-space: pre;
        }

        .lyrics-only .lyric {
            margin-bottom: 2px;
        }

        .song-footer {
            margin-top: 7px;
            padding-top: 4px;
            border-top: 1px solid #d7e0e8;
            color: #8794a2;
            font-size: 5.5pt;
        }
    </style>
</head>

<body>
    @php
        $sectionNames = [
            'verse' => 'Pre-refren',
            'pre_chorus' => 'Pre-refren',
            'stanza' => 'Strofă',
            'chorus' => 'Refren',
            'bridge' => 'Bridge',
            'coda' => 'Coda',
        ];
    @endphp

    <header class="setlist-cover">
        <p class="cover-eyebrow">
            Ce cântăm duminică · Setlist
        </p>

        <h1>{{ $setlist->name }}</h1>

        @if ($setlist->description)
            <p class="cover-description">
                {{ $setlist->description }}
            </p>
        @endif

        <div class="cover-information">
            <span>
                {{ $exportSongs->count() }}

                {{ $exportSongs->count() === 1
                    ? 'piesă'
                    : 'piese'
                }}
            </span>

            <span>
                Creat de {{ $setlist->user->name }}
            </span>

            <span>
                Generat la {{ now()->format('d.m.Y H:i') }}
            </span>
        </div>
    </header>

    <section class="setlist-order">
        <h2>Ordinea pieselor</h2>

        <ol>
            @foreach ($exportSongs as $song)
                <li>
                    <strong>{{ $song['title'] }}</strong>

                    @if ($song['transpose_steps'] !== 0)
                        — {{ $song['display_key'] }}
                    @endif
                </li>
            @endforeach
        </ol>
    </section>

    @foreach ($exportSongs as $song)
        @php
            /*
             * Grupăm secțiunile câte două pentru
             * afișarea lor pe două coloane.
             */
            $sectionRows = collect(
                $song['sections']
            )->chunk(2);
        @endphp

        <article
            class="song
                {{ $options['one_song_per_page']
                    ? 'start-new-page'
                    : ''
                }}

                {{ !$options['include_chords']
                    ? 'lyrics-only'
                    : ''
                }}"
        >
            <header class="song-header">
                <p class="song-number">
                    Piesa {{ $loop->iteration }}
                </p>

                <h2>{{ $song['title'] }}</h2>

                @if ($options['include_details'])
                    <div class="song-meta">
                        @if ($song['author'])
                            <span>
                                Autor:
                                <strong>
                                    {{ $song['author'] }}
                                </strong>
                            </span>
                        @endif

                        <span>
                            Tonalitate:
                            <strong>
                                {{ $song['display_key'] }}
                            </strong>
                        </span>

                        <span>
                            Capo:
                            <strong>
                                {{ $song['capo'] }}
                            </strong>
                        </span>

                        @if ($song['transpose_steps'] !== 0)
                            <span class="transpose-information">
                                Original:
                                {{ $song['original_key'] }}

                                ·

                                {{ $song['transpose_steps'] > 0
                                    ? '+'
                                    : ''
                                }}

                                {{ $song['transpose_steps'] }}

                                {{ abs($song['transpose_steps']) === 1
                                    ? 'semiton'
                                    : 'semitonuri'
                                }}
                            </span>
                        @endif
                    </div>
                @endif
            </header>

@if (trim($song['notes'] ?? '') !== '')
    <div class="song-note">
        <strong>Notiță:</strong>

        {{ $song['notes'] }}
    </div>
@endif

<table class="sections-table">
                <tbody>
                    @foreach ($sectionRows as $sectionRow)
                        <tr>
                            @foreach ($sectionRow as $section)
                                @php
                                    $sectionId =
                                        $section['id'];

                                    $sectionType =
                                        $section['type']
                                        ?? 'stanza';

                                    if ($sectionType === 'custom') {
                                        $sectionLabel =
                                            trim(
                                                $section[
                                                    'custom_label'
                                                ]
                                                ?? ''
                                            )
                                            ?: 'Secțiune';
                                    } else {
                                        $sectionLabel =
                                            $sectionNames[
                                                $sectionType
                                            ]
                                            ?? 'Secțiune';
                                    }

                                    if (
                                        !empty(
                                            $section['number']
                                        )
                                    ) {
                                        $sectionLabel .=
                                            ' '
                                            . $section['number'];
                                    }

                                    $lines = preg_split(
                                        '/\r\n|\r|\n/',
                                        $section['lyrics']
                                        ?? ''
                                    );

                                    $sectionChords =
                                        collect(
                                            $song['chords']
                                        )->filter(
                                            function (
                                                $chord
                                            ) use (
                                                $sectionId
                                            ) {
                                                return
                                                    (
                                                        $chord[
                                                            'section'
                                                        ]
                                                        ?? null
                                                    )
                                                    ===
                                                    $sectionId;
                                            }
                                        );
                                @endphp

                                <td>
                                    <div class="section-card">
                                        @if (
                                            $options[
                                                'include_section_labels'
                                            ]
                                        )
                                            <div class="section-label">
                                                {{ $sectionLabel }}
                                            </div>
                                        @endif

                                        @foreach (
                                            $lines
                                            as $lineIndex => $line
                                        )
                                            @php
                                                /*
                                                 * Selectăm acordurile care aparțin
                                                 * rândului curent.
                                                 */
                                                $lineChords =
                                                    $sectionChords
                                                        ->filter(
                                                            function (
                                                                $chord
                                                            ) use (
                                                                $lineIndex
                                                            ) {
                                                                return
                                                                    (int) (
                                                                        $chord[
                                                                            'line'
                                                                        ]
                                                                        ?? -1
                                                                    )
                                                                    ===
                                                                    $lineIndex;
                                                            }
                                                        );

                                                /*
                                                 * Construim un singur șir monospace.
                                                 * Nu mai folosim poziționare absolută,
                                                 * deoarece DomPDF o redă incorect.
                                                 */
                                                $chordLineText = '';
                                                $currentPosition = 0;

                                                $sortedLineChords =
                                                    $lineChords->sortBy(
                                                        function (
                                                            $chord
                                                        ) {
                                                            return
                                                                (float) (
                                                                    $chord[
                                                                        'position'
                                                                    ]
                                                                    ?? 0
                                                                )
                                                                +
                                                                (float) (
                                                                    $chord[
                                                                        'offset'
                                                                    ]
                                                                    ?? 0
                                                                );
                                                        }
                                                    );

                                                foreach (
                                                    $sortedLineChords
                                                    as $chord
                                                ) {
                                                    $targetPosition =
                                                        max(
                                                            0,
                                                            (int) round(
                                                                (float) (
                                                                    $chord[
                                                                        'position'
                                                                    ]
                                                                    ?? 0
                                                                )
                                                                +
                                                                (float) (
                                                                    $chord[
                                                                        'offset'
                                                                    ]
                                                                    ?? 0
                                                                )
                                                            )
                                                        );

                                                    $chordName =
                                                        $chord[
                                                            'display_name'
                                                        ]
                                                        ??
                                                        $chord['name']
                                                        ??
                                                        '';

                                                    if (
                                                        $targetPosition
                                                        >
                                                        $currentPosition
                                                    ) {
                                                        $chordLineText .=
                                                            str_repeat(
                                                                ' ',
                                                                $targetPosition
                                                                -
                                                                $currentPosition
                                                            );
                                                    } elseif (
                                                        $chordLineText !== ''
                                                    ) {
                                                        $chordLineText .= ' ';
                                                    }

                                                    $chordLineText .=
                                                        $chordName;

                                                    $currentPosition =
                                                        mb_strlen(
                                                            $chordLineText
                                                        );
                                                }
                                            @endphp

                                            <div class="song-line">
                                                @if (
                                                    $options[
                                                        'include_chords'
                                                    ]
                                                )
                                                    <div class="chord-lane">{{ $chordLineText ?: ' ' }}</div>
                                                @endif

                                                <div class="lyric">{{ $line ?: ' ' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach

                            @if ($sectionRow->count() === 1)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <footer class="song-footer">
                {{ $setlist->name }}
                ·
                {{ $song['title'] }}
            </footer>
        </article>
    @endforeach
</body>
</html>