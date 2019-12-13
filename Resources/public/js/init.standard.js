/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {
    if (typeof tinymce == 'undefined') return false;
    if (typeof options == 'undefined') options = stfalcon_tinymce_config;
    // Load when DOM is ready
    domready(function() {
        let i, t = tinymce.editors, textareas = [];
        for (i in t) {
            if (t.hasOwnProperty(i)) t[i].remove();
        }
        if(Array.isArray(options.selector)) {
            options.selector.forEach(function(selector) {
                textareas = processSelector(selector, textareas);
            });
        } else {
            textareas = processSelector(options.selector, textareas);
        }
        if (!textareas.length) {
            return false;
        }

        let externalPlugins = [];
        // Load external plugins
        if (typeof options.external_plugins == 'object') {
            for (let pluginId in options.external_plugins) {
                if (!options.external_plugins.hasOwnProperty(pluginId)) {
                    continue;
                }
                let opts = options.external_plugins[pluginId],
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
            // Get editor's theme from the textarea data
            let theme = textareas[i].getAttribute("data-theme") || 'simple';
            // Get selected theme options
            let settings = (typeof options.theme[theme] != 'undefined')
                ? options.theme[theme]
                : options.theme['simple'];

            settings.external_plugins = settings.external_plugins || {};
            for (let p = 0; p < externalPlugins.length; p++) {
                settings.external_plugins[externalPlugins[p]['id']] = externalPlugins[p]['url'];
            }
            // workaround for an incompatibility with html5-validation
            if (textareas[i].getAttribute("required") !== '') {
                textareas[i].removeAttribute("required")
            }
            let textAreaId = textareas[i].getAttribute('id');
            if (textAreaId === '' || textAreaId === null) {
                textareas[i].setAttribute("id", "tinymce_" + Math.random().toString(36).substr(2));
            }
            // Add custom buttons to current editor
            if (typeof options.tinymce_buttons == 'object') {
                settings.setup = function(editor) {
                    for (let buttonId in options.tinymce_buttons) {
                        if (!options.tinymce_buttons.hasOwnProperty(buttonId)) continue;

                        // Some tricky function to isolate variables values
                        (function(id, opts) {
                            opts.onclick = function() {
                                var callback = window['tinymce_button_' + id];
                                if (typeof callback == 'function') {
                                    callback(editor);
                                } else {
                                    alert('You have to create callback function: "tinymce_button_' + id + '"');
                                }
                            }
                            editor.addButton(id, opts);

                        })(buttonId, clone(options.tinymce_buttons[buttonId]));
                    }
                    //Init Event
                    if (options.use_callback_tinymce_init) {
                        editor.on('init', function() {
                            let callback = window['callback_tinymce_init'];
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
            tinymce
                .createEditor(textareas[i].getAttribute('id'), settings)
                .render();
        }
    });
}

/**
 * @param selector
 * @param textareas
 */
function processSelector(selector, textareas) {
    switch (selector.substring(0, 1)) {
        case "#":
            let _t = document.getElementById(selector.substring(1));
            if (_t) textareas.push(_t);
            break;
        case ".":
            textareas = textareas.concat(document.getElementsByClassName(selector.substring(1)));
            break;
        default:
            textareas = textareas.concat(document.getElementsByTagName('textarea'));
    }
    return textareas;
}

/**
 * Get elements by class name
 *
 * @param classname
 * @param node
 */
function getElementsByClassName(classname, node) {
    let elements = document.getElementsByTagName(node),
        array = [],
        re = new RegExp('\\b' + classname + '\\b');
    for (let i = 0, j = elements.length; i < j; i++) {
        if (re.test(elements[i].className)) array.push(elements[i]);
    }
    return array;
}

/**
 * Clone object
 *
 * @param o
 */
function clone(o) {
    if (!o || "object" !== typeof o) {
        return o;
    }
    let c = "function" === typeof o.pop ? [] : {}, p, v;
    for (p in o) {
        if (o.hasOwnProperty(p)) {
            v = o[p];
            if (v && "object" === typeof v) {
                c[p] = clone(v);
            }
            else c[p] = v;
        }
    }
    return c;
}
