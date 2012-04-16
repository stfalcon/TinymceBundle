/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {
    (function ($, undefined) {
        $(function () {
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
                $textarea.tinymce(themeOptions);
            });
        });
    }(jQuery));
}