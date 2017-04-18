  //Smooth Scrolling
        $(function () {
            $('a[href*="#"]:not([href="#"])').click(function () {
                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        $('html, body').animate({
                            scrollTop: target.offset().top
                        }, 500);
                        return false;
                    }
                }
            });
        });

        //TopLogoReveal
        $(document).scroll(function () {
            var y = $(this).scrollTop();
            if ($(window).width() > 700) {
                if (y > 500) {
                    $('.appearOnScroll').fadeIn();
                } else {
                    $('.appearOnScroll').fadeOut();
                }

            } else {

            }

        });

        //MenuShowHide
        $(".menuButton").click(function (e) {
            e.stopPropagation();
            $('div.menu').fadeIn(200);
        });
        $('html, body').click(function () {
            $('div.menu').fadeOut(200);
        });


//MixItUp controls
        $('#Container').mixItUp({

            animation: {
                enable: true,
                effects: 'fade stagger(100ms)',
                staggerSequence: function (i) {
                    return i % 3;
                },
                animateChangeLayout: false,

                duration: 300,
                animateResizeContainer: false,

            },
            load: {
               sort: 'random',
               filter: '.featured' 
            }

        });

        //Ajax Load
        $('.modalButton').click(function () {
            $('#modal').load($(this).attr('href'));

            return false;
        });



if ($(window).width() > 700) {//No Modal on Mobile


        //ModalShowHide
        $(".modalButton").click(function (e) {
            e.stopPropagation();
            $('html, body').animate({
                scrollTop: $("#page3").offset().top
            }, 0);
            $('#portfolio').slideUp();
            $('#modal').delay(800).slideDown();
            $('#modalCloseButton').delay(800).fadeIn();

        });
        $('#modalCloseButton').click(function () {
            $('#modal').slideUp();
            $('#modalCloseButton').fadeOut();
            $('#portfolio').delay(800).slideDown();
        });
        

        
           $(window).scroll(function() {
    if ($('#modal').is(':in-viewport')) {

    } else {
               $('#modal').slideUp();
            $('#modalCloseButton').fadeOut();
            $('#portfolio').delay(0).slideDown();
    }
});       

};//No Modal on Mobile

//menuHighlight

           $(window).scroll(function() {
    if ($('#page1').is(':in-viewport')) {
        $('#homeButt').css("border-bottom", "thick solid #fff").fadeTo('slow');

    } else {
        
        $('#homeButt').css("border-bottom", "none").fadeTo('slow');
    }
});      


           $(window).scroll(function() {
    if ($('#page2').is(':in-viewport')) {
        $('#aboutButt').css("border-bottom", "thick solid #fff").fadeTo('slow');

    } else {
        
        $('#aboutButt').css("border-bottom", "none").fadeTo('slow');
    }
});  

           $(window).scroll(function() {
    if ($('#page3').is(':in-viewport')) {
        $('#portButt').css("border-bottom", "thick solid #fff").fadeTo('slow');

    } else {
        
        $('#portButt').css("border-bottom", "none").fadeTo('slow');
    }
});  


           $(window).scroll(function() {
    if ($('#page4').is(':in-viewport')) {
        $('#contButt').css("border-bottom", "thick solid #fff").fadeTo('slow');

    } else {
        
        $('#contButt').css("border-bottom", "none").fadeTo('slow');
    }
});  

//preloader

	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});