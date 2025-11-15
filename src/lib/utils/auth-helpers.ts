/**
 * Firebase Authentication 헬퍼 함수
 *
 * 이 파일은 Google 및 Apple 로그인에 필요한 유틸리티 함수를 제공합니다.
 */

import { auth } from '$lib/firebase';
import {
	GoogleAuthProvider,
	OAuthProvider,
	signInWithPopup,
	signOut as firebaseSignOut,
	type UserCredential
} from 'firebase/auth';

/**
 * 브라우저 언어 코드를 Firebase 지원 언어로 변환
 *
 * 지원 언어: ko, ja, zh
 * 기본 언어: en
 *
 * @returns {string} 언어 코드 (ko, ja, zh, en)
 */
export function getAuthLanguage(): string {
	// 브라우저 언어 가져오기
	const browserLang = navigator.language.split('-')[0].toLowerCase();

	// 지원 언어 목록
	const supportedLanguages = ['ko', 'ja', 'zh'];

	// 지원 언어인 경우 해당 언어 반환, 아니면 영어 반환
	return supportedLanguages.includes(browserLang) ? browserLang : 'en';
}

/**
 * Google 로그인 처리
 *
 * Firebase Authentication을 사용하여 Google 계정으로 로그인합니다.
 * 팝업 방식을 사용하며, 사용자 언어 설정을 적용합니다.
 *
 * @returns {Promise<UserCredential>} 사용자 인증 정보
 * @throws {Error} 로그인 실패 시 에러 발생
 */
export async function signInWithGoogle(): Promise<UserCredential> {
	if (!auth) {
		throw new Error('Firebase Auth가 초기화되지 않았습니다. 브라우저 환경에서만 사용 가능합니다.');
	}

	try {
		// Google Provider 생성
		const provider = new GoogleAuthProvider();

		// 언어 설정
		const locale = getAuthLanguage();
		provider.setCustomParameters({
			locale: locale
		});

		// 팝업 방식으로 로그인
		const result = await signInWithPopup(auth, provider);

		// console.log('Google 로그인 성공:', result.user.uid);
		return result;
	} catch (error: any) {
		console.error('Google 로그인 실패:', error);
		throw error;
	}
}

/**
 * Apple 로그인 처리
 *
 * Firebase Authentication을 사용하여 Apple 계정으로 로그인합니다.
 * 팝업 방식을 사용하며, 사용자 언어 설정을 적용합니다.
 *
 * @returns {Promise<UserCredential>} 사용자 인증 정보
 * @throws {Error} 로그인 실패 시 에러 발생
 */
export async function signInWithApple(): Promise<UserCredential> {
	if (!auth) {
		throw new Error('Firebase Auth가 초기화되지 않았습니다. 브라우저 환경에서만 사용 가능합니다.');
	}

	try {
		// Apple Provider 생성
		const provider = new OAuthProvider('apple.com');

		// 언어 설정
		const locale = getAuthLanguage();
		provider.setCustomParameters({
			locale: locale
		});

		// 사용자 정보 요청 스코프 설정
		provider.addScope('email');
		provider.addScope('name');

		// 팝업 방식으로 로그인
		const result = await signInWithPopup(auth, provider);

		// console.log('Apple 로그인 성공:', result.user.uid);
		return result;
	} catch (error: any) {
		console.error('Apple 로그인 실패:', error);
		throw error;
	}
}

/**
 * 로그아웃 처리
 *
 * Firebase Authentication에서 현재 사용자를 로그아웃합니다.
 *
 * @returns {Promise<void>}
 * @throws {Error} 로그아웃 실패 시 에러 발생
 */
export async function signOut(): Promise<void> {
	if (!auth) {
		throw new Error('Firebase Auth가 초기화되지 않았습니다. 브라우저 환경에서만 사용 가능합니다.');
	}

	try {
		await firebaseSignOut(auth);
		// console.log('로그아웃 성공');
	} catch (error: any) {
		console.error('로그아웃 실패:', error);
		throw error;
	}
}

/**
 * Firebase 에러 코드를 사용자 친화적인 메시지로 변환
 *
 * @param {string} errorCode - Firebase 에러 코드
 * @param {string} provider - 로그인 제공자 ('google' | 'apple')
 * @returns {string} 한글 에러 메시지
 */
export function getAuthErrorMessage(errorCode: string, provider: 'google' | 'apple'): string {
	const providerName = provider === 'google' ? 'Google' : 'Apple';

	switch (errorCode) {
		case 'auth/popup-closed-by-user':
			return '로그인 창이 닫혔습니다. 다시 시도해주세요.';
		case 'auth/popup-blocked':
			return '팝업이 차단되었습니다. 팝업 차단을 해제하고 다시 시도해주세요.';
		case 'auth/cancelled-popup-request':
			return '로그인 요청이 취소되었습니다.';
		case 'auth/account-exists-with-different-credential':
			return `이미 다른 방법으로 가입된 이메일입니다. 다른 로그인 방법을 사용해주세요.`;
		case 'auth/invalid-credential':
			return '인증 정보가 올바르지 않습니다.';
		case 'auth/operation-not-allowed':
			return `${providerName} 로그인이 활성화되지 않았습니다. 관리자에게 문의하세요.`;
		case 'auth/user-disabled':
			return '비활성화된 계정입니다. 관리자에게 문의하세요.';
		case 'auth/network-request-failed':
			return '네트워크 연결을 확인하고 다시 시도해주세요.';
		default:
			return `${providerName} 로그인에 실패했습니다. 다시 시도해주세요.`;
	}
}
