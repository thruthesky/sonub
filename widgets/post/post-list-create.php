<?php

/**
 * 게시물 작성 폼
 *
 * @included in page/post/list.php with route: /post/list
 */

load_deferred_js('file-upload');

// 다국어 번역 텍스트 주입
inject_post_list_create_language();

?>
<section id="post-list-create" class="mb-4">

    <form @submit.prevent="submit_post" :class="{ 'expanded': expanded}">
        <nav class="d-flex">
            <label v-show="!expanded" class="flex-shrink-1 pointer">
                <i class="fa-solid fa-camera" style="font-size: 2em;"></i>
                <input type="file" multiple style="display: none;" onchange="handle_file_change(event, { id: 'files', on_uploaded: (data) => { console.log('파일 업로드 완료:', data); if (window.postListCreateVm) { window.postListCreateVm.expanded = true; } } })">
            </label>
            <textarea
                ref="textarea"
                v-model="content"
                class="form-control flex-grow-1"
                name="content"
                placeholder="<?= t()->당신의_이야기를_들려주세요 ?>..."
                :style="{ height: expanded ? '6em' : '1em' }"
                @focus="expand"
                @click="expand"
                @input="expand"
                @touchstart="expand"
                @keydown="expand"></textarea>
        </nav>
        <data v-show="expanded">
            <div class="d-flex justify-content-between align-items-center my-2">
                <label class="flex-shrink-1 pointer">
                    <i class="fa-solid fa-camera" style="font-size: 2em;"></i>
                    <input type="file" multiple style="display: none;" onchange="handle_file_change(event, { id: 'files', on_uploaded: (data) => { console.log('파일 업로드 완료:', data); if (window.postListCreateVm) { window.postListCreateVm.expanded = true; } } })">
                </label>

                <button type="submit" class="btn btn-primary"><?= t()->작성 ?></button>
            </div>
        </data>

        <section>
            <div id="files"></div>
        </section>
    </form>
</section>
<style>
    /* 기본 상태 - 축소된 textarea */
    #post-list-create textarea.form-control {
        height: 1em !important;
        line-height: 1.5em !important;
        resize: vertical !important;
        transition: height 0.3s ease !important;
        overflow: hidden !important;
        padding: 0.375rem 0.75rem !important;
    }

    /* 확장된 상태 - expanded 클래스가 적용되면 */
    #post-list-create .expanded textarea.form-control {
        height: 6em !important;
        min-height: 6em !important;
        overflow-y: auto !important;
    }

    /* 디버깅용: expanded 클래스가 적용되면 배경색 변경 */
    #post-list-create .expanded {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
    }
</style>
<script>
    ready(() => {
        const app = Vue.createApp({
            data() {
                return {
                    // 게시물 내용
                    content: '',
                    expanded: false,
                };
            },
            methods: {
                /**
                 * textarea 확장 함수
                 */
                expand() {
                    this.expanded = true;
                },
                /**
                 * 게시물 작성 폼 제출 처리
                 * @param {Event} event - 폼 제출 이벤트
                 */
                async submit_post(event) {
                    // 폼 기본 동작 방지 (페이지 새로고침 방지)
                    event.preventDefault();

                    // 게시물 내용 가져오기
                    const content = event.target.content.value;

                    // 내용이 비어있으면 경고
                    if (!content.trim()) {
                        alert('<?= t()->게시물_내용을_입력해주세요 ?>');
                        return;
                    }

                    // API 호출하여 게시물 작성
                    const post = await func('create_post', {
                        category: '<?= http_param('category') ?>',
                        content: content,
                        files: document.querySelector('[name="files"]').value,
                        alertOnError: true,
                    });

                    console.log('게시물 작성 응답:', post);

                    if (post && post.id) {
                        // 작성 성공 시, 페이지 새로고침
                        window.location.reload();
                    }
                }
            },
            mounted() {
                console.log('Vue 앱 마운트 완료, expanded 초기값:', this.expanded);
            }
        });

        const vm = app.mount('#post-list-create');
        console.log('Vue 인스턴스 마운트됨:', vm);

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
            'ko' => '당신의 이야기를 들려주세요',
            'en' => 'Share your story',
            'ja' => 'あなたのストーリーを共有してください',
            'zh' => '分享您的故事'
        ],
        '작성' => [
            'ko' => '작성',
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
    ]);
}
?>