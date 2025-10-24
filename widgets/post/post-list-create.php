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
$user_photo = $user && isset($user->photo_url) ? $user->photo_url : '/images/default-user.png';
// first_name, middle_name, last_name을 조합하여 전체 이름 생성
$name_parts = [];
if ($user) {
    if (!empty($user->first_name)) $name_parts[] = $user->first_name;
    if (!empty($user->middle_name)) $name_parts[] = $user->middle_name;
    if (!empty($user->last_name)) $name_parts[] = $user->last_name;
}
$user_name = !empty($name_parts) ? implode(' ', $name_parts) : 'Guest';

?>

<!-- Inject categories data for JavaScript -->
<script>
    window.categoryData = <?= json_encode([
        'rootCategories' => array_values(array_map(function($root) {
            return [
                'display_name' => $root->display_name,
                'categories' => array_values(array_map(function($sub) {
                    return [
                        'category' => $sub->category,
                        'name' => $sub->name
                    ];
                }, $root->getCategories()))
            ];
        }, config()->categories->getRootCategories()))
    ]) ?>;
</script>

id: <?= login()->id ?>
<style>
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

    .post-content-input {
        width: 100%;
        border: none;
        outline: none;
        font-size: 15px;
        color: #050505;
        line-height: 1.5;
        resize: none;
        min-height: 80px;
        max-height: 200px;
        overflow-y: auto;
        padding: 0;
        background: transparent;
    }

    .post-content-input::placeholder {
        color: #65676b;
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


    /* 하단 액션 영역 */
    .post-create-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e4e6eb;
    }

    #post-list-create .post-action-btn {
        background: none;
        border: none;
        padding: 8px;
        color: #65676b;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 4px;
        transition: background-color 0.15s ease;
    }

    #post-list-create .post-action-btn:hover {
        background-color: #f0f2f5;
    }

    #post-list-create .post-action-btn i {
        font-size: 18px;
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
<section id="post-list-create" class="mb-3" v-cloak>
    <!-- 게시물 작성 카드 (post-card 스타일 적용) -->
    <article class="post-card">
        <div class="post-body">
            <form @submit.prevent="submit_post">
                <!-- 축소된 상태: 프로필 + 클릭 가능한 버튼 -->
                <div v-if="!expanded" class="d-flex align-items-center">
                    <!-- 프로필 사진 -->
                    <div class="post-header-avatar">
                        <img v-if="userPhoto"
                            :src="userPhoto"
                            :alt="userName"
                            style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        <i v-else class="fa-solid fa-user"></i>
                    </div>

                    <!-- 클릭 가능한 버튼 -->
                    <button type="button"
                        @click="expand"
                        class="post-create-trigger">
                        {{ placeholder }}
                    </button>
                </div>

                <!-- 확장된 상태: 전체 폼 -->
                <div v-if="expanded">
                    <!-- 사용자 프로필 헤더 -->
                    <div class="post-header" style="padding: 0 0 12px 0; border: none;">
                        <div class="post-header-avatar">
                            <img v-if="userPhoto"
                                :src="userPhoto"
                                :alt="userName"
                                style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            <i v-else class="fa-solid fa-user"></i>
                        </div>
                        <div class="post-header-info">
                            <div class="post-header-name">{{ userName }}</div>
                            <div class="post-header-meta d-flex gap-2 align-items-center">
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

                    <!-- 게시물 내용 입력 -->
                    <textarea
                        ref="textarea"
                        v-model="content"
                        class="post-content-input"
                        name="content"
                        :placeholder="placeholder"
                        @input="autoResize"></textarea>

                    <!-- 카테고리 선택 (공개일 때만) -->

                    <!-- 파일 업로드 컴포넌트 -->
                    <file-upload-component
                        ref="upload"
                        :multiple="true"
                        :accept="'image/*,video/*'"
                        :show-upload-button="false"
                        :show-delete-icon="true"
                        :input-name="'files'"
                        @change="onFileChange">
                    </file-upload-component>


                    <!-- 하단 액션 영역 -->
                    <div class="post-create-actions">
                        <!-- 사진/비디오 업로드 버튼 -->
                        <label class="post-action-btn">
                            <i class="fa-solid fa-image" style="color: #45bd62;"></i>
                            <span><?= t()->사진_동영상 ?></span>
                            <input type="file"
                                multiple
                                accept="image/*,video/*"
                                style="display: none;"
                                @change="$refs.upload.handleFileChange($event)">
                        </label>

                        <!-- 오른쪽: 취소 및 게시 버튼 -->
                        <div style="display: flex; gap: 8px; margin-left: auto;">
                            <button type="submit"
                                class="btn-post"
                                :disabled="!canSubmit">
                                <?= t()->게시 ?>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </article>
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
                    expanded: true,
                    hasFiles: false,
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
                    return this.content.trim().length > 0 || this.hasFiles;
                }
            },
            methods: {
                /**
                 * 폼 확장
                 */
                expand() {
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
                    this.hasFiles = false;
                    this.visibility = 'public';

                    // 파일 업로드 영역 초기화
                    const filesDiv = document.getElementById('files');
                    if (filesDiv) {
                        filesDiv.innerHTML = '';
                    }
                },

                /**
                 * textarea 자동 높이 조정
                 */
                autoResize() {
                    const textarea = this.$refs.textarea;
                    if (!textarea) return;

                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 200) + 'px';
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
                        // 파일 업로드된 파일들의 URL 가져오기
                        const filesInput = document.querySelector('[name="files"]');
                        const filesValue = filesInput ? filesInput.value : '';

                        // API 호출하여 게시물 작성
                        const post = await func('create_post', {
                            category: this.visibility === 'public' ? this.category : '<?= $category ?>',
                            visibility: this.visibility,
                            content: this.content,
                            files: filesValue,
                            alertOnError: true,
                        });

                        console.log('게시물 작성 응답:', post);

                        if (post && post.id) {
                            // 작성 성공 시, 페이지 새로고침
                            window.location.reload();
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
    ]);
}
?>