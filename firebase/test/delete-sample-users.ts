/**
 * Firestore 의 `users` 컬렉션에서 `test: true` 로 표시된 사용자 문서를 삭제합니다.
 * `tsx delete-sample-users.ts` 명령으로 실행하며, 동일 디렉터리의 service account 키를 사용합니다.
 */

import admin, { type ServiceAccount } from 'firebase-admin';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { readFile } from 'node:fs/promises';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const SERVICE_ACCOUNT_PATH = path.resolve(__dirname, 'firebase-service-account-key.json');
const USERS_COLLECTION = 'users';

/**
 * service account JSON 을 로드합니다.
 */
async function loadServiceAccount(): Promise<ServiceAccount> {
	const json = await readFile(SERVICE_ACCOUNT_PATH, 'utf-8');
	return JSON.parse(json) as ServiceAccount;
}

/**
 * Firebase Admin 초기화
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
 * `test: true` 플래그가 설정된 사용자 문서를 삭제합니다.
 */
async function deleteTestUsers(): Promise<void> {
	const firestore = admin.firestore();
	const snapshot = await firestore.collection(USERS_COLLECTION).where('test', '==', true).get();

	if (snapshot.empty) {
		console.info('삭제할 테스트 사용자 문서가 없습니다.');
		return;
	}

	const targetDocs = snapshot.docs.filter((doc) => doc.get('test') === true);

	if (targetDocs.length === 0) {
		console.info('test 플래그가 설정된 사용자 문서가 없습니다.');
		return;
	}

	const total = targetDocs.length;

	for (let index = 0; index < total; index++) {
		const doc = targetDocs[index];
		await doc.ref.delete();
		const progress = Math.round(((index + 1) / total) * 100);
		console.info(`(${index + 1}/${total}) ${doc.id} 삭제 완료 - ${progress}%`);
	}

	console.info(`test 플래그가 설정된 사용자 ${total}명을 삭제했습니다.`);
}

async function main() {
	try {
		await initializeFirebase();
		await deleteTestUsers();
		process.exit(0);
	} catch (error) {
		console.error('테스트 사용자 삭제 중 오류가 발생했습니다:', error);
		process.exit(1);
	}
}

void main();
