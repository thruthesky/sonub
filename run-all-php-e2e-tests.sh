#!/bin/bash
# 스크립트 실행 중 어떤 명령이든 실패하면 즉시 종료.


set -e
set -o pipefail
# 이유: 실패한 상태로 다음 명령을 실행하지 않기 위해.

# run all the tests/e2e/deploy-tsts/*.test.php files
for test_file in ./tests/e2e/deploy-tests/*.test.php; do
  echo "========================================"
  echo "Running test: $test_file"
  echo "========================================"
  php "$test_file"
  echo ""
done