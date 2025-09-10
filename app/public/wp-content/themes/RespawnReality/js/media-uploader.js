jQuery(document).ready(function($){
    var mediaUploader;
    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Odaberi sliku',
            button: {
                text: 'Odaberi sliku'
            }, multiple: false });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#game_image').val(attachment.url);
        });
        mediaUploader.open();
    });
});