let FileSaver = require('file-saver');


if (typeof trigger_download !== 'undefined') {
    setTimeout(function() {
        FileSaver.saveAs(trigger_download.href);
    }, 150);
}
