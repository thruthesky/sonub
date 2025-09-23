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

### Using Docker Compose
Sonub 개발 환경은 Docker Compose를 통해 Nginx와 PHP-FPM 서비스를 실행합니다.

#### Prerequisites
- Docker 및 Docker Compose 설치 필요
- 프로젝트 위치: `~/apps/sonub/`

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

#### Default Configuration
- **Web Root**: `~/apps/sonub`
- **HTTP Port**: 127.0.0.1:8080
- **HTTPS Port**: 127.0.0.1:8443
- **PHP Version**: 8.3-fpm (custom build)
- **Nginx Version**: alpine (latest)
- **Network**: sonub-network (bridge)

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
