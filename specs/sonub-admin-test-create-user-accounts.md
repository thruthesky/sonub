---
name: sonub-admin-test-create-user-accounts
version: 2.0.0
description: 관리자 사용자 목록 페이지에서 테스트 사용자 생성·목록·삭제를 통합 관리하는 SED 명세
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
step: 65
priority: '**'
dependencies:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
  - sonub-design-layout.md
tags:
  - admin
  - test-user
  - firebase
  - rtdb
  - svelte5
---

# Sonub Admin Test User Management (2025-11 업데이트)

## 1. 개요

- 테스트 사용자 생성 페이지(`/admin/test/create-users`)를 **사용자 목록 페이지(`/admin/users`)**와 통합한다.
- 앞으로 관리자 페이지에서는 `/admin/users` 한 곳에서 임시 사용자 **생성 → 조회 → 삭제**를 모두 수행한다.
- 본 명세서는 통합 UI/UX 요구사항과 Firebase 연동 로직을 정의한다.

## 2. 기능 요구사항

1. **페이지 경로**
   - `/admin/users`
   - 상단 관리자 탭(대시보드/테스트/사용자목록/신고목록)은 `/admin/+layout.svelte`에서 제공
2. **섹션 구성**
   1. 제목 & 설명
   2. 통계 카드 (테스트 사용자 수, 상태)
   3. “테스트 사용자 생성” 카드
      - 100명 일괄 생성 버튼
      - 진행률(숫자 + progress bar)
      - 완료/에러 메시지
      - 생성되는 사용자 스펙 안내
   4. 삭제 진행 상태 (일괄 삭제 시)
   5. 사용자 목록 카드 리스트 (개별 삭제 버튼)
   6. 정보 카드 (주의사항)
3. **생성 로직**
   - `generateTestUsers()`로 100명 데이터 생성
   - `saveTestUsersToFirebase()` 호출 시 `onProgress(index,total)` 콜백으로 진행률 업데이트
   - 완료 후 `loadUsers()` 재호출
   - 상태 변수:
     - `isCreating`, `isCreationCompleted`, `creationError`
     - `creationProgress`, `creationTotal`, `creationPercentage`
4. **삭제 로직**
   - 기존 `deleteUserByUid`, `deleteAllTemporaryUsers` 사용
   - 일괄 삭제 진행률 UI는 기존과 동일
5. **빈 상태 메시지**
   - 사용자 수 0일 때 “테스트 사용자 생성” 버튼으로 안내 (별도 페이지 링크 제거)
6. **권한**
   - 현재는 별도 권한 검증 없음 (향후 확장 대비)

## 3. UI 상세

### 3.1 생성 카드
- 버튼 텍스트:
  - 기본: `🚀 테스트 사용자 생성`
  - 생성 중: `⏳ 생성 중...`
  - 완료: `✓ 생성 완료`
- 진행률 바는 `%` 표시, 0~100%
- 생성 정보 그리드:
  - “한 번에 생성되는 수” → 100
  - “현재 생성된 수” → 진행 값
- 에러 발생 시 카드 내부에 붉은 경고 블록 표시

### 3.2 사용자 목록
- 기존 UI 유지 (Card 리스트, 성별/생년/생성일 등)
- 개별 삭제, 일괄 삭제 버튼을 페이지 내에서 제공
- `isTemporary: true` 사용자만 노출

## 4. 데이터 흐름

```
Button 클릭
  └─ generateTestUsers()  // 100명 가짜 데이터
      └─ saveTestUsersToFirebase(users, onProgress)
          └─ Firebase RTDB /users/{uid}
              └─ 생성 완료 후 loadUsers() 재호출
```

- RTDB 저장 구조는 기존 `/users/{uid}` 스키마를 재사용 (displayName, email, gender, birthYear 등)
- `isTemporary: true` 플래그로 구분

## 5. 파일 구조

| 파일 | 설명 |
| --- | --- |
| `src/routes/admin/users/+page.svelte` | 사용자 목록 + 생성/삭제 UI 통합 페이지 |
| `src/lib/utils/test-user-generator.ts` | 테스트 사용자 100명 데이터 생성기 |
| `src/lib/utils/admin-service.ts` | Firebase 저장/삭제 유틸리티 |

> `src/routes/admin/test/create-users/+page.svelte` 파일은 삭제한다.

### 5.1 `src/lib/utils/test-user-generator.ts` 상세

```ts
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
```

#### generateTestUsers()
- 반환값: `TestUser[]` (100명)
- 로직:
  1. `const now = Date.now();`
  2. `i = 1..100` 루프
  3. `paddedNumber = i.toString().padStart(3, '0')`
  4. `uid = generateTestUserId(i)` (아래 함수 참조)
  5. `timestamp = now + i * 1000` → 초 단위 오프셋으로 중복 방지
  6. 날짜 포맷 → `MM-DD HH:MM` (월/일/시/분)
  7. `displayName = "테스트 사용자 ${paddedNumber} (${MM-DD HH:MM})"`
  8. 이메일: `test.user.${paddedNumber}@example.com`
  9. 성별: `['male','female','other']` 중 랜덤
 10. 생년: `generateRandomBirthYear()` (1950~2010 랜덤)
 11. `createdAt = timestamp`, `updatedAt = timestamp`, `isTemporary = true`
 12. 결과 배열에 push

#### generateTestUserId(index: number)
```ts
const timestamp = Date.now();
const randomString = Math.random().toString(36).substring(2, 8);
return `test_${timestamp}_${index}_${randomString}`;
```
- Firebase key 유사한 고유 ID 생성

#### generateRandomBirthYear()
```ts
const minYear = 1950;
const maxYear = 2010;
return minYear + Math.floor(Math.random() * (maxYear - minYear + 1));
```

#### testUserToFirebaseData(user: TestUser)
```ts
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
```
- `saveTestUsersToFirebase()`에서 호출하여 RTDB에 저장하기 위한 평면 객체로 변환한다.

### 5.2 `src/routes/admin/users/+page.svelte` 구조

```svelte
<script lang="ts">
  import { Card } from '$lib/components/ui/card';
  import { Button } from '$lib/components/ui/button';
  import { Alert } from '$lib/components/ui/alert';
  import {
    getTemporaryUsers,
    deleteUserByUid,
    deleteAllTemporaryUsers,
    getTemporaryUserCount,
    saveTestUsersToFirebase
  } from '$lib/utils/admin-service';
  import { generateTestUsers, type TestUser } from '$lib/utils/test-user-generator';

  let users = $state<Record<string, TestUser>>({});
  let isLoading = $state(true);
  let error: string | null = $state(null);
  let isDeleting = $state(false);
  let deleteProgress = $state(0);
  let deleteTotal = $state(0);
  let isCreating = $state(false);
  let isCreationCompleted = $state(false);
  let creationError: string | null = $state(null);
  let creationProgress = $state(0);
  let creationTotal = $state(0);
</script>
```

#### 데이터 로딩
```ts
async function loadUsers() {
  isLoading = true;
  error = null;
  try {
    users = await getTemporaryUsers();
  } catch (err) {
    error = err instanceof Error ? err.message : '알 수 없는 오류가 발생했습니다.';
  } finally {
    isLoading = false;
  }
}
onMount(loadUsers);
```

#### 테스트 사용자 생성
```ts
async function handleCreateUsers() {
  if (isCreating) return;

  isCreating = true;
  isCreationCompleted = false;
  creationError = null;
  creationProgress = 0;

  try {
    const testUsers = generateTestUsers();  // 100명 + 닉네임/타임스탬프 규칙
    creationTotal = testUsers.length;
    await saveTestUsersToFirebase(testUsers, (index, total) => {
      creationProgress = index;
      creationTotal = total;
    });
    isCreationCompleted = true;
    await loadUsers();
  } catch (err) {
    creationError = err instanceof Error ? err.message : '알 수 없는 오류가 발생했습니다.';
  } finally {
    isCreating = false;
  }
}
```

#### UI 레이아웃 핵심
```svelte
<div class="space-y-6">
  <!-- 통계 카드 -->
  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <Card>... 사용자 수 ...</Card>
    <Card>... 상태 메시지 ...</Card>
  </div>

  <!-- 테스트 사용자 생성 카드 -->
  <Card>
    <div class="space-y-6 p-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">테스트 사용자 생성</h2>
          <p class="text-sm text-gray-600">
            버튼을 클릭하면 테스트용 임시 사용자 100명이 순차적으로 생성됩니다.
          </p>
        </div>
        <Button onclick={handleCreateUsers} disabled={isCreating} size="lg" class="min-w-48 bg-blue-600 text-white">
          {#if isCreating}
            ⏳ 생성 중...
          {:else if isCreationCompleted}
            ✓ 생성 완료
          {:else}
            🚀 테스트 사용자 생성
          {/if}
        </Button>
      </div>

      {#if isCreating || creationProgress > 0}
        <div class="space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-gray-700">진행 상황</span>
            <span class="font-semibold text-gray-900">
              {creationProgress} / {creationTotal} ({creationPercentage}%)
            </span>
          </div>
          <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
            <div class="h-full bg-blue-500 transition-all duration-300" style="width: {creationPercentage}%"></div>
          </div>
        </div>
      {/if}

      {#if isCreationCompleted}
        <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800">
          <strong>✓ 완료:</strong> {creationProgress}명의 테스트 사용자가 생성되었습니다.
        </div>
      {/if}

      {#if creationError}
        <div class="rounded-lg bg-red-50 p-4 text-sm text-red-800">
          <strong>✗ 오류:</strong> {creationError}
        </div>
      {/if}
    </div>
  </Card>

  <!-- 사용자 목록 카드 -->
  {#if userCount === 0}
    <Card>생성된 테스트 사용자가 없습니다...</Card>
  {:else}
    <div class="space-y-4">
      {#each userList as [uid, user] (uid)}
        <Card>
          <div class="p-6 flex items-start justify-between">
            <!-- displayName/email/성별/생년도/생성일 -->
            <Button variant="destructive" onclick={() => handleDeleteUser(uid)}>삭제</Button>
          </div>
        </Card>
      {/each}
    </div>
  {/if}
</div>
```

### 5.3 스타일 요약
- Tailwind 유틸리티 클래스 기반 (`flex`, `grid`, `bg-gray-50`, `text-gray-600` 등)
- 진행률 바: `h-3 rounded-full bg-gray-200` + 내부 `div`에 `style="width: {percentage}%"`.
- 상태 메시지 박스: `bg-green-50`, `bg-red-50` 등을 사용하여 완료/에러를 명확히 표현.

## 6. QA 체크리스트

- [ ] `/admin/users` 접속 시 “테스트 사용자 생성” 카드가 표시된다.
- [ ] 버튼 클릭 → 진행률 및 완료 메시지가 표시되고 목록이 자동 갱신된다.
- [ ] 사용자 수 0일 때 별도의 페이지 이동 안내 없이 생성 카드로 유도한다.
- [ ] 일괄 삭제 기능이 기존과 동일하게 동작한다.
- [ ] 관리자 대시보드/테스트 메뉴의 링크가 모두 `/admin/users`를 가리킨다.

## 7. 작업 이력 (SED Log)

| 날짜 | 작업자 | 내용 |
| ---- | ------ | ---- |
| 2025-11-09 | Codex Agent | `/admin/users` 페이지에 테스트 사용자 생성 UI를 통합하고 `/admin/test/create-users` 경로를 제거. 관리자 대시보드/테스트 페이지, 관련 메뉴 및 문서를 모두 `/admin/users`로 업데이트. |
