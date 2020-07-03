/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
async function initTinyMCE(options) {
    if (typeof tinymce == 'undefined') return false;
    let resolver = () => {}, rejector = () => {};
    let editors = [];
    const prom = new Promise((resolve, reject) => {
        resolver = resolve;
        rejector = reject;
    });
    // Load when DOM is ready
    domready(function() {
        let i, t = tinymce.editors, textareas = [];
        for (i in t) {
            if (t.hasOwnProperty(i)) t[i].remove();
        }
        if(!(options.selector instanceof Array)) {
            options.selector = [options.selector];
        }
        options.selector.forEach(function(selector) {
            textareas = processSelector(selector, textareas);
        });

        if (!textareas.length) {
            rejector('TinyMCE error: no target found from selector');
            return false;
        }

        for (i = 0; i < textareas.length; i++) {
            // Get selected options
            let settings = {...{
                    plugins: 'importcss searchreplace autolink directionality fullscreen image link media table charmap hr anchor advlist lists wordcount textpattern noneditable help charmap quickbars code',

                    language: options.language,
                    selector: options.selector,
                    variable_prefix: '{',
                    variable_suffix: '}',
                    menu: {},
                    content_style: '.variable,[data-original-variable]{\n' +
                        '    cursor: default;\n' +
                        '    background-color: #65b9dd;\n' +
                        '    color: #FFF;\n' +
                        '    padding: 2px 8px;\n' +
                        '    border-radius: 3px;\n' +
                        '    font-weight: bold;\n' +
                        '    font-style: normal;\n' +
                        '    display: inline-block;\n' +
                        '}',
                    menubar: false,
                    browser_spellcheck: true,
                    toolbar: 'undo redo | formatselect | bold italic underline strikethrough | removeformat | numlist bullist | alignleft aligncenter alignright alignjustify | '
                        +' link unlink anchor blockquote | image media table | fullscreen code',

                    // image_advtab: true,
                    // content_css: options.content_css,
                    importcss_append: true,
                    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                    // noneditable_noneditable_class: "mceNonEditable",
                    toolbar_mode: 'sliding',
                    spellchecker_whitelist: ['Ephox', 'Moxiecode'],

                    contextmenu: "link image imagetools table configurepermanentpen",
                }, ...options};

            // workaround for an incompatibility with html5-validation
            if (textareas[i].getAttribute("required") !== '') {
                textareas[i].removeAttribute("required")
            }
            const textAreaId = textareas[i].getAttribute('id');
            if (textAreaId === '' || textAreaId === null) {
                textareas[i].setAttribute("id", "tinymce_" + Math.random().toString(36).substr(2));
            }

            // Initialize textarea by its ID attribute
            const editor = tinymce
                .createEditor(textareas[i].getAttribute('id'), settings);
            editor.render();
            editors.push(editor);
        }
        resolver(editors.length > 1 ? editors : editors[0]);
    });

    return prom;
}

/**
 * @param selector
 * @param textareas
 */
function processSelector(selector, textareas) {
    let elements;
    if(typeof selector === "string") {
        switch (selector.substring(0, 1)) {
            case "#":
                const _t = document.getElementById(selector.substring(1));
                if (_t) textareas.push(_t);
                break;
            case ".":
                elements = document.getElementsByClassName(selector.substring(1));
                for (element of elements) {
                    textareas.push(element);
                }
                break;
            default:
                elements = document.querySelectorAll(selector);
                for (element of elements) {
                    textareas.push(element);
                }
        }
    } else {
        textareas.push(selector);
    }
    return textareas;
}

function humanFileSize(bytes, si = false, dp = 1) {
    const thresh = si ? 1000 : 1024;

    if (Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }

    const units = ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    let u = -1;
    const r = 10**dp;

    do {
        bytes /= thresh;
        ++u;
    } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);


    return bytes.toFixed(dp) + ' ' + units[u];
}
