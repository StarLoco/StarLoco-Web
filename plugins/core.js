(function (e) {
    "use strict";
    e("[data-toggle='tooltip']").tooltip();
    e("[data-toggle='popover']").popover();
    e(window).scroll(function () {
        if (e(this).scrollTop() > 40) {
            e("#top").addClass("menu-fixed animated fadeInDown")
        } else {
            e("#top").removeClass("menu-fixed animated fadeInDown")
        }
    });
    e(".modal").on("shown.bs.modal", function (t) {
        var n = e(this).data("effect");
        if (n) {
            e(this).find(".modal-content").addClass("animated " + n + "")
        } else {
            e(this).find(".modal-content").addClass("animated fadeIn")
        }
    });
    e(".modal").on("hidden.bs.modal", function (t) {
        var n = e(this).data("effect");
        if (n) {
            e(this).find(".modal-content").removeClass("animated " + n + "")
        } else {
            e(this).find(".modal-content").removeClass("animated fadeIn")
        }
    });
    e(".bar").click(function () {
        e("nav").toggleClass("animated fadeIn show");
        e("nav ul").first().toggleClass("show")
    });
    e("nav li.dropdown").hover(function () {
        e(this).find(".dropdown-menu").stop(true, true).show;
        e(this).addClass("open")
    }, function () {
        e(this).find(".dropdown-menu").stop(true, true).hide;
        e(this).removeClass("open")
    });
    e("nav .dropdown-submenu").hover(function () {
        e(this).addClass("open")
    }, function () {
        e(this).removeClass("open")
    });
    e(function () {
        var t = e(".video-tab .jcarousel");
        t.jcarousel({wrap: "circular"});
        e(".video-tab .left").jcarouselControl({target: "-=1"});
        e(".video-tab .right").jcarouselControl({target: "+=1"})
    });
    e(function () {
        var t = e(".carousel-tab .jcarousel");
        t.jcarousel({wrap: "circular"});
        t.jcarouselAutoscroll({autostart: true});
        e(".jcarousel-pagination").on("jcarouselpagination:active", "a", function () {
            e(this).addClass("active")
        }).on("jcarouselpagination:inactive", "a", function () {
            e(this).removeClass("active")
        }).on("click", function (e) {
            e.preventDefault()
        }).jcarouselPagination({
            perPage: 1, item: function (e) {
                return '<a href="#' + e + '">' + e + "</a>"
            }
        })
    })
})(jQuery)