---
name: sonub-design-workflow
version: 1.0.0
description: TailwindCSS와 shadcn-svelte를 사용한 디자인 워크플로우 가이드라인
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
created: 2025-01-09
updated: 2025-01-09
step: 10
priority: "*"
dependencies: ["sonub-setup-tailwind.md", "sonub-setup-shadcn.md"]
tags: ["design", "tailwindcss", "shadcn", "ui", "styling"]
---

# Sonub Design Workflow - 디자인 워크플로우

## 1. 개요

### 1.1 목적

본 명세서는 Sonub 프로젝트의 모든 디자인 및 스타일링 작업에 대한 표준 워크플로우를 정의합니다. 일관된 디자인 시스템과 효율적인 개발을 위해 TailwindCSS와 shadcn-svelte를 기본 도구로 사용합니다.

### 1.2 범위

- TailwindCSS를 사용한 유틸리티 우선 스타일링
- shadcn-svelte 컴포넌트 사용 및 커스터마이징
- 디자인 토큰 및 테마 관리
- 반응형 디자인 가이드라인
- 접근성(a11y) 고려사항
- 다크 모드 지원

### 1.3 사전 요구사항

- ✅ TailwindCSS 설치 및 설정 완료 (sonub-setup-tailwind.md 참조)
- ✅ shadcn-svelte 설치 및 설정 완료 (sonub-setup-shadcn.md 참조)
- ✅ SvelteKit 5 프로젝트 환경

### 1.4 핵심 원칙

1. **TailwindCSS 우선**: 모든 스타일링은 TailwindCSS 유틸리티 클래스를 우선 사용
2. **shadcn-svelte 활용**: UI 컴포넌트는 shadcn-svelte 라이브러리를 최대한 활용
3. **커스텀 CSS 최소화**: 불가피한 경우를 제외하고 커스텀 CSS 작성 지양
4. **일관성 유지**: 프로젝트 전체에서 동일한 디자인 패턴 적용
5. **재사용성**: 공통 컴포넌트 및 스타일 패턴 재사용

## 2. 디자인 도구

### 2.1 TailwindCSS

**역할:** 유틸리티 우선 CSS 프레임워크

**사용 방법:**

```svelte
<!-- 좋은 예: TailwindCSS 유틸리티 클래스 사용 -->
<div class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md">
  <h2 class="text-xl font-bold text-gray-900">제목</h2>
  <button class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
    클릭
  </button>
</div>

<!-- 나쁜 예: 인라인 스타일 사용 -->
<div style="display: flex; padding: 16px; background: white;">
  <h2 style="font-size: 20px; font-weight: bold;">제목</h2>
</div>
```

**주요 활용 영역:**
- 레이아웃 (flexbox, grid)
- 간격 (padding, margin)
- 색상 및 배경
- 타이포그래피
- 반응형 디자인
- 호버, 포커스 등 상태 스타일

### 2.2 shadcn-svelte

**역할:** 사전 제작된 접근 가능한 UI 컴포넌트 라이브러리

**사용 가능한 주요 컴포넌트:**
- Button
- Card
- Dialog
- Input
- Label
- Select
- Checkbox
- Radio
- Switch
- Tabs
- Toast
- Alert
- Badge
- Avatar
- Dropdown Menu
- 기타 등등

**사용 방법:**

```svelte
<script>
  import { Button } from "$lib/components/ui/button";
  import { Card, CardContent, CardHeader, CardTitle } from "$lib/components/ui/card";
</script>

<Card>
  <CardHeader>
    <CardTitle>카드 제목</CardTitle>
  </CardHeader>
  <CardContent>
    <p>카드 내용</p>
    <Button>확인</Button>
  </CardContent>
</Card>
```

## 3. 디자인 워크플로우

### 3.1 새로운 UI 구현 절차

**단계별 워크플로우:**

```
1. 요구사항 분석
   ↓
2. shadcn-svelte 컴포넌트 확인
   - 사용 가능한 컴포넌트 검색
   - 공식 문서 참조: https://www.shadcn-svelte.com/docs
   ↓
3. 컴포넌트 선택 및 설치 (필요 시)
   - CLI로 필요한 컴포넌트 추가
   - 예: npx shadcn-svelte@latest add button
   ↓
4. TailwindCSS로 레이아웃 구성
   - 컨테이너 및 그리드 설정
   - 간격 및 정렬 조정
   ↓
5. shadcn 컴포넌트 배치
   - 선택한 컴포넌트 import 및 사용
   - 필요 시 props로 커스터마이징
   ↓
6. TailwindCSS로 세부 스타일링
   - 색상, 폰트, 간격 조정
   - 반응형 클래스 추가
   ↓
7. 반응형 및 접근성 검증
   - 모바일, 태블릿, 데스크톱 확인
   - 키보드 내비게이션 테스트
   ↓
8. 완료
```

### 3.2 컴포넌트 커스터마이징

**shadcn 컴포넌트 커스터마이징 방법:**

```svelte
<script>
  import { Button } from "$lib/components/ui/button";
</script>

<!-- 1. class prop을 통한 스타일 오버라이드 -->
<Button class="bg-purple-600 hover:bg-purple-700">
  커스텀 색상 버튼
</Button>

<!-- 2. variant 및 size props 활용 -->
<Button variant="outline" size="lg">
  큰 아웃라인 버튼
</Button>

<!-- 3. TailwindCSS 클래스와 조합 -->
<div class="flex gap-4 p-6">
  <Button variant="default">기본</Button>
  <Button variant="destructive">삭제</Button>
  <Button variant="ghost">고스트</Button>
</div>
```

### 3.3 커스텀 CSS 작성 (최후의 수단)

**언제 커스텀 CSS를 작성할 수 있는가:**

- ✅ TailwindCSS로 불가능한 복잡한 애니메이션
- ✅ 특정 브라우저 핵 필요 시
- ✅ 써드파티 라이브러리 스타일 오버라이드

**커스텀 CSS 작성 가이드라인:**

```css
/* src/app.css 또는 컴포넌트 <style> 블록 */

/* 좋은 예: Tailwind @apply 지시어 사용 */
.custom-button {
  @apply px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700;
}

/* 좋은 예: CSS 변수와 Tailwind 통합 */
:root {
  --custom-spacing: 2rem;
}

.custom-container {
  padding: var(--custom-spacing);
  @apply max-w-7xl mx-auto;
}

/* 나쁜 예: 순수 CSS 작성 */
.bad-button {
  padding: 8px 16px;
  background-color: #3b82f6;
  color: white;
  border-radius: 4px;
}
```

## 4. 디자인 토큰

### 4.1 색상 시스템

**TailwindCSS 기본 색상 팔레트 사용:**

```javascript
// tailwind.config.js
export default {
  theme: {
    extend: {
      colors: {
        // shadcn-svelte 기본 색상
        border: "hsl(var(--border))",
        input: "hsl(var(--input))",
        ring: "hsl(var(--ring))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          DEFAULT: "hsl(var(--primary))",
          foreground: "hsl(var(--primary-foreground))"
        },
        secondary: {
          DEFAULT: "hsl(var(--secondary))",
          foreground: "hsl(var(--secondary-foreground))"
        },
        // 프로젝트 커스텀 색상 (필요 시 추가)
      }
    }
  }
};
```

**사용 예:**

```svelte
<div class="bg-background text-foreground">
  <h1 class="text-primary">제목</h1>
  <p class="text-secondary">설명</p>
</div>
```

### 4.2 간격 시스템

**TailwindCSS 간격 스케일 준수:**

```svelte
<!-- 간격 단위: 0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16, 20, 24, 32, 40, 48, 56, 64 -->
<div class="p-4">        <!-- padding: 1rem (16px) -->
  <div class="mb-6">     <!-- margin-bottom: 1.5rem (24px) -->
    <div class="space-y-4">  <!-- 자식 요소 간 수직 간격 1rem -->
      <p>항목 1</p>
      <p>항목 2</p>
    </div>
  </div>
</div>
```

### 4.3 타이포그래피

**TailwindCSS 타이포그래피 클래스:**

```svelte
<h1 class="text-4xl font-bold">Heading 1</h1>
<h2 class="text-3xl font-semibold">Heading 2</h2>
<h3 class="text-2xl font-medium">Heading 3</h3>
<h4 class="text-xl">Heading 4</h4>
<p class="text-base leading-relaxed">본문 텍스트</p>
<small class="text-sm text-muted-foreground">작은 텍스트</small>
```

## 5. 반응형 디자인

### 5.1 브레이크포인트

**TailwindCSS 기본 브레이크포인트:**

```
sm:  640px   (모바일 가로)
md:  768px   (태블릿)
lg:  1024px  (데스크톱)
xl:  1280px  (큰 데스크톱)
2xl: 1536px  (초대형 화면)
```

### 5.2 반응형 스타일링 예제

```svelte
<!-- 모바일 우선 접근 -->
<div class="
  flex flex-col         /* 모바일: 세로 배치 */
  md:flex-row          /* 태블릿 이상: 가로 배치 */
  gap-4 
  p-4 
  md:p-6               /* 태블릿 이상: 큰 패딩 */
  lg:p-8               /* 데스크톱 이상: 더 큰 패딩 */
">
  <div class="w-full md:w-1/2">좌측 내용</div>
  <div class="w-full md:w-1/2">우측 내용</div>
</div>
```

## 6. 다크 모드

### 6.1 다크 모드 설정

**TailwindCSS 다크 모드 활성화:**

```javascript
// tailwind.config.js
export default {
  darkMode: 'class', // 'class' 또는 'media'
  // ...
};
```

### 6.2 다크 모드 스타일링

```svelte
<div class="
  bg-white dark:bg-gray-900
  text-gray-900 dark:text-white
  border border-gray-200 dark:border-gray-700
">
  <h1 class="text-black dark:text-white">제목</h1>
  <p class="text-gray-700 dark:text-gray-300">본문</p>
</div>
```

## 7. 접근성 (Accessibility)

### 7.1 접근성 체크리스트

- ✅ **시맨틱 HTML**: 적절한 HTML 태그 사용 (button, nav, main 등)
- ✅ **키보드 내비게이션**: Tab, Enter, Space 키 지원
- ✅ **ARIA 레이블**: 스크린 리더를 위한 레이블 제공
- ✅ **색상 대비**: WCAG 2.1 AA 기준 준수 (최소 4.5:1)
- ✅ **포커스 표시**: 키보드 포커스 시 명확한 아웃라인 표시

### 7.2 접근성 예제

```svelte
<script>
  import { Button } from "$lib/components/ui/button";
</script>

<!-- 좋은 예: 접근성 고려 -->
<button
  type="button"
  aria-label="닫기"
  class="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
>
  <svg>...</svg>
</button>

<!-- shadcn 컴포넌트는 기본적으로 접근성 지원 -->
<Button aria-label="로그인">로그인</Button>
```

## 8. 예제 및 패턴

### 8.1 로그인 폼 예제

```svelte
<script>
  import { Button } from "$lib/components/ui/button";
  import { Card, CardContent, CardHeader, CardTitle } from "$lib/components/ui/card";
  import { Input } from "$lib/components/ui/input";
  import { Label } from "$lib/components/ui/label";
</script>

<div class="flex items-center justify-center min-h-screen bg-gray-50">
  <Card class="w-full max-w-md">
    <CardHeader>
      <CardTitle class="text-2xl">로그인</CardTitle>
    </CardHeader>
    <CardContent class="space-y-4">
      <div class="space-y-2">
        <Label for="email">이메일</Label>
        <Input id="email" type="email" placeholder="example@email.com" />
      </div>
      <div class="space-y-2">
        <Label for="password">비밀번호</Label>
        <Input id="password" type="password" />
      </div>
      <Button class="w-full">로그인</Button>
    </CardContent>
  </Card>
</div>
```

### 8.2 네비게이션 바 예제

```svelte
<script>
  import { Button } from "$lib/components/ui/button";
</script>

<nav class="border-b bg-white">
  <div class="container mx-auto px-4">
    <div class="flex h-16 items-center justify-between">
      <div class="flex items-center gap-8">
        <a href="/" class="text-xl font-bold">Sonub</a>
        <div class="hidden md:flex gap-4">
          <a href="/about" class="text-gray-600 hover:text-gray-900">소개</a>
          <a href="/products" class="text-gray-600 hover:text-gray-900">제품</a>
          <a href="/contact" class="text-gray-600 hover:text-gray-900">연락</a>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <Button variant="ghost">로그인</Button>
        <Button>회원가입</Button>
      </div>
    </div>
  </div>
</nav>
```

## 9. 금지 사항

### 9.1 절대 하지 말아야 할 것

- ❌ **인라인 스타일 사용 금지**: `style="..."` 대신 TailwindCSS 클래스 사용
- ❌ **임의의 CSS 클래스 생성 금지**: 재사용 불가능한 커스텀 클래스 생성 지양
- ❌ **shadcn 컴포넌트 무시 금지**: 이미 있는 컴포넌트를 처음부터 만들지 말 것
- ❌ **CSS 프레임워크 혼용 금지**: Bootstrap, Material-UI 등 다른 프레임워크 사용 금지
- ❌ **!important 남용 금지**: 특수한 경우를 제외하고 !important 사용 지양

### 9.2 예외 상황

다음의 경우에만 위 규칙을 예외적으로 적용할 수 있습니다:

1. **써드파티 라이브러리 통합**: 외부 라이브러리의 스타일을 오버라이드해야 할 때
2. **복잡한 애니메이션**: TailwindCSS로 불가능한 고급 애니메이션 구현 시
3. **레거시 코드 유지보수**: 기존 커스텀 CSS 코드를 점진적으로 마이그레이션하는 경우

## 10. 워크플로우 체크리스트

### 10.1 디자인 작업 전 체크리스트

- [ ] TailwindCSS 설정 파일 확인 (`tailwind.config.js`)
- [ ] shadcn-svelte 설치 및 설정 확인
- [ ] 필요한 shadcn 컴포넌트 확인 및 설치
- [ ] 색상 및 테마 토큰 확인

### 10.2 디자인 작업 중 체크리스트

- [ ] TailwindCSS 유틸리티 클래스 우선 사용
- [ ] shadcn-svelte 컴포넌트 최대한 활용
- [ ] 반응형 클래스 적용 (sm:, md:, lg: 등)
- [ ] 다크 모드 고려 (dark: prefix)
- [ ] 접근성 고려 (aria-label, 키보드 내비게이션 등)

### 10.3 디자인 작업 후 체크리스트

- [ ] 모바일, 태블릿, 데스크톱 반응형 확인
- [ ] 다크 모드 동작 확인
- [ ] 키보드 내비게이션 테스트
- [ ] 색상 대비 확인 (접근성)
- [ ] 불필요한 커스텀 CSS 제거

## 11. 추가 리소스

### 11.1 공식 문서

- **TailwindCSS**: https://tailwindcss.com/docs
- **shadcn-svelte**: https://www.shadcn-svelte.com/docs
- **Svelte 5**: https://svelte.dev/docs

### 11.2 유용한 도구

- **Tailwind CSS IntelliSense**: VS Code 확장 프로그램
- **Headless UI**: 접근 가능한 UI 컴포넌트 참고용
- **Color Contrast Checker**: 접근성 색상 대비 확인 도구

## 12. 결론

모든 디자인 작업은 **반드시** TailwindCSS와 shadcn-svelte를 통해 구현해야 합니다. 이는 코드 일관성, 유지보수성, 그리고 개발 효율성을 극대화하기 위한 필수 원칙입니다.

**핵심 규칙 요약:**
1. TailwindCSS 유틸리티 클래스 우선 사용
2. shadcn-svelte 컴포넌트 최대한 활용
3. 커스텀 CSS는 최후의 수단
4. 반응형 및 접근성 필수 고려
5. 일관된 디자인 패턴 유지