
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
                hasMore: true,
                myUserId: <?= login() ? login() -> id : 'null' ?> // 로그인한 사용자 ID
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

            console.log('[user-list] 사용자 목록 페이지 초기화 완료:', {
                totalUsers: this.users.length,
                total: this.total,
                currentPage: this.currentPage,
                myUserId: this.myUserId
            });
        }
    }).mount('#user-list-app');
});