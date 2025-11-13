---
name: cors.json
description: Firebase Storage CORS(Cross-Origin Resource Sharing) 설정 파일. 교차 출처 리소스 공유 정책을 정의합니다.
version: 1.0.0
type: configuration
category: firebase-config
tags: [configuration, firebase, cors, storage, security]
---

# cors.json

## 개요
Firebase Storage의 CORS 설정 파일입니다. 이 파일은:
- 모든 출처에서의 GET 요청 허용
- 브라우저 캐싱 정책 설정
- Storage 버킷의 교차 출처 접근 제어

## 소스 코드

```json
[
    {
        "origin": [
            "*"
        ],
        "method": [
            "GET"
        ],
        "maxAgeSeconds": 86400
    }
]
```

## 주요 설정

### CORS 규칙
- **origin**: `["*"]` - 모든 출처(도메인)에서 접근 허용
  - 프로덕션에서는 특정 도메인으로 제한 권장
  - 예: `["https://example.com", "https://app.example.com"]`

- **method**: `["GET"]` - GET 메서드만 허용
  - 파일 읽기(다운로드)만 가능
  - PUT, POST, DELETE 등은 차단

- **maxAgeSeconds**: 86400 - 캐시 유효 기간 (24시간)
  - 브라우저가 CORS preflight 응답을 캐싱하는 시간
  - 86400초 = 24시간 = 1일

## 보안 고려사항
- 현재 설정은 **개발/테스트 환경용**입니다
- 프로덕션에서는 origin을 특정 도메인으로 제한해야 합니다
- 필요시 추가 HTTP 메서드 허용 가능 (PUT, POST 등)

## CORS 적용 방법
```bash
# Firebase Storage 버킷에 CORS 설정 적용
gsutil cors set cors.json gs://[YOUR-BUCKET-NAME]
```

## 관련 파일
- [firebase.json](./firebase.json.md) - Firebase 프로젝트 설정
- [database.rules.json](./database.rules.json.md) - Realtime Database 보안 규칙
