---
title: 채팅방 비밀번호 설정 기능
version: 2.0.0
step: 53
priority: **
dependencies:
  - sonub-chat-overview.md
  - sonub-firebase-database-structure.md
  - sonub-firebase-security-rules.md
  - sonub-firebase-cloud-functions.md
tags: [chat, password, security, firestore, cloud-functions, svelte5]
author: Claude Code
created: 2025-11-14
updated: 2025-11-15
status: completed
---

# 채팅방 비밀번호 설정 기능

## 1. 개요

### 1.1 목적
- 그룹/오픈 채팅방 관리자(owner)가 비밀번호를 설정하여 허가된 사용자만 채팅방에 입장할 수 있도록 제어합니다.
- Firebase Security Rules와 Cloud Functions를 결합하여 안전하고 확장 가능한 비밀번호 검증 시스템을 구축합니다.

### 1.2 핵심 기능
- ✅ Owner만 비밀번호 설정/변경/삭제 가능
- ✅ 비밀번호 설정 시 신규 사용자 입장 차단 (Firestore Security Rules)
- ✅ Cloud Functions 기반 비밀번호 검증 (Firestore Triggers)
- ✅ 검증 완료 시 자동으로 members 서브컬렉션에 추가
- ✅ 이미 members인 사용자는 비밀번호 불필요
- ✅ 5초 타임아웃 기반 실시간 검증 UI (onSnapshot)

---

## 2. Database 구조 (Firestore)

### 2.1 `chats/{roomId}` 문서
```typescript
{
  owner: "owner-uid",           // 채팅방 소유자
  password: true,               // 비밀번호 설정 여부 플래그 (true 또는 필드 삭제)
  // ... 기타 채팅방 정보
}
```

**중요**: `password` 필드는 `true` 또는 필드가 존재하지 않음(undefined)만 가능합니다. `false`를 저장하지 않습니다.

### 2.2 `chats/{roomId}/members/{uid}` 서브컬렉션
```typescript
{
  member: true,                  // 멤버 여부
  joinedAt: Timestamp            // 입장 시각 (서버 타임스탬프)
}
```

**멤버 확인 방법**:
```typescript
// Firestore에서 멤버 여부 확인
const memberDoc = await getDoc(doc(db, `chats/${roomId}/members/${uid}`));
const isMember = memberDoc.exists() && memberDoc.data()?.member === true;
```

### 2.3 `chats/{roomId}/password-data/password` 문서
```typescript
{
  password: "plain-text-password"  // 비밀번호 (Plain Text - 암호화 안 함)
}
```

**보안 고려사항**:
- `password-data` 서브컬렉션은 Owner만 읽기/쓰기 가능 (Security Rules)
- Cloud Functions는 admin 권한으로 모든 데이터 접근 가능

### 2.4 `chats/{roomId}/password-tries/{uid}` 서브컬렉션
```typescript
{
  password: "input-password",    // 사용자가 입력한 비밀번호
  timestamp: number               // 시도 시각 (밀리초)
}
```

**중요**:
- 사용자가 비밀번호 입력 시 이 경로에 문서 생성
- Cloud Functions가 트리거되어 비밀번호 검증
- 검증 후 즉시 삭제됨

---

## 3. 입장 제어 플로우

### 3.1 전체 플로우 (Firestore)
```
1. 사용자 채팅방 입장 시도
   ↓
2. chats/{roomId}/members/{uid} 문서 확인
   ├─ 존재함 (member === true) → 바로 입장 (비밀번호 불필요)
   └─ 없음 → 3번으로
   ↓
3. chats/{roomId} 문서의 password 필드 확인
   ├─ 필드 없음 → 바로 입장
   └─ true → 4번으로
   ↓
4. 비밀번호 입력 모달 표시
   ↓
5. 사용자 비밀번호 입력
   ↓
6. chats/{roomId}/password-tries/{uid} 에 문서 생성 (입력값 저장)
   ↓
7. Cloud Functions 자동 트리거 (onDocumentWritten)
   ├─ 비밀번호 일치 → chats/{roomId}/members/{uid} 문서 생성: { member: true, joinedAt: serverTimestamp() }
   └─ 비밀번호 불일치 → password-tries/{uid} 문서 삭제 (에러 로그)
   ↓
8. 클라이언트: 5초 동안 onSnapshot()으로 chats/{roomId}/members/{uid} 실시간 확인
   ├─ 문서 생성됨 (member === true) → invalidate('chat:room') → 입장 성공
   └─ 5초 경과 → "비밀번호가 올바르지 않습니다" 에러 표시
```

### 3.2 상세 단계별 설명

#### 3.2.1 입장 시도 (Step 1-3)
- 채팅방 페이지 로드 시 `/chat-rooms/{roomId}` 데이터 읽기 시도
- Security Rules에 의해:
  - members인 경우: 읽기 허용
  - members가 아닌 경우: 읽기 차단 → 비밀번호 검증 필요

#### 3.2.2 비밀번호 입력 (Step 4-6)
- Dialog/Modal 형태로 비밀번호 입력 UI 표시
- 입력값을 `chats/{roomId}/password-tries/{uid}` 문서로 저장
- 저장 즉시 5초 타이머 시작

#### 3.2.3 Cloud Functions 검증 (Step 7)
- `onDocumentWritten` 트리거로 자동 실행 (Firestore)
- 저장된 비밀번호와 입력값 비교
- 일치: `chats/{roomId}/members/{uid}` 문서 생성 `{ member: true, joinedAt: serverTimestamp() }`
- 불일치: `password-tries/{uid}` 문서 삭제만 수행

#### 3.2.4 클라이언트 실시간 리스닝 (Step 8)
- `onSnapshot()`으로 `chats/{roomId}/members/{uid}` 문서 실시간 확인
- 문서 생성됨 (member === true): `invalidate('chat:room')` → 페이지 데이터 재로드
- 5초 경과: 에러 메시지 + 모달 다시 표시

---

## 4. Firestore Security Rules

### 4.1 `chats/{roomId}` Rules
```javascript
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /chats/{roomId} {
      // 채팅방 문서 읽기
      allow read: if request.auth != null && (
        // Owner는 항상 읽기 가능
        resource.data.owner == request.auth.uid ||
        // Members는 항상 읽기 가능 (members 서브컬렉션 확인)
        exists(/databases/$(database)/documents/chats/$(roomId)/members/$(request.auth.uid))
      );

      // password 필드 수정
      allow update: if request.auth != null &&
        resource.data.owner == request.auth.uid &&
        // password 필드만 수정 가능
        request.resource.data.diff(resource.data).affectedKeys().hasOnly(['password']);

      // Members 서브컬렉션
      match /members/{uid} {
        allow read: if request.auth != null;

        allow create: if request.auth != null && (
          // 1) 비밀번호가 설정되지 않은 경우
          !get(/databases/$(database)/documents/chats/$(roomId)).data.password ||
          // 2) Owner인 경우 (항상 입장 가능)
          get(/databases/$(database)/documents/chats/$(roomId)).data.owner == request.auth.uid
        );

        // 기존 member는 자신의 문서 수정/삭제 가능 (나가기 기능)
        allow update, delete: if request.auth != null && request.auth.uid == uid;
      }
    }
  }
}
```

**핵심 로직**:
1. **신규 추가 차단**: `password` 필드가 true이고 owner가 아니면 members 생성 불가
2. **기존 member**: 본인 문서는 수정/삭제 가능 (나가기 기능)
3. **Owner 특권**: Owner는 비밀번호 설정 여부와 관계없이 항상 자신을 members에 추가 가능
4. **비밀번호 미설정**: `password` 필드가 없으면 누구나 자신을 members에 추가 가능

### 4.2 `chats/{roomId}/password-data` Rules
```javascript
match /chats/{roomId}/password-data/password {
  // Owner만 비밀번호 읽기/쓰기 가능
  allow read, write: if request.auth != null &&
    get(/databases/$(database)/documents/chats/$(roomId)).data.owner == request.auth.uid;
}
```

### 4.3 `chats/{roomId}/password-tries` Rules
```javascript
match /chats/{roomId}/password-tries/{uid} {
  // 본인만 자신의 시도 기록 생성 가능
  allow create: if request.auth != null && request.auth.uid == uid;

  // 읽기 권한 없음 (Cloud Functions만 읽기)
}
```

**핵심 로직**:
1. `password-data/password`: Owner만 읽기/쓰기 가능
2. `password-tries/{uid}`: 본인만 생성 가능
3. `password-tries` 읽기 권한 없음 (Cloud Functions만 읽기)

---

## 5. Cloud Functions 구현 (Firestore)

### 5.1 함수 개요
**파일**: `firebase/functions/src/handlers/chat.password-verification.handler.ts`

**트리거**: `onDocumentWritten('chats/{roomId}/password-tries/{uid}')`

**로직**:
1. `password-tries/{uid}` 문서에 기록된 입력 비밀번호 읽기
2. `chats/{roomId}/password-data/password` 문서에서 실제 비밀번호 읽기
3. 문자열 비교 (Plain Text 비교)
4. 일치하면:
   - `chats/{roomId}/members/{uid}` 문서 생성: `{ member: true, joinedAt: serverTimestamp() }`
   - `password-tries/{uid}` 문서 삭제
5. 불일치하면:
   - `password-tries/{uid}` 문서 삭제
   - 에러 로그 기록

### 5.2 코드 예시
```typescript
import * as logger from "firebase-functions/logger";
import * as admin from "firebase-admin";
import { onDocumentWritten } from "firebase-functions/v2/firestore";

/**
 * 채팅방 비밀번호 검증 핸들러 (Firestore)
 *
 * chats/{roomId}/password-tries/{uid} 경로에 값이 기록되면 자동 실행됩니다.
 *
 * @param roomId - 채팅방 ID
 * @param uid - 사용자 UID
 * @param tryPassword - 입력된 비밀번호
 */
export async function handlePasswordVerification(
  roomId: string,
  uid: string,
  tryPassword: string
): Promise<void> {
  logger.info("채팅방 비밀번호 검증 시작", { roomId, uid });

  const db = admin.firestore();

  try {
    // 1. 실제 비밀번호 읽기 (Firestore)
    const passwordDocRef = db.doc(`chats/${roomId}/password-data/password`);
    const passwordDoc = await passwordDocRef.get();

    if (!passwordDoc.exists) {
      logger.error("❌ 비밀번호가 설정되지 않음", { roomId, uid });
      await db.doc(`chats/${roomId}/password-tries/${uid}`).delete();
      return;
    }

    const actualPassword = passwordDoc.data()?.password as string;

    // 2. 비밀번호 비교 (Plain Text)
    if (tryPassword === actualPassword) {
      logger.info("✅ 비밀번호 일치 - members에 추가 시작", { roomId, uid });

      // 3. members에 추가 (admin 권한으로 Security Rules 우회)
      await db.doc(`chats/${roomId}/members/${uid}`).set({
        member: true,
        joinedAt: admin.firestore.FieldValue.serverTimestamp()
      });

      logger.info("✅ members 추가 완료", { roomId, uid });
    } else {
      logger.warn("❌ 비밀번호 불일치", { roomId, uid });
    }

    // 4. try 경로 삭제 (보안상 즉시 삭제)
    await db.doc(`chats/${roomId}/password-tries/${uid}`).delete();

    logger.info("✅ try 경로 삭제 완료", { roomId, uid });

  } catch (error) {
    logger.error("❌ 비밀번호 검증 에러", { roomId, uid, error });

    // 에러 발생 시에도 try 경로 삭제
    try {
      await db.doc(`chats/${roomId}/password-tries/${uid}`).delete();
    } catch (deleteError) {
      logger.error("❌ try 경로 삭제 실패", { roomId, uid, error: deleteError });
    }
  }
}

/**
 * Cloud Functions 트리거 등록 (Firestore)
 */
export const onPasswordTry = onDocumentWritten(
  {
    document: "chats/{roomId}/password-tries/{uid}",
    region: "asia-southeast1"
  },
  async (event) => {
    const roomId = event.params.roomId as string;
    const uid = event.params.uid as string;
    const afterData = event.data?.after.data();
    const tryPassword = afterData?.password as string | null;

    // 삭제된 경우 무시
    if (!tryPassword || !event.data?.after.exists) {
      logger.info("try 경로 삭제됨 - 무시", { roomId, uid });
      return;
    }

    logger.info("onPasswordTry 트리거 실행", { roomId, uid });

    await handlePasswordVerification(roomId, uid, tryPassword);
  }
);
```

---

## 6. Svelte UI 구현

### 6.1 비밀번호 설정 UI (Owner용) - Firestore

**파일**: `src/lib/components/chat/room-password-setting.svelte`

**기능**:
- 비밀번호 입력 필드 (type="text"로 화면에 표시)
- 저장/취소/삭제 버튼
- Owner만 접근 가능

**UI/UX 특징**:
- ✅ 모달 창을 열면 바로 비밀번호 입력 필드 표시 (토글 없음)
- ✅ `type="text"`로 비밀번호를 화면에 표시
- ✅ 비밀번호 확인란 없음 (즉시 저장)
- ✅ 3개 버튼: "취소", "저장", "비밀번호 삭제"
- ✅ "비밀번호 삭제" 버튼은 기존 비밀번호가 있을 때만 표시

**UI 구조 (Firestore)**:
```svelte
<script lang="ts">
  import { Button } from '$lib/components/ui/button';
  import { Input } from '$lib/components/ui/input';
  import { toast } from 'svelte-sonner';
  import { db } from '$lib/firebase';
  import { doc, setDoc, updateDoc, deleteField } from 'firebase/firestore';
  import { m } from '$lib/paraglide/messages';

  interface Props {
    roomId: string;
    currentPassword?: string;
    onCancel?: () => void;
  }

  let { roomId, currentPassword = '', onCancel }: Props = $props();

  let password = $state(currentPassword);
  let isSaving = $state(false);

  // 비밀번호 저장 (Firestore)
  async function handleSave() {
    // 유효성 검사 (최소 4자)
    if (password.length < 4) {
      toast.error(m.chatPasswordMinLengthError());
      return;
    }

    isSaving = true;

    try {
      // 비밀번호 저장 (password-data subcollection)
      const passwordDocRef = doc(db!, `chats/${roomId}/password-data/password`);
      await setDoc(passwordDocRef, {
        password: password
      });

      // 활성화 플래그 저장 (chat room document)
      const roomDocRef = doc(db!, `chats/${roomId}`);
      await updateDoc(roomDocRef, {
        password: true
      });

      toast.success(m.chatPasswordSetSuccess());

      // 저장 성공 시 모달창 닫기
      if (onCancel) {
        onCancel();
      }
    } catch (error) {
      console.error('❌ 비밀번호 저장 실패:', error);
      toast.error(m.chatPasswordSaveFailure());
    } finally {
      isSaving = false;
    }
  }

  // 비밀번호 삭제 (Firestore)
  async function handleDelete() {
    isSaving = true;

    try {
      // 활성화 플래그 삭제 (password 필드만 제거)
      const roomDocRef = doc(db!, `chats/${roomId}`);
      await updateDoc(roomDocRef, {
        password: deleteField()
      });

      toast.success(m.chatPasswordDeleteSuccess());

      // 비밀번호 입력창 초기화
      password = '';

      // 삭제 성공 시 모달창 닫기
      if (onCancel) {
        onCancel();
      }
    } catch (error) {
      console.error('❌ 비밀번호 삭제 실패:', error);
      toast.error(m.chatPasswordSaveFailure());
    } finally {
      isSaving = false;
    }
  }
</script>

<div class="password-setting-container">
  <!-- 비밀번호 입력 필드 (바로 표시) -->
  <Input
    type="text"
    placeholder={m.chatPasswordInputPlaceholder()}
    bind:value={password}
    disabled={isSaving}
  />

  <!-- 버튼 그룹 -->
  <div class="button-group">
    <!-- 취소 버튼 (좌측) -->
    {#if onCancel}
      <Button variant="outline" onclick={onCancel} disabled={isSaving}>
        {m.commonCancel()}
      </Button>
    {/if}

    <!-- 우측 버튼 그룹 -->
    <div class="right-buttons">
      <!-- 비밀번호 삭제 버튼 (기존 비밀번호가 있을 때만) -->
      {#if currentPassword}
        <Button variant="destructive" onclick={handleDelete} disabled={isSaving}>
          {m.chatPasswordDelete()}
        </Button>
      {/if}

      <!-- 저장 버튼 (파란색) -->
      <Button onclick={handleSave} disabled={isSaving} class="bg-blue-600 hover:bg-blue-700 text-white">
        {isSaving ? m.chatPasswordSaving() : m.commonSave()}
      </Button>
    </div>
  </div>
</div>
```

**주요 변경사항**:
- `rtdb` → `db` (Firestore)
- `ref()`, `update()`, `remove()` → `doc()`, `setDoc()`, `updateDoc()`, `deleteField()`
- 경로 변경:
  - `chat-room-passwords/${roomId}` → `chats/${roomId}/password-data/password`
  - `chat-rooms/${roomId}` → `chats/${roomId}`

**Props**:
- `roomId`: 채팅방 ID
- `currentPassword`: 현재 설정된 비밀번호 (optional)
- `onCancel`: 취소 버튼 클릭 시 호출될 콜백 함수 (optional)

**주요 변경사항**:
- 비밀번호 활성화/비활성화 토글 제거 → 바로 입력 필드 표시
- `type="password"` → `type="text"` 변경 (비밀번호를 화면에 보이게)
- 비밀번호 확인란 제거 (유효성 검사 단순화)
- "취소", "저장", "비밀번호 삭제" 3개 버튼 제공
- `isPasswordEnabled` prop 제거

### 6.2 비밀번호 입력 모달 (입장자용) - Firestore

**파일**: `src/lib/components/chat/room-password-prompt.svelte`

**기능**:
- Dialog 형태 모달
- 비밀번호 입력 필드
- 5초 타임아웃
- onSnapshot()으로 members 실시간 확인
- 확인/취소 버튼

**UI/UX 세부 규칙**:
- 입력 필드에 공백(Space) 또는 Enter 키 입력 시 Dialog 전체가 닫히지 않도록 `keydown` 이벤트 전파를 차단하여 사용자가 안전하게 비밀번호를 입력/제출할 수 있어야 합니다.
- 버튼 배치는 왼쪽에 텍스트 스타일의 `Cancel`, 오른쪽에 기본 버튼 형태의 `Confirm`을 두고, Confirm 버튼만 강조합니다.

**UI 구조 (Firestore)**:
```svelte
<script lang="ts">
  import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '$lib/components/ui/dialog';
  import { Input } from '$lib/components/ui/input';
  import { Button } from '$lib/components/ui/button';
  import { toast } from 'svelte-sonner';
  import { db } from '$lib/firebase';
  import { doc, setDoc, onSnapshot } from 'firebase/firestore';
  import { authStore } from '$lib/stores/auth.svelte';
  import { invalidate } from '$app/navigation';
  import { m } from '$lib/paraglide/messages';

  interface Props {
    roomId: string;
    open: boolean;
    onSuccess: () => void;
    onCancel: () => void;
  }

  let { roomId, open = $bindable(), onSuccess, onCancel }: Props = $props();

  let password = $state('');
  let isVerifying = $state(false);
  let countdown = $state(5);

  /**
   * 입력 필드에서 공백 입력 시 Dialog가 닫히는 것을 방지
   */
  function handlePasswordKeyDown(event: KeyboardEvent) {
    if (
      event.code === 'Space' ||
      event.key === ' ' ||
      event.key === 'Spacebar' ||
      event.key === 'Enter'
    ) {
      event.stopPropagation();
    }
  }

  async function handleSubmit() {
    if (!password || !authStore.user?.uid) return;

    isVerifying = true;
    countdown = 5;

    try {
      // 1. try 경로에 비밀번호 저장 (Firestore)
      const tryDocRef = doc(db!, `chats/${roomId}/password-tries/${authStore.user.uid}`);
      await setDoc(tryDocRef, {
        password: password,
        timestamp: Date.now()
      });

      // 2. 5초 동안 매초 members 확인
      const verified = await waitForVerification(roomId, authStore.user.uid);

      if (verified) {
        toast.success(m.chatPasswordVerifySuccess());
        await invalidate('chat:room'); // SvelteKit 데이터 재로드
        onSuccess();
      } else {
        toast.error(m.chatPasswordIncorrect());
        password = '';
      }
    } catch (error) {
      console.error('❌ 비밀번호 검증 에러:', error);
      toast.error(m.chatPasswordVerifyFailure());
    } finally {
      isVerifying = false;
    }
  }

  /**
   * 비밀번호 검증 대기 함수 (Firestore onSnapshot)
   */
  async function waitForVerification(roomId: string, uid: string): Promise<boolean> {
    return new Promise((resolve) => {
      const memberDocRef = doc(db!, `chats/${roomId}/members/${uid}`);
      let intervalId: any;
      let timeoutId: any;
      let unsubscribe: (() => void) | null = null;

      // 매초 카운트다운
      intervalId = setInterval(() => {
        countdown--;
      }, 1000);

      // 5초 타임아웃
      timeoutId = setTimeout(() => {
        clearInterval(intervalId);
        if (unsubscribe) unsubscribe();
        resolve(false);
      }, 5000);

      // members 경로 실시간 확인 (Firestore onSnapshot)
      unsubscribe = onSnapshot(memberDocRef, (snapshot) => {
        if (snapshot.exists() && snapshot.data()?.member === true) {
          // 검증 성공: members에 추가됨
          clearInterval(intervalId);
          clearTimeout(timeoutId);
          if (unsubscribe) unsubscribe();
          resolve(true);
        }
      });
    });
  }

  function handleCancel() {
    if (isVerifying) return; // 검증 중에는 취소 불가
    password = '';
    onCancel();
  }
</script>

<Dialog bind:open>
  <DialogContent class="modal-content">
    <DialogHeader>
      <DialogTitle class="modal-title">
        {m.chatPasswordSettings()}
      </DialogTitle>
      <DialogDescription class="modal-description">
        {m.chatPasswordRequired()}
      </DialogDescription>
    </DialogHeader>

    <form onsubmit={(e) => { e.preventDefault(); handleSubmit(); }} class="modal-form">
      <!-- 비밀번호 입력 필드 -->
      <Input
        type="password"
        placeholder={m.chatPasswordEnterPrompt()}
        bind:value={password}
        disabled={isVerifying}
        class="password-input"
        onkeydown={handlePasswordKeyDown}
      />

      <!-- 검증 중 카운트다운 표시 -->
      {#if isVerifying}
        <p class="countdown-text">
          {m.chatPasswordVerifying({ countdown })}
        </p>
      {/if}

      <!-- 버튼 영역 -->
      <div class="button-group">
        <button
          type="button"
          class="cancel-text-button"
          onclick={handleCancel}
          disabled={isVerifying}
        >
          {m.commonCancel()}
        </button>
        <Button type="submit" disabled={isVerifying || !password} class="confirm-button">
          {m.commonConfirm()}
        </Button>
      </div>
    </form>
  </DialogContent>
</Dialog>
```

**주요 변경사항**:
- `rtdb` → `db` (Firestore)
- `ref()`, `set()`, `onValue()`, `off()` → `doc()`, `setDoc()`, `onSnapshot()`
- 경로 변경:
  - `chat-room-passwords/${roomId}/try/${uid}` → `chats/${roomId}/password-tries/${uid}`
  - `chat-rooms/${roomId}/members/${uid}` → `chats/${roomId}/members/${uid}`
- 멤버 확인 방식 변경:
  - `snapshot.val() === true` → `snapshot.exists() && snapshot.data()?.member === true`

### 6.3 채팅방 헤더 메뉴 수정

**파일**: `src/routes/chat/room/[roomId]/+page.svelte` (또는 헤더 컴포넌트)

**수정 내용**:
```svelte
<script lang="ts">
  import RoomPasswordSetting from '$lib/components/chat/room-password-setting.svelte';

  let showPasswordSetting = $state(false);

  // 채팅방 데이터 (기존)
  let room = $state<ChatRoom | null>(null);
  let isOwner = $derived(room?.owner === authStore.user?.uid);
</script>

<!-- 드롭다운 메뉴 -->
<DropdownMenu>
  <DropdownMenuContent>
    {#if isOwner}
      <DropdownMenuItem onclick={() => showPasswordSetting = true}>
        <Lock class="w-4 h-4 mr-2" />
        비밀번호 설정
      </DropdownMenuItem>
    {/if}
    <!-- 기타 메뉴 항목... -->
  </DropdownMenuContent>
</DropdownMenu>

<!-- 비밀번호 설정 Dialog -->
{#if showPasswordSetting}
  <Dialog open={showPasswordSetting} onOpenChange={(open) => showPasswordSetting = open}>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>비밀번호 설정</DialogTitle>
      </DialogHeader>
      <RoomPasswordSetting
        roomId={room.id}
        currentPassword={room.password}
        isPasswordEnabled={room.passwordEnabled}
      />
    </DialogContent>
  </Dialog>
{/if}
```

### 6.4 채팅방 입장 로직 수정

**파일**: `src/routes/chat/room/[roomId]/+page.ts` (또는 `+page.svelte`)

**수정 내용**:
```typescript
import type { PageLoad } from './$types';
import { get, ref } from 'firebase/database';
import { rtdb } from '$lib/firebase';
import { authStore } from '$lib/stores/auth.svelte';

export const load: PageLoad = async ({ params }) => {
  const roomId = params.roomId;
  const uid = authStore.user?.uid;

  if (!uid) {
    throw new Error('로그인이 필요합니다');
  }

  // 1. 채팅방 정보 읽기 시도
  const roomRef = ref(rtdb, `chat-rooms/${roomId}`);

  try {
    const roomSnapshot = await get(roomRef);

    if (!roomSnapshot.exists()) {
      throw new Error('채팅방을 찾을 수 없습니다');
    }

    const room = roomSnapshot.val();

    // 2. 이미 members인지 확인
    const isMember = room.members?.[uid] === true;

    // 3. 비밀번호 필요 여부 확인
    const needsPassword = room.password === true && !isMember;

    return {
      room,
      needsPassword
    };

  } catch (error) {
    // Security Rules에 의해 읽기 차단된 경우
    if (error.code === 'PERMISSION_DENIED') {
      // 비밀번호 필요
      return {
        room: null,
        needsPassword: true
      };
    }

    throw error;
  }
};
```

**+page.svelte 수정**:
```svelte
<script lang="ts">
  import RoomPasswordPrompt from '$lib/components/chat/room-password-prompt.svelte';
  import type { PageData } from './$types';

  let { data }: { data: PageData } = $props();

  let showPasswordPrompt = $state(data.needsPassword);

  function handlePasswordSuccess() {
    showPasswordPrompt = false;
    // invalidate는 room-password-prompt.svelte에서 이미 호출됨
  }

  function handlePasswordCancel() {
    goto('/chat/list');
  }
</script>

{#if showPasswordPrompt}
  <RoomPasswordPrompt
    roomId={data.room?.id || ''}
    open={showPasswordPrompt}
    onSuccess={handlePasswordSuccess}
    onCancel={handlePasswordCancel}
  />
{:else}
  <!-- 채팅방 UI -->
{/if}
```

---

## 7. 다국어 처리

### 7.1 메시지 키 추가

**파일**: `messages/ko.json`, `messages/en.json`, etc.

```json
{
  "채팅방_비밀번호_설정": "채팅방 비밀번호 설정",
  "비밀번호_활성화": "비밀번호 활성화",
  "비밀번호_입력": "비밀번호를 입력하세요",
  "비밀번호_확인": "비밀번호 확인",
  "비밀번호_최소_길이": "비밀번호는 최소 {min}자 이상이어야 합니다",
  "비밀번호_불일치": "비밀번호가 일치하지 않습니다",
  "비밀번호_저장_성공": "비밀번호가 설정되었습니다",
  "비밀번호_해제_성공": "비밀번호가 해제되었습니다",
  "비밀번호_검증_성공": "비밀번호가 확인되었습니다",
  "비밀번호_검증_실패": "비밀번호가 올바르지 않습니다",
  "비밀번호_입력_필요": "이 채팅방은 비밀번호가 필요합니다",
  "비밀번호_검증_중": "검증 중... ({countdown}초 남음)",
  "저장": "저장",
  "저장_중": "저장 중...",
  "확인": "확인",
  "취소": "취소"
}
```

---

## 8. 테스트 시나리오

### 8.1 Owner 테스트
- [ ] ✅ 비밀번호 설정 (4자 이상)
- [ ] ✅ 비밀번호 확인 불일치 시 에러
- [ ] ✅ 비밀번호 저장 성공
- [ ] ✅ `/chat-rooms/{roomId}/password: true` 확인
- [ ] ✅ `/chat-room-passwords/{roomId}/password` 확인
- [ ] ✅ 비밀번호 변경
- [ ] ✅ 비밀번호 비활성화
- [ ] ✅ Owner 본인은 비밀번호 없이 입장 가능

### 8.2 입장자 테스트
- [ ] ✅ 비밀번호 없는 채팅방 입장 (정상)
- [ ] ✅ 비밀번호 있는 채팅방 입장 → 모달 표시
- [ ] ✅ 올바른 비밀번호 입력 → 입장 성공
- [ ] ✅ 잘못된 비밀번호 입력 → 에러 메시지
- [ ] ✅ 입장 후 나갔다가 재입장 → 비밀번호 불필요
- [ ] ✅ 비밀번호 변경 후 기존 members 재입장 → 비밀번호 불필요

### 8.3 Security Rules 테스트
- [ ] ✅ 비밀번호 설정 후 직접 members 추가 시도 → 차단 확인
- [ ] ✅ Owner가 아닌 사용자가 password 플래그 변경 시도 → 차단 확인
- [ ] ✅ 본인이 아닌 try 경로 쓰기 시도 → 차단 확인
- [ ] ✅ members 삭제 (나가기) → 정상 동작

### 8.4 Cloud Functions 테스트
- [ ] ✅ 올바른 비밀번호 → members 추가 확인
- [ ] ✅ 잘못된 비밀번호 → members 추가 안 됨
- [ ] ✅ try 경로 자동 삭제 확인
- [ ] ✅ Cloud Functions 로그 확인

### 8.5 UI/UX 테스트
- [ ] ✅ 10초 카운트다운 동작 확인
- [ ] ✅ 검증 중 UI 로딩 표시
- [ ] ✅ 성공 시 Toast 메시지
- [ ] ✅ 실패 시 Toast 메시지 + 재시도
- [ ] ✅ 취소 버튼 → 채팅방 목록으로 이동

---

## 9. 보안 고려사항

### 9.1 Plain Text 비밀번호
- **현재**: Plain Text로 저장
- **이유**: 간단한 구현, 비밀번호 변경 시 기존 members 재검증 불필요
- **위험**: Firebase Console 접근 시 비밀번호 노출
- **완화**:
  - `/chat-room-passwords` 경로는 Owner만 읽기 가능 (Security Rules)
  - 프로덕션 환경에서 Firebase Console 접근 제한

### 9.2 Brute Force 방지
- **현재**: 제한 없음
- **향후**: Rate Limiting 추가 (5분 내 5회 실패 시 10분 차단)

### 9.3 Try 경로 보안
- **현재**: 본인만 쓰기 가능
- **보안**: Cloud Functions에서 즉시 삭제 (검증 후)

---

## 10. 향후 개선사항

### 10.1 비밀번호 암호화
- bcrypt 해싱 도입
- 비밀번호 변경 시 기존 members 재검증 필요

### 10.2 Rate Limiting
- 연속 실패 횟수 추적
- IP 기반 차단 (옵션)

### 10.3 비밀번호 힌트
- 비밀번호 힌트 설정 기능
- 모달에 힌트 표시

### 10.4 QR 코드 공유
- 비밀번호 포함 QR 코드 생성
- 초대 링크 생성

---

## 11. 참조

- [Firebase Security Rules 공식 문서](https://firebase.google.com/docs/database/security)
- [Firebase Cloud Functions 공식 문서](https://firebase.google.com/docs/functions)
- [Svelte 5 Runes 공식 문서](https://svelte-5-preview.vercel.app/docs/runes)

---

## 변경 이력

| 버전 | 날짜 | 변경 내용 | 작성자 |
|------|------|----------|--------|
| 2.0.0 | 2025-11-15 | **Firestore 마이그레이션 완료**<br>- Database 구조 변경: RTDB → Firestore 서브컬렉션<br>  - `chats/{roomId}/members/{uid}`: 멤버 관리<br>  - `chats/{roomId}/password-data/password`: 비밀번호 저장<br>  - `chats/{roomId}/password-tries/{uid}`: 비밀번호 시도<br>- Security Rules 완전 재작성: RTDB JSON → Firestore rules_version 2<br>- Cloud Functions: `onValueWritten` → `onDocumentWritten`<br>- UI 컴포넌트: RTDB API → Firestore API<br>  - `ref()`, `set()`, `update()`, `remove()` → `doc()`, `setDoc()`, `updateDoc()`, `deleteField()`<br>  - `onValue()` polling → `onSnapshot()` 실시간 리스너<br>- 타임아웃 단축: 10초 → 5초<br>- 멤버 확인 방식: `snapshot.val() === true` → `snapshot.exists() && snapshot.data()?.member === true` | Claude Code |
| 1.1.0 | 2025-11-15 | 비밀번호 설정 UI 개선: 토글 제거, type="text" 사용, 버튼 3개 (취소/저장/삭제) | Claude Code |
| 1.0.0 | 2025-11-14 | 초기 버전 작성 (RTDB 기반) | Claude Code |
