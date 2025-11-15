---
title: send-a-message.ts
type: typescript
path: firebase/admin/send-a-message.ts
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `firebase/admin/send-a-message.ts`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```typescript
const admin = require("firebase-admin");

const serviceAccount = require("/Users/thruthesky/Documents/Keys/Firebase-Service-Accounts/sonub-firebase/sonub-firebase-firebase-adminsdk-fbsvc-88519b1894.json");

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
});

const message = {
  token: "e2y7jQe_Ecb-wpvWk_aC08:APA91bGLsbV3ltYMJKMcgueGtBaG9oT5IxmRA101gsp4tu4-hIuawfAk5qDHlSMUid5WystrZO1cbrnJaifJjrwOAkxYA8maCeQnbx7Bmf2MLkpprK-pPFA",
  notification: {
    title: "웹에서 FCM 테스트",
    body: "푸시 잘 도착하나요?",
  },
};

admin
  .messaging()
  .send(message)
  .then((response: unknown) => {
    // console.log("Successfully sent:", response);
  })
  .catch((error:unknown) => {
    console.error("Error sending message:", error);
  });

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
