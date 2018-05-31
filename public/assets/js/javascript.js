$('.navbar-toggler').on('click','.fas', function(){
  $(this).toggleClass('fa-bars fa-times');
} );

//   Slick Slider
$('.single-item').slick({
    mobileFirst: true,
    fade: true,
});

var smoothScroll = function(hash) {
    $('html, body').animate({
        scrollTop: $(hash).offset().top - 100
    }, 500, 'easeInOutSine');
}

// Close BS4 navbar on in-page links, and scroll with offset
$('.navbar-nav > li > a').on('click', function(){
    $('.navbar-collapse').collapse('hide');
    smoothScroll($(this).prop('hash'))
});

if (window.location.hash) {
    smoothScroll(window.location.hash)
}
