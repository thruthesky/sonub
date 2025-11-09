---
name: sonub-design-guideline
version: 1.1.0
description: Sonub UI의 공통 디자인 정책(테마, 인터랙션)을 정의하는 SED 명세서
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-10
updated: 2025-01-09
step: 16
priority: "*"
dependencies: ["sonub-design-workflow.md", "sonub-setup-tailwind.md"]
tags: ["design", "ui", "theme", "interaction", "cursor", "button", "href"]
---

# Sonub Design Guideline

## 1. 개요

### 1.1 목적

본 명세서는 Sonub UI가 반드시 지켜야 하는 핵심 디자인 정책을 정의합니다. 특히 **오직 Light Mode만 지원**한다는 테마 원칙과 **모든 클릭 가능한 요소에 `cursor: pointer`를 적용**하는 인터랙션 원칙을 명시하여, 스크린샷과 같이 커서가 손가락 모양으로 보이지 않는 문제를 방지합니다.

### 1.2 적용 범위

- 전역 레이아웃(`src/routes/+layout.svelte`, `src/routes/admin/+layout.svelte`)
- 공통 컴포넌트(탑바, 사이드바, 카드, 버튼 등)
- shadcn-svelte 기반 UI 및 커스텀 컴포넌트
- 마우스로 상호작용 가능한 모든 DOM 요소

## 2. 테마 정책: Light Mode Only

### 2.1 시스템 다크 모드 무시

1. `prefers-color-scheme: dark` 미디어쿼리를 사용하지 않습니다.
2. 전역 스타일 또는 Tailwind 설정에서 `dark:` 변형 클래스를 등록하지 않습니다.
3. `:root { color-scheme: light; }` 선언을 통해 브라우저가 Light Mode 자산(스크롤바, 폼 컨트롤 등)을 사용하도록 강제합니다.

```css
:root {
  color-scheme: light;
  background-color: #f6f7fb;
}
body {
  @apply bg-white text-gray-900;
}
```

### 2.2 UI 검증 체크리스트

- [ ] 시스템이 다크 모드일 때도 Sonub UI가 밝은 배경과 다크 텍스트 대비를 유지하는지 확인한다.
- [ ] Tailwind 설정 파일에서 다크 모드 옵션이 비활성화(`darkMode: false` 또는 정의하지 않음)인지 검토한다.
- [ ] 새 컴포넌트 추가 시 다크 모드 토글, 스위치, 상태 저장 로직을 구현하지 않는다.

## 3. 인터랙션 정책: Cursor Pointer

### ⚠️ 최고 우선순위 정책 (CRITICAL)

**모든 클릭 가능한 요소에는 반드시 `cursor: pointer`를 적용해야 합니다.** 이는 선택사항이 아니라 **필수 규칙**입니다. 이 규칙을 따르지 않으면 사용자 경험이 심각하게 저하됩니다.

### 3.1 적용 대상 (❌ 예외 없음)

**다음의 모든 요소는 반드시 `cursor: pointer` 또는 `cursor-pointer` 클래스를 적용해야 합니다:**

1. **버튼** - 모든 `<button>` 태그
2. **링크** - 모든 `<a>` 태그
3. **카드** - 클릭 가능한 카드 요소
4. **탭** - 탭 네비게이션 요소
5. **토글** - 토글 스위치
6. **메뉴 항목** - 네비게이션 메뉴
7. **드롭다운 트리거** - 드롭다운을 열 수 있는 요소
8. **테이블 행** - 클릭 가능한 테이블 행
9. **커스텀 인터랙션 요소** - `role="button"` 또는 `tabindex="0"`을 부여한 모든 div, span 등
10. **아이콘 버튼** - 아이콘만으로 이루어진 클릭 가능한 요소
11. **모달 트리거** - 모달을 열 수 있는 모든 요소
12. **폼 요소** - checkbox, radio, select 등 상호작용 가능한 모든 폼 요소

**🔴 예외는 절대 없습니다.** 비활성화된 요소도 `cursor-not-allowed`를 명시해야 합니다.

### 3.2 구현 규칙 (필수 준수)

**❌ 이 규칙 중 하나라도 어기면 PR을 승인하지 않습니다.**

1. **Tailwind 유틸리티 `cursor-pointer` 사용 (권장)**
   ```svelte
   <button class="cursor-pointer ...">클릭</button>
   ```

2. **또는 CSS `cursor: pointer;` 사용**
   ```svelte
   <button style="cursor: pointer;">클릭</button>
   ```

3. **🔥 shadcn Button 컴포넌트에서 링크 기능이 필요할 때는 `href` 속성 사용 (필수)**
   - Button 컴포넌트에 `href` 속성을 전달하면 최종 결과물이 자동으로 `<a>` 태그로 빌드됩니다
   - **`<a>` 태그 안에 `<button>` 중첩이 아닙니다** - 조건부 렌더링으로 하나의 요소만 생성됩니다
   - **이것이 올바른 방식입니다.** 래퍼 div 사용은 더 이상 필요하지 않습니다.
   ```svelte
   <!-- ✅ 올바름: Button의 href 속성 사용 (최종 결과: <a> 태그) -->
   <Button href="/profile" class="cursor-pointer">
     프로필로 이동
   </Button>
   ```
   - 자세한 내용은 [shadcn-svelte Button 공식 문서](https://www.shadcn-svelte.com/docs/components/button)를 참고하세요.

4. **모든 네비게이션/링크 동작은 `<a>`로 렌더링되어야 함**
   - `goto()` 호출이나 `window.location`을 사용하는 **네비게이션 버튼은 금지**
   - 반드시 `<Button href=\"...\">` 또는 순수 `<a href=\"...\">`로 구현해 최종 DOM이 `<a>`가 되도록 한다
   - 아이콘 버튼(예: 탑바 메뉴, 카드 액션 등)도 가능하면 `href`를 부여해 `<a>` 태그로 렌더링한다
   - 이렇게 하면 **손가락 커서가 자동으로 표시**되고 접근성도 향상된다

5. **shadcn-svelte 컴포넌트도 예외 없음**
   - Button의 `href`를 사용하지 않는 경우, 컴포넌트 자체에 커서가 없으면 **반드시 래퍼 요소에서 지정**
   ```svelte
   <div class="cursor-pointer">
     <Button>클릭</Button>
   </div>
   ```

6. **비활성화 상태도 명시적으로 지정**
   ```svelte
   <button disabled class="cursor-not-allowed ...">비활성화</button>
   ```

7. **Hover 상태에서도 cursor가 유지되는지 확인**
   - `hover:` 클래스가 cursor를 덮어쓰지 않도록 주의

### 3.3 구현 예시 (모두 따라야 할 필수 패턴)

```svelte
<!-- ✅ 올바른 예: 탑바 로그인 버튼 (Button href 사용) -->
<Button
  href="/user/login"
  class="cursor-pointer font-medium text-sm text-gray-900 hover:text-gray-700 transition-colors"
>
  로그인
</Button>

<!-- ✅ 올바른 예: 링크 (shadcn Button href 사용) - 최종 결과: <a> 태그 -->
<Button href="/profile" class="cursor-pointer">
  프로필로 이동
</Button>

<!-- ✅ 올바른 예: 일반 링크 -->
<a href="/profile" class="cursor-pointer text-blue-600 hover:text-blue-800">
  프로필
</a>

<!-- ✅ 올바른 예: shadcn Button 래핑 (href 미사용 시) -->
<div class="cursor-pointer">
  <Button onclick={handleGoogleLogin}>
    Google로 로그인
  </Button>
</div>

<!-- ✅ 올바른 예: 카드 -->
<div class="cursor-pointer p-4 hover:bg-gray-100 transition-colors">
  클릭 가능한 카드
</div>

<!-- ✅ 올바른 예: 비활성화 버튼 -->
<button
  disabled
  class="cursor-not-allowed opacity-50 text-gray-400"
>
  사용 불가
</button>

<!-- ❌ 절대 안 됨: cursor 없음 -->
<button onclick={handleClick}>클릭</button>

<!-- ❌ 절대 안 됨: hover에서 cursor 제거됨 -->
<button class="cursor-pointer hover:cursor-auto">클릭</button>
```

### 3.4 개발자 필독 사항 (반드시 읽으세요)

**🔥 이 규칙을 무시하는 코드는 승인되지 않습니다.**

- **모든 PR 리뷰에서 `cursor: pointer` 적용 여부를 확인합니다.**
- **스크린샷 테스트 시 손가락 커서가 명확히 보이지 않으면 재작업을 요청합니다.**
- **새로운 컴포넌트 추가 시, cursor-pointer 적용을 빠뜨린 경우 필수 지적 사항입니다.**
- **기존 컴포넌트도 `cursor: pointer`가 없으면 리팩토링 대상으로 지정합니다.**

### 3.5 QA 검증 체크리스트 (승인 조건)

**모든 항목이 ✅ 체크되어야 PR이 승인됩니다:**

- [ ] **버튼, 링크 등 모든 클릭 가능한 요소**에서 실제로 손가락 모양 커서(`👆`)가 표시되는지 마우스로 확인했다.
- [ ] **hover 상태**에서도 커서가 손가락 모양으로 유지되는지 확인했다.
- [ ] **모바일 에뮬레이션 환경**에서도 동일한 클래스가 적용되어 일관성이 있는지 확인했다.
- [ ] **비활성화된 요소**에는 `cursor-not-allowed`가 명시되어 있는지 확인했다.
- [ ] **네이티브 버튼/링크** (`<button>`, `<a>`)뿐만 아니라 **커스텀 요소** (`<div role="button">` 등)에도 `cursor-pointer`가 적용되어 있는지 확인했다.
- [ ] **shadcn-svelte 컴포넌트**의 경우, 컴포넌트 자체에 cursor가 없으면 **래퍼 div에 `cursor-pointer`를 반드시 추가**했는지 확인했다.
- [ ] **스크린샷을 데스크톱 브라우저에서 캡처**하여 손가락 커서가 명확히 보이는지 최종 검증했다.

### 3.6 문제 해결 가이드

**커서가 작동하지 않는 경우:**

1. **Tailwind 설정 확인**
   ```javascript
   // tailwind.config.js
   // cursor-pointer가 포함되어 있는지 확인
   ```

2. **CSS 우선순위 확인**
   - 다른 스타일이 `cursor` 속성을 덮어쓰지 않는지 확인
   - `!important` 사용이 필요한 경우도 있음

3. **요소가 실제로 클릭 가능한지 확인**
   - `pointer-events: none`이 적용되어 있지 않은지 확인
   - `z-index` 문제로 다른 요소가 위에 있지 않은지 확인

4. **개발자 도구에서 확인**
   ```javascript
   // 브라우저 콘솔에서 실행
   const el = document.querySelector('button');
   console.log(window.getComputedStyle(el).cursor);
   // 결과: "pointer" 이어야 함
   ```

### 3.7 금지 사항 (❌ Anti-patterns)

**다음의 패턴은 절대 사용하면 안 됩니다:**

#### ❌ 금지 1: `<a>` 태그 안에 `<button>` 태그 중첩

```svelte
<!-- ❌ 절대 금지: 시맨틱 오류, 접근성 문제 -->
<a href="/profile">
  <button>프로필</button>
</a>

<!-- ❌ 절대 금지: 버튼 안에 링크 -->
<button>
  <a href="/login">로그인</a>
</button>
```

**문제점:**
- HTML 시맨틱 구조 위반
- 스크린 리더기 및 보조 기술에서 혼동 발생
- 클릭 이벤트 처리 복잡화
- 브라우저 기본 동작 예측 불가능

**✅ 올바른 대안:**

**옵션 1: 🔥 shadcn Button href 사용 (권장 - 최종 결과: `<a>` 태그)**
```svelte
<!-- ✅ 올바름: Button href 속성 사용 - 시맨틱하고 간단함 -->
<Button href="/profile" class="cursor-pointer">
  프로필로 이동
</Button>
```

**옵션 2: `<a>` 태그를 버튼처럼 스타일링**
```svelte
<!-- ✅ 올바름: a 태그를 버튼 스타일로 -->
<a
  href="/profile"
  class="cursor-pointer inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
>
  프로필로 이동
</a>
```

**옵션 3: 네비게이션이 필요 없으면 `<button>` 사용**
```svelte
<!-- ✅ 올바름: 페이지 이동 없이 액션만 실행 -->
<button
  class="cursor-pointer px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
  onclick={handleProfileClick}
>
  프로필 보기
</button>
```

**옵션 4: 프로그래밍적 네비게이션이 필요한 경우**
```svelte
<!-- ✅ 올바름: button에서 프로그래밍적으로 네비게이션 -->
<script>
  import { goto } from '$app/navigation';

  async function navigateToProfile() {
    await goto('/profile');
  }
</script>

<button
  class="cursor-pointer px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
  onclick={navigateToProfile}
>
  프로필로 이동
</button>
```

#### ❌ 금지 2: cursor 없이 상호작용 요소 생성

```svelte
<!-- ❌ 절대 금지: cursor 없음 -->
<div onclick={handleClick}>
  클릭해주세요
</div>

<!-- ❌ 절대 금지: hover에서 cursor 제거 -->
<button class="cursor-pointer hover:cursor-auto">
  클릭
</button>
```

#### ❌ 금지 3: 시맨틱 무시한 커스텀 버튼

```svelte
<!-- ❌ 절대 금지: role 없이 커스텀 인터랙션 -->
<div class="cursor-pointer" onclick={handleClick}>
  클릭
</div>

<!-- ✅ 올바름: role 지정 필수 -->
<div
  class="cursor-pointer"
  role="button"
  tabindex="0"
  onclick={handleClick}
  onkeydown={(e) => e.key === 'Enter' && handleClick()}
>
  클릭
</div>
```

### 3.8 권장 사항 (✅ Best Practices)

#### ✅ 권장 1: shadcn-svelte 컴포넌트 우선 사용

**모든 UI/UX 작업에서 가장 먼저 shadcn을 쓸 수 있는지 확인하고, 가능하면 shadcn을 사용합니다.**

**이유:**
- 접근성(Accessibility) 자동 보장
- 일관된 디자인 언어 유지
- 이미 구현된 상호작용 로직 재사용
- 유지보수 편의성
- 브라우저 호환성 검증됨

#### ✅ 권장 2: Button 및 Link 컴포넌트 사용

**모든 button 또는 `<a>` 태그는 가능하면 shadcn의 Button 컴포넌트를 사용합니다.**

**특히 링크가 필요한 경우, Button 컴포넌트에 `href` 속성을 전달하면 최종 결과물이 `<a>` 태그로 자동 빌드됩니다:**

```svelte
<!-- ❌ 피해야 할 것: 직접 HTML 구현 -->
<button class="cursor-pointer px-4 py-2 bg-blue-600 text-white rounded">
  클릭
</button>

<!-- ✅ 권장: shadcn Button 사용 (액션) -->
<script>
  import { Button } from '$lib/components/ui/button';
</script>

<Button class="cursor-pointer" onclick={handleClick}>
  클릭
</Button>

<!-- ✅ 권장: shadcn Button href 사용 (링크 - 최종 결과: <a> 태그) -->
<Button href="/profile" class="cursor-pointer">
  프로필로 이동
</Button>
```

**이유:**
- 시맨틱 HTML 자동 처리 (href 있으면 `<a>`, 없으면 `<button>`)
- cursor-pointer 추가 불필요 (내부에서 관리 가능)
- 접근성 자동 보장
- 일관된 스타일 유지

#### ✅ 권장 3: shadcn 컴포넌트 선택 기준

| 요소 타입 | shadcn 컴포넌트 | 사용 시기 |
|----------|-----------------|---------|
| 일반 버튼 | `Button` | 모든 버튼 요소 |
| 링크 버튼 | `Button` (href 포함) | 버튼처럼 보이는 링크 - **최종 결과는 `<a>` 태그** |
| 텍스트 링크 | `<a>` + `cursor-pointer` | 인라인 텍스트 링크 |
| 아이콘 버튼 | `Button` + Icon | 아이콘만으로 표현 |
| 드롭다운 | `DropdownMenu` | 메뉴 네비게이션 |
| 토글 | `Toggle` | 토글 상태 |
| 체크박스 | `Checkbox` | 다중 선택 |
| 라디오 | `RadioGroup` | 단일 선택 |
| 입력 필드 | `Input` | 텍스트 입력 |
| 셀렉트 | `Select` | 목록 선택 |
| 카드 | `Card` | 콘텐츠 그룹핑 |
| 탭 | `Tabs` | 탭 네비게이션 |
| 모달 | `Dialog` | 모달 창 |

#### ✅ 권장 4: shadcn 커스터마이징

**shadcn 컴포넌트가 필요하지만 스타일을 조정해야 하는 경우:**

```svelte
<!-- ✅ 올바름: shadcn 베이스에 Tailwind 클래스로 커스터마이징 -->
<script>
  import { Button } from '$lib/components/ui/button';
</script>

<Button
  class="cursor-pointer bg-custom-color hover:bg-custom-color-dark rounded-lg px-6"
>
  커스텀 스타일 버튼
</Button>
```

**주의사항:**
- shadcn 컴포넌트의 기본 구조를 변경하지 않는다
- 색상, 간격, 크기만 조정한다
- `cursor-pointer`는 여전히 명시적으로 추가한다

#### ✅ 권장 5: 커스텀 컴포넌트가 필요한 경우만 구현

**shadcn에 해당하는 컴포넌트가 없을 때만 커스텀 컴포넌트를 구현합니다.**

```svelte
<!-- ✅ 올바름: 커스텀이 필수일 때만 사용 -->
<script>
  // 고유한 비즈니스 로직이 필요한 경우
  import CustomProfileCard from '$lib/components/custom-profile-card.svelte';
</script>

<CustomProfileCard class="cursor-pointer" />
```

#### ✅ 권장 6: shadcn 설치 및 사용 확인

**새로운 컴포넌트 추가 전에 다음을 확인하세요:**

```bash
# shadcn-svelte 컴포넌트 목록 확인
npm list shadcn-svelte

# 필요한 컴포넌트가 설치되어 있는지 확인
ls src/lib/components/ui/
```

**만약 필요한 컴포넌트가 없다면:**

```bash
# shadcn-svelte에서 설치
npx shadcn-svelte@latest add [component-name]

# 예시:
npx shadcn-svelte@latest add button
npx shadcn-svelte@latest add dropdown-menu
```

## 4. 통합 적용 절차

1. **디자인 반영**: 컴포넌트 생성 전 본 명세서와 `sonub-design-workflow.md`를 함께 참조하여 설계한다.
2. **구현**: 레이아웃 및 컴포넌트의 스타일 파일에 Light Mode 강제 스타일과 `cursor-pointer` 규칙을 추가한다.
3. **검증**: 시스템 다크 모드 환경에서 앱을 실행하여 밝은 테마 유지 여부와 커서 변화를 확인한다.
4. **리뷰 기록**: 변경 사항을 적용한 후 `./specs/*.md` SED 로그에 작업 내역을 남긴다.

## 5. 참고 자료

- [sonub-design-workflow.md](./sonub-design-workflow.md) - Tailwind/shadcn 활용 워크플로우
- [sonub-design-layout.md](./sonub-design-layout.md) - 레이아웃 및 네비게이션 구조
- [sonub-user-login.md](./sonub-user-login.md) - 로그인 UI 및 상호작용 명세
- [shadcn-svelte Button 공식 문서](https://www.shadcn-svelte.com/docs/components/button) - Button 컴포넌트 href 사용 방법 및 상세 가이드
