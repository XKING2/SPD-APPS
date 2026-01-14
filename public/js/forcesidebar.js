// ===================================
// FORCE CLEAN WHITE LINE - IMMEDIATE FIX
// Script ini harus diload SETELAH jQuery tapi SEBELUM DOM ready
// ===================================

(function() {
    'use strict';
    
    // Function to force remove white line
    function forceRemoveWhiteLine() {
        const sidebar = document.querySelector('.sidebar');
        const contentWrapper = document.getElementById('content-wrapper');
        const wrapper = document.getElementById('wrapper');
        
        if (sidebar) {
            sidebar.style.border = 'none';
            sidebar.style.borderRight = 'none';
            sidebar.style.boxShadow = 'none';
            sidebar.style.outline = 'none';
            sidebar.style.margin = '0';
            sidebar.style.padding = '0';
        }
        
        if (contentWrapper) {
            contentWrapper.style.border = 'none';
            contentWrapper.style.borderLeft = 'none';
            contentWrapper.style.margin = '0';
            contentWrapper.style.padding = '0';
            
            // Force recalculate width
            if (window.innerWidth >= 768) {
                contentWrapper.style.marginLeft = '14rem';
                contentWrapper.style.width = 'calc(100% - 14rem)';
            } else {
                contentWrapper.style.marginLeft = '0';
                contentWrapper.style.width = '100%';
            }
        }
        
        if (wrapper) {
            wrapper.style.overflow = 'hidden';
            wrapper.style.overflowX = 'hidden';
        }
    }
    
    // Run immediately when script loads
    forceRemoveWhiteLine();
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', forceRemoveWhiteLine);
    } else {
        forceRemoveWhiteLine();
    }
    
    // Run when window loads (after all resources)
    window.addEventListener('load', forceRemoveWhiteLine);
    
    // Run on resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(forceRemoveWhiteLine, 100);
    });
    
    // Use MutationObserver to catch any dynamic changes
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && 
                    (mutation.attributeName === 'style' || 
                     mutation.attributeName === 'class')) {
                    forceRemoveWhiteLine();
                }
            });
        });
        
        // Start observing when DOM is ready
        const startObserving = function() {
            const sidebar = document.querySelector('.sidebar');
            const contentWrapper = document.getElementById('content-wrapper');
            
            if (sidebar) {
                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
            }
            
            if (contentWrapper) {
                observer.observe(contentWrapper, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
            }
        };
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startObserving);
        } else {
            startObserving();
        }
    }
    
    // Additional check every 500ms for first 3 seconds (safety net)
    let checkCount = 0;
    const intervalCheck = setInterval(function() {
        forceRemoveWhiteLine();
        checkCount++;
        if (checkCount >= 6) { // 6 * 500ms = 3 seconds
            clearInterval(intervalCheck);
        }
    }, 500);
    
})();

// jQuery version (if jQuery is loaded)
if (typeof jQuery !== 'undefined') {
    (function($) {
        'use strict';
        
        function jQueryCleanGaps() {
            $('.sidebar').css({
                'border': 'none',
                'border-right': 'none',
                'box-shadow': 'none',
                'outline': 'none',
                'margin': '0',
                'padding': '0'
            });
            
            $('#content-wrapper').css({
                'border': 'none',
                'border-left': 'none',
                'margin': '0',
                'padding': '0'
            });
            
            if ($(window).width() >= 768) {
                $('#content-wrapper').css({
                    'margin-left': '14rem',
                    'width': 'calc(100% - 14rem)'
                });
            } else {
                $('#content-wrapper').css({
                    'margin-left': '0',
                    'width': '100%'
                });
            }
        }
        
        // Run on document ready
        $(document).ready(function() {
            jQueryCleanGaps();
            
            // Run after a small delay to ensure all CSS is loaded
            setTimeout(jQueryCleanGaps, 100);
            setTimeout(jQueryCleanGaps, 500);
        });
        
        // Run on window load
        $(window).on('load', jQueryCleanGaps);
        
        // Run on resize
        $(window).on('resize', function() {
            jQueryCleanGaps();
        });
        
    })(jQuery);
}

console.log('🔧 Force Clean White Line Script Loaded');