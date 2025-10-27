#!/bin/bash
# Sonub API - list_posts 함수 호출 스크립트
# 사용법: ./list_posts.sh [옵션]
# 예제:
#   ./list_posts.sh
#   ./list_posts.sh --category discussion --limit 20
#   ./list_posts.sh --user-id 5 --limit 10
#   ./list_posts.sh --visibility public --page 2

# 기본값 설정
API_URL="${API_URL:-https://sonub.com/api.php}"
LIMIT=10
PAGE=1
CATEGORY=""
USER_ID=""
VISIBILITY=""
ORDER="created_at DESC"

# 도움말 함수
show_help() {
    echo "사용법: $0 [옵션]"
    echo ""
    echo "옵션:"
    echo "  --category STR     카테고리 필터 (예: discussion, qna, my-wall)"
    echo "  --user-id N        특정 사용자의 게시글만 조회"
    echo "  --visibility STR   공개 범위 필터 (public, friends, private)"
    echo "  --limit N          페이지당 게시글 수 (기본값: 10, 최대: 100)"
    echo "  --page N           페이지 번호 (기본값: 1)"
    echo "  --order STR        정렬 순서 (기본값: 'created_at DESC')"
    echo "  --url URL          API URL (기본값: https://local.sonub.com/api.php)"
    echo "  -h, --help         이 도움말 표시"
    echo ""
    echo "예제:"
    echo "  $0 --category discussion --limit 20"
    echo "  $0 --user-id 5 --limit 10"
    echo "  $0 --visibility public --page 2"
    echo "  $0 --category qna --order 'created_at ASC'"
    echo "  $0 --url https://sonub.com/api.php --category my-wall"
    exit 0
}

# 인자 파싱
while [[ $# -gt 0 ]]; do
    case $1 in
        --category)
            CATEGORY="$2"
            shift 2
            ;;
        --user-id)
            USER_ID="$2"
            shift 2
            ;;
        --visibility)
            VISIBILITY="$2"
            shift 2
            ;;
        --limit)
            LIMIT="$2"
            shift 2
            ;;
        --page)
            PAGE="$2"
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
  "func": "list_posts",
  "limit": '"$LIMIT"',
  "page": '"$PAGE"''

# 선택적 파라미터 추가
if [ -n "$CATEGORY" ]; then
    JSON_PAYLOAD+=',
  "category": "'"$CATEGORY"'"'
fi

if [ -n "$USER_ID" ]; then
    JSON_PAYLOAD+=',
  "user_id": '"$USER_ID"
fi

if [ -n "$VISIBILITY" ]; then
    JSON_PAYLOAD+=',
  "visibility": "'"$VISIBILITY"'"'
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
[ -n "$CATEGORY" ] && echo "          category=$CATEGORY"
[ -n "$USER_ID" ] && echo "          user_id=$USER_ID"
[ -n "$VISIBILITY" ] && echo "          visibility=$VISIBILITY"
echo ""

curl -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d "$JSON_PAYLOAD" \
  -s -k | jq '.'
