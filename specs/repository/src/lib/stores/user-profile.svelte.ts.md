---
name: user-profile.svelte.ts
description: 사용자 프로필 전용 스토어
version: 1.0.0
type: store
category: user
tags: [profile, rtdb, cache, svelte5, runes]
---

# user-profile.svelte.ts

## 개요
Firebase Realtime Database의 /users/{uid} 노드를 실시간으로 구독하여 사용자 프로필 데이터를 중앙에서 관리합니다. 프로필 캐시를 통해 중복 RTDB 리스너를 제거합니다.

## 주요 기능

### UserProfileStore 클래스
- **ensureSubscribed(uid)**: 프로필 구독 시작 (상태 변경)
- **getCachedProfile(uid)**: 캐시된 프로필 데이터 조회 (순수 읽기)
- **isLoading(uid)**: 프로필 로딩 상태 확인
- **getError(uid)**: 프로필 에러 확인

### UserProfile 인터페이스
- **displayName**: 사용자 닉네임
- **photoUrl**: 프로필 사진 URL
- **gender**: 성별 (M, F, null)
- **birthYear, birthMonth, birthDay**: 생년월일
- **bio**: 자기소개
- **createdAt, updatedAt**: 생성/수정 시간

## 사용 예시

```typescript
import { userProfileStore } from '$lib/stores/user-profile.svelte';

// $effect에서 구독 시작
$effect(() => {
  userProfileStore.ensureSubscribed('user123');
});

// $derived에서 데이터 읽기
const profile = $derived(userProfileStore.getCachedProfile('user123'));
```
