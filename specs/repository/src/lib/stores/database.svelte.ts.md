---
name: database.svelte.ts
description: Firebase Realtime Database 유틸리티
version: 1.0.0
type: store
category: database
tags: [firebase, rtdb, realtime, crud, svelte]
---

# database.svelte.ts

## 개요
Firebase Realtime Database 읽기, 쓰기, 업데이트, 삭제 및 실시간 구독 기능을 제공합니다. Svelte 스토어와 통합하여 반응형 데이터 바인딩을 지원합니다.

## 주요 함수

### 실시간 구독
- **createRealtimeStore<T>(path, defaultValue?)**: 실시간 데이터 구독
- **rtdbStore<T>(path, defaultValue?)**: createRealtimeStore의 짧은 별칭

### CRUD 작업
- **writeData(path, data)**: 데이터 쓰기 (덮어쓰기)
- **updateData(path, updates)**: 데이터 업데이트 (부분 업데이트)
- **deleteData(path)**: 데이터 삭제
- **pushData(path, data)**: 새 항목 추가 (자동 키 생성)
- **readData<T>(path)**: 데이터 읽기 (한 번만)

### 온라인 상태 관리
- **setupPresence(userId)**: 온라인/오프라인 상태 관리

## 사용 예시

```typescript
import { rtdbStore, writeData } from '$lib/stores/database.svelte';

// 실시간 구독
const posts = rtdbStore<Post[]>('posts');

{#if $posts.loading}
  <p>로딩 중...</p>
{:else if $posts.data}
  {#each $posts.data as post}
    <div>{post.title}</div>
  {/each}
{/if}

// 데이터 쓰기
await writeData('users/user123', { name: 'John', age: 30 });
```
