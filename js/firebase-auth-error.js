/**
 * Firebase Auth 오류 메시지 처리 함수
 *
 * Firebase Auth에서 발생하는 오류 코드를 사용자 친화적인 메시지로 변환합니다.
 * 다국어(ko, en, ja, zh)를 지원합니다.
 *
 * 사용법:
 * - getFirebaseAuthErrorMessage(error) - 현재 사용자 언어로 오류 메시지 반환
 * - getFirebaseAuthErrorMessage(error, 'en') - 특정 언어로 오류 메시지 반환
 *
 * @see https://firebase.google.com/docs/reference/js/auth
 */

/**
 * Firebase Auth 오류 코드를 다국어 메시지로 변환
 *
 * @param {Error} error - Firebase Auth 오류 객체
 * @param {string} lang - 언어 코드 (ko, en, ja, zh). 기본값은 현재 사용자 언어
 * @returns {string} 사용자 친화적인 오류 메시지
 */
function getFirebaseAuthErrorMessage(error, lang = null) {
    // 사용자 언어 가져오기 (기본값: 한국어)
    if (!lang) {
        lang = typeof get_user_lang === 'function' ? get_user_lang() : 'ko';
    }

    // Firebase 오류가 아닌 경우 원본 메시지 반환
    if (!error || !error.code) {
        return error?.message || '알 수 없는 오류가 발생했습니다.';
    }

    const errorCode = error.code;

    // Firebase Auth 오류 메시지 맵 (다국어)
    const errorMessages = {
        // Phone Sign-In 관련 오류
        'auth/invalid-phone-number': {
            ko: '전화번호 형식이 올바르지 않습니다.',
            en: 'Invalid phone number format.',
            ja: '電話番号の形式が無効です。',
            zh: '电话号码格式无效。'
        },
        'auth/missing-phone-number': {
            ko: '전화번호를 입력해주세요.',
            en: 'Please enter your phone number.',
            ja: '電話番号を入力してください。',
            zh: '请输入电话号码。'
        },
        'auth/quota-exceeded': {
            ko: 'SMS 할당량이 초과되었습니다. 잠시 후 다시 시도해주세요.',
            en: 'SMS quota exceeded. Please try again later.',
            ja: 'SMS割り当てを超過しました。後でもう一度お試しください。',
            zh: '短信配额已超限。请稍后再试。'
        },
        'auth/invalid-verification-code': {
            ko: '인증 코드가 올바르지 않습니다. 다시 확인해주세요.',
            en: 'Invalid verification code. Please check again.',
            ja: '認証コードが無効です。もう一度確認してください。',
            zh: '验证码无效。请再次检查。'
        },
        'auth/invalid-verification-id': {
            ko: '인증 세션이 만료되었습니다. 다시 시도해주세요.',
            en: 'Verification session expired. Please try again.',
            ja: '認証セッションが期限切れです。もう一度お試しください。',
            zh: '验证会话已过期。请重试。'
        },
        'auth/code-expired': {
            ko: '인증 코드가 만료되었습니다. 새로운 코드를 요청해주세요.',
            en: 'Verification code expired. Please request a new code.',
            ja: '認証コードの有効期限が切れました。新しいコードをリクエストしてください。',
            zh: '验证码已过期。请请求新验证码。'
        },
        'auth/captcha-check-failed': {
            ko: '보안 인증(reCAPTCHA)에 실패했습니다. 페이지를 새로고침하고 다시 시도해주세요.',
            en: 'reCAPTCHA verification failed. Please refresh the page and try again.',
            ja: 'reCAPTCHA検証に失敗しました。ページを更新してもう一度お試しください。',
            zh: 'reCAPTCHA验证失败。请刷新页面并重试。'
        },
        'auth/missing-verification-code': {
            ko: '인증 코드를 입력해주세요.',
            en: 'Please enter the verification code.',
            ja: '認証コードを入力してください。',
            zh: '请输入验证码。'
        },
        'auth/session-expired': {
            ko: '인증 세션이 만료되었습니다. 처음부터 다시 시도해주세요.',
            en: 'Authentication session expired. Please start over.',
            ja: '認証セッションが期限切れです。最初からやり直してください。',
            zh: '认证会话已过期。请重新开始。'
        },

        // 일반 Auth 오류
        'auth/user-disabled': {
            ko: '이 계정은 비활성화되었습니다. 관리자에게 문의하세요.',
            en: 'This account has been disabled. Please contact support.',
            ja: 'このアカウントは無効化されています。サポートにお問い合わせください。',
            zh: '此帐户已被禁用。请联系支持人员。'
        },
        'auth/user-not-found': {
            ko: '사용자를 찾을 수 없습니다.',
            en: 'User not found.',
            ja: 'ユーザーが見つかりません。',
            zh: '找不到用户。'
        },
        'auth/wrong-password': {
            ko: '비밀번호가 올바르지 않습니다.',
            en: 'Incorrect password.',
            ja: 'パスワードが正しくありません。',
            zh: '密码不正确。'
        },
        'auth/invalid-email': {
            ko: '이메일 형식이 올바르지 않습니다.',
            en: 'Invalid email format.',
            ja: 'メールアドレスの形式が無効です。',
            zh: '电子邮件格式无效。'
        },
        'auth/email-already-in-use': {
            ko: '이미 사용 중인 이메일입니다.',
            en: 'Email already in use.',
            ja: 'このメールアドレスは既に使用されています。',
            zh: '电子邮件已被使用。'
        },
        'auth/weak-password': {
            ko: '비밀번호가 너무 약합니다. 더 강력한 비밀번호를 사용해주세요.',
            en: 'Password is too weak. Please use a stronger password.',
            ja: 'パスワードが弱すぎます。より強力なパスワードを使用してください。',
            zh: '密码太弱。请使用更强的密码。'
        },
        'auth/network-request-failed': {
            ko: '네트워크 오류가 발생했습니다. 인터넷 연결을 확인해주세요.',
            en: 'Network error occurred. Please check your internet connection.',
            ja: 'ネットワークエラーが発生しました。インターネット接続を確認してください。',
            zh: '网络错误。请检查您的互联网连接。'
        },
        'auth/too-many-requests': {
            ko: '너무 많은 요청이 발생했습니다. 잠시 후 다시 시도해주세요.',
            en: 'Too many requests. Please try again later.',
            ja: 'リクエストが多すぎます。しばらくしてからもう一度お試しください。',
            zh: '请求过多。请稍后再试。'
        },
        'auth/operation-not-allowed': {
            ko: '이 작업은 허용되지 않습니다.',
            en: 'This operation is not allowed.',
            ja: 'この操作は許可されていません。',
            zh: '不允许此操作。'
        },
        'auth/requires-recent-login': {
            ko: '보안상 다시 로그인이 필요합니다.',
            en: 'Please re-login for security.',
            ja: 'セキュリティのため再ログインが必要です。',
            zh: '出于安全考虑，请重新登录。'
        },
        'auth/credential-already-in-use': {
            ko: '이 인증 정보는 이미 다른 계정에서 사용 중입니다.',
            en: 'This credential is already associated with another account.',
            ja: 'この認証情報は既に別のアカウントで使用されています。',
            zh: '此凭据已与另一个帐户关联。'
        },
        'auth/timeout': {
            ko: '요청 시간이 초과되었습니다. 다시 시도해주세요.',
            en: 'Request timeout. Please try again.',
            ja: 'リクエストがタイムアウトしました。もう一度お試しください。',
            zh: '请求超时。请重试。'
        },

        // reCAPTCHA 관련 오류
        'auth/recaptcha-not-enabled': {
            ko: '보안 인증(reCAPTCHA)이 활성화되지 않았습니다.',
            en: 'reCAPTCHA is not enabled.',
            ja: 'reCAPTCHAが有効になっていません。',
            zh: 'reCAPTCHA未启用。'
        },
        'auth/missing-app-credential': {
            ko: '앱 인증 정보가 누락되었습니다.',
            en: 'App credential is missing.',
            ja: 'アプリの認証情報がありません。',
            zh: '缺少应用凭据。'
        },

        // 기타 오류 코드 (-39 같은 숫자 코드)
        'auth/error-code:-39': {
            ko: '너무 많은 요청을 하였습니다. 약 12시간 후 다시 시도해주세요.',
            en: 'Too many requests have been made. Please try again in about 12 hours.',
            ja: 'リクエストが多すぎます。約12時間後にもう一度お試しください。',
            zh: '请求过多。请在大约12小时后再试。'
        }
    };

    // 오류 코드에 해당하는 메시지 찾기
    if (errorMessages[errorCode]) {
        return errorMessages[errorCode][lang] || errorMessages[errorCode]['ko'];
    }

    // 정의되지 않은 오류 코드인 경우 기본 메시지 반환
    const defaultMessages = {
        ko: `오류가 발생했습니다. (${errorCode})\n${error.message || '알 수 없는 오류'}`,
        en: `An error occurred. (${errorCode})\n${error.message || 'Unknown error'}`,
        ja: `エラーが発生しました。(${errorCode})\n${error.message || '不明なエラー'}`,
        zh: `发生错误。(${errorCode})\n${error.message || '未知错误'}`
    };

    return defaultMessages[lang] || defaultMessages['ko'];
}

/**
 * Firebase Auth 오류를 콘솔에 로그로 남기고 사용자 친화적인 메시지 반환
 *
 * @param {Error} error - Firebase Auth 오류 객체
 * @param {string} lang - 언어 코드
 * @returns {string} 사용자 친화적인 오류 메시지
 */
function logAndGetFirebaseAuthError(error, lang = null) {
    // 개발자용 콘솔 로그
    console.error('Firebase Auth Error:', {
        code: error.code,
        message: error.message,
        fullError: error
    });

    // 사용자용 메시지 반환
    return getFirebaseAuthErrorMessage(error, lang);
}
