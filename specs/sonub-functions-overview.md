---
name: sonub-functions-overview
title: Sonub Pure Functions 운영 규칙
version: 1.0.0
description: Sonub 프로젝트의 순수 함수(pure function) 관리 원칙과 파일 구조
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-11-11
updated: 2025-11-11
step: 25
priority: "**"
dependencies:
  - sonub-setup-svelte.md
tags:
  - functions
  - architecture
  - pure-functions
---

# Sonub 함수 개요

## 1. 목적

본 문서는 Sonub 코드베이스에서 **재사용 가능한 순수 함수(pure function)** 를 어떻게 정의하고 보관하는지에 대한 공통 규칙을 설명합니다. 모든 세부 도메인 명세(예: `sonub-functions-chat-functions.md`)는 이 문서를 기준으로 작성/유지됩니다.

## 2. Pure Function 정의

Sonub에서 말하는 순수 함수는 다음 조건을 모두 만족해야 합니다.

1. 동일한 입력에 대해 항상 동일한 출력을 반환한다.
2. 외부 상태(스토어, DOM, 네트워크, 로컬 스토리지 등)를 읽거나 변경하지 않는다.
3. 내부적으로 시간, 난수, Date.now, Math.random 등을 직접 호출하지 않는다.
4. 예외 발생 여부를 제외하고 부수 효과(side effect)가 없어야 한다.

## 3. 저장 위치 및 네이밍

- 모든 순수 함수는 `src/lib/functions` 폴더 하위에 위치한다.
- 도메인별로 `*.functions.ts` 파일을 생성하고, 파일명은 `<도메인>.functions.ts` 패턴을 따른다.
  - 예) 채팅 관련 → `chat.functions.ts`, 게시글 → `post.functions.ts`
- 하나의 파일에는 동일 도메인에 속한 순수 함수만 배치한다.
- `default export` 는 사용하지 않으며, 반드시 **Named Export** 로 내보낸다.

## 4. 코드 작성 규칙

1. **의존성 제한**: 순수 함수 파일에서는 `stores`, `actions`, `browser API`, `firebase` 등 부수 효과가 있는 모듈을 import 할 수 없다.
2. **테스트 용이성**: 각 함수는 독립적으로 단위 테스트가 가능하도록 설계한다.
3. **주석**: 함수 상단에 “무엇을 계산/변환하는지” 한 줄 이상의 설명을 남긴다.
4. **타입 명시**: 모든 매개변수와 반환 타입을 명시하여 타입 추론에 의존하지 않는다.
5. **파일 내 구조**:
   - 최상단: 파일 개요 주석
   - 이후: 함수 정의
   - 유틸 상수/헬퍼가 필요하다면 동일 파일 최하단에 배치하고 export 하지 않는다.

## 5. 도큐멘트 연계

- **도메인별 상세 규칙**: `sonub-functions-*.md` (예: [`sonub-functions-chat-functions.md`](./sonub-functions-chat-functions.md))
- 새로운 도메인 문서를 추가할 때는 반드시 이 문서를 선행 참고하고 교차 링크를 명시한다.

## 6. 검증 체크리스트

- [ ] 함수가 외부 상태를 읽거나 쓰지 않는가?
- [ ] 동일 입력에 대해 동일한 값을 반환하는가?
- [ ] `src/lib/functions/<도메인>.functions.ts` 에 위치하는가?
- [ ] Named export만 사용했는가?
- [ ] 타입 및 주석이 명확하게 작성되었는가?

모든 항목이 충족되지 않으면 해당 함수는 순수 함수 모듈에 포함할 수 없습니다.
