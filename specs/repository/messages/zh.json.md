---
name: messages/zh.json
description: 중국어 다국어 메시지 파일. Paraglide i18n 라이브러리를 통해 사용됩니다.
version: 1.0.0
type: configuration
category: i18n-messages
tags: [configuration, i18n, chinese, messages, paraglide]
---

# messages/zh.json

## 개요
Sonub 프로젝트의 중국어(간체) 번역 파일입니다. 이 파일은:
- Inlang Paraglide i18n 시스템에서 사용
- 모든 UI 텍스트의 중국어 번역 포함
- JSON 형식의 키-값 쌍 구조
- 297개의 번역 키 포함 (일부 중복 키 존재)

## 주요 카테고리

### 공통 (common)
- 加载中、关闭、保存、删除、取消、确认
- 成功、错误、完成、进度

### 네비게이션 (nav)
- 首页、介绍、产品、联系、论坛、聊天
- 登录、退出登录、菜单、我的资料

### 인증 (auth)
- 欢迎消息
- Google/Apple 登录指南
- 认证状态

### 프로필 (profile)
- 昵称、性别、出生日期
- 个人资料照片上传/删除
- 验证错误

### 사용자 (user)
- 用户列表相关消息
- 加载、错误状态
- 注册日期、最后登录

### 메뉴 (menu)
- 菜单标题和指南
- 我的账号、编辑资料
- 管理员页面

### 관리자 (admin)
- 管理员控制台
- 测试用户管理
- 举报列表

### 테스트 (test)
- 测试用户创建/删除
- 进度显示
- 确认消息

### 사이드바 (sidebar)
- 最近活动
- 语言选择
- 构建版本
- 通知

### 기능 소개 (feature)
- SvelteKit 5
- Firebase Auth
- TailwindCSS

### 페이지 타이틀 (pageTitle)
- 各页面标题

### 신고 (report)
- 举报原因（辱骂、虚假信息、垃圾信息等）
- 我的举报

### 채팅 (chat)
- 聊天室相关消息
- 消息加载/发送
- 单聊、群聊、开放聊天
- 查找好友、书签、搜索

## 사용 예시

```typescript
import * as m from '$lib/paraglide/messages.js';
import { setLanguageTag } from '$lib/paraglide/runtime.js';

// 중국어로 전환
setLanguageTag('zh');

// 중국어 메시지 사용
console.log(m.authWelcome()); // "欢迎"
console.log(m.navHome()); // "首页"
console.log(m.profileSave()); // "保存资料"
```

## 번역 통계
- **총 번역 키**: 297개 (일부 중복 포함)
- **카테고리**: 15개 (common, nav, auth, profile, user, menu, admin, test, sidebar, feature, pageTitle, report, chat 등)
- **특수 문법**: `{name}`, `{count}` 등 변수 치환 지원
- **주의**: 일부 키가 중복되어 있어 정리 필요

## 관련 파일
- [ko.json](./ko.json.md) - 한국어 번역
- [en.json](./en.json.md) - 영어 번역
- [ja.json](./ja.json.md) - 일본어 번역
- [vite.config.ts](../vite.config.ts.md) - Paraglide 플러그인 설정
- [package.json](../package.json.md) - Paraglide 의존성

## 주요 번역 예시

```json
{
  "commonLoading": "加载中...",
  "authWelcome": "欢迎",
  "profileNickname": "昵称",
  "chatSend": "发送",
  "adminDashboard": "管理员控制台",
  "testUserCreate": "创建测试用户"
}
```

## 개선 사항
이 파일은 일부 키가 중복되어 있습니다 (예: `commonLoading`, `loading`). 향후 중복 키 정리 및 일관성 개선이 필요합니다.

전체 소스 코드는 `/Users/thruthesky/apps/sonub/messages/zh.json` 파일을 참조하세요.
