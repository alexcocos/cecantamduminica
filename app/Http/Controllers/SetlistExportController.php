<?php

namespace App\Http\Controllers;

use App\Models\Setlist;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

class SetlistExportController extends Controller
{
    /**
     * Afișează pagina cu opțiunile exportului.
     */
    public function options(Setlist $setlist)
    {
        $setlist->load([
            'user',
            'songs',
        ]);

        return view(
            'setlists.export',
            compact('setlist')
        );
    }

    /**
     * Generează și descarcă exportul PDF.
     */
    public function pdf(
        Request $request,
        Setlist $setlist
    ) {
        $request->validate([
            'include_chords' => [
                'nullable',
                'boolean',
            ],

            'include_details' => [
                'nullable',
                'boolean',
            ],

            'include_section_labels' => [
                'nullable',
                'boolean',
            ],

            'one_song_per_page' => [
                'nullable',
                'boolean',
            ],
        ]);

        $options = [
            'include_chords' =>
                $request->boolean('include_chords'),

            'include_details' =>
                $request->boolean('include_details'),

            'include_section_labels' =>
                $request->boolean(
                    'include_section_labels'
                ),

            'one_song_per_page' =>
                $request->boolean(
                    'one_song_per_page'
                ),
        ];

        $setlist->load([
            'user',
            'songs',
        ]);

        $exportSongs = $this->buildExportSongs(
            $setlist
        );

        $fileName = $this->buildFileName(
            $setlist->name,
            'pdf'
        );

        $pdf = Pdf::loadView(
            'setlists.exports.pdf',
            compact(
                'setlist',
                'exportSongs',
                'options'
            )
        );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        $pdf->setOption(
            'defaultFont',
            'DejaVu Sans'
        );

        return $pdf->download($fileName);
    }

    /**
     * Generează și descarcă exportul PowerPoint.
     */
    public function powerpoint(
        Request $request,
        Setlist $setlist
    ) {
        $validated = $request->validate([
            'ppt_include_chords' => [
                'nullable',
                'boolean',
            ],

            'ppt_include_section_labels' => [
                'nullable',
                'boolean',
            ],

            'lines_per_slide' => [
                'nullable',
                'integer',
                'min:1',
                'max:8',
            ],

            'background_color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'text_color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
        ]);

        $options = [
            'include_chords' =>
                $request->boolean(
                    'ppt_include_chords'
                ),

            'include_section_labels' =>
                $request->has(
                    'ppt_include_section_labels'
                )
                    ? $request->boolean(
                        'ppt_include_section_labels'
                    )
                    : true,

            'lines_per_slide' =>
                (int) (
                    $validated['lines_per_slide']
                    ?? 4
                ),

            'background_color' =>
                $this->normalizePowerPointColor(
                    $validated['background_color']
                    ?? '#081E34'
                ),

            'text_color' =>
                $this->normalizePowerPointColor(
                    $validated['text_color']
                    ?? '#FFFFFF'
                ),
        ];

        $setlist->load([
            'user',
            'songs',
        ]);

        $exportSongs = $this->buildExportSongs(
            $setlist
        );

        $presentation = new PhpPresentation();

        $presentation
            ->getLayout()
            ->setDocumentLayout(
                DocumentLayout::LAYOUT_SCREEN_16X9
            );

        $presentation
            ->getDocumentProperties()
            ->setCreator(
                $setlist->user->name
                    ?? 'Ce cântăm duminică'
            )
            ->setTitle($setlist->name)
            ->setSubject('Setlist')
            ->setDescription(
                'Setlist generat de aplicația Ce cântăm duminică.'
            );

        /*
         * PhpPresentation creează automat primul slide.
         */
        $titleSlide =
            $presentation->getActiveSlide();

        $this->createTitleSlide(
            $titleSlide,
            $setlist,
            $exportSongs,
            $options
        );

        foreach ($exportSongs as $song) {
            $songTitleSlide =
                $presentation->createSlide();

            $this->createSongTitleSlide(
                $songTitleSlide,
                $song,
                $options
            );

            foreach ($song['sections'] as $section) {
                $lines = preg_split(
                    '/\r\n|\r|\n/',
                    $section['lyrics'] ?? ''
                );

                /*
                 * Eliminăm doar rândurile goale de la
                 * începutul și finalul secțiunii.
                 */
                while (
                    count($lines) > 0
                    &&
                    trim($lines[0]) === ''
                ) {
                    array_shift($lines);
                }

                while (
                    count($lines) > 0
                    &&
                    trim(
                        $lines[
                            count($lines) - 1
                        ]
                    ) === ''
                ) {
                    array_pop($lines);
                }

                if (count($lines) === 0) {
                    continue;
                }

                $lineGroups = array_chunk(
                    $lines,
                    $options['lines_per_slide'],
                    true
                );

                foreach (
                    $lineGroups
                    as $lineGroupIndex => $lineGroup
                ) {
                    $lyricsSlide =
                        $presentation->createSlide();

                    $this->createLyricsSlide(
                        $lyricsSlide,
                        $song,
                        $section,
                        $lineGroup,
                        $lineGroupIndex,
                        count($lineGroups),
                        $options
                    );
                }
            }
        }

        $fileName = $this->buildFileName(
            $setlist->name,
            'pptx'
        );

        $temporaryFile =
            storage_path(
                'app/'
                . Str::uuid()
                . '.pptx'
            );

        $writer = IOFactory::createWriter(
            $presentation,
            'PowerPoint2007'
        );

        $writer->save($temporaryFile);

        return response()
            ->download(
                $temporaryFile,
                $fileName,
                [
                    'Content-Type' =>
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ]
            )
            ->deleteFileAfterSend(true);
    }

    /**
     * Creează primul slide al setlistului.
     */
    private function createTitleSlide(
        Slide $slide,
        Setlist $setlist,
        Collection $exportSongs,
        array $options
    ): void {
        $this->addSlideBackground(
            $slide,
            $options['background_color']
        );

        $eyebrow = $slide
            ->createRichTextShape()
            ->setWidth(800)
            ->setHeight(35)
            ->setOffsetX(80)
            ->setOffsetY(85);

        $eyebrow
            ->getActiveParagraph()
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $eyebrowText =
            $eyebrow->createTextRun(
                'CE CÂNTĂM DUMINICĂ'
            );

        $eyebrowText
            ->getFont()
            ->setName('Arial')
            ->setSize(14)
            ->setBold(true)
            ->setColor(
                new Color('FF7DD3FC')
            );

        $title = $slide
            ->createRichTextShape()
            ->setWidth(800)
            ->setHeight(150)
            ->setOffsetX(80)
            ->setOffsetY(145);

        $title
            ->getActiveParagraph()
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $titleText =
            $title->createTextRun(
                $setlist->name
            );

        $titleText
            ->getFont()
            ->setName('Arial')
            ->setSize(42)
            ->setBold(true)
            ->setColor(
                new Color(
                    $options['text_color']
                )
            );

        $details = $slide
            ->createRichTextShape()
            ->setWidth(800)
            ->setHeight(70)
            ->setOffsetX(80)
            ->setOffsetY(330);

        $details
            ->getActiveParagraph()
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $detailsText =
            $details->createTextRun(
                $exportSongs->count()
                . (
                    $exportSongs->count() === 1
                        ? ' piesă'
                        : ' piese'
                )
            );

        $detailsText
            ->getFont()
            ->setName('Arial')
            ->setSize(20)
            ->setColor(
                new Color('FFCBD5E1')
            );
    }

    /**
     * Creează slide-ul introductiv al unei piese.
     */
    private function createSongTitleSlide(
        Slide $slide,
        array $song,
        array $options
    ): void {
        $this->addSlideBackground(
            $slide,
            $options['background_color']
        );

        $label = $slide
            ->createRichTextShape()
            ->setWidth(800)
            ->setHeight(35)
            ->setOffsetX(80)
            ->setOffsetY(90);

        $label
            ->getActiveParagraph()
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $labelText =
            $label->createTextRun('PIESĂ');

        $labelText
            ->getFont()
            ->setName('Arial')
            ->setSize(14)
            ->setBold(true)
            ->setColor(
                new Color('FF7DD3FC')
            );

        $title = $slide
            ->createRichTextShape()
            ->setWidth(800)
            ->setHeight(160)
            ->setOffsetX(80)
            ->setOffsetY(160);

        $title
            ->getActiveParagraph()
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $titleText =
            $title->createTextRun(
                $song['title']
            );

        $titleText
            ->getFont()
            ->setName('Arial')
            ->setSize(40)
            ->setBold(true)
            ->setColor(
                new Color(
                    $options['text_color']
                )
            );

    }

    /**
     * Creează un slide cu versurile.
     */
    private function createLyricsSlide(
        Slide $slide,
        array $song,
        array $section,
        array $lines,
        int $groupIndex,
        int $groupCount,
        array $options
    ): void {
        $this->addSlideBackground(
            $slide,
            $options['background_color']
        );

        $sectionLabel =
            $this->getSectionLabel($section);

        if (
            $options['include_section_labels']
        ) {
            $label = $slide
                ->createRichTextShape()
                ->setWidth(800)
                ->setHeight(40)
                ->setOffsetX(80)
                ->setOffsetY(35);

            $label
                ->getActiveParagraph()
                ->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_CENTER
                );

            $labelValue = $sectionLabel;

            if ($groupCount > 1) {
                $labelValue .=
                    ' · '
                    . ($groupIndex + 1)
                    . '/'
                    . $groupCount;
            }

            $labelText =
                $label->createTextRun(
                    mb_strtoupper($labelValue)
                );

            $labelText
                ->getFont()
                ->setName('Arial')
                ->setSize(14)
                ->setBold(true)
                ->setColor(
                    new Color('FF7DD3FC')
                );
        }

        $slideLines = [];

        foreach (
            $lines
            as $lineIndex => $line
        ) {
            if ($options['include_chords']) {
                $chordLine =
                    $this->buildChordLine(
                        $song['chords'],
                        $section['id'],
                        $lineIndex
                    );

                if ($chordLine !== '') {
                    $slideLines[] = $chordLine;
                }
            }

            $slideLines[] = $line;
        }

        $lyrics = $slide
            ->createRichTextShape()
            ->setWidth(820)
            ->setHeight(390)
            ->setOffsetX(70)
            ->setOffsetY(105);

        $lyrics
            ->getActiveParagraph()
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $lyricsText =
            $lyrics->createTextRun(
                implode("\n", $slideLines)
            );

        $fontSize = 40;

        $lyricsText
            ->getFont()
            ->setName('Helvetica')

            ->setSize($fontSize)
            ->setBold(false)
            ->setColor(
                new Color(
                    $options['text_color']
                )
            );

    }

    /**
     * Adaugă fundalul unui slide.
     */
    private function addSlideBackground(
        Slide $slide,
        string $color
    ): void {
        $background = $slide
            ->createRichTextShape()
            ->setWidth(960)
            ->setHeight(540)
            ->setOffsetX(0)
            ->setOffsetY(0);

        $background
            ->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            )
            ->setStartColor(
                new Color($color)
            );
    }

    /**
     * Construiește rândul acordurilor pentru PowerPoint.
     */
    private function buildChordLine(
        array $chords,
        string $sectionId,
        int $lineIndex
    ): string {
        $lineChords = collect($chords)
            ->filter(function ($chord) use (
                $sectionId,
                $lineIndex
            ) {
                return
                    (
                        $chord['section']
                        ?? null
                    ) === $sectionId
                    &&
                    (int) (
                        $chord['line']
                        ?? -1
                    ) === $lineIndex;
            })
            ->sortBy(function ($chord) {
                return
                    (float) (
                        $chord['position']
                        ?? 0
                    )
                    +
                    (float) (
                        $chord['offset']
                        ?? 0
                    );
            });

        $chordLine = '';
        $currentPosition = 0;

        foreach ($lineChords as $chord) {
            $targetPosition = max(
                0,
                (int) round(
                    (float) (
                        $chord['position']
                        ?? 0
                    )
                    +
                    (float) (
                        $chord['offset']
                        ?? 0
                    )
                )
            );

            $chordName =
                $chord['display_name']
                ?? $chord['name']
                ?? '';

            if (
                $targetPosition
                >
                $currentPosition
            ) {
                $chordLine .= str_repeat(
                    ' ',
                    $targetPosition
                    -
                    $currentPosition
                );
            } elseif ($chordLine !== '') {
                $chordLine .= ' ';
            }

            $chordLine .= $chordName;

            $currentPosition =
                mb_strlen($chordLine);
        }

        return $chordLine;
    }

    /**
     * Returnează denumirea secțiunii.
     */
    private function getSectionLabel(
        array $section
    ): string {
        $sectionNames = [
            'verse' => 'Pre-refren',
            'pre_chorus' => 'Pre-refren',
            'stanza' => 'Strofă',
            'chorus' => 'Refren',
            'bridge' => 'Bridge',
            'coda' => 'Coda',
        ];

        $type =
            $section['type']
            ?? 'stanza';

        if ($type === 'custom') {
            $label =
                trim(
                    $section['custom_label']
                    ?? ''
                )
                ?: 'Secțiune';
        } else {
            $label =
                $sectionNames[$type]
                ?? 'Secțiune';
        }

        if (
            !empty($section['number'])
        ) {
            $label .=
                ' '
                . $section['number'];
        }

        return $label;
    }

    /**
     * Pregătește piesele pentru export.
     */
    private function buildExportSongs(
        Setlist $setlist
    ): Collection {
        return $setlist->songs
            ->map(function ($song) {
                $transposeSteps = (int) (
                    $song->pivot->transpose_steps
                    ?? 0
                );

                $sections =
                    $song->sections
                    ?? [];

                /*
                 * Compatibilitate cu piesele vechi.
                 */
                if (empty($sections)) {
                    $sections = [
                        [
                            'id' =>
                                'legacy-section-'
                                . $song->id,

                            'type' => 'stanza',

                            'number' => null,

                            'custom_label' => null,

                            'lyrics' =>
                                $song->lyrics ?? '',
                        ],
                    ];
                }

                $chords = collect(
                    $song->chords ?? []
                )
                    ->map(function ($chord) use (
                        $transposeSteps
                    ) {
                        $chord['display_name'] =
                            $this->transposeChord(
                                $chord['name'] ?? '',
                                $transposeSteps
                            );

                        return $chord;
                    })
                    ->values()
                    ->all();

                /*
                 * Compatibilitate pentru acordurile
                 * pieselor vechi.
                 */
                if (count($sections) === 1) {
                    $firstSectionId =
                        $sections[0]['id'];

                    $chords = collect($chords)
                        ->map(function ($chord) use (
                            $firstSectionId
                        ) {
                            if (
                                empty(
                                    $chord['section']
                                )
                            ) {
                                $chord['section'] =
                                    $firstSectionId;
                            }

                            return $chord;
                        })
                        ->values()
                        ->all();
                }

                return [
                    'id' => $song->id,

                    'title' =>
                        $song->title,

                    'author' =>
                        $song->author,

                    'original_key' =>
                        $song->key,

                    'display_key' =>
                        $this->transposeChord(
                            $song->key,
                            $transposeSteps
                        ),

                    'capo' =>
                        $song->capo ?? 0,

                    'transpose_steps' =>
                        $transposeSteps,

                    'song_type' =>
                        $song->song_type,

                    'event_name' =>
    $song->event_name,

'notes' =>
    $song->pivot->notes
    ?? null,

'sections' =>
    $sections,

                    'chords' =>
                        $chords,
                ];
            })
            ->values();
    }

    /**
     * Normalizează o culoare pentru PowerPoint.
     */
    private function normalizePowerPointColor(
        string $color
    ): string {
        return
            'FF'
            . strtoupper(
                ltrim($color, '#')
            );
    }

    /**
     * Construiește numele fișierului.
     */
    private function buildFileName(
        string $name,
        string $extension
    ): string {
        $fileName = Str::slug($name);

        if ($fileName === '') {
            $fileName = 'setlist';
        }

        return
            $fileName
            . '.'
            . $extension;
    }

    /**
     * Transpune un acord cu numărul primit
     * de semitonuri.
     */
    private function transposeChord(
        ?string $chord,
        int $steps
    ): string {
        if (
            empty($chord)
            ||
            $steps === 0
        ) {
            return $chord ?? '';
        }

        $sharpNotes = [
            'C',
            'C#',
            'D',
            'D#',
            'E',
            'F',
            'F#',
            'G',
            'G#',
            'A',
            'A#',
            'B',
        ];

        $flatNotes = [
            'C',
            'Db',
            'D',
            'Eb',
            'E',
            'F',
            'Gb',
            'G',
            'Ab',
            'A',
            'Bb',
            'B',
        ];

        $noteIndexes = [
            'C' => 0,
            'B#' => 0,
            'C#' => 1,
            'Db' => 1,
            'D' => 2,
            'D#' => 3,
            'Eb' => 3,
            'E' => 4,
            'Fb' => 4,
            'E#' => 5,
            'F' => 5,
            'F#' => 6,
            'Gb' => 6,
            'G' => 7,
            'G#' => 8,
            'Ab' => 8,
            'A' => 9,
            'A#' => 10,
            'Bb' => 10,
            'B' => 11,
            'Cb' => 11,
        ];

        $transposeNote = function (
            string $note
        ) use (
            $steps,
            $sharpNotes,
            $flatNotes,
            $noteIndexes
        ): string {
            if (
                !array_key_exists(
                    $note,
                    $noteIndexes
                )
            ) {
                return $note;
            }

            $newIndex = (
                (
                    $noteIndexes[$note]
                    + $steps
                ) % 12
                + 12
            ) % 12;

            $notes = str_contains(
                $note,
                'b'
            )
                ? $flatNotes
                : $sharpNotes;

            return $notes[$newIndex];
        };

        if (
            !preg_match(
                '/^([A-G](?:#|b)?)(.*)$/',
                $chord,
                $matches
            )
        ) {
            return $chord;
        }

        $root =
            $transposeNote($matches[1]);

        $suffix = preg_replace_callback(
            '/\/([A-G](?:#|b)?)/',
            function ($matches) use (
                $transposeNote
            ) {
                return '/'
                    . $transposeNote(
                        $matches[1]
                    );
            },
            $matches[2]
        );

        return $root . $suffix;
    }
}