<script defer src="/js/file-upload.js?v=<?= APP_VERSION ?>"></script>
<style>
    /* 프로필 사진 컨테이너 */
    #profile-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-color: #f0f0f0;
        cursor: pointer;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
        position: relative;
    }

    /* uploaded-files div가 profile-photo를 완전히 채우도록 */
    #profile-photo .uploaded-files {
        width: 100%;
        height: 100%;
        margin: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 0;
        left: 0;
        overflow: hidden;
        border-radius: 50%;
    }

    /* 이미지가 타원형을 완전히 채우도록 */
    #profile-photo .uploaded-files img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0;
        max-width: none !important;
    }

    /* progress bar를 타원형 아래에 명확히 표시 */
    #profile-photo .file-upload-progress-container {
        position: relative;
        width: 150px;
        margin-top: 160px !important;
        z-index: 100;
    }

    /* 카메라 아이콘 스타일 */
    .camera-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        font-size: 2em;
        cursor: pointer;
        background-color: white;
        border-radius: 50%;
        padding: 8px;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s;
    }

    .camera-icon:hover {
        background-color: #f8f9fa;
        transform: scale(1.05);
    }

    /* 삭제 버튼 스타일 */
    .profile-photo-delete-button {
        position: absolute;
        top: 0;
        right: 0;
        background-color: white;
        border: none;
        border-radius: 50%;
        padding: 8px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s;
    }

    .profile-photo-delete-button:hover {
        background-color: #fff5f5;
        transform: scale(1.05);
    }

    .profile-photo-delete-button i {
        font-size: 1.5em;
        color: #dc3545;
    }
</style>

<div class="d-inline-block position-relative" style="width: 150px; height: 150px;">
    <!-- 프로필 사진 업로드 영역 -->
    <label class="d-inline-block position-relative" style="width: 150px; height: 150px; cursor: pointer;">
        <!-- 프로필 사진 표시 영역 -->
        <div id="profile-photo"
            data-single="true"
            data-default-files="<?= login()->photo_url ?>"></div>

        <!-- 파일 입력 (숨김) -->
        <input type="file"
            onchange="handle_file_change(event, { id: 'profile-photo', on_uploaded: on_uploaded })"
            class="d-none"
            accept="image/*" />

        <!-- 카메라 아이콘 (항상 표시) -->
        <i class="fa-solid fa-camera camera-icon text-body"></i>
    </label>

    <!-- 삭제 버튼 (프로필 사진이 있을 때만 표시) -->
    <button class="profile-photo-delete-button <?= login()->photo_url ? '' : 'd-none' ?>"
        type="button"
        onclick="delete_file(get_hidden_input_value('profile-photo'), {id: 'profile-photo', on_deleted: on_deleted});"
        title="<?= t()->삭제 ?>">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<script>
    /**
     * 파일 업로드 후 호출되는 콜백 함수
     *
     * @param {Object} json - 업로드된 파일의 JSON 응답
     * @param {string} json.url - 업로드된 파일의 URL
     */
    function on_uploaded(json) {
        if (json && json.url) {
            // func() 함수를 사용하여 프로필 사진 업데이트
            func('update_user_profile', {
                photo_url: json.url,
                auth: true,
                alertOnError: false
            }).then(response => {
                console.log('프로필 사진 업데이트 성공:', response);

                // 삭제 버튼 표시
                document.querySelector('.profile-photo-delete-button').classList.remove('d-none');
            }).catch(error => {
                console.error('프로필 사진 업데이트 실패:', error);
                alert('프로필 사진 업데이트에 실패했습니다: ' + error.message);
            });
        }
    }

    /**
     * 파일 삭제 후 호출되는 콜백 함수
     *
     * @param {Object} json - 삭제된 파일의 JSON 응답
     * @param {boolean} json.deleted - 삭제 성공 여부
     */
    function on_deleted(json) {
        if (json && json.deleted) {
            // func() 함수를 사용하여 프로필 사진 제거
            func('update_user_profile', {
                photo_url: '',
                auth: true,
                alertOnError: false
            }).then(response => {
                console.log('프로필 사진 삭제 성공:', response);

                // 삭제 버튼 숨김
                document.querySelector('.profile-photo-delete-button').classList.add('d-none');
            }).catch(error => {
                console.error('프로필 사진 삭제 실패:', error);
                alert('프로필 사진 삭제에 실패했습니다: ' + error.message);
            });
        }
    }
</script>
