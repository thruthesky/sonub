

---
title: Firebase Cloud Messaging (FCM) 구현 가이드
version: 1.4.0
status: implemented
priority: high
related:
  - sonub-setup-firebase-fcm.md
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
last_updated: 2025-01-13
changelog:
  - version: 1.4.0
    date: 2025-01-13
    changes:
      - FCM 토큰 저장 로직 개선 - 로그인 안 된 사용자도 device만으로 토큰 저장 가능
      - uid 필드를 optional로 변경 (로그인 시에만 uid 추가)
      - updateFcmTokenWithUid() 함수 추가 - 로그인 시 자동으로 기존 토큰에 uid 업데이트
      - AuthStore에 FCM 토큰 자동 업데이트 로직 통합 (onAuthStateChanged)
      - 동적 import로 순환 의존성 방지 (fcm.ts ↔ auth.svelte.ts)
      - 풍부한 console.log 디버깅 추가 (이모지 접두사, 단계별 로깅, 에러 상세 정보)
      - saveFcmTokenToDatabase()와 requestFcmToken()에 디버깅 로그 대폭 강화
  - version: 1.3.0
    date: 2025-01-13
    changes:
      - FCM 토큰 저장 로직을 데이터베이스 스펙에 맞게 수정 (device: "web", 토큰 문자열을 키로 사용)
      - 서비스 워커 등록 타이밍 개선 (앱 시작 시 미리 등록, active 상태 대기)
      - registerServiceWorker() 함수 추가로 서비스 워커 활성화 대기 기능 구현
      - requestFcmToken()에서 이미 등록된 서비스 워커 재사용 로직 추가
      - +layout.svelte에서 onMount 시 서비스 워커 미리 등록하여 "no active Service Worker" 에러 해결
      - Database null 체크 추가 (채팅 목록 페이지 3개)
      - CHAT_LIST_PATH 제거 (권한 조건 단순화)
  - version: 1.2.0
    date: 2025-01-13
    changes:
      - "나중에" 버튼 클릭 시 설정 페이지로 자동 리다이렉트
      - 설정 페이지 접속 시 SessionStorage 플래그 저장
      - 세션 동안 권한 요청 모달 미표시 기능 추가
      - SessionStorage 플래그 관리 로직 구현 코드 추가
  - version: 1.1.0
    date: 2025-01-13
    changes:
      - FCM 권한 요청 UX 개선 (자동 거절 방지)
      - FcmPermissionGate 컴포넌트 구현 코드 추가
      - 권한 설정 안내 페이지 구현 코드 추가
---

# Firebase Cloud Messaging (FCM) 구현 가이드

# 참고 문서

- [Web Push 알림 : Getting Started with FCM Push messaging (Firebase)](https://firebase.google.com/docs/cloud-messaging/get-started?platform=web)
- [Web: Receive messages using Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging/receive-messages?platform=web)
- [@firebase/messaging](https://firebase.google.com/docs/reference/js/messaging_)
- [FcmOptions interface](https://firebase.google.com/docs/reference/js/messaging_.fcmoptions)
- [GetTokenOptions interface](https://firebase.google.com/docs/reference/js/messaging_.gettokenoptions)
- [MessagingPaylod interface](https://firebase.google.com/docs/reference/js/messaging_.messagepayload)
- [Messaging interface](https://firebase.google.com/docs/reference/js/messaging_.messaging)
- [NotificatoinPayload interface](https://firebase.google.com/docs/reference/js/messaging_.notificationpayload)


# 기본 설정 방법

브라우저 Web Push(PWA/브라우저 탭) 알림을 사용할 경우

반드시 VAPID Key(웹 푸시 인증서)가 필요합니다.

Chrome, Edge, Firefox 등 브라우저의 Web Push API는 보안상 VAPID 공개키를 요구합니다.

Firebase에서도 이를 위해 "Web Push certificates"에서 VAPID Key를 생성하도록 제공하고 있음.

보통 아래처럼 messaging.getToken()에서 vapidKey를 넣어줘야 합니다:

```javascript
const token = await getToken(messaging, { vapidKey: "YOUR_PUBLIC_VAPID_KEY" });
```



만약, capacitor, cordova, react-native, flutter 등은 웹이 아니고, 브라우저 Push가 아니므로 VAPID Key 불필요합니다.




# 기본 코드

클라이언트(Svelte 컴포넌트)에서
	•	알림 권한 요청
	•	getToken() 으로 FCM 토큰 발급
	•	onMessage()로 포그라운드 메시지 처리
	6.	서버에서 Admin SDK / FCM API / 콘솔로 테스트 푸시 전송



 공용 Firebase 초기화 파일 만들기

src/lib/firebase.ts (또는 .js) 파일을 하나 만들어서, 브라우저에서만 messaging을 가져오도록 합니다. (SvelteKit은 서버/클라이언트 모두에서 코드가 실행되므로 보호 필요)

-----


// src/lib/firebase.ts
import { browser } from '$app/environment';
import { initializeApp, getApps, type FirebaseApp } from 'firebase/app';
import { getMessaging, type Messaging, isSupported } from 'firebase/messaging';

const firebaseConfig = {
	apiKey: 'YOUR_API_KEY',
	authDomain: 'YOUR_PROJECT.firebaseapp.com',
	projectId: 'YOUR_PROJECT_ID',
	storageBucket: 'YOUR_PROJECT.appspot.com',
	messagingSenderId: 'YOUR_SENDER_ID',
	appId: 'YOUR_APP_ID'
	// measurementId 등 필요하면 추가
};

let app: FirebaseApp | null = null;
let messaging: Messaging | null = null;

export function getFirebaseApp(): FirebaseApp | null {
	if (!browser) return null;
	if (!getApps().length) {
		app = initializeApp(firebaseConfig);
	} else {
		app = getApps()[0]!;
	}
	return app;
}

export async function getFirebaseMessaging(): Promise<Messaging | null> {
	if (!browser) return null;

	// Safari 등 일부 브라우저는 messaging 미지원일 수 있음
	const supported = await isSupported();
	if (!supported) return null;

	if (!app) getFirebaseApp();
	if (!app) return null;

	if (!messaging) {
		messaging = getMessaging(app);
	}
	return messaging;
}

----
클라이언트에서 토큰 발급 & 권한 요청

예제로 src/routes/+page.svelte에서 버튼을 눌러 토큰을 요청하는 코드를 만들어봅시다.

5-1. 토큰 요청 유틸 (브라우저 전용)
----

// src/lib/fcm.ts
import { browser } from '$app/environment';
import { getFirebaseMessaging } from './firebase';
import { getToken, onMessage, type Messaging } from 'firebase/messaging';

const VAPID_KEY = 'YOUR_WEB_PUSH_PUBLIC_VAPID_KEY'; // 콘솔에서 복사한 값

export async function requestFcmToken(): Promise<string | null> {
	if (!browser) return null;

	const messaging = await getFirebaseMessaging();
	if (!messaging) {
		console.warn('Firebase messaging is not supported in this browser.');
		return null;
	}

	// 서비스 워커 수동 등록 (선택) – 안 해도 default로 firebase-messaging-sw.js를 찾습니다.
	const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

	const token = await getToken(messaging as Messaging, {
		vapidKey: VAPID_KEY,
		serviceWorkerRegistration: registration
	});

	if (!token) {
		console.warn('No registration token available. Request permission to generate one.');
		return null;
	}

	// TODO: 이 토큰을 서버(백엔드)에 저장해서 특정 유저에게 푸시를 보낼 수 있게 합니다.
	console.log('FCM Token:', token);
	return token;
}

export async function subscribeOnMessage(callback: (payload: any) => void) {
	if (!browser) return;

	const messaging = await getFirebaseMessaging();
	if (!messaging) return;

	onMessage(messaging, (payload) => {
		console.log('Message received in foreground: ', payload);
		callback(payload);
	});
}
----
getToken 옵션으로 vapidKey와 serviceWorkerRegistration을 넘길 수 있습니다. 넘기지 않으면 기본 스코프의 서비스 워커를 찾습니다.  ￼

5-2. Svelte 5 컴포넌트 예제 (+page.svelte)
-----
<script lang="ts">
	import { onMount } from 'svelte';
	import { requestFcmToken, subscribeOnMessage } from '$lib/fcm';

	let token: string | null = null;
	let lastMessage: any = null;
	let permission: NotificationPermission | 'unknown' = 'unknown';

	onMount(() => {
		if (typeof Notification === 'undefined') {
			permission = 'unknown';
			return;
		}
		permission = Notification.permission;

		// 포그라운드 메시지 수신
		subscribeOnMessage((payload) => {
			lastMessage = payload;
			// 필요하면 여기서 toast 띄우기 등 UI 처리
			alert(`새 알림: ${payload.notification?.title ?? '제목 없음'}`);
		});
	});

	async function enablePush() {
		if (typeof Notification === 'undefined') {
			alert('이 브라우저는 Notification API를 지원하지 않습니다.');
			return;
		}

		// 1. 브라우저 알림 권한 요청
		const result = await Notification.requestPermission();
		permission = result;

		if (result !== 'granted') {
			alert('알림 권한이 허용되지 않았습니다.');
			return;
		}

		// 2. FCM 토큰 발급
		token = await requestFcmToken();
		if (token) {
			alert('푸시 알림이 활성화되었습니다. 콘솔을 확인해 주세요.');
		}
	}
</script>

<h1 class="text-2xl font-bold mb-4">Firebase Cloud Messaging Demo</h1>

<p class="mb-2">현재 알림 권한: {permission}</p>

<button class="px-4 py-2 rounded bg-blue-500 text-white" on:click={enablePush}>
	푸시 알림 활성화
</button>

{#if token}
	<div class="mt-4">
		<h2 class="font-semibold">FCM Token</h2>
		<pre class="text-xs break-all bg-gray-100 p-2 rounded">{token}</pre>
	</div>
{/if}

{#if lastMessage}
	<div class="mt-4">
		<h2 class="font-semibold">마지막 메시지 payload</h2>
		<pre class="text-xs bg-gray-100 p-2 rounded">
{JSON.stringify(lastMessage, null, 2)}
		</pre>
	</div>
{/if}

-----


6. 테스트 푸시 보내기

(1) Firebase 콘솔에서 보내기 (가장 쉬움)
	1.	Firebase 콘솔 → Cloud Messaging
	2.	Send your first message 또는 “New notification”
	3.	Notification title/body 입력
	4.	Add FCM registration token을 선택하고
	•	앞에서 콘솔에 찍은 토큰을 붙여넣기
	5.	Send message 클릭

브라우저 탭이 열려 있고 포그라운드면 onMessage로 들어오고,
다른 탭이거나 브라우저가 백그라운드면 서비스 워커의 onBackgroundMessage에서 알림이 뜹니다.

----


7. Svelte 5 / SvelteKit에서 자주 나오는 문제들
	1.	서비스 워커에서 ESM import 사용
	•	static/firebase-messaging-sw.js는 일반 JS로 취급되므로 import { initializeApp } from 'firebase/app' 같은 ESM import를 쓰면 에러납니다.
	•	위에서처럼 importScripts + firebase-*-compat.js 방식으로 처리하는 게 편합니다.  ￼
	2.	SSR에서 window / Notification 참조 에러
	•	항상 browser 플래그 ($app/environment) 나 typeof window !== 'undefined' 체크 후 사용.
	3.	토큰이 계속 null
	•	알림 권한이 granted인지 확인
	•	VAPID_KEY가 올바른지, 콘솔에서 복사한 값인지 확인
	•	서비스 워커가 정상 등록되었는지 (navigator.serviceWorker.getRegistration('/firebase-messaging-sw.js'))
	4.	localhost 테스트 한계
	•	크롬에서는 localhost도 보안 컨텍스트로 취급하므로 테스트 가능하지만,
	•	브라우저별로 푸시/알림 정책이 조금씩 다르니 여러 브라우저에서 확인 필요합니다.

---

## 실제 구현 코드

Sonub 프로젝트에 구현된 FCM 코드를 설명합니다.

### 1. Firebase Messaging 초기화

**파일**: `src/lib/firebase.ts`

```typescript
import {
	getMessaging,
	isSupported as isMessagingSupported,
	type Messaging
} from 'firebase/messaging';

let messaging: Messaging | null = null;

/**
 * Firebase Messaging 인스턴스를 가져오는 비동기 함수
 * @returns {Promise<Messaging | null>} Messaging 인스턴스 또는 null
 */
export async function getFirebaseMessaging(): Promise<Messaging | null> {
	// 서버 환경에서는 null 반환
	if (!browser) {
		console.warn('[FCM] 서버 환경에서는 Messaging을 사용할 수 없습니다.');
		return null;
	}

	// 이미 초기화된 경우 기존 인스턴스 반환
	if (messaging) {
		return messaging;
	}

	try {
		// 브라우저가 FCM을 지원하는지 체크
		const supported = await isMessagingSupported();

		if (!supported) {
			console.warn('[FCM] 이 브라우저는 Firebase Cloud Messaging을 지원하지 않습니다.');
			return null;
		}

		// Messaging 인스턴스 생성
		messaging = getMessaging(app);
		console.log('✅ Firebase Messaging 초기화 완료');

		return messaging;
	} catch (error) {
		console.error('[FCM] Messaging 초기화 실패:', error);
		return null;
	}
}
```

**설명**:
- `isSupported()`: 브라우저가 FCM을 지원하는지 체크 (Safari iOS는 지원 안 함)
- `getMessaging()`: Messaging 인스턴스 생성
- 싱글톤 패턴으로 한 번만 초기화

---

### 2. FCM 유틸리티 함수

**파일**: `src/lib/fcm.ts`

#### 2.0 서비스 워커 미리 등록 ⭐ NEW (v1.3.0)

앱 시작 시 서비스 워커를 미리 등록하고 active 상태를 대기합니다.
이렇게 하면 권한 요청 시 "no active Service Worker" 에러를 방지할 수 있습니다.

**사용 위치**: `src/routes/+layout.svelte`의 `onMount`

```typescript
/**
 * 서비스 워커를 미리 등록하고 active 상태를 대기하는 함수
 */
export async function registerServiceWorker(): Promise<ServiceWorkerRegistration | null> {
	if (!browser) {
		console.warn('[FCM] 서버 환경에서는 서비스 워커를 등록할 수 없습니다.');
		return null;
	}

	if (!('serviceWorker' in navigator)) {
		console.warn('[FCM] 이 브라우저는 서비스 워커를 지원하지 않습니다.');
		return null;
	}

	try {
		// 서비스 워커 등록
		const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
			scope: '/'
		});
		console.log('[FCM] 서비스 워커 등록 완료:', registration);

		// 서비스 워커가 active 상태가 될 때까지 대기
		if (registration.installing) {
			console.log('[FCM] 서비스 워커 설치 중...');
			await new Promise<void>((resolve) => {
				registration.installing!.addEventListener('statechange', (e) => {
					const worker = e.target as ServiceWorker;
					if (worker.state === 'activated') {
						console.log('[FCM] 서비스 워커 활성화 완료');
						resolve();
					}
				});
			});
		} else if (registration.waiting) {
			console.log('[FCM] 서비스 워커 대기 중...');
			registration.waiting.postMessage({ type: 'SKIP_WAITING' });
			await navigator.serviceWorker.ready;
		} else if (registration.active) {
			console.log('[FCM] 서비스 워커 이미 활성화됨');
		}

		// 서비스 워커가 완전히 준비될 때까지 대기
		await navigator.serviceWorker.ready;
		console.log('[FCM] 서비스 워커 준비 완료');

		return registration;
	} catch (error) {
		console.error('[FCM] 서비스 워커 등록 실패:', error);
		return null;
	}
}
```

**+layout.svelte에서 사용**:

```typescript
import { onMount } from 'svelte';
import { registerServiceWorker } from '$lib/fcm';

onMount(async () => {
	await registerServiceWorker();
});
```

**주요 기능**:
- 앱 시작 시 서비스 워커를 미리 등록
- installing, waiting, active 상태를 감지하여 완전히 활성화될 때까지 대기
- `navigator.serviceWorker.ready`로 준비 완료 확인
- 권한 요청 시 이미 active 상태인 서비스 워커를 사용할 수 있음

#### 2.1 FCM 토큰 발급 및 저장 (v1.3.0 업데이트)

이미 등록된 서비스 워커를 재사용하고, 없으면 새로 등록합니다.

```typescript
/**
 * FCM 토큰을 발급받고 Realtime Database에 저장하는 함수
 */
export async function requestFcmToken(): Promise<string | null> {
	if (!browser) return null;

	const messaging = await getFirebaseMessaging();
	if (!messaging) return null;

	if (!PUBLIC_FIREBASE_VAPID_KEY) {
		console.error('[FCM] VAPID Key가 환경 변수에 설정되지 않았습니다.');
		return null;
	}

	try {
		// 이미 등록된 서비스 워커 사용, 없으면 새로 등록
		let registration: ServiceWorkerRegistration | undefined;

		if ('serviceWorker' in navigator) {
			// 이미 등록된 서비스 워커가 있는지 확인
			const existingRegistration = await navigator.serviceWorker.getRegistration('/');

			if (existingRegistration) {
				console.log('[FCM] 기존 서비스 워커 사용:', existingRegistration);
				registration = existingRegistration;
				// 서비스 워커가 준비될 때까지 대기
				await navigator.serviceWorker.ready;
			} else {
				// 없으면 새로 등록하고 활성화 대기
				console.log('[FCM] 서비스 워커를 새로 등록합니다...');
				registration = await registerServiceWorker() ?? undefined;
			}
		}

		if (!registration) {
			console.error('[FCM] 서비스 워커를 사용할 수 없습니다.');
			return null;
		}

		// FCM 토큰 발급
		const token = await getToken(messaging, {
			vapidKey: PUBLIC_FIREBASE_VAPID_KEY,
			serviceWorkerRegistration: registration
		});

		if (!token) {
			console.warn('[FCM] 토큰을 발급받지 못했습니다. 알림 권한을 확인하세요.');
			return null;
		}

		console.log('✅ FCM 토큰 발급 완료:', token);

		// Realtime Database에 토큰 저장
		await saveFcmTokenToDatabase(token);

		return token;
	} catch (error) {
		console.error('[FCM] 토큰 발급 실패:', error);
		return null;
	}
}
```

**주요 기능**:
- **v1.3.0**: 이미 등록된 서비스 워커 재사용 (없으면 `registerServiceWorker()` 호출)
- **v1.3.0**: 서비스 워커가 준비될 때까지 대기 (`navigator.serviceWorker.ready`)
- VAPID Key 사용하여 토큰 발급
- Realtime Database에 토큰 저장

**변경 사항**:
- 기존: 항상 새로 서비스 워커 등록 → active 되기 전에 토큰 요청 시 에러
- 개선: 이미 등록된 서비스 워커 재사용 → "no active Service Worker" 에러 방지

#### 2.2 토큰 데이터베이스 저장 (v1.4.0 업데이트)

데이터베이스 구조 스펙(`sonub-firebase-database-structure.md`)에 맞게 수정되었으며, 로그인 안 된 사용자도 토큰 저장이 가능하도록 개선되었습니다.

```typescript
/**
 * FCM 토큰을 Realtime Database에 저장하는 함수
 *
 * 저장 경로: /fcm-tokens/{tokenId}
 * 데이터 구조:
 * {
 *   device: "web" | "android" | "ios",
 *   uid?: string (로그인된 경우에만)
 * }
 *
 * 참고: specs/sonub-firebase-database-structure.md - FCM 토큰 (fcm-tokens) 섹션
 */
async function saveFcmTokenToDatabase(token: string): Promise<void> {
	console.log('[FCM 저장] 🔵 토큰 저장 시작');
	console.log('[FCM 저장] 토큰 (앞 20자):', token.substring(0, 20) + '...');

	if (!rtdb) {
		console.error('[FCM 저장] ❌ Realtime Database가 초기화되지 않았습니다.');
		console.error('[FCM 저장] rtdb 값:', rtdb);
		return;
	}
	console.log('[FCM 저장] ✅ Realtime Database 확인됨');

	// 현재 로그인된 사용자 UID 가져오기
	const uid = auth?.currentUser?.uid;
	console.log('[FCM 저장] 현재 사용자 UID:', uid || '(로그인 안 됨)');

	// 저장할 데이터 (로그인 여부와 관계없이 device는 항상 저장)
	const tokenData: { device: string; uid?: string } = {
		device: 'web' // 웹 환경에서는 항상 "web"
	};

	// 로그인한 경우에만 uid 추가
	if (uid) {
		tokenData.uid = uid;
		console.log('[FCM 저장] ✅ UID 추가됨:', uid);
	} else {
		console.log('[FCM 저장] ℹ️  로그인 안 됨 - device만 저장');
	}

	console.log('[FCM 저장] 저장할 데이터:', JSON.stringify(tokenData, null, 2));

	try {
		// /fcm-tokens/{token} 경로에 저장 (토큰 문자열 자체를 키로 사용)
		const tokenPath = `fcm-tokens/${token}`;
		console.log('[FCM 저장] 저장 경로:', tokenPath);

		const tokenRef = ref(rtdb, tokenPath);
		console.log('[FCM 저장] 🔵 Firebase set() 호출 중...');

		await set(tokenRef, tokenData);

		console.log('[FCM 저장] ✅✅✅ FCM 토큰이 데이터베이스에 저장되었습니다!');
		console.log('[FCM 저장] 저장된 토큰 (앞 20자):', token.substring(0, 20) + '...');
		console.log('[FCM 저장] 저장된 데이터:', JSON.stringify(tokenData, null, 2));
	} catch (error) {
		console.error('[FCM 저장] ❌❌❌ 토큰 저장 실패:', error);
		console.error('[FCM 저장] 에러 상세:', {
			name: (error as Error).name,
			message: (error as Error).message,
			stack: (error as Error).stack
		});
	}
}
```

**저장 경로**: `/fcm-tokens/{token}` (토큰 문자열 자체를 키로 사용)

**데이터 구조** (v1.4.0):
```typescript
{
  device: "web" | "android" | "ios",  // 디바이스 타입 (필수)
  uid?: string                         // 사용자 UID (optional, 로그인 시에만)
}
```

**변경 사항 (v1.4.0)**:
- **uid 필드 optional**: 로그인 안 된 사용자도 `device: "web"` 만으로 토큰 저장 가능
- **디버깅 로그 추가**: 이모지 접두사(`🔵`, `✅`, `❌`, `ℹ️`)로 시각적으로 구분
- **단계별 로깅**: 각 단계마다 상세한 로그 출력
- **에러 상세 정보**: 에러 발생 시 name, message, stack 모두 로깅
- **데이터 출력**: `JSON.stringify()`로 저장할 데이터 구조 명확히 표시

**변경 사항 (v1.3.0)**:
- **키**: `{tokenId}` (Base64 인코딩) → `{token}` (토큰 문자열 자체)
- **필드**: `web: true` → `device: "web"`
- **필드 제거**: `createdAt`, `updatedAt` 타임스탬프 제거

**장점**:
- 로그인 전에도 토큰 저장 가능 (푸시 알림 활성화)
- 로그인 시 자동으로 uid 추가 (기존 토큰 업데이트)
- 토큰 중복 저장 방지 (토큰 자체가 고유 키)
- 멀티 디바이스 지원 구조 (device 필드로 디바이스 타입 구분)
- 풍부한 디버깅 로그로 문제 진단 용이

#### 2.3 포그라운드 메시지 수신

```typescript
/**
 * 포그라운드 메시지 수신 리스너 등록
 */
export async function subscribeOnMessage(
	callback: (payload: MessagePayload) => void
): Promise<void> {
	if (!browser) return;

	const messaging = await getFirebaseMessaging();
	if (!messaging) return;

	// onMessage 리스너 등록
	onMessage(messaging, (payload) => {
		console.log('[FCM] 포그라운드 메시지 수신:', payload);
		callback(payload);
	});

	console.log('✅ FCM 포그라운드 메시지 리스너 등록 완료');
}
```

**사용 예시**:
```typescript
subscribeOnMessage((payload) => {
	const title = payload.notification?.title ?? '새 알림';
	const body = payload.notification?.body ?? '';
	toast.success(title, { description: body });
});
```

#### 2.4 로그인 시 토큰 UID 자동 업데이트 ⭐ NEW (v1.4.0)

사용자가 로그인하거나 다시 접속할 때, 기존에 저장된 FCM 토큰에 자동으로 UID를 추가하는 함수입니다.

```typescript
/**
 * 기존 FCM 토큰의 UID를 업데이트하는 함수
 * 로그인 시 호출하여 로그인 안 된 상태로 저장된 토큰에 UID를 추가합니다.
 *
 * @returns {Promise<boolean>} 업데이트 성공 여부
 */
export async function updateFcmTokenWithUid(): Promise<boolean> {
	console.log('[FCM 업데이트] 🔵🔵🔵 토큰 UID 업데이트 시작');

	if (!browser) {
		console.warn('[FCM 업데이트] ❌ 서버 환경에서는 실행할 수 없습니다.');
		return false;
	}

	// 권한 확인
	if (Notification.permission !== 'granted') {
		console.log('[FCM 업데이트] ℹ️  알림 권한이 없습니다. 건너뜁니다.');
		return false;
	}
	console.log('[FCM 업데이트] ✅ 알림 권한 확인됨');

	// 현재 사용자 확인
	const uid = auth?.currentUser?.uid;
	if (!uid) {
		console.log('[FCM 업데이트] ℹ️  로그인 안 됨. 건너뜁니다.');
		return false;
	}
	console.log('[FCM 업데이트] ✅ 현재 로그인 UID:', uid);

	try {
		// 토큰 발급 (이미 발급받은 토큰이 있으면 그걸 반환함)
		console.log('[FCM 업데이트] 🔵 토큰 가져오는 중...');
		const token = await requestFcmToken();

		if (token) {
			console.log('[FCM 업데이트] ✅✅✅ 토큰 UID 업데이트 완료!');
			return true;
		} else {
			console.log('[FCM 업데이트] ❌ 토큰 가져오기 실패');
			return false;
		}
	} catch (error) {
		console.error('[FCM 업데이트] ❌ 토큰 업데이트 실패:', error);
		return false;
	}
}
```

**주요 기능**:
- 로그인 시 자동으로 호출되어 기존 토큰에 uid 추가
- `requestFcmToken()`을 호출하여 토큰 재발급 (Firebase는 기존 토큰 반환)
- `saveFcmTokenToDatabase()`에서 현재 uid와 함께 저장되므로 자동 업데이트
- 알림 권한이 없거나 로그인 안 된 경우 조기 종료

**작동 원리**:
1. 사용자가 로그인 전 알림 권한 허용 → 토큰 저장 (`{ device: "web" }`)
2. 사용자가 로그인 → `updateFcmTokenWithUid()` 자동 호출
3. `requestFcmToken()` 호출 → Firebase SDK가 기존 토큰 반환
4. `saveFcmTokenToDatabase()` 호출 → 현재 uid와 함께 저장 (`{ device: "web", uid: "..." }`)
5. 동일한 토큰 키이므로 데이터베이스에서 자동 업데이트 (덮어쓰기)

---

### 2.5 AuthStore에 FCM 자동 업데이트 통합 ⭐ NEW (v1.4.0)

사용자 인증 상태 관리 스토어(`src/lib/stores/auth.svelte.ts`)에 FCM 토큰 자동 업데이트 로직을 통합했습니다.

**파일**: `src/lib/stores/auth.svelte.ts`

#### onAuthStateChanged 리스너 통합

```typescript
import { onAuthStateChanged, type User } from 'firebase/auth';

private initializeAuthListener() {
	if (!auth) {
		console.warn('Firebase Auth가 초기화되지 않았습니다.');
		this._state.loading = false;
		this._state.initialized = true;
		return;
	}

	onAuthStateChanged(auth, async (user) => {
		this._state.user = user;

		// 사용자 로그인 시 프로필 동기화 및 관리자 목록 로드
		if (user) {
			console.log('사용자 로그인됨:', user.uid);

			// Firebase Auth의 photoURL, displayName을 RTDB에 동기화
			await this.syncUserProfile(user);

			// 관리자 목록 로드
			await this.loadAdminList();

			// FCM 토큰에 UID 업데이트 (동적 import로 순환 의존성 방지)
			if (typeof window !== 'undefined') {
				import('$lib/fcm')
					.then(({ updateFcmTokenWithUid }) => {
						updateFcmTokenWithUid();
					})
					.catch((error) => {
						console.error('[AuthStore] FCM 토큰 업데이트 import 실패:', error);
					});
			}
		} else {
			console.log('사용자 로그아웃됨');
			this._state.adminList = [];
		}

		this._state.loading = false;
		this._state.initialized = true;
	});
}
```

**주요 기능**:
- **자동 트리거**: 사용자 로그인 시 자동으로 `updateFcmTokenWithUid()` 호출
- **동적 import 사용**: `import('$lib/fcm')`로 순환 의존성 방지
  - `auth.svelte.ts` → `fcm.ts` → `auth.svelte.ts` 순환 의존성 문제 해결
- **브라우저 환경 체크**: `typeof window !== 'undefined'`로 SSR 환경 보호
- **에러 핸들링**: import 실패 시 콘솔에 에러 로깅
- **비동기 처리**: 토큰 업데이트가 완료될 때까지 기다리지 않음 (non-blocking)

**실행 시점**:
1. 페이지 로드 시 (기존 로그인 상태 확인)
2. 새로 로그인 시
3. 다른 탭에서 로그인 시 (Firebase Auth 상태 동기화)

**순환 의존성 해결**:
```typescript
// ❌ 직접 import는 순환 의존성 발생
// import { updateFcmTokenWithUid } from '$lib/fcm';

// ✅ 동적 import로 순환 의존성 방지
import('$lib/fcm')
	.then(({ updateFcmTokenWithUid }) => {
		updateFcmTokenWithUid();
	})
	.catch((error) => {
		console.error('[AuthStore] FCM 토큰 업데이트 import 실패:', error);
	});
```

**전체 로그인 플로우**:
1. 사용자 로그인
2. `onAuthStateChanged` 트리거
3. 사용자 프로필 동기화 (`syncUserProfile`)
4. 관리자 목록 로드 (`loadAdminList`)
5. **FCM 토큰 UID 업데이트** (`updateFcmTokenWithUid`) ⭐ NEW
6. 인증 상태 완료 (`loading = false`, `initialized = true`)

---

### 3. 서비스 워커

**파일**: `static/firebase-messaging-sw.js`

#### 3.1 Firebase 초기화

```javascript
// Firebase SDK CDN (compat 버전)
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js');

// Firebase Config 하드코딩 (환경변수 접근 불가)
firebase.initializeApp({
	apiKey: 'AIzaSyCXAFYnNf7QYcZNpIngCA-lOhb9YRRLDTY',
	authDomain: 'sonub-firebase.firebaseapp.com',
	projectId: 'sonub-firebase',
	storageBucket: 'sonub-firebase.firebasestorage.app',
	messagingSenderId: '406320255657',
	appId: '1:406320255657:web:79b39117a353878b8e8fb8'
});

const messaging = firebase.messaging();
```

**중요**: 서비스 워커는 환경 변수에 접근할 수 없으므로 Firebase Config를 직접 하드코딩해야 합니다.

#### 3.2 백그라운드 메시지 처리

```javascript
// 백그라운드 메시지 수신
messaging.onBackgroundMessage((payload) => {
	console.log('[firebase-messaging-sw.js] 백그라운드 메시지 수신:', payload);

	const notificationTitle = payload.notification?.title ?? '새 알림';
	const notificationOptions = {
		body: payload.notification?.body ?? '',
		icon: '/favicon-512.png',
		badge: '/favicon-64.png',
		data: payload.data,
		tag: payload.data?.tag ?? 'default',
		requireInteraction: false,
		vibrate: [200, 100, 200]
	};

	// 브라우저 알림 표시
	self.registration.showNotification(notificationTitle, notificationOptions);
});
```

**기능**:
- 백그라운드에서 메시지 수신
- 브라우저 알림 자동 표시
- 커스텀 데이터 전달

#### 3.3 알림 클릭 이벤트 처리

```javascript
// 알림 클릭 이벤트
self.addEventListener('notificationclick', (event) => {
	console.log('[firebase-messaging-sw.js] 알림 클릭:', event.notification);

	event.notification.close();

	// click_action URL로 이동
	const urlToOpen = event.notification.data?.click_action || '/';

	event.waitUntil(
		clients
			.matchAll({ type: 'window', includeUncontrolled: true })
			.then((clientList) => {
				// 이미 열려 있는 창 찾기
				for (const client of clientList) {
					if (client.url === urlToOpen && 'focus' in client) {
						return client.focus();
					}
				}
				// 열려 있는 창이 없으면 새 창 열기
				if (clients.openWindow) {
					return clients.openWindow(urlToOpen);
				}
			})
	);
});
```

**기능**:
- 알림 클릭 시 알림 닫기
- `click_action` URL로 이동
- 이미 열려 있는 창이 있으면 포커스

---

### 4. Toast 알림 (svelte-sonner)

**파일**: `src/routes/+layout.svelte`

```svelte
<script lang="ts">
	import { Toaster } from 'svelte-sonner';
</script>

<!-- 전역 Toast 알림 컴포넌트 -->
<Toaster position="top-center" richColors />
```

**사용 예시**:
```typescript
import { toast } from 'svelte-sonner';

// 기본 toast
toast('안녕하세요!');

// 성공 메시지
toast.success('저장 완료');

// 에러 메시지
toast.error('오류가 발생했습니다');

// 커스텀 메시지
toast.success('FCM 토큰 발급 완료', {
	description: '푸시 알림을 받을 준비가 되었습니다.'
});
```

---

## 5. FCM 권한 요청 가드 (자동 거절 방지) ⭐ NEW

### 개요

브라우저는 접속 즉시 푸시 알림 권한을 요청하면 자동으로 거절하는 보안 정책이 있습니다. 이를 방지하기 위해 사용자가 사이트에 익숙해진 후에만 권한을 요청하는 가드 컴포넌트를 구현했습니다.

### 컴포넌트 구현

**파일**: `src/lib/components/FcmPermissionGate.svelte`

#### 5.1 페이지 이동 카운팅 로직

```typescript
const CLICK_THRESHOLD = 10;
const SESSION_KEY = 'pageMoveCount';

/**
 * SessionStorage에서 페이지 이동 카운트 가져오기
 */
function getPageMoveCount(): number {
	if (!browser) return 0;
	const raw = sessionStorage.getItem(SESSION_KEY);
	return raw ? Number(raw) || 0 : 0;
}

/**
 * SessionStorage에 페이지 이동 카운트 저장
 */
function setPageMoveCount(count: number) {
	if (!browser) return;
	sessionStorage.setItem(SESSION_KEY, String(count));
}

/**
 * 페이지 이동 카운트 증가 (+1)
 */
function incrementPageMoveCount() {
	const current = getPageMoveCount();
	const next = current + 1;
	setPageMoveCount(next);
	checkPermissionLogic(next);
}

// 페이지 네비게이션 시점에 카운트 증가
if (browser) {
	afterNavigate(() => {
		incrementPageMoveCount();
	});
}
```

**특징**:
- `afterNavigate` 훅으로 페이지 이동 감지
- SessionStorage 사용 (브라우저 세션 동안만 유지)
- 카운트 증가 후 즉시 권한 로직 체크

#### 5.1.1 SessionStorage 플래그 관리 로직 ⭐ NEW

```typescript
const DISMISSED_KEY = 'fcmPermissionDismissed'; // 권한 요청 거절 플래그

/**
 * SessionStorage에서 권한 요청 거절 플래그 가져오기
 */
function getFcmPermissionDismissed(): boolean {
	if (!browser) return false;
	const raw = sessionStorage.getItem(DISMISSED_KEY);
	return raw === 'true';
}

/**
 * SessionStorage에 권한 요청 거절 플래그 저장
 */
function setFcmPermissionDismissed(dismissed: boolean) {
	if (!browser) return;
	sessionStorage.setItem(DISMISSED_KEY, String(dismissed));
}
```

**특징**:
- 플래그 키: `fcmPermissionDismissed`
- "나중에" 버튼 클릭 또는 설정 페이지 접속 시 `true` 저장
- 세션 동안 권한 요청 모달 미표시 제어

#### 5.2 권한 조건 확인 로직

```typescript
/**
 * 권한 상태 + 조건을 확인하여 모달 표시 여부 결정
 */
function checkPermissionLogic(currentCount?: number) {
	if (!browser) return;
	if (typeof Notification === 'undefined') return;

	// 🔥 사용자가 이미 "나중에" 또는 설정 페이지를 방문한 경우 모달 표시 안 함
	if (getFcmPermissionDismissed()) return;

	const permission = Notification.permission;
	const count = currentCount ?? getPageMoveCount();

	// 조건: 페이지 이동 10회 이상
	const shouldTrigger = count >= CLICK_THRESHOLD;

	if (!shouldTrigger) return;

	// 이미 모달이 표시 중이면 중복 표시 방지
	if (showRequestDialog || showDeniedDialog) return;

	if (permission === 'default') {
		// 아직 권한 요청 전 → 권한 요청 모달
		showRequestDialog = true;
	} else if (permission === 'denied') {
		// 이미 거절 상태 → 안내 모달 + 설정 페이지 이동
		showDeniedDialog = true;
	}
}
```

**조건**:
- 페이지 이동 10회 이상

**권한 상태 처리**:
- `default`: 권한 요청 모달
- `denied`: 설정 안내 모달
- `granted`: 아무 것도 안 함

#### 5.3 권한 허용 버튼 핸들러

```typescript
/**
 * "퍼미션 허용하기" 버튼 클릭 핸들러
 * User gesture 안에서 Notification.requestPermission() 호출
 */
async function handleAllowClick() {
	if (typeof Notification === 'undefined') {
		toast.error('이 브라우저는 알림을 지원하지 않습니다.');
		showRequestDialog = false;
		return;
	}

	isProcessing = true;

	try {
		// 🔥 User gesture 안에서 권한 요청 (자동 거절 방지)
		const result = await Notification.requestPermission();

		if (result === 'granted') {
			showRequestDialog = false;
			toast.success('알림 권한이 허용되었습니다.');

			// FCM 토큰 발급 및 저장
			const token = await requestFcmToken();

			if (token) {
				toast.success('푸시 알림이 활성화되었습니다!');
			} else {
				toast.error('FCM 토큰 발급에 실패했습니다. 콘솔을 확인해주세요.');
			}
		} else if (result === 'denied') {
			showRequestDialog = false;
			toast.error('알림 권한이 거부되었습니다.');
			// 거절되면 안내 페이지로 이동
			goto('/settings/fcm/permission');
		} else {
			// 'default' 그대로인 경우 (사용자가 브라우저 팝업을 닫은 경우 등)
			showRequestDialog = false;
		}
	} catch (error) {
		console.error('[FCM Permission] 권한 요청 실패:', error);
		toast.error('권한 요청 중 오류가 발생했습니다.');
		showRequestDialog = false;
	} finally {
		isProcessing = false;
	}
}
```

**주요 기능**:
- 버튼 클릭 핸들러 안에서만 `Notification.requestPermission()` 호출 (User Gesture 보장)
- 권한 허용 시 즉시 FCM 토큰 발급
- 권한 거절 시 설정 페이지로 리다이렉트
- Toast 알림으로 사용자 피드백 제공

#### 5.3.1 "나중에" 버튼 핸들러 ⭐ NEW

```typescript
/**
 * "나중에" 버튼 클릭 핸들러
 * SessionStorage에 플래그 저장 후 설정 페이지로 이동
 */
function handleLaterClick() {
	showRequestDialog = false;
	setFcmPermissionDismissed(true);
	toast.info('푸시 알림은 나중에 설정할 수 있습니다.');
	goto('/settings/fcm/permission');
}
```

**주요 기능**:
- SessionStorage에 `fcmPermissionDismissed=true` 플래그 저장
- `/settings/fcm/permission` 페이지로 자동 리다이렉트
- Toast 알림으로 사용자 피드백 제공
- 해당 세션 동안 권한 요청 모달 미표시

#### 5.4 모달 UI

**권한 요청 모달 (permission === 'default')**:
```svelte
{#if showRequestDialog}
	<div class="permission-modal-overlay">
		<div class="permission-modal-content">
			<h2 class="modal-title">푸시 알림 권한이 필요합니다</h2>
			<p class="modal-description">
				원활한 서비스 이용을 위해 브라우저 푸시 알림 권한을 허용해 주세요.
				채팅 알림, 새로운 메시지 안내 등 주요 기능에 사용됩니다.
			</p>

			<div class="modal-buttons">
				<button type="button" class="btn-secondary" onclick={handleLaterClick}>
					나중에
				</button>
				<button type="button" class="btn-primary" onclick={handleAllowClick} disabled={isProcessing}>
					{isProcessing ? '처리 중...' : '퍼미션 허용하기'}
				</button>
			</div>
		</div>
	</div>
{/if}
```

**거절 안내 모달 (permission === 'denied')**:
```svelte
{#if showDeniedDialog}
	<div class="permission-modal-overlay">
		<div class="permission-modal-content">
			<h2 class="modal-title">알림 권한이 차단되어 있습니다</h2>
			<p class="modal-description">
				브라우저에서 이 사이트의 알림 권한을 이미 <strong>차단</strong>한 상태입니다.
				푸시 알림을 사용하려면 브라우저 설정에서 직접 권한을 다시 허용해야 합니다.
			</p>
			<p class="modal-description">
				다음 페이지에서 브라우저별로 푸시 권한을 다시 허용하는 방법을 안내해 드릴게요.
			</p>

			<div class="modal-buttons">
				<button type="button" class="btn-primary" onclick={goToSettingsFromDenied}>
					설정 페이지로 이동
				</button>
			</div>
		</div>
	</div>
{/if}
```

#### 5.5 레이아웃에 통합

**파일**: `src/routes/+layout.svelte`

```svelte
<script lang="ts">
	import FcmPermissionGate from '$lib/components/FcmPermissionGate.svelte';
</script>

<!-- 기존 레이아웃 -->
<slot />

<!-- FCM 푸시 알림 권한 요청 가드 -->
<FcmPermissionGate />
```

**특징**:
- 루트 레이아웃에 한 번만 추가
- 전체 앱에서 자동으로 작동
- 페이지 이동 시마다 카운트 체크

---

## 6. 권한 설정 안내 페이지 ⭐ NEW

### 개요

사용자가 푸시 알림 권한을 거절한 경우, 브라우저 설정에서 직접 권한을 재허용하는 방법을 안내하는 페이지입니다.

### 페이지 구현

**파일**: `src/routes/settings/fcm/permission/+page.svelte`

#### 6.1 페이지 구조 ⭐ UPDATED

```svelte
<script lang="ts">
	/**
	 * FCM 푸시 알림 권한 설정 안내 페이지
	 *
	 * 브라우저에서 푸시 알림 권한이 차단된 경우,
	 * 브라우저별로 권한을 다시 허용하는 방법을 안내합니다.
	 *
	 * 이 페이지에 접속하면 SessionStorage에 플래그를 저장하여
	 * 해당 세션 동안 권한 요청 팝업을 더 이상 표시하지 않습니다.
	 */

	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { Card, CardHeader, CardTitle, CardDescription, CardContent }
		from '$lib/components/ui/card';

	const DISMISSED_KEY = 'fcmPermissionDismissed';

	/**
	 * 페이지 마운트 시 SessionStorage에 플래그 저장
	 */
	onMount(() => {
		if (browser) {
			sessionStorage.setItem(DISMISSED_KEY, 'true');
		}
	});
</script>

<div class="container">
	<h1 class="page-title">푸시 알림 권한 설정 안내</h1>

	<!-- 안내 카드 -->
	<Card class="mb-6">
		<CardHeader>
			<CardTitle>알림 권한이 차단되어 있습니다</CardTitle>
			<CardDescription>
				현재 이 사이트의 푸시 알림 권한이 브라우저에서 차단된 상태입니다.
				채팅 알림, 새로운 메시지 안내 등을 받으려면 아래 안내에 따라
				권한을 다시 허용해 주세요.
			</CardDescription>
		</CardHeader>
		<CardContent>
			<p class="notice">
				브라우저에서 한 번 차단된 알림 권한은 사이트에서 직접 요청할 수 없으며,
				사용자가 브라우저 설정에서 직접 변경해야 합니다.
			</p>
		</CardContent>
	</Card>

	<!-- 브라우저별 안내 카드들 -->
	<!-- Chrome, Safari, Firefox, Edge -->
</div>
```

#### 6.2 브라우저별 안내 (예시: Chrome 데스크탑)

```svelte
<Card class="mb-6">
	<CardHeader>
		<CardTitle>Chrome (데스크탑) 권한 허용 방법</CardTitle>
	</CardHeader>
	<CardContent>
		<ol class="instruction-list">
			<li>주소창 왼쪽의 <strong>자물쇠 아이콘(🔒)</strong> 또는
				<strong>정보 아이콘(ℹ️)</strong>을 클릭합니다.</li>
			<li><strong>"알림(Notifications)"</strong> 항목을 찾습니다.</li>
			<li>설정을 <strong>"허용(Allow)"</strong>으로 변경합니다.</li>
			<li>페이지를 새로고침(F5 또는 Ctrl+R)한 뒤, 다시 서비스를 이용해 주세요.</li>
		</ol>
	</CardContent>
</Card>
```

#### 6.3 지원 브라우저

페이지에서 제공하는 브라우저별 안내:

1. **Chrome (데스크탑)**: 주소창 자물쇠 → 알림 → 허용
2. **Chrome (Android)**: 브라우저 메뉴 → 사이트 설정 → 알림
3. **Safari (macOS)**: Safari 메뉴 → 설정 → 웹사이트 → 알림
   - **참고**: Safari iOS는 웹 푸시 미지원
4. **Firefox (데스크탑)**: 주소창 자물쇠 → 추가 정보 → 권한
5. **Edge (데스크탑)**: 주소창 자물쇠 → 권한

#### 6.4 스타일링

```svelte
<style>
	.container {
		@apply mx-auto max-w-4xl p-4;
	}

	.page-title {
		@apply text-3xl font-bold mb-6 text-gray-900;
	}

	.notice {
		@apply text-sm text-gray-700 mb-2;
	}

	.instruction-list {
		@apply list-decimal list-inside text-sm text-gray-700 space-y-2;
	}
</style>
```

**특징**:
- Tailwind CSS `@apply` 사용
- shadcn-svelte Card 컴포넌트 활용
- 반응형 디자인 (max-w-4xl)

---

## 주의사항 및 문제 해결

### 1. 서비스 워커 firebaseConfig 하드코딩

서비스 워커는 SvelteKit의 환경 변수(`$env/static/public`)에 접근할 수 없습니다. 따라서 `static/firebase-messaging-sw.js` 파일에 firebaseConfig를 **직접 하드코딩**해야 합니다.

**이유**:
- 서비스 워커는 별도의 워커 스레드에서 실행됨
- SvelteKit 빌드 시스템과 분리되어 있음
- Firebase SDK는 초기화 시 반드시 `firebaseConfig` 객체가 필요함

**보안**:
- Firebase Config는 공개되어도 안전합니다
- Firebase 공식 문서에서도 이 방식을 권장합니다

### 2. VAPID Key 보안

- `PUBLIC_` 접두사로 클라이언트에 노출됨
- VAPID Key는 **공개 키(Public Key)**이므로 노출되어도 안전
- Private Key는 Firebase에서 관리하므로 노출되지 않음

### 3. 브라우저 지원

- **Chrome, Edge, Firefox**: 완전 지원 ✅
- **Safari (iOS)**: 지원 안 함 ❌ (2025년 1월 기준)
- **Safari (macOS)**: 제한적 지원 ⚠️

### 4. HTTPS 필수

- FCM은 **HTTPS 환경**에서만 작동
- `localhost`는 예외 (개발 테스트 가능)
- 프로덕션 배포 시 HTTPS 인증서 필수

---

## 참고 자료

- [Firebase Cloud Messaging 설치](./sonub-setup-firebase-fcm.md) - 설치 및 설정 가이드
- [Firebase 공식 문서 - Web Push](https://firebase.google.com/docs/cloud-messaging/js/client)
- [Firebase Messaging API Reference](https://firebase.google.com/docs/reference/js/messaging_)
- [svelte-sonner GitHub](https://github.com/wobsoriano/svelte-sonner)


