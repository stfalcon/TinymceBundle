/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
async function initTinyMCE(options) {
    if (typeof tinymce == 'undefined') return false;
    let resolver = () => {};
    let rejector = () => {};
    let editors = [];
    const prom = new Promise((resolve, reject) => {
        resolver = resolve;
        rejector = reject;
    });

    const defaults = {
        plugins: 'importcss searchreplace autolink directionality fullscreen image link media table charmap hr anchor advlist lists wordcount textpattern noneditable help charmap quickbars code',

        language: options.language,
        selector: options.selector,
        variable_prefix: '{',
        variable_suffix: '}',
        menu: {},
        content_style: '',
        menubar: false,
        browser_spellcheck: true,
        entity_encoding: 'raw',
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | removeformat | numlist bullist | alignleft aligncenter alignright alignjustify | '
            + ' link unlink anchor blockquote | image media table | fullscreen code',

        // image_advtab: true,
        content_css: options.content_css,
        importcss_append: true,
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        // noneditable_noneditable_class: "mceNonEditable",
        toolbar_mode: 'sliding',
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],

        contextmenu: "link image imagetools table configurepermanentpen",
    };

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

        const externalPlugins = [];
        // Load external plugins
        if (typeof options.external_plugins == 'object') {
            for (const pluginId in options.external_plugins) {
                if (!options.external_plugins.hasOwnProperty(pluginId)) {
                    continue;
                }
                const opts = options.external_plugins[pluginId],
                    url = opts.url || null;
                if (url) {
                    externalPlugins.push({
                        'id': pluginId,
                        'url': url
                    });
                    tinymce.PluginManager.load(pluginId, url);
                }
            }
        }

        for (i = 0; i < textareas.length; i++) {
            // Get selected options
            let settings = {...defaults, ...options};
            settings.content_style += '.variable,[data-original-variable]{\n' +
                '    cursor: default;\n' +
                '    background-color: #65b9dd;\n' +
                '    color: #FFF;\n' +
                '    padding: 2px 8px;\n' +
                '    border-radius: 3px;\n' +
                '    font-weight: bold;\n' +
                '    font-style: normal;\n' +
                '    display: inline-block;\n' +
                '}';
            settings.external_plugins = settings.external_plugins || {};
            for (let p = 0; p < externalPlugins.length; p++) {
                settings.external_plugins[externalPlugins[p]['id']] = externalPlugins[p]['url'];
            }

            // workaround for an incompatibility with html5-validation
            if (textareas[i].getAttribute("required") !== '') {
                textareas[i].removeAttribute("required")
            }
            const textAreaId = textareas[i].getAttribute('id');
            if (textAreaId === '' || textAreaId === null) {
                textareas[i].setAttribute("id", "tinymce_" + Math.random().toString(36).substr(2));
            }

            // Add custom buttons to current editor
            if (typeof options.tinymce_buttons == 'object') {
                settings.setup = function(editor) {

                    //icons;
                    for (const iconId in options.tinymce_icons) {
                        if (!options.tinymce_icons.hasOwnProperty(iconId)) continue;

                        (function (id, opts) {
                            editor.ui.registry.addIcon(opts.name_icon, opts.svg_data);
                        })(iconId, clone(options.tinymce_icons[iconId]));
                    }

                    ///buttons
                    for (const buttonId in options.tinymce_buttons) {
                        if (!options.tinymce_buttons.hasOwnProperty(buttonId)) continue;

                        // Some tricky function to isolate variables values
                        (function (id, opts) {
                            opts.onAction = function () {
                                const callback = window['tinymce_button_' + id + '_action'];
                                if (typeof callback == 'function') {
                                    callback(editor);
                                } else {
                                    alert('You have to create callback function: "tinymce_button_' + id + '_action"');
                                }
                            };
                            opts.onSetup = function (buttonApi) {
                                const callback = window['tinymce_button_' + id + '_setup'];
                                if (typeof callback == 'function') {
                                    callback(buttonApi, editor);
                                }
                            }

                            editor.ui.registry.addButton(id, opts);

                        })(buttonId, clone(options.tinymce_buttons[buttonId]));
                    }
                    //Init Event
                    if (options.use_callback_tinymce_init) {
                        editor.on('init', function() {
                            const callback = window['callback_tinymce_init'];
                            if (typeof callback == 'function') {
                                callback(editor);
                            } else {
                                alert('You have to create callback function: callback_tinymce_init');
                            }
                        });
                    }
                }
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

/**
 * Clone object
 *
 * @param obj
 */
function clone(obj) {
    if (!obj || "object" !== typeof obj) {
        return obj;
    }
    let objToReturn = "function" === typeof obj.pop ? [] : {};
    for (const property in obj) {
        if (obj.hasOwnProperty(property)) {
            const value = obj[property];
            if (value && "object" === typeof value) {
                objToReturn[property] = clone(value);
            }
            else objToReturn[property] = value;
        }
    }
    return objToReturn;
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
