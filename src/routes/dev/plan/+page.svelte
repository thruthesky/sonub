<script lang="ts">
	/**
	 * 개발 계획 페이지
	 *
	 * Sonub 프로젝트의 향후 개발 기능 목록 및 로드맵을 관리합니다.
	 */

	import * as Card from '$lib/components/ui/card/index.js';
</script>

<svelte:head>
	<title>개발 계획 - Sonub</title>
</svelte:head>

<div class="mx-auto max-w-7xl space-y-8">
	<!-- 페이지 헤더 -->
	<div class="page-header">
		<h1 class="page-title">개발 계획 (Development Plan)</h1>
		<p class="page-description">
			Sonub 프로젝트의 향후 개발 기능 목록과 로드맵입니다. 체크 표시(✅)는 완료된 기능을 나타냅니다.
		</p>
	</div>

	<!-- 개발 기능 카드 목록 -->
	<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
		<!-- 1. 그룹 채팅 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">1</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">그룹 채팅 기능</Card.Title>
						<span class="todo-badge todo-badge--done">✅ 완료</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<div class="space-y-4">
					<div>
						<h4 class="todo-subtitle">1-1. 오픈 채팅 기능</h4>
						<ul class="todo-list">
							<li>공개 채팅방 생성 (누구나 입장 가능)</li>
							<li>오픈 채팅 목록 페이지 (<code>/chat/open</code>)</li>
							<li>채팅방 이름, 설명, 태그로 검색 가능</li>
							<li><code>/open-chats/{'{roomId}'}</code></li>
							<li>최대 참여 인원 설정 옵션</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">1-2. 일반 그룹 채팅</h4>
						<ul class="todo-list">
							<li>초대 기반 비공개 그룹 채팅</li>
							<li>오픈 채팅 메뉴에 표시되지 않음</li>
							<li>검색 불가능 (초대 링크 또는 직접 초대만 가능)</li>
							<li><code>/group-chats/{'{roomId}'}</code></li>
							<li>멤버 관리: 초대, 추방, 나가기</li>
						</ul>
					</div>
				</div>
			</Card.Content>
		</Card.Root>

		<!-- 2. 메시지 뱃지 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">2</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">새로운 메시지 뱃지 증가 및 방 입장시 초기화</Card.Title>
						<span class="todo-badge todo-badge--done">✅ 완료</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>채팅방 목록에서 읽지 않은 메시지 개수 뱃지 표시</li>
					<li>채팅방 입장 시 자동으로 뱃지 카운트 초기화</li>
					<li><code>/chat-joins/{'{uid}'}/{'{roomId}'}/newMessageCount</code> 필드 활용</li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 3. 즐겨찾기 기능 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">3</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">즐겨찾기 기능 (폴더관리)</Card.Title>
						<span class="todo-badge todo-badge--done">✅ 완료</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>채팅방을 즐겨찾기에 추가/제거</li>
					<li>즐겨찾기 폴더 생성 및 관리 (예: 업무, 친구, 가족 등)</li>
					<li>즐겨찾기된 채팅방을 폴더별로 그룹화하여 표시</li>
					<li><code>/user-favorites/{'{uid}'}/{'{folderId}'}/{'{roomId}'}</code></li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 4. 채팅 핀 기능 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">4</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">채팅 핀(Chat Pin) 기능</Card.Title>
						<span class="todo-badge todo-badge--done">✅ 완료</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>중요한 채팅방을 상단에 고정</li>
					<li>핀 설정/해제 토글 버튼</li>
					<li>핀된 채팅방은 항상 목록 최상단에 표시</li>
					<li><code>/chat-joins/{'{uid}'}/{'{roomId}'}/pinned: boolean</code></li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 5. FCM 클라이언트 설정 + 채팅 메시지 알림 전송 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">5</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">FCM 클라이언트 설정 + 채팅 메시지 알림 전송</Card.Title>
						<span class="todo-badge todo-badge--done">✅ 완료</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>Firebase Cloud Messaging (FCM) Permission 요청</li>
					<li>FCM 토큰 생성 및 저장</li>
					<li><code>/fcm-tokens/{'{tokenId}'}: {'{uid, createdAt}'}</code></li>
					<li>토큰 갱신 처리 및 만료된 토큰 정리</li>
					<li>Cloud Functions에서 새 메시지 감지 시 FCM 전송</li>
					<li>배치 전송 구현 (255개씩 분할 전송)</li>
					<li>알림 페이로드: 발신자 이름, 메시지 내용 미리보기, 채팅방 ID</li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 6. 알림 구독 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">6</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">알림 구독</Card.Title>
						<span class="todo-badge todo-badge--done">✅ 완료</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>채팅방 별 알림을 구독/해제 기능 추가</li>
					<li>
						<strong>그룹 채팅:</strong>
						<code>/chat-rooms/{'{roomId}'}/members/{'{uid}'}: boolean</code> - true: 알림 받기,
						false: 알림 안받기
					</li>
					<li>
						<strong>1:1 채팅:</strong>
						<code>/chat-joins/{'{uid}'}/{'{roomId}'}/fcm-subscription</code> - false: 알림 안받기,
						필드 없음: 알림 받기
					</li>
					<li>채팅방 상단 헤더에 알림 구독 버튼 추가 (핀 버튼과 드롭다운 메뉴 사이)</li>
					<li>실시간 구독 상태 모니터링 ($effect + onValue)</li>
					<li>구독 중: 진한 벨 아이콘, 구독 해제: 연한 벨 아이콘 + 슬래시</li>
					<li>Cloud Functions에서 구독 상태 확인 후 FCM 전송</li>
					<li>구독 해제된 사용자는 FCM 전송 대상에서 자동 제외</li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 7. 채팅 초대 수락/거절 흐름 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">7</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">채팅 초대 수락/거절 흐름</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>초대 수신 시 <code>/chat-invites/{'{uid}'}/{'{roomId}'}</code> 에 초대 정보를 저장하고 채팅 목록 상단에 노출</li>
					<li>사용자에게 입장 여부를 먼저 묻는 확인 UI 제공 (입장/거절)</li>
					<li>거절 시 해당 초대 노드를 삭제하고 목록에서 제거</li>
					<li>입장 시 초대 노드를 삭제 후 채팅방 입장 처리 및 <code>/chat-joins</code> 동기화</li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 9. 채팅 첨부파일 업로드 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">9</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">채팅 메시지 첨부파일 업로드</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>이미지, 동영상, PDF 등 일반 파일을 채팅 입력창에서 업로드할 수 있도록 UI 제공</li>
					<li>
						Firebase Storage에 <code>/chat-attachments/{'{roomId}'}/{'{messageId}'}/{'{fileName}'}</code> 구조로 저장
					</li>
					<li>
						첨부 상태에서 전송 시 <code>/chat-messages/{'{messageId}'}</code>에 <code>attachments[]</code> 메타데이터 기록
					</li>
					<li>다운로드/미리보기 권한은 해당 채팅방 멤버만 허용하도록 Security Rules 업데이트</li>
					<li>모바일/데스크톱에서 Drag&Drop, 파일 탐색기 선택 모두 지원</li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 10. 비밀번호 기능 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">10</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">그룹 채팅 비밀번호 기능</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>오픈 채팅에 비밀번호 설정 옵션</li>
					<li>입장 시 비밀번호 입력 모달</li>
					<li><code>/open-chats/{'{roomId}'}/password</code> (해시 저장)</li>
					<li>Cloud Functions에서 비밀번호 검증 수행</li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 11. Post 타입 메시지 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">11</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">채팅 메시지 "Post" 타입 선택</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<div class="space-y-4">
					<div>
						<h4 class="todo-subtitle">11-1. 카테고리 선택</h4>
						<ul class="todo-list">
							<li>메시지 입력창 옆 드롭다운에서 타입 선택: "message" 또는 "post"</li>
							<li>"post" 선택 시 카테고리 선택 UI 표시</li>
							<li>카테고리 목록: 공지사항, 자유게시판, Q&A, 갤러리 등</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">11-2. 제목, 내용, 사진 업로드</h4>
						<ul class="todo-list">
							<li>제목 입력 필드 추가 (필수)</li>
							<li>내용 입력 (rich text editor 또는 마크다운)</li>
							<li>다중 이미지 업로드 지원</li>
							<li>Firebase Storage에 이미지 저장</li>
							<li><code>/chat-messages/{'{messageId}'}</code> (type: "post")</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">11-3. 카테고리별 게시판 메뉴</h4>
						<ul class="todo-list">
							<li>홈페이지 메뉴에 카테고리별 페이지 추가</li>
							<li>DatabaseListView 사용하여 실시간 목록 표시</li>
							<li>orderBy: "createdAt", orderPrefix로 카테고리 필터링</li>
							<li>게시글 상세 페이지: <code>/board/{'{category}'}/{'{postId}'}</code></li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">11-4. 댓글 기능 (게시판처럼 보이게)</h4>
						<ul class="todo-list">
							<li>게시글 상세 페이지에서 댓글 목록 표시</li>
							<li>댓글 작성, 수정, 삭제 기능</li>
							<li><code>/chat-messages/{'{messageId}'}</code> (parentId: postId)</li>
							<li>대댓글 지원 (최대 1단계)</li>
							<li>DatabaseListView 사용하여 실시간 댓글 동기화</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">11-5. 게시판 통계 (post/comment/like)</h4>
						<ul class="todo-list">
							<li>카테고리 및 전체 게시판에 대해 <code>postCount</code>, <code>commentCount</code>, <code>likeCount</code> 통계를 표시</li>
							<li>Cloud Functions가 게시글/댓글/좋아요 이벤트 발생 시 대응 카운터를 업데이트 (<code>/stats/board</code> 트리)</li>
							<li>게시판 목록/상세 헤더에서 최신 통계를 실시간으로 구독하여 UI에 노출</li>
							<li>대시보드 카드 형태로 각 카테고리의 활동량을 비교할 수 있도록 시각화</li>
						</ul>
					</div>
				</div>
			</Card.Content>
		</Card.Root>

		<!-- 12. 게시판 글이 채팅방 메시지 목록에 표시 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">12</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">게시판 글이 채팅방 메시지 목록에 표시</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<ul class="todo-list">
					<li>게시판에서 글을 작성하면 해당 채팅방의 메시지 목록에 자동으로 표시</li>
					<li>type: "post"인 메시지를 채팅방 메시지 목록에 통합하여 표시</li>
					<li>
						<strong>양방향 동기화:</strong> 게시판에서 글을 작성하면 채팅 메시지로 추가되고, 반대로
						채팅 메시지에서 글/post 형태로 메시지를 작성하면 게시판에 표시
					</li>
					<li>채팅방 화면에서 일반 메시지와 게시글을 구분하여 렌더링</li>
					<li>게시글은 제목, 카테고리, 미리보기 형태로 표시</li>
					<li>클릭 시 게시글 상세 페이지로 이동</li>
					<li>
						<strong>멤버 정보 표시:</strong> 게시판에 총 몇 명의 멤버가 있는지 표시하고, 채팅방
						정보를 표시
					</li>
				</ul>
			</Card.Content>
		</Card.Root>

		<!-- 13. 남/여 찾기 기능 (콕 포인트 시스템) -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">13</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">남/여 찾기 기능 (콕 포인트 시스템)</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<div class="space-y-4">
					<div>
						<h4 class="todo-subtitle">13-1. 콕(Coke) 가상 포인트 시스템</h4>
						<ul class="todo-list">
							<li>콕은 채팅에 사용하는 가상 포인트 (연료/코크스 의미)</li>
							<li>1콕 = 1원</li>
							<li><code>/user-points/{'{uid}'}/coke</code>에 보유 콕 수 저장</li>
							<li>콕 구매 히스토리 기록: <code>/coke-purchases/{'{uid}'}/{'{purchaseId}'}</code></li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">13-2. 무료 채팅 한도</h4>
						<ul class="todo-list">
							<li>모르는 사람과 총 50명까지 무료 채팅 가능</li>
							<li>총 채팅 메시지 200번까지 무료 (모르는 사람 대상)</li>
							<li><code>/user-chat-limits/{'{uid}'}/freePeopleCount</code>: 무료로 채팅한 사람 수</li>
							<li><code>/user-chat-limits/{'{uid}'}/freeMessageCount</code>: 무료 메시지 전송 횟수</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">13-3. 콕 소비 규칙</h4>
						<ul class="todo-list">
							<li>모르는 사람에게 처음 채팅 시작: 30콕 소모</li>
							<li>20단어 이하 메시지: 10콕 소모</li>
							<li>20단어 초과 메시지: 20콕 소모</li>
							<li>친구 또는 이미 채팅 중인 사람: 무료</li>
							<li>Cloud Functions에서 메시지 전송 시 콕 차감 처리</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">13-4. 남/여 찾기 UI</h4>
						<ul class="todo-list">
							<li>성별 필터링 옵션 (남성/여성/전체)</li>
							<li>랜덤 매칭 기능</li>
							<li>사용자 프로필 미리보기 (프로필 사진, 닉네임, 간단한 소개)</li>
							<li>채팅 시작 전 콕 소모량 안내</li>
							<li><code>/users/{'{uid}'}/gender</code> 기반 검색</li>
						</ul>
					</div>
				</div>
			</Card.Content>
		</Card.Root>

		<!-- 14. 토스/페이팔 결제 기능 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">14</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">토스 페이먼츠 & 페이팔 결제 기능</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<div class="space-y-4">
					<div>
						<h4 class="todo-subtitle">14-1. 토스 페이먼츠 연동 (국내 결제)</h4>
						<ul class="todo-list">
							<li>토스 페이먼츠 SDK 클라이언트 통합</li>
							<li>결제 수단: 카드, 계좌이체, 간편결제 등</li>
							<li>Cloud Functions에서 결제 승인 처리</li>
							<li>결제 성공 시 콕 자동 충전</li>
							<li><code>/payments/{'{paymentId}'}</code>에 결제 기록 저장</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">14-2. 페이팔 연동 (해외 결제)</h4>
						<ul class="todo-list">
							<li>PayPal JavaScript SDK 통합</li>
							<li>전세계 사용자를 위한 다국가 결제 지원</li>
							<li>Cloud Functions에서 IPN(Instant Payment Notification) 처리</li>
							<li>환율 적용 및 콕 충전</li>
							<li>결제 실패/환불 처리 로직</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">14-3. 광고 구좌 등록 및 광고 등록</h4>
						<ul class="todo-list">
							<li>
								<strong>광고 구좌 등록:</strong> 관리자가 광고를 게시할 수 있는 광고 구좌(슬롯)를
								미리 등록
							</li>
							<li>광고 구좌 위치: 홈페이지 상단, 사이드바, 게시판 목록 사이 등</li>
							<li>
								데이터 구조: <code>/ad-slots/{'{slotId}'}</code> (위치, 크기, 가격 정보 포함)
							</li>
							<li>
								<strong>광고 등록:</strong> 사용자가 광고 구좌를 선택하여 광고를 등록
							</li>
							<li>광고 등록 시 결제가 완료되면 즉시 광고가 시작</li>
							<li>
								<strong>구독 방식:</strong> 1개월 단위로 자동 구독 (매월 자동 결제)
							</li>
							<li>구독 해지 시 다음 결제일부터 광고 중단</li>
							<li>
								데이터 구조: <code>/advertisements/{'{adId}'}</code> (광고 내용, 이미지, 링크,
								구좌 ID, 구독 정보)
							</li>
							<li>광고 클릭 통계 및 노출 수 추적</li>
							<li>Cloud Functions에서 구독 갱신 및 결제 실패 처리</li>
						</ul>
					</div>
				</div>
			</Card.Content>
		</Card.Root>

		<!-- 15. 다음 버전 기능 -->
		<Card.Root class="todo-card">
			<Card.Header>
				<div class="flex items-start gap-3">
					<span class="todo-number">15</span>
					<div class="flex flex-col gap-1">
						<Card.Title class="text-xl">다음 버전 기능</Card.Title>
						<span class="todo-badge todo-badge--pending">📋 예정</span>
					</div>
				</div>
			</Card.Header>
			<Card.Content>
				<div class="space-y-4">
					<div>
						<h4 class="todo-subtitle">15-1. 서브 채팅방 (Sub Chat Room)</h4>
						<ul class="todo-list">
							<li>
								<strong>용도:</strong> 내가 운영 중인 여러 개의 채팅방을 하나로 묶어서 하나의
								채팅방처럼 메시지를 모두 보고자 할 때 사용
							</li>
							<li><strong>특징:</strong> 부모 채팅방은 읽기 전용 (read-only)</li>
							<li>부모 채팅방에서 모든 서브 채팅방의 메시지를 통합하여 표시</li>
							<li>
								<strong>커뮤니케이션 제약:</strong> 서브 채팅방에서 생성한 메시지는 해당 서브
								채팅방의 멤버만 읽을 수 있음
							</li>
							<li>
								부모 채팅방에서 답변이나 관련 대화를 해도 서브 채팅방에서는 보이지 않는 구조적
								제약 존재
							</li>
							<li>
								<strong>해결 방안:</strong> 채팅은 반드시 서브 채팅방에 입장해서만 가능하도록
								구현
							</li>
							<li>
								데이터 구조: <code>/chat-rooms/{'{parentRoomId}'}/subRooms/{'{subRoomId}'}</code>
							</li>
							<li>부모 채팅방 UI에서 서브 채팅방 목록 및 빠른 전환 기능 제공</li>
						</ul>
					</div>
					<div>
						<h4 class="todo-subtitle">15-2. 관리자 페이지 (Dashboard) 및 관리자 기능</h4>
						<ul class="todo-list">
							<li>관리자 권한 시스템 구현 (<code>/users/{'{uid}'}/isAdmin: boolean</code>)</li>
							<li>
								<strong>대시보드 기능:</strong>
								<ul class="todo-list ml-4">
									<li>전체 사용자 통계 (가입자 수, 활성 사용자, 신규 가입자)</li>
									<li>채팅방 통계 (전체 채팅방 수, 활성 채팅방, 메시지 수)</li>
									<li>결제 통계 (총 매출, 결제 건수, 평균 결제 금액)</li>
									<li>콕 포인트 통계 (총 콕 발행량, 사용량, 잔여량)</li>
								</ul>
							</li>
							<li>
								<strong>사용자 관리:</strong>
								<ul class="todo-list ml-4">
									<li>사용자 목록 조회 및 검색</li>
									<li>사용자 상세 정보 확인</li>
									<li>사용자 계정 활성화/비활성화</li>
									<li>사용자 권한 수정 (일반 사용자 ↔ 관리자)</li>
								</ul>
							</li>
							<li>
								<strong>채팅방 관리:</strong>
								<ul class="todo-list ml-4">
									<li>모든 채팅방 목록 조회</li>
									<li>부적절한 채팅방 삭제</li>
									<li>채팅방 메시지 모니터링</li>
									<li>신고된 채팅방/메시지 처리</li>
								</ul>
							</li>
							<li>
								<strong>결제 관리:</strong>
								<ul class="todo-list ml-4">
									<li>결제 내역 조회 및 검색</li>
									<li>환불 처리</li>
									<li>결제 오류 로그 확인</li>
								</ul>
							</li>
							<li>
								<strong>시스템 로그:</strong>
								<ul class="todo-list ml-4">
									<li>Cloud Functions 실행 로그</li>
									<li>FCM 전송 로그</li>
									<li>에러 로그 및 모니터링</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</Card.Content>
		</Card.Root>
	</div>
</div>

<style>
	@import 'tailwindcss' reference;

	/* 페이지 헤더 */
	.page-header {
		@apply space-y-3 border-b border-gray-200 pb-6;
	}

	.page-title {
		@apply text-3xl font-bold text-gray-900 md:text-4xl;
	}

	.page-description {
		@apply text-base text-gray-600 md:text-lg;
	}

	/* TODO 카드 스타일 */
	:global(.todo-card) {
		@apply transition-shadow duration-200 hover:shadow-lg;
	}

	.todo-number {
		@apply flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white;
	}

	.todo-list {
		@apply space-y-2 text-sm text-gray-700;
	}

	.todo-list li {
		@apply relative pl-5;
	}

	.todo-list li::before {
		content: '•';
		@apply absolute left-0 top-0 font-bold text-indigo-600;
	}

	.todo-list code {
		@apply rounded bg-gray-100 px-1.5 py-0.5 text-xs font-mono text-indigo-600;
	}

	.todo-subtitle {
		@apply mb-2 font-semibold text-gray-900;
	}

	.todo-badge {
		@apply inline-flex w-fit items-center gap-1 rounded-full px-3 py-0.5 text-xs font-semibold uppercase tracking-wide;
	}

	.todo-badge--done {
		@apply bg-green-100 text-green-700;
	}

	.todo-badge--progress {
		@apply bg-blue-100 text-blue-700;
	}

	.todo-badge--pending {
		@apply bg-gray-100 text-gray-700;
	}
</style>
