(function ($) {
    $(document).ready(function () {
        $('form').on('submit', function () {
            $('form').one('submit', function (event) {
                event.preventDefault();
            });
        });
    });
}(jQuery));


