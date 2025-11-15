/**
 * Firestore 유틸리티
 *
 * Firestore 데이터베이스 읽기, 쓰기, 업데이트, 삭제 및 실시간 구독 기능을 제공합니다.
 *
 * 사용법:
 * ```typescript
 * import { writeDocument, updateDocument, deleteDocument, addDocument, createFirestoreStore } from '$lib/stores/firestore.svelte';
 *
 * // 문서 쓰기
 * await writeDocument('users/user123', { name: 'John', age: 30 });
 *
 * // 실시간 구독 (제네릭 타입 사용)
 * const user = createFirestoreStore<UserData>('users/user123');
 *
 * {#if $user.loading}
 *   <p>로딩 중...</p>
 * {:else if $user.data}
 *   <div>{$user.data.name}</div>
 * {/if}
 * ```
 */

import { writable, type Writable } from 'svelte/store';
import { db } from '$lib/firebase';
import {
	doc,
	collection,
	setDoc,
	updateDoc,
	deleteDoc,
	addDoc,
	getDoc,
	onSnapshot,
	writeBatch,
	query,
	where,
	orderBy,
	limit,
	startAfter,
	endBefore,
	type DocumentReference,
	type CollectionReference,
	type Query,
	type QueryConstraint,
	type Unsubscribe,
	type DocumentData,
	type WhereFilterOp,
	type OrderByDirection,
	serverTimestamp,
	Timestamp
} from 'firebase/firestore';

// firestore.types.ts에서 가져온 타입 재사용
import type { FirestoreStoreState, DataOperationResult, AddDocumentResult, ReadDocumentResult } from '$lib/types/firestore.types';

/**
 * 실시간 스토어 타입
 * Svelte 스토어에 unsubscribe 메서드를 추가한 타입
 * @template T - 데이터 타입
 */
export interface FirestoreStore<T> extends Writable<FirestoreStoreState<T>> {
	/** Firestore 리스너 구독 해제 함수 */
	unsubscribe: () => void;
}

/**
 * Firestore 경로를 파싱하여 DocumentReference 또는 CollectionReference 반환
 *
 * Firestore는 collection/document 구조를 가지므로 경로를 파싱해야 합니다:
 * - 홀수 개의 세그먼트 = Collection 참조 (예: 'users', 'chats/room123/messages')
 * - 짝수 개의 세그먼트 = Document 참조 (예: 'users/user123', 'chats/room123/messages/msg456')
 *
 * @param path - Firestore 경로 (예: 'users/user123', 'chats/room123/messages')
 * @returns DocumentReference | CollectionReference
 *
 * @example
 * ```typescript
 * parsePath('users/user123') // DocumentReference
 * parsePath('users') // CollectionReference
 * parsePath('chats/room123/messages/msg456') // DocumentReference (subcollection)
 * ```
 */
function parsePath(path: string): DocumentReference | CollectionReference {
	if (!db) {
		throw new Error('Firestore가 초기화되지 않았습니다.');
	}

	const segments = path.split('/').filter(s => s.length > 0);

	// 짝수 개 세그먼트 = Document
	if (segments.length % 2 === 0) {
		// TypeScript spread 타입 추론을 위해 첫 번째 인자를 명시
		return doc(db, segments[0], ...segments.slice(1)) as DocumentReference;
	}
	// 홀수 개 세그먼트 = Collection
	else {
		// TypeScript spread 타입 추론을 위해 첫 번째 인자를 명시
		return collection(db, segments[0], ...segments.slice(1)) as CollectionReference;
	}
}

/**
 * 실시간 문서 구독을 위한 스토어 생성
 *
 * Firestore 문서의 변경사항을 실시간으로 추적합니다.
 * loading, error 상태를 자동으로 추적합니다.
 *
 * @template T - 데이터 타입
 * @param path - Firestore 문서 경로 (예: 'users/user123', 'chats/room123')
 * @param defaultValue - 문서가 존재하지 않을 때 사용할 기본값 (선택적)
 * @returns Svelte 스토어 (구독 가능) - { data, loading, error }
 *
 * @example
 * ```typescript
 * // 기본 사용법 (제네릭 타입 사용)
 * const user = createFirestoreStore<UserData>('users/user123');
 * // $user는 { data: UserData | null, loading: boolean, error: Error | null }
 *
 * // 기본값 사용 (문서가 없으면 기본값 반환)
 * const settings = createFirestoreStore<Settings>('settings/app', { theme: 'light' });
 *
 * {#if $user.loading}
 *   <p>로딩 중...</p>
 * {:else if $user.error}
 *   <p>에러: {$user.error.message}</p>
 * {:else if $user.data}
 *   <p>{$user.data.displayName}</p>
 * {/if}
 * ```
 */
export function createFirestoreStore<T = DocumentData>(
	path: string,
	defaultValue?: T
): FirestoreStore<T> {
	// Firestore가 초기화되지 않은 경우 에러 처리
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		const { subscribe, set: setStore } = writable<FirestoreStoreState<T>>({
			data: defaultValue ?? null,
			loading: false,
			error: new Error('Firestore가 초기화되지 않았습니다.')
		});

		return {
			subscribe,
			set: setStore,
			update: (_updater) => {
				throw new Error('직접 업데이트는 지원하지 않습니다. Firestore를 통해 데이터를 변경하세요.');
			},
			unsubscribe: () => {}
		};
	}

	// 초기 상태: data는 defaultValue 또는 null, loading은 true, error는 null
	const { subscribe, set: setStore } = writable<FirestoreStoreState<T>>({
		data: defaultValue ?? null,
		loading: true,
		error: null
	});

	let unsubscribeFn: Unsubscribe | null = null;

	try {
		const docRef = parsePath(path) as DocumentReference;

		// Document가 아닌 Collection 경로가 전달된 경우 에러
		if (path.split('/').filter(s => s.length > 0).length % 2 !== 0) {
			throw new Error(`createFirestoreStore는 Document 경로만 지원합니다. Collection 경로가 전달되었습니다: ${path}`);
		}

		// 문서 변경 실시간 감지
		unsubscribeFn = onSnapshot(
			docRef,
			(snapshot) => {
				// 문서 로드 성공
				if (snapshot.exists()) {
					const data = snapshot.data() as T;
					setStore({
						data,
						loading: false,
						error: null
					});
					// console.log(`✅ 실시간 문서 로드 성공: ${path}`, data);
				} else {
					// 문서가 존재하지 않음
					setStore({
						data: defaultValue ?? null,
						loading: false,
						error: null
					});
					// console.log(`ℹ️ 문서가 존재하지 않음: ${path}`);
				}
			},
			(error) => {
				// 문서 로드 실패 (권한 오류, 네트워크 오류 등)
				console.error(`❌ 실시간 문서 로드 실패: ${path}`, error);
				setStore({
					data: defaultValue ?? null,
					loading: false,
					error: error as Error
				});
			}
		);
	} catch (error) {
		console.error(`❌ Firestore 스토어 생성 오류: ${path}`, error);
		setStore({
			data: defaultValue ?? null,
			loading: false,
			error: error as Error
		});
	}

	return {
		subscribe,
		set: setStore,
		update: (_updater) => {
			throw new Error('직접 업데이트는 지원하지 않습니다. Firestore를 통해 데이터를 변경하세요.');
		},
		// 컴포넌트 언마운트 시 구독 해제 (메모리 누수 방지)
		unsubscribe: () => {
			if (unsubscribeFn) {
				unsubscribeFn();
			}
		}
	};
}

/**
 * createFirestoreStore() 함수의 alias (별칭)
 *
 * 더 짧은 이름으로 실시간 문서 구독을 할 수 있습니다.
 * createFirestoreStore()와 동일하게 작동합니다.
 *
 * @template T - 데이터 타입
 * @param path - Firestore 문서 경로
 * @param defaultValue - 문서가 존재하지 않을 때 사용할 기본값 (선택적)
 * @returns Svelte 스토어 (구독 가능) - { data, loading, error }
 *
 * @example
 * ```typescript
 * // firestoreStore() 사용 (짧은 이름)
 * const user = firestoreStore<UserData>('users/user123');
 *
 * // createFirestoreStore() 사용 (명시적 이름)
 * const user = createFirestoreStore<UserData>('users/user123');
 * ```
 */
export const firestoreStore = createFirestoreStore;

/**
 * 문서 쓰기 (기존 문서 덮어쓰기)
 *
 * 지정된 경로에 문서를 저장합니다. 기존 문서가 있으면 덮어씁니다.
 *
 * @param path - Firestore 문서 경로 (예: 'users/user123')
 * @param data - 저장할 데이터
 * @param merge - true이면 기존 데이터와 병합, false이면 덮어쓰기 (기본값: false)
 * @returns 작업 결과
 *
 * @example
 * ```typescript
 * const result = await writeDocument('users/user123', {
 *   displayName: 'John Doe',
 *   age: 30,
 *   email: 'john@example.com'
 * });
 *
 * if (result.success) {
 *   console.log('문서 저장 성공');
 * } else {
 *   console.error('문서 저장 실패:', result.error);
 * }
 * ```
 */
export async function writeDocument(
	path: string,
	data: DocumentData,
	merge: boolean = false
): Promise<DataOperationResult> {
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		return { success: false, error: 'Firestore가 초기화되지 않았습니다.' };
	}

	try {
		const docRef = parsePath(path) as DocumentReference;

		// Collection 경로가 전달된 경우 에러
		if (path.split('/').filter(s => s.length > 0).length % 2 !== 0) {
			throw new Error(`writeDocument는 Document 경로만 지원합니다. Collection 경로가 전달되었습니다: ${path}`);
		}

		await setDoc(docRef, data, { merge });
		// console.log(`✅ 문서 쓰기 성공: ${path}`);
		return { success: true };
	} catch (error) {
		console.error('❌ 문서 쓰기 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 문서 업데이트 (부분 업데이트)
 *
 * 지정된 경로의 문서를 부분적으로 업데이트합니다.
 * 기존 데이터를 유지하면서 지정된 필드만 변경합니다.
 *
 * @param path - Firestore 문서 경로
 * @param updates - 업데이트할 필드들 { field1: value1, field2: value2 }
 * @returns 작업 결과
 *
 * @example
 * ```typescript
 * // users/user123 문서에서 age 필드만 업데이트
 * const result = await updateDocument('users/user123', { age: 31 });
 *
 * if (result.success) {
 *   console.log('업데이트 성공');
 * }
 * ```
 */
export async function updateDocument(
	path: string,
	updates: Record<string, any>
): Promise<DataOperationResult> {
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		return { success: false, error: 'Firestore가 초기화되지 않았습니다.' };
	}

	try {
		const docRef = parsePath(path) as DocumentReference;

		// Collection 경로가 전달된 경우 에러
		if (path.split('/').filter(s => s.length > 0).length % 2 !== 0) {
			throw new Error(`updateDocument는 Document 경로만 지원합니다. Collection 경로가 전달되었습니다: ${path}`);
		}

		await updateDoc(docRef, updates);
		// console.log(`✅ 문서 업데이트 성공: ${path}`, updates);
		return { success: true };
	} catch (error) {
		console.error('❌ 문서 업데이트 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 문서 삭제
 *
 * 지정된 경로의 문서를 삭제합니다.
 *
 * @param path - Firestore 문서 경로
 * @returns 작업 결과
 *
 * @example
 * ```typescript
 * // users/user123 문서 삭제
 * const result = await deleteDocument('users/user123');
 *
 * if (result.success) {
 *   console.log('삭제 성공');
 * }
 * ```
 */
export async function deleteDocument(path: string): Promise<DataOperationResult> {
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		return { success: false, error: 'Firestore가 초기화되지 않았습니다.' };
	}

	try {
		const docRef = parsePath(path) as DocumentReference;

		// Collection 경로가 전달된 경우 에러
		if (path.split('/').filter(s => s.length > 0).length % 2 !== 0) {
			throw new Error(`deleteDocument는 Document 경로만 지원합니다. Collection 경로가 전달되었습니다: ${path}`);
		}

		await deleteDoc(docRef);
		// console.log(`✅ 문서 삭제 성공: ${path}`);
		return { success: true };
	} catch (error) {
		console.error('❌ 문서 삭제 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 새 문서 추가 (자동 생성된 ID 사용)
 *
 * 지정된 Collection에 새로운 문서를 추가합니다.
 * Firestore가 자동으로 고유한 ID를 생성합니다.
 *
 * @param path - Firestore Collection 경로 (예: 'users', 'chats/room123/messages')
 * @param data - 저장할 데이터
 * @returns 작업 결과 및 생성된 문서 ID
 *
 * @example
 * ```typescript
 * // users Collection에 새 사용자 추가
 * const result = await addDocument('users', {
 *   displayName: 'Jane Doe',
 *   email: 'jane@example.com'
 * });
 *
 * if (result.success) {
 *   console.log('생성된 문서 ID:', result.id);
 * }
 * ```
 */
export async function addDocument(path: string, data: DocumentData): Promise<AddDocumentResult> {
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		return { success: false, error: 'Firestore가 초기화되지 않았습니다.' };
	}

	try {
		const colRef = parsePath(path) as CollectionReference;

		// Document 경로가 전달된 경우 에러
		if (path.split('/').filter(s => s.length > 0).length % 2 === 0) {
			throw new Error(`addDocument는 Collection 경로만 지원합니다. Document 경로가 전달되었습니다: ${path}`);
		}

		const docRef = await addDoc(colRef, data);
		// console.log(`✅ 문서 추가 성공: ${path}, 생성된 ID: ${docRef.id}`);
		return { success: true, id: docRef.id };
	} catch (error) {
		console.error('❌ 문서 추가 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 문서 읽기 (한 번만)
 *
 * 지정된 경로의 문서를 한 번만 읽습니다.
 * 실시간 구독이 필요 없을 때 사용합니다.
 *
 * @template T - 데이터 타입
 * @param path - Firestore 문서 경로
 * @returns 작업 결과 및 데이터
 *
 * @example
 * ```typescript
 * // users/user123 문서 한 번만 읽기
 * const result = await readDocument<UserData>('users/user123');
 *
 * if (result.success && result.data) {
 *   console.log('사용자 이름:', result.data.displayName);
 * } else if (result.success && !result.data) {
 *   console.log('문서가 존재하지 않습니다.');
 * } else {
 *   console.error('읽기 실패:', result.error);
 * }
 * ```
 */
export async function readDocument<T = DocumentData>(path: string): Promise<ReadDocumentResult<T>> {
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		return { success: false, error: 'Firestore가 초기화되지 않았습니다.' };
	}

	try {
		const docRef = parsePath(path) as DocumentReference;

		// Collection 경로가 전달된 경우 에러
		if (path.split('/').filter(s => s.length > 0).length % 2 !== 0) {
			throw new Error(`readDocument는 Document 경로만 지원합니다. Collection 경로가 전달되었습니다: ${path}`);
		}

		const snapshot = await getDoc(docRef);

		if (snapshot.exists()) {
			// console.log(`✅ 문서 읽기 성공: ${path}`);
			return { success: true, data: snapshot.data() as T };
		} else {
			// console.log(`ℹ️ 문서가 존재하지 않음: ${path}`);
			return { success: true, data: null };
		}
	} catch (error) {
		console.error('❌ 문서 읽기 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 쿼리 옵션 인터페이스
 */
export interface QueryOptions {
	/** WHERE 조건들 */
	where?: Array<{
		field: string;
		operator: WhereFilterOp;
		value: any;
	}>;
	/** ORDER BY 조건들 */
	orderBy?: Array<{
		field: string;
		direction?: OrderByDirection;
	}>;
	/** 제한할 문서 개수 */
	limit?: number;
	/** 시작 지점 (pagination) - 문서 스냅샷 */
	startAfter?: DocumentData;
	/** 끝 지점 (pagination) - 문서 스냅샷 */
	endBefore?: DocumentData;
}

/**
 * 쿼리 결과 인터페이스
 */
export interface QueryResult<T> extends DataOperationResult {
	/** 쿼리 결과 데이터 배열 */
	data?: T[];
	/** 마지막 문서 스냅샷 (pagination용) */
	lastDoc?: DocumentData;
}

/**
 * Collection 쿼리 (복합 쿼리 지원)
 *
 * Firestore Collection에서 복합 조건으로 문서들을 쿼리합니다.
 * WHERE, ORDER BY, LIMIT 등 다양한 조건을 조합할 수 있습니다.
 *
 * @template T - 데이터 타입
 * @param path - Firestore Collection 경로
 * @param options - 쿼리 옵션 (where, orderBy, limit 등)
 * @returns 쿼리 결과
 *
 * @example
 * ```typescript
 * // 나이가 30 이상인 사용자를 이름 순으로 10명만 가져오기
 * const result = await queryCollection<UserData>('users', {
 *   where: [
 *     { field: 'age', operator: '>=', value: 30 }
 *   ],
 *   orderBy: [
 *     { field: 'displayName', direction: 'asc' }
 *   ],
 *   limit: 10
 * });
 *
 * if (result.success && result.data) {
 *   result.data.forEach(user => {
 *     console.log(user.displayName, user.age);
 *   });
 * }
 * ```
 */
export async function queryCollection<T = DocumentData>(
	path: string,
	options: QueryOptions = {}
): Promise<QueryResult<T>> {
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		return { success: false, error: 'Firestore가 초기화되지 않았습니다.' };
	}

	try {
		const colRef = parsePath(path) as CollectionReference;

		// Document 경로가 전달된 경우 에러
		if (path.split('/').filter(s => s.length > 0).length % 2 === 0) {
			throw new Error(`queryCollection은 Collection 경로만 지원합니다. Document 경로가 전달되었습니다: ${path}`);
		}

		// 쿼리 제약 조건들 생성
		const constraints: QueryConstraint[] = [];

		// WHERE 조건 추가
		if (options.where) {
			options.where.forEach(({ field, operator, value }) => {
				constraints.push(where(field, operator, value));
			});
		}

		// ORDER BY 조건 추가
		if (options.orderBy) {
			options.orderBy.forEach(({ field, direction = 'asc' }) => {
				constraints.push(orderBy(field, direction));
			});
		}

		// LIMIT 조건 추가
		if (options.limit) {
			constraints.push(limit(options.limit));
		}

		// Pagination - startAfter
		if (options.startAfter) {
			constraints.push(startAfter(options.startAfter));
		}

		// Pagination - endBefore
		if (options.endBefore) {
			constraints.push(endBefore(options.endBefore));
		}

		// 쿼리 실행
		const q = query(colRef, ...constraints);
		const snapshot = await getDocs(q);

		const data: T[] = [];
		snapshot.forEach((doc) => {
			data.push(doc.data() as T);
		});

		const lastDoc = snapshot.docs[snapshot.docs.length - 1]?.data();

		// console.log(`✅ 쿼리 성공: ${path}, ${data.length}개 문서`);
		return { success: true, data, lastDoc };
	} catch (error) {
		console.error('❌ 쿼리 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 배치 쓰기 작업 인터페이스
 */
export interface BatchOperation {
	/** 작업 타입 */
	type: 'set' | 'update' | 'delete';
	/** 문서 경로 */
	path: string;
	/** 저장/업데이트할 데이터 (delete는 불필요) */
	data?: DocumentData;
	/** set 작업 시 merge 옵션 (기본값: false) */
	merge?: boolean;
}

/**
 * 배치 쓰기 (여러 작업을 원자적으로 실행)
 *
 * 여러 문서 작업(set, update, delete)을 하나의 트랜잭션으로 실행합니다.
 * 모든 작업이 성공하거나 모두 실패합니다 (원자성 보장).
 *
 * @param operations - 배치 작업 배열
 * @returns 작업 결과
 *
 * @example
 * ```typescript
 * // 여러 문서를 한 번에 업데이트
 * const result = await batchWrite([
 *   { type: 'set', path: 'users/user123', data: { name: 'John', age: 30 } },
 *   { type: 'update', path: 'users/user456', data: { age: 31 } },
 *   { type: 'delete', path: 'users/user789' }
 * ]);
 *
 * if (result.success) {
 *   console.log('배치 작업 성공');
 * }
 * ```
 */
export async function batchWrite(operations: BatchOperation[]): Promise<DataOperationResult> {
	if (!db) {
		console.error('❌ Firestore가 초기화되지 않았습니다.');
		return { success: false, error: 'Firestore가 초기화되지 않았습니다.' };
	}

	try {
		const batch = writeBatch(db);

		operations.forEach(({ type, path, data, merge = false }) => {
			const docRef = parsePath(path) as DocumentReference;

			// Collection 경로가 전달된 경우 에러
			if (path.split('/').filter(s => s.length > 0).length % 2 !== 0) {
				throw new Error(`batchWrite의 모든 경로는 Document 경로여야 합니다. Collection 경로가 전달되었습니다: ${path}`);
			}

			if (type === 'set') {
				if (!data) {
					throw new Error(`set 작업에는 data가 필요합니다: ${path}`);
				}
				batch.set(docRef, data, { merge });
			} else if (type === 'update') {
				if (!data) {
					throw new Error(`update 작업에는 data가 필요합니다: ${path}`);
				}
				batch.update(docRef, data);
			} else if (type === 'delete') {
				batch.delete(docRef);
			}
		});

		await batch.commit();
		// console.log(`✅ 배치 쓰기 성공: ${operations.length}개 작업`);
		return { success: true };
	} catch (error) {
		console.error('❌ 배치 쓰기 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * Firestore Timestamp 유틸리티
 * 서버 타임스탬프 생성
 */
export { serverTimestamp, Timestamp };

// getDocs import 추가
import { getDocs } from 'firebase/firestore';
