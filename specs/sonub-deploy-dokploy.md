---
name: sonub
version: 1.0.0
description: GitHub 푸시 기반 자동 배포 워크플로우 명세서
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: GPL-3.0
step: 100
priority: "**"
dependencies:
  - sonub-setup-svelte.md
tags:
  - deployment
  - github
  - dokploy
  - ci-cd
  - production
---

# Dokploy 자동 배포 워크플로우

## 개요

Sonub 프로젝트는 GitHub repository에 코드를 푸시하면 Dokploy에서 자동으로 빌드하고 프로덕션 사이트를 업데이트하는 CI/CD 워크플로우를 사용합니다.

## Dokploy 설정 상세 가이드 (SvelteKit + Svelte 5)

### 프로젝트 개요

Sonub 프로젝트는 SvelteKit + Svelte 5 기반 애플리케이션으로, Dokploy를 통해 Docker 컨테이너로 빌드하고 배포합니다.

### 1. General 설정 (프로젝트 기본 정보)

Dokploy 대시보드에서 프로젝트 생성 시 다음 설정을 사용합니다:

| 설정 항목 | 값 | 설명 |
|----------|-----|------|
| **Repository** | `sonub` | GitHub repository 이름 |
| **Branch** | `main` | 배포할 브랜치 |
| **Build Path** | `/` | 프로젝트 루트 디렉토리 |
| **Trigger Type** | `On Push` | Git push 이벤트 발생 시 자동 배포 |

**중요 사항:**
- **Trigger Type: On Push**를 선택하면 `main` 브랜치에 코드를 푸시할 때마다 자동으로 빌드 및 배포가 시작됩니다.
- GitHub Webhook은 Dokploy가 자동으로 설정합니다.
- 별도의 GitHub Actions 워크플로우 파일(.github/workflows/\*.yml)은 필요하지 않습니다.

### 2. Build Type 설정 (Dockerfile 기반 빌드)

| 설정 항목 | 값 | 설명 |
|----------|-----|------|
| **Build Type** | `Dockerfile` | Docker 이미지 기반 빌드 방식 선택 |
| **Docker File** | `Dockerfile` | Dockerfile 파일명 (프로젝트 루트에 위치) |
| **Docker Context Path** | `.` | Docker 빌드 컨텍스트 경로 (현재 디렉토리) |

**Dockerfile 위치:**
- `Dockerfile`은 프로젝트 루트 디렉토리에 위치합니다 (`package.json`과 동일한 디렉토리).
- 경로: `/Dockerfile`

**Dockerfile 내용 설명:**

```dockerfile
# Node 20 기반으로 빌드 (SvelteKit adapter-node용)
FROM node:20

# 앱 디렉토리 생성 및 설정
WORKDIR /app

# package.json과 package-lock.json 복사
COPY package*.json ./

# 의존성 설치 (npm ci가 더 빠르고 정확)
RUN npm ci

# 나머지 소스 코드 복사
COPY . .

# SvelteKit 빌드 실행
RUN npm run build

# 컨테이너가 수신할 포트 지정 (Dokploy에서 자동 감지됨)
EXPOSE 3000

# Node 서버 실행 (adapter-node에서 build 디렉토리 실행)
CMD ["node", "build"]
```

**주요 포인트:**
1. **Node 20 사용**: SvelteKit과 Svelte 5는 최신 Node.js LTS 버전을 권장합니다.
2. **npm ci 사용**: `npm install` 대신 `npm ci`를 사용하여 정확한 의존성 버전 설치 및 빌드 속도 향상.
3. **SvelteKit adapter-node**: `npm run build` 명령은 `build/` 디렉토리에 프로덕션용 Node.js 서버를 생성합니다.
4. **Port 3000**: SvelteKit의 기본 프로덕션 포트입니다. Dokploy의 Traefik이 이 포트로 트래픽을 라우팅합니다.

### 3. Domains 설정 (도메인 및 HTTPS)

| 설정 항목 | 값 | 설명 |
|----------|-----|------|
| **Domain** | `sonub.com` | 프로덕션 도메인 |
| **Additional Domain** | `www.sonub.com` | www 서브도메인 (선택사항) |
| **Path** | `/` | 루트 경로 |
| **Port** | `3000` | 컨테이너 내부 포트 |
| **HTTPS** | ✓ 활성화 | 자동 SSL/TLS 인증서 |
| **Cert** | `letsencrypt` | Let's Encrypt 자동 인증서 발급 |

**도메인 설정 방법:**

1. **Primary Domain 추가:**
   - Domain: `sonub.com`
   - Port: `3000`
   - HTTPS: 활성화
   - Certificate: Let's Encrypt 자동 선택

2. **www 서브도메인 추가 (선택사항):**
   - Domain: `www.sonub.com`
   - Port: `3000`
   - HTTPS: 활성화
   - Certificate: Let's Encrypt 자동 선택

**HTTPS/SSL 자동 설정:**
- Dokploy는 Let's Encrypt를 통해 **무료 SSL/TLS 인증서**를 자동으로 발급합니다.
- 인증서는 **90일마다 자동 갱신**됩니다.
- HTTP (포트 80) → HTTPS (포트 443) 자동 리다이렉트가 설정됩니다.

### 4. Traefik 리버스 프록시 (Nginx 불필요)

**중요: Dokploy는 내장 Traefik을 사용하므로 수동으로 Nginx를 설정할 필요가 없습니다.**

#### Traefik vs Nginx

| 항목 | Traefik (Dokploy 내장) | Nginx (수동 설정) |
|------|------------------------|-------------------|
| **설치** | Dokploy에 자동 포함됨 | 별도 설치 필요 |
| **설정** | Dokploy UI에서 자동 설정 | 수동 설정 파일 작성 필요 |
| **SSL 인증서** | Let's Encrypt 자동 발급 | certbot 등 별도 도구 필요 |
| **도메인 라우팅** | UI에서 도메인 추가만 하면 자동 | nginx.conf 수동 편집 |
| **포트 매핑** | 443 → 3000 자동 설정 | location 블록 수동 작성 |

**Traefik이 자동으로 처리하는 작업:**

```
사용자 요청
   ↓
https://sonub.com (포트 443)
   ↓
[Traefik 리버스 프록시]
   ↓
Docker 컨테이너 (포트 3000)
   ↓
SvelteKit 애플리케이션
```

**Traefik 설정 내용 (자동):**
- **포트 443 → 3000 프록시**: HTTPS 요청을 컨테이너의 포트 3000으로 전달
- **HTTP → HTTPS 리다이렉트**: 포트 80 요청을 포트 443으로 자동 리다이렉트
- **도메인 라우팅**: `sonub.com`과 `www.sonub.com` 요청을 올바른 컨테이너로 라우팅
- **헬스 체크**: 컨테이너 상태 모니터링 및 다운타임 시 자동 재시작
- **로드 밸런싱**: 여러 컨테이너 인스턴스 실행 시 트래픽 분산

**Nginx를 사용하지 않는 이유:**
1. Dokploy는 Traefik을 내장하고 있어 추가 리버스 프록시가 불필요합니다.
2. Traefik은 Docker 네이티브 통합으로 컨테이너 자동 검색 및 설정이 가능합니다.
3. UI 기반 설정으로 복잡한 설정 파일 작성이 필요 없습니다.
4. Let's Encrypt 통합으로 SSL 인증서 관리가 자동화됩니다.

### 5. 자동 배포 프로세스

GitHub에 코드를 푸시하면 다음과 같은 자동 배포가 진행됩니다:

```
┌─────────────────────────┐
│  로컬 개발 환경          │
│  git push origin main   │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│  GitHub Repository      │
│  (Push Event 발생)      │
└────────────┬────────────┘
             │
             │ Webhook Trigger
             ▼
┌─────────────────────────┐
│  Dokploy Server         │
│  (Webhook 수신)         │
└────────────┬────────────┘
             │
             │ 1. Git Clone/Pull
             ▼
┌─────────────────────────┐
│  Docker Build           │
│  - npm ci               │
│  - npm run build        │
│  - Docker Image 생성    │
└────────────┬────────────┘
             │
             │ 2. Build Success
             ▼
┌─────────────────────────┐
│  Container Deploy       │
│  - 이전 컨테이너 중지    │
│  - 새 컨테이너 시작      │
│  - 포트 3000 바인딩     │
└────────────┬────────────┘
             │
             │ 3. Traefik 라우팅
             ▼
┌─────────────────────────┐
│  Production Live        │
│  https://sonub.com      │
│  https://www.sonub.com  │
└─────────────────────────┘
```

**배포 단계 상세:**

1. **Git Push**: 로컬에서 `git push origin main` 실행
2. **Webhook 트리거**: GitHub가 Dokploy에 push 이벤트 전송
3. **코드 가져오기**: Dokploy가 최신 코드를 clone 또는 pull
4. **Docker 빌드**:
   - `npm ci`: 의존성 설치
   - `npm run build`: SvelteKit 프로덕션 빌드 (build/ 디렉토리 생성)
   - Docker 이미지 생성
5. **컨테이너 배포**:
   - 이전 컨테이너 graceful shutdown
   - 새 컨테이너 시작 (포트 3000 바인딩)
   - Traefik이 새 컨테이너로 트래픽 라우팅
6. **프로덕션 업데이트**: `https://sonub.com` 즉시 업데이트

**배포 시간:**
- 전체 배포 프로세스는 일반적으로 **2-5분** 소요됩니다.
- 빌드 시간은 의존성 수와 코드 크기에 따라 달라집니다.

## 배포 흐름도

```
┌─────────────────────────┐
│  GitHub Repository      │
│  (Push to main branch)  │
└────────────┬────────────┘
             │
             │ Git Push Event
             │
             ▼
┌─────────────────────────┐
│  GitHub Webhook         │
│  (Event Notification)   │
└────────────┬────────────┘
             │
             │ HTTP POST Request
             │
             ▼
┌─────────────────────────┐
│  Dokploy Server         │
│  (Webhook Receiver)     │
└────────────┬────────────┘
             │
             │ Receive Event
             │
             ▼
┌─────────────────────────┐
│  Dokploy Build Process  │
│  - Clone Repository     │
│  - Install Dependencies │
│  - Build SvelteKit App  │
│  - Run Tests (Optional) │
└────────────┬────────────┘
             │
             │ Build Success
             │
             ▼
┌─────────────────────────┐
│  Production Deployment  │
│  - Deploy to Server     │
│  - Update Live Site     │
│  - Health Check         │
└────────────┬────────────┘
             │
             │ Deployment Complete
             │
             ▓
┌─────────────────────────┐
│  Live Production Site   │
│  Updated & Running      │
└─────────────────────────┘
```

## 배포 프로세스 상세 설명

### 1단계: GitHub에 코드 푸시

개발자가 로컬에서 작업한 코드를 GitHub repository에 푸시합니다.

```bash
# 로컬 변경사항을 staging area에 추가
git add .

# 커밋 생성
git commit -m "feat: add new feature"

# GitHub repository에 푸시 (main 브랜치)
git push origin main
```

### 2단계: GitHub Webhook 이벤트 발생

GitHub는 push 이벤트를 감지하고 등록된 webhook을 통해 Dokploy 서버에 알립니다.

**Webhook 설정 정보**:
- **Event**: Push Event
- **Destination**: `https://<dokploy-server>/api/webhooks/github`
- **Content Type**: `application/json`
- **Payload**: Repository 정보, 커밋 정보, 브랜치 정보

### 3단계: Dokploy Webhook 수신

Dokploy는 GitHub webhook 이벤트를 수신하고 자동 배포 프로세스를 시작합니다.

```javascript
// Dokploy Webhook Endpoint
POST /api/webhooks/github
{
  "ref": "refs/heads/main",
  "repository": {
    "name": "sonub",
    "full_name": "thruthesky/sonub",
    "clone_url": "https://github.com/thruthesky/sonub.git"
  },
  "commits": [
    {
      "id": "abc123def456",
      "message": "feat: add new feature",
      "author": { "name": "JaeHo Song", "email": "thruthesky@gmail.com" }
    }
  ]
}
```

### 4단계: Dokploy 빌드 프로세스

Dokploy는 다음 단계를 순서대로 실행합니다:

#### 4.1 Repository 클론 또는 업데이트
```bash
# 처음 배포인 경우: Repository 클론
git clone https://github.com/thruthesky/sonub.git

# 이후 배포: 최신 코드 가져오기
cd sonub
git fetch origin
git checkout main
git pull origin main
```

#### 4.2 의존성 설치
```bash
# Node.js 패키지 설치
npm install

# 또는 Yarn 사용
yarn install
```

#### 4.3 SvelteKit 빌드
```bash
# 프로덕션 빌드 실행
npm run build

# 또는
yarn build
```

**빌드 검증**:
- TypeScript 타입 체크
- 번들 최적화
- 정적 파일 생성
- 불필요한 코드 제거

#### 4.4 테스트 실행 (Optional)
```bash
# 유닛 테스트 실행
npm run test

# E2E 테스트 실행 (Playwright)
npm run test:e2e
```

### 5단계: 프로덕션 배포

빌드가 성공하면 Dokploy는 빌드된 애플리케이션을 프로덕션 서버에 배포합니다.

```bash
# 빌드 결과물 배포
# - build/ 디렉토리 배포
# - package.json 복사
# - 환경 변수 설정
# - 서비스 재시작
```

**배포 단계**:
1. 이전 배포 버전 백업
2. 새로운 빌드 결과물 배포
3. 환경 변수 적용
4. Node.js 애플리케이션 재시작
5. 헬스 체크 실행

### 6단계: 헬스 체크 및 모니터링

배포 완료 후 Dokploy는 프로덕션 사이트의 상태를 확인합니다.

```bash
# 헬스 체크 엔드포인트 요청
GET https://sonub.com/health

# 응답 확인
Status: 200 OK
{
  "status": "healthy",
  "timestamp": "2024-11-09T12:00:00Z"
}
```

## GitHub Webhook 설정

### Webhook URL 설정

GitHub repository → Settings → Webhooks에서 다음 설정을 추가합니다:

- **Payload URL**: `https://<dokploy-domain>/api/webhooks/github`
- **Content type**: `application/json`
- **Events**: Push events
- **Active**: ✓ (체크)

### Webhook Secret (선택사항)

보안을 위해 webhook secret을 설정할 수 있습니다:

```bash
# GitHub에서 secret 생성
Secret: <random-secret-string>

# Dokploy에서 secret 검증 (구현 필요)
const signature = req.headers['x-hub-signature-256'];
const secret = process.env.GITHUB_WEBHOOK_SECRET;
// HMAC 검증...
```

## 배포 환경 변수

`.env` 파일에서 다음 환경 변수를 설정해야 합니다:

```bash
# Firebase 설정
VITE_FIREBASE_API_KEY=<your-api-key>
VITE_FIREBASE_AUTH_DOMAIN=<your-auth-domain>
VITE_FIREBASE_PROJECT_ID=<your-project-id>
VITE_FIREBASE_STORAGE_BUCKET=<your-storage-bucket>
VITE_FIREBASE_MESSAGING_SENDER_ID=<your-sender-id>
VITE_FIREBASE_APP_ID=<your-app-id>

# 애플리케이션 설정
NODE_ENV=production
APP_NAME=sonub
APP_VERSION=1.0.0
```

## 배포 실패 처리

배포 과정에서 오류가 발생하면 다음과 같이 처리됩니다:

### 빌드 실패
```
❌ Build Failed
- Error: TypeScript compilation error
- File: src/routes/+page.svelte
- Line: 42
- Message: Type '...' is not assignable to type '...'

→ 이전 배포 버전 유지
→ 배포자에게 알림
```

### 배포 실패
```
❌ Deployment Failed
- Error: Insufficient disk space
- Available: 100MB
- Required: 500MB

→ 이전 배포 버전 유지
→ 관리자에게 알림
```

### 롤백 프로세스
```bash
# 실패한 배포 자동 롤백
git revert <failed-commit-hash>
git push origin main

# 또는 수동 롤백
dokploy rollback --version=<previous-version>
```

## 배포 모니터링 및 로깅

### Dokploy 대시보드에서 모니터링

- 배포 상태 실시간 확인
- 빌드 로그 확인
- 배포 히스토리 확인
- 성능 메트릭 모니터링

### 로그 확인

```bash
# Dokploy 배포 로그
dokploy logs --app=sonub --lines=100

# 프로덕션 애플리케이션 로그
docker logs sonub-app

# 에러 로그 확인
docker logs sonub-app --since 1h | grep ERROR
```

## 배포 권한 관리

### GitHub Repository 권한

- **Admin**: 전체 접근 가능
- **Write**: 코드 푸시 가능 → 자동 배포 트리거
- **Read**: 코드 조회만 가능 → 배포 불가

### Dokploy 권한

- **Owner**: 모든 설정 변경 가능
- **Maintainer**: 배포 및 기본 설정 변경 가능
- **Developer**: 배포 트리거만 가능

## 배포 전 체크리스트

배포하기 전에 다음을 확인하세요:

- [ ] 모든 코드 리뷰 완료
- [ ] 로컬에서 `npm run build` 성공 확인
- [ ] 테스트 통과 확인
- [ ] 환경 변수 올바르게 설정
- [ ] 데이터베이스 마이그레이션 필요시 실행
- [ ] 백업 준비 완료

## 배포 후 확인 사항

배포 완료 후 다음을 확인하세요:

- [ ] 프로덕션 사이트 접속 확인
- [ ] 주요 기능 작동 확인
- [ ] 브라우저 콘솔 에러 확인
- [ ] 성능 메트릭 확인
- [ ] 로그에 에러 없음 확인
- [ ] 사용자 피드백 모니터링

## 관련 문서

- [SvelteKit 기본 설정](./sonub-setup-svelte.md)
- [Firebase 설정](./sonub-setup-firebase.md)
- [TailwindCSS 설정](./sonub-setup-tailwind.md)
