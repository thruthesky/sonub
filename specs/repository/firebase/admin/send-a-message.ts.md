---
name: send-a-message.ts
description: send-a-message 파일
version: 1.0.0
type: typescript
category: other
original_path: firebase/admin/send-a-message.ts
---

# send-a-message.ts

## 개요

**파일 경로**: `firebase/admin/send-a-message.ts`
**파일 타입**: typescript
**카테고리**: other

send-a-message 파일

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
    console.log("Successfully sent:", response);
  })
  .catch((error:unknown) => {
    console.error("Error sending message:", error);
  });
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
