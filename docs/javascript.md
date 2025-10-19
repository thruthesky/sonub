# JavaScript

## 목차

- [JavaScript](#javascript)
  - [목차](#목차)
  - [개요](#개요)
  - [window.AppStore.state - 전역 상태 관리](#windowappstorestate---전역-상태-관리)
    - [로그인 사용자 정보](#로그인-사용자-정보)
      - [window.AppStore.state 예제](#windowappstorestate-예제)
    - [Vue.js Reactivity 사용](#vuejs-reactivity-사용)
      - [올바른 패턴](#올바른-패턴)
    - [사용 예제 모음](#사용-예제-모음)
      - [예제 1: Optional Chaining 사용 (권장)](#예제-1-optional-chaining-사용-권장)
      - [예제 2: Computed Property 사용](#예제-2-computed-property-사용)
      - [예제 3: Template에서 직접 조건부 렌더링](#예제-3-template에서-직접-조건부-렌더링)
    - [실제 예제 - 프로필 페이지](#실제-예제---프로필-페이지)
    - [안티패턴](#안티패턴)
    - [Reactivity의 장점](#reactivity의-장점)
    - [요약](#요약)
  - [다국어 번역](#다국어-번역)
    - [PHP 함수로 번역 텍스트 주입 (권장)](#php-함수로-번역-텍스트-주입-권장)
    - [window.t 객체 (레거시)](#windowt-객체-레거시)
  - [페이지 URL 라우팅](#페이지-url-라우팅)
    - [PHP href() 함수로 URL 생성 (권장)](#php-href-함수로-url-생성-권장)
    - [window.hrefs 객체 (레거시)](#windowhrefs-객체-레거시)
  - [ready() 함수](#ready-함수)
    - [올바른 사용 예제](#올바른-사용-예제)
    - [로딩 순서](#로딩-순서)

---

## 개요

Sonub의 JavaScript는 **PHP MPA (Multi-Page Application)** 방식으로 동작하며, 다음과 같은 방식으로 JavaScript 코드를 작성합니다:

### JavaScript 파일 분리 방식

**🔥🔥🔥 최강력 규칙: 페이지 파일 내부에 JavaScript가 길어지면 *.javascript.php 파일로 분리해야 합니다 🔥🔥🔥**

- **페이지 내 인라인**: 짧은 JavaScript는 `page/**/*.php` 내부에 `<script>` 태그로 작성
- **별도 파일 분리**: 긴 JavaScript는 `page/**/*.javascript.php` 파일로 분리
- **확장자 .php 사용**: `.javascript.php` 확장자를 사용하여 PHP 함수를 직접 사용 가능

### *.javascript.php 파일의 장점

**✅ PHP 함수 직접 사용:**
- `<?= tr('텍스트') ?>`: 인라인 번역 함수 사용 가능
- `<?= href()->user->profile ?>`: 페이지 URL 라우팅 직접 사용
- `<?= t()->검색 ?>`: 다국어 번역 텍스트 주입
- `<?= login()->id ?>`: 로그인 사용자 정보 접근

**✅ 목적:**
- JavaScript를 별도 PHP 파일로 분리
- PHP 함수를 통해 JavaScript에 필요한 텍스트, URL, 기타 정보 주입
- 긴 JavaScript 코드를 페이지 파일에서 분리하여 가독성 향상

### 전역 객체 (레거시)

다음 전역 객체들은 **레거시**이며, 새로운 코드에서는 **PHP 함수를 직접 사용**하는 것을 권장합니다:

- **window.AppStore.state**: Vue.js Reactivity Proxy로 구현된 전역 상태 관리 (계속 사용)
- ~~**window.t**: 다국어 번역 객체~~ → `<?= tr('텍스트') ?>` 또는 `<?= t()->키 ?>` 사용 권장
- ~~**window.hrefs**: 페이지 URL 라우팅 객체~~ → `<?= href()->페이지->경로 ?>` 사용 권장

### 실제 예제 - page/user/list.php

**page/user/list.php** 파일은 JavaScript를 페이지 파일 내부에 `<script>` 태그로 포함하는 예제입니다:

```php
<!-- page/user/list.php -->
<div id="user-list-app">
    <!-- 사용자 목록 HTML -->
    <div v-for="user in users" :key="user.id">
        <!-- ✅ PHP 함수로 URL 직접 주입 -->
        <a :href="`<?= href()->user->profile ?>?id=${user.id}`">
            {{ user.display_name }}
        </a>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                users: <?= json_encode($users) ?>,
                myUserId: <?= login() ? login()->id : 'null' ?>
            };
        },
        methods: {
            async loadUsers() {
                // ✅ PHP 함수로 alert 메시지 직접 주입
                if (!this.myUserId) {
                    alert('<?= tr('로그인이 필요합니다') ?>');
                    // ✅ PHP 함수로 URL 직접 주입
                    window.location.href = '<?= href()->user->login ?>';
                    return;
                }

                // API 호출
                const result = await func('list_users', {
                    page: 1,
                    per_page: 20
                });
            }
        }
    }).mount('#user-list-app');
});
</script>
```

**장점:**
- ✅ `<?= href()->user->profile ?>`: URL을 PHP에서 직접 생성하여 주입
- ✅ `<?= tr('로그인이 필요합니다') ?>`: 번역 텍스트를 PHP에서 직접 주입
- ✅ `<?= login()->id ?>`: 로그인 사용자 정보를 PHP에서 직접 접근
- ✅ `<?= json_encode($users) ?>`: 서버 데이터를 JavaScript로 Hydration

### *.javascript.php 파일로 분리하는 경우

JavaScript 코드가 길어지면 `*.javascript.php` 파일로 분리할 수 있습니다:

**page/user/list.php:**
```php
<div id="user-list-app">
    <!-- 사용자 목록 HTML -->
</div>

<?php include __DIR__ . '/list.javascript.php'; ?>
```

**page/user/list.javascript.php:**
```php
<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                users: <?= json_encode($users) ?>,
                myUserId: <?= login() ? login()->id : 'null' ?>
            };
        },
        methods: {
            async requestFriend(user) {
                // ✅ PHP 함수로 번역 텍스트 주입
                alert('<?= tr('친구 요청을 보냈습니다') ?>');

                // API 호출
                await func('request_friend', {
                    me: this.myUserId,
                    other: user.id,
                    auth: true
                });
            }
        }
    }).mount('#user-list-app');
});
</script>

<?php
// 다국어 번역 주입 함수
function inject_list_language() {
    t()->inject([
        '사용자_목록' => [
            'ko' => '사용자 목록',
            'en' => 'User List',
            'ja' => 'ユーザーリスト',
            'zh' => '用户列表'
        ]
    ]);
}
inject_list_language();
?>
```

**핵심 포인트:**
- ✅ `.javascript.php` 확장자 사용 → PHP로 실행됨
- ✅ `<?= tr(...) ?>`, `<?= href()->... ?>`, `<?= login()->id ?>` 직접 사용 가능
- ✅ 긴 JavaScript 코드를 별도 파일로 분리하여 가독성 향상
- ✅ 페이지별 번역은 `t()->inject()` 함수 사용

---

## window.AppStore.state - 전역 상태 관리

**🔥🔥🔥 최강력 규칙: 사용자 정보를 사용할 때는 window.AppStore.state.user에서 가져와야 합니다 🔥🔥🔥**

### 로그인 사용자 정보

사용자가 로그인하면, **window.AppStore.state.user**에 **Vue.js의 Reactivity Proxy**로 사용자 정보가 저장됩니다.

#### window.AppStore.state 예제

```javascript
window.AppStore = {
    state: {
        user: {
            id: 1,                    // 사용자 ID
            firebase_uid: 'abc123',   // Firebase UID
            display_name: '홍길동',   // 표시 이름
            gender: 'M',              // 성별
            birthday: 631152000,      // 생년월일 (Unix timestamp)
            photo_url: '/uploads/...' // 프로필 사진 URL
            // ... 기타 사용자 정보
        }
        // ... 기타 전역 상태
    }
};
```

### Vue.js Reactivity 사용

**중요 주의**: `window.AppStore.state`는 **Vue.js Reactivity Proxy**이므로, Vue 컴포넌트의 `data()`에서 참조해야 반응형으로 동작합니다.

#### 올바른 패턴

**✅ Vue 컴포넌트에서 상태 사용:**

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                // ✅ window.AppStore.state를 data에 추가 - 반응형으로 동작!
                state: window.AppStore.state
            };
        },
        methods: {
            async requestFriend(otherUserId) {
                // ✅ this.state.user로 접근 - 반응형!
                if (!this.state?.user?.id) {
                    alert('<?= tr('로그인이 필요합니다') ?>');
                    window.location.href = '<?= href()->user->login ?>';
                    return;
                }

                const myUserId = this.state.user.id;

                // 자기 자신에게 친구 요청 방지
                if (otherUserId === myUserId) {
                    alert('<?= tr('자기 자신에게는 친구 요청을 보낼 수 없습니다') ?>');
                    return;
                }

                try {
                    await func('request_friend', {
                        me: myUserId,
                        other: otherUserId,
                        auth: true
                    });

                    alert('<?= tr('친구 요청을 보냈습니다') ?>');

                } catch (error) {
                    console.error('친구 요청 실패:', error);
                    alert(`<?= tr('친구 요청 실패') ?>: ${error.message}`);
                }
            }
        },
        mounted() {
            console.log('[profile] 사용자 정보:', this.state.user);
        }
    }).mount('#profile-app');
});
```

### 사용 예제 모음

#### 예제 1: Optional Chaining 사용 (권장)

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.AppStore.state
            };
        },
        methods: {
            doSomething() {
                // ✅ Optional Chaining으로 안전하게 사용
                if (!this.state?.user?.id) {
                    alert('<?= tr('로그인이 필요합니다') ?>');
                    window.location.href = '<?= href()->user->login ?>';
                    return;
                }

                // 사용자 사용 가능 - 사용자 정보 접근
                const myUserId = this.state.user.id;
                const myName = this.state.user.display_name;
                console.log(`사용자 정보: ${myName} (ID: ${myUserId})`);
            }
        }
    }).mount('#app');
});
```

#### 예제 2: Computed Property 사용

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.AppStore.state
            };
        },
        computed: {
            // ✅ 사용자 여부를 computed로 정의
            isLoggedIn() {
                return !!(this.state?.user?.id);
            },
            currentUser() {
                return this.state?.user || null;
            }
        },
        methods: {
            doSomething() {
                if (!this.isLoggedIn) {
                    alert('<?= tr('로그인이 필요합니다') ?>');
                    window.location.href = '<?= href()->user->login ?>';
                    return;
                }

                const user = this.currentUser;
                console.log(`사용자 정보: ${user.display_name}`);
            }
        }
    }).mount('#app');
});
```

#### 예제 3: Template에서 직접 조건부 렌더링

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.AppStore.state
            };
        },
        computed: {
            isLoggedIn() {
                return !!(this.state?.user?.id);
            }
        }
    }).mount('#app');
});
```

```html
<div id="app">
    <!-- ✅ 사용자 상태 기반 조건부 UI 표시 -->
    <div v-if="isLoggedIn">
        <p>환영합니다, {{ state.user.display_name }}님!</p>
        <button @click="doSomething">친구 추가</button>
    </div>
    <div v-else>
        <p>로그인이 필요합니다.</p>
        <a href="<?= href()->user->login ?>">로그인</a>
    </div>
</div>
```

### 실제 예제 - 프로필 페이지

**page/user/profile.php:**

```php
<?php
$user_id = http_param('id') ?? login()->id ?? 0;
$user_data = get_user(['id' => $user_id]);
$user = new UserModel($user_data);
$is_me = login() && login()->id === $user->id;
?>

<div id="profile-app">
    <h1><?= htmlspecialchars($user->display_name) ?></h1>

    <?php if (!$is_me): ?>
        <button @click="requestFriend(<?= $user->id ?>)"
                class="btn-add-friend"
                :disabled="requesting || isFriend">
            <span v-if="requesting">요청 중...</span>
            <span v-else-if="isFriend">친구 요청을 보냈습니다</span>
            <span v-else>친구 추가</span>
        </button>
    <?php endif; ?>
</div>
```

**page/user/profile.js:**

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                // ✅ 전역 상태 추가 - 반응형!
                state: window.AppStore.state,
                requesting: false,
                isFriend: false
            };
        },
        computed: {
            isLoggedIn() {
                return !!(this.state?.user?.id);
            },
            currentUserId() {
                return this.state?.user?.id || null;
            }
        },
        methods: {
            async requestFriend(otherUserId) {
                // ✅ 로그인 사용
                if (!this.state?.user?.id) {
                    alert('<?= tr('로그인이 필요합니다') ?>');
                    const currentUrl = encodeURIComponent(window.location.href);
                    window.location.href = `<?= href()->user->login ?>?return=${currentUrl}`;
                    return;
                }

                const myUserId = this.state.user.id;

                // 자기 자신에게 친구 요청 방지
                if (otherUserId === myUserId) {
                    alert('<?= tr('자기 자신에게는 친구 요청을 보낼 수 없습니다') ?>');
                    return;
                }

                if (this.isFriend) {
                    alert('<?= t()->이미_친구입니다 ?>');
                    return;
                }

                try {
                    this.requesting = true;

                    await func('request_friend', {
                        me: myUserId,
                        other: otherUserId,
                        auth: true
                    });

                    this.isFriend = true;
                    alert('<?= t()->친구_요청_전송_완료 ?>');

                } catch (error) {
                    console.error('친구 요청 실패:', error);
                    this.requesting = false;
                    alert(`<?= tr('친구 요청 실패') ?>: ${error.message}`);
                }
            }
        },
        mounted() {
            console.log('[profile] Vue.js 프로필 페이지 초기화됨');
            console.log('[profile] 사용자 정보:', this.state.user);
        }
    }).mount('#profile-app');
});
```

### 안티패턴

**❌ window.AppStore.user 직접 접근 (반응형 X):**

```javascript
// ❌ 잘못된 예제 - window.AppStore.user는 존재하지 않습니다!
if (!window.AppStore.user?.id) {  // ❌ undefined!
    alert('로그인이 필요합니다.');
}
```

**❌ state를 data에 추가하지 않음 (반응형 X):**

```javascript
// ❌ 잘못된 예제 - 반응형으로 동작하지 않습니다!
ready(() => {
    Vue.createApp({
        data() {
            return {
                // state를 추가하지 않음
            };
        },
        methods: {
            doSomething() {
                // ❌ window.AppStore.state.user를 직접 참조 - 반응형 X
                if (!window.AppStore.state?.user?.id) {
                    alert('로그인이 필요합니다.');
                    return;
                }

                // 동작은 하지만 UI 업데이트가 안됨 (사용자 정보 변경 시 UI 미반영)
                const myUserId = window.AppStore.state.user.id;
            }
        }
    }).mount('#app');
});
```

**✅ 올바른 패턴 - state를 data에 추가:**

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                // ✅ state를 data에 추가 - 반응형!
                state: window.AppStore.state
            };
        },
        methods: {
            doSomething() {
                // ✅ this.state.user로 접근 - 반응형!
                if (!this.state?.user?.id) {
                    alert('로그인이 필요합니다.');
                    return;
                }

                // 사용자 정보 변경 시 자동으로 UI 업데이트됨
                const myUserId = this.state.user.id;
            }
        }
    }).mount('#app');
});
```

### Reactivity의 장점

**Vue.js Reactivity Proxy를 사용하면:**

1. **자동 UI 업데이트**: 사용자 정보가 변경되면 자동으로 UI가 업데이트됨
2. **코드 간결성**: 수동으로 UI를 업데이트할 필요 없음
3. **일관성**: 모든 컴포넌트가 동일한 사용자 정보를 공유

**예제: 사용자 정보 변경 시 자동 업데이트**

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.AppStore.state
            };
        },
        methods: {
            async updateProfile() {
                try {
                    await func('update_user', {
                        display_name: '새로운 이름',
                        auth: true
                    });

                    // ✅ state.user가 변경되면 자동으로 업데이트됨!
                    // 템플릿의 {{ state.user.display_name }}도 자동 업데이트!

                } catch (error) {
                    console.error('프로필 업데이트 실패:', error);
                }
            }
        }
    }).mount('#app');
});
```

```html
<div id="app">
    <!-- ✅ state.user가 변경되면 자동으로 업데이트됨! -->
    <p>환영합니다, {{ state.user.display_name }}님!</p>
    <button @click="updateProfile">프로필 업데이트</button>
</div>
```

### 요약

- **✅ 필수**: `data()`에서 `state: window.AppStore.state` 추가
- **✅ 필수**: `this.state.user`로 사용자 정보 접근
- **✅ 권장**: Optional Chaining (`?.`) 사용으로 안전하게 사용
- **✅ 권장**: Computed Property로 `isLoggedIn` 정의
- **❌ 금지**: `window.AppStore.user` 직접 접근 (존재하지 않습니다)
- **❌ 금지**: `window.AppStore.state.user`를 data에 추가하지 않고 직접 접근 (반응형 X)
- **장점**: Vue.js Reactivity로 자동 UI 업데이트, 코드 간결성, 일관성 보장

---

## 다국어 번역

**🔥🔥🔥 중요: window.t는 레거시입니다. PHP 함수를 직접 사용하세요 🔥🔥🔥**

### PHP 함수로 번역 텍스트 주입 (권장)

**✅ 권장 방법 1: tr() 인라인 번역**

```php
<script>
ready(() => {
    Vue.createApp({
        methods: {
            showAlert() {
                // ✅ PHP tr() 함수로 직접 주입
                alert('<?= tr('로그인이 필요합니다') ?>');
            }
        }
    }).mount('#app');
});
</script>
```

**✅ 권장 방법 2: t()->키 사용**

```php
<script>
ready(() => {
    Vue.createApp({
        methods: {
            showAlert() {
                // ✅ PHP t()->키로 직접 주입
                alert('<?= t()->로그인이_필요합니다 ?>');
            }
        }
    }).mount('#app');
});
</script>
```

**장점:**
- ✅ PHP 실행 시점에 번역 텍스트 주입
- ✅ window.t 객체 불필요 (JavaScript 번들 크기 감소)
- ✅ 서버 사이드 번역으로 SEO 개선

### window.t 객체 (레거시)

**❌ 레거시 방법 - 사용하지 마세요:**

```javascript
// ❌ 레거시 - 새 코드에서는 사용하지 마세요
alert(window.t.로그인이_필요합니다);
```

**문제점:**
- ❌ JavaScript 번들 크기 증가 (모든 번역 텍스트를 클라이언트로 전송)
- ❌ ready() 함수 내부에서만 사용 가능
- ❌ 서버 사이드 렌더링 불가

자세한 내용은 [docs/coding-guideline.md - 다국어 번역](./coding-guideline.md#다국어-번역)을 참조하세요.

---

## 페이지 URL 라우팅

**🔥🔥🔥 중요: window.hrefs는 레거시입니다. PHP href() 함수를 직접 사용하세요 🔥🔥🔥**

### PHP href() 함수로 URL 생성 (권장)

**✅ 권장 방법:**

```php
<script>
ready(() => {
    Vue.createApp({
        methods: {
            goToLogin() {
                // ✅ PHP href() 함수로 직접 주입
                window.location.href = '<?= href()->user->login ?>';
            },
            goToProfile(userId) {
                // ✅ PHP href() 함수로 URL 생성
                window.location.href = `<?= href()->user->profile ?>?id=${userId}`;
            }
        }
    }).mount('#app');
});
</script>
```

**장점:**
- ✅ PHP 실행 시점에 URL 생성
- ✅ window.hrefs 객체 불필요 (JavaScript 번들 크기 감소)
- ✅ 서버 사이드 URL 생성으로 안정성 향상

### window.hrefs 객체 (레거시)

**❌ 레거시 방법 - 사용하지 마세요:**

```javascript
// ❌ 레거시 - 새 코드에서는 사용하지 마세요
window.location.href = window.hrefs.login;
```

**문제점:**
- ❌ JavaScript 번들 크기 증가
- ❌ ready() 함수 내부에서만 사용 가능
- ❌ 서버 사이드 렌더링 불가

자세한 내용은 [docs/coding-guideline.md - URL 라우팅](./coding-guideline.md#url-및-페이지-링크-관리-규칙)을 참조하세요.

---

## ready() 함수

**중요 사항**: `window.AppStore.state`는 **반드시 `ready()` 함수 내부에서** 사용해야 합니다.

### 올바른 사용 예제

**✅ 권장 패턴 - PHP 함수 직접 사용:**

```php
<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.AppStore.state
            };
        },
        methods: {
            doSomething() {
                // ✅ PHP 함수로 번역 텍스트 주입
                alert('<?= tr('작업 완료') ?>');

                // ✅ PHP 함수로 URL 주입
                window.location.href = '<?= href()->home ?>';
            }
        }
    }).mount('#app');
});
</script>
```

### 로딩 순서

```
1. Vue.js, Firebase 등 라이브러리 로드
2. 페이지별 JavaScript 파일 로드 (defer)
3. 페이지 콘텐츠 렌더링 (PHP 실행, tr(), href() 등 주입)
4. window.AppStore.state 초기화 (HTML 맨 아래)
5. ready() 함수 실행 (DOM 준비 완료 후)
```

**중요**: `tr()`, `href()`, `t()->키` 등 PHP 함수는 서버 실행 시점에 처리되므로 ready() 불필요합니다.
