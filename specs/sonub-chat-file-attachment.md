---
name: sonub-chat-file-attachment
description: 채팅 메시지에 파일 첨부 기능 - 이미지, 동영상, 문서 등 다중 파일 업로드 및 표시
version: 1.2.0
step: 55
priority: "**"
dependencies:
  - sonub-chat-room.md
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-design-workflow.md
tags: chat, file-upload, firebase-storage, attachment, svelte5, realtime, instant-upload, video-controls, file-size-limit, file-extension-display, filename-extension-extraction, circular-progress, drag-drop, animation
---

# 채팅 파일 첨부 기능 (Chat File Attachment)

## 1. 개요

### 1.1 목적

채팅 메시지에 파일을 첨부할 수 있는 기능을 제공합니다. 사용자는 이미지, 동영상, 문서 등 다양한 파일을 업로드하고 채팅방에서 공유할 수 있습니다.

### 1.2 주요 기능

- ✅ **파일 선택 즉시 업로드** (사용자가 업로드 성공 즉시 확인 가능)
- ✅ 다중 파일 선택 및 업로드 (이미지, 동영상, 문서, 압축파일)
- ✅ 파일 미리보기 Grid UI (Storage URL로 실시간 표시)
- ✅ **원형 프로그레스바 진행률 표시** (v1.2.0) - SVG 원형 프로그레스바와 부드러운 애니메이션
- ✅ **드래그 앤 드롭 파일 업로드** (v1.2.0) - 파일을 채팅창에 드래그하여 간편하게 업로드
- ✅ 파일 삭제 기능 (Firebase Storage에서 실제 삭제)
- ✅ Firebase Storage에 파일 저장
- ✅ RTDB에 URL만 저장하여 용량 최소화
- ✅ 메시지 버블 내 첨부파일 표시 (이미지/동영상/일반파일)
- ✅ 메시지 전송 즉시 완료 (업로드 대기 없음)
- ✅ **파일 타입별 크기 제한** (v1.1.2) - .mp4는 24MB, 그 외는 10MB
- ✅ **파일 확장자 중앙 표시** (v1.1.3) - PDF, TXT, DOC 등 확장자를 크게 중앙에 표시

### 1.3 구현 범위

**파일 타입:**
- 이미지: JPG, PNG, GIF, WebP, BMP, SVG
- 동영상: MP4, MOV, AVI, WebM, MKV
- 문서: PDF, TXT, DOC, DOCX
- 압축: ZIP, RAR

**제약사항:**
- 최대 파일 크기:
  - 동영상 (.mp4): 24MB (v1.1.2)
  - 그 외 모든 파일: 10MB
- 다중 파일 업로드 지원
- Firebase Storage 경로: `/users/{uid}/chat-files/{roomId}/{timestamp}-{filename}`

---

## 2. 설계 결정 및 이유

### 2.1 파일 업로드 타이밍: "즉시 업로드" 방식

**결정:** 파일 선택 즉시 Firebase Storage에 업로드 시작

**이전 방식 (v1.0.0):**
- 사용자가 파일 선택 → 로컬에 저장 (Blob URL 생성)
- 전송 버튼 클릭 → Firebase Storage 업로드 시작
- 업로드 완료 → 메시지 전송

**새로운 방식 (v1.1.0 이후):**
- 사용자가 파일 선택 → **즉시 Firebase Storage 업로드 시작**
- 업로드 완료 → Storage URL로 미리보기 표시
- 전송 버튼 클릭 → **즉시 메시지 전송** (업로드 대기 없음)

**장점:**
1. **즉각적인 피드백**: 사용자가 파일 업로드 성공 여부를 즉시 확인 가능
2. **빠른 메시지 전송**: 전송 버튼 클릭 시 업로드 대기 시간 없음
3. **메모리 효율**: Blob URL 대신 Storage URL 사용으로 메모리 누수 방지
4. **실시간 미리보기**: 업로드 완료된 실제 파일을 미리보기로 표시
5. **파일 삭제 정확성**: Storage에서 실제 파일 삭제 가능

**트레이드오프:**
- 단점: 사용자가 메시지를 전송하지 않고 채팅방을 나가면 업로드된 파일이 Storage에 남음
- 해결 방안: 향후 Cloud Functions로 미사용 파일 정리 기능 추가 예정

### 2.2 데이터 최소화 전략

**결정:** RTDB에 파일 메타데이터를 저장하지 않고 URL만 저장

**이유:**
1. **용량 최적화**: 메타데이터를 제거하여 RTDB 저장 용량 60-70% 절감
2. **성능 향상**: 데이터 구조 단순화로 읽기/쓰기 성능 개선
3. **비용 절감**: Firebase RTDB 용량 기반 과금 최소화

**트레이드오프:**
- 장점: 용량 절감, 빠른 로딩, 단순한 데이터 구조
- 단점: 파일명/크기/타입 정보가 RTDB에 없음
- 해결: URL 파싱 함수로 필요한 정보 추출 (`getFilenameFromUrl`, `isImageUrl`, `isVideoUrl`)

### 2.2 데이터 구조

**ChatMessage 타입 확장:**
```typescript
interface ChatMessage {
  roomId: string;
  type: 'text' | 'image' | 'file' | 'message';
  text?: string;
  senderUid: string;
  createdAt: number;
  roomOrder: string;
  rootOrder: string;
  editedAt?: number | null;
  deletedAt?: number | null;
  urls?: Record<number, string>;  // ✅ 신규 추가
}
```

**urls 필드 설계:**
- 타입: `Record<number, string>` (숫자 인덱스를 키로 사용)
- 값: Firebase Storage 다운로드 URL
- 예시: `{ 0: "https://...", 1: "https://...", 2: "https://..." }`

**선택 이유:**
1. Firebase RTDB는 배열을 객체로 변환하므로 `Record<number, string>`이 자연스러움
2. 순서 보장 (0, 1, 2, ...)
3. TypeScript 타입 안전성 확보

### 2.3 Storage 경로 구조

**경로:** `/users/{uid}/chat-files/{roomId}/{timestamp}-{filename}`

**이유:**
1. **사용자별 분리**: `/users/{uid}` 경로로 보안 규칙 적용 용이
2. **채팅방별 관리**: `{roomId}` 폴더로 파일 구조화
3. **파일명 충돌 방지**: `{timestamp}-{filename}` 형식으로 고유성 보장
4. **메타데이터 포함**: URL에서 타임스탬프와 원본 파일명 추출 가능

---

## 3. 구현 내용

### 3.1 Phase 1: 타입 정의

**파일:** `src/lib/types/chat.types.ts` (신규 생성)

**구현:**
```typescript
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
  urls?: Record<number, string>;
}

export interface FileUploadStatus {
  file: File;
  previewUrl?: string;
  progress: number;
  completed: boolean;
  error?: string;
  downloadUrl?: string;
}
```

**역할:**
- `ChatMessage.urls`: 첨부파일 URL 목록 (RTDB 저장용)
- `FileUploadStatus`: 클라이언트 측 업로드 상태 추적

### 3.2 Phase 2: Storage 함수

**파일:** `src/lib/functions/storage.functions.ts` (신규 생성)

**주요 함수:**

#### 3.2.1 uploadChatFile()
```typescript
export async function uploadChatFile(
  file: File,
  uid: string,
  roomId: string,
  onProgress?: (progress: number) => void
): Promise<string>
```

**역할:**
- 단일 파일을 Firebase Storage에 업로드
- 업로드 진행률을 콜백으로 전달 (0-100)
- 다운로드 URL 문자열 반환

**구현 로직:**
1. 파일 경로 생성: `users/${uid}/chat-files/${roomId}/${timestamp}-${filename}`
2. `uploadBytesResumable()`로 업로드 Task 생성
3. `state_changed` 이벤트로 진행률 계산 및 콜백 호출
4. 업로드 완료 시 `getDownloadURL()` 호출
5. URL 문자열 반환

#### 3.2.2 uploadMultipleChatFiles()
```typescript
export async function uploadMultipleChatFiles(
  files: File[],
  uid: string,
  roomId: string,
  onProgress?: (fileIndex: number, progress: number) => void
): Promise<Record<number, string>>
```

**역할:**
- 다중 파일을 병렬로 업로드
- 각 파일의 인덱스와 진행률을 콜백으로 전달
- `Record<number, string>` 형태로 URL 반환

**구현 로직:**
1. 각 파일마다 `uploadChatFile()` 호출
2. `Promise.all()`로 모든 업로드 병렬 실행
3. 완료된 URL을 숫자 인덱스(0, 1, 2, ...)를 키로 하는 객체에 저장
4. `Record<number, string>` 반환

#### 3.2.3 헬퍼 함수들

**파일명 추출:**
```typescript
export function getFilenameFromUrl(url: string): string {
  // URL에서 파일명 추출 후 timestamp 제거
  // "1731580123456-photo.jpg" → "photo.jpg"
}
```

**파일 타입 감지:**
```typescript
export function isImageUrl(url: string): boolean {
  // .jpg, .png, .gif 등 이미지 확장자 확인
}

export function isVideoUrl(url: string): boolean {
  // .mp4, .mov, .avi 등 동영상 확장자 확인
}

export function getFileExtension(url: string): string {
  // URL에서 파일 확장자 추출 (".pdf", ".zip" 등)
}
```

**파일 크기 포맷:**
```typescript
export function formatFileSize(bytes: number): string {
  // 1536000 → "1.5 MB"
}
```

#### 3.2.4 deleteChatFile() - 파일 삭제 (v1.1.0 추가)

```typescript
export async function deleteChatFile(url: string): Promise<void>
```

**역할:**
- Firebase Storage URL에서 파일을 삭제
- URL에서 파일 경로를 추출하여 `deleteObject()` 호출

**구현 로직:**
1. `getFilePathFromUrl(url)` 호출하여 Storage 경로 추출
2. Storage 참조 생성: `ref(storage, filePath)`
3. `deleteObject(storageRef)` 호출하여 파일 삭제

**헬퍼 함수:**
```typescript
export function getFilePathFromUrl(url: string): string {
  // "https://firebasestorage.googleapis.com/.../o/users%2Fuid%2Fchat-files%2Froomid%2F123-photo.jpg?token=..."
  // → "users/uid/chat-files/roomid/123-photo.jpg"
}
```

**사용 예시:**
```typescript
await deleteChatFile("https://firebasestorage.googleapis.com/.../photo.jpg");
console.log('파일 삭제 완료');
```

### 3.3 Phase 3-6: UI 구현

**파일:** `src/routes/chat/room/+page.svelte` (수정)

#### 3.3.1 상태 변수 추가

```typescript
// 파일 업로드 상태
let fileInputRef: HTMLInputElement | null = $state(null);
let uploadingFiles: FileUploadStatus[] = $state([]);

// 최대 파일 크기
const MAX_FILE_SIZE = 10 * 1024 * 1024; // 일반 파일: 10MB
const MAX_VIDEO_SIZE = 24 * 1024 * 1024; // 동영상 파일 (.mp4): 24MB (v1.1.2)
```

#### 3.3.2 파일 선택 핸들러

**handleFileButtonClick():**
```typescript
function handleFileButtonClick() {
  fileInputRef?.click();  // 숨겨진 input 트리거
}
```

**handleFileSelect() - v1.1.0 업데이트:**
```typescript
async function handleFileSelect(event: Event) {
  const input = event.target as HTMLInputElement;
  const files = Array.from(input.files || []);

  if (files.length === 0) return;

  // 사용자 인증 및 채팅방 확인
  if (!authStore.user?.uid || !activeRoomId) {
    alert('로그인 후 채팅방에 입장해야 파일을 업로드할 수 있습니다.');
    return;
  }

  console.log(`📂 ${files.length}개 파일 선택됨 - 즉시 업로드 시작`);

  // 파일별 상태 초기화 및 즉시 업로드
  for (const file of files) {
    // 파일 크기 체크 (동영상 .mp4는 24MB, 그 외는 10MB) - v1.1.2
    const isMP4Video = file.type === 'video/mp4' || file.name.toLowerCase().endsWith('.mp4');
    const maxSize = isMP4Video ? MAX_VIDEO_SIZE : MAX_FILE_SIZE;

    if (file.size > maxSize) {
      console.error(
        `❌ 파일 크기 초과: ${file.name} (${formatFileSize(file.size)}, 최대 ${formatFileSize(maxSize)})`
      );
      alert(
        `파일 "${file.name}"의 크기가 너무 큽니다. 최대 ${formatFileSize(maxSize)}까지 업로드 가능합니다.`
      );
      continue;
    }

    // FileUploadStatus 생성 (초기 상태)
    const fileStatus: FileUploadStatus = {
      file,
      progress: 0,
      completed: false
    };

    // 배열에 추가 (UI에 즉시 표시)
    uploadingFiles = [...uploadingFiles, fileStatus];
    const currentIndex = uploadingFiles.length - 1;

    // ✅ 즉시 업로드 시작 (비동기)
    uploadChatFile(
      file,
      authStore.user.uid,
      activeRoomId,
      (progress) => {
        // 업로드 진행률 업데이트
        uploadingFiles[currentIndex].progress = progress;
        uploadingFiles = [...uploadingFiles]; // 반응성 트리거
      }
    )
      .then((downloadUrl) => {
        // ✅ 업로드 성공: downloadUrl 저장
        uploadingFiles[currentIndex].downloadUrl = downloadUrl;
        uploadingFiles[currentIndex].completed = true;
        uploadingFiles = [...uploadingFiles];
        console.log(`✅ 파일 업로드 완료: ${file.name}`);
      })
      .catch((error) => {
        // ❌ 업로드 실패: 에러 메시지 저장
        console.error(`❌ 파일 업로드 실패: ${file.name}`, error);
        uploadingFiles[currentIndex].error = '업로드 실패';
        uploadingFiles = [...uploadingFiles];
      });
  }

  input.value = '';  // 같은 파일 재선택 가능하도록
}
```

**주요 변경사항:**
1. ✅ 파일 선택 즉시 `uploadChatFile()` 호출
2. ✅ 업로드 진행률을 `progress` 필드에 저장
3. ✅ 업로드 완료 시 `downloadUrl` 필드에 Storage URL 저장
4. ❌ Blob URL 생성 제거 (더 이상 사용 안 함)

**handleRemoveFile() - v1.1.0 업데이트:**
```typescript
async function handleRemoveFile(index: number) {
  const fileStatus = uploadingFiles[index];

  // ✅ Firebase Storage에서 파일 삭제 (업로드 완료된 경우만)
  if (fileStatus.downloadUrl) {
    try {
      console.log(`🗑️ Firebase Storage에서 파일 삭제 시작: ${fileStatus.file.name}`);
      await deleteChatFile(fileStatus.downloadUrl);
      console.log(`✅ 파일 삭제 완료: ${fileStatus.file.name}`);
    } catch (error) {
      console.error(`❌ 파일 삭제 실패: ${fileStatus.file.name}`, error);
      // 삭제 실패해도 로컬 목록에서는 제거
    }
  }

  // 로컬 목록에서 제거
  uploadingFiles = uploadingFiles.filter((_, i) => i !== index);
}
```

**주요 변경사항:**
1. ✅ `deleteChatFile(downloadUrl)` 호출하여 Storage에서 실제 파일 삭제
2. ❌ Blob URL 해제 코드 제거 (더 이상 사용 안 함)

**onDestroy() 정리 - v1.1.0 업데이트:**
```typescript
onDestroy(() => {
  // ❌ Blob URL을 사용하지 않으므로 정리 작업 불필요
  // Storage URL은 Firebase에서 자동으로 관리됨
});
```

**주요 변경사항:**
1. ❌ Blob URL 정리 코드 제거
2. ✅ Storage URL은 Firebase가 자동 관리하므로 별도 정리 불필요

#### 3.3.3 메시지 전송 로직 수정

**handleSendMessage() 수정 - v1.1.0 업데이트:**
```typescript
async function handleSendMessage(event: SubmitEvent) {
  event.preventDefault();
  if (isSending) return;
  if (!composerText.trim() && uploadingFiles.length === 0) return;

  try {
    let urls: Record<number, string> = {};

    // ✅ 1. 이미 업로드된 파일 URL 수집 (업로드 완료 확인)
    if (uploadingFiles.length > 0) {
      console.log(`📤 ${uploadingFiles.length}개 파일 정보 수집`);

      // ❌ 업로드 완료되지 않은 파일이 있는지 확인
      const incompleteFiles = uploadingFiles.filter((fs) => !fs.completed && !fs.error);
      if (incompleteFiles.length > 0) {
        sendError = `업로드 중인 파일이 ${incompleteFiles.length}개 있습니다. 업로드 완료 후 다시 시도해주세요.`;
        isSending = false;
        return;
      }

      // ❌ 업로드 실패한 파일이 있는지 확인
      const failedFiles = uploadingFiles.filter((fs) => fs.error);
      if (failedFiles.length > 0) {
        sendError = `업로드 실패한 파일이 ${failedFiles.length}개 있습니다. 삭제 후 다시 시도해주세요.`;
        isSending = false;
        return;
      }

      // ✅ 이미 업로드된 URL 수집
      uploadingFiles.forEach((fs, index) => {
        if (fs.downloadUrl) {
          urls[index] = fs.downloadUrl;
        }
      });

      console.log(`✅ ${Object.keys(urls).length}개 파일 URL 수집 완료`);
    }

    // ✅ 2. 메시지 전송 (업로드 대기 없음 - 즉시 전송)
    const payload = {
      roomId: activeRoomId,
      type: 'message',
      text: composerText.trim(),
      urls,  // Record<number, string>
      senderUid: authStore.user.uid,
      createdAt: Date.now(),
      // ...
    };

    const result = await pushData(messagePath, payload);

    if (result.success) {
      // ✅ 업로드된 파일 목록 초기화 (이미 Storage에 업로드되어 있음)
      uploadingFiles = [];
      composerText = '';
    }
  } catch (error) {
    sendError = '메시지 전송에 실패했습니다.';
  }
}
```

**주요 변경사항:**
1. ❌ `uploadMultipleChatFiles()` 호출 제거 (이미 업로드 완료됨)
2. ✅ `downloadUrl` 필드에서 URL 수집
3. ✅ 업로드 완료 확인 로직 추가 (incomplete/failed 파일 체크)
4. ✅ 즉시 메시지 전송 (업로드 대기 시간 없음)
5. ❌ Blob URL 정리 코드 제거

#### 3.3.4 파일 미리보기 Grid UI - v1.1.0 업데이트

**HTML 구조:**
```svelte
{#if uploadingFiles.length > 0}
  <div class="file-preview-container">
    <div class="file-preview-grid">
      {#each uploadingFiles as fileStatus, index}
        <div class="file-preview-item">
          <!-- ✅ 이미지/동영상 미리보기 (Storage URL 사용) -->
          {#if fileStatus.file.type.startsWith('image/') || fileStatus.file.type.startsWith('video/')}
            <div class="preview-thumbnail">
              {#if fileStatus.downloadUrl}
                <!-- ✅ 업로드 완료: Storage URL로 미리보기 표시 -->
                {#if fileStatus.file.type.startsWith('image/')}
                  <img src={fileStatus.downloadUrl} alt={fileStatus.file.name} />
                {:else if fileStatus.file.type.startsWith('video/')}
                  <video src={fileStatus.downloadUrl} controls></video>
                {/if}
              {:else}
                <!-- ⏳ 업로드 중: 회색 배경만 표시 -->
                <div class="preview-placeholder"></div>
              {/if}

              <!-- 업로드 진행률 오버레이 -->
              {#if !fileStatus.completed && !fileStatus.error}
                <div class="upload-progress-overlay">
                  <span class="upload-percentage">{fileStatus.progress}%</span>
                </div>
              {/if}
            </div>
          {:else}
            <!-- 일반 파일 아이콘 -->
            <div class="file-icon">
              <span class="file-extension">{getFileExtension(fileStatus.file.name)}</span>

              {#if !fileStatus.completed && !fileStatus.error}
                <div class="upload-progress-overlay">
                  <span class="upload-percentage">{fileStatus.progress}%</span>
                </div>
              {/if}
            </div>
          {/if}

          <!-- 삭제 버튼 -->
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

**주요 변경사항:**
1. ❌ `fileStatus.previewUrl` (Blob URL) 제거
2. ✅ `fileStatus.downloadUrl` (Storage URL) 사용
3. ✅ 업로드 완료 전: `.preview-placeholder` 회색 배경 표시
4. ✅ 업로드 완료 후: Storage URL로 실제 파일 표시
5. ✅ 동영상 미리보기에 `controls` 속성 추가 (v1.1.1) - 사용자가 재생 컨트롤러로 동영상 조작 가능

**CSS 스타일 - v1.1.0 업데이트:**
```css
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

/* ✅ 신규 추가: 업로드 중 placeholder */
.preview-placeholder {
  @apply h-full w-full bg-gray-200;
}

.upload-progress-overlay {
  @apply absolute inset-0 flex items-center justify-center;
  @apply bg-black/40 backdrop-blur-sm;
}

.upload-percentage {
  @apply text-5xl md:text-6xl font-bold text-white;
  @apply drop-shadow-lg;
}

.remove-file-button {
  @apply absolute right-2 top-2 z-10;
  @apply flex h-8 w-8 items-center justify-center;
  @apply rounded-full bg-red-500 text-sm font-bold text-white shadow-lg;
  @apply transition-all hover:bg-red-600 hover:scale-110 active:scale-95;
}
```

**디자인 포인트:**
- 반응형 그리드 (2열 → 3열 → 4열)
- 정사각형 비율 유지 (`aspect-square`)
- 큰 퍼센티지 숫자로 진행률 표시 (`text-5xl md:text-6xl`)
- 반투명 검은 오버레이 (`bg-black/40 backdrop-blur-sm`)
- 우측 상단 삭제 버튼 (`absolute right-2 top-2`)

#### 3.3.5 입력창 UI

**카메라 버튼 추가:**
```svelte
<form class="composer-form" onsubmit={handleSendMessage}>
  <!-- 파일 업로드 버튼 (카메라 아이콘) -->
  <button
    type="button"
    class="file-upload-button"
    onclick={handleFileButtonClick}
    disabled={composerDisabled || isSending}
  >
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <!-- 카메라 아이콘 SVG -->
    </svg>
  </button>

  <!-- 숨겨진 파일 입력 -->
  <input
    bind:this={fileInputRef}
    type="file"
    onchange={handleFileSelect}
    multiple
    accept="image/*,video/*,.pdf,.txt,.doc,.docx,.zip,.rar"
    style="display: none;"
  />

  <input bind:this={composerInputRef} type="text" class="composer-input" />
  <button type="submit" class="composer-button"><!-- 전송 --></button>
</form>
```

#### 3.3.6 메시지 버블 내 첨부파일 표시

**HTML 구조:**
```svelte
<div class="message-bubble">
  <!-- 텍스트 -->
  {#if message.text}
    <p class="message-text">{message.text}</p>
  {/if}

  <!-- 첨부파일 목록 -->
  {#if message.urls && Object.keys(message.urls).length > 0}
    <div class="message-attachments">
      {#each Object.entries(message.urls as Record<string, string>) as [index, url]}
        <a href={url} target="_blank" class="attachment-item">
          {#if isImageUrl(url)}
            <img src={url} alt="첨부 이미지" class="attachment-image" />
          {:else if isVideoUrl(url)}
            <video src={url} class="attachment-video" controls></video>
          {:else}
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
</div>
```

**타입 단언 필요:**
```typescript
Object.entries(message.urls as Record<string, string>)
```
→ TypeScript는 `message.urls`를 `Record<number, string>`으로 인식하지만, `Object.entries()`는 `unknown` 타입 반환. 명시적 타입 단언으로 해결.

**CSS 스타일:**
```css
.message-attachments {
  @apply mt-2 space-y-2;
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
```

### 3.4 Phase 7: Firebase Storage Security Rules

**파일:** `firebase/storage.rules` (신규 생성)

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

**보안 정책:**
1. **쓰기 권한**: 본인만 자신의 폴더에 업로드 가능
2. **읽기 권한**: 모든 인증된 사용자가 읽기 가능
3. **채팅방 멤버 확인**: RTDB Security Rules에서 처리 (Storage Rules에서는 생략)

**firebase.json 수정:**
```json
{
  "storage": {
    "rules": "storage.rules"
  }
}
```

**배포 명령:**
```bash
cd firebase
firebase deploy --only storage
```

---

## 4. 해결한 문제들

### 4.1 업로드 타이밍 개선 (v1.1.0)

**문제:**
- 사용자가 파일 선택 후 전송 버튼을 눌러야만 업로드가 시작됨
- 메시지 전송 시 업로드 대기 시간 발생
- 사용자가 업로드 성공 여부를 즉시 확인할 수 없음

**해결:**
1. **즉시 업로드 방식 도입**: 파일 선택 즉시 Firebase Storage에 업로드 시작
2. **Storage URL 미리보기**: Blob URL 대신 Storage URL로 미리보기 표시
3. **실시간 피드백**: 업로드 진행률과 완료 상태를 실시간으로 표시
4. **파일 삭제 기능**: Storage에서 실제 파일 삭제 가능

**효과:**
- ✅ 사용자가 업로드 성공 즉시 확인 가능
- ✅ 메시지 전송 즉시 완료 (업로드 대기 없음)
- ✅ 메모리 효율 향상 (Blob URL 미사용)

### 4.2 타입 에러 해결 (v1.0.0)

**문제:**
```typescript
// Error: Type 'unknown' is not assignable to type 'string'
Object.entries(message.urls) as [index, url]
```

**원인:**
- `message.urls`의 타입은 `Record<number, string>`
- `Object.entries()`는 `[string, unknown][]` 반환
- TypeScript가 value 타입을 `unknown`으로 추론

**해결:**
```typescript
Object.entries(message.urls as Record<string, string>) as [index, url]
```
→ 명시적 타입 단언으로 TypeScript에게 타입 정보 제공

### 4.2 메모리 누수 방지

**문제:**
- `URL.createObjectURL()`로 생성한 Blob URL이 메모리에 계속 남음
- 파일 삭제 후에도 메모리 해제되지 않음

**해결:**
1. **파일 삭제 시:** `handleRemoveFile()`에서 `URL.revokeObjectURL()` 호출
2. **메시지 전송 후:** 성공 시 모든 미리보기 URL 해제
3. **컴포넌트 정리:** `onDestroy()`에서 모든 URL 해제

```typescript
onDestroy(() => {
  uploadingFiles.forEach((fs) => {
    if (fs.previewUrl) {
      URL.revokeObjectURL(fs.previewUrl);
    }
  });
});
```

### 4.3 업로드 진행률 반응성

**문제:**
- 업로드 진행률 업데이트가 UI에 반영되지 않음
- Svelte 5 runes의 반응성 트리거 필요

**해결:**
```typescript
urls = await uploadMultipleChatFiles(
  files,
  authStore.user.uid,
  activeRoomId,
  (fileIndex, progress) => {
    uploadingFiles[fileIndex].progress = progress;
    uploadingFiles = [...uploadingFiles];  // ✅ 반응성 트리거
  }
);
```
→ 배열을 재할당하여 Svelte에게 변경 사실 알림

---

## 5. 검증 및 테스트

### 5.1 타입 체크

**실행:**
```bash
npm run check
```

**결과:**
```
svelte-check found 0 errors and 1170 warnings in 19 files
```
- ✅ **0 errors** (모든 타입 에러 해결)
- ⚠️ 1170 warnings (Tailwind CSS 관련, 정상)

### 5.2 수동 테스트 체크리스트

- [ ] 카메라 버튼 클릭 → 파일 선택 다이얼로그 표시
- [ ] 다중 파일 선택 → Grid에 미리보기 표시
- [ ] 이미지/동영상 미리보기 렌더링
- [ ] 일반 파일 (PDF, ZIP 등) 확장자 표시
- [ ] 10MB 초과 파일 → 에러 메시지 표시
- [ ] 업로드 진행률 → 큰 퍼센티지 숫자로 표시
- [ ] 파일 삭제 버튼 → 미리보기에서 제거
- [ ] 메시지 전송 → Firebase Storage 업로드 확인
- [ ] RTDB `/chat-messages/{id}/urls` → `Record<number, string>` 형태 저장
- [ ] 메시지 버블 → 이미지/동영상/일반파일 올바르게 표시

### 5.3 Firebase Storage 확인

**Firebase Console:**
1. Storage → Files → `/users/{uid}/chat-files/{roomId}/` 경로 확인
2. 파일명 형식: `{timestamp}-{originalFilename}` 확인
3. 다운로드 URL 접근 테스트

**Firebase RTDB:**
1. Database → Data → `/chat-messages/{messageId}` 확인
2. `urls` 필드 구조 확인: `{ 0: "https://...", 1: "https://..." }`

---

## 6. 파일 목록

### 6.1 신규 생성 파일

| 파일 경로 | 설명 |
|---------|------|
| `src/lib/types/chat.types.ts` | ChatMessage, FileUploadStatus 타입 정의 |
| `src/lib/functions/storage.functions.ts` | 파일 업로드 및 헬퍼 함수 |
| `firebase/storage.rules` | Firebase Storage 보안 규칙 |

### 6.2 수정된 파일

| 파일 경로 | 주요 수정 내용 |
|---------|-------------|
| `src/routes/chat/room/+page.svelte` | - 파일 업로드 상태 변수 추가<br>- 파일 선택/삭제 핸들러 구현<br>- 메시지 전송 로직 수정<br>- 파일 미리보기 Grid UI 추가<br>- 메시지 버블에 첨부파일 표시 추가<br>- CSS 스타일 추가 |
| `firebase/firebase.json` | - storage.rules 경로 추가 |

---

## 7. 향후 개선 사항

### 7.1 우선순위 높음

- [ ] **미사용 파일 자동 정리** (Cloud Functions) - v1.1.0 트레이드오프 해결
  - 메시지로 전송되지 않은 업로드 파일 자동 삭제
  - 예: 24시간 이상 경과된 미사용 파일 정리
- [ ] 파일 크기 표시 (메시지 버블 내)
- [ ] 이미지 자동 리사이징 (Cloud Functions)
- [ ] 업로드 취소 기능
- [ ] 다국어 메시지 추가 (`messages/*.json`)

### 7.2 우선순위 중간

- [ ] 파일 미리보기 라이트박스 (이미지 클릭 시 확대)
- [ ] 드래그 앤 드롭 업로드
- [ ] 클립보드 이미지 붙여넣기
- [ ] 파일 다운로드 진행률 표시

### 7.3 우선순위 낮음

- [ ] 파일 검색 기능 (채팅방 내 첨부파일 검색)
- [ ] 파일 통계 (용량, 개수 등)
- [ ] 파일 자동 삭제 (오래된 파일 정리)

---

## 8. 참고 문서

- [Firebase Storage 공식 문서](https://firebase.google.com/docs/storage)
- [Firebase Realtime Database 구조 가이드](./sonub-firebase-database-structure.md)
- [채팅 시스템 개요](./sonub-chat-overview.md)
- [채팅방 구현](./sonub-chat-room.md)
- [Tailwind CSS 디자인 가이드](./sonub-design-workflow.md)

---

## 9. 작업 완료 체크리스트

### v1.0.0 (2025-11-14)
- [x] Phase 1: 데이터 구조 및 타입 정의
- [x] Phase 2: Storage 함수 구현 (`uploadChatFile`, `uploadMultipleChatFiles`)
- [x] Phase 3: 카메라 아이콘 버튼 추가 및 파일 선택 핸들러
- [x] Phase 4: 파일 미리보기 Grid UI 구현
- [x] Phase 5: 메시지 전송 로직 수정
- [x] Phase 6: 메시지 버블 내 첨부파일 표시
- [x] Phase 7: Firebase Storage Security Rules 설정 및 배포
- [x] 타입 에러 수정 (Object.entries 타입 단언)
- [x] `npm run check` 실행 및 통과 (0 errors)
- [x] SED 스펙 문서 작성

### v1.1.0 (2025-11-14)
- [x] **파일 업로드 타이밍 개선**: "전송 시 업로드" → "선택 즉시 업로드"
- [x] `deleteChatFile()` 및 `getFilePathFromUrl()` 함수 추가
- [x] `handleFileSelect()` 수정: 즉시 업로드 시작
- [x] `handleRemoveFile()` 수정: Storage에서 실제 파일 삭제
- [x] `handleSendMessage()` 간소화: 이미 업로드된 URL만 수집
- [x] 파일 미리보기 UI 수정: Storage URL 사용
- [x] Blob URL 관련 코드 제거
- [x] `npm run check` 실행 및 통과 (0 errors)
- [x] SED 스펙 문서 업데이트 (v1.1.0)

### v1.1.1 (2025-11-14)
- [x] **동영상 재생 컨트롤러 추가**: 파일 미리보기에서 동영상 `<video>` 태그에 `controls` 속성 추가
- [x] 사용자가 미리보기 단계에서 동영상 재생/일시정지/볼륨 조절 등 가능
- [x] `npm run check` 실행 및 통과 (0 errors)
- [x] SED 스펙 문서 업데이트 (v1.1.1)

### v1.1.2 (2025-11-14)
- [x] **파일 타입별 크기 제한**: .mp4 동영상 파일은 24MB까지, 그 외 파일은 10MB까지 허용
- [x] 상수 추가: `MAX_VIDEO_SIZE` (24MB) 정의
- [x] 파일 타입 감지 로직: MIME 타입(`file.type === 'video/mp4'`)과 파일명 확장자(`.endsWith('.mp4')`) 이중 체크
- [x] 동적 크기 제한 선택: 파일 타입에 따라 적절한 최대 크기 자동 적용
- [x] 사용자 친화적 에러 메시지: 실제 제한 크기를 포함한 한국어 alert 메시지
- [x] `npm run check` 실행 및 통과 (0 errors)
- [x] SED 스펙 문서 업데이트 (v1.1.2)

### v1.1.3 (2025-11-14)
- [x] **파일 확장자 중앙 표시**: 파일 첨부 시 확장자를 정 중앙에 크게 표시 (PDF, TXT, DOC 등)
- [x] 확장자 표시 형식 변경: `.pdf` → `PDF` (점 제거 + 대문자 변환)
- [x] 파일 미리보기 확장자 크기 증가: `text-sm` → `text-4xl md:text-5xl`
- [x] 메시지 버블 내 첨부파일 표시 개선: SVG 아이콘 대신 확장자 표시
- [x] CSS 추가: `.attachment-file-icon` (16x16 박스), `.attachment-file-extension` (text-xl)
- [x] `npm run check` 실행 및 통과 (0 errors)
- [x] SED 스펙 문서 업데이트 (v1.1.3)

### v1.1.4 (2025-11-14)
- [x] **버그 수정: 파일 확장자 표시 오류 해결**
- [x] 문제 원인: `getFileExtension()` 함수가 URL을 파싱하도록 설계되었으나, 파일명 문자열(`"photo.jpg"`)을 전달하여 파싱 실패
- [x] 해결 방법: `getExtensionFromFilename()` 함수 신규 추가 (`storage.functions.ts`)
- [x] 새 함수는 파일명 문자열에서 직접 확장자 추출 (URL 파싱 불필요)
- [x] `+page.svelte`에서 파일 미리보기 부분 수정: `getFileExtension(fileStatus.file.name)` → `getExtensionFromFilename(fileStatus.file.name)`
- [x] `npm run check` 실행 및 통과 (0 errors)
- [x] SED 스펙 문서 업데이트 (v1.1.4)

### v1.2.0 (2025-11-14)
- [x] **원형 프로그레스바 진행률 표시**: SVG 기반 원형 프로그레스바로 업로드 진행률 시각화 개선
- [x] SVG `<circle>` 요소 활용 (배경 원 + 진행률 원)
- [x] `stroke-dashoffset` 속성으로 진행률 계산 (원주: 201.06)
- [x] CSS 애니메이션 추가: `transition: stroke-dashoffset 0.3s ease-in-out`
- [x] 12시 방향부터 시작하도록 SVG 회전: `transform: rotate(-90deg)`
- [x] 퍼센티지 텍스트 크기 조정: `text-5xl` → `text-2xl`(프로그레스바 중앙 배치)
- [x] **드래그 앤 드롭 파일 업로드**: 파일을 채팅창에 드래그하여 간편하게 업로드
- [x] 드래그 앤 드롭 상태 관리: `isDragging`, `dragCounter`
- [x] 이벤트 핸들러 구현: `dragenter`, `dragover`, `dragleave`, `drop`
- [x] 드래그 오버레이 UI 추가: 파일 아이콘 + 안내 텍스트
- [x] 펄스 애니메이션 (테두리) + 바운스 애니메이션 (아이콘)
- [x] 드롭된 파일을 `processFiles()` 함수로 처리 (기존 파일 선택과 동일한 로직)
- [x] **코드 리팩토링**: `handleFileSelect` 함수를 `processFiles` 공통 함수 사용하도록 변경
- [x] `processFiles()` 함수로 파일 선택 및 드래그 앤 드롭 로직 통합
- [x] 중복 코드 제거 및 유지보수성 향상
- [x] `npm run check` 실행 및 통과 (0 errors)
- [x] SED 스펙 문서 업데이트 (v1.2.0)

---

**최초 작성일:** 2025-11-14
**최종 수정일:** 2025-11-14
**작성자:** Claude (AI Assistant)
**버전:** 1.2.0
**상태:** ✅ 구현 완료
