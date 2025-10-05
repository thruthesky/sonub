Sonub 웹사이트 개발을 위한 디자인 가이드라인
================================================

## 목차
- [개요](#개요)
- [디자인 철학](#디자인-철학)
- [색상 가이드라인](#색상-가이드라인)
- [Bootstrap 사용법](#bootstrap-사용법)

## 개요
- 이 문서는 Sonub 웹사이트 개발을 위한 디자인 가이드라인과 규칙을 명시합니다.
- 모든 개발자는 이 문서를 숙지하고 준수해야 합니다.

## 디자인 철학

### 라이트 모드만 지원
- **중요**: Sonub 웹사이트는 **라이트 모드만** 지원합니다
- 다크 모드 기능이나 다크 모드 전용 스타일을 **절대** 구현하지 마세요
- 모든 디자인 결정은 라이트 모드 외관에 최적화되어야 합니다

## 색상 가이드라인

### Bootstrap 색상 사용
- **항상** Bootstrap의 기본 색상 클래스와 변수를 사용하세요
- **권장하는** Bootstrap 색상 유틸리티:
  - 배경: `bg-primary`, `bg-secondary`, `bg-success`, `bg-danger`, `bg-warning`, `bg-info`, `bg-light`, `bg-dark`, `bg-white`
  - 텍스트: `text-primary`, `text-secondary`, `text-success`, `text-danger`, `text-warning`, `text-info`, `text-light`, `text-dark`, `text-white`, `text-muted`
  - 테두리: `border-primary`, `border-secondary` 등

### 커스텀 색상 사용 금지
- HEX 색상 코드를 **사용하지 마세요** (예: `#FF5733`)
- Bootstrap 팔레트 외의 CSS 색상 이름을 **사용하지 마세요** (예: `color: red`)
- **예외**: 브랜딩 요구사항에 꼭 필요한 경우에만 커스텀 색상 사용

## Bootstrap 사용법

### 컴포넌트 가이드라인
- Bootstrap 컴포넌트를 과도한 커스터마이징 없이 그대로 사용하세요
- 일관성을 위해 Bootstrap의 기본 스타일링을 활용하세요
- 간격, 크기, 레이아웃에는 Bootstrap 유틸리티 클래스를 사용하세요

### 올바른 사용 예제
```html
<!-- 좋은 예: Bootstrap 색상 클래스 사용 -->
<div class="bg-light text-dark p-3">
  <h1 class="text-primary">환영합니다</h1>
  <button class="btn btn-success">제출</button>
</div>

<!-- 나쁜 예: 커스텀 색상 사용 -->
<div style="background-color: #f0f0f0; color: #333;">
  <h1 style="color: blue;">환영합니다</h1>
  <button style="background: green;">제출</button>
</div>
```

### 반응형 디자인
- 항상 Bootstrap의 반응형 그리드 시스템을 사용하세요
- 다양한 화면 크기에서 레이아웃을 테스트하세요
- Bootstrap의 반응형 유틸리티 클래스를 사용하세요
