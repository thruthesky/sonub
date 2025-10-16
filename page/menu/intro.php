<?php
/**
 * Sonub 메뉴 페이지
 *
 * 사용자가 접근할 수 있는 모든 메뉴를 표시합니다.
 */

$user = login();
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <h1 class="mb-4 text-center">메뉴</h1>

            <!-- 주요 메뉴 -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">주요 메뉴</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= href()->home ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-house-door fs-4 me-3 text-primary"></i>
                        <div>
                            <div class="fw-semibold">홈</div>
                            <small class="text-muted">메인 페이지로 이동</small>
                        </div>
                    </a>
                    <a href="<?= href()->post->categories ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-grid fs-4 me-3 text-primary"></i>
                        <div>
                            <div class="fw-semibold">게시판</div>
                            <small class="text-muted">카테고리별 게시글 보기</small>
                        </div>
                    </a>
                    <a href="<?= href()->user->list ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-people fs-4 me-3 text-primary"></i>
                        <div>
                            <div class="fw-semibold">사용자 목록</div>
                            <small class="text-muted">가입한 회원 보기</small>
                        </div>
                    </a>
                    <a href="<?= href()->chat->rooms ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-chat-dots fs-4 me-3 text-primary"></i>
                        <div>
                            <div class="fw-semibold">채팅</div>
                            <small class="text-muted">채팅방 목록</small>
                        </div>
                    </a>
                </div>
            </div>

            <?php if ($user): ?>
                <!-- 로그인한 사용자 메뉴 -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">내 계정</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= href()->user->profile ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-person-circle fs-4 me-3 text-success"></i>
                            <div>
                                <div class="fw-semibold">내 프로필</div>
                                <small class="text-muted">프로필 보기 및 수정</small>
                            </div>
                        </a>
                        <a href="<?= href()->user->settings ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-gear fs-4 me-3 text-success"></i>
                            <div>
                                <div class="fw-semibold">설정</div>
                                <small class="text-muted">계정 설정</small>
                            </div>
                        </a>
                        <a href="<?= href()->post->list(1, 'my-wall') ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-journal-text fs-4 me-3 text-success"></i>
                            <div>
                                <div class="fw-semibold">내 벽</div>
                                <small class="text-muted">내가 작성한 글 보기</small>
                            </div>
                        </a>
                        <a href="<?= href()->comment->list($user['id']) ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-chat-left-text fs-4 me-3 text-success"></i>
                            <div>
                                <div class="fw-semibold">내 댓글</div>
                                <small class="text-muted">내가 작성한 댓글 보기</small>
                            </div>
                        </a>
                        <a href="<?= href()->user->logout_submit ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-box-arrow-right fs-4 me-3 text-danger"></i>
                            <div>
                                <div class="fw-semibold">로그아웃</div>
                                <small class="text-muted">계정에서 로그아웃</small>
                            </div>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- 로그인하지 않은 사용자 메뉴 -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">계정</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= href()->user->login ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-box-arrow-in-right fs-4 me-3 text-info"></i>
                            <div>
                                <div class="fw-semibold">로그인</div>
                                <small class="text-muted">계정에 로그인</small>
                            </div>
                        </a>
                        <a href="<?= href()->user->register ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-person-plus fs-4 me-3 text-info"></i>
                            <div>
                                <div class="fw-semibold">회원가입</div>
                                <small class="text-muted">새 계정 만들기</small>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 도움말 및 정보 -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">도움말 및 정보</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= href()->help->howto ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-question-circle fs-4 me-3 text-secondary"></i>
                        <div>
                            <div class="fw-semibold">사용 방법</div>
                            <small class="text-muted">Sonub 사용 가이드</small>
                        </div>
                    </a>
                    <a href="<?= href()->help->guideline ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-file-text fs-4 me-3 text-secondary"></i>
                        <div>
                            <div class="fw-semibold">가이드라인</div>
                            <small class="text-muted">커뮤니티 규칙</small>
                        </div>
                    </a>
                    <a href="/about" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-info-circle fs-4 me-3 text-secondary"></i>
                        <div>
                            <div class="fw-semibold">소개</div>
                            <small class="text-muted">Sonub에 대해</small>
                        </div>
                    </a>
                    <a href="<?= href()->admin->contact ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-envelope fs-4 me-3 text-secondary"></i>
                        <div>
                            <div class="fw-semibold">문의하기</div>
                            <small class="text-muted">관리자에게 문의</small>
                        </div>
                    </a>
                    <a href="<?= href()->help->terms_and_conditions ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-3 text-secondary"></i>
                        <div>
                            <div class="fw-semibold">이용약관</div>
                            <small class="text-muted">서비스 이용약관</small>
                        </div>
                    </a>
                    <a href="<?= href()->help->privacy ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-shield-check fs-4 me-3 text-secondary"></i>
                        <div>
                            <div class="fw-semibold">개인정보처리방침</div>
                            <small class="text-muted">개인정보 보호 정책</small>
                        </div>
                    </a>
                </div>
            </div>

            <?php if ($user): ?>
                <!-- 관리자 메뉴 (관리자만 표시) -->
                <?php if ($user['is_admin'] ?? false): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">관리자</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="<?= href()->admin->dashboard ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="bi bi-speedometer2 fs-4 me-3 text-danger"></i>
                                <div>
                                    <div class="fw-semibold">대시보드</div>
                                    <small class="text-muted">관리자 대시보드</small>
                                </div>
                            </a>
                            <a href="<?= href()->admin->users ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="bi bi-people-fill fs-4 me-3 text-danger"></i>
                                <div>
                                    <div class="fw-semibold">사용자 관리</div>
                                    <small class="text-muted">회원 관리</small>
                                </div>
                            </a>
                            <a href="<?= href()->admin->posts ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="bi bi-file-post fs-4 me-3 text-danger"></i>
                                <div>
                                    <div class="fw-semibold">게시글 관리</div>
                                    <small class="text-muted">게시글 관리</small>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
