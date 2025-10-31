<?php

/**
 * 게시물 작성 폼
 *
 * @included in page/post/list.php with route: /post/list
 */

load_deferred_js('vue-components/file-upload.component');

// 다국어 번역 텍스트 주입
inject_post_list_create_language();

$category = http_param('category') ?? 'story';

// 로그인한 사용자 정보 가져오기
$user = login();
$user_photo = ($user && isset($user->photo_url) && !empty($user->photo_url)) ? $user->photo_url : '';
// first_name, middle_name, last_name을 조합하여 전체 이름 생성
$name_parts = [];
if ($user) {
    if (!empty($user->first_name)) $name_parts[] = $user->first_name;
    if (!empty($user->middle_name)) $name_parts[] = $user->middle_name;
    if (!empty($user->last_name)) $name_parts[] = $user->last_name;
}
$user_name = !empty($name_parts) ? implode(' ', $name_parts) : 'Guest';

?>



<style>
    /* 게시물 작성 위젯 전용 스타일 */
    .post-create-trigger {
        flex: 1;
        background-color: #f0f2f5;
        border: none;
        border-radius: 18px;
        padding: 10px 16px;
        font-size: 15px;
        color: #65676b;
        text-align: left;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .post-create-trigger:hover {
        background-color: #e4e6eb;
    }


    /* 커스텀 드롭다운 래퍼 */
    .post-select-wrapper {
        display: inline-flex;
        align-items: center;
        background-color: #e4e6eb;
        border-radius: 6px;
        padding: 4px 8px;
        cursor: pointer;
        transition: background-color 0.15s ease;
        position: relative;
    }

    .post-select-wrapper:hover {
        background-color: #d8dadf;
    }

    /* 커스텀 select 스타일 */
    .post-select {
        background: transparent;
        border: none;
        outline: none;
        font-size: 13px;
        font-weight: 600;
        color: #050505;
        cursor: pointer;
        padding: 0;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 4px;
    }

    .post-select:focus {
        outline: none;
    }

    /* 아이콘 색상 */
    .post-select-wrapper i {
        color: #65676b;
    }

    /* 게시 버튼 */
    .btn-post {
        background-color: #0866ff;
        border: none;
        padding: 8px 24px;
        font-size: 15px;
        font-weight: 600;
        color: white;
        cursor: pointer;
        border-radius: 6px;
        transition: background-color 0.15s ease;
    }

    .btn-post:hover {
        background-color: #0757d6;
    }

    .btn-post:disabled {
        background-color: #e4e6eb;
        color: #bcc0c4;
        cursor: not-allowed;
    }
</style>
<section id="post-list-create" class="mb-3 card" v-cloak>
    <div class="">
        <form @submit.prevent="submit_post">
            <div v-if="!expanded" class="d-flex align-items-center p-3 gap-2">
                <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                    style="width: 40px; height: 40px;">
                    <img v-if="userPhoto"
                        :src="userPhoto"
                        :alt="userName"
                        class="rounded-circle"
                        style="width: 100%; height: 100%; object-fit: cover;">
                    <i v-else class="fa-solid fa-user text-secondary" style="font-size: 20px;"></i>
                </div>

                <!-- 클릭 가능한 버튼 -->
                <button type="button"
                    @click="expand"
                    class="post-create-trigger flex-grow-1">
                    {{ placeholder }}
                </button>
            </div>

            <!-- 확장된 상태: 전체 폼 -->
            <div v-if="expanded">
                <!-- 헤더 섹션 (사용자 프로필) -->
                <header class="card-header bg-white d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #e4e6eb;">
                    <div class="d-flex align-items-center gap-2" style="flex: 1;">
                        <!-- 프로필 사진 -->
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                            style="width: 40px; height: 40px;">
                            <img v-if="userPhoto"
                                :src="userPhoto"
                                :alt="userName"
                                class="rounded-circle"
                                style="width: 100%; height: 100%; object-fit: cover;">
                            <i v-else class="fa-solid fa-user text-secondary" style="font-size: 20px;"></i>
                        </div>

                        <!-- 사용자 정보 -->
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size: 15px; color: #050505; line-height: 1.3;">
                                {{ userName }}
                            </div>
                            <div class="d-flex gap-2 align-items-center" style="font-size: 13px; color: #65676b; line-height: 1.3;">
                                <!-- 공개범위 선택 -->
                                <div class="post-select-wrapper">
                                    <i class="fa-solid fa-earth-americas" v-if="visibility === 'public'" style="font-size: 12px; margin-right: 4px;"></i>
                                    <i class="fa-solid fa-user-group" v-if="visibility === 'friends'" style="font-size: 12px; margin-right: 4px;"></i>
                                    <i class="fa-solid fa-lock" v-if="visibility === 'private'" style="font-size: 12px; margin-right: 4px;"></i>
                                    <select v-model="visibility" class="post-select">
                                        <option value="public"><?= t()->공개 ?></option>
                                        <option value="friends"><?= t()->친구만 ?></option>
                                        <option value="private"><?= t()->나만_보기 ?></option>
                                    </select>
                                    <i class="fa-solid fa-caret-down" style="font-size: 12px; margin-left: 4px; pointer-events: none;"></i>
                                </div>

                                <!-- 카테고리 선택 (공개일 때만) -->
                                <div v-if="visibility === 'public'" class="post-select-wrapper">
                                    <i class="fa-solid fa-folder" style="font-size: 12px; margin-right: 4px;"></i>
                                    <select v-model="category" class="post-select">
                                        <?php foreach (config()->categories->getRootCategories() as $root) : ?>
                                            <optgroup label="<?= htmlspecialchars($root->display_name) ?>">
                                                <?php foreach ($root->getCategories() as $sub) : ?>
                                                    <option value="<?= htmlspecialchars($sub->category) ?>"><?= htmlspecialchars($sub->name) ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fa-solid fa-caret-down" style="font-size: 12px; margin-left: 4px; pointer-events: none;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- 본문 섹션 -->
                <div class="card-body">
                    <!-- 게시물 내용 입력 -->
                    <textarea
                        ref="textarea"
                        v-model="content"
                        class="w-100 border-0 mb-3"
                        style="outline: none; font-size: 15px; color: #050505; line-height: 1.5; resize: none; min-height: 80px; max-height: 200px; overflow-y: auto; padding: 0; background: transparent;"
                        name="content"
                        :placeholder="placeholder"></textarea>

                    <!-- 업로드된 이미지 미리보기 -->
                    <div v-if="uploadedFiles.length > 0" class="mb-3">
                        <div class="row g-2">
                            <div v-for="(fileUrl, index) in uploadedFiles" :key="index"
                                :class="getPhotoColumnClass(uploadedFiles.length)">
                                <div class="position-relative">
                                    <img :src="thumbnail(fileUrl, 400, 400, 'cover', 85, 'ffffff')"
                                        :alt="'Photo ' + (index + 1)"
                                        style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">

                                    <!-- Delete button (X) on top right -->
                                    <button
                                        @click="removeFile(index)"
                                        type="button"
                                        class="btn btn-sm btn-danger position-absolute"
                                        style="top: 8px; right: 8px; width: 28px; height: 28px; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; opacity: 0.9; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"
                                        title="Remove image">
                                        <i class="fa-solid fa-xmark" style="font-size: 16px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden file upload component -->
                    <file-upload-component
                        ref="upload"
                        :multiple="true"
                        :accept="'image/*,video/*'"
                        :show-upload-button="false"
                        :show-uploaded-files="false"
                        :input-name="'files'"
                        @uploaded="handleFileUploaded">
                    </file-upload-component>
                </div>

                <!-- 하단 액션 영역 -->
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <!-- 사진/비디오 업로드 버튼 -->
                        <label class="btn btn-link text-decoration-none text-secondary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-1 flex-shrink-0">
                            <i class="fa-solid fa-image" style="color: #45bd62;"></i>
                            <span><?= t()->사진_동영상 ?></span>
                            <input type="file"
                                multiple
                                accept="image/*,video/*"
                                style="display: none;"
                                @change="$refs.upload.handleFileChange($event)">
                        </label>

                        <button type="submit"
                            class="btn btn-secondary px-4 py-2 fw-semibold"
                            :disabled="!canSubmit">
                            <?= t()->게시 ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    ready(() => {
        const app = Vue.createApp({
            components: {
                'file-upload-component': FileUploadComponent,
            },
            data() {
                return {
                    content: '',
                    expanded: false,
                    uploadedFiles: [], // 업로드된 파일 URL 배열
                    visibility: 'public',
                    category: '<?= $category ?>',
                    userPhoto: '<?= htmlspecialchars($user_photo) ?>',
                    userName: '<?= htmlspecialchars($user_name) ?>',
                    placeholder: "<?= t()->당신의_이야기를_들려주세요 ?>",
                };
            },
            computed: {
                /**
                 * 게시 버튼 활성화 여부
                 */
                canSubmit() {
                    return this.content.trim().length > 0 || this.uploadedFiles.length > 0;
                }
            },
            methods: {
                /**
                 * thumbnail 함수 (전역 함수 사용)
                 */
                thumbnail: thumbnail,

                /**
                 * 사진 개수에 따른 Bootstrap column 클래스 반환
                 * @param {number} photoCount - 사진 개수
                 * @returns {string} Bootstrap column 클래스
                 */
                getPhotoColumnClass(photoCount) {
                    if (photoCount === 1) return 'col-12';
                    if (photoCount === 2) return 'col-6';
                    if (photoCount === 3) return 'col-4';
                    return 'col-6 col-md-4 col-lg-3'; // 4개 이상
                },

                /**
                 * 파일 업로드 완료 이벤트 핸들러
                 * @param {Object} data - { url: string }
                 */
                handleFileUploaded(data) {
                    console.log('File uploaded:', data.url);
                    if (data.url && !this.uploadedFiles.includes(data.url)) {
                        this.uploadedFiles.push(data.url);
                    }
                },

                /**
                 * 파일 삭제 (file-upload 컴포넌트와 동일한 기능)
                 * @param {number} index - 삭제할 파일 인덱스
                 */
                async removeFile(index) {
                    // 삭제 확인
                    if (!confirm('<?= t()->파일_삭제_확인 ?>')) {
                        return;
                    }

                    const url = this.uploadedFiles[index];

                    try {
                        // API 호출하여 서버에서 파일 삭제
                        const response = await axios.get(appConfig.api.file_delete, {
                            params: {
                                url: url
                            }
                        });

                        console.log('File deleted successfully:', response.data);
                    } catch (error) {
                        const msg = get_axios_error_message(error);
                        // file-not-found 에러는 무시 (이미 삭제된 파일)
                        if (msg.indexOf('file-not-found') === -1) {
                            console.error('Delete error:', error);
                            alert('<?= t()->파일_삭제_오류 ?>: ' + msg);
                        }
                    }

                    // 파일 목록에서 제거
                    this.uploadedFiles.splice(index, 1);
                },

                /**
                 * 폼 확장
                 */
                expand() {
                    if (!login()) {
                        if (confirm('<?= t()->글쓰기_로그인_확인 ?>')) {
                            // 로그인 페이지로 리다이렉트
                            window.location.href = "<?= href()->user->login ?>";
                        } else {
                            alert(tr({
                                'ko': '로그인을 하지 않아 글 쓰기를 취소합니다.',
                                'en': 'You are not logged in, cancelling post creation.',
                                'ja': 'ログインしていないため、投稿の作成をキャンセルします。',
                                'zh': '您未登录，取消发帖。'
                            }));
                        }
                        return;
                    }
                    this.expanded = true;
                    this.$nextTick(() => {
                        // textarea에 포커스
                        if (this.$refs.textarea) {
                            this.$refs.textarea.focus();
                        }
                    });
                },

                /**
                 * 취소 버튼 - 폼 초기화 및 축소
                 */
                cancel() {
                    this.content = '';
                    this.expanded = false;
                    this.uploadedFiles = [];
                    this.visibility = 'public';
                },
                /**
                 * 게시물 작성 제출
                 */
                async submit_post() {
                    // 내용이 비어있으면 경고
                    if (!this.canSubmit) {
                        alert('<?= t()->게시물_내용을_입력해주세요 ?>');
                        return;
                    }

                    try {
                        // 업로드된 파일들의 URL을 콤마로 구분된 문자열로 변환
                        const filesString = this.uploadedFiles.length > 0 ? this.uploadedFiles.join(',') : '';

                        // API 호출하여 게시물 작성
                        const post = await func('create_post', {
                            category: this.visibility === 'public' ? this.category : '<?= $category ?>',
                            visibility: this.visibility,
                            content: this.content,
                            files: filesString,
                            alertOnError: true,
                        });

                        console.log('게시물 작성 응답:', post);

                        if (post && post.id) {
                            // 작성 성공 시, 게시물 목록에 추가
                            window.Store.state.postList.posts.unshift(post);
                            // isEmpty 플래그를 false로 업데이트 (첫 번째 게시글 작성 시에도 즉시 목록에 표시)
                            window.Store.state.postList.isEmpty = false;
                            this.cancel();
                        }
                    } catch (error) {
                        console.error('게시물 작성 실패:', error);
                        alert('<?= t()->게시물_작성_중_오류가_발생했습니다 ?>');
                    }
                },
            },
            watch: {
                /**
                 * content 변경 시 자동 높이 조정
                 */
                content() {
                    this.$nextTick(() => {
                        this.autoResize();
                    });
                }
            },
            mounted() {
                console.log('게시물 작성 위젯 마운트 완료');
            }
        });

        const vm = app.mount('#post-list-create');

        // Vue 인스턴스를 전역 변수로 노출 (파일 업로드 콜백에서 접근하기 위함)
        window.postListCreateVm = vm;
    });
</script>

<?php
/**
 * 게시물 작성 폼 번역 텍스트 주입
 *
 * @return void
 */
function inject_post_list_create_language()
{
    t()->inject([
        '당신의_이야기를_들려주세요' => [
            'ko' => '무슨 생각을 하고 계신가요?',
            'en' => "What's on your mind?",
            'ja' => '今何をしていますか?',
            'zh' => '你在想什么?'
        ],
        '게시' => [
            'ko' => '게시',
            'en' => 'Post',
            'ja' => '投稿',
            'zh' => '发布'
        ],
        '게시물_내용을_입력해주세요' => [
            'ko' => '게시물 내용을 입력해주세요.',
            'en' => 'Please enter the post content.',
            'ja' => '投稿内容を入力してください。',
            'zh' => '请输入帖子内容。'
        ],
        '게시물_작성_중_오류가_발생했습니다' => [
            'ko' => '게시물 작성 중 오류가 발생했습니다.',
            'en' => 'An error occurred while creating the post.',
            'ja' => '投稿の作成中にエラーが発生しました。',
            'zh' => '创建帖子时发生错误。'
        ],
        '공개' => [
            'ko' => '공개',
            'en' => 'Public',
            'ja' => '公開',
            'zh' => '公开'
        ],
        '친구만' => [
            'ko' => '친구만',
            'en' => 'Friends',
            'ja' => '友達のみ',
            'zh' => '仅好友'
        ],
        '나만_보기' => [
            'ko' => '나만 보기',
            'en' => 'Only Me',
            'ja' => '自分のみ',
            'zh' => '仅自己'
        ],
        '사진_동영상' => [
            'ko' => '사진/동영상',
            'en' => 'Photo/Video',
            'ja' => '写真/動画',
            'zh' => '照片/视频'
        ],
        '이모지' => [
            'ko' => '이모지',
            'en' => 'Emoji',
            'ja' => '絵文字',
            'zh' => '表情'
        ],
        '위치' => [
            'ko' => '위치',
            'en' => 'Location',
            'ja' => '場所',
            'zh' => '位置'
        ],
        '태그' => [
            'ko' => '태그',
            'en' => 'Tag',
            'ja' => 'タグ',
            'zh' => '标签'
        ],
        '취소' => [
            'ko' => '취소',
            'en' => 'Cancel',
            'ja' => 'キャンセル',
            'zh' => '取消'
        ],
        '파일_삭제_확인' => [
            'ko' => '이 파일을 삭제하시겠습니까?',
            'en' => 'Are you sure you want to delete this file?',
            'ja' => 'このファイルを削除してもよろしいですか?',
            'zh' => '您确定要删除此文件吗?'
        ],
        '파일_삭제_오류' => [
            'ko' => '파일 삭제 중 오류가 발생했습니다',
            'en' => 'An error occurred while deleting the file',
            'ja' => 'ファイルの削除中にエラーが発生しました',
            'zh' => '删除文件时发生错误'
        ],
    ]);
}
?>