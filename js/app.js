(() => {
    // 자동 리소스 로딩: /js/app.info.js

    // 파이어베이스 로그인
    // firebase.auth().onAuthStateChanged(async (user) => {
    //     if (user) {
    //         if (!appConfig.id) {
    //             // 파이어베이스에 로그인했는데, 세션이 없는 상태
    //             await func('create_user_record', { firebase_uid: user.uid, });
    //         }
    //     } else {
    //         //  로그인 안 된 상태
    //     }
    // });


})();


/**
 * API 함수 호출
 * @param {*} name 
 * @param {*} params 
 * - auth: true - 현재 로그인한 사용자의 ID 토큰을 'idToken'에 포함
 * - alertOnError: true - 오류 발생 시 alert()로 알림
 * @returns 
 */
async function func(name, params = {}) {
    params.func = name;
    const alertOnError = params.alertOnError !== undefined ? params.alertOnError : true;
    if (params.auth) {
        params.idToken = await firebase.auth().currentUser.getIdToken(true);
        delete params.auth;
    }
    try {
        const res = await axios.post('/api.php', params);
        if (res.data.error_code) {
            throw new Error(res.data.error_code + ': ' + res.data.error_message);
        }
        return res.data;
    } catch (error) {
        // Axios 에러 응답에서 error_code와 error_message 추출
        let errorCode = 'unknown-error';
        let errorMessage = '알 수 없는 오류가 발생했습니다.';

        if (error.response && error.response.data) {
            // 서버에서 반환한 에러 정보 (HTTP 4xx, 5xx 응답)
            const data = error.response.data;
            if (data.error_code) {
                errorCode = data.error_code;
                errorMessage = data.error_message || errorCode;
            } else {
                // error_code가 없는 경우 응답 데이터를 그대로 사용
                errorMessage = typeof data === 'string' ? data : JSON.stringify(data);
            }
        } else if (error.message) {
            // Error 객체에서 추출한 에러 메시지
            const match = error.message.match(/^([^:]+):\s*(.+)$/);
            if (match) {
                errorCode = match[1];
                errorMessage = match[2];
            } else {
                errorMessage = error.message;
            }
        }

        // 에러 메시지 형식: "error_message (error_code)"
        const displayMessage = `${errorMessage} (${errorCode})`;

        // 콘솔에 에러 코드와 메시지 로그
        console.error(`Error occurred while calling ${name}:`, {
            errorCode,
            errorMessage,
            displayMessage,
            fullError: error
        });

        // alertOnError가 true일 때 사용자에게 에러 표시
        if (alertOnError) {
            alert(displayMessage);
        }

        // 에러 코드와 메시지를 포함하여 에러 던지기
        const customError = new Error(displayMessage);
        customError.code = errorCode;
        customError.message = displayMessage;
        customError.originalError = error;
        throw customError;
    }
}


/** 자신의 프로필 업데이트. 모든 필드 업데이트 가능. 참고: user.md */
async function update_my_profile(data) {
    return func('update_my_profile', data, { alert_on_error: true });
}


/**
 * 로그인 함수: 이 함수로는 지정된 몇 명의 사용자만 로그인을 할 수 있다.
 */
async function login_email_password(email, pw) {
    const userCredential = await firebase.auth().signInWithEmailAndPassword(email, pw);

    // Signed in
    const user = userCredential.user;
    if (!user) throw new Error("No user in login");
    console.log('로그인 성공:', user.uid);
    return user;


}


/**
 * Axios 에러 메시지를 추출하는 함수
 *
 * Axios 에러 객체에서 사용자 친화적인 에러 메시지를 생성합니다.
 * HTTP 상태 코드별로 적절한 메시지를 반환하고, 에러 코드와 메시지를 포함합니다.
 *
 * @param {AxiosError} err - Axios 에러 객체 (response, request, message 등 포함)
 * @param {object} options - 옵션 { alert_on_error: boolean, debug: boolean }
 * @returns {string} 사용자 친화적인 에러 메시지
 *
 * @example
 * try {
 *   await axios.post('/api', data);
 * } catch (err) {
 *   const message = get_axios_error_message(err, { alert_on_error: true });
 *   console.error(message);
 * }
 */
function get_axios_error_message(err, options = {}) {
    // 디버그 로그 출력
    console.error('get_axios_error_message():', err);

    // HTTP 상태 코드 추출
    // response가 있으면 상태 코드, 없으면 0 (네트워크 에러 등)
    const status = err.response?.status || 0;

    // 에러 코드와 메시지 초기화
    let errorCode = '';        // 서버가 반환한 에러 코드 (예: 'file-not-found')
    let errorMessage = 'Unknown error';  // 기본 에러 메시지

    /**
     * 1. response.data가 있는 경우 (서버 응답이 있는 경우)
     *
     * 서버가 정상적으로 응답했지만 에러 상태 코드(4xx, 5xx)를 반환한 경우
     * 예: 400 Bad Request, 404 Not Found, 500 Internal Server Error
     */
    if (err.response?.data) {
        const errorData = err.response.data;


        // 문자열인 경우 (서버가 단순 문자열 메시지를 반환)
        // 예: "파일을 찾을 수 없습니다"
        if (typeof errorData === 'string') {
            errorMessage = errorData;
        }
        // 객체인 경우 (서버가 구조화된 에러 객체를 반환)
        // 예: { error: 'file-not-found', message: '파일을 찾을 수 없습니다' }
        else if (typeof errorData === 'object') {

            errorCode = errorData.error_code || '';         // 에러 코드 (예: 'file-not-found')
            errorMessage = errorData.error_message || errorMessage;  // 에러 메시지
        }

    }
    /**
     * 2. request는 있지만 response가 없는 경우 (네트워크 에러)
     *
     * 요청은 전송되었지만 서버로부터 응답을 받지 못한 경우
     * 예: 서버가 다운됨, 네트워크 끊김, CORS 에러, 타임아웃
     */
    else if (err.request) {
        errorMessage = '네트워크 연결 오류 또는 서버 응답 없음';
    }
    /**
     * 3. 요청 설정 중 에러 발생
     *
     * 요청을 보내기 전에 에러가 발생한 경우
     * 예: 잘못된 URL, 잘못된 설정, 브라우저 제한
     */
    else if (err.message) {
        errorMessage = err.message;
    }

    /**
     * HTTP 상태 코드별 친화적인 메시지 생성
     *
     * 사용자가 이해하기 쉬운 상태 텍스트로 변환
     */
    let statusText = '';
    switch (status) {
        case 400:
            statusText = 'Bad Request';  // 잘못된 요청
            break;
        case 401:
            statusText = 'Unauthorized';  // 인증 필요
            break;
        case 403:
            statusText = 'Forbidden';  // 권한 없음
            break;
        case 404:
            statusText = 'Not Found';  // 찾을 수 없음
            break;
        case 500:
            statusText = 'Server Error';  // 서버 내부 오류
            break;
        case 502:
            statusText = 'Bad Gateway';  // 게이트웨이 오류
            break;
        case 503:
            statusText = 'Service Unavailable';  // 서비스 이용 불가
            break;
        case 0:
            statusText = 'Network Error';  // 네트워크 에러
            break;
        default:
            // 그 외 상태 코드
            statusText = status ? `Error ${status}` : 'Unknown Error';
    }

    /**
     * 최종 에러 메시지 생성
     *
     * 에러 코드가 있으면 [코드] 형식으로 포함
     * 예: "[file-not-found] 파일을 찾을 수 없습니다"
     */
    const fullMessage = errorCode ? `[${errorCode}] ${errorMessage}` : errorMessage;

    // 상세 에러 정보를 콘솔에 로깅 (디버깅용)
    if (options.debug) {
        console.error('API error details:', {
            status: status,           // HTTP 상태 코드
            statusText: statusText,   // 상태 텍스트
            errorCode: errorCode,     // 에러 코드
            errorMessage: errorMessage, // 에러 메시지
            fullError: err            // 전체 에러 객체
        });
    }
    console.log('extra options:', options);
    /**
     * alert_on_error 옵션이 true이면 사용자에게 에러 알림
     *
     * 팝업 창으로 상태 코드와 에러 메시지를 표시
     * 예: "요청 실패 (Not Found)\n\n[file-not-found] 파일을 찾을 수 없습니다"
     */
    if (options.alert_on_error) {
        alert(`요청 실패 (${statusText})\n\n${fullMessage}`);
    }

    // 상태 텍스트와 에러 메시지를 포함한 최종 문자열 반환
    return `${statusText}: ${fullMessage}`;
}


// Store (Vue 3 전역 상태 관리)
ready(() => {
    // Vue 전역 스토어 (모든 앱이 공유)

    const { reactive, computed } = Vue;

    // 1️⃣ 상태 (state)
    const state = reactive({
        count: 0,
        user: window.__HYDRATE__?.user ?? null,  // index.php에서 주입된 로그인 사용자 정보
        lang: window.__HYDRATE__?.lang || 'en'  // index.php에서 주입된 사용자 언어 정보
    });

    // 2️⃣ 계산값 (getters)
    const getters = {
        doubled: computed(() => state.count * 2)
    };

    // 3️⃣ 액션 (actions)
    const actions = {
        inc() { state.count++; },
        setUser(u) { state.user = u; },
        setUserPhotoUrl(url) { state.user = { ...state.user, photo_url: url }; }
    };

    // 4️⃣ 전역 노출 (모든 Vue 앱이 동일한 인스턴스를 사용)
    window.Store = { state, getters, actions };
});

// 사용자 프로필 아이콘 컴포넌트
// 여러 개의 .user-profile-icon 요소가 있을 수 있으므로 각각에 대해 Vue 앱 마운트
ready(() => {
    // 모든 .user-profile-icon 요소를 찾아서 각각 마운트
    document.querySelectorAll('.user-profile-icon').forEach((element) => {
        Vue.createApp({
            data() {
                return {
                    state: window.Store.state
                };
            },
            template: `
                <img v-if="state.user?.photo_url"
                     :src="state.user.photo_url"
                     class="rounded-circle"
                     style="width: 28px; height: 28px; object-fit: cover;"
                     alt="프로필 사진">
                <i v-else class="bi bi-person-circle fs-3"></i>
            `,
        }).mount(element);
    });
});



// 썸네일 이미지 생성 함수
function thumbnail(fileUrl, width, height, fit = 'cover', quality = 85, bgColor = 'ffffff') {
    // lib/file/file.functions.php의 thumbnail_url()과 동일한 기능
    const url = new URL(appConfig.api.thumbnail_url, window.location.origin);
    url.searchParams.set('src', encodeURIComponent(fileUrl));
    url.searchParams.set('w', width);
    url.searchParams.set('h', height);
    url.searchParams.set('fit', fit);
    url.searchParams.set('q', quality);
    url.searchParams.set('bg', bgColor);
    return url.toString();
}


// 다국어 번역 함수
// 예제: tr({ en: 'Hello', ko: '안녕하세요' });
function tr(texts = {}) {
    const lang = window.Store.state.lang;
    return texts[lang] || texts['en'] || '';
}