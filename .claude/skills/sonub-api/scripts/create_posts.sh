#!/bin/bash

# Sonub API - create_posts script
# 목적: 테스트 계정으로 여러 개의 게시글 생성
# 주의: bash 기본 명령어만 사용 (jq, perl 등 외부 도구 금지)
#
# 사용법: ./create_posts.sh [옵션]
# 예제:
#   ./create_posts.sh --count 5
#   ./create_posts.sh --count 10 --category discussion
#   ./create_posts.sh --count 3 --user banana --api-url "https://local.sonub.com/api.php"

# 기본값
API_URL="${API_URL:-https://sonub.com/api.php}"
POST_COUNT=3
TEST_USER="banana"
TEST_PHONE="+11234567891"
POST_CATEGORY=""

# 이미지 기본 URL (DummyImage 사용 - 안정적이고 빠름)
# 다양한 색상 조합 사용하여 시각적 다양성 확보
IMAGE_BASE="https://dummyimage.com"

# 이미지 색상 팔레트 (다양한 시각적 효과)
IMAGE_COLORS=(
    "4CAF50/FFFFFF"  # 녹색 배경, 흰색 텍스트
    "2196F3/FFFFFF"  # 파란색 배경, 흰색 텍스트
    "FF9800/FFFFFF"  # 주황색 배경, 흰색 텍스트
    "9C27B0/FFFFFF"  # 보라색 배경, 흰색 텍스트
    "E91E63/FFFFFF"  # 분홍색 배경, 흰색 텍스트
    "00BCD4/FFFFFF"  # 청록색 배경, 흰색 텍스트
    "FF5722/FFFFFF"  # 빨간색 배경, 흰색 텍스트
    "009688/FFFFFF"  # 청록색(진함) 배경, 흰색 텍스트
)

# 도움말 함수
show_help() {
    cat << 'EOF'
사용법: ./create_posts.sh [옵션]

옵션:
  --count N          생성할 게시글 수 (기본값: 3, 범위: 1-50)
  --user NAME        테스트 계정명 (기본값: banana)
  --category CAT     카테고리 지정 (기본값: 랜덤)
  --api-url URL      API URL (기본값: https://sonub.com/api.php)
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

  뉴스 (news):
    - technology (기술)
    - business (비즈니스)
    - ai (인공지능)
    - movies (영화)
    - drama (드라마)
    - music (음악)

  부동산 (realestate):
    - buy (구매)
    - sell (판매)
    - rent (임대)

  구인구직 (jobs):
    - full-time (전일제)
    - part-time (시간제)
    - freelance (프리랜서)

사용 예제:
  $0 --count 5
  $0 --count 10 --category discussion
  $0 --count 3 --user apple --api-url 'https://local.sonub.com/api.php'
  $0 --count 5 --category qna --user cherry --api-url 'https://local.sonub.com/api.php'
EOF
    exit 0
}

# JSON 특수문자 이스케이프 함수 (printf와 sed 사용으로 인코딩 문제 해결)
escape_json() {
    local string="$1"
    # printf를 사용하여 문자열을 안전하게 처리하고, sed 에러는 무시
    printf '%s\n' "$string" | sed 's/\\/\\\\/g; s/"/\\"/g' 2>/dev/null
}

# 인자 파싱
while [[ $# -gt 0 ]]; do
    case $1 in
        --count)
            POST_COUNT="$2"
            shift 2
            ;;
        --user)
            TEST_USER="$2"
            shift 2
            ;;
        --category)
            POST_CATEGORY="$2"
            shift 2
            ;;
        --api-url)
            API_URL="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            ;;
        *)
            echo "알 수 없는 옵션: $1"
            echo "$0 --help 를 실행하여 사용법을 확인하세요"
            exit 1
            ;;
    esac
done

# 테스트 사용자를 전화번호로 매핑
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
        echo "오류: 알 수 없는 테스트 사용자: $TEST_USER"
        echo "허용된 사용자: apple, banana, cherry, durian, elderberry, fig, grape, honeydew, jackfruit, kiwi, lemon, mango"
        exit 1
        ;;
esac

# 게시글 수 검증
if ! [[ "$POST_COUNT" =~ ^[0-9]+$ ]] || [ "$POST_COUNT" -lt 1 ] || [ "$POST_COUNT" -gt 50 ]; then
    echo "오류: 게시글 수는 1 이상 50 이하여야 합니다"
    exit 1
fi

# 카테고리 검증
if [ -n "$POST_CATEGORY" ]; then
    case "$POST_CATEGORY" in
        discussion|qna|story|relationships|fitness|beauty|cooking|pets|parenting|\
        electronics|fashion|furniture|books|sports-equipment|vehicles|real-estate|\
        technology|business|ai|movies|drama|music|\
        buy|sell|rent|\
        full-time|part-time|freelance)
            # 유효한 카테고리
            ;;
        *)
            echo "오류: 알 수 없는 카테고리: $POST_CATEGORY"
            echo "$0 --help 를 실행하여 사용 가능한 카테고리를 확인하세요"
            exit 1
            ;;
    esac
fi

# 사용 가능한 카테고리 배열
CATEGORIES=(
    "discussion" "qna" "story" "relationships" "fitness" "beauty" "cooking" "pets" "parenting"
    "electronics" "fashion" "furniture" "books" "sports-equipment" "vehicles" "real-estate"
    "technology" "business" "ai" "movies" "drama" "music"
    "buy" "sell" "rent"
    "full-time" "part-time" "freelance"
)

echo "=========================================="
echo "게시글 생성 - Sonub API 스크립트"
echo "=========================================="
echo ""
echo "설정:"
echo "  API URL: $API_URL"
echo "  생성할 게시글 수: $POST_COUNT"
echo "  테스트 사용자: $TEST_USER"
echo "  전화번호: $TEST_PHONE"
[ -n "$POST_CATEGORY" ] && echo "  카테고리: $POST_CATEGORY"
echo ""

# 1단계: 로그인 및 세션 쿠키 획득
echo "1단계: 테스트 계정으로 로그인 중..."
echo ""

LOGIN_JSON="{\"func\": \"login_with_firebase\", \"firebase_uid\": \"$TEST_USER\", \"phone_number\": \"$TEST_PHONE\"}"

COOKIE_JAR=$(mktemp)
LOGIN_RESPONSE=$(curl -s -k -c "$COOKIE_JAR" -X POST "$API_URL" \
    -H "Content-Type: application/json" \
    -d "$LOGIN_JSON")

# 로그인 응답 확인
if echo "$LOGIN_RESPONSE" | grep -q "error_code"; then
    echo "오류: 로그인 실패"
    echo "$LOGIN_RESPONSE"
    rm -f "$COOKIE_JAR"
    exit 1
fi

# bash로 JSON에서 필드 추출 (grep + cut 사용으로 한글 인코딩 문제 해결)
USER_ID=$(echo "$LOGIN_RESPONSE" | grep -o '"id":[0-9]*' | cut -d: -f2)
if [ -z "$USER_ID" ]; then
    echo "오류: 로그인 응답에서 사용자 ID를 가져올 수 없습니다"
    echo "$LOGIN_RESPONSE"
    rm -f "$COOKIE_JAR"
    exit 1
fi

USER_NAME=$(echo "$LOGIN_RESPONSE" | grep -o '"first_name":"[^"]*"' | cut -d'"' -f4)
[ -z "$USER_NAME" ] && USER_NAME="Unknown"

echo "✓ 로그인 성공!"
echo "  사용자 ID: $USER_ID"
echo "  이름: $USER_NAME"
echo ""

# 2단계: 게시글 생성
echo "2단계: 게시글 생성 중..."
echo ""

SUCCESS_COUNT=0
FAIL_COUNT=0

for i in $(seq 1 "$POST_COUNT"); do
    # 카테고리 선택
    if [ -n "$POST_CATEGORY" ]; then
        CATEGORY="$POST_CATEGORY"
    else
        CATEGORY_INDEX=$((i % ${#CATEGORIES[@]}))
        CATEGORY="${CATEGORIES[$CATEGORY_INDEX]}"
    fi

    # 이미지 개수 결정 (0-7)
    IMAGE_COUNT=$((i % 8))

    # 이미지 URL 생성 (DummyImage 사용)
    IMAGE_URLS=""
    if [ "$IMAGE_COUNT" -gt 0 ]; then
        for j in $(seq 1 "$IMAGE_COUNT"); do
            # 색상 선택 (반복 사용)
            COLOR_INDEX=$(((i + j) % ${#IMAGE_COLORS[@]}))
            COLOR="${IMAGE_COLORS[$COLOR_INDEX]}"

            # 이미지 크기 (다양한 종횡비)
            IMAGE_WIDTH=$((200 + i * 50))
            IMAGE_HEIGHT=$((150 + i * 40))

            # DummyImage 형식: https://dummyimage.com/{width}x{height}/{bgColor}/{textColor}/{text}
            IMAGE_TEXT="Image%20$j"
            IMAGE_URL="$IMAGE_BASE/${IMAGE_WIDTH}x${IMAGE_HEIGHT}/${COLOR}/${IMAGE_TEXT}"

            if [ -z "$IMAGE_URLS" ]; then
                IMAGE_URLS="$IMAGE_URL"
            else
                IMAGE_URLS="$IMAGE_URLS,$IMAGE_URL"
            fi
        done
    fi

    # 게시글 제목 및 내용 생성
    TITLE="게시글 $i - $(date +'%Y-%m-%d %H:%M:%S')"
    CONTENT="자동 생성된 테스트 게시글입니다.\n\n카테고리: $CATEGORY\n이미지: $IMAGE_COUNT개"

    # 특수문자 이스케이프
    TITLE_ESCAPED=$(escape_json "$TITLE")
    CONTENT_ESCAPED=$(escape_json "$CONTENT")
    IMAGE_URLS_ESCAPED=$(escape_json "$IMAGE_URLS")

    # 게시글 JSON 생성 (bash 기본 명령어만 사용)
    if [ -n "$IMAGE_URLS" ]; then
        POST_JSON="{\"func\": \"create_post\", \"title\": \"$TITLE_ESCAPED\", \"content\": \"$CONTENT_ESCAPED\", \"category\": \"$CATEGORY\", \"visibility\": \"public\", \"files\": \"$IMAGE_URLS_ESCAPED\"}"
    else
        POST_JSON="{\"func\": \"create_post\", \"title\": \"$TITLE_ESCAPED\", \"content\": \"$CONTENT_ESCAPED\", \"category\": \"$CATEGORY\", \"visibility\": \"public\"}"
    fi

    # create_post API 호출
    POST_RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
        -H "Content-Type: application/json" \
        -d "$POST_JSON")

    # 응답 확인 (grep + cut 사용)
    POST_ID=$(echo "$POST_RESPONSE" | grep -o '"id":[0-9]*' | cut -d: -f2)
    if [ -n "$POST_ID" ]; then
        SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
        echo "  ✓ 게시글 #$i 생성 완료 (ID: $POST_ID, 카테고리: $CATEGORY, 이미지: $IMAGE_COUNT개)"
    else
        FAIL_COUNT=$((FAIL_COUNT + 1))
        ERROR_CODE=$(echo "$POST_RESPONSE" | grep -o '"error_code":"[^"]*"' | cut -d'"' -f4)
        ERROR_MSG=$(echo "$POST_RESPONSE" | grep -o '"error_message":"[^"]*"' | cut -d'"' -f4)
        [ -z "$ERROR_CODE" ] && ERROR_CODE="unknown"
        [ -z "$ERROR_MSG" ] && ERROR_MSG="알 수 없는 오류"
        echo "  ✗ 게시글 #$i 생성 실패 (오류: $ERROR_CODE - $ERROR_MSG)"
    fi
done

echo ""
echo "=========================================="
echo "결과"
echo "=========================================="
echo ""
echo "총 요청: $POST_COUNT"
echo "성공: $SUCCESS_COUNT"
echo "실패: $FAIL_COUNT"
echo ""

# 정리
rm -f "$COOKIE_JAR"

if [ "$FAIL_COUNT" -gt 0 ]; then
    exit 1
fi

echo "완료!"
