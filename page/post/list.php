<?php
// ========================================================================
// 1단계: 파라미터 가져오기
// ========================================================================
$category = http_param('category');
$page = (int)http_param('page', 1); // 기본값: 1페이지
$per_page = 20; // 페이지당 게시글 수

// ========================================================================
// 2단계: 게시글 목록 조회 (PostListModel 반환)
// ========================================================================
$offset = ($page - 1) * $per_page;
$postList = list_posts([
    'category' => $category,
    'limit' => $per_page,
    'offset' => $offset,
    'page' => $page
]);
?>

<!-- 페이지 컨테이너: 가장자리 여백 최소화 (px-2) -->
<div class="container-fluid px-2 py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">

            <!-- 게시글 작성 위젯 -->
            <?php include WIDGET_DIR . '/post/post-list-create.php'; ?>

            <!-- 게시글 목록 -->
            <?php if ($postList->isEmpty()): ?>
                <div class="empty-state">
                    <p><?= tr(['en' => 'No posts yet', 'ko' => '아직 게시글이 없습니다', 'ja' => 'まだ投稿がありません', 'zh' => '还没有帖子']) ?></p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($postList->posts as $post): ?>


                        <article class="post-card">
                            <!-- 게시글 헤더 -->
                            <div class="post-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="user-name">사용자 #<?= $post->user_id ?></div>
                                        <div class="post-time"><?= date('Y-m-d H:i', $post->created_at) ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- 게시글 내용 -->
                            <?php if ($post->content): ?>
                                <div class="post-content">
                                    <?= nl2br(htmlspecialchars($post->content)) ?>
                                </div>
                            <?php endif; ?>

                            <!-- 게시글 파일 -->
                            <?php if (!empty($post->files)): ?>
                                <div class="post-files">
                                    <?php foreach ($post->files as $file): ?>
                                        <?php display_file($file); ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- 게시글 푸터 (카테고리, 상호작용 버튼) -->
                            <div class="post-footer">
                                <div class="post-actions">
                                    <button type="button" class="action-btn">
                                        <i class="fa-regular fa-heart"></i>
                                        <span><?= tr(['en' => 'Like', 'ko' => '좋아요', 'ja' => 'いいね', 'zh' => '点赞']) ?></span>
                                    </button>
                                    <button type="button" class="action-btn">
                                        <i class="fa-regular fa-comment"></i>
                                        <span><?= tr(['en' => 'Comment', 'ko' => '댓글', 'ja' => 'コメント', 'zh' => '评论']) ?></span>
                                    </button>
                                    <a href="/post/view?id=<?= $post->id ?>" class="action-btn">
                                        <i class="fa-regular fa-arrow-up-right-from-square"></i>
                                        <span><?= tr(['en' => 'View', 'ko' => '보기', 'ja' => '表示', 'zh' => '查看']) ?></span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- 페이지네이션 -->
            <?php if ($postList->total_pages > 1): ?>
                <div class="mt-4">
                    <?php basic_pagination($page, $postList->total_pages, 5, ['category' => $category]); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>