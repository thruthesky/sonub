# Sonub 채팅방 기능 사양

## 개요

Sonub 애플리케이션의 1:1 채팅방 및 그룹 채팅방 기능에 대한 상세 사양 문서입니다.

## 파일 위치

- **페이지**: [src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte)
- **다국어**: [messages/*.json](../messages/)

## 채팅방 유형

### 1:1 채팅방 (Direct Chat)

1:1 채팅방은 두 사용자 간의 개인 대화를 위한 채팅방입니다.

#### URL 파라미터

- **uid**: 채팅 상대방의 사용자 UID
- **형식**: `?uid=TARGET_UID`
- **예시**: `/chat/room?uid=abc123xyz`

#### 채팅방 ID 생성 규칙

```typescript
function buildDirectRoomId(a: string, b: string) {
  return `direct-${[a, b].sort().join('-')}`;
}
```

- 두 사용자의 UID를 알파벳 순으로 정렬하여 고정된 roomId 생성
- 예시: `direct-abc123-xyz789`
- 이렇게 하면 누가 먼저 채팅을 시작하든 동일한 채팅방 ID가 생성됩니다

### 그룹 채팅방 (Chat Room)

그룹 채팅방은 여러 사용자가 참여할 수 있는 채팅방입니다.

#### URL 파라미터

- **roomId**: 그룹 채팅방의 고유 ID
- **형식**: `?roomId=ROOM_KEY`
- **예시**: `/chat/room?roomId=general-chat`

## 주요 기능

### 1. 사용자 프로필 실시간 구독

채팅 상대방의 프로필 정보를 실시간으로 구독하여 표시합니다.

```typescript
// userProfileStore를 사용한 프로필 구독
$effect(() => {
  if (uidParam) {
    userProfileStore.ensureSubscribed(uidParam);
  }
});

// 프로필 데이터 접근
const targetProfile = $derived(userProfileStore.getCachedProfile(uidParam));
const targetProfileLoading = $derived(userProfileStore.isLoading(uidParam));
const targetProfileError = $derived(userProfileStore.getError(uidParam));
```

#### Firebase 경로

- **프로필 경로**: `/users/{uid}`
- **데이터 구조**:
  ```json
  {
    "displayName": "사용자 이름",
    "photoURL": "프로필 이미지 URL",
    "createdAt": 1234567890,
    "lastLoginAt": 1234567890
  }
  ```

### 2. 채팅방 헤더

채팅방 상단에 채팅 상대의 정보를 표시합니다.

#### 표시 정보

- **채팅 유형**: 1:1 채팅 또는 그룹 채팅방
- **상대방 이름**:
  - displayName이 있으면 표시
  - 없으면 `@{uid의 앞 6자리}` 형식으로 표시
  - 프로필이 없으면 다국어 메시지 `chatPartner` 표시
- **Avatar**: 64px 크기의 프로필 이미지
- **상태 메시지**:
  - 로그인 필요
  - 프로필 로딩 중
  - 프로필 로딩 실패
  - 채팅 중 안내

### 3. 메시지 목록 (DatabaseListView)

Firebase Realtime Database의 메시지를 실시간으로 표시합니다.

#### Firebase 데이터 구조

```typescript
// 메시지 경로
const messagePath = 'chat-messages';

// 메시지 데이터 구조
{
  roomId: string;           // 채팅방 ID
  type: 'message';          // 메시지 타입
  text: string;             // 메시지 내용
  urls: string[];           // 첨부 URL (미래 확장)
  senderUid: string;        // 발신자 UID
  createdAt: number;        // 생성 시간 (timestamp)
  editedAt: number | null;  // 수정 시간
  deletedAt: number | null; // 삭제 시간
  roomOrder: string;        // 정렬 키: `-{roomId}-{timestamp}`
  rootOrder: string;        // 루트 정렬 키
}
```

#### DatabaseListView 설정

```typescript
<DatabaseListView
  path="chat-messages"
  pageSize={25}
  orderBy="roomOrder"
  orderPrefix={roomOrderPrefix}  // `-{roomId}-`
  threshold={280}
  reverse={true}
/>
```

- **pageSize**: 한 번에 로드할 메시지 수 (25개)
- **orderBy**: 정렬 기준 필드 (`roomOrder`)
- **orderPrefix**: 특정 채팅방의 메시지만 필터링 (`-{roomId}-`)
- **threshold**: 스크롤 임계값 (280px)
- **reverse**: 역순 정렬 (최신 메시지가 아래에 표시)

#### 메시지 표시

각 메시지는 다음 정보를 포함합니다:

- **Avatar**: 발신자의 프로필 이미지 (40px)
- **발신자 이름**:
  - 본인: 다국어 메시지 `chatYou`
  - 상대방: displayName 또는 UID
  - 알 수 없음: 다국어 메시지 `chatUnknownUser`
- **시간**: 로케일에 맞춘 날짜/시간 형식
- **메시지 내용**: 텍스트 (줄바꿈 지원)

#### 시간 포맷

```typescript
function formatTimestamp(value?: number | null) {
  if (!value) return '';
  const currentLocale = getLocale();
  const locale = currentLocale === 'ko' ? 'ko-KR'
    : currentLocale === 'ja' ? 'ja-JP'
    : currentLocale === 'zh' ? 'zh-CN'
    : 'en-US';
  return new Date(value).toLocaleString(locale, {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
}
```

### 4. 메시지 입력 및 전송

하단에 메시지 입력창과 전송 버튼을 제공합니다.

#### UI 구성

- **입력창**: 텍스트 입력 필드 (placeholder: 다국어 지원)
- **전송 버튼**:
  - 활성화 조건: 로그인 상태 + 채팅방 준비 + 내용 입력
  - 전송 중일 때 "전송 중..." 표시
  - 비활성화 시 회색 배경

#### 메시지 전송 로직

```typescript
async function handleSendMessage(event: SubmitEvent) {
  event.preventDefault();

  // 유효성 검사
  if (isSending) return;
  if (!composerText.trim()) return;
  if (!authStore.user?.uid) {
    sendError = m.chatSignInToSend();
    return;
  }
  if (!activeRoomId) {
    sendError = m.chatRoomNotReady();
    return;
  }

  isSending = true;
  sendError = null;

  const trimmed = composerText.trim();
  const timestamp = Date.now();

  // 메시지 데이터 구성
  const payload = {
    roomId: activeRoomId,
    type: 'message',
    text: trimmed,
    urls: [],
    senderUid: authStore.user.uid,
    createdAt: timestamp,
    editedAt: null,
    deletedAt: null,
    roomOrder: `-${activeRoomId}-${timestamp}`,
    rootOrder: `-${activeRoomId}-${timestamp}`
  };

  // Firebase에 저장
  const result = await pushData(messagePath, payload);

  if (!result.success) {
    sendError = result.error ?? m.chatSendFailed();
  } else {
    composerText = '';
  }

  isSending = false;
}
```

#### Firebase 저장

- **함수**: `pushData(path, data)`
- **경로**: `/chat-messages/{auto-generated-key}`
- **반환**: `{ success: boolean, error?: string }`

## 다국어 지원

모든 UI 텍스트는 Paraglide를 통해 다국어를 지원합니다.

### 다국어 키 목록

| 키 | 한국어 | 영어 | 일본어 | 중국어 |
|---|---|---|---|---|
| `chatDirectChat` | 1:1 채팅 | Direct Chat | ダイレクトチャット | 私聊 |
| `chatChatRoom` | 채팅방 | Chat Room | チャットルーム | 聊天室 |
| `chatRoom` | 방: | Room: | ルーム: | 房间: |
| `chatOverview` | 채팅 개요 | Chat Overview | チャット概要 | 聊天概览 |
| `chatSignInRequired` | 채팅을 시작하려면 로그인하세요. | Please sign in to start chatting. | チャットを開始するにはログインしてください。 | 请登录以开始聊天。 |
| `chatProvideUid` | 1:1 채팅을 열려면 uid 쿼리 파라미터를 제공하세요. | Provide a uid query parameter to open a direct chat. | ダイレクトチャットを開くにはuidクエリパラメータを指定してください。 | 提供uid查询参数以打开私聊。 |
| `chatLoadingProfile` | 참가자 프로필을 불러오는 중... | Loading the participant profile... | 参加者プロフィールを読み込み中... | 加载参与者资料中... |
| `chatLoadProfileFailed` | 참가자 프로필을 불러오는데 실패했습니다. | Failed to load participant profile. | 参加者プロフィールの読み込みに失敗しました。 | 加载参与者资料失败。 |
| `chatChattingWith` | {name}님과 채팅 중입니다. | You are chatting with {name}. | {name}さんとチャット中です。 | 您正在与{name}聊天。 |
| `chatRoomReady` | 방 ID {roomId}가 준비되었습니다. | Room ID {roomId} is ready. | ルームID {roomId}が準備完了。 | 房间ID {roomId}已准备就绪。 |
| `chatSelectConversation` | 대화를 선택하여 시작하세요. | Select a conversation to begin. | 会話を選択して開始してください。 | 选择一个对话开始。 |
| `chatRoomNotReady` | 채팅방이 준비되지 않았습니다. | Chat room is not ready. | チャットルームが準備されていません。 | 聊天室未准备就绪。 |
| `chatAddUidOrRoomId` | 대화를 열려면 URL에 ?uid=TARGET_UID 또는 ?roomId=ROOM_KEY를 추가하세요. | Add ?uid=TARGET_UID or ?roomId=ROOM_KEY to the URL to open a conversation. | 会話を開くにはURLに?uid=TARGET_UIDまたは?roomId=ROOM_KEYを追加してください。 | 在URL中添加?uid=TARGET_UID或?roomId=ROOM_KEY以打开对话。 |
| `chatLoadingMessages` | 메시지를 불러오는 중... | Loading messages... | メッセージを読み込み中... | 加载消息中... |
| `chatNoMessages` | 아직 메시지가 없습니다. 인사해보세요! | No messages yet. Say hello! | まだメッセージがありません。挨拶してみましょう！ | 还没有消息。打个招呼吧！ |
| `chatLoadMessagesFailed` | 메시지를 불러오는데 실패했습니다. | Failed to load messages. | メッセージの読み込みに失敗しました。 | 加载消息失败。 |
| `chatUnknownError` | 알 수 없는 오류. | Unknown error. | 不明なエラー。 | 未知错误。 |
| `chatLoadingMore` | 더 불러오는 중... | Loading more... | さらに読み込み中... | 加载更多中... |
| `chatUpToDate` | 모든 메시지를 확인했습니다. | You are up to date. | すべて確認済みです。 | 已查看所有消息。 |
| `chatPreparingStream` | 메시지 스트림을 준비하는 중... | Preparing the message stream... | メッセージストリームを準備中... | 准备消息流中... |
| `chatWriteMessage` | 메시지를 입력하세요... | Write a message... | メッセージを入力... | 输入消息... |
| `chatSending` | 전송 중... | Sending... | 送信中... | 发送中... |
| `chatSend` | 전송 | Send | 送信 | 发送 |
| `chatSignInToSend` | 메시지를 보내려면 로그인하세요. | Please sign in to send messages. | メッセージを送信するにはログインしてください。 | 请登录以发送消息。 |
| `chatSendFailed` | 메시지 전송에 실패했습니다. | Failed to send message. | メッセージの送信に失敗しました。 | 发送消息失败。 |
| `chatUnknownUser` | 알 수 없는 사용자 | Unknown user | 不明なユーザー | 未知用户 |
| `chatYou` | 나 | You | あなた | 你 |
| `chatPartner` | 채팅 상대 | Chat Partner | チャットパートナー | 聊天伙伴 |

### 사용 예시

```svelte
<p>{m.chatChattingWith({ name: targetDisplayName })}</p>
<p>{m.chatRoomReady({ roomId: roomIdParam })}</p>
<input placeholder={m.chatWriteMessage()} />
```

## 스타일링

### 반응형 디자인

- **데스크톱**: 최대 너비 960px, 가로 레이아웃
- **모바일** (640px 이하):
  - 헤더: 세로 레이아웃
  - 메시지: 세로 레이아웃
  - 입력창: 전체 너비

### 주요 스타일

- **채팅방 헤더**: 흰색 배경, 그림자, 둥근 모서리
- **메시지 목록**: 스크롤 가능, 최대 높이 60vh
- **내 메시지**: 파란색 배경 (`#eef2ff`)
- **상대방 메시지**: 회색 배경 (`#f9fafb`)
- **입력창**: 둥근 모서리 (999px), 흰색 배경
- **전송 버튼**: 검은색 배경, 흰색 텍스트, 호버 효과

## 보안 및 권한

### 현재 구현

- 로그인한 사용자만 메시지 전송 가능
- 누구나 채팅방 조회 가능 (URL 접근)

### 향후 개선 사항

- Firebase Security Rules를 통한 접근 제어
- 채팅방 멤버십 검증
- 메시지 수정/삭제 권한 제어
- 스팸 방지 및 신고 기능

## 성능 최적화

### 현재 적용

- DatabaseListView를 통한 페이지네이션 (25개씩 로드)
- 역순 정렬로 최신 메시지 우선 표시
- 프로필 캐싱 (userProfileStore)

### 향후 개선 사항

- 메시지 가상 스크롤 (Virtual Scrolling)
- 이미지 레이지 로딩
- 오프라인 지원 (IndexedDB 캐싱)
- 읽음 상태 추적

## 테스트 시나리오

### 1:1 채팅 테스트

1. 로그인하지 않은 상태에서 `/chat/room?uid=test-user` 접근
   - 결과: "채팅을 시작하려면 로그인하세요." 메시지 표시

2. 로그인 후 `/chat/room?uid=test-user` 접근
   - 결과: 채팅방 헤더에 상대방 프로필 표시

3. 메시지 입력 및 전송
   - 결과: 실시간으로 메시지 목록에 추가

4. 다른 브라우저/기기에서 동일한 채팅방 접근
   - 결과: 실시간으로 메시지 동기화

### 그룹 채팅 테스트

1. `/chat/room?roomId=general` 접근
   - 결과: 그룹 채팅방 헤더 표시

2. 여러 사용자가 동일한 roomId로 접근
   - 결과: 모든 사용자의 메시지가 공유됨

### 다국어 테스트

1. 언어를 한국어로 설정
   - 결과: 모든 UI가 한국어로 표시

2. 언어를 영어로 전환
   - 결과: 모든 UI가 영어로 즉시 변경

## 관련 파일

- [src/routes/chat/room/+page.svelte](../src/routes/chat/room/+page.svelte) - 채팅방 페이지
- [src/lib/components/DatabaseListView.svelte](../src/lib/components/DatabaseListView.svelte) - 메시지 목록 컴포넌트
- [src/lib/components/user/avatar.svelte](../src/lib/components/user/avatar.svelte) - 사용자 아바타
- [src/lib/stores/user-profile.svelte](../src/lib/stores/user-profile.svelte) - 사용자 프로필 스토어
- [src/lib/stores/database.svelte](../src/lib/stores/database.svelte) - Firebase 데이터베이스 유틸리티
- [messages/*.json](../messages/) - 다국어 메시지 파일

## 변경 이력

- **2025-11-11**: 초기 사양 작성 및 1:1 채팅 기능 완전 구현
  - URL 파라미터 기반 채팅방 접근 (uid, roomId)
  - Firebase Realtime Database를 통한 실시간 메시지 동기화
  - 사용자 프로필 실시간 구독 및 표시
  - DatabaseListView를 통한 메시지 목록 페이지네이션
  - 메시지 입력 및 전송 기능
  - 4개 언어 완전 지원 (한국어, 영어, 일본어, 중국어)
  - 반응형 디자인 (데스크톱/모바일)
