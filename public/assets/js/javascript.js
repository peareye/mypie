$('.navbar-toggler').on('click','.fas', function(){
  console.log('hi')
  $(this).toggleClass('fa-bars fa-times');
} );