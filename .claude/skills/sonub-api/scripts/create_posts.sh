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
CUSTOM_TITLE=""
CUSTOM_CONTENT=""
CUSTOM_IMAGE_COUNT=0

# 실제 이미지 서비스 (고품질 무료 이미지)
# - LoremFlickr: 카테고리별 실제 Flickr 이미지 (최적)
# - Picsum Photos: 무료 이미지, 무한 다양성
# - Unsplash: 최고 품질 이미지

# 기본 이미지 베이스 URL (LoremFlickr 사용)
# LoremFlickr 형식: https://loremflickr.com/{width}/{height}/{keyword1,keyword2,...}
IMAGE_BASE="https://loremflickr.com"

# 카테고리별 이미지 키워드 매핑 함수 (LoremFlickr용)
# LoremFlickr은 comma-separated 키워드를 지원하여 더 정확한 이미지 제공
get_category_image_keywords() {
    local category="$1"
    case "$category" in
        discussion)     echo "people,conversation" ;;        # 자유토론 - 사람, 대화
        qna)            echo "question,answer,help" ;;       # 질문과답변 - 질문, 답변, 도움
        story)          echo "nature,landscape,beautiful" ;; # 나의 이야기 - 자연, 풍경
        relationships)  echo "people,friends,together" ;;    # 관계 - 사람, 친구들
        fitness)        echo "fitness,gym,sport,exercise" ;; # 운동 - 피트니스, 체육관, 운동
        beauty)         echo "beauty,makeup,cosmetics" ;;    # 뷰티 - 뷰티, 화장, 화장품
        cooking)        echo "food,cooking,recipe,kitchen" ;;# 요리 - 음식, 요리, 레시피
        pets)           echo "animals,pets,dog,cat" ;;       # 반려동물 - 동물, 반려동물, 개, 고양이
        parenting)      echo "family,kids,baby,children" ;;  # 육아 - 가족, 아이, 아기
        electronics)    echo "technology,electronics,gadget" ;; # 전자제품 - 기술, 전자
        fashion)        echo "fashion,clothing,style,clothes" ;; # 패션 - 패션, 의류, 스타일
        furniture)      echo "furniture,interior,design,home" ;; # 가구 - 가구, 인테리어, 디자인
        books)          echo "books,library,reading" ;;       # 책 - 책, 도서관, 독서
        sports-equipment) echo "sports,equipment,fitness" ;;  # 스포츠용품 - 스포츠, 장비
        vehicles)       echo "cars,automobile,vehicle,driving" ;; # 차량 - 자동차, 차량
        real-estate)    echo "building,house,architecture,property" ;; # 부동산 - 건물, 집, 건축
        technology)     echo "technology,computer,digital,tech" ;; # 기술 - 기술, 컴퓨터
        business)       echo "business,office,meeting,work" ;; # 비즈니스 - 비즈니스, 사무실
        ai)             echo "technology,artificial,robot,ai" ;; # 인공지능 - 기술, 로봇
        movies)         echo "film,cinema,movie,entertainment" ;; # 영화 - 영화, 엔터테인먼트
        drama)          echo "drama,emotion,scene,performance" ;; # 드라마 - 드라마, 감정, 공연
        music)          echo "music,musician,concert,instrument" ;; # 음악 - 음악, 뮤지션, 콘서트
        buy)            echo "shopping,shop,market,product" ;; # 구매 - 쇼핑, 상점, 제품
        sell)           echo "shop,market,retail,commerce" ;; # 판매 - 상점, 마켓, 상거래
        rent)           echo "building,apartment,housing,rent" ;; # 임대 - 건물, 아파트, 주택
        full-time)      echo "office,work,professional,business" ;; # 전일제 - 사무실, 일, 비즈니스
        part-time)      echo "people,work,job,part-time" ;;   # 시간제 - 사람, 일, 직업
        freelance)      echo "work,computer,office,freelance" ;; # 프리랜서 - 일, 컴퓨터, 자유
        *)              echo "people,nature" ;;                # 기본값
    esac
}

# 도움말 함수
show_help() {
    cat << 'EOF'
사용법: ./create_posts.sh [옵션]

옵션:
  --count N          생성할 게시글 수 (기본값: 3, 범위: 1-50)
  --user NAME        테스트 계정명 (기본값: banana)
  --category CAT     카테고리 지정 (기본값: 랜덤)
  --title TITLE      사용자 정의 글 제목 (기본값: 자동 생성)
  --content CONTENT  사용자 정의 글 내용 (기본값: 자동 생성)
  --image-count N    첨부할 이미지 개수 (기본값: 0, 범위: 0-10)
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
  # 테스트 글 생성 (자동 생성된 제목/내용)
  $0 --count 5
  $0 --count 10 --category discussion

  # 실제 글 생성 (사용자 정의 제목/내용)
  $0 --title '안녕하세요' --content '첫 게시글입니다' --image-count 2
  $0 --count 3 --title '추천 게시글' --content '멋진 내용입니다' --category discussion --user apple

  # 이미지 없는 글 생성 (기본값: --image-count 0)
  $0 --count 5 --title '텍스트만' --category qna --user cherry

  # 로컬 서버 테스트
  $0 --count 3 --user apple --api-url 'https://local.sonub.com/api.php'
  $0 --title '로컬 테스트' --content '로컬 서버에 글을 올립니다' --image-count 1 --api-url 'https://local.sonub.com/api.php'
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
        --title)
            CUSTOM_TITLE="$2"
            shift 2
            ;;
        --content)
            CUSTOM_CONTENT="$2"
            shift 2
            ;;
        --image-count)
            CUSTOM_IMAGE_COUNT="$2"
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

# 이미지 개수 검증
if ! [[ "$CUSTOM_IMAGE_COUNT" =~ ^[0-9]+$ ]] || [ "$CUSTOM_IMAGE_COUNT" -lt 0 ] || [ "$CUSTOM_IMAGE_COUNT" -gt 10 ]; then
    echo "오류: 이미지 개수는 0 이상 10 이하여야 합니다"
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
[ -n "$CUSTOM_TITLE" ] && echo "  글 제목: $CUSTOM_TITLE"
[ -n "$CUSTOM_CONTENT" ] && echo "  글 내용: $CUSTOM_CONTENT"
echo "  이미지 개수: $CUSTOM_IMAGE_COUNT개"
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

    # 이미지 개수 결정
    # 사용자가 --image-count를 지정하면 그 값 사용, 아니면 기본값 0
    IMAGE_COUNT="$CUSTOM_IMAGE_COUNT"

    # 카테고리에 맞는 이미지 키워드 획득
    IMAGE_KEYWORDS=$(get_category_image_keywords "$CATEGORY")

    # LoremFlickr 키워드에서 쉼표를 URL 인코딩 (%2C)으로 변환
    # (파일 리스트 구분용 쉼표와 충돌 방지)
    IMAGE_KEYWORDS_ENCODED="${IMAGE_KEYWORDS//,/%2C}"

    # LoremFlickr 이미지 URL 생성
    IMAGE_URLS=""
    if [ "$IMAGE_COUNT" -gt 0 ]; then
        for j in $(seq 1 "$IMAGE_COUNT"); do
            # 다양한 이미지 크기 (모바일, 태블릿, 데스크톱)
            case $((j % 3)) in
                0)
                    # 세로형 이미지 (모바일)
                    IMAGE_WIDTH=400
                    IMAGE_HEIGHT=600
                    ;;
                1)
                    # 가로형 이미지 (일반)
                    IMAGE_WIDTH=600
                    IMAGE_HEIGHT=400
                    ;;
                *)
                    # 정사각형 이미지
                    IMAGE_WIDTH=500
                    IMAGE_HEIGHT=500
                    ;;
            esac

            # LoremFlickr: 카테고리별 실제 Flickr 이미지
            # 형식: https://loremflickr.com/{width}/{height}/{keyword1%2Ckeyword2%2C...}?random={random_id}
            # - 키워드의 쉼표는 %2C로 URL 인코딩 (파일 리스트 쉼표와 구분)
            # - random 파라미터로 매번 다른 이미지 제공
            RANDOM_ID=$((RANDOM * i + j))
            IMAGE_URL="$IMAGE_BASE/${IMAGE_WIDTH}/${IMAGE_HEIGHT}/${IMAGE_KEYWORDS_ENCODED}?random=${RANDOM_ID}"

            if [ -z "$IMAGE_URLS" ]; then
                IMAGE_URLS="$IMAGE_URL"
            else
                IMAGE_URLS="$IMAGE_URLS,$IMAGE_URL"
            fi
        done
    fi

    # 게시글 제목 및 내용 생성
    # 사용자 정의 제목/내용이 있으면 그걸 사용, 없으면 자동 생성
    if [ -n "$CUSTOM_TITLE" ]; then
        TITLE="$CUSTOM_TITLE"
    else
        TITLE="게시글 $i - $(date +'%Y-%m-%d %H:%M:%S')"
    fi

    if [ -n "$CUSTOM_CONTENT" ]; then
        CONTENT="$CUSTOM_CONTENT"
    else
        CONTENT="자동 생성된 테스트 게시글입니다.\n\n카테고리: $CATEGORY\n이미지: $IMAGE_COUNT개"
    fi

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
