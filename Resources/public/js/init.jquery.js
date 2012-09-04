/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {
    (function ($, undefined) {
        $(function () {
            $('textarea' + options.textarea_class).each(function () {
                var $textarea = $(this),
                    theme = $textarea.data('theme') || 'simple';

                // Get selected theme options
                var themeOptions = (typeof options.theme[theme] != 'undefined')
                    ? options.theme[theme]
                    : options.theme['simple'];

                themeOptions.script_url = options.jquery_script_url;

                // workaround for an incompatibility with html5-validation (see: http://git.io/CMKJTw)
                if ($textarea.is('[required]')) {
                    themeOptions.oninit = function (editor) {
                        editor.onChange.add(function (ed) {
                            ed.save();
                        });
                    };
                }
                // Add custom buttons to current editor
                if (typeof options.tinymce_buttons == 'object') {
                    themeOptions.setup = function (ed) {
                        $.each(options.tinymce_buttons, function (id, opts) {
                            opts = $.extend({}, opts, {
                                onclick:function () {
                                    var callback = window['tinymce_button_' + id];
                                    if (typeof callback == 'function') {
                                        callback(ed);
                                    } else {
                                        alert('You have to create callback function: "tinymce_button_' + id + '"');
                                    }
                                }
                            });
                            ed.addButton(id, opts);
                        });
                    }
                }

                $textarea.tinymce(themeOptions);
            });
        });
    }(jQuery));
}