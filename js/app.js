//ThreeJS Scene
"use strict";
/* globals THREE, $, TweenLite, Power3, TimelineMax  */

let camera, scene, renderer;
let plane;
let raycaster = new THREE.Raycaster();
let normalizedMouse = {
    x: 0,
    y: -180
};

let white = {
    r: 255,
    g: 255,
    b: 255
};
let baseColorRGB = white;
let baseColor = "rgb(" + baseColorRGB.r + "," + baseColorRGB.g + "," + baseColorRGB.b + ")";
let nearStars, farStars, farthestStars, nearStars2, farStars2, farthestStars2;


function init() {
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    renderer = new THREE.WebGLRenderer();



    // Scene initialization
    camera.position.z = 10;


    renderer.setClearColor("#0B1F2D", 1.0);
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);

    document.body.appendChild(renderer.domElement);

    // Lights
    let topLight = new THREE.DirectionalLight(0xffffff, 1);
    topLight.position.set(0, 1, 1).normalize();
    scene.add(topLight);

    let bottomLight = new THREE.DirectionalLight(0xeeeeee, 0.4);
    bottomLight.position.set(1, -1, 1).normalize();
    scene.add(bottomLight);

    let skyLightRight = new THREE.DirectionalLight(0xffffff, 0.2);
    skyLightRight.position.set(-1, -1, 0.2).normalize();
    scene.add(skyLightRight);

    let skyLightCenter = new THREE.DirectionalLight(0xeeeeee, 0.2);
    skyLightCenter.position.set(-0, -1, 0.2).normalize();
    scene.add(skyLightCenter);

    let skyLightLeft = new THREE.DirectionalLight(0xffffff, 0.2);
    skyLightLeft.position.set(1, -1, 0.2).normalize();
    scene.add(skyLightLeft);



    // Mesh creation
    let geometry = new THREE.PlaneGeometry(1000, 1000, 100, 100);
    let darkBlueMaterial = new THREE.MeshPhongMaterial({
        color: 0xffffff,
        flatShading: THREE.FlatShading,
        side: THREE.DoubleSide,
        vertexColors: THREE.FaceColors
    });

    geometry.vertices.forEach(function (vertice) {
        vertice.x += (Math.random() - 0.5) * 4;
        vertice.y += (Math.random() - 0.5) * 4;
        vertice.z += (Math.random() - 0.5) * 4;
        vertice.dx = Math.random() - 0.5;
        vertice.dy = Math.random() - 0.5;
        vertice.randomDelay = Math.random() * 5;
    });

    for (var i = 0; i < geometry.faces.length; i++) {
        geometry.faces[i].color.setStyle(baseColor);
        geometry.faces[i].baseColor = baseColorRGB;
    }


    plane = new THREE.Mesh(geometry, darkBlueMaterial);
    scene.add(plane);



    // Create stars 
    farthestStars = createStars(1200, 420, "#A71D33");
    farStars = createStars(1200, 370, "#3BA548");
    nearStars = createStars(1200, 290, "#1E6982");
    farthestStars2 = createStars(1200, -420, "#A71D33");
    farStars2 = createStars(1200, -370, "#3BA548");
    nearStars2 = createStars(1200, -290, "#1E6982");


    scene.add(farthestStars);
    scene.add(farStars);
    scene.add(nearStars)
    scene.add(farthestStars2);
    scene.add(farStars2);
    scene.add(nearStars2)

    farStars.rotation.x = 0.25;
    nearStars.rotation.x = 0.25;
    farStars2.rotation.x = -0.25;
    nearStars2.rotation.x = -0.25;

}


function createStars(amount, yDistance, color = "0x000000") {
    let opacity = Math.random();
    let starGeometry = new THREE.Geometry();
    let starMaterial = new THREE.PointsMaterial({
        color: color,
        opacity: opacity
    });

    for (let i = 0; i < amount; i++) {
        let vertex = new THREE.Vector3();
        vertex.z = (Math.random() - 0.5) * 1500;
        vertex.y = yDistance;
        vertex.x = (Math.random() - 0.5) * 1500;

        starGeometry.vertices.push(vertex);
    }


    return new THREE.Points(starGeometry, starMaterial);
}


let timer = 0;

//var colors = ['#A71D33', '#3BA548', '#1E6982'];
//    colors[Math.floor(Math.random() * colors.length)]

function render() {
    requestAnimationFrame(render);


    timer += 0.01;
    let vertices = plane.geometry.vertices;

    for (let i = 0; i < vertices.length; i++) {
        vertices[i].x -= (Math.sin(timer + vertices[i].randomDelay) / 40) * vertices[i].dx;
        vertices[i].y += (Math.sin(timer + vertices[i].randomDelay) / 40) * vertices[i].dy;
    }

    // Determine where ray is being projected from camera view
    raycaster.setFromCamera(normalizedMouse, camera);

    // Send objects being intersected into a variable
    let intersects = raycaster.intersectObjects([plane]);

    if (intersects.length > 0) {

        let faceBaseColor = intersects[0].face.baseColor;

        plane.geometry.faces.forEach(function (face) {
            face.color.r *= 255;
            face.color.g *= 255;
            face.color.b *= 255;

            face.color.r += (faceBaseColor.r - face.color.r) * 0.01;
            face.color.g += (faceBaseColor.g - face.color.g) * 0.01;
            face.color.b += (faceBaseColor.b - face.color.b) * 0.01;

            let rInt = Math.floor(face.color.r);
            let gInt = Math.floor(face.color.g);
            let bInt = Math.floor(face.color.b);

            let newBasecol = "rgb(" + rInt + "," + gInt + "," + bInt + ")";
            face.color.setStyle(newBasecol);
        });
        plane.geometry.colorsNeedUpdate = true;

        intersects[0].face.color.setStyle('#ffffff');
        plane.geometry.colorsNeedUpdate = true;

    }



    plane.geometry.verticesNeedUpdate = true;
    plane.geometry.elementsNeedUpdate = true;

    farthestStars.rotation.y -= 0.00001;
    farStars.rotation.y -= 0.00005;
    nearStars.rotation.y -= 0.00011;
    farthestStars2.rotation.y -= 0.00001;
    farStars2.rotation.y -= 0.00005;
    nearStars2.rotation.y -= 0.00011;


    renderer.render(scene, camera);
}

init();

window.addEventListener("resize", function () {

    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);

});

window.addEventListener("mousemove", function (event) {

    // Normalize mouse coordinates
    normalizedMouse.x = (event.clientX / window.innerWidth) * 2 - 1;
    normalizedMouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

});

let introContainer = $('.intro-container');
let skyContainer = $('.sky-container');
let xMark = $('.x-mark');


$('.projectsShiftButton').click(function () { // Half page stars half moon
    let introTimeline = new TimelineMax();

    introTimeline.add([
                TweenLite.fromTo(introContainer, 0.5, {
            opacity: 1
        }, {
            opacity: 0,
            ease: Power3.easeIn
        }),
                TweenLite.to(camera.rotation, 3, {
            x: Math.PI / 2,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 3, {
            y: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 2.5, {
            z: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 2.5, {
            z: 20,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 3, {
            y: 120,
            ease: Power3.easeInOut
        }),
                TweenLite.to(plane.scale, 3, {
            x: 2,
            ease: Power3.easeInOut
        }),
            ]);

    introTimeline.add([
                TweenLite.to(xMark, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        }),
                TweenLite.to(skyContainer, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        })
            ]);
});


$('.aboutShiftButton').click(function () { // Angled Star Moon View
    let introTimeline = new TimelineMax();

    introTimeline.add([
                TweenLite.fromTo(introContainer, 0.5, {
            opacity: 1
        }, {
            opacity: 0,
            ease: Power3.easeIn
        }),
                TweenLite.to(camera.rotation, 3, {
            x: -2.1,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 3, {
            y: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 2.5, {
            z: -0.8,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 2.5, {
            z: 20,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 3, {
            y: -120,
            ease: Power3.easeInOut
        }),
                TweenLite.to(plane.scale, 3, {
            x: 2,
            ease: Power3.easeInOut
        }),
            ]);

    introTimeline.add([
                TweenLite.to(xMark, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        }),
                TweenLite.to(skyContainer, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        })
            ]);
});

$('.contactShiftButton').click(function () { // All Stars View with tilt
    let introTimeline = new TimelineMax();

    introTimeline.add([
                TweenLite.fromTo(introContainer, 0.5, {
            opacity: 1
        }, {
            opacity: 0,
            ease: Power3.easeIn
        }),
                TweenLite.to(camera.rotation, 3, {
            x: 2.5,
            ease: Power3.easeInOut
        }), 
        TweenLite.to(camera.rotation, 3, {
            y: -0.2,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 2.5, {
            z: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 2.5, {
            z: 20,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 3, {
            y: 120,
            ease: Power3.easeInOut
        }),
                TweenLite.to(plane.scale, 3, {
            x: 2,
            ease: Power3.easeInOut
        }),
            ]);

    introTimeline.add([
                TweenLite.to(xMark, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        }),
                TweenLite.to(skyContainer, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        })
            ]);
});


$('.zoomShiftButton').click(function () { // Same as home but zoomed out
    let introTimeline = new TimelineMax();

    introTimeline.add([
                TweenLite.fromTo(introContainer, 0.5, {
            opacity: 1
        }, {
            opacity: 0,
            ease: Power3.easeIn
        }),
                TweenLite.to(camera.rotation, 3, {
            x: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 3, {
            y: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 2.5, {
            z: 200,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 3, {
            y: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(plane.scale, 3, {
            x: 2,
            ease: Power3.easeInOut
        }),
            ]);

    introTimeline.add([
                TweenLite.to(xMark, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        }),
                TweenLite.to(skyContainer, 2, {
            opacity: 1,
            ease: Power3.easeInOut
        })
            ]);
});


$('.homeShiftButton').click(function () { // Home extreme closeup
    let outroTimeline = new TimelineMax();

    outroTimeline.add([
                TweenLite.to(xMark, 0.5, {
            opacity: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(skyContainer, 0.5, {
            opacity: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 3, {
            x: 0,
            ease: Power3.easeInOut
        }),
            TweenLite.to(camera.rotation, 3, {
            y: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.rotation, 2.5, {
            z: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 3, {
            z: 10,
            ease: Power3.easeInOut
        }),
                TweenLite.to(camera.position, 2.5, {
            y: 0,
            ease: Power3.easeInOut
        }),
                TweenLite.to(plane.scale, 3, {
            x: 1,
            ease: Power3.easeInOut
        }),
            ]);

    outroTimeline.add([
                TweenLite.to(introContainer, 0.5, {
            opacity: 1,
            ease: Power3.easeIn
        }),
            ]);
});

render();

//Extra Scripts

$.fn.extend({
    animateCss: function (animationName, callback) {
        var animationEnd = (function (el) {
            var animations = {
                animation: 'animationend',
                OAnimation: 'oAnimationEnd',
                MozAnimation: 'mozAnimationEnd',
                WebkitAnimation: 'webkitAnimationEnd',
            };

            for (var t in animations) {
                if (el.style[t] !== undefined) {
                    return animations[t];
                }
            }
        })(document.createElement('div'));

        this.addClass('animated ' + animationName).one(animationEnd, function () {
            $(this).removeClass('animated ' + animationName);

            if (typeof callback === 'function') callback();
        });

        return this;
    },
});

var activePage = 'homeTab';


function homeTab() {
    fadePage();
    toTop();
    $("canvas").css("position", "absolute");
    activePage = 'homeTab';


    $('#circleMenu').animateCss('fadeOut', function () {
        $("#circleMenu").removeClass("animated fadeOut");
        $("#circleMenu").addClass("hidden");

    });

    $('#hamburger-1').animateCss('fadeOut', function () {
        $("#hamburger-1").removeClass("animated fadeOut");
        $("#hamburger-1").addClass("hidden");

    });

    setTimeout(function () {
        $("#homeTabContent").removeClass("hidden");
        $("#homeTabContent").addClass("animated fadeIn");
    }, 3000);

}

function aboutTab() {
    fadePage();
    toTop();
    setTimeout(function () {
        $("#aboutTabContent").removeClass("hidden");
        $("#aboutTabContent").addClass("animated fadeIn");
        openCircleMenu();
        activePage = 'aboutTab';

    }, 3000);
}

function projectsTab() {
    fadePage();
    toTop();
    $("canvas").css("position", "absolute");
    setTimeout(function () {
        $("#projectsTabContent").removeClass("hidden");
        $("#projectsTabContent").addClass("animated fadeIn");
        openCircleMenu();
        activePage = 'projectsTab';
    }, 3000);
}

function contactTab() {
    fadePage();
    toTop();
    $("canvas").css("position", "absolute");
    setTimeout(function () {
        $("#contactTabContent").removeClass("hidden");
        $("#contactTabContent").addClass("animated fadeIn");
        openCircleMenu();
        activePage = 'contactTab';

    }, 3000);
}

function openCircleMenu() {
    if (activePage === 'homeTab') {
        $("#hamburger-1").removeClass("hidden");


        $("#hamburger-1").animateCss('fadeIn', function () {
            $("#hamburger-1").removeClass("animated fadeIn");
            $("#circleMenu").removeClass("hidden");
            $('#circleMenu').animateCss('fadeIn', function () {
                $("#circleMenu").removeClass("animated fadeIn");


            });


        });
    }
}

// Go to top function
function toTop() { 
    $('body,html').animate({
        scrollTop: 0
    }, 500);
}

function toAbout() { 
    $('body,html').animate({
        scrollTop: $("#mobileAbout").offset().top
    }, 500);
}

function toProjects() { 
    $('body,html').animate({
        scrollTop: $("#portfolioTop").offset().top
    }, 500);
}



function fadePage() {

    if (activePage === 'homeTab') {

        $('#homeTabContent').animateCss('fadeOut', function () {
            $("#homeTabContent").removeClass("animated fadeOut");
            $("#homeTabContent").addClass("hidden");
            console.log('Going Home');

        });
    } else if (activePage === 'aboutTab') {

        $('#aboutTabContent').animateCss('fadeOut', function () {
            $("#aboutTabContent").removeClass("animated fadeOut");
            $("#aboutTabContent").addClass("hidden");
            console.log('Going to About');

        });
    } else if (activePage === 'projectsTab') {

        $('#projectsTabContent').animateCss('fadeOut', function () {
            $("#projectsTabContent").removeClass("animated fadeOut");
            $("#projectsTabContent").addClass("hidden");
            console.log('Going to Projects');

        });
    } else if (activePage === 'contactTab') {

        $('#contactTabContent').animateCss('fadeOut', function () {
            $("#contactTabContent").removeClass("animated fadeOut");
            $("#contactTabContent").addClass("hidden");
            console.log('Going to Contact');

        });
    } else {
        console.log('Going nowhere D:')
    }

}


function hideAll() {
    $("#homeTabContent").addClass("hidden");
}

function animateInAll() {
    $('#aboutTabContent').animateCss('fadeIn', function () {
        $("#aboutTabContent").removeClass("animated fadeIn");
        $("#projectsTabContent").removeClass("animated fadeIn");
        $("#contactTabContent").removeClass("animated fadeIn");
        $("#aboutTabContent").removeClass("hidden");
    });

    $('#projectsTabContent').animateCss('fadeIn', function () {
        $("#aboutTabContent").removeClass("animated fadeIn");
        $("#projectsTabContent").removeClass("animated fadeIn");
        $("#contactTabContent").removeClass("animated fadeIn");
        $("#projectsTabContent").removeClass("hidden");
    });

    $('#contactTabContent').animateCss('fadeIn', function () {
        $("#aboutTabContent").removeClass("animated fadeIn");
        $("#projectsTabContent").removeClass("animated fadeIn");
        $("#contactTabContent").removeClass("animated fadeIn");
        $("#contactTabContent").removeClass("hidden");
    });
}

function animateOutAll() {

    $('#aboutTabContent').animateCss('fadeOut', function () {
        $("#aboutTabContent").removeClass("animated fadeOut");
        $("#projectsTabContent").removeClass("animated fadeOut");
        $("#contactTabContent").removeClass("animated fadeOut");
        $("#aboutTabContent").addClass("hidden");
        $("#projectsTabContent").addClass("hidden");
        $("#contactTabContent").addClass("hidden");
    });

    $('#projectsTabContent').animateCss('fadeOut', function () {
        $("#aboutTabContent").removeClass("animated fadeOut");
        $("#projectsTabContent").removeClass("animated fadeOut");
        $("#contactTabContent").removeClass("animated fadeOut");

        $("#aboutTabContent").addClass("hidden");
        $("#projectsTabContent").addClass("hidden");
        $("#contactTabContent").addClass("hidden");
    });

    $('#contactTabContent').animateCss('fadeOut', function () {
        $("#aboutTabContent").removeClass("animated fadeOut");
        $("#projectsTabContent").removeClass("animated fadeOut");
        $("#contactTabContent").removeClass("animated fadeOut");

        $("#aboutTabContent").addClass("hidden");
        $("#projectsTabContent").addClass("hidden");
        $("#contactTabContent").addClass("hidden");
    });
}


$('.circleIcon').click(function () {
    $('#circleMenu').delay(1000).animate({
        'opacity': '1'
    })
    $('#circleMenu').delay(1000).animate({
        'display': 'none'
    })
    $('#circleMenu').animate({
        'background-color': 'red'
    })
    $('#codeSwitch').animate({
        'opacity': '0'
    })


})


//Porfolio Sorter


$(function() {
		var selectedClass = "";
		$(".fil-cat").click(function(){ 
		selectedClass = $(this).attr("data-rel"); 
     $("#portfolio").fadeTo(100, 0.1);
		$("#portfolio figure").not("."+selectedClass).fadeOut().removeClass('scale-anm');
    setTimeout(function() {
      $("."+selectedClass).fadeIn().addClass('scale-anm');
      $("#portfolio").fadeTo(300, 1);
    }, 300); 
		
	});
});




function allSort() {
    $(".sortButton").removeClass("is-active");
    $(".allSortButton").addClass("is-active");
    //            $(".design").removeClass("hidden");
    //            $(".video").removeClass("hidden");
    //            $(".web").removeClass("hidden");
    //            $(".all").addClass("fadeIn");
}

function webSort() {
    $(".sortButton").removeClass("is-active");
    $(".webSortButton").addClass("is-active");
    //            $(".design").addClass("hidden");
    //            $(".video").addClass("hidden");
    //            $(".web").removeClass("hidden");
    //            $(".web").addClass("fadeIn");
}

function designSort() {
    $(".sortButton").removeClass("is-active");
    $(".designSortButton").addClass("is-active");
    //            $(".web").addClass("hidden");
    //            $(".video").addClass("hidden");
    //            $(".design").removeClass("hidden");
    //            $(".design").addClass("fadeIn");
}

function videoSort() {
    $(".sortButton").removeClass("is-active");
    $(".videoSortButton").addClass("is-active");
    //            $(".web").addClass("hidden");
    //            $(".design").addClass("hidden");
    //            $(".video").removeClass("hidden");
    //            $(".video").addClass("fadeIn");
}

function appSort() {
    $(".sortButton").removeClass("is-active");
    $(".appSortButton").addClass("is-active");
    //            $(".web").addClass("hidden");
    //            $(".design").addClass("hidden");
    //            $(".video").addClass("hidden");
    //            $(".app").removeClass("hidden");
    //            $(".app").addClass("fadeIn");
}


//Hamburger Icon
$(document).ready(function () {
    $(".hamburger").click(function () {
        $(this).toggleClass("is-active");
    });
});


//Hamburger Menu
$('.hamburger').on('click', function (e) {
    $(".branch").removeClass("no-animation");
    $('.branch').toggleClass("open");
});

$('.node').on('click', function (e) {
    $(".branch").removeClass("no-animation");
    $('.branch').toggleClass("open");
    $(".hamburger").removeClass("is-active");
});



$(document).ready(function () {
    $('#projectsModal').hide();
});

function modalExpand(urlLink) {
    $("#projectsModal").slideDown();
    $("#projectsModal").load(urlLink);
}

function closePortfolioModal() {
    $("#projectsModal").slideUp();
}

//Smooth scrolling Anchors


//Morphist Text changer

$("#js-rotating").Morphist({
    animateIn: "bounceIn",
    animateOut: "fadeOut",
    speed: 3000
});