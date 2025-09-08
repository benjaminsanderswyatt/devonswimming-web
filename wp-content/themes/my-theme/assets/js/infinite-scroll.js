jQuery(function($) {
    // Get current page and max page from global
    let currentPage = parseInt(devonScroll.current_page, 10) || 1;
    const maxPage = parseInt(devonScroll.max_page, 10) || 1;
    let loading = false;
    const delay = 600; // Delay (ms) before sending AJAX request

    // DOM elemtents
    const $container = $(devonScroll.container); // Post container
    let $sentinel = $(devonScroll.sentinel); // Scroll sentinel (trigger)
    const $status = $('#infinite-scroll-status'); // Status message container
    const $statusText = $status.find('.status-text'); // Status message text

    // Ensure existence
    if (!$container.length || !$sentinel.length) 
        return;


    if (maxPage <= currentPage) {
        if ($status.length) {
            $statusText.text('No more posts found');
            $status.addClass('is-visible is-end');
        }
        return; // Stop before creating an observer or binding scroll events
    }

    // Show loading status
    const showStatus = (text, isEnd = false) => {
        $statusText.text(text);
        $status.toggleClass('is-end', isEnd).addClass('is-visible');
    };

    // Hide loading status
    const hideStatus = () => $status.removeClass('is-visible');

    // Move the sentinel to the bottom
    function placeAtBottom() {
        $container.append($sentinel, $status);
    }


    function showEndMessage() {
        showStatus('No more posts found', true);
        if (observer) observer.disconnect();
        $sentinel.remove();
    }


    function replaceSentinelAndObserve() {
        if (observer && $sentinel.length) observer.unobserve($sentinel.get(0));
        $sentinel = $('<div id="infinite-scroll-sentinel" aria-hidden="true"></div>');
        placeAtBottom();
        if (observer) observer.observe($sentinel.get(0));
    }


    function loadNextPage() {
        if (loading || currentPage >= maxPage) {
            if (currentPage >= maxPage) showEndMessage();
                return;
            }

            loading = true;
            showStatus('Loading more posts...');

            //return; // Uncomment this line to disable AJAX loading for testing


            setTimeout(function() { // Delay
            $.ajax({
                url: devonScroll.ajaxurl,
                type: 'POST',
                data: {
                action: 'devon_infinite_scroll',
                page: currentPage,
                query_vars: devonScroll.query_vars,
                nonce: devonScroll.nonce
                }
            }).done(function(html) {

                if ($.trim(html)) {
                    $sentinel.before(html); // Append new posts
                    currentPage++;

                    if (currentPage >= maxPage) {
                        showEndMessage();
                    } else {
                        replaceSentinelAndObserve();
                        hideStatus();
                    }
                } else {
                    // Empty response (no more posts)
                    showEndMessage();
                }

            }).always(function() {
                loading = false;
            });
        }, delay);
    }

    // Scroll handling
    let observer = null;

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(function(entries) {
        
        if (entries.some(entry => entry.isIntersecting)) {
            loadNextPage();
        }
        }, { rootMargin: '400px 0px' }); // Load when near the bottom

        placeAtBottom();
        observer.observe($sentinel.get(0));

    } else {
        
        // Fallback for browsers without IntersectionObserver support
        $(window).on('scroll', function() {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 400) {
                loadNextPage();
            }
        });

    }
});
