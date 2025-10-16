# InfiniteScroll 라이브러리 사용 가이드

본 문서에서는 PhilGo 웹 개발에서 사용되는 InfiniteScroll 라이브러리의 사용 방법을 설명한다.

---

## 🚀 초간단 예제 (빠르게 시작하기)

**가장 간단하게 InfiniteScroll을 사용하는 방법입니다. 페이지 하단에 도달하면 자동으로 더 많은 데이터를 로드합니다.**

```php
<?php
// InfiniteScroll 라이브러리를 지연 로딩 (defer)으로 로드
load_deferred_js('infinite-scroll');
?>

<script>
    ready(() => {
        // InfiniteScroll 초기화
        const scrollController = InfiniteScroll.init('body', {
            onScrolledToBottom: () => {
                console.log('하단 도달: 더 많은 데이터 로드');
                // 여기에 데이터 로드 로직 추가
                // 예: loadMorePosts();
            },
            threshold: 10,              // 하단으로부터 10px 이내에서 트리거
            debounceDelay: 100,         // 100ms 디바운스
            initialScrollToBottom: false // 페이지 로드 시 자동 스크롤 안 함
        });
    });
</script>
```

**핵심 포인트**:
- ✅ `load_deferred_js('infinite-scroll')`로 라이브러리 로드
- ✅ `ready()` 함수로 DOM 로드 완료 대기
- ✅ `InfiniteScroll.init('body', {...})`로 body 스크롤 감지
- ✅ `onScrolledToBottom` 콜백에서 데이터 로드 로직 실행

**더 자세한 사용법은 아래 문서를 참고하세요! 👇**

---

## 목차

- [InfiniteScroll 라이브러리 사용 가이드](#infinitescroll-라이브러리-사용-가이드)
  - [🚀 초간단 예제 (빠르게 시작하기)](#-초간단-예제-빠르게-시작하기)
  - [목차](#목차)
  - [개요](#개요)
  - [기본 사용법](#기본-사용법)
    - [1. HTML 요소에 높이 설정 (필수)](#1-html-요소에-높이-설정-필수)
    - [2. InfiniteScroll 초기화](#2-infinitescroll-초기화)
  - [설정 옵션](#설정-옵션)
    - [onScrolledToBottom](#onscrolledtobottom)
    - [onScrolledToTop](#onscrolledtotop)
    - [threshold](#threshold)
    - [debounceDelay](#debouncedelay)
    - [initialScrollToBottom](#initialscrolltobottom)
  - [반환 메서드](#반환-메서드)
    - [scrollToBottom()](#scrolltobottom)
    - [scrollToTop()](#scrolltotop)
    - [scrollTo(position)](#scrolltoposition)
    - [destroy()](#destroy)
  - [사용 예제](#사용-예제)
    - [예제 1: 채팅 메시지 목록 (자동 하단 스크롤)](#예제-1-채팅-메시지-목록-자동-하단-스크롤)
    - [예제 2: 무한 스크롤 게시글 목록](#예제-2-무한-스크롤-게시글-목록)
    - [예제 3: 양방향 무한 스크롤](#예제-3-양방향-무한-스크롤)
    - [예제 4: Vue.js와 통합](#예제-4-vuejs와-통합)
  - [고급 사용법](#고급-사용법)
    - [동적 콘텐츠 추가 후 스크롤](#동적-콘텐츠-추가-후-스크롤)
    - [스크롤 위치 저장 및 복원](#스크롤-위치-저장-및-복원)
  - [주의사항 및 문제 해결](#주의사항-및-문제-해결)
    - [높이 설정이 필수인 이유](#높이-설정이-필수인-이유)
    - [초기 스크롤이 작동하지 않을 때](#초기-스크롤이-작동하지-않을-때)
    - [콜백이 너무 자주 호출될 때](#콜백이-너무-자주-호출될-때)
  - [내부 동작 원리](#내부-동작-원리)
    - [디바운싱 (Debouncing)](#디바운싱-debouncing)
    - [MutationObserver를 통한 초기 스크롤](#mutationobserver를-통한-초기-스크롤)
    - [강제 레이아웃 재계산 (Forced Reflow)](#강제-레이아웃-재계산-forced-reflow)
  - [Best Practices](#best-practices)

---

## 개요

InfiniteScroll 라이브러리는 스크롤 가능한 컨테이너에 무한 스크롤 기능을 제공한다. 주요 기능은 다음과 같다:

- **상단/하단 스크롤 감지**: 사용자가 컨테이너의 상단이나 하단에 도달했을 때 콜백 함수 실행
- **디바운싱**: 스크롤 이벤트 최적화로 성능 향상
- **초기 스크롤 위치 설정**: 페이지 로드 시 자동으로 하단으로 스크롤 (선택적)
- **MutationObserver**: 동적 콘텐츠 추가 감지 및 자동 스크롤

이 라이브러리는 채팅 메시지, 무한 스크롤 게시글 목록, 타임라인 등 다양한 상황에서 사용할 수 있다.

---

## 기본 사용법

### 1. HTML 요소에 높이 설정 (필수)

InfiniteScroll을 사용하려면 **반드시** 대상 요소에 높이를 설정해야 한다. 높이가 없으면 스크롤이 발생하지 않는다.

```html
<!-- ✅ 올바른 예: 높이 설정 -->
<div id="chat-messages" style="height: 500px; overflow-y: auto;">
    <!-- 메시지 목록 -->
</div>

<!-- ❌ 잘못된 예: 높이 미설정 -->
<div id="chat-messages">
    <!-- 스크롤 불가능 -->
</div>
```

### 2. InfiniteScroll 초기화

JavaScript에서 요소를 선택하고 InfiniteScroll을 초기화한다.

```javascript
// 요소 선택
const messagesContainer = document.querySelector('#chat-messages');

// InfiniteScroll 초기화
const scrollController = InfiniteScroll.init(messagesContainer, {
    onScrolledToBottom: () => {
        console.log('하단 도달: 더 많은 데이터 로드');
        loadMoreMessages();
    },
    threshold: 10,
    debounceDelay: 100,
    initialScrollToBottom: true
});
```

---

## 설정 옵션

### onScrolledToBottom

**타입**: `Function`
**기본값**: `() => {}`

사용자가 컨테이너 하단에 도달했을 때 호출되는 콜백 함수다.

```javascript
InfiniteScroll.init(element, {
    onScrolledToBottom: () => {
        console.log('하단 도달!');
        // 추가 데이터 로드 로직
        fetchMoreData();
    }
});
```

**사용 사례**:
- 무한 스크롤 게시글 목록
- 더 많은 채팅 메시지 로드
- 페이지네이션 자동 로드

### onScrolledToTop

**타입**: `Function`
**기본값**: `() => {}`

사용자가 컨테이너 상단에 도달했을 때 호출되는 콜백 함수다.

```javascript
InfiniteScroll.init(element, {
    onScrolledToTop: () => {
        console.log('상단 도달!');
        // 이전 데이터 로드 로직
        loadPreviousMessages();
    }
});
```

**사용 사례**:
- 채팅에서 이전 메시지 로드
- 역순 무한 스크롤
- 상단 새로고침 기능

### threshold

**타입**: `Number`
**기본값**: `10` (픽셀)

하단/상단으로부터 몇 픽셀 이내에서 콜백을 트리거할지 결정한다. 값이 클수록 더 일찍 콜백이 실행된다.

```javascript
InfiniteScroll.init(element, {
    threshold: 50, // 하단/상단으로부터 50px 이내에서 트리거
    onScrolledToBottom: loadMore
});
```

**권장값**:
- **작은 threshold (5-10px)**: 정확한 하단/상단 도달 감지
- **큰 threshold (50-100px)**: 미리 데이터 로드 (사용자 경험 향상)

### debounceDelay

**타입**: `Number`
**기본값**: `100` (밀리초)

스크롤 이벤트 디바운싱 지연 시간이다. 성능 최적화를 위해 스크롤 이벤트가 멈춘 후 지정된 시간이 지나야 콜백이 실행된다.

```javascript
InfiniteScroll.init(element, {
    debounceDelay: 200, // 200ms 대기 후 콜백 실행
    onScrolledToBottom: loadMore
});
```

**권장값**:
- **짧은 delay (50-100ms)**: 빠른 반응 (채팅, 실시간 업데이트)
- **긴 delay (200-300ms)**: 성능 중시 (대용량 데이터)

### initialScrollToBottom

**타입**: `Boolean`
**기본값**: `true`

페이지 로드 시 자동으로 하단으로 스크롤할지 결정한다.

```javascript
// ✅ 채팅: 최신 메시지를 보여주기 위해 자동 하단 스크롤
InfiniteScroll.init(chatContainer, {
    initialScrollToBottom: true
});

// ✅ 게시글 목록: 상단부터 보여주기 위해 자동 스크롤 비활성화
InfiniteScroll.init(postList, {
    initialScrollToBottom: false
});
```

**주의**: 이 옵션은 MutationObserver를 사용하여 콘텐츠가 동적으로 추가될 때도 작동한다.

---

## 반환 메서드

InfiniteScroll.init()은 컨트롤러 객체를 반환한다. 이 객체를 통해 스크롤을 제어하거나 정리 작업을 수행할 수 있다.

### scrollToBottom()

컨테이너를 즉시 하단으로 스크롤한다.

```javascript
const controller = InfiniteScroll.init(element, options);

// 새 메시지 추가 후 하단으로 스크롤
addNewMessage();
controller.scrollToBottom();
```

### scrollToTop()

컨테이너를 즉시 상단으로 스크롤한다.

```javascript
controller.scrollToTop();
```

### scrollTo(position)

컨테이너를 특정 위치(픽셀)로 스크롤한다.

```javascript
// 200px 위치로 스크롤
controller.scrollTo(200);

// 중간 위치로 스크롤
const middlePosition = element.scrollHeight / 2;
controller.scrollTo(middlePosition);
```

### destroy()

이벤트 리스너와 타이머를 정리하고 InfiniteScroll을 제거한다. 메모리 누수 방지를 위해 컴포넌트 제거 시 반드시 호출해야 한다.

```javascript
const controller = InfiniteScroll.init(element, options);

// 페이지 이동 또는 컴포넌트 제거 시
controller.destroy();
```

---

## 사용 예제

### 예제 1: 채팅 메시지 목록 (자동 하단 스크롤)

채팅 애플리케이션에서 새 메시지가 추가되면 자동으로 하단으로 스크롤한다.

```html
<div id="chat-messages" style="height: 400px; overflow-y: auto;">
    <div class="message">안녕하세요</div>
    <div class="message">반갑습니다</div>
    <!-- 더 많은 메시지 -->
</div>
```

```javascript
const chatContainer = document.querySelector('#chat-messages');

const chatScroll = InfiniteScroll.init(chatContainer, {
    // 사용자가 하단까지 스크롤하면 이전 메시지 로드
    onScrolledToBottom: () => {
        console.log('이전 메시지 로드');
        loadOlderMessages();
    },

    // 사용자가 상단까지 스크롤하면 더 오래된 메시지 로드
    onScrolledToTop: () => {
        console.log('더 오래된 메시지 로드');
        loadEvenOlderMessages();
    },

    // 페이지 로드 시 최신 메시지가 보이도록 하단으로 스크롤
    initialScrollToBottom: true,

    // 하단으로부터 20px 이내에서 트리거
    threshold: 20,

    // 빠른 반응을 위해 짧은 debounce
    debounceDelay: 50
});

// 새 메시지 수신 시 하단으로 자동 스크롤
function onNewMessageReceived(message) {
    const messageEl = document.createElement('div');
    messageEl.className = 'message';
    messageEl.textContent = message;
    chatContainer.appendChild(messageEl);

    // 새 메시지 추가 후 하단으로 스크롤
    chatScroll.scrollToBottom();
}
```

### 예제 2: 무한 스크롤 게시글 목록

사용자가 하단에 도달하면 자동으로 다음 페이지의 게시글을 로드한다.

```html
<div id="post-list" style="height: 600px; overflow-y: auto;">
    <div class="post">게시글 1</div>
    <div class="post">게시글 2</div>
    <!-- 더 많은 게시글 -->
</div>
```

```javascript
const postList = document.querySelector('#post-list');
let currentPage = 1;
let isLoading = false;

const postScroll = InfiniteScroll.init(postList, {
    onScrolledToBottom: async () => {
        if (isLoading) return; // 중복 로딩 방지

        isLoading = true;
        console.log(`페이지 ${currentPage + 1} 로드 중...`);

        try {
            const posts = await fetchPosts(currentPage + 1);

            if (posts.length === 0) {
                console.log('더 이상 게시글이 없습니다');
                return;
            }

            // 게시글 추가
            posts.forEach(post => {
                const postEl = document.createElement('div');
                postEl.className = 'post';
                postEl.textContent = post.title;
                postList.appendChild(postEl);
            });

            currentPage++;
        } catch (error) {
            console.error('게시글 로드 실패:', error);
        } finally {
            isLoading = false;
        }
    },

    // 게시글 목록은 상단부터 보여줌
    initialScrollToBottom: false,

    // 미리 로딩하여 사용자 경험 향상
    threshold: 100,

    // 일반적인 debounce
    debounceDelay: 100
});
```

### 예제 3: 양방향 무한 스크롤

상단과 하단 모두에서 데이터를 로드하는 타임라인 스타일 UI다.

```javascript
const timeline = document.querySelector('#timeline');
let newerTimestamp = Date.now();
let olderTimestamp = Date.now() - 86400000; // 1일 전

const timelineScroll = InfiniteScroll.init(timeline, {
    // 하단: 더 오래된 콘텐츠 로드
    onScrolledToBottom: async () => {
        const olderPosts = await fetchPostsBefore(olderTimestamp);
        olderPosts.forEach(post => {
            const postEl = createPostElement(post);
            timeline.appendChild(postEl);
        });
        olderTimestamp = olderPosts[olderPosts.length - 1].timestamp;
    },

    // 상단: 더 최신 콘텐츠 로드
    onScrolledToTop: async () => {
        const newerPosts = await fetchPostsAfter(newerTimestamp);
        newerPosts.reverse().forEach(post => {
            const postEl = createPostElement(post);
            timeline.insertBefore(postEl, timeline.firstChild);
        });
        newerTimestamp = newerPosts[0].timestamp;
    },

    initialScrollToBottom: false,
    threshold: 50
});
```

### 예제 4: Vue.js와 통합

Vue.js 3 Composition API에서 InfiniteScroll을 사용한다.

```html
<div id="chat-app">
    <div ref="messageContainer" style="height: 400px; overflow-y: auto;">
        <div v-for="message in messages" :key="message.id" class="message">
            {{ message.text }}
        </div>
    </div>
</div>
```

```javascript
Vue.createApp({
    setup() {
        const messages = Vue.ref([]);
        const scrollController = Vue.ref(null);
        const messageContainer = Vue.ref(null);

        Vue.onMounted(() => {
            // Vue 컴포넌트 마운트 후 InfiniteScroll 설정
            Vue.nextTick(() => {
                scrollController.value = InfiniteScroll.init(messageContainer.value, {
                    onScrolledToBottom: () => {
                        loadMoreMessages();
                    },
                    initialScrollToBottom: true,
                    threshold: 20
                });
            });

            loadInitialMessages();
        });

        Vue.onUnmounted(() => {
            // 컴포넌트 언마운트 시 정리
            if (scrollController.value) {
                scrollController.value.destroy();
            }
        });

        async function loadInitialMessages() {
            messages.value = await fetchMessages();
        }

        async function loadMoreMessages() {
            const olderMessages = await fetchOlderMessages(messages.value[0].id);
            messages.value = [...olderMessages, ...messages.value];
        }

        function addNewMessage(message) {
            messages.value.push(message);

            // Vue가 DOM을 업데이트한 후 스크롤
            Vue.nextTick(() => {
                scrollController.value.scrollToBottom();
            });
        }

        return {
            messages,
            messageContainer,
            addNewMessage
        };
    }
}).mount('#chat-app');
```

---

## 고급 사용법

### 동적 콘텐츠 추가 후 스크롤

AJAX나 WebSocket으로 콘텐츠를 동적으로 추가한 후 스크롤 위치를 조정한다.

```javascript
const controller = InfiniteScroll.init(container, options);

// AJAX로 데이터 추가
async function addDynamicContent() {
    const newItems = await fetchNewItems();

    newItems.forEach(item => {
        const el = document.createElement('div');
        el.textContent = item.content;
        container.appendChild(el);
    });

    // DOM 업데이트 후 하단으로 스크롤
    setTimeout(() => {
        controller.scrollToBottom();
    }, 0);
}
```

### 스크롤 위치 저장 및 복원

페이지 새로고침 후에도 사용자의 스크롤 위치를 유지한다.

```javascript
const container = document.querySelector('#content');

// 스크롤 위치 저장
container.addEventListener('scroll', () => {
    sessionStorage.setItem('scrollPosition', container.scrollTop);
});

// 페이지 로드 시 스크롤 위치 복원
const controller = InfiniteScroll.init(container, {
    initialScrollToBottom: false, // 자동 스크롤 비활성화
    onScrolledToBottom: loadMore
});

const savedPosition = sessionStorage.getItem('scrollPosition');
if (savedPosition) {
    controller.scrollTo(parseInt(savedPosition));
}
```

---

## 주의사항 및 문제 해결

### 높이 설정이 필수인 이유

InfiniteScroll은 `scrollTop`, `scrollHeight`, `clientHeight` 속성을 사용하여 스크롤 위치를 감지한다. 높이가 설정되지 않으면 이러한 속성이 올바르게 작동하지 않는다.

```css
/* ✅ 올바른 예 */
#container {
    height: 500px;
    overflow-y: auto;
}

/* ✅ 동적 높이도 가능 */
#container {
    max-height: 80vh;
    overflow-y: auto;
}

/* ❌ 잘못된 예: 높이 없음 */
#container {
    overflow-y: auto; /* 높이가 없으면 스크롤 불가 */
}
```

### 초기 스크롤이 작동하지 않을 때

`initialScrollToBottom: true`를 설정했는데도 하단으로 스크롤되지 않는다면:

1. **콘텐츠 로딩 지연**: MutationObserver가 콘텐츠 추가를 감지할 때까지 최대 5초를 기다린다. 콘텐츠가 5초 후에 추가되면 수동으로 스크롤해야 한다.

```javascript
const controller = InfiniteScroll.init(element, {
    initialScrollToBottom: true
});

// 늦게 로드되는 콘텐츠가 있다면 수동 스크롤
setTimeout(() => {
    controller.scrollToBottom();
}, 6000);
```

2. **CSS 높이 문제**: 요소의 높이가 0이거나 콘텐츠보다 크면 스크롤이 발생하지 않는다.

```javascript
// 디버깅: 스크롤 가능 여부 확인
console.log('scrollHeight:', element.scrollHeight);
console.log('clientHeight:', element.clientHeight);
console.log('Can scroll?', element.scrollHeight > element.clientHeight);
```

### 콜백이 너무 자주 호출될 때

`debounceDelay`를 늘려서 콜백 호출 빈도를 줄인다.

```javascript
InfiniteScroll.init(element, {
    debounceDelay: 300, // 300ms로 증가
    onScrolledToBottom: loadMore
});
```

또는 콜백 내부에서 중복 실행을 방지한다.

```javascript
let isLoading = false;

InfiniteScroll.init(element, {
    onScrolledToBottom: async () => {
        if (isLoading) return; // 이미 로딩 중이면 무시

        isLoading = true;
        await loadMore();
        isLoading = false;
    }
});
```

---

## 내부 동작 원리

### 디바운싱 (Debouncing)

스크롤 이벤트는 매우 빈번하게 발생한다. InfiniteScroll은 디바운싱을 사용하여 성능을 최적화한다.

```javascript
let scrollTimer = null;

element.addEventListener('scroll', () => {
    if (scrollTimer) clearTimeout(scrollTimer);

    scrollTimer = setTimeout(() => {
        // 스크롤이 멈춘 후 실행
        checkScrollPosition();
    }, debounceDelay);
});
```

### MutationObserver를 통한 초기 스크롤

`initialScrollToBottom: true`일 때, MutationObserver를 사용하여 DOM에 콘텐츠가 추가될 때마다 자동으로 하단으로 스크롤한다.

```javascript
const observer = new MutationObserver((mutations) => {
    const hasNewContent = mutations.some(mutation =>
        mutation.addedNodes.length > 0
    );

    if (hasNewContent) {
        requestAnimationFrame(() => {
            forceScrollToBottom();
        });
    }
});

observer.observe(element, {
    childList: true,
    subtree: true,
    characterData: true
});
```

이 방식은 AJAX, Alpine.js, React 등 어떤 방식으로 콘텐츠를 추가하더라도 작동한다.

### 강제 레이아웃 재계산 (Forced Reflow)

브라우저가 레이아웃을 즉시 업데이트하도록 강제한다.

```javascript
void element.offsetHeight; // 레이아웃 재계산 강제
element.scrollTop = maxScroll;
```

이는 콘텐츠가 추가된 직후 스크롤할 때 `scrollHeight`가 정확한 값을 갖도록 보장한다.

---

## Best Practices

1. **높이 설정 필수**: 모든 스크롤 컨테이너에 명시적인 높이를 설정한다.

   **스크롤 영역을 최대한 크게 만들기 위한 권장 방법**:

   스크롤 영역을 화면 전체 높이에서 헤더와 푸터를 제외한 만큼 크게 만들려면, `calc(100vh - [헤더+푸터 높이])`를 사용하는 것이 가장 좋은 방법입니다.

   ```css
   /* 예제: 채팅방 스크롤 영역 */
   .chat-room-page-wrapper {
       height: calc(100vh - 340px); /* 100vh에서 헤더(60px) + 푸터(280px) 제외 */
       overflow-y: auto;
   }
   ```

   **더 나은 방법**: 푸터를 제거하고 스크롤 영역을 최대한 크게 설정하는 것이 사용자 경험 측면에서 더 좋습니다. 이렇게 하면 스크롤 가능한 콘텐츠 영역이 최대화되어 더 많은 내용을 한 번에 볼 수 있습니다.

   ```css
   /* 권장: 푸터 없이 최대 높이 사용 */
   .chat-room-page-wrapper {
       height: calc(100vh - 60px); /* 헤더만 제외 */
       overflow-y: auto;
   }
   ```

   **핵심 요점**:
   - 스크롤 영역을 가능한 한 크게 만들면 사용자가 더 많은 콘텐츠를 볼 수 있습니다
   - `100vh` 기반 계산을 사용하여 반응형으로 전체 화면 높이를 활용합니다
   - 불필요한 푸터를 제거하여 스크롤 가능 영역을 최대화하는 것이 최선입니다

2. **메모리 누수 방지**: 컴포넌트 제거 시 `destroy()` 호출을 잊지 않는다.

```javascript
// Alpine.js 예제
destroy() {
    if (this.scrollController) {
        this.scrollController.destroy();
    }
}
```

3. **중복 로딩 방지**: 콜백 내부에서 로딩 상태를 관리한다.

```javascript
let isLoading = false;

onScrolledToBottom: async () => {
    if (isLoading) return;
    isLoading = true;
    try {
        await loadMore();
    } finally {
        isLoading = false;
    }
}
```

4. **적절한 threshold 설정**: 사용자 경험을 고려하여 threshold를 조정한다.
   - 채팅: 작은 threshold (5-10px)
   - 게시글 목록: 큰 threshold (50-100px)

5. **디바운싱 최적화**: 콘텐츠 타입에 따라 debounceDelay를 조정한다.
   - 실시간 채팅: 짧은 delay (50ms)
   - 대용량 데이터: 긴 delay (200ms)

6. **에러 처리**: AJAX 요청 실패 시 적절한 에러 처리를 구현한다.

```javascript
onScrolledToBottom: async () => {
    try {
        await loadMore();
    } catch (error) {
        console.error('데이터 로드 실패:', error);
        showToast('데이터를 불러올 수 없습니다.');
    }
}
```

7. **성능 모니터링**: 대용량 데이터를 다룰 때는 성능을 주시한다.

```javascript
onScrolledToBottom: async () => {
    const startTime = performance.now();
    await loadMore();
    const endTime = performance.now();
    console.log(`로딩 시간: ${endTime - startTime}ms`);
}
```

---

## 관련 문서

- [PhilGo JavaScript 개발 문서](./javascript.md) - 전체 JavaScript 관련 문서
- [PhilGo 디자인 문서](./design.md) - UI/UX 디자인 가이드라인
- [PhilGo Alpine.js 가이드](./javascript.md#alpinejs) - Alpine.js 사용 방법

---

**문서 버전**: 1.0
**최종 업데이트**: 2025-10-01
**라이브러리 파일**: `/js/infinite-scroll.js`
