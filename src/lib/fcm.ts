// src/lib/fcm.ts
// Firebase Cloud Messaging 유틸리티 함수
// FCM 토큰 발급, 포그라운드 메시지 수신, 토큰 저장 기능 제공

import { browser } from '$app/environment';
import { getFirebaseMessaging } from './firebase';
import { getToken, onMessage, type MessagePayload } from 'firebase/messaging';
import { rtdb } from './firebase';
import { ref, set } from 'firebase/database';
import { auth } from './firebase';

// 환경 변수에서 VAPID Key 가져오기
import { PUBLIC_FIREBASE_VAPID_KEY } from '$env/static/public';

/**
 * 서비스 워커를 미리 등록하고 active 상태를 대기하는 함수
 *
 * @returns {Promise<ServiceWorkerRegistration | null>} 서비스 워커 등록 객체 또는 null
 *
 * 사용 예시:
 * const registration = await registerServiceWorker();
 * if (registration) {
 *   console.log('서비스 워커가 활성화되었습니다');
 * }
 */
export async function registerServiceWorker(): Promise<ServiceWorkerRegistration | null> {
	// 서버 환경 체크
	if (!browser) {
		console.warn('[FCM] 서버 환경에서는 서비스 워커를 등록할 수 없습니다.');
		return null;
	}

	// ServiceWorker API 지원 체크
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
			// waiting 상태의 서비스 워커를 즉시 활성화
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

/**
 * FCM 토큰을 발급받고 Realtime Database에 저장하는 함수
 *
 * @returns {Promise<string | null>} FCM 토큰 또는 null
 *
 * 사용 예시:
 * const token = await requestFcmToken();
 * if (token) {
 *   console.log('FCM 토큰 발급 및 저장 완료:', token);
 * }
 */
export async function requestFcmToken(): Promise<string | null> {
	console.log('[FCM 토큰] 🔵🔵🔵 토큰 발급 시작');

	// 서버 환경 체크
	if (!browser) {
		console.warn('[FCM 토큰] ❌ 서버 환경에서는 토큰을 발급받을 수 없습니다.');
		return null;
	}
	console.log('[FCM 토큰] ✅ 브라우저 환경 확인');

	// Messaging 인스턴스 가져오기
	console.log('[FCM 토큰] 🔵 Messaging 인스턴스 가져오는 중...');
	const messaging = await getFirebaseMessaging();
	if (!messaging) {
		console.error('[FCM 토큰] ❌ Messaging을 초기화할 수 없습니다.');
		return null;
	}
	console.log('[FCM 토큰] ✅ Messaging 인스턴스 확인됨');

	// VAPID Key 확인
	if (!PUBLIC_FIREBASE_VAPID_KEY) {
		console.error('[FCM 토큰] ❌ VAPID Key가 환경 변수에 설정되지 않았습니다.');
		return null;
	}
	console.log('[FCM 토큰] ✅ VAPID Key 확인됨 (앞 10자):', PUBLIC_FIREBASE_VAPID_KEY.substring(0, 10) + '...');

	try {
		// 이미 등록된 서비스 워커 사용, 없으면 새로 등록
		let registration: ServiceWorkerRegistration | undefined;

		if ('serviceWorker' in navigator) {
			console.log('[FCM 토큰] 🔵 서비스 워커 확인 중...');
			// 이미 등록된 서비스 워커가 있는지 확인
			const existingRegistration = await navigator.serviceWorker.getRegistration('/');

			if (existingRegistration) {
				console.log('[FCM 토큰] ✅ 기존 서비스 워커 사용');
				console.log('[FCM 토큰] 서비스 워커 상태:', {
					active: existingRegistration.active?.state,
					installing: existingRegistration.installing?.state,
					waiting: existingRegistration.waiting?.state
				});
				registration = existingRegistration;
				// 서비스 워커가 준비될 때까지 대기
				await navigator.serviceWorker.ready;
				console.log('[FCM 토큰] ✅ 서비스 워커 준비 완료');
			} else {
				// 없으면 새로 등록하고 활성화 대기
				console.log('[FCM 토큰] 🔵 서비스 워커를 새로 등록합니다...');
				registration = await registerServiceWorker() ?? undefined;
			}
		}

		if (!registration) {
			console.error('[FCM 토큰] ❌ 서비스 워커를 사용할 수 없습니다.');
			return null;
		}
		console.log('[FCM 토큰] ✅ 서비스 워커 등록 완료');

		// FCM 토큰 발급
		console.log('[FCM 토큰] 🔵 Firebase getToken() 호출 중...');
		const token = await getToken(messaging, {
			vapidKey: PUBLIC_FIREBASE_VAPID_KEY,
			serviceWorkerRegistration: registration
		});

		if (!token) {
			console.error('[FCM 토큰] ❌ 토큰을 발급받지 못했습니다. 알림 권한을 확인하세요.');
			console.error('[FCM 토큰] Notification.permission:', Notification.permission);
			return null;
		}

		console.log('[FCM 토큰] ✅✅✅ FCM 토큰 발급 완료!');
		console.log('[FCM 토큰] 토큰 전체:', token);
		console.log('[FCM 토큰] 토큰 길이:', token.length);

		// Realtime Database에 토큰 저장
		console.log('[FCM 토큰] 🔵 데이터베이스에 토큰 저장 시작...');
		await saveFcmTokenToDatabase(token);

		return token;
	} catch (error) {
		console.error('[FCM 토큰] ❌❌❌ 토큰 발급 실패:', error);
		console.error('[FCM 토큰] 에러 상세:', {
			name: (error as Error).name,
			message: (error as Error).message,
			stack: (error as Error).stack
		});
		return null;
	}
}

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
 *
 * @param {string} token FCM 토큰
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

/**
 * 포그라운드 메시지 수신 리스너 등록
 *
 * 브라우저 탭이 활성화된 상태(포그라운드)에서 메시지를 받았을 때 호출되는 콜백 함수를 등록합니다.
 *
 * @param {Function} callback 메시지 수신 시 실행할 콜백 함수
 *
 * 사용 예시:
 * subscribeOnMessage((payload) => {
 *   console.log('새 메시지:', payload);
 *   toast(payload.notification?.title ?? '알림');
 * });
 */
export async function subscribeOnMessage(
	callback: (payload: MessagePayload) => void
): Promise<void> {
	if (!browser) {
		console.warn('[FCM] 서버 환경에서는 메시지를 수신할 수 없습니다.');
		return;
	}

	const messaging = await getFirebaseMessaging();
	if (!messaging) {
		console.warn('[FCM] Messaging을 초기화할 수 없습니다.');
		return;
	}

	// onMessage 리스너 등록
	onMessage(messaging, (payload) => {
		console.log('[FCM] 포그라운드 메시지 수신:', payload);
		callback(payload);
	});

	console.log('✅ FCM 포그라운드 메시지 리스너 등록 완료');
}
