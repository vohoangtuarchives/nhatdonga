function updateCart(id = 0, code = "", quantity = 1) {
  if (id) {
    var formCart = $(".form-cart");
    var ward = formCart.find(".select-ward-cart").val();

    $.ajax({
      type: "POST",
      url: "api/cart.php",
      dataType: "json",
      data: {
        cmd: "update-cart",
        id: id,
        code: code,
        quantity: quantity,
        ward: ward,
      },
      beforeSend: function () {
        holdonOpen();
      },
      success: function (result) {
        if (result) {
          formCart.find(".load-prices-" + code).html(result.price);
          formCart.find(".load-price-" + code).html(result.regularPrice);
          formCart.find(".load-price-new-" + code).html(result.salePrice);
          formCart.find(".load-price-temp").html(result.tempText);
          formCart.find(".load-price-total").html(result.totalText);
        }
        holdonClose();
      },
    });
  }
}

function deleteCart(obj) {
  var formCart = $(".form-cart");
  var code = obj.data("code");
  var ward = formCart.find(".select-ward-cart").val();

  $.ajax({
    type: "POST",
    url: "api/cart.php",
    dataType: "json",
    data: {
      cmd: "delete-cart",
      code: code,
      ward: ward,
    },
    beforeSend: function () {
      holdonOpen();
    },
    success: function (result) {
      $(".count-cart").html(result.max);
      if (result.max) {
        formCart.find(".load-price-temp").html(result.tempText);
        formCart.find(".load-price-total").html(result.totalText);
        formCart.find(".procart-" + code).remove();
      } else {
        $(".wrap-cart").html(
          '<a href="" class="empty-cart text-decoration-none"><i class="fa fa-cart-arrow-down"></i><p>' +
            LANG["no_products_in_cart"] +
            "</p><span>" +
            LANG["back_to_home"] +
            "</span></a>"
        );
      }
      holdonClose();
    },
  });
}

function loadDistrict(id = 0) {
  $.ajax({
    type: "post",
    url: "api/district.php",
    data: {
      id_city: id,
    },
    beforeSend: function () {
      holdonOpen();
    },
    success: function (result) {
      $(".select-district").html(result);
      $(".select-ward").html('<option value="">' + LANG["ward"] + "</option>");
      holdonClose();
    },
  });
}

function loadWard(id = 0) {
  $.ajax({
    type: "post",
    url: "api/ward.php",
    data: {
      id_district: id,
    },
    beforeSend: function () {
      holdonOpen();
    },
    success: function (result) {
      $(".select-ward").html(result);
      holdonClose();
    },
  });
}

function loadShip(id = 0) {
  if (SHIP_CART) {
    var formCart = $(".form-cart");

    $.ajax({
      type: "POST",
      url: "api/cart.php",
      dataType: "json",
      data: {
        cmd: "ship-cart",
        id: id,
      },
      beforeSend: function () {
        holdonOpen();
      },
      success: function (result) {
        if (result) {
          formCart.find(".load-price-ship").html(result.shipText);
          formCart.find(".load-price-total").html(result.totalText);
        }
        holdonClose();
      },
    });
  }
}

$(document).ready(function () {
  /* Add */
  $("body").on("click", ".addcart", function () {
    $this = $(this);
    $parents = $this.parents(".right-pro-detail");
    var id = $this.data("id");
    var action = $this.data("action");
    var quantity = $parents.find(".quantity-pro-detail").find(".qty-pro").val();
    quantity = quantity ? quantity : 1;
    var color = $parents
      .find(".color-block-pro-detail")
      .find(".color-pro-detail input:checked")
      .val();
    color = color ? color : 0;
    var size = $parents
      .find(".size-block-pro-detail")
      .find(".size-pro-detail input:checked")
      .val();
    size = size ? size : 0;

    if (id) {
      $.ajax({
        url: "api/cart.php",
        type: "POST",
        dataType: "json",
        async: false,
        data: {
          cmd: "add-cart",
          id: id,
          color: color,
          size: size,
          quantity: quantity,
        },
        beforeSend: function () {
          holdonOpen();
        },
        success: function (result) {
          if (action == "addnow") {
            $(".count-cart").html(result.max);
            $.ajax({
              url: "api/cart.php",
              type: "POST",
              dataType: "html",
              async: false,
              data: {
                cmd: "popup-cart",
              },
              success: function (result) {
                $("#popup-cart .modal-body").html(result);
                $("#popup-cart").modal("show");
                if ($("#Modal-Quickview").length) {
                  $("#Modal-Quickview").modal("hide");
                  $("#Modal-Quickview").on("hidden.bs.modal", function () {
                    $(document.body).addClass("modal-open");
                  });
                }
                NN_FRAMEWORK.Lazys();
                holdonClose();
              },
            });
          } else if (action == "buynow") {
            window.location = CONFIG_BASE + "gio-hang";
          }
        },
      });
    }
  });

  /* Delete */
  $("body").on("click", ".del-procart", function () {
    confirmDialog("delete-procart", LANG["delete_product_from_cart"], $(this));
  });

  /* Counter */
  $("body").on("click", ".counter-procart", function () {
    var $button = $(this);
    var quantity = 1;
    var input = $button.parent().find("input");
    var id = input.data("pid");
    var code = input.data("code");
    var oldValue = $button.parent().find("input").val();
    if ($button.text() == "+") quantity = parseFloat(oldValue) + 1;
    else if (oldValue > 1) quantity = parseFloat(oldValue) - 1;
    $button.parent().find("input").val(quantity);
    updateCart(id, code, quantity);
  });

  /* Quantity */
  $("body").on("change", "input.quantity-procart", function () {
    var quantity = $(this).val() < 1 ? 1 : $(this).val();
    $(this).val(quantity);
    var id = $(this).data("pid");
    var code = $(this).data("code");
    updateCart(id, code, quantity);
  });

  /* Quickview */
  $("body").on("click",".viewnow-product", function(){
    let id = $(this).attr("data-id");
    $.ajax({
      url: "api/quickview.php",
      method: "get",
      data: {
        id: id,
      },
      success: function (data) {
        $(".modal-quickview").html(data);
        $("#Modal-Quickview").modal("show");
        if (isExist($(".owl-page"))) {
          $(".owl-page").each(function () {
            NN_FRAMEWORK.OwlData($(this));
          });
          MagicZoom.refresh("Zoom-1");
          NN_FRAMEWORK.OwlData($(".owl-pro-detail"));
          NN_FRAMEWORK.Lazys();
        }
        if (isExist($(".quantity-pro-detail span"))) {
          $(".quantity-pro-detail span").click(function () {
            var $button = $(this);
            var oldValue = $button.parent().find("input").val();
            if ($button.text() == "+") {
              var newVal = parseFloat(oldValue) + 1;
            } else {
              if (oldValue > 1) var newVal = parseFloat(oldValue) - 1;
              else var newVal = 1;
            }
            $button.parent().find("input").val(newVal);
          });
        }
      },
    });
  });

  /* City */
  if (isExist($(".select-city-cart"))) {
    $(".select-city-cart").change(function () {
      var id = $(this).val();
      loadDistrict(id);
      loadShip();
    });
  };

  /* District */
  if (isExist($(".select-district-cart"))) {
    $(".select-district-cart").change(function () {
      var id = $(this).val();
      loadWard(id);
      loadShip();
    });
  };

  /* Ward */
  if (isExist($(".select-ward-cart"))) {
    $(".select-ward-cart").change(function () {
      var id = $(this).val();
      loadShip(id);
    });
  };

  /* Payments */
  if (isExist($(".payments-label"))) {
    $(".payments-label").click(function () {
      var payments = $(this).data("payments");
      $(".payments-cart .payments-label, .payments-info").removeClass("active");
      $(this).addClass("active");
      $(".payments-info-" + payments).addClass("active");
    });
  };

  /* Colors */
  if (isExist($(".color-pro-detail"))) {
    $(".color-pro-detail input").click(function () {
      $this = $(this).parents("label.color-pro-detail");
      $parents = $this.parents(".attr-pro-detail");
      $parents_detail = $this.parents(".grid-pro-detail");
      $parents.find(".color-block-pro-detail").find(".color-pro-detail").removeClass("active");
      $parents.find(".color-block-pro-detail").find(".color-pro-detail input").prop("checked", false);
      $this.addClass("active");
      $this.find("input").prop("checked", true);
      var id_color = $parents.find(".color-block-pro-detail").find(".color-pro-detail input:checked").val();
      var id_pro = $this.data("idproduct");

      $(".size-pro-detail").each(function () {
        if ($(this).hasClass("active")) {
          let id_size2 = $(this).find("input").val();
          $.ajax({
            url: "api/cart.php",
            type: "POST",
            data: {
              size: id_size2,
              color: id_color,
              id: id_pro,
              cmd: "get-price",
            },
            success: function (data) {
              $(".append-price").html("");
              $(".append-price").html(data);
            },
          });
        }
      });

      $.ajax({
        url: "api/color.php",
        type: "POST",
        dataType: "html",
        data: {
          id_color: id_color,
          id_pro: id_pro,
        },
        beforeSend: function () {
          holdonOpen();
        },
        success: function (result) {
          if (result) {
            $parents_detail.find(".left-pro-detail").html(result);
            MagicZoom.refresh("Zoom-1");
            NN_FRAMEWORK.OwlData($(".owl-pro-detail"));
            NN_FRAMEWORK.Lazys();
          }
          holdonClose();
        },
      });
    });
  };

  /* Sizes */
  if (isExist($(".size-pro-detail"))) {
    $(".size-pro-detail input").click(function () {
      $this = $(this).parents("label.size-pro-detail");
      $parents = $this.parents(".attr-pro-detail");
      $parents.find(".size-block-pro-detail").find(".size-pro-detail").removeClass("active");
      $parents.find(".size-block-pro-detail").find(".size-pro-detail input").prop("checked", false);
      $this.addClass("active");
      $this.find("input").prop("checked", true);
      let id_size2 = $(this).val();
      let id_pro = $(this).parents(".size-pro-detail").attr("data-idproduct");
      $(".color-pro-detail").each(function () {
        if ($(this).hasClass("active")) {
          let id_color2 = $(this).find("input").val();
          $.ajax({
            url: "api/cart.php",
            type: "POST",
            data: {
              size: id_size2,
              color: id_color2,
              id: id_pro,
              cmd: "get-price",
            },
            success: function (data) {
              $(".append-price").html("");
              $(".append-price").html(data);
            },
          });
        }
      });
    });
  };

  /* Quantity detail page */
  if (isExist($(".quantity-pro-detail span"))) {
    $(".quantity-pro-detail span").click(function () {
      var $button = $(this);
      var oldValue = $button.parent().find("input").val();
      if ($button.text() == "+") {
        var newVal = parseFloat(oldValue) + 1;
      } else {
        if (oldValue > 1) var newVal = parseFloat(oldValue) - 1;
        else var newVal = 1;
      }
      $button.parent().find("input").val(newVal);
    });
  }

});