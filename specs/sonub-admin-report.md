---
name: sonub
version: 1.0.0
description: 신고 목록 표시 기능 (Admin & User Report List) - 관리자 신고 목록 페이지 및 사용자 신고 목록 페이지 구현 명세서
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: GPL-3.0
step: 60
priority: "*"
dependencies:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
  - sonub-firebase-database-structure.md
  - sonub-firebase-database-structure.md
tags:
  - admin
  - report
  - firebase
  - list-view
  - svelte5
---

# 신고 목록 표시 기능 (Admin & User Report List)

## 목차

- [신고 목록 표시 기능 (Admin \& User Report List)](#신고-목록-표시-기능-admin--user-report-list)
  - [목차](#목차)
  - [Overview](#overview)
  - [Requirements](#requirements)
  - [Workflow](#workflow)
  - [Detail Items](#detail-items)
    - [1. 데이터베이스 구조](#1-데이터베이스-구조)
      - [신고 데이터 경로](#신고-데이터-경로)
      - [신고 데이터 구조](#신고-데이터-구조)
      - [키 형식 규칙](#키-형식-규칙)
    - [2. 타입 정의 사양](#2-타입-정의-사양)
      - [파일 위치](#파일-위치)
      - [ReportType 타입](#reporttype-타입)
      - [ReportReason 타입](#reportreason-타입)
      - [Report 인터페이스](#report-인터페이스)
      - [ReportWithId 인터페이스](#reportwithid-인터페이스)
    - [3. 서비스 API 사양](#3-서비스-api-사양)
      - [파일 위치](#파일-위치-1)
      - [checkReportStatus() 함수](#checkreportstatus-함수)
      - [removeReport() 함수](#removereport-함수)
    - [4. 관리자 신고 목록 페이지 사양](#4-관리자-신고-목록-페이지-사양)
      - [경로](#경로)
      - [파일 위치](#파일-위치-2)
      - [컴포넌트 Props](#컴포넌트-props)
      - [UI 구조](#ui-구조)
      - [DatabaseListView Props](#databaselistview-props)
      - [헬퍼 함수](#헬퍼-함수)
      - [이벤트 핸들러](#이벤트-핸들러)
      - [스타일 요구사항](#스타일-요구사항)
      - [완전한 구현 코드](#완전한-구현-코드)
    - [5. 사용자 신고 목록 페이지 사양](#5-사용자-신고-목록-페이지-사양)
      - [경로](#경로-1)
      - [파일 위치](#파일-위치-3)
      - [컴포넌트 Props](#컴포넌트-props-1)
      - [UI 구조](#ui-구조-1)
      - [DatabaseListView Props](#databaselistview-props-1)
      - [헬퍼 함수](#헬퍼-함수-1)
      - [이벤트 핸들러](#이벤트-핸들러-1)
      - [스타일 요구사항](#스타일-요구사항-1)
      - [완전한 구현 코드](#완전한-구현-코드-1)
    - [6. 라우팅 사양](#6-라우팅-사양)
      - [SvelteKit 파일 기반 자동 라우팅](#sveltekit-파일-기반-자동-라우팅)
      - [네비게이션 메뉴 업데이트](#네비게이션-메뉴-업데이트)
    - [7. 다국어 지원 (i18n)](#7-다국어-지원-i18n)
      - [한국어 (ko.json)](#한국어-kojson)
      - [영어 (en.json)](#영어-enjson)
    - [8. 테스트 사양](#8-테스트-사양)
      - [유닛 테스트](#유닛-테스트)
      - [E2E 테스트 (Playwright)](#e2e-테스트-playwright)
  - [참고 문서](#참고-문서)

---

## Overview

본 문서는 **신고된 목록을 표시하는 방법**에 대한 완벽한 사양서입니다. 관리자 신고 목록 페이지와 사용자 신고 목록 페이지의 구현 방법을 정확히 명시합니다.

**핵심 특징:**
- ✅ **관리자 신고 목록 페이지** (`/admin/reports`): 모든 사용자의 신고를 createdAt 순서로 표시
- ✅ **사용자 신고 목록 페이지** (`/my/reports`): 현재 로그인한 사용자가 작성한 신고만 필터링하여 표시
- ✅ **DatabaseListView 활용**: 무한 스크롤 및 실시간 데이터 동기화
- ✅ **클라이언트 측 필터링**: 사용자 신고 목록은 `filter` prop으로 uid 필터링
- ✅ **신고 취소 기능**: 사용자는 자신의 신고를 취소 가능
- ✅ **대상 보기 기능**: 신고된 게시글/댓글로 이동
- ✅ **실시간 업데이트**: 신고 추가/삭제 시 목록 자동 갱신

---

## Requirements

**필수 라이브러리 및 도구:**
- ✅ Svelte 5 (`svelte@5.43.2`)
- ✅ SvelteKit 5 (파일 기반 라우팅)
- ✅ Firebase Realtime Database
- ✅ i18n 스토어 (`src/lib/stores/i18n.svelte`)
- ✅ Auth 스토어 (`src/lib/stores/auth.svelte`)
- ✅ Report 타입 (`src/lib/types/report.ts`)
- ✅ Report 서비스 (`src/lib/services/report.ts`)
- ✅ DatabaseListView 컴포넌트 (향후 구현, `src/lib/components/DatabaseListView.svelte`)
- ✅ SvelteKit 내장 API:
  - `$app/navigation` - `goto()` 함수
  - `$app/stores` - `page` 스토어

**선행 조건:**
- ✅ Firebase 프로젝트 설정 완료
- ✅ Realtime Database `/reports/` 노드 생성 완료
- ✅ Firebase Authentication 활성화
- ✅ 신고 기능 구현 완료 (PostItem, CommentItem에 신고 버튼 추가)

---

## Workflow

신고 목록 표시 기능 개발은 다음 순서로 진행해야 합니다:

1. **타입 정의 확인**
   - `src/lib/types/report.ts` 파일 확인
   - `ReportWithId`, `ReportType`, `ReportReason` 타입 정의 확인

2. **서비스 API 확인**
   - `src/lib/services/report.ts` 파일 확인
   - `checkReportStatus()`, `removeReport()` 함수 구현 확인

3. **관리자 신고 목록 페이지 작성**
   - `src/routes/admin/reports/+page.svelte` 파일 생성 (SvelteKit 파일 기반 라우팅)
   - DatabaseListView로 모든 신고 렌더링
   - 신고 사유 및 타입 표시
   - "대상_보기" 버튼 구현

4. **사용자 신고 목록 페이지 작성**
   - `src/routes/my/reports/+page.svelte` 파일 생성 (SvelteKit 파일 기반 라우팅)
   - DatabaseListView의 `filter` prop으로 uid 필터링
   - "신고_취소" 버튼 구현
   - 로그인 체크 및 빈 상태 표시

5. **공통 레이아웃 설정 (선택사항)**
   - `src/routes/admin/+layout.svelte` - 관리자 페이지 공통 레이아웃
   - `src/routes/my/+layout.svelte` - 사용자 페이지 공통 레이아웃

6. **다국어 지원 추가**
   - `public/locales/ko.json`, `en.json`, `ja.json`, `zh.json`에 번역 추가
   - 신고 관련 i18n 키 추가

7. **네비게이션 메뉴 업데이트**
   - 상단 바(top-bar) 또는 메뉴 컴포넌트에 신고 목록 링크 추가
   - 관리자 신고 목록: `/admin/reports`
   - 내 신고 목록: `/my/reports`

8. **테스트**
   - 관리자 신고 목록 렌더링 테스트
   - 사용자 신고 목록 필터링 테스트
   - 신고 취소 기능 테스트
   - 실시간 업데이트 테스트

---

## Detail Items

### 1. 데이터베이스 구조

#### 신고 데이터 경로

**Firebase Realtime Database 경로:**
```
/reports/
```

**📚 상세 데이터베이스 구조는 [sonub-firebase-database.md](./sonub-firebase-database.md#신고-reports)를 참조하세요.**

#### 신고 데이터 구조

```json
{
  "reports": {
    "post-abc123-user456": {
      "type": "post",
      "nodeId": "abc123",
      "uid": "user456",
      "reason": "abuse",
      "message": "욕설이 포함되어 있습니다",
      "createdAt": 1698473000000
    },
    "comment-xyz789-user789": {
      "type": "comment",
      "nodeId": "xyz789",
      "uid": "user789",
      "reason": "spam",
      "message": "",
      "createdAt": 1698473100000
    }
  }
}
```

#### 키 형식 규칙

**신고 키 형식:**
```
{type}-{nodeId}-{uid}
```

**예시:**
- 게시글 신고: `post-abc123-user456`
- 댓글 신고: `comment-xyz789-user789`

**중복 방지:**
- 동일한 사용자가 같은 게시글/댓글을 중복 신고하면 덮어쓰기됨
- Firebase의 키 중복 방지 기능 활용

---

### 2. 타입 정의 사양

#### 파일 위치

**경로:** `src/lib/types/report.ts`

#### ReportType 타입

**정의:**
```typescript
export type ReportType = 'post' | 'comment';
```

**설명:**
- `'post'`: 게시글 신고
- `'comment'`: 댓글 신고

#### ReportReason 타입

**정의:**
```typescript
export type ReportReason = 'abuse' | 'fake-news' | 'spam' | 'inappropriate' | 'other';
```

**설명:**
- `'abuse'`: 욕설, 시비, 모욕, 명예훼손
- `'fake-news'`: 가짜 뉴스, 잘못된 정보
- `'spam'`: 스팸, 악용
- `'inappropriate'`: 카테고리에 맞지 않는 글 등록
- `'other'`: 기타

#### Report 인터페이스

**정의:**
```typescript
export interface Report {
  type: ReportType;
  nodeId: string;
  uid: UserId;
  reason: ReportReason;
  message: string;
  createdAt: number;
}
```

**필드 설명:**
- `type`: 신고 대상 타입
- `nodeId`: 게시글 ID 또는 댓글 ID
- `uid`: 신고자 사용자 UID
- `reason`: 신고 사유
- `message`: 상세 설명 (선택 사항, 기본값: 빈 문자열)
- `createdAt`: 신고 생성 시간 (Unix timestamp, 밀리초)

#### ReportWithId 인터페이스

**정의:**
```typescript
export interface ReportWithId extends Report {
  reportId: string;
}
```

**필드 설명:**
- `reportId`: 신고 고유 ID (형식: `"post-{postId}-{uid}"` 또는 `"comment-{commentId}-{uid}"`)
- 나머지 필드는 `Report` 인터페이스와 동일

**사용 위치:**
- AdminReportListPage.svelte
- MyReportListPage.svelte

---

### 3. 서비스 API 사양

#### 파일 위치

**경로:** `src/lib/services/report.ts`

#### checkReportStatus() 함수

**역할:** 사용자가 특정 게시글/댓글을 이미 신고했는지 확인합니다.

**함수 시그니처:**
```typescript
export async function checkReportStatus(
  type: ReportType,
  nodeId: FirebaseKey,
  userId: UserId
): Promise<CheckReportStatusResult>
```

**파라미터:**
- `type` (ReportType): `'post'` 또는 `'comment'`
- `nodeId` (FirebaseKey): 게시글 ID 또는 댓글 ID
- `userId` (UserId): 사용자 UID

**리턴값:**
```typescript
interface CheckReportStatusResult {
  isReported: boolean;
  reportId?: string;
}
```

**구현 로직:**
1. `nodeId`에서 앞의 `-` 제거 (Firebase push key 대응)
2. 신고 ID 생성: `${type}-${cleanNodeId}-${userId}`
3. Firebase에서 `/reports/{reportId}` 경로 조회
4. 존재하면 `{ isReported: true, reportId }` 반환
5. 존재하지 않으면 `{ isReported: false }` 반환

**사용 예시:**
```typescript
const status = await checkReportStatus('post', 'abc123', 'user456');
if (status.isReported) {
  console.log('이미 신고함:', status.reportId);
} else {
  console.log('아직 신고하지 않음');
}
```

#### removeReport() 함수

**역할:** 게시글 또는 댓글의 신고를 취소합니다.

**함수 시그니처:**
```typescript
export async function removeReport(
  type: ReportType,
  nodeId: FirebaseKey,
  userId: UserId
): Promise<RemoveReportResult>
```

**파라미터:**
- `type` (ReportType): `'post'` 또는 `'comment'`
- `nodeId` (FirebaseKey): 게시글 ID 또는 댓글 ID
- `userId` (UserId): 신고자 사용자 UID

**리턴값:**
```typescript
interface RemoveReportResult {
  success: boolean;
  error?: string;
  errorMessage?: string;
}
```

**구현 로직:**
1. `nodeId`에서 앞의 `-` 제거
2. 신고 ID 생성: `${type}-${cleanNodeId}-${userId}`
3. Firebase에서 `/reports/{reportId}` 경로 삭제
4. 성공 시 `{ success: true }` 반환
5. 실패 시 `{ success: false, error: i18nKey, errorMessage }` 반환

**에러 처리:**
- ✅ Firebase 권한 오류: `error.db.permissionDenied`
- ✅ 네트워크 오류: `error.db.networkError`
- ✅ 알 수 없는 오류: `error.unknown`

**사용 예시:**
```typescript
const result = await removeReport('post', 'abc123', 'user456');
if (result.success) {
  showToast('신고가취소되었습니다', 'success');
} else {
  showToast(result.error || 'error.unknown', 'error');
}
```

---

### 4. 관리자 신고 목록 페이지 사양

#### 경로

**URL:** `/admin/reports`

#### 파일 위치

**파일명:** `src/routes/admin/reports/+page.svelte`

#### 컴포넌트 Props

**Props:** 없음 (경로 기반 페이지)

#### UI 구조

**페이지 레이아웃:**
```
┌─────────────────────────────────────────┐
│ 페이지 헤더                              │
│ - 타이틀: "관리자 신고 목록"             │
│ - 설명: "모든 사용자의 신고를 확인..."   │
├─────────────────────────────────────────┤
│ DatabaseListView                        │
│ ┌─────────────────────────────────────┐ │
│ │ 신고 아이템 #1                       │ │
│ │ - 신고 번호: #1                      │ │
│ │ - 신고 타입: 게시글/댓글             │ │
│ │ - 신고 날짜: 2024-01-15 14:30       │ │
│ │ - 신고자: user456                   │ │
│ │ - 대상ID: abc123                    │ │
│ │ - 신고사유: 욕설, 시비, 모욕...     │ │
│ │ - 상세메시지: (있으면 표시)         │ │
│ │ [대상_보기] 버튼                    │ │
│ └─────────────────────────────────────┘ │
│ ┌─────────────────────────────────────┐ │
│ │ 신고 아이템 #2                       │ │
│ │ ...                                 │ │
│ └─────────────────────────────────────┘ │
│ ... (무한 스크롤)                        │
└─────────────────────────────────────────┘
```

#### DatabaseListView Props

**필수 Props:**
```svelte
<DatabaseListView
  path="reports"
  orderBy="createdAt"
  limitToFirst={20}
  let:item
  let:index
>
```

**Props 설명:**
- `path`: `"reports"` - Firebase RTDB 경로
- `orderBy`: `"createdAt"` - 생성 시간 기준 정렬
- `limitToFirst`: `20` - 한 페이지에 20개씩 로드
- `let:item`: 각 신고 데이터 (타입: `any`, 사용 시 `ReportWithId`로 타입 단언)
- `let:index`: 인덱스 (0부터 시작)

#### 헬퍼 함수

**함수 1: getReasonText()**

**역할:** 신고 사유를 한글로 변환

**함수 시그니처:**
```typescript
function getReasonText(reason: string): string
```

**구현:**
```typescript
function getReasonText(reason: string): string {
  return $t(`신고사유_${reason}`);
}
```

**매핑:**
- `abuse` → `신고사유_abuse` → "욕설, 시비, 모욕, 명예훼손"
- `fake-news` → `신고사유_fake-news` → "가짜 뉴스, 잘못된 정보"
- `spam` → `신고사유_spam` → "스팸, 악용"
- `inappropriate` → `신고사유_inappropriate` → "카테고리에 맞지 않는 글 등록"
- `other` → `신고사유_other` → "기타"

**함수 2: getTypeText()**

**역할:** 신고 타입을 한글로 변환

**함수 시그니처:**
```typescript
function getTypeText(type: string): string
```

**구현:**
```typescript
function getTypeText(type: string): string {
  return type === "post" ? $t("게시글") : $t("댓글");
}
```

**매핑:**
- `post` → "게시글"
- `comment` → "댓글"

#### 이벤트 핸들러

**핸들러: handleGoToNode()**

**역할:** 신고된 게시글/댓글로 이동

**함수 시그니처:**
```typescript
function handleGoToNode(report: ReportWithId): void
```

**구현 로직:**
```typescript
function handleGoToNode(report: ReportWithId) {
  if (report.type === "post") {
    // 게시글 상세 페이지로 이동
    navigate(`/post/detail/${report.nodeId}`);
  } else {
    // 댓글은 게시글 상세 페이지로 이동 (댓글이 속한 게시글로 이동)
    // 댓글 ID로는 직접 이동할 수 없으므로, 게시글 목록으로 이동
    navigate("/post/list");
  }
}
```

**주의사항:**
- ✅ 게시글 신고: `/post/detail/{nodeId}` 경로로 이동
- ⚠️ 댓글 신고: 현재 댓글 ID만으로는 어떤 게시글에 속하는지 알 수 없으므로 `/post/list`로 이동
  - 향후 개선: 댓글 데이터에 `postId` 필드 추가 필요

#### 스타일 요구사항

**컨테이너:**
- `max-width`: `900px`
- `margin`: `0 auto`
- `padding`: `2rem 1rem`

**페이지 헤더:**
- `margin-bottom`: `2rem`
- `padding-bottom`: `1rem`
- `border-bottom`: `2px solid #e5e7eb`

**신고 아이템:**
- `background-color`: `#ffffff`
- `border`: `1px solid #e5e7eb`
- `border-radius`: `0.5rem`
- `padding`: `1.5rem`
- `margin-bottom`: `1rem`
- `transition`: `box-shadow 0.2s ease`
- `hover`: `box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1)`

**신고 타입 뱃지:**
- 게시글 (`post`): `background-color: #3b82f6` (파랑)
- 댓글 (`comment`): `background-color: #10b981` (초록)

**반응형:**
- 모바일 (768px 이하): `padding: 1rem 0.5rem`

#### 완전한 구현 코드

**파일:** `src/routes/admin/reports/+page.svelte`

```svelte
<script lang="ts">
  /**
   * 관리자 신고 목록 페이지
   *
   * 모든 사용자의 신고를 createdAt 순서로 표시합니다.
   * 관리자만 접근 가능합니다.
   */
  import { t } from "$lib/stores/i18n.svelte";
  import { goto } from "$app/navigation";
  import { page } from "$app/stores";
  // import DatabaseListView from "$lib/components/DatabaseListView.svelte";
  // import type { ReportWithId } from "$lib/types/report";

  /**
   * 신고 사유를 한글로 변환하는 함수
   *
   * @param reason - 신고 사유 (abuse, fake-news, spam, inappropriate, other)
   * @returns 한글 신고 사유
   */
  function getReasonText(reason: string): string {
    return $t(`신고사유_${reason}`);
  }

  /**
   * 신고 타입을 한글로 변환하는 함수
   *
   * @param type - 신고 타입 (post, comment)
   * @returns 한글 신고 타입
   */
  function getTypeText(type: string): string {
    return type === "post" ? $t("게시글") : $t("댓글");
  }

  /**
   * 게시글/댓글로 이동하는 함수
   *
   * @param type - 신고 대상 타입
   * @param nodeId - 신고 대상 ID
   */
  function handleGoToNode(type: string, nodeId: string) {
    if (type === "post") {
      // 게시글 상세 페이지로 이동
      goto(`/post/detail/${nodeId}`);
    } else {
      // 댓글은 게시글 상세 페이지로 이동 (댓글이 속한 게시글로 이동)
      // 댓글 ID로는 직접 이동할 수 없으므로, 게시글 목록으로 이동
      goto("/post/list");
    }
  }
</script>

<div class="admin-report-list-page">
  <!-- 페이지 헤더 -->
  <div class="page-header">
    <h1 class="page-title">{$t("관리자_신고_목록")}</h1>
    <p class="page-description">{$t("모든_사용자의_신고를_확인할_수_있습니다")}</p>
  </div>

  <!-- 신고 목록 -->
  <!--
    향후 구현:
    DatabaseListView 컴포넌트를 사용하여 실시간 신고 목록 표시
    - path="reports"
    - orderBy="createdAt"
    - limitToFirst={20}
    - 페이지네이션 및 무한 스크롤 지원
  -->
  <div class="report-list-container">
    <p class="empty-message">신고 목록이 비어있습니다.</p>
  </div>
</div>

<style>
  /* 페이지 컨테이너 */
  .admin-report-list-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }

  /* 페이지 헤더 */
  .page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
  }

  .page-title {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
  }

  .page-description {
    margin: 0;
    font-size: 0.95rem;
    color: #6b7280;
  }

  /* 신고 아이템 */
  .report-item {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: box-shadow 0.2s ease;
  }

  .report-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  /* 신고 헤더 */
  .report-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
  }

  .report-number {
    font-size: 0.85rem;
    font-weight: 700;
    color: #9ca3af;
  }

  .report-type {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #ffffff;
  }

  .report-type.post {
    background-color: #3b82f6;
  }

  .report-type.comment {
    background-color: #10b981;
  }

  .report-date {
    margin-left: auto;
    font-size: 0.8rem;
    color: #9ca3af;
  }

  /* 신고 내용 */
  .report-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .report-info-row {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    min-width: 80px;
  }

  .value {
    font-size: 0.85rem;
    color: #4b5563;
    word-break: break-word;
  }

  .value.reason {
    font-weight: 600;
    color: #dc2626;
  }

  .value.message {
    font-style: italic;
  }

  /* 액션 버튼 */
  .report-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
  }

  .action-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
  }

  .action-btn.go-to-node {
    background-color: #3b82f6;
    color: #ffffff;
  }

  .action-btn.go-to-node:hover {
    background-color: #2563eb;
  }

  /* 반응형 스타일 */
  @media (max-width: 768px) {
    .admin-report-list-page {
      padding: 1rem 0.5rem;
    }

    .page-title {
      font-size: 1.5rem;
    }

    .report-item {
      padding: 1rem;
    }

    .label {
      min-width: 60px;
      font-size: 0.8rem;
    }

    .value {
      font-size: 0.8rem;
    }
  }
</style>
```

---

### 5. 사용자 신고 목록 페이지 사양

#### 경로

**URL:** `/my/reports`

#### 파일 위치

**파일명:** `src/routes/my/reports/+page.svelte`

#### 컴포넌트 Props

**Props:** 없음 (경로 기반 페이지)

#### UI 구조

**로그인하지 않은 경우:**
```
┌─────────────────────────────────────────┐
│ 빈 상태 (Empty State)                    │
│ ┌─────────────────────────────────────┐ │
│ │ "로그인이 필요합니다"                │ │
│ │ [로그인] 버튼                        │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

**로그인한 경우:**
```
┌─────────────────────────────────────────┐
│ 페이지 헤더                              │
│ - 타이틀: "내 신고 목록"                 │
│ - 설명: "내가 작성한 신고를 확인..."     │
├─────────────────────────────────────────┤
│ DatabaseListView (uid 필터링)            │
│ ┌─────────────────────────────────────┐ │
│ │ 신고 아이템 #1                       │ │
│ │ - 신고 번호: #1                      │ │
│ │ - 신고 타입: 게시글/댓글             │ │
│ │ - 신고 날짜: 2024-01-15 14:30       │ │
│ │ - 대상ID: abc123                    │ │
│ │ - 신고사유: 욕설, 시비, 모욕...     │ │
│ │ - 상세메시지: (있으면 표시)         │ │
│ │ [대상_보기] [신고_취소] 버튼        │ │
│ └─────────────────────────────────────┘ │
│ ┌─────────────────────────────────────┐ │
│ │ 신고 아이템 #2                       │ │
│ │ ...                                 │ │
│ └─────────────────────────────────────┘ │
│ ... (무한 스크롤)                        │
└─────────────────────────────────────────┘
```

#### DatabaseListView Props

**필수 Props:**
```svelte
<DatabaseListView
  path="reports"
  orderBy="createdAt"
  limitToFirst={20}
  filter={(item) => item.uid === authStore.user?.uid}
  let:item
  let:index
>
```

**Props 설명:**
- `path`: `"reports"` - Firebase RTDB 경로
- `orderBy`: `"createdAt"` - 생성 시간 기준 정렬
- `limitToFirst`: `20` - 한 페이지에 20개씩 로드
- `filter`: 클라이언트 측 필터링 함수 (uid 일치하는 신고만 표시)
- `let:item`: 각 신고 데이터
- `let:index`: 인덱스

**filter prop 중요:**
- ✅ 클라이언트 측에서 uid 필터링
- ✅ 모든 신고 데이터를 가져온 후 필터링 (성능 주의)
- ⚠️ 신고 개수가 많으면 성능 저하 가능 (향후 서버 측 필터링 개선 필요)
- **참고:** `authStore.user?.uid`는 로그인한 사용자의 UID를 나타냅니다

#### 헬퍼 함수

**함수 1: getReasonText()**

관리자 페이지와 동일

**함수 2: getTypeText()**

관리자 페이지와 동일

#### 이벤트 핸들러

**핸들러 1: handleGoToNode()**

관리자 페이지와 동일

**핸들러 2: handleCancelReport()**

**역할:** 신고를 취소합니다.

**함수 시그니처:**
```typescript
async function handleCancelReport(report: ReportWithId): Promise<void>
```

**구현 로직:**
```typescript
async function handleCancelReport(report: ReportWithId) {
  // 1. 확인 다이얼로그
  if (!confirm($t("신고를취소하시겠습니까"))) {
    return;
  }

  // 2. 로그인 확인
  if (!$user) {
    showToast($t("로그인필요"), "error");
    return;
  }

  try {
    // 3. 신고 삭제 API 호출
    const result = await removeReport(report.type, report.nodeId, $user.uid);

    // 4. 결과 처리
    if (result.success) {
      showToast($t("신고가취소되었습니다"), "success");
    } else {
      showToast($t(result.error || "error.unknown"), "error");
    }
  } catch (error) {
    console.error("신고 취소 오류:", error);
    showToast($t("error.unknown"), "error");
  }
}
```

**단계별 설명:**
1. 사용자에게 확인 다이얼로그 표시
2. 로그인 상태 확인 (이중 체크)
3. `removeReport()` API 호출
4. 성공/실패 Toast 메시지 표시
5. 에러 발생 시 콘솔 로그 및 에러 Toast 표시

**주의사항:**
- ✅ `removeReport()` 호출 시 `report.type`, `report.nodeId`, `$user.uid` 정확히 전달
- ✅ 에러 메시지는 i18n 키로 반환되므로 `$t()` 함수로 번역
- ✅ 신고 취소 후 DatabaseListView가 자동으로 실시간 업데이트됨

#### 스타일 요구사항

**컨테이너:**
- 관리자 페이지와 동일

**빈 상태 (로그인 안 됨):**
- `text-align`: `center`
- `padding`: `3rem 1rem`

**신고 취소 버튼:**
- `background-color`: `#ef4444` (빨강)
- `color`: `#ffffff`
- `hover`: `background-color: #dc2626`

**반응형 (모바일):**
- 버튼을 세로로 배치: `flex-direction: column`
- 버튼 너비: `width: 100%`

#### 완전한 구현 코드

**파일:** `src/routes/my/reports/+page.svelte`

```svelte
<script lang="ts">
  /**
   * 내 신고 목록 페이지
   *
   * 현재 로그인한 사용자가 작성한 신고만 createdAt 순서로 표시합니다.
   */
  import { t } from "$lib/stores/i18n.svelte";
  import { authStore } from "$lib/stores/auth.svelte";
  import { goto } from "$app/navigation";
  import { page } from "$app/stores";
  // import DatabaseListView from "$lib/components/DatabaseListView.svelte";
  // import type { ReportWithId } from "$lib/types/report";
  // import { removeReport } from "$lib/services/report";

  /**
   * 신고 사유를 한글로 변환하는 함수
   *
   * @param reason - 신고 사유 (abuse, fake-news, spam, inappropriate, other)
   * @returns 한글 신고 사유
   */
  function getReasonText(reason: string): string {
    return $t(`신고사유_${reason}`);
  }

  /**
   * 신고 타입을 한글로 변환하는 함수
   *
   * @param type - 신고 타입 (post, comment)
   * @returns 한글 신고 타입
   */
  function getTypeText(type: string): string {
    return type === "post" ? $t("게시글") : $t("댓글");
  }

  /**
   * 게시글/댓글로 이동하는 함수
   *
   * @param type - 신고 대상 타입
   * @param nodeId - 신고 대상 ID
   */
  function handleGoToNode(type: string, nodeId: string) {
    if (type === "post") {
      // 게시글 상세 페이지로 이동
      goto(`/post/detail/${nodeId}`);
    } else {
      // 댓글은 게시글 상세 페이지로 이동 (댓글이 속한 게시글로 이동)
      // 댓글 ID로는 직접 이동할 수 없으므로, 게시글 목록으로 이동
      goto("/post/list");
    }
  }

  /**
   * 신고 취소 핸들러
   *
   * @param reportId - 신고 ID
   */
  async function handleCancelReport(reportId: string) {
    // 확인 다이얼로그
    if (!confirm($t("신고를취소하시겠습니까"))) {
      return;
    }

    // 향후 구현:
    // removeReport() API 호출
    // Toast 메시지 표시
    // DatabaseListView 실시간 업데이트
  }
</script>

{#if !authStore.isAuthenticated}
  <!-- 로그인하지 않은 경우 -->
  <div class="my-report-list-page">
    <div class="empty-state">
      <p>{$t("로그인필요")}</p>
      <button class="login-btn" onclick={() => goto("/user/login")}>
        {$t("로그인")}
      </button>
    </div>
  </div>
{:else}
  <!-- 로그인한 경우 -->
  <div class="my-report-list-page">
    <!-- 페이지 헤더 -->
    <div class="page-header">
      <h1 class="page-title">{$t("내_신고_목록")}</h1>
      <p class="page-description">{$t("내가_작성한_신고를_확인할_수_있습니다")}</p>
    </div>

    <!-- 신고 목록 -->
    <!--
      향후 구현:
      DatabaseListView 컴포넌트를 사용하여 실시간 신고 목록 표시
      - path="reports"
      - orderBy="createdAt"
      - limitToFirst={20}
      - filter={(item) => item.uid === authStore.user?.uid}
      - 페이지네이션 및 무한 스크롤 지원
    -->
    <div class="report-list-container">
      <p class="empty-message">신고 목록이 비어있습니다.</p>
    </div>
  </div>
{/if}

<style>
  /* 페이지 컨테이너 */
  .my-report-list-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }

  /* 빈 상태 (로그인 안 됨) */
  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
  }

  .empty-state p {
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
    color: #6b7280;
  }

  .login-btn {
    padding: 0.75rem 2rem;
    background-color: #3b82f6;
    color: #ffffff;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .login-btn:hover {
    background-color: #2563eb;
  }

  /* 페이지 헤더 */
  .page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
  }

  .page-title {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
  }

  .page-description {
    margin: 0;
    font-size: 0.95rem;
    color: #6b7280;
  }

  /* 신고 아이템 */
  .report-item {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: box-shadow 0.2s ease;
  }

  .report-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  /* 신고 헤더 */
  .report-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
  }

  .report-number {
    font-size: 0.85rem;
    font-weight: 700;
    color: #9ca3af;
  }

  .report-type {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #ffffff;
  }

  .report-type.post {
    background-color: #3b82f6;
  }

  .report-type.comment {
    background-color: #10b981;
  }

  .report-date {
    margin-left: auto;
    font-size: 0.8rem;
    color: #9ca3af;
  }

  /* 신고 내용 */
  .report-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .report-info-row {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    min-width: 80px;
  }

  .value {
    font-size: 0.85rem;
    color: #4b5563;
    word-break: break-word;
  }

  .value.reason {
    font-weight: 600;
    color: #dc2626;
  }

  .value.message {
    font-style: italic;
  }

  /* 액션 버튼 */
  .report-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
  }

  .action-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
  }

  .action-btn.go-to-node {
    background-color: #3b82f6;
    color: #ffffff;
  }

  .action-btn.go-to-node:hover {
    background-color: #2563eb;
  }

  .action-btn.cancel-report {
    background-color: #ef4444;
    color: #ffffff;
  }

  .action-btn.cancel-report:hover {
    background-color: #dc2626;
  }

  /* 반응형 스타일 */
  @media (max-width: 768px) {
    .my-report-list-page {
      padding: 1rem 0.5rem;
    }

    .page-title {
      font-size: 1.5rem;
    }

    .report-item {
      padding: 1rem;
    }

    .label {
      min-width: 60px;
      font-size: 0.8rem;
    }

    .value {
      font-size: 0.8rem;
    }

    .report-actions {
      flex-direction: column;
    }

    .action-btn {
      width: 100%;
    }
  }
</style>
```

---

### 6. 라우팅 사양

#### SvelteKit 파일 기반 자동 라우팅

**핵심 개념:** SvelteKit은 파일 시스템 기반 라우팅을 사용합니다. `src/routes/` 디렉토리 구조가 자동으로 URL 경로가 됩니다.

**라우트 파일:**
| 파일 경로 | 자동 생성 경로 | 설명 |
|----------|-------------|------|
| `src/routes/admin/reports/+page.svelte` | `/admin/reports` | 관리자 신고 목록 페이지 |
| `src/routes/my/reports/+page.svelte` | `/my/reports` | 사용자 신고 목록 페이지 |

**레이아웃 파일 (선택사항):**
| 파일 경로 | 적용 범위 | 설명 |
|----------|---------|------|
| `src/routes/admin/+layout.svelte` | `/admin/*` 모든 경로 | 관리자 페이지 공통 레이아웃 |
| `src/routes/my/+layout.svelte` | `/my/*` 모든 경로 | 사용자 페이지 공통 레이아웃 |

**중요:** 명시적인 라우팅 설정이 필요 없습니다. 파일을 생성하는 것만으로 자동으로 라우트가 생성됩니다.

#### 네비게이션 메뉴 업데이트

**파일 위치:** `src/lib/components/top-bar.svelte` 또는 유사 네비게이션 컴포넌트

**추가할 메뉴 항목:**
```svelte
<!-- 관리자 신고 목록 (관리자만 표시) -->
{#if authStore.isAdmin}
  <a href="/admin/reports" class="nav-link">
    {$t('관리자_신고_목록')}
  </a>
{/if}

<!-- 내 신고 목록 (로그인한 사용자만 표시) -->
{#if authStore.isAuthenticated}
  <a href="/my/reports" class="nav-link">
    {$t('내_신고_목록')}
  </a>
{/if}
```

**주의사항:**
- ✅ 관리자 신고 목록은 관리자(`authStore.isAdmin`)만 접근 가능하도록 조건부 렌더링
- ✅ 내 신고 목록은 로그인한 사용자(`authStore.isAuthenticated`)만 접근 가능하도록 조건부 렌더링
- ✅ 페이지 내부에서도 추가적인 권한 검사를 수행하는 것 권장
- ✅ SvelteKit 파일 기반 라우팅은 명시적인 라우트 설정 불필요

---

### 7. 다국어 지원 (i18n)

#### 한국어 (ko.json)

**파일 위치:** `public/locales/ko.json`

**추가할 키:**
```json
{
  "관리자_신고_목록": "관리자 신고 목록",
  "모든_사용자의_신고를_확인할_수_있습니다": "모든 사용자의 신고를 확인할 수 있습니다",
  "내_신고_목록": "내 신고 목록",
  "내가_작성한_신고를_확인할_수_있습니다": "내가 작성한 신고를 확인할 수 있습니다",
  "신고자": "신고자",
  "대상ID": "대상 ID",
  "신고사유": "신고 사유",
  "상세메시지": "상세 메시지",
  "대상_보기": "대상 보기",
  "신고_취소": "신고 취소",
  "신고를취소하시겠습니까": "신고를 취소하시겠습니까?",
  "신고가취소되었습니다": "신고가 취소되었습니다",
  "게시글": "게시글",
  "댓글": "댓글",
  "신고사유_abuse": "욕설, 시비, 모욕, 명예훼손",
  "신고사유_fake-news": "가짜 뉴스, 잘못된 정보",
  "신고사유_spam": "스팸, 악용",
  "신고사유_inappropriate": "카테고리에 맞지 않는 글 등록",
  "신고사유_other": "기타"
}
```

#### 영어 (en.json)

**파일 위치:** `public/locales/en.json`

**추가할 키:**
```json
{
  "관리자_신고_목록": "Admin Report List",
  "모든_사용자의_신고를_확인할_수_있습니다": "You can view all user reports",
  "내_신고_목록": "My Reports",
  "내가_작성한_신고를_확인할_수_있습니다": "You can view your reports",
  "신고자": "Reporter",
  "대상ID": "Target ID",
  "신고사유": "Reason",
  "상세메시지": "Message",
  "대상_보기": "View Target",
  "신고_취소": "Cancel Report",
  "신고를취소하시겠습니까": "Do you want to cancel this report?",
  "신고가취소되었습니다": "Report has been cancelled",
  "게시글": "Post",
  "댓글": "Comment",
  "신고사유_abuse": "Abuse, Harassment, Defamation",
  "신고사유_fake-news": "Fake News, Misinformation",
  "신고사유_spam": "Spam, Abuse",
  "신고사유_inappropriate": "Inappropriate Category",
  "신고사유_other": "Other"
}
```

**일본어 및 중국어는 별도 문서를 참조하거나 동일한 구조로 추가하세요.**

---

### 8. 테스트 사양

#### 유닛 테스트

**테스트 파일:** `tests/unit/report-list.test.ts`

**테스트 케이스:**

1. **getReasonText() 함수 테스트**
   - ✅ `'abuse'` → "욕설, 시비, 모욕, 명예훼손"
   - ✅ `'fake-news'` → "가짜 뉴스, 잘못된 정보"
   - ✅ `'spam'` → "스팸, 악용"
   - ✅ `'inappropriate'` → "카테고리에 맞지 않는 글 등록"
   - ✅ `'other'` → "기타"

2. **getTypeText() 함수 테스트**
   - ✅ `'post'` → "게시글"
   - ✅ `'comment'` → "댓글"

3. **handleGoToNode() 함수 테스트**
   - ✅ 게시글 신고: `/post/detail/{nodeId}` 경로로 이동
   - ✅ 댓글 신고: `/post/list` 경로로 이동

4. **handleCancelReport() 함수 테스트**
   - ✅ 확인 다이얼로그 표시
   - ✅ `removeReport()` API 호출
   - ✅ 성공 시 Toast 메시지 표시
   - ✅ 실패 시 에러 Toast 메시지 표시

#### E2E 테스트 (Playwright)

**테스트 파일:** `tests/e2e/report-list.spec.ts`

**테스트 시나리오:**

1. **관리자 신고 목록 E2E**
   - 관리자 로그인
   - `/admin/reports` 페이지 이동
   - 신고 목록 렌더링 확인
   - 신고 아이템 클릭하여 게시글로 이동 확인

2. **사용자 신고 목록 E2E**
   - 사용자 로그인
   - `/my/reports` 페이지 이동
   - 내 신고만 필터링되었는지 확인
   - "신고_취소" 버튼 클릭
   - 확인 다이얼로그 확인
   - Toast 메시지 표시 확인
   - 신고 목록에서 제거됨 확인

3. **로그인하지 않은 상태 E2E**
   - 로그아웃 상태
   - `/my/reports` 페이지 이동
   - "로그인이 필요합니다" 메시지 확인
   - "로그인" 버튼 클릭
   - `/user/login` 페이지로 이동 확인

---

## 참고 문서

**필수 참고 문서:**
- **[sonub-firebase-database.md](./sonub-firebase-database.md)** - Firebase 데이터베이스 전체 구조
- **[sonub-user-overview.md](./sonub-user-overview.md)** - 사용자 관리 시스템 설계
- **[sonub-setup-firebase.md](./sonub-setup-firebase.md)** - Firebase 기본 설정
- **[sonub-setup-shadcn.md](./sonub-setup-shadcn.md)** - shadcn-svelte UI 컴포넌트

**추가 참고 문서:**
- [SvelteKit 공식 문서](https://kit.svelte.dev/docs/introduction)
- [Svelte 5 공식 문서](https://svelte.dev/docs/svelte/overview)
- [Firebase Realtime Database 공식 문서](https://firebase.google.com/docs/database)
- [Playwright 공식 문서](https://playwright.dev/)
- [shadcn-svelte 공식 문서](https://www.shadcn-svelte.com/)
