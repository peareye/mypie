$('.navbar-toggler').on('click','.fas', function(){
  console.log('hi')
  $(this).toggleClass('fa-bars fa-times');
} );

//   Slick Slider
$(document).ready(function() {

  $('.single-item').slick({
   mobileFirst: true,
   fade: true,
  });
  
  })
  