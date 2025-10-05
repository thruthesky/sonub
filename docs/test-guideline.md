# 테스트 문서

## 목차
- [개요](#개요)
- [테스트 파일 구조](#테스트-파일-구조)
- [필수 부트스트랩](#필수-부트스트랩)
- [테스트 작성하기](#테스트-작성하기)
- [테스트 실행하기](#테스트-실행하기)
- [테스트 예제](#테스트-예제)

## 개요

이 문서는 SONUB 프로젝트의 테스트 가이드라인과 규칙을 설명합니다. 모든 테스트는 프로젝트의 표준 워크플로우를 따라 외부 테스트 프레임워크 없이 순수 PHP로 작성됩니다.

## 테스트 파일 구조

- 테스트 파일은 `tests/` 디렉토리에 저장됩니다
- 테스트 파일 이름은 반드시 `.test.php`로 끝나야 합니다
- 디렉토리 구조는 소스 코드 구조를 반영해야 합니다
- 각 테스트 파일은 독립적으로 실행 가능해야 합니다

예제:
```
tests/
├── db/
│   └── db.test.php          # lib/db/db.php를 위한 테스트
├── db.connection.test.php   # 데이터베이스 연결 테스트
└── user/
    └── user.test.php        # 사용자 기능 테스트
```

## 필수 부트스트랩

**중요:** 모든 테스트 파일은 모든 의존성과 함수를 사용할 수 있도록 다음 순서로 부트스트랩 파일을 로드해야 합니다:

```php
<?php
// 부트스트랩 함수를 먼저 로드
require_once __DIR__ . '/../../etc/boot/boot.functions.php';

// 필요한 모든 includes 로드
require_once etc_folder('includes');
```

이 로딩 순서는 다음을 보장합니다:
1. 핵심 유틸리티 함수를 사용할 수 있음 (`etc_folder()`, `is_dev_computer()` 등)
2. 필요한 모든 설정 파일이 로드됨
3. 데이터베이스 연결 및 기타 의존성이 올바르게 초기화됨

## 테스트 작성하기

### 기본 테스트 구조

```php
<?php
// 필수 부트스트랩
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== 테스트 이름 ===\n\n";

try {
    // 테스트 1
    echo "테스트 1: 설명...\n";
    // 테스트 로직
    echo "✅ 테스트 1 통과\n\n";

    // 테스트 2
    echo "테스트 2: 설명...\n";
    // 테스트 로직
    echo "✅ 테스트 2 통과\n\n";

    echo "=============================\n";
    echo "✅ 모든 테스트가 성공적으로 통과했습니다!\n";
    echo "=============================\n";

} catch (Exception $e) {
    echo "\n❌ 테스트 실패: " . $e->getMessage() . "\n";
    exit(1);
}
```

### 단언(Assertion) 예제

외부 테스트 프레임워크를 사용하지 않으므로 간단한 단언을 사용합니다:

```php
// 간단한 단언
if ($result !== $expected) {
    throw new Exception("예상값 $expected 이지만 실제값은 $result 입니다");
}

// 불리언 단언
if (!$condition) {
    throw new Exception("조건 실패: 실패한 내용에 대한 설명");
}

// 배열/객체 비교
if ($array1 != $array2) {
    throw new Exception("배열이 일치하지 않습니다");
}
```

## 테스트 실행하기

테스트는 PHP CLI를 사용하여 직접 실행할 수 있습니다:

```bash
# 특정 테스트 실행
php tests/db/db.test.php

# 데이터베이스 연결 테스트 실행
php tests/db.connection.test.php

# 디렉토리의 모든 테스트 실행 (쉘 스크립트 사용)
for test in tests/**/*.test.php; do
    echo "Running $test..."
    php "$test"
done
```

## 테스트 예제

### 데이터베이스 연결 테스트

```php
<?php
// 필수 부트스트랩
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== 데이터베이스 연결 테스트 ===\n\n";

try {
    $connection = db_connection();

    if ($connection instanceof PDO) {
        echo "✅ 데이터베이스에 성공적으로 연결되었습니다\n";
    } else {
        throw new Exception("연결이 유효한 PDO 인스턴스가 아닙니다");
    }

} catch (PDOException $e) {
    echo "\n❌ 데이터베이스 연결 오류: " . $e->getMessage() . "\n";
    exit(1);
}
```

### 쿼리 빌더 테스트

```php
<?php
// 필수 부트스트랩
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== 쿼리 빌더 테스트 ===\n\n";

try {
    // INSERT 테스트
    $id = db()->insert(['name' => 'Test'])->into('users');
    echo "✅ 삽입 성공, ID: $id\n";

    // SELECT 테스트
    $result = db()->select('*')->from('users')->where('id = ?', [$id])->first();
    if ($result['name'] === 'Test') {
        echo "✅ 조회 성공\n";
    }

    // DELETE 테스트
    $affected = db()->delete()->from('users')->where('id = ?', [$id])->execute();
    echo "✅ 삭제 성공, 영향받은 행: $affected\n";

} catch (Exception $e) {
    echo "\n❌ 쿼리 빌더 테스트 실패: " . $e->getMessage() . "\n";
    exit(1);
}
```

### 단위 테스트 예제

```php
<?php
// 필수 부트스트랩
require_once __DIR__ . '/../../etc/boot/boot.functions.php';
require_once etc_folder('includes');

echo "=== 사용자 유효성 검사 테스트 ===\n\n";

try {
    // 이메일 유효성 검사 테스트
    echo "테스트 1: 이메일 유효성 검사...\n";

    $validEmail = "user@example.com";
    $invalidEmail = "invalid-email";

    if (filter_var($validEmail, FILTER_VALIDATE_EMAIL)) {
        echo "✅ 유효한 이메일이 허용되었습니다\n";
    } else {
        throw new Exception("유효한 이메일이 거부되었습니다");
    }

    if (!filter_var($invalidEmail, FILTER_VALIDATE_EMAIL)) {
        echo "✅ 유효하지 않은 이메일이 거부되었습니다\n";
    } else {
        throw new Exception("유효하지 않은 이메일이 허용되었습니다");
    }

    echo "\n✅ 모든 유효성 검사 테스트가 통과했습니다!\n";

} catch (Exception $e) {
    echo "\n❌ 유효성 검사 테스트 실패: " . $e->getMessage() . "\n";
    exit(1);
}
```

## 모범 사례

1. **항상 부트스트랩 순서를 사용하세요** - `boot.functions.php`와 `includes` 로딩을 건너뛰지 마세요
2. **테스트를 독립적으로 유지하세요** - 각 테스트 파일은 단독으로 실행 가능해야 합니다
3. **명확한 출력을 사용하세요** - 이모지(✅ ❌)와 서식을 사용하여 결과를 읽기 쉽게 만드세요
4. **오류를 우아하게 처리하세요** - try-catch 블록을 사용하고 도움이 되는 오류 메시지를 제공하세요
5. **적절한 종료 코드로 종료하세요** - 실패 시 `exit(1)` 사용, 성공 시 정상 종료
6. **성공과 실패 케이스를 모두 테스트하세요** - 테스트가 엣지 케이스를 다루는지 확인하세요
7. **테스트 후 정리하세요** - 테스트가 데이터를 생성하면 완료 후 정리하세요
