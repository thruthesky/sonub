---
name: sonub-test-database-list-view
version: 1.0.0
description: Firebase RTDB 테스트 데이터 생성 및 DatabaseListView 무한 스크롤 검증 절차
author: Codex Agent
email: noreply@openai.com
step: 65
priority: '*'
dependencies:
  - sonub-firebase-database.md
  - sonub-firebase-database-list-view.md
  - sonub-firebase-security.md
tags:
  - test
  - rtdb
  - infinite-scroll
  - admin
  - dev-tools
---

## 1. 개요

- 본 명세서는 Firebase Realtime Database의 `/test/data` 노드를 대상으로 테스트 데이터를 생성하고, DatabaseListView 컴포넌트로 무한 스크롤을 검증하기 위한 **관리자/개발자 전용 도구**를 정의한다.
- 모든 데이터는 QA 용도로만 사용하며, 클라이언트/서버 간 추가 의존성이 없도록 독립 경로에 저장한다.

## 2. 데이터 구조

| 필드 | 타입 | 필수 | 설명 |
| ---- | ---- | ---- | ---- |
| `title` | string | ✅ | 리스트 화면에 출력할 제목. 형식: `[PageNumber] OrderNumber. [orderPrefix] [category] [YYYY-MM-DD HH:II:SS]` (예: `[1] 1. [Apple] [Q&A] [2025-11-09 15:30:45]`, `[2] 21. [Banana] [News] [2025-11-09 15:30:25]`) |
| `createdAt` | number | ✅ | 레코드 생성 시각(밀리초). DatabaseListView 정렬 기준 |
| `category` | string | ✅ | 데이터의 카테고리 (`qna`, `news`, `reminder`) |
| `order` | string | ✅ | orderPrefix 기반 정렬용 필드. 형식: `prefix-timestamp` (예: `apple-1699520445123`, `banana-1699520446123`) |
| `qnaCreatedAt` | number | ✅/❌ | Q&A 카테고리 전용 타임스탬프. **`category: 'qna'`일 때만 추가** |
| `newsCreatedAt` | number | ✅/❌ | News 카테고리 전용 타임스탬프. **`category: 'news'`일 때만 추가** |
| `reminderCreatedAt` | number | ✅/❌ | Reminder 카테고리 전용 타임스탬프. **`category: 'reminder'`일 때만 추가** |

> ℹ️ **카테고리별 타임스탬프는 배타적**입니다. 하나의 레코드에는 `qnaCreatedAt`, `newsCreatedAt`, `reminderCreatedAt` 중 정확히 하나만 존재합니다.  
> ℹ️ 따라서 `orderBy="qnaCreatedAt"`을 선택하면 Q&A 데이터만 조회되고, 다른 카테고리는 목록에서 제외됩니다.  
> ℹ️ 실제 카테고리는 `category` 필드로 구분하며, UI에 배지로 표시됩니다.
> ℹ️ `order` 필드는 orderPrefix 필터링 테스트를 위한 전용 필드로, `apple-`, `banana-`, `cherry-` 접두사를 포함합니다.

## 3. 관리자 페이지: `/admin/test/create-test-data`

- **파일 경로**: `src/routes/admin/test/create-test-data/+page.svelte`
- **주요 기능**
  1. 입력한 수량(1~200개)을 기준으로 `/test/data` 하위에 테스트 레코드를 순차 생성한다.
  2. 카테고리는 `qna`, `news`, `reminder` 중에서 랜덤으로 선정된다.
  3. 진행 상황, 최근 생성 키, 마지막 카테고리 정보를 즉시 표시한다.
- **UI 요구사항**
  - shadcn-svelte `Card`, `Button`, `Alert` 컴포넌트를 사용한다.
  - 생성 버튼은 진행 중에는 비활성화되고 `생성 중...` 레이블을 보여준다.
  - 에러 발생 시 붉은 배경의 경고 박스로 메시지를 표기한다.
- **비즈니스 규칙**
  - `rtdb` 초기화 실패 시 명확한 에러 메시지를 노출한다.
  - 입력 값이 범위를 벗어나면 자동으로 1~200 사이로 보정한다.
  - 최근 생성 키는 최대 5개까지 보관하여 Firebase Console과 대조할 수 있게 한다.
- **접근 경로 안내**
  - DatabaseListView 동작 검증은 개발 도구 전용 페이지(`/dev/test/database-list-view`)에서만 지원한다.
  - 관리자 네비게이션에는 중복 링크를 두지 않으며, QA 담당자는 개발 전용 경로를 직접 입력하거나 사내 가이드를 통해 접근한다.

## 4. 개발자 전용 리스트 뷰: `/dev/test/database-list-view`

- **파일 경로**: `src/routes/dev/test/database-list-view/+page.svelte`
- **DatabaseListView 설정** (동적으로 변경 가능)
  - `path="test/data"`
  - `orderBy` - 내부적으로 추출: `createdAt`, `qnaCreatedAt`, `newsCreatedAt`, `reminderCreatedAt`, `order`
  - `orderPrefix` - 내부적으로 추출: `''`, `apple-`, `banana-`, `cherry-`
  - `pageSize` - 선택 가능: 10, 20, 30, 40, 50
  - `threshold={320}`
  - `reverse` - 체크박스로 true/false 전환 가능
- **필터링 옵션 (UI - 2열 그리드)**
  - **pageSize**: 한 번에 로드할 데이터 개수 선택 (10~50)
  - **orderBy (통합)**: 정렬 기준 필드 선택 - orderBy와 orderPrefix를 하나의 드롭다운으로 통합
    - `createdAt`
    - `qnaCreatedAt`
    - `newsCreatedAt`
    - `reminderCreatedAt`
    - `order (전체)`
    - `order - Apple (apple-)`
    - `order - Banana (banana-)`
    - `order - Cherry (cherry-)`
  - **reverse**: 역순 정렬 여부 (체크박스)
  - 옵션 변경 시 {#key} 블록을 통해 컴포넌트가 완전히 재마운트되어 새로운 쿼리가 실행됨
- **내부 구현**
  - `combinedOrderBy` state 변수: UI에서 선택된 값을 저장
  - `orderBy`와 `orderPrefix`는 `$derived`로 자동 추출
  - 예: `order-apple` 선택 시 → `orderBy="order"`, `orderPrefix="apple-"`
- **UI 요구사항**
  - 상단 카드에서 연결 정보(RTDB 경로, orderBy, orderPrefix, reverse, pageSize, 생성 도구 링크)를 표기한다.
  - 카테고리 메타(`qnaCreatedAt`, `newsCreatedAt`, `reminderCreatedAt`)를 목록으로 안내한다.
  - 각 아이템 카드에는 key, title, 생성 시각, 카테고리 배지를 표시하고, JSON 원본을 `<pre>` 블록으로 출력한다.
  - `loading`, `loadingMore`, `noMore`, `empty` snippet을 모두 구현하여 상태별 메시지를 제공한다.
- **카테고리 판별**
  - `category` 필드가 존재하면 해당 값으로 카테고리를 결정한다.
  - `category` 필드가 없으면 호환성을 위해 타임스탬프 필드 존재 여부로 판단한다.
  - 매칭되는 필드가 없으면 `Unlabeled` 배지를 표시한다.

## 5. 보안 및 운영

- `/test/data` 경로는 QA 편의를 위해 `.read: true`, `.write: true` 로 공개되어 있으며, 상세 이유는 `specs/sonub-firebase-security.md`에 기록한다.
- 해당 노드는 **민감 데이터 저장 금지** 원칙을 따른다.
- 운영 환경에서도 테스트 데이터가 실제 서비스 영역과 혼동되지 않도록 UI 및 경로명을 명확히 표기한다.

## 6. 검증 절차

1. `/dev/test/database-list-view` 페이지에서 **데이터 생성하기** 버튼을 클릭하여 100개의 테스트 데이터를 생성한다.
   - [ ] 진행률이 실시간으로 갱신되는지 확인 (생성 중... 0/100 → 100/100)
   - [ ] 생성 완료 시 성공 메시지가 표시되는지 확인
2. Firebase Console > Database > `test/data` 경로에서 새 노드가 생성되었는지 확인한다.
   - [ ] 각 노드에 `title`, `createdAt`, `category`, `order`, 카테고리별 타임스탬프가 존재하는지 점검
   - [ ] `order` 필드가 `apple-`, `banana-`, `cherry-` 접두사를 포함하는지 확인
   - [ ] `title`에 orderPrefix, category, 시간이 올바르게 포함되어 있는지 확인
3. `/dev/test/database-list-view` 페이지에서 필터링 옵션을 테스트한다.
   - [ ] **pageSize** 변경 시 표시되는 데이터 개수가 변경되는지 확인
   - [ ] **orderBy** 드롭다운에서 `createdAt`, `qnaCreatedAt` 등 선택 시 정렬 순서가 유지되는지 확인 (모든 필드에서 동일한 타임스탬프)
   - [ ] **orderBy** 드롭다운에서 `order - Apple`, `order - Banana`, `order - Cherry` 선택 시 해당 접두사 데이터만 필터링되는지 확인
   - [ ] **reverse** 체크박스 변경 시 정렬 방향이 바뀌는지 확인 (최신순 ↔ 오래된 순)
4. 무한 스크롤 동작을 검증한다.
   - [ ] 최신 데이터가 상단에 노출되는지 (reverse=true 일 때)
   - [ ] 스크롤 바닥 근처에서 다음 페이지가 자동으로 로드되는지
   - [ ] `loading`, `loadingMore`, `noMore`, `empty` 상태 문구가 명세대로 출력되는지
5. 연결 정보 섹션을 확인한다.
   - [ ] orderBy, orderPrefix, reverse, pageSize 값이 실제 선택한 값과 일치하는지 확인
6. 카테고리 배지와 JSON 원본이 정상적으로 표시되는지 확인한다.
   - [ ] 각 아이템의 카테고리 배지가 올바르게 표시되는지 (Q&A, News, Reminder)
   - [ ] JSON 원본에 모든 필드가 포함되어 있는지 확인

## 7. 작업 이력 (SED Log)

| 날짜 | 작업자 | 내용 |
| ---- | ------ | ---- |
| 2025-11-09 | Codex Agent | `/admin/test/create-test-data` 페이지 생성, `/dev/test/database-list-view` 구현, `/test/data` 공개 규칙 문서화, 본 명세 최초 작성 |
| 2025-11-09 | Codex Agent | 관리자 사이드바에 `/admin/test/database-list-view` 링크를 추가하여 테스트 페이지 접근성을 개선하고, 본 명세의 관리자 페이지 섹션을 최신 구조로 보강 |
| 2025-11-09 | Codex Agent | `/admin/test/database-list-view` 경로가 `/dev/test/database-list-view`와 완전히 중복되어 삭제됨에 따라 관리자 네비게이션 링크를 제거하고 본 문서를 최신 정책(개발 전용 접근)으로 업데이트 |
| 2025-11-09 | Claude Code | order 필드 및 orderPrefix 필터링 기능 추가: `order` 필드에 `apple-`, `banana-`, `cherry-` 접두사를 포함하도록 테스트 데이터 생성 로직 수정, `/dev/test/database-list-view` 페이지에 orderPrefix 드롭다운 및 reverse 체크박스 추가, DatabaseListView 컴포넌트에 orderPrefix와 reverse props 전달, 연결 정보 섹션 업데이트, 본 명세 문서의 데이터 구조, 필터링 옵션, 검증 절차 섹션 업데이트 |
| 2025-11-09 | Claude Code | UI 개선: orderBy와 orderPrefix 드롭다운을 하나로 통합하여 사용성 개선. `combinedOrderBy` state 변수 도입 및 `$derived`를 사용해 orderBy/orderPrefix 자동 추출. 2열 그리드 레이아웃으로 변경. 연결 정보 섹션에서 orderPrefix는 값이 있을 때만 표시. 본 명세 문서의 필터링 옵션 및 검증 절차 섹션 업데이트 |
| 2025-11-09 | Claude Code | 제목 형식 개선: 페이지 번호 [1], [2]와 로딩 순서 번호 1. 2. 3.을 제목에 추가하여 테스트 데이터 식별성 향상. pageNumber는 `Math.floor(i / 20) + 1`로 계산하고 orderNumber는 `i + 1`로 계산. 제목 형식: `[PageNumber] OrderNumber. [orderPrefix] [category] [YYYY-MM-DD HH:II:SS]`. 본 명세 문서의 데이터 구조 섹션 업데이트 |
| 2025-11-09 | Claude Code | **중요 버그 수정**: DatabaseListView 컴포넌트에서 orderBy 필드 필터링 로직 추가. qnaCreatedAt으로 정렬 시 해당 필드가 없는 항목(newsCreatedAt, reminderCreatedAt만 있는 항목)도 화면에 표시되던 문제 해결. Firebase는 `startAt()`과 `endBefore()`를 동시에 사용할 수 없어 페이지네이션 시 orderBy 필드가 없는 항목도 반환될 수 있음. 이를 해결하기 위해 `loadInitialData()`와 `loadMore()` 함수에서 orderBy 필드가 존재하는 항목만 필터링하도록 수정. `loadedItems`를 `const`에서 `let`으로 변경하여 필터링 결과 재할당 가능하게 수정. |
| 2025-11-09 | Claude Code | **UI 개선**: DatabaseListView 컴포넌트의 item snippet에서 실제 로드 순서(index)를 상위 컴포넌트로 전달. `/dev/test/database-list-view` 페이지에서 index 파라미터를 받아 실제 페이지 번호(`Math.floor(index / pageSize) + 1`)와 순서 번호(`index + 1`)를 계산하여 표시. 데이터베이스에 저장된 번호 대신 실제 화면 표시 순서를 제목에 반영하도록 정규식으로 제목 재구성 (`title.replace(/^\[\d+\] \d+\./, ...)`). Key 레이블에 현재 인덱스 표시 추가 (`Key (Index: {index})`). 필터링 시에도 순차적인 번호(1, 2, 3...)가 정확하게 표시되도록 개선. |
| 2025-11-09 | Claude Code | **중요 버그 수정 - 정렬 순서 문제**: DatabaseListView 컴포넌트의 `loadInitialData()`와 `loadMore()`에서 `Object.entries(snapshot.val())`를 사용하면 JavaScript 객체 프로퍼티 순서로 인해 Firebase의 정렬 순서가 보장되지 않는 문제 해결. `snapshot.forEach()`를 사용하여 Firebase가 반환한 정렬 순서를 그대로 유지하도록 수정. 특히 `order` 필드로 정렬 시 문자열 비교 순서가 깨지던 문제 완전 해결. 상세한 디버깅 로그 추가: 쿼리 설정, Firebase 반환 순서, 필터링 전후, reverse 전후, 최종 결과를 색상별로 구분하여 콘솔에 출력. 모든 로그에 `%c` 스타일 적용으로 가독성 향상. |
