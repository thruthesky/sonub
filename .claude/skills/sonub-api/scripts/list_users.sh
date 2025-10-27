#!/bin/bash
# Sonub API - list_users 함수 호출 스크립트
# 사용법: ./list_users.sh [옵션]
# 예제:
#   ./list_users.sh
#   ./list_users.sh --limit 20 --page 2
#   ./list_users.sh --gender M --limit 10
#   ./list_users.sh --age-min 20 --age-max 30

# 기본값 설정
API_URL="${API_URL:-https://sonub.com/api.php}"
LIMIT=10
PAGE=1
GENDER=""
AGE_MIN=""
AGE_MAX=""
ORDER="created_at DESC"

# 도움말 함수
show_help() {
    echo "사용법: $0 [옵션]"
    echo ""
    echo "옵션:"
    echo "  --limit N          페이지당 사용자 수 (기본값: 10, 최대: 100)"
    echo "  --page N           페이지 번호 (기본값: 1)"
    echo "  --gender [M|F]     성별 필터 (M: 남성, F: 여성)"
    echo "  --age-min N        최소 나이"
    echo "  --age-max N        최대 나이"
    echo "  --order STR        정렬 순서 (기본값: 'created_at DESC')"
    echo "  --url URL          API URL (기본값: https://local.sonub.com/api.php)"
    echo "  -h, --help         이 도움말 표시"
    echo ""
    echo "예제:"
    echo "  $0 --limit 20 --page 2"
    echo "  $0 --gender F --limit 10"
    echo "  $0 --age-min 20 --age-max 30"
    echo "  $0 --url https://sonub.com/api.php --limit 5"
    exit 0
}

# 인자 파싱
while [[ $# -gt 0 ]]; do
    case $1 in
        --limit)
            LIMIT="$2"
            shift 2
            ;;
        --page)
            PAGE="$2"
            shift 2
            ;;
        --gender)
            GENDER="$2"
            shift 2
            ;;
        --age-min)
            AGE_MIN="$2"
            shift 2
            ;;
        --age-max)
            AGE_MAX="$2"
            shift 2
            ;;
        --order)
            ORDER="$2"
            shift 2
            ;;
        --url)
            API_URL="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            ;;
        *)
            echo "알 수 없는 옵션: $1"
            echo "도움말을 보려면 $0 --help 를 실행하세요."
            exit 1
            ;;
    esac
done

# JSON 페이로드 생성
JSON_PAYLOAD='{
  "func": "list_users",
  "limit": '"$LIMIT"',
  "page": '"$PAGE"''

# 선택적 파라미터 추가
if [ -n "$GENDER" ]; then
    JSON_PAYLOAD+=',
  "gender": "'"$GENDER"'"'
fi

if [ -n "$AGE_MIN" ]; then
    JSON_PAYLOAD+=',
  "age_min": '"$AGE_MIN"
fi

if [ -n "$AGE_MAX" ]; then
    JSON_PAYLOAD+=',
  "age_max": '"$AGE_MAX"
fi

if [ -n "$ORDER" ]; then
    JSON_PAYLOAD+=',
  "order": "'"$ORDER"'"'
fi

JSON_PAYLOAD+='
}'

# API 호출
echo "API 호출 중: $API_URL"
echo "파라미터: limit=$LIMIT, page=$PAGE"
[ -n "$GENDER" ] && echo "          gender=$GENDER"
[ -n "$AGE_MIN" ] && echo "          age_min=$AGE_MIN"
[ -n "$AGE_MAX" ] && echo "          age_max=$AGE_MAX"
echo ""

curl -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$JSON_PAYLOAD" \
  -s -k | jq '.'