---
name: sonub
version: 1.0.0
description: Firebase Realtime Database 구조 가이드 문서의 SED 사양
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: GPL-3.0
dependencies: []
---

- [Firebase Realtime Database 구조 가이드](#firebase-realtime-database-구조-가이드)
  - [워크플로우](#워크플로우)
    - [📋 문서의 범위](#-문서의-범위)
    - [🔀 클라이언트와 백엔드의 데이터 책임 구분](#-클라이언트와-백엔드의-데이터-책임-구분)
    - [클라이언트와 백엔드의 역할 분리](#클라이언트와-백엔드의-역할-분리)
  - [개요](#개요)
  - [데이터베이스 전체 구조](#데이터베이스-전체-구조)
  - [사용자 정보 (users)](#사용자-정보-users)
    - [사용자 Realtime Database 데이터 구조](#사용자-realtime-database-데이터-구조)
    - [필드 설명](#필드-설명)
    - [클라이언트/서버 역할 분리](#클라이언트서버-역할-분리)
    - [⚠️ 중요: Firebase Auth vs RTDB 필드](#️-중요-firebase-auth-vs-rtdb-필드)
    - [관련 가이드](#관련-가이드)
  - [사용자 속성 분리 (user-props)](#사용자-속성-분리-user-props)
    - [데이터 구조](#데이터-구조)
    - [클라이언트/서버 역할 분리](#클라이언트서버-역할-분리-1)
    - [관련 가이드](#관련-가이드-1)
  - [친구 관계 (friends, followers, following)](#친구-관계-friends-followers-following)
    - [데이터 구조](#데이터-구조-1)
    - [설명](#설명)
    - [클라이언트/서버 역할 분리](#클라이언트서버-역할-분리-2)
    - [관련 가이드](#관련-가이드-2)
  - [주요 설계 원칙](#주요-설계-원칙)
    - [1. Flat Style 구조](#1-flat-style-구조)
    - [2. 속성 분리](#2-속성-분리)
    - [3. Cloud Functions 활용](#3-cloud-functions-활용)
    - [4. 보안 규칙](#4-보안-규칙)
  - [주의사항](#주의사항)
    - [Firebase Auth vs RTDB 필드명 차이](#firebase-auth-vs-rtdb-필드명-차이)
  - [관련 가이드 문서](#관련-가이드-문서)
  - [참고 자료](#참고-자료)


## Overview
- 이 문서는 "Firebase Realtime Database 구조 가이드"에 대한 세부 사양을 정리하며, 기존 내용을 그대로 유지한 채 SED 구조에 맞춰 제공합니다.

## Requirements
- 문서 전반에 걸쳐 소개되는 지침과 참고 사항을 모두 숙지해야 하며, 별도의 추가 선행 조건은 원문 각 절에서 제시되는 내용을 따릅니다.

## Workflow
1. 아래 `## Detail Items` 절에 포함된 원문 목차를 검토합니다.
2. 필요한 경우 원문의 각 절을 순서대로 읽으며 프로젝트 작업 흐름에 반영합니다.
3. 문서에 명시된 모든 지침을 확인한 뒤 실제 개발 단계에 적용합니다.

## Detail Items
- 이하에는 기존 문서의 모든 내용을 원형 그대로 포함하여 참조할 수 있도록 구성했습니다.

# Firebase Realtime Database 구조 가이드

본 문서는 SNS 웹 애플리케이션의 Firebase Realtime Database 스키마 정의를 제공합니다.
각 기능의 상세한 구현 방법은 해당 가이드 문서를 참고하세요.

## 워크플로우

### 📋 문서의 범위

본 문서는 **데이터베이스 구조(스키마)와 구조에 대한 설명만** 포함합니다.

- ✅ **포함되는 내용**:
  - Firebase Realtime Database 경로 및 구조 정의
  - 각 필드의 타입 및 설명
  - 데이터 구조 예시
  - 클라이언트/백엔드 역할 구분 (어떤 필드를 누가 저장하는지)

- ❌ **포함되지 않는 내용**:
  - 구체적인 구현 코드 예제 (TypeScript, JavaScript)
  - 케이스별 상세 설명 및 사용 예시
  - API 함수 사용법

**구현 예제와 상세 설명**은 다음 개별 가이드 문서를 참고하세요:
- [Firebase Cloud Functions 개발 가이드](./sns-firebase-cloud-functions.md) - Cloud Functions 구현 예제

### 🔀 클라이언트와 백엔드의 데이터 책임 구분

**매우 중요**: DB 구조의 각 필드는 **클라이언트가 저장**하거나 **백엔드가 업데이트**하도록 명확히 구분되어 있습니다.

| 필드 유형 | 책임 주체 | 예시 필드 |
|----------|---------|----------|
| **사용자 입력 데이터** | 클라이언트만 저장 | `title`, `content`, `uid`, `createdAt` |
| **카운터 필드** | 백엔드만 업데이트 | `likeCount`, `commentCount`, `postCount` |
| **통계 및 집계** | 백엔드만 업데이트 | `/stats/counters/*`, `/categories/{category}/postCount` |
| **속성 분리 데이터** | 백엔드만 동기화 | `/user-props/displayName/{uid}` |

⚠️ **개발 시 필수 준수 사항**:
- 클라이언트는 **절대로** 카운터 필드를 직접 증가/감소시키지 않습니다
- 클라이언트는 **절대로** 통계 데이터를 직접 계산하여 저장하지 않습니다
- 백엔드(Cloud Functions)만이 카운터, 통계, 속성 분리 작업을 수행합니다

**⚠️ 중요 원칙**: 웹/앱 클라이언트에서는 **최소한의 정보만 RTDB에 기록**하고, **추가적인 정보 업데이트는 Firebase Cloud Functions 백엔드에서 처리**합니다.

### 클라이언트와 백엔드의 역할 분리

**클라이언트의 역할 (최소한의 데이터만 저장):**
- ✅ 사용자가 직접 입력한 데이터만 RTDB에 저장합니다 (예: 게시글 제목, 내용, 댓글 내용)
- ✅ 기본적인 메타데이터만 포함합니다 (예: uid, createdAt, category)
- ❌ 카운터 증가/감소를 직접 처리하지 않습니다 (예: likeCount, commentCount)
- ❌ 데이터 집계 및 통계를 직접 계산하지 않습니다 (예: stats/counters, categories)
- ❌ 속성 분리 작업을 직접 하지 않습니다 (예: user-props/)

**백엔드(Cloud Functions)의 역할 (자동 데이터 처리):**
- ✅ 클라이언트가 저장한 데이터를 감지하여 추가 데이터를 자동으로 업데이트합니다
- ✅ 카운터 자동 증가/감소 (예: likeCount, commentCount, postCount)
- ✅ 전체 통계 자동 집계 (예: stats/counters/like, stats/counters/post)
- ✅ 사용자 속성 분리 자동 동기화 (예: /users/{uid} → /user-props/displayName/{uid})
- ✅ 데이터 무결성 보장 (예: 게시글 삭제 시 관련 댓글/좋아요 정리)

**구체적인 예시**는 다음 가이드 문서를 참고하세요:
- [Firebase Cloud Functions 개발 가이드](./sns-firebase-cloud-functions.md)

이러한 역할 분리를 통해 다음과 같은 이점을 얻을 수 있습니다:
- 🔒 **데이터 무결성**: 백엔드에서 일관되게 처리하여 중복 증가/감소 방지
- ⚡ **성능 최적화**: 클라이언트는 단순 작업만 수행하여 빠른 응답
- 🔧 **유지보수성**: 비즈니스 로직이 백엔드에 집중되어 관리 용이
- 🌐 **플랫폼 독립성**: 웹/앱 모두 동일한 백엔드 로직 공유


---

## 개요

- Firebase Realtime Database(RTDB)는 실시간 데이터 동기화가 필요한 SNS 웹 애플리케이션에 적합합니다.
- 데이터는 **flat style** 구조로 저장되어 쿼리 효율성을 극대화합니다.
- 애플리케이션에서는 필요한 최소한의 데이터만 저장하고, Firebase Cloud Functions를 사용하여 복잡한 데이터 처리 및 집계를 수행합니다.

---

## 데이터베이스 전체 구조

```
Firebase Realtime Database (루트)
├── users/                    # 사용자 프로필
├── user-props/               # 사용자 속성 (대량 쿼리 최적화)
├── friends/                  # 친구 관계
├── followers/                # 팔로워 (나를 팔로우하는 사용자)
├── following/                # 팔로잉 (내가 팔로우하는 사용자)
├── chat-messages/            # 채팅 메시지 (게시글 + 댓글 역할 통합)
├── chat-joins/               # 채팅방 참여 정보 (채팅방 목록용)
└── stats/                    # 전역 통계
    └── counters/
        └── user              # 전체 사용자 수 (Cloud Functions에서만 증가)
```

---

## 통계 (stats/counters)

전체 통계 값은 `/stats/counters/` 경로에서 관리하며, **백엔드(Cloud Functions)만** 쓰기가 가능합니다.

### 사용자 수 통계 구조

```
/stats/
  /counters/
    user: number   // 전체 가입 사용자 수
```

### 동작 방식

- 새 사용자가 생성되면 `firebase/functions/src/handlers/user.handler.ts`의 `handleUserCreate()`가 실행됩니다.
- 해당 로직은 `/stats/counters/user` 값을 `ServerValue.increment(1)`로 증가시켜 동시성 문제 없이 총 사용자 수를 집계합니다.
- 클라이언트는 이 값을 읽기만 하며, 쓰기는 금지됩니다.

### 활용 예시

- 홈페이지 우측 사이드바의 통계 카드
- `/stats` 페이지에서 제공하는 사용자 수 대시보드

---

## 사용자 정보 (users)

사용자 프로필 정보는 `/users/<uid>/` 경로에 저장됩니다.

### 사용자 Realtime Database 데이터 구조


사용자 프로필 데이터의 저장 구조는 다음과 같습니다:

```
/users/
├── <uid1>/
│   ├── displayName: "사용자1"
│   ├── photoUrl: "https://firebasestorage.googleapis.com/..."
│   ├── gender: "M"
│   ├── birthYear: 1990
│   ├── birthMonth: 1
│   ├── birthDay: 15
│   ├── birthYearMonthDay: "1990-01-15"
│   ├── birthMonthDay: "01-15"
│   ├── bio: "자기소개"
│   ├── createdAt: 1698473000000
│   └── updatedAt: 1698474000000
└── <uid2>/
    ├── displayName: "사용자2"
    ├── photoUrl: null
    ├── gender: "F"
    ├── createdAt: 1698473100000
    └── updatedAt: 1698474100000
```

### 필드 설명

| 필드 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `displayName` | string | ✅ | 사용자 닉네임 |
| `photoUrl` | string | ❌ | 프로필 사진 URL |
| `gender` | (M|F) | ❌ | 성별 |
| `birthYear` | number | ❌ | 생년 |
| `birthMonth` | number | ❌ | 생월 |
| `birthDay` | number | ❌ | 생일 |
| `birthYearMonthDay` | string | ❌ | 생년월일 (YYYY-MM-DD) |
| `birthMonthDay` | string | ❌ | 생월일 (MM-DD)
| `bio` | string | ❌ | 자기소개 |
| `createdAt` | number | ✅ | 계정 생성 시간 |
| `updatedAt` | number | ✅ | 프로필 수정 시간 |

### 클라이언트/서버 역할 분리

사용자 정보의 경우:
- **클라이언트는** `displayName`, `photoUrl`, `gender`, `birthYear`, `birthMonth`, `birthDay`, `bio` 를 저장할 수 있고,
- **서버는** `createdAt` 과 `updatedAt` 만 저장할 수 있습니다.

### ⚠️ 중요: Firebase Auth vs RTDB 필드

**/users/<uid> 노드에는 Firebase Auth 정보를 저장하지 않습니다:**

Firebase Authentication의 다음 필드들은 `/users/<uid>` 노드에 **저장하지 않습니다**:
- ❌ `phoneNumber` - Firebase Auth에서만 관리
- ❌ `email` - Firebase Auth에서만 관리
- ❌ `photoURL` (대문자 URL) - Firebase Auth에서만 관리

이들 정보는 `login` 인스턴스를 통해 접근할 수 있습니다. 자세한 사용법은 [코딩 가이드라인 - Firebase 로그인 사용자 관리](./sns-web-coding-guideline.md#firebase-로그인-사용자-관리-login)를 참고하세요.

**단, `photoUrl`(camelCase)은 예외입니다:**

- ✅ **`photoUrl`** (camelCase) - 사용자가 직접 업로드한 프로필 사진 URL을 RTDB에 저장
- 이는 Firebase Auth의 `photoURL`(대문자)과 **다른 필드**입니다
- 사용자가 Firebase Storage에 사진을 업로드하면, 다운로드 URL을 `/users/<uid>/photoUrl`에 저장합니다

**필드명 차이 요약:**

| 필드 | 위치 | 설명 |
|------|------|------|
| `phoneNumber` | Firebase Auth | 전화번호 (login.phoneNumber로 접근) |
| `email` | Firebase Auth | 이메일 (login.email로 접근) |
| `photoURL` (대문자) | Firebase Auth | Firebase Auth 프로필 사진 |
| `photoUrl` (camelCase) | RTDB | 사용자 업로드 프로필 사진 (login.data.photoUrl로 접근) |

자세한 내용은 [사용자 관리 개발 가이드](./sns-web-user.md)와 [코딩 가이드라인](./sns-web-coding-guideline.md#firebase-로그인-사용자-관리-login)을 참고하세요.

### 관련 가이드

- **📖 구현 가이드**: [사용자 관리 개발 가이드](./sns-web-user.md) - 프로필 관리, 프로필 사진 업로드, 사용자 정보 조회
- **📖 파일 업로드**: [파일 및 사진 업로드 가이드](./sns-web-storage.md) - 프로필 사진 Firebase Storage 업로드
- **📖 보안**: [Firebase 보안 규칙 개발 가이드](./sns-web-security.md) - 사용자 프로필 접근 제어

---

## 사용자 속성 분리 (user-props)

특정 속성에 대한 대량 조회를 효율적으로 수행하기 위해 사용자 속성을 별도로 관리합니다.

### 데이터 구조

```
/user-props/
  /displayName/
    ├── <uid1>: "사용자1"
    ├── <uid2>: "사용자2"
    └── <uid3>: "사용자3"
  /photoUrl/
    ├── <uid1>: "https://..."
    ├── <uid2>: null
    └── <uid3>: "https://..."
  /createdAt/
    ├── <uid1>: 1698473000000
    ├── <uid2>: 1698473100000
    └── <uid3>: 1698473200000
  /updatedAt/
    ├── <uid1>: 1698474000000
    ├── <uid2>: 1698474100000
    └── <uid3>: 1698474200000
```

### 클라이언트/서버 역할 분리

사용자 속성 분리의 경우:
- **클라이언트는** 직접 저장하지 않으며, `/users/<uid>` 노드의 필드를 수정합니다.
- **서버는** `/users/<uid>` 노드의 변경을 감지하여 `/user-props/displayName/<uid>`, `/user-props/photoUrl/<uid>` 등의 필드를 자동으로 동기화합니다. (Cloud Functions)

### 관련 가이드

- **📖 구현 가이드**: [사용자 관리 개발 가이드 - 사용자 속성 분리](./sns-web-user.md#사용자-속성-분리-user-props) - 속성 분리 전략, 효율적인 대량 조회 방법

---

## 친구 관계 (friends, followers, following)

사용자 간의 관계를 관리합니다.

### 데이터 구조

```
/friends/
  <uid>/
    ├── <other-uid1>: 1698473000000
    ├── <other-uid2>: 1698473100000
    └── ...

/followers/
  <uid>/
    ├── <follower-uid1>: 1698473000000
    └── ...

/following/
  <uid>/
    ├── <following-uid1>: 1698473000000
    └── ...
```

### 설명

- **friends**: 상호 친구 관계 (양방향)
- **followers**: 나를 팔로우하는 사용자 (단방향 수신)
- **following**: 내가 팔로우하는 사용자 (단방향 발신)
- 각 값은 관계 형성 시간 (Unix timestamp, 밀리초)

### 클라이언트/서버 역할 분리

친구 관계의 경우:
- **클라이언트는** `/friends/<uid>/<other-uid>`, `/followers/<uid>/<follower-uid>`, `/following/<uid>/<following-uid>` 노드를 추가/삭제하여 친구 관계를 요청할 수 있고,
- **서버는** 친구 추가/삭제 시 양방향 관계 동기화를 자동으로 처리합니다. (Cloud Functions)
  - 예: A가 B를 팔로우하면 `/following/<A-uid>/<B-uid>`와 `/followers/<B-uid>/<A-uid>`가 모두 업데이트됨

### 관련 가이드

- **📖 구현 가이드**: [친구 관계 관리 개발 가이드](./sns-web-friends.md) - 친구 추가, 팔로우, 언팔로우, 친구 목록 조회
- **📖 사용자 정보**: [사용자 관리 개발 가이드](./sns-web-user.md) - 사용자 프로필 조회, 기본 정보
- **📖 보안 규칙**: [Firebase 보안 규칙 개발 가이드](./sns-web-security.md) - 친구 관계 접근 제어

---

## 채팅방 (chat-rooms)

채팅방 메타데이터는 `/chat-rooms/<roomId>/` 경로에 저장됩니다. 클라이언트는 방 이름, 설명, 공개 여부 등 기본 정보와 함께 `owner` 필드를 설정하며, 보안 규칙이 `owner`가 `auth.uid`와 일치하는지 검증합니다. Cloud Functions는 `createdAt`, `members` (owner 자동 추가), `memberCount` 필드를 자동으로 생성하고, `members` 필드 변경 시 `memberCount`를 자동으로 동기화합니다. 클라이언트는 자기 자신의 uid를 `members`에 추가/수정하여 채팅방 입장/퇴장 및 메시지 알림 설정을 관리합니다.

### 데이터 구조

```
/chat-rooms/
├── group-team123/
│   ├── name: "팀 채팅방"
│   ├── description: "Sprint 24 진행 채널"
│   ├── open: false
│   ├── type: "group"
│   ├── owner: "uid_creator"              // 클라이언트가 설정 (보안 규칙으로 검증)
│   ├── createdAt: 1698473000000          // Cloud Functions에서 자동 설정
│   ├── updatedAt: 1698473000000
│   ├── groupListOrder: "-1698473000000"
│   ├── members:
│   │   ├── uid_creator: true             // Cloud Functions에서 owner 자동 추가, 클라이언트는 자기 자신만 추가/수정 가능
│   │   └── uid_teammate: true            // 클라이언트가 입장 시 추가 (true=메시지 알림 받기, false=알림 안 받기)
│   ├── memberCount: 2                    // Cloud Functions에서 members 기반으로 자동 계산 (모든 uid 개수, true/false 구분 없이)
│   └── stats:
│       └── messageCount: 120
└── open-general/
    ├── name: "오픈 커뮤니티"
    ├── open: true
    ├── type: "open"
    ├── owner: "uid_creator"              // 클라이언트가 설정 (보안 규칙으로 검증)
    ├── createdAt: 1698474000000          // Cloud Functions에서 자동 설정
    ├── openListOrder: "-1698474000000"
    ├── members:
    │   ├── uid_creator: true             // 메시지 알림 받음
    │   ├── uid_user2: true               // 메시지 알림 받음
    │   └── uid_user3: false              // 메시지 알림 안 받음 (채팅방에는 참여 중)
    ├── memberCount: 3                    // Cloud Functions에서 members 기반으로 자동 계산 (모든 uid 개수, true/false 구분 없이)
    └── ...
```

### 필드 설명

| 필드 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `name` | string | ✅ | 채팅방 이름, 최대 50자 |
| `description` | string | ❌ | 소개 문구, 최대 200자 |
| `type` | string | ✅ | 채팅방 유형 (`group`, `open`, `single`) |
| `open` | boolean | ✅ | 오픈 챗 여부 (`true`면 전체 공개) |
| `owner` | string | ✅ | 방 생성자 UID. **클라이언트가 설정** (보안 규칙으로 `auth.uid`와 일치 검증) |
| `createdAt` | number | ✅ | 생성 시간 (Unix timestamp ms). **Cloud Functions에서만 설정** |
| `updatedAt` | number | ✅ | 수정 시간 (Unix timestamp ms) |
| `groupListOrder` | string/number | ❌ | 그룹 챗 정렬용 order. 클라이언트가 `-Date.now()` 형식으로 설정 |
| `openListOrder` | string/number | ❌ | 오픈 챗 정렬용 order. `open: true`일 때만 생성 |
| `members` | object | ✅ | 참여자 및 알림 설정 객체 (`{uid: boolean}`). **Cloud Functions에서 owner 자동 추가**, **클라이언트는 자기 자신만 추가/수정/삭제 가능**. `true`=메시지 알림 받음, `false`=알림 안 받음, `삭제됨`=채팅방에서 완전히 나감 |
| `memberCount` | number | ✅ | 전체 참여자 수. **Cloud Functions에서 자동 계산** (`members` 객체의 모든 uid 개수, true/false 구분 없이) |
| `stats` | object | ❌ | 메시지 수 등 확장 가능한 통계 필드 |

### 클라이언트/서버 역할 분리

#### 채팅방 생성 시

- **클라이언트**
  - `/chat-rooms/{roomId}`에 `name`, `description`, `open`, `type`, `owner` 등 기본 정보를 작성
  - `owner` 필드를 `auth.uid`로 설정 (보안 규칙이 `auth.uid`와 일치하는지 검증)
  - `createdAt`, `memberCount`, `members` 필드는 **작성하지 않음** (Cloud Functions에서만 설정)
- **Cloud Functions**
  - `onValueCreated("/chat-rooms/{roomId}")` 트리거 실행
  - `owner` 값을 읽어 생성자 UID 확인 (보안 규칙으로 이미 검증됨)
  - `createdAt` 필드를 현재 타임스탬프로 설정
  - `members` 객체에 `{owner: true}` 추가
  - `memberCount`를 1로 설정 (초기값)

#### 채팅방 입장/퇴장 시

- **클라이언트**
  - 채팅방 입장: `/chat-rooms/{roomId}/members/{uid}`를 `true`로 설정
    - `joinChatRoom(db, roomId, uid)` 함수 사용 (chat.functions.ts)
    - 메시지 알림을 받도록 설정
    - 보안 규칙: 자기 자신의 uid만 쓰기 가능
  - 채팅방 퇴장: `/chat-rooms/{roomId}/members/{uid}`를 `false`로 설정
    - `leaveChatRoom(db, roomId, uid)` 함수 사용 (chat.functions.ts)
    - 메시지 알림을 받지 않도록 설정
- **Cloud Functions**
  - `onValueWritten("/chat-rooms/{roomId}/members/{uid}")` 트리거 실행
  - `/chat-rooms/{roomId}/members` 아래의 모든 uid 읽기
  - `value === true`인 uid의 개수를 세어 `memberCount` 업데이트
  - 자동으로 활성 참여자 수를 동기화

> 🔐 **보안 규칙 연계**:
> - `/chat-rooms/{roomId}/owner`는 방이 존재하지 않을 때만 쓰기 가능하며, `newData.val() === auth.uid` 조건으로 제한
> - `/chat-rooms/{roomId}/createdAt`와 `/chat-rooms/{roomId}/memberCount`는 `.write: false`로 클라이언트 쓰기 금지
> - `/chat-rooms/{roomId}/members/{uid}`는 `$uid === auth.uid` 조건으로 자기 자신만 쓰기 가능
> - 따라서 사양상 owner 위조가 불가능하며, Cloud Functions만이 신뢰할 수 있는 값을 설정할 수 있습니다.

> 💡 **메시지 알림 관리**:
> - `members` 필드는 참여자 목록과 메시지 알림 수신 여부를 함께 관리합니다
> - `true`: 사용자가 채팅방에 참여 중이며 메시지 알림을 받음
> - `false`: 사용자가 채팅방에서 나갔거나 알림을 받지 않음
> - `memberCount`는 항상 `members` 필드에서 `true`인 uid의 개수와 동일하게 유지됩니다

---

## 채팅 메시지 (chat-messages)

채팅 메시지는 `/chat-messages/<messageId>/` 경로에 저장됩니다.
게시글과 댓글 기능을 통합하여 하나의 메시지 구조로 관리합니다.

### 데이터 구조

```
/chat-messages/
├── <messageId1>/
│   ├── roomId: "single-uid1-uid2"
│   ├── type: "text"
│   ├── text: "안녕하세요!"
│   ├── senderUid: "uid1"
│   ├── createdAt: 1698473000000
│   └── protocol: null
└── <messageId2>/
    ├── roomId: "group-roomid"
    ├── type: "image"
    ├── text: ""
    ├── imageUrl: "https://..."
    ├── senderUid: "uid2"
    ├── createdAt: 1698473100000
    └── protocol: null
```

### 필드 설명

| 필드 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `roomId` | string | ✅ | 채팅방 ID (1:1, 그룹, 오픈 채팅) |
| `type` | string | ✅ | 메시지 유형 (text, image, file 등) |
| `text` | string | ❌ | 메시지 텍스트 내용 |
| `senderUid` | string | ✅ | 발신자 UID |
| `createdAt` | number | ✅ | 메시지 생성 시간 (Unix timestamp, 밀리초) |
| `protocol` | string | ❌ | 프로토콜 메시지 유형 (join, leave 등 시스템 메시지) |
| `imageUrl` | string | ❌ | 이미지 메시지의 경우 이미지 URL |
| `fileUrl` | string | ❌ | 파일 메시지의 경우 파일 다운로드 URL |

### roomId 형식

채팅방 유형에 따라 roomId 형식이 다릅니다:

- **1:1 채팅**: `single-{uid1}-{uid2}` (알파벳 순 정렬)
  - 예: `single-abc123-xyz789`
  - 두 사용자의 UID를 알파벳 순으로 정렬하여 고정된 roomId 생성
- **그룹 채팅**: `group-{roomId}`
  - 예: `group-team123`
- **오픈 채팅**: `open-{roomId}`
  - 예: `open-general`

### 클라이언트/서버 역할 분리

채팅 메시지의 경우:
- **클라이언트는** `roomId`, `type`, `text`, `senderUid`, `createdAt` 등 메시지 기본 정보를 저장합니다.
- **서버는** 메시지 생성을 감지하여 다음 작업을 자동으로 수행합니다:
  - 1:1 채팅의 경우 양쪽 사용자의 `chat-joins` 자동 생성/업데이트 (Cloud Functions)
  - 그룹/오픈 채팅의 경우 참여자 목록 기반 `chat-joins` 업데이트 (추후 구현)
  - 읽지 않은 메시지 카운터 업데이트 (추후 구현)

### 관련 가이드

- **📖 구현 가이드**: [채팅 기능 개발 가이드](./sonub-chat-room.md) - 채팅방 생성, 메시지 전송, 실시간 메시지 수신
- **📖 Cloud Functions**: [Firebase Cloud Functions 개발 가이드](./sonub-firebase-functions.md) - 채팅 메시지 처리 로직
- **📖 파일 업로드**: [파일 및 사진 업로드 가이드](./sns-web-storage.md) - 이미지/파일 메시지 업로드

---

## 채팅방 참여 (chat-joins)

채팅방 참여 정보는 `/chat-joins/<uid>/<roomId>/` 경로에 저장됩니다.
각 사용자가 참여한 채팅방 목록을 관리하며, 채팅방 목록 화면에서 사용됩니다.

### 데이터 구조

```
/chat-joins/
├── <uid1>/
│   ├── single-uid1-uid2/
│   │   ├── roomId: "single-uid1-uid2"
│   │   ├── roomType: "single"
│   │   ├── partnerUid: "uid2"
│   │   ├── lastMessageText: "안녕하세요!"
│   │   ├── lastMessageAt: 1698473000000
│   │   ├── joinedAt: 1698472000000
│   │   ├── updatedAt: 1698473000000
│   │   ├── newMessageCount: 0
│   │   ├── listOrder: "1698473000000"  // (사용 중단 예정, 후방 호환성용)
│   │   ├── singleChatListOrder: "1698473000000"  // 읽지 않은 메시지 없음
│   │   └── allChatListOrder: 1698473000000  // 전체 채팅방 목록용
│   ├── group-team123/
│   │   ├── roomId: "group-team123"
│   │   ├── roomType: "group"
│   │   ├── roomName: "팀 회의방"
│   │   ├── lastMessageText: "회의 시작합니다"
│   │   ├── lastMessageAt: 1698474000000
│   │   ├── joinedAt: 1698472500000
│   │   ├── updatedAt: 1698474000000
│   │   ├── newMessageCount: 3
│   │   ├── listOrder: "2001698474000000"  // (사용 중단 예정, 후방 호환성용)
│   │   ├── groupChatListOrder: "2001698474000000"  // 읽지 않은 메시지 3개
│   │   ├── openAndGroupChatListOrder: 1698474000000  // 그룹+오픈 통합 목록용
│   │   └── allChatListOrder: 1698474000000  // 전체 채팅방 목록용
│   └── open-discussion/
│       ├── roomId: "open-discussion"
│       ├── roomType: "open"
│       ├── roomName: "자유 토론방"
│       ├── lastMessageText: "누구나 환영합니다"
│       ├── lastMessageAt: 1698475000000
│       ├── joinedAt: 1698473000000
│       ├── updatedAt: 1698475000000
│       ├── newMessageCount: 0
│       ├── listOrder: "1698475000000"  // (사용 중단 예정, 후방 호환성용)
│       ├── openChatListOrder: "1698475000000"  // 읽지 않은 메시지 없음
│       ├── openAndGroupChatListOrder: 1698475000000  // 그룹+오픈 통합 목록용
│       └── allChatListOrder: 1698475000000  // 전체 채팅방 목록용
└── <uid2>/
    └── single-uid1-uid2/
        ├── roomId: "single-uid1-uid2"
        ├── roomType: "single"
        ├── partnerUid: "uid1"
        ├── lastMessageText: "안녕하세요!"
        ├── lastMessageAt: 1698473000000
        ├── joinedAt: 1698472000000
        ├── updatedAt: 1698473000000
        ├── newMessageCount: 1
        ├── listOrder: "2001698473000000"  // (사용 중단 예정, 후방 호환성용)
        ├── singleChatListOrder: "2001698473000000"  // 읽지 않은 메시지 1개
        └── allChatListOrder: 1698473000000  // 전체 채팅방 목록용
```

### 필드 설명

| 필드 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `roomId` | string | ✅ | 채팅방 ID |
| `roomType` | string | ✅ | 채팅방 유형 (single, group, open) |
| `roomName` | string | ❌ | 채팅방 이름 (그룹/오픈 채팅방의 경우) |
| `partnerUid` | string | ❌ | 1:1 채팅의 상대방 UID (1:1 채팅만 해당) |
| `lastMessageText` | string | ❌ | 마지막 메시지 내용 (미리보기용) |
| `lastMessageAt` | number | ✅ | 마지막 메시지 시간 (Unix timestamp, 밀리초) |
| `joinedAt` | number | ✅ | 채팅방 참여 시간 (Unix timestamp, 밀리초) |
| `updatedAt` | number | ✅ | 마지막 업데이트 시간 (Unix timestamp, 밀리초) |
| `newMessageCount` | number | ✅ | 읽지 않은 메시지 개수 (Cloud Functions가 자동 증감) |
| `listOrder` | string | ❌ | **정렬 필드** (후방 호환성용, 사용 중단 예정) |
| `singleChatListOrder` | string | ❌ | **1:1 채팅 정렬 필드** (prefix + timestamp) |
| `groupChatListOrder` | string/number | ❌ | **그룹 채팅 정렬 필드** (prefix + timestamp) |
| `openChatListOrder` | string/number | ❌ | **오픈 채팅 정렬 필드** (prefix + timestamp) |
| `openAndGroupChatListOrder` | number | ❌ | **그룹+오픈 통합 정렬 필드** (timestamp) |
| `allChatListOrder` | number | ❌ | **전체 채팅방 통합 정렬 필드** (timestamp, 모든 타입) |

### 🔥 정렬 필드 상세 설명

채팅방 목록을 다양한 방식으로 정렬하기 위해 여러 정렬 필드가 제공됩니다.

#### 정렬 필드 개요

| 필드 | 용도 | 타입 | Cloud Functions 설정 |
|------|------|------|---------------------|
| `singleChatListOrder` | 1:1 채팅만 정렬 | string (prefix + timestamp) | ✅ 자동 |
| `groupChatListOrder` | 그룹 채팅만 정렬 | string (prefix + timestamp) | ✅ 자동 |
| `openChatListOrder` | 오픈 채팅만 정렬 | string (prefix + timestamp) | ✅ 자동 |
| `openAndGroupChatListOrder` | 그룹+오픈 통합 정렬 | number (timestamp) | ✅ 자동 |
| `allChatListOrder` | 모든 채팅방 통합 정렬 | number (timestamp) | ✅ 자동 |
| `listOrder` | (후방 호환성용) | string | ⚠️ 사용 중단 |

#### 왜 정렬 필드가 필요한가?

Firebase Realtime Database에서 채팅방 목록을 정렬할 때 다음 요구사항을 만족해야 합니다:

1. ❌ **단순 timestamp 정렬**: `orderByChild('lastMessageAt')` 사용 시
   - 문제점: 클라이언트에서 reverse() 필요 (성능 저하)
   - 문제점: 읽지 않은 메시지가 있는 채팅방을 맨 위에 표시할 수 없음
   - 문제점: PIN(고정) 기능 구현 불가

2. ✅ **prefix + timestamp 사용**: 타입별 정렬 필드 사용
   - 장점: Firebase 쿼리만으로 내림차순 정렬 가능
   - 장점: 읽지 않은 메시지가 있는 채팅방을 맨 위에 자동 배치
   - 장점: PIN 기능을 prefix로 쉽게 구현 가능
   - 장점: 서버에서 자동으로 관리되어 데이터 일관성 보장

#### 정렬 필드 계산 방식 (Cloud Functions)

```typescript
// Cloud Functions에서 자동으로 계산
const timestamp = messageData.createdAt || Date.now();

// === 1:1 채팅 ===
// 발신자: 읽음 상태 (prefix 없음)
const senderSingleListOrder = `${timestamp}`;  // "1698473000000"
// 수신자: 읽지 않은 상태 (200 prefix)
const partnerSingleListOrder = `200${timestamp}`;  // "2001698473000000"

// === 그룹 채팅 ===
// 발신자: 읽음 상태
const senderGroupListOrder = `${timestamp}`;
// 수신자들: 읽지 않은 상태 (200 prefix)
const memberGroupListOrder = `200${timestamp}`;

// === 오픈 채팅 ===
// 발신자: 읽음 상태
const senderOpenListOrder = `${timestamp}`;
// 수신자들: 읽지 않은 상태 (200 prefix)
const memberOpenListOrder = `200${timestamp}`;

// === 통합 정렬 필드 ===
// 모든 사용자에게 동일한 timestamp (읽음/읽지않음 구분 없음)
const allChatListOrder = timestamp;  // 1698473000000
const openAndGroupChatListOrder = timestamp;  // 그룹+오픈만
```

#### prefix 규칙

| Prefix | 상태 | 설명 | 예시 값 |
|--------|------|------|---------|
| 없음 | 읽음 상태 | 메시지를 읽은 채팅방 (일반 정렬) | `1698473000000` |
| `200` | 읽지 않음 | 읽지 않은 메시지가 있는 채팅방 (맨 위 정렬) | `2001698473000000` |
| `500` | PIN 고정 | 사용자가 고정한 채팅방 (최상위 정렬) | `5001698473000000` |

#### 정렬 원리

Firebase는 문자열을 **사전순(lexicographical order)**으로 정렬하고, `reverse()`로 내림차순 정렬합니다:

```
내림차순 정렬 결과 (최신순):
"5001698474000000"  (PIN 고정 채팅방)          ← 최상위
"2001698473000000"  (읽지 않은 메시지 있음)    ← 맨 위
"2001698472000000"  (읽지 않은 메시지 있음)
"1698471000000"     (읽음 상태, 최신 메시지)
"1698470000000"     (읽음 상태, 오래된 메시지)  ← 맨 아래
```

- **500 prefix**: PIN 고정 채팅방은 항상 최상위
- **200 prefix**: 읽지 않은 메시지가 있는 채팅방은 읽음 채팅방보다 위
- **prefix 없음**: 읽음 상태 채팅방은 일반 timestamp 정렬

#### 읽음 처리 (정렬 필드 업데이트)

사용자가 채팅방에 입장하면 클라이언트에서 200 prefix를 제거합니다:

```typescript
// 사용자가 채팅방 입장 시
const currentListOrder = "2001698473000000";  // 읽지 않은 상태

// 200 prefix 제거 (읽음 처리)
if (currentListOrder.startsWith("200")) {
  const newListOrder = currentListOrder.substring(3);  // "1698473000000"

  // 채팅방 타입에 따라 적절한 필드 업데이트
  if (roomType === 'single') {
    await database.ref(`chat-joins/${uid}/${roomId}/singleChatListOrder`).set(newListOrder);
  } else if (roomType === 'group') {
    await database.ref(`chat-joins/${uid}/${roomId}/groupChatListOrder`).set(newListOrder);
  } else if (roomType === 'open') {
    await database.ref(`chat-joins/${uid}/${roomId}/openChatListOrder`).set(newListOrder);
  }

  // 읽지 않은 메시지 카운터도 0으로 초기화
  await database.ref(`chat-joins/${uid}/${roomId}/newMessageCount`).set(0);
}
```

#### 클라이언트에서 사용 예시

```typescript
// 예시 1: 1:1 채팅만 조회
const query = database.ref(`chat-joins/${uid}`)
  .orderByChild('singleChatListOrder')
  .limitToLast(20);

// 예시 2: 그룹 채팅만 조회
const query = database.ref(`chat-joins/${uid}`)
  .orderByChild('groupChatListOrder')
  .limitToLast(20);

// 예시 3: 그룹+오픈 채팅 통합 조회
const query = database.ref(`chat-joins/${uid}`)
  .orderByChild('openAndGroupChatListOrder')
  .limitToLast(20);

// 예시 4: 모든 채팅방 통합 조회
const query = database.ref(`chat-joins/${uid}`)
  .orderByChild('allChatListOrder')
  .limitToLast(20);

// 결과 처리 (공통)
query.on('value', (snapshot) => {
  const chatRooms = [];
  snapshot.forEach((child) => {
    chatRooms.push(child.val());
  });
  chatRooms.reverse(); // 최신순 정렬
  console.log(chatRooms);
});
```

#### 주의사항

- ⚠️ **발신자의 정렬 필드**: prefix 없이 timestamp만 저장 (읽음 상태)
- ⚠️ **수신자의 정렬 필드**: 200 prefix + timestamp 저장 (읽지 않은 상태)
- ✅ **Cloud Functions가 메시지 생성 시 자동으로 모든 정렬 필드를 업데이트**합니다
- ✅ **클라이언트는 읽음 처리/PIN 기능만 직접 수정**합니다
- ⚠️ 타입별 정렬 필드(single/group/open)는 문자열 타입이지만 사전순으로 정렬됩니다
- ⚠️ 통합 정렬 필드(all/openAndGroup)는 숫자 타입입니다
- ⚠️ `newMessageCount`와 함께 사용하여 읽지 않은 메시지 개수를 표시합니다

### 클라이언트/서버 역할 분리

채팅방 참여 정보의 경우:
- **클라이언트는** 다음 작업을 수행합니다:
  - **채팅방 입장 시** `chat-joins/{uid}/{roomId}` 노드 생성 (모든 채팅방 타입: 1:1, 그룹, 오픈)
  - 채팅방 입장 시 각 정렬 필드의 200 prefix 제거 (읽음 처리)
    - 예: `singleChatListOrder`의 "200{timestamp}"를 "{timestamp}"로 변경
  - 채팅방 입장 시 `newMessageCount`를 0으로 초기화
  - 채팅방 PIN 고정/해제 시 각 정렬 필드의 500 prefix 추가/제거
    - 예: `singleChatListOrder`의 "{timestamp}"를 "500{timestamp}"로 변경

- **서버는** 채팅 메시지 생성을 감지하여 다음 작업을 자동으로 수행합니다 (Cloud Functions):

  **1:1 채팅의 경우:**
  - 양쪽 사용자의 `chat-joins/{uid}/{roomId}` 자동 생성/업데이트
  - `lastMessageText`, `lastMessageAt` 자동 업데이트
  - **정렬 필드 자동 계산 및 업데이트**:
    - 발신자:
      - `singleChatListOrder`: `${timestamp}` (prefix 없음, 읽음 상태)
      - `allChatListOrder`: `timestamp` (숫자 타입)
    - 수신자:
      - `singleChatListOrder`: `200${timestamp}` (200 prefix, 읽지 않은 상태)
      - `allChatListOrder`: `timestamp` (숫자 타입)
  - **`newMessageCount` 자동 증가**: 수신자만 increment(1)
  - `joinedAt`는 최초 생성 시에만 설정 (기존 값이 있으면 유지)
  - `partnerUid` 설정 (1:1 채팅 상대방 UID)

  **그룹/오픈 채팅의 경우:**
  - `chat-rooms/{roomId}`에서 `members` 목록 조회
  - 모든 멤버의 `chat-joins/{memberUid}/{roomId}` 자동 업데이트
  - 각 멤버에 대해 다음 필드 자동 설정:
    - `roomName`: 채팅방 이름 (chat-rooms에서 가져옴)
    - `lastMessageText`: 마지막 메시지 내용
    - `lastMessageAt`: 마지막 메시지 시간
    - `allChatListOrder`: timestamp (숫자 타입)
    - `openAndGroupChatListOrder`: timestamp (숫자 타입)
    - **타입별 정렬 필드**:
      - 그룹 채팅인 경우:
        - 발신자: `groupChatListOrder`: `${timestamp}`
        - 다른 멤버들: `groupChatListOrder`: `200${timestamp}`
      - 오픈 채팅인 경우:
        - 발신자: `openChatListOrder`: `${timestamp}`
        - 다른 멤버들: `openChatListOrder`: `200${timestamp}`
  - **`newMessageCount` 자동 증가**: 발신자를 제외한 모든 멤버에 대해 increment(1)
  - `joinedAt`는 최초 생성 시에만 설정 (기존 값이 있으면 유지)

### 관련 가이드

- **📖 구현 가이드**: [채팅 기능 개발 가이드](./sonub-chat-room.md) - 채팅방 목록 조회, 정렬, 필터링
- **📖 Cloud Functions**: [Firebase Cloud Functions 개발 가이드](./sonub-firebase-functions.md) - chat-joins 자동 생성 로직
- **📖 보안 규칙**: [Firebase 보안 규칙 개발 가이드](./sns-web-security.md) - 채팅방 참여 정보 접근 제어

---

## 주요 설계 원칙

### 1. Flat Style 구조

- 데이터를 단순하고 평탄한 구조로 저장
- 복잡한 다단계 노드 구조 회피
- 쿼리 효율성과 성능 극대화

### 2. 속성 분리

- 특정 속성에 대한 대량 조회가 필요한 경우 별도 경로에서 관리
- 예: `user-props/displayName`
- 네트워크 최적화 및 쿼리 성능 향상

### 3. Cloud Functions 활용

클라이언트와 백엔드의 역할을 명확히 분리하여 데이터 무결성과 성능을 보장합니다.

**원칙:**
- ✅ **클라이언트는 최소한의 데이터만 RTDB에 기록**
  - 사용자가 직접 입력한 데이터 (게시글, 댓글 내용 등)
  - 기본 메타데이터 (uid, createdAt, category)
- ✅ **백엔드(Cloud Functions)는 추가 데이터 자동 처리**
  - 카운터 자동 증가/감소 (likeCount, commentCount, postCount)
  - 전체 통계 자동 집계 (stats/counters)
  - 사용자 속성 분리 자동 동기화 (user-props)
  - 데이터 무결성 보장

**클라이언트에서 하지 말아야 할 작업:**
- ❌ 카운터 직접 증가/감소 (`increment()` 사용 금지)
- ❌ 데이터 집계 및 통계 직접 계산
- ❌ 속성 분리 작업 직접 수행
- ❌ 복잡한 비즈니스 로직 처리

**구체적인 예시와 구현 방법은 각 기능별 개발 가이드 문서를 참고하세요:**
- [Firebase Cloud Functions 개발 가이드](./sns-firebase-cloud-functions.md) - 백엔드 로직 구현 상세 가이드

### 4. 보안 규칙

- Firebase Security Rules로 접근 권한 제어
- 각 데이터 섹션별로 읽기/쓰기 권한 정의
- 데이터 검증 규칙 포함

---

## 주의사항

### Firebase Auth vs RTDB 필드명 차이

**프로필 사진 필드명이 다릅니다:**
- **Firebase Auth**: `photoURL` (대문자 URL)
- **RTDB**: `photoUrl` (camelCase url)

자세한 내용과 구현 예제는 [사용자 관리 개발 가이드](./sns-web-user.md) 및 [코딩 가이드라인 - Firebase Auth vs RTDB 필드명 차이](../CLAUDE.md#firebase-auth-vs-rtdb-필드명-차이-매우-중요)를 참고하세요.

---

## 관련 가이드 문서

전체 데이터베이스 구조와 관련된 상세한 개발 가이드:

- **[사용자 관리](./sns-web-user.md)** - 사용자 프로필, 속성 분리, Cloud Functions
- **[친구 관계](./sns-web-friends.md)** - 친구, 팔로우, 팔로워 관리
- **[Firebase 보안](./sns-web-security.md)** - 보안 규칙 설정

---

## 참고 자료

- [Firebase Realtime Database 공식 문서](https://firebase.google.com/docs/database)
- [Firebase Security Rules 공식 문서](https://firebase.google.com/docs/rules)
- [Firebase Cloud Functions 공식 문서](https://firebase.google.com/docs/functions)

---

## 작업 이력 (SED Log)

| 날짜 | 작업자 | 내용 |
| ---- | ------ | ---- |
| 2025-11-12 | Codex Agent | `/chat-rooms` 섹션을 신설하고 Cloud Functions + Security Rules가 owner/createdBy를 자동 설정·검증하는 과정을 상세화하여 채팅방 데이터 흐름을 명시. |
| 2025-11-12 | Claude Code | `roomTitle` 필드를 사양에 맞게 `roomName`으로 통일. 클라이언트(ChatCreateDialog.svelte)와 Firebase Functions 타입 정의(types/index.ts)에서 `roomTitle`을 `roomName`으로 변경. 후방 호환성을 위해 읽기 함수(resolveRoomTitle)는 `roomTitle`과 `roomName` 둘 다 체크하도록 유지. |
| 2025-11-12 | Claude Code | `listOrder` 필드를 모든 소스 코드와 스펙 문서에서 완전히 제거. 채팅방 타입별 전용 정렬 필드 사용으로 변경: 1) types/index.ts에서 `listOrder` 필드 삭제 2) chat.handler.ts의 멤버 입장 로직에서 타입별 정렬 필드 자동 설정 (groupChatListOrder, openChatListOrder, openAndGroupChatListOrder, allChatListOrder) 3) chat/list/+page.svelte에서 `allChatListOrder` 사용 (모든 채팅 통합 목록) 4) group-chat-list/+page.svelte에서 `openAndGroupChatListOrder` 사용 (그룹+오픈 통합 목록) 5) database.rules.json 인덱스 업데이트 6) Firebase Functions 배포 완료. |
| 2025-11-12 | Claude Code | `handleChatJoinCreate` 함수 보완하여 클라이언트가 `chat-joins/{uid}/{roomId}` 노드를 직접 생성할 때 타입별 정렬 필드가 자동 생성되도록 수정: 1) 1:1 채팅 감지 로직 추가 (`isSingleChat()` 활용) 2) 1:1 채팅 시 `singleChatListOrder`, `allChatListOrder`, `partnerUid`, `roomType` 자동 설정 3) 그룹/오픈 채팅 시 `chat-rooms` 조회 후 `roomType`, `roomName`, `allChatListOrder` 및 타입별 정렬 필드 설정 4) 이미 완전히 설정된 경우 (`joinedAt` + `roomType` 존재) 건너뛰기 최적화 5) `index.ts`의 `onChatJoinCreate` JSDoc 주석 업데이트하여 새 로직 반영 6) Firebase Functions 배포 완료. |
| 2025-11-13 | Claude Code | 친구 페이지(chat/list) 정렬 필드 수정: 1:1 채팅방만 표시하도록 `allChatListOrder` → `singleChatListOrder` 변경. `chat/list/+page.svelte`의 `JOIN_ORDER_FIELD` 상수 및 템플릿 내 변수명 업데이트. |
| 2025-11-13 | Claude Code | 클라이언트 채팅방 입장 함수 수정 및 `displayNameLowerCase` 자동 생성 로직 개선: 1) `chat.functions.ts`의 `createSingleChatJoin` 함수명을 `enterSingleChatRoom`으로 변경하고 `set()` 대신 `update()` 사용으로 기존 데이터 보존하도록 수정 2) `user.handler.ts`의 `handleUserUpdate`에서 `displayNameLowerCase` 필드가 없으면 자동 생성하도록 로직 개선 (기존: displayName 변경 시에만 생성 → 개선: displayNameLowerCase 필드 없거나 displayName 변경 시 생성) 3) `types/index.ts`의 `UserData` 인터페이스에 `displayNameLowerCase` 필드 추가 4) Firebase Functions 배포 완료. |
| 2025-11-13 | Claude Code | `handleChatRoomMemberJoin` 함수 개선: 사용자가 채팅방에 입장할 때 마지막 메시지 정보를 자동으로 저장하도록 수정. 1) `chat-messages`에서 `roomOrder` 필드로 해당 채팅방의 마지막 메시지 조회 (limitToLast(1) 사용) 2) 마지막 메시지의 `text`와 `createdAt` 값을 읽어서 `lastMessageText`, `lastMessageAt`으로 변환 3) `chat-joins/{uid}/{roomId}`에 마지막 메시지 정보 저장 (메시지가 있는 경우에만) 4) 함수 JSDoc 주석 업데이트 5) Firebase Functions 빌드 및 배포 완료. |
| 2025-11-13 | Claude Code | `/chat-joins/` 데이터 구조 예제 보완: 누락되었던 정렬 필드들을 모든 채팅방 타입 예제에 추가하여 문서 완성도 향상. 1) 1:1 채팅 예제에 `singleChatListOrder`, `allChatListOrder` 추가 2) 그룹 채팅 예제에 `groupChatListOrder`, `openAndGroupChatListOrder`, `allChatListOrder` 및 `roomName` 추가 3) 오픈 채팅 예제 신규 추가 (`open-discussion`) - `openChatListOrder`, `openAndGroupChatListOrder`, `allChatListOrder` 포함 4) 각 정렬 필드에 설명 주석 추가 (읽지 않은 메시지 개수, 용도 등) 5) `listOrder` 필드에 사용 중단 예정 주석 추가. 이로써 데이터 구조 예제가 필드 설명 테이블과 일치하게 되어 개발자가 실제 구조를 더 잘 이해할 수 있게 됨. |
| 2025-11-13 | Claude Code | 채팅방 입장 시 `newMessageCount` 자동 초기화 기능 구현: 사용자가 채팅방에 입장할 때마다 `/chat-joins/{uid}/{roomId}/newMessageCount`를 0으로 초기화하여 메시지를 모두 읽은 것으로 표시. 1) `enterSingleChatRoom()` 함수 수정 (1:1 채팅용) - `chat-joins` 업데이트 시 `newMessageCount: 0` 추가 2) `joinChatRoom()` 함수 수정 (그룹/오픈 채팅용) - members 등록 후 `chat-joins`에도 `newMessageCount: 0` 설정 추가 3) 두 함수의 JSDoc 주석 업데이트하여 새 동작 문서화. 이로써 사용자가 채팅방 페이지(`/chat/room`)에 들어갈 때마다 `$effect` 훅이 자동으로 실행되어 읽지 않은 메시지 카운트가 0으로 초기화됨. 수정 파일: `src/lib/functions/chat.functions.ts` |
| 2025-11-13 | Claude Code | 채팅방 헤더 메뉴 기능 구현: `/chat/room` 페이지 상단에 네비게이션 및 메뉴 추가. 1) **헤더 구조**: 뒤로가기 버튼, 채팅 정보 (1:1: 프로필 사진+이름 / 그룹·오픈: 방 이름), 메뉴 버튼을 가로로 배치 2) **드롭다운 메뉴**: shadcn-svelte의 `DropdownMenu` 컴포넌트 활용하여 7개 메뉴 항목 구현 (북마크, 핀: 상단고정, URL 복사, 멤버 목록, 방 탈퇴하기, 신고하고 탈퇴하기, 닫기) 3) **기능 구현**: `handleGoBack()` (채팅 목록으로 이동), `handleCopyUrl()` (현재 URL 클립보드 복사), `handleLeaveRoom()` (`leaveChatRoom()` 호출 후 목록 이동, 확인 다이얼로그 포함) 4) **타입 유틸리티 추가**: `$lib/utils.ts`에 shadcn-svelte 컴포넌트용 타입 추가 (`WithElementRef`, `WithoutChild`, `WithoutChildrenOrChild`) 5) **shadcn-svelte 설치**: `npx shadcn-svelte add dropdown-menu` 실행 6) 일부 메뉴는 TODO placeholder로 향후 구현 예정 (북마크, 핀, 멤버 목록, 신고). 수정 파일: `src/routes/chat/room/+page.svelte`, `src/lib/utils.ts` |
