<?php



inject_quick_links_language();
?>

<!-- Facebook-style Quick Links -->
<div class="card border-0 bg-transparent">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= href()->help->privacy ?>"
                class="text-decoration-none text-secondary small quick-link-item">
                <i class="fa-solid fa-shield-halved me-1"></i><?= t()->개인정보보호 ?>
            </a>
            <span class="text-secondary">·</span>
            <a href="<?= href()->help->terms_and_conditions ?>"
                class="text-decoration-none text-secondary small quick-link-item">
                <i class="fa-solid fa-file-contract me-1"></i><?= t()->이용약관 ?>
            </a>
            <span class="text-secondary">·</span>
            <a href="<?= href()->adv->intro ?>"
                class="text-decoration-none text-secondary small quick-link-item">
                <i class="fa-solid fa-bullhorn me-1"></i><?= t()->광고 ?>
            </a>
            <span class="text-secondary">·</span>
            <a href="<?= href()->help->about ?>"
                class="text-decoration-none text-secondary small quick-link-item">
                <i class="fa-solid fa-circle-info me-1"></i><?= t()->소개 ?>
            </a>
        </div>
    </div>
</div>

<style>
</style>

<?php
function inject_quick_links_language(): void
{
    t()->inject([
        '개인정보보호' => [
            'ko' => '개인정보보호',
            'en' => 'Privacy',
            'ja' => 'プライバシー',
            'zh' => '隐私'
        ],
        '이용약관' => [
            'ko' => '이용약관',
            'en' => 'Terms',
            'ja' => '利用規約',
            'zh' => '条款'
        ],
        '광고' => [
            'ko' => '광고',
            'en' => 'Advertising',
            'ja' => '広告',
            'zh' => '广告'
        ],
        '소개' => [
            'ko' => '소개',
            'en' => 'About',
            'ja' => 'について',
            'zh' => '关于'
        ],
    ]);
}
?>