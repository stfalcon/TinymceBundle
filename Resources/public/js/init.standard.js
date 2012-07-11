/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {

    if (typeof tinyMCE == 'undefined') return false;
    // Load when DOM is ready
    domready(function () {
        var textareas = getElementsByClassName(options.textarea_class, 'textarea'),
            textareasCount = textareas.length,
            currentTextarea,
            buttonData,
            buttonFunction,
            errorCount = 0;
        // Get custom buttons data
        if (typeof options.tinymce_buttons == 'object') {
            for (var buttonId in options.tinymce_buttons) {
                if (!options.tinymce_buttons.hasOwnProperty(buttonId)) continue;
                buttonData = options.tinymce_buttons[buttonId];
                if (typeof window['tinymce_button_' + buttonId] == 'function') {
                    buttonFunction = window['tinymce_button_' + buttonId];
                }
            }
        }
        for (var i = 0; i < textareasCount; i++) {
            // Skip if can't get element
            if (typeof textareas[i] == 'undefined') continue;

            var textarea = textareas[i];
            // Get editor's theme from the textarea data
            var theme = textarea.getAttribute("data-theme") || 'simple';

            // Get selected theme options
            tinyMCE.settings = (typeof options.theme[theme] != 'undefined')
                ? options.theme[theme]
                : options.theme['simple']

            // workaround for an incompatibility with html5-validation (see: http://git.io/CMKJTw)
            if (textarea.getAttribute("required")) {
                tinyMCE.settings.onchange_callback = function (ed) {
                    ed.save();

                }
            }

            // Add custom buttons to current editor
            if (buttonData && buttonFunction) {
                tinyMCE.settings.setup = function (editor) {
                    var thisButtonData = clone(buttonData);
                    thisButtonData.onclick = function () {
                        buttonFunction(editor);
                    }
                    editor.addButton(buttonId, thisButtonData);
                }
            }
            if (textareasCount == 1) {
                currentTextarea = textarea;
            } else if (false === textarea.hasAttribute('id')) {
                errorCount++;
                continue;
            } else {
                currentTextarea = textarea.id;
            }
            tinyMCE.execCommand('mceAddControl', true, currentTextarea);
        }
        if (errorCount) {
            alert("Some of textareas on the page hasn't unique ID attribute! TinyMCE couldn't initialize it.");
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