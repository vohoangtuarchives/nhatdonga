<?php

include "config.php";

use Tuezy\Repository\PhotoRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\SettingRepository;
use Tuezy\SecurityHelper;

$photoRepo = new PhotoRepository($d, $cache, $lang, $sluglang);
$newsRepo = new NewsRepository($d, $lang, 'chi-nhanh');
$settingRepo = new SettingRepository($d, $cache);

$type = SecurityHelper::sanitizeGet('type', '');

if ($type == 'video-fotorama') {
	$video_home = $photoRepo->getFeaturedVideos('video');
	
	if (count($video_home)) { ?>
		<div id="fotorama-videos" data-width="100%" data-thumbmargin="10" data-height="330" data-fit="cover" data-thumbwidth="140" data-thumbheight="80" data-allowfullscreen="true" data-nav="thumbs">
			<?php foreach ($video_home as $v) { ?>
				<a href="https://youtube.com/watch?v=<?= $func->getYoutube($v['link_video']) ?>" title="<?= $v['name' . $lang] ?>"></a>
			<?php } ?>
		</div>
	<?php }
}

if ($type == 'video-select') {
	$video_home = $photoRepo->getFeaturedVideos('video');
	
	if (count($video_home)) { ?>
		<div class="video-main">
			<iframe width="100%" height="100%" src="//www.youtube.com/embed/<?= $func->getYoutube($video_home[0]['link_video']) ?>" frameborder="0" allowfullscreen></iframe>
		</div>
		<select class="listvideos">
			<?php foreach ($video_home as $v) { ?>
				<option value="<?= $v['id'] ?>"><?= $v['name' . $lang] ?></option>
			<?php } ?>
		</select>
	<?php }
}

if ($type == 'chi-nhanh') {
	$chi_nhanh = $newsRepo->getByType('chi-nhanh', true, 0, 'numb');
	
	if ($chi_nhanh) { ?>
		<div class="block-chi-nhanh">
			<div class="block-toa-do">
				<div class="scroll-toa-do">
					<h4>HỆ THỐNG CHI NHÁNH</h4>
					<?php foreach ($chi_nhanh as $key => $value) { ?>
						<div class="item-toa-do <?php echo $key == 0 ? "active" : "" ?>">
							<input type="hidden" value="<?php echo $custom->match_iframe_src(htmlspecialchars_decode($value['map'])) ?>">
							<strong class="d-block ten-chi-nhanh"><?php echo $value['name' . $lang] ?> </strong>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="iframe">
				<iframe src="<?php echo $custom->match_iframe_src(htmlspecialchars_decode($chi_nhanh[0]['map'])) ?>" frameborder="0"></iframe>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$("body").on("click", ".item-toa-do", function(event) {
					$(this).addClass('active').siblings().removeClass('active');
					let src = $(this).find("input").val();
					$(".iframe iframe").attr("src", src);
				});
			});
		</script>
	<?php }
}

if ($type == 'video-cbo') {
	$videos = $photoRepo->getVideosByType('video');
	
	if (!empty($videos)) { ?>
		<iframe width="578" height="384" id="player" src="https://www.youtube.com/embed/<?php echo $custom->getIDyoutube($videos[0]['link_video']) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		<select id="cbo_video">
			<?php foreach ($videos as $value) { ?>
				<option value="<?php echo $custom->getIDyoutube($value['link_video']) ?>"><?php echo $value['name' . $lang] ?></option>
			<?php } ?>
		</select>
		<script>
			$(document).ready(function() {
				$("body").on('change', '#cbo_video', function(event) {
					event.preventDefault();
					$("#player").attr("src", "https://www.youtube.com/embed/" + $(this).val() + "?rel=0&showinfo=0&autoplay=1&enablejsapi=1");
				});
			});
		</script>
	<?php }
}

if ($type == 'video-slick') {
	$videos = $photoRepo->getFeaturedVideos('video');
	
	if (!empty($videos)) { ?>
		<div class="video-wrapper">
			<iframe width="435px" height="330px" id="player" src="https://www.youtube.com/embed/<?php echo $custom->getIDyoutube($videos[0]['link_video']) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			<div class="carousel-video">
				<div class="slick-video-addon">
					<?php foreach ($videos as $value) { ?>
						<div class="item-video-addon">
							<div class="yt bg" data-bg="url(<?php echo $custom->getImgYoutube($value['link_video']) ?>)">
								<a href="javascript:void(0);" data-yt-id="<?php echo $custom->getIDyoutube($value['link_video']) ?>" class="full yt-id"></a>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				var ll = new LazyLoad({
					elements_selector: ".yt[data-bg]"
				});
				$(".slick-video-addon").slick({
					slidesToShow: 3,
					slidesToScroll: 1,
					arrows: false,
					dots: false,
					autoplay: true,
					autoplaySpeed: 3000,
					speed: 500,
					vertical: false,
					verticalSwiping: false,
					responsive: [{
						breakpoint: 769,
						settings: {
							slidesToShow: 3,
						}
					}]
				}).on('afterChange', function(slick) {
					ll.update();
				});
				$("body").on('click', '.yt-id', function(event) {
					event.preventDefault();
					$("#player").attr("src", "https://www.youtube.com/embed/" + $(this).attr("data-yt-id") + "?rel=0&showinfo=0&autoplay=1&enablejsapi=1");
				});
			});
		</script>
	<?php }
}

if ($type == 'messages-facebook') {
	$setting = $settingRepo->getFirst();
	$optsetting = !empty($setting['options']) ? json_decode($setting['options'], true) : null;
	?>
	<div class="js-facebook-messenger-box onApp rotate bottom-right cfm rubberBand animated" data-anim="rubberBand">
		<svg id="fb-msng-icon" data-name="messenger icon" xmlns="//www.w3.org/2000/svg" viewBox="0 0 30.47 30.66">
			<path d="M29.56,14.34c-8.41,0-15.23,6.35-15.23,14.19A13.83,13.83,0,0,0,20,39.59V45l5.19-2.86a16.27,16.27,0,0,0,4.37.59c8.41,0,15.23-6.35,15.23-14.19S38,14.34,29.56,14.34Zm1.51,19.11-3.88-4.16-7.57,4.16,8.33-8.89,4,4.16,7.48-4.16Z" transform="translate(-14.32 -14.34)" style="fill:#fff"></path>
		</svg>
		<svg id="close-icon" data-name="close icon" xmlns="//www.w3.org/2000/svg" viewBox="0 0 39.98 39.99">
			<path d="M48.88,11.14a3.87,3.87,0,0,0-5.44,0L30,24.58,16.58,11.14a3.84,3.84,0,1,0-5.44,5.44L24.58,30,11.14,43.45a3.87,3.87,0,0,0,0,5.44,3.84,3.84,0,0,0,5.44,0L30,35.45,43.45,48.88a3.84,3.84,0,0,0,5.44,0,3.87,3.87,0,0,0,0-5.44L35.45,30,48.88,16.58A3.87,3.87,0,0,0,48.88,11.14Z" transform="translate(-10.02 -10.02)" style="fill:#fff"></path>
		</svg>
	</div>
	<div class="js-facebook-messenger-container">
		<div class="js-facebook-messenger-top-header">
			<span><?= $setting['name' . $lang] ?></span>
		</div>
		<div class="fb-page" data-href="<?= $optsetting['fanpage'] ?? '' ?>" data-tabs="messages" data-small-header="true" data-height="300" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
			<blockquote cite="<?= $optsetting['fanpage'] ?? '' ?>" class="fb-xfbml-parse-ignore"><a href="<?= $optsetting['fanpage'] ?? '' ?>">Facebook</a></blockquote>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			$("#messages-facebook").one("DOMSubtreeModified", function() {
				$(".js-facebook-messenger-box").on("click", function() {
					$(".js-facebook-messenger-box, .js-facebook-messenger-container").toggleClass("open"),
					$(".js-facebook-messenger-tooltip").length && $(".js-facebook-messenger-tooltip").toggle();
				}),
				$(".js-facebook-messenger-box").hasClass("cfm") && setTimeout(function() {
					$(".js-facebook-messenger-box").addClass("rubberBand animated");
				}, 3500),
				$(".js-facebook-messenger-tooltip").length && ($(".js-facebook-messenger-tooltip").hasClass("fixed") ?
					$(".js-facebook-messenger-tooltip").show() :
					$(".js-facebook-messenger-box").on("hover", function() {
						$(".js-facebook-messenger-tooltip").show();
					}),
					$(".js-facebook-messenger-close-tooltip").on("click", function() {
						$(".js-facebook-messenger-tooltip").addClass("closed");
					}));
				$(".search_open").click(function() {
					$(".search_box_hide").toggleClass("opening");
				});
			});
		})
	</script>
<?php }

if ($type == 'script-main') { ?>
	<div id="fb-root"></div>
	<script>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s);
			js.id = id;
			js.async = true;
			js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.6";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>
	<script src="//sp.zalo.me/plugins/sdk.js"></script>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-55e11040eb7c994c"></script>
<?php }

