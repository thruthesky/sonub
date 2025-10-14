/**
 * Infinite Scroll Library
 *
 * Provides infinite scroll functionality for scrollable containers
 * Calls a callback function when scrolled to the bottom
 *
 * Usage:
 * 1. Set height style on HTML element (required)
 * 2. Call InfiniteScroll.init(element, options)
 *
 * Options:
 * - onScrolledToBottom: Callback function called when scrolled to bottom
 * - onScrolledToTop: Callback function called when scrolled to top
 * - threshold: Distance from bottom/top threshold (default: 10px)
 * - debounceDelay: Debounce delay time (default: 100ms)
 * - initialScrollToBottom: Scroll to bottom on initial load (default: true)
 */

const InfiniteScroll = {
    /**
     * Initialize infinite scroll
     * @param {string} element - DOM element for observing the scroll of the list
     * @param {Object} options - Configuration options
     */
    init(element, options = {}) {

        // Apply required styles - set overflow-y to auto for scrollability
        element.style.overflowY = 'auto';
        element.style.position = 'relative';

        // Default options
        const config = {
            onScrolledToBottom: options.onScrolledToBottom || (() => { }),
            onScrolledToTop: options.onScrolledToTop || (() => { }),
            threshold: options.threshold || 10, // Trigger within 10px from bottom/top
            debounceDelay: options.debounceDelay || 100, // Debounce delay
            initialScrollToBottom: options.initialScrollToBottom !== undefined ? options.initialScrollToBottom : true // Default to true
        };

        // Timer for debouncing
        let scrollTimer = null;

        // Scroll event handler
        const handleScroll = () => {
            // Debounce handling
            if (scrollTimer) {
                clearTimeout(scrollTimer);
            }

            scrollTimer = setTimeout(() => {
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;

                // Check if scrolled to top
                if (scrollTop <= config.threshold) {
                    config.onScrolledToTop();
                }

                // Check if scrolled to bottom
                if (scrollTop + clientHeight >= scrollHeight - config.threshold) {
                    config.onScrolledToBottom();
                }

            }, config.debounceDelay);
        };

        // Register scroll event listener
        element.addEventListener('scroll', handleScroll);

        // Helper function to force scroll to bottom
        const forceScrollToBottom = () => {
            // Force layout recalculation by reading offsetHeight
            void element.offsetHeight; // Force reflow

            const scrollHeight = element.scrollHeight;
            const clientHeight = element.clientHeight;
            const maxScroll = scrollHeight - clientHeight;

            //  console.log('InfiniteScroll: Attempting to scroll', {
            //     scrollHeight,
            //     clientHeight,
            //     maxScroll,
            //     currentScrollTop: element.scrollTop,
            //     overflow: window.getComputedStyle(element).overflowY
            // });

            if (maxScroll > 0) {
                // Try multiple methods to ensure scrolling works
                element.scrollTop = maxScroll;

                // Alternative method if first doesn't work
                if (element.scrollTop === 0) {
                    element.scrollTo({
                        top: maxScroll,
                        behavior: 'instant'
                    });
                }

                // Force another reflow and check
                void element.offsetHeight;
                // console.log('InfiniteScroll: After scroll attempt, scrollTop:', element.scrollTop);
            }
        };

        // Scroll to bottom on initial load if configured
        if (config.initialScrollToBottom) {
            let observer = null;
            let scrollAttempts = 0;
            const maxAttempts = 20;

            // Function to attempt scrolling
            const attemptScroll = () => {
                scrollAttempts++;
                forceScrollToBottom();

                // Check if scroll was successful
                if (element.scrollTop > 0 || scrollAttempts >= maxAttempts) {
                    if (observer) {
                        observer.disconnect();
                        observer = null;
                    }
                    if (element.scrollTop > 0) {
                        // console.log('InfiniteScroll: Successfully scrolled to bottom after', scrollAttempts, 'attempts');
                    } else {
                        // console.warn('InfiniteScroll: Failed to scroll after', scrollAttempts, 'attempts');
                    }
                }
            };

            // Use MutationObserver to detect when content is added
            observer = new MutationObserver((mutations) => {
                // Check if any actual nodes were added
                const hasNewContent = mutations.some(mutation =>
                    mutation.addedNodes.length > 0 ||
                    (mutation.type === 'characterData' && mutation.target.nodeValue?.trim())
                );

                if (hasNewContent) {
                    // Use requestAnimationFrame to ensure rendering is complete
                    requestAnimationFrame(() => {
                        attemptScroll();
                    });
                }
            });

            // Start observing
            observer.observe(element, {
                childList: true,
                subtree: true,
                characterData: true
            });

            // Also try immediate scroll attempts with various timings
            requestAnimationFrame(() => attemptScroll());
            setTimeout(() => attemptScroll(), 50);
            setTimeout(() => attemptScroll(), 100);
            setTimeout(() => attemptScroll(), 200);
            setTimeout(() => attemptScroll(), 500);

            // Clean up observer after a reasonable time
            setTimeout(() => {
                if (observer) {
                    observer.disconnect();
                    observer = null;
                }
            }, 5000);
        }

        // Return cleanup functions
        return {
            destroy() {
                element.removeEventListener('scroll', handleScroll);
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }
            },
            // Scroll to bottom
            scrollToBottom() {
                // Force layout recalculation
                void element.offsetHeight;

                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const maxScroll = scrollHeight - clientHeight;

                if (maxScroll > 0) {
                    element.scrollTop = maxScroll;

                    // Alternative method if first doesn't work
                    if (element.scrollTop !== maxScroll) {
                        element.scrollTo({
                            top: maxScroll,
                            behavior: 'instant'
                        });
                    }
                }
            },
            // Scroll to top
            scrollToTop() {
                element.scrollTop = 0;
            },
            // Scroll to specific position
            scrollTo(position) {
                element.scrollTop = position;
            }
        };
    }
};

// Expose as global object (if needed)
window.InfiniteScroll = InfiniteScroll;