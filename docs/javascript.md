# JavaScript

## 목차

- [JavaScript](#javascript)
  - [목차](#목차)
  - [개요](#개요)
  - [window.Store.state - 전역 상태 관리](#windowStorestate---전역-상태-관리)
    - [로그인 사용자 정보](#로그인-사용자-정보)
      - [window.Store.state 예제](#windowStorestate-예제)
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
  - [Petite Vue.js - 경량 반응형 프레임워크](#petite-vuejs---경량-반응형-프레임워크)
    - [개요](#petite-vuejs-개요)
    - [자동 초기화](#자동-초기화)
    - [기본 사용법](#기본-사용법)
    - [Vue.js와의 차이점](#vuejs와의-차이점)
    - [실제 예제](#petite-vue-실제-예제)
    - [주의사항](#petite-vue-주의사항)

---

## 개요

Sonub의 JavaScript는 **PHP MPA (Multi-Page Application)** 방식으로 동작하며, 다음과 같은 방식으로 JavaScript 코드를 작성합니다:

### JavaScript 작성 방식

**🔥🔥🔥 최강력 규칙: 모든 JavaScript는 페이지 파일 내부에 `<script>` 태그로 작성해야 합니다 🔥🔥🔥**

- **✅ 필수**: 모든 JavaScript는 `page/**/*.php` 또는 `widgets/**/*.php` 내부에 `<script>` 태그로 작성
- **✅ 필수**: `ready()` 래퍼 함수 사용
- **❌ 금지**: 외부 JavaScript 파일로 분리 금지

### PHP 함수 직접 사용

**✅ PHP 함수 직접 사용:**
- `<?= tr('텍스트') ?>`: 인라인 번역 함수 사용 가능
- `<?= href()->user->profile ?>`: 페이지 URL 라우팅 직접 사용
- `<?= t()->검색 ?>`: 다국어 번역 텍스트 주입
- `<?= login()->id ?>`: 로그인 사용자 정보 접근

**✅ 장점:**
- HTML, CSS, JavaScript가 한 파일에 모여 있어 가독성 향상
- PHP 함수를 통해 JavaScript에 필요한 텍스트, URL, 기타 정보 주입
- 파일 관리 간소화

### 전역 객체 (레거시)

다음 전역 객체들은 **레거시**이며, 새로운 코드에서는 **PHP 함수를 직접 사용**하는 것을 권장합니다:

- **window.Store.state**: Vue.js Reactivity Proxy로 구현된 전역 상태 관리 (계속 사용)
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
            {{ user.first_name }} {{ user.last_name }}
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

### 긴 JavaScript 코드 관리

JavaScript 코드가 길어지더라도 **반드시 페이지 파일 내부에 `<script>` 태그로 작성**해야 합니다:

**page/user/list.php:**
```php
<div id="user-list-app">
    <!-- 사용자 목록 HTML -->
</div>

<!-- ✅ 긴 JavaScript도 페이지 내부에 작성 -->
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
```

**핵심 포인트:**
- ✅ 모든 JavaScript는 `<script>` 태그 내에 작성
- ✅ `<?= tr(...) ?>`, `<?= href()->... ?>`, `<?= login()->id ?>` 직접 사용 가능
- ✅ HTML, CSS, JavaScript가 한 파일에 모여 있어 가독성 향상
- ✅ 페이지별 번역은 파일 하단에 `inject_[파일명]_language()` 함수 정의

---

## window.Store.state - 전역 상태 관리

**🔥🔥🔥 최강력 규칙: 사용자 정보를 사용할 때는 window.Store.state.user에서 가져와야 합니다 🔥🔥🔥**

### 로그인 사용자 정보

사용자가 로그인하면, **window.Store.state.user**에 **Vue.js의 Reactivity Proxy**로 사용자 정보가 저장됩니다.

#### window.Store.state 예제

```javascript
window.Store = {
    state: {
        user: {
            id: 1,                    // 사용자 ID
            firebase_uid: 'abc123',   // Firebase UID
            first_name: '길동',       // 이름
            last_name: '홍',          // 성
            middle_name: '',          // 중간 이름
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

**중요 주의**: `window.Store.state`는 **Vue.js Reactivity Proxy**이므로, Vue 컴포넌트의 `data()`에서 참조해야 반응형으로 동작합니다.

#### 올바른 패턴

**✅ Vue 컴포넌트에서 상태 사용:**

```javascript
ready(() => {
    Vue.createApp({
        data() {
            return {
                // ✅ window.Store.state를 data에 추가 - 반응형으로 동작!
                state: window.Store.state
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
                state: window.Store.state
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
                const myName = `${this.state.user.first_name} ${this.state.user.last_name}`;
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
                state: window.Store.state
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
                console.log(`사용자 정보: ${user.first_name} ${user.last_name}`);
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
                state: window.Store.state
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
        <p>환영합니다, {{ state.user.first_name }} {{ state.user.last_name }}님!</p>
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
    <h1><?= htmlspecialchars($user->displayFullName()) ?></h1>

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
                state: window.Store.state,
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

**❌ window.Store.user 직접 접근 (반응형 X):**

```javascript
// ❌ 잘못된 예제 - window.Store.user는 존재하지 않습니다!
if (!window.Store.user?.id) {  // ❌ undefined!
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
                // ❌ window.Store.state.user를 직접 참조 - 반응형 X
                if (!window.Store.state?.user?.id) {
                    alert('로그인이 필요합니다.');
                    return;
                }

                // 동작은 하지만 UI 업데이트가 안됨 (사용자 정보 변경 시 UI 미반영)
                const myUserId = window.Store.state.user.id;
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
                state: window.Store.state
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
                state: window.Store.state
            };
        },
        methods: {
            async updateProfile() {
                try {
                    await func('update_user', {
                        first_name: '새로운',
                        last_name: '이름',
                        auth: true
                    });

                    // ✅ state.user가 변경되면 자동으로 업데이트됨!
                    // 템플릿의 {{ state.user.first_name }} {{ state.user.last_name }}도 자동 업데이트!

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
    <p>환영합니다, {{ state.user.first_name }} {{ state.user.last_name }}님!</p>
    <button @click="updateProfile">프로필 업데이트</button>
</div>
```

### 요약

- **✅ 필수**: `data()`에서 `state: window.Store.state` 추가
- **✅ 필수**: `this.state.user`로 사용자 정보 접근
- **✅ 권장**: Optional Chaining (`?.`) 사용으로 안전하게 사용
- **✅ 권장**: Computed Property로 `isLoggedIn` 정의
- **❌ 금지**: `window.Store.user` 직접 접근 (존재하지 않습니다)
- **❌ 금지**: `window.Store.state.user`를 data에 추가하지 않고 직접 접근 (반응형 X)
- **장점**: Vue.js Reactivity로 자동 UI 업데이트, 코드 간결성, 일관성 보장

---

## 다국어 번역

### 번역 방식 개요

Sonub에서는 **모든 JavaScript 코드를 PHP 페이지 파일 내부의 `<script>` 태그로 작성**하므로, 다음 두 가지 번역 방식을 사용할 수 있습니다:

1. **PHP `tr()` 함수**: 서버 실행 시점에 번역 텍스트 주입 (정적 번역)
2. **JavaScript `tr()` 함수**: 클라이언트 실행 시점에 동적 번역 (동적 언어 전환)

**🔥🔥🔥 중요: JavaScript는 외부 `.js` 파일로 분리하지 않고 페이지 내 `<script>` 태그로 작성합니다 🔥🔥🔥**

### JavaScript tr() 함수 (페이지 내 `<script>` 태그에서만 사용 가능)

**⚠️ 중요 제한 사항:**
- **✅ 사용 가능**: PHP 페이지 파일 내부의 `<script>` 태그에서만 사용 가능
- **❌ 사용 불가**: 외부 `.js` 파일에서는 사용 불가 (Sonub는 외부 JS 파일 분리를 권장하지 않음)

JavaScript의 `tr()` 함수는 PHP의 `tr()` 함수와 유사한 방식으로 동작하며, 클라이언트 사이드에서 동적으로 언어를 전환할 때 사용합니다.

**함수 위치**: `/js/app.js`

```javascript
// 다국어 번역 함수
// 예제: tr({ en: 'Hello', ko: '안녕하세요' });
function tr(texts = {}) {
    const lang = window.Store.state.lang;
    return texts[lang] || texts['en'] || '';
}
```

**✅ 사용 방법 (페이지 내 `<script>` 태그):**

```php
<!-- page/user/profile.php -->
<div id="app">
    <!-- HTML 내용 -->
</div>

<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.Store.state
            };
        },
        methods: {
            showWelcomeMessage() {
                // ✅ JavaScript tr() 함수 사용 (페이지 내 <script>에서만 사용 가능)
                const message = tr({
                    ko: '환영합니다',
                    en: 'Welcome',
                    ja: 'ようこそ',
                    zh: '欢迎'
                });
                alert(message);
            },
            showLoginRequiredAlert() {
                // ✅ 동적 번역 - 사용자 언어에 맞게 자동 표시
                const message = tr({
                    ko: '로그인이 필요합니다',
                    en: 'Login required',
                    ja: 'ログインが必要です',
                    zh: '需要登录'
                });
                alert(message);
            }
        }
    }).mount('#app');
});
</script>
```

**장점:**
- ✅ 사용자 언어(`window.Store.state.lang`)에 맞게 자동 번역
- ✅ 동적 언어 전환 지원 (언어 변경 시 자동 업데이트)
- ✅ PHP의 `tr()` 함수와 유사한 인터페이스
- ✅ 페이지 파일 내부에 모든 코드가 모여 있어 관리 편리

**사용 시나리오:**
- JavaScript에서 동적으로 생성되는 메시지
- 사용자 액션에 따라 변경되는 텍스트
- Vue.js computed property에서 사용
- 클라이언트 사이드에서 언어를 동적으로 전환해야 하는 경우

**실제 예제 - 동적 에러 메시지:**

```php
<!-- page/user/submit.php -->
<div id="app">
    <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
    <p>{{ welcomeText }}</p>
    <button @click="submitForm">제출</button>
</div>

<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.Store.state,
                errorMessage: ''
            };
        },
        computed: {
            // ✅ computed에서 tr() 사용 - 언어 변경 시 자동 업데이트
            welcomeText() {
                return tr({
                    ko: '환영합니다',
                    en: 'Welcome',
                    ja: 'ようこそ',
                    zh: '欢迎'
                });
            }
        },
        methods: {
            async submitForm() {
                try {
                    await func('submit_data', { auth: true });

                    // ✅ 성공 메시지 - 사용자 언어로 표시
                    alert(tr({
                        ko: '제출이 완료되었습니다',
                        en: 'Submission completed',
                        ja: '送信が完了しました',
                        zh: '提交完成'
                    }));
                } catch (error) {
                    // ✅ 에러 메시지 - 사용자 언어로 표시
                    this.errorMessage = tr({
                        ko: '제출 중 오류가 발생했습니다',
                        en: 'An error occurred during submission',
                        ja: '送信中にエラーが発生しました',
                        zh: '提交时发生错误'
                    });
                }
            }
        }
    }).mount('#app');
});
</script>
```

### PHP 함수로 번역 텍스트 주입 (정적 번역 - 권장)

**🔥🔥🔥 최강력 권장: 대부분의 경우 PHP `tr()` 함수를 사용하세요 🔥🔥🔥**

PHP의 `tr()` 함수는 서버 실행 시점에 번역 텍스트를 주입하므로, **대부분의 경우 이 방법을 사용하는 것이 권장됩니다**.

**✅ 사용 방법 1: PHP tr() 함수 (가장 권장)**

```php
<!-- page/user/profile.php -->
<div id="app">
    <!-- HTML 내용 -->
</div>

<script>
ready(() => {
    Vue.createApp({
        methods: {
            showAlert() {
                // ✅ PHP tr() 함수로 직접 주입 (서버 실행 시점)
                alert('<?= tr('로그인이 필요합니다') ?>');
            },
            goToLogin() {
                // ✅ PHP tr() 함수로 confirm 메시지 주입
                if (confirm('<?= tr('로그인 페이지로 이동하시겠습니까?') ?>')) {
                    window.location.href = '<?= href()->user->login ?>';
                }
            }
        }
    }).mount('#app');
});
</script>
```

**✅ 사용 방법 2: t()->키 사용**

```php
<!-- page/user/profile.php -->
<div id="app">
    <!-- HTML 내용 -->
</div>

<script>
ready(() => {
    Vue.createApp({
        methods: {
            showAlert() {
                // ✅ PHP t()->키로 직접 주입 (서버 실행 시점)
                alert('<?= t()->로그인이_필요합니다 ?>');
            }
        }
    }).mount('#app');
});
</script>
```

**장점:**
- ✅ PHP 실행 시점에 번역 텍스트 주입 (서버 사이드 렌더링)
- ✅ 서버 사이드 번역으로 SEO 개선
- ✅ 정적 텍스트에 가장 적합
- ✅ 페이지 로드 시 이미 번역된 텍스트 제공 (성능 향상)
- ✅ 외부 `.js` 파일로 분리할 필요 없음 (Sonub 표준 패턴)

### JavaScript tr() vs PHP tr() 비교

| 항목 | JavaScript `tr()` | PHP `tr()` / `t()->키` |
|------|------------------|----------------------|
| **실행 시점** | 클라이언트 (브라우저) | 서버 (PHP 실행 시점) |
| **사용 위치** | 페이지 내 `<script>` 태그만 | 페이지 내 `<script>` 태그 및 HTML |
| **외부 `.js` 파일** | ❌ 사용 불가 | ❌ 사용 불가 (외부 JS 파일 분리 금지) |
| **사용 시나리오** | 동적 번역 필요한 경우 | 정적 텍스트 번역 (대부분의 경우) |
| **언어 전환** | ✅ 실시간 언어 전환 가능 | ❌ 페이지 새로고침 필요 |
| **Vue computed** | ✅ 사용 가능 (반응형) | ✅ 사용 가능 (PHP 실행 후 고정) |
| **SEO** | ❌ 클라이언트 렌더링 | ✅ 서버 사이드 렌더링 |
| **성능** | 클라이언트 처리 (약간 느림) | ✅ 서버 처리 (빠름) |
| **권장 사용처** | JavaScript 동적 메시지, computed | **HTML 템플릿 정적 텍스트 (우선 권장)** |

**🔥🔥🔥 사용 가이드라인:**

1. **✅ PHP `tr()` 함수 우선 사용** (대부분의 경우)
   - HTML 템플릿의 정적 텍스트
   - JavaScript `<script>` 태그 내부의 alert, confirm 메시지
   - 서버 사이드 렌더링이 필요한 모든 텍스트

2. **✅ JavaScript `tr()` 함수는 특별한 경우에만 사용**
   - JavaScript에서 동적으로 생성되는 메시지
   - Vue.js computed property에서 언어 전환이 필요한 경우
   - 클라이언트 사이드에서 실시간 언어 전환이 필요한 경우

3. **❌ 외부 `.js` 파일에서는 둘 다 사용 불가**
   - Sonub는 JavaScript를 외부 파일로 분리하지 않음
   - 모든 JavaScript는 페이지 내 `<script>` 태그로 작성

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

**중요 사항**: `window.Store.state`는 **반드시 `ready()` 함수 내부에서** 사용해야 합니다.

### 올바른 사용 예제

**✅ 권장 패턴 - PHP 함수 직접 사용:**

```php
<script>
ready(() => {
    Vue.createApp({
        data() {
            return {
                state: window.Store.state
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
4. window.Store.state 초기화 (HTML 맨 아래)
5. ready() 함수 실행 (DOM 준비 완료 후)
```

**중요**: `tr()`, `href()`, `t()->키` 등 PHP 함수는 서버 실행 시점에 처리되므로 ready() 불필요합니다.

---

## Petite Vue.js - 경량 반응형 프레임워크

### Petite Vue.js 개요

Petite Vue.js는 Vue.js의 경량 버전으로, **6KB 크기의 프로그레시브 향상(Progressive Enhancement) 프레임워크**입니다. Sonub에서는 간단한 반응형 UI를 구현할 때 Petite Vue를 사용할 수 있습니다.

**🔥🔥🔥 중요: Petite Vue는 layout.php에서 자동으로 포함되고 초기화되어 Sonub의 모든 영역에서 즉시 사용 가능한 상태입니다 🔥🔥🔥**

### 자동 초기화

Petite Vue는 **layout.php**에서 자동으로 로드되고 초기화됩니다:

```javascript
// layout.php에서 자동 실행되는 코드
<script src="/js/petite-vue.iife.js" defer></script>
<script>
ready(() => {
    // v-scope가 붙은 요소만 골라서 Petite Vue 적용
    document.querySelectorAll('[v-scope]').forEach(el => {
        // 각 v-scope 엘리먼트를 독립적으로 마운트
        PetiteVue.createApp().mount(el)
    })
})
</script>
```

**특징:**
- ✅ **자동 초기화**: 페이지 로드 시 자동으로 모든 `v-scope` 요소 초기화
- ✅ **즉시 사용 가능**: 별도의 초기화 코드 없이 바로 사용
- ✅ **독립적 마운트**: 각 `v-scope` 요소는 독립적으로 동작
- ✅ **전역 사용 가능**: Sonub의 모든 페이지와 위젯에서 사용 가능

### 기본 사용법

**✅ 가장 간단한 예제:**

```html
<!-- 카운터 예제 -->
<div v-scope="{ count: 0 }">
    <button @click="count++">클릭: {{ count }}</button>
</div>

<!-- 토글 예제 -->
<div v-scope="{ show: false }">
    <button @click="show = !show">토글</button>
    <p v-if="show">보이는 콘텐츠</p>
</div>
```

**✅ 복합 예제 (요청된 예제):**

```html
<div>
    <h1>Petite Vue 테스트 페이지</h1>
    <nav v-scope="{count: 0, show: false}">
        count: <button @click="count++">{{ count }}</button>
        show: <button @click="show = !show">{{ show }}</button>
        <hr>
        <div v-if="show">
            <p>카운트 값이 {{ count }} 입니다.</p>
        </div>
    </nav>
</div>
```

### Vue.js와의 차이점

| 항목 | Petite Vue | Vue.js 3.x |
|------|------------|------------|
| **크기** | 6KB | 34KB+ |
| **초기화** | `v-scope` 속성만 추가 | `Vue.createApp().mount()` 필요 |
| **사용 시나리오** | 간단한 반응형 UI | 복잡한 SPA 및 컴포넌트 |
| **컴포넌트** | ❌ 지원 안함 | ✅ 완전 지원 |
| **Computed** | ❌ 지원 안함 | ✅ 지원 |
| **Watch** | ❌ 지원 안함 | ✅ 지원 |
| **Lifecycle** | ❌ 지원 안함 | ✅ 지원 |
| **디렉티브** | 기본 디렉티브만 | 모든 디렉티브 |

**Petite Vue 지원 디렉티브:**
- ✅ `v-if`, `v-else`, `v-else-if` - 조건부 렌더링
- ✅ `v-for` - 리스트 렌더링
- ✅ `v-show` - 표시/숨김
- ✅ `v-model` - 양방향 바인딩
- ✅ `v-text`, `v-html` - 텍스트/HTML 바인딩
- ✅ `@click`, `@input` 등 - 이벤트 리스너
- ✅ `:class`, `:style` 등 - 속성 바인딩

### Petite Vue 실제 예제

#### 예제 1: 할 일 목록 (Todo List)

```html
<!-- page/test/petite-vue-todo.php -->
<div class="container py-4">
    <h2>할 일 목록 (Petite Vue)</h2>

    <div v-scope="{
        todos: [],
        newTodo: '',
        addTodo() {
            if (this.newTodo.trim()) {
                this.todos.push({
                    id: Date.now(),
                    text: this.newTodo,
                    done: false
                });
                this.newTodo = '';
            }
        },
        removeTodo(id) {
            this.todos = this.todos.filter(t => t.id !== id);
        }
    }">
        <!-- 입력 폼 -->
        <div class="input-group mb-3">
            <input type="text"
                   v-model="newTodo"
                   @keyup.enter="addTodo"
                   class="form-control"
                   placeholder="할 일을 입력하세요">
            <button @click="addTodo" class="btn btn-primary">추가</button>
        </div>

        <!-- 할 일 목록 -->
        <ul class="list-group">
            <li v-for="todo in todos"
                :key="todo.id"
                class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <input type="checkbox" v-model="todo.done" class="form-check-input me-2">
                    <span :class="{ 'text-decoration-line-through': todo.done }">
                        {{ todo.text }}
                    </span>
                </div>
                <button @click="removeTodo(todo.id)" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </li>
        </ul>

        <!-- 빈 상태 -->
        <p v-if="todos.length === 0" class="text-muted text-center mt-3">
            할 일이 없습니다.
        </p>
    </div>
</div>
```

#### 예제 2: 탭 네비게이션

```html
<!-- widgets/tab-navigation.php -->
<div v-scope="{ activeTab: 'tab1' }" class="tab-widget">
    <!-- 탭 헤더 -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <button @click="activeTab = 'tab1'"
                    :class="['nav-link', activeTab === 'tab1' ? 'active' : '']">
                탭 1
            </button>
        </li>
        <li class="nav-item">
            <button @click="activeTab = 'tab2'"
                    :class="['nav-link', activeTab === 'tab2' ? 'active' : '']">
                탭 2
            </button>
        </li>
        <li class="nav-item">
            <button @click="activeTab = 'tab3'"
                    :class="['nav-link', activeTab === 'tab3' ? 'active' : '']">
                탭 3
            </button>
        </li>
    </ul>

    <!-- 탭 콘텐츠 -->
    <div class="tab-content p-3 border border-top-0">
        <div v-show="activeTab === 'tab1'">
            <h4>탭 1 콘텐츠</h4>
            <p>첫 번째 탭의 내용입니다.</p>
        </div>
        <div v-show="activeTab === 'tab2'">
            <h4>탭 2 콘텐츠</h4>
            <p>두 번째 탭의 내용입니다.</p>
        </div>
        <div v-show="activeTab === 'tab3'">
            <h4>탭 3 콘텐츠</h4>
            <p>세 번째 탭의 내용입니다.</p>
        </div>
    </div>
</div>
```

#### 예제 3: 실시간 검색 필터

```html
<!-- widgets/user-filter.php -->
<div v-scope="{
    searchQuery: '',
    users: [
        { id: 1, name: '홍길동', email: 'hong@example.com' },
        { id: 2, name: '김철수', email: 'kim@example.com' },
        { id: 3, name: '이영희', email: 'lee@example.com' },
        { id: 4, name: '박민수', email: 'park@example.com' }
    ],
    get filteredUsers() {
        const query = this.searchQuery.toLowerCase();
        return this.users.filter(user =>
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query)
        );
    }
}">
    <!-- 검색 입력 -->
    <div class="mb-3">
        <input type="text"
               v-model="searchQuery"
               class="form-control"
               placeholder="이름 또는 이메일로 검색...">
    </div>

    <!-- 사용자 목록 -->
    <div class="row g-2">
        <div v-for="user in filteredUsers"
             :key="user.id"
             class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">{{ user.name }}</h6>
                    <p class="card-text text-muted">{{ user.email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 검색 결과 없음 -->
    <p v-if="filteredUsers.length === 0" class="text-center text-muted">
        검색 결과가 없습니다.
    </p>
</div>
```

### Petite Vue 주의사항

**✅ 사용하기 좋은 경우:**
- 간단한 토글, 카운터, 탭 전환
- 폼 유효성 검사 및 동적 폼
- 리스트 필터링 및 정렬
- 간단한 상태 관리가 필요한 위젯

**❌ 사용하지 말아야 할 경우:**
- 복잡한 컴포넌트 계층 구조
- Computed, Watch, Lifecycle이 필요한 경우
- 대규모 상태 관리가 필요한 경우
- API 호출 및 비동기 작업이 많은 경우

**🔥 중요 제한사항:**
1. **Computed 미지원**: getter 함수로 대체 가능하지만 캐싱 없음
2. **Watch 미지원**: 상태 변경 감지 불가
3. **컴포넌트 미지원**: 재사용 가능한 컴포넌트 생성 불가
4. **Lifecycle 미지원**: mounted, created 등 훅 사용 불가
5. **메서드 정의**: v-scope 내에서 직접 정의해야 함

**Vue.js vs Petite Vue 선택 가이드:**
```javascript
// ✅ Petite Vue 사용 - 간단한 UI
<div v-scope="{ show: false }">
    <button @click="show = !show">토글</button>
    <p v-if="show">간단한 토글 UI</p>
</div>

// ✅ Vue.js 사용 - 복잡한 로직
ready(() => {
    Vue.createApp({
        data() {
            return { users: [] };
        },
        async mounted() {
            // API 호출, 복잡한 초기화
            this.users = await func('list_users', {});
        },
        computed: {
            activeUsers() {
                return this.users.filter(u => u.active);
            }
        }
    }).mount('#app');
});
```

**요약:**
- Petite Vue는 **layout.php에서 자동 초기화**되어 즉시 사용 가능
- `v-scope` 속성만 추가하면 자동으로 반응형 UI 생성
- 간단한 UI에는 Petite Vue, 복잡한 앱에는 Vue.js 3.x 사용
- 두 프레임워크는 같은 페이지에서 독립적으로 공존 가능
