---
name: button.css
description: Storybook 버튼 컴포넌트 스타일 정의 파일
version: 1.0.0
type: css
category: storybook
tags: [storybook, button, component, styling]
---

# button.css

## 개요
이 파일은 Storybook에서 사용되는 버튼 컴포넌트의 스타일을 정의합니다. Primary와 Secondary 두 가지 스타일 변형과 Small, Medium, Large 세 가지 크기 변형을 제공합니다.

## 소스 코드

```css
.storybook-button {
  display: inline-block;
  cursor: pointer;
  border: 0;
  border-radius: 3em;
  font-weight: 700;
  line-height: 1;
  font-family: 'Nunito Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
}
.storybook-button--primary {
  background-color: #555ab9;
  color: white;
}
.storybook-button--secondary {
  box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 0px 1px inset;
  background-color: transparent;
  color: #333;
}
.storybook-button--small {
  padding: 10px 16px;
  font-size: 12px;
}
.storybook-button--medium {
  padding: 11px 20px;
  font-size: 14px;
}
.storybook-button--large {
  padding: 12px 24px;
  font-size: 16px;
}
```

## 주요 기능

### 버튼 스타일 변형
- **Primary**: 보라색 배경 (`#555ab9`) + 흰색 텍스트
- **Secondary**: 투명 배경 + 검은색 테두리 + 어두운 텍스트

### 버튼 크기 변형
- **Small**: 10px/16px 패딩, 12px 폰트
- **Medium**: 11px/20px 패딩, 14px 폰트
- **Large**: 12px/24px 패딩, 16px 폰트

### 공통 스타일
- 둥근 모서리 (3em border-radius)
- 굵은 폰트 (700 weight)
- Nunito Sans 폰트 패밀리

## 사용 예시
```html
<!-- Primary 버튼 (Medium) -->
<button class="storybook-button storybook-button--primary storybook-button--medium">
  Click me
</button>

<!-- Secondary 버튼 (Large) -->
<button class="storybook-button storybook-button--secondary storybook-button--large">
  Learn More
</button>
```
