import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

class NoBorderTablePlugin {
    constructor(editor) {
        this.editor = editor;
    }

    afterInit() {
        const editor = this.editor;

        editor.model.schema.extend('table', {
            allowAttributes: ['noBorderTable'],
        });

        editor.conversion.for('upcast').elementToAttribute({
            view: {
                name: 'figure',
                classes: 'no-border-table',
            },
            model: {
                key: 'noBorderTable',
                value: true,
            },
        });

        editor.conversion.for('upcast').elementToAttribute({
            view: {
                name: 'table',
                classes: 'no-border-table',
            },
            model: {
                key: 'noBorderTable',
                value: true,
            },
        });

        editor.conversion.for('downcast').add(function (dispatcher) {
            dispatcher.on('attribute:noBorderTable:table', function (event, data, conversionApi) {
                const viewWriter = conversionApi.writer;
                const viewFigure = conversionApi.mapper.toViewElement(data.item);

                if (!viewFigure) {
                    return;
                }

                const viewTable = Array.from(viewFigure.getChildren()).find(function (child) {
                    return child.is('element', 'table');
                });

                if (data.attributeNewValue) {
                    viewWriter.addClass('no-border-table', viewFigure);
                    if (viewTable) {
                        viewWriter.addClass('no-border-table', viewTable);
                    }
                } else {
                    viewWriter.removeClass('no-border-table', viewFigure);
                    if (viewTable) {
                        viewWriter.removeClass('no-border-table', viewTable);
                    }
                }
            });
        });
    }
}

window.createLetterEditor = function (element, config = {}) {
    return ClassicEditor.create(element, {
        extraPlugins: [NoBorderTablePlugin],
        toolbar: {
            items: [
                'undo',
                'redo',
                'bold',
                'italic',
                'numberedList',
                'bulletedList',
                'outdent',
                'indent',
                'insertTable',
            ],
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
        },
        language: 'id',
        ...config,
    }).then(function (editor) {
        element.ckeditorInstance = editor;
        return editor;
    });
};

function getSelectedTable(editor) {
    const selection = editor.model.document.selection;
    const selectedElement = selection.getSelectedElement();

    if (selectedElement?.is('element', 'table')) {
        return selectedElement;
    }

    const firstPosition = selection.getFirstPosition();

    return firstPosition ? firstPosition.findAncestor('table') : null;
}

function syncEditorToInput(editor, input) {
    if (input) {
        input.value = editor.getData();
    }
}

function showFallback(wrapper, editorElement, fallback) {
    wrapper?.classList.remove('is-ready');
    if (editorElement) {
        editorElement.classList.add('hidden');
    }
    if (fallback) {
        fallback.classList.remove('hidden');
    }
}

function initLetterEditorElement(editorElement) {
    if (editorElement.dataset.ckeditorInitialized === 'true') {
        return;
    }

    const wrapper = editorElement.closest('.letter-editor-wrapper');
    const input = document.getElementById(editorElement.dataset.inputId);
    const fallback = document.getElementById(editorElement.dataset.fallbackId);
    const initialData = editorElement.dataset.initialValue || '';

    editorElement.dataset.ckeditorInitialized = 'true';

    window.createLetterEditor(editorElement, {
        initialData,
        placeholder: editorElement.dataset.placeholder || 'Ketik isi surat...',
    }).then(function (editor) {
        syncEditorToInput(editor, input);
        wrapper?.classList.add('is-ready');
        editorElement.classList.remove('hidden');
        fallback?.classList.add('hidden');

        editor.model.document.on('change:data', function () {
            syncEditorToInput(editor, input);
        });
    }).catch(function (error) {
        console.error('CKEditor error:', error);
        editorElement.dataset.ckeditorInitialized = 'false';
        showFallback(wrapper, editorElement, fallback);
    });
}

function applyTableBorderState(editorElement, noBorder) {
    const editor = editorElement?.ckeditorInstance;

    if (!editor) {
        return false;
    }

    let changed = false;

    editor.editing.view.focus();

    editor.model.change(function (writer) {
        const table = getSelectedTable(editor);

        if (!table) {
            return;
        }

        if (noBorder) {
            writer.setAttribute('noBorderTable', true, table);
        } else {
            writer.removeAttribute('noBorderTable', table);
        }

        changed = true;
    });

    return changed;
}

function insertBorderlessTableTemplate(editorElement) {
    const editor = editorElement?.ckeditorInstance;

    if (!editor) {
        return false;
    }

    const html = `
        <figure class="table no-border-table">
            <table class="no-border-table">
                <tbody>
                    <tr>
                        <td style="width:25%;">Hari/Tanggal</td>
                        <td style="width:3%;">:</td>
                        <td>Senin, 6 Juli 2026</td>
                    </tr>
                    <tr>
                        <td>Pukul</td>
                        <td>:</td>
                        <td>09.00 WIB s.d. selesai</td>
                    </tr>
                    <tr>
                        <td>Tempat</td>
                        <td>:</td>
                        <td>Ruang/Aula/Kantor Tata Usaha SMA PERSIS SERANG</td>
                    </tr>
                </tbody>
            </table>
        </figure>
    `;

    editor.editing.view.focus();

    const viewFragment = editor.data.processor.toView(html);
    const modelFragment = editor.data.toModel(viewFragment);

    editor.model.insertContent(modelFragment, editor.model.document.selection);

    return true;
}

window.initLetterEditors = function () {
    const editorElements = document.querySelectorAll('.letter-ckeditor');

    editorElements.forEach(initLetterEditorElement);
};

document.addEventListener('DOMContentLoaded', function () {
    window.initLetterEditors();

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.letter-editor-wrapper').forEach(function (wrapper) {
                const input = wrapper.querySelector('.letter-editor-input');
                const editorElement = wrapper.querySelector('.letter-ckeditor');
                const fallback = wrapper.querySelector('.letter-editor-fallback');

                if (editorElement?.ckeditorInstance) {
                    input.value = editorElement.ckeditorInstance.getData();
                } else if (fallback && !fallback.classList.contains('hidden')) {
                    input.value = fallback.value;
                }
            });
        });
    });

    document.addEventListener('mousedown', function (event) {
        if (event.target.closest('[data-letter-table-border], [data-letter-insert-borderless-table]')) {
            event.preventDefault();
        }
    });

    document.addEventListener('click', function (event) {
        const insertButton = event.target.closest('[data-letter-insert-borderless-table]');

        if (insertButton) {
            const wrapper = insertButton.closest('.letter-editor-wrapper');
            const editorElement = wrapper?.querySelector('.letter-ckeditor');

            insertBorderlessTableTemplate(editorElement);
            return;
        }

        const button = event.target.closest('[data-letter-table-border]');

        if (!button) {
            return;
        }

        const wrapper = button.closest('.letter-editor-wrapper');
        const editorElement = wrapper?.querySelector('.letter-ckeditor');
        const noBorder = button.dataset.letterTableBorder === 'hide';

        if (!applyTableBorderState(editorElement, noBorder)) {
            button.blur();
        }
    });
});
