---
title: test-user-generator.ts
type: typescript
path: src/lib/utils/test-user-generator.ts
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/utils/test-user-generator.ts`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
/**
 * 테스트 사용자 데이터 생성 유틸리티
 *
 * 테스트 목적으로 임시 사용자 데이터를 생성합니다.
 * 고유한 UID, 이름, 이메일, 성별, 생년도 등을 자동으로 생성합니다.
 */

export interface TestUser {
	uid: string;
	displayName: string;
	email: string;
	photoUrl: string | null;
	gender: 'male' | 'female' | 'other';
	birthYear: number;
	createdAt: number;
	updatedAt: number;
	isTemporary: boolean;
}

/**
 * 테스트 사용자 100명의 데이터를 생성합니다.
 *
 * 각 사용자는 1초 단위로 createdAt 시간 차이를 가지며,
 * displayName에 사용자 번호가 포함됩니다.
 *
 * @returns 테스트 사용자 데이터 배열
 */
export function generateTestUsers(): TestUser[] {
	const users: TestUser[] = [];
	const now = Date.now();

	for (let i = 1; i <= 100; i++) {
		const paddedNumber = String(i).padStart(3, '0');
		const uid = generateTestUserId(i);
		const genders: Array<'male' | 'female' | 'other'> = ['male', 'female', 'other'];

		// 각 사용자마다 1초(1000ms)씩 시간 차이를 두어 생성 시간을 초 단위로 저장
		const timestamp = now + i * 1000;

		users.push({
			uid,
			// displayName에 사용자 생성 번호 포함
			displayName: `테스트 사용자 #${paddedNumber}`,
			email: `test.user.${paddedNumber}@example.com`,
			photoUrl: null, // 필요시 아바타 URL 생성 가능
			gender: genders[Math.floor(Math.random() * genders.length)],
			birthYear: generateRandomBirthYear(), // 1950~2010년 랜덤
			createdAt: timestamp, // 초 단위로 저장 (각 사용자마다 1초씩 차이)
			updatedAt: timestamp,
			isTemporary: true
		});
	}

	return users;
}

/**
 * 테스트 사용자의 고유 ID를 생성합니다.
 * Firebase 형식의 ID를 생성합니다.
 *
 * @param index 사용자 인덱스
 * @returns 고유 ID
 */
function generateTestUserId(index: number): string {
	// Firebase 형식의 고유 ID 생성
	// test 라벨 + 타임스탬프 + 인덱스 조합
	const timestamp = Date.now();
	const randomString = Math.random().toString(36).substring(2, 8);
	return `test_${timestamp}_${index}_${randomString}`;
}

/**
 * 1950년부터 2010년 사이의 랜덤 생년도를 생성합니다.
 *
 * @returns 생년도 (1950~2010)
 */
function generateRandomBirthYear(): number {
	const minYear = 1950;
	const maxYear = 2010;
	return minYear + Math.floor(Math.random() * (maxYear - minYear + 1));
}

/**
 * 테스트 사용자 데이터를 Firebase 저장용 객체로 변환합니다.
 *
 * @param user 테스트 사용자
 * @returns Firebase 저장용 객체
 */
export function testUserToFirebaseData(user: TestUser): Record<string, unknown> {
	return {
		displayName: user.displayName,
		email: user.email,
		photoUrl: user.photoUrl,
		gender: user.gender,
		birthYear: user.birthYear,
		createdAt: user.createdAt,
		updatedAt: user.updatedAt,
		isTemporary: user.isTemporary
	};
}

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
