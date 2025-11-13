---
name: messages/ko.json
description: 한국어 다국어 메시지 파일. Paraglide i18n 라이브러리를 통해 사용됩니다.
version: 1.0.0
type: configuration
category: i18n-messages
tags: [configuration, i18n, korean, messages, paraglide]
---

# messages/ko.json

## 개요
Sonub 프로젝트의 한국어 번역 파일입니다. 이 파일은:
- Inlang Paraglide i18n 시스템에서 사용
- 모든 UI 텍스트의 한국어 번역 포함
- JSON 형식의 키-값 쌍 구조
- 244개의 번역 키 포함

## 주요 카테고리

### 공통 (common)
- 로딩, 닫기, 저장, 삭제, 취소, 확인 등 일반적인 액션
- 성공, 오류, 완료, 진행 상황 등 상태 메시지

### 네비게이션 (nav)
- 홈, 소개, 제품, 연락, 게시판, 채팅
- 로그인, 로그아웃, 메뉴, 내 프로필

### 인증 (auth)
- 환영 메시지
- Google/Apple 로그인 안내
- 로그인 상태 메시지

### 프로필 (profile)
- 닉네임, 성별, 생년월일
- 프로필 사진 업로드/제거
- 검증 오류 메시지

### 사용자 (user)
- 사용자 목록 관련 메시지
- 로딩, 에러 상태
- 가입일, 마지막 로그인

### 메뉴 (menu)
- 메뉴 제목 및 안내
- 내 계정, 프로필 수정
- 관리자 페이지

### 관리자 (admin)
- 대시보드
- 테스트 사용자 관리
- 신고 목록

### 테스트 (test)
- 테스트 사용자 생성/삭제
- 진행 상태 표시
- 확인 메시지

### 사이드바 (sidebar)
- 최근 활동
- 언어 선택
- 빌드 버전
- 알림

### 기능 소개 (feature)
- SvelteKit 5
- Firebase Auth
- TailwindCSS

### 페이지 타이틀 (pageTitle)
- 각 페이지의 제목 (홈, 메뉴, 로그인 등)

### 신고 (report)
- 신고 사유 (욕설, 허위정보, 스팸 등)
- 내 신고 목록

### 채팅 (chat)
- 채팅방 관련 메시지
- 메시지 로딩/전송
- 1:1 채팅, 그룹챗, 오픈챗
- 친구 찾기, 북마크, 검색

## 사용 예시

```typescript
import * as m from '$lib/paraglide/messages.js';

// 한국어 메시지 사용
console.log(m.authWelcome()); // "환영합니다"
console.log(m.navHome()); // "홈"
console.log(m.profileSave()); // "프로필 저장"
```

## 번역 통계
- **총 번역 키**: 244개
- **카테고리**: 15개 (common, nav, auth, profile, user, menu, admin, test, sidebar, feature, pageTitle, report, chat 등)
- **특수 문법**: `{name}`, `{count}` 등 변수 치환 지원

## 관련 파일
- [en.json](./en.json.md) - 영어 번역
- [ja.json](./ja.json.md) - 일본어 번역
- [zh.json](./zh.json.md) - 중국어 번역
- [vite.config.ts](../vite.config.ts.md) - Paraglide 플러그인 설정
- [package.json](../package.json.md) - Paraglide 의존성

## 주요 번역 예시

```json
{
  "commonLoading": "로딩 중...",
  "authWelcome": "환영합니다",
  "profileNickname": "닉네임",
  "chatSend": "전송",
  "adminDashboard": "관리자 대시보드",
  "testUserCreate": "테스트 사용자 생성"
}
```

전체 소스 코드는 `/Users/thruthesky/apps/sonub/messages/ko.json` 파일을 참조하세요.
