jQuery(document).ready(function($) {
    var progress = $('#auction-progress-bar-fill').data('progress');
    $('#auction-progress-bar-fill').css('width', progress + '%').text(progress + '%');
});