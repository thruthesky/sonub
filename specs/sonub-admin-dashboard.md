---
name: snsweb
version: 1.0.0
description: 관리자 대시보드에서 사용자 목록, 글, 댓글 목록 및 신고 내역, 각종 통계 등을 확인하고 처리하는 기능 제공.
author: JaeHo Song
email: thruthesky@gmail.com
homepage: https://github.com/thruthesky/
funding: ""
license: SED Specification License v1.0
dependencies: []
---



## Overview
관리자 대시보드에서 사용자 관리, 글 및 댓글 관리, 신고 내역 확인 및 처리, 통계 대시보드 기능을 제공하는 SED 사양 문서입니다. 이 문서는 관리자 대시보드의 데이터베이스 스키마, API 엔드포인트, 라우팅, UI/UX 요구사항, 테스트 사양을 상세히 설명합니다


## Requirements

- Firebase Cloud Functions 의 설정에 관리자 UID 를 기록해야 합니다. 이 UID 는 관리자 대시보드 접근 권한 확인에 사용됩니다.


## Workflow
1. 관리자 페이지 경로는 `/admin` 으로 설정합니다.
2. 사용자 관리, 글 관리, 댓글 관리, 신고 내역 관리, 통계 대시보드 등 주요 기능별로 섹션을 나눕니다.




## Details

### 1. 상단 탭 내비게이션 (NEW)
- **위치**: `/admin/+layout.svelte`
- **요구사항**:
  1. 관리자 페이지의 내비게이션은 **페이지 상단 탭바**로 제공한다.
  2. 탭 목록: `대시보드`, `테스트`, `사용자목록`, `신고 목록`.
  3. 각 탭은 `<a>`로 렌더링되며, 활성 탭은 파란색(Primary) 배경과 그림자를 가지고 다른 탭과 명확히 구분한다.
  4. 기존 사이드바(왼쪽 세로 메뉴)는 제거한다. Top tabs만 사용한다.
  5. 모바일(≤640px)에서는 탭이 세로 스택으로 정렬되고 전체 폭을 차지해야 한다.
  6. 접근성: `nav`에 `aria-label="관리자 상단 탭"`을 부여하고, 포커스/hover 시 시각적 피드백을 제공한다.
- **목적**: 대시보드 메뉴를 상단 탭 형태로 노출하여 관리자 도구 접근성을 개선하고, 디자인 가이드라인의 Light Mode 탭 스타일을 준수한다.

### 신고 목록 관리
- 경로: `/admin/reports`
- 신고된 글과 댓글을 목록으로 표시합니다.
