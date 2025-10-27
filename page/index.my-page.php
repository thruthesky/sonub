<?php

$per_page = 5;
$page = 1;
inject_index_language();
load_deferred_js('infinite-scroll');
// file-upload 컴포넌트를 post 컴포넌트보다 먼저 로드 (의존성)
load_deferred_js('vue-components/file-upload.component');
load_deferred_js('vue-components/post.component');
load_page_css();
?>

<div>
    <!-- 게시글 작성 위젯 -->
    <?php
    include WIDGET_DIR . '/post/post-list-create.php';
    ?>
</div>



<?php
// **중요: get_posts_from_feed_entries()는 오직 feed_entries 테이블에서만 조회합니다**
// 더 이상 posts 테이블에서 추가 조회하지 않으므로, fanout된 글만 표시됩니다.

$offset = ($page - 1) * $per_page;
$feedItems = get_posts_from_feed_entries([
    'me' => login()->id,
    'limit' => $per_page,
    'offset' => $offset
]);

// get_posts_from_feed_entries()는 배열을 직접 반환하므로, list_posts() 형식으로 변환
$postList = [
    'posts' => $feedItems,
    'page' => $page,
    'isEmpty' => empty($feedItems),
    'isLastPage' => count($feedItems) < $per_page
];

?>

<style>
    #my-page {
        min-height: 50vh;
        /* Allow natural growth for infinite scroll */
    }
</style>

<div id="my-page" class="mt-4">

    <?php include WIDGET_DIR . '/loading/skeleton.php' ?>

    <!-- Vue 앱이 여기에 렌더링됩니다 -->
</div>

<script>
    ready(() => {
        // InfiniteScroll 초기화
        const scrollController = InfiniteScroll.init('body', {
            onScrolledToBottom: () => {
                console.log('하단 도달: 더 많은 데이터 로드');
                if (window.myPageApp) {
                    window.myPageApp.loadNextPage();
                }
            },
            threshold: 400,
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
                        <p class="text-muted">아직 게시물이 없습니다.</p>
                    </div>

                    <!-- 게시물 목록 -->
                    <div v-else>
                        <article v-for="post in postList.posts" :key="post.post_id" class="post-card">
                            <post-component
                                :post="post"
                                @post-deleted="handlePostDeleted"
                            ></post-component>
                        </article>
                    </div>
            `,
            data() {

                Object.assign(window.Store.state.postList, <?= json_encode($postList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);
                return {
                    // 서버에서 hydrate된 게시물 목록
                    postList: window.Store.state.postList,
                    toogleCommentBox: false,
                    // 현재 로그인한 사용자 프로필 사진
                    currentUserPhoto: <?= login() && login()->photo_url ? json_encode(login()->photo_url) : 'null' ?>
                };
            },
            methods: {
                async loadNextPage() {
                    if (this.postList.isLastPage) {
                        console.log('마지막 페이지에 도달했습니다.');
                        return;
                    }
                    const nextPage = this.postList.page + 1;
                    try {
                        // get_posts_from_feed_entries API 호출: feed_entries 테이블에서 글 번호를 가져와 글 제목/내용/글쓴이 등의 정보를 함께 리턴
                        const offset = nextPage * <?= $per_page ?>;
                        const feedItems = await func('get_posts_from_feed_entries', {
                            me: <?= login() ? login()->id : 'null' ?>,
                            limit: <?= $per_page ?>,
                            offset: offset,
                            alertOnError: true,
                        });
                        console.log('다음 페이지 데이터:', feedItems);
                        if (feedItems && feedItems.length > 0) {
                            this.postList.posts.push(...feedItems);
                            this.postList.page = nextPage;
                            this.postList.isLastPage = feedItems.length < <?= $per_page ?>;
                            console.log(nextPage + '번 째 페이지 로드 완료, 총 게시물 수:', this.postList.posts.length);
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
                    const index = this.postList.posts.findIndex(p => p.post_id === postId);
                    if (index !== -1) {
                        this.postList.posts.splice(index, 1);
                        console.log('Post removed from list. Remaining posts:', this.postList.posts.length);
                    }
                },
            },
            mounted() {
                console.log('Vue 앱 마운트 완료, 게시물 목록:', this.postList);

                // 스켈레톤 로더 숨기기
                const skeleton = document.getElementById('skeleton-loader');
                if (skeleton) {
                    skeleton.remove();
                }
            }
        });

        const vm = app.mount('#my-page');
        console.log('Vue 인스턴스 마운트됨:', vm);

        // Vue 인스턴스를 전역 변수로 노출
        window.myPageApp = vm;
    });
</script>


<?php

function inject_index_language()
{
    t()->inject([
        'Welcome to Sonub' => ['ko' => 'Sonub에 오신 것을 환영합니다', 'en' => 'Welcome to Sonub', 'ja' => 'Sonubへようこそ', 'zh' => '欢迎使用Sonub'],
        'Sonub application is running!' => ['ko' => 'Sonub 애플리케이션이 실행 중입니다!', 'en' => 'Sonub application is running!', 'ja' => 'Sonubアプリケーションが実行中です！', 'zh' => 'Sonub应用程序正在运行！'],
        'Browser Language' => ['ko' => '브라우저 언어', 'en' => 'Browser Language', 'ja' => 'ブラウザ言語', 'zh' => '浏览器语言'],
        'This is a sample application using Firebase Phone Authentication and a MySQL database.' => ['ko' => '이것은 Firebase 전화 인증과 MySQL 데이터베이스를 사용하는 샘플 애플리케이션입니다.', 'en' => 'This is a sample application using Firebase Phone Authentication and a MySQL database.', 'ja' => 'これはFirebase電話認証とMySQLデータベースを使用したサンプルアプリケーションです。', 'zh' => '这是一个使用Firebase电话认证和MySQL数据库的示例应用程序。'],
        'Getting Started' => ['ko' => '시작하기', 'en' => 'Getting Started', 'ja' => 'はじめに', 'zh' => '入门指南'],
        'Start building your application with our powerful features.' => ['ko' => '강력한 기능으로 애플리케이션 구축을 시작하세요.', 'en' => 'Start building your application with our powerful features.', 'ja' => '強力な機能でアプリケーションの構築を開始しましょう。', 'zh' => '使用我们的强大功能开始构建您的应用程序。'],
        'Learn More' => ['ko' => '더 알아보기', 'en' => 'Learn More', 'ja' => 'もっと詳しく', 'zh' => '了解更多'],
        '환영합니다' => ['ko' => '환영합니다', 'en' => 'Welcome', 'ja' => 'ようこそ', 'zh' => '欢迎'],
        '손님' => ['ko' => '손님', 'en' => 'Guest', 'ja' => 'ゲスト', 'zh' => '访客'],
    ]);
}
