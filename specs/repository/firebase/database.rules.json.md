---
title: "firebase/database.rules.json"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "firebase/database.rules.json"
spec_type: "repository-source"
---

## 개요

이 파일은 database.rules.json의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-13: 스펙 문서 생성/업데이트
