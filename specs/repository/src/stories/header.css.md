---
name: header.css
description: Storybook 헤더 컴포넌트 스타일 정의 파일
version: 1.0.0
type: css
category: storybook
tags: [storybook, header, component, styling]
---

# header.css

## 개요
이 파일은 Storybook에서 사용되는 헤더 컴포넌트의 스타일을 정의합니다. Flexbox 레이아웃을 사용하여 로고, 제목, 버튼들을 수평 정렬합니다.

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

### 레이아웃
- **Flexbox**: 좌우 끝 정렬 (`justify-content: space-between`)
- **수직 중앙 정렬**: `align-items: center`
- **하단 테두리**: 1px solid rgba(0, 0, 0, 0.1)
- **패딩**: 15px 상하, 20px 좌우

### 자식 요소 스타일
- **SVG 로고**: inline-block + vertical-align top
- **제목 (h1)**: 20px 폰트, 700 weight, 10px 좌측 마진
- **버튼 간격**: 인접 버튼 사이 10px 좌측 마진
- **환영 메시지**: 14px 폰트, 10px 우측 마진

## 사용 예시
```html
<header class="storybook-header">
  <div>
    <svg><!-- 로고 --></svg>
    <h1>My App</h1>
  </div>
  <div>
    <span class="welcome">환영합니다!</span>
    <button>로그인</button>
    <button>회원가입</button>
  </div>
</header>
```
