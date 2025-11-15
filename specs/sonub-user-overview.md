---
name: sonub-user-overview
version: 2.0.0
description: Firestore 기반 사용자 관리·프로필·목록 스펙
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
  - sonub-user-profile-sync.md
  - sonub-store-auth.md
tags:
  - firestore
  - user
  - profile
  - list
---

# 사용자 관리 개요

본 문서는 Sonub 프로젝트에서 **Cloud Firestore + Firebase Authentication**을 사용해 사용자 데이터를 저장·표시·동기화하는 방식을 정리합니다. 내용은 현재 코드 기준입니다.

## 1. 저장소 구조

- **컬렉션**: `users`
  - 문서 ID = Firebase Auth UID
  - 필드 정의는 `src/lib/types/firestore.types.ts`의 `UserData` 인터페이스를 따른다.
  - 파생 필드(`displayNameLowerCase`, `sort_recent*`, `birthYear*`)는 Cloud Functions `firebase/functions/src/handlers/user.handler.ts`가 자동으로 유지한다.
- **서브컬렉션**
  - `users/{uid}/chat-joins`, `chat-invitations`, `chat-favorites` (채팅 문서 참고)
- **시스템 컬렉션**
  - `system/stats`: `userCount` 등을 저장, `handleUserCreate`가 증분
  - `system/settings`: `admins` 객체, `src/lib/stores/auth.svelte.ts`에서 로드

## 2. 프로필 동기화

- 로그인 성공 시 `src/lib/stores/auth.svelte.ts`의 `syncUserProfile()`이 Firestore 문서를 생성/병합한다.
  - 없는 필드만 `displayName`, `photoUrl`, `languageCode`로 채워 넣음.
  - 서버 타임스탬프는 클라이언트에서 직접 기록하지 않는다.
- Cloud Functions:
  - `handleUserCreate`: `createdAt`, `displayNameLowerCase`, `system/stats.userCount` 설정
  - `handleUserBirthYearMonthDayUpdate`: `birthYear`, `birthMonth`, `birthMonthDay` 파생 생성
  - `handleUserPhotoUpdate` 등 세부 핸들러는 `firebase/functions/src/index.ts`에서 라우팅

## 3. 사용자 목록 ( `/user/list` )

- UI 파일: `src/routes/user/list/+page.svelte`
- 리스트 컴포넌트: `FirestoreListView` (`src/lib/components/FirestoreListView.svelte`)
  - `path="users"`, `pageSize` 15 기본
  - 정렬 필드: `createdAt`, `sort-recent-with-photo`, `sort-recent-female-with-photo`, `sort-recent-male-with-photo`
  - 검색: `displayNameLowerCase` prefix 쿼리 (`>= keyword`, `< keyword+'\uf8ff'`)
- 아바타: `src/lib/components/user/avatar.svelte`에서 Storage URL 혹은 기본 이미지 사용

### UX 요구사항

- 정렬 드롭다운과 검색 버튼은 기존 명세(2025‑11‑14 갱신)를 그대로 유지하되, `FirestoreListView` props (`orderByField`, `whereFilters`)를 업데이트하도록 한다.
- 검색 중에는 `{#key}`를 사용해 쿼리를 재구독하고, 결과 배지 + 초기화 버튼을 노출한다.
- 모든 버튼/모달은 shadcn-svelte 컴포넌트 기준.

## 4. 보안 및 권한

- Firestore 규칙(`firebase/firestore.rules`)에서 `users/{uid}` 문서는 읽기 모두 허용, 쓰기는 본인만 허용.
- 관리자 권한은 `system/settings` 문서의 `admins` 객체를 통해 부여된다. 인증 스토어가 이 문서를 읽어 `adminList`를 캐시한다.
- 프로필 사진 업로드는 Firebase Storage를 사용하며, 규칙은 `sonub-firebase-security-rules.md`를 따른다.

## 5. 핵심 요약

1. 사용자 데이터는 `users/{uid}` Firestore 문서에만 저장한다.
2. 클라이언트는 기본 프로필 필드만 작성하고, 파생 필드/통계는 Cloud Functions가 관리한다.
3. 사용자 목록 화면은 Firestore 쿼리(`FirestoreListView`)로만 데이터를 로드하며, 정렬·검색 옵션은 파생 필드를 활용한다.
4. 관리자 여부는 `system/settings` 문서를 통해 판별한다.

## 6. 참고 문서

- [sonub-firebase-database-structure.md](./sonub-firebase-database-structure.md)
- [sonub-user-profile-sync.md](./sonub-user-profile-sync.md)
- [sonub-store-auth.md](./sonub-store-auth.md)
