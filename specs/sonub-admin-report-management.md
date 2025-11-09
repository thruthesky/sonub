---
name: sonub-admin-report-management
version: 1.0.0
description: 신고된 게시글 및 댓글 관리 기능 - 관리자 및 사용자 신고 목록 표시
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-09
updated: 2025-01-09
step: 40
priority: "**"
dependencies:
  - sonub-setup-firebase.md
  - sonub-firebase-database.md
  - sonub-user-login.md
  - sonub-design-workflow.md
  - sonub-setup-svelte.md
tags: ["admin", "report", "moderation", "firebase", "sveltekit5"]
---

# Sonub Admin Report Management - 신고 관리 기능

## 목차

- [Sonub Admin Report Management - 신고 관리 기능](#sonub-admin-report-management---신고-관리-기능)
  - [목차](#목차)
  - [1. 개요](#1-개요)
    - [1.1 목적](#11-목적)
    - [1.2 범위](#12-범위)
    - [1.3 사전 요구사항](#13-사전-요구사항)
    - [1.4 제외 사항](#14-제외-사항)
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
      - [App.svelte 라우팅](#appsvelte-라우팅)
      - [Menu.svelte 메뉴 항목](#menusvelte-메뉴-항목)
    - [7. 다국어 지원 (i18n)](#7-다국어-지원-i18n)
      - [한국어 (ko.json)](#한국어-kojson)
      - [영어 (en.json)](#영어-enjson)
    - [8. 테스트 사양](#8-테스트-사양)
      - [유닛 테스트](#유닛-테스트)
      - [E2E 테스트 (Playwright)](#e2e-테스트-playwright)
  - [참고 문서](#참고-문서)

---

## 1. 개요

### 1.1 목적

본 명세서는 Sonub 프로젝트에서 **신고된 게시글 및 댓글을 관리하는 기능**을 정의합니다. 관리자는 모든 신고를 확인할 수 있고, 사용자는 자신이 작성한 신고를 확인하고 취소할 수 있습니다.

### 1.2 범위

- ✅ **관리자 신고 목록 페이지** (`/admin/reports`): 모든 신고를 시간순으로 표시
- ✅ **사용자 신고 목록 페이지** (`/my/reports`): 현재 사용자의 신고만 필터링하여 표시
- ✅ **신고 취소 기능**: 사용자는 자신의 신고를 취소 가능
- ✅ **대상 보기 기능**: 신고된 게시글/댓글로 이동
- ✅ **실시간 업데이트**: Firebase Realtime Database 사용
- ✅ **반응형 디자인**: TailwindCSS 사용
- ✅ **다국어 지원**: Paraglide i18n 사용
- ✅ **shadcn-svelte 컴포넌트**: Card, Button 등 활용

### 1.3 사전 요구사항

- ✅ Firebase 프로젝트 설정 완료 ([sonub-setup-firebase.md](./sonub-setup-firebase.md) 참조)
- ✅ Firebase Realtime Database 활성화
- ✅ TailwindCSS 설치 완료 ([sonub-design-workflow.md](./sonub-design-workflow.md) 참조)
- ✅ shadcn-svelte 설치 완료 ([sonub-setup-shadcn.md](./sonub-setup-shadcn.md) 참조)
- ✅ Paraglide i18n 설정 완료 ([sonub-setup-svelte.md](./sonub-setup-svelte.md) 참조)
- ✅ Firebase Authentication 활성화 ([sonub-user-login.md](./sonub-user-login.md) 참조)
- ✅ SvelteKit 5 프로젝트 환경

### 1.4 제외 사항

- ❌ 신고 생성 기능 (별도 명세서에서 정의)
- ❌ 신고 처리 및 조치 기능 (별도 명세서에서 정의)
- ❌ 신고 통계 및 분석 기능

---

## 2. 데이터베이스 구조

### 2.1 신고 데이터 경로

Firebase Realtime Database에서 신고 데이터는 다음 경로에 저장됩니다:

```
/reports/{reportId}
```

**경로 설명:**
- `/reports/`: 모든 신고 데이터의 루트 노드
- `{reportId}`: 신고 고유 ID (자동 생성 키 또는 커스텀 ID)

### 2.2 신고 데이터 구조

각 신고는 다음 필드를 포함합니다:

```typescript
{
  uid: string;              // 신고자 UID
  displayName: string;      // 신고자 닉네임
  photoURL: string;         // 신고자 프로필 사진 URL
  type: 'post' | 'comment'; // 신고 대상 타입
  targetId: string;         // 신고된 게시글/댓글 ID
  targetUid: string;        // 신고된 게시글/댓글 작성자 UID
  reason: 'spam' | 'abuse' | 'harassment' | 'inappropriate' | 'other'; // 신고 사유
  description?: string;     // 추가 설명 (선택)
  createdAt: number;        // 신고 생성 시간 (timestamp)
}
```

**필드 설명:**
- `uid`: 신고를 제출한 사용자의 Firebase Authentication UID
- `displayName`: 신고자의 표시 이름
- `photoURL`: 신고자의 프로필 사진 URL (옵션)
- `type`: 신고 대상이 게시글인지 댓글인지 구분
- `targetId`: 신고된 게시글 또는 댓글의 고유 ID
- `targetUid`: 신고된 콘텐츠를 작성한 사용자의 UID
- `reason`: 신고 사유 (스팸, 욕설, 괴롭힘, 부적절한 콘텐츠, 기타)
- `description`: 사용자가 추가로 작성한 설명 (선택 사항)
- `createdAt`: 신고가 생성된 시간 (밀리초 단위 timestamp)

### 2.3 키 형식 규칙

신고 ID(`reportId`)는 Firebase의 `push()` 메서드로 자동 생성되거나, 다음 형식을 따를 수 있습니다:

```
{uid}_{targetId}_{timestamp}
```

**예시:**
```
abc123_post_xyz789_1704844800000
```

---

## 3. 타입 정의

### 3.1 파일 위치

```
src/lib/types/report.ts
```

### 3.2 ReportType 타입

신고 대상 타입을 정의합니다.

```typescript
export type ReportType = 'post' | 'comment';
```

### 3.3 ReportReason 타입

신고 사유를 정의합니다.

```typescript
export type ReportReason = 'spam' | 'abuse' | 'harassment' | 'inappropriate' | 'other';
```

### 3.4 Report 인터페이스

Firebase에 저장되는 신고 데이터 구조입니다.

```typescript
export interface Report {
  uid: string;
  displayName: string;
  photoURL?: string;
  type: ReportType;
  targetId: string;
  targetUid: string;
  reason: ReportReason;
  description?: string;
  createdAt: number;
}
```

### 3.5 ReportWithId 인터페이스

Firebase에서 조회한 신고 데이터에 ID를 포함한 구조입니다.

```typescript
export interface ReportWithId extends Report {
  id: string;
}
```

---

## 4. 서비스 API

### 4.1 파일 위치

```
src/lib/services/report.service.ts
```

### 4.2 checkReportStatus() 함수

특정 대상(게시글 또는 댓글)에 대해 현재 사용자가 이미 신고했는지 확인합니다.

**함수 시그니처:**

```typescript
export async function checkReportStatus(
  targetId: string,
  uid: string
): Promise<boolean>
```

**파라미터:**
- `targetId`: 확인할 대상의 ID (게시글 또는 댓글)
- `uid`: 현재 사용자의 UID

**반환값:**
- `true`: 이미 신고한 경우
- `false`: 신고하지 않은 경우

**구현 예시:**

```typescript
import { ref as dbRef, query, orderByChild, equalTo, get } from 'firebase/database';
import { database } from '$lib/firebase';

export async function checkReportStatus(targetId: string, uid: string): Promise<boolean> {
  const reportsRef = dbRef(database, 'reports');
  const q = query(reportsRef, orderByChild('targetId'), equalTo(targetId));
  
  const snapshot = await get(q);
  
  if (!snapshot.exists()) {
    return false;
  }
  
  const reports = snapshot.val();
  return Object.values(reports).some((report: any) => report.uid === uid);
}
```

### 4.3 removeReport() 함수

특정 신고를 삭제합니다.

**함수 시그니처:**

```typescript
export async function removeReport(reportId: string): Promise<void>
```

**파라미터:**
- `reportId`: 삭제할 신고의 ID

**반환값:**
- `Promise<void>`: 삭제 완료 시 resolve

**구현 예시:**

```typescript
import { ref as dbRef, remove } from 'firebase/database';
import { database } from '$lib/firebase';

export async function removeReport(reportId: string): Promise<void> {
  const reportRef = dbRef(database, `reports/${reportId}`);
  await remove(reportRef);
}
```

---

## 5. 관리자 신고 목록 페이지

### 5.1 라우트 및 파일 위치

**라우트:**
```
/admin/reports
```

**파일 위치:**
```
src/routes/admin/reports/+page.svelte
```

### 5.2 페이지 구조

관리자 신고 목록 페이지는 다음 구조를 따릅니다:

1. **페이지 제목**: "신고 관리" (Paraglide i18n 사용)
2. **신고 목록**:
   - shadcn Card 컴포넌트로 각 신고 표시
   - 신고자 정보 (프로필 사진, 닉네임)
   - 신고 대상 타입 (게시글/댓글)
   - 신고 사유
   - 신고 시간 (상대 시간 표시)
   - "대상 보기" 버튼
3. **빈 상태**: 신고가 없을 경우 안내 메시지 표시

### 5.3 구현 코드

```svelte
<script lang="ts">
  import { onMount } from 'svelte';
  import { ref as dbRef, onValue, off } from 'firebase/database';
  import { database } from '$lib/firebase';
  import { Card } from '$lib/components/ui/card';
  import { Button } from '$lib/components/ui/button';
  import * as m from '$lib/paraglide/messages';
  import type { ReportWithId } from '$lib/types/report';

  let reports = $state<ReportWithId[]>([]);
  let loading = $state(true);

  onMount(() => {
    const reportsRef = dbRef(database, 'reports');
    
    const unsubscribe = onValue(reportsRef, (snapshot) => {
      if (snapshot.exists()) {
        const data = snapshot.val();
        reports = Object.entries(data)
          .map(([id, report]) => ({ id, ...(report as any) }))
          .sort((a, b) => b.createdAt - a.createdAt);
      } else {
        reports = [];
      }
      loading = false;
    });

    return () => {
      off(reportsRef);
    };
  });

  function getReasonText(reason: string): string {
    switch (reason) {
      case 'spam':
        return m.report_reason_spam();
      case 'abuse':
        return m.report_reason_abuse();
      case 'harassment':
        return m.report_reason_harassment();
      case 'inappropriate':
        return m.report_reason_inappropriate();
      case 'other':
        return m.report_reason_other();
      default:
        return reason;
    }
  }

  function getTypeText(type: string): string {
    return type === 'post' ? m.report_type_post() : m.report_type_comment();
  }

  function formatTime(timestamp: number): string {
    const now = Date.now();
    const diff = now - timestamp;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return m.time_just_now();
    if (minutes < 60) return m.time_minutes_ago({ minutes });
    if (hours < 24) return m.time_hours_ago({ hours });
    return m.time_days_ago({ days });
  }

  function handleViewTarget(report: ReportWithId) {
    // 신고된 게시글/댓글로 이동하는 로직
    const path = report.type === 'post' 
      ? `/posts/${report.targetId}` 
      : `/comments/${report.targetId}`;
    window.location.href = path;
  }
</script>

<div class="container mx-auto p-4 max-w-4xl">
  <h1 class="text-3xl font-bold mb-6">{m.admin_reports_title()}</h1>

  {#if loading}
    <div class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
    </div>
  {:else if reports.length === 0}
    <Card className="p-8 text-center">
      <p class="text-gray-500">{m.admin_reports_empty()}</p>
    </Card>
  {:else}
    <div class="space-y-4">
      {#each reports as report (report.id)}
        <Card className="p-4">
          <div class="flex items-start gap-4">
            {#if report.photoURL}
              <img 
                src={report.photoURL} 
                alt={report.displayName}
                class="w-12 h-12 rounded-full object-cover"
              />
            {:else}
              <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                <span class="text-gray-600 font-semibold">
                  {report.displayName[0]?.toUpperCase() || '?'}
                </span>
              </div>
            {/if}

            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="font-semibold">{report.displayName}</span>
                <span class="text-sm text-gray-500">·</span>
                <span class="text-sm text-gray-500">{formatTime(report.createdAt)}</span>
              </div>

              <div class="mb-2">
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 mr-2">
                  {getTypeText(report.type)}
                </span>
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                  {getReasonText(report.reason)}
                </span>
              </div>

              {#if report.description}
                <p class="text-sm text-gray-700 mb-3">{report.description}</p>
              {/if}

              <Button 
                variant="outline" 
                size="sm"
                onclick={() => handleViewTarget(report)}
              >
                {m.admin_reports_view_target()}
              </Button>
            </div>
          </div>
        </Card>
      {/each}
    </div>
  {/if}
</div>
```

---

## 6. 사용자 신고 목록 페이지

### 6.1 라우트 및 파일 위치

**라우트:**
```
/my/reports
```

**파일 위치:**
```
src/routes/my/reports/+page.svelte
```

### 6.2 페이지 구조

사용자 신고 목록 페이지는 다음 구조를 따릅니다:

1. **페이지 제목**: "내 신고 내역" (Paraglide i18n 사용)
2. **로그인 체크**: 비로그인 시 로그인 페이지로 리다이렉트
3. **신고 목록**:
   - 현재 사용자가 작성한 신고만 필터링
   - shadcn Card 컴포넌트로 각 신고 표시
   - 신고 대상 타입 (게시글/댓글)
   - 신고 사유
   - 신고 시간
   - "대상 보기" 버튼
   - "신고 취소" 버튼
4. **빈 상태**: 신고가 없을 경우 안내 메시지 표시

### 6.3 구현 코드

```svelte
<script lang="ts">
  import { onMount } from 'svelte';
  import { ref as dbRef, onValue, off } from 'firebase/database';
  import { database, auth } from '$lib/firebase';
  import { authStore } from '$lib/stores/auth.svelte';
  import { Card } from '$lib/components/ui/card';
  import { Button } from '$lib/components/ui/button';
  import * as m from '$lib/paraglide/messages';
  import { removeReport } from '$lib/services/report.service';
  import type { ReportWithId } from '$lib/types/report';

  let reports = $state<ReportWithId[]>([]);
  let loading = $state(true);
  let removing = $state<string | null>(null);

  onMount(() => {
    if (!authStore.user) {
      window.location.href = '/user/login';
      return;
    }

    const reportsRef = dbRef(database, 'reports');
    
    const unsubscribe = onValue(reportsRef, (snapshot) => {
      if (snapshot.exists()) {
        const data = snapshot.val();
        reports = Object.entries(data)
          .map(([id, report]) => ({ id, ...(report as any) }))
          .filter((report) => report.uid === authStore.user?.uid)
          .sort((a, b) => b.createdAt - a.createdAt);
      } else {
        reports = [];
      }
      loading = false;
    });

    return () => {
      off(reportsRef);
    };
  });

  function getReasonText(reason: string): string {
    switch (reason) {
      case 'spam':
        return m.report_reason_spam();
      case 'abuse':
        return m.report_reason_abuse();
      case 'harassment':
        return m.report_reason_harassment();
      case 'inappropriate':
        return m.report_reason_inappropriate();
      case 'other':
        return m.report_reason_other();
      default:
        return reason;
    }
  }

  function getTypeText(type: string): string {
    return type === 'post' ? m.report_type_post() : m.report_type_comment();
  }

  function formatTime(timestamp: number): string {
    const now = Date.now();
    const diff = now - timestamp;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return m.time_just_now();
    if (minutes < 60) return m.time_minutes_ago({ minutes });
    if (hours < 24) return m.time_hours_ago({ hours });
    return m.time_days_ago({ days });
  }

  function handleViewTarget(report: ReportWithId) {
    const path = report.type === 'post' 
      ? `/posts/${report.targetId}` 
      : `/comments/${report.targetId}`;
    window.location.href = path;
  }

  async function handleRemoveReport(reportId: string) {
    if (!confirm(m.my_reports_confirm_cancel())) {
      return;
    }

    removing = reportId;
    try {
      await removeReport(reportId);
      // Firebase onValue로 자동 업데이트됨
    } catch (error) {
      console.error('Failed to remove report:', error);
      alert(m.my_reports_cancel_failed());
    } finally {
      removing = null;
    }
  }
</script>

<div class="container mx-auto p-4 max-w-4xl">
  <h1 class="text-3xl font-bold mb-6">{m.my_reports_title()}</h1>

  {#if loading}
    <div class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
    </div>
  {:else if reports.length === 0}
    <Card className="p-8 text-center">
      <p class="text-gray-500">{m.my_reports_empty()}</p>
    </Card>
  {:else}
    <div class="space-y-4">
      {#each reports as report (report.id)}
        <Card className="p-4">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-sm text-gray-500">{formatTime(report.createdAt)}</span>
              </div>

              <div class="mb-2">
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 mr-2">
                  {getTypeText(report.type)}
                </span>
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                  {getReasonText(report.reason)}
                </span>
              </div>

              {#if report.description}
                <p class="text-sm text-gray-700 mb-3">{report.description}</p>
              {/if}

              <div class="flex gap-2">
                <Button 
                  variant="outline" 
                  size="sm"
                  onclick={() => handleViewTarget(report)}
                >
                  {m.my_reports_view_target()}
                </Button>
                <Button 
                  variant="destructive" 
                  size="sm"
                  disabled={removing === report.id}
                  onclick={() => handleRemoveReport(report.id)}
                >
                  {removing === report.id ? m.my_reports_canceling() : m.my_reports_cancel()}
                </Button>
              </div>
            </div>
          </div>
        </Card>
      {/each}
    </div>
  {/if}
</div>
```

---

## 7. 다국어 지원

Paraglide i18n을 사용하여 다음 메시지 키를 추가합니다.

**메시지 파일 위치:**
```
messages/ko.json
messages/en.json
messages/ja.json
messages/zh.json
```

**한국어 (ko.json):**

```json
{
  "admin_reports_title": "신고 관리",
  "admin_reports_empty": "신고가 없습니다.",
  "admin_reports_view_target": "대상 보기",
  "my_reports_title": "내 신고 내역",
  "my_reports_empty": "신고 내역이 없습니다.",
  "my_reports_view_target": "대상 보기",
  "my_reports_cancel": "신고 취소",
  "my_reports_canceling": "취소 중...",
  "my_reports_confirm_cancel": "정말 신고를 취소하시겠습니까?",
  "my_reports_cancel_failed": "신고 취소에 실패했습니다.",
  "report_reason_spam": "스팸",
  "report_reason_abuse": "욕설",
  "report_reason_harassment": "괴롭힘",
  "report_reason_inappropriate": "부적절한 콘텐츠",
  "report_reason_other": "기타",
  "report_type_post": "게시글",
  "report_type_comment": "댓글",
  "time_just_now": "방금",
  "time_minutes_ago": "{minutes}분 전",
  "time_hours_ago": "{hours}시간 전",
  "time_days_ago": "{days}일 전"
}
```

**영어 (en.json):**

```json
{
  "admin_reports_title": "Report Management",
  "admin_reports_empty": "No reports found.",
  "admin_reports_view_target": "View Target",
  "my_reports_title": "My Reports",
  "my_reports_empty": "You haven't submitted any reports.",
  "my_reports_view_target": "View Target",
  "my_reports_cancel": "Cancel Report",
  "my_reports_canceling": "Canceling...",
  "my_reports_confirm_cancel": "Are you sure you want to cancel this report?",
  "my_reports_cancel_failed": "Failed to cancel report.",
  "report_reason_spam": "Spam",
  "report_reason_abuse": "Abusive Language",
  "report_reason_harassment": "Harassment",
  "report_reason_inappropriate": "Inappropriate Content",
  "report_reason_other": "Other",
  "report_type_post": "Post",
  "report_type_comment": "Comment",
  "time_just_now": "Just now",
  "time_minutes_ago": "{minutes} minutes ago",
  "time_hours_ago": "{hours} hours ago",
  "time_days_ago": "{days} days ago"
}
```

---

## 8. 테스트

### 8.1 유닛 테스트

**테스트 파일 위치:**
```
src/lib/services/report.service.spec.ts
```

**테스트 케이스:**

```typescript
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { ref as dbRef, set, remove } from 'firebase/database';
import { database } from '$lib/firebase';
import { checkReportStatus, removeReport } from './report.service';

describe('Report Service', () => {
  const testReportId = 'test-report-123';
  const testTargetId = 'test-post-456';
  const testUid = 'test-user-789';

  beforeEach(async () => {
    // 테스트 데이터 생성
    const reportRef = dbRef(database, `reports/${testReportId}`);
    await set(reportRef, {
      uid: testUid,
      displayName: 'Test User',
      type: 'post',
      targetId: testTargetId,
      targetUid: 'target-user-123',
      reason: 'spam',
      createdAt: Date.now()
    });
  });

  afterEach(async () => {
    // 테스트 데이터 삭제
    const reportRef = dbRef(database, `reports/${testReportId}`);
    await remove(reportRef);
  });

  it('should check if user has reported', async () => {
    const hasReported = await checkReportStatus(testTargetId, testUid);
    expect(hasReported).toBe(true);
  });

  it('should return false if user has not reported', async () => {
    const hasReported = await checkReportStatus(testTargetId, 'different-user');
    expect(hasReported).toBe(false);
  });

  it('should remove report', async () => {
    await removeReport(testReportId);
    const hasReported = await checkReportStatus(testTargetId, testUid);
    expect(hasReported).toBe(false);
  });
});
```

### 8.2 E2E 테스트

**테스트 파일 위치:**
```
e2e/admin-reports.test.ts
e2e/my-reports.test.ts
```

**관리자 신고 목록 테스트 (admin-reports.test.ts):**

```typescript
import { test, expect } from '@playwright/test';

test.describe('Admin Report List', () => {
  test.beforeEach(async ({ page }) => {
    // 관리자로 로그인
    await page.goto('/user/login');
    await page.fill('input[type="email"]', 'admin@test.com');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL('/');
  });

  test('should display all reports', async ({ page }) => {
    await page.goto('/admin/reports');
    
    // 신고 목록이 표시되는지 확인
    const reportCards = page.locator('[data-testid="report-card"]');
    await expect(reportCards.first()).toBeVisible();
  });

  test('should show report details', async ({ page }) => {
    await page.goto('/admin/reports');
    
    // 첫 번째 신고 카드 확인
    const firstReport = page.locator('[data-testid="report-card"]').first();
    await expect(firstReport.locator('[data-testid="report-type"]')).toBeVisible();
    await expect(firstReport.locator('[data-testid="report-reason"]')).toBeVisible();
  });

  test('should navigate to target', async ({ page }) => {
    await page.goto('/admin/reports');
    
    // "대상 보기" 버튼 클릭
    await page.click('[data-testid="view-target-button"]');
    
    // URL이 변경되는지 확인
    await page.waitForURL(/\/(posts|comments)\/.+/);
  });
});
```

**사용자 신고 목록 테스트 (my-reports.test.ts):**

```typescript
import { test, expect } from '@playwright/test';

test.describe('My Report List', () => {
  test.beforeEach(async ({ page }) => {
    // 일반 사용자로 로그인
    await page.goto('/user/login');
    await page.fill('input[type="email"]', 'user@test.com');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL('/');
  });

  test('should display only my reports', async ({ page }) => {
    await page.goto('/my/reports');
    
    // 내 신고만 표시되는지 확인
    const reportCards = page.locator('[data-testid="report-card"]');
    await expect(reportCards.first()).toBeVisible();
  });

  test('should cancel report', async ({ page }) => {
    await page.goto('/my/reports');
    
    // 첫 번째 신고의 ID 저장
    const reportCount = await page.locator('[data-testid="report-card"]').count();
    
    // "신고 취소" 버튼 클릭
    page.on('dialog', dialog => dialog.accept());
    await page.click('[data-testid="cancel-report-button"]');
    
    // 신고가 삭제되었는지 확인
    await page.waitForTimeout(1000);
    const newReportCount = await page.locator('[data-testid="report-card"]').count();
    expect(newReportCount).toBe(reportCount - 1);
  });

  test('should redirect to login if not authenticated', async ({ page }) => {
    // 로그아웃
    await page.goto('/user/logout');
    
    // /my/reports 접근 시도
    await page.goto('/my/reports');
    
    // 로그인 페이지로 리다이렉트되는지 확인
    await expect(page).toHaveURL('/user/login');
  });
});
```

---

## 9. 참고 문서

- [Firebase Realtime Database](https://firebase.google.com/docs/database)
- [SvelteKit 5 Documentation](https://kit.svelte.dev/docs)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [shadcn-svelte Documentation](https://www.shadcn-svelte.com/)
- [Paraglide i18n Documentation](https://inlang.com/m/gerre34r/library-inlang-paraglideJs)
- [Playwright Testing](https://playwright.dev/)
- [Vitest Documentation](https://vitest.dev/)
- [sonub-setup-firebase.md](./sonub-setup-firebase.md)
- [sonub-firebase-database.md](./sonub-firebase-database.md)
- [sonub-user-login.md](./sonub-user-login.md)
- [sonub-design-workflow.md](./sonub-design-workflow.md)
- [sonub-setup-svelte.md](./sonub-setup-svelte.md)
- [sonub-setup-shadcn.md](./sonub-setup-shadcn.md)

#### 신고 데이터 경로

**Firebase Realtime Database 경로:**
```
/reports/
```

**📚 상세 데이터베이스 구조는 [snsweb-firebase-database.md](./snsweb-firebase-database.md#신고-reports)를 참조하세요.**

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

**파일명:** `src/demo/AdminReportListPage.svelte`

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

**파일:** `src/demo/AdminReportListPage.svelte`

```svelte
<script lang="ts">
  /**
   * 관리자 신고 목록 페이지
   *
   * 모든 사용자의 신고를 createdAt 순서로 표시합니다.
   * 관리자만 접근 가능합니다.
   */
  import { t } from "../lib/stores/i18n";
  import DatabaseListView from "../lib/components/DatabaseListView.svelte";
  import type { ReportWithId } from "../lib/types/report";
  import { navigate } from "../lib/utils/navigation";

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
   * @param report - 신고 데이터
   */
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
</script>

<div class="admin-report-list-page">
  <!-- 페이지 헤더 -->
  <div class="page-header">
    <h1 class="page-title">{$t("관리자_신고_목록")}</h1>
    <p class="page-description">{$t("모든_사용자의_신고를_확인할_수_있습니다")}</p>
  </div>

  <!-- 신고 목록 -->
  <DatabaseListView
    path="reports"
    orderBy="createdAt"
    limitToFirst={20}
    let:item
    let:index
  >
    {@const report = item as ReportWithId}
    <div class="report-item">
      <!-- 신고 번호 및 타입 -->
      <div class="report-header">
        <span class="report-number">#{index + 1}</span>
        <span class="report-type {report.type}">{getTypeText(report.type)}</span>
        <span class="report-date">
          {new Date(report.createdAt).toLocaleDateString("ko-KR", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
          })}
        </span>
      </div>

      <!-- 신고 내용 -->
      <div class="report-content">
        <div class="report-info-row">
          <span class="label">{$t("신고자")}:</span>
          <span class="value">{report.uid}</span>
        </div>

        <div class="report-info-row">
          <span class="label">{$t("대상ID")}:</span>
          <span class="value">{report.nodeId}</span>
        </div>

        <div class="report-info-row">
          <span class="label">{$t("신고사유")}:</span>
          <span class="value reason">{getReasonText(report.reason)}</span>
        </div>

        {#if report.message}
          <div class="report-info-row">
            <span class="label">{$t("상세메시지")}:</span>
            <span class="value message">{report.message}</span>
          </div>
        {/if}
      </div>

      <!-- 액션 버튼 -->
      <div class="report-actions">
        <button class="action-btn go-to-node" onclick={() => handleGoToNode(report)}>
          {$t("대상_보기")}
        </button>
      </div>
    </div>
  </DatabaseListView>
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

**파일명:** `src/demo/MyReportListPage.svelte`

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
  filter={(item) => item.uid === $user?.uid}
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

**파일:** `src/demo/MyReportListPage.svelte`

```svelte
<script lang="ts">
  /**
   * 내 신고 목록 페이지
   *
   * 현재 로그인한 사용자가 작성한 신고만 createdAt 순서로 표시합니다.
   */
  import { t } from "../lib/stores/i18n";
  import { user } from "../lib/stores/auth";
  import DatabaseListView from "../lib/components/DatabaseListView.svelte";
  import type { ReportWithId } from "../lib/types/report";
  import { navigate } from "../lib/utils/navigation";
  import { removeReport } from "../lib/services/report";
  import { showToast } from "../lib/stores/toast";

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
   * @param report - 신고 데이터
   */
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

  /**
   * 신고 취소 핸들러
   *
   * @param report - 신고 데이터
   */
  async function handleCancelReport(report: ReportWithId) {
    // 확인 다이얼로그
    if (!confirm($t("신고를취소하시겠습니까"))) {
      return;
    }

    if (!$user) {
      showToast($t("로그인필요"), "error");
      return;
    }

    try {
      const result = await removeReport(report.type, report.nodeId, $user.uid);

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
</script>

{#if !$user}
  <!-- 로그인하지 않은 경우 -->
  <div class="my-report-list-page">
    <div class="empty-state">
      <p>{$t("로그인필요")}</p>
      <button class="login-btn" onclick={() => navigate("/user/login")}>
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
    <DatabaseListView
      path="reports"
      orderBy="createdAt"
      limitToFirst={20}
      filter={(item) => item.uid === $user?.uid}
      let:item
      let:index
    >
      {@const report = item as ReportWithId}
      <div class="report-item">
        <!-- 신고 번호 및 타입 -->
        <div class="report-header">
          <span class="report-number">#{index + 1}</span>
          <span class="report-type {report.type}">{getTypeText(report.type)}</span>
          <span class="report-date">
            {new Date(report.createdAt).toLocaleDateString("ko-KR", {
              year: "numeric",
              month: "2-digit",
              day: "2-digit",
              hour: "2-digit",
              minute: "2-digit",
            })}
          </span>
        </div>

        <!-- 신고 내용 -->
        <div class="report-content">
          <div class="report-info-row">
            <span class="label">{$t("대상ID")}:</span>
            <span class="value">{report.nodeId}</span>
          </div>

          <div class="report-info-row">
            <span class="label">{$t("신고사유")}:</span>
            <span class="value reason">{getReasonText(report.reason)}</span>
          </div>

          {#if report.message}
            <div class="report-info-row">
              <span class="label">{$t("상세메시지")}:</span>
              <span class="value message">{report.message}</span>
            </div>
          {/if}
        </div>

        <!-- 액션 버튼 -->
        <div class="report-actions">
          <button class="action-btn go-to-node" onclick={() => handleGoToNode(report)}>
            {$t("대상_보기")}
          </button>
          <button class="action-btn cancel-report" onclick={() => handleCancelReport(report)}>
            {$t("신고_취소")}
          </button>
        </div>
      </div>
    </DatabaseListView>
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

#### App.svelte 라우팅

**파일 위치:** `src/demo/App.svelte`

**추가할 import:**
```typescript
import AdminReportListPage from "./AdminReportListPage.svelte";
import MyReportListPage from "./MyReportListPage.svelte";
```

**라인 위치:** 파일 상단 import 섹션

**추가할 라우트:**
```svelte
{:else if currentPath === "/admin/reports"}
  <!-- 관리자 신고 목록 페이지 -->
  <AdminReportListPage />
{:else if currentPath === "/my/reports"}
  <!-- 내 신고 목록 페이지 -->
  <MyReportListPage />
```

**삽입 위치:** `/admin` 라우트 다음, `/about` 라우트 이전

**정확한 코드:**
```svelte
{:else if currentPath === "/admin"}
  <!-- 관리자 페이지 -->
  <AdminPage />
{:else if currentPath === "/admin/reports"}
  <!-- 관리자 신고 목록 페이지 -->
  <AdminReportListPage />
{:else if currentPath === "/my/reports"}
  <!-- 내 신고 목록 페이지 -->
  <MyReportListPage />
{:else if currentPath === "/about"}
  <!-- 앱 정보 페이지 -->
  <AboutPage />
```

#### Menu.svelte 메뉴 항목

**파일 위치:** `src/demo/Menu.svelte`

**추가할 메뉴 항목:**
```svelte
let menuItems = $derived([
  // ... 기존 항목들
  { label: $t('관리자'), path: '/admin' },
  { label: $t('관리자_신고_목록'), path: '/admin/reports' },
  { label: $t('내_신고_목록'), path: '/my/reports' },
  // ... 나머지 항목들
]);
```

**삽입 위치:** 관리자 메뉴 다음, 앱 정보 메뉴 이전

**정확한 코드:**
```svelte
let menuItems = $derived([
  { label: $t('홈'), path: '/' },
  { label: $t('메뉴'), path: '/menu' },
  { label: $t('로그인'), path: '/user/login' },
  { label: $t('프로필'), path: '/user/profile' },
  { label: $t('게시글목록'), path: '/post/list' },
  { label: $t('설정'), path: '/settings' },
  { label: $t('관리자'), path: '/admin' },
  { label: $t('관리자_신고_목록'), path: '/admin/reports' },
  { label: $t('내_신고_목록'), path: '/my/reports' },
  { label: $t('앱정보'), path: '/about' },
  { label: $t('도움말'), path: '/help' },
  { label: $t('채팅목록'), path: '/chat/list' }
]);
```

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

**일본어 및 중국어는 [snsweb-forum-report.md](./snsweb-forum-report.md#10-다국어-지원-i18n)를 참조하세요.**

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
- **[snsweb-firebase-database.md](./snsweb-firebase-database.md)** - Firebase 데이터베이스 전체 구조
- **[snsweb-forum-report.md](./snsweb-forum-report.md)** - 신고 기능 전체 개발 가이드
- **[sns-web-coding-guideline.md](./sns-web-coding-guideline.md)** - 코딩 가이드라인 및 DatabaseListView 사용법

**추가 참고 문서:**
- [Firebase Realtime Database 공식 문서](https://firebase.google.com/docs/database)
- [Svelte 5 공식 문서](https://svelte.dev/docs/svelte/overview)
- [Playwright 공식 문서](https://playwright.dev/)
