<div class="card border-0 shadow-sm">
    <div class="card-body text-center p-5">
        <div class="mb-4">
            <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
        </div>

        <h3 class="card-title text-primary mb-3">
            <?= tr(['en' => 'Please Sign In', 'ko' => '로그인이 필요합니다', 'ja' => 'サインインしてください', 'zh' => '请登录']) ?>
        </h3>

        <p class="card-text text-muted mb-4">
            <?= tr([
                'en' => 'You need to sign in to access this feature. Please click the button below to continue.',
                'ko' => '이 기능을 사용하려면 로그인이 필요합니다. 아래 버튼을 클릭하여 로그인해주세요.',
                'ja' => 'この機能にアクセスするにはサインインが必要です。下のボタンをクリックして続行してください。',
                'zh' => '您需要登录才能使用此功能。请点击下方按钮继续。'
            ]) ?>
        </p>

        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <a href="<?= href()->user->login ?>" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                <?= tr(['en' => 'Sign In', 'ko' => '로그인', 'ja' => 'サインイン', 'zh' => '登录']) ?>
            </a>
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <a href="/" class="text-decoration-none text-muted">
        <i class="bi bi-house me-1"></i>
        <?= tr(['en' => 'Back to Home', 'ko' => '홈으로 돌아가기', 'ja' => 'ホームに戻る', 'zh' => '返回首页']) ?>
    </a>
</div>