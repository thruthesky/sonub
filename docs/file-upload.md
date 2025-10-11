파일 업로드
- 본 문서에서는 파일 및 사진, 동영상 등의 업로드 기능에 대해 설명합니다.

# 목차 (Table of Contents)

- [목차 (Table of Contents)](#목차-table-of-contents)
- [파일 업로드 개요](#파일-업로드-개요)
- [주요 함수](#주요-함수)
- [기본 사용법](#기본-사용법)
  - [1단계: 파일 입력 버튼 추가](#1단계-파일-입력-버튼-추가)
  - [2단계: 표시 영역(Display Area) 추가](#2단계-표시-영역display-area-추가)
  - [3단계: ID 연결](#3단계-id-연결)
- [파일 입력 버튼 디자인](#파일-입력-버튼-디자인)
  - [기본 스타일](#기본-스타일)
  - [커스텀 디자인](#커스텀-디자인)
- [extra 파라미터 옵션](#extra-파라미터-옵션)
  - [옵션 설명](#옵션-설명)
  - [사용 예제](#사용-예제)
- [Single 모드: 단일 파일 업로드 (data-single)](#single-모드-단일-파일-업로드-data-single)
  - [개요](#개요)
  - [동작 방식](#동작-방식)
  - [가장 간단한 완전한 예제 (강력 권장)](#가장-간단한-완전한-예제-강력-권장)
  - [프로필 사진 업로드 완전한 예제](#프로필-사진-업로드-완전한-예제)
  - [디자인 커스터마이징](#디자인-커스터마이징)
    - [타원형 프로필 사진 만들기](#타원형-프로필-사진-만들기)
    - [Progress Bar 위치 조정](#progress-bar-위치-조정)
    - [카메라 아이콘 추가](#카메라-아이콘-추가)
    - [기본 아이콘 클릭 방식 (Input 숨기기)](#기본-아이콘-클릭-방식-input-숨기기)
    - [자동 삭제 버튼 (data-delete-icon="show")](#자동-삭제-버튼-data-delete-iconshow)
      - [개요](#개요-1)
      - [주요 특징](#주요-특징)
      - [완전한 사용 예제](#완전한-사용-예제)
      - [동작 방식](#동작-방식-1)
      - [이벤트 버블링 방지 - 중요!](#이벤트-버블링-방지---중요)
      - [속성 비교](#속성-비교)
      - [실제 사용 예시](#실제-사용-예시-1)
    - [삭제 버튼 추가 (수동 구현)](#삭제-버튼-추가-수동-구현)
      - [기본 삭제 버튼](#기본-삭제-버튼)
      - [삭제 버튼 숨기기/보이기](#삭제-버튼-숨기기보이기)
  - [콜백 함수 (on\_uploaded, on\_deleted)](#콜백-함수-on_uploaded-on_deleted)
    - [on\_uploaded 콜백](#on_uploaded-콜백)
    - [on\_deleted 콜백](#on_deleted-콜백)
    - [콜백 함수 파라미터](#콜백-함수-파라미터)
    - [실제 사용 예제](#실제-사용-예제)
    - [콜백 없이 사용하기](#콜백-없이-사용하기)
  - [CSS 스타일링 가이드](#css-스타일링-가이드)
    - [정사각형 프로필 사진](#정사각형-프로필-사진)
    - [직사각형 배너 이미지](#직사각형-배너-이미지)
    - [테두리와 배경 추가](#테두리와-배경-추가)
  - [주의사항](#주의사항)
    - [1. data-single="true" 필수](#1-data-singletrue-필수)
    - [2. accept="image/\*" 권장](#2-acceptimage-권장)
    - [3. 파일 타입 검사 없음](#3-파일-타입-검사-없음)
    - [4. Progress Bar 위치](#4-progress-bar-위치)
    - [5. 삭제 버튼 직접 구현](#5-삭제-버튼-직접-구현)
    - [6. 서버 측 deleteFile 처리](#6-서버-측-deletefile-처리)
- [업로드 진행 상태 표시](#업로드-진행-상태-표시)
- [Hidden Input으로 URL 전송](#hidden-input으로-url-전송)
  - [동작 방식](#동작-방식-1)
  - [사용 예제](#사용-예제-1)
  - [동작 단계별 예시](#동작-단계별-예시)
  - [서버에서 받는 방법](#서버에서-받는-방법)
  - [주요 함수](#주요-함수-1)
- [파일 삭제 기능](#파일-삭제-기능)
  - [동작 방식](#동작-방식-2)
  - [삭제 버튼 위치](#삭제-버튼-위치)
  - [삭제 함수 시그니처](#삭제-함수-시그니처)
  - [삭제 동작 예시](#삭제-동작-예시)
  - [서버 API 요청 형식](#서버-api-요청-형식)
  - [에러 처리](#에러-처리)
  - [주의사항](#주의사항-1)
- [차분 업데이트 (화면 깜빡임 최소화)](#차분-업데이트-화면-깜빡임-최소화)
  - [동작 원리](#동작-원리)
  - [장점](#장점)
  - [동작 예시](#동작-예시)
  - [기술적 구현](#기술적-구현)
  - [전체 재렌더링과 비교](#전체-재렌더링과-비교)
- [기본 파일 표시 (data-default-files)](#기본-파일-표시-data-default-files)
  - [동작 방식](#동작-방식-3)
  - [사용 예제](#사용-예제-2)
    - [기본 사용법](#기본-사용법-1)
    - [Hidden Input과 함께 사용](#hidden-input과-함께-사용)
    - [게시글 수정 완전한 예제](#게시글-수정-완전한-예제)
  - [동작 단계별 설명](#동작-단계별-설명)
  - [파일 삭제 시 동작](#파일-삭제-시-동작)
  - [주의사항](#주의사항-2)
  - [서버에서 파일 업데이트 처리](#서버에서-파일-업데이트-처리)
- [전체 구현 예제](#전체-구현-예제)
  - [기본 예제](#기본-예제)
  - [Hidden Input 포함 예제](#hidden-input-포함-예제)
  - [게시글 수정 시 기본 파일 포함 예제](#게시글-수정-시-기본-파일-포함-예제)

# 파일 업로드 개요

- 순수 자바스크립트(Pure JavaScript)로 파일 업로드 기능을 제공한다.
- Axios를 사용하여 파일을 서버로 전송한다 (Axios는 body 태그 상단에 이미 로드되어 있음).
- `./js/file-upload.js` 파일에 업로드 관련 함수가 있으며, 이 파일이 전체 파일 업로드의 핵심 기능을 제공한다.
- 파일 업로드는 `widgets/post/form/text-editor.php`, 코멘트 생성/수정 등에서 사용된다.

# 주요 함수

- `handle_file_change(event, extra = {})`: 파일 선택 이벤트 핸들러. 사용자가 파일을 선택하면 호출된다. **`extra.id`는 필수 파라미터**이다.
- `upload_file(file, extra = {})`: 개별 파일을 서버로 업로드하는 함수. 업로드 진행 상황을 추적하고, 완료 후 hidden input에 URL을 추가한 뒤 파일 목록을 차분 업데이트한다.
- `display_uploaded_files(displayAreaId)`: hidden input의 URL 목록을 기반으로 **차분 업데이트(differential update)**를 수행하는 함수. 기존 파일은 유지하고 새로 추가되거나 삭제된 파일만 업데이트하여 **화면 깜빡임을 최소화**한다.
- `create_file_item(url, displayAreaId)`: 개별 파일 아이템을 생성하는 함수. 각 파일에 삭제 버튼이 자동으로 추가된다.
- `delete_file(url, displayAreaId)`: 서버에 업로드된 파일을 삭제하고, hidden input에서 해당 URL을 제거한 후 파일 목록을 차분 업데이트하는 함수.
- `add_url_to_hidden_input(displayAreaId, url)`: display-area의 `data-input-name` 속성을 읽어서 해당 이름의 hidden input에 URL을 추가하는 함수.
- `remove_url_from_hidden_input(displayAreaId, url)`: display-area의 `data-input-name` 속성을 읽어서 해당 이름의 hidden input에서 특정 URL을 제거하는 함수.

# 기본 사용법

파일 업로드 기능을 구현하려면 다음 3단계를 따른다.

## 1단계: 파일 입력 버튼 추가

HTML에 파일 입력 요소를 추가한다. `multiple` 속성을 사용하면 여러 파일을 동시에 선택할 수 있다.

```html
<input type="file" multiple onchange="handle_file_change(event, { id: 'display-uploaded-files' })">
```

## 2단계: 표시 영역(Display Area) 추가

업로드된 파일(이미지, 영상 등)을 표시할 위치에 HTML 요소를 추가한다. 이 영역을 "표시 영역(Display Area)" 또는 "디스플레이 공간"이라고 부른다.

```html
<div id="display-uploaded-files"></div>
```

- 이 `<div>` 요소는 HTML 어디든 원하는 위치에 배치할 수 있다.
- 업로드된 파일들이 이 영역에 자동으로 표시된다.
- **자동 name 생성**: `data-input-name` 속성이 없으면 `id` 속성 값이 hidden input의 name으로 자동 사용된다.
- **커스텀 name**: `data-input-name` 속성을 지정하여 hidden input의 name을 커스터마이징할 수 있다.

```html
<!-- 기본 방식: id를 hidden input name으로 사용 -->
<div id="profile-photo"></div>
<!-- 생성됨: <input type="hidden" name="profile-photo"> -->

<!-- 커스텀 name 지정 -->
<div id="display-uploaded-files" data-input-name="files"></div>
<!-- 생성됨: <input type="hidden" name="files"> -->
```

## 3단계: ID 연결

`handle_file_change()` 함수의 `id` 옵션과 표시 영역의 `id` 속성을 동일하게 지정하여 서로 연결한다.

```javascript
// handle_file_change의 id 옵션
{ id: 'display-uploaded-files' }

// HTML 요소의 id 속성
<div id="display-uploaded-files"></div>
```

이렇게 하면 업로드된 파일이 지정한 영역에 자동으로 표시되고, hidden input에 URL이 저장된다.

**예제:**

```html
<!-- 방법 1: id만 사용 (간단) -->
<input type="file" onchange="handle_file_change(event, { id: 'profile-photo' })">
<div id="profile-photo"></div>
<!-- 결과: <input type="hidden" name="profile-photo" value="..."> -->

<!-- 방법 2: 커스텀 name 사용 -->
<input type="file" onchange="handle_file_change(event, { id: 'my-files' })">
<div id="my-files" data-input-name="attachments"></div>
<!-- 결과: <input type="hidden" name="attachments" value="..."> -->
```

# 파일 입력 버튼 디자인

## 기본 스타일

기본 파일 입력 버튼을 그대로 사용할 수 있다.

```html
<input type="file" multiple onchange="handle_file_change(event, { id: 'display-uploaded-files' })">
```

## 커스텀 디자인

기본 파일 입력 버튼의 디자인이 마음에 들지 않는다면, `<label>` 태그와 아이콘을 사용하여 커스텀 디자인을 적용할 수 있다.

```html
<label class="pointer">
    <i class="fa-solid fa-camera" style="font-size: 2em;"></i>
    <input type="file" multiple style="display: none;" onchange="handle_file_change(event, { id: 'display-uploaded-files' })">
</label>
```

**설명:**
- `<input>` 요소를 `style="display: none;"`으로 숨긴다.
- `<label>` 태그로 감싸서 클릭 가능한 영역을 만든다.
- FontAwesome 아이콘 (예: `fa-camera`)을 사용하여 시각적으로 더 나은 UI를 제공한다.
- `.pointer` 클래스를 사용하여 마우스 커서를 포인터로 변경한다.

# extra 파라미터 옵션

`handle_file_change()` 함수의 두 번째 인자인 `extra` 객체는 다음 옵션들을 지원한다.

## 옵션 설명

| 옵션 | 타입 | 설명 |
|------|------|------|
| `id` | string | **업로드된 파일을 표시할 HTML 요소의 ID (필수)** |
| `single` | boolean | `true`: 기존 파일을 서버에서 자동 삭제하고 새 파일로 교체<br>`false`: 기존 파일 유지하고 새 파일 추가 (기본값) |
| `decodeQrCode` | boolean | `true`: 업로드된 이미지를 QR 코드로 간주하고 디코드 시도<br>`false`: QR 코드 디코드 하지 않음 (기본값) |

**⚠️ 중요**:
- `id` 파라미터는 반드시 지정해야 한다. 지정하지 않으면 에러 메시지가 표시되고 업로드가 진행되지 않는다.
- `data-input-name` 속성은 선택사항이다. 지정하지 않으면 display-area의 `id` 값이 hidden input의 name으로 자동 사용된다.

**`single` 옵션 동작 방식**:

`single: true`로 설정하면 다음과 같이 동작한다:

1. **업로드 전**: hidden input의 기존 URL 값을 읽음
2. **서버 전송**: 기존 URL을 `deleteFile` 파라미터로 서버에 전달
3. **서버 처리**: 서버가 기존 파일을 삭제하고 새 파일을 업로드
4. **업로드 후**: hidden input 값을 새 URL로 완전히 교체
5. **화면 갱신**: display area를 새 파일만 표시하도록 재렌더링

**사용 사례**:
- 프로필 사진: 항상 1개만 유지
- 배너 이미지: 이전 이미지를 교체
- 대표 이미지: 단일 이미지만 허용

**예제**:

```javascript
// 프로필 사진 업로드 (기존 사진 자동 삭제)
<input type="file"
       onchange="handle_file_change(event, {
           id: 'profile-photo',
           single: true
       })">
<div id="profile-photo"></div>
```

**동작 흐름**:

```
초기 상태: hidden input value = "old-photo.jpg"
         display area = [old-photo.jpg 표시]

↓ 새 파일 선택 (new-photo.jpg)

서버 요청: { file: new-photo.jpg, deleteFile: "old-photo.jpg" }

↓ 서버 응답: { url: "new-photo.jpg" }

최종 상태: hidden input value = "new-photo.jpg"
         display area = [new-photo.jpg 표시]
```

## 사용 예제

```javascript
// 여러 파일 업로드 (기본값: 기존 파일 유지)
handle_file_change(event, {
    id: 'display-uploaded-files'
})

// 단일 파일 모드: 기존 파일을 서버에서 자동 삭제하고 새 파일로 교체
handle_file_change(event, {
    id: 'profile-photo',
    single: true
})

// QR 코드 이미지 업로드 및 디코드
handle_file_change(event, {
    id: 'qr-code-area',
    decodeQrCode: true
})
```

**HTML 예제:**

```html
<!-- 여러 파일 업로드 -->
<div id="display-uploaded-files" data-input-name="files"></div>

<!-- 단일 파일 (프로필 사진) -->
<div id="profile-photo" data-input-name="photo"></div>

<!-- QR 코드 -->
<div id="qr-code-area" data-input-name="qr_code"></div>
```

# Single 모드: 단일 파일 업로드 (data-single)

## 개요

`data-single="true"` 속성을 사용하면 단일 파일만 유지하는 모드로 동작한다. 주로 프로필 사진, 배너 이미지, 대표 이미지 등 하나의 파일만 필요한 경우에 사용한다.

**주요 특징:**
- 새 파일 업로드 시 기존 파일이 서버에서 자동으로 삭제됨
- Hidden input에는 항상 하나의 URL만 저장됨
- 파일 타입 검사 없이 무조건 이미지로 처리
- 삭제 버튼이 자동으로 표시되지 않음 (별도로 구현 필요)

## 동작 방식

**1. 초기 HTML (개발자가 작성하는 코드)**
```html
<!-- 파일 업로드 input -->
<input type="file" accept="image/*"
       onchange="handle_file_change(event, { id: 'profile-photo' })">

<!-- display area: 이것만 작성하면 됨 -->
<div id="profile-photo" data-single="true"></div>
```

**2. 페이지 로드 후 (data-default-files가 있는 경우)**

개발자가 작성:
```html
<div id="profile-photo"
     data-single="true"
     data-default-files="old-photo.jpg"></div>
```

file-upload.js가 자동으로 생성:
```html
<div id="profile-photo" data-single="true" data-default-files="old-photo.jpg">
    <!-- 자동 생성된 hidden input -->
    <input type="hidden" name="profile-photo" value="old-photo.jpg">

    <!-- 자동 생성된 이미지 -->
    <div class="uploaded-files my-3">
        <img src="old-photo.jpg" class="img-fluid rounded" style="max-width: 300px;">
    </div>
</div>
```

**3. 새 파일 선택 시**
```javascript
// file-upload.js가 서버로 전송하는 FormData
{
    uid: "firebase-user-uid",
    file: new-photo.jpg,              // 새로 선택한 파일
    deleteFile: "old-photo.jpg"        // 기존 파일 URL (자동으로 읽어서 전달)
}
```

**4. 업로드 완료 후**
```html
<div id="profile-photo" data-single="true" data-default-files="old-photo.jpg">
    <!-- hidden input 값이 새 파일로 교체됨 -->
    <input type="hidden" name="profile-photo" value="new-photo.jpg">

    <!-- 이미지도 새 파일로 교체됨 -->
    <div class="uploaded-files my-3">
        <img src="new-photo.jpg" class="img-fluid rounded" style="max-width: 300px;">
    </div>
</div>
```

**중요 포인트:**
- 개발자는 단순히 `<div id="profile-photo" data-single="true"></div>` 만 작성
- hidden input과 이미지는 file-upload.js가 자동으로 생성하고 관리
- `data-default-files` 속성으로 기본 파일 URL 지정 가능
- single 모드에서는 새 파일 업로드 시 기존 파일을 서버에서 자동 삭제

## 가장 간단한 완전한 예제 (강력 권장)

이 예제는 가장 간단하면서도 완전한 기능을 제공하며, 활용 가능성이 매우 높다.

```html
<label>
    <div id="logo_url" data-single="true" data-default-files="<?= $company->logo_url ?>"></div>
    <input type="file" onchange="handle_file_change(event, { id: 'logo_url'})" accept="image/*" />
</label>
```

**주요 특징:**
- **오직 3줄**: 최소한의 HTML만으로 완전한 파일 업로드 구현
- **자동 name 생성**: `logo_url` (display-area의 id)이 자동으로 hidden input의 name으로 사용됨
- **폼 제출 시 자동 전송**: 폼 제출 시 `logo_url` 파라미터로 파일 URL이 자동으로 서버에 전송됨
- **파일 교체 자동화**: 새 파일 선택 시 기존 파일이 서버에서 자동 삭제되고 새 파일로 교체됨
- **기본 파일 표시**: `data-default-files`로 DB에 저장된 기본 파일을 자동으로 표시
- **높은 재사용성**: 회사 로고, 제품 이미지, 배너 등 다양한 곳에 바로 사용 가능

**서버에서 받는 방법:**

```php
// company/register.php 또는 update.php
$logo_url = in('logo_url'); // hidden input의 name이 logo_url이므로 이 파라미터로 전달됨

// DB에 저장
$company->logo_url = $logo_url;
$company->save();
```

**실제 사용 예시:**

```html
<!-- 회사 로고 업로드 -->
<form action="/company/update.php" method="POST">
    <label>
        <div id="logo_url" data-single="true" data-default-files="<?= $company->logo_url ?>"></div>
        <input type="file" onchange="handle_file_change(event, { id: 'logo_url'})" accept="image/*" />
    </label>
    <button type="submit">저장</button>
</form>

<!-- 제품 대표 이미지 -->
<label>
    <div id="product_image" data-single="true" data-default-files="<?= $product->image ?>"></div>
    <input type="file" onchange="handle_file_change(event, { id: 'product_image'})" accept="image/*" />
</label>

<!-- 배너 이미지 -->
<label>
    <div id="banner_url" data-single="true" data-default-files="<?= $banner->url ?>"></div>
    <input type="file" onchange="handle_file_change(event, { id: 'banner_url'})" accept="image/*" />
</label>
```

**왜 이 방법을 권장하는가:**
1. **단순함**: 3줄만으로 완전한 기능 구현
2. **완전함**: 업로드, 교체, 표시, 전송 모든 기능 포함
3. **유지보수 용이**: 코드가 짧고 명확하여 이해하기 쉬움
4. **재사용성**: 어디서든 바로 복사해서 사용 가능

**스타일 디자인 추가:**

필요한 경우 CSS로 이미지 크기와 디자인을 커스터마이징할 수 있다.

```html
<style>
    .logo-upload {
        cursor: pointer;
    }

    .logo-upload #logo_url img {
        width: 128px !important;
        height: 128px !important;
        object-fit: cover;
        border: 1px solid var(--bs-border-color);
        border-radius: 8px;
        background-color: var(--bs-secondary-bg);
    }
</style>
<label class="logo-upload">
    <div id="logo_url" data-single="true" data-default-files="<?= $company->logo_url ?>"></div>
    <input type="file" onchange="handle_file_change(event, { id: 'logo_url'})" accept="image/*" />
</label>
```

**CSS 포인트:**
- `cursor: pointer`: 마우스 커서를 포인터로 변경하여 클릭 가능함을 표시
- `display: block`: 이미지를 블록 요소로 표시하여 레이아웃 제어
- `margin-bottom: .5rem`: 이미지 하단에 여백 추가
- `width`, `height`: 업로드된 이미지의 크기를 128x128px로 고정
- `object-fit: cover`: 이미지 비율을 유지하면서 지정된 크기에 맞춰 자르기
- `border`: Bootstrap 테마 색상을 사용한 1px 테두리
- `border-radius: 8px`: 모서리를 둥글게 처리
- `background-color`: Bootstrap 보조 배경색 사용 (다크/라이트 모드 자동 대응)
- `!important`: 기본 스타일을 덮어쓰기 위해 사용

## 프로필 사진 업로드 완전한 예제

다음은 `widgets/user/photo-upload.php`의 실제 구현 예제다.

```php
<?php load_deferred_js('file-upload'); ?>
<style>
    #profile-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-color: #f0f0f0;
        cursor: pointer;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
        position: relative;
    }

    /* uploaded-files div가 profile-photo를 완전히 채우도록 */
    #profile-photo .uploaded-files {
        width: 100%;
        height: 100%;
        margin: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 0;
        left: 0;
        overflow: hidden;
        border-radius: 50%;
    }

    /* 이미지가 타원형을 완전히 채우도록 */
    #profile-photo .uploaded-files img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0;
        max-width: none !important;
    }

    /* progress bar를 타원형 아래에 명확히 표시 */
    #profile-photo .file-upload-progress-container {
        position: relative;
        width: 150px;
        margin-top: 160px !important;
        z-index: 100;
    }
</style>

<div class="d-inline-block position-relative pointer" style="width: 150px; height: 150px;">
    <label class="d-inline-block position-relative pointer" style="width: 150px; height: 150px;">
        <section>
            <div id="profile-photo"
                 data-single="true"
                 data-default-files="<?= login()->photo_url ?>"></div>
            <input type="file"
                   onchange="handle_file_change(event, { id: 'profile-photo', on_uploaded: on_uploaded })"
                   class="d-none"
                   accept="image/*" />
            <i class="fa-solid fa-camera position-absolute bottom-0 end-0 text-body"
               style="font-size: 2em; cursor: pointer;"></i>
        </section>
    </label>

    <!-- 별도 삭제 버튼 (프로필 사진이 있을 때만 표시) -->
    <button class="profile-photo-delete-button <?= login()->photo_url ? '' : 'd-none' ?> position-absolute top-0 end-0"
            style="background: none; border: none; cursor: pointer;"
            type="button"
            onclick="delete_file(get_hidden_input_value('profile-photo'), {id: 'profile-photo', on_deleted: on_deleted});"
            title="<?= t()->삭제 ?>">
        <i class="fa-solid fa-xmark text-danger" style="font-size: 1.5em;"></i>
    </button>
</div>

<script>
    /**
     * 파일 업로드 후 호출되는 콜백 함수
     * @param {Object} json - 업로드된 파일의 JSON 응답
     */
    function on_uploaded(json) {
        if (json && json.url) {
            // 프로필 사진 업데이트 요청
            update_my_profile({
                photo_url: json.url
            });
            // 삭제 버튼 표시
            document.querySelector('.profile-photo-delete-button').classList.remove('d-none');
        }
    }

    /**
     * 파일 삭제 후 호출되는 콜백 함수
     * @param {Object} json - 삭제된 파일의 JSON 응답
     */
    function on_deleted(json) {
        if (json && json.deleted) {
            // 프로필 사진 삭제 요청
            update_my_profile({
                photo_url: ''
            });
            // 삭제 버튼 숨김
            document.querySelector('.profile-photo-delete-button').classList.add('d-none');
        }
    }
</script>
```

## 디자인 커스터마이징

### 타원형 프로필 사진 만들기

```css
#profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;  /* 타원형 */
    overflow: visible;   /* progress bar가 밖으로 나올 수 있도록 */
    position: relative;
}

/* 이미지 컨테이너 */
#profile-photo .uploaded-files {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    overflow: hidden;      /* 이미지를 타원형으로 자르기 */
    border-radius: 50%;
}

/* 이미지가 타원형을 완전히 채우도록 */
#profile-photo .uploaded-files img {
    width: 100%;
    height: 100%;
    object-fit: cover;     /* 비율 유지하며 채우기 */
    max-width: none !important;
}
```

### Progress Bar 위치 조정

```css
/* progress bar를 타원형 아래에 표시 */
#profile-photo .file-upload-progress-container {
    position: relative;
    width: 150px;
    margin-top: 160px !important;  /* 타원형 높이 + 여백 */
    z-index: 100;
}
```

### 카메라 아이콘 추가

```html
<label class="position-relative pointer" style="width: 150px; height: 150px;">
    <div id="profile-photo" data-single="true"></div>
    <input type="file" class="d-none" accept="image/*"
           onchange="handle_file_change(event, { id: 'profile-photo' })">

    <!-- 오른쪽 아래에 카메라 아이콘 -->
    <i class="fa-solid fa-camera position-absolute bottom-0 end-0 text-body"
       style="font-size: 2em; cursor: pointer;"></i>
</label>
```

### 기본 아이콘 클릭 방식 (Input 숨기기)

파일 input을 완전히 숨기고 기본 아이콘이나 이미지를 클릭하여 파일을 업로드할 수 있다.

```html
<label class="pointer">
    <div id="logo_url" data-single="true">
        <!-- 기본 아이콘/이미지 직접 지정 -->
        <div class="uploaded-files my-3">
            <img src="/res/img/upload-icon.webp"
                 class="img-fluid rounded"
                 style="max-width: 300px;">
        </div>
    </div>
    <input type="file"
           onchange="handle_file_change(event, { id: 'logo_url'})"
           class="d-none"
           accept="image/*" />
</label>
```

**동작 방식:**
1. **초기 상태**: 기본 아이콘/이미지(`/res/img/upload-icon.webp`)가 표시됨
2. **클릭 시**: label로 감싸져 있어 이미지를 클릭하면 파일 선택 창이 열림
3. **업로드 후**: 기본 이미지가 업로드된 파일로 교체됨
4. **삭제 시**: `uploaded-files` div가 완전히 제거됨

**⚠️ 중요한 주의사항:**

파일 삭제 후 `uploaded-files` div가 통째로 제거되므로, **다시 업로드할 수 없는 문제**가 발생할 수 있다.

**해결 방법 1: 삭제 버튼 제거 (권장)**
```html
<!-- 삭제 기능 없이 교체만 가능 -->
<label class="pointer">
    <div id="logo_url" data-single="true">
        <div class="uploaded-files my-3">
            <img src="/res/img/upload-icon.webp" class="img-fluid rounded" style="max-width: 300px;">
        </div>
    </div>
    <input type="file" onchange="handle_file_change(event, { id: 'logo_url'})"
           class="d-none" accept="image/*" />
</label>
```

**해결 방법 2: 기본 아이콘을 별도로 유지**
```html
<div class="position-relative">
    <label class="pointer">
        <!-- 기본 아이콘 (항상 유지) -->
        <div class="default-upload-icon text-center p-3 border rounded">
            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 3em;"></i>
            <p>클릭하여 이미지 업로드</p>
        </div>

        <!-- 업로드된 파일 표시 영역 -->
        <div id="logo_url" data-single="true"></div>

        <input type="file"
               onchange="handle_file_change(event, { id: 'logo_url'})"
               class="d-none"
               accept="image/*" />
    </label>

    <!-- 삭제 버튼 (파일이 있을 때만 표시) -->
    <button class="delete-btn d-none position-absolute top-0 end-0"
            onclick="delete_file(get_hidden_input_value('logo_url'), {
                id: 'logo_url',
                on_deleted: on_logo_deleted
            });">
        <i class="fa-solid fa-xmark text-danger"></i>
    </button>
</div>

<script>
    function on_logo_deleted(json) {
        if (json && json.deleted) {
            // 삭제 버튼 숨김
            document.querySelector('.delete-btn').classList.add('d-none');
            // 기본 아이콘은 그대로 유지됨
        }
    }
</script>
```

**권장 방법:**

단일 파일 모드에서 기본 아이콘 클릭 방식을 사용할 때는 **삭제 기능 없이 교체만 허용**하는 것을 권장한다. 사용자가 다른 파일을 선택하면 자동으로 교체되므로 삭제 버튼이 꼭 필요하지 않다.

### 자동 삭제 버튼 (`data-delete-icon="show"`)

#### 개요

`data-delete-icon="show"` 속성을 사용하면 single 모드에서 자동으로 삭제 버튼이 표시된다.

#### 주요 특징

- **`data-single="true"` 전용**: 이 옵션은 single 모드에서만 작동한다
- **자동 삭제 버튼**: X 버튼이 이미지 오른쪽 상단에 자동으로 표시됨
- **일반 모드는 자동**: `data-single="true"`가 없는 일반 모드에서는 삭제 버튼이 자동으로 표시되므로 이 속성이 필요 없음
- **서버 삭제**: 삭제 버튼 클릭 시 이미지가 서버에서 삭제되고 hidden input 값이 초기화됨
- **이벤트 버블링 방지**: `<label for="...">` 와 `<input id="...">` 연결 필수

#### 완전한 사용 예제

```html
<label for="title_image_file_upload" class="position-relative d-block pointer w-100">
    <div id="title_image_url"
         data-single="true"
         data-delete-icon="show"
         data-default-files="<?= $company->title_image_url ?>"></div>
    <input id="title_image_file_upload"
           type="file"
           accept="image/*"
           onchange="handle_file_change(event, { id: 'title_image_url'})" />
</label>
```

#### 동작 방식

**1. 이미지 클릭:**
- 이미지를 클릭하면 파일 선택 대화상자가 열림
- 새 이미지를 선택하면 자동으로 업로드됨
- 기존 이미지가 서버에서 삭제되고 새 이미지로 교체됨

**2. 삭제 버튼(X) 클릭:**
- X 버튼을 클릭하면 확인 대화상자가 표시됨
- 확인 시 서버에서 이미지가 삭제됨
- Hidden input 값이 빈 문자열로 초기화됨
- 이미지가 화면에서 제거됨

#### 이벤트 버블링 방지 - 중요!

**⚠️⚠️⚠️ 필수 요구사항:**

1. **`<label for="...">` 와 `<input id="...">` 연결 필수**
2. **`<label>` 태그에 필수 클래스**: `position-relative d-block pointer`

**필수 클래스 설명:**
- `position-relative`: 삭제 버튼의 `position: absolute` 기준점 제공
- `d-block`: label을 블록 요소로 표시하여 전체 너비 차지
- `pointer`: 마우스 커서를 포인터로 변경하여 클릭 가능함을 표시

**올바른 예:**
```html
<!-- ✅ label for, input id 연결 + 필수 클래스 -->
<label for="my_file_input" class="position-relative d-block pointer">
    <div id="my_image" data-single="true" data-delete-icon="show"></div>
    <input id="my_file_input" type="file" onchange="handle_file_change(event, { id: 'my_image'})" />
</label>
```

**잘못된 예:**
```html
<!-- ❌ label for 와 input id 미연결 - 이벤트 버블링 발생! -->
<label>
    <div id="my_image" data-single="true" data-delete-icon="show"></div>
    <input type="file" onchange="handle_file_change(event, { id: 'my_image'})" />
</label>

<!-- ❌ 필수 클래스 누락 - 레이아웃 깨짐 -->
<label for="my_file_input">
    <div id="my_image" data-single="true" data-delete-icon="show"></div>
    <input id="my_file_input" type="file" onchange="handle_file_change(event, { id: 'my_image'})" />
</label>
```

**이유:**
- `for` 와 `id` 연결 없이 label을 사용하면 클릭 이벤트가 예측할 수 없이 전파됨
- 이미지 클릭 시 삭제 버튼이 트리거되거나 삭제 버튼 클릭 시 파일 선택 대화상자가 열리는 문제 발생
- 필수 클래스 누락 시 삭제 버튼 위치가 틀어지고 클릭 영역이 제대로 작동하지 않음
- `for`와 `id` 연결 + 필수 클래스로 이벤트 전파 경로가 명확해지고 레이아웃이 올바르게 표시됨

#### 속성 비교

| 속성 | `data-single="true"` | 일반 모드 (single 없음) |
|------|---------------------|----------------------|
| 삭제 버튼 기본 동작 | 자동으로 표시되지 않음 | 자동으로 표시됨 |
| `data-delete-icon="show"` | ✅ 삭제 버튼 표시 | ❌ 불필요 (이미 표시됨) |
| 파일 개수 | 항상 1개만 유지 | 여러 개 가능 |
| 교체 방식 | 기존 파일 서버 삭제 후 새 파일 업로드 | 기존 파일 유지하고 새 파일 추가 |

#### 실제 사용 예시

**회사 소개 이미지:**
```html
<label for="title_image_upload" class="position-relative d-block pointer w-100">
    <div id="title_image_url"
         data-single="true"
         data-delete-icon="show"
         data-default-files="<?= $company->title_image_url ?>"></div>
    <input id="title_image_upload" type="file" accept="image/*"
           onchange="handle_file_change(event, { id: 'title_image_url'})" />
</label>
```

**회사 로고 (추가 커스텀 클래스):**
```html
<label for="logo_upload" class="position-relative d-block pointer company-logo-upload">
    <div id="logo_url"
         data-single="true"
         data-delete-icon="show"
         data-default-files="<?= $company->logo_url ?>"></div>
    <input id="logo_upload" type="file" accept="image/*"
           onchange="handle_file_change(event, { id: 'logo_url'})" />
</label>
```

**사업자 등록증 업로드:**
```html
<label for="business_license_upload" class="position-relative d-block pointer">
    <div id="business_license_url"
         data-single="true"
         data-delete-icon="show"
         data-default-files="<?= $company->business_license_url ?>"></div>
    <input id="business_license_upload" type="file" accept="image/*"
           onchange="handle_file_change(event, { id: 'business_license_url'})" />
</label>
```

### 삭제 버튼 추가 (수동 구현)

Single 모드에서 `data-delete-icon="show"`를 사용하지 않고 별도로 삭제 버튼을 구현하려면 다음과 같이 한다.

#### 기본 삭제 버튼

```html
<div class="position-relative" style="width: 150px; height: 150px;">
    <label class="pointer">
        <div id="profile-photo" data-single="true"></div>
        <input type="file" class="d-none"
               onchange="handle_file_change(event, { id: 'profile-photo' })">
    </label>

    <!-- 오른쪽 위에 삭제 버튼 -->
    <button class="position-absolute top-0 end-0 btn btn-sm"
            type="button"
            style="background: none; border: none;"
            onclick="delete_file(get_hidden_input_value('profile-photo'), {id: 'profile-photo'});">
        <i class="fa-solid fa-xmark text-danger" style="font-size: 1.5em;"></i>
    </button>
</div>
```

#### 삭제 버튼 숨기기/보이기

프로필 사진이 없을 때는 삭제 버튼을 숨기고, 업로드/삭제 시 자동으로 표시/숨김 처리할 수 있다.

```html
<div class="position-relative" style="width: 150px; height: 150px;">
    <label class="pointer">
        <div id="profile-photo" data-single="true"></div>
        <input type="file" class="d-none" accept="image/*"
               onchange="handle_file_change(event, {
                   id: 'profile-photo',
                   on_uploaded: on_uploaded
               })">
    </label>

    <!-- 삭제 버튼: 초기에는 사진이 있을 때만 표시 -->
    <button class="delete-btn <?= $photo_url ? '' : 'd-none' ?> position-absolute top-0 end-0"
            type="button"
            style="background: none; border: none;"
            onclick="delete_file(get_hidden_input_value('profile-photo'), {
                id: 'profile-photo',
                on_deleted: on_deleted
            });">
        <i class="fa-solid fa-xmark text-danger" style="font-size: 1.5em;"></i>
    </button>
</div>

<script>
    function on_uploaded(json) {
        if (json && json.url) {
            // 서버에 저장
            savePhotoUrl(json.url);
            // 삭제 버튼 표시
            document.querySelector('.delete-btn').classList.remove('d-none');
        }
    }

    function on_deleted(json) {
        if (json && json.deleted) {
            // 서버에서 제거
            savePhotoUrl('');
            // 삭제 버튼 숨김
            document.querySelector('.delete-btn').classList.add('d-none');
        }
    }
</script>
```

**주요 포인트:**
- 초기 렌더링 시: PHP 조건문으로 `d-none` 클래스 제어
- 업로드 후: `on_uploaded` 콜백에서 `classList.remove('d-none')`
- 삭제 후: `on_deleted` 콜백에서 `classList.add('d-none')`

**get_hidden_input_value() 함수:**
- displayArea의 hidden input 값을 가져오는 유틸리티 함수
- `file-upload.js`에 정의되어 있음
- 첫 번째 파라미터: displayArea의 ID

**delete_file() 함수 파라미터:**
- 첫 번째: 삭제할 파일 URL
- 두 번째: extra 객체 `{id: displayAreaId, on_deleted: callback}`

## 콜백 함수 (on_uploaded, on_deleted)

콜백 함수를 사용하면 파일 업로드/삭제 후 추가 작업을 수행할 수 있다.

### on_uploaded 콜백

파일 업로드가 성공적으로 완료된 후 호출된다.

```javascript
function on_uploaded(json) {
    console.log('Upload success:', json);
    // json.url: 업로드된 파일의 URL
    // json.qr_code: QR 코드 디코드 결과 (decodeQrCode: true인 경우)

    if (json && json.url) {
        // 서버에 프로필 사진 URL 저장
        update_my_profile({
            photo_url: json.url
        });

        // 또는 다른 작업 수행
        showSuccessMessage('프로필 사진이 변경되었습니다.');
    }
}

// 콜백 함수를 extra 파라미터로 전달
handle_file_change(event, {
    id: 'profile-photo',
    on_uploaded: on_uploaded
});
```

### on_deleted 콜백

파일 삭제가 완료된 후 호출된다.

```javascript
function on_deleted(json) {
    console.log('Delete success:', json);
    // json.deleted: 삭제 성공 여부

    if (json && json.deleted) {
        // 서버에서 프로필 사진 URL 제거
        update_my_profile({
            photo_url: ''
        });

        // 또는 다른 작업 수행
        showSuccessMessage('프로필 사진이 삭제되었습니다.');
    }
}

// delete_file 함수의 세 번째 파라미터로 전달
delete_file(url, displayAreaId, on_deleted);
```

### 콜백 함수 파라미터

**on_uploaded 콜백:**
```javascript
{
    url: string,        // 업로드된 파일의 URL
    qr_code: string     // QR 코드 디코드 결과 (옵션)
}
```

**on_deleted 콜백:**
```javascript
{
    deleted: boolean    // 삭제 성공 여부
}
```

### 실제 사용 예제

```javascript
// 프로필 사진 업로드 완료 시
function on_profile_uploaded(json) {
    if (!json || !json.url) return;

    // 1. 서버에 저장
    axios.post('/api/user/update', {
        photo_url: json.url
    }).then(response => {
        // 2. 성공 메시지 표시
        alert('프로필 사진이 변경되었습니다.');

        // 3. 다른 UI 업데이트
        document.querySelector('.header-avatar').src = json.url;
    });
}

// 프로필 사진 삭제 시
function on_profile_deleted(json) {
    if (!json || !json.deleted) return;

    // 1. 서버에서 제거
    axios.post('/api/user/update', {
        photo_url: ''
    }).then(response => {
        // 2. 기본 아바타로 변경
        document.querySelector('.header-avatar').src = '/img/default-avatar.png';
        alert('프로필 사진이 삭제되었습니다.');
    });
}
```

### 콜백 없이 사용하기

콜백 함수는 선택사항이다. 필요 없으면 생략할 수 있다.

```javascript
// 콜백 없이 단순 업로드
handle_file_change(event, {
    id: 'profile-photo'
});

// 콜백 없이 단순 삭제
delete_file(url, displayAreaId);
```

## CSS 스타일링 가이드

### 정사각형 프로필 사진

```css
#profile-photo {
    width: 200px;
    height: 200px;
    border-radius: 8px;  /* 둥근 모서리 */
}

#profile-photo .uploaded-files {
    width: 100%;
    height: 100%;
    border-radius: 8px;
}

#profile-photo .uploaded-files img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
```

### 직사각형 배너 이미지

```css
#banner-image {
    width: 100%;
    max-width: 800px;
    height: 200px;
}

#banner-image .uploaded-files {
    width: 100%;
    height: 100%;
}

#banner-image .uploaded-files img {
    width: 100%;
    height: 100%;
    object-fit: cover;  /* 또는 contain */
}
```

### 테두리와 배경 추가

```css
#profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 3px solid #007bff;  /* 파란색 테두리 */
    background-color: #f8f9fa;   /* 회색 배경 */
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);  /* 그림자 */
}
```

## 주의사항

### 1. data-single="true" 필수

HTML 속성에 반드시 `data-single="true"`를 지정해야 single 모드로 동작한다.

```html
<!-- ✅ 올바른 예 -->
<div id="profile-photo" data-single="true"></div>

<!-- ❌ 잘못된 예 - 여러 파일 모드로 동작 -->
<div id="profile-photo"></div>
```

### 2. accept="image/*" 권장

이미지만 선택하도록 제한하는 것이 좋다.

```html
<input type="file" accept="image/*"
       onchange="handle_file_change(event, { id: 'profile-photo' })">
```

### 3. 파일 타입 검사 없음

Single 모드에서는 파일 타입 검사 없이 무조건 `<img>` 태그로 렌더링된다. 이미지가 아닌 파일을 업로드하면 broken image가 표시될 수 있다.

### 4. Progress Bar 위치

타원형이나 특수한 레이아웃에서는 progress bar 위치를 CSS로 조정해야 한다.

```css
#profile-photo .file-upload-progress-container {
    position: relative;
    margin-top: 160px !important;
}
```

### 5. 삭제 버튼 직접 구현

Single 모드에서는 자동 삭제 버튼이 표시되지 않으므로 필요한 경우 별도로 구현해야 한다.

### 6. 서버 측 deleteFile 처리

서버는 `deleteFile` 파라미터를 받아 기존 파일을 삭제해야 한다.

```php
// PHP 예제
if (isset($_POST['deleteFile']) && !empty($_POST['deleteFile'])) {
    $oldFile = $_POST['deleteFile'];
    // 파일 삭제 로직
    unlink($oldFile);
}
```

# 업로드 진행 상태 표시

파일 업로드가 진행될 때, 표시 영역(Display Area)에 Bootstrap progress-bar가 자동으로 추가되어 업로드 진행률을 표시한다.

**동작 방식:**
1. 파일 업로드가 시작되면 표시 영역에 progress-bar가 추가된다.
2. Axios의 `onUploadProgress` 콜백을 통해 실시간으로 진행률을 업데이트한다.
3. 여러 파일 업로드 시 모든 파일 업로드가 완료될 때까지 progress-bar가 유지된다.
4. 마지막 파일 업로드가 완료되면 500ms 후에 progress-bar가 자동으로 제거된다.

**생성되는 HTML 구조:**

```html
<div class="mt-2 file-upload-progress-container">
    <div class="progress" role="progressbar" aria-label="File upload progress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar" style="width: 0%">0%</div>
    </div>
</div>
```

# Hidden Input으로 URL 전송

display-area 요소의 `data-input-name` 속성을 사용하면 업로드된 파일의 URL을 자동으로 hidden input에 저장할 수 있다. 이는 폼 제출 시 서버로 파일 URL을 전송하는 데 유용하다.

## 동작 방식

1. display-area에 `data-input-name` 속성이 지정되면 해당 이름의 `<input type="hidden">` 요소가 자동으로 생성된다.
2. 파일 업로드가 완료될 때마다 URL이 hidden input의 value에 추가된다.
3. 여러 파일의 URL은 콤마(`,`)로 구분되어 저장된다.

## 사용 예제

```html
<form action="/submit" method="POST">
    <input type="file" multiple onchange="handle_file_change(event, {
        id: 'display-uploaded-files'
    })">

    <div id="display-uploaded-files" data-input-name="files">
        <!-- 자동으로 생성됨: -->
        <!-- <input type="hidden" name="files" value="https://a.com/abc.jpeg,https://a.com/def.webp"> -->
    </div>

    <button type="submit">제출</button>
</form>
```

## 동작 단계별 예시

**1단계: 첫 번째 파일 업로드 완료**

```html
<input type="hidden" name="files" value="https://a.com/abc.jpeg">
```

**2단계: 두 번째 파일 업로드 완료**

```html
<input type="hidden" name="files" value="https://a.com/abc.jpeg,https://a.com/def.webp">
```

**3단계: 세 번째 파일 업로드 완료**

```html
<input type="hidden" name="files" value="https://a.com/abc.jpeg,https://a.com/def.webp,https://a.com/ghi.png">
```

## 서버에서 받는 방법

폼 제출 시 `files` 파라미터로 모든 URL이 콤마로 구분되어 전송된다.

```php
// PHP 예제
$files = $_POST['files']; // "https://a.com/abc.jpeg,https://a.com/def.webp,https://a.com/ghi.png"
$fileUrls = explode(',', $files); // 배열로 분리
```

## 주요 함수

**`add_url_to_hidden_input(displayAreaId, url)`**

- display-area의 `data-input-name` 속성 값을 읽어서 hidden input의 name을 결정
- display-area 내에서 해당 name의 `input[type="hidden"]` 검색
- 없으면 새로 생성하여 display-area 상단에 추가
- 기존 value에 새 URL을 콤마로 구분하여 추가

# 파일 삭제 기능

업로드된 파일은 자동으로 삭제 버튼이 추가되며, 사용자가 클릭하면 다음과 같이 동작한다.

## 동작 방식

1. **삭제 확인 대화상자**: 사용자가 삭제 버튼을 클릭하면 확인 대화상자가 표시됨
2. **서버 API 호출**: Axios를 사용하여 파일 삭제 API 호출 (`appConfig.api.file_delete_url`)
3. **Hidden Input 업데이트**: 서버 삭제 성공 여부와 관계없이 hidden input에서 해당 URL 제거
4. **UI 재렌더링**: `display_uploaded_files()` 함수를 호출하여 전체 파일 목록 재렌더링

## 삭제 버튼 위치

각 파일 아이템의 **좌측 상단**에 빨간색 X 버튼이 표시된다.

```html
<!-- 자동 생성되는 삭제 버튼 -->
<button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-0 m-1">
    <i class="fa-solid fa-xmark"></i>
</button>
```

## 삭제 함수 시그니처

```javascript
async function delete_file(url, displayAreaId)
```

**파라미터:**
- `url` (string): 삭제할 파일의 URL
- `displayAreaId` (string): display-area의 HTML ID (display-area의 `data-input-name` 속성을 통해 hidden input name을 자동으로 찾음)

## 삭제 동작 예시

**삭제 전 상태:**

```html
<div id="display-uploaded-files">
    <input type="hidden" name="files" value="url1.jpg,url2.png,url3.webp">
    <nav class="uploaded-files">
        <div class="row g-2">
            <div class="col-3"><!-- url1.jpg --></div>
            <div class="col-3"><!-- url2.png --></div>
            <div class="col-3"><!-- url3.webp --></div>
        </div>
    </nav>
</div>
```

**url2.png 삭제 후:**

```html
<div id="display-uploaded-files">
    <input type="hidden" name="files" value="url1.jpg,url3.webp">
    <nav class="uploaded-files">
        <div class="row g-2">
            <div class="col-3"><!-- url1.jpg --></div>
            <div class="col-3"><!-- url3.webp --></div>
        </div>
    </nav>
</div>
```

## 서버 API 요청 형식

```javascript
// GET 요청 (URL 파라미터로 전송)
axios.get(appConfig.api.file_delete_url, {
    params: {
        url: 'https://example.com/file.jpg',
        uid: 'firebase-user-uid'
    }
})
```

## 에러 처리

- 서버 삭제 실패 시에도 클라이언트 측 목록에서는 제거됨
- 에러 발생 시 사용자에게 상세한 alert 메시지 표시
- 콘솔에 전체 에러 객체 로그 출력

**에러 메시지 우선순위:**

1. `e.response.data` (문자열인 경우)
2. `e.response.data.error` (error 필드)
3. `e.response.data.message` (message 필드)
4. `JSON.stringify(e.response.data)` (객체 전체)
5. `e.message` (Axios 에러 메시지)

**예시:**

```javascript
// 서버 응답이 { error: "URL과 UID 값이 전달되지 않았습니다." } 인 경우
alert('파일 삭제 실패: URL과 UID 값이 전달되지 않았습니다.');

// 서버 응답이 문자열 "Permission denied" 인 경우
alert('파일 삭제 실패: Permission denied');
```

## 주의사항

- **서버 삭제 실패해도 계속 진행**: 네트워크 오류나 서버 오류가 발생해도 클라이언트 측에서는 파일이 제거됨
- **차분 업데이트**: 삭제 후 해당 파일만 화면에서 제거되며 다른 파일들은 깜빡임 없이 유지됨
- **Firebase 인증 필수**: `firebase.auth().currentUser.uid`가 필요하므로 로그인 상태여야 함

# 차분 업데이트 (화면 깜빡임 최소화)

파일 업로드/삭제 시 전체 화면을 다시 그리지 않고, 변경된 부분만 업데이트하는 **차분 업데이트(Differential Update)** 방식을 사용한다.

## 동작 원리

1. **기존 상태 파악**: 현재 화면에 표시된 파일 URL 목록을 가져옴
2. **새 상태 파악**: hidden input의 최신 URL 목록을 가져옴
3. **차이 계산**:
   - 삭제할 URL: 기존에는 있지만 새 목록에는 없는 URL
   - 추가할 URL: 새 목록에는 있지만 기존에는 없는 URL
4. **최소 업데이트**: 삭제/추가할 파일만 DOM 조작

## 장점

- **화면 깜빡임 최소화**: 기존 파일은 그대로 유지되므로 깜빡임이 없음
- **성능 향상**: 전체 재렌더링 대신 필요한 부분만 업데이트
- **사용자 경험 개선**: 부드러운 UI 전환

## 동작 예시

**초기 상태 (3개 파일)**
```
[img1.jpg] [img2.jpg] [img3.jpg]
```

**새 파일 업로드 (img4.jpg 추가)**
```
[img1.jpg] [img2.jpg] [img3.jpg] [img4.jpg]  ← img4만 추가됨 (깜빡임 없음)
```

**파일 삭제 (img2.jpg 제거)**
```
[img1.jpg] [img3.jpg] [img4.jpg]  ← img2만 제거됨 (다른 파일 깜빡임 없음)
```

## 기술적 구현

```javascript
// 기존 파일 목록
const existingUrls = ['img1.jpg', 'img2.jpg', 'img3.jpg'];

// 새 파일 목록
const newUrls = ['img1.jpg', 'img3.jpg', 'img4.jpg'];

// 삭제할 파일: img2.jpg
const urlsToRemove = existingUrls.filter(url => !newUrls.includes(url));

// 추가할 파일: img4.jpg
const urlsToAdd = newUrls.filter(url => !existingUrls.includes(url));

// img2.jpg만 DOM에서 제거
urlsToRemove.forEach(url => element.remove());

// img4.jpg만 DOM에 추가
urlsToAdd.forEach(url => element.append());
```

## 전체 재렌더링과 비교

| 항목 | 전체 재렌더링 | 차분 업데이트 |
|------|-------------|-------------|
| 화면 깜빡임 | ❌ 모든 파일 깜빡임 | ✅ 깜빡임 없음 |
| DOM 조작 | 🔴 전체 삭제 후 재생성 | 🟢 필요한 부분만 수정 |
| 성능 | ⚠️ 파일 많을수록 느림 | ✅ 항상 빠름 |
| 이미지 로딩 | ❌ 매번 다시 로드 | ✅ 기존 이미지 유지 |

# 기본 파일 표시 (data-default-files)

DB에 저장되어 있는 파일 URL 목록을 페이지 로드 시 자동으로 표시할 수 있다. 이 기능은 게시글 수정 시 기존에 업로드했던 파일들을 미리 보여주는 데 유용하다.

## 동작 방식

1. display-area에 `data-default-files` 속성을 추가하고 콤마(`,`)로 구분된 URL 목록을 지정한다.
2. 페이지 로드 시 `file-upload.js`가 자동으로 이 속성을 읽어서 파일들을 display-area에 렌더링한다.
3. hidden input이 설정되어 있으면 자동으로 URL 목록을 value에 추가한다.
4. 사용자가 파일을 추가로 업로드하면 기존 파일과 함께 표시된다.

## 사용 예제

### 기본 사용법

```html
<!-- DB에서 가져온 파일 URL 배열 -->
<?php
$post = get_post($idx); // 예: ['url1.jpg', 'url2.png', 'url3.webp']
?>

<!-- display-area에 data-default-files 속성 추가 -->
<div id="display-uploaded-files" data-default-files="<?= implode(',', $post->files) ?>"></div>
```

### Hidden Input과 함께 사용

```html
<form action="/post/update.php" method="POST">
    <input type="file" multiple onchange="handle_file_change(event, {
        id: 'display-uploaded-files'
    })">

    <!-- DB의 파일 URL을 기본값으로 표시 -->
    <div id="display-uploaded-files"
         data-input-name="files"
         data-default-files="<?= implode(',', $post->files) ?>">
        <!-- 페이지 로드 시 자동 생성: -->
        <!-- <input type="hidden" name="files" value="url1.jpg,url2.png,url3.webp"> -->
        <!-- 각 파일의 미리보기 이미지 자동 표시 -->
    </div>

    <button type="submit" class="btn btn-primary">수정 완료</button>
</form>
```

### 게시글 수정 완전한 예제

```php
<?php
// 게시글 데이터 가져오기
$post = PostModel::findByPk($idx);
// $post->files = ['https://example.com/image1.jpg', 'https://example.com/image2.png'];
?>

<form action="/post/update.php" method="POST">
    <input type="hidden" name="idx" value="<?= $post->idx ?>">

    <label class="pointer">
        <i class="fa-solid fa-images"></i> 파일 추가
        <input type="file" multiple style="display: none;"
               onchange="handle_file_change(event, {
                   id: 'display-uploaded-files'
               })">
    </label>

    <!-- 기존 파일 자동 표시 -->
    <div id="display-uploaded-files"
         data-input-name="files"
         data-default-files="<?= implode(',', $post->files) ?>"
         class="mt-3">
        <!-- 자동으로 기존 파일들이 여기에 렌더링됨 -->
    </div>

    <textarea name="content"><?= $post->content ?></textarea>
    <button type="submit" class="btn btn-primary">수정</button>
</form>
```

## 동작 단계별 설명

**1단계: 페이지 로드 시**

```html
<!-- HTML 렌더링 결과 -->
<div id="display-uploaded-files" data-default-files="url1.jpg,url2.png"></div>
```

**2단계: 자바스크립트가 자동으로 파일 렌더링**

```html
<div id="display-uploaded-files" data-default-files="url1.jpg,url2.png">
    <!-- hidden input 자동 생성 (name이 지정된 경우) -->
    <input type="hidden" name="files" value="url1.jpg,url2.png">

    <!-- 각 파일의 미리보기 자동 생성 -->
    <div class="file-item">
        <img src="url1.jpg" alt="Uploaded file">
        <button onclick="delete_file('url1.jpg')">삭제</button>
    </div>
    <div class="file-item">
        <img src="url2.png" alt="Uploaded file">
        <button onclick="delete_file('url2.png')">삭제</button>
    </div>
</div>
```

**3단계: 사용자가 새 파일 업로드**

```html
<div id="display-uploaded-files" data-default-files="url1.jpg,url2.png">
    <!-- hidden input 업데이트 -->
    <input type="hidden" name="files" value="url1.jpg,url2.png,url3.webp">

    <!-- 기존 파일 + 새 파일 -->
    <div class="file-item">...</div> <!-- url1.jpg -->
    <div class="file-item">...</div> <!-- url2.png -->
    <div class="file-item">...</div> <!-- url3.webp (새로 추가) -->
</div>
```

## 파일 삭제 시 동작

사용자가 기본 파일 중 하나를 삭제하면:

1. 화면에서 해당 파일 미리보기가 제거됨
2. hidden input의 value에서 해당 URL이 제거됨
3. 서버로 전송 시 삭제된 파일 URL은 포함되지 않음

```html
<!-- 삭제 전 -->
<input type="hidden" name="files" value="url1.jpg,url2.png,url3.webp">

<!-- url2.png 삭제 후 -->
<input type="hidden" name="files" value="url1.jpg,url3.webp">
```

## 주의사항

- **URL 형식**: 콤마(`,`)로 구분된 완전한 URL 또는 상대 경로를 사용
- **특수문자**: URL에 콤마가 포함되어 있으면 안 됨 (구분자로 사용됨)
- **빈 배열**: DB에 파일이 없으면 빈 문자열(`""`) 전달
- **인코딩**: PHP의 `implode(',', $array)`로 안전하게 문자열 생성

## 서버에서 파일 업데이트 처리

```php
// post/update.php

// 폼에서 전송된 파일 URL 목록
$files = $_POST['files']; // "url1.jpg,url3.webp,url4.png"

// 배열로 변환
$fileArray = !empty($files) ? explode(',', $files) : [];

// DB 업데이트
$post = PostModel::findByPk($_POST['idx']);
$post->files = $fileArray;
$post->save();
```

# 전체 구현 예제

다음은 파일 업로드 기능을 완전히 구현한 예제다.

## 기본 예제

```html
<!-- 파일 업로드 폼 -->
<form>
    <!-- 커스텀 디자인의 파일 입력 버튼 -->
    <label class="pointer">
        <i class="fa-solid fa-camera" style="font-size: 2em;"></i>
        <input
            type="file"
            multiple
            style="display: none;"
            onchange="handle_file_change(event, {
                id: 'my-upload-area'
            })">
    </label>

    <!-- 업로드된 파일 표시 영역 -->
    <div id="my-upload-area" class="mt-3">
        <!-- 업로드된 파일들이 여기에 자동으로 표시됨 -->
    </div>

    <!-- 제출 버튼 -->
    <button type="submit" class="btn btn-primary mt-3">작성 완료</button>
</form>
```

## Hidden Input 포함 예제

```html
<!-- 파일 URL을 서버로 전송하는 폼 -->
<form action="/submit-post" method="POST">
    <label class="pointer">
        <i class="fa-solid fa-images" style="font-size: 2em;"></i>
        <input
            type="file"
            multiple
            style="display: none;"
            onchange="handle_file_change(event, {
                id: 'post-images'
            })">
    </label>

    <div id="post-images" data-input-name="images" class="mt-3">
        <!-- 자동 생성: <input type="hidden" name="images" value="url1,url2,url3"> -->
        <!-- 업로드된 파일 목록 -->
    </div>

    <textarea name="content" placeholder="내용을 입력하세요"></textarea>
    <button type="submit" class="btn btn-primary">게시글 작성</button>
</form>
```

## 게시글 수정 시 기본 파일 포함 예제

```html
<?php
// 게시글 데이터 가져오기
$post = PostModel::findByPk($idx);
?>

<!-- 파일 URL을 서버로 전송하는 폼 (수정 모드) -->
<form action="/post/update.php" method="POST">
    <input type="hidden" name="idx" value="<?= $post->idx ?>">

    <label class="pointer">
        <i class="fa-solid fa-images" style="font-size: 2em;"></i>
        <input
            type="file"
            multiple
            style="display: none;"
            onchange="handle_file_change(event, {
                id: 'post-images'
            })">
    </label>

    <!-- DB에 저장된 기본 파일 자동 표시 -->
    <div id="post-images"
         data-input-name="images"
         data-default-files="<?= implode(',', $post->files) ?>"
         class="mt-3">
        <!-- 자동 생성: <input type="hidden" name="images" value="기존url1,기존url2"> -->
        <!-- 기존 파일 미리보기 자동 표시 -->
        <!-- 새로 업로드한 파일도 추가로 표시됨 -->
    </div>

    <textarea name="content" placeholder="내용을 입력하세요"><?= $post->content ?></textarea>
    <button type="submit" class="btn btn-primary">게시글 수정</button>
</form>
```

**주요 포인트:**
- `multiple` 속성으로 여러 파일 동시 선택 가능
- `id: 'post-images'`와 `<div id="post-images">`가 서로 연결됨
- `data-input-name="images"` 속성으로 hidden input의 name이 자동 설정됨
- `data-default-files` 속성으로 DB의 기존 파일을 자동 표시
- 업로드 진행 상태는 Bootstrap progress-bar로 자동 표시됨
- 업로드된 파일은 표시 영역에 자동으로 렌더링됨
- 폼 제출 시 `images` 파라미터로 모든 파일 URL이 전송됨 (기존 파일 + 새 파일)