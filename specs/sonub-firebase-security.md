
## Firebase Realtime Database 보안 규칙

사용자의 프로필 데이터는 다음과 같이 보호됩니다:

```json
{
  "rules": {
    "users": {
      "$uid": {
        // 모든 사용자가 읽기 가능
        ".read": true,
        // 본인만 쓰기 가능
        ".write": "auth.uid == $uid",
        // 필수 필드 검증
        ".validate": "newData.hasChildren(['displayName', 'email'])"
      }
    }
  }
}
```


## Firebase Storage 보안 규칙

프로필 사진 저장소의 보안 규칙:

```
rules_version = '2';
service firebase.storage {
  match /b/{bucket}/o {
    // /users/{userId}/profile 경로의 파일
    match /users/{userId}/profile/{fileName=**} {
      allow read: if true;  // 모든 사용자가 읽기 가능
      allow write: if request.auth.uid == userId;  // 본인만 쓰기 가능
      allow delete: if request.auth.uid == userId;  // 본인만 삭제 가능
    }
  }
}
```

