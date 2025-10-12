/**
 * Development Environment JavaScript
 *
 * This file detects and alerts developers to issues during development.
 */

// ============================================================================
// Vue.js Loading Check
// ============================================================================
// If a PHP execution error causes the HTML page to be cut off,
// Vue.js at the bottom of the page may not load.
// Alert the developer if Vue.js is not loaded after 2 seconds.

setTimeout(() => {
    // Check if Vue object is undefined
    if (typeof Vue === 'undefined') {
        alert(
            '⚠️ Vue.js가 로드되지 않았습니다!\n\n' +
            '가능한 원인:\n' +
            '1. PHP 실행 오류로 인해 페이지가 중간에 끊김\n' +
            '2. Vue.js CDN 로딩 실패\n' +
            '3. 네트워크 오류\n\n' +
            '개발자 콘솔(F12)에서 오류를 확인하세요.'
        );
        console.error('❌ Vue.js 로딩 실패: Vue 객체가 정의되지 않았습니다.');
    } else {
        // Vue.js loaded successfully
        console.log('✅ Vue.js 정상 로드됨 (버전: ' + (Vue.version || 'unknown') + ')');
    }
}, 2000);
