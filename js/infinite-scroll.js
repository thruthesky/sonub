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
     * @param {string|HTMLElement} element - CSS selector string or DOM element for observing the scroll of the list
     * @param {Object} options - Configuration options
     */
    init(element, options = {}) {
        // element가 유효한지 확인
        if (!element) {
            throw new Error('InfiniteScroll: element is required');
        }

        // 문자열인 경우 querySelector로 DOM 요소 찾기
        let targetElement = element;
        if (typeof element === 'string') {
            // 'body' 또는 'html' 문자열인 경우 직접 참조
            if (element === 'body') {
                targetElement = document.body;
            } else if (element === 'html') {
                targetElement = document.documentElement;
            } else {
                targetElement = document.querySelector(element);
                if (!targetElement) {
                    throw new Error(`InfiniteScroll: element not found: ${element}`);
                }
            }
        }

        // body 또는 html 태그인 경우 window 스크롤을 사용
        const isBodyOrHtml = targetElement === document.body || targetElement === document.documentElement;

        // Apply required styles - body/html이 아닌 경우에만 스타일 적용
        if (!isBodyOrHtml) {
            targetElement.style.overflowY = 'auto';
            targetElement.style.position = 'relative';
        }

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
                let scrollTop, scrollHeight, clientHeight;

                // body/html 태그인 경우 window 스크롤 사용
                if (isBodyOrHtml) {
                    scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    scrollHeight = document.documentElement.scrollHeight;
                    clientHeight = window.innerHeight;
                } else {
                    scrollTop = targetElement.scrollTop;
                    scrollHeight = targetElement.scrollHeight;
                    clientHeight = targetElement.clientHeight;
                }

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

        // Register scroll event listener - body/html인 경우 window에 등록
        const scrollTarget = isBodyOrHtml ? window : targetElement;
        scrollTarget.addEventListener('scroll', handleScroll);

        // Helper function to force scroll to bottom
        const forceScrollToBottom = () => {
            let scrollHeight, clientHeight, maxScroll;

            // body/html 태그인 경우 window 스크롤 사용
            if (isBodyOrHtml) {
                scrollHeight = document.documentElement.scrollHeight;
                clientHeight = window.innerHeight;
                maxScroll = scrollHeight - clientHeight;

                if (maxScroll > 0) {
                    window.scrollTo({
                        top: maxScroll,
                        behavior: 'instant'
                    });
                }
            } else {
                // Force layout recalculation by reading offsetHeight
                void targetElement.offsetHeight; // Force reflow

                scrollHeight = targetElement.scrollHeight;
                clientHeight = targetElement.clientHeight;
                maxScroll = scrollHeight - clientHeight;

                if (maxScroll > 0) {
                    // Try multiple methods to ensure scrolling works
                    targetElement.scrollTop = maxScroll;

                    // Alternative method if first doesn't work
                    if (targetElement.scrollTop === 0) {
                        targetElement.scrollTo({
                            top: maxScroll,
                            behavior: 'instant'
                        });
                    }

                    // Force another reflow and check
                    void targetElement.offsetHeight;
                }
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
                const currentScrollTop = isBodyOrHtml
                    ? (window.pageYOffset || document.documentElement.scrollTop)
                    : targetElement.scrollTop;

                if (currentScrollTop > 0 || scrollAttempts >= maxAttempts) {
                    if (observer) {
                        observer.disconnect();
                        observer = null;
                    }
                    if (currentScrollTop > 0) {
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
            observer.observe(targetElement, {
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
                scrollTarget.removeEventListener('scroll', handleScroll);
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }
            },
            // Scroll to bottom
            scrollToBottom() {
                if (isBodyOrHtml) {
                    const scrollHeight = document.documentElement.scrollHeight;
                    const clientHeight = window.innerHeight;
                    const maxScroll = scrollHeight - clientHeight;

                    if (maxScroll > 0) {
                        window.scrollTo({
                            top: maxScroll,
                            behavior: 'instant'
                        });
                    }
                } else {
                    // Force layout recalculation
                    void targetElement.offsetHeight;

                    const scrollHeight = targetElement.scrollHeight;
                    const clientHeight = targetElement.clientHeight;
                    const maxScroll = scrollHeight - clientHeight;

                    if (maxScroll > 0) {
                        targetElement.scrollTop = maxScroll;

                        // Alternative method if first doesn't work
                        if (targetElement.scrollTop !== maxScroll) {
                            targetElement.scrollTo({
                                top: maxScroll,
                                behavior: 'instant'
                            });
                        }
                    }
                }
            },
            // Scroll to top
            scrollToTop() {
                if (isBodyOrHtml) {
                    window.scrollTo({
                        top: 0,
                        behavior: 'instant'
                    });
                } else {
                    targetElement.scrollTop = 0;
                }
            },
            // Scroll to specific position
            scrollTo(position) {
                if (isBodyOrHtml) {
                    window.scrollTo({
                        top: position,
                        behavior: 'instant'
                    });
                } else {
                    targetElement.scrollTop = position;
                }
            }
        };
    }
};

// Expose as global object (if needed)
window.InfiniteScroll = InfiniteScroll;