const songForm = document.getElementById('song-form');

if (songForm) {
    const initialData = window.songEditorInitialData ?? {
        sections: [],
        chords: [],
    };

    const sectionsInput =
        document.getElementById('sections');

    const chordsInput =
        document.getElementById('chords');

    const sectionsList =
        document.getElementById('sections-list');

    const addSectionButton =
        document.getElementById('add-section');

    const addSectionTitle =
        document.getElementById('add-section-title');

    const sectionFeedback =
        document.getElementById('section-feedback');

    const continueButton =
        document.getElementById('continue-to-chords');

    const saveSongButton =
        document.getElementById('save-song');

    const sectionsStep =
        document.querySelector('.section-builder');

    const chordsStep =
        document.getElementById('global-chords-step');

    const chordsEditor =
        document.getElementById('global-chords-editor');

    const backToSectionsButton =
        document.getElementById('back-to-sections');

    let sections = Array.isArray(initialData.sections)
        ? initialData.sections
        : [];

    let chords = Array.isArray(initialData.chords)
        ? initialData.chords
        : [];

    let activeSectionId = null;

    function createSectionId() {
        if (
            window.crypto &&
            typeof window.crypto.randomUUID === 'function'
        ) {
            return window.crypto.randomUUID();
        }

        return (
            'section-' +
            Date.now() +
            '-' +
            Math.random().toString(16).slice(2)
        );
    }

    function createEmptySection() {
        return {
            id: createSectionId(),
            type: 'stanza',
            number: '',
            custom_label: '',
            lyrics: '',
        };
    }

    function saveState() {
        sectionsInput.value =
            JSON.stringify(sections);

        chordsInput.value =
            JSON.stringify(chords);
    }

    function getTypeLabel(section) {
        const labels = {
            stanza: 'Strofă',
            pre_chorus: 'Pre-refren',
            chorus: 'Refren',
            bridge: 'Bridge',
            coda: 'Coda',
            custom:
                section.custom_label ||
                'Secțiune personalizată',
        };

        return labels[section.type] || 'Secțiune';
    }

    function getSectionLabel(section) {
        const label = getTypeLabel(section);

        return section.number
            ? `${label} ${section.number}`
            : label;
    }

    function getSectionPreview(section) {
        const firstLine = (section.lyrics || '')
            .split('\n')
            .find((line) => line.trim());

        if (!firstLine) {
            return 'Fără versuri';
        }

        return firstLine.length > 75
            ? `${firstLine.slice(0, 75)}…`
            : firstLine;
    }

    function findSectionIndex(sectionId) {
        return sections.findIndex(
            (section) => section.id === sectionId
        );
    }

    function showFeedback(message) {
        sectionFeedback.textContent = message;
        sectionFeedback.hidden = false;
    }

    function hideFeedback() {
        sectionFeedback.textContent = '';
        sectionFeedback.hidden = true;
    }

    function validateSection(section) {
        if (!(section.lyrics || '').trim()) {
            return 'Scrie versurile secțiunii înainte de salvare.';
        }

        if (
            section.type === 'custom' &&
            !(section.custom_label || '').trim()
        ) {
            return 'Scrie denumirea secțiunii personalizate.';
        }

        return null;
    }

    function validateAllSections() {
        if (sections.length === 0) {
            return 'Adaugă cel puțin o secțiune.';
        }

        for (const section of sections) {
            const error = validateSection(section);

            if (error) {
                return error;
            }
        }

        return null;
    }

    function closeActiveSection() {
        if (activeSectionId === null) {
            return true;
        }

        const sectionIndex =
            findSectionIndex(activeSectionId);

        if (sectionIndex === -1) {
            activeSectionId = null;

            return true;
        }

        const error = validateSection(
            sections[sectionIndex]
        );

        if (error) {
            showFeedback(error);

            return false;
        }

        activeSectionId = null;

        hideFeedback();
        saveState();
        renderSections();

        return true;
    }

    function addSection() {
        if (!closeActiveSection()) {
            return;
        }

        const section = createEmptySection();

        sections.push(section);
        activeSectionId = section.id;

        hideFeedback();
        saveState();
        renderSections();

        requestAnimationFrame(() => {
            const activeCard =
                document.querySelector(
                    `[data-section-id="${section.id}"]`
                );

            activeCard?.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
            });
        });
    }

    function editSection(sectionId) {
        if (
            activeSectionId !== null &&
            activeSectionId !== sectionId &&
            !closeActiveSection()
        ) {
            return;
        }

        activeSectionId = sectionId;

        hideFeedback();
        renderSections();

        requestAnimationFrame(() => {
            const activeCard =
                document.querySelector(
                    `[data-section-id="${sectionId}"]`
                );

            activeCard?.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
            });
        });
    }

    function deleteSection(sectionId) {
        const sectionIndex =
            findSectionIndex(sectionId);

        if (sectionIndex === -1) {
            return;
        }

        const section = sections[sectionIndex];

        const confirmed = window.confirm(
            `Sigur vrei să ștergi „${getSectionLabel(section)}”?`
        );

        if (!confirmed) {
            return;
        }

        sections.splice(sectionIndex, 1);

        chords = chords.filter(
            (chord) => chord.section !== sectionId
        );

        if (activeSectionId === sectionId) {
            activeSectionId = null;
        }

        hideFeedback();
        saveState();
        renderSections();
    }

    function moveSection(sectionId, direction) {
        const currentIndex =
            findSectionIndex(sectionId);

        const newIndex =
            currentIndex + direction;

        if (
            currentIndex === -1 ||
            newIndex < 0 ||
            newIndex >= sections.length
        ) {
            return;
        }

        const [movedSection] =
            sections.splice(currentIndex, 1);

        sections.splice(
            newIndex,
            0,
            movedSection
        );

        saveState();
        renderSections();
    }

    function createActionButton(
        text,
        action,
        className = ''
    ) {
        const button =
            document.createElement('button');

        button.type = 'button';
        button.textContent = text;
        button.className = className;

        button.addEventListener(
            'click',
            action
        );

        return button;
    }

    function createCollapsedSection(
        section,
        sectionIndex
    ) {
        const card =
            document.createElement('article');

        card.className = 'collapsed-section';
        card.dataset.sectionId = section.id;

        const content =
            document.createElement('button');

        content.type = 'button';
        content.className =
            'collapsed-section-content';

        content.addEventListener(
            'click',
            () => {
                editSection(section.id);
            }
        );

        const status =
            document.createElement('span');

        status.className =
            'section-complete-icon';

        status.textContent = '✓';

        const text =
            document.createElement('span');

        text.className =
            'collapsed-section-text';

        const title =
            document.createElement('strong');

        title.textContent =
            getSectionLabel(section);

        const preview =
            document.createElement('small');

        preview.textContent =
            getSectionPreview(section);

        text.append(
            title,
            preview
        );

        content.append(
            status,
            text
        );

        const actions =
            document.createElement('div');

        actions.className =
            'collapsed-section-actions';

        const upButton = createActionButton(
            '↑',
            () => {
                moveSection(section.id, -1);
            }
        );

        upButton.title = 'Mută mai sus';
        upButton.disabled =
            sectionIndex === 0;

        const downButton = createActionButton(
            '↓',
            () => {
                moveSection(section.id, 1);
            }
        );

        downButton.title = 'Mută mai jos';
        downButton.disabled =
            sectionIndex === sections.length - 1;

        const editButton = createActionButton(
            'Editează',
            () => {
                editSection(section.id);
            }
        );

        const deleteButton = createActionButton(
            'Șterge',
            () => {
                deleteSection(section.id);
            },
            'compact-delete-button'
        );

        actions.append(
            upButton,
            downButton,
            editButton,
            deleteButton
        );

        card.append(
            content,
            actions
        );

        return card;
    }

    function createActiveSection(section) {
        const card =
            document.createElement('article');

        card.className =
            'active-section-card';

        card.dataset.sectionId =
            section.id;

        const heading =
            document.createElement('div');

        heading.className =
            'active-section-heading';

        const headingText =
            document.createElement('div');

        const eyebrow =
            document.createElement('span');

        eyebrow.textContent =
            'Secțiune deschisă';

        const title =
            document.createElement('h3');

        title.textContent =
            (section.lyrics || '').trim()
                ? `Editează ${getSectionLabel(section)}`
                : 'Adaugă o secțiune';

        headingText.append(
            eyebrow,
            title
        );

        const deleteButton = createActionButton(
            'Șterge',
            () => {
                deleteSection(section.id);
            },
            'compact-delete-button'
        );

        heading.append(
            headingText,
            deleteButton
        );

        const settings =
            document.createElement('div');

        settings.className =
            'active-section-settings';

        const typeGroup =
            document.createElement('div');

        typeGroup.className =
            'form-group';

        const typeLabel =
            document.createElement('label');

        typeLabel.textContent = 'Tip';

        const typeSelect =
            document.createElement('select');

        const sectionTypes = [
            ['stanza', 'Strofă'],
            ['pre_chorus', 'Pre-refren'],
            ['chorus', 'Refren'],
            ['bridge', 'Bridge'],
            ['coda', 'Coda'],
            ['custom', 'Personalizat'],
        ];

        sectionTypes.forEach(
            ([value, label]) => {
                const option =
                    document.createElement('option');

                option.value = value;
                option.textContent = label;
                option.selected =
                    section.type === value;

                typeSelect.appendChild(option);
            }
        );

        typeSelect.addEventListener(
            'change',
            () => {
                section.type =
                    typeSelect.value;

                saveState();
                renderSections();
            }
        );

        typeGroup.append(
            typeLabel,
            typeSelect
        );

        const numberGroup =
            document.createElement('div');

        numberGroup.className =
            'form-group';

        const numberLabel =
            document.createElement('label');

        numberLabel.textContent = 'Număr';

        const numberInput =
            document.createElement('input');

        numberInput.type = 'number';
        numberInput.min = '1';
        numberInput.placeholder = 'Opțional';
        numberInput.value =
            section.number || '';

        numberInput.addEventListener(
            'input',
            () => {
                section.number =
                    numberInput.value;

                saveState();
            }
        );

        numberGroup.append(
            numberLabel,
            numberInput
        );

        settings.append(
            typeGroup,
            numberGroup
        );

        if (section.type === 'custom') {
            const customGroup =
                document.createElement('div');

            customGroup.className =
                'form-group custom-label-group';

            const customLabel =
                document.createElement('label');

            customLabel.textContent =
                'Denumire';

            const customInput =
                document.createElement('input');

            customInput.type = 'text';
            customInput.placeholder =
                'Exemplu: Pre-refren';

            customInput.value =
                section.custom_label || '';

            customInput.addEventListener(
                'input',
                () => {
                    section.custom_label =
                        customInput.value;

                    saveState();
                }
            );

            customGroup.append(
                customLabel,
                customInput
            );

            settings.appendChild(
                customGroup
            );
        }

        const lyricsGroup =
            document.createElement('div');

        lyricsGroup.className =
            'form-group active-section-lyrics';

        const lyricsLabel =
            document.createElement('label');

        lyricsLabel.textContent =
            'Versuri';

        const help =
            document.createElement('p');

        help.className =
            'field-help';

        help.textContent =
            'Scrie fiecare vers pe un rând separat.';

        const textarea =
            document.createElement('textarea');

        textarea.rows = 8;

        textarea.placeholder =
            'Scrie versurile acestei secțiuni…';

        textarea.value =
            section.lyrics || '';

        textarea.addEventListener(
            'input',
            () => {
                section.lyrics =
                    textarea.value;

                hideFeedback();
                saveState();
            }
        );

        lyricsGroup.append(
            lyricsLabel,
            help,
            textarea
        );

        const saveButton = createActionButton(
            'Salvează secțiunea',
            closeActiveSection,
            'button button-primary save-section-button'
        );

        card.append(
            heading,
            settings,
            lyricsGroup,
            saveButton
        );

        return card;
    }

    function renderSections() {
        sectionsList.innerHTML = '';

        sections.forEach(
            (section, sectionIndex) => {
                const element =
                    section.id === activeSectionId
                        ? createActiveSection(section)
                        : createCollapsedSection(
                            section,
                            sectionIndex
                        );

                sectionsList.appendChild(
                    element
                );
            }
        );

        addSectionButton.hidden =
            activeSectionId !== null;

        addSectionTitle.textContent =
            sections.length === 0
                ? 'Adaugă prima secțiune'
                : 'Adaugă următoarea secțiune';

        saveState();
    }

    function getCharacterWidth(element) {
        const styles =
            window.getComputedStyle(element);

        const canvas =
            document.createElement('canvas');

        const context =
            canvas.getContext('2d');

        context.font =
            `${styles.fontWeight} ${styles.fontSize} ${styles.fontFamily}`;

        return context.measureText('0').width;
    }

    function saveChordPosition(
        chordElement,
        chordIndex,
        lyricLine
    ) {
        const characterWidth =
            getCharacterWidth(lyricLine);

        if (!characterWidth) {
            return;
        }

        const exactPosition =
            chordElement.offsetLeft /
            characterWidth;

        const position =
            Math.round(exactPosition);

        const offset =
            exactPosition - position;

        chords[chordIndex].position =
            position;

        chords[chordIndex].offset =
            Number(offset.toFixed(2));

        chordElement.style.left =
            `calc(${position}ch + ${offset.toFixed(2)}ch)`;

        saveState();
    }

    function enableChordDragging(
        chordElement,
        chordIndex,
        chordLane,
        lyricLine
    ) {
        let isDragging = false;
        let hasMoved = false;
        let startX = 0;
        let startLeft = 0;

        chordElement.addEventListener(
            'pointerdown',
            (event) => {
                if (
                    event.target.classList.contains(
                        'editor-chord-delete'
                    )
                ) {
                    return;
                }

                isDragging = true;
                hasMoved = false;
                startX = event.clientX;
                startLeft =
                    chordElement.offsetLeft;

                chordElement.classList.add(
                    'is-dragging'
                );

                chordElement.setPointerCapture(
                    event.pointerId
                );

                event.preventDefault();
            }
        );

        chordElement.addEventListener(
            'pointermove',
            (event) => {
                if (!isDragging) {
                    return;
                }

                const movement =
                    event.clientX - startX;

                if (Math.abs(movement) > 3) {
                    hasMoved = true;
                }

                const maximumLeft =
                    chordLane.clientWidth -
                    chordElement.offsetWidth;

                const newLeft = Math.min(
                    Math.max(
                        startLeft + movement,
                        0
                    ),
                    Math.max(
                        maximumLeft,
                        0
                    )
                );

                chordElement.style.left =
                    `${newLeft}px`;
            }
        );

        chordElement.addEventListener(
            'pointerup',
            (event) => {
                if (!isDragging) {
                    return;
                }

                isDragging = false;

                chordElement.classList.remove(
                    'is-dragging'
                );

                if (
                    chordElement.hasPointerCapture(
                        event.pointerId
                    )
                ) {
                    chordElement.releasePointerCapture(
                        event.pointerId
                    );
                }

                saveChordPosition(
                    chordElement,
                    chordIndex,
                    lyricLine
                );
            }
        );

        chordElement.addEventListener(
            'pointercancel',
            () => {
                isDragging = false;

                chordElement.classList.remove(
                    'is-dragging'
                );
            }
        );

        chordElement.addEventListener(
            'dblclick',
            (event) => {
                if (
                    hasMoved ||
                    event.target.classList.contains(
                        'editor-chord-delete'
                    )
                ) {
                    return;
                }

                const newName =
                    window.prompt(
                        'Modifică acordul:',
                        chords[chordIndex].name
                    );

                if (
                    newName === null ||
                    !newName.trim()
                ) {
                    return;
                }

                chords[chordIndex].name =
                    newName.trim();

                saveState();
                renderGlobalChords();
            }
        );
    }

    function createChordElement(
        chord,
        chordIndex,
        chordLane,
        lyricLine
    ) {
        const chordElement =
            document.createElement('div');

        chordElement.className =
            'editor-chord';

        chordElement.style.left =
            `calc(${chord.position || 0}ch + ${chord.offset || 0}ch)`;

        const chordName =
            document.createElement('span');

        chordName.className =
            'editor-chord-name';

        chordName.textContent =
            chord.name;

        chordName.title =
            'Trage pentru mutare sau dublu click pentru editare';

        const deleteButton =
            document.createElement('button');

        deleteButton.type = 'button';

        deleteButton.className =
            'editor-chord-delete';

        deleteButton.textContent = '×';
        deleteButton.title =
            'Șterge acordul';

        deleteButton.addEventListener(
            'click',
            (event) => {
                event.stopPropagation();

                chords.splice(
                    chordIndex,
                    1
                );

                saveState();
                renderGlobalChords();
            }
        );

        chordElement.append(
            chordName,
            deleteButton
        );

        enableChordDragging(
            chordElement,
            chordIndex,
            chordLane,
            lyricLine
        );

        return chordElement;
    }

    function addChordsToLine(
        sectionId,
        lineIndex,
        chordText,
        lyricText
    ) {
        const chordNames = chordText
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        if (chordNames.length === 0) {
            return;
        }

        const lyricLength =
            Math.max(
                lyricText.length,
                1
            );

        chordNames.forEach(
            (name, index) => {
                const position =
                    Math.round(
                        (
                            (index + 1) *
                            lyricLength
                        ) /
                        (
                            chordNames.length +
                            1
                        )
                    );

                chords.push({
                    name,
                    section: sectionId,
                    line: lineIndex,
                    position,
                    offset: 0,
                });
            }
        );

        saveState();
        renderGlobalChords();
    }

    function createChordEditorLine(
        section,
        line,
        lineIndex
    ) {
        const editorLine =
            document.createElement('div');

        editorLine.className =
            'editor-line';

        const toolbar =
            document.createElement('div');

        toolbar.className =
            'line-toolbar';

        const lineNumber =
            document.createElement('span');

        lineNumber.className =
            'line-number';

        lineNumber.textContent =
            `Rândul ${lineIndex + 1}`;

        const chordEntry =
            document.createElement('input');

        chordEntry.type = 'text';
        chordEntry.className =
            'chord-entry';

        chordEntry.placeholder =
            'Exemplu: G D Em C';

        const addButton =
            document.createElement('button');

        addButton.type = 'button';

        addButton.className =
            'add-chords-button';

        addButton.textContent =
            'Adaugă';

        function submitChords() {
            addChordsToLine(
                section.id,
                lineIndex,
                chordEntry.value,
                line
            );
        }

        addButton.addEventListener(
            'click',
            submitChords
        );

        chordEntry.addEventListener(
            'keydown',
            (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    submitChords();
                }
            }
        );

        toolbar.append(
            lineNumber,
            chordEntry,
            addButton
        );

        const chordLane =
            document.createElement('div');

        chordLane.className =
            'chord-lane';

        const lyricLine =
            document.createElement('div');

        lyricLine.className =
            'lyric-line';

        lyricLine.textContent =
            line || ' ';

        chords.forEach(
            (chord, chordIndex) => {
                if (
                    chord.section !==
                        section.id ||
                    Number(chord.line) !==
                        lineIndex
                ) {
                    return;
                }

                const chordElement =
                    createChordElement(
                        chord,
                        chordIndex,
                        chordLane,
                        lyricLine
                    );

                chordLane.appendChild(
                    chordElement
                );
            }
        );

        editorLine.append(
            toolbar,
            chordLane,
            lyricLine
        );

        return editorLine;
    }

    function renderGlobalChords() {
        chordsEditor.innerHTML = '';

        chords = chords.filter(
            (chord) => {
                const section =
                    sections.find(
                        (item) =>
                            item.id ===
                            chord.section
                    );

                if (!section) {
                    return false;
                }

                const lines =
                    (section.lyrics || '')
                        .split('\n');

                return (
                    Number(chord.line) >= 0 &&
                    Number(chord.line) <
                        lines.length
                );
            }
        );

        sections.forEach((section) => {
            const sectionBlock =
                document.createElement('section');

            sectionBlock.className =
                'global-chord-section';

            const sectionHeading =
                document.createElement('div');

            sectionHeading.className =
                'global-chord-section-heading';

            const title =
                document.createElement('h3');

            title.textContent =
                getSectionLabel(section);

            const description =
                document.createElement('p');

            description.textContent =
                'Adaugă acordurile, apoi trage-le deasupra cuvintelor potrivite.';

            sectionHeading.append(
                title,
                description
            );

            sectionBlock.appendChild(
                sectionHeading
            );

            const lines =
                (section.lyrics || '')
                    .split('\n');

            lines.forEach(
                (line, lineIndex) => {
                    const editorLine =
                        createChordEditorLine(
                            section,
                            line,
                            lineIndex
                        );

                    sectionBlock.appendChild(
                        editorLine
                    );
                }
            );

            chordsEditor.appendChild(
                sectionBlock
            );
        });

        saveState();
    }

    function openChordStep() {
        if (!closeActiveSection()) {
            return;
        }

        const error =
            validateAllSections();

        if (error) {
            showFeedback(error);

            sectionsStep.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });

            return;
        }

        hideFeedback();

        sectionsStep.hidden = true;
        chordsStep.hidden = false;

        continueButton.hidden = true;
        saveSongButton.hidden = false;

        renderGlobalChords();

        chordsStep.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }

    function returnToSections() {
        chordsStep.hidden = true;
        sectionsStep.hidden = false;

        continueButton.hidden = false;
        saveSongButton.hidden = true;

        sectionsStep.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }

    addSectionButton.addEventListener(
        'click',
        addSection
    );

    continueButton.addEventListener(
        'click',
        openChordStep
    );

    backToSectionsButton.addEventListener(
        'click',
        returnToSections
    );

    songForm.addEventListener(
        'submit',
        (event) => {
            const error =
                validateAllSections();

            if (error) {
                event.preventDefault();

                returnToSections();
                showFeedback(error);

                return;
            }

            saveState();
        }
    );

    saveState();
    renderSections();
}