(function (e, t, n) {
    function i(e) {
        return e
    }

    function s(e) {
        return decodeURIComponent(e.replace(r, " "))
    }

    var r = /\+/g;
    var o = e.cookie = function (r, u, a) {
        if (u !== n) {
            a = e.extend({}, o.defaults, a);
            if (u === null) {
                a.expires = -1
            }
            if (typeof a.expires === "number") {
                var f = a.expires, l = a.expires = new Date;
                l.setDate(l.getDate() + f)
            }
            u = o.json ? JSON.stringify(u) : String(u);
            return t.cookie = [encodeURIComponent(r), "=", o.raw ? u : encodeURIComponent(u), a.expires ? "; expires=" + a.expires.toUTCString() : "", a.path ? "; path=" + a.path : "", a.domain ? "; domain=" + a.domain : "", a.secure ? "; secure" : ""].join("")
        }
        var c = o.raw ? i : s;
        var h = t.cookie.split("; ");
        for (var p = 0, d; d = h[p] && h[p].split("="); p++) {
            if (c(d.shift()) === r) {
                var v = c(d.join("="));
                return o.json ? JSON.parse(v) : v
            }
        }
        return null
    };
    o.defaults = {};
    e.removeCookie = function (t, n) {
        if (e.cookie(t) !== null) {
            e.cookie(t, null, n);
            return true
        }
        return false
    }
})(jQuery, document);
$(document).ready(function () {
    function t(t) {
        $.cookie(e, t);
        $("head link[id=themes]").attr("href", "css/themes/" + t + ".css")
    }

    $("#styleswitcher h2 a").click(function (e) {
        e.preventDefault();
        var t = $("#styleswitcher");
        if (t.css("left") === "0px") {
            $("#styleswitcher").animate({left: "-180px"})
        } else {
            $("#styleswitcher").animate({left: "0px"})
        }
    });
    $(".fullwidth").click(function () {
        $("body").addClass("fullwidth")
    });
    $(".boxed").click(function () {
        $("body").removeClass("fullwidth")
    });
    var e = "themes";
    if ($.cookie(e)) {
        t($.cookie(e))
    }
    $(".green").click(function () {
        $;
        t("green")
    });
    $(".blue").click(function () {
        $;
        t("blue")
    });
    $(".red").click(function () {
        $;
        t("red")
    });
    $(".pink").click(function () {
        $;
        t("pink")
    });
    $(".bg li a").click(function (e) {
        var t = $(this).css("backgroundImage");
        $("body").css("backgroundImage", t);
        return false
    });
    $(".modal-sample").on("click", function () {
        var e = $(this).html();
        var t = '<div class="modal myModalSample" tabindex="-1" data-effect="fadeIn" role="dialog" aria-labelledby="myModalSample" aria-hidden="true"><div class="modal-dialog"><div class="modal-content  animated ' + e + '"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalSampleLabel">' + e + ' modal effect</h4></div><div class="modal-body"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent nec mattis odio. In hac habitasse platea dictumst. Vestibulum volutpat pretium porta. Cras mattis metus non ipsum porttitor pulvinar. Proin tempus erat ac neque gravida suscipit. Morbi faucibus turpis a turpis hendrerit sodales sed vel nisl. Praesent vitae magna luctus, blandit quam eu, semper mi.</p></div></div></div></div>';
        $(this).after(t);
        $(".myModalSample").on("hidden.bs.modal", function (e) {
            $(this).remove()
        })
    })
})