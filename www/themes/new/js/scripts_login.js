$(function () {
    if (jQuery().slick) {
        $('#login-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            customPaging: function (slider, i) {
                return '<span></span>';
            }
        });
    }
});
