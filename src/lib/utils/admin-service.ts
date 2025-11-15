/**
 * 관리자 서비스 - Firestore 연동
 *
 * 테스트 사용자 생성, 사용자 목록 조회 등의 관리자 기능을 담당합니다.
 */

import { db } from '$lib/firebase';
import { doc, setDoc, getDoc, deleteDoc, collection, getDocs, query, where } from 'firebase/firestore';
import type { TestUser } from './test-user-generator';
import { testUserToFirebaseData } from './test-user-generator';

/**
 * 테스트 사용자를 Firestore에 저장합니다.
 *
 * @param users 저장할 테스트 사용자 배열
 * @param onProgress 진행 상황 콜백 함수
 * @returns 저장 완료 여부
 */
export async function saveTestUsersToFirebase(
	users: TestUser[],
	onProgress?: (index: number, total: number) => void
): Promise<boolean> {
	if (!db) {
		console.error('Firestore가 초기화되지 않았습니다.');
		return false;
	}

	try {
		// 각 사용자를 순차적으로 저장
		for (let i = 0; i < users.length; i++) {
			const user = users[i];
			const userRef = doc(db, `users/${user.uid}`);
			const firebaseData = testUserToFirebaseData(user);

			await setDoc(userRef, firebaseData);

			// 진행 상황 콜백
			if (onProgress) {
				onProgress(i + 1, users.length);
			}
		}

		return true;
	} catch (error) {
		console.error('테스트 사용자 저장 중 오류 발생:', error);
		throw error;
	}
}

/**
 * Firestore에서 모든 임시 사용자를 조회합니다.
 *
 * @returns 임시 사용자 데이터
 */
export async function getTemporaryUsers(): Promise<Record<string, TestUser>> {
	if (!db) {
		console.error('Firestore가 초기화되지 않았습니다.');
		return {};
	}

	try {
		// isTemporary가 true인 사용자만 쿼리
		const usersRef = collection(db, 'users');
		const q = query(usersRef, where('isTemporary', '==', true));
		const snapshot = await getDocs(q);

		if (snapshot.empty) {
			return {};
		}

		const temporaryUsers: Record<string, TestUser> = {};

		// 문서를 순회하며 데이터 추출
		snapshot.forEach((doc) => {
			const uid = doc.id;
			const user = doc.data();
			temporaryUsers[uid] = {
				uid,
				displayName: (user.displayName as string) || '',
				email: (user.email as string) || '',
				photoUrl: (user.photoUrl as string | null) || null,
				gender: (user.gender as 'male' | 'female' | 'other') || 'other',
				birthYear: (user.birthYear as number) || 0,
				createdAt: (user.createdAt as number) || 0,
				updatedAt: (user.updatedAt as number) || 0,
				isTemporary: true
			};
		});

		return temporaryUsers;
	} catch (error) {
		console.error('임시 사용자 조회 중 오류 발생:', error);
		throw error;
	}
}

/**
 * 특정 사용자를 Firestore에서 삭제합니다.
 *
 * @param uid 삭제할 사용자의 UID
 * @returns 삭제 완료 여부
 */
export async function deleteUserByUid(uid: string): Promise<boolean> {
	if (!db) {
		console.error('Firestore가 초기화되지 않았습니다.');
		return false;
	}

	try {
		const userRef = doc(db, `users/${uid}`);
		await deleteDoc(userRef);
		return true;
	} catch (error) {
		console.error('사용자 삭제 중 오류 발생:', error);
		throw error;
	}
}

/**
 * 모든 임시 사용자를 삭제합니다.
 *
 * @param onProgress 진행 상황 콜백 함수
 * @returns 삭제 완료 여부
 */
export async function deleteAllTemporaryUsers(
	onProgress?: (deleted: number, total: number) => void
): Promise<boolean> {
	if (!db) {
		console.error('Firestore가 초기화되지 않았습니다.');
		return false;
	}

	try {
		const temporaryUsers = await getTemporaryUsers();
		const userIds = Object.keys(temporaryUsers);
		const total = userIds.length;

		// 각 사용자를 순차적으로 삭제
		for (let i = 0; i < total; i++) {
			const uid = userIds[i];
			await deleteUserByUid(uid);

			// 진행 상황 콜백
			if (onProgress) {
				onProgress(i + 1, total);
			}
		}

		return true;
	} catch (error) {
		console.error('임시 사용자 삭제 중 오류 발생:', error);
		throw error;
	}
}

/**
 * 임시 사용자의 개수를 반환합니다.
 *
 * @returns 임시 사용자 개수
 */
export async function getTemporaryUserCount(): Promise<number> {
	const temporaryUsers = await getTemporaryUsers();
	return Object.keys(temporaryUsers).length;
}
