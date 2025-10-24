<!-- 최소한의 SEO 콘텐츠 제공 (Vue 마운트 전까지 표시 - post-component 디자인 동일) -->
<div id="ssr-content">
    <?php if ($postList['posts']): ?>
        <?php foreach ($postList['posts'] as $post): ?>
            <article class="post-card" itemscope itemtype="http://schema.org/Article">
                <!-- 게시물 헤더 (작성자 정보) -->
                <header class="post-header">
                    <div class="d-flex align-items-center">
                        <!-- 프로필 사진 -->
                        <div class="post-header-avatar">
                            <?php if ($post->author && $post->author->photo_url): ?>
                                <img src="<?= htmlspecialchars($post->author->photo_url) ?>"
                                    alt="<?= htmlspecialchars($post->author->first_name) ?>"
                                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;"
                                    itemprop="image">
                            <?php else: ?>
                                <i class="fa-solid fa-user"></i>
                            <?php endif; ?>
                        </div>

                        <!-- 사용자 이름, 날짜 -->
                        <div class="post-header-info">
                            <div class="post-header-name" itemprop="author">
                                <?= htmlspecialchars($post->author->first_name ?? 'No name') ?>
                            </div>
                            <div class="post-header-meta">
                                <time datetime="<?= date('c', $post->created_at) ?>" itemprop="datePublished">
                                    <?php
                                    $date = new DateTime('@' . $post->created_at);
                                    echo $date->format('Y. m. d. H:i');
                                    ?>
                                </time>
                                · <span class="badge bg-secondary"><?= $post->visibility ?? 'public' ?></span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- 게시물 본문 -->
                <div class="post-body">
                    <!-- 제목 -->
                    <?php if (!empty($post->title)): ?>
                        <div class="post-title" itemprop="headline">
                            <?= htmlspecialchars($post->title) ?>
                        </div>
                    <?php endif; ?>

                    <!-- 내용 -->
                    <div class="post-content" itemprop="articleBody">
                        <?= nl2br(htmlspecialchars($post->content)) ?>
                    </div>

                    <!-- 이미지 -->
                    <?php if (!empty($post->files)): ?>
                        <?php
                        $files = is_string($post->files) ? explode(',', $post->files) : $post->files;
                        $validFiles = array_filter(array_map('trim', $files));
                        ?>
                        <?php if (!empty($validFiles)): ?>
                            <div class="post-images">
                                <div class="row g-2">
                                    <?php
                                    $photoCount = count($validFiles);
                                    $colClass = $photoCount === 1 ? 'col-12' : ($photoCount === 2 ? 'col-6' : ($photoCount === 3 ? 'col-4' : 'col-6 col-md-4 col-lg-3'));
                                    ?>
                                    <?php foreach ($validFiles as $index => $fileUrl): ?>
                                        <div class="<?= $colClass ?>">
                                            <img src="<?= htmlspecialchars($fileUrl) ?>"
                                                alt="Photo <?= $index + 1 ?>"
                                                style="width: 100%; height: 200px; object-fit: cover;"
                                                itemprop="image">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- 게시물 액션 버튼 -->
                <div class="post-actions">
                    <button class="post-action-btn">
                        <i class="fa-regular fa-thumbs-up"></i>
                        <span>Like</span>
                    </button>
                    <button class="post-action-btn">
                        <i class="fa-regular fa-comment"></i>
                        <span>Comment<?= $post->comment_count > 0 ? ' (' . $post->comment_count . ')' : '' ?></span>
                    </button>
                    <button class="post-action-btn">
                        <i class="fa-regular fa-share-from-square"></i>
                        <span>Share</span>
                    </button>
                </div>

                <!-- 댓글 섹션 -->
                <?php if ($post->comment_count > 0): ?>
                    <?php
                    $comments = get_comments([
                        'post_id' => $post->id,
                        'limit' => 3
                    ]);
                    ?>
                    <?php if (!empty($comments)): ?>
                        <div class="post-comments-section" itemscope itemtype="http://schema.org/Comment">
                            <!-- 댓글 목록 -->
                            <div class="comments-list">
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment-item">
                                        <!-- 댓글 작성자 아바타 -->
                                        <div class="comment-avatar">
                                            <?php if ($comment->author && $comment->author->photo_url): ?>
                                                <img src="<?= htmlspecialchars($comment->author->photo_url) ?>"
                                                    alt="<?= htmlspecialchars($comment->author->first_name) ?>"
                                                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fa-solid fa-user"></i>
                                            <?php endif; ?>
                                        </div>

                                        <!-- 댓글 내용 -->
                                        <div class="comment-content-wrapper">
                                            <!-- 댓글 작성자와 날짜 -->
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <div class="comment-author" itemprop="author">
                                                    <?= htmlspecialchars($comment->author->first_name ?? 'Anonymous') ?>
                                                </div>
                                                <div class="comment-meta">
                                                    <time datetime="<?= date('c', $comment->created_at) ?>">
                                                        <?php
                                                        $commentDate = new DateTime('@' . $comment->created_at);
                                                        echo $commentDate->format('Y. m. d. H:i');
                                                        ?>
                                                    </time>
                                                </div>
                                            </div>
                                            <!-- 댓글 내용 -->
                                            <div class="comment-bubble">
                                                <div class="comment-text" itemprop="text">
                                                    <?= htmlspecialchars($comment->content) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- 더 많은 댓글이 있음을 표시 -->
                            <?php if ($post->comment_count > 3): ?>
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        <span itemprop="commentCount"><?= $post->comment_count - 3 ?></span>개의 댓글 더보기
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>