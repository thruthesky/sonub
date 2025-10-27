<?php
// 국가 목록 데이터 로드
require_once ROOT_DIR . '/etc/country/country-list.php';

// 다국어 번역 함수 호출
inject_login_language();

// 기존 JSON 상수를 JavaScript로 변환 (raw array 사용)
$countriesJSON = json_encode(COUNTRIES, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>


<script defer src="/js/firebase-auth-error.js"></script>

<script>
    // PHP에서 로드된 국가 목록 데이터 (<?= count(COUNTRIES) ?>개 국가)
    const COUNTRIES_RAW = <?= $countriesJSON ?>;

    // 원본 배열을 Vue.js가 기대하는 형식으로 변환
    // 원본: { countryNameEn, countryNameLocal, countryCallingCode, flag, region, ... }
    // 변환: { code, name, nameLocal, flag, dial, region }
    const ALL_COUNTRIES = COUNTRIES_RAW.map((country, index) => {
        // 고유한 코드 생성: 배열 인덱스 기반 (C000, C001, ..., C263)
        // 영문명 첫 2글자만으로는 중복 가능 (Japan=JA, Jamaica=JA)
        const code = 'C' + index.toString().padStart(3, '0');

        return {
            code: code,
            name: country.countryNameEn,
            nameLocal: country.countryNameLocal,
            flag: country.flag,
            dial: country.countryCallingCode,
            region: country.region,
        };
    });

    firebase_ready(() => {

        /**
         * Firebase reCAPTCHA 인스턴스 초기화 및 렌더링
         *
         * 새 reCAPTCHA 인스턴스를 생성하고 렌더링합니다.
         * 기존 인스턴스가 있으면 먼저 정리하고 새로 생성합니다.
         *
         * @returns {Promise<number>} reCAPTCHA widget ID
         */
        async function setupRecaptcha() {
            // 기존 reCAPTCHA 인스턴스가 있으면 정리
            if (window.recaptchaVerifier) {
                try {
                    window.recaptchaVerifier.clear();
                    window.recaptchaVerifier = null;
                    window.recaptchaWidgetId = undefined;
                    console.log('Old reCAPTCHA instance cleared');
                } catch (e) {
                    console.warn('Failed to clear reCAPTCHA:', e);
                }
            }

            // 새 reCAPTCHA 인스턴스 생성
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier(
                'sign-in-button', {
                    size: 'invisible',
                    callback: function(token) {
                        // reCAPTCHA 통과 시 자동 호출 (토큰 발급)
                        console.log('reCAPTCHA verified');
                    },
                    // 'expired-callback' 는 reCAPTCHA가 만료되었을 때 호출된다.
                    // 즉, 사용자가 SMS 코드를 받고 나서, 오랜동안 SMS 코드 입력을 하지 않을 경우 호출된다.
                    'expired-callback': function() {
                        // 토큰 만료 시 호출: 새로운 인스턴스 생성 필요
                        console.warn('reCAPTCHA token expired. Cleanup required.');
                        window.recaptchaWidgetId = undefined;
                    }
                }
            );

            // reCAPTCHA 렌더링
            // 사전 렌더링하여 사용자가 버튼을 클릭할 때 즉시 반응하도록 합니다.
            try {
                const widgetId = await window.recaptchaVerifier.render();
                window.recaptchaWidgetId = widgetId;
                console.log('reCAPTCHA rendered successfully with widgetId:', widgetId);
                return widgetId;
            } catch (error) {
                console.error('Failed to render reCAPTCHA:', error);
                // 렌더링 실패 시 인스턴스 정리
                try {
                    window.recaptchaVerifier.clear();
                } catch (e) {
                    console.warn('Failed to clear reCAPTCHA on render error:', e);
                }
                window.recaptchaVerifier = null;
                window.recaptchaWidgetId = undefined;
                throw error;
            }
        }

    })

    /**
     * Phone number validation for login
     *
     * @logic
     * - Removes special characters from the input phone number and generates country dial codes for Korea (+82) or Philippines (+63).
     * - If the input phone number starts with 010, this value is changed to +8210.
     * - If the input phone number starts with 09, this value is changed to +639.
     * - If the input phone number starts with +, the international phone number is used as is.
     * - For both Korean and Philippine phone numbers, the total length of the phone number must be 13 digits.
     *   Examples: '+821012345678' or '+639171234567'
     *
     * @param {string} phone_number - The phone number entered by the user
     *
     * @return {string} - Returns a valid phone number or throws an error code.
     * - If there is an error, throws an error like throw new Error('xxx').
     *
     * @example
     * ```javascript
     *    $(() => {
     *        console.log("DOM ready");
     *        alert(check_login_phone_number('+8210123456-78'));
     *    });
     * ```
     */

    /**
     * 전화번호를 국제 형식으로 포맷
     *
     * @param {string} phone_number - 사용자가 입력한 전화번호 (예: "010-1234-5678", "09123456789")
     * @param {string} country_dial_code - 국가 다이얼 코드 (예: "82", "63")
     * @returns {string} 국제 형식 전화번호 (예: "+821012345678")
     *
     * 처리 과정:
     * 1. 앞뒤 공백 제거
     * 2. 모든 특수문자(-,(),공백 등) 제거하여 숫자만 남김
     * 3. 맨 앞의 0 제거 (한국: 010 → 10, 필리핀: 09 → 9)
     * 4. 국가 다이얼 코드(+) + 정제된 전화번호 조합
     *
     * @example
     * check_login_phone_number('010-1234-5678', '82') // 반환: '+821012345678'
     * check_login_phone_number('09123456789', '63') // 반환: '+639123456789'
     * check_login_phone_number('010-1234-5678', '+82') // 반환: '+821012345678' (+기호 있어도 처리)
     * check_login_phone_number('010-1234-5678', '+82 ') // 반환: '+821012345678' (공백 있어도 처리)
     */
    function check_login_phone_number(phone_number, country_dial_code) {
        // 1. 앞뒤 공백 제거
        phone_number = phone_number.trim();

        // 2. 모든 특수문자 제거 (숫자만 남김)
        phone_number = phone_number.replace(/\D/g, '');

        // 3. 맨 앞의 0 제거 (사용자가 국내 형식으로 입력한 경우 처리)
        if (phone_number.startsWith('0')) {
            phone_number = phone_number.substring(1);
        }

        // 4. 국가 다이얼 코드에서 '+' 및 특수문자 제거하고 숫자만 추출
        // (country_dial_code가 '+82', '82', '+82 ' 등 어떤 형식이든 처리)
        const dialCode = country_dial_code.trim().replace(/\D/g, '');

        // 5. 최종 국제 형식 전화번호 반환: +국가코드전화번호
        return '+' + dialCode + phone_number;
    }
</script>


<div id="login-app">
    <form @submit.prevent id="login-form" class="align-content">
        <!-- 에러 표시 -->
        <div v-if="error" class="alert alert-danger mb-3" role="alert">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            <span>{{ error }}</span>
        </div>

        <!-- 국가 선택 섹션 -->
        <section v-show="step === 'select-country'">
            <div class="text-center mb-4">
                <p class="fs-6 fw-semibold text-secondary"><?= t()->어느_국가에서_로그인하시나요 ?></p>
            </div>

            <!-- 주요 국가 선택 (필리핀, 한국) -->
            <nav class="d-flex gap-3 justify-content-center mb-4">
                <!-- 필리핀 -->
                <button
                    type="button"
                    @click="selectCountry(phCode)"
                    class="btn btn-outline-primary py-4 px-5 d-flex flex-column align-items-center"
                    style="min-width: 120px;">
                    <span class="fs-1 mb-2">🇵🇭</span>
                    <span class="fs-6 fw-semibold"><?= t()->필리핀 ?></span>
                </button>

                <!-- 한국 -->
                <button
                    type="button"
                    @click="selectCountry(krCode)"
                    class="btn btn-outline-primary py-4 px-5 d-flex flex-column align-items-center"
                    style="min-width: 120px;">
                    <span class="fs-1 mb-2">🇰🇷</span>
                    <span class="fs-6 fw-semibold"><?= t()->한국 ?></span>
                </button>
            </nav>

            <!-- 다른 국가 선택 -->
            <div class="text-center">
                <button
                    type="button"
                    @click="step = 'select-country-other'"
                    class="btn btn-link btn-sm text-secondary">
                    <i class="fa-solid fa-globe me-1"></i>
                    <?= t()->다른_국가_선택 ?>
                </button>
            </div>
        </section>

        <!-- 다른 국가 선택 섹션 -->
        <section v-show="step === 'select-country-other'">
            <div class="text-center mb-4">
                <button
                    type="button"
                    @click="step = 'select-country'"
                    class="btn btn-link btn-sm text-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    <?= t()->돌아가기 ?>
                </button>
            </div>

            <div class="mb-3">
                <label for="country_search" class="form-label"><?= t()->국가_검색 ?></label>
                <input
                    type="text"
                    v-model="countrySearchQuery"
                    class="form-control p-2"
                    id="country_search"
                    placeholder="<?= t()->국가명_또는_지역명으로_검색 ?>"
                    autofocus>
            </div>

            <!-- 국가 목록 -->
            <div class="list-group" style="max-height: 400px; overflow-y: auto;">
                <button
                    v-for="country in filteredCountries"
                    :key="country.code"
                    type="button"
                    @click="selectCountry(country.code)"
                    class="list-group-item list-group-item-action text-start py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-start gap-2">
                            <span class="fs-5">{{ country.flag }}</span>
                            <div>
                                <span class="fw-semibold d-block">{{ country.name }}</span>
                                <span class="text-muted small d-block" v-if="country.nameLocal && country.nameLocal !== country.name">{{ country.nameLocal }}</span>
                                <span class="text-secondary small d-block">{{ country.region }}</span>
                            </div>
                        </div>
                        <span class="text-muted ms-2">+{{ country.dial }}</span>
                    </div>
                </button>
            </div>
        </section>

        <!-- 전화번호 입력 섹션 -->
        <section v-show="step === 'input-phone-number'">
            <!-- 선택된 국가 표시 -->
            <div class="bg-light border rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start gap-2" style="flex: 1;">
                        <span class="fs-3">{{ selectedCountryInfo.flag }}</span>
                        <div style="flex: 1;">
                            <p class="mb-1 fw-semibold">{{ selectedCountryInfo.name }}</p>
                            <p class="mb-1 text-muted small" v-if="selectedCountryInfo.nameLocal && selectedCountryInfo.nameLocal !== selectedCountryInfo.name">
                                {{ selectedCountryInfo.nameLocal }}
                            </p>
                            <p class="mb-0 text-secondary small">
                                <i class="fa-solid fa-globe me-1"></i>{{ selectedCountryInfo.region }} | <span class="fw-semibold">+{{ selectedCountryInfo.dial }}</span>
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="step = 'select-country'"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-repeat me-1"></i><?= t()->변경 ?>
                    </button>
                </div>
            </div>

            <div class="mb-1">
                <label for="phone_number" class="form-label"><?= t()->전화번호 ?></label>
                <input
                    type="tel"
                    v-model="phoneNumber"
                    class="form-control p-2 xl"
                    id="phone_number"
                    name="phone_number"
                    required
                    autofocus
                    @keyup.enter="sendSMSCode"
                    maxlength="32">
            </div>
            <small><?= t()->전화번호를_입력해주세요 ?></small>

            <nav>
                <button
                    v-if="!loading"
                    @click="sendSMSCode"
                    id="sign-in-button"
                    type="button"
                    class="btn btn-primary my-5 px-3 py-2"
                    :disabled="!phoneNumber.trim()">
                    <?= t()->SMS_코드_전송 ?>
                </button>
                <button
                    v-if="loading"
                    type="button"
                    class="btn btn-primary my-5 px-3 py-2"
                    disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <?= t()->전송_중 ?>
                </button>
            </nav>
        </section>

        <!-- SMS 코드 입력 섹션 -->
        <section v-show="step === 'input-sms-code'">
            <div class="mb-3">
                <label for="sms_code" class="form-label"><?= t()->SMS_코드 ?></label>
                <input
                    type="text"
                    v-model="smsCode"
                    id="sms_code"
                    class="form-control p-2 xl"
                    required
                    placeholder="<?= t()->여섯자리_코드를_입력하세요 ?>"
                    @keyup.enter="verifySMSCode"
                    maxlength="6"
                    autofocus>
            </div>
            <small class="d-block text-muted">
                <i class="fa-solid fa-info-circle me-1"></i>
                <?= t()->휴대폰으로_받은_여섯자리_코드를_입력하세요 ?>
            </small>

            <nav class="d-flex justify-content-between">
                <aside>
                    <button
                        type="button"
                        class="btn btn-link my-5 px-3 py-2"
                        :class="{'d-none': !showResetButton}"
                        @click="resetForm">
                        <?= t()->다시_시도 ?>
                    </button>
                </aside>

                <aside>
                    <button
                        v-if="!loading"
                        @click="verifySMSCode"
                        type="button"
                        class="btn btn-primary my-5 px-3 py-2 me-3"
                        :disabled="smsCode.length < 6">
                        <?= t()->코드_확인 ?>
                    </button>
                    <button
                        v-if="loading"
                        type="button"
                        class="btn btn-primary my-5 px-3 py-2 me-3"
                        disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <?= t()->확인_중 ?>
                    </button>
                </aside>
            </nav>
        </section>
    </form>
</div>

<script>
    firebase_ready(() => {
        Vue.createApp({
            data() {
                return {
                    step: 'select-country', // 'select-country', 'select-country-other', 'input-phone-number', 'input-sms-code'
                    loading: false,
                    phoneNumber: '',
                    smsCode: '',
                    confirmationResult: null,
                    error: '',
                    showResetButton: false,
                    selectedCountry: '', // mounted()에서 필리핀으로 설정
                    countrySearchQuery: '',
                    countries: ALL_COUNTRIES, // PHP에서 로드된 국가 목록 (264개)
                    phCode: '', // 필리핀의 실제 국가 코드
                    krCode: '' // 한국의 실제 국가 코드
                }
            },
            computed: {
                selectedCountryInfo() {
                    return this.countries.find(c => c.code === this.selectedCountry) || this.countries[0];
                },
                filteredCountries() {
                    let filtered = this.countries;

                    // 검색어가 있으면 필터링
                    if (this.countrySearchQuery) {
                        const query = this.countrySearchQuery.toLowerCase();
                        filtered = this.countries.filter(country =>
                            // 영문명으로 검색 (Philippines, Japan 등)
                            country.name.toLowerCase().includes(query) ||
                            // 현지명으로 검색 (日本, 대한민국, 中国 등)
                            country.nameLocal.toLowerCase().includes(query) ||
                            // 지역명으로 검색 (Asia, Europe 등)
                            country.region.toLowerCase().includes(query) ||
                            // 전화코드로 검색 (+63, +82 등)
                            country.dial.includes(query)
                        );
                    }

                    // 영문 국가명 기준으로 알파벳순 정렬
                    return filtered.sort((a, b) => {
                        return a.name.localeCompare(b.name);
                    });
                }
            },
            methods: {
                selectCountry(countryCode) {
                    // 국가 선택
                    this.selectedCountry = countryCode;
                    // 국가 검색 쿼리 초기화
                    this.countrySearchQuery = '';
                    // 전화번호 입력 단계로 이동
                    this.step = 'input-phone-number';
                    // 폼 초기화
                    this.phoneNumber = '';
                    this.smsCode = '';
                    this.error = '';
                    this.confirmationResult = null;
                },

                async sendSMSCode() {
                    this.loading = true;
                    this.error = '';
                    this.showResetButton = false;

                    try {

                        if (this.phoneNumber.indexOf(':') !== -1) {
                            const arr = this.phoneNumber.split(':');
                            const res = await login_email_password(arr[0], arr[1]);
                            console.log("Login response", res);
                            await this.onLoginSuccess(res);
                            return;
                        }

                        // reCAPTCHA 준비 확인 및 필요하면 재설정
                        // - reCAPTCHA 토큰 만료 또는 미렌더링된 경우 대비
                        if (!window.recaptchaVerifier || window.recaptchaWidgetId === undefined) {
                            console.log('reCAPTCHA not ready. Reinitializing...');
                            await setupRecaptcha();
                        }

                        // 전화번호를 국제 형식으로 포맷
                        const formattedPhone = check_login_phone_number(this.phoneNumber, this.selectedCountryInfo.dial);

                        console.log("Formatted Phone", formattedPhone);

                        // SMS 코드 전송
                        this.confirmationResult = await firebase.auth().signInWithPhoneNumber(
                            formattedPhone,
                            window.recaptchaVerifier
                        );

                        this.step = 'input-sms-code';
                        console.log('SMS sent successfully');

                        // 15초 후 리셋 버튼 표시
                        setTimeout(() => {
                            this.showResetButton = true;
                        }, 1000 * 15);

                    } catch (error) {
                        console.error('Error sending SMS:', error);
                        this.error = this.getErrorMessage(error);
                    } finally {
                        this.loading = false;
                    }
                },

                async verifySMSCode() {
                    this.loading = true;
                    this.error = '';

                    try {
                        if (!this.confirmationResult) {
                            throw new Error('No confirmation result available. Please resend SMS code.');
                        }

                        const result = await this.confirmationResult.confirm(this.smsCode);
                        const user = result.user;

                        console.log('User signed in successfully:', user);

                        this.onLoginSuccess(user);

                    } catch (error) {
                        console.error('Error verifying SMS code:', error);
                        this.error = this.getErrorMessage(error);
                    } finally {
                        this.loading = false;
                    }
                },

                /**
                 * Firebase Auth 에러를 다국어 메시지로 변환
                 * firebase-auth-error.js의 getFirebaseAuthErrorMessage() 함수 활용
                 *
                 * @param {Error} error - Firebase Auth 에러 객체
                 * @returns {string} 다국어 번역된 에러 메시지
                 */
                getErrorMessage(error) {
                    // firebase-auth-error.js의 getFirebaseAuthErrorMessage() 함수가 있으면 사용
                    if (typeof getFirebaseAuthErrorMessage === 'function') {
                        return getFirebaseAuthErrorMessage(error);
                    }

                    // 폴백: 함수가 없는 경우 기본 에러 메시지 반환
                    switch (error?.code) {
                        case 'auth/invalid-phone-number':
                            return 'Invalid phone number format';
                        case 'auth/too-many-requests':
                            return 'Too many requests. Please try again later.';
                        case 'auth/invalid-verification-code':
                            return 'Invalid SMS code. Please check and try again.';
                        case 'auth/code-expired':
                            return 'SMS code has expired. Please request a new code.';
                        case 'auth/captcha-check-failed':
                            return 'reCAPTCHA verification failed. Please try again.';
                        default:
                            return error?.message || 'An error occurred. Please try again.';
                    }
                },

                resetForm() {
                    // SMS 코드 입력 화면에서 '다시 시도' 버튼을 클릭했을 때
                    // 전화번호 입력 단계로 돌아감 (국가는 유지)
                    this.step = 'input-phone-number';
                    this.phoneNumber = '';
                    this.smsCode = '';
                    this.confirmationResult = null;
                    this.error = '';
                    this.loading = false;
                    this.showResetButton = false;
                },

                async onLoginSuccess(user) {
                    // 로그인 성공 후 처리
                    // 예: 서버에 사용자 정보 전송, 리다이렉션 등
                    console.log('Login successful! User:', user);
                    await func('login_with_firebase', {
                        firebase_uid: user.uid,
                        phone_number: user.phoneNumber,
                        alertOnError: true,
                    });
                    // 로그인 성공 후 리다이렉션
                    window.location.href = '<?= href()->home ?>';
                }
            },
            mounted() {
                // 필리핀과 한국의 실제 국가 코드 찾기
                const phCountry = this.countries.find(c => c.name === 'Philippines');
                const krCountry = this.countries.find(c => c.name === 'South Korea');

                if (phCountry) {
                    this.phCode = phCountry.code;
                    this.selectedCountry = phCountry.code; // 초기값으로 필리핀 설정
                }
                if (krCountry) {
                    this.krCode = krCountry.code;
                }

                // reCAPTCHA 초기화 (비동기 처리)
                // 에러 발생 시 사용자가 SMS 전송을 시도할 때 다시 초기화됨
                setupRecaptcha().catch(error => {
                    console.warn('reCAPTCHA initialization failed. Will retry on SMS send:', error);
                });
            }
        }).mount('#login-app');
    })
</script>

<?php
/**
 * 로그인 페이지 다국어 번역 주입 함수
 *
 * 지원 언어: 한국어(ko), 영어(en), 일본어(ja), 중국어(zh)
 */
function inject_login_language()
{
    t()->inject([
        '어느_국가에서_로그인하시나요' => [
            'ko' => '어느 국가에서 로그인하시나요?',
            'en' => 'Which country are you logging in from?',
            'ja' => 'どの国からログインしていますか？',
            'zh' => '您要从哪个国家登录？'
        ],
        '필리핀' => [
            'ko' => '필리핀',
            'en' => 'Philippines',
            'ja' => 'フィリピン',
            'zh' => '菲律宾'
        ],
        '한국' => [
            'ko' => '한국',
            'en' => 'Korea',
            'ja' => '韓国',
            'zh' => '韩国'
        ],
        '다른_국가_선택' => [
            'ko' => '다른 국가 선택',
            'en' => 'Choose other country',
            'ja' => '他の国を選択',
            'zh' => '选择其他国家'
        ],
        '돌아가기' => [
            'ko' => '돌아가기',
            'en' => 'Back',
            'ja' => '戻る',
            'zh' => '返回'
        ],
        '국가_검색' => [
            'ko' => '국가 검색',
            'en' => 'Search Country',
            'ja' => '国を検索',
            'zh' => '搜索国家'
        ],
        '국가명_또는_지역명으로_검색' => [
            'ko' => '국가명 또는 지역명으로 검색',
            'en' => 'Search by country or region name',
            'ja' => '国名または地域名で検索',
            'zh' => '按国家或地区名称搜索'
        ],
        '전화번호' => [
            'ko' => '전화번호',
            'en' => 'Phone Number',
            'ja' => '電話番号',
            'zh' => '电话号码'
        ],
        '전화번호를_입력해주세요' => [
            'ko' => '전화번호를 입력해주세요',
            'en' => 'Please enter your phone number',
            'ja' => '電話番号を入力してください',
            'zh' => '请输入您的电话号码'
        ],
        'SMS_코드_전송' => [
            'ko' => 'SMS 코드 전송',
            'en' => 'Send SMS Code',
            'ja' => 'SMS コードを送信',
            'zh' => '发送短信代码'
        ],
        '전송_중' => [
            'ko' => '전송 중...',
            'en' => 'Sending...',
            'ja' => '送信中...',
            'zh' => '发送中...'
        ],
        'SMS_코드' => [
            'ko' => 'SMS 코드',
            'en' => 'SMS Code',
            'ja' => 'SMS コード',
            'zh' => '短信代码'
        ],
        '여섯자리_코드를_입력하세요' => [
            'ko' => '여섯자리 코드를 입력하세요',
            'en' => 'Enter the 6-digit code',
            'ja' => '6 桁のコードを入力してください',
            'zh' => '输入 6 位代码'
        ],
        '휴대폰으로_받은_여섯자리_코드를_입력하세요' => [
            'ko' => '휴대폰으로 받은 여섯자리 코드를 입력하세요',
            'en' => 'Enter the 6-digit code sent to your phone',
            'ja' => '電話に送信された 6 桁のコードを入力してください',
            'zh' => '输入发送到您手机的 6 位代码'
        ],
        '다시_시도' => [
            'ko' => '전화번호 다시 입력',
            'en' => 'Try again',
            'ja' => 'もう一度試す',
            'zh' => '重试'
        ],
        '코드_확인' => [
            'ko' => '코드 확인',
            'en' => 'Verify Code',
            'ja' => 'コードを確認',
            'zh' => '验证代码'
        ],
        '확인_중' => [
            'ko' => 'Verifying...',
            'en' => 'Verifying...',
            'ja' => '確認中...',
            'zh' => '验证中...'
        ],
        '변경' => [
            'ko' => '변경',
            'en' => 'Change',
            'ja' => '変更',
            'zh' => '更改'
        ],
    ]);
}
?>