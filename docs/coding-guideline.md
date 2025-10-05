# Sonub 코딩 가이드라인

## 목차

- [Sonub 코딩 가이드라인](#sonub-코딩-가이드라인)
  - [목차](#목차)
  - [개요](#개요)
  - [일반 코딩 표준](#일반-코딩-표준)
  - [디자인 및 스타일링 표준](#디자인-및-스타일링-표준)
  - [JavaScript 프레임워크 - Vue.js 3.x](#javascript-프레임워크---vuejs-3x)
    - [Vue.js 사용 방식](#vuejs-사용-방식)
    - [MPA 방식의 장점](#mpa-방식의-장점)
    - [Vue.js 기본 사용법](#vuejs-기본-사용법)
  - [프레임워크 및 라이브러리 저장 가이드라인](#프레임워크-및-라이브러리-저장-가이드라인)
    - [완전한 프레임워크 패키지](#완전한-프레임워크-패키지)
    - [단일 JavaScript 라이브러리](#단일-javascript-라이브러리)
  - [페이지별 CSS 및 JavaScript 자동 로딩](#페이지별-css-및-javascript-자동-로딩)
    - [개요](#개요-1)
    - [작동 방식](#작동-방식)
    - [사용 가이드라인](#사용-가이드라인)
    - [예제](#예제)
  - [Firebase 통합 가이드라인](#firebase-통합-가이드라인)
    - [로딩 동작](#로딩-동작)
    - [JavaScript에서 사용](#javascript에서-사용)
    - [부팅 절차에서 사용](#부팅-절차에서-사용)
  - [개발 시스템 시작](#개발-시스템-시작)
    - [빠른 시작 (필수 명령어)](#빠른-시작-필수-명령어)
    - [Docker Compose 사용](#docker-compose-사용)
    - [핫 리로드 개발 서버](#핫-리로드-개발-서버)
  - [레이아웃 파일 구조](#레이아웃-파일-구조)
  - [URL 및 페이지 링크 관리 규칙](#url-및-페이지-링크-관리-규칙)
  - [CSS 및 디자인 규칙](#css-및-디자인-규칙)
  - [필수 언어 사용 규칙](#필수-언어-사용-규칙)

---

## 개요

- Sonub는 Social Network Hub의 약자로, 소셜 네트워크 허브입니다.
- Sonub는 필고(PHILGO) 프로젝트의 일부로 개발되고 있습니다.
- 이 문서는 Sonub 웹사이트 개발 시 준수해야 할 코딩 가이드라인과 규칙을 명시합니다.
- 모든 개발자는 이 문서를 숙지하고 준수해야 합니다.

### 명칭

- 한글 명칭: 소너브
- 한글 약칭: 소넙
- 영문 명칭: Sonub
- 영문 약칭: Sonub

---

## 일반 코딩 표준

- 들여쓰기는 탭 대신 4개의 공백을 사용합니다.
- BOM 없는 UTF-8 인코딩을 사용합니다.
- 복잡한 로직에는 주석과 문서화를 작성합니다.
- 모든 주석과 문서는 **반드시 한국어**로 작성합니다.

---

## 디자인 및 스타일링 표준

- **라이트 모드만 지원**: Sonub 웹사이트는 라이트 모드만 지원합니다. 다크 모드 기능을 절대 구현하지 마세요.
- **Bootstrap 색상 사용**: 항상 Bootstrap의 기본 색상 클래스와 변수를 최대한 사용하세요.
- **커스텀 색상 금지**: 꼭 필요한 경우가 아니면 커스텀 HEX 코드나 CSS 색상 이름을 사용하지 마세요.

---

## JavaScript 프레임워크 - Vue.js 3.x

### Vue.js 사용 방식

Sonub는 **Vue.js 3.x를 CDN 방식**으로 사용하며, **PHP MPA (Multi-Page Application)** 방식으로 동작합니다.

- **CDN 로딩**: Vue.js 3.x는 CDN을 통해 자동으로 모든 페이지에 로드됩니다
- **수동 추가 불필요**: 모든 페이지 하단에 Vue.js가 자동으로 포함되므로 별도로 추가할 필요가 없습니다
- **전역 사용 가능**: 모든 페이지에서 `Vue` 객체를 바로 사용할 수 있습니다

### MPA 방식의 장점

PHP MPA 방식으로 동작하므로 다음과 같은 특징이 있습니다:

- **자동 리셋**: 페이지 이동 시 모든 Vue 인스턴스와 이벤트 리스너가 자동으로 해제됩니다
- **이벤트 리스너 정리 불필요**: SPA와 달리 `beforeUnmount`나 `unmounted`에서 이벤트 리스너를 수동으로 해제할 필요가 없습니다
- **메모리 누수 방지**: 페이지 전환 시 브라우저가 자동으로 메모리를 정리합니다
- **단순한 상태 관리**: 각 페이지가 독립적이므로 복잡한 전역 상태 관리가 필요 없습니다

### Vue.js 기본 사용법

#### 기본 Vue 앱 생성

```javascript
// 각 페이지의 JavaScript 파일에서
const { createApp } = Vue;

createApp({
  data() {
    return {
      message: '안녕하세요, Sonub!',
      count: 0
    }
  },
  methods: {
    increment() {
      this.count++;
    }
  },
  mounted() {
    console.log('Vue 앱이 마운트되었습니다');
  }
}).mount('#app');
```

#### HTML에서 Vue 사용

```html
<div id="app">
  <h1>{{ message }}</h1>
  <p>카운트: {{ count }}</p>
  <button @click="increment">증가</button>
</div>
```

#### 컴포넌트 정의 및 사용

```javascript
const { createApp } = Vue;

// 컴포넌트 정의
const UserCard = {
  props: ['user'],
  template: `
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">{{ user.name }}</h5>
        <p class="card-text">{{ user.email }}</p>
      </div>
    </div>
  `
};

// 앱 생성 및 컴포넌트 등록
createApp({
  components: {
    UserCard
  },
  data() {
    return {
      users: [
        { name: '홍길동', email: 'hong@example.com' },
        { name: '김철수', email: 'kim@example.com' }
      ]
    }
  }
}).mount('#app');
```

#### Firebase와 함께 사용

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      posts: []
    }
  },
  methods: {
    async loadPosts() {
      // Firebase Firestore에서 게시글 로드
      const snapshot = await firebase.firestore()
        .collection('posts')
        .orderBy('createdAt', 'desc')
        .limit(10)
        .get();

      this.posts = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    }
  },
  mounted() {
    // 인증 상태 확인
    firebase.auth().onAuthStateChanged(user => {
      this.user = user;
      if (user) {
        this.loadPosts();
      }
    });
  }
}).mount('#app');
```

#### 중요 사항

- **이벤트 리스너 해제 불필요**: MPA 방식이므로 페이지 이동 시 자동으로 모든 리스너가 정리됩니다
- **페이지별 독립적**: 각 페이지의 Vue 인스턴스는 완전히 독립적입니다
- **간단한 구조 유지**: 복잡한 라우팅이나 상태 관리 라이브러리 없이 간단하게 사용합니다

---

## 프레임워크 및 라이브러리 저장 가이드라인

### 완전한 프레임워크 패키지

완전한 프레임워크 파일(예: 모든 컴포넌트를 포함한 Bootstrap)을 다운로드할 때:

- 저장 위치: `etc/frameworks/<프레임워크-이름>/<프레임워크-이름>-x.x.x/`
- 예시: `etc/frameworks/bootstrap/bootstrap-5.3.0/`
- 이 구조는 버전 관리를 유지하고 여러 버전이 공존할 수 있게 합니다.

### 단일 JavaScript 라이브러리

단일 JavaScript 파일 라이브러리의 경우:

- 저장 위치: `/js/`에 직접 저장
- 예시: `/js/library-name-x.x.x.min.js`
- 라이브러리 버전을 추적하기 위해 버전이 포함된 파일명을 사용합니다.
- **참고**: Vue.js는 CDN으로 자동 로드되므로 별도로 저장하지 않습니다.

---

## 페이지별 CSS 및 JavaScript 자동 로딩

### 개요

Sonub는 `lib/page/page.functions.php`에 정의된 두 개의 헬퍼 함수를 통해 페이지별 CSS 및 JavaScript 파일의 자동 로딩 기능을 제공합니다. 이 함수들은 현재 제공되는 페이지에 해당하는 CSS 및 JS 파일을 자동으로 감지하고 포함시켜, 페이지별 자산을 수동으로 포함할 필요가 없습니다.

### 작동 방식

자동 로딩 시스템은 메인 `index.php` 파일에서 호출되는 두 개의 함수를 사용합니다:

1. **`include_page_css()`** - 페이지의 PHP 파일과 같은 디렉토리에 CSS 파일이 있으면 자동으로 포함
2. **`include_page_js()`** - 페이지의 PHP 파일과 같은 디렉토리에 JavaScript 파일이 있으면 자동으로 포함

이 함수들은 다음과 같이 작동합니다:

1. `page()` 함수를 사용하여 현재 페이지 경로를 결정
2. 같은 위치에 해당하는 `.css` 또는 `.js` 파일이 있는지 확인
3. 파일이 있으면 적절한 `<link>` 또는 `<script>` 태그를 자동으로 생성하고 출력

### 사용 가이드라인

#### 자동 포함

- **수동 개입 불필요**: 올바른 명명 규칙으로 CSS 또는 JS 파일을 생성하면 자동으로 로드됩니다
- **설정보다 규칙**: PHP 페이지 파일에 맞게 파일 이름만 지정하면 됩니다
- **깔끔하고 유지보수 가능**: 보일러플레이트 코드를 줄이고 포함을 잊어버리는 것을 방지합니다

#### 파일 명명 규칙

자동 로딩이 작동하려면 다음 명명 패턴을 따르세요:

**기본 페이지:**
- PHP 페이지: `/page/about.php`
- CSS 파일: `/page/about.css` (존재하면 자동 로드)
- JS 파일: `/page/about.js` (존재하면 defer 속성과 함께 자동 로드)

**중첩된 페이지:**
- PHP 페이지: `/page/user/login.php`
- CSS 파일: `/page/user/login.css`
- JS 파일: `/page/user/login.js`

#### 사용 시기

**다음과 같은 경우 페이지별 CSS/JS 자동 로딩을 항상 사용하세요:**

- 커스텀 스타일링 또는 JavaScript 기능이 필요한 새 페이지 생성 시
- 독립된 스타일이 필요한 기능별 인터페이스 구축 시
- 전역 스타일에 영향을 주지 않아야 하는 컴포넌트 개발 시
- 특정 라우트에만 적용되는 페이지별 JavaScript 작성 시

**장점:**

- 자동 코드 분할 - 필요할 때만 CSS/JS를 로드
- 성능 향상 - 필요한 자산만 로드하여 초기 페이지 로드를 줄임
- 유지보수성 향상 - 페이지별 코드를 PHP 파일과 함께 정리
- 설정 불필요 - 파일만 생성하면 작동

### 예제

#### 예제 1: 지원 페이지 만들기

`/page/support.php`에 새 지원 페이지를 만들 때:

```php
<!-- /page/support.php -->
<div class="support-container">
    <h1>고객 지원</h1>
    <!-- 페이지 콘텐츠 -->
</div>
```

해당 파일들을 간단히 생성:

```css
/* /page/support.css */
.support-container {
  padding: 20px;
  background: var(--bs-light);
}
```

```javascript
// /page/support.js
const { createApp } = Vue;

createApp({
  data() {
    return {
      message: '지원 페이지에 오신 것을 환영합니다',
      tickets: []
    }
  },
  methods: {
    async loadTickets() {
      // 지원 티켓 로드
      console.log('지원 티켓을 로드합니다');
    }
  },
  mounted() {
    console.log('지원 페이지 로드됨');
    this.loadTickets();
  }
}).mount('#support-app');
```

이 파일들은 `/support` 접속 시 자동으로 포함됩니다.

#### 예제 2: 사용자 로그인 페이지

`/page/user/login.php`의 로그인 페이지:

```php
<!-- /page/user/login.php -->
<div id="login-app">
  <form @submit.prevent="handleLogin">
    <div class="mb-3">
      <label class="form-label">이메일</label>
      <input type="email" class="form-control" v-model="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">비밀번호</label>
      <input type="password" class="form-control" v-model="password" required>
    </div>
    <button type="submit" class="btn btn-primary">로그인</button>
    <p v-if="error" class="text-danger mt-2">{{ error }}</p>
  </form>
</div>
```

다음을 생성:

```css
/* /page/user/login.css */
#login-app form {
  max-width: 400px;
  margin: 0 auto;
}
```

```javascript
// /page/user/login.js
const { createApp } = Vue;

createApp({
  data() {
    return {
      email: '',
      password: '',
      error: null
    }
  },
  methods: {
    async handleLogin() {
      try {
        // Firebase 인증
        await firebase.auth().signInWithEmailAndPassword(
          this.email,
          this.password
        );
        // 로그인 성공 시 리다이렉트
        window.location.href = '/dashboard';
      } catch (err) {
        this.error = '로그인에 실패했습니다: ' + err.message;
      }
    }
  }
}).mount('#login-app');
```

#### 출력 예제

`https://local.sonub.com/support` 접속 시, 시스템은 자동으로 다음을 생성합니다:

```html
<link href="/page/support.css" rel="stylesheet" />
<script defer src="/page/support.js"></script>
```

이 태그들은 `index.php`에서 호출되는 함수에 의해 페이지의 `<head>` 섹션에 자동으로 주입됩니다.

---

## Firebase 통합 가이드라인

### 로딩 동작

**⚠️⚠️⚠️ 중요: Firebase와 Vue.js의 로딩 순서 및 사용 가능 시점 ⚠️⚠️⚠️**

Firebase JavaScript SDK와 Vue.js는 페이지의 `<body>` 태그 상단에서 순차적으로 로드됩니다:

1. **Firebase SDK 로드 위치**: `<body>` 태그 시작 직후 즉시 로드
2. **Vue.js 로드 위치**: Firebase SDK 바로 다음에 로드
3. **로딩 방식**: 모두 동기적으로 로드 (defer 또는 async 속성 없음)

이는 다음을 의미합니다:

- ✅ Firebase는 페이지 어디서나 **즉시 사용 가능**합니다
- ✅ Vue.js도 페이지 어디서나 **즉시 사용 가능**합니다
- ✅ Vue.js에서 Firebase를 **즉시 사용 가능**합니다
- ✅ Vue.js의 `mounted()` 훅에서 Firebase를 **바로 호출 가능**합니다
- ✅ 별도의 대기 시간이나 초기화 체크가 **불필요**합니다

**로딩 순서 예시:**

```html
<body>
    <!-- 1. Firebase SDK 로드 (최우선) -->
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-firestore-compat.js"></script>

    <!-- 2. Firebase 초기화 -->
    <script>
        firebase.initializeApp({ /* config */ });
    </script>

    <!-- 3. Vue.js 로드 -->
    <script src="/js/vue.global.prod.js"></script>

    <!-- 이제 Firebase와 Vue.js 모두 즉시 사용 가능 -->
    <!-- 페이지 콘텐츠 -->
    <?php include page() ?>
</body>
```

### JavaScript에서 사용

Firebase는 즉시 로드되고 초기화되므로, Vue.js 앱 내에서 Firebase를 직접 사용할 수 있습니다:

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      user: null,
      users: []
    }
  },
  methods: {
    async loadUsers() {
      // Firestore에서 사용자 목록 로드
      const snapshot = await firebase.firestore()
        .collection('users')
        .get();

      this.users = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    }
  },
  mounted() {
    // Firebase 인증 상태 감지
    firebase.auth().onAuthStateChanged(user => {
      this.user = user;
      if (user) {
        console.log('사용자 로그인:', user.uid);
        this.loadUsers();
      } else {
        console.log('사용자 로그아웃');
      }
    });
  }
}).mount('#app');
```

### Vue.js에서 Firebase 실시간 업데이트

```javascript
const { createApp } = Vue;

createApp({
  data() {
    return {
      notifications: [],
      unsubscribe: null
    }
  },
  methods: {
    setupRealtimeListener() {
      // Firestore 실시간 리스너 설정
      this.unsubscribe = firebase.firestore()
        .collection('notifications')
        .where('userId', '==', this.user.uid)
        .orderBy('createdAt', 'desc')
        .onSnapshot(snapshot => {
          this.notifications = snapshot.docs.map(doc => ({
            id: doc.id,
            ...doc.data()
          }));
        });
    }
  },
  mounted() {
    firebase.auth().onAuthStateChanged(user => {
      if (user) {
        this.user = user;
        this.setupRealtimeListener();
      }
    });
  },
  beforeUnmount() {
    // MPA 방식이므로 자동으로 정리되지만, 명시적으로 해제 가능
    if (this.unsubscribe) {
      this.unsubscribe();
    }
  }
}).mount('#app');
```

**중요 참고 사항:**

- Vue.js의 `mounted()` 훅에서 Firebase를 바로 사용할 수 있습니다
- MPA 방식이므로 페이지 이동 시 자동으로 리스너가 정리됩니다
- 필요시 `beforeUnmount()`에서 명시적으로 리스너를 해제할 수 있습니다
- Firebase 구성은 시스템에서 자동으로 처리되므로 재초기화하지 마세요

---

## 개발 시스템 시작

### 빠른 시작 (필수 명령어)

Sonub 개발을 시작하려면 **두 개의 명령어를 실행**해야 합니다:

```bash
# 1. Docker 컨테이너 시작 (Nginx + PHP-FPM)
cd ~/apps/sonub/dev/docker
docker compose up -d

# 2. 핫 리로드 서버 시작 (자동 파일 변경 감지)
cd ~/apps/sonub
npm run dev
```

**중요**: 완전한 개발 환경을 구축하려면 두 명령어를 모두 실행해야 합니다.

- `docker compose up`: 웹 서버 및 PHP 런타임 환경 제공
- `npm run dev`: 파일 변경 시 브라우저 자동 새로고침

### Docker Compose 사용

Sonub 개발 환경은 Docker Compose를 통해 Nginx와 PHP-FPM 서비스를 실행합니다.

#### 사전 요구 사항

- Docker 및 Docker Compose 설치 필요
- 프로젝트 위치: `~/apps/sonub/`
- 로컬 개발 도메인 설정 필요 (아래 참조)

#### 빠른 시작 명령어

```bash
# Docker 디렉토리로 이동
cd ~/apps/sonub/dev/docker

# 시스템 시작 (Nginx + PHP-FPM)
docker compose up -d

# 컨테이너 상태 확인
docker compose ps

# 로그 보기
docker compose logs -f

# 시스템 중지
docker compose down

# 시스템 재시작
docker compose restart

# 고아 컨테이너 제거와 함께 시작
docker compose up -d --remove-orphans
```

#### 로컬 개발 도메인 설정

localhost/127.0.0.1 대신 로컬 개발 도메인을 사용하려면 hosts 파일에 다음 항목을 추가하세요:

**macOS/Linux의 경우:**

```bash
sudo nano /etc/hosts
# 다음 줄 추가:
127.0.0.1 local.sonub.com
```

**Windows의 경우:**

```bash
# 관리자 권한으로 편집:
# C:\Windows\System32\drivers\etc\hosts
# 다음 줄 추가:
127.0.0.1 local.sonub.com
```

이 항목을 추가한 후 다음에서 개발 사이트에 접속할 수 있습니다:

- http://local.sonub.com:8080
- https://local.sonub.com:8443

#### 기본 구성

- **웹 루트**: `~/apps/sonub`
- **HTTP 포트**: 127.0.0.1:8080
- **HTTPS 포트**: 127.0.0.1:8443
- **PHP 버전**: 8.3-fpm (커스텀 빌드)
- **Nginx 버전**: alpine (최신)
- **네트워크**: sonub-network (브리지)
- **로컬 도메인**: local.sonub.com (hosts 파일 구성 필요)

#### 디렉토리 구조

```
dev/docker/
├── compose.yaml         # Docker Compose 구성 파일
├── php.dockerfile       # PHP-FPM 커스텀 이미지
├── etc/
│   ├── nginx/
│   │   └── nginx.conf  # Nginx 메인 구성
│   └── php.ini         # PHP 구성
└── var/
    ├── log/nginx/      # Nginx 로그
    └── logs/php/       # PHP 로그
```

#### 주요 기능

- Docker Compose를 통한 간편한 서비스 관리
- 자동 Nginx 및 PHP-FPM 통합
- 볼륨 마운트를 통한 실시간 코드 반영
- 외부 로그 파일 저장

#### 문제 해결

- 포트 충돌 발생 시 compose.yaml에서 포트 번호 변경
- `docker compose logs` 명령어로 오류 확인
- ERR_UNSAFE_PORT 오류 발생 시 다른 포트(예: 8080) 사용

### 핫 리로드 개발 서버

Sonub는 파일 변경 시 브라우저를 자동으로 새로고침하는 핫 리로드 개발 서버를 제공합니다.

#### 기능

- 파일 변경 시 자동 브라우저 새로고침
- 페이지 새로고침 없이 CSS 핫 스왑
- 로컬 SSL 인증서로 HTTPS 지원
- PHP, CSS, JS 및 템플릿 파일 감시

#### 설정 및 사용

```bash
# 의존성 설치 (최초 1회만)
npm install

# 핫 리로드 서버 시작
npm run dev
```

핫 리로드 서버는:

- https://localhost:3034에서 실행 (SSL 인증서가 없으면 http)
- 모든 프로젝트 파일의 변경 사항 모니터링
- PHP 파일 변경 시 브라우저 자동 새로고침
- 전체 페이지 새로고침 없이 CSS 파일 핫 스왑
- 콘솔에 파일 변경 알림 표시

#### 구성

핫 리로드 서버는 환경 변수로 구성할 수 있습니다:

- `DOMAIN`: 개발 도메인 (기본값: localhost)
- `PORT`: 소켓 서버 포트 (기본값: 3034)
- `USE_HTTPS`: HTTPS 활성화/비활성화 (기본값: true)

#### SSL 인증서

서버는 다음 위치의 mkcert로 생성된 SSL 인증서를 사용합니다:

- `./etc/server-settings/nginx/ssl/sonub/localhost-key.pem`
- `./etc/server-settings/nginx/ssl/sonub/localhost-cert.pem`

인증서를 찾을 수 없으면 서버가 자동으로 HTTP 모드로 전환됩니다.

---

## 레이아웃 파일 구조

**⚠️⚠️⚠️ 최강력 경고: Sonub의 레이아웃 파일은 반드시 `/sonub.php`를 참조해야 한다 ⚠️⚠️⚠️**

### Sonub 메인 레이아웃 파일

- **파일 위치**: `/sonub.php` (프로젝트 루트 폴더)
- **역할**: Sonub 애플리케이션의 **인덱스 파일이자 메인 레이아웃 파일**
- **중요**: 레이아웃 관련 모든 작업은 **반드시 `/sonub.php` 파일을 참조**해야 함

### 파일 구조 설명

```
/sonub.php                    ← 🔴 인덱스 & 레이아웃 파일 (메인)
├── Header Navigation         ← 상단 네비게이션 바
├── Main Content Area         ← 3단 레이아웃 (좌/중앙/우 사이드바)
│   ├── Left Sidebar          ← 왼쪽 사이드바 (로그인, 대시보드 등)
│   ├── Main Content          ← 중앙 콘텐츠 영역 (<?php include page() ?>)
│   └── Right Sidebar         ← 오른쪽 사이드바 (활동, 통계 등)
└── Footer                    ← 하단 푸터
```

### 레이아웃 작업 시 필수 규칙

1. **✅ 레이아웃 수정 요청 시**: 개발자나 AI가 "레이아웃", "layout", "헤더", "푸터", "사이드바" 등의 표현을 사용하면 **반드시 `/sonub.php` 파일을 참조**

2. **✅ 페이지 콘텐츠 수정 시**: 실제 페이지 콘텐츠는 `/apps/sonub/` 폴더 아래의 개별 파일 수정

3. **✅ 레이아웃 포함 사항**:
   - 상단 네비게이션 (Header Navigation)
   - 좌측 사이드바 (Left Sidebar) - 로그인, 대시보드, 프로필 등
   - 우측 사이드바 (Right Sidebar) - 활동, 통계 등
   - 푸터 (Footer)
   - Bootstrap, FontAwesome, Firebase 설정

4. **✅ 콘텐츠 로딩 방식**:
   ```php
   <main class="col-12 col-md-6 col-lg-8 p-4">
       <?php include page() ?>  ← 개별 페이지 콘텐츠가 여기에 로드됨
   </main>
   ```

### 작업 예시

| 요청 내용                 | 수정할 파일                            |
| ------------------------- | -------------------------------------- |
| "레이아웃을 수정해줘"     | `/sonub.php`                           |
| "헤더를 변경해줘"         | `/sonub.php` (Header Navigation 섹션)  |
| "사이드바를 업데이트해줘" | `/sonub.php` (Left/Right Sidebar 섹션) |
| "푸터를 수정해줘"         | `/sonub.php` (Footer 섹션)             |
| "메인 페이지 콘텐츠 수정" | `/apps/sonub/index.php`                |
| "게시글 목록 페이지 수정" | `/apps/sonub/posts.php`                |

**중요 사항:**

- **절대 원칙**: "레이아웃"이라는 표현이 나오면 **무조건 `/sonub.php`**
- AI 어시스턴트도 이 규칙을 따라야 함
- 레이아웃 파일은 오직 하나: `/sonub.php`

---

## URL 및 페이지 링크 관리 규칙

**⚠️⚠️⚠️ 최강력 경고: Sonub의 모든 URL, 페이지 링크, 이동은 반드시 전용 함수를 사용해야 한다 ⚠️⚠️⚠️**

### URL 함수 필수 사용 규칙

- **파일 위치**: `lib/sonub/sonub.page_url.functions.php`
- **✅ 필수**: Sonub 관련 모든 URL, 링크, 페이지 이동은 **반드시** 이 파일의 함수만 사용
- **✅ 필수**: 하드코딩된 URL 경로 사용 절대 금지
- **✅ 필수**: 새로운 페이지 추가 시 해당 파일에 함수 추가 후 사용
- **❌ 금지**: `/sonub/profile`, `/sonub/settings` 등 직접 경로 작성 금지
- **❌ 금지**: 문자열로 URL 조합하여 사용 금지

### 제공되는 URL 함수 목록

```php
// 프로필 관련
sonub_profile_page()           // 프로필 페이지
sonub_profile_edit_page()      // 프로필 편집 페이지

// 설정 관련
sonub_settings_page()          // 설정 페이지

// 추가 페이지 함수들은 lib/sonub/sonub.page_url.functions.php 참조
```

### 올바른 URL 사용 예제

```php
<!-- 프로필 페이지 링크 -->
<a href="/<?= sonub_profile_page() ?>">프로필</a>

<!-- 프로필 편집 페이지 링크 -->
<a href="/<?= sonub_profile_edit_page() ?>">프로필 편집</a>

<!-- 설정 페이지 링크 -->
<a href="/<?= sonub_settings_page() ?>">설정</a>

<!-- JavaScript에서 사용 -->
<script>
const profileUrl = "/<?= sonub_profile_page() ?>";
window.location.href = profileUrl;
</script>
```

### 잘못된 URL 사용 예제 (절대 금지)

```php
<!-- ❌ 절대 금지 - 하드코딩된 URL -->
<a href="/sonub/profile">프로필</a>
<a href="/apps/sonub/profile">프로필</a>

<!-- ❌ 절대 금지 - 문자열 조합 -->
<a href="<?= '/sonub/' . 'profile' ?>">프로필</a>

<!-- ❌ 절대 금지 - 직접 경로 작성 -->
<script>
window.location.href = "/sonub/settings";  // 금지!
</script>
```

### 위반 시 결과

- URL 변경 시 모든 파일을 찾아 수정해야 함
- 오타로 인한 링크 오류 발생
- 유지보수 극도로 어려워짐
- 일관성 없는 URL 체계

### 새로운 페이지 추가 시 절차

1. `lib/sonub/sonub.page_url.functions.php`에 새 함수 추가
2. 함수명 규칙: `sonub_{페이지명}_page()` 형식 사용
3. 함수는 경로 문자열만 반환 (슬래시 제외)
4. 모든 코드에서 해당 함수 사용

**예시 - 새 페이지 함수 추가:**

```php
// lib/sonub/sonub.page_url.functions.php

function sonub_friends_page()
{
    return 'friends';
}

function sonub_messages_page()
{
    return 'messages';
}
```

---

## CSS 및 디자인 규칙

**⚠️⚠️⚠️ 최강력 경고: Sonub의 모든 CSS와 디자인 작업은 반드시 정해진 규칙을 따라야 한다 ⚠️⚠️⚠️**

### Sonub 기본 CSS 파일

- **파일 위치**: `apps/sonub/sonub.css`
- **역할**: Sonub 홈페이지 및 전체 페이지에서 사용되는 **기본 CSS 파일**
- **✅ 필수**: Sonub 홈페이지 관련 작업 시 **반드시** `apps/sonub/sonub.css` 파일을 참고하고 수정
- **✅ 필수**: 모든 디자인 및 UI 수정 시 이 파일의 스타일을 우선 확인
- **❌ 금지**: 인라인 스타일로 모든 디자인을 처리하는 것 금지
- **❌ 금지**: 각 페이지마다 별도의 CSS 파일을 무분별하게 생성하는 것 금지

### 디자인 작업 시 필수 규칙

**🔥🔥🔥 초강력 규칙: 레이아웃과 스타일을 명확히 구분해야 합니다 🔥🔥🔥**

#### 1️⃣ 레이아웃 포지션 관련 (인라인 클래스 사용)

- **✅ 필수**: **Bootstrap 5.3.8 유틸리티 클래스**를 `class="..."` 속성에 직접 작성
- **적용 대상**:
  - 레이아웃 (Layout): `d-flex`, `flex-column`, `flex-row` 등
  - 포지션 (Position): `position-relative`, `position-absolute` 등
  - 위치 (Alignment): `align-items-center`, `justify-content-between` 등
  - 간격 (Spacing): `gap-2`, `gap-3` 등 (레이아웃 간격)
- **이유**: 레이아웃 구조를 HTML에서 직접 확인 가능하도록 하기 위함

#### 2️⃣ 스타일 관련 (CSS 파일 사용)

- **✅ 필수**: `apps/sonub/sonub.css` 파일에 클래스를 정의하고 사용
- **적용 대상**:
  - 마진/패딩 (디자인 목적): `.custom-spacing` 등
  - 색상 (Colors): `.custom-bg`, `.text-custom` 등
  - 보더 (Borders): `.custom-border`, `.rounded-custom` 등
  - 효과 (Effects): `.shadow-custom`, `.hover-effect` 등
  - 폰트 (Typography): `.custom-font`, `.text-style` 등
- **이유**: 재사용 가능한 스타일을 중앙 집중식으로 관리하기 위함

### 올바른 CSS 사용 예제

```html
<!-- ✅ 올바른 예: 레이아웃은 인라인 클래스, 스타일은 CSS 파일 -->
<div class="d-flex flex-column align-items-center gap-3 custom-container">
  <div class="position-relative custom-card">
    <h2 class="custom-title">제목</h2>
    <p class="custom-text">내용</p>
  </div>
</div>
```

```css
/* apps/sonub/sonub.css */

/* 커스텀 컨테이너 스타일 */
.custom-container {
  background-color: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color);
  border-radius: 8px;
  padding: 20px;
}

/* 커스텀 카드 스타일 */
.custom-card {
  background-color: var(--bs-secondary-bg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 16px;
}

.custom-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* 커스텀 텍스트 스타일 */
.custom-title {
  color: var(--bs-emphasis-color);
  font-weight: bold;
  margin-bottom: 12px;
}

.custom-text {
  color: var(--bs-body-color);
  line-height: 1.6;
}
```

### 잘못된 CSS 사용 예제 (절대 금지)

```html
<!-- ❌ 절대 금지: 레이아웃과 스타일을 모두 인라인 스타일로 처리 -->
<div
  style="display: flex; flex-direction: column; align-items: center; background: white; padding: 20px; border: 1px solid #ccc;"
>
  <div
    style="position: relative; background: #f5f5f5; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 16px;"
  >
    <h2 style="color: #333; font-weight: bold; margin-bottom: 12px;">제목</h2>
    <p style="color: #666; line-height: 1.6;">내용</p>
  </div>
</div>

<!-- ❌ 절대 금지: 레이아웃을 CSS 파일로만 처리 -->
<div class="custom-layout-container">
  <div class="custom-layout-card">
    <h2 class="custom-title">제목</h2>
    <p class="custom-text">내용</p>
  </div>
</div>
```

```css
/* ❌ 절대 금지: 레이아웃을 CSS 파일에 정의 */
.custom-layout-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  /* 이런 레이아웃 속성은 Bootstrap 클래스로 처리해야 함 */
}

.custom-layout-card {
  position: relative;
  /* 포지션도 Bootstrap 클래스로 처리해야 함 */
}
```

### 위반 시 결과

- HTML 구조와 레이아웃의 분리로 인한 가독성 저하
- 레이아웃 수정 시 CSS 파일과 HTML 파일을 동시에 수정해야 하는 불편함
- Bootstrap의 반응형 유틸리티 클래스 활용 불가
- 코드 유지보수 어려움

### 작업 체크리스트

**1. 레이아웃 작업 시:**
- [ ] Bootstrap 5.3.8 유틸리티 클래스 사용 확인
- [ ] HTML `class` 속성에 직접 작성 확인
- [ ] 반응형 클래스 활용 확인 (예: `d-md-flex`, `col-lg-6`)

**2. 스타일 작업 시:**
- [ ] `apps/sonub/sonub.css` 파일에 클래스 정의 확인
- [ ] 재사용 가능한 클래스명 사용 확인
- [ ] Bootstrap CSS 변수 사용 확인 (`var(--bs-body-color)` 등)

**3. 디자인 완료 후:**
- [ ] 인라인 `style` 속성이 없는지 확인
- [ ] 레이아웃이 Bootstrap 클래스로 구성되었는지 확인
- [ ] 스타일이 CSS 파일에 정의되었는지 확인

---

## 필수 언어 사용 규칙

**⚠️⚠️⚠️ 최강력 경고: 모든 주석과 텍스트는 반드시 한국어로 작성해야 한다 ⚠️⚠️⚠️**

### 주석 및 텍스트 작성 규칙

- **✅ 필수**: 모든 PHP, JavaScript, HTML, CSS 파일의 주석은 **반드시 한국어**로 작성
- **✅ 필수**: 변수명, 함수명, 클래스명 등 코드 자체는 영문 사용 (camelCase, snake_case)
- **✅ 필수**: 주석, 설명, 문서화는 **무조건 한국어**로 작성
- **✅ 필수**: 사용자에게 표시되는 모든 텍스트는 **한국어**로 작성
- **❌ 금지**: 주석을 영어로 작성하는 것 절대 금지
- **❌ 금지**: UI 텍스트를 영어로 하드코딩하는 것 절대 금지

### 예외 사항

- 코드 자체 (변수명, 함수명, 클래스명)
- 기술 용어 (API, REST, JSON 등)
- 파일명과 경로
- 라이브러리 및 프레임워크 관련 코드

### 올바른 예제

```php
<?php
/**
 * 사용자 정보를 조회하는 함수
 *
 * @param int $user_id 사용자 ID
 * @return array 사용자 정보 배열
 */
function getUserInfo($user_id) {
    // 데이터베이스에서 사용자 정보 조회
    $user = db()->get('users', $user_id);

    // 사용자 정보가 없으면 빈 배열 반환
    if (!$user) {
        return [];
    }

    // 사용자 정보 반환
    return $user;
}
?>
```

```javascript
/**
 * 게시글 목록을 로드하는 함수
 * @param {number} page - 페이지 번호
 * @returns {Promise} 게시글 목록 프로미스
 */
function loadPosts(page) {
  // API 호출하여 게시글 목록 가져오기
  return fetch(`/api/posts?page=${page}`)
    .then((response) => response.json())
    .catch((error) => {
      // 에러 발생 시 콘솔에 출력
      console.error("게시글 로드 실패:", error);
    });
}
```

```html
<!-- 사용자 프로필 카드 -->
<div class="card">
  <div class="card-body">
    <!-- 프로필 이미지 -->
    <img src="<?= $user->photo ?>" alt="프로필 사진" />

    <!-- 사용자 이름 -->
    <h5 class="card-title"><?= $user->name ?></h5>

    <!-- 자기소개 -->
    <p class="card-text"><?= $user->bio ?></p>
  </div>
</div>
```

### 잘못된 예제 (절대 금지)

```php
<?php
/**
 * Get user information
 *
 * @param int $user_id User ID
 * @return array User information array
 */
function getUserInfo($user_id) {
    // Get user from database  ❌ 영어 주석 금지
    $user = db()->get('users', $user_id);

    // Return empty array if user not found  ❌ 영어 주석 금지
    if (!$user) {
        return [];
    }

    return $user;
}
?>
```

```html
<!-- User profile card --> ❌ 영어 주석 금지
<div class="card">
  <div class="card-body">
    <h5>Welcome</h5> ❌ 영어 UI 텍스트 금지
    <button>Click here</button> ❌ 영어 버튼 텍스트 금지
  </div>
</div>
```

### 위반 시 결과

- 코드 가독성 저하
- 팀 내 일관성 파괴
- 유지보수 어려움
- 협업 효율성 감소
