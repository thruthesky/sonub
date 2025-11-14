# 채팅 파일 첨부 기능 구현 계획

**작성일:** 2025-11-14
**작성자:** Claude Code
**목적:** 채팅 메시지에 파일(이미지, 동영상, PDF, TXT, ZIP 등) 첨부 기능 추가

---

## 1. 개요

### 1.1 목표
- 채팅 메시지 입력창에서 파일 업로드 UI 제공
- Firebase Storage에 파일 저장 (`/users/{uid}/**`)
- 업로드된 파일 URL을 메시지와 함께 저장
- 파일 미리보기 UI 제공 (Grid 레이아웃)
- **업로드 진행률을 큰 숫자로 시각적으로 표시** (첨부 이미지 디자인 참고)

### 1.2 기능 범위
- ✅ 이미지 파일 업로드 (JPG, PNG, GIF, WebP)
- ✅ 동영상 파일 업로드 (MP4, MOV, AVI)
- ✅ 문서 파일 업로드 (PDF, TXT, DOCX)
- ✅ 압축 파일 업로드 (ZIP, RAR)
- ✅ 다중 파일 선택 지원
- ✅ **업로드 진행률 퍼센티지 표시** (큰 숫자로 중앙 오버레이, 첨부 이미지 참고)
- ✅ 파일 미리보기 (업로드 전/중/후)
- ✅ **파일 삭제 기능** (업로드 전/중/후 모두 가능, X 버튼)

---

## 2. 데이터 구조 설계

### 2.1 Firebase Storage 경로
```
/users/{uid}/chat-files/{roomId}/{timestamp}-{originalFilename}
```

**예시:**
```
/users/alice123/chat-files/single-alice-bob/1731580123456-photo.jpg
/users/alice123/chat-files/group-roomid/1731580234567-document.pdf
```

**설명:**
- `{uid}`: 업로드한 사용자의 UID
- `{roomId}`: 채팅방 ID (중복 방지 및 관리 편의성)
- `{timestamp}`: 업로드 시각 (밀리초)
- `{originalFilename}`: 원본 파일명 (중복 방지)

### 2.2 RTDB 메시지 데이터 구조

#### 현재 구조 (sonub-firebase-database-structure.md 참조)
```typescript
/chat-messages/<messageId>/
├── roomId: "single-uid1-uid2"
├── type: "text"
├── text: "안녕하세요!"
├── senderUid: "uid1"
├── createdAt: 1698473000000
├── imageUrl: "https://..."  // 기존 (단일 이미지)
└── fileUrl: "https://..."   // 기존 (단일 파일)
```

#### 새로운 구조 (urls 필드 추가)
```typescript
/chat-messages/<messageId>/
├── roomId: "single-uid1-uid2"
├── type: "text"
├── text: "첨부파일 포함 메시지"
├── senderUid: "uid1"
├── createdAt: 1698473000000
├── urls: {
│   0: "https://firebasestorage.googleapis.com/.../photo.jpg",
│   1: "https://firebasestorage.googleapis.com/.../document.pdf"
│ }
└── roomOrder: "-single-uid1-uid2-1698473000000"
```

**urls 필드 상세:**
- **키(Key):** 숫자 인덱스 (0, 1, 2, ...)
- **값(Value):** Firebase Storage 다운로드 URL (문자열)
- **목적:** RTDB 저장 용량 최소화를 위해 메타데이터를 제거하고 URL만 저장
- **파일 정보 획득:** URL에서 파일명 추출 가능, 크기/타입은 필요시 Storage API로 조회

### 2.3 TypeScript 타입 정의

**파일:** `src/lib/types/chat.types.ts` (신규 생성)

/**
 * 채팅 메시지 데이터 (urls 필드 포함)
 */
export interface ChatMessage {
  roomId: string;
  type: 'text' | 'image' | 'file' | 'message';
  text?: string;
  senderUid: string;
  createdAt: number;
  roomOrder: string;
  rootOrder: string;
  editedAt?: number | null;
  deletedAt?: number | null;
  /** 첨부파일 URL 목록 (숫자 인덱스를 키로 사용, 값은 URL 문자열) */
  urls?: Record<number, string>;
}

/**
 * 파일 업로드 상태
 */
export interface FileUploadStatus {
  /** 로컬 파일 객체 */
  file: File;
  /** 미리보기 URL (이미지/동영상) */
  previewUrl?: string;
  /** 업로드 진행률 (0-100) */
  progress: number;
  /** 업로드 완료 여부 */
  completed: boolean;
  /** 에러 메시지 */
  error?: string;
  /** Firebase Storage 업로드 URL (완료 시) */
  downloadUrl?: string;
}
```

---

## 3. UI 컴포넌트 설계

### 3.1 파일 업로드 버튼

**위치:** 채팅 메시지 입력창 오른쪽 (전송 버튼 왼쪽)

**파일:** `src/routes/chat/room/+page.svelte` 수정

**UI 구조:**
```html
<form class="composer-form" onsubmit={handleSendMessage}>
  <input type="text" class="composer-input" bind:value={composerText} />

  <!-- 파일 업로드 버튼 (신규 추가) -->
  <button type="button" class="file-upload-button" onclick={handleFileButtonClick}>
    <svg><!-- 카메라 아이콘 --></svg>
  </button>

  <!-- 숨겨진 파일 입력 -->
  <input
    type="file"
    bind:this={fileInputRef}
    onchange={handleFileSelect}
    multiple
    accept="image/*,video/*,.pdf,.txt,.doc,.docx,.zip,.rar"
    style="display: none;"
  />

  <!-- 전송 버튼 -->
  <button type="submit" class="composer-button">
    <svg><!-- 전송 아이콘 --></svg>
  </button>
</form>
```

**스타일:**
```css
.file-upload-button {
  @apply flex items-center justify-center;
  @apply rounded-full border-0 bg-transparent;
  @apply text-gray-700 transition-all duration-200;
  @apply p-2;
  @apply hover:bg-gray-100 active:bg-gray-200;
}

.file-upload-button:disabled {
  @apply cursor-not-allowed text-gray-300;
  @apply hover:bg-transparent;
}
```

### 3.2 파일 미리보기 그리드

**위치:** 채팅 메시지 입력창 바로 위

**파일:** `src/routes/chat/room/+page.svelte` 수정

**디자인 참고:**
- 첨부 이미지 참고: 각 그리드 항목에 업로드 퍼센티지를 큰 숫자로 중앙에 표시
- 배경 이미지/동영상 위에 반투명 오버레이를 추가하고 그 위에 퍼센티지 표시
- 업로드 완료 시 퍼센티지 숨김
- 우측 상단에 X 삭제 버튼 배치

**UI 구조:**
```html
{#if uploadingFiles.length > 0}
  <div class="file-preview-container">
    <div class="file-preview-grid">
      {#each uploadingFiles as fileStatus, index}
        <div class="file-preview-item">
          <!-- 이미지/동영상 미리보기 -->
          {#if fileStatus.previewUrl}
            <div class="preview-thumbnail">
              {#if fileStatus.file.type.startsWith('image/')}
                <img src={fileStatus.previewUrl} alt={fileStatus.file.name} />
              {:else if fileStatus.file.type.startsWith('video/')}
                <video src={fileStatus.previewUrl} />
              {/if}

              <!-- 업로드 진행률 오버레이 (숫자로 표시) -->
              {#if !fileStatus.completed && !fileStatus.error}
                <div class="upload-progress-overlay">
                  <span class="upload-percentage">{fileStatus.progress}%</span>
                </div>
              {/if}
            </div>
          {:else}
            <!-- 일반 파일 아이콘 -->
            <div class="file-icon">
              <svg><!-- 파일 아이콘 --></svg>

              <!-- 업로드 진행률 (일반 파일) -->
              {#if !fileStatus.completed && !fileStatus.error}
                <div class="upload-progress-overlay">
                  <span class="upload-percentage">{fileStatus.progress}%</span>
                </div>
              {/if}
            </div>
          {/if}

          <!-- 에러 표시 -->
          {#if fileStatus.error}
            <div class="upload-error-overlay">
              <p class="upload-error">{fileStatus.error}</p>
            </div>
          {/if}

          <!-- 삭제 버튼 (항상 표시) -->
          <button
            type="button"
            class="remove-file-button"
            onclick={() => handleRemoveFile(index)}
          >
            ✕
          </button>
        </div>
      {/each}
    </div>
  </div>
{/if}
```

**스타일:**
```css
.file-preview-container {
  @apply px-2 pb-2 md:px-4 md:pb-3;
}

.file-preview-grid {
  @apply grid grid-cols-2 gap-2 md:grid-cols-3 lg:grid-cols-4;
}

.file-preview-item {
  @apply relative rounded-lg border-2 overflow-hidden shadow-sm;
  @apply transition-all hover:shadow-md;
}

.preview-thumbnail {
  @apply relative aspect-square w-full overflow-hidden bg-gray-100;
}

.preview-thumbnail img,
.preview-thumbnail video {
  @apply h-full w-full object-cover;
}

.file-icon {
  @apply relative flex aspect-square w-full items-center justify-center;
  @apply bg-gray-100 text-4xl text-gray-400;
}

/* 업로드 진행률 오버레이 (이미지 참고) */
.upload-progress-overlay {
  @apply absolute inset-0 flex items-center justify-center;
  @apply bg-black/40 backdrop-blur-sm;
}

/* 퍼센티지 숫자 (크고 굵게) */
.upload-percentage {
  @apply text-5xl md:text-6xl font-bold text-white;
  @apply drop-shadow-lg;
}

/* 에러 오버레이 */
.upload-error-overlay {
  @apply absolute inset-0 flex items-center justify-center;
  @apply bg-red-500/80 backdrop-blur-sm p-2;
}

.upload-error {
  @apply text-xs text-center text-white font-semibold;
}

/* 삭제 버튼 (우측 상단 고정) */
.remove-file-button {
  @apply absolute right-2 top-2 z-10;
  @apply flex h-8 w-8 items-center justify-center;
  @apply rounded-full bg-red-500 text-sm font-bold text-white shadow-lg;
  @apply transition-all hover:bg-red-600 hover:scale-110 active:scale-95;
}
```

### 3.3 메시지 내 첨부파일 표시

**위치:** 채팅 메시지 버블 내부

**파일:** `src/routes/chat/room/+page.svelte` 수정

**UI 구조:**
```html
<div class="message-bubble">
  <!-- 텍스트 -->
  {#if message.text}
    <p class="message-text">{message.text}</p>
  {/if}

  <!-- 첨부파일 목록 (신규 추가) -->
  {#if message.urls && Object.keys(message.urls).length > 0}
    <div class="message-attachments">
      {#each Object.entries(message.urls) as [index, url]}
        <a
          href={url}
          target="_blank"
          rel="noopener noreferrer"
          class="attachment-item"
        >
          <!-- URL에서 파일명 추출 -->
          {#if isImageUrl(url)}
            <img src={url} alt="첨부 이미지" class="attachment-image" />

          {:else if isVideoUrl(url)}
            <video src={url} class="attachment-video" controls />

          {:else}
            <!-- 일반 파일 링크 -->
            <div class="attachment-file">
              <svg class="file-icon"><!-- 파일 아이콘 --></svg>
              <div class="file-details">
                <p class="file-name">{getFilenameFromUrl(url)}</p>
              </div>
              <svg class="download-icon"><!-- 다운로드 아이콘 --></svg>
            </div>
          {/if}
        </a>
      {/each}
    </div>
  {/if}

  <span class="message-timestamp">{formatLongDate(message.createdAt)}</span>
</div>
```

**스타일:**
```css
.message-attachments {
  @apply mt-2 space-y-2;
}

.attachment-item {
  @apply block overflow-hidden rounded-lg transition-opacity hover:opacity-90;
}

.attachment-image {
  @apply max-h-64 w-full rounded-lg object-cover;
}

.attachment-video {
  @apply max-h-64 w-full rounded-lg;
}

.attachment-file {
  @apply flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3;
  @apply transition-colors hover:bg-gray-100;
}

.file-icon {
  @apply h-10 w-10 shrink-0 text-gray-400;
}

.file-details {
  @apply flex-1 overflow-hidden;
}

.file-name {
  @apply truncate text-sm font-medium text-gray-900;
}

.file-size {
  @apply text-xs text-gray-500;
}

.download-icon {
  @apply h-5 w-5 shrink-0 text-blue-500;
}
```

---

## 4. 기능 구현 상세

### 4.1 파일 업로드 함수

**파일:** `src/lib/functions/storage.functions.ts` (신규 생성)

```typescript
import { storage } from '$lib/firebase';
import { ref, uploadBytesResumable, getDownloadURL, type UploadTask } from 'firebase/storage';

/**
 * 채팅 파일을 Firebase Storage에 업로드합니다.
 *
 * @param file - 업로드할 파일
 * @param uid - 사용자 UID
 * @param roomId - 채팅방 ID
 * @param onProgress - 업로드 진행률 콜백 (0-100)
 * @returns Promise<string> 업로드된 파일의 다운로드 URL
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
        console.log(`📤 업로드 진행률: ${progress}% (${file.name})`);
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
          console.log('✅ 파일 업로드 성공:', downloadUrl);
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
 */
export function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 B';

  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return `${(bytes / Math.pow(k, i)).toFixed(1)} ${sizes[i]}`;
}
```

### 4.2 Svelte 컴포넌트 로직

**파일:** `src/routes/chat/room/+page.svelte` 수정

```typescript
import { uploadMultipleChatFiles, formatFileSize } from '$lib/functions/storage.functions';
import type { FileUploadStatus } from '$lib/types/chat.types';

// 파일 업로드 상태
let fileInputRef: HTMLInputElement | null = $state(null);
let uploadingFiles: FileUploadStatus[] = $state([]);

/**
 * URL에서 파일명을 추출합니다.
 * @param url - Firebase Storage URL
 * @returns 파일명
 */
function getFilenameFromUrl(url: string): string {
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
 * @param url - Firebase Storage URL
 * @returns 이미지이면 true
 */
function isImageUrl(url: string): boolean {
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp', '.svg'];
  const lowerUrl = url.toLowerCase();
  return imageExtensions.some(ext => lowerUrl.includes(ext));
}

/**
 * URL이 동영상인지 확인합니다.
 * @param url - Firebase Storage URL
 * @returns 동영상이면 true
 */
function isVideoUrl(url: string): boolean {
  const videoExtensions = ['.mp4', '.mov', '.avi', '.webm', '.mkv'];
  const lowerUrl = url.toLowerCase();
  return videoExtensions.some(ext => lowerUrl.includes(ext));
}

/**
 * 파일 선택 버튼 클릭 핸들러
 */
function handleFileButtonClick() {
  fileInputRef?.click();
}

/**
 * 파일 선택 핸들러
 */
async function handleFileSelect(event: Event) {
  const input = event.target as HTMLInputElement;
  const files = Array.from(input.files || []);

  if (files.length === 0) return;

  console.log(`📂 ${files.length}개 파일 선택됨`);

  // 파일별 상태 초기화
  const newFiles: FileUploadStatus[] = files.map((file) => ({
    file,
    progress: 0,
    completed: false
  }));

  // 이미지/동영상 미리보기 생성
  for (const fileStatus of newFiles) {
    if (
      fileStatus.file.type.startsWith('image/') ||
      fileStatus.file.type.startsWith('video/')
    ) {
      fileStatus.previewUrl = URL.createObjectURL(fileStatus.file);
    }
  }

  uploadingFiles = [...uploadingFiles, ...newFiles];

  // input 초기화 (같은 파일 다시 선택 가능하도록)
  input.value = '';
}

/**
 * 파일 삭제 핸들러 (업로드 전)
 */
function handleRemoveFile(index: number) {
  const fileStatus = uploadingFiles[index];

  // 미리보기 URL 해제
  if (fileStatus.previewUrl) {
    URL.revokeObjectURL(fileStatus.previewUrl);
  }

  uploadingFiles = uploadingFiles.filter((_, i) => i !== index);
}

/**
 * 메시지 전송 핸들러 (파일 업로드 포함)
 */
async function handleSendMessage(event: SubmitEvent) {
  event.preventDefault();

  if (isSending) return;
  if (!composerText.trim() && uploadingFiles.length === 0) return;
  if (!authStore.user?.uid) {
    sendError = m.chatSignInToSend();
    return;
  }
  if (!activeRoomId) {
    sendError = m.chatRoomNotReady();
    return;
  }

  isSending = true;
  sendError = null;

  try {
    let urls: Record<number, string> | undefined;

    // 1. 파일 업로드 (있는 경우)
    if (uploadingFiles.length > 0) {
      console.log(`📤 ${uploadingFiles.length}개 파일 업로드 시작`);

      const files = uploadingFiles.map((fs) => fs.file);

      urls = await uploadMultipleChatFiles(
        files,
        authStore.user.uid,
        activeRoomId,
        (fileIndex, progress) => {
          uploadingFiles[fileIndex].progress = progress;
          uploadingFiles = [...uploadingFiles]; // 반응성 트리거
        }
      );

      // 업로드 완료 표시
      uploadingFiles.forEach((fs) => {
        fs.completed = true;
      });

      console.log('✅ 모든 파일 업로드 완료');
    }

    // 2. 메시지 전송
    const trimmed = composerText.trim();
    const timestamp = Date.now();

    const payload = {
      roomId: activeRoomId,
      type: 'message',
      text: trimmed,
      urls: urls || {},
      senderUid: authStore.user.uid,
      createdAt: timestamp,
      editedAt: null,
      deletedAt: null,
      roomOrder: `-${activeRoomId}-${timestamp}`,
      rootOrder: `-${activeRoomId}-${timestamp}`
    };

    const result = await pushData(messagePath, payload);

    if (!result.success) {
      sendError = result.error ?? m.chatSendFailed();
      isSending = false;
    } else {
      // 메시지 전송 성공 시
      composerText = '';
      sendError = null;
      isSending = false;

      // 업로드된 파일 목록 초기화
      uploadingFiles.forEach((fs) => {
        if (fs.previewUrl) {
          URL.revokeObjectURL(fs.previewUrl);
        }
      });
      uploadingFiles = [];

      // 전송 소리 재생
      try {
        const sendSound = new Audio('/sound/send.mp3');
        sendSound.play().catch((error) => {
          console.warn('소리 재생 실패:', error);
        });
      } catch (error) {
        console.warn('소리 재생 실패:', error);
      }

      // DOM 업데이트 완료 후 포커스 추가
      await tick();

      // 브라우저 렌더링 완료를 확실히 기다린 후 포커스
      requestAnimationFrame(() => {
        if (composerInputRef) {
          composerInputRef.focus();
          console.log('✅ 채팅 입력 창에 포커스 추가됨');
        }
      });
    }
  } catch (error) {
    console.error('❌ 파일 업로드 또는 메시지 전송 실패:', error);
    sendError = '파일 업로드에 실패했습니다. 다시 시도해주세요.';
    isSending = false;
  }
}
```

---

## 5. Firebase Storage Security Rules

**파일:** `firebase/storage.rules` (신규 생성 또는 수정)

```
rules_version = '2';

service firebase.storage {
  match /b/{bucket}/o {
    // 사용자별 디렉토리
    match /users/{userId} {
      // 채팅 파일: 본인만 업로드 가능, 모든 인증된 사용자 읽기 가능
      match /chat-files/{roomId}/{filename} {
        // 본인만 업로드 가능
        allow write: if request.auth != null && request.auth.uid == userId;

        // 모든 인증된 사용자 읽기 가능 (채팅방 멤버 확인은 복잡하므로 생략)
        allow read: if request.auth != null;
      }

      // 프로필 사진 등 기타 파일
      match /{allPaths=**} {
        allow read: if request.auth != null;
        allow write: if request.auth != null && request.auth.uid == userId;
      }
    }
  }
}
```

**배포 명령어:**
```bash
firebase deploy --only storage
```

---

## 6. 구현 순서 및 체크리스트

### Phase 1: 데이터 구조 및 타입 정의
- [ ] `src/lib/types/chat.types.ts` 파일 생성
- [ ] `ChatMessage`, `FileUploadStatus` 타입 정의 (urls는 `Record<number, string>` 타입)
- [ ] `firebase/functions/src/types/index.ts`에 동일한 타입 추가 (Cloud Functions용)
- [ ] **주의:** ChatAttachment 타입은 제거됨 (RTDB 용량 최소화를 위해 URL만 저장)

### Phase 2: Storage 함수 구현
- [ ] `src/lib/functions/storage.functions.ts` 파일 생성
- [ ] `uploadChatFile()` 함수 구현 (반환값: `Promise<string>` - URL만 반환)
- [ ] `uploadMultipleChatFiles()` 함수 구현 (반환값: `Promise<Record<number, string>>`)
- [ ] `formatFileSize()` 유틸리티 함수 구현

### Phase 3: UI 컴포넌트 구현
- [ ] 파일 업로드 버튼 추가 (`src/routes/chat/room/+page.svelte`)
- [ ] 숨겨진 파일 입력 추가
- [ ] `handleFileButtonClick()` 구현
- [ ] `handleFileSelect()` 구현
- [ ] `handleRemoveFile()` 구현
- [ ] URL 헬퍼 함수 구현 (`getFilenameFromUrl`, `isImageUrl`, `isVideoUrl`)

### Phase 4: 파일 미리보기 UI
- [ ] 파일 미리보기 그리드 컴포넌트 추가
- [ ] 이미지 미리보기 렌더링 (배경)
- [ ] 동영상 미리보기 렌더링 (배경)
- [ ] 일반 파일 아이콘 렌더링 (배경)
- [ ] **업로드 진행률 퍼센티지 숫자 표시** (이미지 참고: 큰 숫자로 중앙 오버레이)
- [ ] 반투명 오버레이 배경 (`bg-black/40 backdrop-blur-sm`)
- [ ] 에러 메시지 오버레이 표시
- [ ] 삭제 버튼 기능 (우측 상단, 항상 표시)

### Phase 5: 메시지 전송 로직 수정
- [ ] `handleSendMessage()` 함수 수정
- [ ] 파일 업로드 후 메시지 전송 흐름 구현
- [ ] 에러 처리 강화
- [ ] 업로드 완료 후 상태 초기화

### Phase 6: 메시지 표시 UI
- [ ] 메시지 버블 내 첨부파일 표시 영역 추가
- [ ] 이미지 첨부파일 렌더링 (URL 기반)
- [ ] 동영상 첨부파일 렌더링 (controls 포함, URL 기반)
- [ ] 일반 파일 첨부파일 렌더링 (URL에서 파일명 추출)
- [ ] 다운로드 아이콘 추가
- [ ] **주의:** `Object.entries(message.urls)`로 [index, url] 쌍을 순회

### Phase 7: Firebase Storage Security Rules
- [ ] `firebase/storage.rules` 파일 생성/수정
- [ ] 읽기/쓰기 규칙 정의
- [ ] `firebase deploy --only storage` 실행

### Phase 8: 테스트 및 검증
- [ ] 단일 파일 업로드 테스트
- [ ] 다중 파일 업로드 테스트
- [ ] 이미지 파일 미리보기 확인
- [ ] 동영상 파일 미리보기 확인
- [ ] 일반 파일 업로드 확인
- [ ] 업로드 진행률 표시 확인
- [ ] 파일 삭제 기능 확인
- [ ] 메시지 전송 후 첨부파일 표시 확인
- [ ] 다운로드 기능 확인
- [ ] 모바일 환경 테스트
- [ ] 에러 시나리오 테스트

### Phase 9: 최적화 및 개선
- [ ] 대용량 파일 업로드 제한 (예: 최대 10MB)
- [ ] 파일 타입 검증 강화
- [ ] 이미지 자동 리사이징 (선택 사항)
- [ ] 업로드 취소 기능 (선택 사항)
- [ ] 다국어 메시지 추가 (`messages/*.json`)

---

## 7. 주의사항 및 고려사항

### 7.0 데이터 최소화 전략 (중요)

**설계 결정:** RTDB 저장 용량 최소화를 위해 메타데이터를 제거하고 URL만 저장합니다.

**장점:**
- ✅ RTDB 용량 절약 (메타데이터 제거로 약 60-70% 용량 감소)
- ✅ 데이터 구조 단순화
- ✅ 쓰기/읽기 성능 향상

**단점 및 대응:**
- ⚠️ 파일명, 크기, 타입 정보가 RTDB에 없음
  - **대응:** URL에서 파일명 추출 (`getFilenameFromUrl` 함수)
  - **대응:** 파일 확장자로 타입 추론 (`isImageUrl`, `isVideoUrl` 함수)
  - **대응:** 필요시 Storage API로 메타데이터 조회 가능
- ⚠️ 파일 크기 표시 불가
  - **대응:** 미리보기 화면에서만 크기 표시 (업로드 전)
  - **대응:** 메시지 내에서는 크기 표시 생략

**권장사항:**
- 클라이언트에서 파일 업로드 시 로컬 상태로 메타데이터 보관 (미리보기용)
- 메시지 표시 시에는 URL만으로 충분 (이미지/동영상은 미리보기 가능)
- 파일 정보가 꼭 필요한 경우에만 Storage API 호출

### 7.1 파일 크기 제한
- **최대 파일 크기:** 10MB (권장)
- **이유:** 무료 Firebase 플랜의 Storage 용량 제한
- **구현 위치:** `handleFileSelect()` 함수에서 파일 크기 체크

```typescript
const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

if (file.size > MAX_FILE_SIZE) {
  fileStatus.error = '파일 크기는 10MB 이하여야 합니다.';
  console.error(`❌ 파일 크기 초과: ${file.name} (${formatFileSize(file.size)})`);
}
```

### 7.2 파일 타입 검증
- **클라이언트 검증:** `accept` 속성 사용
- **추가 검증:** MIME 타입 확인

```typescript
const ALLOWED_TYPES = [
  'image/jpeg',
  'image/png',
  'image/gif',
  'image/webp',
  'video/mp4',
  'video/quicktime',
  'application/pdf',
  'text/plain',
  'application/zip',
  'application/x-rar-compressed'
];

if (!ALLOWED_TYPES.includes(file.type)) {
  fileStatus.error = '지원하지 않는 파일 형식입니다.';
}
```

### 7.3 네트워크 에러 처리
- 업로드 실패 시 재시도 UI 제공
- 네트워크 연결 상태 확인
- 타임아웃 설정 (Firebase Storage 기본값 사용)

### 7.4 메모리 관리
- `URL.createObjectURL()` 사용 후 반드시 `URL.revokeObjectURL()` 호출
- 컴포넌트 언마운트 시 모든 미리보기 URL 해제

```typescript
// 컴포넌트 정리 (onDestroy)
onDestroy(() => {
  uploadingFiles.forEach((fs) => {
    if (fs.previewUrl) {
      URL.revokeObjectURL(fs.previewUrl);
    }
  });
});
```

### 7.5 보안
- Firebase Storage Security Rules로 접근 제어
- 악성 파일 업로드 방지 (추후 Cloud Functions에서 바이러스 스캔 추가 가능)
- HTTPS 필수 (Firebase Storage 기본값)

### 7.6 성능 최적화
- 이미지 lazy loading
- 동영상 자동 재생 방지 (controls 속성 사용)
- 대용량 파일 업로드 시 UI 프리징 방지 (비동기 처리)

### 7.7 접근성 (Accessibility)
- 파일 업로드 버튼에 `aria-label` 추가
- 키보드 네비게이션 지원
- 스크린 리더 지원

### 7.8 UI 디자인 주의사항 (첨부 이미지 참고)

**업로드 진행률 표시:**
- ✅ 퍼센티지를 **큰 숫자**로 중앙에 표시 (`text-5xl md:text-6xl`)
- ✅ 반투명 검은 배경 오버레이 (`bg-black/40 backdrop-blur-sm`)
- ✅ 숫자는 흰색, 굵게, 그림자 효과 (`font-bold text-white drop-shadow-lg`)
- ❌ 작은 progress bar는 사용하지 않음 (가시성이 낮음)

**삭제 버튼:**
- ✅ 우측 상단에 고정 (`absolute right-2 top-2 z-10`)
- ✅ 빨간색 원형 버튼 (`bg-red-500 rounded-full`)
- ✅ 크기: `h-8 w-8` (모바일에서도 클릭하기 쉬운 크기)
- ✅ 항상 표시 (업로드 전/중/후 모두)
- ✅ hover 효과: 크기 증가 (`hover:scale-110`)

**그리드 레이아웃:**
- ✅ 반응형 그리드 (`grid-cols-2 md:grid-cols-3 lg:grid-cols-4`)
- ✅ 정사각형 비율 유지 (`aspect-square`)
- ✅ 테두리로 각 항목 구분 (`border-2 rounded-lg`)

---

## 8. 향후 개선 사항 (선택 사항)

### 8.1 이미지 자동 리사이징
- **목적:** 스토리지 용량 절약, 로딩 속도 개선
- **구현:** Cloud Functions에서 업로드된 이미지 자동 리사이징
- **참고:** [Firebase Storage 이미지 리사이징 Extension](https://firebase.google.com/products/extensions/storage-resize-images)

### 8.2 업로드 취소 기능
- **목적:** 사용자가 업로드 중 취소 가능
- **구현:** `UploadTask.cancel()` 사용

### 8.3 드래그 앤 드롭 지원
- **목적:** 사용자 편의성 향상
- **구현:** `ondragover`, `ondrop` 이벤트 처리

### 8.4 파일 미리보기 확대
- **목적:** 이미지/동영상 상세 보기
- **구현:** 모달 다이얼로그 추가

### 8.5 파일 편집 기능
- **목적:** 이미지 크롭, 회전 등
- **구현:** Canvas API 또는 이미지 편집 라이브러리 사용

---

## 9. 관련 문서 및 참고 자료

### Firebase 공식 문서
- [Firebase Storage 업로드 가이드](https://firebase.google.com/docs/storage/web/upload-files)
- [Firebase Storage Security Rules](https://firebase.google.com/docs/storage/security)
- [Firebase Storage 모니터링](https://firebase.google.com/docs/storage/monitor)

### 프로젝트 내 문서
- [채팅 메시지 데이터 구조](./specs/sonub-firebase-database-structure.md#채팅-메시지-chat-messages)
- [Firebase 초기화](./src/lib/firebase.ts)
- [채팅방 페이지](./src/routes/chat/room/+page.svelte)

### 외부 라이브러리 (선택 사항)
- [ImageKit](https://imagekit.io/) - 이미지 최적화 CDN
- [react-dropzone](https://react-dropzone.js.org/) - 드래그 앤 드롭 라이브러리 (Svelte 버전 탐색 필요)

---

## 10. 완료 조건 (Definition of Done)

- ✅ 모든 체크리스트 항목 완료
- ✅ 타입 체크 통과 (`npm run check`)
- ✅ 빌드 성공 (`npm run build`)
- ✅ 단위 테스트 통과 (필요시)
- ✅ E2E 테스트 통과 (필요시)
- ✅ SED 사양 문서 업데이트
- ✅ Git 커밋 및 푸시

---

**작성 완료일:** 2025-11-14
**검토자:** [개발자 이름]
**승인자:** [프로젝트 매니저 이름]
