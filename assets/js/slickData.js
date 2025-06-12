function fetchSlick(obj) {
  if (!obj.length) {
    return false;
  } else {
    //danh sách config
    //data-split="1/0": phân biệt itemslicks
    //data-show="5": số lượng itemslicks hiển thị
    //data-scroll="1": số lượng itemslicks khi kéo
    //data-drag="1/0": cho phép cuộn chuột
    //data-fade="1/0": hiệu ứng fade khi chỉ hiện 1 itemslicks
    //data-pauseHover="1/0": dừng lại khi rê chuột
    //data-rtl="1/0": chạy từ phải sang trái
    //data-nav="1/0": hiển thị phím qua , nếu bật data-nav thì bật luôn cả data-next, data-prev
    //data-prev="<button type="button" class="slick-prev">Previous</button>": button trở về trước
    //data-next="<button type="button" class="slick-next">Next</button>": button kế tiếp
    //data-wrapArrow=".containerArrow": element sinh ra phím qua lại
    //data-auto="1/0": tự động chạy
    //data-speedAuto="3000": timeout tự động chạy
    //data-speed="500": tốc độ trượt
    //data-loop="1/0":vòng lặp
    //data-vertical: slick đứng
    //data-rows="1": số lượng dòng
    //data-perRows="1": số lượng itemslicks mỗi dòng
    //data-dots="1/0": phân trang
    //data-wrapDots=".containerDots": element sinh ra phân trang
    //data-classDots="customDots": class nút phân trang
    //data-sync=".slick-sub": đồng bộ với slick khác
    //data-lg-items='{"slidesToShow":4,"arrows":false}' : responsive dưới màn hình 991
    //data-md-items='{"slidesToShow":4}' : responsive dưới màn hình 768
    //data-sm-items='{"slidesToShow":4}' : responsive dưới màn hình 576

    var configSlick = {};

    //lấy giá trị
    var splitItem = obj.attr("data-split") ? (parseInt(obj.attr("data-split"))==1 ? true:false) : false;
    var itemShow = obj.attr("data-show") ? parseInt(obj.attr("data-show")) : 1;
    var itemScroll = obj.attr("data-scroll") ? parseInt(obj.attr("data-scroll")) : 1;
    var arrows = obj.attr("data-nav") ? (parseInt(obj.attr("data-nav"))==1 ? true:false) : false;
    var auto = obj.attr("data-auto") ? (parseInt(obj.attr("data-auto"))==1 ? true:false) : false;
    var drag = obj.attr("data-drag") ? (parseInt(obj.attr("data-drag"))==1 ? true:false) : true;
    var fade = obj.attr("data-fade") ? (parseInt(obj.attr("data-fade"))==1 ? true:false) : false;
    var pauseHover = obj.attr("data-pauseHover") ? (parseInt(obj.attr("data-pauseHover")==1) ? true:false) : false;
    var rtl = obj.attr("data-rtl") ? (parseInt(obj.attr("data-rtl"))==1 ? true:false) : false;
    var autoplaySpeed = obj.attr("data-speedAuto") ? parseInt(obj.attr("data-speedAuto")) : 3000;
    var speed = obj.attr("data-speed") ? parseInt(obj.attr("data-speed")) : 500;
    var dots = obj.attr("data-dots") ? (parseInt(obj.attr("data-dots"))==1 ? true:false) : false; 
    var loop = obj.attr("data-loop") ? (parseInt(obj.attr("data-loop"))==1 ? true:false) : true;
    var vertical = obj.attr("data-vertical") ? (parseInt(obj.attr("data-vertical"))==1 ? true:false) : false;
    var rows = obj.attr("data-rows") ? parseInt(obj.attr("data-rows")) : 1;
    var sync = obj.attr("data-sync") ? obj.attr("data-sync") : null;

    var lg_items = obj.attr("data-lg-items") ? obj.attr("data-lg-items") : null;
    var md_items = obj.attr("data-md-items") ? obj.attr("data-md-items") : null;
    var sm_items = obj.attr("data-sm-items") ? obj.attr("data-sm-items") : null;

    //gán giá trị
    configSlick["infinite"] = loop;
    configSlick["autoplay"] = auto;
    configSlick["autoplaySpeed"] = autoplaySpeed;
    configSlick["speed"] = speed;
    configSlick["slidesToShow"] = itemShow;
    configSlick["slidesToScroll"] = itemScroll;
    configSlick["draggable"] = drag;
    configSlick["fade"] = fade;
    configSlick["pauseOnHover"] = pauseHover;
    configSlick["rtl"] = rtl;

    if (vertical == true) {
      configSlick["vertical"] = vertical;
      configSlick["verticalSwiping"] = true;
    }

    if (rows > 1) {
      configSlick["rows"] = rows;
      configSlick["slidesPerRow"] = obj.attr("data-perRows") ? obj.attr("data-perRows") : 1;
    }

    configSlick["arrows"] = arrows;
    if (arrows == true) {
      configSlick["prevArrow"] = obj.attr("data-prev") ? obj.attr("data-prev") : '<button type="button" class="slick-prev">Previous</button>';
      configSlick["nextArrow"] = obj.attr("data-next") ? obj.attr("data-next") : '<button type="button" class="slick-next">Next</button>';
      appendArrows = obj.attr("data-wrapArrow") ? obj.attr("data-wrapArrow") : "";
      if (appendArrows) {
        configSlick["appendArrows"] = appendArrows;
      }
    }

    configSlick["dots"] = dots;
    if (dots == true) {
      wrapDots = obj.attr("data-wrapDots") ? obj.attr("data-wrapDots") : "";
      if (wrapDots) {
        configSlick["appendDots"] = wrapDots;
      }
      configSlick["dotsClass"] = obj.attr("data-classDots") ? obj.attr("data-classDots") : "slick-dots";
      configSlick["customPaging"] =  function (slick, index) {
        return '<button type="button"></button>';
      }
    }

    if (sync) {
      configSlick["asNavFor"] = sync;
    }

    configSlick["responsive"] = null;


    var responsive = [];
    var responsive_item_lg = {},
      responsive_item_md = {},
      responsive_item_sm = {};
    if (lg_items) {
      responsive_item_lg["breakpoint"] = 991;
      responsive_item_lg["settings"] = JSON.parse(lg_items);
      responsive.push(responsive_item_lg);
    }
    if (md_items) {
      responsive_item_md["breakpoint"] = 768;
      responsive_item_md["settings"] = JSON.parse(md_items);
      responsive.push(responsive_item_md);
    }
    if (sm_items) {
      responsive_item_sm["breakpoint"] = 576;
      responsive_item_sm["settings"] = JSON.parse(sm_items);
      responsive.push(responsive_item_sm);
    }

    if (responsive.length) {
      configSlick["responsive"] = responsive;
    }

    // console.log(configSlick);

    obj
      .on("init", function () {
        var $this = $(this);
        $this.addClass("loaded");
        if (splitItem == true) {
          let actives = $this.find(".slick-active");
          for (let i = 0; i < actives.length; i++) {
            let $active = $(actives[i]);
            $active.addClass("active-" + i);
          }
          $this.on("afterChange", function (ev, slick, current, next) {
            let actives = $this.find(".slick-active"),
              direction = current - next,
              slides = slick.$slides,
              index,
              speed = slick.options.speed;
            if (direction == 1 || direction == (slides.length - 1) * -1) {
              index = -1;
            } else if (direction == -1 || direction == slides.length - 1) {
              index = 1;
            }
            for (let i = 0; i < actives.length; i++) {
              let $active = $(actives[i]);
              let el = actives[i],
                prefix = "active-";
              var classes = el.className.split(" ").filter(function (c) {
                return c.lastIndexOf(prefix, 0) !== 0;
              });
              el.className = classes.join(" ").trim();
              $active.addClass("active-" + i);
            }
          });
        }
      })
      .slick(configSlick);
  }
}

$(document).ready(function () {
  if ($(".slick-data").length) {
    $(".slick-data").each(function () {
      fetchSlick($(this));
    });
  }
});