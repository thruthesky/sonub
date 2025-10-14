<?php
// ========================================================================
// 1단계: 파라미터 가져오기
// ========================================================================
$category = http_param('category');
$page = 1; // 첫 페이지
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

// ========================================================================
// 3단계: Infinite Scroll JS 로드
// ========================================================================
load_deferred_js('infinite-scroll');
?>

<style>
    #post-list-container {
        height: calc(100vh - 200px);
        overflow-y: auto;
    }
</style>

<!-- 페이지 컨테이너: 가장자리 여백 최소화 (px-2) -->
<div class="container-fluid px-2 py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">

            <!-- 게시글 작성 위젯 -->
            <?php include WIDGET_DIR . '/post/post-list-create.php'; ?>

            <!-- 게시글 목록 컨테이너 (Vue 앱이 마운트됨) -->
            <div id="post-list-container">
                <!-- Vue 앱이 여기에 렌더링됩니다 -->
            </div>

        </div>
    </div>
</div>

<script>
    ready(() => {
        // InfiniteScroll 초기화
        const scrollController = InfiniteScroll.init('#post-list-container', {
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
        // Vue 앱 초기화
        const app = Vue.createApp({
            template: `
                <div>
                    <!-- 게시물이 없을 때 -->
                    <div v-if="postList.isEmpty" class="text-center py-5">
                        <p class="text-muted"><?= tr(['en' => 'No posts yet', 'ko' => '아직 게시글이 없습니다', 'ja' => 'まだ投稿がありません', 'zh' => '还没有帖子']) ?></p>
                    </div>

                    <!-- 게시물 목록 -->
                    <div v-else class="d-flex flex-column gap-3">
                        <article v-for="post in postList.posts" :key="post.id" class="post-card">
                            <!-- 게시글 헤더 -->
                            <div class="post-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="user-name">사용자 #{{ post.user_id }}</div>
                                        <div class="post-time">{{ formatDate(post.created_at) }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 게시글 내용 -->
                            <div v-if="post.content" class="post-content" v-html="formatContent(post.content)"></div>

                            <!-- 게시글 파일 -->
                            <div v-if="post.files && post.files.length > 0" class="post-files">
                                <!-- 파일 표시 로직은 추후 구현 -->
                            </div>

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
                                    <a :href="'/post/view?id=' + post.id" class="action-btn">
                                        <i class="fa-regular fa-arrow-up-right-from-square"></i>
                                        <span><?= tr(['en' => 'View', 'ko' => '보기', 'ja' => '表示', 'zh' => '查看']) ?></span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- 로딩 중 표시 -->
                    <div v-if="isLoading" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            `,
            data() {
                return {
                    // 서버에서 hydrate된 게시물 목록
                    postList: <?= json_encode($postList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
                    isLoading: false
                };
            },
            methods: {
                async loadNextPage() {
                    // 이미 로딩 중이거나 마지막 페이지인 경우 무시
                    if (this.isLoading || this.postList.isLastPage) {
                        console.log('로딩 중이거나 마지막 페이지입니다.');
                        return;
                    }

                    const nextPage = this.postList.page + 1;
                    this.isLoading = true;

                    try {
                        const obj = await func('list_posts', {
                            category: '<?= $category ?>',
                            limit: <?= $per_page ?>,
                            page: nextPage,
                            alertOnError: true,
                        });

                        console.log('다음 페이지 데이터:', obj);

                        if (obj.posts && obj.posts.length > 0) {
                            this.postList.posts.push(...obj.posts);
                            this.postList.page = nextPage;
                            this.postList.isLastPage = obj.posts.length < <?= $per_page ?>;
                            console.log(nextPage + '번 째 페이지 로드 완료, 총 게시물 수:', this.postList.posts.length);
                        } else {
                            this.postList.isLastPage = true;
                            console.log('더 이상 로드할 게시물이 없습니다.');
                        }
                    } catch (error) {
                        console.error('게시물 로드 중 오류 발생:', error);
                    } finally {
                        this.isLoading = false;
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
                    const date = new Date(timestamp * 1000); // Unix timestamp를 밀리초로 변환
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
                console.log('Vue 앱 마운트 완료, 게시물 목록:', this.postList);
            }
        });

        const vm = app.mount('#post-list-container');
        console.log('Vue 인스턴스 마운트됨:', vm);

        // Vue 인스턴스를 전역 변수로 노출
        window.postListVm = vm;
    });
</script>
