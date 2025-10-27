#!/bin/bash

# Sonub API - create_posts script
# Purpose: Create multiple posts using test account
#
# Usage: ./create_posts.sh [options]
# Examples:
#   ./create_posts.sh --count 5
#   ./create_posts.sh --count 10 --api-url "https://local.sonub.com/api.php"
#   ./create_posts.sh --count 3 --user banana --api-url "https://local.sonub.com/api.php"

# Default values
API_URL="${API_URL:-https://sonub.com/api.php}"
POST_COUNT=3
TEST_USER="banana"
TEST_PHONE="+11234567891"

# Post categories
CATEGORIES=("discussion" "qna" "my-wall")

# Image base URL (picsum.photos)
IMAGE_BASE="https://picsum.photos"

# Help function
show_help() {
    echo "Usage: $0 [options]"
    echo ""
    echo "Options:"
    echo "  --count N          Number of posts to create (default: 3, min: 1, max: 50)"
    echo "  --user NAME        Test user name (default: banana)"
    echo "  --api-url URL      API URL (default: https://sonub.com/api.php)"
    echo "  -h, --help         Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 --count 5"
    echo "  $0 --count 10 --api-url 'https://local.sonub.com/api.php'"
    echo "  $0 --count 3 --user apple --api-url 'https://local.sonub.com/api.php'"
    exit 0
}

# Parse arguments
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
        --api-url)
            API_URL="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            ;;
        *)
            echo "Unknown option: $1"
            echo "Run $0 --help for usage information"
            exit 1
            ;;
    esac
done

# Map test user to phone number
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
        echo "Error: Unknown test user: $TEST_USER"
        echo "Allowed users: apple, banana, cherry, durian, elderberry, fig, grape, honeydew, jackfruit, kiwi, lemon, mango"
        exit 1
        ;;
esac

# Validate post count
if ! [[ "$POST_COUNT" =~ ^[0-9]+$ ]] || [ "$POST_COUNT" -lt 1 ] || [ "$POST_COUNT" -gt 50 ]; then
    echo "Error: Post count must be between 1 and 50"
    exit 1
fi

echo "=========================================="
echo "Create Multiple Posts - Sonub API Script"
echo "=========================================="
echo ""
echo "Configuration:"
echo "  API URL: $API_URL"
echo "  Posts to create: $POST_COUNT"
echo "  Test user: $TEST_USER"
echo "  Phone: $TEST_PHONE"
echo ""

# Step 1: Login and get session cookie
echo "Step 1: Logging in with test account..."
echo ""

LOGIN_JSON='{
  "func": "login_with_firebase",
  "firebase_uid": "'"$TEST_USER"'",
  "phone_number": "'"$TEST_PHONE"'"
}'

COOKIE_JAR=$(mktemp)
LOGIN_RESPONSE=$(curl -s -k -c "$COOKIE_JAR" -X POST "$API_URL" \
    -H "Content-Type: application/json" \
    -d "$LOGIN_JSON")

# Check login response
if echo "$LOGIN_RESPONSE" | grep -q "error_code"; then
    echo "Error: Login failed"
    echo "$LOGIN_RESPONSE" | jq '.' 2>/dev/null || echo "$LOGIN_RESPONSE"
    rm -f "$COOKIE_JAR"
    exit 1
fi

USER_ID=$(echo "$LOGIN_RESPONSE" | jq -r '.id // empty' 2>/dev/null)
if [ -z "$USER_ID" ]; then
    echo "Error: Could not get user ID from login response"
    echo "$LOGIN_RESPONSE" | jq '.' 2>/dev/null || echo "$LOGIN_RESPONSE"
    rm -f "$COOKIE_JAR"
    exit 1
fi

USER_NAME=$(echo "$LOGIN_RESPONSE" | jq -r '.first_name // "Unknown"' 2>/dev/null)
echo "✓ Login successful!"
echo "  User ID: $USER_ID"
echo "  Name: $USER_NAME"
echo ""

# Step 2: Create posts
echo "Step 2: Creating posts..."
echo ""

SUCCESS_COUNT=0
FAIL_COUNT=0

for i in $(seq 1 "$POST_COUNT"); do
    # Random category
    CATEGORY_INDEX=$((RANDOM % ${#CATEGORIES[@]}))
    CATEGORY="${CATEGORIES[$CATEGORY_INDEX]}"

    # Random image count (0-7)
    IMAGE_COUNT=$((RANDOM % 8))

    # Generate image URLs
    IMAGE_URLS=""
    if [ "$IMAGE_COUNT" -gt 0 ]; then
        for j in $(seq 1 "$IMAGE_COUNT"); do
            IMAGE_ID=$((RANDOM % 1000))
            IMAGE_WIDTH=$((200 + RANDOM % 400))
            IMAGE_HEIGHT=$((200 + RANDOM % 400))
            IMAGE_URL="$IMAGE_BASE/${IMAGE_WIDTH}/${IMAGE_HEIGHT}?random=$IMAGE_ID"

            if [ -z "$IMAGE_URLS" ]; then
                IMAGE_URLS="$IMAGE_URL"
            else
                IMAGE_URLS="$IMAGE_URLS,$IMAGE_URL"
            fi
        done
    fi

    # Generate post content
    TITLE="Test Post #$i - $(date +'%Y-%m-%d %H:%M:%S')"
    CONTENT="This is an auto-generated test post.\\n\\nCategory: $CATEGORY\\nImages: $IMAGE_COUNT\\n\\nFor testing purposes only."

    # Create post JSON
    POST_JSON='{
      "func": "create_post",
      "title": "'"$(echo "$TITLE" | sed 's/"/\\"/g')"'",
      "content": "'"$(echo "$CONTENT" | sed 's/"/\\"/g')"'",
      "category": "'"$CATEGORY"'",
      "visibility": "public"'

    if [ -n "$IMAGE_URLS" ]; then
        POST_JSON="$POST_JSON"',
      "files": "'"$(echo "$IMAGE_URLS" | sed 's/"/\\"/g')"'"'
    fi

    POST_JSON="$POST_JSON"'
    }'

    # Call create_post API
    POST_RESPONSE=$(curl -s -k -b "$COOKIE_JAR" -X POST "$API_URL" \
        -H "Content-Type: application/json" \
        -d "$POST_JSON")

    # Check response
    POST_ID=$(echo "$POST_RESPONSE" | jq -r '.id // empty' 2>/dev/null)
    if [ -n "$POST_ID" ]; then
        SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
        echo "  ✓ Post #$i created (ID: $POST_ID, Category: $CATEGORY, Images: $IMAGE_COUNT)"
    else
        FAIL_COUNT=$((FAIL_COUNT + 1))
        ERROR_CODE=$(echo "$POST_RESPONSE" | jq -r '.error_code // "unknown"' 2>/dev/null)
        ERROR_MSG=$(echo "$POST_RESPONSE" | jq -r '.error_message // "Unknown error"' 2>/dev/null)
        echo "  ✗ Post #$i failed (Error: $ERROR_CODE - $ERROR_MSG)"
    fi
done

echo ""
echo "=========================================="
echo "Results"
echo "=========================================="
echo ""
echo "Total requests: $POST_COUNT"
echo "Successful: $SUCCESS_COUNT"
echo "Failed: $FAIL_COUNT"
echo ""

# Cleanup
rm -f "$COOKIE_JAR"

if [ "$FAIL_COUNT" -gt 0 ]; then
    exit 1
fi

echo "Done!"
