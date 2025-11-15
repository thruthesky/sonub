---
title: button.css
type: stylesheet
path: src/stories/button.css
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/stories/button.css`의 소스 코드를 포함하는 SED 스펙 문서입니다.

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

## 변경 이력

- 2025-11-15: 스펙 문서 생성
