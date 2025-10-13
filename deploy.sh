#!/bin/bash

# 스크립트 실행 중 어떤 명령이든 실패하면 즉시 종료.
# 이유: 실패한 상태로 다음 명령을 실행하지 않기 위해.
set -e
set -o pipefail



# Parse command line arguments
DRY_RUN=false
FAST_MODE=false

for arg in "$@"; do
  case $arg in
    --dry-run)
      DRY_RUN=true
      shift
      ;;
    fast)
      FAST_MODE=true
      shift
      ;;
    *)
      ;;
  esac
done

# patch the APP_VERSION constant in etc/app.version.php with `y.m.d h:m`
YMD=$(date "+%Y-%m-%d-%H-%M-%S")

# 버전 업데이트, APP_VERSION 상수 패치
echo "🔧 버전 업데이트: APP_VERSION 상수 패치..."
sed -i '' "s/const APP_VERSION = '.*';/const APP_VERSION = '${YMD}';/" ./etc/app.version.php

# ./etc/app.version.php 를 읽어, APP_VERSION 이 올바로 출력되는지 확인
if grep -q "const APP_VERSION = '${YMD}';" ./etc/app.version.php; then
  echo "✅ APP_VERSION 이 올바로 패치되었습니다."
else
  echo "❌ APP_VERSION 패치에 실패하였습니다."
  exit 1
fi

# ./etc/app.version.php 를 PHP 로 실행해서, APP_VERSION 이 올바로 출력되는지 확인
if php -r "include './etc/app.version.php'; echo APP_VERSION;" | grep -q "${YMD}"; then
  echo "✅ APP_VERSION 이 올바로 출력됩니다."
else
  echo "❌ APP_VERSION 출력에 실패하였습니다."
  exit 1
fi




# Git commit & push
git add .
git commit -m "Commit for the release of the version: ${YMD}"
git push




# SSH update
echo "🚀 SSH를 통한 실제 배포 시작..."
ssh sonub@68.183.185.185 'cd sonub && git pull'

echo "✅ 배포 완료!"
open 'https://sonub.com'

