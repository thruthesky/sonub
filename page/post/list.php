<?php

/**
 * 게시글 목록 페이지
 *
 * SEO를 위해 첫 페이지는 PHP로 SSR(Server-Side Rendering)하고,
 * Vue.js Hydration으로 동적 기능을 추가한 후,
 * 무한 스크롤로 다음 페이지를 로드하는 하이브리드 방식을 사용합니다.
 *
 * @package Sonub
 * @subpackage Pages
 * @since 1.0
 */

// ========================================================================
// 1단계: 파라미터 및 설정
// ========================================================================
$category = http_param('category');
$per_page = 5;
$page = 1;

// ========================================================================
// 2단계: JavaScript 및 CSS 로드
// ========================================================================
inject_post_list_language();
load_deferred_js('infinite-scroll');
// file-upload 컴포넌트를 post 컴포넌트보다 먼저 로드 (의존성)
load_deferred_js('vue-components/file-upload.component');
load_deferred_js('vue-components/post.component');
load_page_css();

// ========================================================================
// 3단계: 첫 페이지 게시글 목록 조회 (SSR용)
// ========================================================================
$offset = ($page - 1) * $per_page;
$postListData = list_posts([
    'category' => $category,
    'limit' => $per_page,
    'offset' => $offset,
    'page' => $page
]);

// list_posts()의 PostListModel을 배열 형식으로 변환
$postList = [
    'posts' => $postListData->posts,
    'page' => $page,
    'isEmpty' => $postListData->isEmpty(),
    'isLastPage' => !$postListData->hasNextPage()
];
?>

<!-- 게시글 작성 위젯 -->
<div>
    <?php include WIDGET_DIR . '/post/post-list-create.php'; ?>
</div>

<style>
    #post-list {
        min-height: 50vh;
        /* Allow natural growth for infinite scroll */
    }
</style>

<!-- 게시글 목록 컨테이너 -->
<div id="post-list" class="mt-4">
    <?php include_once WIDGET_DIR . '/post/list-seo.php' ?>

</div>

<script>
    ready(() => {
        // InfiniteScroll 초기화
        const scrollController = InfiniteScroll.init('body', {
            onScrolledToBottom: () => {
                console.log('하단 도달: 더 많은 데이터 로드');
                if (window.postListApp) {
                    window.postListApp.loadNextPage();
                }
            },
            threshold: 200,
            debounceDelay: 100,
            initialScrollToBottom: false
        });
    });
</script>

<script>
    ready(() => {
        // Vue 앱 초기화
        const app = Vue.createApp({
            components: {
                'post-component': postComponent,
            },
            template: `
                <!-- 게시물이 없을 때 -->
                <div v-if="postList.isEmpty" class="text-center py-5">
                    <i class="fa-regular fa-comment-dots fa-3x mb-3 opacity-25"></i>
                    <p class="text-muted"><?= t()->아직_게시글이_없습니다 ?></p>
                </div>

                <!-- 게시물 목록 -->
                <div v-else>
                    <article v-for="post in postList.posts" :key="post.id" class="post-card">
                        <post-component
                            :post="post"
                            @post-deleted="handlePostDeleted"
                        ></post-component>
                    </article>
                </div>
            `,
            data() {
                // 서버에서 받은 데이터를 window.Store.state.postList에 저장
                Object.assign(window.Store.state.postList, <?= json_encode($postList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);

                return {
                    // 서버에서 hydrate된 게시물 목록 (window.Store 사용)
                    postList: window.Store.state.postList,
                    category: '<?= $category ?>',
                    perPage: <?= $per_page ?>,
                    // 현재 로그인한 사용자 프로필 사진
                    currentUserPhoto: <?= login() && login()->photo_url ? json_encode(login()->photo_url) : 'null' ?>
                };
            },
            methods: {
                /**
                 * 다음 페이지 로드 (무한 스크롤)
                 */
                async loadNextPage() {
                    if (this.postList.isLastPage) {
                        console.log('마지막 페이지에 도달했습니다.');
                        return;
                    }

                    const nextPage = this.postList.page + 1;

                    try {
                        // list_posts API 호출
                        const result = await func('list_posts', {
                            category: this.category,
                            limit: this.perPage,
                            page: nextPage,
                            alertOnError: true,
                        });

                        console.log('다음 페이지 데이터:', result);

                        if (result && result.posts && result.posts.length > 0) {
                            // files 문자열을 배열로 변환
                            result.posts.forEach(post => {
                                if (post.files && typeof post.files === 'string') {
                                    post.files = post.files.split(',').map(f => f.trim()).filter(f => f);
                                }
                                // comments 초기화
                                post.comments = post.comments || [];
                            });

                            this.postList.posts.push(...result.posts);
                            this.postList.page = nextPage;
                            this.postList.isLastPage = result.posts.length < this.perPage;
                            console.log(nextPage + '번째 페이지 로드 완료, 총 게시물 수:', this.postList.posts.length);
                        } else {
                            this.postList.isLastPage = true;
                            console.log('더 이상 로드할 게시물이 없습니다.');
                        }
                    } catch (error) {
                        console.error('게시물 로드 중 오류 발생:', error);
                    }
                },

                /**
                 * 게시물 삭제 이벤트 핸들러
                 * post-component에서 @post-deleted 이벤트가 발생하면 호출됨
                 * @param {number} postId - 삭제된 게시물 ID
                 */
                handlePostDeleted(postId) {
                    console.log('Post deleted event received:', postId);
                    const index = this.postList.posts.findIndex(p => p.id === postId);
                    if (index !== -1) {
                        this.postList.posts.splice(index, 1);
                        console.log('Post removed from list. Remaining posts:', this.postList.posts.length);

                        // 게시물이 없으면 isEmpty 상태 업데이트
                        if (this.postList.posts.length === 0) {
                            this.postList.isEmpty = true;
                        }
                    }
                },
            },
            mounted() {
                console.log('Vue 앱 마운트 완료, 게시물 목록:', this.postList);

                // SSR 콘텐츠 제거 (Vue가 리렌더링하므로)
                const ssrContent = document.getElementById('ssr-content');
                if (ssrContent) {
                    ssrContent.remove();
                    console.log('✅ SSR 콘텐츠 제거 완료 (Vue가 post-component로 리렌더링)');
                }
            }
        });

        const vm = app.mount('#post-list');
        console.log('Vue 인스턴스 마운트됨:', vm);

        // Vue 인스턴스를 전역 변수로 노출
        window.postListApp = vm;
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
