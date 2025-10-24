# Post Component 사용 가이드

## 개요

`post-component`는 재사용 가능한 Vue.js 컴포넌트로, 게시물을 표시, 편집, 삭제할 수 있습니다.

**주요 기능:**
- ✅ 게시물 표시 (제목, 내용, 이미지, 작성자 정보)
- ✅ 게시물 편집 (내용, 가시성, 카테고리, 파일)
- ✅ 게시물 삭제
- ✅ 파일 업로드 및 삭제
- ✅ 댓글 기능 (UI 준비됨)
- ✅ 좋아요, 공유 버튼 (UI 준비됨)

## 이벤트 기반 아키텍처

`post-component`는 부모 컴포넌트와 **이벤트**를 통해 통신합니다:

- **자식 컴포넌트** (`post-component`): 게시물 삭제 시 `post-deleted` 이벤트를 발생(emit)시킴
- **부모 컴포넌트** (페이지의 Vue 앱): `@post-deleted` 이벤트를 수신하여 자신의 데이터를 업데이트

---

## 사용 방법

### 1. 컴포넌트 로드 (PHP)

```php
<?php
// 페이지 상단에 컴포넌트 로드
load_deferred_js('vue-components/post.component');

// 파일 업로드 기능을 사용하는 경우 (편집 모드에서 필요)
load_deferred_js('vue-components/file-upload.component');
?>
```

### 2. 카테고리 데이터 주입 (필수)

게시물 편집 시 카테고리 선택 기능을 사용하려면 `window.categoryData`를 주입해야 합니다:

```php
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
```

**중요:**
- `array_values()`를 사용하여 PHP 배열을 JSON 배열로 변환 (객체가 아닌 배열로)
- 루트 카테고리와 서브 카테고리 모두 배열 형태여야 함

### 3. 템플릿에서 사용 (Vue Template)

```html
<!-- 게시물 목록 표시 -->
<article v-for="post in posts" :key="post.post_id" class="post-card">
    <post-component
        :post="post"
        @post-deleted="handlePostDeleted"
    ></post-component>
</article>
```

**필수 속성:**
- `:post="post"` - 게시물 데이터를 전달 (prop)

**선택 이벤트:**
- `@post-deleted="handlePostDeleted"` - 삭제 이벤트를 수신하는 핸들러 등록

### 4. 이벤트 핸들러 구현 (Vue Methods)

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

---

## 게시물 편집 기능

### 편집 모드 진입

게시물 우측 상단의 **점 세 개 (⋯) 메뉴**를 클릭하면:
- **Edit** 버튼: 편집 모드로 전환
- **Delete** 버튼: 게시물 삭제

**편집 모드 진입 조건:**
- 현재 로그인한 사용자가 게시물 작성자인 경우에만 메뉴가 표시됨
- `window.Store.state.user.id === post.author_id` 검증

### 편집 가능한 항목

1. **게시물 내용** (Textarea)
2. **가시성 설정** (Bootstrap Dropdown)
   - Public (공개)
   - Friends (친구만)
   - Only Me (나만 보기)
3. **카테고리** (Public일 때만 표시, Bootstrap Dropdown)
   - 루트 카테고리별 그룹화된 서브 카테고리 목록
4. **파일 관리**
   - 기존 이미지 삭제 (X 버튼)
   - 새 이미지 업로드 (file-upload-component)

### 자동 스크롤

편집 모드로 진입하면 해당 게시물이 화면 상단으로 자동 스크롤됩니다:
- 100px 오프셋으로 상단 네비게이션 가리지 않음
- 부드러운 애니메이션 (`behavior: 'smooth'`)

### 편집 저장 및 취소

**Save 버튼:**
- `update_post` API 호출
- 변경된 내용, 가시성, 카테고리, 파일 저장
- 성공 시 게시물 데이터 업데이트 (`Object.assign(this.post, result)`)

**Cancel 버튼:**
- 편집 모드 종료
- 변경 사항 버림

---

## 완전한 예제

### 예제 1: 게시물 목록 페이지 (편집 기능 포함)

```php
<?php
// page/post/list.php

// 컴포넌트 로드
load_deferred_js('vue-components/post.component');
load_deferred_js('vue-components/file-upload.component');

// 게시물 조회
$category = $_GET['category'] ?? 'story';
$posts = list_posts(['category' => $category, 'limit' => 20]);
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

<div id="post-list">
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
                <h2>{{ category }} 게시물</h2>

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
                category: '<?= $category ?>',
                posts: <?= json_encode($posts->posts, JSON_UNESCAPED_UNICODE) ?>
            };
        },
        methods: {
            handlePostDeleted(postId) {
                console.log('Post deleted:', postId);
                // 배열에서 삭제된 게시물 제거
                const index = this.posts.findIndex(p => p.post_id === postId);
                if (index !== -1) {
                    this.posts.splice(index, 1);
                }
            }
        }
    }).mount('#post-list');
});
</script>
```

### 예제 2: 사용자 프로필 페이지

```php
<?php
// page/user/profile.php

// 컴포넌트 로드
load_deferred_js('vue-components/post.component');
load_deferred_js('vue-components/file-upload.component');

// 사용자 게시물 조회
$user_id = $_GET['user_id'] ?? 0;
$posts = list_posts(['user_id' => $user_id, 'limit' => 20]);
?>

<!-- Categories data injection -->
<script>
    window.categoryData = <?= json_encode([...]) ?>;
</script>

<div id="user-profile">
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
                <h2>사용자 게시물</h2>

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

---

## Props 및 이벤트

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `post` | Object | Yes | 게시물 객체 (post_id, content, author_id, files, visibility, category 등) |

### Events

| Event | Payload | Description |
|-------|---------|-------------|
| `post-deleted` | `postId: number` | 게시물이 삭제되었을 때 발생 |

---

## 컴포넌트 내부 구조

### Data

```javascript
data() {
    return {
        post: this.post,  // Props로 전달받은 게시물
        categories: [],   // 카테고리 데이터 (window.categoryData에서 로드)
        edit: {
            enabled: false,     // 편집 모드 활성화 여부
            content: '',        // 편집 중인 내용
            visibility: 'public', // 가시성 설정
            category: 'story',  // 선택된 카테고리 (기본값: story)
            files: [],          // 편집 중인 파일 목록
        }
    };
}
```

### Computed Properties

```javascript
computed: {
    /**
     * 현재 로그인한 사용자가 이 게시물의 작성자인지 확인
     * @returns {boolean} 본인 게시물이면 true
     */
    isMyPost() {
        if (!window.Store || !window.Store.state || !window.Store.state.user) {
            return false;
        }
        const currentUserId = window.Store.state.user.id;
        const postAuthorId = this.post.author_id;
        return postAuthorId === currentUserId;
    }
}
```

### Methods (주요 메서드)

#### handleEditPost()
- 편집 모드 진입/종료 토글
- 편집 모드 진입 시:
  - 현재 게시물 데이터를 `edit` 객체에 복사
  - 카테고리 유효성 검증 (존재하지 않으면 'story'로 기본값)
  - 파일 배열 복사 (`[...this.post.files]`)
  - 게시물로 자동 스크롤

#### saveEdit()
- `update_post` API 호출
- 파라미터:
  - `id`: 게시물 ID
  - `content`: 수정된 내용
  - `visibility`: 가시성 설정
  - `category`: 카테고리 (public일 때만)
  - `files`: 쉼표로 구분된 파일 URL 문자열

#### handleDeletePost()
- 삭제 확인 다이얼로그 표시
- `delete_post` API 호출
- 성공 시 `post-deleted` 이벤트 발생

#### removeImageFromEdit(index)
- `delete_file_from_post` API 호출
- 로컬 `edit.files` 배열에서 제거
- 결과를 `this.post.files`에 반영

#### handleFileUploaded(data)
- file-upload-component에서 `@uploaded` 이벤트 수신
- 새 파일 URL을 `edit.files`에 추가

#### getCategoryName(categoryId)
- 카테고리 ID로 카테고리 이름 조회
- 드롭다운 버튼에 표시할 텍스트 반환

---

## 카테고리 데이터 구조

### 올바른 형식 (배열)

```javascript
window.categoryData = {
    rootCategories: [
        {
            display_name: "Community",
            categories: [
                { category: "story", name: "My story" },
                { category: "discussion", name: "Discussion" }
            ]
        },
        {
            display_name: "Buy & Sell",
            categories: [
                { category: "marketplace", name: "Marketplace" },
                { category: "garage-sale", name: "Garage sale" }
            ]
        }
    ]
};
```

### 잘못된 형식 (객체)

```javascript
// ❌ 이렇게 하면 안 됨!
window.categoryData = {
    rootCategories: {
        community: { ... },
        buyandsell: { ... }
    }
};
```

**해결 방법:**
- PHP: `array_values(array_map(...))` 사용
- JavaScript: `Object.values()` 또는 배열 검증 로직 추가

---

## 스타일링

컴포넌트는 다음 CSS 클래스를 사용합니다:

- `.post-card` - 게시물 카드 (부모에서 제공)
- `.post-header` - 게시물 헤더
- `.post-body` - 게시물 본문
- `.post-actions` - 좋아요, 댓글, 공유 버튼 영역
- `.post-comments-section` - 댓글 섹션

**편집 모드 스타일:**
- `border border-warning border-1 rounded-3` - 편집 중인 게시물 강조
- Bootstrap 드롭다운 컴포넌트 사용

---

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

---

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

---

## 핵심 포인트

1. **컴포넌트는 이벤트만 발생시킴** - 자신의 데이터를 직접 수정하지 않음
2. **부모가 데이터를 관리** - 이벤트를 받아 자신의 데이터를 업데이트
3. **재사용 가능** - 어떤 페이지에서든 동일한 방식으로 사용 가능
4. **느슨한 결합** - 컴포넌트는 부모의 데이터 구조를 알 필요가 없음
5. **본인 게시물만 편집/삭제 가능** - `isMyPost` computed property로 검증

---

## 문제 해결 (Troubleshooting)

### 1. 편집 메뉴가 보이지 않는 경우

**원인:**
- `window.Store.state.user`가 없음 (로그인하지 않음)
- `post.author_id`와 `user.id`가 일치하지 않음

**해결:**
```javascript
// 브라우저 콘솔에서 확인
console.log('Current user:', window.Store.state.user);
console.log('Post author:', post.author_id);
```

### 2. 카테고리 드롭다운이 비어있는 경우

**원인:**
- `window.categoryData`가 주입되지 않음
- `rootCategories`가 객체 형태 (배열이 아님)

**해결:**
```javascript
// 브라우저 콘솔에서 확인
console.log('categoryData:', window.categoryData);
console.log('Is array?', Array.isArray(window.categoryData?.rootCategories));

// PHP에서 array_values() 사용
array_values(array_map(...))
```

### 3. 파일 업로드가 작동하지 않는 경우

**원인:**
- file-upload-component가 로드되지 않음

**해결:**
```php
// 페이지 상단에 추가
load_deferred_js('vue-components/file-upload.component');
```

### 4. 편집 후 스크롤되지 않는 경우

**원인:**
- `$refs.postContainer`가 존재하지 않음
- `$nextTick`이 호출되지 않음

**해결:**
```javascript
// handleEditPost() 메서드가 edit.enabled === true일 때만 스크롤하는지 확인
if (this.edit.enabled) {
    this.$nextTick(() => {
        // scroll logic
    });
}
```

---

## 추가 참고사항

### API 함수 호출

컴포넌트는 `func()` 함수를 사용하여 API를 호출합니다:

```javascript
// 게시물 삭제
await func('delete_post', {
    id: this.post.post_id,
    auth: true
});

// 게시물 수정
await func('update_post', {
    id: this.post.post_id,
    content: this.edit.content,
    visibility: this.edit.visibility,
    category: this.edit.category,
    files: filesString
});

// 파일 삭제
await func('delete_file_from_post', {
    id: this.post.post_id,
    url: imageUrl,
    auth: true
});
```

### 파일 형식

컴포넌트는 다음 파일 형식을 지원합니다:
- 이미지: `image/*`
- 비디오: `video/*`

파일은 배열로 관리되며, API 호출 시 쉼표로 구분된 문자열로 변환됩니다:

```javascript
const filesString = this.edit.files
    .filter(f => f && f.trim() !== '')
    .join(',');
```

---

## 마지막 업데이트

2025-01-24 - 편집 기능, 카테고리 선택, 파일 관리 기능 추가
