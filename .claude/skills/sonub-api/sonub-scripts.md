# Sonub API Scripts - 명령줄 도구 상세 가이드

Sonub API를 명령줄에서 간편하게 테스트하고 호출할 수 있는 Bash 스크립트에 대한 상세 문서입니다.

모든 스크립트는 `.claude/skills/sonub-api/scripts/` 디렉토리에 위치합니다.

---

## 목차

- [사전 요구사항](#사전-요구사항)
- [환경 변수 설정](#환경-변수-설정)
- [list_users.sh - 사용자 목록 조회](#list_userssh---사용자-목록-조회)
- [create_posts.sh - 게시글 생성](#create_postssh---게시글-생성)
- [글 생성 시 필수 지침](#글-생성-시-필수-지침)
- [create_post() API 함수 상세 가이드](#create_post-api-함수-상세-가이드)
- [list_posts.sh - 게시글 목록 조회](#list_postssh---게시글-목록-조회)
- [스크립트 개발 및 확장](#스크립트-개발-및-확장)

---

## 사전 요구사항

### 필수 도구

- **curl**: HTTP 요청을 보내기 위해 필수 (일반적으로 이미 설치됨)
- **jq**: JSON 응답을 보기 좋게 포맷팅 (선택사항이지만 강력히 권장)

### jq 설치

```bash
# macOS
brew install jq

# Ubuntu/Debian
sudo apt-get install jq

# CentOS/RHEL
sudo yum install jq
```

---

## 환경 변수 설정

모든 스크립트는 `API_URL` 환경 변수를 지원합니다.

### 기본 설정

```bash
# 기본값 (설정하지 않으면 https://local.sonub.com/api.php 사용)
export API_URL="https://sonub.com/api.php"

# 또는 각 명령에서 --url 옵션 사용
./list_users.sh --url https://sonub.com/api.php
```

### 환경 변수 영구 설정

```bash
# ~/.bashrc 또는 ~/.zshrc에 추가
export API_URL="https://sonub.com/api.php"

# 설정 적용
source ~/.bashrc
```

---

## list_users.sh - 사용자 목록 조회

### 위치

`.claude/skills/sonub-api/scripts/list_users.sh`

### 설명

`list_users()` API 함수를 호출하여 사용자 목록을 조회합니다. 페이지네이션, 성별 필터, 나이 필터, 정렬 등 다양한 옵션을 지원합니다.

### 사용법

```bash
# 기본 사용 (10명의 사용자 조회)
./list_users.sh

# 20명의 사용자 조회
./list_users.sh --limit 20

# 2페이지 조회
./list_users.sh --limit 10 --page 2

# 여성 사용자만 조회
./list_users.sh --gender F --limit 10

# 남성 사용자만 조회
./list_users.sh --gender M --limit 15

# 20세에서 30세 사이의 사용자 조회
./list_users.sh --age-min 20 --age-max 30

# 정렬 순서 지정
./list_users.sh --order "name ASC" --limit 20

# 프로덕션 서버에서 조회
./list_users.sh --url https://sonub.com/api.php --limit 5

# 도움말 표시
./list_users.sh --help
```

### 옵션

| 옵션 | 설명 | 기본값 |
|------|------|--------|
| `--limit N` | 페이지당 사용자 수 (최대: 100) | 10 |
| `--page N` | 페이지 번호 | 1 |
| `--gender [M\|F]` | 성별 필터 (M: 남성, F: 여성) | (없음) |
| `--age-min N` | 최소 나이 | (없음) |
| `--age-max N` | 최대 나이 | (없음) |
| `--order STR` | 정렬 순서 | "created_at DESC" |
| `--url URL` | API URL | https://local.sonub.com/api.php |
| `-h, --help` | 도움말 표시 | - |

### 실용 예제

```bash
# 여성 사용자 20-25세 조회
./list_users.sh --gender F --age-min 20 --age-max 25 --limit 20

# 최신 가입자 50명 조회
./list_users.sh --order "created_at DESC" --limit 50

# 3페이지의 남성 사용자 조회
./list_users.sh --gender M --page 3 --limit 10

# 모든 여성 사용자 조회 (페이지 크기 최대)
./list_users.sh --gender F --limit 100

# 나이순 정렬로 사용자 조회
./list_users.sh --order "birthday DESC" --limit 30
```

---

## create_posts.sh - 게시글 생성

### 위치

`.claude/skills/sonub-api/scripts/create_posts.sh`

### 설명

`create_post()` API 함수를 호출하여 새로운 게시글을 생성합니다. 테스트 계정으로 자동 로그인하고, 한 번에 여러 개의 게시글을 생성할 수 있습니다.

### 주요 기능

- ✅ 12개 테스트 계정 지원 (apple, banana, cherry, durian, elderberry, fig, grape, honeydew, jackfruit, kiwi, lemon, mango)
- ✅ 25개 이상의 카테고리 지원 (커뮤니티, 장터, 뉴스, 부동산, 구인구직)
- ✅ 자동 로그인 및 세션 쿠키 관리
- ✅ 이미지 자동 첨부 (DummyImage.com)
- ✅ 배치 처리 지원 (한 번에 최대 50개까지 생성)
- ✅ bash 기본 명령어만 사용 (외부 패키지에 의존하지 않음)

### 사용법

```bash
# 도움말 표시 (모든 옵션 및 사용 가능한 카테고리 확인)
./create_posts.sh --help

# 기본 사용 (banana 계정으로 3개 게시글 생성, 랜덤 카테고리)
./create_posts.sh

# 프로덕션 서버에서 5개 게시글 생성 (discussion 카테고리)
./create_posts.sh --count 5 --category discussion --api-url https://sonub.com/api.php

# apple 계정으로 10개 게시글 생성 (qna 카테고리)
./create_posts.sh --count 10 --user apple --category qna

# 로컬 환경에서 cherry 계정으로 discussion 카테고리 3개 게시글 생성
./create_posts.sh --user cherry --category discussion --api-url https://local.sonub.com/api.php
```

### 옵션

| 옵션 | 설명 | 기본값 | 예제 |
|------|------|--------|------|
| `--count N` | 생성할 게시글 수 (범위: 1-50) | 3 | `--count 10` |
| `--user NAME` | 테스트 계정명 | banana | `--user apple` |
| `--category CAT` | 카테고리 지정 (옵션) | 랜덤 | `--category discussion` |
| `--api-url URL` | API URL | https://sonub.com/api.php | `--api-url https://local.sonub.com/api.php` |
| `-h, --help` | 도움말 및 모든 카테고리 목록 표시 | - | `--help` |

### 사용 가능한 카테고리

`--help` 명령으로 모든 사용 가능한 카테고리를 확인할 수 있습니다.

#### 커뮤니티 (community)
- `discussion` (자유토론)
- `qna` (질문과답변)
- `story` (나의 이야기)
- `relationships` (관계)
- `fitness` (운동)
- `beauty` (뷰티)
- `cooking` (요리)
- `pets` (반려동물)
- `parenting` (육아)

#### 장터 (buyandsell)
- `electronics` (전자제품)
- `fashion` (패션)
- `furniture` (가구)
- `books` (책)
- `sports-equipment` (스포츠용품)
- `vehicles` (차량)
- `real-estate` (부동산)

#### 뉴스 (news)
- `technology` (기술)
- `business` (비즈니스)
- `ai` (인공지능)
- `movies` (영화)
- `drama` (드라마)
- `music` (음악)

#### 부동산 (realestate)
- `buy` (구매)
- `sell` (판매)
- `rent` (임대)

#### 구인구직 (jobs)
- `full-time` (전일제)
- `part-time` (시간제)
- `freelance` (프리랜서)

### 테스트 계정 매핑

| 계정명 | 전화번호 | 로그인 |
|--------|---------|--------|
| apple | +11234567890 | apple@test.com:12345a,* |
| banana | +11234567891 | banana@test.com:12345a,* |
| cherry | +11234567892 | cherry@test.com:12345a,* |
| durian | +11234567893 | durian@test.com:12345a,* |
| elderberry | +11234567894 | elderberry@test.com:12345a,* |
| fig | +11234567895 | fig@test.com:12345a,* |
| grape | +11234567896 | grape@test.com:12345a,* |
| honeydew | +11234567897 | honeydew@test.com:12345a,* |
| jackfruit | +11234567898 | jackfruit@test.com:12345a,* |
| kiwi | +11234567899 | kiwi@test.com:12345a,* |
| lemon | +11234567900 | lemon@test.com:12345a,* |
| mango | +11234567901 | mango@test.com:12345a,* |

### 실행 예제

```bash
# 프로덕션 서버에 5개 게시글 생성
./create_posts.sh --count 5 --api-url https://sonub.com/api.php

# 로컬 환경에서 기본 설정으로 3개 게시글 생성
./create_posts.sh --api-url https://local.sonub.com/api.php

# apple 계정으로 프로덕션 서버에 20개 게시글 생성
./create_posts.sh --count 20 --user apple --api-url https://sonub.com/api.php

# discussion 카테고리로 최대 50개 생성
./create_posts.sh --count 50 --category discussion
```

---

## 글 생성 시 필수 지침

### 🔥 중요 규칙

Sonub에 글(게시글)을 생성할 때는 **반드시 이미 만들어져 있는 `create_posts.sh` 스크립트를 사용**해야 합니다.

### ❌ 절대 금지 사항

- **새로운 코드 작성 금지**: 별도의 bash 스크립트나 새로운 코드를 작성하지 마세요
- **코드 복사 금지**: 기존 create_posts.sh를 복사하여 새로운 파일을 만들지 마세요
- **직접 API 호출 금지**: curl이나 fetch를 직접 작성하여 API를 호출하지 마세요

### ✅ 올바른 방법

**반드시 `create_posts.sh`의 옵션 파라미터를 활용하여 실행하세요:**

```bash
# 기본 형식
./create_posts.sh [옵션]

# 옵션 종류
--count N              # 생성할 게시글 수 지정
--user NAME            # 테스트 계정 선택
--api-url URL          # API 서버 선택 (로컬/프로덕션)
--category CAT         # 카테고리 선택
```

### 올바른 예제

```bash
# ✅ 올바른 예: create_posts.sh의 옵션을 활용하여 실행
./create_posts.sh --count 5 --user banana --api-url https://sonub.com/api.php

# ✅ 올바른 예: 프로덕션 서버에서 10개 게시글 생성
./create_posts.sh --count 10 --api-url https://sonub.com/api.php

# ✅ 올바른 예: discussion 카테고리로 특정 계정 사용
./create_posts.sh --count 20 --user apple --category discussion

# ❌ 잘못된 예: 새로운 bash 스크립트 작성 (금지!)
# bash create_new_posts.sh   <- 절대 금지!

# ❌ 잘못된 예: 직접 curl 호출 (금지!)
# curl -X POST https://sonub.com/api.php ...   <- 절대 금지!
```

**기억하세요: `create_posts.sh` 스크립트는 이미 완벽하게 구현되어 있습니다. 옵션 파라미터만 조정하여 필요한 대로 사용하세요!** ✨

---

## create_post() API 함수 상세 가이드

### 함수명

`create_post`

### 설명

새로운 게시글을 생성합니다. 로그인된 사용자만 사용 가능하며, 자동으로 현재 사용자를 게시글 작성자로 설정합니다.

### 필수 파라미터

| 파라미터 | 타입 | 설명 |
|---------|------|------|
| `func` | string | API 함수명 (`create_post` 고정) |
| `title` | string | 게시글 제목 (필수) |
| `content` | string | 게시글 내용 (필수) |
| `category` | string | 카테고리 (필수): `discussion`, `qna`, `my-wall` |
| `visibility` | string | 공개 범위 (필수): `public`, `friends`, `private` |

### JavaScript를 통한 호출 예제

```javascript
// 1. Firebase 로그인
const user = await firebase.auth().signInWithEmailAndPassword(
  'banana@test.com',
  '12345a'
);

// 2. 게시글 생성 (로그인 상태)
const result = await func('create_post', {
  title: '새로운 토론 주제',
  content: '이것은 새로운 게시글입니다.\\n\\n내용을 자유롭게 작성할 수 있습니다.',
  category: 'discussion',
  visibility: 'public'
});

console.log('게시글 생성 완료:', result.id);
```

### 일반적인 에러 코드

| 에러 코드 | 설명 | 해결 방법 |
|---------|------|---------|
| `input-title-empty` | 제목이 비어있음 | title 파라미터 확인 |
| `input-content-empty` | 내용이 비어있음 | content 파라미터 확인 |
| `input-category-empty` | 카테고리가 지정되지 않음 | category 파라미터 지정 |
| `input-visibility-empty` | 공개 범위가 지정되지 않음 | visibility 파라미터 지정 |
| `category-not-found` | 유효하지 않은 카테고리 | 유효한 카테고리 사용 |
| `not-logged-in` | 로그인하지 않음 | 먼저 login_with_firebase로 로그인 |

---

## list_posts.sh - 게시글 목록 조회

### 위치

`.claude/skills/sonub-api/scripts/list_posts.sh`

### 설명

`list_posts()` API 함수를 호출하여 게시글 목록을 조회합니다. 카테고리, 사용자, 공개 범위 필터를 지원합니다.

### 사용법

```bash
# 기본 사용 (최신 게시글 10개 조회)
./list_posts.sh

# 'discussion' 카테고리 게시글 20개 조회
./list_posts.sh --category discussion --limit 20

# 특정 사용자(ID: 5)의 게시글 조회
./list_posts.sh --user-id 5 --limit 10

# 공개(public) 게시글만 조회
./list_posts.sh --visibility public --limit 15

# 2페이지 조회
./list_posts.sh --category qna --page 2 --limit 10

# 정렬 순서 지정 (오래된 순)
./list_posts.sh --order "created_at ASC" --limit 20

# 도움말 표시
./list_posts.sh --help
```

### 옵션

| 옵션 | 설명 | 기본값 |
|------|------|--------|
| `--category STR` | 카테고리 필터 (예: discussion, qna, my-wall) | (없음) |
| `--user-id N` | 특정 사용자의 게시글만 조회 | (없음) |
| `--visibility STR` | 공개 범위 필터 (public, friends, private) | (없음) |
| `--limit N` | 페이지당 게시글 수 (최대: 100) | 10 |
| `--page N` | 페이지 번호 | 1 |
| `--order STR` | 정렬 순서 | "created_at DESC" |
| `--url URL` | API URL | https://local.sonub.com/api.php |
| `-h, --help` | 도움말 표시 | - |

### 실용 예제

```bash
# 'discussion' 카테고리의 최신 게시글 30개
./list_posts.sh --category discussion --limit 30

# 사용자 ID 10의 모든 게시글
./list_posts.sh --user-id 10 --limit 50

# 'qna' 카테고리의 공개 게시글만
./list_posts.sh --category qna --visibility public --limit 20

# 'my-wall' 카테고리의 2페이지
./list_posts.sh --category my-wall --page 2

# 오래된 순으로 게시글 조회
./list_posts.sh --category discussion --order "created_at ASC"

# 최신 게시글 100개 (최대값)
./list_posts.sh --limit 100
```

---

## 스크립트 개발 및 확장

### 새 스크립트 추가하기

1. `.claude/skills/sonub-api/scripts/` 디렉토리에 새 `.sh` 파일 생성
2. 기존 스크립트(`list_users.sh` 또는 `list_posts.sh`)를 템플릿으로 사용
3. API 함수에 맞게 옵션 및 파라미터 수정
4. 실행 권한 부여: `chmod +x script_name.sh`
5. [SKILL.md](SKILL.md)에 사용법 문서 추가

### cURL 옵션 설명

| 옵션 | 설명 |
|------|------|
| `-X POST` | HTTP POST 메서드 사용 |
| `-H "Content-Type: application/json"` | JSON 콘텐츠 타입 헤더 |
| `-d "$JSON_PAYLOAD"` | JSON 페이로드 전송 |
| `-s` | Silent 모드 (진행 상황 숨기기) |
| `-k` | SSL 인증서 검증 무시 (로컬 개발 환경용) |
| `-b "$COOKIE_JAR"` | 쿠키 파일 사용 (세션 유지) |
| `-c "$COOKIE_JAR"` | 쿠키 저장 |

### jq 사용법

```bash
# JSON 응답 포맷팅
curl ... | jq '.'

# 특정 필드만 추출
curl ... | jq '.users[].name'

# 조건부 필터링
curl ... | jq '.users[] | select(.gender == "F")'

# 배열 길이 확인
curl ... | jq '.users | length'

# 특정 필드로 정렬
curl ... | jq '.users | sort_by(.name)'
```

---

**참고**: 모든 스크립트는 UTF-8 인코딩으로 저장되어 있습니다.
