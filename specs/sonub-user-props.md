---
name: sonub
version: 1.0.0
description: 사용자 속성 분리 및 대량 조회 최적화 명세서
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: GPL-3.0
step: 50
priority: "*"
dependencies:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
tags:
  - user-props
  - database-optimization
  - firebase-realtime
  - performance
---

# 사용자 속성 분리 (user-props)

## 개요

사용자 노드 `/users/<uid>/` 에 있는 각 개별 속성은 `user-props` 노드에 별도로 저장됩니다.
이는 **특정 속성에 대한 대량 조회를 효율적으로 수행**하기 위한 데이터베이스 최적화 전략입니다.

### 필요성

- **효율성**: 회원 목록을 조회할 때 `/users/` 에서 모든 사용자와 그들의 전체 정보를 가져오는 것은 비효율적입니다
- **대량 쿼리**: `/user-props/displayName` 경로에서만 모든 사용자의 이름을 빠르게 조회할 수 있습니다
- **네트워크 최적화**: 필요한 필드만 선택적으로 조회하여 데이터 전송량 최소화

### 예시

- **경우 1**: 회원 목록 페이지에서 모든 사용자의 이름만 필요
  - ❌ 비효율: `/users/` 조회 → 각 사용자의 모든 정보(이메일, 성별, 생년월일 등) 전송
  - ✅ 효율: `/user-props/displayName` 조회 → 필요한 이름만 전송

- **경우 2**: 최신 가입 회원 확인
  - ❌ 비효율: `/users/` 에서 모든 사용자 정보 가져온 후 `createdAt`로 정렬
  - ✅ 효율: `/user-props/createdAt` 에서 `orderByValue()` 로 직접 정렬 조회

## 데이터 구조

```
/user-props/
  /displayName/
    ├── <uid1>: "사용자1"
    ├── <uid2>: "사용자2"
    └── <uid3>: "사용자3"
  /photoUrl/
    ├── <uid1>: "https://firebasestorage.googleapis.com/..."
    ├── <uid2>: "https://firebasestorage.googleapis.com/..."
    └── <uid3>: null
  /createdAt/
    ├── <uid1>: 1698473000000
    ├── <uid2>: 1698473100000
    └── <uid3>: 1698473200000
  /updatedAt/
    ├── <uid1>: 1698474000000
    ├── <uid2>: 1698474100000
    └── <uid3>: 1698474200000
  /gender/
    ├── <uid1>: "M"
    ├── <uid2>: "F"
    └── <uid3>: "M"
  /birthYear/
    ├── <uid1>: 1990
    ├── <uid2>: 1985
    └── <uid3>: 2000
```

## 분리 가능한 속성

다음 속성들은 `/user-props/` 에서 분리하여 관리할 수 있습니다:

- `displayName`: 사용자 이름
- `photoUrl`: 프로필 사진 URL
- `createdAt`: 계정 생성일
- `updatedAt`: 프로필 수정일
- `gender`: 성별
- `birthYear`: 생년
- `birthMonth`: 생월
- `birthDay`: 생일
- `bio`: 자기소개

## 분리하면 안 되는 속성

다음 속성들은 민감한 정보이므로 분리하지 않습니다:

- `email`: 이메일 주소 (민감 정보)
- `password`: 비밀번호 (절대 저장 금지)
- 기타 개인정보

## 자동 동기화 (Cloud Functions)

사용자 프로필이 업데이트될 때마다 `user-props`도 자동으로 동기화됩니다.
이는 `onUserProfileUpdate` Cloud Function을 통해 수행됩니다.

```typescript
// firebase/functions/src/index.ts
import * as functions from 'firebase-functions/v2';
import * as admin from 'firebase-admin';

/**
 * 사용자 프로필 업데이트 시 user-props 자동 동기화
 */
export const onUserProfileUpdate = functions.database.onValueUpdated(
  '/users/{uid}',
  async (event) => {
    const uid = event.params.uid as string;
    const userData = event.data.after.val();

    const propsUpdate: any = {};

    // 각 속성을 user-props로 복사
    if (userData.displayName !== undefined) {
      propsUpdate[`user-props/displayName/${uid}`] = userData.displayName;
    }
    if (userData.photoUrl !== undefined) {
      propsUpdate[`user-props/photoUrl/${uid}`] = userData.photoUrl;
    }
    if (userData.createdAt !== undefined) {
      propsUpdate[`user-props/createdAt/${uid}`] = userData.createdAt;
    }
    if (userData.updatedAt !== undefined) {
      propsUpdate[`user-props/updatedAt/${uid}`] = userData.updatedAt;
    }
    if (userData.gender !== undefined) {
      propsUpdate[`user-props/gender/${uid}`] = userData.gender;
    }
    if (userData.birthYear !== undefined) {
      propsUpdate[`user-props/birthYear/${uid}`] = userData.birthYear;
    }

    // 한 번의 업데이트로 모든 props 동기화
    await admin.database().ref().update(propsUpdate);
  }
);
```

## API 함수 예시

### 모든 사용자 이름 조회

```javascript
import { ref, query, get, orderByKey } from 'firebase/database';
import { database } from '$lib/utils/firebase.js';

/**
 * 모든 사용자의 displayName 조회
 */
async function getAllUserNames() {
  try {
    const namesRef = ref(database, 'user-props/displayName');
    const q = query(namesRef, orderByKey());
    const snapshot = await get(q);

    const users = [];
    if (snapshot.exists()) {
      snapshot.forEach((childSnapshot) => {
        users.push({
          uid: childSnapshot.key,
          displayName: childSnapshot.val()
        });
      });
    }

    return users;
  } catch (error) {
    console.error('사용자 이름 조회 실패:', error);
    return [];
  }
}
```

### 최신 가입 회원 조회

```javascript
import { ref, query, orderByValue, limitToLast, get } from 'firebase/database';

/**
 * 최신 10명의 가입 회원 조회
 */
async function getRecentUsers(limit = 10) {
  try {
    const createdAtRef = ref(database, 'user-props/createdAt');
    const q = query(
      createdAtRef,
      orderByValue(),
      limitToLast(limit)
    );
    const snapshot = await get(q);

    const users = [];
    if (snapshot.exists()) {
      snapshot.forEach((childSnapshot) => {
        users.unshift({
          uid: childSnapshot.key,
          createdAt: childSnapshot.val()
        });
      });
    }

    return users;
  } catch (error) {
    console.error('최신 회원 조회 실패:', error);
    return [];
  }
}
```
