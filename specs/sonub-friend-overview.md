---
name: sonub
version: 1.0.0
description: 친구/팔로잉/팔로워 관계 및 홈 피드 연동 개요
author: JaeHo Song
email: thruthesky@gmail.com
license: GPL-3.0
step: 55
priority: "**"
dependencies:
  - sonub-user-overview.md
  - sonub-firebase-database-structure.md
  - sonub-store-user-profile.md
tags:
  - friends
  - following
  - follower
  - feed
  - social
---

## 개요
- 본 문서는 Sonub에서 **친구(Following/Follower)** 관계를 정의하고, 친구의 활동을 사용자의 홈 피드에 노출시키기 위한 데이터 구조와 워크플로를 설명합니다.
- Chat 탭과 게시판에서 모두 동일한 친구 관계 정보를 공유하며, 채팅 초대/추천/탐색 기능과 홈 피드를 하나의 스펙으로 관리합니다.

## 친구 관계의 정의 및 용어

본 섹션에서는 Sonub의 친구 관계를 구성하는 핵심 용어와 개념을 명확히 정의합니다. 이를 통해 개발 과정에서의 혼동을 방지하고 일관된 명칭을 사용합니다.

### 1. 팔로우 (Follow)

**정의**: 내가 그 사람을 따르겠다, 내가 그 사람의 글(또는 메시지)을 구독하겠다는 의미입니다.

**동작 방식**:
- A가 B를 팔로우하면, B가 작성한 글이 A에게 전달됩니다.
- 이렇게 전달되는 형상(동작 방식)을 **피드(Feed)**라고 합니다.

**예시**:
- 사용자 A가 사용자 B를 팔로우
- B가 새 게시글을 작성
- A의 홈 피드에 B의 게시글이 표시됨

### 2. 팔로잉 (Following)

**정의**: 내가 이 사람을 팔로우하고 있다(Follow+ing)라는 의미입니다.

**관계 표현**:
- A가 B를 팔로우한 상태이면, "A가 B를 팔로잉한다"고 표현합니다.
- 팔로우와 팔로잉은 같은 의미로 사용됩니다.
- 차이점: 팔로우는 **동작(행위)**을, 팔로잉은 **상태(진행 중)**를 강조합니다.

**데이터 저장**:
- Firebase 경로: `/user-following/{uid}/{targetUid}: true`
- A의 팔로잉 목록에 B가 포함됨

### 3. 팔로워 (Follower)

**정의**: 따르는 사람, 팬을 의미합니다.

**관계 표현**:
- A가 B를 팔로잉하면, **A는 B의 팔로워**가 됩니다.
- 즉, 나의 팔로워가 많다는 의미는 나를 팔로우한 사람이 많다는 의미입니다.

**관점의 차이**:
- **A 입장**: B를 "팔로잉"하는 것
- **B 입장**: A가 "팔로워"가 됨 (A가 B의 팬)

**데이터 저장**:
- Firebase 경로: `/user-followers/{uid}/{followerUid}: true`
- B의 팔로워 목록에 A가 포함됨

### 4. 친구 (Friend) 관계 또는 맞팔로우

**정의**: 맞팔로우는 서로 팔로우한 상태를 의미합니다.

**조건**:
- A가 B를 팔로우하고
- B도 A를 팔로우하는 경우

**명칭**:
- 이러한 상호 팔로우 관계를 **"친구(Friend)" 관계**라고 부릅니다.
- 또는 **"맞팔로우(Mutual Follow)"**라고도 표현합니다.

**UI 표시**:
- 프로필 화면에서 "친구" 또는 "상호 팔로우" 상태로 표시
- 일반 팔로우/팔로잉과 구분하여 사용자에게 관계를 명확히 전달

### 용어 정리 표

| 용어 | 영문 | 정의 | 데이터 경로 |
|------|------|------|------------|
| 팔로우 | Follow | 내가 그 사람의 글을 구독하는 행위 | `/user-following/{me}/{target}` |
| 팔로잉 | Following | 내가 팔로우하고 있는 상태 | `/user-following/{me}/{target}` |
| 팔로워 | Follower | 나를 팔로우하는 사람 (나의 팬) | `/user-followers/{me}/{follower}` |
| 친구/맞팔로우 | Friend/Mutual Follow | 서로 팔로우한 상태 | 양쪽 경로 모두 존재 |

## 핵심 목표
1. `팔로우 → 팔로잉 목록 업데이트 → 홈 피드 반영`까지 일관된 UX 제공
2. `/user-following`, `/user-followers` 두 경로만으로 관계 파생 데이터를 구성하여 유지보수 최소화
3. Cloud Functions가 게시글/메시지 생성 시 팔로워에게 알림·피드 반영을 수행
4. Privacy 컨트롤(차단, 공개 범위) 확장을 고려한 구조 유지

## 데이터 구조
- `/user-following/{uid}/{targetUid}: true`  
  - 사용자가 팔로우한 대상 UID 목록
- `/user-followers/{uid}/{followerUid}: true`  
  - 사용자를 팔로우하는 UID 목록 (역참조)
- `/user-feed/{uid}/{feedId}`  
  - 팔로우한 친구가 작성한 메시지·포스트의 요약
- Cloud Functions 파생 데이터:
  - `onFollowCreate`: `/user-followers` 동기화 및 알림
  - `onPostOrMessageCreate`: 작성자의 followers를 순회하여 `/user-feed` 업데이트

## UI/UX 요구사항
1. **프로필/친구 버튼**
   - `/user/{uid}` 또는 `/my/profile`에서 `팔로우/언팔로우` 버튼 제공
   - 상태(팔로잉/상호팔로우/미팔로우)를 실시간 표시
2. **친구 찾기**
   - `ChatListMenu`의 "친구" 탭에서 `친구 찾기` CTA와 추천 목록 제공
   - 추천 목록: 최근 가입자, 공통 친구 수, 관심사 태그 기준
3. **홈 피드**
   - `/` 또는 `/feed`에서 `user-feed` 데이터로 카드 렌더링
   - 카드 병합 규칙: 동일 사용자의 연속 포스트는 그룹화, 읽음 처리 필드 포함
4. **알림**
   - 새 팔로워 발생 시 `/notifications/{uid}`에 추가하고 TopBar 벨 아이콘 배지 증가

## 동작 플로우
1. 사용자가 `팔로우` 버튼 클릭 → `/user-following/{me}/{target}` true 설정
2. Cloud Functions `onUserFollowingCreate`
   - `/user-followers/{target}/{me}` true 설정
   - target에게 알림 기록 + FCM 전송
3. target이 글/메시지 게시 → `onPostCreate`/`onMessageCreate`가 followers 목록을 순회
   - 각 follower의 `/user-feed/{follower}/{feedId}`에 카드 요약 저장
   - 필요 시 FCM "친구가 새 글을 올렸습니다" 전송
4. 사용자가 홈 피드에서 카드를 열람하면 `read: true` 업데이트 → badge 감소

## QA 체크리스트
- [ ] 팔로우/언팔로우 시 `/user-following`, `/user-followers`가 정확히 동기화되는지 확인
- [ ] 팔로잉 사용자만 홈 피드 카드가 나타나는지 수동 테스트
- [ ] Cloud Functions 오류 시 재시도 로직(Dead letter) 로그 확인
- [ ] FCM 토픽/단건 전송이 중복되지 않는지 BroadcastChannel 로그 검토

## 향후 작업
- 친구 추천 알고리즘 강화 (공통 관심사, 위치 기반)
- 차단/숨기기 기능 추가
- `/user-feed` 데이터 정리 배치 (30일 지난 항목 삭제)

---

## 작업 이력 (SED Log)

| 날짜 | 작업자 | 변경 내용 |
| ---- | ------ | -------- |
| 2025-11-15 | Claude Code | "친구 관계의 정의 및 용어" 섹션 추가: 팔로우/팔로잉/팔로워/친구 관계의 명확한 정의 및 용어 정리 표 추가 |
