/**
 * Firebase Realtime Database 유틸리티
 *
 * 데이터베이스 읽기, 쓰기, 업데이트, 삭제 및 실시간 구독 기능을 제공합니다.
 *
 * 사용법:
 * ```typescript
 * import { writeData, updateData, deleteData, pushData, createRealtimeStore, rtdb } from '$lib/stores/database.svelte';
 *
 * // 데이터 쓰기
 * await writeData('users/user123', { name: 'John', age: 30 });
 *
 * // 실시간 구독 (제네릭 타입 사용)
 * const posts = createRealtimeStore<Post[]>('posts');
 * // 또는 짧은 별칭 사용
 * const posts = rtdb<Post[]>('posts');
 *
 * {#if $posts.loading}
 *   <p>로딩 중...</p>
 * {:else if $posts.data}
 *   {#each $posts.data as post}
 *     <div>{post.title}</div>
 *   {/each}
 * {/if}
 * ```
 */

import { writable, type Writable } from 'svelte/store';
import { rtdb } from '$lib/firebase';
import {
	ref,
	push,
	set,
	onValue,
	off,
	update,
	remove,
	get,
	type DatabaseReference
} from 'firebase/database';

/**
 * 실시간 스토어 상태 타입
 * @template T - 데이터 타입
 */
export interface RealtimeStoreState<T> {
	/** 데이터베이스에서 가져온 데이터 */
	data: T | null;
	/** 데이터 로딩 중 여부 */
	loading: boolean;
	/** 에러 발생 시 에러 객체 */
	error: Error | null;
}

/**
 * 실시간 스토어 타입
 * Svelte 스토어에 unsubscribe 메서드를 추가한 타입
 * @template T - 데이터 타입
 */
export interface RealtimeStore<T> extends Writable<RealtimeStoreState<T>> {
	/** 데이터베이스 리스너 구독 해제 함수 */
	unsubscribe: () => void;
}

/**
 * 데이터 작업 결과 타입
 */
export interface DataOperationResult {
	/** 작업 성공 여부 */
	success: boolean;
	/** 에러 메시지 (실패 시) */
	error?: string;
}

/**
 * 데이터 추가 결과 타입 (생성된 키 포함)
 */
export interface PushDataResult extends DataOperationResult {
	/** 생성된 키 (push 작업 시) */
	key?: string | null;
}

/**
 * 데이터 읽기 결과 타입
 * @template T - 데이터 타입
 */
export interface ReadDataResult<T> extends DataOperationResult {
	/** 읽어온 데이터 */
	data?: T | null;
}

/**
 * 실시간 데이터 구독을 위한 스토어 생성
 *
 * 직관적이고 이해하기 쉬운 함수형 API 유지
 * loading, error 상태를 자동으로 추적합니다
 *
 * @template T - 데이터 타입
 * @param path - 데이터베이스 경로 (예: 'posts', 'users/user123')
 * @param defaultValue - 노드가 존재하지 않을 때 사용할 기본값 (선택적)
 * @returns Svelte 스토어 (구독 가능) - { data, loading, error }
 *
 * @example
 * ```typescript
 * // 기본 사용법 (제네릭 타입 사용)
 * const posts = createRealtimeStore<Post[]>('posts');
 * // $posts는 { data: Post[] | null, loading: boolean, error: Error | null }
 *
 * // 기본값 사용 (노드가 없으면 0 반환)
 * const likeCount = createRealtimeStore<number>('likes/post-post-123-uid-456', 0);
 * // $likeCount.data는 노드가 없으면 0, 있으면 해당 값
 *
 * {#if $posts.loading}
 *   <p>로딩 중...</p>
 * {:else if $posts.error}
 *   <p>에러: {$posts.error.message}</p>
 * {:else if $posts.data}
 *   {#each $posts.data as post}
 *     <p>{post.title}</p>
 *   {/each}
 * {/if}
 * ```
 */
export function createRealtimeStore<T = any>(
	path: string,
	defaultValue?: T
): RealtimeStore<T> {
	// Firebase RTDB가 초기화되지 않은 경우 에러 처리
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		const { subscribe, set: setStore } = writable<RealtimeStoreState<T>>({
			data: defaultValue ?? null,
			loading: false,
			error: new Error('Firebase Realtime Database가 초기화되지 않았습니다.')
		});

		return {
			subscribe,
			set: setStore,
			update: (_updater) => {
				throw new Error('직접 업데이트는 지원하지 않습니다. Firebase를 통해 데이터를 변경하세요.');
			},
			unsubscribe: () => {}
		};
	}

	// 초기 상태: data는 defaultValue 또는 null, loading은 true, error는 null
	const { subscribe, set: setStore } = writable<RealtimeStoreState<T>>({
		data: defaultValue ?? null,
		loading: true,
		error: null
	});

	const dbRef: DatabaseReference = ref(rtdb, path);

	// 데이터 변경 실시간 감지 (성공 및 실패 콜백 포함)
	onValue(
		dbRef,
		(snapshot) => {
			// 데이터 로드 성공
			const data = snapshot.val() as T | null;
			// 노드가 존재하지 않으면 (data === null) defaultValue 사용
			setStore({
				data: data !== null ? data : (defaultValue ?? null),
				loading: false,
				error: null
			});
			// console.log(`✅ 실시간 데이터 로드 성공: ${path}`, data !== null ? data : (defaultValue ?? null));
		},
		(error) => {
			// 데이터 로드 실패 (권한 오류, 네트워크 오류 등)
			console.error(`❌ 실시간 데이터 로드 실패: ${path}`, error);
			setStore({
				data: defaultValue ?? null,
				loading: false,
				error: error as Error
			});
		}
	);

	return {
		subscribe,
		set: setStore,
		update: (_updater) => {
			// Writable 인터페이스 구현을 위한 update 메서드
			// updater는 사용되지 않지만 Writable 인터페이스를 충족하기 위해 필요함
			throw new Error('직접 업데이트는 지원하지 않습니다. Firebase를 통해 데이터를 변경하세요.');
		},
		// 컴포넌트 언마운트 시 구독 해제 (메모리 누수 방지)
		unsubscribe: () => off(dbRef)
	};
}

/**
 * createRealtimeStore() 함수의 alias (별칭)
 *
 * 더 짧은 이름으로 실시간 데이터 구독을 할 수 있습니다.
 * createRealtimeStore()와 동일하게 작동합니다.
 *
 * @template T - 데이터 타입
 * @param path - 데이터베이스 경로 (예: 'posts', 'users/user123')
 * @param defaultValue - 노드가 존재하지 않을 때 사용할 기본값 (선택적)
 * @returns Svelte 스토어 (구독 가능) - { data, loading, error }
 *
 * @example
 * ```typescript
 * // rtdb() 사용 (짧은 이름) - 권장
 * const posts = rtdb<Post[]>('posts');
 *
 * // 기본값과 함께 사용
 * const likeStore = rtdb<number>('likes/post-post-123-uid-456', 0);
 * // 노드가 없으면 0, 있으면 해당 값 반환
 *
 * // createRealtimeStore() 사용 (명시적 이름)
 * const posts = createRealtimeStore<Post[]>('posts');
 *
 * // 두 방식 모두 동일합니다
 * ```
 */
export const rtdbStore = createRealtimeStore;

/**
 * 데이터 쓰기 (기존 데이터 덮어쓰기)
 *
 * 지정된 경로에 데이터를 저장합니다. 기존 데이터가 있으면 덮어씁니다.
 *
 * @param path - 데이터베이스 경로
 * @param data - 저장할 데이터
 * @returns 작업 결과
 *
 * @example
 * ```typescript
 * const result = await writeData('users/user123', {
 *   name: 'John Doe',
 *   age: 30,
 *   email: 'john@example.com'
 * });
 *
 * if (result.success) {
 *   console.log('데이터 저장 성공');
 * } else {
 *   console.error('데이터 저장 실패:', result.error);
 * }
 * ```
 */
export async function writeData(path: string, data: any): Promise<DataOperationResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		await set(dbRef, data);
		// console.log(`✅ 데이터 쓰기 성공: ${path}`);
		return { success: true };
	} catch (error) {
		console.error('❌ 데이터 쓰기 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 데이터 업데이트 (부분 업데이트)
 *
 * 지정된 경로에 데이터를 부분적으로 업데이트합니다.
 * 기존 데이터를 유지하면서 지정된 필드만 변경합니다.
 *
 * @param path - 데이터베이스 경로
 * @param updates - 업데이트할 필드들 { field1: value1, field2: value2 }
 * @returns 작업 결과
 *
 * @example
 * ```typescript
 * // users/user123 노드에서 age 필드만 업데이트
 * const result = await updateData('users/user123', { age: 31 });
 *
 * if (result.success) {
 *   console.log('업데이트 성공');
 * }
 * ```
 */
export async function updateData(
	path: string,
	updates: Record<string, any>
): Promise<DataOperationResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		await update(dbRef, updates);
		// console.log(`✅ 데이터 업데이트 성공: ${path}`, updates);
		return { success: true };
	} catch (error) {
		console.error('❌ 데이터 업데이트 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 데이터 삭제
 *
 * 지정된 경로의 데이터를 삭제합니다.
 *
 * @param path - 데이터베이스 경로
 * @returns 작업 결과
 *
 * @example
 * ```typescript
 * // users/user123 노드 삭제
 * const result = await deleteData('users/user123');
 *
 * if (result.success) {
 *   console.log('삭제 성공');
 * }
 * ```
 */
export async function deleteData(path: string): Promise<DataOperationResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		await remove(dbRef);
		// console.log(`✅ 데이터 삭제 성공: ${path}`);
		return { success: true };
	} catch (error) {
		console.error('❌ 데이터 삭제 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 새 항목 추가 (자동 생성된 키 사용)
 *
 * 지정된 경로에 새로운 항목을 추가합니다.
 * Firebase가 자동으로 고유한 키를 생성합니다.
 *
 * @param path - 데이터베이스 경로
 * @param data - 저장할 데이터
 * @returns 작업 결과 및 생성된 키
 *
 * @example
 * ```typescript
 * // posts 노드에 새 게시글 추가
 * const result = await pushData('posts', {
 *   title: '제목',
 *   content: '내용',
 *   authorId: 'user123'
 * });
 *
 * if (result.success) {
 *   console.log('생성된 키:', result.key);
 * }
 * ```
 */
export async function pushData(path: string, data: any): Promise<PushDataResult> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		const newRef = push(dbRef);
		await set(newRef, data);
		// console.log(`✅ 데이터 추가 성공: ${path}, 생성된 키: ${newRef.key}`);
		return { success: true, key: newRef.key };
	} catch (error) {
		console.error('❌ 데이터 추가 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 데이터 읽기 (한 번만)
 *
 * 지정된 경로의 데이터를 한 번만 읽습니다.
 * 실시간 구독이 필요 없을 때 사용합니다.
 *
 * @template T - 데이터 타입
 * @param path - 데이터베이스 경로
 * @returns 작업 결과 및 데이터
 *
 * @example
 * ```typescript
 * // users/user123 데이터 한 번만 읽기
 * const result = await readData<UserProfile>('users/user123');
 *
 * if (result.success && result.data) {
 *   console.log('사용자 이름:', result.data.name);
 * } else if (result.success && !result.data) {
 *   console.log('데이터가 존재하지 않습니다.');
 * } else {
 *   console.error('읽기 실패:', result.error);
 * }
 * ```
 */
export async function readData<T = any>(path: string): Promise<ReadDataResult<T>> {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return { success: false, error: 'Firebase Realtime Database가 초기화되지 않았습니다.' };
	}

	try {
		const dbRef = ref(rtdb, path);
		const snapshot = await get(dbRef);

		if (snapshot.exists()) {
			// console.log(`✅ 데이터 읽기 성공: ${path}`);
			return { success: true, data: snapshot.val() as T };
		} else {
			// console.log(`ℹ️ 데이터가 존재하지 않음: ${path}`);
			return { success: true, data: null };
		}
	} catch (error) {
		console.error('❌ 데이터 읽기 오류:', error);
		return { success: false, error: (error as Error).message };
	}
}

/**
 * 온라인 상태 인터페이스
 */
export interface UserStatus {
	/** 온라인 여부 */
	online: boolean;
	/** 마지막 접속 시간 (Unix timestamp, 밀리초) */
	lastSeen: number;
}

/**
 * 온라인 상태 관리
 *
 * Firebase의 .info/connected를 사용하여 사용자의 온라인/오프라인 상태를 관리합니다.
 * 사용자가 연결되면 online: true로 설정되고, 연결이 끊기면 자동으로 online: false로 변경됩니다.
 *
 * @param userId - 사용자 ID
 * @returns 상태 관리 해제 함수
 *
 * @example
 * ```typescript
 * // 로그인 시 온라인 상태 관리 시작
 * const cleanup = setupPresence('user123');
 *
 * // 로그아웃 시 정리
 * cleanup();
 * ```
 */
export function setupPresence(userId: string): () => void {
	if (!rtdb) {
		console.error('❌ Firebase Realtime Database가 초기화되지 않았습니다.');
		return () => {};
	}

	const userStatusRef = ref(rtdb, `status/${userId}`);
	const connectedRef = ref(rtdb, '.info/connected');

	// Firebase 연결 상태 모니터링
	onValue(connectedRef, (snapshot) => {
		if (snapshot.val() === true) {
			// 온라인 상태로 설정
			set(userStatusRef, {
				online: true,
				lastSeen: Date.now()
			});

			// 연결이 끊기면 오프라인으로 변경
			onValue(ref(rtdb!, '.info/connected'), (snap) => {
				if (!snap.val()) {
					update(userStatusRef, {
						online: false,
						lastSeen: Date.now()
					});
				}
			});
		}
	});

	return () => {
		// 수동으로 오프라인 상태로 설정
		update(userStatusRef, {
			online: false,
			lastSeen: Date.now()
		});
		off(connectedRef);
	};
}
