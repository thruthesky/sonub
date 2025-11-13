---
name: messages/en.json
description: 영어 다국어 메시지 파일. Paraglide i18n 라이브러리를 통해 사용됩니다.
version: 1.0.0
type: configuration
category: i18n-messages
tags: [configuration, i18n, english, messages, paraglide]
---

# messages/en.json

## 개요
Sonub 프로젝트의 영어 번역 파일입니다. 이 파일은:
- Inlang Paraglide i18n 시스템에서 사용
- 모든 UI 텍스트의 영어 번역 포함
- JSON 형식의 키-값 쌍 구조
- 245개의 번역 키 포함

## 주요 카테고리

### 공통 (common)
- Loading, Close, Save, Delete, Cancel, Confirm
- Success, Error, Complete, Progress

### 네비게이션 (nav)
- Home, About, Products, Contact, Board, Chat
- Login, Logout, Menu, My Profile

### 인증 (auth)
- Welcome messages
- Google/Apple sign in guide
- Authentication status

### 프로필 (profile)
- Nickname, Gender, Date of Birth
- Profile picture upload/remove
- Validation errors

### 사용자 (user)
- User list messages
- Loading, error states
- Join date, last login

### 메뉴 (menu)
- Menu title and guide
- My Account, Edit Profile
- Admin Page

### 관리자 (admin)
- Admin Dashboard
- Test User Management
- Report List

### 테스트 (test)
- Test user creation/deletion
- Progress indicators
- Confirmation messages

### 사이드바 (sidebar)
- Recent Activity
- Language Selection
- Build Version
- Notifications

### 기능 소개 (feature)
- SvelteKit 5
- Firebase Auth
- TailwindCSS

### 페이지 타이틀 (pageTitle)
- Page titles (Home, Menu, Login, etc.)

### 신고 (report)
- Report reasons (Abuse, Misinformation, Spam, etc.)
- My Reports list

### 채팅 (chat)
- Chat room messages
- Message loading/sending
- Single Chat, Group Chats, Open Chats
- Find Friends, Bookmarks, Search

## 사용 예시

```typescript
import * as m from '$lib/paraglide/messages.js';
import { setLanguageTag } from '$lib/paraglide/runtime.js';

// 영어로 전환
setLanguageTag('en');

// 영어 메시지 사용
console.log(m.authWelcome()); // "Welcome"
console.log(m.navHome()); // "Home"
console.log(m.profileSave()); // "Save Profile"
```

## 번역 통계
- **총 번역 키**: 245개
- **카테고리**: 15개 (common, nav, auth, profile, user, menu, admin, test, sidebar, feature, pageTitle, report, chat 등)
- **특수 문법**: `{name}`, `{count}` 등 변수 치환 지원

## 관련 파일
- [ko.json](./ko.json.md) - 한국어 번역
- [ja.json](./ja.json.md) - 일본어 번역
- [zh.json](./zh.json.md) - 중국어 번역
- [vite.config.ts](../vite.config.ts.md) - Paraglide 플러그인 설정
- [package.json](../package.json.md) - Paraglide 의존성

## 주요 번역 예시

```json
{
  "commonLoading": "Loading...",
  "authWelcome": "Welcome",
  "profileNickname": "Nickname",
  "chatSend": "Send",
  "adminDashboard": "Admin Dashboard",
  "testUserCreate": "Create Test Users"
}
```

전체 소스 코드는 `/Users/thruthesky/apps/sonub/messages/en.json` 파일을 참조하세요.
