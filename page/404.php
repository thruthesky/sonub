<?php
/**
 * 404 페이지
 * 
 * 간결하고 단순한 디자인
 */
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
            <div class="text-center">
                <!-- 404 숫자 -->
                <h1 class="display-1 fw-bold text-secondary mb-3">404</h1>
                
                <!-- 메시지 -->
                <p class="text-muted mb-4">페이지를 찾을 수 없습니다</p>
                
                <!-- 버튼 -->
                <div class="d-flex gap-2 justify-content-center">
                    <a href="/" class="btn btn-primary">
                        <i class="bi bi-house"></i> 홈
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> 뒤로
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
