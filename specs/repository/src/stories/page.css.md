---
name: page.css
description: Storybook 페이지 컴포넌트 스타일 정의 파일
version: 1.0.0
type: css
category: storybook
tags: [storybook, page, component, styling, typography]
---

# page.css

## 개요
이 파일은 Storybook에서 사용되는 페이지 컴포넌트의 스타일을 정의합니다. 타이포그래피, 링크, 리스트, 팁 섹션 등 문서 스타일을 제공합니다.

## 소스 코드

```css
.storybook-page {
  margin: 0 auto;
  padding: 48px 20px;
  max-width: 600px;
  color: #333;
  font-size: 14px;
  line-height: 24px;
  font-family: 'Nunito Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
}

.storybook-page h2 {
  display: inline-block;
  vertical-align: top;
  margin: 0 0 4px;
  font-weight: 700;
  font-size: 32px;
  line-height: 1;
}

.storybook-page p {
  margin: 1em 0;
}

.storybook-page a {
  color: inherit;
}

.storybook-page ul {
  margin: 1em 0;
  padding-left: 30px;
}

.storybook-page li {
  margin-bottom: 8px;
}

.storybook-page .tip {
  display: inline-block;
  vertical-align: top;
  margin-right: 10px;
  border-radius: 1em;
  background: #e7fdd8;
  padding: 4px 12px;
  color: #357a14;
  font-weight: 700;
  font-size: 11px;
  line-height: 12px;
}

.storybook-page .tip-wrapper {
  margin-top: 40px;
  margin-bottom: 40px;
  font-size: 13px;
  line-height: 20px;
}

.storybook-page .tip-wrapper svg {
  display: inline-block;
  vertical-align: top;
  margin-top: 3px;
  margin-right: 4px;
  width: 12px;
  height: 12px;
}

.storybook-page .tip-wrapper svg path {
  fill: #1ea7fd;
}
```

## 주요 기능

### 페이지 레이아웃
- **최대 너비**: 600px
- **중앙 정렬**: margin 0 auto
- **패딩**: 48px 상하, 20px 좌우
- **기본 폰트**: 14px, 24px line-height

### 타이포그래피
- **h2**: 32px 굵은 폰트, 4px 하단 마진
- **p**: 1em 상하 마진
- **a**: 부모 색상 상속
- **ul**: 30px 좌측 패딩, 1em 상하 마진
- **li**: 8px 하단 마진

### 팁 섹션
- **배지 스타일**: 둥근 모서리, 연두색 배경 (#e7fdd8)
- **팁 색상**: 초록색 텍스트 (#357a14)
- **팁 래퍼**: 40px 상하 마진
- **SVG 아이콘**: 12x12px, 파란색 (#1ea7fd)

## 사용 예시
```html
<div class="storybook-page">
  <h2>페이지 제목</h2>
  <p>본문 내용입니다.</p>

  <ul>
    <li>항목 1</li>
    <li>항목 2</li>
  </ul>

  <div class="tip-wrapper">
    <span class="tip">TIP</span>
    <span>유용한 정보를 여기에 표시합니다.</span>
  </div>
</div>
```
