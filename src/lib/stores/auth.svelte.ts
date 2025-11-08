/**
 * 인증 상태 관리 스토어
 *
 * Svelte 5의 runes를 사용하여 Firebase Authentication 상태를 전역으로 관리합니다.
 */

import { auth } from '$lib/firebase';
import { onAuthStateChanged, type User } from 'firebase/auth';

/**
 * 인증 상태 타입 정의
 */
export interface AuthState {
	user: User | null; // 현재 로그인한 사용자 (null이면 비로그인)
	loading: boolean; // 인증 상태 확인 중 여부
	initialized: boolean; // 인증 시스템 초기화 완료 여부
}

/**
 * 인증 상태를 관리하는 클래스
 */
class AuthStore {
	// Svelte 5 runes를 사용한 반응형 상태
	private _state = $state<AuthState>({
		user: null,
		loading: true,
		initialized: false
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
	 * Firebase 인증 상태 변경 리스너 초기화
	 */
	private initializeAuthListener() {
		if (!auth) {
			console.warn('Firebase Auth가 초기화되지 않았습니다.');
			this._state.loading = false;
			this._state.initialized = true;
			return;
		}

		onAuthStateChanged(auth, (user) => {
			this._state.user = user;
			this._state.loading = false;
			this._state.initialized = true;

			if (user) {
				console.log('사용자 로그인됨:', user.uid);
			} else {
				console.log('사용자 로그아웃됨');
			}
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
