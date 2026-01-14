// ===================================
// RESPONSIVE SIDEBAR TOGGLE
// ===================================

(function($) {
    "use strict";

    // Toggle sidebar on mobile
    $("#sidebarToggleTop, #sidebarToggle").on('click', function(e) {
        e.preventDefault();
        
        // Toggle sidebar
        $(".sidebar").toggleClass("show");
        
        // Toggle overlay
        if ($(".sidebar").hasClass("show")) {
            if (!$(".sidebar-overlay").length) {
                $("body").append('<div class="sidebar-overlay"></div>');
            }
            $(".sidebar-overlay").addClass("show");
        } else {
            $(".sidebar-overlay").removeClass("show");
        }
    });

    // Close sidebar when clicking overlay
    $(document).on('click', '.sidebar-overlay', function() {
        $(".sidebar").removeClass("show");
        $(this).removeClass("show");
    });

    // Close sidebar when clicking a link on mobile
    if ($(window).width() < 768) {
        $(".sidebar .nav-link, .collapse-item").on('click', function() {
            if (!$(this).hasClass('collapsed')) {
                setTimeout(function() {
                    $(".sidebar").removeClass("show");
                    $(".sidebar-overlay").removeClass("show");
                }, 300);
            }
        });
    }

    // Handle window resize
    $(window).on('resize', function() {
        if ($(window).width() >= 768) {
            $(".sidebar").removeClass("show");
            $(".sidebar-overlay").removeClass("show");
        }
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });

    // Prevent body scroll when sidebar is open on mobile
    $(".sidebar").on('show.bs.collapse', function() {
        if ($(window).width() < 768) {
            $("body").css("overflow", "hidden");
        }
    });

    $(".sidebar").on('hide.bs.collapse', function() {
        $("body").css("overflow", "auto");
    });

    // Handle table responsiveness
    function wrapTables() {
        $('table').each(function() {
            if (!$(this).parent().hasClass('table-wrapper')) {
                $(this).wrap('<div class="table-wrapper"></div>');
            }
        });
    }

    wrapTables();

    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 800);
        }
    });

    // Add active class to current page
    var currentUrl = window.location.href;
    $('.sidebar .nav-link, .collapse-item').each(function() {
        var linkUrl = $(this).attr('href');
        if (currentUrl.indexOf(linkUrl) !== -1 && linkUrl !== '#') {
            $(this).addClass('active');
            $(this).closest('.collapse').addClass('show');
        }
    });

    // Handle long text truncation
    function truncateText() {
        $('.text-truncate-mobile').each(function() {
            var text = $(this).text();
            if ($(window).width() < 768 && text.length > 20) {
                $(this).attr('title', text);
            }
        });
    }

    truncateText();
    $(window).on('resize', truncateText);

    // Make alerts dismissible
    $('.alert').each(function() {
        if (!$(this).find('.close').length && $(this).hasClass('alert-dismissible')) {
            $(this).append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        }
    });

    // Lazy load images
    if ('IntersectionObserver' in window) {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img.lazy').forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // Handle touch events for better mobile experience
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-enabled');
    }

    // Prevent double-tap zoom on iOS
    var lastTouchEnd = 0;
    document.addEventListener('touchend', function(e) {
        var now = Date.now();
        if (now - lastTouchEnd <= 300) {
            e.preventDefault();
        }
        lastTouchEnd = now;
    }, false);

    // Add loading indicator for AJAX requests
    $(document).ajaxStart(function() {
        $('body').addClass('loading');
    }).ajaxStop(function() {
        $('body').removeClass('loading');
    });

    // Handle form validation on mobile
    $('form').on('submit', function(e) {
        var isValid = true;
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Scroll to first invalid field
            var firstInvalid = $(this).find('.is-invalid').first();
            if (firstInvalid.length) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
            }
        }
    });

    // Remove invalid class on input
    $('input, select, textarea').on('change input', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });

})(jQuery);