$(document).ready(function () {
    var $blockPageText = $('#pageText');
    if ($blockPageText.data('page') > 0) {
        $('html, body').animate({ scrollTop: $blockPageText.offset().top - 60 }, 500);
    }
});