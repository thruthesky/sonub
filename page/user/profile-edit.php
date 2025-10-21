<?php

/**
 * 다국어 번역 주입 함수
 *
 * 이 페이지에서 사용하는 모든 번역 텍스트를 주입합니다.
 */
function inject_profile_edit_language()
{
    t()->inject([
        '프로필_수정' => [
            'ko' => '프로필 수정',
            'en' => 'Edit Profile',
            'ja' => 'プロフィール編集',
            'zh' => '编辑资料'
        ],
        '표시_이름' => [
            'ko' => '표시 이름',
            'en' => 'Display Name',
            'ja' => '表示名',
            'zh' => '显示名称'
        ],
        '다른_사용자에게_표시되는_이름입니다' => [
            'ko' => '다른 사용자에게 표시되는 이름입니다.',
            'en' => 'The name displayed to other users.',
            'ja' => '他のユーザーに表示される名前です。',
            'zh' => '显示给其他用户的名称。'
        ],
        '성별' => [
            'ko' => '성별',
            'en' => 'Gender',
            'ja' => '性別',
            'zh' => '性别'
        ],
        '남성' => [
            'ko' => '남성',
            'en' => 'Male',
            'ja' => '男性',
            'zh' => '男性'
        ],
        '여성' => [
            'ko' => '여성',
            'en' => 'Female',
            'ja' => '女性',
            'zh' => '女性'
        ],
        '생년월일' => [
            'ko' => '생년월일',
            'en' => 'Birthday',
            'ja' => '生年月日',
            'zh' => '出生日期'
        ],
        '년도' => [
            'ko' => '년도',
            'en' => 'Year',
            'ja' => '年',
            'zh' => '年'
        ],
        '월' => [
            'ko' => '월',
            'en' => 'Month',
            'ja' => '月',
            'zh' => '月'
        ],
        '일' => [
            'ko' => '일',
            'en' => 'Day',
            'ja' => '日',
            'zh' => '日'
        ],
        '저장' => [
            'ko' => '저장',
            'en' => 'Save',
            'ja' => '保存',
            'zh' => '保存'
        ],
        '저장_중' => [
            'ko' => '저장 중...',
            'en' => 'Saving...',
            'ja' => '保存中...',
            'zh' => '保存中...'
        ],
        '취소' => [
            'ko' => '취소',
            'en' => 'Cancel',
            'ja' => 'キャンセル',
            'zh' => '取消'
        ],
        '프로필이_성공적으로_업데이트되었습니다' => [
            'ko' => '프로필이 성공적으로 업데이트되었습니다.',
            'en' => 'Profile updated successfully.',
            'ja' => 'プロフィールが正常に更新されました。',
            'zh' => '个人资料更新成功。'
        ],
        '표시_이름을_입력해주세요' => [
            'ko' => '표시 이름을 입력해주세요.',
            'en' => 'Please enter a display name.',
            'ja' => '表示名を入力してください。',
            'zh' => '请输入显示名称。'
        ],
        '프로필_정보를_불러오는_중_오류가_발생했습니다' => [
            'ko' => '프로필 정보를 불러오는 중 오류가 발생했습니다.',
            'en' => 'An error occurred while loading profile information.',
            'ja' => 'プロフィール情報の読み込み中にエラーが発生しました。',
            'zh' => '加载个人资料信息时发生错误。'
        ],
        '프로필_업데이트_중_오류가_발생했습니다' => [
            'ko' => '프로필 업데이트 중 오류가 발생했습니다.',
            'en' => 'An error occurred while updating profile.',
            'ja' => 'プロフィール更新中にエラーが発生しました。',
            'zh' => '更新个人资料时发生错误。'
        ],
    ]);
}

inject_profile_edit_language();

// 페이지 고유 로직: 로그인 확인
$user = login();
if (!$user) {
    include_once ROOT_DIR . '/widgets/login/login-first.php';
    return;
}

?>

<style>
    /* 프로필 편집 커버 영역 */
    .profile-edit-cover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 200px;
        border-radius: 8px;
        position: relative;
        margin-bottom: 80px;
    }

    .profile-edit-photo-wrapper {
        position: absolute;
        bottom: -60px;
        left: 50%;
        transform: translateX(-50%);
    }



    /* 프로필 편집 섹션 */
    .profile-edit-section {
        background-color: white;
        border-radius: 8px;
        margin-top: 1rem;
    }

    /* 섹션 제목 */
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #050505;
        margin-bottom: 1.5rem;
    }

    /* 폼 라벨 */
    .profile-edit-section .form-label {
        font-weight: 600;
        color: #050505;
        margin-bottom: 0.5rem;
    }

    /* 폼 텍스트 */
    .profile-edit-section .form-text {
        color: #65676b;
        font-size: 0.875rem;
    }

    /* 반응형 디자인 */
    @media (max-width: 768px) {
        .profile-edit-cover {
            height: 150px;
            margin-bottom: 70px;
        }

        .profile-edit-section {
            padding: 1.5rem 1rem;
        }

        .section-title {
            font-size: 1.25rem;
        }
    }
</style>

<!-- 페이지 고유 HTML -->
<div class="container py-4">
    <!-- 커버 영역 -->
    <div class="profile-edit-cover">
        <!-- 프로필 사진 편집 위젯 -->
        <div class="profile-edit-photo-wrapper">
            <?php include ROOT_DIR . '/widgets/user/profile/user-profile-edit-photo.php'; ?>
        </div>
    </div>

    <!-- 프로필 편집 폼 -->
    <div class="profile-edit-section">
        <h1 class="section-title"><?= t()->프로필_수정 ?></h1>





        <div id="profile-edit-component">
            <form @submit.prevent="updateProfile">
                <!-- 표시 이름 -->
                <div class="mb-4">
                    <label for="displayName" class="form-label"><?= t()->표시_이름 ?></label>
                    <input
                        type="text"
                        class="form-control"
                        id="displayName"
                        v-model="form.displayName"
                        required
                        maxlength="64">
                    <div class="form-text"><?= t()->다른_사용자에게_표시되는_이름입니다 ?></div>
                </div>

                <!-- 성별 -->
                <div class="mb-4">
                    <label class="form-label"><?= t()->성별 ?></label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="genderM"
                                value="M"
                                v-model="form.gender">
                            <label class="form-check-label" for="genderM"><?= t()->남성 ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="genderF"
                                value="F"
                                v-model="form.gender">
                            <label class="form-check-label" for="genderF"><?= t()->여성 ?></label>
                        </div>
                    </div>
                </div>

                <!-- 생년월일 -->
                <div class="mb-4">
                    <label class="form-label"><?= t()->생년월일 ?></label>
                    <div class="row g-2">
                        <!-- 년도 -->
                        <div class="col-4">
                            <select class="form-select" v-model="birthdayYear" @change="updateDays">
                                <option value=""><?= t()->년도 ?></option>
                                <option v-for="year in years" :key="year" :value="year">
                                    {{ year }}년
                                </option>
                            </select>
                        </div>
                        <!-- 월 -->
                        <div class="col-4">
                            <select class="form-select" v-model="birthdayMonth" @change="updateDays">
                                <option value=""><?= t()->월 ?></option>
                                <option v-for="month in 12" :key="month" :value="month">
                                    {{ month }}월
                                </option>
                            </select>
                        </div>
                        <!-- 일 -->
                        <div class="col-4">
                            <select class="form-select" v-model="birthdayDay">
                                <option value=""><?= t()->일 ?></option>
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

                <!-- 구분선 -->
                <hr class="my-4">

                <!-- 버튼 -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-if="!loading" class="fa-solid fa-check me-2"></i>
                        {{ loading ? '<?= t()->저장_중 ?>' : '<?= t()->저장 ?>' }}
                    </button>
                    <a href="<?= href()->user->profile ?>?id=<?= $user->id ?>" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark me-2"></i><?= t()->취소 ?>
                    </a>
                </div>
            </form>
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
        }).mount('#profile-edit-component');
    })
</script>