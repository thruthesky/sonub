---
name: tsconfig.json
description: TypeScript 컴파일러 설정 파일. SvelteKit 프로젝트의 TypeScript 옵션을 정의합니다.
version: 1.0.0
type: configuration
category: root-config
tags: [configuration, typescript, compiler]
---

# tsconfig.json

## 개요
Sonub 프로젝트의 TypeScript 설정 파일입니다. 이 파일은:
- SvelteKit의 자동 생성 설정을 확장
- 엄격한 타입 체크 활성화
- Paraglide 다국어 파일 제외
- 번들러 모드의 모듈 해석 사용

## 소스 코드

```json
{
	"extends": "./.svelte-kit/tsconfig.json",
	"compilerOptions": {
		"allowImportingTsExtensions": true,
		"allowJs": true,
		"checkJs": true,
		"esModuleInterop": true,
		"forceConsistentCasingInFileNames": true,
		"resolveJsonModule": true,
		"skipLibCheck": true,
		"sourceMap": true,
		"strict": true,
		"moduleResolution": "bundler"
	},
	"exclude": ["src/paraglide/**/*"]
	// Path aliases are handled by https://svelte.dev/docs/kit/configuration#alias
	// except $lib which is handled by https://svelte.dev/docs/kit/configuration#files
	//
	// To make changes to top-level options such as include and exclude, we recommend extending
	// the generated config; see https://svelte.dev/docs/kit/configuration#typescript
}
```

## 주요 설정

### 기본 설정
- **extends**: `./.svelte-kit/tsconfig.json` - SvelteKit이 자동 생성하는 기본 설정 상속

### 컴파일러 옵션
- **allowImportingTsExtensions**: true - .ts 확장자로 import 허용
- **allowJs**: true - JavaScript 파일 허용
- **checkJs**: true - JavaScript 파일도 타입 체크
- **esModuleInterop**: true - CommonJS와 ES 모듈 간 상호운용성 개선
- **forceConsistentCasingInFileNames**: true - 파일명 대소문자 일관성 강제
- **resolveJsonModule**: true - JSON 파일 import 허용
- **skipLibCheck**: true - 라이브러리 파일 타입 체크 건너뛰기
- **sourceMap**: true - 소스맵 생성
- **strict**: true - 엄격한 타입 체크 활성화
- **moduleResolution**: bundler - 번들러 모드 모듈 해석

### 제외 패턴
- **exclude**: `["src/paraglide/**/*"]` - Paraglide 자동 생성 파일 제외

## 관련 파일
- [package.json](./package.json.md) - npm 패키지 설정
- [svelte.config.js](./svelte.config.js.md) - Svelte 설정 (경로 별칭 처리)
- [vite.config.ts](./vite.config.ts.md) - Vite 빌드 설정
