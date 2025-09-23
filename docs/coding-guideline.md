Sonub Coding Guideline
=======================

## General Coding Standards
- Use 4 spaces for indentation, no tabs.
- Use UTF-8 encoding without BOM.
- Write comments and documentation for complex logic.

## Framework and Library Storage Guidelines

### Complete Framework Packages
- When downloading complete framework files (e.g., Bootstrap with all its components):
  - Store in: `etc/frameworks/<framework-name>/<framework-name>-x.x.x/`
  - Example: `etc/frameworks/bootstrap/bootstrap-5.3.0/`
  - This structure maintains version control and allows multiple versions to coexist.

### Single JavaScript Libraries
- For single JavaScript file libraries (e.g., jQuery):
  - Store directly in: `/js/`
  - Example: `/js/jquery-3.7.1.min.js`
  - Use versioned filenames to track library versions.

## Development System Startup

### Quick Start (Required Commands)
Sonub 개발을 시작하려면 **반드시 두 가지 명령을 실행**해야 합니다:

```bash
# 1. Docker 컨테이너 시작 (Nginx + PHP-FPM)
cd ~/apps/sonub/dev/docker
docker compose up -d

# 2. Hot Reload 서버 시작 (파일 변경 자동 감지)
cd ~/apps/sonub
npm run dev
```

**중요**: 두 명령 모두 실행해야 완전한 개발 환경이 구성됩니다.
- `docker compose up`: 웹 서버와 PHP 실행 환경 제공
- `npm run dev`: 파일 변경 시 브라우저 자동 새로고침

### Using Docker Compose
Sonub 개발 환경은 Docker Compose를 통해 Nginx와 PHP-FPM 서비스를 실행합니다.

#### Prerequisites
- Docker 및 Docker Compose 설치 필요
- 프로젝트 위치: `~/apps/sonub/`
- Local development domain setup required (see below)

#### Quick Start Commands
```bash
# Docker 디렉토리로 이동
cd ~/apps/sonub/dev/docker

# 시스템 시작 (Nginx + PHP-FPM)
docker compose up -d

# 컨테이너 상태 확인
docker compose ps

# 로그 보기
docker compose logs -f

# 시스템 중지
docker compose down

# 시스템 재시작
docker compose restart

# orphan 컨테이너 제거와 함께 시작
docker compose up -d --remove-orphans
```

#### Local Development Domain Setup
To use local development domain instead of localhost/127.0.0.1, add the following entry to your hosts file:

**For macOS/Linux:**
```bash
sudo nano /etc/hosts
# Add this line:
127.0.0.1 local.sonub.com
```

**For Windows:**
```bash
# Edit with administrator privileges:
# C:\Windows\System32\drivers\etc\hosts
# Add this line:
127.0.0.1 local.sonub.com
```

After adding this entry, you can access your development site at:
- http://local.sonub.com:8080
- https://local.sonub.com:8443

#### Default Configuration
- **Web Root**: `~/apps/sonub`
- **HTTP Port**: 127.0.0.1:8080
- **HTTPS Port**: 127.0.0.1:8443
- **PHP Version**: 8.3-fpm (custom build)
- **Nginx Version**: alpine (latest)
- **Network**: sonub-network (bridge)
- **Local Domain**: local.sonub.com (requires hosts file configuration)

#### Directory Structure
```
dev/docker/
├── compose.yaml         # Docker Compose 설정 파일
├── php.dockerfile       # PHP-FPM 커스텀 이미지
├── etc/
│   ├── nginx/
│   │   └── nginx.conf  # Nginx 메인 설정
│   └── php.ini         # PHP 설정
└── var/
    ├── log/nginx/      # Nginx 로그
    └── logs/php/       # PHP 로그
```

#### Key Features
- Docker Compose를 통한 간편한 서비스 관리
- Nginx와 PHP-FPM 자동 연동
- 볼륨 마운트를 통한 실시간 코드 반영
- 로그 파일 외부 저장

#### Troubleshooting
- 포트 충돌 시 compose.yaml에서 포트 번호 변경
- `docker compose logs` 명령으로 오류 확인
- ERR_UNSAFE_PORT 에러 발생 시 다른 포트 사용 (예: 8080)

### Hot Reload Development Server
Sonub provides a hot reload development server that automatically refreshes your browser when files change.

#### Features
- Automatic browser refresh on file changes
- CSS hot-swapping without page reload
- HTTPS support with local SSL certificates
- Watches PHP, CSS, JS, and template files

#### Setup and Usage
```bash
# Install dependencies (first time only)
npm install

# Start the hot reload server
npm run dev
```

The hot reload server will:
- Run on https://localhost:3034 (or http if SSL certificates not available)
- Monitor all project files for changes
- Automatically reload the browser when PHP files change
- Hot-swap CSS files without full page reload
- Display file change notifications in the console

#### Configuration
The hot reload server can be configured with environment variables:
- `DOMAIN`: Development domain (default: localhost)
- `PORT`: Socket server port (default: 3034)
- `USE_HTTPS`: Enable/disable HTTPS (default: true)

#### SSL Certificates
The server uses mkcert-generated SSL certificates located in:
- `./etc/server-settings/nginx/ssl/sonub/localhost-key.pem`
- `./etc/server-settings/nginx/ssl/sonub/localhost-cert.pem`

If certificates are not found, the server will automatically fall back to HTTP mode.
