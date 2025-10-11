<?php
// 페이지 고유 로직: 로그인 확인
$user = login();
if (!$user) {
    include_once ROOT_DIR . '/widgets/login/login-first.php';
    return;
}


?>

<!-- 페이지 고유 HTML -->
<div class="sonub-container my-5">

    <?php include ROOT_DIR . '/widgets/user/profile/user-profile-edit-photo.php'; ?>



    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">프로필 수정</h4>
                </div>
                <div class="card-body">
                    <div id="app">
                        <form @submit.prevent="updateProfile">
                            <!-- 표시 이름 -->
                            <div class="mb-3">
                                <label for="displayName" class="form-label">표시 이름</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="displayName"
                                    v-model="form.displayName"
                                    required
                                    maxlength="64">
                                <div class="form-text">다른 사용자에게 표시되는 이름입니다.</div>
                            </div>

                            <!-- 성별 -->
                            <div class="mb-3">
                                <label class="form-label">성별</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            id="genderM"
                                            value="M"
                                            v-model="form.gender">
                                        <label class="form-check-label" for="genderM">남성</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            id="genderF"
                                            value="F"
                                            v-model="form.gender">
                                        <label class="form-check-label" for="genderF">여성</label>
                                    </div>
                                </div>
                            </div>

                            <!-- 생년월일 -->
                            <div class="mb-3">
                                <label class="form-label">생년월일</label>
                                <div class="row g-2">
                                    <!-- 년도 -->
                                    <div class="col-4">
                                        <select class="form-select" v-model="birthdayYear" @change="updateDays">
                                            <option value="">년도</option>
                                            <option v-for="year in years" :key="year" :value="year">
                                                {{ year }}년
                                            </option>
                                        </select>
                                    </div>
                                    <!-- 월 -->
                                    <div class="col-4">
                                        <select class="form-select" v-model="birthdayMonth" @change="updateDays">
                                            <option value="">월</option>
                                            <option v-for="month in 12" :key="month" :value="month">
                                                {{ month }}월
                                            </option>
                                        </select>
                                    </div>
                                    <!-- 일 -->
                                    <div class="col-4">
                                        <select class="form-select" v-model="birthdayDay">
                                            <option value="">일</option>
                                            <option v-for="day in availableDays" :key="day" :value="day">
                                                {{ day }}일
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 에러 메시지 -->
                            <div v-if="errorMessage" class="alert alert-danger" role="alert">
                                {{ errorMessage }}
                            </div>

                            <!-- 버튼 -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                                    {{ loading ? '저장 중...' : '저장' }}
                                </button>
                                <a href="/" class="btn btn-secondary">취소</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast 알림 -->
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                프로필이 성공적으로 업데이트되었습니다.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- 페이지 고유 Vue.js 앱 -->
<script>
    ready(() => {

        Vue.createApp({
            data() {
                return {
                    form: {
                        displayName: '',
                        gender: '',
                    },
                    birthdayYear: '',
                    birthdayMonth: '',
                    birthdayDay: '',
                    years: [],
                    availableDays: [],
                    loading: false,
                    errorMessage: ''
                };
            },
            mounted() {
                this.initializeYears();
                this.loadUserProfile();
            },
            methods: {
                /**
                 * 년도 선택 옵션 초기화
                 * 60년 전부터 18년 전까지
                 */
                initializeYears() {
                    const currentYear = new Date().getFullYear();
                    const startYear = currentYear - 60;
                    const endYear = currentYear - 18;

                    for (let year = endYear; year >= startYear; year--) {
                        this.years.push(year);
                    }
                },

                /**
                 * 선택된 년도와 월에 따라 일 수 업데이트
                 */
                updateDays() {
                    if (!this.birthdayYear || !this.birthdayMonth) {
                        this.availableDays = Array.from({
                            length: 31
                        }, (_, i) => i + 1);
                        return;
                    }

                    const year = parseInt(this.birthdayYear);
                    const month = parseInt(this.birthdayMonth);
                    const daysInMonth = new Date(year, month, 0).getDate();

                    this.availableDays = Array.from({
                        length: daysInMonth
                    }, (_, i) => i + 1);

                    // 현재 선택된 일이 새로운 월의 일 수보다 크면 초기화
                    if (this.birthdayDay && parseInt(this.birthdayDay) > daysInMonth) {
                        this.birthdayDay = '';
                    }
                },

                /**
                 * 사용자 프로필 로드
                 */
                async loadUserProfile() {
                    try {
                        // PHP에서 전달받은 사용자 정보
                        const user = <?= json_encode($user->toArray()) ?>;

                        this.form.displayName = user.display_name || '';
                        this.form.gender = user.gender || '';

                        // 생년월일 파싱 (Unix timestamp)
                        if (user.birthday && user.birthday > 0) {
                            const birthday = new Date(user.birthday * 1000);
                            this.birthdayYear = birthday.getFullYear();
                            this.birthdayMonth = birthday.getMonth() + 1;
                            this.birthdayDay = birthday.getDate();
                            this.updateDays();
                        } else {
                            this.updateDays();
                        }
                    } catch (error) {
                        console.error('프로필 로드 오류:', error);
                        this.errorMessage = '프로필 정보를 불러오는 중 오류가 발생했습니다.';
                    }
                },

                /**
                 * 프로필 업데이트 제출
                 */
                async updateProfile() {
                    this.errorMessage = '';

                    // 유효성 검사
                    if (!this.form.displayName.trim()) {
                        this.errorMessage = '표시 이름을 입력해주세요.';
                        return;
                    }

                    // 생년월일을 Unix timestamp로 변환
                    let birthday = 0;
                    if (this.birthdayYear && this.birthdayMonth && this.birthdayDay) {
                        const birthDate = new Date(
                            parseInt(this.birthdayYear),
                            parseInt(this.birthdayMonth) - 1,
                            parseInt(this.birthdayDay)
                        );
                        birthday = Math.floor(birthDate.getTime() / 1000);
                    }

                    this.loading = true;

                    try {
                        const result = await update_my_profile({
                            display_name: this.form.displayName.trim(),
                            gender: this.form.gender,
                            birthday: birthday
                        });

                        // Bootstrap Toast 표시
                        const toastElement = document.getElementById('successToast');
                        const toast = new bootstrap.Toast(toastElement);
                        toast.show();

                    } catch (error) {
                        console.error('프로필 업데이트 오류:', error);
                        this.errorMessage = error.message || '프로필 업데이트 중 오류가 발생했습니다.';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }).mount('#app');
    })
</script>