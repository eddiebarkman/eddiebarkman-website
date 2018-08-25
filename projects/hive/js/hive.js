 $(document).ready(function() {

            $("#slide1").click(function() {
                $(".hexModal").fadeToggle();
                $("#boxShadow").fadeToggle();
                $("#modal1").show();
                $("#modal2").hide();
                $("#modal3").hide();
                $("#modal4").hide();
                $("#modal5").hide();
            });

            $("#slide2").click(function() {
                $(".hexModal").fadeToggle();
                $("#boxShadow").fadeToggle();
                $("#modal1").hide();
                $("#modal2").show();
                $("#modal3").hide();
                $("#modal4").hide();
                $("#modal5").hide();
            });

            $("#slide3").click(function() {
                $(".hexModal").fadeToggle();
                $("#boxShadow").fadeToggle();
                $("#modal1").hide();
                $("#modal2").hide();
                $("#modal3").show();
                $("#modal4").hide();
                $("#modal5").hide();
            });

            $("#slide4").click(function() {
                $(".hexModal").fadeToggle();
                $("#boxShadow").fadeToggle();
                $("#modal1").hide();
                $("#modal2").hide();
                $("#modal3").hide();
                $("#modal4").show();
                $("#modal5").hide();
            });

            $("#slide5").click(function() {
                $(".hexModal").fadeToggle();
                $("#boxShadow").fadeToggle();
                $("#modal1").hide();
                $("#modal2").hide();
                $("#modal3").hide();
                $("#modal4").hide();
                $("#modal5").show();
            });

            //Shadow for closing modal
            $("#boxShadow").click(function() {
                $(".hexModal").fadeToggle();
                $("#boxShadow").fadeToggle();
            });


        //Menu Toggle  

            $('.bars').click(function() {
                $('#nav').toggleClass('open');
                $('.container').toggleClass('menu-open');
            });

            $('.closeMenuBtn').click(function() {
                $('#nav').toggleClass('open');
                $('.container').toggleClass('menu-open');
            });




        //Smooth Scrolling
        $('a[href*="#"]')
            // Remove links that don't actually link to anything
            .not('[href="#"]')
            .not('[href="#0"]')
            .click(function(event) {
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
                        }, 1000, function() {
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





        var honeycomb = document.querySelector("#honeycomb"),
            mousetrail = document.querySelector("#mousetrail");

        honeycomb.width = window.innerWidth;
        honeycomb.height = window.innerHeight;
        mousetrail.width = window.innerWidth;
        mousetrail.height = window.innerHeight;

        var cxh = honeycomb.getContext("2d"),
            cxm = mousetrail.getContext("2d"),
            w = honeycomb.width,
            h = honeycomb.height,
            yd = 16,
            xd = 2 * yd * Math.sin(Math.PI / 3),
            numHexW = Math.ceil(w / (2 * xd)) + 1,
            numHexH = Math.ceil(h / (3 * yd)) + 1,
            mx,
            my;

        cxh.strokeStyle = "#111";
        cxh.lineWidth = 1;

        function create() {
            var yBase;
            cxh.beginPath();

            for (var i = 0; i < numHexH; i++) {
                yBase = (i * 3 + 2) * yd - i % 2 * yd;
                cxh.moveTo(0, yBase);
                for (var j = 1; j <= numHexW; j++) {
                    if (i % 2) {
                        cxh.lineTo((2 * j - 1) * xd, yBase + yd);
                        cxh.lineTo(2 * j * xd, yBase);
                        cxh.moveTo(2 * j * xd, yBase - 2 * yd);
                        cxh.lineTo(2 * j * xd, yBase);
                    } else {
                        cxh.lineTo((2 * j - 1) * xd, yBase - yd);
                        cxh.lineTo((2 * j - 1) * xd, yBase - 3 * yd);
                        cxh.moveTo((2 * j - 1) * xd, yBase - yd);
                        cxh.lineTo(2 * j * xd, yBase);
                    }
                }
            }
            cxh.stroke();
            cxh.closePath();
        }
        create();

        cxm.strokeStyle = "#111";
        cxm.lineWidth = 1;
        cxm.fillStyle = "#111";

        function makeHoney(mx, my) {
            cxm.clearRect(0, 0, w, h);

            var hexCx, hexCy,
                xl = mx % xd,
                xr = xd - mx % xd,
                yu = my % (3 * yd),
                yb = 3 * yd - my % (3 * yd),
                hlu = Math.sqrt(Math.pow(xl, 2) + Math.pow(yu, 2)),
                hlb = Math.sqrt(Math.pow(xl, 2) + Math.pow(yb, 2)),
                hru = Math.sqrt(Math.pow(xr, 2) + Math.pow(yu, 2)),
                hrb = Math.sqrt(Math.pow(xr, 2) + Math.pow(yb, 2));

            if ((mx % (2 * xd) < xd && my % (6 * yd) < 3 * yd) ||
                (mx % (2 * xd) > xd && my % (6 * yd) >= 3 * yd)) {
                if (hlu < hrb) {
                    hexCx = mx - xl;
                    hexCy = my - yu;
                } else {
                    hexCx = mx + xr;
                    hexCy = my + yb;
                }
            } else {
                if (hlb < hru) {
                    hexCx = mx - xl;
                    hexCy = my + yb;
                } else {
                    hexCx = mx + xr;
                    hexCy = my - yu;
                }
            }
            cxm.moveTo(hexCx - xd, hexCy - yd);
            cxm.beginPath();
            cxm.lineTo(hexCx, hexCy - 2 * yd);
            cxm.lineTo(hexCx + xd, hexCy - yd);
            cxm.lineTo(hexCx + xd, hexCy + yd);
            cxm.lineTo(hexCx, hexCy + 2 * yd);
            cxm.lineTo(hexCx - xd, hexCy + yd);
            cxm.lineTo(hexCx - xd, hexCy - yd);
            cxm.closePath();
            cxm.fill();
            cxm.stroke();
        }

        function init(e) {
            mx = e.clientX;
            my = e.clientY;
            makeHoney(mx, my);
        }

        mousetrail.addEventListener("mousemove", init, false);

        window.addEventListener("resize", function() {
            honeycomb.width = window.innerWidth;
            honeycomb.height = window.innerHeight;
            mousetrail.width = window.innerWidth;
            mousetrail.height = window.innerHeight;
            w = honeycomb.width;
            h = honeycomb.height;
            numHexW = Math.ceil(w / (2 * xd)) + 1;
            numHexH = Math.ceil(h / (3 * yd)) + 1;
            cxh = honeycomb.getContext("2d");
            cxm = mousetrail.getContext("2d");
            cxh.strokeStyle = "#111";
            cxh.lineWidth = 1;
            create();
            cxm.strokeStyle = "#111";
            cxm.lineWidth = 1;
            cxm.fillStyle = "#111";
        });
     
     
     //Go to Top scripts
     
     $(document).ready(function() {
			// Show or hide the sticky footer button
			$(window).scroll(function() {
				if ($(this).scrollTop() > 200) {
					$('.goTop').fadeIn(200);
//                    $("#nav").css("background-color", "#1E2129");
				} else {
					$('.goTop').fadeOut(200);
//                    $("#nav").css("background-color", "transparent");
				}
			});
			
			// Animate the scroll to top
			$('.goTop').click(function(event) {
				event.preventDefault();
				
				$('html, body').animate({scrollTop: 0}, 300);
			})			
         
         $('.goHome').click(function(event) {
				event.preventDefault();
				
				$('html, body').animate({scrollTop: 0}, 300);
			})
		});
     
     
             });
