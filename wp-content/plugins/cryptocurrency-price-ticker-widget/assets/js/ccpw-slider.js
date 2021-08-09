jQuery(document).ready(function($){
    $('.slider-row').each(function (index) {
        var columnsToDisplay=$(this).attr('data-display-column');
        var sliderAutoplay=$(this).attr('data-slider-autoplay') ? true: !1;
        $(this).not('.slick-initialized').slick( {
            autoplay: sliderAutoplay, dots: !1, autoplaySpeed: 3000, infinite: !0, slidesToShow: columnsToDisplay, slidesToScroll: 1, prevArrow: '<div class="ccpw-prev"><button type="button" class="ccpw-slick-prev"><i class="ccpw_icon-left"></i></button></div>', nextArrow: '<div class="ccpw-next"><button type="button" class="ccpw-slick-next"><i class="ccpw_icon-right"></i></button></div>', responsive: [ {
                breakpoint: 840, settings: {
                    slidesToShow: 2, slidesToScroll: 1
                }
            }
            , {
                breakpoint: 640, settings: {
                    slidesToShow: 1, slidesToScroll: 1
                }
            }
            ]
        }
        )
    }
    );


});