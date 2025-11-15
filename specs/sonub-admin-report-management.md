---
name: sonub-admin-report-management
version: 2.0.0
description: Firestore 기반 신고 목록/처리 워크플로
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-firebase-functions.md
  - sonub-firebase-database-structure.md
tags:
  - admin
  - report
  - firestore
---

# 관리자 신고 관리 개요

이 문서는 Firestore에 저장된 신고 데이터를 조회/처리하기 위한 관리자 UI 및 Cloud Functions 흐름을 정의합니다.

## 1. 데이터 구조

- **컬렉션**: `reports`
- **문서 ID**: `reportType-nodeId-uid` 패턴 (`comment-abc123-user789`)
- **필드** (참고: `firebase/functions/src/types/index.ts`의 `ReportData`)
  | 필드 | 타입 | 설명 |
  |------|------|------|
  | `type` | `"post"`/`"comment"` | 신고 대상 유형 |
  | `nodeId` | string | 게시글 ID 또는 댓글 ID |
  | `uid` | string | 신고자 UID |
  | `reason` | string | 신고 사유 |
  | `message` | string | 상세 설명 (선택) |
  | `createdAt` | number | 신고 시각 |
  | `status` | `"pending" \| "accepted" \| "rejected"` | 처리 상태 (Cloud Functions 추가 필드) |

Cloud Functions는 신고 접수 시 `status="pending"`으로 저장하고, 관리자 처리 후 `status`/`handledBy`/`handledAt`을 업데이트합니다.

## 2. 관리자 페이지 요구사항

### 2.1 `/admin/reports` (목록)

- Firestore 쿼리: `collection(db, 'reports')`, `orderBy('createdAt', 'desc')`, pageSize 20
- 필터:
  - 상태 필터 (`pending` / `accepted` / `rejected`)
  - 타입 필터 (`post`, `comment`)
- UI 요소:
  - 신고 대상 미리보기 (게시글/댓글 내용은 별도 조회 필요 시 `doc(db, 'posts', nodeId)` 등)
  - 처리 버튼 (수락/거절)
  - 처리자, 처리 사유 입력 모달
- Firestore 업데이트:
  ```ts
  await updateDoc(doc(db, `reports/${reportId}`), {
    status: 'accepted',
    handledBy: authStore.user?.uid,
    handledAt: Date.now(),
    adminComment
  });
  ```

### 2.2 `/admin/reports/[id]` (상세)

- 선택 사항. 필요 시 단일 신고 문서를 구독하여 상세 정보를 표시할 수 있음.
- `firestoreStore<ReportData>(`reports/${id}`)`를 사용하면 실시간 업데이트 가능.

## 3. Cloud Functions 연동

- 신고 생성: `firebase/functions/src/index.ts`의 핸들러가 `reports` 문서를 추가
- 처리 로그: 향후 필요 시 `reports/{id}/history` 서브컬렉션으로 작업 이력을 남길 수 있음
- 추가 액션(예: 게시글 숨김)은 별도 Cloud Function에서 트리거하거나 관리자 UI가 직접 호출

## 4. Firestore 규칙

- `reports` 컬렉션은 관리자만 읽고 쓸 수 있도록 규칙을 설정한다. (예: `system/settings.admins` 기반)
- 예시 (`firebase/firestore.rules`):
  ```groovy
  match /reports/{reportId} {
    allow read, write: if isAdmin();
  }
  ```

## 5. 체크리스트

- [ ] Firestore 색인: `reports` 컬렉션에 `status+createdAt`, `type+createdAt` 복합 색인 추가
- [ ] 관리자 UI에서 Firestore 쓰기 전 `authStore.isAdmin` 검증
- [ ] Cloud Functions에서 신고 접수 시 `status="pending"`으로 초기화
- [ ] 로딩/오류 상태를 명확히 표시

Firestore 기반으로 신고 시스템을 운영하면 RTDB 없이도 동일한 워크플로를 유지할 수 있습니다.
