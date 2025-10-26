# 파일 업로드 (File Upload) - Vue.js 컴포넌트

파일 업로드 기능을 제공하는 Vue.js 컴포넌트입니다. 이미지, 비디오 등 다양한 파일을 업로드하고 관리할 수 있습니다.

## 목차

- [개요](#개요)
- [설치 및 설정](#설치-및-설정)
- [기본 사용법](#기본-사용법)
- [컴포넌트 Props](#컴포넌트-props)
- [컴포넌트 이벤트](#컴포넌트-이벤트)
- [업로드 버튼 타입 커스터마이징](#업로드-버튼-타입-커스터마이징)
- [커스텀 Progress Bar](#커스텀-progress-bar)
- [단일 파일 업로드](#단일-파일-업로드)
- [다중 파일 업로드](#다중-파일-업로드)
- [이미지 갤러리](#이미지-갤러리)
- [프로필 사진 업로드](#프로필-사진-업로드)
- [게시글 파일 첨부](#게시글-파일-첨부)
- [업로드 버튼과 파일 미리보기 분리](#업로드-버튼과-파일-미리보기-분리)
- [v-model 사용법](#v-model-사용법)
- [Hidden Input 활용](#hidden-input-활용)
- [파일 삭제](#파일-삭제)
- [QR 코드 디코딩](#qr-코드-디코딩)
- [고급 사용법](#고급-사용법)
- [CSS 커스터마이징](#css-커스터마이징)
- [문제 해결](#문제-해결)

## 개요

`FileUploadComponent`는 Vue.js 3.x를 사용한 파일 업로드 컴포넌트입니다.
- **단일/다중 파일 업로드** 지원
- **진행률 표시**
- **파일 미리보기** (이미지, 비디오)
- **파일 삭제**
- **v-model 지원**
- **QR 코드 디코딩**

### 주요 특징

- ✅ Vue.js Options API 기반
- ✅ Props와 Events를 통한 통신
- ✅ Bootstrap 5 스타일링
- ✅ 반응형 디자인
- ✅ TypeScript 친화적 Props 정의

## 설치 및 설정

### 1단계: 컴포넌트 로드

```php
<?php
// PHP 페이지 상단에서 컴포넌트 로드
load_deferred_js('vue-components/file-upload.component');
?>
```

### 2단계: Vue 앱에서 컴포넌트 등록

```javascript
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                uploadedFiles: []
            };
        }
    }).mount('#app');
});
```

## 기본 사용법

### 가장 간단한 예제

```html
<div id="app">
    <!-- 파일 업로드 컴포넌트 -->
    <file-upload
        input-name="attachments"
        @uploaded="handleUpload">
    </file-upload>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        methods: {
            handleUpload(data) {
                console.log('파일 업로드 완료:', data.url);
            }
        }
    }).mount('#app');
});
</script>
```

## 컴포넌트 Props

| Prop | 타입 | 기본값 | 설명 |
|------|------|--------|------|
| `single` | Boolean | `false` | 단일 파일 모드 (true: 기존 파일 자동 교체) |
| `inputName` | String | `''` | Hidden input의 name 속성 |
| `showUploadButton` | Boolean | `true` | 업로드 버튼 표시 여부 |
| `uploadButtonType` | String | `'default'` | 업로드 버튼 타입 (`"default"` 또는 `"camera-icon"`) |
| `showUploadedFiles` | Boolean | `true` | 업로드된 파일 미리보기 표시 여부 |
| `showDeleteIcon` | Boolean | `false` | 삭제 아이콘 표시 여부 |
| `showProgressBar` | Boolean | `true` | Progress bar 표시 여부 |
| `decodeQrCode` | Boolean | `false` | QR 코드 디코딩 활성화 |
| `accept` | String | `'*'` | 파일 타입 제한 (예: `"image/*"`) |
| `defaultFiles` | Array | `[]` | 기본 파일 URL 목록 |

### Props 사용 예제

```html
<file-upload
    :single="true"
    input-name="profile_photo"
    :show-upload-button="true"
    :show-delete-icon="true"
    :decode-qr-code="false"
    accept="image/*"
    :default-files="['/uploads/photo.jpg']">
</file-upload>
```

## 컴포넌트 이벤트

| 이벤트 | 페이로드 | 설명 |
|--------|---------|------|
| `@uploaded` | `{ url: string, qr_code?: string }` | 파일 업로드 완료 시 발생 |
| `@deleted` | `Object` | 파일 삭제 완료 시 발생 |
| `@update:modelValue` | `Array<string>` | v-model 업데이트 시 발생 |
| `@uploading-progress` | `{ progress: number, uploading: boolean }` | 업로드 진행률 변경 시 발생 |

### 이벤트 처리 예제

```javascript
methods: {
    handleUpload(data) {
        console.log('업로드된 파일 URL:', data.url);
        if (data.qr_code) {
            console.log('QR 코드 데이터:', data.qr_code);
        }
    },

    handleDelete(data) {
        console.log('파일 삭제됨:', data);
    }
}
```

## 업로드 버튼 타입 커스터마이징

`upload-button-type` prop을 사용하여 업로드 버튼의 스타일을 변경할 수 있습니다.

### 사용 가능한 타입

| 타입 | 설명 | 아이콘 | 텍스트 |
|------|------|--------|--------|
| `"default"` | 기본 버튼 스타일 | `bi bi-upload` | "파일 선택" 또는 "파일들 선택" |
| `"camera-icon"` | 카메라 아이콘만 표시 | `fa-solid fa-camera` | 없음 (아이콘만) |

### 기본 업로드 버튼 (default)

기본 설정으로, 파일 선택 버튼이 텍스트와 함께 표시됩니다.

```html
<file-upload
    :single="false"
    upload-button-type="default"
    accept="image/*">
</file-upload>

<!-- 또는 prop을 생략하면 기본값 사용 -->
<file-upload
    :single="false"
    accept="image/*">
</file-upload>
```

**결과**: `📤 파일들 선택` 버튼이 표시됩니다.

### 카메라 아이콘 버튼 (camera-icon)

카메라 아이콘만 표시되며, 텍스트 없이 간결한 UI를 제공합니다. 주로 댓글/답글 작성 Modal이나 채팅 UI에서 사용됩니다.

```html
<file-upload
    :single="false"
    upload-button-type="camera-icon"
    accept="image/*">
</file-upload>
```

**결과**: `📷` 카메라 아이콘만 표시됩니다 (텍스트 없음).

### Modal에서 카메라 아이콘 사용 예제

댓글/답글 작성 시 카메라 아이콘으로 파일을 업로드하는 예제입니다.

```html
<!-- Bootstrap Modal -->
<div class="modal fade" id="commentModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">댓글 작성</h5>
            </div>

            <div class="modal-body">
                <!-- 댓글 내용 입력 -->
                <textarea v-model="content" class="form-control mb-3" rows="4"></textarea>

                <!-- 하단 액션 바: 카메라 아이콘 + 버튼들 -->
                <div class="d-flex justify-content-between align-items-center">
                    <!-- 왼쪽: 카메라 아이콘으로 파일 업로드 -->
                    <div>
                        <file-upload
                            :single="false"
                            :show-uploaded-files="false"
                            upload-button-type="camera-icon"
                            accept="image/*"
                            @uploaded="handleFileUploaded">
                        </file-upload>
                    </div>

                    <!-- 오른쪽: 취소/전송 버튼 -->
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button class="btn btn-sm btn-primary" @click="submit">
                            Send
                        </button>
                    </div>
                </div>

                <!-- 업로드된 파일 미리보기 -->
                <div v-if="files.length > 0" class="row g-2 mt-3">
                    <div v-for="(url, i) in files" :key="i" class="col-4">
                        <img :src="url" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                content: '',
                files: []
            };
        },
        methods: {
            handleFileUploaded(data) {
                this.files.push(data.url);
            },
            submit() {
                // 댓글 전송 로직
            }
        }
    }).mount('#app');
});
</script>
```

### CSS 커스터마이징

카메라 아이콘 버튼의 스타일을 커스터마이징할 수 있습니다.

```css
/* 카메라 아이콘 크기 및 색상 변경 */
.file-upload-camera-icon {
    font-size: 1.5rem;
    color: #0d6efd;
    cursor: pointer;
    transition: color 0.2s;
}

.file-upload-camera-icon:hover {
    color: #0a58ca;
}

/* 아이콘 주변 여백 조정 */
.file-upload-camera-icon {
    padding: 0.5rem;
}
```

### 사용 사례

**✅ `upload-button-type="default"` 사용 케이스:**
- 프로필 사진 업로드
- 게시글 작성 폼
- 파일 첨부가 주요 기능인 경우
- 사용자에게 명확한 안내가 필요한 경우

**✅ `upload-button-type="camera-icon"` 사용 케이스:**
- 댓글/답글 작성 Modal
- 채팅 메시지에 파일 첨부
- 공간이 제한된 UI
- 미니멀한 디자인이 필요한 경우
- SNS 스타일의 게시글 작성

### 조합 사용 예제

`:show-uploaded-files="false"`와 함께 사용하여 업로드 버튼과 미리보기를 완전히 분리할 수 있습니다.

```html
<file-upload
    :single="false"
    :show-uploaded-files="false"
    upload-button-type="camera-icon"
    accept="image/*"
    @uploaded="handleFileUploaded">
</file-upload>

<!-- 별도 위치에 파일 미리보기 표시 -->
<div v-if="uploadedFiles.length > 0" class="my-custom-preview">
    <div v-for="url in uploadedFiles" :key="url">
        <img :src="url" class="preview-image">
    </div>
</div>
```

자세한 내용은 [업로드 버튼과 파일 미리보기 분리](#업로드-버튼과-파일-미리보기-분리) 섹션을 참고하세요.

---

## 커스텀 Progress Bar

컴포넌트 내부의 progress bar를 숨기고, 부모 컴포넌트에서 원하는 위치에 커스텀 progress bar를 표시할 수 있습니다.

### 기본 개념

`:show-progress-bar="false"` prop을 설정하여 내부 progress bar를 숨기고, `@uploading-progress` 이벤트를 통해 진행률 정보를 받아 부모 컴포넌트에서 직접 표시합니다.

**핵심 설정:**
- `:show-progress-bar="false"` - 컴포넌트 내부 progress bar 숨김
- `@uploading-progress="handleProgress"` - 업로드 진행률 변경 시 이벤트 수신
- 페이로드: `{ progress: 0-100, uploading: true/false }`

**장점:**
- ✅ Progress bar를 원하는 위치에 자유롭게 배치
- ✅ 커스텀 디자인 및 애니메이션 적용 가능
- ✅ 여러 파일 업로드 시 통합 진행률 표시 가능
- ✅ 업로드 상태에 따른 추가 UI 표시 가능

### 기본 사용 예제

```html
<div id="custom-progress-app">
    <!-- 커스텀 Progress Bar 표시 영역 -->
    <div v-if="isUploading" class="mb-3">
        <div class="alert alert-info">
            <strong>업로드 중...</strong> {{ uploadProgress }}%
        </div>
        <div class="progress" style="height: 30px;">
            <div
                class="progress-bar progress-bar-striped progress-bar-animated"
                role="progressbar"
                :style="{ width: uploadProgress + '%' }"
                :aria-valuenow="uploadProgress"
                aria-valuemin="0"
                aria-valuemax="100">
                {{ uploadProgress }}%
            </div>
        </div>
    </div>

    <!-- File Upload 컴포넌트 (내부 progress bar 숨김) -->
    <file-upload
        :single="false"
        :show-progress-bar="false"
        accept="image/*"
        @uploading-progress="handleUploadProgress"
        @uploaded="handleUploaded">
    </file-upload>

    <!-- 업로드 완료 메시지 -->
    <div v-if="uploadComplete" class="alert alert-success mt-3">
        파일 업로드 완료!
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                uploadProgress: 0,
                isUploading: false,
                uploadComplete: false
            };
        },
        methods: {
            handleUploadProgress(data) {
                // data = { progress: 0-100, uploading: true/false }
                this.uploadProgress = data.progress;
                this.isUploading = data.uploading;
                this.uploadComplete = false;

                console.log('Upload progress:', data.progress + '%');
            },
            handleUploaded(data) {
                console.log('File uploaded:', data.url);
                this.uploadComplete = true;

                // 3초 후 완료 메시지 숨김
                setTimeout(() => {
                    this.uploadComplete = false;
                }, 3000);
            }
        }
    }).mount('#custom-progress-app');
});
</script>
```

### 원형 Progress Bar 예제

원형(Circular) 형태의 progress bar를 표시하는 예제입니다.

```html
<div id="circular-progress-app">
    <!-- 원형 Progress Bar -->
    <div v-if="isUploading" class="text-center mb-4">
        <div class="position-relative d-inline-block" style="width: 150px; height: 150px;">
            <!-- SVG 원형 progress bar -->
            <svg width="150" height="150" style="transform: rotate(-90deg);">
                <!-- 배경 원 -->
                <circle
                    cx="75"
                    cy="75"
                    r="65"
                    fill="none"
                    stroke="#e9ecef"
                    stroke-width="10">
                </circle>
                <!-- 진행률 원 -->
                <circle
                    cx="75"
                    cy="75"
                    r="65"
                    fill="none"
                    stroke="#0d6efd"
                    stroke-width="10"
                    :stroke-dasharray="circumference"
                    :stroke-dashoffset="circumference - (uploadProgress / 100) * circumference"
                    stroke-linecap="round"
                    style="transition: stroke-dashoffset 0.3s;">
                </circle>
            </svg>
            <!-- 진행률 텍스트 -->
            <div class="position-absolute top-50 start-50 translate-middle">
                <h3 class="mb-0">{{ uploadProgress }}%</h3>
                <small class="text-muted">업로드 중</small>
            </div>
        </div>
    </div>

    <!-- File Upload 컴포넌트 -->
    <file-upload
        :show-progress-bar="false"
        @uploading-progress="handleUploadProgress">
    </file-upload>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                uploadProgress: 0,
                isUploading: false,
                // 원의 둘레 계산 (2πr, r=65)
                circumference: 2 * Math.PI * 65
            };
        },
        methods: {
            handleUploadProgress(data) {
                this.uploadProgress = data.progress;
                this.isUploading = data.uploading;
            }
        }
    }).mount('#circular-progress-app');
});
</script>
```

### Modal에서 커스텀 Progress Bar

Modal 상단에 progress bar를 표시하는 예제입니다.

```html
<!-- Bootstrap Modal -->
<div class="modal fade" id="uploadModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">파일 업로드</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Progress Bar (Modal 상단에 고정) -->
            <div v-if="isUploading" class="position-absolute top-0 start-0 w-100" style="z-index: 1050;">
                <div class="progress" style="height: 4px; border-radius: 0;">
                    <div
                        class="progress-bar bg-success"
                        :style="{ width: uploadProgress + '%' }"
                        style="transition: width 0.3s;">
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <file-upload
                    :show-progress-bar="false"
                    :show-uploaded-files="false"
                    @uploading-progress="handleUploadProgress"
                    @uploaded="handleUploaded">
                </file-upload>

                <!-- 업로드 상태 메시지 -->
                <div v-if="isUploading" class="text-center text-muted mt-3">
                    <i class="fa-solid fa-spinner fa-spin me-2"></i>
                    업로드 중... {{ uploadProgress }}%
                </div>
            </div>
        </div>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                uploadProgress: 0,
                isUploading: false
            };
        },
        methods: {
            handleUploadProgress(data) {
                this.uploadProgress = data.progress;
                this.isUploading = data.uploading;
            },
            handleUploaded(data) {
                console.log('Uploaded:', data.url);
                // Modal 닫기
                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                modal.hide();
            }
        }
    }).mount('#app');
});
</script>
```

### 여러 파일 통합 진행률

여러 파일을 업로드할 때 전체 진행률을 계산하여 표시하는 예제입니다.

```html
<div id="multi-progress-app">
    <!-- 전체 진행률 표시 -->
    <div v-if="uploadingFiles > 0" class="mb-3">
        <div class="d-flex justify-content-between mb-2">
            <span>전체 진행률</span>
            <span>{{ completedFiles }}/{{ totalFiles }} 파일</span>
        </div>
        <div class="progress" style="height: 25px;">
            <div
                class="progress-bar"
                :style="{ width: totalProgress + '%' }">
                {{ totalProgress }}%
            </div>
        </div>
        <small class="text-muted">업로드 중인 파일: {{ uploadingFiles }}개</small>
    </div>

    <!-- File Upload 컴포넌트 -->
    <file-upload
        :single="false"
        :show-progress-bar="false"
        @uploading-progress="handleUploadProgress"
        @uploaded="handleUploaded">
    </file-upload>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                totalFiles: 0,
                completedFiles: 0,
                uploadingFiles: 0,
                currentProgress: 0
            };
        },
        computed: {
            totalProgress() {
                if (this.totalFiles === 0) return 0;
                return Math.round(
                    ((this.completedFiles + (this.currentProgress / 100)) / this.totalFiles) * 100
                );
            }
        },
        methods: {
            handleUploadProgress(data) {
                this.currentProgress = data.progress;

                if (data.uploading && data.progress === 0) {
                    // 새 파일 업로드 시작
                    this.totalFiles++;
                    this.uploadingFiles++;
                }

                if (!data.uploading && data.progress === 0 && this.uploadingFiles > 0) {
                    // 파일 업로드 완료
                    this.uploadingFiles--;
                }
            },
            handleUploaded(data) {
                console.log('File uploaded:', data.url);
                this.completedFiles++;
            }
        }
    }).mount('#multi-progress-app');
});
</script>
```

### CSS 커스터마이징

Progress bar의 디자인을 커스터마이징할 수 있습니다.

```css
/* 그라디언트 Progress Bar */
.progress-bar-gradient {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}

/* 애니메이션 효과 */
.progress-bar-animated {
    animation: progress-shine 2s linear infinite;
}

@keyframes progress-shine {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 50px 0;
    }
}

/* 네온 효과 */
.progress-bar-neon {
    background: #0d6efd;
    box-shadow: 0 0 10px #0d6efd, 0 0 20px #0d6efd;
}
```

### 사용 사례

**✅ 커스텀 Progress Bar 사용 케이스:**
- Modal 상단에 얇은 progress bar 표시
- 원형 progress bar로 시각적 효과
- 여러 파일 업로드 시 통합 진행률 표시
- 브랜드 디자인에 맞춘 커스텀 스타일
- 업로드 상태에 따른 추가 정보 표시

**✅ 기본 Progress Bar 사용 케이스:**
- 간단한 파일 업로드 폼
- 별도 커스터마이징이 필요 없는 경우
- 빠른 프로토타이핑

---

## 단일 파일 업로드

단일 파일 모드에서는 새 파일을 업로드하면 기존 파일이 자동으로 교체됩니다.

### 프로필 사진 업로드 예제

```php
<div id="profile-app">
    <h3>프로필 사진</h3>

    <!-- 단일 파일 업로드 컴포넌트 -->
    <file-upload
        :single="true"
        input-name="profile_photo"
        :show-delete-icon="true"
        accept="image/*"
        :default-files="profilePhoto ? [profilePhoto] : []"
        @uploaded="handleProfileUpload"
        @deleted="handleProfileDelete">
    </file-upload>

    <!-- 현재 프로필 사진 URL 표시 -->
    <div v-if="profilePhoto" class="mt-3">
        <p>현재 프로필: {{ profilePhoto }}</p>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                // PHP에서 기존 프로필 사진 URL 전달
                profilePhoto: '<?= $user->photo_url ?? '' ?>'
            };
        },
        methods: {
            async handleProfileUpload(data) {
                console.log('프로필 사진 업로드:', data.url);
                this.profilePhoto = data.url;

                // 서버에 프로필 사진 URL 저장
                try {
                    await func('update_user_profile', {
                        photo_url: data.url,
                        auth: true
                    });
                    alert('프로필 사진이 업데이트되었습니다.');
                } catch (error) {
                    console.error('프로필 업데이트 실패:', error);
                }
            },

            handleProfileDelete(data) {
                console.log('프로필 사진 삭제됨');
                this.profilePhoto = '';
            }
        }
    }).mount('#profile-app');
});
</script>
```

### 배너 이미지 업로드 예제

```html
<div id="banner-app">
    <!-- 배너 이미지 (16:9 비율) -->
    <div class="banner-upload-container" style="max-width: 800px;">
        <file-upload
            :single="true"
            input-name="banner_image"
            :show-delete-icon="true"
            accept="image/*"
            :default-files="bannerImage ? [bannerImage] : []"
            @uploaded="handleBannerUpload">
        </file-upload>
    </div>
</div>

<style>
/* 배너 이미지 16:9 비율 유지 */
.banner-upload-container .single-file-display img {
    width: 100%;
    height: auto;
    aspect-ratio: 16/9;
    object-fit: cover;
}
</style>
```

## 다중 파일 업로드

다중 파일 모드에서는 여러 파일을 동시에 업로드하고 관리할 수 있습니다.

### 갤러리 업로드 예제

```html
<div id="gallery-app">
    <h3>갤러리 이미지</h3>

    <!-- 다중 파일 업로드 컴포넌트 -->
    <file-upload
        :single="false"
        input-name="gallery[]"
        :show-delete-icon="true"
        accept="image/*,video/*"
        :default-files="galleryImages"
        @uploaded="handleGalleryUpload"
        @deleted="handleGalleryDelete">
    </file-upload>

    <!-- 업로드된 파일 개수 표시 -->
    <p class="mt-3">총 {{ galleryImages.length }}개 파일</p>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                // PHP에서 기존 갤러리 이미지 전달
                galleryImages: <?= json_encode($gallery_images ?? []) ?>
            };
        },
        methods: {
            handleGalleryUpload(data) {
                console.log('갤러리 이미지 추가:', data.url);
                this.galleryImages.push(data.url);
            },

            handleGalleryDelete(data) {
                console.log('갤러리 이미지 삭제됨');
                // 컴포넌트가 자동으로 목록을 관리하므로 추가 작업 불필요
            }
        }
    }).mount('#gallery-app');
});
</script>
```

## 이미지 갤러리

### 제품 이미지 갤러리 예제

```html
<div id="product-app">
    <div class="row">
        <div class="col-md-8">
            <h4>제품 이미지</h4>

            <!-- 다중 이미지 업로드 -->
            <file-upload
                :single="false"
                input-name="product_images[]"
                :show-delete-icon="true"
                accept="image/*"
                :default-files="productImages"
                v-model="productImages">
            </file-upload>
        </div>

        <div class="col-md-4">
            <h4>업로드된 이미지 URL</h4>
            <ul class="list-unstyled">
                <li v-for="(url, index) in productImages" :key="index">
                    <small>{{ url }}</small>
                </li>
            </ul>
        </div>
    </div>
</div>
```

## 게시글 파일 첨부

게시글 작성 시 파일 첨부 기능 구현 예제입니다.

### 게시글 작성 폼 예제

```php
<div id="post-create-app">
    <form @submit.prevent="submitPost">
        <!-- 제목 입력 -->
        <div class="mb-3">
            <label class="form-label">제목</label>
            <input type="text" v-model="title" class="form-control" required>
        </div>

        <!-- 내용 입력 -->
        <div class="mb-3">
            <label class="form-label">내용</label>
            <textarea v-model="content" class="form-control" rows="5" required></textarea>
        </div>

        <!-- 파일 첨부 (업로드 버튼 숨김) -->
        <div class="mb-3">
            <label class="form-label">파일 첨부</label>

            <!-- 파일 업로드 컴포넌트 -->
            <file-upload
                ref="fileUpload"
                :single="false"
                input-name="attachments[]"
                :show-upload-button="false"
                :show-delete-icon="true"
                accept="image/*,video/*,.pdf,.doc,.docx"
                v-model="attachments">
            </file-upload>

            <!-- 파일 선택 버튼 (커스텀) -->
            <button type="button" class="btn btn-outline-primary" @click="selectFiles">
                <i class="bi bi-paperclip"></i> 파일 첨부
            </button>

            <!-- 첨부 파일 개수 -->
            <span v-if="attachments.length > 0" class="ms-2 text-muted">
                {{ attachments.length }}개 파일 첨부됨
            </span>
        </div>

        <!-- 전송 버튼 -->
        <button type="submit" class="btn btn-primary" :disabled="submitting">
            <span v-if="submitting">
                <span class="spinner-border spinner-border-sm me-2"></span>
                전송 중...
            </span>
            <span v-else>게시글 작성</span>
        </button>
    </form>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                title: '',
                content: '',
                attachments: [],
                submitting: false
            };
        },
        methods: {
            // 파일 선택 창 열기
            selectFiles() {
                // ref를 통해 컴포넌트 메서드 호출
                this.$refs.fileUpload.openFileSelector();
            },

            // 게시글 전송
            async submitPost() {
                this.submitting = true;

                try {
                    const result = await func('create_post', {
                        title: this.title,
                        content: this.content,
                        attachments: this.attachments,
                        auth: true
                    });

                    alert('게시글이 작성되었습니다.');
                    window.location.href = '<?= href()->post->view ?>' + '?id=' + result.post_id;

                } catch (error) {
                    console.error('게시글 작성 실패:', error);
                    alert('게시글 작성에 실패했습니다.');
                } finally {
                    this.submitting = false;
                }
            }
        }
    }).mount('#post-create-app');
});
</script>
```

### 게시글 수정 예제

```php
<?php
// PHP에서 기존 게시글 데이터 가져오기
$post = get_post(['id' => $_GET['id']]);
$attachments = json_encode($post['attachments'] ?? []);
?>

<div id="post-edit-app">
    <form @submit.prevent="updatePost">
        <!-- 제목 수정 -->
        <div class="mb-3">
            <label class="form-label">제목</label>
            <input type="text" v-model="title" class="form-control" required>
        </div>

        <!-- 내용 수정 -->
        <div class="mb-3">
            <label class="form-label">내용</label>
            <textarea v-model="content" class="form-control" rows="5" required></textarea>
        </div>

        <!-- 기존 첨부 파일 표시 및 수정 -->
        <div class="mb-3">
            <label class="form-label">첨부 파일</label>

            <file-upload
                :single="false"
                input-name="attachments[]"
                :show-delete-icon="true"
                accept="image/*,video/*,.pdf"
                :default-files="attachments"
                v-model="attachments">
            </file-upload>
        </div>

        <!-- 수정 버튼 -->
        <button type="submit" class="btn btn-primary">게시글 수정</button>
    </form>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                postId: <?= $post['id'] ?>,
                title: '<?= addslashes($post['title']) ?>',
                content: `<?= addslashes($post['content']) ?>`,
                attachments: <?= $attachments ?>
            };
        },
        methods: {
            async updatePost() {
                try {
                    await func('update_post', {
                        id: this.postId,
                        title: this.title,
                        content: this.content,
                        attachments: this.attachments,
                        auth: true
                    });

                    alert('게시글이 수정되었습니다.');
                    window.location.href = '<?= href()->post->view ?>?id=' + this.postId;

                } catch (error) {
                    console.error('게시글 수정 실패:', error);
                    alert('게시글 수정에 실패했습니다.');
                }
            }
        }
    }).mount('#post-edit-app');
});
</script>
```

## 업로드 버튼과 파일 미리보기 분리

업로드 버튼과 파일 미리보기를 분리하여 표시하는 방법입니다. 이 방식은 Bootstrap Modal이나 커스텀 레이아웃에서 유용합니다.

### 기본 개념

`file-upload-component`의 `:show-uploaded-files="false"`를 설정하고, 업로드된 파일 URL을 별도의 배열(예: `replyFiles`, `commentFiles`)에 저장하여 직접 표시합니다.

**핵심 Props:**
- `:show-uploaded-files="false"` - 컴포넌트 자체 미리보기 숨김
- `:show-upload-button="true"` - 업로드 버튼만 표시
- `@uploaded="handleFileUploaded"` - 업로드 완료 시 URL을 배열에 추가

**장점:**
- ✅ 업로드 버튼과 미리보기 위치를 자유롭게 배치
- ✅ 커스텀 미리보기 레이아웃 구현 가능
- ✅ Modal, Sidebar 등 복잡한 UI에 적합
- ✅ 삭제 버튼, 순서 변경 등 완전한 커스터마이징

### 답글(Reply) 작성 Modal 예제

답글 작성 시 카메라 아이콘으로 업로드하고, 하단에 미리보기를 표시하는 예제입니다.

```html
<!-- Bootstrap Modal -->
<div class="modal fade" id="replyModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header border-bottom">
                <h5 class="modal-title">
                    <i class="fa-solid fa-reply me-2 text-primary"></i>
                    답글 작성
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3">
                <!-- 답글 내용 입력 -->
                <textarea
                    v-model="replyContent"
                    class="form-control mb-3"
                    rows="4"
                    placeholder="답글을 입력하세요..."></textarea>

                <!-- 하단 액션 영역: 카메라 아이콘 + 버튼들 -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- 왼쪽: 파일 업로드 카메라 아이콘 -->
                    <div>
                        <file-upload-component
                            :single="false"
                            :show-uploaded-files="false"
                            :show-upload-button="true"
                            upload-button-type="camera-icon"
                            accept="image/*"
                            @uploaded="handleReplyFileUploaded">
                        </file-upload-component>
                    </div>

                    <!-- 오른쪽: Cancel, Send Reply 버튼 -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" @click="submitReply">
                            Send Reply
                        </button>
                    </div>
                </div>

                <!-- 업로드된 파일 미리보기 (빈 박스 없음) -->
                <div v-if="replyFiles.length > 0" class="row g-2">
                    <div v-for="(fileUrl, index) in replyFiles" :key="index" class="col-4">
                        <div class="position-relative border rounded" style="padding-bottom: 100%; background-color: #f8f9fa;">
                            <!-- 썸네일 이미지 -->
                            <img :src="thumbnail(fileUrl, 300, 300, 'cover', 85, 'ffffff')"
                                 :alt="'Image ' + (index + 1)"
                                 class="position-absolute top-0 start-0 rounded"
                                 style="width: 100%; height: 100%; object-fit: cover;">

                            <!-- 삭제 버튼 -->
                            <button
                                @click="removeReplyFile(index)"
                                type="button"
                                class="btn btn-sm btn-danger position-absolute"
                                style="top: 4px; right: 4px; width: 24px; height: 24px; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 10;"
                                title="Remove image">
                                <i class="fa-solid fa-xmark" style="font-size: 12px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload-component': window.FileUploadComponent
        },
        data() {
            return {
                replyContent: '',     // 답글 내용
                replyFiles: []        // 업로드된 파일 URL 배열
            };
        },
        methods: {
            // 파일 업로드 완료 시 호출
            handleReplyFileUploaded(data) {
                console.log('파일 업로드됨:', data.url);
                this.replyFiles.push(data.url);  // 배열에 URL 추가
            },

            // 파일 삭제
            removeReplyFile(index) {
                this.replyFiles.splice(index, 1);  // 배열에서 제거
            },

            // 답글 전송
            async submitReply() {
                if (!this.replyContent.trim()) {
                    alert('답글 내용을 입력하세요.');
                    return;
                }

                try {
                    await func('create_comment', {
                        content: this.replyContent,
                        files: this.replyFiles,  // 파일 URL 배열 전송
                        parent_id: this.parentCommentId,
                        auth: true
                    });

                    // 초기화
                    this.replyContent = '';
                    this.replyFiles = [];

                    // Modal 닫기
                    const modalEl = document.getElementById('replyModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    alert('답글이 작성되었습니다.');
                } catch (error) {
                    console.error('답글 작성 실패:', error);
                    alert('답글 작성에 실패했습니다.');
                }
            },

            // 썸네일 함수 (전역 함수 사용)
            thumbnail(url, width, height, mode, quality, bgColor) {
                return window.thumbnail(url, width, height, mode, quality, bgColor);
            }
        }
    }).mount('#app');
});
</script>
```

### 댓글(Comment) 작성 Modal 예제

최상위 댓글 작성 시에도 동일한 패턴을 사용할 수 있습니다.

```html
<!-- 댓글 작성 Bootstrap Modal -->
<div class="modal fade" id="commentModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header border-bottom">
                <h5 class="modal-title">
                    <i class="fa-solid fa-comment me-2 text-primary"></i>
                    댓글 작성
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3">
                <!-- 댓글 내용 입력 -->
                <textarea
                    v-model="commentContent"
                    class="form-control mb-3"
                    rows="4"
                    placeholder="댓글을 입력하세요..."></textarea>

                <!-- 하단 액션 영역 -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- 왼쪽: 카메라 아이콘 -->
                    <div>
                        <file-upload-component
                            :single="false"
                            :show-uploaded-files="false"
                            :show-upload-button="true"
                            upload-button-type="camera-icon"
                            accept="image/*"
                            @uploaded="handleCommentFileUploaded">
                        </file-upload-component>
                    </div>

                    <!-- 오른쪽: 버튼들 -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" @click="submitComment">
                            Send Comment
                        </button>
                    </div>
                </div>

                <!-- 업로드된 파일 미리보기 (3열 그리드) -->
                <div v-if="commentFiles.length > 0" class="row g-2">
                    <div v-for="(fileUrl, index) in commentFiles" :key="index" class="col-4">
                        <div class="position-relative border rounded" style="padding-bottom: 100%; background-color: #f8f9fa;">
                            <img :src="thumbnail(fileUrl, 300, 300, 'cover', 85, 'ffffff')"
                                 :alt="'Image ' + (index + 1)"
                                 class="position-absolute top-0 start-0 rounded"
                                 style="width: 100%; height: 100%; object-fit: cover;">

                            <button
                                @click="removeCommentFile(index)"
                                type="button"
                                class="btn btn-sm btn-danger position-absolute"
                                style="top: 4px; right: 4px; width: 24px; height: 24px; padding: 0; border-radius: 50%;"
                                title="Remove image">
                                <i class="fa-solid fa-xmark" style="font-size: 12px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload-component': window.FileUploadComponent
        },
        data() {
            return {
                commentContent: '',
                commentFiles: []
            };
        },
        methods: {
            handleCommentFileUploaded(data) {
                this.commentFiles.push(data.url);
            },

            removeCommentFile(index) {
                this.commentFiles.splice(index, 1);
            },

            async submitComment() {
                if (!this.commentContent.trim()) {
                    alert('댓글 내용을 입력하세요.');
                    return;
                }

                try {
                    await func('create_comment', {
                        content: this.commentContent,
                        files: this.commentFiles,
                        parent_id: null,  // 최상위 댓글
                        auth: true
                    });

                    // 초기화
                    this.commentContent = '';
                    this.commentFiles = [];

                    // Modal 닫기
                    const modalEl = document.getElementById('commentModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    alert('댓글이 작성되었습니다.');
                } catch (error) {
                    console.error('댓글 작성 실패:', error);
                }
            },

            thumbnail(url, width, height, mode, quality, bgColor) {
                return window.thumbnail(url, width, height, mode, quality, bgColor);
            }
        }
    }).mount('#app');
});
</script>
```

### 인라인 폼 예제 (Modal 없이)

Modal 대신 인라인 폼으로도 구현할 수 있습니다.

```html
<div id="inline-upload-app">
    <!-- 글 작성 영역 -->
    <div class="card">
        <div class="card-body">
            <textarea
                v-model="postContent"
                class="form-control mb-3"
                rows="4"
                placeholder="무슨 생각을 하고 계신가요?"></textarea>

            <!-- 업로드 버튼 -->
            <div class="mb-3">
                <file-upload-component
                    :single="false"
                    :show-uploaded-files="false"
                    :show-upload-button="true"
                    accept="image/*,video/*"
                    @uploaded="handlePostFileUploaded">
                </file-upload-component>
            </div>

            <!-- 업로드된 파일 미리보기 (4열 그리드) -->
            <div v-if="postFiles.length > 0" class="row g-2 mb-3">
                <div v-for="(fileUrl, index) in postFiles" :key="index" class="col-3">
                    <div class="position-relative border rounded" style="padding-bottom: 100%;">
                        <img :src="thumbnail(fileUrl, 300, 300, 'cover', 85, 'ffffff')"
                             class="position-absolute top-0 start-0 rounded"
                             style="width: 100%; height: 100%; object-fit: cover;">

                        <button
                            @click="postFiles.splice(index, 1)"
                            type="button"
                            class="btn btn-sm btn-danger position-absolute"
                            style="top: 4px; right: 4px; width: 24px; height: 24px; padding: 0; border-radius: 50%;">
                            <i class="fa-solid fa-xmark" style="font-size: 12px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 게시 버튼 -->
            <button @click="submitPost" class="btn btn-primary">
                게시
            </button>
        </div>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload-component': window.FileUploadComponent
        },
        data() {
            return {
                postContent: '',
                postFiles: []
            };
        },
        methods: {
            handlePostFileUploaded(data) {
                this.postFiles.push(data.url);
            },

            async submitPost() {
                if (!this.postContent.trim() && this.postFiles.length === 0) {
                    alert('내용을 입력하거나 파일을 업로드하세요.');
                    return;
                }

                try {
                    await func('create_post', {
                        content: this.postContent,
                        files: this.postFiles,
                        auth: true
                    });

                    // 초기화
                    this.postContent = '';
                    this.postFiles = [];

                    alert('게시글이 작성되었습니다.');
                } catch (error) {
                    console.error('게시글 작성 실패:', error);
                }
            },

            thumbnail(url, width, height, mode, quality, bgColor) {
                return window.thumbnail(url, width, height, mode, quality, bgColor);
            }
        }
    }).mount('#inline-upload-app');
});
</script>
```

### 커스텀 그리드 레이아웃

미리보기 그리드를 2열, 3열, 4열 등으로 자유롭게 변경할 수 있습니다.

```html
<!-- 2열 그리드 -->
<div class="row g-2">
    <div v-for="(fileUrl, index) in files" :key="index" class="col-6">
        <!-- 이미지 카드 -->
    </div>
</div>

<!-- 3열 그리드 (기본) -->
<div class="row g-2">
    <div v-for="(fileUrl, index) in files" :key="index" class="col-4">
        <!-- 이미지 카드 -->
    </div>
</div>

<!-- 4열 그리드 -->
<div class="row g-2">
    <div v-for="(fileUrl, index) in files" :key="index" class="col-3">
        <!-- 이미지 카드 -->
    </div>
</div>

<!-- 반응형 그리드 (모바일 2열, 태블릿 3열, 데스크톱 4열) -->
<div class="row g-2">
    <div v-for="(fileUrl, index) in files" :key="index" class="col-6 col-md-4 col-lg-3">
        <!-- 이미지 카드 -->
    </div>
</div>
```

### 핵심 포인트

1. **`:show-uploaded-files="false"`**: 컴포넌트 자체 미리보기를 숨김
2. **`@uploaded="handleFileUploaded"`**: 업로드 완료 시 URL을 배열에 추가
3. **`v-if="files.length > 0"`**: 파일이 있을 때만 미리보기 표시 (빈 박스 방지)
4. **`v-for="(fileUrl, index) in files"`**: 파일 목록을 순회하며 표시
5. **`@click="files.splice(index, 1)"`**: 파일 삭제
6. **`col-4`**: 3열 그리드 (Bootstrap 그리드 시스템)
7. **`padding-bottom: 100%`**: 정사각형 비율 유지

### 실제 사용 사례

- ✅ **댓글/답글 Modal**: post.component.js
- ✅ **게시글 작성 폼**: 인라인 또는 Modal
- ✅ **채팅 메시지**: 파일 첨부
- ✅ **프로필 갤러리**: 여러 사진 업로드
- ✅ **제품 이미지**: E-commerce 상품 등록

---

## v-model 사용법

`v-model`을 사용하여 양방향 데이터 바인딩을 구현할 수 있습니다.

### v-model 기본 예제

```html
<div id="vmodel-app">
    <!-- v-model로 파일 목록 관리 -->
    <file-upload
        v-model="files"
        :single="false"
        input-name="files[]"
        :show-delete-icon="true">
    </file-upload>

    <!-- 업로드된 파일 목록 실시간 표시 -->
    <div class="mt-3">
        <h5>업로드된 파일 ({{ files.length }}개)</h5>
        <ul>
            <li v-for="url in files" :key="url">{{ url }}</li>
        </ul>
    </div>

    <!-- 파일 목록 초기화 버튼 -->
    <button @click="files = []" class="btn btn-warning">
        모든 파일 제거
    </button>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                files: []
            };
        },
        watch: {
            // 파일 목록 변경 감지
            files: {
                handler(newFiles) {
                    console.log('파일 목록 변경:', newFiles);
                },
                deep: true
            }
        }
    }).mount('#vmodel-app');
});
</script>
```

## Hidden Input 활용

폼 전송 시 파일 URL을 hidden input으로 전달하는 방법입니다.

### 폼과 함께 사용하기

```html
<form id="traditional-form" method="POST" action="/submit">
    <!-- 일반 폼 필드 -->
    <input type="text" name="title" placeholder="제목">

    <!-- 파일 업로드 컴포넌트 -->
    <div id="form-upload">
        <file-upload
            :single="false"
            input-name="uploaded_files[]"
            :show-delete-icon="true"
            v-model="uploadedFiles">
        </file-upload>
    </div>

    <!-- hidden input이 자동으로 생성되어 폼과 함께 전송됨 -->
    <!-- <input type="hidden" name="uploaded_files[]" value="url1,url2,url3"> -->

    <button type="submit">전송</button>
</form>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                uploadedFiles: []
            };
        }
    }).mount('#form-upload');
});
</script>
```

### PHP에서 받기

```php
// POST로 전송된 파일 URL 목록 받기
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $files = $_POST['uploaded_files'] ?? [];

    // 쉼표로 구분된 URL을 배열로 변환
    if (is_string($files)) {
        $files = explode(',', $files);
    }

    // 파일 URL 처리
    foreach ($files as $fileUrl) {
        echo "업로드된 파일: " . htmlspecialchars($fileUrl) . "<br>";
    }
}
```

## 파일 삭제

### 삭제 이벤트 처리

```javascript
methods: {
    async handleDelete(data) {
        console.log('파일 삭제됨:', data);

        // 데이터베이스에서도 파일 참조 제거
        try {
            await func('remove_file_reference', {
                file_url: data.url,
                auth: true
            });
        } catch (error) {
            console.error('파일 참조 제거 실패:', error);
        }
    }
}
```

### 삭제 확인 커스터마이징

컴포넌트 내부에서 기본적으로 확인 대화상자를 표시하지만, 필요시 커스터마이징 가능합니다.

## QR 코드 디코딩

QR 코드가 포함된 이미지를 업로드하면 자동으로 디코딩합니다.

### QR 코드 스캔 예제

```html
<div id="qr-scanner-app">
    <h3>QR 코드 스캔</h3>

    <!-- QR 코드 디코딩 활성화 -->
    <file-upload
        :single="true"
        input-name="qr_image"
        :decode-qr-code="true"
        accept="image/*"
        @uploaded="handleQRUpload">
    </file-upload>

    <!-- 디코딩된 QR 데이터 표시 -->
    <div v-if="qrData" class="alert alert-success mt-3">
        <h5>QR 코드 데이터:</h5>
        <pre>{{ qrData }}</pre>
    </div>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                qrData: null
            };
        },
        methods: {
            handleQRUpload(data) {
                console.log('QR 코드 업로드:', data);

                if (data.qr_code) {
                    this.qrData = data.qr_code;
                    alert('QR 코드가 성공적으로 스캔되었습니다!');
                } else {
                    this.qrData = null;
                    alert('이미지에서 QR 코드를 찾을 수 없습니다.');
                }
            }
        }
    }).mount('#qr-scanner-app');
});
</script>
```

## 고급 사용법

### ref를 통한 메서드 호출

```html
<div id="advanced-app">
    <!-- ref 속성 추가 -->
    <file-upload
        ref="uploader"
        :single="false"
        input-name="files[]"
        :show-upload-button="false">
    </file-upload>

    <!-- 커스텀 버튼으로 파일 선택 -->
    <button @click="openFileDialog" class="btn btn-primary">
        <i class="bi bi-folder-open"></i> 파일 선택
    </button>

    <!-- 프로그래밍 방식으로 파일 추가 -->
    <button @click="addDefaultFile" class="btn btn-secondary">
        기본 이미지 추가
    </button>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        methods: {
            openFileDialog() {
                // ref를 통해 컴포넌트 메서드 호출
                this.$refs.uploader.openFileSelector();
            },

            addDefaultFile() {
                // 프로그래밍 방식으로 파일 추가
                this.$refs.uploader.uploadedFiles.push('/images/default.jpg');
            }
        }
    }).mount('#advanced-app');
});
</script>
```

### 조건부 렌더링

```html
<div id="conditional-app">
    <!-- 로그인 사용자만 업로드 가능 -->
    <file-upload
        v-if="isLoggedIn"
        :single="false"
        input-name="user_files[]">
    </file-upload>

    <div v-else class="alert alert-warning">
        로그인 후 파일을 업로드할 수 있습니다.
    </div>
</div>
```

### 동적 Props

```html
<div id="dynamic-app">
    <!-- 파일 타입 선택 -->
    <select v-model="fileType" class="form-select mb-3">
        <option value="image/*">이미지만</option>
        <option value="video/*">비디오만</option>
        <option value=".pdf">PDF만</option>
        <option value="*">모든 파일</option>
    </select>

    <!-- 단일/다중 모드 전환 -->
    <div class="form-check mb-3">
        <input type="checkbox" v-model="singleMode" class="form-check-input">
        <label class="form-check-label">단일 파일 모드</label>
    </div>

    <!-- 동적 Props 적용 -->
    <file-upload
        :single="singleMode"
        :accept="fileType"
        :show-delete-icon="true"
        input-name="dynamic_files">
    </file-upload>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        },
        data() {
            return {
                fileType: 'image/*',
                singleMode: false
            };
        }
    }).mount('#dynamic-app');
});
</script>
```

## CSS 커스터마이징

### 원형 프로필 사진

```css
/* 원형 프로필 사진 스타일 */
.profile-upload .single-file-display img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #dee2e6;
}

.profile-upload .single-file-display {
    display: inline-block;
    position: relative;
}

/* 삭제 버튼을 원형 외곽에 위치 */
.profile-upload .btn-danger {
    border-radius: 50%;
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
```

### 그리드 레이아웃 커스터마이징

```css
/* 3열 그리드로 변경 */
.custom-grid .row {
    --bs-gutter-x: 0.5rem;
    --bs-gutter-y: 0.5rem;
}

.custom-grid .col-3 {
    flex: 0 0 auto;
    width: 33.333333%;
}

/* 모바일에서 2열 */
@media (max-width: 768px) {
    .custom-grid .col-3 {
        width: 50%;
    }
}
```

### 업로드 버튼 스타일링

```css
/* 업로드 버튼 커스터마이징 */
.file-upload-wrapper .btn-outline-secondary {
    border-style: dashed;
    border-width: 2px;
    padding: 1rem 2rem;
    color: #6c757d;
    transition: all 0.3s;
}

.file-upload-wrapper .btn-outline-secondary:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}
```

### 진행률 바 스타일링

```css
/* 진행률 바 커스터마이징 */
.file-upload-progress-container .progress {
    height: 25px;
    border-radius: 10px;
}

.file-upload-progress-container .progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    font-weight: bold;
    font-size: 14px;
}
```

## 문제 해결

### 파일이 업로드되지 않을 때

1. **네트워크 확인**: 브라우저 개발자 도구에서 네트워크 오류 확인
2. **파일 크기 제한**: PHP `upload_max_filesize` 및 `post_max_size` 설정 확인
3. **파일 타입**: `accept` 속성과 실제 파일 타입이 일치하는지 확인

### 이미지가 표시되지 않을 때

```javascript
// 이미지 URL 확인
methods: {
    handleUpload(data) {
        console.log('업로드된 URL:', data.url);

        // URL이 상대 경로인지 절대 경로인지 확인
        if (!data.url.startsWith('http') && !data.url.startsWith('/')) {
            console.warn('상대 경로 URL:', data.url);
        }
    }
}
```

### v-model이 동작하지 않을 때

```javascript
// v-model 디버깅
watch: {
    uploadedFiles: {
        handler(newVal) {
            console.log('v-model 변경:', newVal);
        },
        deep: true,
        immediate: true
    }
}
```

### 메모리 누수 방지

```javascript
// 컴포넌트 정리
beforeUnmount() {
    // 진행 중인 업로드 취소
    if (this.uploadRequest) {
        this.uploadRequest.cancel();
    }
}
```

## API 참조

### 컴포넌트 메서드

| 메서드 | 설명 |
|--------|------|
| `openFileSelector()` | 파일 선택 대화상자 열기 |
| `uploadFile(file)` | 프로그래밍 방식으로 파일 업로드 |
| `deleteFile(url)` | 특정 파일 삭제 |

### 내부 데이터

| 속성 | 타입 | 설명 |
|------|------|------|
| `uploadedFiles` | Array | 업로드된 파일 URL 목록 |
| `uploading` | Boolean | 업로드 진행 상태 |
| `uploadProgress` | Number | 업로드 진행률 (0-100) |

## 마이그레이션 가이드

### Vue 컴포넌트로 마이그레이션

**⚠️ 중요**: `file-upload.js` 파일은 더 이상 사용되지 않습니다. 대신 **`js/vue-components/file-upload.component.js`**를 사용하세요.

기존 방식을 사용하던 코드를 Vue 컴포넌트로 마이그레이션하는 방법입니다.

#### 기존 코드 (더 이상 사용 안 함)
```html
<!-- 기존 방식 -->
<input type="file"
       onchange="handle_file_change(event, {id: 'display-area'})"
       accept="image/*">
<div id="display-area"
     data-single="true"
     data-input-name="photo"></div>
```

#### 새 코드 (Vue 컴포넌트)
```html
<!-- 새로운 방식 -->
<div id="app">
    <file-upload
        :single="true"
        input-name="photo"
        accept="image/*">
    </file-upload>
</div>

<script>
ready(() => {
    Vue.createApp({
        components: {
            'file-upload': window.FileUploadComponent
        }
    }).mount('#app');
});
</script>
```

### data 속성 매핑

| 기존 (data-*) | 새로운 (Props) |
|---------------|----------------|
| `data-single="true"` | `:single="true"` |
| `data-input-name="files"` | `input-name="files"` |
| `data-delete-icon="show"` | `:show-delete-icon="true"` |
| `data-decode-qr-code="true"` | `:decode-qr-code="true"` |
| `data-default-files="url1,url2"` | `:default-files="['url1','url2']"` |

### 콜백 함수 마이그레이션

#### 기존 방식
```javascript
function my_upload_callback(data) {
    console.log('Uploaded:', data.url);
}

// HTML
onchange="handle_file_change(event, {
    id: 'display',
    on_uploaded: my_upload_callback
})"
```

#### 새로운 방식
```javascript
methods: {
    handleUpload(data) {
        console.log('Uploaded:', data.url);
    }
}

// Template
@uploaded="handleUpload"
```

## 성능 최적화

### 대용량 파일 처리

```javascript
// 파일 크기 검증
methods: {
    async handleFileSelect(event) {
        const file = event.target.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (file.size > maxSize) {
            alert('파일 크기는 10MB를 초과할 수 없습니다.');
            return;
        }

        // 업로드 진행
        await this.uploadFile(file);
    }
}
```

### 이미지 미리보기 최적화

```javascript
// 썸네일 생성
methods: {
    createThumbnail(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // 썸네일 크기
                    const maxWidth = 200;
                    const maxHeight = 200;

                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    resolve(canvas.toDataURL());
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
}
```

## 보안 고려사항

### 파일 타입 검증

서버 측에서 반드시 파일 타입을 재검증해야 합니다.

```php
// PHP 서버 측 검증
function validateFile($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('허용되지 않은 파일 타입입니다.');
    }

    return true;
}
```

### XSS 방지

```javascript
// 파일 URL 출력 시 이스케이프
methods: {
    sanitizeUrl(url) {
        return url.replace(/[<>"']/g, '');
    }
}
```

## 접근성

### ARIA 속성

컴포넌트는 기본적으로 ARIA 속성을 포함합니다:
- `role="progressbar"` - 진행률 표시
- `aria-label` - 스크린 리더를 위한 레이블
- `aria-valuenow`, `aria-valuemin`, `aria-valuemax` - 진행률 값

### 키보드 네비게이션

- `Tab` - 업로드 버튼으로 이동
- `Enter` / `Space` - 파일 선택 대화상자 열기
- `Delete` - 선택된 파일 삭제 (포커스가 있을 때)

## 브라우저 지원

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### 폴리필

구형 브라우저 지원이 필요한 경우:

```html
<!-- Promise 폴리필 -->
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"></script>

<!-- FormData 폴리필 -->
<script src="https://cdn.jsdelivr.net/npm/formdata-polyfill@4.0.10/formdata.min.js"></script>
```

---

## 요약

`FileUploadComponent`는 Vue.js 기반의 강력하고 유연한 파일 업로드 솔루션입니다. Props와 Events를 통한 깔끔한 인터페이스, v-model 지원, 그리고 다양한 커스터마이징 옵션을 제공합니다.

### 핵심 기능
- ✅ Vue.js 3.x Options API
- ✅ 단일/다중 파일 업로드
- ✅ 실시간 진행률 표시
- ✅ 파일 미리보기
- ✅ v-model 양방향 바인딩
- ✅ 이벤트 기반 통신
- ✅ Bootstrap 5 스타일링

### 시작하기
1. 컴포넌트 로드: `load_deferred_js('vue-components/file-upload.component')`
2. Vue 앱에 등록: `components: { 'file-upload': window.FileUploadComponent }`
3. 템플릿에서 사용: `<file-upload :single="true" @uploaded="handleUpload"></file-upload>`

더 자세한 정보는 소스 코드의 주석을 참고하거나 개발팀에 문의하세요.