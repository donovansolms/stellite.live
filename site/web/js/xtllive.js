/*
  Primary JS for stellite.live
 */
$(document).ready(function(){
  console.log("Ready!");

  // Slideout for side menu
  var slideout = new Slideout({
    'panel': document.getElementById('panel'),
    'menu': document.getElementById('menu'),
    'padding': 256,
    'tolerance': 70
  });
  // Toggle button to open side menu
  $('a.menu-toggle').bind('click', function() {
    $(this).toggleClass('open');
    $(this).find('i.fa').toggleClass('fa-bars');
    $(this).find('i.fa').toggleClass('fa-times');
    slideout.toggle();
  });
});
