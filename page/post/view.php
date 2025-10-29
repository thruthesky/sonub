<?php

$post_id = (int)http_param('id');

if (!$post_id) {
    error('post-id-required', tr([
        'ko' => '게시물 ID가 필요합니다.',
        'en' => 'Post ID is required.',
        'ja' => '投稿IDが必要です。',
        'zh' => '需要帖子ID。'
    ]));
}

$post = get_post(["post_id" => $post_id, "with_user" => true, "with_comment" => true]);

if (!$post) {
    error('post-not-found', tr([
        'ko' => '게시물을 찾을 수 없습니다.',
        'en' => 'Post not found.',
        'ja' => '投稿が見つかりません。',
        'zh' => '找不到帖子。'
    ]));
}

// Vue 컴포넌트 및 CSS 로드
load_deferred_js('vue-components/post.component');
load_deferred_js('vue-components/file-upload.component');

// 다국어 번역 주입
inject_post_view_language();

?>

<!-- Bootstrap 레이아웃 유틸리티 클래스 사용 -->
<div id="post-view-container" class="container py-3" style="max-width: 680px;" v-cloak>
    <!-- 뒤로 가기 버튼 -->
    <div class="mb-3">
        <a href="javascript:history.back()"
           class="btn btn-light d-inline-flex align-items-center gap-2 fw-semibold"
           style="transition: background-color 0.2s ease;"
           onmouseover="this.style.backgroundColor='#e4e6eb'"
           onmouseout="this.style.backgroundColor='#f0f2f5'">
            <i class="fa-solid fa-arrow-left"></i>
            <span><?= t()->뒤로_가기 ?></span>
        </a>
    </div>

    <!-- 게시물 컴포넌트 -->
    <post-component
        :post="post"
        @post-deleted="handlePostDeleted">
    </post-component>
</div>

<script>
    ready(() => {
        const app = Vue.createApp({
            components: {
                'post-component': postComponent,
            },
            data() {
                return {
                    post: <?= json_encode($post->toArray()) ?>,
                };
            },
            methods: {
                /**
                 * 게시물 삭제 후 처리
                 * @param {number} postId - 삭제된 게시물 ID
                 */
                handlePostDeleted(postId) {
                    console.log('Post deleted:', postId);

                    // 삭제 성공 메시지 표시 후 목록으로 이동
                    alert('<?= t()->게시물이_삭제되었습니다 ?>');

                    // 이전 페이지로 이동 (또는 게시물 목록으로)
                    window.history.back();
                },
            },
            mounted() {
                console.log('Post view page mounted');
                console.log('Post:', this.post);

                // 댓글 섹션 자동으로 열기
                this.$nextTick(() => {
                    // post-component의 댓글 섹션을 자동으로 표시
                    // (댓글이 있는 경우에만)
                    if (this.post.comment_count > 0) {
                        // 댓글을 로드하기 위해 showMoreComments 호출은
                        // post-component 내부에서 처리됨
                    }
                });
            }
        });

        app.mount('#post-view-container');
    });
</script>

<?php
/**
 * 게시물 상세 보기 페이지 번역 텍스트 주입
 *
 * @return void
 */
function inject_post_view_language()
{
    t()->inject([
        '뒤로_가기' => [
            'ko' => '뒤로 가기',
            'en' => 'Back',
            'ja' => '戻る',
            'zh' => '返回'
        ],
        '게시물이_삭제되었습니다' => [
            'ko' => '게시물이 삭제되었습니다.',
            'en' => 'Post has been deleted.',
            'ja' => '投稿が削除されました。',
            'zh' => '帖子已删除。'
        ],
    ]);
}
?>