---
name: sonub
version: 1.0.0
description: 사용자 프로필 사진 업로드 및 관리 명세서
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: GPL-3.0
step: 45
priority: "**"
dependencies:
  - sonub-user-overview.md
  - sonub-setup-firebase.md
  - sonub-setup-shadcn.md
tags:
  - user-profile
  - firebase-storage
  - photo-upload
  - svelte5
---

# 프로필 사진 저장 및 업로드

## 사용자 정보 수정 페이지 경로

- 사용자 정보 수정 및 프로필 사진 업로드 화면은 **`/user/profile`** 경로에서 제공됩니다.
- 해당 페이지는 `src/routes/user/profile/+page.svelte` (또는 동일 경로 하위 Svelte 파일)로 구현하며, 본 명세서의 요구사항을 모두 충족해야 합니다.
- 다른 경로나 다중 페이지로 분리하지 말고, `/user/profile`을 단일 소스로 유지하여 사용자 경험을 일관되게 관리합니다.

## 저장소 디렉토리 구조

프로필 사진은 **사용자별로 격리된** 디렉토리에 저장됩니다:

```
users/
└─ {userId}/
   └─ profile/
      └─ {photo_filename}
```

**예시**:
```
users/user_001/profile/1699564800000-avatar.jpg
users/user_001/profile/photo.jpg
```

## 프로필 사진 업로드 구현

프로필 사진 업로드 후, 다운로드 URL을 `/users/<uid>/photoURL`에 저장합니다.

```javascript
import { ref as storageRef, uploadBytes, getDownloadURL } from 'firebase/storage';
import { ref as dbRef, update } from 'firebase/database';
import { storage, database, auth } from '$lib/utils/firebase.js';

async function uploadProfilePhoto(file) {
  const userId = auth.currentUser.uid;

  try {
    // 1. 파일명 생성 (타임스탬프 + 원본 파일명)
    const fileName = `${Date.now()}-${file.name}`;

    // 2. Firebase Storage에 파일 업로드
    const photoStorageRef = storageRef(storage, `users/${userId}/profile/${fileName}`);
    const snapshot = await uploadBytes(photoStorageRef, file);

    // 3. 다운로드 URL 생성
    const downloadURL = await getDownloadURL(snapshot.ref);

    // 4. Realtime Database의 /users/{userId}/photoURL에 저장
    const userRef = dbRef(database, `users/${userId}`);
    await update(userRef, {
      photoURL: downloadURL
    });

    console.log('프로필 사진 업로드 완료:', downloadURL);
    return downloadURL;

  } catch (error) {
    console.error('프로필 사진 업로드 실패:', error);
    throw error;
  }
}
```

## Svelte 컴포넌트 구현

`UserProfile.svelte` 컴포넌트 구현 예시:

```svelte
<script>
  import { uploadBytes, getDownloadURL, ref as storageRef } from 'firebase/storage';
  import { update, ref as dbRef } from 'firebase/database';
  import { storage, database } from '$lib/utils/firebase.js';
  import { user } from '../stores/auth.js';

  let file = $state(null);
  let preview = $state(null);
  let isSaving = $state(false);
  let successMessage = $state('');
  let errorMessage = $state('');

  // 파일 선택 이벤트 핸들러
  function handleFileSelect(event) {
    const selectedFile = event.target.files?.[0];
    if (!selectedFile) return;

    // 파일 크기 검증
    const MAX_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    if (selectedFile.size > MAX_SIZE) {
      errorMessage = '파일 크기는 5MB 이하여야 합니다';
      return;
    }

    if (!ALLOWED_TYPES.includes(selectedFile.type)) {
      errorMessage = '지원하는 파일 형식: JPEG, PNG, WebP';
      return;
    }

    file = selectedFile;

    // 미리보기 생성
    const reader = new FileReader();
    reader.onload = (e) => {
      preview = e.target?.result;
    };
    reader.readAsDataURL(file);
    errorMessage = '';
  }

  // 폼 제출 핸들러
  async function handleSubmit(event) {
    event.preventDefault();
    isSaving = true;

    try {
      const updateData = {
        displayName: formData.displayName,
        gender: formData.gender,
        dateOfBirth: formData.dateOfBirth
      };

      // 새 프로필 사진이 있는 경우 업로드
      if (file) {
        const fileName = `${Date.now()}-${file.name}`;
        const photoStorageRef = storageRef(storage, `users/${$user.uid}/profile/${fileName}`);

        // Firebase Storage에 업로드
        const snapshot = await uploadBytes(photoStorageRef, file);

        // 다운로드 URL 생성
        const downloadURL = await getDownloadURL(snapshot.ref);
        updateData.photoURL = downloadURL;
      }

      // Realtime Database에 사용자 정보 저장
      const userRef = dbRef(database, `users/${$user.uid}`);
      await update(userRef, updateData);

      // 성공 메시지 표시
      successMessage = '프로필이 업데이트되었습니다';
      setTimeout(() => {
        successMessage = '';
      }, 3000);

      // 상태 초기화
      file = null;
      preview = null;

    } catch (error) {
      console.error('저장 실패:', error);
      errorMessage = '프로필 업데이트에 실패했습니다: ' + error.message;
    } finally {
      isSaving = false;
    }
  }
</script>

<!-- 파일 입력 -->
<div class="form-group">
  <label for="photo">프로필 사진</label>
  {#if preview}
    <img src={preview} alt="프로필 미리보기" class="preview-image" />
  {/if}
  <input
    type="file"
    id="photo"
    accept="image/jpeg,image/png,image/webp"
    onchange={handleFileSelect}
  />
</div>

<!-- 제출 버튼 -->
<button type="submit" disabled={isSaving} onclick={handleSubmit}>
  {isSaving ? '저장 중...' : '저장'}
</button>
```

---



---

# 프로필 사진 URL 저장 패턴

## URL 저장 구조

```
/users/{userId}/photoURL = "https://storage.googleapis.com/.../photo.jpg"
```

## 주의사항

1. **URL은 주기적으로 갱신**: 다운로드 URL은 유효기간이 있으므로 새로운 사진 업로드 시마다 갱신
2. **URL 검증**: URL이 유효한지 확인하고 404 에러 처리
3. **여러 사진 관리**: 향후 여러 프로필 사진을 지원하려면 `/users/{userId}/photos/{photoId}` 구조 고려
4. **이전 사진 정리**: 새 사진 업로드 후 이전 사진은 Storage에서 삭제

---

# 실제 구현 예시

## 프로필 업데이트 플로우

```javascript
async function updateUserProfile(userData) {
  try {
    // 데이터 검증
    const validationErrors = validateUserProfile(userData);
    if (validationErrors.length > 0) {
      throw new Error(validationErrors[0]);
    }

    // 프로필 업데이트
    const userRef = dbRef(database, `users/${auth.currentUser.uid}`);
    await update(userRef, userData);

    return { success: true };

  } catch (error) {
    if (error.code === 'PERMISSION_DENIED') {
      console.error('권한 오류: 다른 사용자의 프로필을 수정할 수 없습니다');
      return { success: false, error: '권한 없음' };
    } else if (error.code === 'NETWORK_ERROR') {
      console.error('네트워크 오류가 발생했습니다');
      return { success: false, error: '네트워크 오류' };
    } else {
      console.error('저장 실패:', error);
      return { success: false, error: error.message };
    }
  }
}
```

---



# 사용자 프로필 데이터 조회

## 현재 사용자 프로필 조회

```javascript
import { ref as dbRef, get } from 'firebase/database';
import { database, auth } from '$lib/utils/firebase.js';

async function getCurrentUserProfile() {
  try {
    const userId = auth.currentUser?.uid;
    if (!userId) throw new Error('로그인 필요');

    const userRef = dbRef(database, `users/${userId}`);
    const snapshot = await get(userRef);

    if (snapshot.exists()) {
      return snapshot.val();
    } else {
      console.warn('사용자 프로필을 찾을 수 없습니다');
      return null;
    }
  } catch (error) {
    console.error('프로필 조회 실패:', error);
    throw error;
  }
}
```

## 다른 사용자 프로필 조회

```javascript
async function getUserProfile(userId) {
  try {
    const userRef = dbRef(database, `users/${userId}`);
    const snapshot = await get(userRef);

    if (snapshot.exists()) {
      return snapshot.val();
    } else {
      console.warn(`사용자 ${userId}의 프로필을 찾을 수 없습니다`);
      return null;
    }
  } catch (error) {
    console.error('프로필 조회 실패:', error);
    throw error;
  }
}
```

## 사용자 프로필 실시간 감시

Svelte 스토어를 활용하여 사용자 프로필을 실시간으로 감시할 수 있습니다:

```javascript
// src/lib/stores/user.js
import { writable } from 'svelte/store';
import { onValue, ref as dbRef } from 'firebase/database';
import { database } from '$lib/utils/firebase.js';

function createUserStore(userId) {
  const { subscribe, set } = writable(null);

  if (userId) {
    // 실시간 감시 시작
    const userRef = dbRef(database, `users/${userId}`);
    const unsubscribe = onValue(
      userRef,
      (snapshot) => {
        if (snapshot.exists()) {
          set(snapshot.val());
        } else {
          set(null);
        }
      },
      (error) => {
        console.error('프로필 감시 실패:', error);
      }
    );

    // 구독 해제
    return () => unsubscribe();
  }

  return subscribe;
}

export const userProfile = {
  subscribe: (callback) => {
    // 사용자 ID를 전달받아 감시 시작
  }
};
```



# 사용자 데이터 검증

## 데이터 유효성 검증

```javascript
function validateUserProfile(data) {
  const errors = [];

  // displayName 검증
  if (!data.displayName || data.displayName.trim().length === 0) {
    errors.push('닉네임은 필수입니다');
  }
  if (data.displayName.length > 50) {
    errors.push('닉네임은 50자 이하여야 합니다');
  }

  // gender 검증
  const validGenders = ['male', 'female', 'other', 'none'];
  if (data.gender && !validGenders.includes(data.gender)) {
    errors.push('성별은 올바른 값이어야 합니다');
  }

  // dateOfBirth 검증 (YYYY-MM-DD 형식)
  if (data.dateOfBirth) {
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateRegex.test(data.dateOfBirth)) {
      errors.push('생년월일은 YYYY-MM-DD 형식이어야 합니다');
    }
    // 미래 날짜 검증
    const birthDate = new Date(data.dateOfBirth);
    if (birthDate > new Date()) {
      errors.push('생년월일은 과거여야 합니다');
    }
  }

  // bio 검증
  if (data.bio && data.bio.length > 500) {
    errors.push('자기소개는 500자 이하여야 합니다');
  }

  return errors;
}
```
