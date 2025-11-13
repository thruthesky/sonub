---
name: auth-helpers.ts
description: Firebase Authentication 헬퍼 함수
version: 1.0.0
type: util
category: authentication
tags: [firebase-auth, google, apple, login]
---

# auth-helpers.ts

## 개요
Google 및 Apple 로그인에 필요한 유틸리티 함수를 제공합니다.

## 주요 함수
- **signInWithGoogle()**: Google 로그인
- **signInWithApple()**: Apple 로그인
- **signOut()**: 로그아웃
- **getAuthLanguage()**: 브라우저 언어를 Firebase 지원 언어로 변환
- **getAuthErrorMessage(errorCode, provider)**: 에러 코드를 한글 메시지로 변환

## 사용 예시
```typescript
import { signInWithGoogle, signInWithApple } from '$lib/utils/auth-helpers';

await signInWithGoogle();
await signInWithApple();
```
