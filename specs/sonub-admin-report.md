---
name: sonub-admin-report
version: 2.0.0
description: Firestore 신고 생성 및 처리 트리거 명세
author: Codex Agent
email: noreply@openai.com
license: GPL-3.0
dependencies:
  - sonub-firebase-database-structure.md
  - sonub-firebase-functions.md
tags:
  - report
  - firestore
  - cloud-functions
---

# 신고 생성/처리 Flow

## 1. 클라이언트 신고 생성

- API: Firestore `addDoc(collection(db, 'reports'), payload)`
- 필수 필드 (`ReportData` 참고):
  ```ts
  {
    type: 'post' | 'comment',
    nodeId: string,
    uid: string,
    reason: 'abuse' | 'spam' | ...,
    message?: string,
    createdAt: Date.now(),
    status: 'pending'
  }
  ```
- 신고 버튼 위치: 게시글/댓글 메뉴, 채팅 메시지 등
- 중복 신고 방지: 문서 ID를 `type-nodeId-uid`로 만들거나 Cloud Functions에서 dedupe 처리

## 2. Cloud Functions

- 파일: `firebase/functions/src/index.ts`
- 역할:
  - 신고 생성 시 추가 가공 필요 없으면 Pass-through
  - 향후 자동 조치(예: 신고 횟수 N회 이상 시 자동 숨김)를 구현할 경우 Functions에서 처리

## 3. 관리 UI와 연계

- `/admin/reports` 페이지에서 `status`/`reason` 필터로 신고를 조회 (Firestore 쿼리)
- 상태 업데이트:
  ```ts
  await updateDoc(doc(db, `reports/${reportId}`), {
    status: 'accepted',
    handledBy: authStore.user?.uid,
    handledAt: Date.now(),
    adminComment
  });
  ```
- Cloud Functions가 추가 로직(예: 해당 게시글 숨김)을 수행하려면 `status` 필드 변경을 트리거로 활용

## 4. 보안 규칙

Firestore 규칙 예시:

```groovy
match /reports/{reportId} {
  allow create: if request.auth != null; // 누구나 신고 가능
  allow read, update, delete: if isAdmin();
}
```

## 5. 향후 확장

- `reports/{id}/history` 서브컬렉션을 만들어 상태 변경 로그 저장
- 신고 대상 문서를 별도 캐시(예: `reports/{id}/targetSnapshot`)에 저장해 삭제 후에도 확인 가능
- Slack/Discord 등 외부 알림 연계

Firestore 구조로 신고 시스템을 구축하면 클라이언트/관리자/Cloud Functions가 동일한 컬렉션을 사용하여 단순하게 유지할 수 있습니다.
