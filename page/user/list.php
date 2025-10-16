<?php
/**
 * 사용자 목록 페이지
 *
 * Vue.js를 사용하여 사용자 목록을 표시하고, infinite scroll로 추가 로드합니다.
 */

// 초기 사용자 목록 조회 (첫 페이지, 20명)
$result = list_users(['per_page' => 20, 'page' => 1]);
$users = $result['users'] ?? [];
$total = $result['total'] ?? 0;

// Vue.js hydration을 위한 데이터 준비
$hydrationData = [
    'users' => $users,
    'total' => $total,
    'currentPage' => 1,
    'perPage' => 20
];

// InfiniteScroll 라이브러리 로드
load_deferred_js('infinite-scroll');
?>

<!-- Vue.js 앱 컨테이너 -->
<div id="user-list-app" class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">사용자 목록</h1>

            <!-- 사용자 목록 그리드 -->
            <div class="row g-3">
                <!-- 사용자 없음 메시지 -->
                <div v-if="users.length === 0" class="col-12">
                    <div class="alert alert-info">
                        등록된 사용자가 없습니다.
                    </div>
                </div>

                <!-- 사용자 카드 -->
                <div v-for="user in users" :key="user.id" class="col-12 col-sm-6 col-md-4">
                    <a :href="`<?= href()->user->profile ?>?id=${user.id}`" class="text-decoration-none">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <!-- 프로필 사진 -->
                                <img v-if="user.photo_url"
                                     :src="user.photo_url"
                                     class="rounded-circle mb-3"
                                     style="width: 80px; height: 80px; object-fit: cover;"
                                     :alt="user.display_name">
                                <div v-else
                                     class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3"
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-person fs-1 text-secondary"></i>
                                </div>

                                <!-- 사용자 이름 -->
                                <h5 class="card-title mb-1">{{ user.display_name }}</h5>

                                <!-- 가입일 -->
                                <p class="card-text text-muted small">
                                    가입일: {{ formatDate(user.created_at) }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- 로딩 인디케이터 -->
            <div v-if="loading" class="text-center mt-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">로딩 중...</span>
                </div>
            </div>

            <!-- 모든 사용자 로드 완료 메시지 -->
            <div v-if="!loading && hasMore === false && users.length > 0" class="text-center mt-4 text-muted">
                모든 사용자를 불러왔습니다.
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
                loading: false,
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
                threshold: 100,              // 하단으로부터 100px 이내에서 트리거
                debounceDelay: 200,          // 200ms 디바운스
                initialScrollToBottom: false // 페이지 로드 시 자동 스크롤 안 함
            });

            console.log('사용자 목록 페이지 초기화 완료:', {
                totalUsers: this.users.length,
                total: this.total,
                currentPage: this.currentPage
            });
        }
    }).mount('#user-list-app');
});
</script>
