<?php
// 다국어 번역 텍스트 주입
inject_logout_submit_language();
?>

<!-- 로그아웃 페이지 -->
<div class="sonub-container">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-md-6 col-lg-5 text-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <!-- 아이콘 -->
                    <div class="mb-4">
                        <i class="bi bi-hand-wave text-primary" style="font-size: 5rem;"></i>
                    </div>

                    <!-- 메인 메시지 -->
                    <h2 class="mb-3 text-primary fw-bold"><?= tr('안녕히 가세요') ?></h2>
                    <p class="text-muted mb-4 fs-5"><?= tr('Sonub.com을 다시 방문해 주세요') ?></p>
                    <p class="text-muted mb-4"><?= tr('감사합니다') ?></p>

                    <!-- 로딩 스피너 -->
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden"><?= tr('로그아웃 중...') ?></span>
                    </div>

                    <!-- 부가 메시지 -->
                    <p class="text-muted small"><?= tr('잠시 후 홈페이지로 이동합니다') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    firebase_ready(() => {

        // Firebase Auth에서 로그아웃 후 홈으로 리다이렉트
        firebase.auth().signOut().then(() => {
            // 로그아웃 성공
            setTimeout(() => {
                window.location.href = '<?= href()->home ?>';
            }, 1500); // 1.5초 후 리다이렉트
        }).catch((error) => {
            // 로그아웃 중 오류 발생
            console.error("로그아웃 오류:", error);
            window.location.href = '<?= href()->home ?>'; // 오류가 발생해도 홈으로 리다이렉트
        });
    })
</script>

<?php
/**
 * 로그아웃 페이지 다국어 번역 텍스트 주입
 *
 * 이 함수는 로그아웃 페이지에서 사용되는 모든 텍스트의 다국어 번역을 주입합니다.
 * 지원 언어: 영어(기본), 한국어, 일본어, 중국어
 */
function inject_logout_submit_language()
{
    t()->inject([
        // 메인 메시지
        '안녕히 가세요' => [
            'en' => 'Goodbye',
            'ko' => '안녕히 가세요',
            'ja' => 'さようなら',
            'zh' => '再见',
        ],
        'Sonub.com을 다시 방문해 주세요' => [
            'en' => 'Please visit Sonub.com again',
            'ko' => 'Sonub.com을 다시 방문해 주세요',
            'ja' => 'Sonub.comをまた訪れてください',
            'zh' => '请再次访问 Sonub.com',
        ],
        '감사합니다' => [
            'en' => 'Thank you',
            'ko' => '감사합니다',
            'ja' => 'ありがとうございます',
            'zh' => '谢谢',
        ],

        // 로딩 및 안내 메시지
        '로그아웃 중...' => [
            'en' => 'Logging out...',
            'ko' => '로그아웃 중...',
            'ja' => 'ログアウト中...',
            'zh' => '正在登出...',
        ],
        '잠시 후 홈페이지로 이동합니다' => [
            'en' => 'You will be redirected to the homepage shortly',
            'ko' => '잠시 후 홈페이지로 이동합니다',
            'ja' => 'まもなくホームページに移動します',
            'zh' => '即将跳转到主页',
        ],
    ]);
}
?>