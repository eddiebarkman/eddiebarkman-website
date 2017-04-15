
// this is all about the load/splash screen
var arScene = document.querySelector('ar-scene');
var statusMsg = document.querySelector('#status');
var loader = document.querySelector('#loader-wrapper');
statusMsg.innerHTML = "loading argon and aframe...";
var frame = document.querySelector("#frame");

var hudElem = document.querySelector("#lookattarget");
var hudElem2 = hudElem.cloneNode( true );
hudElem2.id = hudElem.id + "2";

var contentRoot = document.querySelector('#logoscene');
var animations = document.querySelectorAll('#logoscene a-animation');
contentRoot.pause();

arScene.addEventListener('argon-initialized', function(evt) {
    statusMsg.innerHTML = "argon initialized, starting vuforia...";
});
arScene.addEventListener('argon-vuforia-initialized', function(evt) {
    statusMsg.innerHTML = "vuforia initialized, downloading dataset...";
});
arScene.addEventListener('argon-vuforia-initialization-failed', function(evt) {
    statusMsg.innerHTML = "vuforia failed to initialize: " + evt.detail.error.message;
});

arScene.addEventListener('argon-vuforia-dataset-loaded', function(evt) {
    statusMsg.innerHTML = "done";
    loader.classList.add('loaded');

	// hudElem.style.display = 'inline-block'; // start hidden
    arScene.hud.appendChild(hudElem, hudElem2);

    frame.addEventListener('referenceframe-statuschanged', function(evt) {
        if (evt.detail.found) {
            hudElem.classList.add("hide");
            hudElem2.classList.add("hide");
	        // hudElem.style.display = 'none'; // hide when target seen
	        // hudElem2.style.display = 'none'; // hide when target seen

            contentRoot.pause();
            contentRoot.setAttribute("visible", false);
            contentRoot.play();

        } else {
            hudElem.classList.remove("hide");
            hudElem2.classList.remove("hide");
	        // hudElem.style.display = 'inline-block'; // show when target lost
	        // hudElem2.style.display = 'inline-block'; // hide when target seen
            contentRoot.pause();
        }
    });

    arScene.addEventListener('target_trigger', function(evt) {
        console.log("TRIGGER: " + (evt.detail.inside ? "ENTERED" : "EXITED"));
    });
});
arScene.addEventListener('argon-vuforia-dataset-load-failed', function(evt) {
    statusMsg.innerHTML = "vuforia failed to load: " + evt.detail.error.message;
});

arScene.addEventListener('argon-vuforia-not-available', function(evt) {
    frame.setAttribute("trackvisibilty", false);
    frame.setAttribute("visible", true);
    frame.setAttribute("position", {x: 0, y: 0, z: -0.5});

    contentRoot.setAttribute("rotation", { x: 0, y: -90, z:0 });

    hudElem.innerHTML = "No Vuforia. Showing scene that would be on the target."
    hudElem.style.display = 'inline-block'; // show when target lost
    arScene.hud.appendChild(hudElem);

    statusMsg.innerHTML = "done";
    loader.classList.add('loaded');
    contentRoot.play();
});

arScene.addEventListener('enter-vr', function (evt) {
    hudElem.classList.add("viewerMode");
    hudElem2.classList.add("viewerMode");
});
arScene.addEventListener('exit-vr', function (evt) {
    hudElem.classList.remove("viewerMode");
    hudElem2.classList.remove("viewerMode");
});




app.vuforia.isAvailable().then(function (available) {
    // vuforia not available on this platform
    if (!available) {
        console.warn("vuforia not available on this platform.");
        return;
    }
    // tell argon to initialize vuforia for our app, using our license information.
    app.vuforia.init({
        encryptedLicenseData: "-----BEGIN PGP MESSAGE-----Version: OpenPGP.js v2.3.2Comment: http://openpgpjs.orgwcFMA+gV6pi+O8zeARAAyVclqE0S3TH7fQ6Qrehx/GcCZTyF3xDivwruyKyTUwKBPGbWb990K7GjCouiQfEb7PzSXP+pkNw/ib01q3cTgRDJKgijGb9rVALfxYr/NY7RDHApnXusDoIB+42alY3hrbI6wYJjlpZJ7Hyv1c+Z3Otw88GMoejqHMRCfy0D5vB9u4SCXGAOTsy3CnLXqGpuokvECDfkNrZgTKO3ED9bBMjHzVxYpjyRU8MamQ1127LfaxsmnELqJi/Btv0sLZVBF1r7XzhO9L/lMvldYxjr3eqd3Cv6uw7kJ1CJXVgfQGXwnYmxDHRhmOMNiT018lxeSbFdipJMEVSV1wFOyaXyt9h68JLEERUv9ImrP9BWkE53V8UAkjIo2fchtJ3IaogyLmAbfCFAhXADHrCYuZAw/fluC1BAh/k1I+2stojtHep44Dr7OWLcJSTJNpGi1Jn9Y8eL94DgB+O9JjtTi2dMx+bu+rRmFI6AtHSWdoyLMo16NtFXyH/qFlthXU+W0fBIgg+ctQor+xhqMCCOvJbib5KxPPaCMYxBke+ln7B0RXctLCdY/It7in7byg7eL+WtVVH+30pEl/lukMoklBf7xpbJhVAUQfjAv64shavgp/wCJ/MFsjmwlsYRol6SBa6nRT2OVRND+GxU3VF5fu3zbQohlhkyHJlGc2Rr8OwpOhrBwU4DAGn1enGTza0QB/4ubKUuKjQya+aa0FcyyRRlqh64y7tEW91zb46YE3Ds1nOMKI6nYotrP93wyCqopg5syDitSZmXI8h4JecPrszkxPAkGGpjAzVgso09kjLNt39MYdgprOz0gwIoLzouE1kwmUJaJuuQ3D3m9MBjnsdenSn6jok1PM9b0Nv6Urt6jIVxyRznGJQVuO3QD/sVW4e4KXwHQ/GRS6IuGyUGRyJKPZzIh5ePKTfRyygs++2w0/zdWq46gQI5lqPvLJ5FqVs6izBE03nqYA72GH4B+wHIIu9J7Q2MCHFVcoIZ2X5csGw6fS57AVMeSdvx+IWJq46ZMUBgxoxbGoCuuXyWCTr5B/9Cd3POCvXMldIfQU4J7Pm2pzFZl/J+HIK/47QnhRBc0OL/pVhYKGFcpAgIK00/WqQNjCEAjj2JTV+z7MzfCvSICrNWQkgYqQa9H4vXYtu4Goxf5zT5MiA0Nu7uwaHdMBEQdyM7tfOb0/+OvbD6YapyKfSSmjXrwZJy8WDr//r9rhLnLDHix7ngB1nUrHNGZni7IN5QNXBDHSN0cE8La9jfIEFb9itBOHi51UqkKxFmbsbhBgC1IjLbBxKQ/KWeaSnIlLJr7TYhn3WYd7nZQTCixVDNOad0CsMkXIAXBEyBrRHvDkI7OJ9N49Hahz0K1dC7XgqzMrgyCPJgYPwBsD7AwcFMA47tt+RhMWHyAQ//S8mNx2U3TVd4lgcyEDMAGmBSd00Uc9C2t/2XDoHiCrwg8ciag39r9L1TnETMX2lQwehWwyq1A9pKXaL1OB2cdSNctlJEGcUsrKRGlSQyHZLuMULZCnlQl9o7odYXRn7/uz33ON8YPc08tiTZ5A8rSTkHnV+FQQiYGAXRxN09+Cgw/JM40pu8QTx5BBMislHe1AgYdj/m7jiLjqHKJ9nsEPSPUukUMkNVKsrOPzfLAEPZhmV5/mnDq1KpA8aY5prCImNVOi9g7/E6uEhJhmuanmhud8rifwaC+eVN3QI6u5qBAUVoMqjnKKlL//fB4hD7bUlQzKGWqEyPFvllfK0qlFBpDvP0lAErGnwzWMlZPJxzQ82in8Za9EyIU7o0WUQektoqGid+tjohsdyIv3oAonAeq/PkqUfwuFK3cMH8vQq+LWKkuAN7eb38X4xMgjxuEqARjS9RBk3iyznxlkBquHzI/BsKoHE4rNuWn+ZMAVLSN77Ln+ZFwFOoZSwcSO7/70uU7U1RvwYSlX562m/vwVwWqAq5w5TDhbvpP/cVPWLJQbGG5sPso1U/FDaBb2zWcQP2Pv97qRCDbHaxPmwKkDrj3hO664OBFRAKJNHw6dEoTAn+D7s6VnBt/jDsDvrgJiu8eRot4z/wX8XC0YFfnsBpQkiCce56rXfzhYLDDxrSwU0BcwOWZwPdaEjebslokWpgMVel65Yn+JAhdWx8CqD5oxg5oShkXejD1t6jvoxCqKJq+MsPS6j2SCgg6a+ctAWBTEr5d3LdqZwOcQwwcIiRrLp+KukHg1Kh6q2AWq4GS1+KMZ/+fMhaIuKEzXTfoWMjBvyMSaZ+m4XcDbofWHQvCXJKCSd7yELHkYSgX76+rHxuHc8woO/Co8mhFtJb1xINvJNZHoz1hI4QpmYWWnfERQzDa1PrhdrLVIAiWYtNPpAti0qY2FnoSl0VZeAruwjeJK4JgQjj8ln0lei6GaOko10mbV22fX9eVBoOd5Kwuk5Fjo2ascEQtQ9UWKnJaC2zWpInz3/3GvRJWKc4h1LtT9zQet4fV8btNlyvSwdN5B5vPkJm1U5wMPrvOkogAI4XyFYUkB7NVnzy/CyQ3DWUpcFHBY2VFbzGlqaQqehu6V+rXXYbCDTaj999/USZ1H5DzFhtGsdlB+ZA6mYPw6vp5+GUpBUGB3h9V1XP94BQ8AsWt2FMVplMLDEnBJgYhtLgXJWNTl2UekrCHEaESHwndEWgDow+sWSPHAq280piS14mhYC6GZ9FjFmxhhMZX+Eo/WHe0cfzr2txNliaLvUwFPgw8Onl2xYVdXxdZF1XUn1rb2I9ViNcPeZdORN1Y/VfEAQsO4q88fIK20DS611oZpKENzODGIw0HKYa/To==cjpK-----END PGP MESSAGE-----"
    }).then(function (api) {
        //
        // the vuforia API is ready, so we can start using it.
        //
    }).catch(function (err) {
        console.log("vuforia failed to initialize: " + err.message);
    });
});
    
