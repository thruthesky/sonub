---
name: playwright.config.ts
description: Playwright E2E 테스트 설정 파일. 웹 서버 및 테스트 디렉토리를 정의합니다.
version: 1.0.0
type: configuration
category: root-config
tags: [configuration, playwright, e2e, test]
---

# playwright.config.ts

## 개요
Playwright E2E(End-to-End) 테스트의 설정 파일입니다. 이 파일은:
- 테스트용 웹 서버 자동 시작
- E2E 테스트 파일 디렉토리 지정

## 소스 코드

```typescript
import { defineConfig } from '@playwright/test';

export default defineConfig({
	webServer: {
		command: 'npm run build && npm run preview',
		port: 4173
	},
	testDir: 'e2e'
});
```

## 주요 설정

### 웹 서버 (webServer)
- **command**: `npm run build && npm run preview`
  - 프로덕션 빌드 후 미리보기 서버 실행
  - Vite preview 서버 사용
- **port**: 4173 - Vite preview 기본 포트

### 테스트 디렉토리
- **testDir**: `e2e` - E2E 테스트 파일 저장 경로

## 테스트 워크플로우
1. `npm run build`로 프로덕션 빌드 생성
2. `npm run preview`로 포트 4173에서 서버 시작
3. `e2e/` 폴더의 테스트 파일 실행
4. 테스트 완료 후 서버 자동 종료

## 관련 파일
- [package.json](./package.json.md) - Playwright 의존성 및 test:e2e 스크립트
- [vite.config.ts](./vite.config.ts.md) - Vite 빌드 및 미리보기 설정
- [e2e/](../e2e/) - E2E 테스트 파일 디렉토리
