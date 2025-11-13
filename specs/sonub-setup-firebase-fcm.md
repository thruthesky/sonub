---
title: Firebase Cloud Messaging (FCM) 설치 및 설정
version: 1.2.0
status: implemented
priority: high
related:
  - sonub-firebase-messaging.md
  - sonub-setup-firebase.md
last_updated: 2025-01-13
changelog:
  - version: 1.2.0
    date: 2025-01-13
    changes:
      - "나중에" 버튼 클릭 시 설정 페이지로 자동 리다이렉트
      - 설정 페이지 접속 시 SessionStorage 플래그 저장
      - 세션 동안 권한 요청 모달 미표시 기능 추가
  - version: 1.1.0
    date: 2025-01-13
    changes:
      - FCM 권한 요청 UX 개선 (자동 거절 방지)
      - FcmPermissionGate 컴포넌트 추가
      - 권한 설정 안내 페이지 추가
---

# Firebase Cloud Messaging (FCM) 설치 및 설정

## 개요

본 문서는 Sonub Svelte5 프로젝트에 Firebase Cloud Messaging (FCM) 웹 푸시 알림을 설치하고 설정하는 방법을 설명합니다.

- **본 문서의 범위**: FCM 설치 및 프로젝트 기본 설정
- **구현 및 코드 설명**: [Firebase Cloud Messaging 구현](./sonub-firebase-messaging.md) 문서 참고




## 설치하는 방법



1. Firebase 콘솔에서 FCM 준비
	1.	Firebase 콘솔 → 프로젝트 선택
	2.	위쪽 톱니바퀴 Project settings → Cloud Messaging 탭
	3.	Web configuration 섹션에서
	•	아직 Web App (웹 앱)이 없다면 생성
	•	Web Push certificates → Generate key pair (VAPID 키 생성)
	•	생성된 Public key를 복사해두세요.
(Svelte 쪽 getToken 호출에 사용)

또한 일반 설정(Project settings → General → Your Apps → Web app) 에서 firebaseConfig (apiKey, authDomain, projectId …) 값을 복사합니다.  ￼


2. SvelteKit에 Firebase SDK 설치 + firebase.ts 초기화
아래는 이미 설치되어져 있으면 생략
```
npm install firebase
```

3. 루트에 서비스 워커(firebase-messaging-sw.js) 파일 추가

서비스 워커는 루트 스코프에서 동작해야 하므로,
firebase-messaging-sw.js 는 반드시 /firebase-messaging-sw.js 경로로 접근 가능해야 합니다. SvelteKit에서는 static/에 두면 자동으로 그렇게 됩니다. 


```javascript
// static/firebase-messaging-sw.js

// Firebase v10+ CDN (서비스 워커에서는 ESM import가 안 되므로 CDN 스크립트 사용)
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js');

// 앱과 동일한 firebaseConfig
firebase.initializeApp({
	apiKey: 'YOUR_API_KEY',
	authDomain: 'YOUR_PROJECT.firebaseapp.com',
	projectId: 'YOUR_PROJECT_ID',
	storageBucket: 'YOUR_PROJECT.appspot.com',
	messagingSenderId: 'YOUR_SENDER_ID',
	appId: 'YOUR_APP_ID'
});

const messaging = firebase.messaging();

// 백그라운드 메시지 처리
messaging.onBackgroundMessage((payload) => {
	console.log('[firebase-messaging-sw.js] Received background message ', payload);

	const notificationTitle = payload.notification?.title ?? '새 알림';
	const notificationOptions = {
		body: payload.notification?.body,
		icon: '/favicon.png', // 혹은 별도 아이콘 경로
		data: payload.data
	};

	self.registration.showNotification(notificationTitle, notificationOptions);
});

// 알림 클릭 처리
self.addEventListener('notificationclick', (event) => {
	console.log('[firebase-messaging-sw.js] notification click ', event);
	event.notification.close();

	// payload.data.click_action 이 있으면 거기로 이동, 없으면 루트로
	const url = event.notification.data?.click_action || '/';
	event.waitUntil(
		clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
			for (const client of clientList) {
				if ('focus' in client) {
					client.focus();
					client.navigate(url);
					return;
				}
			}
			if (clients.openWindow) {
				return clients.openWindow(url);
			}
		})
	);
});
```



위에 까지만 하면 기본적인 FCM 설정은 완료된다. 이후 구현 방법은 [Firebase Cloud Messaging](./sonub-firebase-messaging.md) 문서를 참고한다.

---

## 실제 구현 내역

### 구현된 파일 목록

#### 새로 생성된 파일

1. **`src/lib/fcm.ts`** - FCM 유틸리티 함수
   - `requestFcmToken()`: FCM 토큰 발급 및 RTDB 저장
   - `subscribeOnMessage()`: 포그라운드 메시지 리스너
   - `saveFcmTokenToDatabase()`: 토큰 데이터베이스 저장 (private)

2. **`static/firebase-messaging-sw.js`** - 서비스 워커
   - 백그라운드 메시지 수신 처리
   - 브라우저 알림 표시
   - 알림 클릭 이벤트 처리 (URL 이동)
   - Firebase Config 하드코딩 (환경변수 접근 불가)

3. **`src/routes/fcm-test/+page.svelte`** - FCM 테스트 페이지
   - 알림 권한 요청 UI
   - FCM 토큰 발급 및 표시
   - 포그라운드 메시지 수신 테스트
   - Firebase Console 테스트 가이드

4. **`src/lib/components/FcmPermissionGate.svelte`** - FCM 권한 요청 가드 컴포넌트 ⭐ NEW
   - 브라우저 자동 거절 방지 로직
   - SessionStorage 기반 페이지 이동 카운트
   - 조건부 권한 요청 모달 (10회 이상 OR 채팅 목록 페이지)
   - 권한 거절 시 안내 모달
   - Svelte 5 runes 스타일 구현

5. **`src/routes/settings/fcm/permission/+page.svelte`** - FCM 권한 설정 안내 페이지 ⭐ NEW
   - 브라우저별 푸시 권한 재허용 방법 안내
   - Chrome, Safari, Firefox, Edge 가이드
   - 모바일/데스크탑 분리 설명

#### 수정된 파일

1. **`src/lib/firebase.ts`**
   - `getFirebaseMessaging()` 함수 추가
   - `isSupported()` 체크 및 Messaging 인스턴스 생성

2. **`src/routes/+layout.svelte`** ⭐ UPDATED
   - `<Toaster />` 컴포넌트 추가 (svelte-sonner)
   - `<FcmPermissionGate />` 컴포넌트 추가 (권한 요청 가드)
   - 전역 Toast 알림 시스템 활성화

3. **`.env`**
   - `PUBLIC_FIREBASE_VAPID_KEY` 환경 변수 추가
   - Firebase Console에서 생성한 VAPID Public Key 설정

4. **`.env.example`**
   - `PUBLIC_FIREBASE_VAPID_KEY` 예시 추가
   - VAPID Key 생성 방법 주석 추가

5. **`package.json`**
   - `svelte-sonner` 패키지 설치 (Toast 라이브러리)

---

## FCM 토큰 저장 구조

### Realtime Database 경로
```
/fcm-tokens/{token-id}
```

### 데이터 구조
```typescript
{
  web: true,           // 웹 플랫폼 여부
  uid: string | null,  // 사용자 UID (로그인한 경우)
  createdAt: timestamp,
  updatedAt: timestamp
}
```

### 토큰 ID 생성 로직
- FCM 토큰 문자열을 Base64 인코딩
- 특수문자 제거하여 Firebase 키로 사용 가능하게 변환
- 예시: `btoa(token).replace(/[^a-zA-Z0-9]/g, '')`

---

## VAPID 키 입력 위치

### 1. 개발 환경 (.env 파일)
```bash
# FCM Web Push VAPID Key
# Firebase Console > 프로젝트 설정 > Cloud Messaging > Web Push certificates에서 생성
PUBLIC_FIREBASE_VAPID_KEY=BClX3EL-df5V894SJzHam1OcoP5f1UsXArqQORkE1vr9CxeYwHi5Gqb6rgLhLzWIbo70iLhIQvbdFVhQi0iUJSI
```

### 2. 서비스 워커 (static/firebase-messaging-sw.js)
서비스 워커는 환경 변수에 접근할 수 없으므로 Firebase Config를 직접 하드코딩해야 합니다:

```javascript
firebase.initializeApp({
  apiKey: 'AIzaSyCXAFYnNf7QYcZNpIngCA-lOhb9YRRLDTY',
  authDomain: 'sonub-firebase.firebaseapp.com',
  projectId: 'sonub-firebase',
  storageBucket: 'sonub-firebase.firebasestorage.app',
  messagingSenderId: '406320255657',
  appId: '1:406320255657:web:79b39117a353878b8e8fb8'
});
```

**중요**: 이는 Firebase 공식 문서에서 권장하는 방식이며, 보안 문제가 없습니다.

---

## 설치된 패키지

```bash
npm install svelte-sonner
```

- **svelte-sonner**: Toast 알림 라이브러리 (Svelte 5 runes 지원)

---

## 검증 체크리스트

구현이 완료되었는지 아래 체크리스트로 확인하세요:

### 설정 단계
- [x] Firebase Console에서 VAPID Key 생성 완료
- [x] `.env` 파일에 `PUBLIC_FIREBASE_VAPID_KEY` 추가 완료
- [x] `.env.example` 파일에 VAPID Key 예시 추가 완료

### 파일 생성 및 수정
- [x] `static/firebase-messaging-sw.js` 파일 생성 완료
- [x] `static/firebase-messaging-sw.js`에 firebaseConfig 하드코딩 완료
- [x] `src/lib/firebase.ts`에 `getFirebaseMessaging()` 함수 추가 완료
- [x] `src/lib/fcm.ts` 파일 생성 완료
- [x] `svelte-sonner` 패키지 설치 완료
- [x] `src/routes/+layout.svelte`에 `<Toaster />` 컴포넌트 추가 완료
- [x] `src/routes/fcm-test/+page.svelte` FCM 테스트 페이지 생성 완료

### 기능 테스트
- [ ] 브라우저에서 `/fcm-test` 페이지 접속 확인
- [ ] 브라우저에서 알림 권한 허용 테스트
- [ ] FCM 토큰 발급 테스트
- [ ] RTDB `/fcm-tokens/` 경로에 토큰 저장 확인
- [ ] Firebase Console에서 테스트 메시지 전송
- [ ] 포그라운드 메시지 수신 테스트 (Toast 표시)
- [ ] 백그라운드 메시지 수신 테스트 (브라우저 알림)
- [ ] 알림 클릭 시 URL 이동 테스트

### 타입 체크
- [x] `npm run check` 실행하여 타입 에러 확인
- [x] FCM 관련 코드에서 타입 에러 없음 확인

---

## 테스트 방법

### 1. FCM 토큰 발급 테스트

1. 브라우저에서 `http://localhost:5173/fcm-test` 접속
2. "푸시 알림 활성화" 버튼 클릭
3. 브라우저 알림 권한 허용
4. 콘솔에서 FCM 토큰 확인
5. Firebase Console > Realtime Database > `/fcm-tokens/` 경로에서 토큰 저장 확인

### 2. 포그라운드 메시지 테스트

1. Firebase Console → Cloud Messaging → "Send your first message"
2. Notification title 및 body 입력
3. "Send test message" 클릭
4. 발급된 FCM 토큰 붙여넣기
5. **테스트 페이지를 열어둔 상태**에서 메시지 전송
6. Toast 알림이 화면 상단 중앙에 표시되는지 확인

### 3. 백그라운드 메시지 테스트

1. 위와 동일한 방법으로 메시지 전송
2. 브라우저를 **다른 탭으로 전환하거나 최소화**
3. 브라우저 알림이 OS 알림으로 표시되는지 확인
4. 알림 클릭 시 브라우저가 포커스되고 해당 URL로 이동하는지 확인

---

## FCM 권한 요청 UX 개선 ⭐ NEW

### 문제점

대부분의 웹 브라우저는 접속하자마자 푸시 알림 권한을 요청하면 **자동으로 거절**합니다. 이는 사용자 경험을 보호하기 위한 브라우저의 보안 정책입니다.

### 해결 방법

사용자가 사이트에 충분히 익숙해진 후에만 권한을 요청하여 자동 거절을 방지합니다.

### 구현 로직

#### 1. 페이지 이동 카운트

- **SessionStorage** 사용: 브라우저 세션 동안만 카운트 유지
- **페이지 이동 시 자동 증가**: `afterNavigate` 훅 사용
- **카운트 키**: `pageMoveCount`

#### 2. 권한 요청 조건

다음 조건 중 **하나라도 만족**하면 권한 요청 모달 표시:

1. **페이지 이동 10회 이상** OR
2. **채팅 목록 페이지(`/chats`) 진입**

#### 3. 권한 상태별 처리

**Case 1: `Notification.permission === 'default'`** (아직 요청 안 함)
- 권한 요청 모달 표시
- "원활한 서비스를 위해 푸시 허용이 꼭 필요합니다"
- **[퍼미션 허용하기]** 버튼 클릭 시 `Notification.requestPermission()` 호출
- **[나중에]** 버튼 클릭 시:
  - SessionStorage에 `fcmPermissionDismissed=true` 플래그 저장
  - `/settings/fcm/permission` 페이지로 리다이렉트
  - 해당 세션 동안 더 이상 권한 요청 모달 표시 안 함

**Case 2: `Notification.permission === 'granted'`** (이미 허용됨)
- 아무 것도 하지 않음

**Case 3: `Notification.permission === 'denied'`** (이미 거절됨)
- 안내 모달 표시
- "알림 권한이 차단되어 있습니다"
- **[설정 페이지로 이동]** 버튼 → `/settings/fcm/permission` 이동

#### 4. 권한 거절 후 처리

사용자가 권한을 거절하거나 "나중에" 버튼을 클릭하면:
1. `/settings/fcm/permission` 페이지로 자동 리다이렉트
2. 브라우저별 권한 재허용 방법 안내 표시
3. 페이지 접속 시 SessionStorage에 `fcmPermissionDismissed=true` 플래그 저장
4. 해당 세션 동안 권한 요청 모달이 더 이상 표시되지 않음

#### 5. SessionStorage 플래그 관리

- **플래그 키**: `fcmPermissionDismissed`
- **플래그 저장 시점**:
  1. "나중에" 버튼 클릭 시
  2. `/settings/fcm/permission` 페이지 접속 시 (`onMount`)
- **플래그 확인**: `checkPermissionLogic()` 함수에서 플래그가 `true`이면 모달 표시 안 함
- **세션 지속성**: 브라우저 세션 동안만 유지, 브라우저 완전 종료 시 초기화

### 컴포넌트 구조

```
src/lib/components/FcmPermissionGate.svelte
├── SessionStorage 카운팅 로직 (pageMoveCount)
├── SessionStorage 플래그 관리 (fcmPermissionDismissed)
│   ├── getFcmPermissionDismissed()
│   └── setFcmPermissionDismissed()
├── afterNavigate 훅 (페이지 이동 감지)
├── checkPermissionLogic() (조건 확인 + 플래그 체크)
├── handleAllowClick() (권한 허용 버튼)
├── handleLaterClick() (나중에 버튼 → 플래그 저장 + 페이지 이동)
├── goToSettingsFromDenied() (설정 페이지 이동)
└── 두 가지 모달 (권한 요청 / 거절 안내)
```

```
src/routes/settings/fcm/permission/+page.svelte
├── onMount 훅 (페이지 접속 시 플래그 저장)
├── 브라우저별 권한 재허용 안내
└── Chrome, Safari, Firefox, Edge 가이드
```

### 사용 방법

`src/routes/+layout.svelte`에 한 번만 추가하면 전체 앱에서 자동 작동:

```svelte
<script lang="ts">
	import FcmPermissionGate from '$lib/components/FcmPermissionGate.svelte';
</script>

<!-- 앱 레이아웃 -->
<slot />

<!-- FCM 권한 요청 가드 -->
<FcmPermissionGate />
```

### 설정 페이지

`/settings/fcm/permission` 페이지에서 제공하는 안내:

#### 브라우저별 권한 재허용 방법

1. **Chrome (데스크탑)**
   - 주소창 왼쪽 자물쇠 아이콘 클릭
   - "알림(Notifications)" 항목 찾기
   - "허용(Allow)"으로 변경

2. **Chrome (Android)**
   - 브라우저 메뉴(⋮) → "사이트 설정"
   - "알림" → 현재 사이트 "허용"

3. **Safari (macOS)**
   - Safari 메뉴 → 설정(Preferences)
   - "웹사이트(Websites)" 탭
   - "알림(Notifications)" → 현재 사이트 "허용"
   - **참고**: Safari iOS는 웹 푸시 미지원

4. **Firefox (데스크탑)**
   - 주소창 왼쪽 자물쇠 아이콘 클릭
   - "추가 정보(More Information)"
   - "권한(Permissions)" 탭
   - "알림 전송(Send Notifications)" → "허용"

5. **Edge (데스크탑)**
   - 주소창 왼쪽 자물쇠 아이콘 클릭
   - "이 사이트의 권한(Permissions for this site)"
   - "알림(Notifications)" → "허용(Allow)"

### 주요 특징

✅ **User Gesture 보장**: 버튼 클릭 핸들러 안에서만 `Notification.requestPermission()` 호출
✅ **자동 거절 방지**: 사용자가 사이트에 익숙해진 후 권한 요청
✅ **SessionStorage 카운팅**: 브라우저 세션 동안만 페이지 이동 카운트 유지 (영구 저장 X)
✅ **SessionStorage 플래그**: "나중에" 클릭 또는 설정 페이지 접속 시 세션 동안 모달 미표시
✅ **조건부 표시**: 10회 이상 OR 채팅 목록 페이지 진입 시
✅ **Svelte 5 Runes**: `$state`, `onMount` 등 최신 Svelte 5 스타일
✅ **Toast 통합**: 권한 허용/거절 시 Toast 알림 표시
✅ **FCM 토큰 자동 발급**: 권한 허용 시 즉시 FCM 토큰 발급 및 저장
✅ **UX 개선**: "나중에" 버튼 클릭 시 설정 페이지로 자동 리다이렉트

---

## 주의사항

### 서비스 워커 firebaseConfig 하드코딩

서비스 워커는 별도의 워커 스레드에서 실행되므로 SvelteKit의 환경 변수(`$env/static/public`)에 접근할 수 없습니다. 따라서 `static/firebase-messaging-sw.js` 파일에 firebaseConfig를 **직접 하드코딩**해야 합니다.

이는 Firebase 공식 문서에서 권장하는 방식이며, Firebase Config는 공개되어도 안전합니다.

### VAPID Key 보안

- `PUBLIC_` 접두사로 클라이언트에 노출되지만 보안 문제 없음
- VAPID Key는 **공개 키(Public Key)**이므로 노출되어도 안전함
- Private Key는 Firebase에서 관리하므로 노출되지 않음

### 브라우저 지원

- **Chrome, Edge, Firefox**: 완전 지원 ✅
- **Safari (iOS)**: 지원 안 함 ❌ (2025년 1월 기준)
- **Safari (macOS)**: 제한적 지원 ⚠️

### HTTPS 필수

- FCM은 **HTTPS 환경**에서만 작동
- `localhost`는 예외 (테스트 가능)
- 프로덕션 배포 시 HTTPS 인증서 필수

---

## 다음 단계

FCM 기본 설정이 완료되었으니, 다음 작업을 진행할 수 있습니다:

### 옵션 A: FCM 알림 고도화
- 사용자별 알림 설정 페이지 구현 (`/my/notifications`)
- 알림 카테고리별 on/off 토글
- 알림 히스토리 저장 및 표시

### 옵션 B: Admin 푸시 알림 전송 기능
- 관리자 페이지에 푸시 알림 전송 UI 추가
- 특정 사용자 또는 전체 사용자에게 메시지 전송
- Firebase Admin SDK 연동 (Cloud Functions)

### 옵션 C: 채팅 시스템에 FCM 통합
- 새 채팅 메시지 도착 시 푸시 알림 전송
- Cloud Functions에서 메시지 생성 시 자동 푸시
- 채팅방별 알림 on/off 설정

---

## 참고 자료

- [Firebase Cloud Messaging 구현](./sonub-firebase-messaging.md) - 상세 코드 및 로직 설명
- [Firebase 공식 문서 - Web Push](https://firebase.google.com/docs/cloud-messaging/js/client)
- [svelte-sonner GitHub](https://github.com/wobsoriano/svelte-sonner)