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
└── chat-joins/               # 채팅방 참여 정보 (채팅방 목록용)
```

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
│   │   ├── listOrder: "1698473000000"
│   │   └── newMessageCount: 0
│   └── group-team123/
│       ├── roomId: "group-team123"
│       ├── roomType: "group"
│       ├── lastMessageText: "회의 시작합니다"
│       ├── lastMessageAt: 1698474000000
│       ├── joinedAt: 1698472500000
│       ├── updatedAt: 1698474000000
│       ├── listOrder: "2001698474000000"
│       └── newMessageCount: 3
└── <uid2>/
    └── single-uid1-uid2/
        ├── roomId: "single-uid1-uid2"
        ├── roomType: "single"
        ├── partnerUid: "uid1"
        ├── lastMessageText: "안녕하세요!"
        ├── lastMessageAt: 1698473000000
        ├── joinedAt: 1698472000000
        ├── updatedAt: 1698473000000
        ├── listOrder: "2001698473000000"
        └── newMessageCount: 1
```

### 필드 설명

| 필드 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `roomId` | string | ✅ | 채팅방 ID |
| `roomType` | string | ✅ | 채팅방 유형 (single, group, open) |
| `partnerUid` | string | ❌ | 1:1 채팅의 상대방 UID (1:1 채팅만 해당) |
| `lastMessageText` | string | ❌ | 마지막 메시지 내용 (미리보기용) |
| `lastMessageAt` | number | ✅ | 마지막 메시지 시간 (Unix timestamp, 밀리초) |
| `joinedAt` | number | ✅ | 채팅방 참여 시간 (Unix timestamp, 밀리초) |
| `updatedAt` | number | ✅ | 마지막 업데이트 시간 (Unix timestamp, 밀리초) |
| `listOrder` | string | ✅ | **정렬 필드** (prefix + timestamp, 최신순/PIN 정렬용) |
| `newMessageCount` | number | ✅ | 읽지 않은 메시지 개수 (Cloud Functions가 자동 증감) |

### 🔥 listOrder 필드 상세 설명

`listOrder`는 **채팅방 목록을 최신 메시지 순으로 정렬하고, 읽지 않은 메시지/PIN 상태를 구분**하기 위한 특수 필드입니다.

#### 왜 listOrder가 필요한가?

Firebase Realtime Database에서 채팅방 목록을 정렬할 때 다음 요구사항을 만족해야 합니다:

1. ❌ **단순 timestamp 정렬**: `orderByChild('lastMessageAt')` 사용 시
   - 문제점: 클라이언트에서 reverse() 필요 (성능 저하)
   - 문제점: 읽지 않은 메시지가 있는 채팅방을 맨 위에 표시할 수 없음
   - 문제점: PIN(고정) 기능 구현 불가

2. ✅ **prefix + timestamp 사용**: `listOrder = "prefix + ${timestamp}"` 형식으로 저장
   - 장점: Firebase 쿼리만으로 내림차순 정렬 가능 (reverse() 사용)
   - 장점: 읽지 않은 메시지가 있는 채팅방을 맨 위에 자동 배치
   - 장점: PIN 기능을 prefix로 쉽게 구현 가능
   - 장점: 서버에서 자동으로 관리되어 데이터 일관성 보장

#### listOrder 계산 방식

```typescript
// Cloud Functions에서 자동으로 계산
const timestamp = messageData.createdAt || Date.now();

// 발신자: 읽음 상태 (prefix 없음)
const senderListOrder = `${timestamp}`;  // "1698473000000"

// 수신자: 읽지 않은 상태 (200 prefix 추가)
const partnerListOrder = `200${timestamp}`;  // "2001698473000000"

// PIN 기능: 사용자가 채팅방을 고정하면 500 prefix 사용
const pinnedListOrder = `500${timestamp}`;  // "5001698473000000"
```

#### listOrder prefix 규칙

| Prefix | 상태 | 설명 | 예시 값 |
|--------|------|------|---------|
| 없음 | 읽음 상태 | 메시지를 읽은 채팅방 (일반 정렬) | `1698473000000` |
| `200` | 읽지 않음 | 읽지 않은 메시지가 있는 채팅방 (맨 위 정렬) | `2001698473000000` |
| `500` | PIN 고정 | 사용자가 고정한 채팅방 (최상위 정렬) | `5001698473000000` |

#### listOrder 정렬 원리

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

#### 읽음 처리 (listOrder 업데이트)

사용자가 채팅방에 입장하면 클라이언트에서 200 prefix를 제거합니다:

```typescript
// 사용자가 채팅방 입장 시
const currentListOrder = "2001698473000000";  // 읽지 않은 상태

// 200 prefix 제거 (읽음 처리)
if (currentListOrder.startsWith("200")) {
  const newListOrder = currentListOrder.substring(3);  // "1698473000000"
  await database.ref(`chat-joins/${uid}/${roomId}/listOrder`).set(newListOrder);

  // 읽지 않은 메시지 카운터도 0으로 초기화
  await database.ref(`chat-joins/${uid}/${roomId}/newMessageCount`).set(0);
}

// PIN 채팅방은 prefix 제거하지 않음
if (currentListOrder.startsWith("500")) {
  // 500 prefix는 유지 (항상 맨 위에 고정)
  // newMessageCount만 0으로 초기화
  await database.ref(`chat-joins/${uid}/${roomId}/newMessageCount`).set(0);
}
```

#### PIN 고정 기능

사용자가 채팅방을 고정/해제할 때:

```typescript
// 채팅방 고정
const currentListOrder = "1698473000000";
const pinnedListOrder = `500${currentListOrder}`;  // "5001698473000000"
await database.ref(`chat-joins/${uid}/${roomId}/listOrder`).set(pinnedListOrder);

// 채팅방 고정 해제
const currentListOrder = "5001698473000000";
if (currentListOrder.startsWith("500")) {
  const unpinnedListOrder = currentListOrder.substring(3);  // "1698473000000"
  await database.ref(`chat-joins/${uid}/${roomId}/listOrder`).set(unpinnedListOrder);
}
```

#### 클라이언트에서 사용 예시

```typescript
// 채팅방 목록 조회 (내림차순 정렬: PIN → 읽지 않음 → 읽음)
const chatJoinsRef = database.ref(`chat-joins/${uid}`);
const query = chatJoinsRef
  .orderByChild('listOrder')  // listOrder로 정렬
  .limitToLast(20);           // 최신 20개

query.on('value', (snapshot) => {
  const chatRooms = [];
  snapshot.forEach((child) => {
    chatRooms.push(child.val());
  });

  // reverse()로 내림차순 변환 (큰 값부터)
  chatRooms.reverse();

  // 정렬 순서:
  // 1. PIN 고정 (500 prefix)
  // 2. 읽지 않음 (200 prefix)
  // 3. 읽음 (prefix 없음)
  console.log(chatRooms);
});
```

#### 주의사항

- ⚠️ **발신자의 listOrder**: prefix 없이 timestamp만 저장 (읽음 상태)
- ⚠️ **수신자의 listOrder**: 200 prefix + timestamp 저장 (읽지 않은 상태)
- ✅ **Cloud Functions가 메시지 생성 시 자동으로 업데이트**합니다
- ✅ **클라이언트는 읽음 처리/PIN 기능만 직접 수정**합니다
- ⚠️ `listOrder`는 문자열 타입이지만 사전순으로 정렬됩니다
- ⚠️ `newMessageCount`와 함께 사용하여 읽지 않은 메시지 개수를 표시합니다

### 클라이언트/서버 역할 분리

채팅방 참여 정보의 경우:
- **클라이언트는** `chat-joins` 노드를 직접 생성하지 않지만, 다음 작업은 수행할 수 있습니다:
  - 채팅방 입장 시 `listOrder`의 200 prefix 제거 (읽음 처리)
  - 채팅방 입장 시 `newMessageCount`를 0으로 초기화
  - 채팅방 PIN 고정/해제 시 `listOrder`의 500 prefix 추가/제거
- **서버는** 채팅 메시지 생성을 감지하여 다음 작업을 자동으로 수행합니다 (Cloud Functions):
  - 1:1 채팅의 경우 양쪽 사용자의 `chat-joins/{uid}/{roomId}` 자동 생성/업데이트
  - `lastMessageText`, `lastMessageAt`, `updatedAt` 자동 업데이트
  - **`listOrder` 자동 계산 및 업데이트**:
    - 발신자: `${timestamp}` (prefix 없음, 읽음 상태)
    - 수신자: `200${timestamp}` (200 prefix, 읽지 않은 상태)
  - **`newMessageCount` 자동 증가**: 수신자의 카운터만 increment(1)
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
