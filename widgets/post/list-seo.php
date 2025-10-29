<!-- 최소한의 SEO 콘텐츠 제공 (Vue 마운트 전까지 표시 - post-component 디자인 동일) -->
<div id="ssr-content">
    <?php if ($postList['posts']): ?>
        <?php foreach ($postList['posts'] as $post): ?>
            <article class="card shadow-sm mb-4" itemscope itemtype="http://schema.org/Article">
                <!-- 게시물 헤더 (사용자 정보) -->
                <header class="card-header bg-white d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #e4e6eb;">
                    <div class="d-flex align-items-center gap-2">
                        <!-- 프로필 사진 (Bootstrap 유틸리티 클래스 사용) -->
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                             style="width: 40px; height: 40px; background-color: #e4e6eb;">
                            <?php if ($post->author && $post->author->photo_url): ?>
                                <img src="<?= htmlspecialchars($post->author->photo_url) ?>"
                                    alt="<?= htmlspecialchars($post->author->first_name) ?>"
                                    class="rounded-circle"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                    itemprop="image">
                            <?php else: ?>
                                <i class="fa-solid fa-user text-secondary" style="font-size: 20px; color: #65676b;"></i>
                            <?php endif; ?>
                        </div>

                        <!-- 사용자 이름, 날짜, 공개범위 -->
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size: 15px; color: #050505; line-height: 1.3;">
                                <a href="/user/profile?id=<?= $post->user_id ?>" style="color: inherit; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" itemprop="author">
                                    <?= htmlspecialchars($post->author->first_name ?? 'No name') ?>
                                </a>
                            </div>
                            <div class="small" style="font-size: 13px; color: #65676b; line-height: 1.3;">
                                <time datetime="<?= date('c', $post->created_at) ?>" itemprop="datePublished">
                                    <?php
                                    $date = new DateTime('@' . $post->created_at);
                                    echo $date->format('Y. m. d. H:i');
                                    ?>
                                </time>
                                · <span class="badge bg-secondary me-1"><?= $post->visibility ?? 'public' ?></span>
                                <?php if ($post->visibility !== 'private' && $post->visibility !== 'friends' && !empty($post->category)): ?>
                                    <span class="badge bg-secondary me-1"><?= $post->category ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- 게시물 본문 (Bootstrap 패딩 사용) -->
                <div class="card-body">
                    <!-- 제목 (Bootstrap 타이포그래피) -->
                    <?php if (!empty($post->title)): ?>
                        <h2 class="fw-semibold mb-2" style="font-size: 18px; color: #050505; line-height: 1.4;" itemprop="headline">
                            <?= htmlspecialchars($post->title) ?>
                        </h2>
                    <?php endif; ?>

                    <!-- 내용 (Bootstrap 타이포그래피) -->
                    <p class="mb-2" style="font-size: 15px; color: #050505; line-height: 1.5; white-space: pre-wrap; word-break: break-word;" itemprop="articleBody">
                        <?= nl2br(htmlspecialchars($post->content)) ?>
                    </p>

                    <!-- 이미지 -->
                    <?php if (!empty($post->files)): ?>
                        <?php
                        $files = is_string($post->files) ? explode(',', $post->files) : $post->files;
                        $validFiles = array_filter(array_map('trim', $files));
                        ?>
                        <?php if (!empty($validFiles)): ?>
                            <figure>
                                <div class="row g-2">
                                    <?php
                                    $photoCount = count($validFiles);
                                    $colClass = $photoCount === 1 ? 'col-12' : ($photoCount === 2 ? 'col-6' : ($photoCount === 3 ? 'col-4' : 'col-6 col-md-4 col-lg-3'));
                                    ?>
                                    <?php foreach ($validFiles as $index => $fileUrl): ?>
                                        <div class="<?= $colClass ?>">
                                            <img src="<?= htmlspecialchars($fileUrl) ?>"
                                                alt="Photo <?= $index + 1 ?>"
                                                class="rounded"
                                                style="width: 100%; max-height: 512px; object-fit: cover; cursor: pointer;"
                                                itemprop="image">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </figure>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- 게시물 액션 버튼 (Bootstrap 버튼 그룹) -->
                <div class="card-footer bg-white d-flex gap-2 justify-content-around border-top py-2">
                    <button class="card-link text-decoration-none text-secondary fw-bold small border-0 bg-transparent">
                        <i class="fa-regular fa-thumbs-up me-2"></i>
                        <span>Like</span>
                    </button>
                    <button class="card-link text-decoration-none text-secondary fw-bold small border-0 bg-transparent">
                        <i class="fa-regular fa-comment me-2"></i>
                        <span>Comment<?= $post->comment_count > 0 ? ' (' . $post->comment_count . ')' : '' ?></span>
                    </button>
                    <button class="card-link text-decoration-none text-secondary fw-bold small border-0 bg-transparent">
                        <i class="fa-regular fa-share-from-square me-2"></i>
                        <span>Share</span>
                    </button>
                </div>

                <!-- 댓글 섹션 (Bootstrap 패딩) -->
                <?php if ($post->comment_count > 0): ?>
                    <?php
                    $comments = get_comments([
                        'post_id' => $post->id,
                        'limit' => 3
                    ]);
                    ?>
                    <?php if (!empty($comments)): ?>
                        <div class="card-footer" itemscope itemtype="http://schema.org/Comment">
                            <!-- 댓글 더보기 버튼 (댓글 목록 위) -->
                            <?php if ($post->comment_count > 3): ?>
                                <div class="text-center my-3">
                                    <button class="btn btn-link text-decoration-none text-muted p-2">
                                        <i class="fa-regular fa-comment-dots me-2"></i>
                                        <span class="small"><?= $post->comment_count - 3 ?>개의 댓글이 더 있습니다. <b>더보기</b></span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- 댓글 목록 -->
                            <section>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="mb-2">
                                        <div class="d-flex align-items-start gap-2">
                                            <!-- 댓글 작성자 아바타 (Bootstrap 유틸리티) -->
                                            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                                                 style="width: 32px; height: 32px; background-color: #e4e6eb;">
                                                <?php if ($comment->author && $comment->author->photo_url): ?>
                                                    <img src="<?= htmlspecialchars($comment->author->photo_url) ?>"
                                                        alt="<?= htmlspecialchars($comment->author->first_name) ?>"
                                                        class="rounded-circle"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="fa-solid fa-user text-secondary" style="font-size: 14px;"></i>
                                                <?php endif; ?>
                                            </div>

                                            <!-- 댓글 내용 -->
                                            <div class="flex-grow-1">
                                                <!-- 첫 번째 줄: 이름 + 날짜 -->
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <strong class="fw-semibold" style="font-size: 13px; color: #050505;" itemprop="author">
                                                        <?= htmlspecialchars($comment->author->first_name ?? 'Anonymous') ?>
                                                    </strong>
                                                    <time class="text-muted" style="font-size: 12px;" datetime="<?= date('c', $comment->created_at) ?>">
                                                        <?php
                                                        $commentDate = new DateTime('@' . $comment->created_at);
                                                        echo $commentDate->format('Y. m. d. H:i');
                                                        ?>
                                                    </time>
                                                </div>

                                                <!-- 두 번째 줄: 댓글 내용 (연한 회색 배경 박스) -->
                                                <p class="mb-1" style="background-color: #f8f9fa; font-size: 14px; color: #050505; line-height: 1.3; word-break: break-word; white-space: pre-wrap;" itemprop="text">
                                                    <?= htmlspecialchars($comment->content) ?>
                                                </p>

                                                <!-- 세 번째 줄: 액션 버튼 -->
                                                <div class="d-flex align-items-center gap-2">
                                                    <button class="btn btn-link text-decoration-none text-muted p-0 fw-semibold" style="font-size: 11px;">
                                                        Like
                                                    </button>
                                                    <button class="btn btn-link text-decoration-none text-muted p-0 fw-semibold" style="font-size: 11px;">
                                                        Reply<?= $comment->comment_count > 0 ? ' (' . $comment->comment_count . ')' : '' ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </section>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>