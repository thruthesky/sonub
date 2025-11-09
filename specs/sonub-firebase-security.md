---
name: sonub-firebase-security
version: 1.1.0
description: Firebase RTDB 및 Storage의 보안 규칙 정의 - 사용자 데이터 보호 및 관리자 권한 관리
author: Claude
email: noreply@anthropic.com
step: 35
priority: '**'
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database.md
tags:
  - security
  - firebase
  - rules
  - rtdb
  - authorization
---

## 📋 개요

Sonub의 보안 규칙은 다음 원칙을 따릅니다:
- **최소 권한 원칙**: 필요한 최소한의 권한만 부여
- **RTDB 기반 권한**: `/system/settings/admins` 배열에 관리자 UID 저장
- **클라이언트 읽기**: 메뉴에서 표시 여부만 클라이언트에서 판단
- **서버 검증**: 실제 데이터 수정은 Security Rules로 검증

---

## 1️⃣ 관리자 권한 관리 시스템

### RTDB 구조

```json
{
  "system": {
    "settings": {
      "admins": {
        "uid-user1": true,
        "uid-user2": true,
        "uid-user3": true
      }
    }
  }
}
```

**특징:**
- 객체 형식으로 관리자 UID 저장 (key: uid, value: true)
- 모든 로그인 사용자가 읽기 가능
- 관리자만 쓰기 가능
- 변경이 즉시 반영됨 (재로그인 불필요)
- Security Rules에서 쉽게 체크 가능

### 클라이언트 로직

메뉴 페이지에서 관리자 권한 확인:

```typescript
// src/lib/stores/auth.svelte.ts
let adminList: string[] = $state([]);

// 로그인 후 관리자 목록 조회
onAuthStateChanged(auth, async (user) => {
  if (user) {
    const adminSnapshot = await database.ref('system/settings/admins').once('value');
    adminList = adminSnapshot.val() ?? [];
  }
});

// 계산 속성
export const isAdmin = $derived(
  authStore.isAuthenticated && adminList.includes(authStore.user?.uid ?? '')
);
```

메뉴 페이지에서 사용:

```svelte
{#if authStore.isAdmin}
  <Button onclick={goToAdmin}>관리자 페이지</Button>
{/if}
```

---

## Firebase Realtime Database 보안 규칙

사용자의 프로필 데이터는 다음과 같이 보호됩니다:

```json
{
  "rules": {
    "users": {
        "$uid": {
          // 자신만 읽기 가능. 모든 사용자가 읽기 불가능
        ".read": "auth.uid == $uid",
          // 2025-12-12 까지는 무조건 쓰기 통과 (테스트 데이터 생성용)
          // 그 이후는 본인만 쓰기 가능
          ".write": "now < 1765555200000 || auth.uid == $uid",
          // 필수 필드 검증
          ".validate": "newData.hasChildren(['displayName'])"
        },
        ".indexOn": ["createdAt"]
      },
      "system": {
        "settings": {
          "admins": {
            // 로그인한 모든 사용자가 읽기 가능 (메뉴에서 사용)
            ".read": "auth != null",
            // 관리자만 쓰기 가능 (배열에 있는 사용자만)
            ".write": "root.child('system/settings/admins').val().contains(auth.uid)"
          }
        }
      },
    "test": {
        "data": {
          // QA 전용 테스트 데이터 노드 - 누구나 읽고 쓰기 가능
          ".read": true,
          ".write": true
       }
    }
  }
}
```

**설명:**
- `users/$uid`: 사용자 프로필 (모두 읽기, 2025-12-12까지 무조건 쓰기, 이후 본인만 쓰기)
  - `now < 1765555200000`: 2025-12-12 자정(UTC) 이전에는 모든 사용자 쓰기 허용 (테스트 데이터 생성용)
  - `auth.uid == $uid`: 2025-12-12 이후에는 본인 데이터만 쓰기 가능
- `system/settings/admins`: 관리자 객체 (key: UID, value: true, 로그인 사용자는 읽기, 관리자만 쓰기)
- `test/data`: QA 테스트 전용 경로. DatabaseListView 데모가 자유롭게 데이터를 생성/삭제할 수 있도록 `.read`와
  `.write`를 모두 `true`로 설정한다. 이 노드는 **프로덕션 데이터와 분리된 테스트 공간**이므로 민감한 정보를 저장하지 않는다.


## Firebase Storage 보안 규칙

프로필 사진 저장소의 보안 규칙:

```
rules_version = '2';
service firebase.storage {
  match /b/{bucket}/o {
    // /users/{userId}/profile 경로의 파일
    match /users/{userId}/profile/{fileName=**} {
      allow read: if true;  // 모든 사용자가 읽기 가능
      allow write: if request.auth.uid == userId;  // 본인만 쓰기 가능
      allow delete: if request.auth.uid == userId;  // 본인만 삭제 가능
    }
  }
}
```

