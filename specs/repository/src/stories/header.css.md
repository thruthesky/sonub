---
name: header.css
description: header 파일
version: 1.0.0
type: css
category: other
original_path: src/stories/header.css
---

# header.css

## 개요

**파일 경로**: `src/stories/header.css`
**파일 타입**: css
**카테고리**: other

header 파일

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

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
