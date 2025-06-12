<div id="arcontactus"></div>
<script type="text/javascript">
    $(function() {
        var Accordion = function(el, multiple) {
            this.el = el || {};
            this.multiple = multiple || false;
            // Variables privadas
            var links = this.el.find('.link');
            // Evento
            links.on('click', {
                el: this.el,
                multiple: this.multiple
            }, this.dropdown)
        }
        Accordion.prototype.dropdown = function(e) {
            var $el = e.data.el;
            $this = $(this),
                $next = $this.next();
            $next.slideToggle();
            $this.parent().toggleClass('open');
            if (!e.data.multiple) {
                $el.find('.submenu').not($next).slideUp().parent().removeClass('open');
            };
        }
        var accordion = new Accordion($('#accordion'), false);
    });
    var zaloWidgetInterval;
    var tawkToInterval;
    var skypeWidgetInterval;
    var lcpWidgetInterval;
    var closePopupTimeout;
    var lzWidgetInterval;
    var arCuMessages = ["Xin Ch\u00e0o!", "B\u1ea1n c\u1ea7n gi\u00fap g\u00ec kh\u00f4ng?", "H\u00e3y li\u00ean h\u1ec7 v\u1edbi ch\u00fang t\u00f4i<br \/>\n\u0111\u1ec3 \u0111\u01b0\u1ee3c t\u01b0 v\u1ea5n!!!"];
    var arCuLoop = false;;
    var arCuCloseLastMessage = false;
    var arCuPromptClosed = false;
    var _arCuTimeOut = null;
    var arCuDelayFirst = 2000;
    var arCuTypingTime = 2000;
    var arCuMessageTime = 4000;
    var arCuClosedCookie = 0;
    var arcItems = [];

    window.addEventListener('load', function() {
        arCuClosedCookie = arCuGetCookie('arcu-closed');
        jQuery('#arcontactus').on('arcontactus.init', function() {
            if (arCuClosedCookie) {
                return false;
            }
            arCuShowMessages();
        });

        jQuery('#arcontactus').on('arcontactus.openMenu', function() {
            clearTimeout(_arCuTimeOut);
            if (!arCuPromptClosed) {
                arCuPromptClosed = true;
                jQuery('#arcontactus').contactUs('hidePrompt');
            }
        });

        jQuery('#arcontactus').on('arcontactus.openCallbackPopup', function() {
            clearTimeout(_arCuTimeOut);
            if (!arCuPromptClosed) {
                arCuPromptClosed = true;
                jQuery('#arcontactus').contactUs('hidePrompt');
            }
        });



        jQuery('#arcontactus').on('arcontactus.hidePrompt', function() {
            clearTimeout(_arCuTimeOut);
            if (arCuClosedCookie != "1") {
                arCuClosedCookie = "1";
                arCuPromptClosed = true;
                arCuCreateCookie('arcu-closed', 1, 0);
            }
        });



        var arcItem = {};
        arcItem.id = 'msg-item-1';
        arcItem.class = 'msg-item-facebook-messenger';
        arcItem.title = 'Messenger';
        arcItem.icon = '<svg xmlns="//www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 32C15.9 32-77.5 278 84.6 400.6V480l75.7-42c142.2 39.8 285.4-59.9 285.4-198.7C445.8 124.8 346.5 32 224 32zm23.4 278.1L190 250.5 79.6 311.6l121.1-128.5 57.4 59.6 110.4-61.1-121.1 128.5z"></path></svg>';
        arcItem.href = '<?= $optsetting['fanpage'] ?>';
        arcItem.color = '#02A2FF';
        arcItems.push(arcItem);


        var arcItem = {};
        arcItem.id = 'msg-item-2';
        arcItem.class = 'msg-item-phone';
        arcItem.title = 'Call <?= $optsetting['hotline'] ?>';
        arcItem.icon = '<svg xmlns="//www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg>';
        arcItem.href = 'tel:<?= preg_replace('/[^0-9]/', '', $optsetting['hotline']); ?>';
        arcItem.color = '#4EB625';
        arcItems.push(arcItem);


        var arcItem = {};
        arcItem.id = 'msg-item-3';
        arcItem.class = 'msg-item-phone';
        arcItem.title = 'Chỉ đường';
        arcItem.icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path fill="currentColor" d="M248 8C111.03 8 0 119.03 0 256s111.03 248 248 248 248-111.03 248-248S384.97 8 248 8zm-11.34 240.23c-2.89 4.82-8.1 7.77-13.72 7.77h-.31c-4.24 0-8.31 1.69-11.31 4.69l-5.66 5.66c-3.12 3.12-3.12 8.19 0 11.31l5.66 5.66c3 3 4.69 7.07 4.69 11.31V304c0 8.84-7.16 16-16 16h-6.11c-6.06 0-11.6-3.42-14.31-8.85l-22.62-45.23c-2.44-4.88-8.95-5.94-12.81-2.08l-19.47 19.46c-3 3-7.07 4.69-11.31 4.69H50.81C49.12 277.55 48 266.92 48 256c0-110.28 89.72-200 200-200 21.51 0 42.2 3.51 61.63 9.82l-50.16 38.53c-5.11 3.41-4.63 11.06.86 13.81l10.83 5.41c5.42 2.71 8.84 8.25 8.84 14.31V216c0 4.42-3.58 8-8 8h-3.06c-3.03 0-5.8-1.71-7.15-4.42-1.56-3.12-5.96-3.29-7.76-.3l-17.37 28.95zM408 358.43c0 4.24-1.69 8.31-4.69 11.31l-9.57 9.57c-3 3-7.07 4.69-11.31 4.69h-15.16c-4.24 0-8.31-1.69-11.31-4.69l-13.01-13.01a26.767 26.767 0 0 0-25.42-7.04l-21.27 5.32c-1.27.32-2.57.48-3.88.48h-10.34c-4.24 0-8.31-1.69-11.31-4.69l-11.91-11.91a8.008 8.008 0 0 1-2.34-5.66v-10.2c0-3.27 1.99-6.21 5.03-7.43l39.34-15.74c1.98-.79 3.86-1.82 5.59-3.05l23.71-16.89a7.978 7.978 0 0 1 4.64-1.48h12.09c3.23 0 6.15 1.94 7.39 4.93l5.35 12.85a4 4 0 0 0 3.69 2.46h3.8c1.78 0 3.35-1.18 3.84-2.88l4.2-14.47c.5-1.71 2.06-2.88 3.84-2.88h6.06c2.21 0 4 1.79 4 4v12.93c0 2.12.84 4.16 2.34 5.66l11.91 11.91c3 3 4.69 7.07 4.69 11.31v24.6z"/></svg>';
        arcItem.href = '<?= $optsetting['coords'] ?>';
        arcItem.color = '#02a2ff';
        arcItems.push(arcItem);

        var arcItem = {};
        arcItem.id = 'msg-item-4';
        arcItem.class = 'msg-item-zalo';
        arcItem.title = "Zalo <?= $optsetting['zalo'] ?>";
        arcItem.icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.1 436.6"><path fill="currentColor" class="st0" d="M82.6 380.9c-1.8-.8-3.1-1.7-1-3.5 1.3-1 2.7-1.9 4.1-2.8 13.1-8.5 25.4-17.8 33.5-31.5 6.8-11.4 5.7-18.1-2.8-26.5C69 269.2 48.2 212.5 58.6 145.5 64.5 107.7 81.8 75 107 46.6c15.2-17.2 33.3-31.1 53.1-42.7 1.2-.7 2.9-.9 3.1-2.7-.4-1-1.1-.7-1.7-.7-33.7 0-67.4-.7-101 .2C28.3 1.7.5 26.6.6 62.3c.2 104.3 0 208.6 0 313 0 32.4 24.7 59.5 57 60.7 27.3 1.1 54.6.2 82 .1 2 .1 4 .2 6 .2H290c36 0 72 .2 108 0 33.4 0 60.5-27 60.5-60.3v-.6-58.5c0-1.4.5-2.9-.4-4.4-1.8.1-2.5 1.6-3.5 2.6-19.4 19.5-42.3 35.2-67.4 46.3-61.5 27.1-124.1 29-187.6 7.2-5.5-2-11.5-2.2-17.2-.8-8.4 2.1-16.7 4.6-25 7.1-24.4 7.6-49.3 11-74.8 6zm72.5-168.5c1.7-2.2 2.6-3.5 3.6-4.8 13.1-16.6 26.2-33.2 39.3-49.9 3.8-4.8 7.6-9.7 10-15.5 2.8-6.6-.2-12.8-7-15.2-3-.9-6.2-1.3-9.4-1.1-17.8-.1-35.7-.1-53.5 0-2.5 0-5 .3-7.4.9-5.6 1.4-9 7.1-7.6 12.8 1 3.8 4 6.8 7.8 7.7 2.4.6 4.9.9 7.4.8 10.8.1 21.7 0 32.5.1 1.2 0 2.7-.8 3.6 1-.9 1.2-1.8 2.4-2.7 3.5-15.5 19.6-30.9 39.3-46.4 58.9-3.8 4.9-5.8 10.3-3 16.3s8.5 7.1 14.3 7.5c4.6.3 9.3.1 14 .1 16.2 0 32.3.1 48.5-.1 8.6-.1 13.2-5.3 12.3-13.3-.7-6.3-5-9.6-13-9.7-14.1-.1-28.2 0-43.3 0zm116-52.6c-12.5-10.9-26.3-11.6-39.8-3.6-16.4 9.6-22.4 25.3-20.4 43.5 1.9 17 9.3 30.9 27.1 36.6 11.1 3.6 21.4 2.3 30.5-5.1 2.4-1.9 3.1-1.5 4.8.6 3.3 4.2 9 5.8 14 3.9 5-1.5 8.3-6.1 8.3-11.3.1-20 .2-40 0-60-.1-8-7.6-13.1-15.4-11.5-4.3.9-6.7 3.8-9.1 6.9zm69.3 37.1c-.4 25 20.3 43.9 46.3 41.3 23.9-2.4 39.4-20.3 38.6-45.6-.8-25-19.4-42.1-44.9-41.3-23.9.7-40.8 19.9-40 45.6zm-8.8-19.9c0-15.7.1-31.3 0-47 0-8-5.1-13-12.7-12.9-7.4.1-12.3 5.1-12.4 12.8-.1 4.7 0 9.3 0 14v79.5c0 6.2 3.8 11.6 8.8 12.9 6.9 1.9 14-2.2 15.8-9.1.3-1.2.5-2.4.4-3.7.2-15.5.1-31 .1-46.5z"/></svg>';
        arcItem.href = 'https://zalo.me/<?= str_replace(" ", "", $optsetting['zalo'])  ?>';
        arcItem.color = '#1456FF';
        arcItems.push(arcItem);

        jQuery('#arcontactus').contactUs({
            items: arcItems
        });
        jQuery('#arcontactus').contactUs({
            buttonIcon: '<svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Canvas" transform="translate(-825 -308)"><g id="Vector"><use xlink:href="#path0_fill0123" transform="translate(825 308)" fill="currentColor"></use></g></g><defs><path id="path0_fill0123" d="M 19 4L 17 4L 17 13L 4 13L 4 15C 4 15.55 4.45 16 5 16L 16 16L 20 20L 20 5C 20 4.45 19.55 4 19 4ZM 15 10L 15 1C 15 0.45 14.55 0 14 0L 1 0C 0.45 0 0 0.45 0 1L 0 15L 4 11L 14 11C 14.55 11 15 10.55 15 10Z"></path></defs></svg>',
            drag: true,
            mode: 'regular',
            buttonIconUrl: 'https://projectshipping.vn/wp-content/plugins/ar-contactus/res/img/msg.svg',
            showMenuHeader: true,
            menuHeaderText: "Liên Hệ Với Chúng Tôi",
            showHeaderCloseBtn: true,
            headerCloseBtnBgColor: '#001109',
            headerCloseBtnColor: '#FFFFFF',
            itemsIconType: 'rounded',
            align: 'right',
            reCaptcha: false,
            reCaptchaKey: '',
            countdown: 0,
            theme: '#FECB00',
            buttonText: "Liên hệ",
            buttonSize: 'large',
            menuSize: 'large',
            phonePlaceholder: '0909411668',
            callbackSubmitText: 'Waiting for call',
            errorMessage: 'Connection error. Please refresh the page and try again.',
            callProcessText: 'We are calling you to phone',
            callSuccessText: 'Thank you.<br />We are call you back soon.',
            iconsAnimationSpeed: 600,
            callbackFormText: '',
            items: arcItems,
            ajaxUrl: 'https://projectshipping.vn/wp-admin/admin-ajax.php',
            promptPosition: 'top',
            callbackFormFields: {
                phone: {
                    name: 'phone',
                    enabled: true,
                    required: true,
                    type: 'tel',
                    label: '',
                    placeholder: "0909411668"
                },
            },
            action: 'arcontactus_request_callback'
        });
    });
</script>