// Mobile Menu Toggle - Optimized for Performance
(function() {
    'use strict';
    
    var $mobileHamburger, $mobileMenu, $mobileOverlay, $body;
    var isMenuOpen = false;
    var resizeTimer = null;

    function init() {
        $mobileHamburger = $("#mobile-hamburger");
        $mobileMenu = $("#mobile-menu");
        $mobileOverlay = $("#mobile-menu-overlay");
        $body = $("body");

        if (!$mobileHamburger.length || !$mobileMenu.length) {
            return;
        }

        setupEventHandlers();
    }

    function openMobileMenu() {
        if (isMenuOpen) return;
        isMenuOpen = true;
        
        $mobileMenu.addClass("active");
        $mobileHamburger.addClass("active");
        if ($mobileOverlay.length) {
            $mobileOverlay.addClass("active");
        }
        $body.css("overflow", "hidden");
    }

    function closeMobileMenu() {
        if (!isMenuOpen) return;
        isMenuOpen = false;
        
        $mobileMenu.removeClass("active");
        $mobileHamburger.removeClass("active");
        if ($mobileOverlay.length) {
            $mobileOverlay.removeClass("active");
        }
        $body.css("overflow", "");
        // Close all submenus
        $mobileMenu.find("ul.open").removeClass("open");
        $mobileMenu.find("a.has-child.open").removeClass("open");
    }

    function toggleMenu(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        if (isMenuOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
        return false;
    }

    function handleSubmenuClick(e) {
        var $this = $(this);
        var $submenu = $this.next("ul");
        
        if ($submenu.length) {
            e.preventDefault();
            e.stopPropagation();
            
            var isOpen = $this.hasClass("open");
            if (isOpen) {
                $this.removeClass("open");
                $submenu.removeClass("open");
            } else {
                $this.addClass("open");
                $submenu.addClass("open");
            }
        }
    }

    function handleResize() {
        // Debounce resize event
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if ($(window).width() > 991 && isMenuOpen) {
                closeMobileMenu();
            }
        }, 150);
    }

    function setupEventHandlers() {
        // Toggle menu
        $mobileHamburger.on("click", toggleMenu);

        // Close menu when clicking overlay
        if ($mobileOverlay.length) {
            $mobileOverlay.on("click", closeMobileMenu);
        }

        // Handle submenu toggle - use event delegation for better performance
        $mobileMenu.on("click", "a.has-child", handleSubmenuClick);

        // Close menu on window resize if desktop - debounced
        $(window).on("resize", handleResize);

        // Close menu on ESC key
        $(document).on("keydown", function(e) {
            if (e.keyCode === 27 && isMenuOpen) { // ESC key
                closeMobileMenu();
            }
        });
    }

    // Initialize when DOM is ready
    $(document).ready(init);
})();
