---
name: auth.svelte.ts
description: 현재 사용자의 인증 상태 관리 스토어
version: 1.0.0
type: store
category: authentication
tags: [svelte5, runes, firebase-auth, store, admin]
---

# auth.svelte.ts

## 개요
Svelte 5의 runes를 사용하여 Firebase Authentication 상태를 전역으로 관리합니다. 사용자 로그인/로그아웃, 관리자 권한 확인, 프로필 동기화 등의 기능을 제공합니다.

## 주요 기능

### AuthStore 클래스
- **user**: 현재 로그인한 사용자 (User | null)
- **loading**: 인증 상태 확인 중 여부
- **initialized**: 인증 시스템 초기화 완료 여부
- **adminList**: 관리자 UID 배열 (/system/settings/admins)

### 주요 메서드
- **isAuthenticated**: 사용자 로그인 여부
- **isAdmin**: 현재 사용자가 관리자인지 확인
- **syncUserProfile()**: Firebase Auth 프로필을 RTDB에 동기화
- **loadAdminList()**: RTDB에서 관리자 목록 로드

### 프로필 동기화 규칙
- **photoUrl**: RTDB에 값이 없거나 공백일 때만 Auth의 photoURL 저장
- **displayName**: RTDB에 값이 없을 때만 Auth의 displayName 저장
- **email, phoneNumber**: 동기화하지 않음

## 사용 예시

```typescript
import { authStore } from '$lib/stores/auth.svelte';

// 현재 사용자
const user = authStore.user;

// 로그인 여부
if (authStore.isAuthenticated) {
  console.log('로그인됨:', user.uid);
}

// 관리자 여부
if (authStore.isAdmin) {
  console.log('관리자 권한 있음');
}
```
