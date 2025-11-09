/**
 * 인증 상태 관리 스토어
 *
 * Svelte 5의 runes를 사용하여 Firebase Authentication 상태를 전역으로 관리합니다.
 */

import { auth, rtdb } from '$lib/firebase';
import { onAuthStateChanged, type User } from 'firebase/auth';
import { ref, get } from 'firebase/database';

/**
 * 인증 상태 타입 정의
 */
export interface AuthState {
	user: User | null; // 현재 로그인한 사용자 (null이면 비로그인)
	loading: boolean; // 인증 상태 확인 중 여부
	initialized: boolean; // 인증 시스템 초기화 완료 여부
	adminList: string[]; // 관리자 UID 배열 (/system/settings/admins)
}

/**
 * 인증 상태를 관리하는 클래스
 */
class AuthStore {
	// Svelte 5 runes를 사용한 반응형 상태
	private _state = $state<AuthState>({
		user: null,
		loading: true,
		initialized: false,
		adminList: []
	});

	constructor() {
		// 브라우저 환경에서만 Firebase 인증 상태 리스너 등록
		if (typeof window !== 'undefined') {
			this.initializeAuthListener();
		} else {
			// 서버 환경에서는 초기화만 완료 처리
			this._state.loading = false;
			this._state.initialized = true;
		}
	}

	/**
	 * RTDB에서 관리자 목록 로드
	 *
	 * /system/settings/admins는 객체 형식으로 저장됨:
	 * { "uid-user1": true, "uid-user2": true, ... }
	 *
	 * 이를 UID 배열로 변환하여 저장합니다.
	 * Firebase SDK v9+ 모듈식 API를 사용합니다.
	 */
	private async loadAdminList() {
		if (!rtdb) {
			console.warn('Firebase Realtime Database가 초기화되지 않았습니다.');
			return;
		}

		try {
			// Firebase SDK v9+ 모듈식 API 사용
			const adminRef = ref(rtdb, 'system/settings/admins');
			const snapshot = await get(adminRef);
			const adminsObj = snapshot.val();

			// 객체에서 UID 배열로 변환 (value가 true인 항목만 포함)
			if (adminsObj && typeof adminsObj === 'object') {
				this._state.adminList = Object.keys(adminsObj).filter(uid => adminsObj[uid] === true);
			} else {
				this._state.adminList = [];
			}

			console.log('관리자 목록 로드됨:', this._state.adminList);
		} catch (error) {
			console.error('관리자 목록 로드 실패:', error);
			this._state.adminList = [];
		}
	}

	/**
	 * Firebase 인증 상태 변경 리스너 초기화
	 */
	private initializeAuthListener() {
		if (!auth) {
			console.warn('Firebase Auth가 초기화되지 않았습니다.');
			this._state.loading = false;
			this._state.initialized = true;
			return;
		}

		onAuthStateChanged(auth, async (user) => {
			this._state.user = user;

			// 사용자 로그인 시 관리자 목록 로드
			if (user) {
				console.log('사용자 로그인됨:', user.uid);
				await this.loadAdminList();
			} else {
				console.log('사용자 로그아웃됨');
				this._state.adminList = [];
			}

			this._state.loading = false;
			this._state.initialized = true;
		});
	}

	/**
	 * 현재 인증 상태 가져오기
	 */
	get state(): AuthState {
		return this._state;
	}

	/**
	 * 현재 사용자 가져오기
	 */
	get user(): User | null {
		return this._state.user;
	}

	/**
	 * 로딩 상태 가져오기
	 */
	get loading(): boolean {
		return this._state.loading;
	}

	/**
	 * 초기화 상태 가져오기
	 */
	get initialized(): boolean {
		return this._state.initialized;
	}

	/**
	 * 사용자가 로그인했는지 확인
	 */
	get isAuthenticated(): boolean {
		return this._state.user !== null;
	}

	/**
	 * 현재 사용자가 관리자인지 확인
	 * 로그인되어 있고, 관리자 목록에 포함되어 있으면 true
	 */
	get isAdmin(): boolean {
		return this.isAuthenticated && this._state.adminList.includes(this._state.user?.uid ?? '');
	}

	/**
	 * 관리자 목록 가져오기
	 */
	get adminList(): string[] {
		return this._state.adminList;
	}
}

/**
 * 인증 스토어 인스턴스 export
 *
 * 사용 예:
 * import { authStore } from '$lib/stores/auth.svelte';
 *
 * const user = authStore.user;
 * const isLoggedIn = authStore.isAuthenticated;
 */
export const authStore = new AuthStore();
