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
 * @returns 테스트 사용자 데이터 배열
 */
export function generateTestUsers(): TestUser[] {
	const users: TestUser[] = [];
	const now = Date.now();

	for (let i = 1; i <= 100; i++) {
		const paddedNumber = String(i).padStart(3, '0');
		const uid = generateTestUserId(i);
		const genders: Array<'male' | 'female' | 'other'> = ['male', 'female', 'other'];

		users.push({
			uid,
			displayName: `테스트 사용자 ${paddedNumber}`,
			email: `test.user.${paddedNumber}@example.com`,
			photoUrl: null, // 필요시 아바타 URL 생성 가능
			gender: genders[Math.floor(Math.random() * genders.length)],
			birthYear: generateRandomBirthYear(), // 1950~2010년 랜덤
			createdAt: now,
			updatedAt: now,
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
