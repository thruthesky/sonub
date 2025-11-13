---
name: firebase.ts
description: Firebase 초기화 및 서비스 인스턴스
version: 1.0.0
type: typescript
category: firebase
tags: [firebase, initialization, auth, firestore, rtdb, storage, analytics]
---

# firebase.ts

## 개요
Firebase 앱을 초기화하고 필요한 서비스들을 export합니다. 중복 초기화 방지와 SSR 대응을 포함합니다.

## Export 서비스
- **auth**: Firebase Authentication
- **db**: Firestore Database
- **rtdb**: Realtime Database
- **storage**: Firebase Storage
- **analytics**: Firebase Analytics

## 특징
- 브라우저 환경에서만 초기화
- 중복 초기화 방지 (getApps() 사용)
- SvelteKit 환경 변수 활용 (PUBLIC_ 접두사)

## 사용 예시
```typescript
import { auth, rtdb } from '$lib/firebase';

// Authentication
const user = auth?.currentUser;

// Realtime Database
const userRef = ref(rtdb!, 'users/user123');
```
