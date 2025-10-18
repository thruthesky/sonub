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

    // 데이터 속성에서 초기 데이터 가져오기
    const otherUserId = parseInt(appElement.dataset.otherUserId) || 0;
    const isMe = appElement.dataset.isMe === 'true';
    const myUserId = parseInt(appElement.dataset.myUserId) || 0;

    // Vue.js 프로필 앱 생성
    Vue.createApp({
        data() {
            return {
                // 친구 요청 상태
                requesting: false,
                isFriend: false,

                // 사용자 ID
                otherUserId: otherUserId,
                myUserId: myUserId,
                isMe: isMe
            };
        },
        methods: {
            /**
             * 친구 추가 요청
             */
            async requestFriend() {
                // 로그인 확인
                if (!this.myUserId) {
                    alert(t.로그인이_필요합니다);
                    window.location.href = '<?= href()->user->login ?>';
                    return;
                }

                // 자기 자신에게 친구 요청 방지
                if (this.otherUserId === this.myUserId) {
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

                    console.log(`친구 요청 전송: 사용자 ID ${this.otherUserId}`);

                    // API 호출: request_friend 함수 사용
                    await func('request_friend', {
                        me: this.myUserId,
                        other: this.otherUserId,
                        auth: true // Firebase 인증 포함
                    });

                    // 성공: 친구 상태 업데이트
                    this.isFriend = true;
                    this.requesting = false;

                    console.log(`친구 요청 성공: 사용자 ID ${this.otherUserId}`);
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
            console.log('[profile] Vue.js 프로필 페이지 초기화 완료:', {
                otherUserId: this.otherUserId,
                myUserId: this.myUserId,
                isMe: this.isMe
            });
        }
    }).mount('#profile-app');
});
