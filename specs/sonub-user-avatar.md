---
name: sonub-user-avatar
version: 2.0.0
description: 사용자 아바타 컴포넌트 구현 및 CORS/이미지 로드 문제 해결
author: Claude Code
email: noreply@anthropic.com
step: 45
priority: '*'
dependencies:
  - sonub-firebase-auth.md
  - sonub-setup-firebase.md
tags:
  - component
  - avatar
  - user-profile
  - rtdb
  - cors
  - image-loading
---

## 1. 개요

### 1.1 프로젝트 배경

#### 1.1.1 배경 및 문제점

여러 곳에서 사용자 프로필 사진을 UI에 표시해야 하는 상황이었습니다:
- **top-bar**: 상단 네비게이션 바의 우측 사용자 아바타
- **menu**: 메뉴 페이지의 사용자 프로필
- **프로필 페이지**: 사용자 정보 표시
- **채팅/댓글**: 작성자 아바타

초기 문제점:
1. **코드 중복**: 각 페이지마다 아바타 로직을 별도로 구현
2. **데이터 출처 불일치**: Firebase Auth의 `photoURL`을 직접 사용 (RTDB `/users/{uid}/photoUrl` 무시)
3. **오류 처리 부족**: 이미지 로드 실패 시 fallback 로직 없음
4. **CORS 문제**: Google 프로필 이미지 로드 시 referrer 정책으로 인한 차단
5. **반응성 문제**: 사용자가 프로필 사진 변경 시 실시간 업데이트 안 됨

#### 1.1.2 요구사항

1. **사용자 아바타 재사용 컴포넌트**: 모든 곳에서 동일하게 사용
2. **RTDB 데이터 출처 사용**: `/users/{uid}/photoUrl` 값을 이미지 소스로 사용
3. **첫 글자 fallback**: 이미지가 없으면 `displayName`의 첫 글자 표시
4. **크기 옵션 제공**: `size` prop으로 아바타 크기 지정
5. **실시간 업데이트**: RTDB 데이터 변경 시 자동 반영
6. **유연한 사용법**: uid 또는 photoUrl/displayName을 직접 제공 가능

### 1.2 해결책 개요

**컴포넌트 위치:** `src/lib/components/user/avatar.svelte`

**주요 기능:**
- uid prop을 받아서 RTDB에서 프로필 데이터 자동 로드
- 이미지 로드 실패 추적 (`imageLoadFailed` 상태)
- CORS/Referrer 문제 해결 (`referrerpolicy="no-referrer"`, `crossorigin="anonymous"`)
- size prop으로 크기 지정
- 이미지 없을 시 자동으로 첫 글자 표시

## 2. 기술 구조 및 아키텍처

### 2.1 컴포넌트 동작 흐름

```
사용자 로그인
    ↓
┌─────────────────────────────────────┐
│ Avatar 컴포넌트 마운트               │
│ - uid prop 전달                     │
│ - size prop 전달 (기본값: 40)       │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ uid가 존재?                         │
└─────────────────────────────────────┘
    │                   │
   YES                 NO
    │                   │
┌──────────────┐   ┌────────────────┐
│ RTDB 리스너  │   │ uid 없음 경고  │
│ 등록         │   │                │
│ /users/{uid} │   └────────────────┘
└──────────────┘
    ↓
┌─────────────────────────────────────┐
│ RTDB 데이터 수신                    │
│ - photoUrl: "https://..."           │
│ - displayName: "JaeHo Song"         │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 상태 업데이트                       │
│ - photoUrl = data.photoUrl          │
│ - displayName = data.displayName    │
│ - imageLoadFailed = false (초기화)  │
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ $derived 반응형 계산                │
│ - initial = displayName[0]          │
│ - shouldShowImage = photoUrl &&     │
│                     !imageLoadFailed│
└─────────────────────────────────────┘
    ↓
┌─────────────────────────────────────┐
│ shouldShowImage?                    │
└─────────────────────────────────────┘
    │                   │
   YES                 NO
    │                   │
┌──────────────┐   ┌────────────────┐
│ <img> 렌더링 │   │ displayName의  │
│ src=photoUrl │   │ 첫 글자 표시   │
│ + CORS 속성  │   │                │
└──────────────┘   └────────────────┘
    │
    ↓
┌─────────────────────────────────────┐
│ 이미지 로드 성공?                   │
└─────────────────────────────────────┘
    │                   │
  SUCCESS              ERROR
    │                   │
┌──────────────┐   ┌────────────────┐
│ 이미지 표시  │   │ handleImageError│
│              │   │ → imageLoadFailed = true │
│              │   │ → 첫 글자로    │
│              │   │   자동 전환    │
└──────────────┘   └────────────────┘
    ↓
┌─────────────────────────────────────┐
│ 컴포넌트 언마운트                   │
│ → RTDB 리스너 해제                  │
└─────────────────────────────────────┘
```

### 2.2 데이터 흐름 상세

Avatar 컴포넌트는 다음의 흐름으로 데이터를 처리합니다:

```typescript
// 1. Props 입력
uid: "GljDA3yso2b3wIHh1M45vHGUcK72"
size: 40

// 2. RTDB 리스너 등록
onMount() → onValue(ref(rtdb, `users/${uid}`))

// 3. RTDB 데이터 수신
{
  photoUrl: "https://lh3.googleusercontent.com/a/ACg8ocLvjytedzeQRQiOE3JrttCGJ1kuE83wuDmCFDougD-AfD_wdTos=s96-c",
  displayName: "JaeHo Song"
}

// 4. 상태 업데이트
photoUrl = "https://..." (from RTDB)
displayName = "JaeHo Song" (from RTDB)
imageLoadFailed = false

// 5. $derived 계산
initial = "J" (displayName[0].toUpperCase())
shouldShowImage = true (photoUrl 존재 && !imageLoadFailed)

// 6. 렌더링
<img
  src="https://..."
  referrerpolicy="no-referrer"
  crossorigin="anonymous"
/>

// 7-a. 이미지 로드 성공
→ 이미지 표시

// 7-b. 이미지 로드 실패 (CORS, 404 등)
→ handleImageError() 호출
→ imageLoadFailed = true
→ shouldShowImage = false (자동 계산)
→ 첫 글자 "J" 표시
```

**핵심 개선점:**
- **이전**: 이미지 실패 시 `display: none`으로 숨김 → 빈 원만 보임
- **현재**: `imageLoadFailed` 상태 추적 → `shouldShowImage` 재계산 → 첫 글자 자동 표시

### 2.3 시스템 컴포넌트 관계

```
┌──────────────────────────────────────────────────────────────┐
│ 부모 컴포넌트 (top-bar, menu, 프로필 페이지 등)              │
│ ┌──────────────────────────────────────────────────────────┐ │
│ │ <Avatar uid={authStore.user.uid} size={40} />           │ │
│ └──────────────────────────────────────────────────────────┘ │
└────────────────────────────┬─────────────────────────────────┘
                             │ uid prop 전달
                             ↓
┌──────────────────────────────────────────────────────────────┐
│ Avatar 컴포넌트                                              │
│ ┌──────────────────────────────────────────────────────────┐ │
│ │ onMount()                                                │ │
│ │   - RTDB 리스너 등록                                     │ │
│ │   - onValue(ref(rtdb, `users/${uid}`))                  │ │
│ └──────────────────────────────────────────────────────────┘ │
│ ┌──────────────────────────────────────────────────────────┐ │
│ │ $derived 반응형 값                                       │ │
│ │   - initial = displayName[0].toUpperCase()              │ │
│ │   - shouldShowImage = photoUrl && !imageLoadFailed      │ │
│ └──────────────────────────────────────────────────────────┘ │
└────────────────────────────┬─────────────────────────────────┘
                             │ onValue 리스너
                             ↓
┌──────────────────────────────────────────────────────────────┐
│ Firebase Realtime Database                                   │
│ /users/{uid}/                                                │
│   - photoUrl: "https://lh3.googleusercontent.com/..."        │
│   - displayName: "JaeHo Song"                                │
└──────────────────────────────────────────────────────────────┘
```

## 3. 구현 상세

### 3.1 파일 구조

**파일:** `src/lib/components/user/avatar.svelte`

### 3.2 Props 정의

```typescript
interface Props {
	/**
	 * 사용자 UID (필수)
	 * RTDB에서 photoUrl과 displayName을 자동으로 가져옵니다.
	 */
	uid?: string;

	/**
	 * 아바타 크기 (픽셀 단위)
	 * width와 height에 동일하게 적용됩니다.
	 * @default 40
	 */
	size?: number;

	/**
	 * 추가 CSS 클래스
	 */
	class?: string;
}
```

**주요 변경사항 (v1 → v2):**
- ❌ **제거**: `photoUrl`, `displayName` props (단순화)
- ✅ **유지**: `uid`, `size`, `class`
- ✅ **목적**: 컴포넌트 단순화 및 RTDB를 단일 데이터 소스로 사용

### 3.3 전체 구현 코드

```typescript
<script lang="ts">
	/**
	 * 사용자 아바타 컴포넌트
	 *
	 * 사용자 프로필 사진을 표시하거나, 사진이 없을 경우 displayName의 첫 글자를 표시합니다.
	 * RTDB의 /users/{uid}/photoUrl 값을 사용합니다.
	 */

	import { onMount } from 'svelte';
	import { rtdb } from '$lib/firebase';
	import { ref, onValue, type Unsubscribe } from 'firebase/database';

	// Props
	interface Props {
		/**
		 * 사용자 UID (필수)
		 * RTDB에서 photoUrl과 displayName을 자동으로 가져옵니다.
		 */
		uid?: string;

		/**
		 * 아바타 크기 (픽셀 단위)
		 * @default 40
		 */
		size?: number;

		/**
		 * 추가 CSS 클래스
		 */
		class?: string;
	}

	let {
		uid = undefined,
		size = 40,
		class: className = ''
	}: Props = $props();

	// RTDB에서 가져온 데이터
	let photoUrl = $state<string | null>(null);
	let displayName = $state<string | null>(null);

	// 이미지 로드 실패 추적
	let imageLoadFailed = $state(false);

	// displayName의 첫 글자 계산
	const initial = $derived.by(() => {
		const name = displayName;
		if (!name || name.trim() === '') return 'U';
		return name.charAt(0).toUpperCase();
	});

	// 이미지를 표시할지 여부 결정
	const shouldShowImage = $derived(
		photoUrl &&
		photoUrl.trim() !== '' &&
		!imageLoadFailed
	);

	// 디버깅 로그
	$effect(() => {
		console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
		console.log('[Avatar 상태]');
		console.log('  uid:', uid);
		console.log('  photoUrl:', photoUrl);
		console.log('  displayName:', displayName);
		console.log('  imageLoadFailed:', imageLoadFailed);
		console.log('  shouldShowImage:', shouldShowImage);
		console.log('  initial:', initial);
		console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
	});

	// RTDB에서 데이터 가져오기
	let unsubscribe: Unsubscribe | null = null;

	onMount(() => {
		console.log('[Avatar onMount] 시작, uid:', uid);

		if (!uid) {
			console.warn('[Avatar] uid가 제공되지 않았습니다.');
			return;
		}

		if (!rtdb) {
			console.error('[Avatar] Firebase RTDB가 초기화되지 않았습니다.');
			return;
		}

		const userRef = ref(rtdb, `users/${uid}`);
		console.log('[Avatar] RTDB 리스너 등록 시작:', `users/${uid}`);

		unsubscribe = onValue(
			userRef,
			(snapshot) => {
				const data = snapshot.val();
				console.log('[Avatar] ✅ RTDB 데이터 수신:', data);

				if (data) {
					// 이미지 로드 실패 상태 초기화
					imageLoadFailed = false;

					// 데이터 설정
					photoUrl = data.photoUrl || null;
					displayName = data.displayName || null;

					console.log('[Avatar] 데이터 설정 완료');
					console.log('  - photoUrl:', photoUrl);
					console.log('  - displayName:', displayName);
				} else {
					console.warn('[Avatar] ⚠️ RTDB에 사용자 데이터가 없습니다.');
					photoUrl = null;
					displayName = null;
				}
			},
			(error) => {
				console.error('[Avatar] ❌ RTDB 데이터 로드 실패:', error);
			}
		);

		// 정리 함수
		return () => {
			console.log('[Avatar] 리스너 해제, uid:', uid);
			if (unsubscribe) {
				unsubscribe();
			}
		};
	});

	/**
	 * 이미지 로드 에러 핸들러
	 */
	function handleImageError(e: Event) {
		console.error('[Avatar] ❌ 이미지 로드 실패:', photoUrl);
		imageLoadFailed = true;
	}
</script>

<!-- 아바타 컨테이너 -->
<div
	class="inline-flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white font-semibold shadow-sm overflow-hidden {className}"
	style="width: {size}px; height: {size}px;"
	role="img"
	aria-label={displayName || '사용자 아바타'}
>
	{#if shouldShowImage}
		<!-- 프로필 사진 표시 -->
		<img
			src={photoUrl || ''}
			alt={displayName || '사용자'}
			class="h-full w-full object-cover"
			referrerpolicy="no-referrer"
			crossorigin="anonymous"
			onerror={handleImageError}
		/>
	{:else}
		<!-- 프로필 사진이 없거나 로드 실패: 첫 글자 표시 -->
		<span class="text-lg" style="font-size: {size * 0.45}px;">
			{initial}
		</span>
	{/if}
</div>
```

### 3.4 핵심 로직 설명

#### 3.4.1 이미지 로드 실패 추적 (핵심 개선)

**문제점 (v1):**
```typescript
// ❌ 잘못된 방법
onerror={(e) => {
	const target = e.currentTarget as HTMLImageElement;
	target.style.display = 'none'; // 이미지만 숨김
}}

// 결과: {#if photoUrl} 조건은 여전히 true
// → else 블록(첫 글자) 실행 안 됨
// → 빈 원만 보임
```

**해결 방법 (v2):**
```typescript
// ✅ 올바른 방법
let imageLoadFailed = $state(false);

const shouldShowImage = $derived(
	photoUrl &&
	photoUrl.trim() !== '' &&
	!imageLoadFailed  // ← 이미지 로드 실패 추적
);

function handleImageError(e: Event) {
	console.error('[Avatar] ❌ 이미지 로드 실패:', photoUrl);
	imageLoadFailed = true;  // ← 상태 변경
}

// 템플릿
{#if shouldShowImage}
	<img onerror={handleImageError} />
{:else}
	<span>{initial}</span>  // ← 자동으로 실행됨
{/if}
```

**동작 흐름:**
1. RTDB에서 `photoUrl` 로드 성공
2. `shouldShowImage = true` (이미지 표시 시도)
3. 브라우저가 이미지 로드 시도
4. **CORS 에러 발생** → `onerror` 핸들러 실행
5. `imageLoadFailed = true` 설정
6. `shouldShowImage` 자동으로 `false`로 재계산
7. **Svelte가 자동으로 else 블록으로 전환** → 첫 글자 표시

#### 3.4.2 CORS/Referrer 문제 해결

**문제 진단:**
```
콘솔 로그:
[Avatar] ✅ RTDB 데이터 수신: photoUrl: "https://lh3.googleusercontent.com/..."
shouldShowImage: true   ✅ 이미지 표시 시도
[Avatar] ❌ 이미지 로드 실패   ⚠️ 브라우저가 이미지 로드 차단
```

새 탭에서 URL을 직접 열면 이미지가 정상 표시 → **CORS/Referrer 정책 문제**

**원인:**
- Google 프로필 이미지 서버(`lh3.googleusercontent.com`)는 보안상의 이유로 referrer 헤더를 검사
- 기본적으로 브라우저는 `Referer: http://localhost:5174/` 전송
- Google 서버가 localhost를 신뢰하지 않아 요청 차단

**해결:**
```html
<img
	src={photoUrl || ''}
	referrerpolicy="no-referrer"     <!-- ✅ referrer 전송 안 함 -->
	crossorigin="anonymous"          <!-- ✅ CORS 허용 -->
	onerror={handleImageError}
/>
```

**속성 설명:**

| 속성 | 설명 | 효과 |
|------|------|------|
| `referrerpolicy="no-referrer"` | 이미지 요청 시 Referer 헤더를 전송하지 않음 | Google 서버가 localhost를 보지 못함 → 차단 안 함 |
| `crossorigin="anonymous"` | CORS 요청을 명시적으로 허용 | 외부 도메인의 이미지를 안전하게 로드 |

**검증:**
```
이전 (속성 없음):
→ Request Headers: Referer: http://localhost:5174/
→ Google 서버: 403 Forbidden
→ 이미지 로드 실패

이후 (속성 추가):
→ Request Headers: (no Referer)
→ Google 서버: 200 OK
→ ✅ 이미지 로드 성공
```

#### 3.4.3 실시간 데이터 가져오기

```typescript
onMount(() => {
	if (!uid || !rtdb) return;

	const userRef = ref(rtdb, `users/${uid}`);

	// 실시간 리스너 등록
	unsubscribe = onValue(
		userRef,
		(snapshot) => {
			const data = snapshot.val();

			if (data) {
				// 이미지 로드 실패 상태 초기화 (중요!)
				imageLoadFailed = false;

				// 데이터 설정
				photoUrl = data.photoUrl || null;
				displayName = data.displayName || null;
			}
		},
		(error) => {
			console.error('[Avatar] ❌ RTDB 데이터 로드 실패:', error);
		}
	);

	// 컴포넌트 언마운트 시 리스너 해제
	return () => {
		if (unsubscribe) unsubscribe();
	};
});
```

**핵심 포인트:**
1. **데이터 수신 시 `imageLoadFailed = false` 초기화**: 새 이미지 URL이 올 때마다 재시도
2. **onValue 사용 이유**: RTDB 데이터 변경 시 자동 업데이트
3. **메모리 누수 방지**: 언마운트 시 `unsubscribe()` 호출

**`get()` vs `onValue()` 비교:**

```typescript
// ❌ get() 사용 (1회성 조회)
const snapshot = await get(userRef);
const data = snapshot.val();
// 문제: 데이터가 변경되어도 업데이트 안 됨

// ✅ onValue() 사용 (실시간 리스너)
const unsubscribe = onValue(userRef, (snapshot) => {
	const data = snapshot.val();
	// 데이터 변경 시 자동으로 호출됨
});
```

#### 3.4.4 반응형 계산 ($derived)

```typescript
// displayName의 첫 글자 계산
const initial = $derived.by(() => {
	const name = displayName;
	if (!name || name.trim() === '') return 'U';
	return name.charAt(0).toUpperCase();
});

// 이미지 표시 여부 결정
const shouldShowImage = $derived(
	photoUrl &&
	photoUrl.trim() !== '' &&
	!imageLoadFailed
);
```

**$derived의 역할:**
- Svelte 5 runes 문법
- 종속성(`photoUrl`, `displayName`, `imageLoadFailed`)이 변경되면 자동으로 재계산
- `initial`과 `shouldShowImage`는 항상 최신 상태 유지

**흐름 예시:**
```
1. 초기 상태:
   photoUrl = null
   displayName = null
   imageLoadFailed = false
   → shouldShowImage = false
   → initial = "U"

2. RTDB 데이터 수신:
   photoUrl = "https://..."
   displayName = "JaeHo Song"
   → shouldShowImage = true  (자동 재계산)
   → initial = "J"  (자동 재계산)

3. 이미지 로드 실패:
   imageLoadFailed = true
   → shouldShowImage = false  (자동 재계산)
   → 템플릿이 else 블록으로 전환
```

#### 3.4.5 동적 크기 지정

```html
<div style="width: {size}px; height: {size}px;">
	<!-- ... -->
	<span style="font-size: {size * 0.45}px;">
		{initial}
	</span>
</div>
```

**계산:**
- 컨테이너 크기: `size` prop 값
- 글자 크기: `size * 0.45` (아바타 대비 적절한 비율)
- 예시:
  - size=40 → 글자 크기 18px
  - size=64 → 글자 크기 28.8px
  - size=100 → 글자 크기 45px

## 4. 사용 예제

### 4.1 top-bar.svelte에서 적용

**파일:** `src/lib/components/top-bar.svelte`

```typescript
// Import 추가
import Avatar from '$lib/components/user/avatar.svelte';

// 아바타 표시
{#if authStore.isAuthenticated && authStore.user}
	<a
		href="/my/profile"
		class="cursor-pointer hover:opacity-80 transition-opacity"
		aria-label="내 프로필"
		title={authStore.user.displayName || authStore.user.email || '내 프로필'}
	>
		<Avatar uid={authStore.user.uid} size={40} />
	</a>
{/if}
```

**변경 사항:**
- ❌ **제거**: `getInitial()` 함수
- ❌ **제거**: 조건부 `{#if authStore.user?.photoURL}` 블록
- ✅ **추가**: `<Avatar>` 컴포넌트 사용
- ✅ **추가**: RTDB 데이터 기반 표시

### 4.2 menu/+page.svelte에서 적용

**파일:** `src/routes/menu/+page.svelte`

```typescript
// Import 추가
import Avatar from '$lib/components/user/avatar.svelte';

// 프로필 정보
<Card.Content class="space-y-3">
	<!-- 프로필 정보 -->
	<div class="flex justify-center">
		<Avatar uid={authStore.user?.uid} size={64} />
	</div>
	<div class="text-center">
		<p class="font-medium text-gray-900">
			{authStore.user?.displayName || '사용자'}
		</p>
		<p class="text-sm text-gray-600">
			{authStore.user?.email || ''}
		</p>
	</div>
</Card.Content>
```

**변경 사항:**
- ❌ **제거**: 조건부 `{#if authStore.user?.photoURL}` 블록
- ❌ **제거**: `<img>` 태그
- ✅ **추가**: `<Avatar>` 컴포넌트 (size=64)

### 4.3 사용 패턴

#### 패턴 1: uid로 RTDB에서 자동 로드 (권장)

```svelte
<Avatar uid={authStore.user?.uid} size={40} />
```

**장점:**
- 실시간 업데이트 자동 적용
- RTDB `/users/{uid}/photoUrl` 데이터 사용
- 코드 간결

**단점:**
- RTDB 조회 발생 (하지만 캐싱됨)

## 5. 동작 시나리오

### 5.1 프로필 사진이 있는 경우

```
1. 사용자 로그인
   - authStore.user.uid = "GljDA3yso2b3wIHh1M45vHGUcK72"

2. top-bar 렌더링
   - <Avatar uid="GljDA3yso2b3wIHh1M45vHGUcK72" size={40} />

3. Avatar 컴포넌트 마운트
   - onMount() 실행
   - RTDB 리스너 등록: /users/GljDA3yso2b3wIHh1M45vHGUcK72

4. RTDB 데이터 로드
   {
     photoUrl: "https://lh3.googleusercontent.com/a/ACg8ocLvjytedzeQRQiOE3JrttCGJ1kuE83wuDmCFDougD-AfD_wdTos=s96-c",
     displayName: "JaeHo Song"
   }

5. 상태 업데이트
   - photoUrl = "https://..."
   - displayName = "JaeHo Song"
   - imageLoadFailed = false

6. $derived 계산
   - initial = "J"
   - shouldShowImage = true

7. 렌더링
   - {#if shouldShowImage} → true
   - <img
       src="https://..."
       referrerpolicy="no-referrer"
       crossorigin="anonymous"
     />

8. 이미지 로드
   - ✅ CORS 속성 덕분에 성공적으로 로드
   - Google 프로필 사진 표시
```

### 5.2 프로필 사진이 없는 경우

```
1. 사용자 로그인
   - authStore.user.uid = "xyz789"

2. Avatar 컴포넌트 마운트

3. RTDB 데이터 로드
   {
     displayName: "홍길동"
     // photoUrl 필드 없음
   }

4. 상태 업데이트
   - photoUrl = null
   - displayName = "홍길동"
   - imageLoadFailed = false

5. $derived 계산
   - initial = "홍"
   - shouldShowImage = false (photoUrl이 null)

6. 렌더링
   - {#if shouldShowImage} → false
   - {:else} 블록 실행
   - <span>홍</span> 표시 (그라데이션 배경)
```

### 5.3 이미지 로드 실패 (CORS/404 등)

```
1. RTDB 데이터
   {
     photoUrl: "https://invalid-url.com/broken.jpg",
     displayName: "테스트"
   }

2. 초기 렌더링
   - shouldShowImage = true
   - <img src="https://invalid-url.com/broken.jpg">

3. 이미지 로드 실패
   - onerror 핸들러 실행

4. 상태 변경
   - handleImageError() 호출
   - imageLoadFailed = true

5. $derived 재계산
   - shouldShowImage = false (자동)

6. Svelte 반응형 업데이트
   - 템플릿이 자동으로 else 블록으로 전환
   - <span>테</span> 표시

✅ 결과: 빈 원이 아니라 첫 글자가 표시됨
```

### 5.4 실시간 업데이트

```
1. 초기 상태
   - photoUrl: "https://old-photo.jpg"
   - 화면에 old-photo.jpg 표시

2. 사용자가 프로필 사진 변경
   - 다른 탭 또는 /my/profile에서 사진 업로드
   - RTDB /users/abc123/photoUrl 업데이트
   - photoUrl: "https://new-photo.jpg"

3. onValue 리스너 자동 호출
   - Avatar 컴포넌트가 변경 감지
   - photoUrl = "https://new-photo.jpg"
   - imageLoadFailed = false (초기화)

4. $derived 자동 재계산
   - shouldShowImage = true

5. Svelte 반응형 업데이트
   - <img src> 속성 자동 변경
   - 새 이미지 로드 및 표시

✅ 별도의 새로고침 없이 자동 업데이트!
```

## 6. 설계 결정 및 이유

### 6.1 왜 RTDB를 사용하는가?

**Firebase Auth photoURL vs RTDB /users/{uid}/photoUrl:**

| 항목 | Firebase Auth | RTDB |
|------|--------------|------|
| 데이터 출처 | Google/Apple 제공 | 직접 커스텀 가능 |
| 업데이트 | 로그인 시점만 | 언제든지 가능 |
| 추가 필드 | 제한적 | 원하는 대로 |
| 실시간 동기화 | 없음 | onValue 리스너 |
| 관리 권한 | Auth 시스템 | 개발자 (보안 규칙) |

**RTDB 선택 이유:**
1. **사용자 커스터마이징**: 직접 앱 내에서 프로필 사진 변경 가능
2. **실시간 업데이트**: 다른 탭에서 사진 변경 시 자동 반영
3. **일관성**: 모든 사용자 데이터를 RTDB에서 관리
4. **확장성**: 추가 필드(상태 메시지, 커버 사진 등) 쉽게 추가 가능

### 6.2 왜 onValue를 사용하는가?

**get() vs onValue() 비교:**

```typescript
// ❌ get() 사용 (1회성 조회)
const snapshot = await get(userRef);
const data = snapshot.val();
// 문제: 데이터가 변경되어도 업데이트 안 됨

// ✅ onValue() 사용 (실시간 리스너)
const unsubscribe = onValue(userRef, (snapshot) => {
	const data = snapshot.val();
	// 데이터 변경 시 자동으로 호출됨
});
```

**onValue 선택 이유:**
1. **실시간성**: 프로필 사진 변경 시 자동 반영
2. **사용자 경험**: 새로고침 없이 업데이트
3. **데이터 일관성**: 항상 최신 상태 유지

**주의사항:**
- 반드시 `unsubscribe()` 호출로 메모리 누수 방지
- 컴포넌트가 많으면 RTDB 비용 증가 가능

### 6.3 imageLoadFailed 상태 추적 방식

**v1 (잘못된 방법):**
```typescript
onerror={(e) => {
	const target = e.currentTarget as HTMLImageElement;
	target.style.display = 'none'; // 이미지만 숨김
}}

// 문제:
// - 이미지가 숨겨져도 {#if photoUrl} 조건은 여전히 true
// - else 블록이 실행되지 않음
// - 결과: 빈 원만 보임
```

**v2 (올바른 방법):**
```typescript
let imageLoadFailed = $state(false);

function handleImageError(e: Event) {
	imageLoadFailed = true; // 상태 변경
}

// 템플릿
{#if shouldShowImage && !imageLoadFailed}
	<img onerror={handleImageError} />
{:else}
	<span>{initial}</span>  // ← 자동으로 실행됨
{/if}

// 장점:
// - imageLoadFailed 상태 변경 → shouldShowImage 재계산
// - Svelte가 자동으로 else 블록으로 전환
// - 첫 글자가 정상적으로 표시됨
```

**설계 원칙:**
- **반응형 상태 사용**: DOM 조작보다 Svelte의 반응성 활용
- **선언적 접근**: "어떻게"가 아니라 "무엇을" 표시할지 선언
- **자동 전환**: 상태 변경 시 Svelte가 자동으로 UI 업데이트

### 6.4 CORS/Referrer 문제 해결 방식

**문제 재현:**
```
브라우저 콘솔:
[Avatar] ✅ RTDB 데이터 수신: photoUrl: "https://lh3.googleusercontent.com/..."
shouldShowImage: true
[Avatar] ❌ 이미지 로드 실패

브라우저 Network 탭:
GET https://lh3.googleusercontent.com/...
Status: (failed)
```

**원인 분석:**
1. Google 이미지 서버는 referrer 헤더를 검사
2. `localhost`에서 요청 시 기본적으로 `Referer: http://localhost:5174/` 전송
3. Google 서버가 신뢰하지 않는 도메인이라 차단

**해결책:**
```html
<img
	src={photoUrl || ''}
	referrerpolicy="no-referrer"     <!-- Referer 헤더 전송 안 함 -->
	crossorigin="anonymous"          <!-- CORS 명시적 허용 -->
	onerror={handleImageError}
/>
```

**검증:**
```
이전:
Request Headers:
  Referer: http://localhost:5174/

이후:
Request Headers:
  (Referer 없음)

결과: ✅ 이미지 로드 성공
```

**주의사항:**
- `referrerpolicy="no-referrer"`는 개인정보 보호에도 도움
- 프로덕션에서도 동일하게 적용 가능
- 모든 외부 이미지 (Google, Facebook 등)에 적용 권장

## 7. 테스트 및 검증

### 7.1 기본 테스트 체크리스트

1. **프로필 사진 표시**
   - [ ] top-bar에 40x40 아바타 표시 확인
   - [ ] menu에 64x64 아바타 표시 확인
   - [ ] 이미지가 원형으로 표시되는지 확인
   - [ ] 이미지 비율이 정확한지 확인 (object-cover)

2. **첫 글자 표시**
   - [ ] photoUrl이 없을 때 첫 글자 표시 확인
   - [ ] 한글 이름의 한글 첫 글자 표시 확인
   - [ ] 영문 이름의 영문 첫 글자 표시 확인
   - [ ] displayName이 없으면 "U" 표시 확인

3. **실시간 업데이트**
   - [ ] Firebase Console에서 photoUrl 변경
   - [ ] 별도의 새로고침 없이 자동 업데이트 확인
   - [ ] 다른 탭에서 변경 시 현재 탭도 업데이트 확인

4. **이미지 로드 실패**
   - [ ] 잘못된 URL 입력 시 첫 글자 표시 확인
   - [ ] CORS 에러 URL 시 첫 글자 표시 확인
   - [ ] 빈 원이 아니라 첫 글자가 표시되는지 확인

5. **크기 지정**
   - [ ] size={20} 시 20x20 표시 확인
   - [ ] size={100} 시 100x100 표시 확인
   - [ ] 글자 크기가 아바타에 비례하는지 확인

6. **성능**
   - [ ] 동일 사용자의 Avatar 여러 개 렌더링 (리스트 페이지)
   - [ ] 메모리 누수 없는지 확인 (DevTools Memory)
   - [ ] 메모리 누수 없는지 확인 (컴포넌트 마운트/언마운트 반복)

### 7.2 타입 체크

```bash
npm run check
```

**기대 출력:**
```
✅ svelte-check found 0 errors
```

### 7.3 콘솔 로그 확인

**정상 동작 시 로그:**
```
[Avatar onMount] 시작, uid: GljDA3yso2b3wIHh1M45vHGUcK72
[Avatar] RTDB 리스너 등록 시작: users/GljDA3yso2b3wIHh1M45vHGUcK72
[Avatar] ✅ RTDB 데이터 수신: {photoUrl: "https://...", displayName: "JaeHo Song"}
[Avatar] 데이터 설정 완료
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[Avatar 상태]
  uid: GljDA3yso2b3wIHh1M45vHGUcK72
  photoUrl: https://lh3.googleusercontent.com/...
  displayName: JaeHo Song
  imageLoadFailed: false
  shouldShowImage: true
  initial: J
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

**이미지 로드 실패 시 로그:**
```
[Avatar] ❌ 이미지 로드 실패: https://...
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
[Avatar 상태]
  imageLoadFailed: true
  shouldShowImage: false
  initial: J
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

## 8. 프로덕션 정리

### 8.1 디버깅 로그 제거

프로덕션 배포 전에 다음 코드를 제거하거나 조건부로 변경:

```typescript
// ❌ 제거할 코드
$effect(() => {
	console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
	console.log('[Avatar 상태]');
	// ... 모든 디버깅 로그
});

console.log('[Avatar onMount] 시작, uid:', uid);
console.log('[Avatar] RTDB 리스너 등록 시작:', `users/${uid}`);
// ... 기타 console.log
```

**대안: 환경 변수 활용**
```typescript
import { dev } from '$app/environment';

$effect(() => {
	if (dev) {  // ← 개발 환경에서만 실행
		console.log('[Avatar 상태]');
		// ...
	}
});
```

### 8.2 최적화 권장사항

**1. 이미지 최적화**
```typescript
// Google 이미지 URL에 크기 파라미터 추가
const optimizedPhotoUrl = photoUrl?.replace(/=s\d+/, `=s${size * 2}`);
// 예: =s96-c → =s80-c (size=40일 때, 레티나 디스플레이 대응)
```

**2. 리스너 최적화**
```typescript
// 동일한 uid의 Avatar가 여러 개 있을 경우
// 전역 캐시 또는 Context API 사용 고려
```

**3. 레이지 로딩**
```html
<img
	loading="lazy"  <!-- ← 스크롤 시 로드 -->
	src={photoUrl || ''}
	...
/>
```

## 9. 확장 아이디어

### 9.1 온라인 상태 표시

```svelte
<div class="relative">
	<Avatar uid={user.uid} size={40} />
	{#if user.online}
		<div class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 rounded-full border-2 border-white"></div>
	{/if}
</div>
```

### 9.2 뱃지 시스템

```svelte
<div class="relative">
	<Avatar uid={user.uid} size={40} />
	{#if user.verified}
		<div class="absolute -top-1 -right-1">
			<svg><!-- 인증 뱃지 아이콘 --></svg>
		</div>
	{/if}
</div>
```

### 9.3 스켈레톤 로딩

```svelte
{#if loading}
	<div class="h-10 w-10 animate-pulse rounded-full bg-gray-200"></div>
{:else}
	<Avatar uid={user.uid} size={40} />
{/if}
```

### 9.4 이미지 업로드 연동

```svelte
<script>
	interface Props {
		// ... 기존 props
		editable?: boolean;
		onUpload?: (file: File) => void;
	}
</script>

<div
	class="relative"
	onclick={editable ? () => fileInput.click() : undefined}
>
	<Avatar uid={user.uid} size={40} />
	{#if editable}
		<input
			type="file"
			accept="image/*"
			bind:this={fileInput}
			style="display: none"
			onchange={handleFileChange}
		/>
	{/if}
</div>
```

## 10. 관련 문서

- [sonub-firebase-auth.md](./sonub-firebase-auth.md): Firebase Auth 프로필 동기화
- [sonub-setup-firebase.md](./sonub-setup-firebase.md): Firebase 초기 설정
- [Svelte 5 Runes](https://svelte.dev/docs/svelte/$derived): $derived, $state 문법
- [Firebase RTDB onValue](https://firebase.google.com/docs/database/web/read-and-write#listen_for_value_events): 실시간 리스너
- [MDN referrerpolicy](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img#referrerpolicy): Referrer 정책
- [MDN crossorigin](https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/crossorigin): CORS 속성

## 11. 변경 이력

| 버전 | 날짜 | 변경 내용 |
|------|------|-----------|
| 1.0.0 | 2025-11-09 | 초기 구현: Avatar 컴포넌트 생성, top-bar 및 menu 적용 |
| 2.0.0 | 2025-11-09 | 주요 개선: imageLoadFailed 상태 추적, CORS/Referrer 문제 해결 (`referrerpolicy="no-referrer"`, `crossorigin="anonymous"` 추가), Props 단순화 (photoUrl/displayName props 제거), 상세한 디버깅 로그 추가 |

## 12. 트러블슈팅

### 12.1 이미지가 표시되지 않음

**증상:** 빈 원 또는 첫 글자만 표시됨

**확인 사항:**
1. ✅ 브라우저 콘솔에서 `[Avatar] ✅ RTDB 데이터 수신` 로그 확인
2. ✅ `photoUrl` 값이 올바른 URL인지 확인
3. ✅ Network 탭에서 이미지 요청 상태 확인
4. ✅ `shouldShowImage` 값이 `true`인지 확인

**해결:**
- CORS 에러: 이미 `referrerpolicy="no-referrer"` 속성 적용됨
- 404 에러: RTDB의 photoUrl이 잘못됨 → Firebase Console에서 확인
- `imageLoadFailed: true`: 정상 동작 (첫 글자 표시)

### 12.2 실시간 업데이트가 안 됨

**증상:** RTDB에서 photoUrl 변경했는데 화면에 반영 안 됨

**확인 사항:**
1. ✅ `onValue` 리스너가 등록되었는지 콘솔 로그 확인
2. ✅ Firebase Realtime Database 보안 규칙 확인
3. ✅ 네트워크 연결 확인

**해결:**
```bash
# 브라우저 콘솔에서 확인
[Avatar] RTDB 리스너 등록 시작: users/...

# 보안 규칙 확인 (Firebase Console)
{
  "rules": {
    "users": {
      "$uid": {
        ".read": true,
        ".write": "$uid === auth.uid"
      }
    }
  }
}
```

### 12.3 메모리 누수

**증상:** 페이지를 여러 번 이동했을 때 메모리 사용량 계속 증가

**원인:** `unsubscribe()` 호출 안 됨

**해결:**
```typescript
// onMount의 return 함수가 올바르게 구현되었는지 확인
onMount(() => {
	// ... 리스너 등록

	return () => {  // ← 이 부분 필수!
		if (unsubscribe) {
			unsubscribe();
		}
	};
});
```

### 12.4 TypeScript 오류

**증상:** `Property 'style' does not exist on type 'EventTarget'`

**원인:** 이벤트 타입 캐스팅 필요

**해결:** 이미 적용됨
```typescript
function handleImageError(e: Event) {
	// ✅ 타입 캐스팅 적용됨
	imageLoadFailed = true;
}
```

## 13. 성능 고려사항

### 13.1 RTDB 리스너 수

**문제:**
- 화면에 Avatar 컴포넌트가 10개 있으면 10개의 RTDB 리스너 생성
- Firebase RTDB 동시 연결 수 제한 및 비용 발생 가능

**해결책 (향후 개선):**
```typescript
// Context API로 사용자 데이터 공유
// src/lib/contexts/users.svelte.ts
export class UsersCache {
	private cache = new Map<string, UserData>();
	private listeners = new Map<string, Unsubscribe>();

	getUser(uid: string) {
		if (!this.cache.has(uid)) {
			// 리스너가 없으면 생성
			this.subscribeToUser(uid);
		}
		return this.cache.get(uid);
	}

	// ... 구현
}
```

### 13.2 이미지 캐싱

**문제:** 동일한 이미지를 여러 번 다운로드

**해결:** 브라우저가 자동으로 캐싱하지만, 추가 최적화 가능
```html
<img
	src={photoUrl || ''}
	loading="lazy"        <!-- 레이지 로딩 -->
	decoding="async"      <!-- 비동기 디코딩 -->
	...
/>
```

## 14. 보안 고려사항

### 14.1 XSS 방지

**Svelte의 자동 이스케이핑:**
```svelte
<!-- ✅ 안전: Svelte가 자동으로 이스케이프 -->
<img src={photoUrl} alt={displayName} />

<!-- ❌ 위험: @html 사용 금지 -->
{@html userInput}
```

### 14.2 RTDB 보안 규칙

**권장 규칙:**
```json
{
  "rules": {
    "users": {
      "$uid": {
        ".read": true,  // 누구나 읽기 가능 (프로필 공개)
        ".write": "$uid === auth.uid"  // 본인만 수정 가능
      }
    }
  }
}
```

### 14.3 이미지 URL 검증

```typescript
// 향후 개선: URL 유효성 검사
function isValidImageUrl(url: string): boolean {
	try {
		const parsed = new URL(url);
		return ['http:', 'https:'].includes(parsed.protocol);
	} catch {
		return false;
	}
}
```

## 15. 결론

Avatar 컴포넌트는 다음과 같은 문제를 해결했습니다:

1. ✅ **코드 중복 제거**: 재사용 가능한 컴포넌트로 통합
2. ✅ **RTDB 데이터 사용**: Firebase Auth가 아닌 RTDB를 데이터 소스로 사용
3. ✅ **실시간 업데이트**: onValue 리스너로 자동 반영
4. ✅ **이미지 로드 실패 처리**: imageLoadFailed 상태로 자동 fallback
5. ✅ **CORS 문제 해결**: referrerpolicy와 crossorigin 속성으로 Google 이미지 로드
6. ✅ **반응형 설계**: Svelte 5 runes로 깔끔한 로직

**핵심 기술:**
- Svelte 5 runes ($state, $derived, $effect)
- Firebase Realtime Database (onValue)
- CORS/Referrer 정책 이해 및 적용
- 상태 기반 UI 전환 (imageLoadFailed)

**다음 단계:**
- 프로덕션 배포 전 디버깅 로그 제거
- 성능 최적화 (Context API로 리스너 공유)
- 추가 기능 (온라인 상태, 뱃지 등)
