# 소너브 홈페이지 다국어 지원 개발 지침

이 문서는 소너브 홈페이지의 다국어 지원(번역) 기능을 개발하는 방법에 대해 설명합니다. 이 지침을 따르면, 새로운 언어를 추가하거나 기존 번역을 수정하는 작업이 쉬워집니다.

## 목차

- [개요](#개요)
- [지원 언어](#지원-언어)
- [Standard Workflow](#standard-workflow)
- [번역 규칙](#번역-규칙)
- [t()->inject() 사용법](#t-inject-사용법)
- [tr() 인라인 번역 사용법](#tr-인라인-번역-사용법)
- [번역 텍스트 출력 방법](#번역-텍스트-출력-방법)
- [완전한 예시](#완전한-예시)
- [자주 하는 실수](#자주-하는-실수)

## 개요

소너브 홈페이지는 다국어 지원 기능을 통해 전 세계 사용자에게 서비스를 제공합니다. 이 문서는 개발자가 다국어 번역 기능을 일관되고 올바르게 구현할 수 있도록 가이드를 제공합니다.

## 지원 언어

소너브 홈페이지는 **반드시** 다음 **4개 언어**를 지원해야 합니다:

| 순서 | 언어 | 언어 코드 | 필수 여부 |
|------|------|-----------|-----------|
| 1 | 한국어 | ko | ✅ 필수 |
| 2 | 영어 | en | ✅ 필수 |
| 3 | 일본어 | ja | ✅ 필수 |
| 4 | 중국어 | zh | ✅ 필수 |

**🔥🔥🔥 최강력 규칙: 모든 번역은 반드시 위 4개 언어를 순서대로 포함해야 합니다 🔥🔥🔥**

### 언어 선택 방식

- 웹 브라우저의 언어 설정에 따라 자동으로 언어가 선택됩니다
- 사용자는 페이지 하단의 언어 선택 메뉴를 통해 수동으로 언어를 변경할 수 있습니다
- 지원하지 않는 언어의 경우 기본적으로 영어가 적용됩니다
- 모든 언어는 UTF-8 인코딩을 사용합니다

## Standard Workflow

모든 다국어 번역 작업은 다음 워크플로우를 **반드시** 따라야 합니다:

1. **번역 함수 정의**: 각 PHP 파일 하단에 `inject_[php_file_name]_language()` 로컬 함수를 정의
2. **번역 텍스트 주입**: 함수 내부에서 `t()->inject(...)` 함수를 통해 4개 언어의 번역 텍스트를 주입
3. **함수 호출**: PHP 파일 맨 위에서 번역 함수를 호출하여 번역 텍스트를 등록
4. **번역 출력**: `<?= t()->키이름 ?>` 형식으로 번역 텍스트를 출력

**⚠️ 위반 금지**: 이 워크플로우를 따르지 않는 번역 구현은 절대 금지됩니다.

## 번역 규칙

### 필수 규칙

1. **4개 언어 필수**: 모든 번역은 반드시 한국어(ko), 영어(en), 일본어(ja), 중국어(zh) 4개 언어를 포함해야 합니다
2. **순서 준수**: 언어 코드는 반드시 ko → en → ja → zh 순서로 작성해야 합니다
3. **키는 한글**: 번역 키는 **반드시 한글**이어야 합니다 (예: `'이름'`, `'나이'`, `'프로필 업데이트'`)
4. **UTF-8 인코딩**: 모든 파일은 반드시 UTF-8 인코딩으로 저장해야 합니다

### 올바른 예제

```php
// ✅ 올바른 방법: 4개 언어 순서대로, 키는 한글
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
    '나이' => ['ko' => '나이', 'en' => 'Age', 'ja' => '年齢', 'zh' => '年龄'],
]);
```

### 잘못된 예제

```php
// ❌ 잘못된 방법: 언어가 부족함 (4개 언어 필수)
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name'],
]);

// ❌ 잘못된 방법: 키가 영어 (키는 반드시 한글)
t()->inject([
    'name' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
]);

// ❌ 잘못된 방법: 순서가 틀림 (ko → en → ja → zh 순서 필수)
t()->inject([
    '이름' => ['en' => 'Name', 'ko' => '이름', 'zh' => '姓名', 'ja' => '名前'],
]);
```

## t()->inject() 사용법

`t()->inject()` 함수는 번역 텍스트를 시스템에 주입하는 주요 방법입니다.

### 기본 구조

```php
t()->inject([
    '한글키1' => ['ko' => '한국어', 'en' => 'English', 'ja' => '日本語', 'zh' => '中文'],
    '한글키2' => ['ko' => '한국어', 'en' => 'English', 'ja' => '日本語', 'zh' => '中文'],
    // ...
]);
```

### 사용 예제

```php
<?php
// 파일 맨 위에서 번역 함수 호출
inject_profile_language();
?>

<h1><?= t()->프로필_업데이트 ?></h1>
<form>
    <label><?= t()->이름 ?></label>
    <input type="text" name="name">

    <label><?= t()->이메일 ?></label>
    <input type="email" name="email">

    <button><?= t()->저장 ?></button>
</form>

<?php
// 파일 맨 아래에 번역 함수 정의
function inject_profile_language()
{
    t()->inject([
        '프로필_업데이트' => [
            'ko' => '프로필 업데이트',
            'en' => 'Update Profile',
            'ja' => 'プロフィール更新',
            'zh' => '更新个人资料'
        ],
        '이름' => [
            'ko' => '이름',
            'en' => 'Name',
            'ja' => '名前',
            'zh' => '姓名'
        ],
        '이메일' => [
            'ko' => '이메일',
            'en' => 'Email',
            'ja' => 'メール',
            'zh' => '电子邮件'
        ],
        '저장' => [
            'ko' => '저장',
            'en' => 'Save',
            'ja' => '保存',
            'zh' => '保存'
        ],
    ]);
}
?>
```

### 키 이름 규칙

- **권장**: 짧고 명확한 한글 키 사용 (예: `'이름'`, `'나이'`, `'저장'`)
- **띄어쓰기 포함 가능**: 문장 형태의 키 사용 가능 (예: `'프로필 업데이트'`, `'비밀번호 변경'`)
- **언더스코어 사용 가능**: PHP 프로퍼티로 사용 시 띄어쓰기 대신 언더스코어 사용 (예: `'프로필_업데이트'` → `t()->프로필_업데이트`)

## tr() 인라인 번역 사용법

`tr()` 함수는 짧은 텍스트를 인라인으로 바로 번역할 때 사용합니다.

### 기본 구조

```php
<?= tr(['ko' => '한국어', 'en' => 'English', 'ja' => '日本語', 'zh' => '中文']) ?>
```

### 사용 예제

```php
<!-- 버튼 텍스트 -->
<button class="btn btn-primary">
    <?= tr(['ko' => '확인', 'en' => 'OK', 'ja' => 'OK', 'zh' => '确定']) ?>
</button>

<!-- 알림 메시지 -->
<div class="alert alert-success">
    <?= tr(['ko' => '저장되었습니다', 'en' => 'Saved', 'ja' => '保存されました', 'zh' => '已保存']) ?>
</div>

<!-- 링크 텍스트 -->
<a href="/help">
    <?= tr(['ko' => '도움말', 'en' => 'Help', 'ja' => 'ヘルプ', 'zh' => '帮助']) ?>
</a>
```

### 언제 tr()을 사용할까?

- **한 번만 사용되는 짧은 텍스트**: "확인", "취소", "닫기" 등
- **동적 메시지**: 조건에 따라 다른 메시지를 표시할 때
- **간단한 알림**: 성공/실패 메시지 등

### 언제 t()->inject()를 사용할까?

- **여러 곳에서 재사용되는 텍스트**: "이름", "이메일", "비밀번호" 등
- **페이지 전체에 걸쳐 사용되는 용어**: "프로필", "설정", "로그아웃" 등
- **긴 문장이나 설명 텍스트**: 여러 줄의 안내 문구 등

## 번역 텍스트 출력 방법

### 방법 1: t() 객체 프로퍼티 (권장)

```php
<?= t()->이름 ?>
<?= t()->나이 ?>
<?= t()->프로필_업데이트 ?>
```

**장점**:
- 간결하고 읽기 쉬움
- IDE 자동 완성 지원 가능
- 오타 방지

### 방법 2: tr() 함수

```php
<?= tr('이름') ?>
<?= tr('나이') ?>
<?= tr('프로필 업데이트') ?>
```

**장점**:
- 키에 띄어쓰기 포함 가능
- 동적 키 사용 가능

### 방법 3: tr() 인라인 배열 (임시 사용)

```php
<?= tr(['ko' => '환영합니다', 'en' => 'Welcome', 'ja' => 'ようこそ', 'zh' => '欢迎']) ?>
```

**장점**:
- 즉시 사용 가능
- 별도 주입 불필요

## 완전한 예시

### 예시 1: 사용자 프로필 페이지 (`page/user/profile.php`)

```php
<?php
// 1. 번역 함수 호출 (파일 맨 위)
inject_user_profile_language();

// 페이지 로직
$user = login();
if (!$user) {
    header('Location: /page/user/login.php');
    exit;
}
?>

<!-- 2. 번역 텍스트 사용 -->
<div class="container my-5">
    <h1><?= t()->프로필_업데이트 ?></h1>

    <form id="profile-form">
        <div class="mb-3">
            <label class="form-label"><?= t()->이름 ?></label>
            <input type="text" class="form-control" name="first_name" value="<?= $user['first_name'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label"><?= t()->성 ?></label>
            <input type="text" class="form-control" name="last_name" value="<?= $user['last_name'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label"><?= t()->이메일 ?></label>
            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label"><?= t()->생년월일 ?></label>
            <input type="date" class="form-control" name="birthday" value="<?= $user['birthday'] ?>">
        </div>

        <button type="submit" class="btn btn-primary">
            <?= t()->저장 ?>
        </button>

        <a href="/page/user/profile.php" class="btn btn-secondary">
            <?= t()->취소 ?>
        </a>
    </form>
</div>

<?php
// 3. 번역 함수 정의 (파일 맨 아래)
function inject_user_profile_language()
{
    t()->inject([
        '프로필_업데이트' => [
            'ko' => '프로필 업데이트',
            'en' => 'Update Profile',
            'ja' => 'プロフィール更新',
            'zh' => '更新个人资料'
        ],
        '이름' => [
            'ko' => '이름',
            'en' => 'Name',
            'ja' => '名前',
            'zh' => '姓名'
        ],
        '이메일' => [
            'ko' => '이메일',
            'en' => 'Email',
            'ja' => 'メール',
            'zh' => '电子邮件'
        ],
        '생년월일' => [
            'ko' => '생년월일',
            'en' => 'Birthday',
            'ja' => '生年月日',
            'zh' => '出生日期'
        ],
        '저장' => [
            'ko' => '저장',
            'en' => 'Save',
            'ja' => '保存',
            'zh' => '保存'
        ],
        '취소' => [
            'ko' => '취소',
            'en' => 'Cancel',
            'ja' => 'キャンセル',
            'zh' => '取消'
        ],
        '프로필이_성공적으로_업데이트되었습니다' => [
            'ko' => '프로필이 성공적으로 업데이트되었습니다.',
            'en' => 'Profile updated successfully.',
            'ja' => 'プロフィールが正常に更新されました。',
            'zh' => '个人资料更新成功。'
        ],
        '프로필_업데이트_중_오류가_발생했습니다' => [
            'ko' => '프로필 업데이트 중 오류가 발생했습니다.',
            'en' => 'An error occurred while updating the profile.',
            'ja' => 'プロフィール更新中にエラーが発生しました。',
            'zh' => '更新个人资料时发生错误。'
        ],
    ]);
}
?>
```

### 예시 2: 에러 메시지 표시 (`lib/error/error.functions.php`)

```php
<?php

/**
 * 에러 메시지를 사용자에게 보기 좋게 표시하는 함수
 *
 * @param string $message 표시할 에러 메시지 (번역 키 또는 일반 텍스트)
 * @param string $title 에러 제목 (기본값: "오류")
 */
function display_error($message, $title = '오류')
{
    // 번역 텍스트 주입
    inject_error_language();

    $safe_message = htmlspecialchars($message);
    $safe_title = htmlspecialchars(tr($title));

    echo <<<HTML
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="alert alert-danger shadow-sm border-0" role="alert">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="alert-heading mb-2">{$safe_title}</h4>
                            <p class="mb-0">{$safe_message}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
HTML;
}

/**
 * 에러 관련 번역 텍스트 주입
 */
function inject_error_language()
{
    t()->inject([
        '오류' => [
            'ko' => '오류',
            'en' => 'Error',
            'ja' => 'エラー',
            'zh' => '错误'
        ],
        '경고' => [
            'ko' => '경고',
            'en' => 'Warning',
            'ja' => '警告',
            'zh' => '警告'
        ],
        '알림' => [
            'ko' => '알림',
            'en' => 'Notice',
            'ja' => 'お知らせ',
            'zh' => '通知'
        ],
    ]);
}
```

### 예시 3: 인라인 번역 사용 (`page/home/index.php`)

```php
<?php
inject_home_language();
?>

<div class="container my-5">
    <h1><?= t()->환영합니다 ?></h1>

    <p><?= t()->소개_메시지 ?></p>

    <div class="btn-group">
        <a href="/page/user/register.php" class="btn btn-primary">
            <?= t()->회원가입 ?>
        </a>

        <a href="/page/user/login.php" class="btn btn-secondary">
            <?= t()->로그인 ?>
        </a>
    </div>

    <!-- 임시 메시지 (인라인 번역) -->
    <div class="alert alert-info mt-3">
        <?= tr(['ko' => '베타 서비스입니다', 'en' => 'Beta Service', 'ja' => 'ベータサービス', 'zh' => '测试版服务']) ?>
    </div>
</div>

<?php
function inject_home_language()
{
    t()->inject([
        '환영합니다' => [
            'ko' => '소너브에 오신 것을 환영합니다',
            'en' => 'Welcome to Sonub',
            'ja' => 'ソナブへようこそ',
            'zh' => '欢迎来到Sonub'
        ],
        '소개_메시지' => [
            'ko' => '소너브는 한국의 대표적인 커뮤니티 플랫폼입니다.',
            'en' => 'Sonub is Korea\'s leading community platform.',
            'ja' => 'ソナブは韓国を代表するコミュニティプラットフォームです。',
            'zh' => 'Sonub是韩国领先的社区平台。'
        ],
        '회원가입' => [
            'ko' => '회원가입',
            'en' => 'Sign Up',
            'ja' => '会員登録',
            'zh' => '注册'
        ],
        '로그인' => [
            'ko' => '로그인',
            'en' => 'Login',
            'ja' => 'ログイン',
            'zh' => '登录'
        ],
    ]);
}
?>
```

## 자주 하는 실수

### 실수 1: 언어가 부족함

```php
// ❌ 잘못된 방법: 4개 언어 중 일부만 포함
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name'],
]);

// ✅ 올바른 방법: 4개 언어 모두 포함
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
]);
```

### 실수 2: 키가 영어

```php
// ❌ 잘못된 방법: 키가 영어
t()->inject([
    'name' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
]);

// ✅ 올바른 방법: 키가 한글
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
]);
```

### 실수 3: 순서가 틀림

```php
// ❌ 잘못된 방법: 언어 순서가 틀림
t()->inject([
    '이름' => ['en' => 'Name', 'ko' => '이름', 'zh' => '姓名', 'ja' => '名前'],
]);

// ✅ 올바른 방법: ko → en → ja → zh 순서
t()->inject([
    '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
]);
```

### 실수 4: 번역 함수를 호출하지 않음

```php
<?php
// ❌ 잘못된 방법: 번역 함수를 호출하지 않음
?>

<h1><?= t()->이름 ?></h1>  <!-- 번역이 작동하지 않음! -->

<?php
function inject_page_language()
{
    t()->inject([
        '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
    ]);
}
?>
```

```php
<?php
// ✅ 올바른 방법: 파일 맨 위에서 번역 함수를 호출
inject_page_language();
?>

<h1><?= t()->이름 ?></h1>  <!-- 정상 작동! -->

<?php
function inject_page_language()
{
    t()->inject([
        '이름' => ['ko' => '이름', 'en' => 'Name', 'ja' => '名前', 'zh' => '姓名'],
    ]);
}
?>
```

### 실수 5: UTF-8 인코딩이 아님

```php
// ❌ 잘못된 방법: 파일이 EUC-KR 또는 다른 인코딩으로 저장됨
// 결과: 한글이 깨져서 표시됨 (예: "이름" → "���")

// ✅ 올바른 방법: 파일을 UTF-8 인코딩으로 저장
// 확인 방법: file -I page/user/profile.php
// 출력: charset=utf-8
```

## 요약

1. **4개 언어 필수**: 한국어(ko), 영어(en), 일본어(ja), 중국어(zh) 순서대로 포함
2. **키는 한글**: 번역 키는 반드시 한글로 작성
3. **t()->inject() 사용**: 번역 텍스트는 반드시 `t()->inject()`로 주입
4. **함수 정의 및 호출**: 각 PHP 파일 하단에 번역 함수 정의, 맨 위에서 호출
5. **UTF-8 인코딩**: 모든 파일은 UTF-8 인코딩으로 저장

이 지침을 준수하면 일관되고 유지보수하기 쉬운 다국어 지원 코드를 작성할 수 있습니다.
