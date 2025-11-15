---
name: sonub-user-derived-fields
version: 2.0.0
description: Firestore 사용자 파생 필드(구 user-props) 관리 가이드
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-user-overview.md
  - sonub-user-profile-sync.md
  - sonub-firebase-database-structure.md
tags:
  - firestore
  - derived-fields
  - user
---

# 사용자 파생 필드 (user props 개념)

별도의 RTDB `user-props` 노드 대신, Sonub은 **Firestone `users/{uid}` 문서 내부 필드**를 통해 대량 조회와 정렬을 처리합니다. Cloud Functions가 파생 필드를 자동으로 유지하므로 클라이언트는 필요한 정보를 즉시 쿼리할 수 있습니다.

## 1. 파생 필드 목록

| 필드 | 설명 | 작성 주체 |
|------|------|-----------|
| `displayNameLowerCase` | 소문자 이름. 검색 키(`displayNameLowerCase >= keyword`)에 사용 | Cloud Functions `handleUserCreate` / `handleUserDisplayNameUpdate` |
| `sort-recent-with-photo` | 프로필 사진이 있는 사용자 정렬용 Timestamp | Cloud Functions (메시지/사진 업데이트 시) |
| `sort-recent-female-with-photo` | 여성 + 사진 보유 사용자 정렬용 | 동일 |
| `sort-recent-male-with-photo` | 남성 + 사진 보유 사용자 정렬용 | 동일 |
| `birthYear`, `birthMonth`, `birthDay`, `birthMonthDay` | `birthYearMonthDay` 숫자에서 추출 | `handleUserBirthYearMonthDayUpdate` |
| `createdAt`, `updatedAt` | 최초 가입 / 주요 필드 변경 시각 | Cloud Functions |

> 모든 필드 이름은 Firestore 컬렉션/문서와 동일하게 **kebab-case**로 표기합니다.

## 2. 생성/변경 플로우

1. 클라이언트가 `users/{uid}` 문서에 기본 프로필 필드를 저장 (`displayName`, `photoUrl`, `birthYearMonthDay` 등)
2. Firestore 트리거가 변경된 필드를 감지
3. 핸들러 별 작업
   - `displayName` 변경 → `displayNameLowerCase`, `updatedAt`
   - `photoUrl` 변경 → `sort-recent-*`, `updatedAt`
   - `birthYearMonthDay` 변경 → `birthYear`, `birthMonth`, `birthDay`, `birthMonthDay`
4. 사용자 목록 페이지는 해당 필드를 기준으로 쿼리 수행

## 3. 사용자 목록에서의 활용

`src/routes/user/list/+page.svelte`는 `FirestoreListView`에 다음 쿼리를 전달한다.

```ts
const listOrderBy = isSearching ? 'displayNameLowerCase' : selectedSortField;
const listWhereFilters = isSearching
  ? [
      { field: 'displayNameLowerCase', operator: '>=', value: keyword },
      { field: 'displayNameLowerCase', operator: '<', value: keyword + '\uf8ff' }
    ]
  : [];
```

- 정렬 옵션 → `createdAt`, `sort-recent-with-photo`, `sort-recent-female-with-photo`, `sort-recent-male-with-photo`
- 검색 옵션 → `displayNameLowerCase`

## 4. Cloud Functions 참고

- 파일: `firebase/functions/src/handlers/user.handler.ts`
- `firebase/functions/src/index.ts`에서 Firestore document change를 감지하여 위 핸들러들을 호출
- `system/stats` 업데이트와 동일한 `admin.firestore.FieldValue.increment` 패턴 사용

## 5. 가이드라인

1. **클라이언트는 파생 필드에 직접 접근하거나 수정하지 않는다.**
2. 모든 Firestore 컬렉션과 문서 이름은 kebab-case (`users`, `system/stats`, `chat-room-passwords`)로 유지한다.
3. 새 파생 필드가 필요하면
   - Firestore 문서 필드명 결정
   - Cloud Functions에 계산 로직 추가
   - 관련 스펙·타입 정의 업데이트
4. 리스트나 검색 화면은 항상 파생 필드를 기반으로 쿼리를 구성하고, 서버에서 계산된 값을 사용한다.

## 6. 참고 문서

- [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md)
- [sonub-user-profile-sync.md](./sonub-user-profile-sync.md)
- [firebase/functions/src/handlers/user.handler.ts](../firebase/functions/src/handlers/user.handler.ts)
