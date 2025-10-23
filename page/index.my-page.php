<?php

$per_page = 10;
$page = 1;
inject_index_language();
load_deferred_js('infinite-scroll');
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
// 로그인한 사용자의 피드 조회 (내 글 + 친구 글 + pending 상태 친구 글)
// **중요: get_posts_from_feed_entries()는 오직 feed_entries 테이블에서만 조회합니다**
// 더 이상 posts 테이블에서 추가 조회하지 않으므로, fanout된 글만 표시됩니다.
if (login()) {
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
} else {
    // 비로그인 사용자: 빈 피드 표시
    $postList = [
        'posts' => [],
        'page' => 1,
        'isEmpty' => true,
        'isLastPage' => true
    ];
}
?>

<style>
    #my-page {
        min-height: 50vh;
        /* Allow natural growth for infinite scroll */
    }

    /* 게시물 카드 스타일 */
    .post-card {
        background-color: white;
        border-radius: 8px;
        border: 1px solid #e4e6eb;
        margin-bottom: 16px;
    }

    /* 게시물 헤더 */
    .post-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid #e4e6eb;
    }

    .post-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 12px;
        background-color: #e4e6eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .post-header-avatar i {
        font-size: 20px;
        color: #65676b;
    }

    .post-header-info {
        flex: 1;
    }

    .post-header-name {
        font-size: 15px;
        font-weight: 600;
        color: #050505;
        line-height: 1.3;
    }

    .post-header-meta {
        font-size: 13px;
        color: #65676b;
        line-height: 1.3;
    }

    /* 게시물 본문 */
    .post-body {
        padding: 16px;
    }

    .post-title {
        font-size: 18px;
        font-weight: 600;
        color: #050505;
        margin-bottom: 8px;
    }

    .post-content {
        font-size: 15px;
        color: #050505;
        line-height: 1.5;
        margin-bottom: 12px;
    }

    /* 게시물 이미지 */
    .post-images {
        margin-top: 12px;
    }

    .post-images img {
        width: 100%;
        border-radius: 8px;
        cursor: pointer;
    }

    .post-images-placeholder {
        background-color: #f0f2f5;
        border-radius: 8px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #65676b;
    }

    /* 게시물 액션 버튼 */
    .post-actions {
        display: flex;
        border-top: 1px solid #e4e6eb;
        padding: 4px 16px;
    }

    .post-action-btn {
        flex: 1;
        background: none;
        border: none;
        padding: 8px;
        color: #65676b;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 4px;
        transition: background-color 0.15s ease;
    }

    .post-action-btn:hover {
        background-color: #f0f2f5;
    }

    .post-action-btn i {
        font-size: 18px;
    }

    /* 댓글 섹션 */
    .post-comments-section {
        border-top: 1px solid #e4e6eb;
        padding: 12px 16px;
    }

    /* 댓글 입력 박스 */
    .comment-input-box {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .comment-input-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #e4e6eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .comment-input-avatar i {
        font-size: 16px;
        color: #65676b;
    }

    .comment-input-wrapper {
        flex: 1;
        display: flex;
        gap: 8px;
    }

    .comment-input {
        flex: 1;
        border: 1px solid #ccc;
        border-radius: 18px;
        padding: 8px 12px;
        font-size: 15px;
        outline: none;
        background-color: #f0f2f5;
    }

    .comment-input:focus {
        background-color: white;
        border-color: #0866ff;
    }

    .comment-submit-btn {
        background: none;
        border: none;
        color: #0866ff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        padding: 8px 12px;
    }

    .comment-submit-btn:disabled {
        color: #bcc0c4;
        cursor: not-allowed;
    }

    /* 댓글 목록 */
    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .comment-item {
        display: flex;
        gap: 8px;
    }

    .comment-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #e4e6eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .comment-avatar i {
        font-size: 16px;
        color: #65676b;
    }

    .comment-content-wrapper {
        flex: 1;
    }

    .comment-bubble {
        background-color: #f0f2f5;
        border-radius: 18px;
        padding: 8px 12px;
        display: inline-block;
    }

    .comment-author {
        font-size: 13px;
        font-weight: 600;
        color: #050505;
        margin-bottom: 2px;
    }

    .comment-text {
        font-size: 15px;
        color: #050505;
        line-height: 1.3333;
    }

    .comment-meta {
        font-size: 12px;
        color: #65676b;
        margin-top: 4px;
        padding-left: 12px;
    }

    /* 반응형 디자인 */
    @media (max-width: 768px) {
        .post-header-avatar {
            width: 36px;
            height: 36px;
        }

        .post-action-btn {
            font-size: 13px;
        }

        .comment-input {
            font-size: 14px;
        }
    }

    /* 게시물 메뉴 드롭다운 */
    .post-menu-dropdown {
        min-width: 120px;
        z-index: 1000;
    }

    .post-menu-dropdown button:hover {
        background-color: #f0f2f5;
    }

    .post-menu-dropdown button.text-danger:hover {
        background-color: #ffe8e8;
    }

    /* 스켈레톤 로더 스타일 */
    .skeleton {
        background: linear-gradient(90deg, #f0f2f5 25%, #e4e6eb 50%, #f0f2f5 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .skeleton-text {
        border-radius: 4px;
    }

    .skeleton-image {
        width: 100%;
        height: 200px;
        border-radius: 8px;
    }

    /* Image delete button in edit mode */
    .image-delete-btn {
        transition: all 0.2s ease;
    }

    .image-delete-btn:hover {
        opacity: 1 !important;
        transform: scale(1.1);
    }
</style>

<div id="my-page" class="mt-4">
    <!-- 로딩 스켈레톤 (Vue 마운트 전에만 표시) -->
    <div id="skeleton-loader">
        <!-- 스켈레톤 게시물 카드 1 -->
        <article class="post-card skeleton-card">
            <div class="post-header">
                <div class="skeleton skeleton-avatar"></div>
                <div style="flex: 1;">
                    <div class="skeleton skeleton-text" style="width: 150px; height: 16px; margin-bottom: 8px;"></div>
                    <div class="skeleton skeleton-text" style="width: 100px; height: 14px;"></div>
                </div>
            </div>
            <div class="post-body">
                <div class="skeleton skeleton-text" style="width: 100%; height: 16px; margin-bottom: 8px;"></div>
                <div class="skeleton skeleton-text" style="width: 80%; height: 16px; margin-bottom: 8px;"></div>
                <div class="skeleton skeleton-text" style="width: 60%; height: 16px;"></div>
                <div class="skeleton skeleton-image" style="margin-top: 12px;"></div>
            </div>
        </article>

        <!-- 스켈레톤 게시물 카드 2 -->
        <article class="post-card skeleton-card">
            <div class="post-header">
                <div class="skeleton skeleton-avatar"></div>
                <div style="flex: 1;">
                    <div class="skeleton skeleton-text" style="width: 150px; height: 16px; margin-bottom: 8px;"></div>
                    <div class="skeleton skeleton-text" style="width: 100px; height: 14px;"></div>
                </div>
            </div>
            <div class="post-body">
                <div class="skeleton skeleton-text" style="width: 100%; height: 16px; margin-bottom: 8px;"></div>
                <div class="skeleton skeleton-text" style="width: 90%; height: 16px; margin-bottom: 8px;"></div>
            </div>
        </article>
    </div>

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
            components: {
                'post-component': postComponent,
            },
            template: /*html*/ `
                    <!-- 게시물이 없을 때 -->
                    <div v-if="postList.isEmpty" class="text-center py-5">
                        <p class="text-muted">아직 게시물이 없습니다.</p>
                    </div>

                    <!-- 게시물 목록 -->
                    <div v-else>
                        <article v-for="post in postList.posts" :key="post.post_id" class="post-card">
                            <post-component :post="post"></post-component>
                        </article>
                    </div>
            `,
            data() {
                return {
                    // 서버에서 hydrate된 게시물 목록
                    postList: <?= json_encode($postList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
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
                 * 게시물 새로고침
                 */
                refreshPosts() {
                    window.location.reload();
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
