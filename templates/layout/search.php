<div class="block-search">
  <div class="wrapper">
    <div class="d-flex align-items-center justify-content-center search-wrap">
      <a href="javascript:;" class="close-form-search"><i class="fas fa-times"></i></a>
      <form method="get" class="form-search form-search-d">
        <input type="text" class="keyword" placeholder="Nhập từ khóa tìm kiếm">
        <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $(".open-form-search").click(function() {
      $(".block-search").show();
      setTimeout(function() {
        $(".block-search").find("form").addClass("active");
      }, 50);
      $("body").addClass("overflow-hidden");
    });
    $(".close-form-search").click(function() {
      $(".block-search").hide();
      $(".block-search").find("form").removeClass("active");
      $("body").removeClass("overflow-hidden");
    });
  })
</script>