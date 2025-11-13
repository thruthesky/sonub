---
name: messages/ja.json
description: 일본어 다국어 메시지 파일. Paraglide i18n 라이브러리를 통해 사용됩니다.
version: 1.0.0
type: configuration
category: i18n-messages
tags: [configuration, i18n, japanese, messages, paraglide]
---

# messages/ja.json

## 개요
Sonub 프로젝트의 일본어 번역 파일입니다. 이 파일은:
- Inlang Paraglide i18n 시스템에서 사용
- 모든 UI 텍스트의 일본어 번역 포함
- JSON 형식의 키-값 쌍 구조
- 246개의 번역 키 포함

## 주요 카테고리

### 공통 (common)
- 読み込み中、閉じる、保存、削除、キャンセル、確認
- 成功、エラー、完了、進行状況

### 네비게이션 (nav)
- ホーム、紹介、製品、お問い合わせ、掲示板、チャット
- ログイン、ログアウト、メニュー、マイプロフィール

### 인증 (auth)
- ようこそメッセージ
- Google/Apple ログインガイド
- 認証状態

### 프로필 (profile)
- ニックネーム、性別、生年月日
- プロフィール写真アップロード/削除
- 検証エラー

### 사용자 (user)
- ユーザーリスト関連メッセージ
- ローディング、エラー状態
- 登録日、最終ログイン

### 메뉴 (menu)
- メニュータイトルとガイド
- マイアカウント、プロフィール修正
- 管理者ページ

### 관리자 (admin)
- 管理者ダッシュボード
- テストユーザー管理
- 通報リスト

### 테스트 (test)
- テストユーザー作成/削除
- 進行状況表示
- 確認メッセージ

### 사이드바 (sidebar)
- 最近のアクティビティ
- 言語選択
- ビルドバージョン
- 通知

### 기능 소개 (feature)
- SvelteKit 5
- Firebase Auth
- TailwindCSS

### 페이지 타이틀 (pageTitle)
- 各ページのタイトル

### 신고 (report)
- 通報理由（誹謗中傷、虚偽情報、スパムなど）
- マイ通報リスト

### 채팅 (chat)
- チャットルーム関連メッセージ
- メッセージ読み込み/送信
- 1:1チャット、グループチャット、オープンチャット
- 友達検索、ブックマーク、検索

## 사용 예시

```typescript
import * as m from '$lib/paraglide/messages.js';
import { setLanguageTag } from '$lib/paraglide/runtime.js';

// 일본어로 전환
setLanguageTag('ja');

// 일본어 메시지 사용
console.log(m.authWelcome()); // "ようこそ"
console.log(m.navHome()); // "ホーム"
console.log(m.profileSave()); // "プロフィール保存"
```

## 번역 통계
- **총 번역 키**: 246개
- **카테고리**: 15개 (common, nav, auth, profile, user, menu, admin, test, sidebar, feature, pageTitle, report, chat 등)
- **특수 문법**: `{name}`, `{count}` 등 변수 치환 지원

## 관련 파일
- [ko.json](./ko.json.md) - 한국어 번역
- [en.json](./en.json.md) - 영어 번역
- [zh.json](./zh.json.md) - 중국어 번역
- [vite.config.ts](../vite.config.ts.md) - Paraglide 플러그인 설정
- [package.json](../package.json.md) - Paraglide 의존성

## 주요 번역 예시

```json
{
  "commonLoading": "読み込み中...",
  "authWelcome": "ようこそ",
  "profileNickname": "ニックネーム",
  "chatSend": "送信",
  "adminDashboard": "管理者ダッシュボード",
  "testUserCreate": "テストユーザー作成"
}
```

전체 소스 코드는 `/Users/thruthesky/apps/sonub/messages/ja.json` 파일을 참조하세요.
