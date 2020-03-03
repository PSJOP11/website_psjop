(function($) {


    $('.multiple-items-sc').slick({
        arrows: php_vars.inner.arrows,
  	  	infinite: php_vars.inner.infinite,
  		slidesToShow: php_vars.inner.slidestoshow,
  		slidesToScroll: php_vars.inner.slidestoscroll,
  		autoplay: php_vars.inner.autoplay,
  		autoplaySpeed: php_vars.inner.autoplay_interval,
        speed: php_vars.inner.speed,
        dots: php_vars.inner.dots,

        responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 3,
            infinite: true,
            dots: false
          }
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
            dots: false,        
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false,
          }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ]
    });
    $('.multiple-items-sc').slickLightbox({
        largesrc: 'largesrc',
        itemSelector: '.item a'
    });
})(jQuery);


