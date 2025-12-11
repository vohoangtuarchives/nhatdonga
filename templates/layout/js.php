<!-- Js Config -->

<script type="text/javascript">

    var NN_FRAMEWORK = NN_FRAMEWORK || {};

    var CONFIG_BASE = '<?= $configBase ?>';

    var ASSET = '<?= ASSET ?>';

    var WEBSITE_NAME = '<?= (!empty($setting['name' . $lang])) ? addslashes($setting['name' . $lang]) : '' ?>';

    var TIMENOW = '<?= date("d/m/Y", time()) ?>';

    var SHIP_CART = <?= (!empty($config['order']['ship'])) ? 'true' : 'false' ?>;

    var RECAPTCHA_ACTIVE = <?= (!empty($config['googleAPI']['recaptcha']['active'])) ? 'true' : 'false' ?>;

    var RECAPTCHA_SITEKEY = '<?= $config['googleAPI']['recaptcha']['sitekey'] ?>';

    var GOTOP = ASSET + 'assets/images/top.png';

    var LANG = {

        'no_keywords': '<?= chuanhaptukhoatimkiem ?>',

        'delete_product_from_cart': '<?= banmuonxoasanphamnay ?>',

        'no_products_in_cart': '<?= khongtontaisanphamtronggiohang ?>',

        'ward': '<?= phuongxa ?>',

        'back_to_home': '<?= vetrangchu ?>',

    };

    let HOTLINE = '<?php echo $optsetting['phone'] ?>';

    let MESSENGER = '<?php echo $optsetting['fanpage'] ?>';

</script>



<!-- Js Files -->

<?php

$js->set("js/jquery.min.js");

$js->set("js/lazyload.min.js");

$js->set("bootstrap-5.2.3/js/bootstrap.bundle.min.js");

$js->set("js/wow.min.js");

$js->set("confirm/confirm.js");

$js->set("holdon/HoldOn.js");

$js->set("mmenu/mmenu.js");

$js->set("simplenotify/simple-notify.js");

$js->set("fileuploader/jquery.fileuploader.min.js");

$js->set("toc/toc.js");

$js->set("js/slickData.js");

$js->set("js/scrollAnimation.js");

$js->set("js/functions.js");

if ($source == 'product') {
    $js->set('magiczoomplus/magiczoomplus.js');
    $js->set('slick/slick.js');
    $js->set('fotorama/fotorama.js');
    $js->set('owlcarousel2/owl.carousel.js');
    $js->set('fancybox3/jquery.fancybox.js');
} elseif ($source == 'news') {
    $js->set('fancybox3/jquery.fancybox.js');
    $js->set('photobox/photobox.js');
    $js->set('owlcarousel2/owl.carousel.js');
} elseif ($source == 'index') {
    $js->set('owlcarousel2/owl.carousel.js');
    $js->set('slick/slick.js');
    $js->set('flipster/jquery.flipster.min.js');
}

$js->set("js/apps.js");

$js->set("js/cart.js");

$js->set("js/mobile-menu.js");

echo $js->get();

?>



<?php if (!empty($config['googleAPI']['recaptcha']['active'])) { ?>

    <!-- Js Google Recaptcha V3 -->

    <script type="text/javascript">

        var delayJs = function(url, callback, id = "") {

            var script = document.createElement("script");

            script.type = "text/javascript";

            if (script.readyState) {

                script.onreadystatechange = function() {

                    if (

                        script.readyState === "loaded" ||

                        script.readyState === "complete"

                    ) {

                        script.onreadystatechange = null;

                        if (typeof callback == "function") {

                            callback();

                        }

                    }

                };

            } else {

                if (typeof callback == "function") {

                    script.onload = function() {

                        callback();

                    };

                }

            }

            script.src = url;

            if (id) {

                script.setAttribute("id", id);

            }

            document.getElementsByTagName("head")[0].appendChild(script);

        };

        if (RECAPTCHA_SITEKEY != "") {
            var initRecaptcha = function(){
                delayJs(
                    `https://www.google.com/recaptcha/api.js?render=${RECAPTCHA_SITEKEY}`,
                    function() {
                        grecaptcha.ready(function() {
                            generateCaptcha('Newsletter', 'recaptchaResponseNewsletter');
                            <?php if ($source == 'contact') { ?>
                                generateCaptcha('contact', 'recaptchaResponseContact');
                            <?php } ?>
                        });
                    }
                );
            };
            if(document.readyState==='loading'){
                document.addEventListener('DOMContentLoaded', initRecaptcha);
            }else{
                initRecaptcha();
            }
        }

    </script>

<?php } ?>



<?php if (!empty($config['oneSignal']['active'])) { ?>

    <!-- Js OneSignal -->

    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>

    <script type="text/javascript">

        var OneSignal = window.OneSignal || [];

        OneSignal.push(function() {

            OneSignal.init({

                appId: "<?= $config['oneSignal']['id'] ?>"

            });

        });

    </script>

<?php } ?>



<!-- Js Structdata -->

<?php include TEMPLATE . LAYOUT . "strucdata.php"; ?>

<script type="text/javascript">
    (function(){
        function ensureRel(){
            var links=document.querySelectorAll('a[target="_blank"]');
            for(var i=0;i<links.length;i++){
                var rel=links[i].getAttribute('rel')||'';
                if(rel.indexOf('noopener')===-1||rel.indexOf('noreferrer')===-1){
                    links[i].setAttribute('rel','noopener noreferrer');
                }
            }
        }
        if(document.readyState==='loading'){
            document.addEventListener('DOMContentLoaded',ensureRel);
        }else{
            ensureRel();
        }
    })();
</script>



<!-- Js Addons -->

<?= $addons->set('script-main', 'script-main', 2); ?>

<?= $addons->get(); ?>



<!-- Js Body -->

<?= !empty($setting['bodyjs']) ? htmlspecialchars_decode($setting['bodyjs']) : '' ?>



<?php

$popup = $d->rawQueryOne("SELECT photo,link,name$lang FROM table_photo WHERE type='popup' and find_in_set('hienthi',status) LIMIT 0,1");

if (!empty($popup) > 0 &&  $source == 'index') {

    include TEMPLATE . LAYOUT . "popup.php"; ?>

    <script type="text/javascript">

        $(function() {

            setTimeout(function() {

                $("#popupModal").modal("show");

            }, 1000);

        });

    </script>

<?php } ?>

<?php if (isset($config['coppy']['lock']) && $config['coppy']['lock'] == true) { ?>

    <script type="text/javascript">

        eval(function(p, a, c, k, e, d) {

            e = function(c) {

                return c.toString(36)

            };

            if (!''.replace(/^/, String)) {

                while (c--) {

                    d[e(c)] = k[c] || e(c)

                }

                k = [function(e) {

                    return d[e]

                }];

                e = function() {

                    return '\\w+'

                };

                c = 1

            };

            while (c--) {

                if (k[c]) {

                    p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c])

                }

            }

            return p

        }('8.n=6(0){0=(0||a.0);5(0.d===g){7 4}};8.o=6(0){0=(0||a.0);5(0.d===g){7 4}};8.i=6(0){0=(0||a.0);5(0.d===g){7 4}};6 b(){7 4};6 q(e){f h=(j)?e:0;f c=(j)?h.1:h.m;5((c===2)||(c===3))7 4};8.x=b;8.y=b;f 9=4;a.r=6(e){5(e.1===k)9=4};a.i=6(e){5(e.1===k)9=l;5(((e.1===z)||(e.1===w)||(e.1===v)||(e.1===s)||(e.1===t)||(e.1===u))&&9===l){7 4}};9=4;8.p=b;', 36, 36, 'event|which|||false|if|function|return|document|isCtrl|window|contentprotector|eventbutton|keyCode||var|123|myevent|onkeydown|isNS|17|true|button|onkeypress|onmousedown|ondragstart|mousehandler|onkeyup|67|86|83|88|65|oncontextmenu|onmouseup|85'.split('|'), 0, {}))

    </script>

<?php } ?>

<script>

    $(document).ready(function () {

        $(".datlich").click(function(event) {

            $(".dangkynhantin").addClass("open");

            $(".block-menu").addClass("hide");

            $("#header").hide();

        });

        $(".close-dknt").click(function(event) {

            $(".dangkynhantin").removeClass("open");

            $(".block-menu").removeClass("hide");

            $("#header").show();

        });

    })

</script>
<script type="text/javascript">
    (function(){
        var plugins=[];
        <?php if ($source == 'product') { ?>
         
        <?php } elseif ($source == 'news') { ?>

        <?php } elseif ($source == 'index') { ?>

        <?php } ?>
        function loadDefer(src){
            var s=document.createElement('script');
            s.src=ASSET+src;
            s.defer=true;
            document.body.appendChild(s);
        }
        if(document.readyState==='loading'){
            document.addEventListener('DOMContentLoaded',function(){plugins.forEach(loadDefer);});
        }else{
            plugins.forEach(loadDefer);
        }
    })();
</script>
