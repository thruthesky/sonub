<script defer src="/js/phone-number.js"></script>

<script>
    function setupRecaptcha() {
        // 기존 reCAPTCHA 인스턴스가 있으면 정리
        if (window.recaptchaVerifier) {
            // 이미 렌더링된 경우 reset만 수행
            if (window.recaptchaWidgetId !== undefined && typeof grecaptcha !== 'undefined') {
                try {
                    grecaptcha.reset(window.recaptchaWidgetId);
                    // console.log('reCAPTCHA reset completed');
                    return Promise.resolve(window.recaptchaWidgetId);
                } catch (e) {
                    console.warn('Failed to reset reCAPTCHA:', e);
                }
            }

            // reset이 실패하면 clear 시도
            try {
                window.recaptchaVerifier.clear();
                window.recaptchaVerifier = null;
                window.recaptchaWidgetId = undefined;
            } catch (e) {
                console.warn('Failed to clear reCAPTCHA:', e);
            }
        }

        // 새 reCAPTCHA 인스턴스 생성
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier(
            'sign-in-button', // invisible일 때는 보통 클릭하면 동작 할 버튼의 id
            {
                size: 'invisible',
                callback: function(token) {
                    // reCAPTCHA 통과 시 자동 호출 (토큰 발급)
                    // console.log('reCAPTCHA verified:', token);
                },
                // 'expired-callback' 는 reCAPTCHA가 만료되었을 때 호출된다.
                // 즉, 사용자가 SMS 코드를 받고 나서, 오랜동안 SMS 코드 입력을 하지 않을 경우 호출된다.
                'expired-callback': function() {
                    // 토큰 만료 시 호출: reset 또는 재생성
                    console.warn('reCAPTCHA expired. Resetting...');
                    if (typeof grecaptcha !== 'undefined' && window.recaptchaWidgetId !== undefined) {
                        // 가장 빠른 방법: 위젯 리셋
                        grecaptcha.reset(window.recaptchaWidgetId);
                    } else {
                        // 상황에 따라 상태가 꼬일 수 있으므로, 안전하게 재생성
                        setupRecaptcha();
                    }
                }
            });

        // 사용자가 sign-in-button 을 클릭하기 전에,
        // 미리 reCAPTCHA 를 렌더링을 해서, 사용자가 SMS 코드 전송 버튼을 누르면 즉시 반응하도록 한다.
        // 목표: 사전 렌더링하여, 첫 클릭 지연 줄이는 것!
        return window.recaptchaVerifier.render().then(function(widgetId) {
            window.recaptchaWidgetId = widgetId;
            console.log('reCAPTCHA rendered with widgetId:', widgetId);
        }).catch(function(error) {
            console.error('Failed to render reCAPTCHA:', error);
            throw error;
        });
    }

    function login() {
        return {
            step: 'input-phone-number',
            loading: false,
            phoneNumber: '',
            smsCode: '',
            confirmationResult: null,
            error: '',

            async sendSMSCode() {
                this.loading = true;
                this.error = '';

                try {
                    // Format phone number to international format
                    const formattedPhone = check_login_phone_number(this.phoneNumber);

                    console.log("Formatted Phone", formattedPhone);
                    // Setup reCAPTCHA
                    setupRecaptcha();

                    // Send SMS code
                    // this.confirmationResult = await firebase.auth().signInWithPhoneNumber(formattedPhone, window.recaptchaVerifier);

                    this.step = 'input-sms-code';
                    console.log('SMS sent successfully');

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

                    // Redirect to dashboard or handle successful login
                    window.location.href = '';

                } catch (error) {
                    console.error('Error verifying SMS code:', error);
                    this.error = this.getErrorMessage(error);
                } finally {
                    this.loading = false;
                }
            },


            getErrorMessage(error) {
                switch (error.code) {
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
                        return error.message || 'An error occurred. Please try again.';
                }
            },

            resetForm() {
                this.step = 'input-phone-number';
                this.phoneNumber = '';
                this.smsCode = '';
                this.confirmationResult = null;
                this.error = '';
                this.loading = false;
            }
        }
    }
</script>


<h1>Testing</h1>

<form x-data="login()" id="login-form" action="#" class="align-content">
    <!-- Error Display -->
    <div x-show="error" class="alert alert-danger mb-3" role="alert">
        <i class="fa-solid fa-exclamation-triangle me-2"></i>
        <span x-text="error"></span>
    </div>

    <!-- Phone Number Input Section -->
    <section x-show="step === 'input-phone-number'">
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input
                type="tel"
                x-model="phoneNumber"
                class="form-control p-2 xl"
                id="phone_number"
                name="phone_number"
                required
                placeholder="Please enter your phone number"
                autofocus
                @keyup.enter="sendSMSCode()"
                maxlength="15">
        </div>
        <small class="d-block text-muted">
            <i class="fa-solid fa-info-circle me-1"></i>
            Format: 09XX-XXX-XXXX or +63-9XX-XXX-XXXX
        </small>

        <nav>
            <button
                x-show="!loading"
                @click="sendSMSCode()"
                id="sign-in-button"
                type="button"
                class="btn btn-primary my-5 px-3 py-2"
                :disabled="!phoneNumber.trim()">
                Send SMS Code
            </button>
            <button
                x-show="loading"
                type="button"
                class="btn btn-primary my-5 px-3 py-2"
                disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Sending...
            </button>
        </nav>
    </section>

    <!-- SMS Code Input Section -->
    <section x-show="step === 'input-sms-code'">
        <div class="mb-3">
            <label for="sms_code" class="form-label">SMS Code</label>
            <input
                type="text"
                x-model="smsCode"
                id="sms_code"
                class="form-control p-2 xl"
                required
                placeholder="Enter the 6-digit code"
                @keyup.enter="verifySMSCode()"
                maxlength="6"
                autofocus>
        </div>
        <small class="d-block text-muted">
            <i class="fa-solid fa-info-circle me-1"></i>
            Enter the 6-digit code sent to your phone
        </small>

        <nav>
            <button
                x-show="!loading"
                @click="verifySMSCode()"
                type="button"
                class="btn btn-primary my-5 px-3 py-2 me-3"
                :disabled="smsCode.length < 6">
                Verify Code
            </button>
            <button
                x-show="loading"
                type="button"
                class="btn btn-primary my-5 px-3 py-2 me-3"
                disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Verifying...
            </button>
            <button
                @click="resetForm()"
                type="button"
                class="btn btn-secondary my-5 px-3 py-2">
                Back to Phone Number
            </button>
        </nav>
    </section>
</form>