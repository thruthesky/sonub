/**
 * Firebase Storage 관련 함수
 *
 * 이 파일은 파일 업로드 및 Storage 관련 유틸리티 함수를 제공합니다.
 */

import { storage } from '$lib/firebase';
import {
	ref,
	uploadBytesResumable,
	getDownloadURL,
	deleteObject,
	type UploadTask
} from 'firebase/storage';

/**
 * 채팅 파일을 Firebase Storage에 업로드합니다.
 *
 * @param file - 업로드할 파일
 * @param uid - 사용자 UID
 * @param roomId - 채팅방 ID
 * @param onProgress - 업로드 진행률 콜백 (0-100)
 * @returns Promise<string> 업로드된 파일의 다운로드 URL
 *
 * @example
 * ```typescript
 * const url = await uploadChatFile(
 *   file,
 *   'user123',
 *   'room456',
 *   (progress) => console.log(`Progress: ${progress}%`)
 * );
 * ```
 */
export async function uploadChatFile(
	file: File,
	uid: string,
	roomId: string,
	onProgress?: (progress: number) => void
): Promise<string> {
	if (!storage) {
		throw new Error('Firebase Storage가 초기화되지 않았습니다.');
	}

	// 파일명 생성: {timestamp}-{originalFilename}
	const timestamp = Date.now();
	const filename = `${timestamp}-${file.name}`;
	const filePath = `users/${uid}/chat-files/${roomId}/${filename}`;

	// Storage 참조 생성
	const storageRef = ref(storage, filePath);

	// 업로드 Task 생성
	const uploadTask: UploadTask = uploadBytesResumable(storageRef, file);

	return new Promise((resolve, reject) => {
		uploadTask.on(
			'state_changed',
			(snapshot) => {
				// 업로드 진행률 계산
				const progress = Math.round((snapshot.bytesTransferred / snapshot.totalBytes) * 100);
				onProgress?.(progress);
				// console.log(`📤 업로드 진행률: ${progress}% (${file.name})`);
			},
			(error) => {
				// 업로드 실패
				console.error('❌ 파일 업로드 실패:', error);
				reject(error);
			},
			async () => {
				// 업로드 성공 - URL만 반환
				try {
					const downloadUrl = await getDownloadURL(uploadTask.snapshot.ref);
					// console.log('✅ 파일 업로드 성공:', downloadUrl);
					resolve(downloadUrl);
				} catch (error) {
					console.error('❌ 다운로드 URL 가져오기 실패:', error);
					reject(error);
				}
			}
		);
	});
}

/**
 * 다중 파일 업로드
 *
 * @param files - 업로드할 파일 목록
 * @param uid - 사용자 UID
 * @param roomId - 채팅방 ID
 * @param onProgress - 각 파일의 업로드 진행률 콜백
 * @returns Promise<Record<number, string>> 숫자 인덱스를 키로, URL을 값으로 하는 객체
 *
 * @example
 * ```typescript
 * const urls = await uploadMultipleChatFiles(
 *   [file1, file2, file3],
 *   'user123',
 *   'room456',
 *   (fileIndex, progress) => console.log(`File ${fileIndex}: ${progress}%`)
 * );
 * // 결과: { 0: "https://...", 1: "https://...", 2: "https://..." }
 * ```
 */
export async function uploadMultipleChatFiles(
	files: File[],
	uid: string,
	roomId: string,
	onProgress?: (fileIndex: number, progress: number) => void
): Promise<Record<number, string>> {
	const urls: Record<number, string> = {};
	const uploadPromises: Promise<void>[] = [];

	files.forEach((file, index) => {
		const promise = uploadChatFile(
			file,
			uid,
			roomId,
			(progress) => onProgress?.(index, progress)
		).then((downloadUrl) => {
			// 숫자 인덱스를 키로, URL을 값으로 저장
			urls[index] = downloadUrl;
		});

		uploadPromises.push(promise);
	});

	await Promise.all(uploadPromises);
	return urls;
}

/**
 * 파일 크기를 읽기 쉬운 형식으로 변환
 *
 * @param bytes - 파일 크기 (바이트)
 * @returns 포맷된 파일 크기 문자열 (예: "1.5 MB")
 *
 * @example
 * ```typescript
 * formatFileSize(1024) // "1.0 KB"
 * formatFileSize(1536000) // "1.5 MB"
 * formatFileSize(0) // "0 B"
 * ```
 */
export function formatFileSize(bytes: number): string {
	if (bytes === 0) return '0 B';

	const k = 1024;
	const sizes = ['B', 'KB', 'MB', 'GB'];
	const i = Math.floor(Math.log(bytes) / Math.log(k));

	return `${(bytes / Math.pow(k, i)).toFixed(1)} ${sizes[i]}`;
}

/**
 * URL에서 파일명을 추출합니다.
 *
 * @param url - Firebase Storage URL
 * @returns 파일명 (timestamp 제거됨)
 *
 * @example
 * ```typescript
 * getFilenameFromUrl("https://...users%2Fuid%2Fchat-files%2Froomid%2F1731580123456-photo.jpg?...")
 * // 결과: "photo.jpg"
 * ```
 */
export function getFilenameFromUrl(url: string): string {
	try {
		const urlObj = new URL(url);
		const pathname = decodeURIComponent(urlObj.pathname);
		const parts = pathname.split('/');
		const filename = parts[parts.length - 1];
		// timestamp 제거 (예: "1731580123456-photo.jpg" → "photo.jpg")
		return filename.replace(/^\d+-/, '');
	} catch {
		return '파일';
	}
}

/**
 * URL이 이미지인지 확인합니다.
 *
 * @param url - Firebase Storage URL
 * @returns 이미지이면 true
 *
 * @example
 * ```typescript
 * isImageUrl("https://.../photo.jpg") // true
 * isImageUrl("https://.../video.mp4") // false
 * ```
 */
export function isImageUrl(url: string): boolean {
	const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp', '.svg'];
	const lowerUrl = url.toLowerCase();
	return imageExtensions.some((ext) => lowerUrl.includes(ext));
}

/**
 * URL이 동영상인지 확인합니다.
 *
 * @param url - Firebase Storage URL
 * @returns 동영상이면 true
 *
 * @example
 * ```typescript
 * isVideoUrl("https://.../video.mp4") // true
 * isVideoUrl("https://.../photo.jpg") // false
 * ```
 */
export function isVideoUrl(url: string): boolean {
	const videoExtensions = ['.mp4', '.mov', '.avi', '.webm', '.mkv'];
	const lowerUrl = url.toLowerCase();
	return videoExtensions.some((ext) => lowerUrl.includes(ext));
}

/**
 * 파일명에서 확장자를 추출합니다.
 *
 * @param filename - 파일명 (예: "photo.jpg", "document.pdf")
 * @returns 파일 확장자 (소문자, 점 포함) 또는 빈 문자열
 *
 * @example
 * ```typescript
 * getExtensionFromFilename("photo.jpg") // ".jpg"
 * getExtensionFromFilename("document.pdf") // ".pdf"
 * getExtensionFromFilename("archive.tar.gz") // ".gz"
 * getExtensionFromFilename("noextension") // ""
 * ```
 */
export function getExtensionFromFilename(filename: string): string {
	const dotIndex = filename.lastIndexOf('.');
	if (dotIndex === -1 || dotIndex === filename.length - 1) return '';
	return filename.substring(dotIndex).toLowerCase();
}

/**
 * URL에서 파일 확장자를 추출합니다.
 *
 * @param url - Firebase Storage URL
 * @returns 파일 확장자 (소문자, 점 포함) 또는 빈 문자열
 *
 * @example
 * ```typescript
 * getFileExtension("https://.../document.pdf") // ".pdf"
 * getFileExtension("https://.../archive.zip") // ".zip"
 * ```
 */
export function getFileExtension(url: string): string {
	try {
		const filename = getFilenameFromUrl(url);
		const dotIndex = filename.lastIndexOf('.');
		if (dotIndex === -1) return '';
		return filename.substring(dotIndex).toLowerCase();
	} catch {
		return '';
	}
}

/**
 * Firebase Storage URL에서 파일 경로를 추출합니다.
 *
 * @param url - Firebase Storage 다운로드 URL
 * @returns 파일 경로 (예: "users/uid/chat-files/roomId/timestamp-filename.jpg")
 *
 * @example
 * ```typescript
 * const path = getFilePathFromUrl("https://firebasestorage.googleapis.com/v0/b/bucket/o/users%2Fuid%2Fchat-files%2Froomid%2F123-photo.jpg?token=...");
 * // 결과: "users/uid/chat-files/roomid/123-photo.jpg"
 * ```
 */
export function getFilePathFromUrl(url: string): string {
	try {
		const urlObj = new URL(url);
		// Firebase Storage URL 형식: /v0/b/{bucket}/o/{path}?token=...
		const pathname = urlObj.pathname;
		const match = pathname.match(/\/o\/(.+?)(\?|$)/);
		if (match && match[1]) {
			return decodeURIComponent(match[1]);
		}
		throw new Error('잘못된 Firebase Storage URL 형식');
	} catch (error) {
		console.error('URL에서 파일 경로 추출 실패:', error);
		throw error;
	}
}

/**
 * Firebase Storage에서 파일을 삭제합니다.
 *
 * @param url - 삭제할 파일의 다운로드 URL
 * @returns Promise<void>
 *
 * @example
 * ```typescript
 * await deleteChatFile("https://firebasestorage.googleapis.com/.../photo.jpg?token=...");
 * console.log('파일 삭제 완료');
 * ```
 */
export async function deleteChatFile(url: string): Promise<void> {
	if (!storage) {
		throw new Error('Firebase Storage가 초기화되지 않았습니다.');
	}

	try {
		// URL에서 파일 경로 추출
		const filePath = getFilePathFromUrl(url);
		// console.log(`🗑️ 파일 삭제 시작: ${filePath}`);

		// Storage 참조 생성
		const storageRef = ref(storage, filePath);

		// 파일 삭제
		await deleteObject(storageRef);
		// console.log(`✅ 파일 삭제 완료: ${filePath}`);
	} catch (error) {
		console.error('❌ 파일 삭제 실패:', error);
		throw error;
	}
}
