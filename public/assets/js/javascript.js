//   Slick Slider
$('.single-item').slick({
    mobileFirst: true,
    fade: true,
});

// Hamburger Animation Credit: http://w3bits.com/animated-hamburger-icons/
var el = document.querySelectorAll('.hamburger');
    for(i=0; i<el.length; i++) {
        el[i].addEventListener('click', function() {
            this.classList.toggle('active');
        }, false);
    }

// Scroll with offset to named anchor
var smoothScroll = function(hash) {
    $('html, body').animate({
        scrollTop: $(hash).offset().top - 45
    }, 500, 'easeInOutSine');
}
// Close BS4 navbar on in-page links
$('.navbar-nav > li > a').on('click', function(){
    $('.navbar-collapse').collapse('hide');
    $('.hamburger').toggleClass('active collapsed');
    smoothScroll($(this).prop('hash'))
});

// If deep linking to a named anchor, scroll to target
if (window.location.hash) {
    smoothScroll(window.location.hash)
}
// Jarallax
objectFitImages();
jarallax(document.querySelectorAll('.jarallax'));
