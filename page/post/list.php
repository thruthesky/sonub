<?php

/**
 * 게시글 목록 페이지
 *
 * SEO를 위해 첫 페이지는 PHP로 SSR(Server-Side Rendering)하고,
 * 이후 무한 스크롤은 Vue.js로 처리하는 하이브리드 방식을 사용합니다.
 *
 * @package Sonub
 * @subpackage Pages
 * @since 1.0
 */

// ========================================================================
// 1단계: 파라미터 가져오기
// ========================================================================
$category = http_param('category');
$page = 1; // 첫 페이지 (SSR용)
$per_page = 20; // 페이지당 게시글 수

// ========================================================================
// 2단계: 첫 페이지 게시글 목록 조회 (SSR용)
// ========================================================================
$offset = ($page - 1) * $per_page;
$postList = list_posts([
    'category' => $category,
    'limit' => $per_page,
    'offset' => $offset,
    'page' => $page
]);

// ========================================================================
// 3단계: Infinite Scroll JS 로드
// ========================================================================
load_deferred_js('infinite-scroll');

// 번역 함수 호출
inject_post_list_language();
?>

<!-- 페이지 컨테이너 -->
<div class="container-fluid px-0 py-3">
    <div class="row g-0">
        <div class="col-12">

            <!-- 게시글 작성 위젯 -->
            <?php include WIDGET_DIR . '/post/post-list-create.php'; ?>

            <!-- 게시글 목록 컨테이너 -->
            <div id="post-list-container" class="mt-3">

                <!-- 첫 페이지 게시글 목록 (PHP SSR - SEO용) -->
                <?php if ($postList->isEmpty()): ?>
                    <!-- 게시글이 없을 때 -->
                    <div class="text-center py-5 text-muted">
                        <i class="fa-regular fa-comment-dots fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0"><?= t()->아직_게시글이_없습니다 ?></p>
                    </div>
                <?php else: ?>
                    <!-- 게시글 목록 -->
                    <div class="d-flex flex-column gap-3" id="ssr-posts">
                        <?php foreach ($postList->posts as $post): ?>
                            <article class="post-card">
                                <!-- 게시글 헤더 -->
                                <div class="post-header">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="user-avatar flex-shrink-0">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="user-name"><?= t()->사용자 ?> #<?= $post->user_id ?></div>
                                            <div class="post-time"><?= date('Y.m.d H:i', $post->created_at) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 게시글 제목 -->
                                <?php if (!empty($post->title)): ?>
                                    <h2 class="post-title"><?= htmlspecialchars($post->title) ?></h2>
                                <?php endif; ?>

                                <!-- 게시글 내용 -->
                                <?php if (!empty($post->content)): ?>
                                    <div class="post-content"><?= nl2br(htmlspecialchars($post->content)) ?></div>
                                <?php endif; ?>

                                <!-- 게시글 이미지 -->
                                <?php if (!empty($post->files)): ?>
                                    <?php
                                    $files = is_string($post->files) ? explode(',', $post->files) : $post->files;
                                    ?>
                                    <div class="post-images px-1 pb-1">
                                        <?php foreach ($files as $file): ?>
                                            <img src="<?= htmlspecialchars(trim($file)) ?>"
                                                alt="게시글 이미지"
                                                class="post-image rounded">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- 게시글 푸터 -->
                                <div class="post-footer">
                                    <div class="post-actions">
                                        <button type="button" class="action-btn" aria-label="<?= t()->좋아요 ?>">
                                            <i class="fa-regular fa-heart"></i>
                                            <span><?= t()->좋아요 ?></span>
                                        </button>
                                        <button type="button" class="action-btn" aria-label="<?= t()->댓글 ?>">
                                            <i class="fa-regular fa-comment"></i>
                                            <span><?= t()->댓글 ?></span>
                                        </button>
                                        <a href="<?= href()->post->view($post->id) ?>"
                                            class="action-btn"
                                            aria-label="<?= t()->보기 ?>">
                                            <i class="fa-regular fa-arrow-up-right-from-square"></i>
                                            <span><?= t()->보기 ?></span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Vue.js 동적 게시글 목록 (무한 스크롤용) -->
                <div id="vue-posts" class="d-flex flex-column gap-3 mt-3"></div>

                <!-- 로딩 인디케이터 -->
                <div id="loading-indicator" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden"><?= t()->로딩_중 ?></span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    ready(() => {
        // ============================================================
        // Infinite Scroll 초기화
        // ============================================================
        const scrollController = InfiniteScroll.init('body', {
            onScrolledToBottom: () => {
                console.log('하단 도달: 더 많은 데이터 로드');
                if (window.postListVm) {
                    window.postListVm.loadNextPage();
                }
            },
            threshold: 10,
            debounceDelay: 100,
            initialScrollToBottom: false
        });
    });
</script>

<script>
    ready(() => {
        // ============================================================
        // Vue 앱 초기화 (무한 스크롤용)
        // ============================================================
        const app = Vue.createApp({
            template: `
            <div>
                <!-- Vue로 로드된 게시글 목록 -->
                <article v-for="post in posts" :key="post.id" class="post-card">
                    <!-- 게시글 헤더 -->
                    <div class="post-header">
                        <div class="d-flex align-items-start gap-2">
                            <div class="user-avatar flex-shrink-0">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="user-name"><?= t()->사용자 ?> #{{ post.user_id }}</div>
                                <div class="post-time">{{ formatDate(post.created_at) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- 게시글 제목 -->
                    <h2 v-if="post.title" class="post-title">{{ post.title }}</h2>

                    <!-- 게시글 내용 -->
                    <div v-if="post.content" class="post-content" v-html="formatContent(post.content)"></div>

                    <!-- 게시글 이미지 -->
                    <div v-if="post.files && post.files.length > 0" class="post-images px-1 pb-1">
                        <img v-for="(file, index) in post.files" :key="index"
                             :src="file" alt="게시글 이미지" class="post-image rounded">
                    </div>

                    <!-- 게시글 푸터 -->
                    <div class="post-footer">
                        <div class="post-actions">
                            <button type="button" class="action-btn" :aria-label="'<?= t()->좋아요 ?>'">
                                <i class="fa-regular fa-heart"></i>
                                <span><?= t()->좋아요 ?></span>
                            </button>
                            <button type="button" class="action-btn" :aria-label="'<?= t()->댓글 ?>'">
                                <i class="fa-regular fa-comment"></i>
                                <span><?= t()->댓글 ?></span>
                            </button>
                            <a :href="'/post/view?id=' + post.id" class="action-btn" :aria-label="'<?= t()->보기 ?>'">
                                <i class="fa-regular fa-arrow-up-right-from-square"></i>
                                <span><?= t()->보기 ?></span>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        `,
            data() {
                return {
                    posts: [], // Vue로 로드된 게시글 (2페이지부터)
                    currentPage: 1, // 현재 페이지 (첫 페이지는 PHP SSR)
                    isLoading: false,
                    isLastPage: <?= !$postList->hasNextPage() ? 'true' : 'false' ?>
                };
            },
            methods: {
                async loadNextPage() {
                    // 이미 로딩 중이거나 마지막 페이지인 경우 무시
                    if (this.isLoading || this.isLastPage) {
                        console.log('로딩 중이거나 마지막 페이지입니다.');
                        return;
                    }

                    const nextPage = this.currentPage + 1;
                    this.isLoading = true;

                    // 로딩 인디케이터 표시
                    document.getElementById('loading-indicator').style.display = 'block';

                    try {
                        const obj = await func('list_posts', {
                            category: '<?= $category ?>',
                            limit: <?= $per_page ?>,
                            page: nextPage,
                            alertOnError: true,
                        });

                        console.log('다음 페이지 데이터:', obj);

                        if (obj.posts && obj.posts.length > 0) {
                            // files 문자열을 배열로 변환
                            obj.posts.forEach(post => {
                                if (post.files && typeof post.files === 'string') {
                                    post.files = post.files.split(',').map(f => f.trim());
                                }
                            });

                            this.posts.push(...obj.posts);
                            this.currentPage = nextPage;
                            this.isLastPage = obj.posts.length < <?= $per_page ?>;
                            console.log(nextPage + '번째 페이지 로드 완료, 총 게시물 수:', this.posts.length);
                        } else {
                            this.isLastPage = true;
                            console.log('더 이상 로드할 게시물이 없습니다.');
                        }
                    } catch (error) {
                        console.error('게시물 로드 중 오류 발생:', error);
                    } finally {
                        this.isLoading = false;
                        // 로딩 인디케이터 숨김
                        document.getElementById('loading-indicator').style.display = 'none';
                    }
                },
                /**
                 * 게시물 내용 포맷팅 (줄바꿈 처리)
                 */
                formatContent(content) {
                    if (!content) return '';
                    return content.replace(/\n/g, '<br>');
                },
                /**
                 * 날짜 포맷팅
                 */
                formatDate(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp * 1000);
                    return date.toLocaleString('ko-KR', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            },
            mounted() {
                console.log('Vue 앱 마운트 완료');
            }
        });

        const vm = app.mount('#vue-posts');
        console.log('Vue 인스턴스 마운트됨:', vm);

        // Vue 인스턴스를 전역 변수로 노출
        window.postListVm = vm;
    });
</script>

<?php
/**
 * 다국어 번역 주입
 *
 * 이 함수는 페이지에서 사용하는 모든 텍스트를 4개 국어로 번역합니다.
 *
 * @return void
 */
function inject_post_list_language(): void
{
    t()->inject([
        '아직_게시글이_없습니다' => [
            'ko' => '아직 게시글이 없습니다',
            'en' => 'No posts yet',
            'ja' => 'まだ投稿がありません',
            'zh' => '暂无帖子'
        ],
        '사용자' => [
            'ko' => '사용자',
            'en' => 'User',
            'ja' => 'ユーザー',
            'zh' => '用户'
        ],
        '좋아요' => [
            'ko' => '좋아요',
            'en' => 'Like',
            'ja' => 'いいね',
            'zh' => '点赞'
        ],
        '댓글' => [
            'ko' => '댓글',
            'en' => 'Comment',
            'ja' => 'コメント',
            'zh' => '评论'
        ],
        '보기' => [
            'ko' => '보기',
            'en' => 'View',
            'ja' => '表示',
            'zh' => '查看'
        ],
        '로딩_중' => [
            'ko' => '로딩 중...',
            'en' => 'Loading...',
            'ja' => '読み込み中...',
            'zh' => '加载中...'
        ],
    ]);
}
