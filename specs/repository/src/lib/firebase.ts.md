---
name: firebase.ts
description: firebase 파일
version: 1.0.0
type: typescript
category: other
original_path: src/lib/firebase.ts
---

# firebase.ts

## 개요

**파일 경로**: `src/lib/firebase.ts`
**파일 타입**: typescript
**카테고리**: other

firebase 파일

## 소스 코드

```typescript
/**
 * Firebase 초기화 및 서비스 인스턴스
 *
 * 이 파일은 Firebase 앱을 초기화하고 필요한 서비스들을 export합니다.
 * - 중복 초기화 방지
 * - 브라우저 환경 체크 (SSR 대응)
 */

import { browser } from '$app/environment';
import { initializeApp, getApps, type FirebaseApp } from 'firebase/app';

// SvelteKit 환경 변수 (PUBLIC_ 접두사가 있는 환경 변수)
import {
	PUBLIC_FIREBASE_API_KEY,
	PUBLIC_FIREBASE_AUTH_DOMAIN,
	PUBLIC_FIREBASE_PROJECT_ID,
	PUBLIC_FIREBASE_STORAGE_BUCKET,
	PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
	PUBLIC_FIREBASE_APP_ID,
	PUBLIC_FIREBASE_MEASUREMENT_ID,
	PUBLIC_FIREBASE_DATABASE_URL
} from '$env/static/public';

// Authentication
import { getAuth, type Auth } from 'firebase/auth';

// Firestore
import { getFirestore, type Firestore } from 'firebase/firestore';

// Realtime Database
import { getDatabase, type Database } from 'firebase/database';

// Storage
import { getStorage, type FirebaseStorage } from 'firebase/storage';

// Analytics (브라우저에서만 사용)
import { getAnalytics, type Analytics } from 'firebase/analytics';

/**
 * Firebase 설정
 * SvelteKit 환경 변수에서 값을 가져옵니다.
 */
const firebaseConfig = {
	apiKey: PUBLIC_FIREBASE_API_KEY,
	authDomain: PUBLIC_FIREBASE_AUTH_DOMAIN,
	projectId: PUBLIC_FIREBASE_PROJECT_ID,
	storageBucket: PUBLIC_FIREBASE_STORAGE_BUCKET,
	messagingSenderId: PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
	appId: PUBLIC_FIREBASE_APP_ID,
	measurementId: PUBLIC_FIREBASE_MEASUREMENT_ID,
	databaseURL: PUBLIC_FIREBASE_DATABASE_URL
};

// 디버깅: Firebase 설정 확인
if (browser) {
	console.log('✅ Firebase 환경 변수 로드 성공');
	console.log('Firebase Config:', {
		apiKey: firebaseConfig.apiKey ? '✓ Loaded' : '✗ Missing',
		authDomain: firebaseConfig.authDomain ? '✓ Loaded' : '✗ Missing',
		projectId: firebaseConfig.projectId ? '✓ Loaded' : '✗ Missing',
		appId: firebaseConfig.appId ? '✓ Loaded' : '✗ Missing'
	});
}

/**
 * Firebase 앱 초기화
 * 중복 초기화 방지: getApps()로 기존 앱 확인
 */
let app: FirebaseApp;

if (browser) {
	// 브라우저 환경에서만 Firebase 초기화
	app = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
} else {
	// 서버 환경에서는 더미 앱 생성 (사용되지 않음)
	app = {} as FirebaseApp;
}

/**
 * Authentication 서비스
 * 브라우저 환경에서만 초기화
 */
export const auth: Auth | null = browser ? getAuth(app) : null;

/**
 * Firestore 서비스
 * 브라우저 환경에서만 초기화
 */
export const db: Firestore | null = browser ? getFirestore(app) : null;

/**
 * Realtime Database 서비스
 * 브라우저 환경에서만 초기화
 */
export const rtdb: Database | null = browser ? getDatabase(app) : null;

/**
 * Storage 서비스
 * 브라우저 환경에서만 초기화
 */
export const storage: FirebaseStorage | null = browser ? getStorage(app) : null;

/**
 * Analytics 서비스
 * 브라우저 환경에서만 초기화
 */
export const analytics: Analytics | null = browser ? getAnalytics(app) : null;

/**
 * Firebase 앱 인스턴스 export (고급 사용)
 */
export default app;

// ==================== FCM Messaging 추가 ====================

import {
	getMessaging,
	isSupported as isMessagingSupported,
	type Messaging
} from 'firebase/messaging';

/**
 * Firebase Cloud Messaging 서비스
 * 브라우저 환경에서만 초기화하고, Messaging API 지원 여부를 체크합니다.
 * Safari 등 일부 브라우저는 FCM을 지원하지 않을 수 있습니다.
 */
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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
