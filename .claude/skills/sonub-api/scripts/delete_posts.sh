#!/bin/bash

# Sonub API - delete_posts script
# 목적: 테스트 계정의 게시글 삭제
# 주의: bash 기본 명령어만 사용 (jq, perl 등 외부 도구 금지)
#
# 사용법: ./delete_posts.sh [옵션]
# 예제:
#   ./delete_posts.sh --count 5
#   ./delete_posts.sh --count 10 --category discussion
#   ./delete_posts.sh --count 3 --user banana --api-url "https://local.sonub.com/api.php" --confirm
#   ./delete_posts.sh --category qna --confirm

# 기본값
API_URL="${API_URL:-https://sonub.com/api.php}"
DELETE_COUNT=10
TEST_USER="banana"
TEST_PHONE=""
CATEGORY=""
CONFIRM_DELETE=false
TEMP_DIR=$(mktemp -d)
COOKIE_JAR="$TEMP_DIR/cookies.txt"

# 종료 시 임시 파일 정리
cleanup() {
    rm -rf "$TEMP_DIR"
}
trap cleanup EXIT

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 도움말 함수
show_help() {
    cat << 'EOF'
사용법: ./delete_posts.sh [옵션]

옵션:
  --count N          삭제할 게시글 수 (기본값: 10, 범위: 1-100)
  --user NAME        테스트 계정명 (기본값: banana)
  --category CAT     카테고리 필터 (기본값: 모든 카테고리)
  --api-url URL      API URL (기본값: https://sonub.com/api.php)
  --confirm          확인 없이 즉시 삭제 (기본값: 삭제 전 확인)
  -h, --help         이 도움말 표시

테스트 계정:
  apple, banana, cherry, durian, elderberry
  fig, grape, honeydew, jackfruit, kiwi, lemon, mango

사용 가능한 카테고리:
  커뮤니티 (community):
    - discussion (자유토론)
    - qna (질문과답변)
    - story (나의 이야기)
    - relationships (관계)
    - fitness (운동)
    - beauty (뷰티)
    - cooking (요리)
    - pets (반려동물)
    - parenting (육아)

  장터 (buyandsell):
    - electronics (전자제품)
    - fashion (패션)
    - furniture (가구)
    - books (책)
    - sports-equipment (스포츠용품)
    - vehicles (차량)
    - real-estate (부동산)

  취업 (job):
    - full-time (전일제)
    - part-time (시간제)
    - freelance (프리랜서)

  기타:
    - ai (인공지능)
    - movies (영화)
    - drama (드라마)
    - music (음악)
    - technology (기술)
    - business (비즈니스)

사용 예제:
  $0 --count 5 --confirm
  $0 --count 10 --category discussion
  $0 --count 3 --user banana --category qna --confirm
  $0 --category fitness --confirm
  $0 --api-url https://local.sonub.com/api.php --count 5 --confirm

중요 사항:
  ⚠️  본인이 작성한 글만 삭제 가능합니다
  ⚠️  삭제된 글은 복구할 수 없습니다
  ⚠️  게시글을 삭제하면 관련된 댓글도 함께 삭제됩니다

EOF
    exit 0
}

# 인자 파싱
while [[ $# -gt 0 ]]; do
    case $1 in
        --count)
            DELETE_COUNT="$2"
            shift 2
            ;;
        --user)
            TEST_USER="$2"
            shift 2
            ;;
        --category)
            CATEGORY="$2"
            shift 2
            ;;
        --api-url)
            API_URL="$2"
            shift 2
            ;;
        --confirm)
            CONFIRM_DELETE=true
            shift
            ;;
        -h|--help)
            show_help
            ;;
        *)
            echo "❌ 알 수 없는 옵션: $1"
            echo "도움말을 보려면 $0 --help 를 실행하세요."
            exit 1
            ;;
    esac
done

# 테스트 사용자를 전화번호로 매핑 (create_posts.sh와 동일)
case "$TEST_USER" in
    apple) TEST_PHONE="+11234567890" ;;
    banana) TEST_PHONE="+11234567891" ;;
    cherry) TEST_PHONE="+11234567892" ;;
    durian) TEST_PHONE="+11234567893" ;;
    elderberry) TEST_PHONE="+11234567894" ;;
    fig) TEST_PHONE="+11234567895" ;;
    grape) TEST_PHONE="+11234567896" ;;
    honeydew) TEST_PHONE="+11234567897" ;;
    jackfruit) TEST_PHONE="+11234567898" ;;
    kiwi) TEST_PHONE="+11234567899" ;;
    lemon) TEST_PHONE="+11234567900" ;;
    mango) TEST_PHONE="+11234567901" ;;
    *)
        echo "❌ 오류: 알 수 없는 테스트 사용자: $TEST_USER"
        echo "허용된 사용자: apple, banana, cherry, durian, elderberry, fig, grape, honeydew, jackfruit, kiwi, lemon, mango"
        exit 1
        ;;
esac

# 입력값 검증
if ! [[ "$DELETE_COUNT" =~ ^[0-9]+$ ]] || [ "$DELETE_COUNT" -lt 1 ] || [ "$DELETE_COUNT" -gt 100 ]; then
    echo "❌ 삭제할 게시글 수는 1~100 사이의 숫자여야 합니다."
    exit 1
fi

echo -e "${BLUE}════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}Sonub API - 게시글 삭제 스크립트${NC}"
echo -e "${BLUE}════════════════════════════════════════════════════════════════${NC}"
echo ""
echo "설정:"
echo "  • API URL: $API_URL"
echo "  • 계정: $TEST_USER"
echo "  • 삭제 수: $DELETE_COUNT"
if [ -n "$CATEGORY" ]; then
    echo "  • 카테고리: $CATEGORY"
fi
echo "  • 자동 삭제: $([[ $CONFIRM_DELETE == true ]] && echo '예' || echo '아니오')"
echo ""

# 1단계: 로그인
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}1단계: 로그인${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo "계정 '$TEST_USER' 로그인 중..."

LOGIN_RESPONSE=$(curl -s -k -c "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "{\"func\": \"login_with_firebase\", \"firebase_uid\": \"$TEST_USER\", \"phone_number\": \"$TEST_PHONE\"}" 2>/dev/null)

# 로그인 결과 확인
if echo "$LOGIN_RESPONSE" | grep -q "error_code"; then
    echo -e "${RED}❌ 로그인 실패${NC}"
    echo "응답: $LOGIN_RESPONSE"
    exit 1
fi

# 사용자 ID 추출
USER_ID=$(echo "$LOGIN_RESPONSE" | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2)

if [ -z "$USER_ID" ]; then
    echo -e "${RED}❌ 사용자 ID를 찾을 수 없습니다${NC}"
    echo "응답: $LOGIN_RESPONSE"
    exit 1
fi

echo -e "${GREEN}✓ 로그인 성공!${NC}"
echo "  • 사용자 ID: $USER_ID"
echo ""

# 2단계: 게시글 목록 조회
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}2단계: 게시글 목록 조회${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo "게시글 목록 조회 중..."

# JSON 페이로드 생성
JSON_PAYLOAD='{
  "func": "list_posts",
  "limit": '"$DELETE_COUNT"''

# 카테고리 필터 추가
if [ -n "$CATEGORY" ]; then
    JSON_PAYLOAD+=',
  "category": "'"$CATEGORY"'"'
fi

JSON_PAYLOAD+='
}'

LIST_RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$JSON_PAYLOAD" 2>/dev/null)

# 게시글 ID 추출 (grep + cut 사용, jq 사용 금지)
# 형식: "id":123 → 123 추출
POST_IDS=$(echo "$LIST_RESPONSE" | grep -o '"id":[0-9]*' | cut -d: -f2 | head -"$DELETE_COUNT")

# 실제로 추출된 게시글 수 계산
ACTUAL_COUNT=$(echo "$POST_IDS" | wc -w)

if [ "$ACTUAL_COUNT" -eq 0 ]; then
    echo -e "${YELLOW}⚠️  삭제할 게시글이 없습니다${NC}"
    exit 0
fi

echo -e "${GREEN}✓ $ACTUAL_COUNT 개의 게시글을 찾았습니다${NC}"
echo ""
echo "발견된 게시글 ID:"
for POST_ID in $POST_IDS; do
    echo "  • #$POST_ID"
done
echo ""

# 3단계: 삭제 확인
if [ "$CONFIRM_DELETE" = false ]; then
    echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}⚠️  경고: $ACTUAL_COUNT 개의 게시글을 삭제하려고 합니다!${NC}"
    echo -e "${YELLOW}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo "삭제된 게시글은 복구할 수 없습니다."
    echo ""
    echo -n "정말로 삭제하시겠습니까? (yes/no): "
    read -r CONFIRM_INPUT

    if [ "$CONFIRM_INPUT" != "yes" ]; then
        echo -e "${YELLOW}취소되었습니다${NC}"
        exit 0
    fi
    echo ""
fi

# 4단계: 게시글 삭제
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}3단계: 게시글 삭제${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

DELETE_SUCCESS=0
DELETE_FAILED=0
FAILED_IDS=()

for POST_ID in $POST_IDS; do
    echo -n "게시글 #$POST_ID 삭제 중... "

    DELETE_RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
      -H "Content-Type: application/json" \
      -d "{\"func\": \"delete_post\", \"id\": $POST_ID}" 2>/dev/null)

    # error_code가 없으면 성공으로 간주
    if ! echo "$DELETE_RESPONSE" | grep -q "error_code"; then
        echo -e "${GREEN}✓ 삭제 완료${NC}"
        ((DELETE_SUCCESS++))
    else
        echo -e "${RED}✗ 삭제 실패${NC}"
        ((DELETE_FAILED++))
        FAILED_IDS+=("$POST_ID")
    fi
done

echo ""

# 5단계: 결과 요약
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}삭제 결과 요약${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo "성공: $DELETE_SUCCESS / 실패: $DELETE_FAILED / 합계: $ACTUAL_COUNT"

if [ "$DELETE_FAILED" -gt 0 ]; then
    echo -e "${YELLOW}삭제 실패한 게시글 ID:${NC}"
    for FAILED_ID in "${FAILED_IDS[@]}"; do
        echo "  • #$FAILED_ID"
    done
fi

echo ""

if [ "$DELETE_SUCCESS" -eq "$ACTUAL_COUNT" ]; then
    echo -e "${GREEN}✓ 모든 게시글이 성공적으로 삭제되었습니다!${NC}"
    exit 0
else
    echo -e "${YELLOW}⚠️  일부 게시글 삭제에 실패했습니다${NC}"
    exit 1
fi
