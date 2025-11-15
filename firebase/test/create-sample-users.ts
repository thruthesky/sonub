/**
 * Firebase Admin SDK 를 사용하여 테스트 사용자 100명을 생성하는 스크립트입니다.
 * `tsx create-sample-users.ts` 로 실행할 수 있으며, service account 키는
 * 현재 디렉터리의 `firebase-service-account-key.json` 을 사용합니다.
 */

import admin, { type ServiceAccount } from 'firebase-admin';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { readFile } from 'node:fs/promises';
import { generateTestUsers, testUserToFirebaseData } from '../../src/lib/utils/test-user-generator';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const SERVICE_ACCOUNT_PATH = path.resolve(__dirname, 'firebase-service-account-key.json');
const USERS_COLLECTION = 'users';
const MIN_BIRTH_YEAR = 1950;
const MAX_BIRTH_YEAR = 2010;
const MIN_BIRTH_MONTH = 1;
const MAX_BIRTH_MONTH = 12;
const MIN_BIRTH_DAY = 1;
const MAX_BIRTH_DAY = 28;

/**
 * service account JSON 을 로드합니다.
 */
async function loadServiceAccount(): Promise<ServiceAccount> {
	const json = await readFile(SERVICE_ACCOUNT_PATH, 'utf-8');
	return JSON.parse(json) as ServiceAccount;
}

/**
 * Firebase Admin 인스턴스를 초기화합니다.
 */
async function initializeFirebase(): Promise<void> {
	if (admin.apps.length > 0) {
		return;
	}

	const serviceAccount = await loadServiceAccount();
	admin.initializeApp({
		credential: admin.credential.cert(serviceAccount)
	});

	const projectId = (serviceAccount as { project_id?: string }).project_id ?? '알 수 없음';
	console.info(`Firebase Admin 초기화 완료 (project: ${projectId})`);
}

/**
 * Firestore 의 users 컬렉션에 테스트 사용자 문서를 저장합니다.
 */
async function createSampleUsers(): Promise<void> {
	const firestore = admin.firestore();
	const testUsers = generateTestUsers();
	const total = testUsers.length;

	for (let index = 0; index < total; index++) {
		const user = testUsers[index];
		const gender = Math.random() < 0.5 ? 'M' : 'F';
		const birthYear = getRandomInt(MIN_BIRTH_YEAR, MAX_BIRTH_YEAR);
		const birthMonth = getRandomInt(MIN_BIRTH_MONTH, MAX_BIRTH_MONTH);
		const birthDay = getRandomInt(MIN_BIRTH_DAY, MAX_BIRTH_DAY);
		const birthYearMonthDay = buildBirthDateNumber(birthYear, birthMonth, birthDay);
		const photoUrl = buildRandomPhotoUrl(user.uid);

		const data = {
			...testUserToFirebaseData(user),
			gender,
			birthYearMonthDay,
			photoUrl,
			test: true
		};

		const docRef = firestore.collection(USERS_COLLECTION).doc(user.uid);
		await docRef.set(data);

		const progress = Math.round(((index + 1) / total) * 100);
		console.info(`(${index + 1}/${total}) ${user.displayName} 저장 완료 - ${progress}%`);
	}

	console.info(`테스트 사용자 ${total}명 생성이 완료되었습니다.`);
}

async function main() {
	try {
		await initializeFirebase();
		await createSampleUsers();
		process.exit(0);
	} catch (error) {
		console.error('테스트 사용자 생성 중 오류가 발생했습니다:', error);
		process.exit(1);
	}
}

void main();

/**
 * 최소/최대 범위에서 랜덤 정수를 반환합니다.
 */
function getRandomInt(min: number, max: number): number {
	const floorMin = Math.ceil(min);
	const floorMax = Math.floor(max);
	return Math.floor(Math.random() * (floorMax - floorMin + 1)) + floorMin;
}

/**
 * picsum.photos 이미지를 반환합니다.
 */
function buildRandomPhotoUrl(seed: string): string {
	return `https://picsum.photos/seed/${seed}/400/400`;
}

/**
 * YYYYMMDD 형태의 숫자를 반환합니다.
 */
function buildBirthDateNumber(year: number, month: number, day: number): number {
	const paddedMonth = String(month).padStart(2, '0');
	const paddedDay = String(day).padStart(2, '0');
	return Number(`${year}${paddedMonth}${paddedDay}`);
}
