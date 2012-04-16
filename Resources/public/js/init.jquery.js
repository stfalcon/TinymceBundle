/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {
    (function ($, undefined) {
        $(function () {

            // Get custom buttons data
            var buttonData, buttonFunction;
            if (typeof options.tinymce_buttons == 'object') {
                for (var buttonId in options.tinymce_buttons) {
                    buttonData = options.tinymce_buttons[buttonId];
                    if (typeof window['tinymce_button_' + buttonId] == 'function') {
                        buttonFunction = window['tinymce_button_' + buttonId];
                    }
                }
            }

            $('textarea' + options.textarea_class).each(function () {
                var $textarea = $(this);

                var theme = $textarea.data('theme') || 'simple';

                // Get selected theme options
                var themeOptions = (typeof options.theme[theme] != 'undefined')
                    ? options.theme[theme]
                    : options.theme['simple'];

                themeOptions.script_url = options.jquery_script_url;

                // workaround for an incompatibility with html5-validation (see: http://git.io/CMKJTw)
                if ($textarea.is('[required]')) {
                    themeOptions.oninit = function (editor) {
                        editor.onChange.add(function (ed, l) {
                            ed.save();
                        });
                    };
                }

                // Add custom buttons to current editor
                if (buttonData && buttonFunction) {
                    themeOptions.setup = function (editor) {
                        editor.addButton(buttonId, $.extend({}, buttonData, {
                            onclick:function () {
                                buttonFunction(editor);
                            }
                        }));
                    }
                }

                $textarea.tinymce(themeOptions);
            });
        });
    }(jQuery));
}