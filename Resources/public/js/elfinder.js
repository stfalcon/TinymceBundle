function getBrowser(browser, browserTitle) {
    return  (callback, value, meta) => {

        tinymce.activeEditor.activeWindow = tinymce.activeEditor.windowManager.openUrl({
            url: browser,
            title: browserTitle
        });
        tinymce.activeEditor.getFileCallback = (url, name, size, mime) => {
            let info;

            // Make file info
            info = name + ' (' + humanFileSize(size) + ')';

            // Provide file and text for the link dialog
            if (mime.substr(0, 4) === 'file') {
                callback(url, {text: info, title: info});
            }

            // Provide image and alt text for the image dialog
            if (mime.substr(0, 5) === 'image') {
                callback(url, {alt: info});
            }

            // Provide alternative source and posted for the media dialog
            if (mime.substr(0, 5) === 'media') {
                callback(url);
            }
        };

        return false;
    };
}
