$('.navbar-toggler').on('click','.fas', function(){
  $(this).toggleClass('fa-bars fa-times');
} );

//   Slick Slider
$('.single-item').slick({
    mobileFirst: true,
    fade: true,
});

// Close BS4 navbar on in-page links, and scroll with offset
$('.navbar-nav > li > a').on('click', function(){
    $('.navbar-collapse').collapse('hide');
    $('html, body').animate({
        scrollTop: $( $(this).prop('hash') ).offset().top - 100
    });
});
