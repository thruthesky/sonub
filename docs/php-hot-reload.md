# PHP 핫 리로드 시스템

## 목차

- [개요](#개요)
- [아키텍처](#아키텍처)
- [설치](#설치)
- [구성](#구성)
- [사용법](#사용법)
- [파일 구조](#파일-구조)
- [작동 방식](#작동-방식)
- [SSL 구성](#ssl-구성)
- [문제 해결](#문제-해결)
- [개발](#개발)

## 개요

PHP 핫 리로드 시스템은 PHP, CSS, JavaScript 또는 기타 프로젝트 파일이 수정될 때 브라우저를 자동으로 새로 고침하는 개발 도구입니다. 지능적인 리로드 기능을 제공합니다:

- **CSS 핫 스왑**: 전체 페이지 리로드 없이 CSS 파일 업데이트
- **전체 리로드**: PHP, JavaScript 및 기타 파일 변경 시 전체 페이지 새로 고침
- **SSL 지원**: 보안 로컬 개발을 위한 HTTPS 작동
- **다중 도메인 지원**: localhost 및 커스텀 도메인(예: local.sonub.com) 모두 처리

## 아키텍처

시스템은 두 가지 주요 구성 요소로 구성됩니다:

1. **서버 (`etc/php-hot-reload-server.js`)**: 파일 변경을 감시하는 Node.js WebSocket 서버
2. **클라이언트 (`etc/php-hot-reload-client.php`)**: 서버에 연결하고 리로드를 처리하는 브라우저 측 JavaScript

### 의존성

- **chokidar**: 변경 사항 감지를 위한 파일 시스템 감시자
- **express**: 웹 서버 프레임워크
- **socket.io**: 실시간 양방향 통신
- **fs, path, http, https**: Node.js 내장 모듈

## 설치

### 전제 조건

Node.js가 설치되어 있고 필요한 의존성이 있는지 확인하세요:

```bash
npm install chokidar express socket.io
```

### SSL 인증서 (선택 사항)

HTTPS 지원을 위해 SSL 인증서를 다음 위치에 배치하세요:
```
../dev/ssl/
├── sonub-key.pem
└── sonub-cert.pem
```

## 구성

### 환경 변수

서버는 환경 변수를 사용하여 구성할 수 있습니다:

| 변수 | 기본값 | 설명 |
|----------|---------|-------------|
| `DOMAIN` | `local.sonub.com` | 핫 리로드 서버의 도메인 |
| `PORT` | `3034` | WebSocket 서버의 포트 |
| `USE_HTTPS` | `true` | HTTPS 지원 활성화/비활성화 |
| `KEY` | `/../dev/ssl/sonub-key.pem` | SSL 개인 키 경로 |
| `CERT` | `/../dev/ssl/sonub-cert.pem` | SSL 인증서 경로 |

### 감시 디렉토리

시스템은 기본적으로 다음 디렉토리를 모니터링합니다:

- `./api` - API 엔드포인트
- `./css` - 스타일시트
- `./etc` - 구성 파일
- `./js` - JavaScript 파일
- `./lib` - 라이브러리 파일
- `./page` - 페이지 템플릿
- `./post` - 게시물 관련 파일
- `./res` - 리소스
- `./user` - 사용자 관련 파일
- `./widgets` - 위젯 구성 요소
- `./api.php` - 메인 API 파일
- `./boot.php` - 부트스트랩 파일
- `./index.php` - 진입점

### 무시되는 파일

다음 패턴은 무시됩니다:

- `.git/` 디렉토리
- `node_modules/` 디렉토리
- `vendor/` 디렉토리
- `storage/` 디렉토리
- `cache/` 디렉토리
- `.map` 파일

## 사용법

### 서버 시작

```bash
node etc/php-hot-reload-server.js
```

서버가 시작되면 다음이 표시됩니다:
- 프로토콜 (HTTP/HTTPS)
- 도메인 및 포트
- 감시 디렉토리
- SSL 인증서 상태

### 클라이언트 포함

PHP 페이지에 클라이언트 스크립트를 추가하세요:

```php
<?php include 'etc/php-hot-reload-client.php'; ?>
```

이것은 `</body>` 태그를 닫기 전에 포함되어야 합니다.

### 클라이언트 요구 사항

Socket.IO 클라이언트 라이브러리가 사용 가능한지 확인하세요:

```html
<script src="/js/socket.io/socket.io.min.js"></script>
```

## 파일 구조

```
project/
├── etc/
│   ├── php-hot-reload-server.js    # WebSocket 서버
│   └── php-hot-reload-client.php   # 브라우저 클라이언트 스크립트
├── js/
│   └── socket.io/
│       └── socket.io.min.js        # Socket.IO 클라이언트 라이브러리
└── ../dev/ssl/                     # SSL 인증서 (선택 사항)
    ├── sonub-key.pem
    └── sonub-cert.pem
```

## 작동 방식

### 서버 측

1. **파일 감시**: Chokidar가 지정된 디렉토리의 변경 사항을 모니터링
2. **이벤트 처리**: CSS와 기타 파일 변경 사항을 구분
3. **WebSocket 통신**: 연결된 클라이언트에 적절한 신호 전송
4. **SSL 처리**: 인증서가 있는 경우 자동으로 HTTPS 구성

### 클라이언트 측

1. **연결**: 서버에 WebSocket 연결 설정
2. **CSS 핫 스왑**: 캐시 무효화 매개변수로 스타일시트 링크 업데이트
3. **전체 리로드**: CSS가 아닌 변경 사항에 대해 `location.reload()` 트리거
4. **동적 호스트 감지**: 올바른 서버 URL을 자동으로 결정

### 이벤트 타입

- **`css`**: CSS 파일이 변경될 때 발생, 변경된 파일 경로 포함
- **`reload`**: CSS가 아닌 파일이 변경될 때 발생, 전체 페이지 리로드 트리거

## SSL 구성

### 자동 SSL 감지

서버는 자동으로:
1. SSL 인증서 파일 확인
2. 인증서가 없으면 HTTP로 폴백
3. SSL 상태 및 예상 인증서 경로 로그 기록

### 인증서 경로

기본 인증서 위치:
- 개인 키: `../dev/ssl/sonub-key.pem`
- 인증서: `../dev/ssl/sonub-cert.pem`

### 인증서 생성

로컬 개발을 위해 다음과 같은 도구를 사용할 수 있습니다:
- mkcert
- OpenSSL
- 로컬 CA 인증 기관

## 문제 해결

### 일반적인 문제

1. **연결 실패**
   - 서버가 올바른 포트에서 실행 중인지 확인
   - HTTPS를 사용하는 경우 SSL 인증서 확인
   - 방화벽이 포트를 차단하지 않는지 확인

2. **CSS 핫 스왑이 작동하지 않음**
   - CSS 파일 경로가 감시 디렉토리와 일치하는지 확인
   - 브라우저 콘솔에서 클라이언트 측 오류 확인
   - 스타일시트 링크에 적절한 파일 이름이 포함되어 있는지 확인

3. **SSL 인증서 오류**
   - 인증서 파일이 존재하고 읽을 수 있는지 확인
   - 인증서 유효성 및 도메인 일치 확인
   - 개발을 위해 HTTP로 폴백하는 것을 고려

### 디버그 정보

서버 로그:
- 파일 경로가 포함된 파일 변경 이벤트
- WebSocket 연결 및 연결 해제
- SSL 인증서 로딩 상태
- 발생한 이벤트 (css/reload)

클라이언트 로그:
- 연결 상태
- 핫 리로드 이벤트
- CSS 업데이트 시도

## 개발

### 감시 경로 사용자 정의

`php-hot-reload-server.js`의 `WATCH_PATHS` 배열을 수정하세요:

```javascript
const WATCH_PATHS = [
    './custom-directory',
    './another-path',
    // 여기에 경로를 추가하세요
];
```

### 디바운스 타이밍 조정

디바운스 지연 시간을 변경하세요 (기본값 200ms):

```javascript
debounced(() => {
    io.emit('reload');
}, 500); // 밀리초 단위의 사용자 정의 지연 시간
```

### 파일 타입 지원 추가

CSS 핫 스왑을 다른 파일 타입으로 확장하세요:

```javascript
const HOT_SWAP_EXT = new Set(['.css', '.scss', '.less']);
function isHotSwapFile(file) {
    return HOT_SWAP_EXT.has(path.extname(file).toLowerCase());
}
```

### 클라이언트 사용자 정의

클라이언트 스크립트를 다음과 같이 수정할 수 있습니다:
- 추가 이벤트 타입 처리
- 리로드 동작 사용자 정의
- 로딩 표시기 추가
- 재시도 로직 구현

---

이 핫 리로드 시스템은 HTTP 및 HTTPS 구성 모두에서 작업할 수 있는 유연성을 유지하면서 코드 변경에 대한 즉각적인 피드백을 제공하여 개발 워크플로우를 향상시킵니다.
