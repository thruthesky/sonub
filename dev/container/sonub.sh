#!/usr/bin/env bash
set -euo pipefail

# =========================
#  Apple Container macOS 26  |  SONUB Nginx + PHP-FPM launcher
# =========================
#
# ========================================
# macOS Container 기술 심층 이해
# ========================================
#
# 1. Apple Container란?
# ----------------------
# Apple Container는 macOS Sequoia(15.0)부터 도입된 네이티브 컨테이너 런타임입니다.
# Docker Desktop이나 다른 가상화 솔루션 없이 macOS에서 직접 Linux 컨테이너를 실행할 수 있습니다.
#
# 2. 핵심 기술 스택
# -----------------
# - Hypervisor.framework: Apple의 Type-2 하이퍼바이저로 경량 가상화 제공
# - Virtualization.framework: 고수준 가상화 API로 Linux VM 관리
# - Rosetta 2: x86_64 컨테이너를 Apple Silicon에서 실행 가능
# - APFS: Copy-on-Write 파일시스템으로 효율적인 이미지 레이어 관리
#
# 3. Docker와의 주요 차이점
# -------------------------
# | 항목              | Docker Desktop          | macOS Container        |
# |-------------------|------------------------|------------------------|
# | 아키텍처          | Linux VM 위 컨테이너    | macOS 네이티브         |
# | 메모리 사용       | VM 오버헤드 (2-4GB)     | 최소 오버헤드          |
# | 시작 시간         | 10-30초                | 1-3초                  |
# | 파일 시스템 성능   | VirtioFS/gRPC FUSE     | 네이티브 APFS          |
# | CPU 아키텍처      | 에뮬레이션 필요         | Rosetta 2 통합         |
#
# 4. 네트워킹 아키텍처
# --------------------
# macOS Container는 세 가지 네트워킹 모드를 지원합니다:
#
# a) Bridge Network (기본값)
#    - 격리된 네트워크 네임스페이스
#    - 컨테이너 간 통신은 브리지를 통해
#    - NAT를 통한 외부 접근
#
# b) Host Network
#    - 호스트의 네트워크 스택 직접 사용
#    - 포트 충돌 주의 필요
#    - 최고 성능
#
# c) None Network
#    - 네트워크 인터페이스 없음
#    - 완전 격리된 환경
#
# 5. 볼륨 마운트 메커니즘
# -----------------------
# - Bind Mount: 호스트 디렉터리를 컨테이너에 직접 마운트
# - Volume: Container 관리 볼륨, APFS 스냅샷 지원
# - tmpfs: 메모리 기반 임시 파일시스템
#
# 권한 관리:
# - :ro (읽기 전용): 컨테이너가 파일 수정 불가
# - :rw (읽기/쓰기): 기본값, 양방향 수정 가능
# - :delegated: 컨테이너 쓰기 성능 최적화 (캐싱)
# - :cached: 호스트 읽기 성능 최적화
#
# 6. 이 스크립트의 동작 흐름
# --------------------------
#
# [시작 시퀀스]
#   1. container system start
#      └─> Hypervisor 초기화 및 런타임 준비
#
#   2. 네트워크 생성 (webnet)
#      └─> 격리된 브리지 네트워크 구성
#
#   3. PHP-FPM 컨테이너 시작
#      ├─> FastCGI 프로세스 매니저 실행
#      ├─> 포트 9000에서 대기
#      └─> 볼륨: 소스코드, php.ini
#
#   4. Nginx 컨테이너 시작
#      ├─> 웹 서버 프로세스 실행
#      ├─> 포트 80 -> 호스트 8080 매핑
#      ├─> PHP 요청을 php-fpm:9000으로 프록시
#      └─> 볼륨: 소스코드, nginx.conf
#
# [요청 처리 플로우]
#   브라우저 -> localhost:8080 -> Nginx 컨테이너
#                                    ├─> 정적 파일: 직접 서빙
#                                    └─> PHP 파일: FastCGI로 전달
#                                                    └─> PHP-FPM 컨테이너
#
# 7. 보안 고려사항
# ----------------
# - 컨테이너는 기본적으로 권한 없는 사용자로 실행
# - SELinux/AppArmor 레이블링 지원
# - Seccomp 프로파일로 시스템 콜 제한
# - 네임스페이스 격리 (PID, Network, Mount, IPC, User, UTS)
#
# ========================================
# 사용법
# ========================================
#   ./sonub.sh start        # 컨테이너 시스템 기동 + 네트워크 + PHP-FPM + Nginx 실행
#   ./sonub.sh stop         # 컨테이너 중지(nginx, php-fpm)
#   ./sonub.sh restart      # 재시작
#   ./sonub.sh status       # 상태 보기
#   ./sonub.sh logs         # nginx, php-fpm 로그
#   ./sonub.sh reload       # Nginx 설정 리로드
#   ./sonub.sh open         # 브라우저 열기 (localhost 기준)
#
# 포트/리소스/경로는 아래 변수를 바꿔서 사용하세요.
#
# ========================================
# 참고 문서
# ========================================
# MacOS Container 요약 문서: ./docs/dev/container/macos-container.md
# MacOS Container 공식 홈페이지: https://github.com/apple/container
# MacOS Container Tutorial: https://github.com/apple/container/blob/main/docs/tutorial.md
# MacOS Container How-to: https://github.com/apple/container/blob/main/docs/how-to.md
# MacOS Container Technical Overview: https://github.com/apple/container/blob/main/docs/technical-overview.md
# MacOS Container Command Reference: https://github.com/apple/container/blob/main/docs/command-reference.md

# ---------- 설정(필요 시 수정) ----------
APP_ROOT="${HOME}/apps/sonub"

# 중요: PUBLIC_DIR은 웹 루트 디렉터리입니다.
# macOS Container는 디렉터리만 마운트할 수 있습니다.
# 프로젝트의 index.php가 루트에 있으므로 프로젝트 루트를 사용합니다.
PUBLIC_DIR="${APP_ROOT}"                  # public 루트 (index.php가 있는 위치)

NGINX_CONF_DIR="${APP_ROOT}/dev/container/nginx"
PHP_CONF_DIR="${APP_ROOT}/dev/container/php"

# 컨테이너/네트워크 이름
NET_NAME="webnet"
NGINX_CNAME="nginx"
PHPFPM_CNAME="php-fpm"

# 포트 매핑
HOST_HTTP="127.0.0.1:12345"               # 호스트:컨테이너(80)
NGINX_PORT_IN_CONTAINER="80"

# 리소스(원하면 조정)
NGINX_CPUS="2"
NGINX_MEM="1g"

# 이미지 태그
IMG_NGINX="docker.io/nginx:alpine"
IMG_PHPFPM="docker.io/php:fpm-alpine"

# (선택) 내장 DNS 로컬 도메인 사용 여부 (test 도메인)
USE_INTERNAL_DNS="false"                  # "true" 로 변경 시 *.test 사용
DNS_DOMAIN="test"

# 타임존
TZ="Asia/Seoul"

# ---------------------------------------

# ========================================
# 헬퍼 함수들
# ========================================

# say(): 녹색 텍스트로 성공/정보 메시지 출력
# ANSI 이스케이프 코드 사용: \033[1;32m (굵은 녹색)
say() { printf "\033[1;32m%s\033[0m\n" "$*"; }

# err(): 빨간색 텍스트로 에러 메시지를 stderr로 출력
# ANSI 이스케이프 코드 사용: \033[1;31m (굵은 빨간색)
err() { printf "\033[1;31m%s\033[0m\n" "$*" >&2; }

# require_bin(): 필수 바이너리 존재 여부 확인
# container 명령어가 설치되어 있는지 검증
require_bin() {
  command -v "$1" >/dev/null 2>&1 || { err "필수 명령어가 없습니다: $1"; exit 1; }
}

# validate_mount_paths(): 마운트 경로 사전 검증
# VZErrorDomain Code=2 에러 방지를 위한 철저한 경로 검증
validate_mount_paths() {
  say "[마운트 경로 검증]"

  # PUBLIC_DIR 검증 (디렉터리여야 함)
  if [[ -e "${PUBLIC_DIR}" ]]; then
    if [[ ! -d "${PUBLIC_DIR}" ]]; then
      err ""
      err "=== VZErrorDomain 에러 감지 ==="
      err "원인: ${PUBLIC_DIR} 경로가 디렉터리가 아닙니다."
      err "타입: $(file -b "${PUBLIC_DIR}" 2>/dev/null)"
      err ""
      err "해결 방법:"
      err "1. 파일 삭제: rm '${PUBLIC_DIR}'"
      err "2. 또는 PUBLIC_DIR 변경: 스크립트 상단의 PUBLIC_DIR 변수 수정"
      err "3. 또는 백업 후 삭제: mv '${PUBLIC_DIR}' '${PUBLIC_DIR}.backup'"
      err "================================"
      exit 1
    fi
  fi

  # 설정 파일 디렉터리 검증
  for conf_dir in "${NGINX_CONF_DIR}" "${PHP_CONF_DIR}"; do
    if [[ -e "${conf_dir}" && ! -d "${conf_dir}" ]]; then
      err ""
      err "=== 경로 타입 오류 ==="
      err "${conf_dir}가 파일입니다. 디렉터리여야 합니다."
      err "해결: rm '${conf_dir}' && mkdir -p '${conf_dir}'"
      err "======================"
      exit 1
    fi
  done

  say "  ✓ 모든 마운트 경로 검증 완료"
}

# ========================================
# 시스템 초기화 함수들
# ========================================

# ensure_container_system(): Container 시스템 데몬 시작
# - Hypervisor.framework 초기화
# - containerd 런타임 시작
# - 네트워킹 서브시스템 활성화
ensure_container_system() {
  say "[system] container system start"
  container system start >/dev/null || true
}

# ensure_dirs_and_seed(): 필요한 디렉터리 생성 및 초기 파일 설정
# - 프로젝트 디렉터리 구조 생성
# - 기본 PHP 파일 생성 (index.php, phpinfo.php)
# - Nginx 및 PHP 설정 파일 생성
ensure_dirs_and_seed() {
  # 디렉터리 경로 검증 및 생성
  # macOS Container는 디렉터리가 아닌 파일을 마운트하려 할 때 VZErrorDomain 에러 발생
  for dir in "${PUBLIC_DIR}" "${NGINX_CONF_DIR}" "${PHP_CONF_DIR}"; do
    if [[ -e "${dir}" && ! -d "${dir}" ]]; then
      err "오류: ${dir} 경로에 파일이 존재합니다. 디렉터리여야 합니다."
      err "해결방법: rm '${dir}' 명령으로 파일을 삭제하거나 다른 경로를 사용하세요."
      exit 1
    fi

    # 심볼릭 링크인 경우 실제 경로 확인
    if [[ -L "${dir}" ]]; then
      local real_path
      real_path=$(readlink -f "${dir}" 2>/dev/null || readlink "${dir}")
      if [[ ! -d "${real_path}" ]]; then
        err "오류: ${dir} 심볼릭 링크가 유효하지 않은 디렉터리를 가리킵니다: ${real_path}"
        exit 1
      fi
      say "[검증] ${dir} -> ${real_path} (심볼릭 링크)"
    fi

    # 디렉터리 생성
    if [[ ! -d "${dir}" ]]; then
      mkdir -p "${dir}" || { err "디렉터리 생성 실패: ${dir}"; exit 1; }
      say "[생성] 디렉터리: ${dir}"
    else
      say "[확인] 디렉터리 존재: ${dir}"
    fi
  done

  # index.php 기본 파일
  if [[ ! -f "${PUBLIC_DIR}/index.php" ]]; then
    cat > "${PUBLIC_DIR}/index.php" <<"PHP"
<?php
echo "Hello SONUB!";
PHP
    say "[seed] ${PUBLIC_DIR}/index.php 생성"
  fi

  # phpinfo.php 기본 파일
  if [[ ! -f "${PUBLIC_DIR}/phpinfo.php" ]]; then
    cat > "${PUBLIC_DIR}/phpinfo.php" <<"PHP"
<?php
phpinfo();
PHP
    say "[seed] ${PUBLIC_DIR}/phpinfo.php 생성"
  fi

  # php.ini 기본 파일
  # PHP-FPM은 conf.d/*.ini 파일을 자동으로 로드하므로
  # 디렉터리 전체를 마운트할 때 custom.ini로 저장
  if [[ ! -f "${PHP_CONF_DIR}/custom.ini" ]] && [[ ! -f "${PHP_CONF_DIR}/php.ini" ]]; then
    cat > "${PHP_CONF_DIR}/custom.ini" <<INI
; Custom PHP configuration for Sonub
date.timezone = ${TZ}
opcache.enable=1
opcache.enable_cli=1
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000

; Upload limits
upload_max_filesize = 64M
post_max_size = 64M

; Memory and execution limits
memory_limit = 256M
max_execution_time = 300
INI
    say "[seed] ${PHP_CONF_DIR}/custom.ini 생성"
  elif [[ -f "${PHP_CONF_DIR}/php.ini" ]]; then
    # 기존 php.ini 파일이 있다면 custom.ini로 이동
    mv "${PHP_CONF_DIR}/php.ini" "${PHP_CONF_DIR}/custom.ini"
    say "[이동] ${PHP_CONF_DIR}/php.ini -> custom.ini"
  fi

  # nginx conf 기본 파일
  if [[ ! -f "${NGINX_CONF_DIR}/default.conf" ]]; then
    cat > "${NGINX_CONF_DIR}/default.conf" <<"NGX"
server {
  listen 80;
  server_name _;

  root /var/www/html;
  index index.php index.html;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location ~ \.php$ {
    include       fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    # macOS Container에서 PHP-FPM 컨테이너는 일반적으로 192.168.65.x 대역을 사용
    # PHP-FPM IP는 동적으로 할당되므로 container inspect php-fpm으로 확인 필요
    # 초기값: php-fpm 컨테이너 이름 사용 (DNS가 작동하지 않으면 IP로 수정 필요)
    fastcgi_pass  php-fpm:9000;
  }

  add_header X-Frame-Options SAMEORIGIN;
  add_header X-Content-Type-Options nosniff;
  add_header Referrer-Policy strict-origin-when-cross-origin;
}
NGX
    say "[seed] ${NGINX_CONF_DIR}/default.conf 생성"
  fi
}

# ========================================
# 네트워크 및 이미지 관리 함수들
# ========================================

# ensure_network(): 컨테이너용 사용자 정의 네트워크 생성
# - Bridge 타입 네트워크 생성 (기본값)
# - 컨테이너 간 DNS 이름으로 통신 가능
# - 네트워크 격리로 보안 향상
ensure_network() {
  if ! container network list --format json | grep -q "\"${NET_NAME}\""; then
    say "[net] 사용자 네트워크 생성: ${NET_NAME}"
    container network create "${NET_NAME}"
  else
    say "[net] 사용자 네트워크 확인됨: ${NET_NAME}"
  fi
}

# maybe_setup_dns(): 로컬 개발용 DNS 설정 (선택사항)
# - *.test 도메인을 로컬 컨테이너에 매핑
# - /etc/resolver/test 파일 자동 생성
# - 브라우저에서 nginx.test, php-fpm.test 등으로 접근 가능
# 주의: sudo 권한 필요
maybe_setup_dns() {
  if [[ "${USE_INTERNAL_DNS}" == "true" ]]; then
    # 관리자 권한 필요
    if ! container system dns list | grep -q "${DNS_DOMAIN}"; then
      say "[dns] 로컬 DNS 도메인 생성: ${DNS_DOMAIN}"
      sudo container system dns create "${DNS_DOMAIN}"
    fi
    say "[dns] 기본 도메인 설정: ${DNS_DOMAIN}"
    container system property set dns.domain "${DNS_DOMAIN}"
  fi
}

# pull_images(): Docker Hub에서 필요한 이미지 다운로드
# - nginx:alpine: 경량 Alpine Linux 기반 Nginx
# - php:fpm-alpine: PHP-FPM이 포함된 Alpine Linux
# 캐싱: 이미 다운로드된 이미지는 재사용
pull_images() {
  say "[image] pull ${IMG_PHPFPM}"
  container image pull "${IMG_PHPFPM}" || true
  say "[image] pull ${IMG_NGINX}"
  container image pull "${IMG_NGINX}" || true
}

# ========================================
# 컨테이너 생명주기 관리 함수들
# ========================================

# stop_if_running(): 실행 중인 컨테이너 정리
# - 기존 컨테이너 정상 종료 (SIGTERM)
# - 컨테이너 삭제로 클린 상태 유지
# - jq 사용하여 JSON 파싱 (컨테이너 ID 추출)
stop_if_running() {
  for n in "${NGINX_CNAME}" "${PHPFPM_CNAME}"; do
    if container ls -a --format json | jq -r '.[].configuration.id' | grep -qx "${n}" 2>/dev/null; then
      say "[stop] ${n}"
      container stop "${n}" >/dev/null || true
      container delete "${n}" >/dev/null || true
    fi
  done
}

# start_phpfpm(): PHP-FPM 컨테이너 시작
# 옵션 설명:
# - -d: 백그라운드(detached) 모드로 실행
# - --name: 컨테이너 이름 지정 (DNS 이름으로도 사용)
# - --network: 사용자 정의 네트워크에 연결
# - -e TZ: 타임존 설정 (Asia/Seoul)
# - -v: 볼륨 마운트
#   - 소스코드: 읽기/쓰기 모드 (개발 시 실시간 반영)
#   - PHP 설정 디렉터리: macOS Container는 파일 마운트를 지원하지 않음
# 주의: --rm 옵션 제거 (관리자 권한 요청 방지)
start_phpfpm() {
  say "[run] ${PHPFPM_CNAME}"

  # 볼륨 마운트 경로 검증
  if [[ ! -d "${PUBLIC_DIR}" ]]; then
    err "오류: PUBLIC_DIR이 디렉터리가 아닙니다: ${PUBLIC_DIR}"
    err "현재 타입: $(file -b "${PUBLIC_DIR}" 2>/dev/null || echo '존재하지 않음')"
    exit 1
  fi

  if [[ ! -d "${PHP_CONF_DIR}" ]]; then
    err "오류: PHP_CONF_DIR이 디렉터리가 아닙니다: ${PHP_CONF_DIR}"
    exit 1
  fi

  # PHP 설정 파일 확인 (custom.ini 또는 php.ini)
  if ! ls "${PHP_CONF_DIR}"/*.ini >/dev/null 2>&1; then
    err "오류: ${PHP_CONF_DIR}에 .ini 파일이 없습니다"
    exit 1
  fi

  # 디버깅 정보 출력
  say "[볼륨] 소스코드: ${PUBLIC_DIR} -> /var/www/html"
  say "[볼륨] PHP 설정 디렉터리: ${PHP_CONF_DIR} -> /usr/local/etc/php/conf.d"
  say "[정보] macOS Container는 개별 파일 마운트를 지원하지 않아 디렉터리 전체를 마운트합니다"

  # macOS Container는 파일 마운트를 지원하지 않으므로 디렉터리 전체를 마운트
  # PHP-FPM은 /usr/local/etc/php/conf.d/*.ini 파일을 자동으로 로드함
  container run -d --name "${PHPFPM_CNAME}" \
    --network "${NET_NAME}" \
    -e TZ="${TZ}" \
    -v "${PUBLIC_DIR}:/var/www/html" \
    -v "${PHP_CONF_DIR}:/usr/local/etc/php/conf.d:ro" \
    "${IMG_PHPFPM}" >/dev/null || {
      err "PHP-FPM 컨테이너 시작 실패"
      err "디버그 명령: container run -it -v ${PUBLIC_DIR}:/var/www/html ${IMG_PHPFPM} sh"
      exit 1
    }
}

# start_nginx(): Nginx 웹 서버 컨테이너 시작
# 옵션 설명:
# - -p: 포트 매핑 (호스트:컨테이너)
#   예: 127.0.0.1:12345:80 (로컬호스트만 접근 가능)
# - --cpus: CPU 코어 제한 (기본값: 2)
# - --memory: 메모리 제한 (기본값: 1GB)
# - 볼륨 마운트:
#   - 소스코드: PHP와 동일한 경로 공유
#   - Nginx 설정 디렉터리: macOS Container는 파일 마운트를 지원하지 않음
start_nginx() {
  say "[run] ${NGINX_CNAME}  (host ${HOST_HTTP} -> container :${NGINX_PORT_IN_CONTAINER})"

  # PHP-FPM 컨테이너의 IP 주소 확인 (디버깅용)
  local php_ip
  # jq를 사용하여 JSON에서 IP 추출
  php_ip=$(container inspect php-fpm 2>/dev/null | jq -r '.[0].networks[0].address' | cut -d'/' -f1)

  if [[ -z "${php_ip}" ]]; then
    err "오류: PHP-FPM 컨테이너의 IP 주소를 찾을 수 없습니다"
    err "PHP-FPM이 실행 중인지 확인하세요: container ls"
    exit 1
  fi

  say "[네트워크] PHP-FPM IP: ${php_ip}"

  # PHP-FPM IP가 192.168.65.x 대역인지 확인
  if [[ ! "${php_ip}" =~ ^192\.168\.65\. ]]; then
    say "[경고] PHP-FPM IP가 예상 대역이 아닙니다 (예상: 192.168.65.x, 실제: ${php_ip})"
  fi

  # Nginx 설정 파일에서 PHP-FPM IP 동적 업데이트
  if [[ -f "${NGINX_CONF_DIR}/default.conf" ]]; then
    say "[설정] Nginx 설정의 PHP-FPM 주소를 ${php_ip}:9000으로 업데이트"
    # 임시 파일에 수정된 설정 저장
    sed "s/fastcgi_pass.*9000;/fastcgi_pass  ${php_ip}:9000;/" \
      "${NGINX_CONF_DIR}/default.conf" > "${NGINX_CONF_DIR}/default.conf.tmp"
    mv "${NGINX_CONF_DIR}/default.conf.tmp" "${NGINX_CONF_DIR}/default.conf"
  fi

  # 볼륨 마운트 경로 검증
  if [[ ! -d "${PUBLIC_DIR}" ]]; then
    err "오류: PUBLIC_DIR이 디렉터리가 아닙니다: ${PUBLIC_DIR}"
    err "현재 타입: $(file -b "${PUBLIC_DIR}" 2>/dev/null || echo '존재하지 않음')"
    exit 1
  fi

  if [[ ! -f "${NGINX_CONF_DIR}/default.conf" ]]; then
    err "오류: nginx 설정 파일이 존재하지 않습니다: ${NGINX_CONF_DIR}/default.conf"
    exit 1
  fi

  # 디버깅 정보 출력
  say "[볼륨] 소스코드: ${PUBLIC_DIR} -> /var/www/html"
  say "[볼륨] Nginx 설정 디렉터리: ${NGINX_CONF_DIR} -> /etc/nginx/conf.d"
  say "[정보] macOS Container는 개별 파일 마운트를 지원하지 않아 디렉터리 전체를 마운트합니다"

  # macOS Container는 파일 마운트를 지원하지 않으므로 디렉터리 전체를 마운트
  # Nginx는 /etc/nginx/conf.d/*.conf 파일을 자동으로 로드함
  # 주의: --rm 옵션 제거 (관리자 권한 요청 방지)
  say "[실행] Nginx 컨테이너 시작 중..."

  if container run -d --name "${NGINX_CNAME}" \
    --network "${NET_NAME}" \
    -p "${HOST_HTTP}:${NGINX_PORT_IN_CONTAINER}" \
    -v "${PUBLIC_DIR}:/var/www/html" \
    -v "${NGINX_CONF_DIR}:/etc/nginx/conf.d:ro" \
    --cpus "${NGINX_CPUS}" --memory "${NGINX_MEM}" \
    "${IMG_NGINX}" >/dev/null 2>&1; then
    say "  ✅ Nginx 컨테이너 시작 성공"
  else
    err "  ❌ Nginx 컨테이너 시작 실패"
    err ""
    err "문제 진단을 위한 상세 실행:"
    err "container run -it --name ${NGINX_CNAME} \\"
    err "  --network ${NET_NAME} \\"
    err "  -p ${HOST_HTTP}:${NGINX_PORT_IN_CONTAINER} \\"
    err "  -v ${PUBLIC_DIR}:/var/www/html \\"
    err "  -v ${NGINX_CONF_DIR}:/etc/nginx/conf.d:ro \\"
    err "  ${IMG_NGINX}"
    err ""
    err "또는 대화형 셸로 진입:"
    err "container run -it --network ${NET_NAME} ${IMG_NGINX} sh"
    exit 1
  fi
}

# ========================================
# 유틸리티 함수들
# ========================================

# open_browser(): 기본 브라우저로 URL 열기
# - macOS의 open 명령어 사용
# - 파라미터 확장 ${HOST_HTTP##*:}로 포트번호 추출
open_browser() {
  local url="http://localhost:${HOST_HTTP##*:}/"
  say "[open] ${url}"
  if command -v open >/dev/null 2>&1; then
    open "${url}" || true
  fi
}

# ========================================
# 명령어 처리 함수들
# ========================================

# cmd_start(): 전체 스택 시작 시퀀스
# 실행 순서:
# 1. 시스템 검증 (container 명령어 확인)
# 2. Container 시스템 데몬 시작
# 3. 디렉터리 및 초기 파일 생성
# 4. 네트워크 인프라 구성
# 5. DNS 설정 (선택사항)
# 6. Docker 이미지 다운로드
# 7. 기존 컨테이너 정리
# 8. PHP-FPM 시작 (백엔드)
# 9. Nginx 시작 (프론트엔드)
# 10. 접속 URL 안내
cmd_start() {
  say "========================================="
  say "macOS Container 시스템 시작"
  say "========================================="

  # 환경 정보 출력
  say "[환경 정보]"
  say "  - PUBLIC_DIR: ${PUBLIC_DIR}"
  say "  - NGINX_CONF_DIR: ${NGINX_CONF_DIR}"
  say "  - PHP_CONF_DIR: ${PHP_CONF_DIR}"
  say "  - 네트워크: ${NET_NAME}"
  say "  - 포트: ${HOST_HTTP} -> :${NGINX_PORT_IN_CONTAINER}"

  # 경로 타입 확인
  say "[경로 검증]"
  for path in "${PUBLIC_DIR}" "${NGINX_CONF_DIR}" "${PHP_CONF_DIR}"; do
    if [[ -e "${path}" ]]; then
      if [[ -d "${path}" ]]; then
        say "  ✓ ${path} (디렉터리)"
      elif [[ -f "${path}" ]]; then
        err "  ✗ ${path} (파일 - 오류!)"
        err "    VZErrorDomain 에러를 방지하기 위해 디렉터리여야 합니다."
        exit 1
      elif [[ -L "${path}" ]]; then
        say "  ~ ${path} (심볼릭 링크 -> $(readlink "${path}"))"
      fi
    else
      say "  ? ${path} (생성 예정)"
    fi
  done
  say "-----------------------------------------"

  require_bin container
  require_bin jq  # JSON 파싱용
  validate_mount_paths  # VZErrorDomain 에러 방지
  ensure_container_system
  ensure_dirs_and_seed
  ensure_network
  maybe_setup_dns
  pull_images
  stop_if_running
  start_phpfpm
  start_nginx
  say "========================================="
  say "✅ 시스템 시작 완료"
  say "========================================="
  say "접속 확인:"
  say "  - http://localhost:${HOST_HTTP##*:}/index.php"
  say "  - http://localhost:${HOST_HTTP##*:}/phpinfo.php"
  if [[ "${USE_INTERNAL_DNS}" == "true" ]]; then
    say "  - http://${NGINX_CNAME}.${DNS_DOMAIN}"
  fi
  say ""
  say "문제 해결:"
  say "  - 로그 확인: ./sonub.sh logs"
  say "  - 상태 확인: ./sonub.sh status"
  say "  - 재시작: ./sonub.sh restart"
}

# cmd_stop(): 컨테이너 중지
# - 실행 중인 모든 컨테이너 정상 종료
# - 네트워크는 유지 (재시작 시 재사용)
cmd_stop() {
  stop_if_running
  say "[ok] nginx, php-fpm 중지 완료"
}

# cmd_restart(): 재시작
# - 완전한 중지 후 시작
# - 설정 변경사항 적용
cmd_restart() {
  cmd_stop
  cmd_start
}

# cmd_status(): 시스템 상태 확인
# - 실행 중인 컨테이너 목록
# - 네트워크 구성 정보
# - 리소스 사용량 표시
cmd_status() {
  require_bin container
  say "[containers]"
  container ls -a || true
  say "[networks]"
  container network list || true
}

# cmd_logs(): 컨테이너 로그 확인
# - Nginx 액세스/에러 로그
# - PHP-FPM 프로세스 로그
# - 실시간 스트리밍은 container logs -f 사용
cmd_logs() {
  require_bin container
  say "[logs] ${NGINX_CNAME}"
  container logs "${NGINX_CNAME}" 2>/dev/null || true
  say "----------------------------------"
  say "[logs] ${PHPFPM_CNAME}"
  container logs "${PHPFPM_CNAME}" 2>/dev/null || true
}

# cmd_reload(): Nginx 설정 리로드
# - 무중단 설정 적용
# - nginx -s reload 시그널 전송
# - 새로운 워커 프로세스 생성 후 기존 프로세스 종료
cmd_reload() {
  require_bin container
  say "[reload] nginx"
  container exec "${NGINX_CNAME}" nginx -s reload
}

# cmd_open(): 브라우저 열기
# - 기본 웹 브라우저로 애플리케이션 접속
cmd_open() {
  open_browser
}

# cmd_debug(): VZErrorDomain 에러 진단 모드
cmd_debug() {
  say "========================================="
  say "macOS Container 디버그 모드"
  say "========================================="

  # 시스템 정보
  say "[시스템 정보]"
  say "  - macOS 버전: $(sw_vers -productVersion)"
  say "  - 아키텍처: $(uname -m)"
  say "  - Container 버전: $(container --version 2>/dev/null || echo 'unknown')"

  # 경로 진단
  say ""
  say "[경로 진단]"
  say "  APP_ROOT: ${APP_ROOT}"
  for item in "${APP_ROOT}"/*; do
    if [[ -d "${item}" ]]; then
      say "    📁 $(basename "${item}") (디렉터리)"
    elif [[ -f "${item}" ]]; then
      say "    📄 $(basename "${item}") (파일)"
    elif [[ -L "${item}" ]]; then
      say "    🔗 $(basename "${item}") -> $(readlink "${item}")"
    fi
  done

  # 테스트 마운트
  say ""
  say "[테스트 마운트]"
  say "macOS Container 볼륨 마운트 테스트를 실행합니다..."

  # 임시 테스트 디렉터리 생성
  local test_dir="${APP_ROOT}/test_mount_$$"
  mkdir -p "${test_dir}"
  echo "test content" > "${test_dir}/test.txt"

  # 1. 디렉터리 마운트 테스트 (성공해야 함)
  say ""
  say "[1] 디렉터리 마운트 테스트:"
  if container run --rm -v "${test_dir}:/mnt/test" ${IMG_NGINX} ls /mnt/test >/dev/null 2>&1; then
    say "  ✅ 디렉터리 마운트 성공"
  else
    err "  ❌ 디렉터리 마운트 실패"
  fi

  # 2. 개별 파일 마운트 테스트 (실패가 예상됨)
  say ""
  say "[2] 파일 마운트 테스트 (macOS Container 제한사항):"
  if container run --rm -v "${test_dir}/test.txt:/mnt/test.txt" ${IMG_NGINX} cat /mnt/test.txt >/dev/null 2>&1; then
    say "  ✅ 파일 마운트 성공 (예상치 못한 결과)"
  else
    err "  ⚠️  파일 마운트 실패 (예상된 결과 - macOS Container는 파일 마운트 미지원)"
    err "     Docker와 달리 디렉터리만 마운트 가능합니다"
  fi

  # 3. 실제 경로 테스트
  say ""
  say "[3] 실제 경로 마운트 테스트:"
  if container run --rm -v "${PUBLIC_DIR}:/mnt/test" ${IMG_NGINX} ls /mnt/test >/dev/null 2>&1; then
    say "  ✅ PUBLIC_DIR 마운트 성공: ${PUBLIC_DIR}"
  else
    err "  ❌ PUBLIC_DIR 마운트 실패: ${PUBLIC_DIR}"
    err "     VZErrorDomain 에러가 발생했을 수 있습니다"
  fi

  # 4. PHP 설정 디렉터리 테스트
  if [[ -d "${PHP_CONF_DIR}" ]]; then
    say ""
    say "[4] PHP 설정 디렉터리 마운트 테스트:"
    if container run --rm -v "${PHP_CONF_DIR}:/mnt/test" ${IMG_NGINX} ls /mnt/test >/dev/null 2>&1; then
      say "  ✅ PHP_CONF_DIR 마운트 성공: ${PHP_CONF_DIR}"
      local ini_files=$(ls -1 "${PHP_CONF_DIR}"/*.ini 2>/dev/null | wc -l)
      say "     찾은 .ini 파일 개수: ${ini_files}"
    else
      err "  ❌ PHP_CONF_DIR 마운트 실패"
    fi
  fi

  # 정리
  rm -rf "${test_dir}"

  say ""
  say "[권장사항]"
  say "  - PUBLIC_DIR을 별도 디렉터리로 분리하는 것을 권장합니다."
  say "  - 예: mkdir -p ${APP_ROOT}/public && mv ${APP_ROOT}/index.php ${APP_ROOT}/public/"
}

main() {
  case "${1:-}" in
    start)   cmd_start ;;
    stop)    cmd_stop ;;
    restart) cmd_restart ;;
    status)  cmd_status ;;
    logs)    cmd_logs ;;
    reload)  cmd_reload ;;
    open)    cmd_open ;;
    debug)   cmd_debug ;;  # 디버그 모드 추가
    *)
      cat <<USAGE
사용법:
  ${0##*/} start    # 컨테이너 시스템/네트워크 및 nginx+php-fpm 실행
  ${0##*/} stop
  ${0##*/} restart
  ${0##*/} status
  ${0##*/} logs
  ${0##*/} reload
  ${0##*/} open
  ${0##*/} debug    # VZErrorDomain 에러 진단

문제 해결:
  VZErrorDomain 에러가 발생하면 './sonub.sh debug' 실행
USAGE
      ;;
  esac
}
main "$@"