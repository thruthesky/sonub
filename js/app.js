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

    // 언어 선택 기능 초기화
    ready(() => {
        initLanguageSelector();
    });
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
        let errorMessage = error.message;

        if (error.response && error.response.data) {
            // 서버에서 반환한 에러 정보
            errorCode = error.response.data.error_code || errorCode;
            errorMessage = error.response.data.error_message || errorMessage;
        } else if (error.message) {
            // Error 객체에서 추출한 에러 메시지
            const match = error.message.match(/^([^:]+):\s*(.+)$/);
            if (match) {
                errorCode = match[1];
                errorMessage = match[2];
            }
        }

        // 콘솔에 에러 코드와 메시지 로그
        console.error(`Error occurred while calling ${name}:`, {
            errorCode,
            errorMessage,
            fullError: error
        });

        // alertOnError가 true일 때 사용자에게 에러 표시
        if (alertOnError) {
            alert(`Error: ${errorCode}\n${errorMessage}`);
        }

        // 에러 코드와 메시지를 포함하여 에러 던지기
        const customError = new Error(errorMessage);
        customError.code = errorCode;
        customError.originalError = error;
        throw customError;
    }
}

/**
 * 로그인 함수: 이 함수로는 지정된 몇 명의 사용자만 로그인을 할 수 있다.
 */
async function login(id, pw) {
    const userCredential = await firebase.auth().signInWithEmailAndPassword(id, pw);

    // Signed in
    const user = userCredential.user;
    if (!user) throw new Error("No user in login");
    console.log('로그인 성공:', user.uid);
    return user;


}
/**
 * 언어 선택 기능 초기화
 */
function initLanguageSelector() {
    // 언어 선택 옵션에 클릭 이벤트 리스너 추가
    const languageOptions = document.querySelectorAll('.language-option');
    languageOptions.forEach(option => {
        option.addEventListener('click', async (event) => {
            event.preventDefault();
            const selectedLang = event.target.getAttribute('data-lang');
            await selectLanguage(selectedLang);
        });
    });
}

/**
 * 언어 선택 및 저장
 * @param {string} languageCode - 선택된 언어 코드 (en, ko, ja, zh)
 */
async function selectLanguage(languageCode) {
    try {
        // API 호출하여 언어 선택 저장
        const response = await func('select_language', {
            language_code: languageCode,
            alertOnError: true
        });

        if (response.success) {
            // 현재 언어 표시 업데이트
            document.getElementById('currentLanguage').textContent = languageCode;

            // 페이지 새로고침하여 언어 변경 적용
            window.location.reload();
        }
    } catch (error) {
        console.error('언어 선택 중 오류 발생:', error);
    }
}