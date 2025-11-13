---
name: database.rules.json
description: Firebase Realtime Database 보안 규칙 파일. 읽기/쓰기 권한 및 데이터 검증 규칙을 정의합니다.
version: 1.0.0
type: configuration
category: firebase-config
tags: [configuration, firebase, database, security, rules]
---

# database.rules.json

## 개요
Firebase Realtime Database의 보안 규칙 파일입니다. 이 파일은:
- 사용자, 채팅, 시스템 데이터의 접근 권한 정의
- 데이터 검증 및 무결성 보장
- 관리자 권한 및 테스트 데이터 처리

## 소스 코드

```json
{
  "rules": {
    "users": {
      // 자신만 읽기 가능. 모든 사용자가 읽기 불가능
      ".read": true,
      // 하위 모든 경로 쓰기 삭제: 단, shallower 경로에서 개별 규칙 설정 가능
      ".write": false,
      "$uid": {
        // 2025-12-12 까지는 무조건 쓰기 통과 (테스트 데이터 생성용)
        // 그 이후는 본인만 쓰기 가능
        ".write": "now < 1765555200000 || auth.uid == $uid"
      },
      ".indexOn": [
        "createdAt",
        "displayNameLowerCase"
      ]
    },
    "system": {
      "settings": {
        "admins": {
          // 로그인한 모든 사용자가 읽기 가능 (메뉴에서 사용)
          ".read": "auth != null",
          // 관리자만 쓰기 가능 (배열에 있는 사용자만)
          ".write": "root.child('system/settings/admins').val().contains(auth.uid)"
        }
      }
    },
    "stats": {
      ".read": true,
      ".write": false,
      "counters": {
        ".read": true,
        ".write": false
      }
    },
    "chat-rooms": {
      // 채팅방 메타데이터
      // createdAt과 owner 필드는 Cloud Functions에서만 설정됨
      ".read": true,
      "$roomId": {
        ".write": "auth != null",
        "owner": {
          // 채팅방이 존재하지 않으면 본인 UID로 설정 가능, 존재하면 수정 불가
          ".write": "!root.child('chat-rooms').child($roomId).exists() && newData.val() === auth.uid",
          ".validate": "newData.isString()"
        },
        "createdAt": {
          // Cloud Functions에서만 설정 가능 (클라이언트는 쓰기 불가)
          ".write": false,
          ".validate": "newData.isNumber()"
        },
        "name": {
          // 채팅방이 존재하지 않으면 누구나 쓰기 가능, 존재하면 owner만 수정 가능
          ".write": "!root.child('chat-rooms').child($roomId).exists() || root.child('chat-rooms').child($roomId).child('owner').val() === auth.uid",
          ".validate": "newData.isString() && newData.val().length > 0 && newData.val().length <= 50"
        },
        "description": {
          // 채팅방이 존재하지 않으면 누구나 쓰기 가능, 존재하면 owner만 수정 가능
          ".write": "!root.child('chat-rooms').child($roomId).exists() || root.child('chat-rooms').child($roomId).child('owner').val() === auth.uid",
          ".validate": "newData.isString() && newData.val().length <= 200"
        },
        "type": {
          ".write": "!data.exists()",
          ".validate": "newData.val() === 'group' || newData.val() === 'open' || newData.val() === 'single'"
        },
        "open": {
          ".write": "!data.exists()",
          ".validate": "newData.isBoolean()"
        },
        "groupListOrder": {
          ".write": "!data.exists()",
          ".validate": "newData.isNumber()"
        },
        "openListOrder": {
          ".write": "!data.exists()",
          ".validate": "newData.isNumber()"
        },
        "memberCount": {
          // Cloud Functions에서만 설정 가능 (자동 생성/증감)
          ".write": false,
          ".validate": "newData.isNumber() && newData.val() >= 0"
        },
        "members": {
          // 사용자는 자기 자신의 uid만 추가/수정 가능
          // true = 메시지 알림 받기, false = 알림 받지 않기
          "$uid": {
            ".write": "auth != null && $uid === auth.uid",
            ".validate": "newData.isBoolean() || newData.val() === null"
          }
        },
        "$other": {
          ".validate": false
        }
      },
      ".indexOn": [
        "openListOrder"
      ]
    },
    "chat-joins": {
      "$uid": {
        ".indexOn": [
          "allChatListOrder",
          "singleChatListOrder",
          "groupChatListOrder",
          "openChatListOrder",
          "openAndGroupChatListOrder"
        ],
        ".read": "$uid === auth.uid",
        ".write": "$uid === auth.uid"
      }
    },
    "chat-messages": {
      ".read": true,
      ".write": true,
      ".indexOn": [
        "roomOrder"
      ]
    },
    "test": {
      "data": {
        // QA 전용 테스트 데이터 노드 - 누구나 읽고 쓰기 가능
        ".read": true,
        ".write": true,
        ".indexOn": [
          "order",
          "createdAt",
          "qnaCreatedAt",
          "reminderCreatedAt",
          "newsCreatedAt"
        ]
      }
    }
  }
}
```

## 주요 설정

### 사용자 데이터 (users)
- **읽기**: 모든 사용자 가능
- **쓰기**:
  - 2025-12-12까지: 무제한 (테스트용)
  - 이후: 본인만 가능
- **인덱스**: `createdAt`, `displayNameLowerCase`

### 시스템 설정 (system)
- **admins**:
  - 읽기: 로그인한 모든 사용자
  - 쓰기: 관리자만

### 통계 (stats)
- **읽기**: 모든 사용자
- **쓰기**: 차단 (Cloud Functions만 수정 가능)

### 채팅방 (chat-rooms)
- **읽기**: 모든 사용자
- **owner**: 채팅방 생성 시 본인 UID로만 설정 가능
- **createdAt**: Cloud Functions만 설정 가능
- **name**: 1~50자, owner만 수정 가능
- **description**: 0~200자, owner만 수정 가능
- **type**: `group`, `open`, `single` 중 하나
- **members**: 본인 UID만 추가/수정 가능
- **인덱스**: `openListOrder`

### 채팅 참여 (chat-joins)
- **읽기/쓰기**: 본인 데이터만 접근
- **인덱스**: 5개의 listOrder 필드

### 채팅 메시지 (chat-messages)
- **읽기/쓰기**: 모든 로그인 사용자
- **인덱스**: `roomOrder`

### 테스트 데이터 (test)
- **읽기/쓰기**: 모두 허용 (QA 전용)
- **인덱스**: 5개의 order 필드

## 보안 고려사항
- 사용자 쓰기 권한은 2025-12-12 이후 본인만 가능
- Cloud Functions 전용 필드: `createdAt`, `memberCount`
- 채팅방 생성 후 불변 필드: `type`, `open`, `groupListOrder`, `openListOrder`

## 관련 파일
- [firebase.json](./firebase.json.md) - Firebase 프로젝트 설정
- [sonub-firebase-database-structure.md](../../specs/sonub-firebase-database-structure.md) - Database 구조 문서
