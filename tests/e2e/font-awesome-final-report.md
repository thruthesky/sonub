# Font Awesome 아이콘 표시 문제 E2E 테스트 보고서

## 테스트 일시
2025-10-12

## 테스트 대상
- 페이지: `/post/list` (https://local.sonub.com/post/list)
- 위젯: `/widgets/post/post-list-create.php`
- 아이콘: `<i class="fa-solid fa-camera">`

## 발견된 문제점

### 🔴 문제 1: Fatal Error 발생 (최우선 해결 필요)

**에러 메시지:**
```
Fatal error: Uncaught Error: Call to a member function data() on null
in /sonub/etc/config/app.config.php:9
```

**원인:**
- `app.config.php`의 `config()` 함수에서 `login()->data()` 호출 시 `login()` 함수가 null 반환
- 로그인하지 않은 상태에서 페이지 접근 시 발생
- 이 에러로 인해 전체 페이지가 렌더링되지 않음

**영향:**
- 페이지가 완전히 렌더링되지 않아 Font Awesome 아이콘뿐만 아니라 모든 콘텐츠가 표시되지 않음
- 위젯 파일(`post-list-create.php`)이 로드되지 않음

**해결 방법:**
```php
// /etc/config/app.config.php 수정
function config(): array
{
    $loginData = login() ? login()->data() : null;

    return [
        'login' => $loginData,
        'api' => [
            'file_upload_url' => '/api.php?func=file_upload',
            'file_delete_url' => '/api.php?func=file_delete'
        ],
        'upload_path' => get_file_upload_path(),
    ];
}

function get_file_upload_path(): string
{
    $userId = login() ? login()->id : 'guest';
    return ROOT_DIR . '/var/uploads/' . $userId . '/';
}
```

### 🟡 문제 2: Font Awesome CSS 파일 불완전 (두번째 우선순위)

**현재 상태:**
```html
<link rel="stylesheet" href="/etc/frameworks/font-awesome/css/fontawesome.min.css">
```

**문제점:**
- `fontawesome.min.css`만 로드되어 있음
- 실제 아이콘을 표시하려면 아이콘 스타일 CSS 파일도 필요함
- `fa-solid`, `fa-regular`, `fa-light` 등의 스타일별 CSS 파일이 누락됨

**해결 방법 1: all.min.css 사용 (권장)**
```html
<link rel="stylesheet" href="/etc/frameworks/font-awesome/css/all.min.css">
```

**해결 방법 2: 개별 CSS 파일 사용**
```html
<link rel="stylesheet" href="/etc/frameworks/font-awesome/css/fontawesome.min.css">
<link rel="stylesheet" href="/etc/frameworks/font-awesome/css/solid.min.css">
<link rel="stylesheet" href="/etc/frameworks/font-awesome/css/regular.min.css">
```

**수정 대상 파일:**
- `index.php` 또는 `head.php` (Font Awesome CSS가 로드되는 위치)

## 테스트 결과

### ✅ 정상 작동
1. HTTP 상태 코드: 200 (페이지 접근 성공)
2. Font Awesome CSS 파일 링크 존재

### ❌ 실패
1. 페이지가 Fatal Error로 인해 완전히 렌더링되지 않음
2. Font Awesome 아이콘 요소가 HTML에 포함되지 않음
3. `<form>` 태그가 렌더링되지 않음
4. Font Awesome 아이콘 스타일 CSS 파일 누락

## 해결 순서

1. **1단계 (필수)**: `app.config.php`의 Fatal Error 수정
   - `login()` null 체크 추가
   - 비로그인 사용자도 페이지 접근 가능하도록 수정

2. **2단계 (필수)**: Font Awesome CSS 파일 수정
   - `fontawesome.min.css` → `all.min.css`로 변경
   - 또는 `solid.min.css` 추가

3. **3단계 (검증)**: E2E 테스트 재실행
   - 페이지가 정상적으로 렌더링되는지 확인
   - Font Awesome 아이콘이 표시되는지 확인

## 추가 확인 사항

수정 후 다음 사항을 확인하세요:

1. **웹 브라우저 개발자 도구 (F12) 확인:**
   - Console 탭: JavaScript 에러 확인
   - Network 탭: Font Awesome CSS 파일이 200 상태로 로드되는지 확인
   - Network 탭: 웹폰트 파일(.woff, .woff2)이 로드되는지 확인

2. **Font Awesome 아이콘 표시 확인:**
   - 카메라 아이콘이 웹 브라우저에서 보이는지 확인
   - 아이콘 크기가 `font-size: 2em`으로 올바르게 표시되는지 확인

3. **로그인 상태별 테스트:**
   - 비로그인 상태에서 페이지 접근
   - 로그인 상태에서 페이지 접근
   - 두 경우 모두 정상 작동하는지 확인

## 참고 파일

- E2E 테스트 파일: `/tests/e2e/font-awesome-icon.e2e.test.php`
- 디버그 테스트 파일: `/tests/e2e/font-awesome-debug.e2e.test.php`
- 수정 대상 파일: `/etc/config/app.config.php`
- Font Awesome CSS 로드 위치: `index.php` 또는 `head.php`
