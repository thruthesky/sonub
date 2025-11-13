---
name: test-user-generator.ts
description: 테스트 사용자 데이터 생성 유틸리티
version: 1.0.0
type: util
category: test
tags: [test, user, generator]
---

# test-user-generator.ts

## 개요
테스트 목적으로 임시 사용자 데이터 100명을 생성합니다.

## 주요 함수
- **generateTestUsers()**: 테스트 사용자 100명 생성
- **testUserToFirebaseData(user)**: 테스트 사용자를 Firebase 저장용 객체로 변환

## TestUser 인터페이스
- **uid, displayName, email, photoUrl**
- **gender**: 'male' | 'female' | 'other'
- **birthYear**: 1950~2010 랜덤
- **createdAt, updatedAt**: 각 사용자마다 1초씩 차이
- **isTemporary**: true (임시 사용자 표시)

## 사용 예시
```typescript
import { generateTestUsers } from '$lib/utils/test-user-generator';

const users = generateTestUsers(); // 100명 생성
```
