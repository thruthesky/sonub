---
name: sonub-firebase-security
version: 1.3.0
description: Firebase RTDB 및 Storage의 보안 규칙 정의 - 사용자 데이터 보호 및 관리자 권한 관리, 채팅방 필드별 세밀한 보안 규칙, 초기화 및 배포 방법
author: Claude
email: noreply@anthropic.com
step: 35
priority: '**'
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
tags:
  - security
  - firebase
  - rules
  - rtdb
  - authorization
  - deployment
  - chat-rooms
  - field-level-security
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

## 2️⃣ Firebase Realtime Database 보안 규칙

### 초기화 및 설정

Firebase Realtime Database 보안 규칙을 설정하려면 다음 단계를 따릅니다:

```bash
# Firebase 프로젝트 폴더로 이동
cd firebase

# Firebase Realtime Database 초기화
firebase init database
```

초기화가 완료되면 `firebase/database.rules.json` 파일이 생성됩니다.

### 보안 규칙 정의

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

### 보안 규칙 배포

`firebase/database.rules.json` 파일을 수정한 후, 다음 명령어로 Firebase Realtime Database에 보안 규칙을 배포합니다:

```bash
# firebase 폴더에서 실행
cd firebase

# Realtime Database 보안 규칙만 배포
firebase deploy --only database
```

**주의사항:**
- 보안 규칙 배포 전에 반드시 JSON 문법을 확인하세요.
- 배포 후 Firebase Console에서 규칙이 올바르게 적용되었는지 확인하세요.
- 규칙 변경은 즉시 적용되므로 프로덕션 환경에서는 신중하게 배포하세요.

---

## 2️⃣-2 채팅방(chat-rooms) 보안 규칙

### 설계 원칙

채팅방 보안 규칙은 **가독성(readability) 향상**을 위해 각 필드별로 세밀하게 설정합니다:
- 복잡한 단일 규칙보다 필드별 명확한 규칙 작성
- 각 필드의 생명주기(생성, 수정, 삭제) 명확히 정의
- `.write`와 `.validate`를 분리하여 권한과 데이터 검증 구분

### 필드별 보안 규칙

```json
{
  "rules": {
    "chat-rooms": {
      ".read": true,
      "$roomId": {
        ".write": "auth != null",
        "owner": {
          ".write": "!root.child('chat-rooms').child($roomId).exists() && newData.val() === auth.uid",
          ".validate": "newData.isString()"
        },
        "name": {
          ".write": "!root.child('chat-rooms').child($roomId).exists() || root.child('chat-rooms').child($roomId).child('owner').val() === auth.uid",
          ".validate": "newData.isString() && newData.val().length > 0 && newData.val().length <= 50"
        },
        "description": {
          ".write": "!root.child('chat-rooms').child($roomId).exists() || root.child('chat-rooms').child($roomId).child('owner').val() === auth.uid",
          ".validate": "newData.isString() && newData.val().length <= 200"
        },
        "type": {
          ".write": "!data.exists()",
          ".validate": "newData.val() === 'group' || newData.val() === 'open' || newData.val() === 'single'"
        },
        "createdAt": {
          ".write": "!data.exists()",
          ".validate": "newData.isNumber()"
        },
        "open": {
          ".write": "!data.exists()",
          ".validate": "newData.isBoolean()"
        },
        "groupListOrder": {
          ".write": "!data.exists()",
          ".validate": "newData.isNumber()"
        },
        "openListOrder": {
          ".write": "!data.exists()",
          ".validate": "newData.isNumber()"
        },
        "memberCount": {
          ".write": false,
          ".validate": "newData.isNumber() && newData.val() >= 0"
        },
        "members": {
          "$uid": {
            ".write": "auth != null && ($uid === auth.uid || root.child('chat-rooms').child($roomId).child('owner').val() === auth.uid)",
            ".validate": "newData.isBoolean() || newData.val() === null"
          }
        },
        "$other": {
          ".validate": false
        }
      },
      ".indexOn": ["openListOrder"]
    }
  }
}
```

### 필드별 규칙 설명

| 필드 | 쓰기 권한 | 검증 규칙 | 설명 |
|------|----------|----------|------|
| **owner** | Cloud Functions만 | 문자열 | 채팅방 소유자 UID. **Cloud Functions에서만 설정 가능** (`.write: false`) |
| **createdAt** | Cloud Functions만 | 숫자(타임스탬프) | 생성 시간. **Cloud Functions에서만 설정 가능** (`.write: false`) |
| **_requestingUid** | 생성 시 본인만 | 문자열 | **임시 필드**. 클라이언트가 `auth.uid`와 동일한 값으로 전달하면, Cloud Functions가 검증 후 `owner`로 복사하고 삭제 |
| **name** | owner만 | 1-50자 문자열 | 채팅방 이름. owner만 수정 가능 |
| **description** | owner만 | 최대 200자 문자열 | 채팅방 설명. owner만 수정 가능 |
| **type** | 생성 시만 | 'group', 'open', 'single' | 채팅방 타입. 생성 후 변경 불가 |
| **open** | 생성 시만 | boolean | 공개 여부. 생성 후 변경 불가 |
| **groupListOrder** | 생성 시만 | 숫자 | 그룹 채팅 정렬 순서. 생성 후 변경 불가 |
| **openListOrder** | 생성 시만 | 숫자 | 오픈 채팅 정렬 순서. 생성 후 변경 불가 |
| **memberCount** | Cloud Functions만 | 0 이상의 숫자 | 멤버 수. 서버에서 자동 관리 |
| **members/$uid** | 본인 또는 owner | boolean / null | 방 참여 여부와 알림 설정. 사용자는 스스로 true/false 설정, owner는 구성원 관리 가능, null은 퇴장 처리 |
| **$other** | 허용 안 함 | - | 정의되지 않은 필드는 생성 불가 |

### 보안 규칙 패턴

#### 1. 불변 필드 (Immutable Field)
```json
"owner": {
  ".write": "!data.exists() && newData.val() === auth.uid",
  ".validate": "newData.isString()"
}
```
- `!data.exists()`: 데이터가 없을 때만 (= 생성 시에만)
- `newData.val() === auth.uid`: 값이 현재 사용자 UID와 일치해야 함

#### 2. Owner 전용 수정 필드
```json
"name": {
  ".write": "root.child('chat-rooms').child($roomId).child('owner').val() === auth.uid",
  ".validate": "newData.isString() && newData.val().length > 0 && newData.val().length <= 50"
}
```
- owner 값을 확인하여 소유자만 수정 가능
- 데이터 타입과 길이 검증

#### 3. 읽기 전용 필드 (생성 후)
```json
"type": {
  ".write": "!data.exists()",
  ".validate": "newData.val() === 'group' || newData.val() === 'open' || newData.val() === 'single'"
}
```
- 생성 시에만 설정 가능
- 허용된 값만 설정 가능

#### 4. 정의되지 않은 필드 차단
```json
"$other": {
  ".validate": false
}
```
- 정의되지 않은 필드는 생성/수정 불가
- 데이터베이스 스키마 보호

---


## 3️⃣ Firebase Storage 보안 규칙

### 초기화 및 설정

Firebase Storage 보안 규칙을 설정하려면 다음 단계를 따릅니다:

```bash
# Firebase 프로젝트 폴더로 이동
cd firebase

# Firebase Storage 초기화
firebase init storage
```

초기화가 완료되면 `firebase/storage.rules` 파일이 생성됩니다.

### 보안 규칙 정의

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

### 보안 규칙 배포

`firebase/storage.rules` 파일을 수정한 후, 다음 명령어로 Firebase Storage에 보안 규칙을 배포합니다:

```bash
# firebase 폴더에서 실행
cd firebase

# Storage 보안 규칙만 배포
firebase deploy --only storage
```

**주의사항:**
- 보안 규칙 배포 전에 반드시 문법을 확인하세요.
- 배포 후 Firebase Console에서 규칙이 올바르게 적용되었는지 확인하세요.
- 규칙 변경은 즉시 적용되므로 프로덕션 환경에서는 신중하게 배포하세요.

