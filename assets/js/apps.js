/* Validation form */
validateForm("validation-newsletter");
validateForm("validation-cart");
validateForm("validation-user");
validateForm("validation-contact");

/* Lazys */
NN_FRAMEWORK.Lazys = function () {
  if (isExist($(".lazy"))) {
    var lazyLoadInstance = new LazyLoad({
      elements_selector: ".lazy",
    });
  }
};

/* Load name input file */
NN_FRAMEWORK.loadNameInputFile = function () {
  if (isExist($(".custom-file input[type=file]"))) {
    $("body").on("change", ".custom-file input[type=file]", function () {
      var fileName = $(this).val();
      fileName = fileName.substr(
        fileName.lastIndexOf("\\") + 1,
        fileName.length
      );
      $(this).siblings("label").html(fileName);
    });
  }
};

/* Back to top */
NN_FRAMEWORK.GoTop = function () {
  $(window).scroll(function () {
    if (!$(".scrollToTop").length)
      $("body").append(
        '<div class="scrollToTop"><img src="' + GOTOP + '" alt="Go Top"/></div>'
      );
    if ($(this).scrollTop() > 100) $(".scrollToTop").fadeIn();
    else $(".scrollToTop").fadeOut();
  });

  $("body").on("click", ".scrollToTop", function () {
    $("html, body").animate({ scrollTop: 0 }, 800);
    return false;
  });
};

/* Alt images */
NN_FRAMEWORK.AltImg = function () {
  $("img").each(function (index, element) {
    if (!$(this).attr("alt") || $(this).attr("alt") == "") {
      $(this).attr("alt", WEBSITE_NAME);
    }
  });
};

/* Menu */
NN_FRAMEWORK.Menu = function () {
  /* Menu remove empty ul */
  if (isExist($(".menu"))) {
    $(".menu ul li a").each(function () {
      $this = $(this);

      if (!isExist($this.next("ul").find("li"))) {
        $this.next("ul").remove();
        $this.removeClass("has-child");
      }
    });
  }

  $(window).scroll(function () {
    if ($(".block-menu").length) {
      if ($(window).scrollTop() >= $(".block-menu").height()) {
        $(".block-menu").addClass(
          "sticky animate__animated animate__fadeInDown"
        );
      } else {
        $(".block-menu").removeClass(
          "sticky animate__animated animate__fadeInDown"
        );
      }
    }

    if ($(`.section-slider`).length) {
      if (
        $(window).scrollTop() >=
        $(`.section-slider`).outerHeight() + $(`.section-slider`).offset().top
      ) {
        $(`.menu-desktop`).addClass("show-second-menu");
      } else {
        $(`.menu-desktop`).removeClass("show-second-menu");
      }
    }
  });

  /* Mmenu */
  if (isExist($("nav#menu"))) {
    $("nav#menu").mmenu({
      extensions: ["border-full", "position-left", "position-front"],
      navbars: [
        {
          position: "top",
          content: ["prev", "title", "close"],
        },
        {
          position: "bottom",
          content: [
            "<a href='tel:" +
              HOTLINE +
              "' class='contact-mmobile'>Gọi điện <i class='fas fa-phone ml-3'></i></a>",
            "<a href='" +
              MESSENGER +
              "' class='contact-mmobile'>Nhắn tin <i class='fab fa-facebook-messenger ml-3'></i></a>",
          ],
        },
      ],
    });
  }
};

/* Wow */
NN_FRAMEWORK.Wows = function () {
  new WOW().init();
};

/* Photobox */
NN_FRAMEWORK.Photobox = function () {
  if (isExist($(".album-gallery"))) {
    $(".album-gallery").photobox("a", { thumbs: true, loop: false });
  }
};

/* Search */
NN_FRAMEWORK.Search = function () {
  if (isExist($(".icon-search"))) {
    $(".icon-search").click(function () {
      if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        $(".search-grid")
          .stop(true, true)
          .animate({ opacity: "0", width: "0px" }, 200);
      } else {
        $(this).addClass("active");
        $(".search-grid")
          .stop(true, true)
          .animate({ opacity: "1", width: "230px" }, 200);
      }
      document.getElementById($(this).next().find("input").attr("id")).focus();
      $(".icon-search i").toggleClass("fa fa-search fa fa-times");
    });
  }

  $("body").on("submit", ".form-search", function (event) {
    event.preventDefault();
    var keywords = $(this).find(".keyword");
    if (keywords.val()) {
      var k = keywords.val();
      window.location.href = encodeURI(`${CONFIG_BASE}tim-kiem?keyword=${k}`);
    } else {
      notifyDialog(LANG.no_keywords);
    }
  });
};

/* Videos */
NN_FRAMEWORK.Videos = function () {
  if (isExist($('[data-fancybox="video"]'))) {
    $('[data-fancybox="video"]').fancybox({
      transitionEffect: "fade",
      transitionDuration: 800,
      animationEffect: "fade",
      animationDuration: 800,
      arrows: true,
      infobar: false,
      toolbar: true,
      hash: false,
    });
  }
};

/* Owl Data */
NN_FRAMEWORK.OwlData = function (obj) {
  if (!isExist(obj)) return false;
  var xsm_items = obj.attr("data-xsm-items");
  var sm_items = obj.attr("data-sm-items");
  var md_items = obj.attr("data-md-items");
  var lg_items = obj.attr("data-lg-items");
  var xlg_items = obj.attr("data-xlg-items");
  var rewind = obj.attr("data-rewind");
  var autoplay = obj.attr("data-autoplay");
  var loop = obj.attr("data-loop");
  var lazyLoad = obj.attr("data-lazyload");
  var mouseDrag = obj.attr("data-mousedrag");
  var touchDrag = obj.attr("data-touchdrag");
  var animations = obj.attr("data-animations");
  var smartSpeed = obj.attr("data-smartspeed");
  var autoplaySpeed = obj.attr("data-autoplayspeed");
  var autoplayTimeout = obj.attr("data-autoplaytimeout");
  var dots = obj.attr("data-dots");
  var nav = obj.attr("data-nav");
  var navText = false;
  var navContainer = false;
  var responsive = {};
  var responsiveClass = true;
  var responsiveRefreshRate = 200;
  var margin = obj.attr("data-margin");

  if (xsm_items != "") {
    xsm_items = xsm_items.split(":");
  }
  if (sm_items != "") {
    sm_items = sm_items.split(":");
  }
  if (md_items != "") {
    md_items = md_items.split(":");
  }
  if (lg_items != "") {
    lg_items = lg_items.split(":");
  }
  if (xlg_items != "") {
    xlg_items = xlg_items.split(":");
  }
  if (rewind == 1) {
    rewind = true;
  } else {
    rewind = false;
  }
  if (autoplay == 1) {
    autoplay = true;
  } else {
    autoplay = false;
  }
  if (loop == 1) {
    loop = true;
  } else {
    loop = false;
  }
  if (lazyLoad == 1) {
    lazyLoad = true;
  } else {
    lazyLoad = false;
  }
  if (mouseDrag == 1) {
    mouseDrag = true;
  } else {
    mouseDrag = false;
  }
  if (animations != "") {
    animations = animations;
  } else {
    animations = false;
  }
  if (smartSpeed > 0) {
    smartSpeed = Number(smartSpeed);
  } else {
    smartSpeed = 800;
  }
  if (autoplaySpeed > 0) {
    autoplaySpeed = Number(autoplaySpeed);
  } else {
    autoplaySpeed = 800;
  }
  if (autoplayTimeout > 0) {
    autoplayTimeout = Number(autoplayTimeout);
  } else {
    autoplayTimeout = 5000;
  }
  if (dots == 1) {
    dots = true;
  } else {
    dots = false;
  }
  if (nav == 1) {
    nav = true;
    navText = obj.attr("data-navtext");
    navContainer = obj.attr("data-navcontainer");

    if (navText != "") {
      navText =
        navText.indexOf("|") > 0 ? navText.split("|") : navText.split(":");
      navText = [navText[0], navText[1]];
    }

    if (navContainer != "") {
      navContainer = navContainer;
    }
  } else {
    nav = false;
  }

  responsive = {
    0: {
      items: Number(xsm_items[0]),
      margin: Number(xsm_items[1]),
    },
    576: {
      items: Number(sm_items[0]),
      margin: Number(sm_items[1]),
    },
    768: {
      items: Number(md_items[0]),
      margin: Number(md_items[1]),
    },
    992: {
      items: Number(lg_items[0]),
      margin: Number(lg_items[1]),
    },
    1200: {
      items: Number(xlg_items[0]),
      margin: Number(xlg_items[1]),
    },
  };

  obj.owlCarousel({
    rewind: rewind,
    autoplay: autoplay,
    loop: loop,
    lazyLoad: lazyLoad,
    mouseDrag: mouseDrag,
    touchDrag: touchDrag,
    smartSpeed: smartSpeed,
    autoplaySpeed: autoplaySpeed,
    autoplayTimeout: autoplayTimeout,
    dots: dots,
    nav: nav,
    navText: navText,
    navContainer: navContainer,
    responsiveClass: responsiveClass,
    responsiveRefreshRate: responsiveRefreshRate,
    responsive: responsive,
  });

  if (autoplay) {
    obj.on("translate.owl.carousel", function (event) {
      obj.trigger("stop.owl.autoplay");
    });

    obj.on("translated.owl.carousel", function (event) {
      obj.trigger("play.owl.autoplay", [autoplayTimeout]);
    });
  }

  if (animations && isExist(obj.find("[owl-item-animation]"))) {
    var animation_now = "";
    var animation_count = 0;
    var animations_excuted = [];
    var animations_list = animations.indexOf(",")
      ? animations.split(",")
      : animations;

    obj.on("changed.owl.carousel", function (event) {
      $(this)
        .find(".owl-item.active")
        .find("[owl-item-animation]")
        .removeClass(animation_now);
    });

    obj.on("translate.owl.carousel", function (event) {
      var item = event.item.index;

      if (Array.isArray(animations_list)) {
        var animation_trim = animations_list[animation_count].trim();

        if (!animations_excuted.includes(animation_trim)) {
          animation_now = "animate__animated " + animation_trim;
          animations_excuted.push(animation_trim);
          animation_count++;
        }

        if (animations_excuted.length == animations_list.length) {
          animation_count = 0;
          animations_excuted = [];
        }
      } else {
        animation_now = "animate__animated " + animations_list.trim();
      }
      $(this)
        .find(".owl-item")
        .eq(item)
        .find("[owl-item-animation]")
        .addClass(animation_now);
    });
  }
};

/* Owl Page */
NN_FRAMEWORK.OwlPage = function () {
  if (isExist($(".owl-page"))) {
    $(".owl-page").each(function () {
      NN_FRAMEWORK.OwlData($(this));
    });
  }
};

NN_FRAMEWORK.Toc = function () {
  if ($(".toc-list").length) {
    $(".toc-list").toc({
      content: "div#toc-content",
      headings: "h2,h3,h4",
    });

    if (!$(".toc-list li").length) $(".meta-toc").hide();

    $(".toc-list")
      .find("a")
      .click(function () {
        var x = $(this).attr("data-rel");
        goToByScroll(x);
      });
  }
};

NN_FRAMEWORK.Add = function () {

};
/* Ready */
$(document).ready(function () {
  NN_FRAMEWORK.Add();
  NN_FRAMEWORK.Lazys();
  NN_FRAMEWORK.Wows();
  NN_FRAMEWORK.AltImg();
  NN_FRAMEWORK.GoTop();
  NN_FRAMEWORK.Menu();
  NN_FRAMEWORK.OwlPage();
  NN_FRAMEWORK.Videos();
  NN_FRAMEWORK.Photobox();
  NN_FRAMEWORK.Search();
  NN_FRAMEWORK.loadNameInputFile();
  NN_FRAMEWORK.Toc();
  if (isExist($(".flipster"))) {
    $(".flipster").flipster({ 
      loop: true,
      style: 'carousel',
      spacing: -0.5,
      scrollwheel: false,
      nav: true,
      buttons: true,
    });
  }
  if (isExist($("#fotorama-videos"))) {
    $("#fotorama-videos").fotorama();
  }

});
