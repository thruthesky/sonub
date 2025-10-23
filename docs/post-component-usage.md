# Post Component 사용 가이드

## 개요

`post-component`는 재사용 가능한 Vue.js 컴포넌트로, 게시물을 표시하고 삭제할 수 있습니다.

## 이벤트 기반 아키텍처

`post-component`는 부모 컴포넌트와 **이벤트**를 통해 통신합니다:

- **자식 컴포넌트** (`post-component`): 게시물 삭제 시 `post-deleted` 이벤트를 발생(emit)시킴
- **부모 컴포넌트** (페이지의 Vue 앱): `@post-deleted` 이벤트를 수신하여 자신의 데이터를 업데이트

## 사용 방법

### 1. 컴포넌트 로드 (PHP)

```php
<?php
// 페이지 상단에 컴포넌트 로드
load_deferred_js('vue-components/post.component');
?>
```

### 2. 템플릿에서 사용 (Vue Template)

```html
<!-- 게시물 목록 표시 -->
<article v-for="post in posts" :key="post.post_id" class="post-card">
    <post-component
        :post="post"
        @post-deleted="handlePostDeleted"
    ></post-component>
</article>
```

**중요 속성:**
- `:post="post"` - 게시물 데이터를 전달 (prop)
- `@post-deleted="handlePostDeleted"` - 삭제 이벤트를 수신하는 핸들러 등록

### 3. 이벤트 핸들러 구현 (Vue Methods)

```javascript
methods: {
    /**
     * 게시물 삭제 이벤트 핸들러
     * @param {number} postId - 삭제된 게시물 ID
     */
    handlePostDeleted(postId) {
        console.log('Post deleted:', postId);

        // 배열에서 삭제된 게시물 제거
        const index = this.posts.findIndex(p => p.post_id === postId);
        if (index !== -1) {
            this.posts.splice(index, 1);
        }
    }
}
```

## 완전한 예제

### 예제 1: 사용자 프로필 페이지

```php
<?php
// page/user/profile.php

// 컴포넌트 로드
load_deferred_js('vue-components/post.component');

// 사용자 게시물 조회
$user_id = $_GET['user_id'] ?? 0;
$posts = list_posts(['user_id' => $user_id, 'limit' => 20]);
?>

<div id="user-profile">
    <!-- Vue 앱이 여기에 마운트됨 -->
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'post-component': postComponent
        },
        template: `
            <div>
                <h2>사용자 게시물</h2>

                <!-- 게시물이 없을 때 -->
                <div v-if="posts.length === 0" class="text-center py-5">
                    <p class="text-muted">게시물이 없습니다.</p>
                </div>

                <!-- 게시물 목록 -->
                <article v-for="post in posts" :key="post.post_id" class="post-card">
                    <post-component
                        :post="post"
                        @post-deleted="handlePostDeleted"
                    ></post-component>
                </article>
            </div>
        `,
        data() {
            return {
                posts: <?= json_encode($posts->posts, JSON_UNESCAPED_UNICODE) ?>
            };
        },
        methods: {
            handlePostDeleted(postId) {
                const index = this.posts.findIndex(p => p.post_id === postId);
                if (index !== -1) {
                    this.posts.splice(index, 1);
                }
            }
        }
    }).mount('#user-profile');
});
</script>
```

### 예제 2: 카테고리별 게시물 페이지

```php
<?php
// page/post/category.php

load_deferred_js('vue-components/post.component');

$category = $_GET['category'] ?? 'discussion';
$posts = list_posts(['category' => $category, 'limit' => 20]);
?>

<div id="category-page">
    <!-- Vue 앱 -->
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'post-component': postComponent
        },
        template: `
            <div>
                <h2>{{ category }} 게시물</h2>

                <article v-for="post in posts" :key="post.post_id" class="post-card">
                    <post-component
                        :post="post"
                        @post-deleted="removePost"
                    ></post-component>
                </article>
            </div>
        `,
        data() {
            return {
                category: '<?= $category ?>',
                posts: <?= json_encode($posts->posts, JSON_UNESCAPED_UNICODE) ?>
            };
        },
        methods: {
            removePost(postId) {
                // 배열에서 게시물 제거
                this.posts = this.posts.filter(p => p.post_id !== postId);
            }
        }
    }).mount('#category-page');
});
</script>
```

### 예제 3: 단일 게시물 페이지 (이벤트 핸들러 선택사항)

단일 게시물 페이지에서는 삭제 후 다른 페이지로 리다이렉트할 수 있으므로 이벤트 핸들러가 선택사항입니다:

```php
<?php
// page/post/view.php

load_deferred_js('vue-components/post.component');

$post_id = $_GET['id'] ?? 0;
$post = get_post_by_id($post_id);
?>

<div id="post-view">
    <!-- Vue 앱 -->
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'post-component': postComponent
        },
        template: `
            <div>
                <post-component
                    v-if="post"
                    :post="post"
                    @post-deleted="handlePostDeleted"
                ></post-component>

                <div v-else class="alert alert-warning">
                    게시물을 찾을 수 없습니다.
                </div>
            </div>
        `,
        data() {
            return {
                post: <?= $post ? json_encode($post->toArray(), JSON_UNESCAPED_UNICODE) : 'null' ?>
            };
        },
        methods: {
            handlePostDeleted(postId) {
                // 단일 게시물 페이지에서는 삭제 후 목록 페이지로 리다이렉트
                alert('게시물이 삭제되었습니다.');
                window.location.href = '/';
            }
        }
    }).mount('#post-view');
});
</script>
```

## 이벤트 핸들러를 등록하지 않으면?

`@post-deleted` 이벤트 핸들러를 등록하지 않아도 게시물 삭제 자체는 정상 작동합니다:

```html
<!-- 이벤트 핸들러 없이 사용 (삭제는 되지만 UI 업데이트는 안 됨) -->
<post-component :post="post"></post-component>
```

**결과:**
- ✅ 게시물이 데이터베이스에서 삭제됨
- ✅ 사용자에게 성공 알림 표시
- ❌ 페이지의 게시물 목록이 자동으로 업데이트되지 않음 (새로고침 필요)

**권장사항:**
- 게시물 목록 페이지: `@post-deleted` 핸들러 **필수** (UI 업데이트)
- 단일 게시물 페이지: `@post-deleted` 핸들러 **권장** (리다이렉트)

## Vue 이벤트 시스템 원리

```
┌─────────────────────────────────────────────────────────────┐
│ 부모 컴포넌트 (페이지의 Vue 앱)                                  │
│                                                             │
│  methods: {                                                 │
│    handlePostDeleted(postId) {                              │
│      // 배열에서 게시물 제거                                    │
│      this.posts = this.posts.filter(p => p.post_id !== postId);│
│    }                                                        │
│  }                                                          │
│                                                             │
│  ┌───────────────────────────────────────┐                 │
│  │ <post-component                       │                 │
│  │   :post="post"                        │                 │
│  │   @post-deleted="handlePostDeleted"   │  ← 이벤트 수신   │
│  │ />                                    │                 │
│  └────────────────┬──────────────────────┘                 │
└───────────────────┼────────────────────────────────────────┘
                    │
                    │ $emit('post-deleted', postId)
                    │ ↑ 이벤트 발생
                    │
┌───────────────────┴────────────────────────────────────────┐
│ 자식 컴포넌트 (post-component)                                │
│                                                             │
│  methods: {                                                 │
│    async handleDeletePost() {                               │
│      await func('delete_post', { id: this.post.post_id }); │
│      this.$emit('post-deleted', this.post.post_id);  ←───  │
│    }                                                        │
│  }                                                          │
└─────────────────────────────────────────────────────────────┘
```

## 핵심 포인트

1. **컴포넌트는 이벤트만 발생시킴** - 자신의 데이터를 직접 수정하지 않음
2. **부모가 데이터를 관리** - 이벤트를 받아 자신의 데이터를 업데이트
3. **재사용 가능** - 어떤 페이지에서든 동일한 방식으로 사용 가능
4. **느슨한 결합** - 컴포넌트는 부모의 데이터 구조를 알 필요가 없음

## 추가 참고사항

### 여러 이벤트 핸들러 사용

게시물 컴포넌트는 다양한 이벤트를 발생시킬 수 있습니다:

```html
<post-component
    :post="post"
    @post-deleted="handlePostDeleted"
    @post-updated="handlePostUpdated"
    @comment-added="handleCommentAdded"
></post-component>
```

### 이벤트 페이로드

현재는 `postId`만 전달하지만, 필요에 따라 더 많은 정보를 전달할 수 있습니다:

```javascript
// 컴포넌트에서
this.$emit('post-deleted', {
    postId: this.post.post_id,
    timestamp: Date.now()
});

// 부모에서
handlePostDeleted(payload) {
    console.log('Post deleted:', payload.postId, 'at', payload.timestamp);
}
```

## 마지막 업데이트

2025-01-23
