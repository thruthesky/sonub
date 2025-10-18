# Sonub 커뮤니티: 게시판·사용자·친구·피드·댓글 SQL 설계 설명서

## 목차
- [개요](#개요)
- [데이터베이스 테이블 구조](#데이터베이스-테이블-구조)
  - [Users (사용자)](#users-사용자)
  - [Posts (게시글)](#posts-게시글)
  - [Comments (댓글)](#comments-댓글)
  - [Friendships (친구 관계)](#friendships-친구-관계)
  - [Blocks (차단)](#blocks-차단)
  - [Feed Entries (피드 캐시)](#feed-entries-피드-캐시)
- [엔터티 간 연관 관계](#엔터티-간-연관-관계)
- [피드 전송(팬아웃) 흐름](#피드-전송팬아웃-흐름)
- [성능 최적화 전략](#성능-최적화-전략)
- [실전 쿼리 예제](#실전-쿼리-예제)

---

## 개요

이 문서는 Sonub 커뮤니티의 핵심 기능인 **게시판(Posts)**, **사용자(Users)**, **친구 관계(Friendships)**, **차단(Blocks)**, **댓글(Comments)**, **피드 캐시(Feed Entries)** 를 유기적으로 연결하여 고성능 소셜 피드를 제공하기 위한 데이터베이스 설계를 설명합니다.

**핵심 설계 원칙:**
- 모든 시각 컬럼은 **UNIX epoch (INT UNSIGNED)** 사용
- 친구 관계는 **무방향 1행** 모델 (중복 방지)
- 피드는 **Fan-out on write** 전략 (쓰기 시 전파, 읽기 시 고속)
- 외래 키는 **ON DELETE CASCADE** (데이터 무결성 자동 유지)

---

## 데이터베이스 테이블 구조

### Users (사용자)

```sql
CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `firebase_uid` varchar(128) NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `display_name` varchar(64) DEFAULT NULL,
  `birthday` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `gender` char(1) NOT NULL DEFAULT '',
  `photo_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `firebase_uid` (`firebase_uid`),
  UNIQUE KEY `display_name` (`display_name`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**컬럼 설명:**
- `id`: 내부 사용자 ID (AUTO_INCREMENT)
- `firebase_uid`: Firebase 인증 UID (외부 인증 시스템 연동)
- `display_name`: 사용자 표시 이름 (UNIQUE, 닉네임 중복 방지)
- `birthday`: 생년월일 (UNIX timestamp)
- `gender`: 성별 ('M', 'F', '')
- `photo_url`: 프로필 사진 URL

**인덱스:**
- `firebase_uid` UNIQUE: Firebase 인증 기반 로그인 고속화
- `display_name` UNIQUE: 닉네임 중복 체크 및 검색 최적화
- `created_at`: 가입 시간 기준 정렬 (최근 가입자 조회 등)

---

### Posts (게시글)

```sql
CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL DEFAULT '',
  `visibility` enum('public','friends','private') NOT NULL DEFAULT 'public',
  `files` text NOT NULL DEFAULT '',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category` (`category`),
  KEY `created_at` (`created_at`),
  KEY `ix_posts_user_created` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**컬럼 설명:**
- `id`: 게시글 ID (AUTO_INCREMENT)
- `user_id`: 작성자 ID (FK → users.id)
- `category`: 게시판 카테고리 (discussion, news, story, ai, drama 등)
- `title`: 게시글 제목 (빈 문자열 허용)
- `content`: 게시글 본문 (빈 문자열 허용)
- `visibility`: 공개 범위
  - `public`: 모두에게 공개
  - `friends`: 친구에게만 공개
  - `private`: 나만 보기
- `files`: 첨부 파일 URL (콤마로 구분된 문자열)
- `created_at`: 게시글 생성 시간 (UNIX timestamp)
- `updated_at`: 게시글 수정 시간 (UNIX timestamp)

**인덱스:**
- `user_id`: 특정 사용자의 게시글 조회
- `category`: 카테고리별 게시글 목록
- `created_at`: 최신순 정렬
- `ix_posts_user_created` (복합 인덱스): 특정 사용자의 게시글을 최신순으로 조회 (마이페이지 등)

**Visibility 규칙:**
- `public`: 모든 사용자에게 노출 + 피드 전파
- `friends`: 친구에게만 노출 + 친구 피드에만 전파
- `private`: 작성자 본인만 조회 가능 + 피드 전파 안 함

---

### Comments (댓글)

```sql
CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_estonian_ci NOT NULL DEFAULT '',
  `files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_estonian_ci NOT NULL DEFAULT '',
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**컬럼 설명:**
- `id`: 댓글 ID (AUTO_INCREMENT)
- `user_id`: 댓글 작성자 ID (FK → users.id)
- `post_id`: 원본 게시글 ID (FK → posts.id)
- `content`: 댓글 본문 (빈 문자열 허용)
- `files`: 첨부 파일 URL (콤마로 구분된 문자열)
- `created_at`: 댓글 생성 시간 (UNIX timestamp)
- `updated_at`: 댓글 수정 시간 (UNIX timestamp)

**인덱스:**
- `user_id`: 특정 사용자의 댓글 목록
- `post_id`: 특정 게시글의 댓글 목록 (댓글 조회 최적화)
- `created_at`: 최신순 정렬

**특징:**
- 게시글과 별도로 파일 첨부 가능
- 게시글 삭제 시 댓글 처리는 애플리케이션 레벨에서 정책 결정 (CASCADE vs RESTRICT)

---

### Friendships (친구 관계)

```sql
CREATE TABLE `friendships` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id_a` INT(10) UNSIGNED NOT NULL,
  `user_id_b` INT(10) UNSIGNED NOT NULL,
  `status` ENUM('pending','accepted','rejected','blocked') NOT NULL DEFAULT 'pending',
  `requested_by` INT(10) UNSIGNED NOT NULL,
  `created_at` INT(10) UNSIGNED NOT NULL,
  `updated_at` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_friend_pair` (`user_id_a`,`user_id_b`),
  KEY `ix_status` (`status`),
  KEY `ix_requested_by` (`requested_by`),
  CONSTRAINT `fk_friend_a` FOREIGN KEY (`user_id_a`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_friend_b` FOREIGN KEY (`user_id_b`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**모델링 의도:**

**1. 무방향 1행 모델**
- 한 쌍의 친구 관계를 **1행**으로 표현
- `(user_id_a, user_id_b)` 는 항상 **작은 ID를 a, 큰 ID를 b** 에 저장
- 대칭 중복을 원천 차단 (예: (1, 2)와 (2, 1)은 (1, 2) 하나로 통합)

**2. 제약 조건**
- `uq_friend_pair` UNIQUE KEY: 동일 쌍 중복 삽입 방지
- `FK ON DELETE CASCADE`: 사용자가 삭제되면 관련 친구 행도 자동 정리

**3. Status 상태**
- `pending`: 친구 요청 대기 중
- `accepted`: 친구 관계 성립
- `rejected`: 친구 요청 거절
- `blocked`: 차단 상태 (정책에 따라 사용 가능)

**4. requested_by 컬럼**
- 누가 친구 요청을 생성했는지 기록
- 알림 및 UX 개선에 활용 (예: "홍길동님이 친구 요청을 보냈습니다")

**예제: 친구 추가**
```php
// 사용자 ID 5가 사용자 ID 10에게 친구 요청
$user_a = min(5, 10); // 1
$user_b = max(5, 10); // 10

INSERT INTO friendships (user_id_a, user_id_b, status, requested_by, created_at, updated_at)
VALUES ($user_a, $user_b, 'pending', 5, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
```

---

### Blocks (차단)

```sql
CREATE TABLE `blocks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `blocker_id` INT(10) UNSIGNED NOT NULL,
  `blocked_id` INT(10) UNSIGNED NOT NULL,
  `created_at` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_block` (`blocker_id`,`blocked_id`),
  KEY `ix_blocker` (`blocker_id`),
  KEY `ix_blocked` (`blocked_id`),
  CONSTRAINT `fk_blocker` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_blocked` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**왜 Friendships와 분리하나요?**

1. **정책 분석 명확화**: 차단을 친구 상태와 별도로 관리하면 비즈니스 로직이 명확해집니다.
2. **중복 차단 방지**: `UNIQUE (blocker_id, blocked_id)` 로 동일한 차단 관계 중복 삽입 방지
3. **양방향 차단 체크 간편화**: 피드/댓글/DM 조회 시 `NOT EXISTS` 쿼리로 양방향 차단을 쉽게 배제

**차단 로직:**
- A가 B를 차단 → B의 게시글/댓글이 A에게 보이지 않음
- B가 A를 차단 → A의 게시글/댓글이 B에게 보이지 않음
- 양방향 차단 체크 필요

**예제: 차단 확인 쿼리**
```sql
-- 사용자 1이 사용자 5를 차단했는지 또는 사용자 5가 사용자 1을 차단했는지 확인
SELECT COUNT(*) > 0 AS is_blocked
FROM blocks
WHERE (blocker_id = 1 AND blocked_id = 5)
   OR (blocker_id = 5 AND blocked_id = 1);
```

---

### Feed Entries (피드 캐시)

```sql
CREATE TABLE `feed_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `receiver_id` INT(10) UNSIGNED NOT NULL,   -- 피드를 받아볼 사용자
  `post_id` INT(10) UNSIGNED NOT NULL,
  `post_author_id` INT(10) UNSIGNED NOT NULL,
  `created_at` INT(10) UNSIGNED NOT NULL,    -- posts.created_at 복사
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_receiver_post` (`receiver_id`,`post_id`),
  KEY `ix_receiver_created` (`receiver_id`,`created_at`),
  CONSTRAINT `fk_fe_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fe_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fe_author` FOREIGN KEY (`post_author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**역할과 성능 효과:**

**1. feed_entries가 하는 일**

글이 작성될 때 **한 번만**, 해당 작성자의 친구들에게 "피드 캐시"를 미리 만들어 둡니다.

**쓰기 시 (글 작성 시) - 피드 캐시 생성:**
```sql
INSERT IGNORE INTO feed_entries (receiver_id, post_id, post_author_id, created_at)
SELECT
  CASE WHEN user_id_a = :author THEN user_id_b ELSE user_id_a END AS receiver_id,
  :post_id,
  :author,
  :created_at
FROM friendships
WHERE (user_id_a = :author OR user_id_b = :author)
  AND status = 'accepted';
```

이렇게 하면 조회 시에는 단순히 아래 쿼리만 실행하면 됩니다 👇

**읽기 시 (피드 조회 시) - 단순 조회:**
```sql
SELECT fe.post_id, fe.post_author_id, p.title, p.content
FROM feed_entries fe
JOIN posts p ON p.id = fe.post_id
WHERE fe.receiver_id = :me
ORDER BY fe.created_at DESC
LIMIT 20;
```

**핵심 장점:**
- 피드를 구성할 때 **친구 목록 조인 없이** 바로 읽을 수 있습니다
- 복잡한 JOIN 연산이 제거되어 조회 속도가 극대화됩니다

**2. 쓰기 시 전파 (Fan-out on write) 캐시**
- 글 작성 시, 작성자의 모든 친구를 수신자로 하여 `feed_entries` 를 미리 만들어 둠
- 쓰기 부하는 증가하지만, 읽기 속도가 극대화됨

**3. 조회 시 고속 응답 (추가 설명)**
```sql
SELECT fe.post_id
FROM feed_entries fe
WHERE fe.receiver_id = :me
ORDER BY fe.created_at DESC
LIMIT :limit OFFSET :offset;
```
- 인덱스 `ix_receiver_created(receiver_id, created_at)` 로 수신자별 최신순 스캔이 O(1)에 가깝게 동작
- JOIN 없이 단일 테이블 스캔으로 피드 조회 완료

**4. 중복 방지**
- `UNIQUE (receiver_id, post_id)`: 동일한 게시글이 동일한 수신자에게 중복 전파되지 않음

**5. 자동 정리**
- `ON DELETE CASCADE`: 게시글 삭제 시 모든 피드 엔트리도 자동 삭제
- 사용자 삭제 시 해당 사용자의 모든 피드 엔트리도 자동 삭제

---

## 엔터티 간 연관 관계

### 1. Users ↔ Posts (1:N)
- 한 사용자는 여러 게시글을 작성할 수 있음
- FK: `posts.user_id` → `users.id`

### 2. Users ↔ Comments (1:N)
- 한 사용자는 여러 댓글을 작성할 수 있음
- FK: `comments.user_id` → `users.id`

### 3. Posts ↔ Comments (1:N)
- 한 게시글에는 여러 댓글이 달릴 수 있음
- FK: `comments.post_id` → `posts.id`

### 4. Users ↔ Friendships (N:⟷:N)
- 친구 한 쌍 당 1행 (무방향)
- 친구인지 여부는 `friendships.status='accepted'` 여부로 결정
- FK: `friendships.user_id_a` → `users.id`
- FK: `friendships.user_id_b` → `users.id`

### 5. Users ↔ Blocks (1:⟶:N)
- 차단자(블로커)는 여러 사용자를 차단할 수 있음
- FK: `blocks.blocker_id` → `users.id`
- FK: `blocks.blocked_id` → `users.id`

### 6. Feed Entries (수신자 중심 캐시)
- `receiver_id` (피드 주인) ↔ `post_id` (게시글) ↔ `post_author_id` (작성자)
- 전파 규칙: `posts.visibility IN ('public','friends')` 일 때만 전파
- `private` 글은 전파 금지

---

## 피드 전송(팬아웃) 흐름

### 1단계: 글 작성 (Posts insert)

```php
// 게시글 생성
$post = create_post([
    'category' => 'story',
    'title' => '오늘의 이야기',
    'content' => '친구들에게 공유하고 싶은 내용',
    'visibility' => 'friends' // 친구에게만 공개
]);
```

- `posts.visibility` 가 `public` 또는 `friends` 면 전파 가능
- `private` 글은 피드에 전파하지 않음

### 2단계: 친구 수신자 계산 (Friendships)

작성자의 친구 목록을 무방향 1행에서 조회:

```sql
SELECT CASE
    WHEN user_id_a = :author THEN user_id_b
    ELSE user_id_a
END AS receiver_id
FROM friendships
WHERE (user_id_a = :author OR user_id_b = :author)
  AND status = 'accepted';
```

**예제:**
- 사용자 ID 5가 글 작성
- 친구: 1, 3, 10, 15
- → receiver_id = [1, 3, 10, 15]

### 3단계: 피드 캐시 삽입 (Feed Entries)

```sql
INSERT IGNORE INTO feed_entries (receiver_id, post_id, post_author_id, created_at)
SELECT
  CASE WHEN user_id_a = :author THEN user_id_b ELSE user_id_a END AS receiver_id,
  :post_id,
  :author,
  :created_at
FROM friendships
WHERE (user_id_a = :author OR user_id_b = :author)
  AND status = 'accepted'
  AND NOT EXISTS (
    -- 양방향 차단 체크
    SELECT 1 FROM blocks
    WHERE (blocker_id = :author AND blocked_id = CASE WHEN user_id_a = :author THEN user_id_b ELSE user_id_a END)
       OR (blocker_id = CASE WHEN user_id_a = :author THEN user_id_b ELSE user_id_a END AND blocked_id = :author)
  );
```

**동작:**
1. 작성자의 친구 목록 조회
2. 차단된 사용자 제외
3. 각 친구의 피드에 게시글 전파

### 4단계: 피드 조회 (사용자별 타임라인)

```sql
-- 사용자 ID 10의 피드 조회 (최신 20개)
SELECT
  p.id,
  p.title,
  p.content,
  p.files,
  p.created_at,
  u.display_name AS author_name,
  u.photo_url AS author_photo
FROM feed_entries fe
INNER JOIN posts p ON fe.post_id = p.id
INNER JOIN users u ON p.user_id = u.id
WHERE fe.receiver_id = 10
ORDER BY fe.created_at DESC
LIMIT 20 OFFSET 0;
```

**성능:**
- `ix_receiver_created` 인덱스로 매우 빠른 조회
- JOIN은 최종 결과 표시를 위한 최소한의 JOIN만 수행
- 페이지네이션 지원 (LIMIT/OFFSET)

---

## 성능 최적화 전략

### 1. 인덱스 전략

**복합 인덱스 (Composite Index):**
- `ix_posts_user_created (user_id, created_at)`: 특정 사용자의 게시글을 최신순으로 조회
- `ix_receiver_created (receiver_id, created_at)`: 피드 조회 최적화

**커버링 인덱스 (Covering Index):**
- 인덱스만으로 쿼리 결과를 반환할 수 있도록 설계
- 예: 피드 조회 시 `feed_entries` 테이블의 `ix_receiver_created` 인덱스만으로 post_id 목록 추출

### 2. 쿼리 최적화

**좋은 예:**
```sql
-- 인덱스 사용 가능 (ix_receiver_created)
SELECT fe.post_id
FROM feed_entries fe
WHERE fe.receiver_id = 10
ORDER BY fe.created_at DESC
LIMIT 20;
```

**나쁜 예:**
```sql
-- 인덱스 사용 불가 (created_at에 함수 적용)
SELECT fe.post_id
FROM feed_entries fe
WHERE fe.receiver_id = 10
ORDER BY FROM_UNIXTIME(fe.created_at) DESC
LIMIT 20;
```

### 3. Fan-out on Write vs Fan-out on Read

**Fan-out on Write (현재 적용):**
- ✅ 읽기 속도 극대화 (피드 조회 시 단일 테이블 스캔)
- ✅ 사용자 경험 향상 (피드 로딩 시간 단축)
- ❌ 쓰기 부하 증가 (친구 많은 사용자의 글 작성 시)

**Fan-out on Read (대안):**
- ✅ 쓰기 속도 빠름 (글 작성 시 피드 전파 없음)
- ❌ 읽기 부하 증가 (피드 조회 시 JOIN 및 정렬 필요)

**선택 기준:**
- 읽기 빈도 >> 쓰기 빈도 → Fan-out on Write (현재)
- 쓰기 빈도 >> 읽기 빈도 → Fan-out on Read

### 4. 데이터 정리 (Cleanup)

**자동 정리 (ON DELETE CASCADE):**
- 사용자 삭제 → 모든 게시글, 댓글, 친구 관계, 피드 엔트리 자동 삭제
- 게시글 삭제 → 모든 피드 엔트리 자동 삭제

**주기적 정리 (Cron Job):**
- 오래된 피드 엔트리 삭제 (예: 6개월 이상 된 피드)
- 거절된 친구 요청 정리 (status='rejected' 및 1개월 경과)

---

## 실전 쿼리 예제

### 1. 친구 목록 조회

```sql
-- 사용자 ID 5의 친구 목록 (수락된 친구만)
SELECT
  CASE
    WHEN user_id_a = 5 THEN user_id_b
    ELSE user_id_a
  END AS friend_id,
  u.display_name,
  u.photo_url,
  f.created_at AS friend_since
FROM friendships f
INNER JOIN users u ON u.id = CASE
  WHEN f.user_id_a = 5 THEN f.user_id_b
  ELSE f.user_id_a
END
WHERE (f.user_id_a = 5 OR f.user_id_b = 5)
  AND f.status = 'accepted'
ORDER BY f.created_at DESC;
```

### 2. 친구 요청 목록 (받은 요청)

```sql
-- 사용자 ID 10이 받은 친구 요청 목록
SELECT
  f.id AS friendship_id,
  CASE
    WHEN f.user_id_a = 10 THEN f.user_id_b
    ELSE f.user_id_a
  END AS requester_id,
  u.display_name AS requester_name,
  u.photo_url AS requester_photo,
  f.created_at AS requested_at
FROM friendships f
INNER JOIN users u ON u.id = f.requested_by
WHERE (f.user_id_a = 10 OR f.user_id_b = 10)
  AND f.status = 'pending'
  AND f.requested_by != 10
ORDER BY f.created_at DESC;
```

### 3. 특정 게시글의 댓글 목록

```sql
-- 게시글 ID 100의 댓글 목록 (최신순)
SELECT
  c.id,
  c.content,
  c.files,
  c.created_at,
  u.display_name AS author_name,
  u.photo_url AS author_photo
FROM comments c
INNER JOIN users u ON c.user_id = u.id
WHERE c.post_id = 100
  AND NOT EXISTS (
    -- 차단된 사용자의 댓글 제외 (로그인 사용자 ID: :me)
    SELECT 1 FROM blocks
    WHERE (blocker_id = :me AND blocked_id = c.user_id)
       OR (blocker_id = c.user_id AND blocked_id = :me)
  )
ORDER BY c.created_at ASC
LIMIT 50;
```

### 4. 카테고리별 게시글 목록 (공개 글만)

```sql
-- 'story' 카테고리의 공개 게시글 목록 (최신 20개)
SELECT
  p.id,
  p.title,
  p.content,
  p.files,
  p.created_at,
  u.display_name AS author_name,
  u.photo_url AS author_photo,
  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count
FROM posts p
INNER JOIN users u ON p.user_id = u.id
WHERE p.category = 'story'
  AND p.visibility = 'public'
  AND NOT EXISTS (
    -- 차단된 사용자의 게시글 제외 (로그인 사용자 ID: :me)
    SELECT 1 FROM blocks
    WHERE (blocker_id = :me AND blocked_id = p.user_id)
       OR (blocker_id = p.user_id AND blocked_id = :me)
  )
ORDER BY p.created_at DESC
LIMIT 20;
```

### 5. 사용자 프로필 통계

```sql
-- 사용자 ID 5의 통계 정보
SELECT
  (SELECT COUNT(*) FROM posts WHERE user_id = 5) AS post_count,
  (SELECT COUNT(*) FROM comments WHERE user_id = 5) AS comment_count,
  (SELECT COUNT(*) FROM friendships WHERE (user_id_a = 5 OR user_id_b = 5) AND status = 'accepted') AS friend_count
FROM DUAL;
```

### 6. 피드 조회 (차단 사용자 제외)

```sql
-- 사용자 ID 10의 피드 (차단된 사용자의 글 제외)
SELECT
  p.id,
  p.title,
  p.content,
  p.files,
  p.created_at,
  u.display_name AS author_name,
  u.photo_url AS author_photo
FROM feed_entries fe
INNER JOIN posts p ON fe.post_id = p.id
INNER JOIN users u ON p.user_id = u.id
WHERE fe.receiver_id = 10
  AND NOT EXISTS (
    -- 양방향 차단 체크
    SELECT 1 FROM blocks
    WHERE (blocker_id = 10 AND blocked_id = p.user_id)
       OR (blocker_id = p.user_id AND blocked_id = 10)
  )
ORDER BY fe.created_at DESC
LIMIT 20 OFFSET 0;
```

---

## 요약

**Sonub 커뮤니티 데이터베이스 설계의 핵심:**

1. **무방향 친구 관계**: 중복 없는 효율적 설계
2. **Fan-out on Write 피드**: 읽기 속도 극대화
3. **차단 시스템 분리**: 명확한 정책 관리
4. **UNIX Timestamp**: 시간대 독립적 설계
5. **인덱스 최적화**: 복합 인덱스로 쿼리 성능 향상
6. **ON DELETE CASCADE**: 자동 데이터 정리

이 설계는 소셜 네트워크의 핵심 기능을 고성능으로 제공하면서도 데이터 무결성을 보장합니다.
