# 소너브 홈페이지 다국어 지원 개발 지침

이 문서는 소너브 홈페이지의 다국어 지원(번역) 기능을 개발하는 방법에 대해 설명합니다. 이 지침을 따르면, 새로운 언어를 추가하거나 기존 번역을 수정하는 작업이 쉬워집니다.

## 개요

### 지원 언어

소너브 홈페이지는 다음 언어를 지원합니다:

- 영어 (기본)
- 한국어
- 일본어
- 중국어

영어를 기본 언어로 사용하며, 웹 브라우저의 언어 설정에 따라 자동으로 언어가 선택됩니다. 만약, 웹브라우저의 언어가, 위 언어 중 하나가 아닐 경우, 기본적으로 영어가 적용됩니다.

사용자는 페이지 하단의 언어 선택 메뉴를 통해 수동으로 언어를 변경할 수도 있습니다.

모든 언어는 UTF-8 인코딩을 사용합니다.

## Standard Workflow

- [ ] 모든 언어는 각 PHP 의 하단에 `inject_[module_page]/[php_file_name]_language()` 로컬 함수를 정의 하고, 내부적으로 `t()->inject(...)` 함수를 통해서, 다국어 언어 번역 텍스트를 주입해야 합니다.
  - 예: `index.php` 파일에서는 맨 아래에 `inject_index_language()` 함수를 정의 해 놓고, `index.php` 파일의 맨 위에서 이 함수를 호출 하여 각 언어별 번역된 텍스트를 주입합니다.
  - 아래에서, `page/user/profile.php` 파일의 예시를 참고하세요.
- [ ] 언어 번역 코드와 텍스트를 주입 할 때, `t()->inject(...)` 함수 내부에 배열로 정의 해야 합니다.
- [ ] 각 언어 번역 코드는 반드시 한 글이어야 합니다.
  - 예: `t()->inject([ '텍스트1' => 'Text1', '텍스트2' => 'Text2', ... ]);`

## 예시: `page/user/profile.php` 파일

```php
<?php
inject_user_profile_language();
?>
<h1><?php echo tr('프로필 업데이트'); ?></h1>
<?php
function inject_user_profile_language()
{
    t()->inject([
        '프로필 업데이트' => 'Update Profile',
        '이름' => 'Name',
        '이메일' => 'Email',
        '저장' => 'Save',
        '프로필이 성공적으로 업데이트되었습니다.' => 'Profile updated successfully.',
        '프로필 업데이트 중 오류가 발생했습니다.' => 'An error occurred while updating the profile.',
    ]);
}
?>
```

위 예시에서, `inject_user_profile_language()` 함수는 `t()->inject(...)` 함수를 사용하여 한국어에서 영어로 번역된 텍스트를 주입합니다. 그런 다음, `tr('...')` 함수를 사용하여 번역된 텍스트를 출력합니다.
