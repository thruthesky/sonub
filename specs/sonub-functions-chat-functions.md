---
name: sonub-functions-chat
title: Chat Pure Functions 명세
version: 1.0.0
description: 채팅 도메인에서 사용하는 순수 함수 목록과 사용 규칙
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-11
updated: 2025-11-11
step: 30
priority: "*"
dependencies:
  - sonub-functions-overview.md
tags:
  - chat
  - functions
  - architecture
---

# Chat 순수 함수 명세

본 문서는 `src/lib/functions/chat.functions.ts` 파일에 정의된 순수 함수의 역할과 사용 규칙을 설명합니다. 전역 규칙은 반드시 [sonub-functions-overview.md](./sonub-functions-overview.md)를 먼저 읽은 후 적용합니다.

---

## 1. 파일 구조

```
src/
└── lib/
    └── functions/
        └── chat.functions.ts   # 채팅 관련 순수 함수 전용
```

### 작성 규칙 요약

1. **순수 함수만 기록**: 채팅 도메인에서 부수 효과가 없는 계산/변환 함수만 허용합니다.
2. **Named Export**: 모든 함수를 `export function ...` 형태로 노출합니다.
3. **주석 필수**: 각 함수는 입력/출력, 적용 시나리오를 간단히 주석으로 설명합니다.
4. **의존성 제한**: `stores`, `svelte` runes, Firebase API 등 사이드 이펙트 모듈 import 금지.

---

## 2. 제공 함수 목록

### 2.1 `buildSingleRoomId(a: string, b: string): string`

```ts
/**
 * 1:1 채팅방의 roomId를 UID 두 개로부터 고정적으로 생성한다.
 */
export function buildSingleRoomId(a: string, b: string) {
	return `single-${[a, b].sort().join('-')}`;
}
```

- **파일 위치**: `src/lib/functions/chat.functions.ts`
- **설명**: 1:1 채팅방의 roomId를 두 사용자 UID로부터 항상 동일하게 생성합니다.
- **입력 조건**:
  - `a`, `b` 는 Firebase UID 또는 임의의 사용자 식별자 문자열이어야 합니다.
  - 공백 문자열이 들어오면 정렬 후 그대로 포함되므로, 호출측에서 유효성 검사를 완료해야 합니다.
- **동작**:
  1. `[a, b]` 배열을 정렬하여 UID 순서를 고정합니다.
  2. `'single-'` 접두사를 붙인 뒤 `-` 구분자로 이어붙여 최종 roomId를 만듭니다.
- **반환 예시**:
  - `buildSingleRoomId('uidA', 'uidB')` → `single-uidA-uidB` (정렬 결과에 따라 순서 고정)
  - `buildSingleRoomId('uidZ', 'uidA')` → `single-uidA-uidZ` (입력 순서가 달라도 결과 동일)
- **사용처**:
  - `src/routes/chat/room/+page.svelte`: GET 파라미터 `uid` 가 존재할 때 단일 채팅방의 `roomId` 를 계산합니다.
  - 서버 함수나 다른 컴포넌트에서도 동일한 룸 키를 생성해야 할 때 이 함수를 가져다 쓰면 일관성이 유지됩니다.
- **검증 포인트**:
  - 동일한 두 UID 조합에 대해 항상 동일 값을 반환해야 합니다.
  - UID가 동일한 경우(`a === b`)에도 `single-uid-uid` 형태로 표현되어 Firebase 경로가 안정적으로 유지됩니다.
  - 함수 내에서 외부 상태나 시간 값을 참조하지 않으므로 순수 함수 규약을 만족합니다.

---

### 2.2 `resolveRoomTypeLabel(roomType: string): string`

```ts
/**
 * 채팅방 유형 문자열을 배지 텍스트로 변환한다.
 */
export function resolveRoomTypeLabel(roomType: string): string {
	const normalized = roomType?.toLowerCase() ?? '';
	if (normalized.includes('open')) return 'Open';
	if (normalized.includes('group')) return 'Group';
	if (normalized.includes('single')) return 'Single';
	return 'Room';
}
```

- **파일 위치**: `src/lib/functions/chat.functions.ts`
- **설명**: DB 또는 파이어스토어에 저장된 `roomType`, `type` 필드에서 UI 배지를 생성합니다. 문자열 비교 외의 부수 효과가 없어 어느 환경에서도 동일한 결과를 보장합니다.
- **입력 규칙**:
  - 대소문자가 뒤섞여도 되며, 함수 내부에서 소문자로 정규화합니다.
  - null/undefined가 들어오면 빈 문자열로 처리되어 기본값 `Room` 을 반환합니다.
- **반환 규칙**:
  - `"open"` 이 포함되면 `'Open'`
  - `"group"` 이 포함되면 `'Group'`
  - `"single"` 이 포함되면 `'Single'`
  - 위 조건에 해당하지 않으면 기본 `'Room'`
- **사용처**:
  - `src/routes/chat/list/+page.svelte`: 내 채팅방 목록에서 룸 유형 배지를 표시할 때 사용합니다.
  - 향후 관리자 화면, 알림 설정 등에서 동일 로직이 필요하면 이 함수를 재사용합니다.
- **확장 지침**:
  - 새 유형이 도입되면 조건 분기를 추가하고, UI/스펙에 어떤 키워드를 사용할지 합의한 뒤 본 문서도 업데이트합니다.

---

## 3. 적용 지침

1. 1:1 채팅방 생성, 조회, 정리 등에서 roomId 계산이 필요할 경우 **반드시** `buildSingleRoomId` 를 사용한다.
2. 새로운 채팅 관련 순수 함수를 추가할 때는 본 문서의 “제공 함수 목록” 섹션에 표를 추가하고, 사용처/검증 방법을 명시한다.
3. 도메인 외부(예: 알림, 분석 등)에서 채팅 전용 함수를 사용해야 한다면, 의존 관계를 검토하고 필요한 경우 별도 공유 모듈로 승격한다.

---

## 4. 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-11 | Codex Agent | 채팅 순수 함수 명세 최초 작성 및 `buildSingleRoomId` 설명 추가. |
| 2025-11-11 | Codex Agent | `resolveRoomTypeLabel` 함수 추가 및 사용 규칙 문서화. |
