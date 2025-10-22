<?php

/**
 * 404 페이지
 *
 * 간결하고 단순한 디자인
 */

// 번역 함수 호출
inject_404_language();
?>

<div class="d-flex flex-column justify-content-center align-items-center h-100">
    <!-- 404 숫자 -->
    <h1 class="display-1 fw-bold text-secondary mb-3">404</h1>

    <!-- 메시지 -->
    <p class="text-muted mb-4"><?= t()->페이지를_찾을_수_없습니다 ?></p>

    <!-- 버튼 -->
    <div class="d-flex gap-2 justify-content-center">
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house"></i> <?= t()->홈 ?>
        </a>
        <button onclick="history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> <?= t()->뒤로 ?>
        </button>
    </div>
</div>

<?php
/**
 * 404 페이지 번역 텍스트 주입
 */
function inject_404_language()
{
    t()->inject([
        '페이지를_찾을_수_없습니다' => [
            'ko' => '페이지를 찾을 수 없습니다',
            'en' => 'Page not found',
            'ja' => 'ページが見つかりません',
            'zh' => '找不到页面'
        ],
        '홈' => [
            'ko' => '홈',
            'en' => 'Home',
            'ja' => 'ホーム',
            'zh' => '首页'
        ],
        '뒤로' => [
            'ko' => '뒤로',
            'en' => 'Back',
            'ja' => '戻る',
            'zh' => '返回'
        ],
    ]);
}
?>