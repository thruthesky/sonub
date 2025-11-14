// static/firebase-messaging-sw.js
// Firebase Cloud Messaging 서비스 워커
// 백그라운드 메시지 처리 및 알림 클릭 이벤트 관리

// Firebase SDK v10+ CDN (서비스 워커에서는 ESM import 불가하므로 compat 버전 사용)
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js');

// ⚠️ 중요: 서비스 워커는 환경 변수에 접근할 수 없으므로 firebaseConfig를 직접 입력해야 합니다
// Firebase Console > Project Settings > General > Your apps > Web app에서 복사한 값
firebase.initializeApp({
	apiKey: 'AIzaSyCXAFYnNf7QYcZNpIngCA-lOhb9YRRLDTY',
	authDomain: 'sonub-firebase.firebaseapp.com',
	projectId: 'sonub-firebase',
	storageBucket: 'sonub-firebase.firebasestorage.app',
	messagingSenderId: '406320255657',
	appId: '1:406320255657:web:79b39117a353878b8e8fb8'
});

const messaging = firebase.messaging();

/**
 * 백그라운드 메시지 수신 처리
 * Notification 필드가 있으면, FCM 이 운영체제에 자동 알림하므로, 직접 처리할 필요 없음
 */
// messaging.onBackgroundMessage((payload) => {
// 	console.log('[firebase-messaging-sw.js] 백그라운드 메시지 수신:', payload);

// 	// 알림 제목 및 옵션 설정
// 	const notificationTitle = payload.notification?.title ?? '새 알림';
// 	const notificationOptions = {
// 		body: payload.notification?.body ?? '',
// 		icon: '/favicon-512.png', // static 폴더의 아이콘 경로
// 		badge: '/favicon-64.png',
// 		data: payload.data, // 커스텀 데이터 전달
// 		tag: payload.data?.tag ?? 'default', // 중복 알림 방지
// 		requireInteraction: false, // 자동으로 사라지도록 설정
// 		vibrate: [200, 100, 200] // 진동 패턴 (모바일)
// 	};

// 	// 브라우저 알림 표시
// 	self.registration.showNotification(notificationTitle, notificationOptions);
// });

// 알림 클릭 이벤트 처리
self.addEventListener('notificationclick', (event) => {
	console.log('[firebase-messaging-sw.js] 알림 클릭:', event.notification);

	// 알림 닫기
	event.notification.close();

	// 클릭 액션 URL (payload.data.click_action이 있으면 해당 URL로, 없으면 루트로)
	const urlToOpen = event.notification.data?.click_action || '/';

	// 이미 열려 있는 브라우저 창이 있으면 포커스, 없으면 새 창 열기
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

// 서비스 워커 설치 이벤트
self.addEventListener('install', (event) => {
	console.log('[firebase-messaging-sw.js] 서비스 워커 설치됨');
	self.skipWaiting(); // 즉시 활성화
});

// 서비스 워커 활성화 이벤트
self.addEventListener('activate', (event) => {
	console.log('[firebase-messaging-sw.js] 서비스 워커 활성화됨');
	event.waitUntil(clients.claim()); // 모든 클라이언트에 즉시 적용
});
