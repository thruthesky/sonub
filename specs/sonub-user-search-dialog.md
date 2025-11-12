---
name: sonub-user-search-dialog
version: 2.0.0
description: Firebase RTDB에서 사용자를 검색하고 선택할 수 있는 재사용 가능한 다이얼로그 컴포넌트 사양
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
license: SED Specification License v1.0
step: 25
priority: normal
dependencies:
  - sonub-setup-shadcn.md
  - sonub-setup-tailwindcss.md
  - sonub-design-guideline.md
tags:
  - component
  - dialog
  - user-search
  - firebase
  - rtdb
---

# 사용자 검색 다이얼로그 (UserSearchDialog)

## Overview

Firebase Realtime Database에서 사용자를 검색하고 선택할 수 있는 재사용 가능한 다이얼로그 컴포넌트입니다. 두 가지 모드를 지원합니다:

1. **검색어 전달 모드** (\`showResults=false\`, 기본값): 검색어를 입력받아 부모 컴포넌트로 전달
2. **검색 결과 표시 모드** (\`showResults=true\`): 검색 결과를 다이얼로그 내에 DatabaseListView로 표시하고, 사용자 클릭 시 해당 사용자 데이터를 부모 컴포넌트로 전달

## Requirements

### 기능적 요구사항

1. **검색 기능**
   - Firebase RTDB의 \`users\` 경로에서 사용자 검색
   - \`displayNameLowerCase\` 필드를 기준으로 정확히 일치하는 사용자 검색
   - 검색어는 자동으로 소문자로 변환 (선택적)
   - 최소 검색어 길이 검증 (기본값: 2자)

2. **두 가지 동작 모드**
   - **모드 1** (\`showResults=false\`): 검색어만 부모로 전달
     - 검색 버튼 클릭 시 \`search\` 이벤트 발생
     - 다이얼로그 자동 닫힘
   - **모드 2** (\`showResults=true\`): 검색 결과 표시 및 사용자 선택
     - 검색 버튼 클릭 시 다이얼로그 내에 검색 결과 표시
     - DatabaseListView를 통해 무한 스크롤 지원
     - 사용자 클릭 시 \`userSelect\` 이벤트 발생
     - 다이얼로그 자동 닫힘

3. **UI/UX**
   - shadcn-svelte Dialog 컴포넌트 사용
   - 다이얼로그 열림 시 검색 입력 필드에 자동 포커스
   - 검색 초기화 기능 제공
   - 검색 결과 표시 시 Avatar 컴포넌트를 사용한 사용자 프로필 표시
   - 검색 중, 빈 결과 등 상태별 UI 제공

### 비기능적 요구사항

1. **성능**
   - DatabaseListView를 통한 페이지네이션으로 대량 데이터 처리
   - 검색 결과 최대 높이 제한 (max-h-96)으로 UI 안정성 유지

2. **접근성**
   - 키보드 네비게이션 지원
   - 포커스 관리 (다이얼로그 열림/닫힘)
   - 시각적 피드백 (hover, focus-visible 상태)

3. **재사용성**
   - Props를 통한 완전한 커스터마이징 지원
   - 이벤트 기반 통신으로 느슨한 결합

## Workflow

### 모드 1: 검색어 전달 모드

1. 사용자가 다이얼로그를 엽니다
2. 검색어를 입력하고 "검색하기" 버튼을 클릭합니다
3. 검색어가 자동으로 소문자로 변환됩니다
4. \`search\` 이벤트가 발생하며 \`{ keyword: string }\` 객체를 전달합니다
5. 다이얼로그가 자동으로 닫힙니다
6. 부모 컴포넌트에서 검색어를 받아 필요한 작업을 수행합니다

### 모드 2: 검색 결과 표시 모드

1. 사용자가 다이얼로그를 엽니다
2. 검색어를 입력하고 "검색하기" 버튼을 클릭합니다
3. 검색 결과가 다이얼로그 내에 DatabaseListView로 표시됩니다
4. 각 검색 결과는 Avatar, 이름, 이메일, UID를 표시합니다
5. 사용자가 검색 결과를 클릭합니다
6. \`userSelect\` 이벤트가 발생하며 \`{ user: UserData, uid: string }\` 객체를 전달합니다
7. 다이얼로그가 자동으로 닫힙니다
8. 부모 컴포넌트에서 선택된 사용자 데이터를 받아 필요한 작업을 수행합니다

## Details

### 1. Props 인터페이스

- \`open\`: 다이얼로그 열림/닫힘 상태 (bindable)
- \`keyword\`: 검색어 (bindable)
- \`title\`: 다이얼로그 제목 (기본값: "사용자 검색")
- \`description\`: 다이얼로그 설명
- \`label\`: 입력 필드 레이블
- \`helperText\`: 도움말 텍스트
- \`placeholder\`: 입력 필드 플레이스홀더
- \`submitLabel\`: 검색 버튼 레이블 (기본값: "검색하기")
- \`clearLabel\`: 초기화 버튼 레이블 (기본값: "검색 초기화")
- \`minLength\`: 최소 검색어 길이 (기본값: 2)
- \`autoLowercase\`: 자동 소문자 변환 (기본값: true)
- \`showResults\`: 검색 결과를 다이얼로그 내에 표시할지 여부 (기본값: false)
- \`usersPath\`: RTDB 사용자 경로 (기본값: "users")
- \`searchField\`: 검색할 필드 이름 (기본값: "displayNameLowerCase")
- \`pageSize\`: 검색 결과 페이지 크기 (기본값: 20)

### 2. 이벤트 인터페이스

- \`search\`: 검색어 전달 이벤트 (showResults=false 모드)
  - \`detail.keyword\`: 정규화된 검색어 (소문자 변환 적용)
- \`userSelect\`: 사용자 선택 이벤트 (showResults=true 모드)
  - \`detail.user\`: 선택된 사용자의 RTDB 노드 데이터
  - \`detail.uid\`: 선택된 사용자의 UID (RTDB 키)
- \`clear\`: 초기화 이벤트 (파라미터 없음)

### 3. 컴포넌트 구조

- Dialog (shadcn-svelte)
  - DialogContent
    - DialogHeader (제목, 설명)
    - form (검색 폼)
      - input (검색어 입력)
      - DialogFooter (버튼들)
    - div.search-results (showResults=true일 때만 표시)
      - DatabaseListView
        - Avatar + 사용자 정보 (이름, 이메일, UID)

### 4. 스타일 가이드라인

- 검색 입력 필드: \`rounded-xl border px-4 py-3\`
- 검색 결과 컨테이너: \`max-h-96 overflow-y-auto rounded-lg border\`
- 사용자 아이템: \`hover:bg-blue-50 focus-visible:ring-2\`
- Light Mode 전용 (Dark Mode 미지원)

### 5. 데이터베이스 구조

RTDB 사용자 노드:

\`\`\`
users/
  {uid}/
    displayName: string
    displayNameLowerCase: string  // 검색용
    email: string
    photoURL: string
    createdAt: number
\`\`\`

## Implementation Notes

### 파일 위치
- \`src/lib/components/user/UserSearchDialog.svelte\`

### 의존성
- shadcn-svelte (Dialog, Button)
- DatabaseListView.svelte
- Avatar.svelte
- Firebase RTDB

### 성능 최적화
- DatabaseListView 페이지네이션
- 검색 결과 높이 제한 (max-h-96)
- \`{#key searchKeyword}\`로 재초기화

## References

- shadcn-svelte Dialog: https://www.shadcn-svelte.com/docs/components/dialog
- Firebase RTDB 쿼리: https://firebase.google.com/docs/database/web/lists-of-data
- DatabaseListView 사양: \`src/lib/components/DatabaseListView.svelte\`
- Avatar 사양: \`src/lib/components/user/avatar.svelte\`
