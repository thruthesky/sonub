# JavaScript

## 목차

- [개요](#개요)
- [window.AppStore.state - 전역 상태 관리](#windowappstorestate---전역-상태-관리)
  - [로그인 사용자 정보](#로그인-사용자-정보)
  - [Vue.js Reactivity 사용](#vuejs-reactivity-사용)
  - [사용 예제 모음](#사용-예제-모음)
- [window.t - 다국어 번역](#windowt---다국어-번역)
- [window.hrefs - 페이지 URL 라우팅](#windowhrefs---페이지-url-라우팅)
- [ready() 함수](#ready-함수)

---

## 개요

Sonub의 JavaScript는 다음과 같이 **3가지 전역 객체**를 통해 주요 기능을 제공합니다:

- **window.AppStore.state**: Vue.js Reactivity Proxy로 구현된 전역 상태 관리
- **window.t**: 다국어 번역 객체
- **window.hrefs**: 페이지 URL 라우팅 객체

**중요 사항**: 모든 전역 객체는 **반드시 `ready()` 함수 내부에서** 사용해야 합니다.

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
                    alert(window.t.로그인이_필요합니다);
                    window.location.href = window.hrefs.login;
                    return;
                }

                const myUserId = this.state.user.id;

                // 자기 자신에게 친구 요청 방지
                if (otherUserId === myUserId) {
                    alert(window.t.자기_자신에게는_친구_요청을_보낼_수_없습니다);
                    return;
                }

                try {
                    await func('request_friend', {
                        me: myUserId,
                        other: otherUserId,
                        auth: true
                    });

                    alert(window.t.친구_요청_전송_완료);

                } catch (error) {
                    console.error('친구 요청 실패:', error);
                    alert(`${window.t.친구_요청_실패}: ${error.message}`);
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
                    alert(window.t.로그인이_필요합니다);
                    window.location.href = window.hrefs.login;
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
                    alert(window.t.로그인이_필요합니다);
                    window.location.href = window.hrefs.login;
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
        <a :href="window.hrefs.login">로그인</a>
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
    const t = window.t;
    const hrefs = window.hrefs;

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
                    alert(t.로그인이_필요합니다);
                    const currentUrl = encodeURIComponent(window.location.href);
                    window.location.href = `${hrefs.login}?return=${currentUrl}`;
                    return;
                }

                const myUserId = this.state.user.id;

                // 자기 자신에게 친구 요청 방지
                if (otherUserId === myUserId) {
                    alert(t.자기_자신에게는_친구_요청을_보낼_수_없습니다);
                    return;
                }

                if (this.isFriend) {
                    alert(t.이미_친구입니다);
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
                    alert(t.친구_요청_전송_완료);

                } catch (error) {
                    console.error('친구 요청 실패:', error);
                    this.requesting = false;
                    alert(`${t.친구_요청_실패}: ${error.message}`);
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

## window.t - 다국어 번역

자세한 내용은 [docs/coding-guideline.md - JavaScript에서 다국어 번역 사용](./coding-guideline.md#javascript에서-다국어-번역-사용---windowt)를 참조하세요.

**간단 예제:**

```javascript
ready(() => {
    const t = window.t;

    Vue.createApp({
        methods: {
            showAlert() {
                alert(t.로그인이_필요합니다);
            }
        }
    }).mount('#app');
});
```

---

## window.hrefs - 페이지 URL 라우팅

자세한 내용은 [docs/coding-guideline.md - JavaScript에서 페이지 URL 라우팅](./coding-guideline.md#javascript에서-페이지-url-라우팅---windowhrefs)를 참조하세요.

**간단 예제:**

```javascript
ready(() => {
    const hrefs = window.hrefs;

    Vue.createApp({
        methods: {
            goToLogin() {
                window.location.href = hrefs.login;
            },
            goToProfile(userId) {
                window.location.href = `${hrefs.profile}?id=${userId}`;
            }
        }
    }).mount('#app');
});
```

---

## ready() 함수

**중요 사항**: 모든 전역 객체(`window.AppStore.state`, `window.t`, `window.hrefs`)는 **반드시 `ready()` 함수 내부에서** 사용해야 합니다.

**로딩 순서:**

```
1. Vue.js, Firebase 등 라이브러리 로드
2. 페이지별 JavaScript 파일 로드 (defer)
3. 페이지 콘텐츠 렌더링
4. window.t 객체 생성 (HTML 맨 아래)
5. window.hrefs 객체 생성 (HTML 맨 아래)
6. window.AppStore.state 초기화 (HTML 맨 아래)
7. ready() 함수 실행 (DOM 준비 완료 후)
```

**예제:**

```javascript
// ✅ 올바른 패턴
ready(() => {
    const t = window.t;
    const hrefs = window.hrefs;

    Vue.createApp({
        data() {
            return {
                state: window.AppStore.state
            };
        },
        methods: {
            // ...
        }
    }).mount('#app');
});

// ❌ 잘못된 패턴 - ready() 밖에서 사용
const t = window.t;  // ❌ undefined!
const hrefs = window.hrefs;  // ❌ undefined!
const state = window.AppStore.state;  // ❌ undefined!
```
