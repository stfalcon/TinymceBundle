/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {
    if (typeof tinyMCE == 'undefined') return false;
    // Load when DOM is ready
    domready(function () {
        var textareas,
            err = 0;

        if (options.textarea_class) {
            textareas = getElementsByClassName(options.textarea_class, 'textarea');
        } else {
            textareas = document.getElementsByTagName('textarea');
        }

        // Load external plugins
        if (typeof options.external_plugins == 'object') {
            for (var pluginId in options.external_plugins) {
                if (!options.external_plugins.hasOwnProperty(pluginId)) continue;

                var opts = options.external_plugins[pluginId],
                    url = opts.url || null;
                if (url) {
                    tinymce.PluginManager.load(pluginId, url);
                }
            }
        }

        for (var i = 0; i < textareas.length; i++) {
            // Get editor's theme from the textarea data
            var theme = textareas[i].getAttribute("data-theme") || 'simple';

            // Get selected theme options
            tinyMCE.settings = (typeof options.theme[theme] != 'undefined')
                ? options.theme[theme]
                : options.theme['simple'];

            // workaround for an incompatibility with html5-validation (see: http://git.io/CMKJTw)
            if (textareas[i].getAttribute("required")) {
                tinyMCE.settings.onchange_callback = function (ed) {
                    ed.save();
                }
            }

            // Add custom buttons to current editor
            if (typeof options.tinymce_buttons == 'object') {
                tinyMCE.settings.setup = function (editor) {
                    for (var buttonId in options.tinymce_buttons) {
                        if (!options.tinymce_buttons.hasOwnProperty(buttonId)) continue;

                        // Some tricky function to isolate variables values
                        (function (id, opts) {
                            opts.onclick = function () {
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
                }
            }

            if (textareas.length == 1) {
                // Single textarea, so we can init it without ID attribute
                tinyMCE.execCommand('mceAddControl', true, textareas[i]);
            } else if ((textareas.length > 1) && (false === textareas[i].hasAttribute('id'))) {
                // Skip some textarea without ID which unable to initialize and increase error's counter
                err++;
                continue;
            } else {
                // Initialize textarea by its ID attribute
                tinyMCE.execCommand('mceAddControl', true, textareas[i].getAttribute('id'));
            }

        }

        //Show error message if target elements are invalid
        if (err) alert("Some of textareas on the page hasn't unique ID attribute! TinyMCE couldn't initialize it.");
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
    var c = "function" === typeof o.pop ? [] : {};
    var p, v;
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