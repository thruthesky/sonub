<?php

/**
 * 사용자 목록 페이지
 *
 * Vue.js를 사용하여 사용자 목록을 표시하고, infinite scroll로 추가 로드합니다.
 * 각 사용자 카드에 친구 맺기 요청 버튼이 포함되어 있습니다.
 */

// 초기 사용자 목록 조회 (첫 페이지, 20명)
$result = list_users(['per_page' => 20, 'page' => 1]);
$users = $result['users'] ?? [];
$total = $result['total'] ?? 0;

// 내 친구 목록 조회 (로그인한 경우에만)
// get_friends() 함수를 사용하여 친구 상세 정보를 한 번에 조회
$myFriends = [];
if (login()) {
    try {
        // get_friends() 함수로 친구 상세 정보 조회 (최대 5명)
        $friendsResult = get_friends(['me' => login()->id, 'limit' => 5]);
        $myFriends = $friendsResult['friends'] ?? [];
    } catch (Exception $e) {
        // 친구 섹션은 선택적 기능이므로 에러 발생 시 조용히 실패
        // 페이지는 정상적으로 로드되어야 함
        $myFriends = [];
    }
}

// Vue.js hydration을 위한 데이터 준비
$hydrationData = [
    'users' => $users,
    'total' => $total,
    'currentPage' => 1,
    'perPage' => 20,
    'myFriends' => $myFriends
];

// InfiniteScroll 라이브러리 로드
load_deferred_js('infinite-scroll');
?>

<!-- Vue.js 앱 컨테이너 -->
<div id="user-list-app" class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><?= t()->사용자_목록 ?></h1>

            <!-- Friend Search Button -->
            <button @click="openSearchModal" class="btn btn-primary mb-3">
                <i class="bi bi-search me-2"></i>
                <?= t()->친구_검색 ?>
            </button>

            <!-- My Friends Section (only show if logged in and has friends) -->
            <div v-if="myUserId && myFriends.length > 0" class="mb-4">
                <h5 class="mb-3"><?= t()->내_친구_목록 ?></h5>
                <div class="row g-3">
                    <div v-for="friend in myFriends" :key="'friend-' + friend.id" class="col-6">
                        <div class="card h-100">
                            <div class="card-body p-2 d-flex align-items-center">
                                <!-- Profile Photo -->
                                <a :href="`<?= href()->user->profile ?>?id=${friend.id}`" 
                                   class="flex-shrink-0 me-2 text-decoration-none">
                                    <img v-if="friend.photo_url"
                                        :src="friend.photo_url"
                                        class="rounded-circle"
                                        style="width: 50px; height: 50px; object-fit: cover;"
                                        :alt="friend.display_name">
                                    <div v-else
                                        class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="bi bi-person fs-5 text-secondary"></i>
                                    </div>
                                </a>

                                <!-- User Info -->
                                <a :href="`<?= href()->user->profile ?>?id=${friend.id}`" 
                                   class="flex-grow-1 min-w-0 text-decoration-none">
                                    <h6 class="card-title mb-0 text-truncate text-dark">
                                        {{ friend.display_name }}
                                    </h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;">
                                        {{ formatDate(friend.created_at) }}
                                    </p>
                                </a>

                                <!-- Friend Badge (no button needed) -->
                                <div class="flex-shrink-0 ms-2">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <?= t()->친구 ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr v-if="myUserId && myFriends.length > 0" class="my-4">

            <!-- 사용자 목록 그리드 -->
            <div class="row g-3">
                <!-- 사용자 없음 메시지 -->
                <div v-if="users.length === 0" class="col-12">
                    <div class="alert alert-info">
                        <?= t()->등록된_사용자가_없습니다 ?>
                    </div>
                </div>

                <!-- 사용자 카드 -->
                <div v-for="user in users" :key="user.id" class="col-6">
                    <div class="card h-100">
                        <div class="card-body p-2 d-flex align-items-center">
                            <!-- 프로필 사진 (클릭하면 프로필 페이지로 이동) -->
                            <a :href="`<?= href()->user->profile ?>?id=${user.id}`" class="flex-shrink-0 me-2 text-decoration-none">
                                <img v-if="user.photo_url"
                                    :src="user.photo_url"
                                    class="rounded-circle"
                                    style="width: 50px; height: 50px; object-fit: cover;"
                                    :alt="user.display_name">
                                <div v-else
                                    class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-person fs-5 text-secondary"></i>
                                </div>
                            </a>

                            <!-- 사용자 정보 (클릭하면 프로필 페이지로 이동) -->
                            <a :href="`<?= href()->user->profile ?>?id=${user.id}`" class="flex-grow-1 min-w-0 text-decoration-none">
                                <!-- 사용자 이름 -->
                                <h6 class="card-title mb-0 text-truncate text-dark">{{ user.display_name }}</h6>

                                <!-- 가입일 -->
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;">
                                    {{ formatDate(user.created_at) }}
                                </p>
                            </a>

                            <!-- 친구 맺기 버튼 (본인이 아닌 경우에만 표시) -->
                            <div v-if="myUserId && user.id !== myUserId" class="flex-shrink-0 ms-2">
                                <button @click="requestFriend(user)"
                                    class="btn btn-sm"
                                    :class="user.is_friend ? 'btn-success' : 'btn-primary'"
                                    :disabled="user.requesting || user.is_friend">
                                    <span v-if="user.requesting">
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        <?= t()->요청_중 ?>
                                    </span>
                                    <span v-else-if="user.is_friend">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <?= t()->친구 ?>
                                    </span>
                                    <span v-else>
                                        <i class="bi bi-person-plus me-1"></i>
                                        <?= t()->친구_추가 ?>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 로딩 인디케이터 -->
            <div v-if="loading" class="text-center mt-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden"><?= t()->로딩_중 ?></span>
                </div>
            </div>

            <!-- 모든 사용자 로드 완료 메시지 -->
            <div v-if="!loading && hasMore === false && users.length > 0" class="text-center mt-4 text-muted">
                <?= t()->모든_사용자를_불러왔습니다 ?>
            </div>
        </div>
    </div>
</div>

<!-- Friend Search Modal -->
<div class="modal fade" id="friendSearchModal" tabindex="-1" aria-labelledby="friendSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="friendSearchModalLabel"><?= t()->친구_검색 ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="search-modal-body">
                <!-- Search Input Group -->
                <div class="input-group mb-3">
                    <input v-model="searchTerm" 
                           @keyup.enter="performSearch"
                           type="text" 
                           class="form-control" 
                           :placeholder="'<?= t()->이름을_입력하세요 ?>'"
                           aria-label="<?= t()->이름을_입력하세요 ?>">
                    <button @click="performSearch" 
                            class="btn btn-primary"
                            :disabled="searchLoading">
                        <span v-if="searchLoading"><?= t()->검색_중 ?></span>
                        <span v-else><?= t()->검색 ?></span>
                    </button>
                </div>
                
                <!-- Search Results Grid -->
                <div v-if="searchResults.length > 0" class="row g-3">
                    <div v-for="user in searchResults" :key="'search-' + user.id" class="col-6">
                        <div class="card h-100">
                            <div class="card-body p-2 d-flex align-items-center">
                                <!-- Profile Photo (clickable link) -->
                                <a :href="`<?= href()->user->profile ?>?id=${user.id}`" 
                                   class="flex-shrink-0 me-2 text-decoration-none">
                                    <img v-if="user.photo_url"
                                        :src="user.photo_url"
                                        class="rounded-circle"
                                        style="width: 50px; height: 50px; object-fit: cover;"
                                        :alt="user.display_name">
                                    <div v-else
                                        class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="bi bi-person fs-5 text-secondary"></i>
                                    </div>
                                </a>

                                <!-- User Info (clickable link) -->
                                <a :href="`<?= href()->user->profile ?>?id=${user.id}`" 
                                   class="flex-grow-1 min-w-0 text-decoration-none">
                                    <h6 class="card-title mb-0 text-truncate text-dark">
                                        {{ user.display_name }}
                                    </h6>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;">
                                        {{ formatDate(user.created_at) }}
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Results Message -->
                <div v-else-if="searchPerformed && searchResults.length === 0 && !searchLoading" 
                     class="alert alert-info">
                    <?= t()->검색_결과가_없습니다 ?>
                </div>
                
                <!-- Pagination Bar -->
                <nav v-if="searchTotalPages > 1" class="mt-4" aria-label="Search results pagination">
                    <ul class="pagination justify-content-center">
                        <!-- First Page -->
                        <li class="page-item" :class="{disabled: searchPage === 1}">
                            <a class="page-link" href="#" @click.prevent="goToSearchPage(1)" aria-label="First page">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                        </li>
                        
                        <!-- Previous Page -->
                        <li class="page-item" :class="{disabled: searchPage === 1}">
                            <a class="page-link" href="#" @click.prevent="goToSearchPage(searchPage - 1)" aria-label="Previous page">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Page Numbers (dynamic range) -->
                        <li v-for="pageNum in visiblePageNumbers" 
                            :key="pageNum"
                            class="page-item" 
                            :class="{active: pageNum === searchPage}">
                            <a class="page-link" href="#" @click.prevent="goToSearchPage(pageNum)">
                                {{ pageNum }}
                            </a>
                        </li>
                        
                        <!-- Next Page -->
                        <li class="page-item" :class="{disabled: searchPage === searchTotalPages}">
                            <a class="page-link" href="#" @click.prevent="goToSearchPage(searchPage + 1)" aria-label="Next page">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        
                        <!-- Last Page -->
                        <li class="page-item" :class="{disabled: searchPage === searchTotalPages}">
                            <a class="page-link" href="#" @click.prevent="goToSearchPage(searchTotalPages)" aria-label="Last page">
                                <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    ready(() => {
        // Vue.js 앱 생성
        Vue.createApp({
            data() {
                return {
                    // Hydration 데이터
                    users: <?= json_encode($hydrationData['users'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
                    total: <?= json_encode($hydrationData['total']) ?>,
                    currentPage: <?= json_encode($hydrationData['currentPage']) ?>,
                    perPage: <?= json_encode($hydrationData['perPage']) ?>,
                    myFriends: <?= json_encode($hydrationData['myFriends'], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>,
                    loading: false,
                    hasMore: true,
                    myUserId: <?= login() ? login()->id : 'null' ?>, // 로그인한 사용자 ID
                    
                    // Search-related properties
                    searchModalOpen: false,
                    searchTerm: '',
                    searchResults: [],
                    searchPage: 1,
                    searchTotalPages: 0,
                    searchTotal: 0,
                    searchLoading: false,
                    searchPerformed: false,
                    modalInstance: null
                };
            },
            computed: {
                // 더 불러올 데이터가 있는지 계산
                canLoadMore() {
                    return this.users.length < this.total && !this.loading;
                },
                
                /**
                 * 검색 결과 페이지네이션에 표시할 페이지 번호 배열 계산
                 * 현재 페이지를 중심으로 최대 5개의 페이지 번호를 표시
                 */
                visiblePageNumbers() {
                    const displayPages = 5;
                    const half = Math.floor(displayPages / 2);
                    let start = Math.max(1, this.searchPage - half);
                    let end = Math.min(this.searchTotalPages, this.searchPage + half);
                    
                    // 항상 5개의 페이지를 표시하도록 범위 조정 (가능한 경우)
                    if (end - start + 1 < displayPages) {
                        if (start === 1) {
                            end = Math.min(this.searchTotalPages, start + displayPages - 1);
                        } else {
                            start = Math.max(1, end - displayPages + 1);
                        }
                    }
                    
                    const pages = [];
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                }
            },
            methods: {
                /**
                 * 친구 검색 모달 열기
                 */
                openSearchModal() {
                    console.log('친구 검색 모달 열기');
                    if (this.modalInstance) {
                        this.modalInstance.show();
                    }
                },

                /**
                 * 친구 검색 모달 닫기
                 */
                closeSearchModal() {
                    console.log('친구 검색 모달 닫기');
                    if (this.modalInstance) {
                        this.modalInstance.hide();
                    }
                },

                /**
                 * 친구 검색 수행
                 */
                async performSearch() {
                    // 입력 검증
                    if (!this.searchTerm || this.searchTerm.trim() === '') {
                        alert('<?= t()->검색어를_입력해주세요 ?>');
                        return;
                    }

                    console.log('검색 수행:', this.searchTerm);

                    // 상태 초기화
                    this.searchPage = 1;
                    this.searchPerformed = true;

                    // API 호출
                    await this.loadSearchResults();
                },

                /**
                 * 검색 결과 로드
                 */
                async loadSearchResults() {
                    try {
                        this.searchLoading = true;

                        console.log(`검색 API 호출: term="${this.searchTerm.trim()}", page=${this.searchPage}`);

                        const result = await func('list_users', {
                            name: this.searchTerm.trim(),
                            per_page: 10,
                            page: this.searchPage
                        });

                        this.searchResults = result.users || [];
                        this.searchTotal = result.total || 0;
                        this.searchTotalPages = result.total_pages || 0;

                        console.log(`검색 결과: ${this.searchResults.length}명 (총 ${this.searchTotal}명, ${this.searchTotalPages}페이지)`);

                    } catch (error) {
                        console.error('검색 실패:', error);
                        alert('<?= t()->검색에_실패했습니다 ?>: ' + (error.message || error.error_message || ''));
                    } finally {
                        this.searchLoading = false;
                    }
                },

                /**
                 * 검색 결과의 특정 페이지로 이동
                 * @param {number} pageNum - 이동할 페이지 번호
                 */
                async goToSearchPage(pageNum) {
                    // 유효성 검사: 페이지 번호가 범위 내에 있는지 확인
                    if (pageNum < 1 || pageNum > this.searchTotalPages) {
                        console.log(`잘못된 페이지 번호: ${pageNum} (범위: 1-${this.searchTotalPages})`);
                        return;
                    }
                    
                    // 현재 페이지와 동일하면 중단
                    if (pageNum === this.searchPage) {
                        console.log(`이미 ${pageNum}페이지에 있습니다.`);
                        return;
                    }
                    
                    console.log(`검색 결과 페이지 이동: ${this.searchPage} -> ${pageNum}`);
                    
                    // 페이지 번호 업데이트
                    this.searchPage = pageNum;
                    
                    // 검색 결과 다시 로드
                    await this.loadSearchResults();
                    
                    // 모달 본문을 맨 위로 스크롤
                    const modalBody = document.getElementById('search-modal-body');
                    if (modalBody) {
                        modalBody.scrollTop = 0;
                    }
                },

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
                 * 친구 맺기 요청
                 * @param {Object} user - 친구 요청을 보낼 사용자 객체
                 */
                async requestFriend(user) {
                    // 로그인 확인
                    if (!this.myUserId) {
                        alert('<?= t()->로그인이_필요합니다 ?>');
                        window.location.href = '<?= href()->user->login ?>';
                        return;
                    }

                    // 자기 자신에게 친구 요청 방지
                    if (user.id === this.myUserId) {
                        alert('<?= t()->자기_자신에게는_친구_요청을_보낼_수_없습니다 ?>');
                        return;
                    }

                    // 이미 친구인 경우
                    if (user.is_friend) {
                        alert('<?= t()->이미_친구입니다 ?>');
                        return;
                    }

                    // 확인 창 표시
                    const confirmed = confirm(`${user.display_name}<?= t()->님에게_친구_맺기_신청을_하시겠습니까 ?>`);
                    if (!confirmed) {
                        return;
                    }

                    try {
                        // 요청 중 상태 설정
                        user.requesting = true;

                        console.log(`친구 요청 전송: ${user.display_name} (ID: ${user.id})`);

                        // API 호출: request_friend 함수 사용
                        await func('request_friend', {
                            me: this.myUserId,
                            other: user.id,
                            auth: true // Firebase 인증 포함
                        });

                        // 성공: 친구 상태 업데이트
                        user.is_friend = true;
                        user.requesting = false;

                        console.log(`친구 요청 성공: ${user.display_name}`);
                        alert(`${user.display_name}<?= t()->님에게_친구_요청을_보냈습니다 ?>`);
                    } catch (error) {
                        console.error('친구 요청 실패:', error);
                        user.requesting = false;

                        // 에러 메시지 표시
                        const errorMessage = error.message || error.error_message || '<?= t()->친구_요청에_실패했습니다 ?>';
                        alert(`<?= t()->친구_요청_실패 ?>: ${errorMessage}`);
                    }
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

                // Bootstrap Modal 초기화
                const modalElement = document.getElementById('friendSearchModal');
                if (modalElement && typeof bootstrap !== 'undefined') {
                    this.modalInstance = new bootstrap.Modal(modalElement);
                    console.log('친구 검색 모달 초기화 완료');
                } else {
                    console.error('Bootstrap Modal 초기화 실패');
                }

                console.log('사용자 목록 페이지 초기화 완료:', {
                    totalUsers: this.users.length,
                    total: this.total,
                    currentPage: this.currentPage,
                    myUserId: this.myUserId
                });
            }
        }).mount('#user-list-app');
    });
</script>

<?php
/**
 * 다국어 번역 주입 함수
 *
 * 이 페이지에서 사용하는 모든 번역 텍스트를 주입합니다.
 */
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
            'ja' => '友達検索',
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
            'ja' => '検索語を入力してください',
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
            'ja' => '登録されたユーザーがありません。',
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
            'ja' => '友達追加',
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
            'ja' => '自分自身にはフレンドリクエストを送信できません。',
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
            'ja' => 'さんにフレンドリクエストを送信しますか？',
            'zh' => '，您想发送好友请求吗？'
        ],
        '님에게_친구_요청을_보냈습니다' => [
            'ko' => '님에게 친구 요청을 보냈습니다.',
            'en' => ', friend request sent.',
            'ja' => 'さんにフレンドリクエストを送信しました。',
            'zh' => '，已发送好友请求。'
        ],
        '친구_요청에_실패했습니다' => [
            'ko' => '친구 요청에 실패했습니다.',
            'en' => 'Friend request failed.',
            'ja' => 'フレンドリクエストに失敗しました。',
            'zh' => '好友请求失败。'
        ],
        '친구_요청_실패' => [
            'ko' => '친구 요청 실패',
            'en' => 'Friend request failed',
            'ja' => 'フレンドリクエスト失敗',
            'zh' => '好友请求失败'
        ],
    ]);
}

inject_list_language();
