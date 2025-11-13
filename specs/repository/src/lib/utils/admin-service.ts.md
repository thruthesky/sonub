---
name: admin-service.ts
description: 관리자 서비스
version: 1.0.0
type: util
category: admin
tags: [admin, test-users, firebase, rtdb]
---

# admin-service.ts

## 개요
테스트 사용자 생성, 사용자 목록 조회 등의 관리자 기능을 담당합니다.

## 주요 함수
- **saveTestUsersToFirebase(users, onProgress?)**: 테스트 사용자를 Firebase에 저장
- **getTemporaryUsers()**: 모든 임시 사용자 조회
- **deleteUserByUid(uid)**: 특정 사용자 삭제
- **deleteAllTemporaryUsers(onProgress?)**: 모든 임시 사용자 삭제
- **getTemporaryUserCount()**: 임시 사용자 개수

## 사용 예시
```typescript
import { saveTestUsersToFirebase, getTemporaryUsers } from '$lib/utils/admin-service';

const users = generateTestUsers();
await saveTestUsersToFirebase(users);
```
