# PHP 테스트 가이드

## 목차
- [테스트 파일 저장 위치 - 최강력 규칙](#테스트-파일-저장-위치---최강력-규칙)
- [테스트 실행 방법](#테스트-실행-방법)
- [데이터베이스 설정 자동 선택](#데이터베이스-설정-자동-선택)
- [테스트 작성 규칙](#테스트-작성-규칙)

## 테스트 파일 저장 위치 - 최강력 규칙

**🔥🔥🔥 최강력 규칙: 모든 테스트 파일, 임시 파일, 검증 파일은 반드시 `./tests` 폴더 아래에 저장해야 합니다 🔥🔥🔥**

### 필수 준수 사항

- **✅ 필수**: 모든 테스트 파일은 `tests` 디렉토리에 저장
- **✅ 필수**: 모든 임시 검증 파일은 `tests` 디렉토리에 저장
- **✅ 필수**: 테스트 관련 모든 파일은 `tests` 디렉토리에 저장
- **❌ 절대 금지**: 프로젝트 루트에 테스트 파일 생성 금지
- **❌ 절대 금지**: `lib`, `src` 등 소스 코드 폴더에 테스트 파일 생성 금지
- **❌ 절대 금지**: 임시 파일을 루트나 소스 폴더에 생성 금지

### 올바른 저장 위치 예시

```
✅ tests/db/db.connection.test.php       # DB 연결 테스트
✅ tests/user/user.crud.test.php         # 사용자 CRUD 테스트
✅ tests/api/api.test.php                # API 테스트
✅ tests/temp/verify_function.php        # 임시 검증 파일
✅ tests/temp/test_query.php             # 임시 쿼리 테스트
```

### 잘못된 저장 위치 예시

```
❌ db.test.php                           # 루트 폴더 (절대 금지!)
❌ test.php                              # 루트 폴더 (절대 금지!)
❌ temp.php                              # 루트 폴더 (절대 금지!)
❌ lib/db/db.test.php                    # 소스 코드 폴더 (절대 금지!)
❌ verify.php                            # 루트 폴더 (절대 금지!)
```

### 위반 시 발생하는 문제

- 프로젝트 구조 오염
- 운영 코드와 테스트 코드 혼재
- Git 관리 어려움
- 배포 시 불필요한 파일 포함
- 팀 협업 시 혼란 발생

## 테스트 코드 작성 시 필수 사항

**모든 테스트 파일 맨 위에 반드시 `include '../init.php'`를 추가하세요!**

- `init.php`를 포함하면 모든 라이브러리, 함수, DB 설정 등을 즉시 사용 가능
- `init.php`는 ROOT_DIR 정의 및 `etc/includes.php` 로드를 자동으로 처리
- 상대 경로는 테스트 파일의 위치에 따라 조정 (예: `tests/user/` → `../../init.php`)

### 테스트 파일 기본 템플릿

```php
<?php
// tests/user/example.test.php
include '../../init.php';

// 이제 모든 함수와 클래스를 바로 사용 가능
// db(), get_user(), error() 등 모든 함수 사용 가능
```

## 테스트 실행 방법

프로젝트 루트 폴더에서 아래와 같이 실행합니다:

```bash
php tests/db/db.connection.test.php
```

## 데이터베이스 설정 자동 선택

개발자 컴퓨터에서 테스트 실행 시 데이터베이스 설정이 자동으로 선택됩니다:

### DB 접속 흐름

1. **개발자 컴퓨터에서 실행 시**:
   - `is_dev_computer()` 함수가 `true` 반환
   - `etc/includes.php`에서 `etc/config/db.dev.config.php` 로드
   - 개발용 DB 설정 사용 (호스트: `sonub-mariadb` 또는 `127.0.0.1`)

2. **운영 서버에서 실행 시**:
   - `is_dev_computer()` 함수가 `false` 반환
   - `etc/includes.php`에서 `etc/config/db.config.php` 로드
   - 운영용 DB 설정 사용

### 설정 파일 구조

```
etc/
├── includes.php                    # 모든 파일 로드 (DB 설정 자동 선택)
└── config/
    ├── db.dev.config.php          # 개발용 DB 설정
    └── db.config.php              # 운영용 DB 설정
```

### includes.php의 DB 설정 로드 코드

```php
// 데이터베이스 설정 로드
if (is_dev_computer()) {
    include_once ROOT_DIR . '/etc/config/db.dev.config.php';
} else {
    include_once ROOT_DIR . '/etc/config/db.config.php';
}
```

## 테스트 작성 규칙

1. **외부 프레임워크 없이 순수 PHP로 작성**
   - PHPUnit, Pest 등 외부 테스트 프레임워크 사용 안 함
   - 간단한 assert 함수로 테스트 검증

2. **테스트 파일 위치 및 명명 규칙**
   - **반드시 `tests` 디렉토리에 저장** (최강력 규칙!)
   - 소스 코드와 동일한 구조로 저장
   - 파일 이름은 `.test.php`로 끝나야 함
   - 예시: `lib/db/db.php` → `tests/db/db.test.php`

3. **독립 실행 가능**
   - 각 테스트 파일은 PHP 명령어로 단독 실행 가능해야 함
   - `php tests/db/db.test.php` 형태로 실행

4. **명확한 출력 메시지**
   - 테스트 성공/실패 시 명확한 메시지 출력
   - 실패 시 원인 파악이 쉽도록 상세 정보 제공

### 테스트 코드 예제

```php
<?php
// tests/db/db.connection.test.php

// 필수: init.php 포함 (모든 라이브러리와 설정 로드)
include __DIR__ . '/../../init.php';

// 테스트: DB 연결 확인
try {
    $conn = db_connection();
    echo "✅ DB 연결 성공\n";
    echo "   호스트: " . DB_HOST . "\n";
    echo "   데이터베이스: " . DB_NAME . "\n";
} catch (Exception $e) {
    echo "❌ DB 연결 실패: " . $e->getMessage() . "\n";
    exit(1);
}

// 테스트: 간단한 쿼리 실행
try {
    $result = db()->select('1 as test')->get();
    if ($result && $result[0]['test'] === '1') {
        echo "✅ 쿼리 실행 성공\n";
    } else {
        echo "❌ 쿼리 결과 불일치\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ 쿼리 실행 실패: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 모든 테스트 통과!\n";
```
