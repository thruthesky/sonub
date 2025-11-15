---
title: header.css
type: stylesheet
path: src/stories/header.css
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/stories/header.css`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```css
.storybook-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  padding: 15px 20px;
  font-family: 'Nunito Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
}

.storybook-header svg {
  display: inline-block;
  vertical-align: top;
}

.storybook-header h1 {
  display: inline-block;
  vertical-align: top;
  margin: 6px 0 6px 10px;
  font-weight: 700;
  font-size: 20px;
  line-height: 1;
}

.storybook-header button + button {
  margin-left: 10px;
}

.storybook-header .welcome {
  margin-right: 10px;
  color: #333;
  font-size: 14px;
}

```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
