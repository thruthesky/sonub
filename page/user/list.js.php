<script>
    /**
     * 사용자 검색 모듈
     *
     * 친구 검색 기능을 제공하는 독립적인 Vue.js 애플리케이션입니다.
     * #user-search 요소에 마운트되며, Bootstrap 모달을 통해 검색 인터페이스를 제공합니다.
     */

    ready(() => {
        // user-search 요소가 없으면 초기화하지 않음
        const searchElement = document.getElementById('user-search');
        if (!searchElement) {
            console.warn('user-search 요소를 찾을 수 없습니다. 사용자 검색 기능을 초기화하지 않습니다.');
            return;
        }

        // Vue.js 앱 생성
        Vue.createApp({
            data() {
                return {
                    // 검색 관련 데이터
                    searchTerm: '', // 검색어
                    searchResults: [], // 검색 결과 배열
                    searchPage: 1, // 현재 검색 결과 페이지
                    searchTotalPages: 0, // 전체 검색 결과 페이지 수
                    searchTotal: 0, // 전체 검색 결과 수
                    searchLoading: false, // 검색 중 상태
                    searchPerformed: false, // 검색이 수행되었는지 여부
                    modalInstance: null, // Bootstrap 모달 인스턴스

                    // PHP에서 전달된 번역 텍스트
                    translations: {
                        친구_검색: searchTranslations.친구_검색 || '친구 검색',
                        이름을_입력하세요: searchTranslations.이름을_입력하세요 || '이름을 입력하세요',
                        검색: searchTranslations.검색 || '검색',
                        검색_중: searchTranslations.검색_중 || '검색 중...',
                        검색_결과가_없습니다: searchTranslations.검색_결과가_없습니다 || '검색 결과가 없습니다.',
                        검색어를_입력해주세요: searchTranslations.검색어를_입력해주세요 || '검색어를 입력해주세요',
                        검색에_실패했습니다: searchTranslations.검색에_실패했습니다 || '검색에 실패했습니다'
                    },

                    // PHP에서 전달된 URL
                    profileUrl: searchTranslations.profileUrl || '/user/profile'
                };
            },

            computed: {
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
                    console.log('[user-search] 친구 검색 모달 열기');
                    if (this.modalInstance) {
                        this.modalInstance.show();
                    }
                },

                /**
                 * 친구 검색 모달 닫기
                 */
                closeSearchModal() {
                    console.log('[user-search] 친구 검색 모달 닫기');
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
                        alert(this.translations.검색어를_입력해주세요);
                        return;
                    }

                    console.log('[user-search] 검색 수행:', this.searchTerm);

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

                        console.log(`[user-search] 검색 API 호출: term="${this.searchTerm.trim()}", page=${this.searchPage}`);

                        const result = await func('list_users', {
                            name: this.searchTerm.trim(),
                            per_page: 10,
                            page: this.searchPage
                        });

                        this.searchResults = result.users || [];
                        this.searchTotal = result.total || 0;
                        this.searchTotalPages = result.total_pages || 0;

                        console.log(`[user-search] 검색 결과: ${this.searchResults.length}명 (총 ${this.searchTotal}명, ${this.searchTotalPages}페이지)`);

                    } catch (error) {
                        console.error('[user-search] 검색 실패:', error);
                        alert(this.translations.검색에_실패했습니다 + ': ' + (error.message || error.error_message || ''));
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
                        console.log(`[user-search] 잘못된 페이지 번호: ${pageNum} (범위: 1-${this.searchTotalPages})`);
                        return;
                    }

                    // 현재 페이지와 동일하면 중단
                    if (pageNum === this.searchPage) {
                        console.log(`[user-search] 이미 ${pageNum}페이지에 있습니다.`);
                        return;
                    }

                    console.log(`[user-search] 검색 결과 페이지 이동: ${this.searchPage} -> ${pageNum}`);

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
                // Bootstrap Modal 초기화
                const modalElement = document.getElementById('friendSearchModal');
                if (modalElement && typeof bootstrap !== 'undefined') {
                    this.modalInstance = new bootstrap.Modal(modalElement);
                    console.log('[user-search] 친구 검색 모달 초기화 완료');
                } else {
                    console.error('[user-search] Bootstrap Modal 초기화 실패');
                }

                // 전역 함수로 노출 (외부에서 모달을 열 수 있도록)
                window.openFriendSearchModal = () => {
                    this.openSearchModal();
                };

                console.log('[user-search] 사용자 검색 모듈 초기화 완료');
            }
        }).mount('#user-search');
    });
</script>