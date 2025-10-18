/**
 * 프로필 페이지 JavaScript (Vue.js)
 *
 * 사용자 프로필 페이지의 친구 추가 기능을 처리합니다.
 */

ready(() => {
    // Vue.js 앱이 마운트될 요소 확인
    const appElement = document.getElementById('profile-app');

    // 앱 요소가 없으면 종료
    if (!appElement) {
        return;
    }

    // Vue.js 프로필 앱 생성
    Vue.createApp({
        data() {
            return {
                // 친구 요청 상태
                requesting: false,
                isFriend: false
            };
        },
        methods: {
            /**
             * 친구 추가 요청
             * @param {number} otherUserId - 친구 요청을 보낼 사용자 ID
             */
            async requestFriend(otherUserId) {
                // 로그인 확인 - window.AppStore.user에서 로그인한 사용자 정보 가져오기
                if (!window.AppStore || !window.AppStore.user || !window.AppStore.user.id) {
                    alert(t.로그인이_필요합니다);
                    window.location.href = '<?= href()->user->login ?>';
                    return;
                }

                const myUserId = window.AppStore.user.id;

                // 자기 자신에게 친구 요청 방지
                if (otherUserId === myUserId) {
                    alert(t.자기_자신에게는_친구_요청을_보낼_수_없습니다);
                    return;
                }

                // 이미 친구인 경우
                if (this.isFriend) {
                    alert(t.이미_친구입니다);
                    return;
                }

                try {
                    // 요청 중 상태 설정
                    this.requesting = true;

                    console.log(`친구 요청 전송: 사용자 ID ${otherUserId}`);

                    // API 호출: request_friend 함수 사용
                    await func('request_friend', {
                        me: myUserId,
                        other: otherUserId,
                        auth: true // Firebase 인증 포함
                    });

                    // 성공: 친구 상태 업데이트
                    this.isFriend = true;
                    this.requesting = false;

                    console.log(`친구 요청 성공: 사용자 ID ${otherUserId}`);
                    alert(t.친구_요청_전송_완료);

                } catch (error) {
                    console.error('친구 요청 실패:', error);
                    this.requesting = false;

                    // 에러 메시지 표시
                    const errorMessage = error.message || error.error_message || t.친구_요청에_실패했습니다;
                    alert(`${t.친구_요청_실패}: ${errorMessage}`);
                }
            }
        },
        mounted() {
            console.log('[profile] Vue.js 프로필 페이지 초기화 완료');
        }
    }).mount('#profile-app');
});
