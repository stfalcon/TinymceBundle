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
        var i, t = tinymce.editors, textareas = [];
        for (i in t) {
            if (t.hasOwnProperty(i)) t[i].remove();
        }
        switch (options.selector.substring(0, 1)) {
            case "#":
                var _t = document.getElementById(options.selector.substring(1));
                if (_t) textareas.push(_t);
                break;
            case ".":
                textareas = getElementsByClassName(options.selector.substring(1), 'textarea');
                break;
            default :
                textareas = document.getElementsByTagName('textarea');
        }
        if (!textareas.length) {
            return false;
        }

        var externalPlugins = [];
        // Load external plugins
        if (typeof options.external_plugins == 'object') {
            for (var pluginId in options.external_plugins) {
                if (!options.external_plugins.hasOwnProperty(pluginId)) {
                    continue;
                }
                var opts = options.external_plugins[pluginId],
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
            var theme = textareas[i].getAttribute("data-theme") || 'simple';
            // Get selected theme options
            var settings = (typeof options.theme[theme] != 'undefined')
                ? options.theme[theme]
                : options.theme['simple'];

            settings.external_plugins = settings.external_plugins || {};
            for (var p = 0; p < externalPlugins.length; p++) {
                settings.external_plugins[externalPlugins[p]['id']] = externalPlugins[p]['url'];
            }
            // workaround for an incompatibility with html5-validation
            if (textareas[i].getAttribute("required") !== '') {
                textareas[i].removeAttribute("required")
            }
            var textAreaId = textareas[i].getAttribute('id');
            if (textAreaId === '' || textAreaId === null) {
                textareas[i].setAttribute("id", "tinymce_" + Math.random().toString(36).substr(2));
            }
            // Add custom buttons to current editor
            if (typeof options.tinymce_buttons == 'object') {
                settings.setup = function(editor) {
                    for (var buttonId in options.tinymce_buttons) {
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
                            var callback = window['callback_tinymce_init'];
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
 * Get elements by class name
 *
 * @param classname
 * @param node
 */
function getElementsByClassName(classname, node) {
    var elements = document.getElementsByTagName(node),
        array = [],
        re = new RegExp('\\b' + classname + '\\b');
    for (var i = 0, j = elements.length; i < j; i++) {
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
    var c = "function" === typeof o.pop ? [] : {}, p, v;
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
