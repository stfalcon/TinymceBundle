/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {

    if (typeof tinyMCE == 'undefined') return false;

    var textareas = getElementsByClassName(options.textarea_class, 'textarea');
    for (var i in textareas) {
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
        tinyMCE.execCommand('mceAddControl', true, textarea.id);
    }
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