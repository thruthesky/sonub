<?php

/**
 * 에러 메시지를 사용자에게 보기 좋게 표시하는 함수
 *
 * Bootstrap Alert 컴포넌트를 사용하여 에러 메시지를 표시합니다.
 * 밝은 배경색에 위험 색상의 테두리와 텍스트를 사용하여 시각적으로 명확하게 표시합니다.
 * 홈, 뒤로가기, 관리자 문의 링크를 제공합니다.
 *
 * @param string $message 표시할 에러 메시지
 * @param string $title 에러 제목 (기본값: "오류")
 */
function display_error($message, $title = '')
{
    if (empty($title)) {
        $title = tr(['ko' => '오류', 'en' => 'Error', 'ja' => 'エラー', 'zh' => '错误']);
    }
    $safe_message = htmlspecialchars($message);
    $safe_title = htmlspecialchars($title);

    // 링크 텍스트 번역
    $home_text = tr(['ko' => '홈', 'en' => 'Home', 'ja' => 'ホーム', 'zh' => '首页']);
    $back_text = tr(['ko' => '뒤로', 'en' => 'Back', 'ja' => '戻る', 'zh' => '返回']);
    $contact_text = tr(['ko' => '관리자 문의', 'en' => 'Contact Admin', 'ja' => '管理者に連絡', 'zh' => '联系管理员']);

    // 링크 URL
    $home_url = href('/');
    $contact_url = href('/page/help/contact.php');

    echo <<<HTML
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card border-danger border-2 shadow rounded-3">
                    <div class="card-body bg-light rounded-3">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 20px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="card-title text-danger mb-2 fw-bold">{$safe_title}</h4>
                                <p class="card-text text-dark mb-0">{$safe_message}</p>
                            </div>
                        </div>
                        <hr class="border-danger border-opacity-25">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{href()->home}" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-house-door me-1"></i>{$home_text}
                            </a>
                            <button onclick="history.back()" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>{$back_text}
                            </button>
                            <a href="{href()->admin->contact}" class="btn btn-danger btn-sm">
                                <i class="bi bi-envelope me-1"></i>{$contact_text}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
HTML;
}
