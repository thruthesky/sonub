이 함수는 sonub 프로젝트를 개발하기 위한 전반적인 개요를 제공합니다. 주요 구성 요소, 기술 스택, 아키텍처 및 개발 지침을 포함합니다.

# Sonub 프로젝트 개요

- Sonub 는 Social Network Hub 의 약자로, 사용자들이 소셜 네트워킹을 할 수 있는 플랫폼입니다.
- 본 SED 스펙 문서는 https://sedai.dev 를 바탕으로 한 소셜 네트워킹 서비스 웹/앱 개발 문서입니다.
  - Svelte 5 과 Firebase 를 통한 웹앱 서비스를 개발 할 수 있는 완전한 SED 스펙을 포함하여, 손 쉽게 바이브 코딩으로 소셜 서비스를 만들 수 있게 해 줍니다.
  - 즉, 본 Sonub SED 스펙의 목적은 바이브 코딩으로 소셜 웹 서비스를 개발하기 위한 것입니다. 
- 주요 기능으로는 사용자 프로필 관리, 친구 추가, 채팅, 게시물 작성 및 피드, 푸시 알림 등이 있습니다.


- 본 Sonub SED 스펙의 저작권은 송재호 (연락처: thruthesky@gmail.com) 님에게 있으며 만약, 여러분들이 이 Sonub SED 스펙을 통해서 개발하는데 어려움이 있거나 또는 질문이 있다면, 언제든지 연락을 주세요. 함께 해결해 나가도록 합시다.


# 프로젝트 셋업

- 본 항목에서는 Sonub 프로젝트를 설정하는 방법에 대해 설명합니다.
- SvelteKit, Tailwind CSS, shadcn-svelte, Firebase 등을 사용하여 프로젝트를 설정합니다.


# 프로젝트 소스 구성

- 본 항목에서는 Sonub 프로젝트의 소스 코드 구조에 대해 설명합니다.
- 주요 디렉토리 및 파일의 역할과 위치를 설명합니다.
- 예: src/lib/components, src/routes, firebase/functions 등

## Sonub 아키텍쳐
- 본 항목에서는 Sonub 프로젝트의 아키텍처에 대해 설명합니다.
- Sonub 프로젝트는 클라이언트, 서버 및 shared 모듈로 구성됩니다


## client 모듈
- 본 항목에서는 Sonub 프로젝트의 클라이언트 모듈에 대해 설명합니다.
- 클라이언트 모듈은 SvelteKit 기반의 웹앱 소스 코드를 포함합니다.
## server 모듈
- 본 항목에서는 Sonub 프로젝트의 서버 모듈에 대해 설명합니다.
- 서버 모듈은 Firebase Cloud Functions 기반의 서버 소스 코드를 포함합니다.

## shared 모듈

- 본 항목에서는 Sonub 프로젝트에서 서버와 클라이언트가 공유하는 shared 모듈에 대해 설명합니다.
- shared 모듈은 서버(Firebase Cloud Functions)와 클라이언트(Svelte) 양쪽에서 사용되는 **100% 순수 함수(Pure Functions)**를 포함합니다.

### shared 모듈 특징

**위치**: `/Users/thruthesky/apps/sonub/shared/`

**핵심 원칙**:
- ✅ **Zero External Dependencies**: 외부 라이브러리나 프레임워크에 전혀 의존하지 않음
- ✅ **Framework Independent**: Firebase, Svelte, Paraglide 등 어떤 프레임워크와도 독립적
- ✅ **Pure Functions Only**: 동일한 입력에 항상 동일한 출력을 보장
- ✅ **서버/클라이언트 코드 재사용**: 동일한 비즈니스 로직을 중복 없이 양쪽에서 사용

### shared 모듈 구조

```
shared/
├── date.pure-functions.ts      # 날짜 관련 순수 함수
│   ├── formatLongDate()        # 긴 형식 날짜 포맷팅
│   └── formatShortDate()       # 짧은 형식 날짜 포맷팅
│
└── chat.pure-functions.ts      # 채팅 관련 순수 함수
    ├── buildSingleRoomId()         # 1:1 채팅방 ID 생성
    ├── isSingleChat()              # 1:1 채팅 여부 확인
    ├── extractUidsFromSingleRoomId() # roomId에서 UID 추출
    └── resolveRoomTypeLabel()      # 채팅방 타입 레이블 변환
```

### 사용 패턴

**클라이언트 (Svelte)**:
```typescript
// src/lib/functions/date.functions.ts
import { formatLongDate as pure } from '$shared/date.pure-functions';

// Paraglide locale을 주입하는 wrapper
export function formatLongDate(ts: number) {
  return pure(ts, getLocale());
}
```

**서버 (Firebase Functions)**:
```typescript
// firebase/functions/src/handlers/notification.handler.ts
import { formatLongDate } from '../../../../shared/date.pure-functions';

// 직접 사용 (locale 명시적 전달)
const formatted = formatLongDate(timestamp, 'ko-KR');
```

### 관련 문서

- [Sonub Shared Date Pure Functions](./sonub-shared-date-pure-functions.md) - 날짜 순수 함수 상세 스펙
- [Sonub Shared Chat Pure Functions](./sonub-shared-chat-pure-functions.md) - 채팅 순수 함수 상세 스펙
- [Sonub Functions Overview](./sonub-functions-overview.md) - Functions 아키텍처 전체 개요



# 개발 지침
- 본 항목에서는 Sonub 프로젝트 개발 시 따라야 할 지침을 제공합니다.
- 코드 스타일, 커밋 메시지 규칙, 테스트 작성 방법 등을 포함합니다.



# 배포 및 운영
- 본 항목에서는 Sonub 프로젝트의 배포 및 운영 방법에 대해 설명합니다.
- Firebase Hosting, Cloud Functions 배포 방법 및 운영 시 고려사항 등을 포함합니다.


# 유지보수 및 확장
- 본 항목에서는 Sonub 프로젝트의 유지보수 및 확장 방법에 대해 설명합니다.
- 새로운 기능 추가, 버그 수정 및 성능 최적화 방법 등을 포함합니다.



# 이슈 및 피드백
- 본 항목에서는 Sonub 프로젝트에 대한 이슈 보고 및 피드백 제공 방법에 대해 설명합니다.
- Sonub SED 스펙은 https://sedai.dev 의 git issue 를 통해서 관리됩니다. 즉, Sonub SED 스펙에 대한 이슈가 있거나 또는 피드백이 있다면, https://github.com/thruthesky/sedai/issues 를 통해서 알려주시기 바랍니다.