
/**
 * 사용자 검색 모듈
 *
 * 친구 검색 기능을 제공하는 독립적인 Vue.js 컴포넌트입니다.
 *
 * 사용법 1: 자동 마운트 (권장)
 * HTML에 <div class="user-search-component"></div> 요소를 추가하면 자동으로 마운트됩니다.
 *
 * 사용법 2: 수동 마운트
 * Vue.createApp(UserSearchComponent).mount('#user-search');
 */

// 인스턴스 카운터 초기화
if (typeof window.userSearchInstanceCounter === 'undefined') {
    window.userSearchInstanceCounter = 0;
}



// 사용자 검색 컴포넌트 정의 (Options API)
// 중복 로드 방지를 위해 이미 정의되어 있으면 재정의하지 않음

window.UserSearchComponent = {
    template: `
        <!-- Friend Search Button -->
        <button @click="openSearchModal" class="user-search-btn">
            <i class="bi bi-search"></i>{{ t.친구_검색 }}
        </button>

        <!-- Friend Search Modal with higher z-index for proper layering -->
        <div class="modal fade" :id="modalId" tabindex="-1" :aria-labelledby="modalId + '-label'" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" :id="modalId + '-label'">{{ t.친구_검색 }}</h5>
                        <button type="button" class="btn-close" @click="closeSearchModal" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" :id="modalBodyId">
                        <!-- Search Input Group -->
                        <div class="input-group mb-3">
                            <input v-model="searchTerm"
                                @keyup.enter="performSearch"
                                type="text"
                                class="form-control"
                                :placeholder="t.이름을_입력하세요"
                                :aria-label="t.이름을_입력하세요">
                            <button @click="performSearch"
                                class="btn btn-primary"
                                :disabled="searchLoading">
                                <span v-if="searchLoading">{{ t.검색_중 }}</span>
                                <span v-else>{{ t.검색 }}</span>
                            </button>
                        </div>

                        <!-- Search Results Grid -->
                        <div v-if="searchResults.length > 0" class="row g-3">
                            <div v-for="user in searchResults" :key="'search-' + user.id" class="col-6">
                                <div class="card h-100">
                                    <div class="card-body p-2 d-flex align-items-center">
                                        <!-- Profile Photo (clickable link) -->
                                        <a :href="\`\${profileUrl}?id=\${user.id}\`"
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
                                        <a :href="\`\${profileUrl}?id=\${user.id}\`"
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
                            {{ t.검색_결과가_없습니다 }}
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
    `,
    data() {
        return {
            // 인스턴스 ID (자동 생성)
            instanceId: window.userSearchInstanceCounter++,

            // 검색 관련 데이터
            searchTerm: '', // 검색어
            searchResults: [], // 검색 결과 배열
            searchPage: 1, // 현재 검색 결과 페이지
            searchTotalPages: 0, // 전체 검색 결과 페이지 수
            searchTotal: 0, // 전체 검색 결과 수
            searchLoading: false, // 검색 중 상태
            searchPerformed: false, // 검색이 수행되었는지 여부
            modalInstance: null, // Bootstrap 모달 인스턴스

            // 전역 상태 관리
            state: window.Store.state,

            // 프로필 URL
            profileUrl: '/user/profile'
        };
    },

    computed: {
        /**
         * 모달 ID (각 인스턴스마다 고유)
         */
        modalId() {
            return `friendSearchModal-${this.instanceId}`;
        },

        /**
         * 모달 본문 ID (각 인스턴스마다 고유)
         */
        modalBodyId() {
            return `search-modal-body-${this.instanceId}`;
        },

        /**
         * 다국어 번역 텍스트
         */
        t() {
            return {
                친구_검색: tr({ ko: '친구 검색', en: 'Find Friends', ja: '友達検索', zh: '查找朋友' }),
                이름을_입력하세요: tr({ ko: '이름을 입력하세요', en: 'Enter name', ja: '名前を入力してください', zh: '输入姓名' }),
                검색: tr({ ko: '검색', en: 'Search', ja: '検索', zh: '搜索' }),
                검색_중: tr({ ko: '검색 중...', en: 'Searching...', ja: '検索中...', zh: '搜索中...' }),
                검색_결과가_없습니다: tr({ ko: '검색 결과가 없습니다.', en: 'No results found.', ja: '検索結果がありません。', zh: '未找到结果。' }),
                검색어를_입력해주세요: tr({ ko: '검색어를 입력해주세요', en: 'Please enter a search term', ja: '検索キーワードを入力してください', zh: '请输入搜索关键词' }),
                검색에_실패했습니다: tr({ ko: '검색에 실패했습니다', en: 'Search failed', ja: '検索に失敗しました', zh: '搜索失败' })
            };
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
            // console.log(`[user-search-${this.instanceId}] 친구 검색 모달 열기`);
            if (this.modalInstance) {
                this.modalInstance.show();
            } else {
                // console.error(`[user-search-${this.instanceId}] modalInstance가 null입니다.`);
            }
        },

        /**
         * 친구 검색 모달 닫기
         */
        closeSearchModal() {
            // console.log(`[user-search-${this.instanceId}] 친구 검색 모달 닫기`);
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
                alert(this.t.검색어를_입력해주세요);
                return;
            }

            // console.log(`[user-search-${this.instanceId}] 검색 수행:`, this.searchTerm);

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

                // console.log(`[user-search-${this.instanceId}] 검색 API 호출: term="${this.searchTerm.trim()}", page=${this.searchPage}`);

                const result = await func('list_users', {
                    name: this.searchTerm.trim(),
                    per_page: 10,
                    page: this.searchPage
                });

                this.searchResults = result.users || [];
                this.searchTotal = result.total || 0;
                this.searchTotalPages = result.total_pages || 0;

                // console.log(`[user-search-${this.instanceId}] 검색 결과: ${this.searchResults.length}명 (총 ${this.searchTotal}명, ${this.searchTotalPages}페이지)`);

            } catch (error) {
                // console.error(`[user-search-${this.instanceId}] 검색 실패:`, error);
                alert(this.t.검색에_실패했습니다 + ': ' + (error.message || error.error_message || ''));
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
                // console.log(`[user-search-${this.instanceId}] 잘못된 페이지 번호: ${pageNum} (범위: 1-${this.searchTotalPages})`);
                return;
            }

            // 현재 페이지와 동일하면 중단
            if (pageNum === this.searchPage) {
                // console.log(`[user-search-${this.instanceId}] 이미 ${pageNum}페이지에 있습니다.`);
                return;
            }

            // console.log(`[user-search-${this.instanceId}] 검색 결과 페이지 이동: ${this.searchPage} -> ${pageNum}`);

            // 페이지 번호 업데이트
            this.searchPage = pageNum;

            // 검색 결과 다시 로드
            await this.loadSearchResults();

            // 모달 본문을 맨 위로 스크롤
            const modalBody = document.getElementById(this.modalBodyId);
            if (modalBody) {
                modalBody.scrollTop = 0;
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
        // console.log(`[user-search-${this.instanceId}] mounted() 호출됨, modalId: ${this.modalId}`);

        // Vue가 DOM을 완전히 렌더링할 때까지 대기
        this.$nextTick(() => {
            // Bootstrap Modal 초기화
            const modalElement = document.getElementById(this.modalId);
            // console.log(`[user-search-${this.instanceId}] $nextTick에서 modalElement 검색:`, modalElement);

            if (modalElement && typeof bootstrap !== 'undefined') {
                // 모달을 body의 직접 자식으로 이동 (z-index 문제 해결)
                document.body.appendChild(modalElement);

                this.modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });



                // console.log(`[user-search-${this.instanceId}] 친구 검색 모달 초기화 완료 (ID: ${this.modalId})`);
            } else {
                // console.error(`[user-search-${this.instanceId}] Bootstrap Modal 초기화 실패 - modalElement:`, modalElement, 'bootstrap:', typeof bootstrap);
            }

            // console.log(`[user-search-${this.instanceId}] 사용자 검색 모듈 초기화 완료`);
        });
    }
};

/**
 * 자동 마운트 함수
 *
 * .user-search-component 클래스를 가진 모든 요소를 찾아서 자동으로 Vue 앱을 마운트합니다.
 *
 * 사용법:
 * 1. HTML에 <div class="user-search-component"></div> 요소를 추가
 * 2. 이 스크립트가 로드되면 자동으로 모든 .user-search-component 요소에 마운트됨
 *
 * 주의사항:
 * - 한 요소에 한 번만 마운트됩니다 (중복 마운트 방지)
 * - DOM이 완전히 로드된 후에 실행됩니다
 */
ready(() => {
    // .user-search-component 클래스를 가진 모든 요소 찾기
    const userSearchApps = document.querySelectorAll('.user-search-component');

    if (userSearchApps.length > 0) {
        // console.log(`[user-search] ${userSearchApps.length}개의 .user-search-component 요소 발견`);

        // 각 요소에 Vue 앱 마운트
        userSearchApps.forEach((element, index) => {
            // 이미 마운트된 요소는 건너뛰기 (중복 마운트 방지)
            if (element.__vue_app__) {
                // console.log(`[user-search] 요소 ${index}는 이미 마운트되어 있습니다.`);
                return;
            }

            // console.log(`[user-search] 요소 ${index}에 Vue 앱 마운트 중...`);

            try {
                const app = Vue.createApp(window.UserSearchComponent);
                app.mount(element);

                // console.log(`[user-search] 요소 ${index}에 Vue 앱 마운트 완료`);
            } catch (error) {
                // console.error(`[user-search] 요소 ${index} 마운트 실패:`, error);
            }
        });
    } else {
        // console.log('[user-search] .user-search-component 요소를 찾지 못했습니다.');
    }
});
