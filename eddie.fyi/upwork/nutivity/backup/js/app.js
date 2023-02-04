 // Smooth Scroll
 $('a[href*="#"]')
     // Remove links that don't actually link to anything
     .not('[href="#"]')
     .not('[href="#0"]')
     .click(function (event) {
         // On-page links
         if (
             location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
             location.hostname == this.hostname
         ) {
             // Figure out element to scroll to
             var target = $(this.hash);
             target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
             // Does a scroll target exist?
             if (target.length) {
                 // Only prevent default if animation is actually gonna happen
                 event.preventDefault();
                 $('html, body').animate({
                     scrollTop: target.offset().top
                 }, 1000, function () {
                     // Callback after animation
                     // Must change focus!
                     var $target = $(target);
                     $target.focus();
                     if ($target.is(":focus")) { // Checking if the target was focused
                         return false;
                     } else {
                         $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
                         $target.focus(); // Set focus again
                     };
                 });
             }
         }
     });


 //Parallax Effect for top
 $.fn.moveIt = function () {
     var $window = $(window);
     var instances = [];

     $(this).each(function () {
         instances.push(new moveItItem($(this)));
     });

     window.addEventListener('scroll', function () {
         var scrollTop = $window.scrollTop();
         instances.forEach(function (inst) {
             inst.update(scrollTop);
         });
     }, {
         passive: true
     });
 }

 var moveItItem = function (el) {
     this.el = $(el);
     this.speed = parseInt(this.el.attr('data-scroll-speed'));
 };

 moveItItem.prototype.update = function (scrollTop) {
     this.el.css('transform', 'translateY(' + -(scrollTop / this.speed) + 'px)');
 };

 // Initialize the parallax
 $(function () {
     $('[data-scroll-speed]').moveIt();
 });

 //Initializes the carousels
 $(document).ready(function () {
     var carousels = bulmaCarousel.attach(); // carousels now contains an array of all Carousel instances
 });


 //animates stars

 $('.animate').scrolla();

 particlesJS("particles-js", {
     "particles": {
         "number": {
             "value": 355,
             "density": {
                 "enable": true,
                 "value_area": 789.1476416322727
             }
         },
         "color": {
             "value": "#ffffff"
         },
         "shape": {
             "type": "circle",
             "stroke": {
                 "width": 0,
                 "color": "#000000"
             },
             "polygon": {
                 "nb_sides": 5
             },
             "image": {
                 "src": "img/github.svg",
                 "width": 100,
                 "height": 100
             }
         },
         "opacity": {
             "value": 0.48927153781200905,
             "random": false,
             "anim": {
                 "enable": true,
                 "speed": 2,
                 "opacity_min": 0,
                 "sync": false
             }
         },
         "size": {
             "value": 2.5,
             "random": true,
             "anim": {
                 "enable": true,
                 "speed": 5,
                 "size_min": 0,
                 "sync": false
             }
         },
         "line_linked": {
             "enable": false,
             "distance": 150,
             "color": "#ffffff",
             "opacity": 0.4,
             "width": 1
         },
         "move": {
             "enable": true,
             "speed": 0.2,
             "direction": "none",
             "random": true,
             "straight": false,
             "out_mode": "out",
             "bounce": false,
             "attract": {
                 "enable": false,
                 "rotateX": 600,
                 "rotateY": 1200
             }
         }
     },
     "interactivity": {
         "detect_on": "canvas",
         "events": {
             "onhover": {
                 "enable": false,
                 "mode": "bubble"
             },
             "onclick": {
                 "enable": false,
                 "mode": "push"
             },
             "resize": true
         },
         "modes": {
             "grab": {
                 "distance": 400,
                 "line_linked": {
                     "opacity": 1
                 }
             },
             "bubble": {
                 "distance": 83.91608391608392,
                 "size": 1,
                 "duration": 3,
                 "opacity": 1,
                 "speed": 3
             },
             "repulse": {
                 "distance": 200,
                 "duration": 0.4
             },
             "push": {
                 "particles_nb": 4
             },
             "remove": {
                 "particles_nb": 2
             }
         }
     },
     "retina_detect": true
 });


 //Castmember Change Script
 function clearCastmembers() {
     $("#cast1 ,#cast2 ,#cast3 ,#cast4 ,#cast5 ,#cast6").addClass("hide");
     $("#cast1Button ,#cast2Button ,#cast3Button ,#cast4Button ,#cast5Button ,#cast6Button").removeClass("activeCast");
 }

 $(document).ready(function () {
     //First Castmember Button
     $("#cast1Button").click(function (event) {
         clearCastmembers();
         $("#cast1").removeClass("hide");
         $("#cast1Button").addClass("activeCast");
         $('.animate').scrolla();
     });

     //Second Castmember Button
     $("#cast2Button").click(function (event) {
         clearCastmembers();
         $("#cast2").removeClass("hide");
         $("#cast2Button").addClass("activeCast");
         $('.animate').scrolla();
     });

     //Third Castmember Button
     $("#cast3Button").click(function (event) {
         clearCastmembers();
         $("#cast3").removeClass("hide");
         $("#cast3Button").addClass("activeCast");
         $('.animate').scrolla();
     });

     //Fourth Castmember Button
     $("#cast4Button").click(function (event) {
         clearCastmembers();
         $("#cast4").removeClass("hide");
         $("#cast4Button").addClass("activeCast");
         $('.animate').scrolla();
     });

     //Fifth Castmember Button
     $("#cast5Button").click(function (event) {
         clearCastmembers();
         $("#cast5").removeClass("hide");
         $("#cast5Button").addClass("activeCast");
         $('.animate').scrolla();
     });

     //Sixth Castmember Button
     $("#cast6Button").click(function (event) {
         clearCastmembers();
         $("#cast6").removeClass("hide");
         $("#cast6Button").addClass("activeCast");
         $('.animate').scrolla();
     });


 });

 //Scrolling Controller
 $(function () {
     $.scrollify({
         section: "section",
         sectionName: "section-name",
         interstitialSection: "#page5",
         easing: "easeOutExpo",
         scrollSpeed: 800,
         offset: 0,
         scrollbars: true,
         standardScrollElements: "",
         setHeights: true,
         overflowScroll: true,
         updateHash: false,
         touchScroll: true,
         before: function () {},
         after: function () {},
         afterResize: function () {},
         afterRender: function () {}
     });
 });

 //Mobile Menu
 (function () {
     $('.menu-wrapper').on('click', function () {
         $('.hamburger-menu').toggleClass('animate');
         $('.mobileMenu').slideToggle();

     })
 })();