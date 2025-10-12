# 데이터베이스 접근 문서

## 목차
- [개요](#개요)
- [🔥🔥🔥 최우선 권장사항 - PDO 직접 사용](#최우선-권장사항---pdo-직접-사용)
- [설치 및 구성](#설치-및-구성)
- [PDO 기본 사용법 (최우선)](#pdo-기본-사용법-최우선)
  - [PDO INSERT 작업](#pdo-insert-작업)
  - [PDO SELECT 작업](#pdo-select-작업)
  - [PDO UPDATE 작업](#pdo-update-작업)
  - [PDO DELETE 작업](#pdo-delete-작업)
  - [PDO 트랜잭션](#pdo-트랜잭션)
- [쿼리 빌더 사용법 (차선택)](#쿼리-빌더-사용법-차선택)
  - [INSERT 작업](#insert-작업)
  - [SELECT 작업](#select-작업)
  - [UPDATE 작업](#update-작업)
  - [DELETE 작업](#delete-작업)
  - [원시 쿼리](#원시-쿼리)
- [고급 기능](#고급-기능)
  - [WHERE 조건](#where-조건)
  - [JOIN 작업](#join-작업)
  - [ORDER BY](#order-by)
  - [LIMIT 및 페이지네이션](#limit-및-페이지네이션)
  - [트랜잭션](#트랜잭션)
- [엔티티 시스템](#엔티티-시스템)
  - [엔티티 기본 클래스](#엔티티-기본-클래스)
  - [엔티티 클래스 생성](#엔티티-클래스-생성)
  - [엔티티 CRUD 작업](#엔티티-crud-작업)
  - [사용자 엔티티](#사용자-엔티티)
  - [게시물 엔티티](#게시물-엔티티)
- [메서드 참조](#메서드-참조)
- [예제](#예제)

## 개요

Sonub는 데이터베이스 접근을 위해 **PDO (PHP Data Objects) 직접 사용을 최우선으로 권장**합니다. PDO는 PHP의 표준 데이터베이스 접근 방식이며, prepared statement를 통해 SQL 인젝션으로부터 보호합니다.

**🔥🔥🔥 중요: 가능한 모든 경우에 PDO를 직접 사용하세요 🔥🔥🔥**

## 🔥🔥🔥 최우선 권장사항 - PDO 직접 사용

**✅ 필수: 모든 데이터베이스 작업은 `pdo()` 함수를 통해 PDO 객체를 얻어서 수행해야 합니다**

**❌ 차선택: `db()` 쿼리 빌더는 특별한 경우에만 사용하고, 가능한 사용하지 마세요**

### PDO 사용의 장점

1. **표준화**: PHP 표준 데이터베이스 접근 방식
2. **성능**: 불필요한 추상화 레이어 없이 직접 실행
3. **유연성**: 모든 SQL 기능에 완전한 접근
4. **명확성**: SQL 쿼리가 명확하게 보임
5. **디버깅**: 쿼리 문제를 빠르게 파악 가능
6. **호환성**: 다른 프로젝트나 프레임워크와 호환

### pdo() 함수 사용법

```php
// PDO 객체 가져오기
$pdo = pdo();

// 이제 PDO의 모든 메서드를 직접 사용할 수 있습니다
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([123]);
$user = $stmt->fetch();
```

## 설치 및 구성

데이터베이스 구성은 `/lib/db/db.php`에 위치합니다. 연결 매개변수는 다음과 같습니다:

```php
$host = 'sonub-mariadb';
$db = 'sonub';
$user = 'sonub';
$pass = 'asdf';
$charset = 'utf8mb4';
```

PDO 객체를 가져오려면 `pdo()` 함수를 호출하기만 하면 됩니다:

```php
require_once '/lib/db/db.php';

// pdo() 함수는 싱글톤 PDO 인스턴스를 반환합니다
$pdo = pdo();
```

## PDO 기본 사용법 (최우선)

**🔥🔥🔥 최강력 규칙: 모든 데이터베이스 작업은 PDO를 직접 사용해야 합니다 🔥🔥🔥**

### PDO INSERT 작업

새 레코드를 삽입하고 삽입된 ID를 가져옵니다:

```php
// ✅ 올바른 방법: PDO 직접 사용
$pdo = pdo();

// 단순 삽입 - 마지막 삽입 ID를 반환합니다
$stmt = $pdo->prepare("INSERT INTO users (display_name, email, created_at) VALUES (?, ?, ?)");
$stmt->execute(['jaeho', 'jaeho@example.com', time()]);
$userId = $pdo->lastInsertId();

echo "새 사용자 ID: {$userId}";

// 여러 필드로 삽입
$stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, category, created_at) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([
    123,
    '게시글 제목',
    '게시글 내용',
    'discussion',
    time()
]);
$postId = $pdo->lastInsertId();
```

### PDO SELECT 작업

#### 여러 레코드 검색

```php
$pdo = pdo();

// 모든 사용자 조회
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

// 조건부 조회
$stmt = $pdo->prepare("SELECT * FROM users WHERE id > ? LIMIT 5");
$stmt->execute([2]);
$users = $stmt->fetchAll();

// 플레이스홀더를 사용한 prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = ? AND created_at > ?");
$stmt->execute(['active', strtotime('-30 days')]);
$users = $stmt->fetchAll();
```

#### 단일 레코드 검색

```php
$pdo = pdo();

// 첫 번째 일치하는 레코드 가져오기
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([123]);
$user = $stmt->fetch();

if ($user) {
    echo "사용자 이름: {$user['display_name']}";
} else {
    echo "사용자를 찾을 수 없습니다";
}

// 이메일로 사용자 찾기
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute(['hong@example.com']);
$user = $stmt->fetch();
```

#### 레코드 수 세기

```php
$pdo = pdo();

// 전체 레코드 수
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$count = $stmt->fetchColumn();

// 조건부 수 세기
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE status = ?");
$stmt->execute(['active']);
$activeCount = $stmt->fetchColumn();
```

### PDO UPDATE 작업

```php
$pdo = pdo();

// 레코드 업데이트 - 영향받은 행 수를 반환합니다
$stmt = $pdo->prepare("UPDATE users SET display_name = ?, updated_at = ? WHERE id = ?");
$stmt->execute(['송재호', time(), 123]);
$affectedRows = $stmt->rowCount();

echo "{$affectedRows}개 행이 업데이트되었습니다";

// 여러 필드 업데이트
$stmt = $pdo->prepare("UPDATE users SET display_name = ?, email = ?, updated_at = ? WHERE id = ?");
$stmt->execute(['Updated Name', 'new@example.com', time(), 5]);
$affectedRows = $stmt->rowCount();
```

### PDO DELETE 작업

```php
$pdo = pdo();

// 레코드 삭제 - 삭제된 행 수를 반환합니다
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([123]);
$deletedRows = $stmt->rowCount();

echo "{$deletedRows}개 행이 삭제되었습니다";

// 여러 조건으로 삭제
$stmt = $pdo->prepare("DELETE FROM users WHERE status = ? AND created_at < ?");
$stmt->execute(['inactive', strtotime('-1 year')]);
$deletedRows = $stmt->rowCount();
```

### PDO 트랜잭션

```php
$pdo = pdo();

try {
    $pdo->beginTransaction();

    // 사용자 삽입
    $stmt = $pdo->prepare("INSERT INTO users (display_name, email, created_at) VALUES (?, ?, ?)");
    $stmt->execute(['John Doe', 'john@example.com', time()]);
    $userId = $pdo->lastInsertId();

    // 프로필 삽입
    $stmt = $pdo->prepare("INSERT INTO profiles (user_id, bio) VALUES (?, ?)");
    $stmt->execute([$userId, 'Software Developer']);

    // 통계 업데이트
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    $stmt = $pdo->prepare("UPDATE stats SET user_count = ? WHERE id = 1");
    $stmt->execute([$userCount]);

    $pdo->commit();
    echo "트랜잭션이 성공적으로 완료되었습니다";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "트랜잭션 실패: " . $e->getMessage();
}
```

## 쿼리 빌더 사용법 (차선택)

**⚠️ 경고: 쿼리 빌더(`db()` 클래스)는 특별한 경우에만 사용하고, 가능한 사용하지 마세요**

쿼리 빌더는 다음과 같은 **제한적인 경우에만** 사용하세요:
- 매우 복잡한 동적 쿼리 생성이 필요한 경우
- 여러 조건이 동적으로 추가되어야 하는 검색 기능
- 레거시 코드 유지보수

**✅ 일반적인 경우: PDO를 직접 사용하세요**

```php
require_once '/lib/db/db.php';

// ❌ 차선택: 쿼리 빌더 사용
$result = db()->select('*')->from('users')->get();

// ✅ 최우선: PDO 직접 사용
$pdo = pdo();
$stmt = $pdo->query("SELECT * FROM users");
$result = $stmt->fetchAll();
```

## 기본 사용법

### INSERT 작업

새 레코드를 삽입하고 삽입된 ID를 가져옵니다:

```php
// 단순 삽입 - 마지막 삽입 ID를 반환합니다
$id = db()->insert(['display_name' => 'jaeho', 'email' => 'jaeho@example.com'])
    ->into('users');

// 여러 필드로 삽입
$id = db()->insert([
    'display_name' => 'John Doe',
    'email' => 'john@example.com',
    'created_at' => date('Y-m-d H:i:s')
])->into('users');
```

### SELECT 작업

#### 여러 레코드 검색

```php
// 모든 컬럼 선택
$users = db()->select('*')->from('users')->get();

// 특정 컬럼 선택
$users = db()->select('id, display_name, email')->from('users')->get();

// WHERE 조건 사용
$users = db()->select('*')
    ->from('users')
    ->where('id > 2')
    ->limit(5)
    ->get();

// 플레이스홀더를 사용한 prepared statement 사용
$users = db()->select('*')
    ->from('users')
    ->where('status = ? AND created_at > ?', ['active', '2024-01-01'])
    ->get();
```

#### 단일 레코드 검색

```php
// 첫 번째 일치하는 레코드 가져오기
$user = db()->select('*')
    ->from('users')
    ->where('id = ?', [1])
    ->first();

// 레코드가 없으면 null 반환
$user = db()->select('*')
    ->from('users')
    ->where('email = ?', ['nonexistent@example.com'])
    ->first();
```

#### 레코드 수 세기

```php
// 레코드 수 가져오기
$count = db()->select()->from('users')->count();

// 조건이 있는 수 세기
$activeCount = db()->select()
    ->from('users')
    ->where('status = ?', ['active'])
    ->count();
```

### UPDATE 작업

```php
// 레코드 업데이트 - 영향을 받은 행 수를 반환합니다
$affected = db()->update(['display_name' => 'song'])
    ->table('users')
    ->where("display_name = 'jaeho'")
    ->execute();

// 여러 필드로 업데이트
$affected = db()->update([
    'display_name' => 'Updated Name',
    'updated_at' => date('Y-m-d H:i:s')
])
->table('users')
->where('id = ?', [5])
->execute();

// WHERE 없이 업데이트 (모든 레코드 업데이트 - 주의해서 사용!)
$affected = db()->update(['status' => 'inactive'])
    ->table('users')
    ->execute();
```

### DELETE 작업

```php
// 레코드 삭제 - 삭제된 행 수를 반환합니다
$deleted = db()->delete()
    ->from('users')
    ->where('id = ?', [5])
    ->execute();

// 여러 조건으로 삭제
$deleted = db()->delete()
    ->from('users')
    ->where('status = ? AND created_at < ?', ['inactive', '2023-01-01'])
    ->execute();

// 모든 레코드 삭제 (주의해서 사용!)
$deleted = db()->delete()->from('temp_table')->execute();
```

### 원시 쿼리

빌더 패턴에 맞지 않는 복잡한 쿼리의 경우:

```php
// SELECT 쿼리
$results = db()->query("SELECT * FROM users WHERE id = ?", [1]);

// INSERT 쿼리 - 마지막 삽입 ID를 반환합니다
$id = db()->query(
    "INSERT INTO users (display_name, email) VALUES (?, ?)",
    ['John', 'john@example.com']
);

// UPDATE 쿼리 - 영향을 받은 행을 반환합니다
$affected = db()->query(
    "UPDATE users SET display_name = ? WHERE id = ?",
    ['New Name', 5]
);

// DELETE 쿼리 - 삭제된 행을 반환합니다
$deleted = db()->query("DELETE FROM users WHERE id = ?", [10]);

// JOIN이 있는 복잡한 쿼리
$results = db()->query("
    SELECT u.*, COUNT(p.id) as post_count
    FROM users u
    LEFT JOIN posts p ON u.id = p.user_id
    GROUP BY u.id
    HAVING post_count > ?
", [5]);
```

## 고급 기능

### WHERE 조건

```php
// 단순 WHERE
$users = db()->select('*')
    ->from('users')
    ->where('age > 18')
    ->get();

// 여러 AND 조건
$users = db()->select('*')
    ->from('users')
    ->where('age > ?', [18])
    ->where('status = ?', ['active'])
    ->get();

// OR 조건
$users = db()->select('*')
    ->from('users')
    ->where('role = ?', ['admin'])
    ->orWhere('role = ?', ['moderator'])
    ->get();

// 복잡한 조건
$users = db()->select('*')
    ->from('users')
    ->where('(age > ? AND status = ?)', [18, 'active'])
    ->orWhere('role = ?', ['admin'])
    ->get();
```

### JOIN 작업

```php
// INNER JOIN
$results = db()->select('users.*, posts.title')
    ->from('users')
    ->join('posts', 'users.id = posts.user_id')
    ->get();

// LEFT JOIN
$results = db()->select('users.*, COUNT(posts.id) as post_count')
    ->from('users')
    ->join('posts', 'users.id = posts.user_id', 'LEFT')
    ->get();

// 여러 JOIN
$results = db()->select('u.display_name, p.title, c.comment')
    ->from('users u')
    ->join('posts p', 'u.id = p.user_id')
    ->join('comments c', 'p.id = c.post_id', 'LEFT')
    ->where('u.status = ?', ['active'])
    ->get();
```

### ORDER BY

```php
// 단일 컬럼 정렬
$users = db()->select('*')
    ->from('users')
    ->orderBy('created_at', 'DESC')
    ->get();

// 여러 컬럼 정렬
$users = db()->select('*')
    ->from('users')
    ->orderBy('status', 'ASC')
    ->orderBy('created_at', 'DESC')
    ->get();

// 다른 절과 함께 정렬
$users = db()->select('*')
    ->from('users')
    ->where('status = ?', ['active'])
    ->orderBy('display_name', 'ASC')
    ->limit(10)
    ->get();
```

### LIMIT 및 페이지네이션

```php
// 단순 limit
$users = db()->select('*')
    ->from('users')
    ->limit(10)
    ->get();

// 페이지네이션을 위한 offset이 있는 limit
$page = 2;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$users = db()->select('*')
    ->from('users')
    ->limit($perPage, $offset)
    ->get();

// 페이지네이션을 위한 전체 수 가져오기
$total = db()->select()->from('users')->count();
$totalPages = ceil($total / $perPage);
```

### 트랜잭션

```php
try {
    db()->beginTransaction();

    // 사용자 삽입
    $userId = db()->insert([
        'display_name' => 'John Doe',
        'email' => 'john@example.com'
    ])->into('users');

    // 관련 프로필 삽입
    db()->insert([
        'user_id' => $userId,
        'bio' => 'Software Developer'
    ])->into('profiles');

    // 통계 업데이트
    db()->update(['user_count' => db()->query("SELECT COUNT(*) FROM users")[0]['COUNT(*)']])
        ->table('stats')
        ->where('id = 1')
        ->execute();

    db()->commit();
    echo "트랜잭션이 성공적으로 완료되었습니다";

} catch (Exception $e) {
    db()->rollback();
    echo "트랜잭션 실패: " . $e->getMessage();
}
```

## 엔티티 시스템

엔티티 시스템은 데이터베이스 쿼리 빌더 위에 객체 지향 레이어를 제공하여 데이터베이스 레코드를 객체로 작업할 수 있게 합니다. 각 데이터베이스 테이블은 기본 Entity 클래스를 확장하는 Entity 클래스로 표현됩니다.

### 엔티티 기본 클래스

`Entity` 클래스 (`/lib/db/entity.php`)는 모든 엔티티 클래스의 기반을 제공합니다:

#### 주요 기능
- `id`, `created_at`, `updated_at` 필드의 자동 처리
- **중요**: 모든 타임스탬프 필드 (`created_at`, `updated_at` 등)는 Unix 타임스탬프(정수 값)를 사용합니다
- CRUD 작업: `get()`, `create()`, `update()`, `delete()`
- 매직 getter/setter가 있는 `$data` 배열에 데이터 저장
- 레코드 쿼리를 위한 find 메서드
- 내장 유효성 검사 및 비즈니스 로직

#### 기본 엔티티 메서드

```php
// ID로 엔티티 가져오기
$entity = EntityClass::get($id);

// 새 엔티티 생성
$entity = EntityClass::create(['field' => 'value']);

// 엔티티 업데이트
$entity->update(['field' => 'new value']);

// 엔티티 삭제
$entity->delete();

// 엔티티 찾기
$entities = EntityClass::find(['status' => 'active']);
$entity = EntityClass::findFirst(['email' => 'user@example.com']);

// 모든 엔티티 가져오기
$all = EntityClass::all();

// 엔티티 수 세기
$count = EntityClass::count(['status' => 'active']);

// 엔티티 존재 여부 확인
$exists = EntityClass::exists($id);
```

### 엔티티 클래스 생성

새 엔티티 클래스를 생성하려면 기본 `Entity` 클래스를 확장하고 테이블 이름을 정의하세요:

```php
class Product extends Entity {
    protected static $table = 'products';

    // 엔티티에 특정한 사용자 정의 메서드 추가
    public function getPrice() {
        return $this->getValue('price');
    }

    public function isInStock() {
        return $this->getValue('stock_quantity', 0) > 0;
    }
}
```

### 엔티티 CRUD 작업

#### Create (생성)
```php
// 유효성 검사와 함께 생성
$user = User::create([
    'firebase_uid' => 'firebase_uid_abc123',
    'phone_number' => '+1234567890',
    'display_name' => 'John Doe'
]);

// 생성된 엔티티 데이터에 접근
echo $user->getValue('id');           // 자동 생성된 ID
echo $user->getValue('created_at');   // Unix 타임스탬프 (예: 1735030800)
```

#### Read (읽기)
```php
// ID로 가져오기
$user = User::get(1);

// find 메서드
$user = User::findByEmail('john@example.com');
$users = User::find(['status' => 'active'], 10); // 최대 10개
$first = User::findFirst(['role' => 'admin']);

// 매직 getter
echo $user->display_name;  // getValue('display_name')와 동일
echo $user->email;
```

#### Update (업데이트)
```php
// 엔티티 업데이트
$user->update(['display_name' => 'Jane Doe']);

// 매직 setter 사용
$user->display_name = 'New Name';
$user->save();

// 대량 업데이트
$user->setData([
    'display_name' => 'Updated Name',
    'email' => 'new@example.com'
]);
$user->save();
```

#### Delete (삭제)
```php
// 인스턴스 삭제
$user->delete();

// ID로 정적 삭제
User::destroy($id);
```

### 사용자 엔티티

`User` 클래스 (`/lib/db/user.php`)는 사용자별 기능으로 Entity를 확장합니다:

```php
// Firebase 전화 인증으로 사용자 생성
$user = User::create([
    'firebase_uid' => 'firebase_uid_123',
    'phone_number' => '+1234567890',
    'display_name' => 'John Doe' // 선택 사항, 제공되지 않으면 마스킹된 전화번호 사용
]);

// 사용자 찾기
$user = User::findByFirebaseUid('firebase_uid_123');
$user = User::findByPhoneNumber('+1234567890');
$user = User::findByDisplayName('John Doe');

// 사용자 생성 또는 가져오기 (Firebase 인증 흐름에 유용)
$user = User::createOrGetByFirebaseUid(
    'firebase_uid_123',
    '+1234567890',
    ['display_name' => 'John Doe'] // 선택적 추가 데이터
);

// 상태 관리
if ($user->isActive()) {
    $user->deactivate();
}
$user->activate();

// 사용자의 게시물 가져오기
$posts = $user->getPosts(10); // 최대 10개

// 활성/비활성 사용자 가져오기
$activeUsers = User::getActiveUsers();
$inactiveUsers = User::getInactiveUsers();
```

### 게시물 엔티티

`Post` 클래스 (`/lib/db/post.php`)는 게시물/기사 기능으로 Entity를 확장합니다:

```php
// 게시물 생성
$post = Post::create([
    'user_id' => 1,
    'title' => 'My First Post',
    'content' => 'Post content here...',
    'status' => 'draft' // 선택 사항, 기본값은 'draft'
]);

// 게시물 찾기
$post = Post::findBySlug('my-first-post'); // 자동 생성된 slug
$posts = Post::getByUser($userId);
$published = Post::getPublished(10); // 최대 10개
$drafts = Post::getDrafts();

// 게시물 관리
$post->publish();  // 상태를 'published'로 설정
$post->unpublish(); // 상태를 'draft'로 설정

// 조회수 추적
$post->incrementViewCount();
echo $post->getViewCount();

// 작성자 가져오기
$author = $post->getAuthor(); // User 엔티티 반환

// 콘텐츠 헬퍼
echo $post->getTitle();
echo $post->getExcerpt(150); // 첫 150자

// 게시물 검색
$results = Post::search('keyword', 20); // 제한과 함께 검색

// 최근 게시물 가져오기
$recent = Post::getRecent(5); // 가장 최근 게시된 게시물 5개
```

### 엔티티 매직 메서드

모든 엔티티는 편리한 속성 접근을 위한 매직 메서드를 지원합니다:

```php
$user = User::get(1);

// 매직 getter
echo $user->display_name;     // getValue('display_name')와 동일

// 매직 setter
$user->display_name = 'New Name'; // setValue('display_name', 'New Name')와 동일

// 매직 isset
if (isset($user->email)) {
    echo "이메일이 설정되었습니다";
}

// 매직 unset
unset($user->temporary_field);
```

### 엔티티 유틸리티

```php
// 배열로 변환
$array = $user->toArray();

// JSON으로 변환
$json = $user->toJson();

// 데이터베이스에서 새로 고침
$user->refresh();

// 모든 데이터 가져오기
$data = $user->getData();

// 존재 여부 확인
if (User::exists($id)) {
    echo "사용자가 존재합니다";
}

// 레코드 수 세기
$total = User::count();
$active = User::count(['status' => 'active']);
```

### 엔티티 시스템 테스트

엔티티 시스템 테스트 실행:

```bash
php tests/db/entity.test.php
```

테스트 스위트는 다음을 다룹니다:
- 엔티티 CRUD 작업
- 사용자 엔티티 기능
- 게시물 엔티티 기능
- 매직 메서드
- 엔티티 간 관계
- 유효성 검사 및 오류 처리

## 메서드 참조

### 쿼리 빌딩 메서드

| 메서드 | 설명 | 반환값 |
|--------|-------------|---------|
| `select($fields)` | SELECT 쿼리 시작 | `$this` |
| `insert($data)` | INSERT 쿼리 시작 | `$this` |
| `update($data)` | UPDATE 쿼리 시작 | `$this` |
| `delete()` | DELETE 쿼리 시작 | `$this` |
| `table($table)` | 테이블 이름 설정 | `$this` |
| `from($table)` | `table()`의 별칭 | `$this` |
| `into($table)` | 테이블 설정 및 INSERT 실행 | `int` (삽입 ID) 또는 `$this` |
| `where($condition, $params)` | WHERE 조건 추가 | `$this` |
| `orWhere($condition, $params)` | OR WHERE 조건 추가 | `$this` |
| `join($table, $on, $type)` | JOIN 절 추가 | `$this` |
| `orderBy($field, $direction)` | ORDER BY 추가 | `$this` |
| `limit($limit, $offset)` | LIMIT 절 추가 | `$this` |

### 실행 메서드

| 메서드 | 설명 | 반환값 |
|--------|-------------|---------|
| `get()` | SELECT 실행 및 모든 결과 반환 | `array` |
| `first()` | SELECT 실행 및 첫 번째 결과 반환 | `array\|null` |
| `count()` | SELECT 실행 및 수 반환 | `int` |
| `execute()` | 빌드된 쿼리 실행 | `mixed` |
| `query($sql, $params)` | 원시 SQL 쿼리 실행 | `mixed` |

### 트랜잭션 메서드

| 메서드 | 설명 | 반환값 |
|--------|-------------|---------|
| `beginTransaction()` | 트랜잭션 시작 | `void` |
| `commit()` | 트랜잭션 커밋 | `void` |
| `rollback()` | 트랜잭션 롤백 | `void` |

## 예제

### 사용자 등록 예제

```php
// 이메일이 존재하는지 확인
$existing = db()->select('id')
    ->from('users')
    ->where('email = ?', [$email])
    ->first();

if ($existing) {
    throw new Exception('이메일이 이미 등록되었습니다');
}

// 새 사용자 삽입
$userId = db()->insert([
    'display_name' => $name,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'created_at' => date('Y-m-d H:i:s')
])->into('users');

// 사용자 프로필 생성
db()->insert([
    'user_id' => $userId,
    'avatar' => '/default-avatar.png'
])->into('profiles');
```

### 댓글이 있는 블로그 게시물 예제

```php
// 작성자 및 댓글 수와 함께 게시물 가져오기
$post = db()->query("
    SELECT
        p.*,
        u.display_name as author_name,
        COUNT(c.id) as comment_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN comments c ON p.id = c.post_id
    WHERE p.id = ? AND p.status = 'published'
    GROUP BY p.id
", [$postId])[0];

// 게시물의 댓글 가져오기
$comments = db()->select('c.*, u.display_name')
    ->from('comments c')
    ->join('users u', 'c.user_id = u.id')
    ->where('c.post_id = ?', [$postId])
    ->orderBy('c.created_at', 'DESC')
    ->limit(10)
    ->get();
```

### 페이지네이션이 있는 검색 예제

```php
function searchUsers($keyword, $page = 1, $perPage = 20) {
    $offset = ($page - 1) * $perPage;

    // 전체 수 가져오기
    $total = db()->select()
        ->from('users')
        ->where('display_name LIKE ? OR email LIKE ?', ["%$keyword%", "%$keyword%"])
        ->count();

    // 페이지네이션된 결과 가져오기
    $users = db()->select('*')
        ->from('users')
        ->where('display_name LIKE ? OR email LIKE ?', ["%$keyword%", "%$keyword%"])
        ->orderBy('display_name', 'ASC')
        ->limit($perPage, $offset)
        ->get();

    return [
        'users' => $users,
        'total' => $total,
        'pages' => ceil($total / $perPage),
        'current_page' => $page
    ];
}
```

## 모범 사례

1. **항상 prepared statement 사용** - SQL 인젝션을 방지하기 위해 사용자 입력에 플레이스홀더(?)를 사용하세요
2. **관련 작업에는 트랜잭션 사용** - 함께 성공하거나 실패해야 하는 작업
3. **SELECT 필드 제한** - `*` 대신 필요한 컬럼만 선택하세요
4. **인덱스 추가** - WHERE, ORDER BY 및 JOIN 절에 사용되는 컬럼에
5. **단일 결과를 예상할 때는 `first()` 사용** - `get()[0]` 대신
6. **반환 값 확인** - INSERT는 ID를 반환하고, UPDATE/DELETE는 영향을 받은 행을 반환합니다
7. **연결 닫기** - 장기 실행 스크립트에서 데이터베이스 작업이 완료되면 `db_close()`를 호출하세요
