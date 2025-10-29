<?php

require_once __DIR__ . '/../../widgets/page-header.php';
/**
 * 사용자 목록 페이지
 *
 * Vue.js를 사용하여 사용자 목록을 표시하고, infinite scroll로 추가 로드합니다.
 * Bootstrap 카드 컴포넌트를 사용하여 사용자 정보를 표시합니다.
 */

/**
 * 다국어 번역 주입 함수
 *
 * 이 페이지에서 사용하는 모든 번역 텍스트를 주입합니다.
 */


inject_list_language();

// 초기 사용자 목록 조회 (첫 페이지, 20명)
$result = list_users(['per_page' => 20, 'page' => 1]);
$users = $result['users'] ?? [];
$total = $result['total'] ?? 0;

// 내 친구 목록은 위젯 내부에서 조회합니다.

// 친구 요청 수 및 친구 수 조회 (로그인한 경우에만)
$friendRequestsSentCount = 0;
$friendRequestsReceivedCount = 0;
$friendsCount = 0;
if (login()) {
    $myUserId = login()->id;
    $friendRequestsSentCount = count_friend_requests_sent(['me' => $myUserId]);
    $friendRequestsReceivedCount = count_friend_requests_received(['me' => $myUserId]);

    // 친구 ID 목록을 가져와서 개수 계산
    $friendIds = get_friend_ids(['me' => $myUserId]);
    $friendsCount = count($friendIds);
}

// Vue.js hydration을 위한 데이터 준비 (친구 목록 제외)
$hydrationData = [
    'users' => $users,
    'total' => $total,
    'currentPage' => 1,
    'perPage' => 20
];

// InfiniteScroll 라이브러리 로드
load_deferred_js('infinite-scroll');

// 사용자 검색 컴포넌트 자동 로드
load_deferred_js('vue-components/user-search.component');
?>

<div class="container">
    <?php
    // 헤더 위젯 표시
    page_header([
        'title' => t()->사용자_목록,
        'icon' => 'fa-user',
        'breadcrumbs' => [
            ['label' => t()->홈, 'url' => href()->home, 'icon' => 'fa-house']
        ],
    ]);
    ?>



    <!-- Friend Action Buttons -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <!-- 사용자 검색 컴포넌트 (자동 마운트) -->
        <div class="user-search-component"></div>

        <!-- Friends List Button -->
        <a href="<?= href()->friend->list ?>" class="btn-pill-gray">
            <i class="bi bi-people me-2"></i>
            <?= t()->친구_목록 ?>
            <?php if ($friendsCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                    <?= $friendsCount ?>
                    <span class="visually-hidden"><?= t()->전체_친구_수 ?></span>
                </span>
            <?php endif; ?>
        </a>

        <!-- Received Requests Button -->
        <a href="<?= href()->friend->request_received ?>" class="btn-pill-gray">
            <i class="bi bi-inbox me-2"></i>
            <?= t()->받은_요청 ?>
            <?php if ($friendRequestsReceivedCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $friendRequestsReceivedCount ?>
                    <span class="visually-hidden"><?= t()->받은_친구_요청 ?></span>
                </span>
            <?php endif; ?>
        </a>

        <!-- Sent Requests Button -->
        <a href="<?= href()->friend->request_sent ?>" class="btn-pill-gray position-relative">
            <i class="bi bi-send me-2"></i>
            <?= t()->보낸_요청 ?>
            <?php if ($friendRequestsSentCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                    <?= $friendRequestsSentCount ?>
                    <span class="visually-hidden"><?= t()->보낸_친구_요청 ?></span>
                </span>
            <?php endif; ?>
        </a>
    </div>

    <!-- My Friends Section (친구 한 줄 표시 위젯) -->
    <?php
    $limit = 5;
    include __DIR__ . '/../../widgets/user/friend/friend-one-line-display.php';
    ?>
</div>

<!-- Vue.js 앱 컨테이너 -->
<div id="user-list-app" class="container">
    <div class="row">
        <div class="col-12">
            <!-- 사용자 없음 메시지 -->
            <div v-if="!isInitialLoading && users.length === 0" class="col-12" v-cloak>
                <div class="alert alert-info">
                    <?= t()->등록된_사용자가_없습니다 ?>
                </div>
            </div>

            <!-- 사용자 목록 (Responsive Grid: 1 col mobile, 2 col tablet and desktop) -->
            <div class="row row-cols-1 row-cols-md-2 g-3">
                <!-- 사용자 카드 (Horizontal Card with Image) -->
                <div v-for="user in users" :key="user.id" class="col" v-cloak>
                    <a :href="`<?= href()->user->profile ?>?id=${user.id}`"
                        class="text-decoration-none h-100 d-block">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3 p-3">
                                <!-- 프로필 이미지 -->
                                <div class="flex-shrink-0">
                                    <img v-if="user.photo_url"
                                        :src="user.photo_url"
                                        class="rounded-circle"
                                        style="width: 64px; height: 64px; object-fit: cover;"
                                        :alt="getUserFullName(user)">
                                    <div v-else
                                        class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"
                                        style="width: 64px; height: 64px;">
                                        <i class="bi bi-person fs-4 text-secondary"></i>
                                    </div>
                                </div>

                                <!-- 사용자 정보 -->
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="card-title mb-1 text-dark text-truncate">{{ getUserFullName(user) }}</h6>
                                    <p class="card-text text-muted mb-0 small">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ formatDate(user.created_at) }}
                                    </p>
                                    <p v-if="user.email" class="card-text text-muted mb-0 small text-truncate">
                                        <i class="bi bi-envelope me-1"></i>
                                        {{ user.email }}
                                    </p>
                                </div>

                                <!-- 화살표 아이콘 -->
                                <div class="flex-shrink-0">
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- 로딩 인디케이터 -->
            <div v-if="loading" class="text-center mt-4" v-cloak>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden"><?= t()->로딩_중 ?></span>
                </div>
            </div>

            <!-- 모든 사용자 로드 완료 메시지 -->
            <div v-if="!loading && hasMore === false && users.length > 0" class="text-center mt-4 text-muted" v-cloak>
                <?= t()->모든_사용자를_불러왔습니다 ?>
            </div>
        </div>
    </div>
</div>

<script>
    ready(() => {

        // Vue.js 사용자 목록 앱 생성
        Vue.createApp({
            data() {
                return {
                    // Hydration 데이터
                    users: <?= json_encode($hydrationData['users'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
                    total: <?= json_encode($hydrationData['total']) ?>,
                    currentPage: <?= json_encode($hydrationData['currentPage']) ?>,
                    perPage: <?= json_encode($hydrationData['perPage']) ?>,
                    loading: false,
                    isInitialLoading: false, // 스켈레톤 로딩 상태 (SSR 데이터가 있으므로 false)
                    hasMore: true
                };
            },
            computed: {
                // 더 불러올 데이터가 있는지 계산
                canLoadMore() {
                    return this.users.length < this.total && !this.loading;
                }
            },
            methods: {
                /**
                 * 다음 페이지 로드
                 */
                async loadNextPage() {
                    // 이미 로딩 중이거나 더 이상 데이터가 없으면 중단
                    if (this.loading || !this.hasMore) {
                        return;
                    }

                    // 모든 사용자를 이미 로드했으면 중단
                    if (this.users.length >= this.total) {
                        this.hasMore = false;
                        return;
                    }

                    try {
                        this.loading = true;
                        const nextPage = this.currentPage + 1;

                        console.log(`다음 페이지 로드 중: ${nextPage}페이지`);

                        // API 호출하여 다음 페이지 데이터 가져오기
                        const result = await func('list_users', {
                            per_page: this.perPage,
                            page: nextPage
                        });

                        // 결과 확인
                        if (result && result.users && result.users.length > 0) {
                            // 기존 사용자 목록에 새 사용자 추가
                            this.users.push(...result.users);
                            this.currentPage = nextPage;
                            this.total = result.total;

                            console.log(`${result.users.length}명의 사용자 추가됨 (총 ${this.users.length}/${this.total})`);

                            // 더 이상 데이터가 없으면 hasMore를 false로 설정
                            if (this.users.length >= this.total) {
                                this.hasMore = false;
                                console.log('모든 사용자를 불러왔습니다.');
                            }
                        } else {
                            // 더 이상 데이터가 없음
                            this.hasMore = false;
                            console.log('더 이상 불러올 사용자가 없습니다.');
                        }
                    } catch (error) {
                        console.error('사용자 목록 로드 실패:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                /**
                 * Unix timestamp를 날짜 문자열로 변환
                 * @param {number} timestamp - Unix timestamp
                 * @returns {string} 날짜 문자열 (YYYY-MM-DD)
                 */
                formatDate(timestamp) {
                    const date = new Date(timestamp * 1000);
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                /**
                 * 사용자 전체 이름 반환
                 * @param {Object} user - 사용자 객체
                 * @returns {string} 전체 이름
                 */
                getUserFullName(user) {
                    const parts = [
                        user.first_name,
                        user.middle_name,
                        user.last_name
                    ].filter(name => name && name.trim() !== '');

                    return parts.length > 0 ? parts.join(' ') : 'No name';
                }
            },
            mounted() {
                // InfiniteScroll 초기화
                const scrollController = InfiniteScroll.init('body', {
                    onScrolledToBottom: () => {
                        console.log('하단 도달: 다음 페이지 로드 시작');
                        // Vue 메서드 호출
                        this.loadNextPage();
                    },
                    threshold: 100, // 하단으로부터 100px 이내에서 트리거
                    debounceDelay: 200, // 200ms 디바운스
                    initialScrollToBottom: false // 페이지 로드 시 자동 스크롤 안 함
                });

                // console.log('[user-list] 사용자 목록 페이지 초기화 완료:', {
                //     totalUsers: this.users.length,
                //     total: this.total,
                //     currentPage: this.currentPage
                // });
            }
        }).mount('#user-list-app');
    });
</script>

<?php
function inject_list_language()
{
    t()->inject([
        '사용자_목록' => [
            'ko' => '사용자 목록',
            'en' => 'User List',
            'ja' => 'ユーザーリスト',
            'zh' => '用户列表'
        ],
        '친구_검색' => [
            'ko' => '친구 검색',
            'en' => 'Search Friends',
            'ja' => '友達を検索',
            'zh' => '搜索朋友'
        ],
        '이름을_입력하세요' => [
            'ko' => '이름을 입력하세요',
            'en' => 'Enter name',
            'ja' => '名前を入力してください',
            'zh' => '请输入姓名'
        ],
        '검색' => [
            'ko' => '검색',
            'en' => 'Search',
            'ja' => '検索',
            'zh' => '搜索'
        ],
        '검색_중' => [
            'ko' => '검색 중...',
            'en' => 'Searching...',
            'ja' => '検索中...',
            'zh' => '搜索中...'
        ],
        '검색어를_입력해주세요' => [
            'ko' => '검색어를 입력해주세요',
            'en' => 'Please enter a search term',
            'ja' => '検索キーワードを入力してください',
            'zh' => '请输入搜索词'
        ],
        '검색에_실패했습니다' => [
            'ko' => '검색에 실패했습니다',
            'en' => 'Search failed',
            'ja' => '検索に失敗しました',
            'zh' => '搜索失败'
        ],
        '검색_결과가_없습니다' => [
            'ko' => '검색 결과가 없습니다.',
            'en' => 'No results found.',
            'ja' => '検索結果がありません。',
            'zh' => '未找到结果。'
        ],
        '내_친구_목록' => [
            'ko' => '내 친구 목록',
            'en' => 'My Friends',
            'ja' => 'マイフレンド',
            'zh' => '我的朋友'
        ],
        '등록된_사용자가_없습니다' => [
            'ko' => '등록된 사용자가 없습니다.',
            'en' => 'No users registered.',
            'ja' => '登録されたユーザーがいません。',
            'zh' => '没有注册用户。'
        ],
        '요청_중' => [
            'ko' => '요청 중...',
            'en' => 'Requesting...',
            'ja' => 'リクエスト中...',
            'zh' => '请求中...'
        ],
        '친구' => [
            'ko' => '친구',
            'en' => 'Friend',
            'ja' => '友達',
            'zh' => '朋友'
        ],
        '친구_추가' => [
            'ko' => '친구 추가',
            'en' => 'Add Friend',
            'ja' => '友達を追加',
            'zh' => '添加朋友'
        ],
        '로딩_중' => [
            'ko' => '로딩 중...',
            'en' => 'Loading...',
            'ja' => '読み込み中...',
            'zh' => '加载中...'
        ],
        '모든_사용자를_불러왔습니다' => [
            'ko' => '모든 사용자를 불러왔습니다.',
            'en' => 'All users loaded.',
            'ja' => 'すべてのユーザーを読み込みました。',
            'zh' => '已加载所有用户。'
        ],
        '로그인이_필요합니다' => [
            'ko' => '로그인이 필요합니다.',
            'en' => 'Login required.',
            'ja' => 'ログインが必要です。',
            'zh' => '需要登录。'
        ],
        '자기_자신에게는_친구_요청을_보낼_수_없습니다' => [
            'ko' => '자기 자신에게는 친구 요청을 보낼 수 없습니다.',
            'en' => 'You cannot send a friend request to yourself.',
            'ja' => '自分自身に友達リクエストを送信できません。',
            'zh' => '您不能向自己发送好友请求。'
        ],
        '이미_친구입니다' => [
            'ko' => '이미 친구입니다.',
            'en' => 'Already friends.',
            'ja' => 'すでに友達です。',
            'zh' => '已经是朋友了。'
        ],
        '님에게_친구_맺기_신청을_하시겠습니까' => [
            'ko' => '님에게 친구 맺기 신청을 하시겠습니까?',
            'en' => ', do you want to send a friend request?',
            'ja' => 'さんに友達リクエストを送信しますか？',
            'zh' => '，您想发送好友请求吗？'
        ],
        '님에게_친구_요청을_보냈습니다' => [
            'ko' => '님에게 친구 요청을 보냈습니다.',
            'en' => ', friend request sent.',
            'ja' => 'さんに友達リクエストを送信しました。',
            'zh' => '，已发送好友请求。'
        ],
        '친구_요청에_실패했습니다' => [
            'ko' => '친구 요청에 실패했습니다.',
            'en' => 'Friend request failed.',
            'ja' => '友達リクエストに失敗しました。',
            'zh' => '好友请求失败。'
        ],
        '친구_요청_실패' => [
            'ko' => '친구 요청 실패',
            'en' => 'Friend request failed',
            'ja' => '友達リクエスト失敗',
            'zh' => '好友请求失败'
        ],
        '받은_요청' => [
            'ko' => '받은 요청',
            'en' => 'Received Requests',
            'ja' => '受信リクエスト',
            'zh' => '收到的请求'
        ],
        '보낸_요청' => [
            'ko' => '보낸 요청',
            'en' => 'Sent Requests',
            'ja' => '送信リクエスト',
            'zh' => '发送的请求'
        ],
        '받은_친구_요청' => [
            'ko' => '받은 친구 요청',
            'en' => 'Received friend requests',
            'ja' => '受信した友達リクエスト',
            'zh' => '收到的好友请求'
        ],
        '보낸_친구_요청' => [
            'ko' => '보낸 친구 요청',
            'en' => 'Sent friend requests',
            'ja' => '送信した友達リクエスト',
            'zh' => '发送的好友请求'
        ],
        '친구_목록' => [
            'ko' => '친구 목록',
            'en' => 'Friends List',
            'ja' => '友達リスト',
            'zh' => '朋友列表'
        ],
        '전체_친구_수' => [
            'ko' => '전체 친구 수',
            'en' => 'Total friends',
            'ja' => '友達の総数',
            'zh' => '好友总数'
        ],
    ]);
}
?>