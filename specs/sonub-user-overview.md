---
name: sonub
version: 1.0.0
description: 사용자 관리 체계 및 프로필 관리 명세서
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: GPL-3.0
step: 40
priority: "**"
dependencies:
  - sonub-user-login.md
  - sonub-setup-firebase.md
  - sonub-firebase-database-structure.md
tags:
  - user-management
  - profile
  - firebase
  - authentication
---

## Overview
- 이 문서는 "사용자 관리"에 대한 세부 사양을 정리하며, 기존 내용을 그대로 유지한 채 SED 구조에 맞춰 제공합니다.

## Requirements
- 문서 전반에 걸쳐 소개되는 지침과 참고 사항을 모두 숙지해야 하며, 별도의 추가 선행 조건은 원문 각 절에서 제시되는 내용을 따릅니다.

## Workflow
1. 아래 `## Detail Items` 절에 포함된 원문 목차를 검토합니다.
2. 필요한 경우 원문의 각 절을 순서대로 읽으며 프로젝트 작업 흐름에 반영합니다.
3. 문서에 명시된 모든 지침을 확인한 뒤 실제 개발 단계에 적용합니다.

## Detail Items
- 이하에는 기존 문서의 모든 내용을 원형 그대로 포함하여 참조할 수 있도록 구성했습니다.

# 사용자 관리

SNS 웹 프로젝트에서 사용자 관리는 Firebase의 Authentication과 Realtime Database를 활용합니다. 사용자 프로필, 프로필 사진, 사용자 정보 조회 및 실시간 업데이트 기능을 제공합니다.

**관련 SED 문서**:
- [사용자 프로필 사진 업로드 및 관리](./sonub-user-profile.md) - 프로필 사진 저장소 구조, 업로드 구현, URL 관리
- [사용자 속성 분리 (user-props)](./sonub-user-props.md) - 대량 조회 최적화, 속성 분리 전략, Cloud Functions 자동 동기화
- [Firebase 기본 설정](./sonub-setup-firebase.md) - Firebase Authentication, Realtime Database 설정
- [사용자 로그인](./sonub-user-login.md) - Google 및 Apple 소셜 로그인 구현

---

# 사용자 프로필 관리

SNS 웹 프로젝트에서 사용자의 프로필 정보는 다음과 같이 구성됩니다:

- **기본 프로필 정보**: displayName(닉네임), photoURL(프로필 사진), 성별, 생년월일, 자기소개
- **프로필 사진 저장소**: Firebase Storage에 프로필 사진을 저장
- **프로필 데이터 저장소**: Firebase Realtime Database에 프로필 정보 저장
- **실시간 업데이트**: 프로필 변경 시 즉시 다른 사용자에게 반영

## 주요 기술

- **Firebase Authentication**: 사용자 인증 관리
- **Firebase Realtime Database**: 프로필 데이터 실시간 저장소
- **Firebase Cloud Storage**: 프로필 사진 파일 저장소
- **Svelte 5 Runes**: 반응형 상태 관리

## 사용자 목록 검색 UX (2025-11-11 갱신)

- `/user/list` 상단에 **"사용자 검색"** 버튼을 추가하고, 버튼은 `svelte-shadcn`의 `Button` 컴포넌트로 구현한다.
- 버튼 클릭 시 `Dialog`(shadcn-svelte) + Tailwind CSS 조합으로 만든 모달이 열리며, 입력 필드는 `displayNameLowerCase` 값을 그대로 받을 수 있도록 소문자 안내 문구를 포함한다.
- 검색어는 소문자로 정규화되어 `DatabaseListView`의 `equalToValue` Prop으로 전달된다. `orderBy="displayNameLowerCase"`와 함께 사용하여 **정확히 일치하는 사용자만** 서버 쿼리에서 가져온다.
- 검색 중에는 DatabaseListView를 `{#key users-search-${keyword}}`로 래핑해 컴포넌트를 재구독하며, 결과 배지는 "검색 결과" 문구와 초기화 버튼을 함께 제공한다.
- 모달 하단에는 `검색 초기화` 버튼을 두어 입력값과 활성 검색 상태를 동시에 초기화한다. 이 버튼 또한 shadcn-svelte `Button` 컴포넌트를 사용한다.
- 검색이 비활성화되면 기본 목록(정렬 기준: `createdAt`)으로 자동 복귀하며, 실시간 listener는 기존과 동일하게 유지된다.

---

# 사용자 프로필 데이터 구조

- 사용자 데이터 구조는 [Firebase Realtime Database 구조 설계](./sonub-firebase-database.md) 문서를 참고합니다.

---

---

---

# 보안 규칙

## Firebase Storage 보안 규칙

프로필 사진 저장소의 보안 규칙은 [./sonub-firebase-security.md](./sonub-firebase-security.md) 문서를 참고합니다.


---

# 핵심 요약

1. **프로필 사진은 `/users/{userId}/profile/` 디렉토리에 저장**: 사용자별로 격리되어 보안 강화
2. **photoUrl은 `/users/{userId}/photoUrl`에 저장**: 다운로드 URL을 Realtime Database에 저장하여 쉽게 접근
3. **프로필 보안은 보안 규칙으로 관리**: Firebase 보안 규칙을 통해 본인 프로필만 수정 가능
4. **프로필 데이터는 실시간으로 동기화**: 다른 사용자의 프로필 변경이 즉시 반영됨
5. **displayName은 필수 필드**: 사용자 식별을 위해 반드시 필요
6. **user-props로 대량 쿼리 최적화**: 특정 속성만 필요할 때 `/user-props/` 경로 사용하여 효율성 향상
7. **Cloud Functions로 자동 동기화**: 프로필 업데이트 시 user-props도 자동으로 동기화되어 데이터 일관성 보장
