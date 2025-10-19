<?php
$category = 'story';
$per_page = 20;
$page = 1;
inject_index_language();
load_deferred_js('infinite-scroll');
?>

<div>
    <!-- 게시글 작성 위젯 -->
    <?php
    include WIDGET_DIR . '/post/post-list-create.php';
    ?>
</div>

<?php
$offset = ($page - 1) * $per_page;
$postList = list_posts([
    'user_id' => login() ? login()->id : null,
    'limit' => $per_page,
    'offset' => $offset,
    'page' => $page
]);




?>

<style>
    #my-wall {
        height: calc(100vh - 200px);
    }
</style>

<h1>해야 할 일: (1) 친구 맺기 요청. 일방 요청이면 팔로우, 쌍방 요청이면 친구 관계 (2) 친구 보낸 요청 목록 및 취소, (3) 친구 받은 요청 목록, 확인, 거절 (4) 총 친구 요청 수 뱃지로 표시.</h1>

<div id="my-wall" class="container mt-4">
    <!-- Vue 앱이 여기에 렌더링됩니다 -->
</div>

<script>
    ready(() => {
        // InfiniteScroll 초기화
        const scrollController = InfiniteScroll.init('body', {
            onScrolledToBottom: () => {
                console.log('하단 도달: 더 많은 데이터 로드');
                if (window.myWallVm) {
                    window.myWallVm.loadNextPage();
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
                        <p class="text-muted">아직 게시물이 없습니다.</p>
                    </div>

                    <!-- 게시물 목록 -->
                    <div v-else>
                        <article v-for="post in postList.posts" :key="post.id" class="post-card card mb-3">
                            <div class="card-body">
                                <!-- 게시물 내용 -->
                                <div v-if="post.content" class="post-content mb-3" v-html="formatContent(post.content)"></div>

                                <!-- 게시물 사진들 -->
                                <div v-if="hasPhotos(post.files)" class="post-photos mb-3">
                                    <div class="row g-2">
                                        <div v-for="(fileUrl, index) in getValidPhotos(post.files)" :key="index"
                                             :class="getPhotoColumnClass(getValidPhotos(post.files).length)">
                                            <img :src="thumbnail(fileUrl, 400, 400, 'cover', 85, 'ffffff')"
                                                 :alt="'Photo ' + (index + 1)"
                                                 class="img-fluid rounded"
                                                 style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                                                 @click="openPhotoModal(fileUrl)">
                                        </div>
                                    </div>
                                </div>

                                <!-- 게시물 메타 정보 -->
                                <div class="post-meta text-muted small mt-2">
                                    <span>작성자: {{ post.user_display_name || '익명' }}</span>
                                    <span class="ms-3">작성일: {{ formatDate(post.created_at) }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            `,
            data() {
                return {
                    // 서버에서 hydrate된 게시물 목록
                    postList: <?= json_encode($postList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
                };
            },
            methods: {
                thumbnail: thumbnail,
                async loadNextPage() {
                    if (this.postList.isLastPage) {
                        console.log('마지막 페이지에 도달했습니다.');
                        return;
                    }
                    const nextPage = this.postList.page + 1;
                    try {
                        const obj = await func('list_posts', {
                            category: '<?= $category ?>',
                            user_id: <?= login() ? login()->id : 'null' ?>,
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
                    }
                },
                /**
                 * 게시물 새로고침
                 */
                refreshPosts() {
                    window.location.reload();
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
                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleString('ko-KR');
                },
                /**
                 * 사진 개수에 따른 Bootstrap column 클래스 반환
                 * @param {number} photoCount - 사진 개수
                 * @returns {string} Bootstrap column 클래스
                 */
                getPhotoColumnClass(photoCount) {
                    if (photoCount === 1) return 'col-12';
                    if (photoCount === 2) return 'col-6';
                    if (photoCount === 3) return 'col-4';
                    return 'col-6 col-md-4 col-lg-3'; // 4개 이상
                },
                /**
                 * 사진 모달 열기 (확대 보기)
                 * @param {string} photoUrl - 사진 URL
                 */
                openPhotoModal(photoUrl) {
                    // Bootstrap 모달이나 라이트박스를 사용할 수 있습니다
                    // 간단하게 새 창으로 열기
                    window.open(photoUrl, '_blank');
                },
                /**
                 * 유효한 사진이 있는지 확인
                 * @param {Array} files - 파일 URL 배열
                 * @returns {boolean} 유효한 사진이 하나라도 있으면 true
                 */
                hasPhotos(files) {
                    if (!files || !Array.isArray(files) || files.length === 0) {
                        return false;
                    }
                    return files.some(url => url && url.trim() !== '');
                },
                /**
                 * 유효한 사진 URL만 필터링
                 * @param {Array} files - 파일 URL 배열
                 * @returns {Array} 빈 문자열이 제거된 URL 배열
                 */
                getValidPhotos(files) {
                    if (!files || !Array.isArray(files)) {
                        return [];
                    }
                    return files.filter(url => url && url.trim() !== '');
                }
            },
            mounted() {
                console.log('Vue 앱 마운트 완료, 게시물 목록:', this.postList);
            }
        });

        const vm = app.mount('#my-wall');
        console.log('Vue 인스턴스 마운트됨:', vm);

        // Vue 인스턴스를 전역 변수로 노출
        window.myWallVm = vm;
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
