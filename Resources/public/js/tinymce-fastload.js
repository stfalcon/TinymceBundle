function tinymce_button_imageuploader(ed) {
    $('#tinymce_file_uploader input[type=file]').click();

    $('#tiny_inner_image').bind('change', function (event) {
        event.stopPropagation();
        event.preventDefault();

        formElement = document.getElementById("tinymce_file_uploader");
        data = new FormData(formElement);

        $.ajax({
            url: $('#tinymce_file_uploader').attr('action'),
            type: 'POST',
            data: data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (typeof(tinymce) != 'undefined') {
                    ed.selection.setContent(response);
                }
            },
            error: function () {
                alert('Whoa! Something goes wrong. Try again later');
            }
        });
    });

    return false;
}