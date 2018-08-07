
$('.banner-slide').slick({
    infinite: true,
    loop:true,
    autoplay:true,
    slidesToShow: 1,
    slidesToScroll: 1,
    speed:1000,
    arrows:false,
    dots:true
});
$('.product-slide').slick({
    infinite: true,
    loop:true,
    autoplay:true,
    slidesToShow: 1,
    slidesToScroll: 1,
    speed:700,
    arrows:true,
    dots:false
});


//brand slider
$('.brand-slide').slick({
     dots: false,
     infinite: true,
     autoplay:true,
     speed: 1000,
     slidesToShow: 5,
     slidesToScroll: 1,
     arrows:true,
     responsive: [
       {
         breakpoint: 1024,
         settings: {
           slidesToShow: 3,
           slidesToScroll: 3,
           infinite: true,
           dots: true
         }
       },
       {
         breakpoint: 600,
         settings: {
           slidesToShow: 2,
           slidesToScroll: 2
         }
       },
       {
         breakpoint: 480,
         settings: {
           slidesToShow: 2,
           slidesToScroll: 1
         }
       }
       // You can unslick at a given breakpoint now by adding:
       // settings: "unslick"
       // instead of a settings object
     ]
   });   
  
$(window).scroll(function(){
    if ($(window).scrollTop() >= 50) {
        $('header').addClass('fixed-header');        
    }
    else {
        $('header').removeClass('fixed-header');        
    }
});