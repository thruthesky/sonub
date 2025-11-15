<script lang="ts">
	/**
	 * 채팅방 페이지 (Firestore)
	 *
	 * GET 파라미터로 전달된 uid 값이 있으면 1:1 채팅방으로 동작합니다.
	 * 채팅 상대의 프로필을 실시간으로 구독하고 메시지 목록 및 입력창을 제공합니다.
	 * - 메시지 경로: chats/{roomId}/messages (Firestore subcollection)
	 */

	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import FirestoreListView from '$lib/components/FirestoreListView.svelte';
	import Avatar from '$lib/components/user/avatar.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { userProfileFirestoreStore } from '$lib/stores/user-profile-firestore.svelte';
	import { addDocument } from '$lib/stores/firestore.svelte';
	import { m } from '$lib/paraglide/messages';
	import {
		buildSingleRoomId,
		enterSingleChatRoom,
		joinChatRoom,
		leaveChatRoom,
		togglePinChatRoom,
		inviteUserToChatRoom
	} from '$lib/functions/chat.functions';
	import { formatChatMessageDate } from '$lib/functions/date.functions';
	import {
		uploadChatFile,
		deleteChatFile,
		formatFileSize,
		getFilenameFromUrl,
		isImageUrl,
		isVideoUrl,
		getFileExtension,
		getExtensionFromFilename
	} from '$lib/functions/storage.functions';
	import type { FileUploadStatus } from '$lib/types/chat.types';
	import { tick, onDestroy } from 'svelte';
	import { db } from '$lib/firebase';
	import {
		doc,
		updateDoc,
		onSnapshot,
		setDoc,
		deleteDoc,
		getDoc,
		deleteField,
		Timestamp
	} from 'firebase/firestore';
	import * as DropdownMenu from '$lib/components/ui/dropdown-menu';
	import { Button } from '$lib/components/ui/button';
	import ChatFavoritesDialog from '$lib/components/chat/ChatFavoritesDialog.svelte';
	import UserSearchDialog from '$lib/components/user/UserSearchDialog.svelte';
	import RoomPasswordSetting from '$lib/components/chat/room-password-setting.svelte';
	import RoomPasswordPrompt from '$lib/components/chat/room-password-prompt.svelte';
	import MessageEditModal from '$lib/components/chat/MessageEditModal.svelte';
	import {
		Dialog,
		DialogContent,
		DialogHeader,
		DialogTitle
	} from '$lib/components/ui/dialog';

	// GET 파라미터 추출
	const uidParam = $derived.by(() => $page.url.searchParams.get('uid') ?? '');
	const roomIdParam = $derived.by(() => $page.url.searchParams.get('roomId') ?? '');

	// 1:1 채팅 여부
	const isSingleChat = $derived.by(() => Boolean(uidParam));

	const activeRoomId = $derived.by(() => {
		if (roomIdParam) return roomIdParam;
		if (isSingleChat && authStore.user?.uid && uidParam) {
			return buildSingleRoomId(authStore.user.uid, uidParam);
		}
		return '';
	});

	// FirestoreListView 설정 (계층 구조)
	const messagePath = $derived.by(() => (activeRoomId ? `chats/${activeRoomId}/messages` : ''));
	const messageOrderField = 'createdAt';
	const canRenderMessages = $derived.by(() => Boolean(messagePath));

	// 채팅 상대 프로필 구독 (Firestore)
	$effect(() => {
		if (uidParam) {
			userProfileFirestoreStore.ensureSubscribed(uidParam);
		}
	});

	/**
	 * 채팅방 입장 처리
	 *
	 * 1:1 채팅: 즉시 입장 (비밀번호 없음)
	 * 그룹/오픈 채팅:
	 * - 비밀번호 필요 여부 확인 (roomPasswordEnabled && !isRoomMember && !isRoomOwner)
	 * - 비밀번호 필요: passwordPromptOpen = true
	 * - 비밀번호 불필요: joinChatRoom 호출
	 */
	$effect(() => {
		if (!activeRoomId || !authStore.user?.uid || !db) return;

		if (isSingleChat) {
			// 1:1 채팅: chat-joins 문서에 최소 정보만 업데이트
			// Cloud Functions(onChatJoinCreate)가 자동으로 필요한 필드들을 추가합니다.
			enterSingleChatRoom(db, activeRoomId, authStore.user.uid);
		} else {
			// 그룹/오픈 채팅: 비밀번호 확인 후 입장
			// 채팅방 정보 로드 완료 확인 (roomOwner가 null이 아니면 로드 완료)
			if (roomOwner !== null) {
				const needsPassword = roomPasswordEnabled && !isRoomMember && !isRoomOwner;

				if (needsPassword) {
					// 비밀번호 필요: 모달 표시
					passwordPromptOpen = true;
				} else if (isRoomMember || isRoomOwner) {
					// 이미 members이거나 owner인 경우: 입장 (chat-joins 업데이트)
					joinChatRoom(db, activeRoomId, authStore.user.uid);
				} else {
					// 비밀번호 불필요하지만 members도 아닌 경우: 자동으로 members에 추가
					joinChatRoom(db, activeRoomId, authStore.user.uid);
				}
			}
		}
	});

	/**
	 * 비밀번호 입력 성공 핸들러
	 *
	 * room-password-prompt.svelte에서 invalidate()를 호출하므로
	 * isRoomMember가 true로 변경되어 위의 $effect가 다시 실행되고
	 * joinChatRoom이 자동으로 호출됩니다.
	 */
	function handlePasswordSuccess() {
		passwordPromptOpen = false;
		// console.log('✅ 비밀번호 검증 성공 - 채팅방 입장');
	}

	/**
	 * 비밀번호 입력 취소 핸들러
	 *
	 * 채팅 목록으로 이동
	 */
	function handlePasswordCancel() {
		passwordPromptOpen = false;
		goto('/chat/list');
	}

	const targetProfile = $derived(userProfileFirestoreStore.getCachedProfile(uidParam));
	const targetProfileLoading = $derived(userProfileFirestoreStore.isLoading(uidParam));
	const targetProfileError = $derived(userProfileFirestoreStore.getError(uidParam));

	// 채팅 상대 표시 이름
	const targetDisplayName = $derived.by(() => {
		if (targetProfile?.displayName) return targetProfile.displayName;
		if (uidParam) return `@${uidParam.slice(0, 6)}`;
		return m.chatPartner();
	});

	// 작성 중인 메시지
	let composerText = $state('');
	let composerRows = $state(1); // textarea 줄 수 (최대 4줄까지 높이 증가, 이후 스크롤)
	let isSending = $state(false);
	let sendError = $state<string | null>(null);

	// 파일 업로드 상태
	let fileInputRef: HTMLInputElement | null = $state(null);
	let uploadingFiles: FileUploadStatus[] = $state([]);

	// v1.2.0: 드래그 앤 드롭 상태
	let isDragging = $state(false);
	let dragCounter = $state(0); // dragenter/dragleave 카운터

	// 최대 파일 크기
	const MAX_FILE_SIZE = 10 * 1024 * 1024; // 일반 파일: 10MB
	const MAX_VIDEO_SIZE = 24 * 1024 * 1024; // 동영상 파일 (.mp4): 24MB

	// ChatFavoritesDialog 상태
	let favoritesDialogOpen = $state(false);

	// UserSearchDialog 상태 (친구 초대용)
	let inviteDialogOpen = $state(false);

	// 비밀번호 설정 Dialog 상태
	let passwordSettingDialogOpen = $state(false);

	// 비밀번호 입력 Prompt 모달 상태
	let passwordPromptOpen = $state(false);

	// 메시지 수정 모달 상태
	let editModalOpen = $state(false);
	let selectedMessageId = $state<string>('');
	let selectedMessageText = $state<string>('');
	let selectedMessageUrls = $state<Record<number, string>>({});
	let selectedMessageCreatedAt = $state<number>(0);

	// 채팅방 정보 구독 (owner, password 등)
	let roomOwner = $state<string | null>(null);
	let roomPasswordEnabled = $state(false);
	let roomPasswordValue = $state<string>('');
	let isRoomMember = $state(false); // 현재 사용자가 members인지 여부

	/**
	 * 채팅방 정보 구독 (그룹/오픈 채팅방만) - Firestore
	 *
	 * 구독 경로:
	 * - chats/{roomId}: 채팅방 문서 (owner, password 필드 포함)
	 * - chats/{roomId}/members/{uid}: 현재 사용자의 멤버 상태
	 * - chat-passwords/{roomId}: 실제 비밀번호 (owner만 읽기 가능)
	 */
	$effect(() => {
		if (!activeRoomId || !authStore.user?.uid || !db || isSingleChat) {
			roomOwner = null;
			roomPasswordEnabled = false;
			roomPasswordValue = '';
			isRoomMember = false;
			return;
		}

		// 채팅방 기본 정보 구독 (owner, password 플래그)
		const roomRef = doc(db, `chats/${activeRoomId}`);
		const unsubscribeRoom = onSnapshot(roomRef, (snapshot) => {
			if (snapshot.exists()) {
				const data = snapshot.data();
				roomOwner = data?.owner ?? null;
				roomPasswordEnabled = data?.password === true;
			} else {
				roomOwner = null;
				roomPasswordEnabled = false;
			}
		});

		// 현재 사용자의 members 상태 구독
		// 중요: members/{uid} 문서가 존재하면 멤버입니다
		// - value: true → 멤버이며 알림 구독
		// - value: false → 멤버이지만 알림 미구독
		// - 문서 없음 → 멤버가 아님
		const memberRef = doc(db, `chats/${activeRoomId}/members/${authStore.user.uid}`);
		const unsubscribeMember = onSnapshot(memberRef, (snapshot) => {
			isRoomMember = snapshot.exists(); // 문서 존재 여부만 확인
		});

		// 실제 비밀번호 구독 (owner만 읽기 가능)
		const passwordRef = doc(db, `chat-passwords/${activeRoomId}`);
		const unsubscribePassword = onSnapshot(passwordRef, (snapshot) => {
			if (snapshot.exists()) {
				const data = snapshot.data();
				roomPasswordValue = data?.password ?? '';
			} else {
				roomPasswordValue = '';
			}
		});

		return () => {
			unsubscribeRoom();
			unsubscribeMember();
			unsubscribePassword();
		};
	});

	// 현재 사용자가 채팅방 owner인지 확인
	const isRoomOwner = $derived.by(() => {
		if (!roomOwner || !authStore.user?.uid) return false;
		return roomOwner === authStore.user.uid;
	});

	// 핀 상태 관리
	let isPinned = $state(false);
	let currentRoomType = $derived.by(() => {
		if (isSingleChat) return 'single';
		// 그룹/오픈 채팅 구분은 roomId로는 불가능하므로 기본값 사용
		// TODO: 채팅방 정보에서 타입 가져오기
		return 'group';
	});

	// 채팅방 핀 상태 구독 - Firestore
	$effect(() => {
		if (!activeRoomId || !authStore.user?.uid || !db) {
			isPinned = false;
			return;
		}

		const chatJoinRef = doc(db, `users/${authStore.user.uid}/chat-joins/${activeRoomId}`);
		const unsubscribe = onSnapshot(chatJoinRef, (snapshot) => {
			if (!snapshot.exists()) {
				isPinned = false;
				return;
			}

			const data = snapshot.data();
			const pinValue = data?.pin;
			isPinned = pinValue === true;
		});

		return () => {
			unsubscribe();
		};
	});

	// 채팅방 알림 구독 상태 관리
	let isNotificationSubscribed = $state(true); // 기본값: 구독 중
	let subscriptionLoading = $state(false);

	/**
	 * 채팅방 알림 구독 상태 로드 - Firestore
	 *
	 * 1:1 채팅방: users/{uid}/chat-joins/{roomId}/fcm-subscription 확인
	 * - 필드 없음 → 구독 중 (true)
	 * - false → 구독 해제
	 *
	 * 그룹/오픈 채팅방: chats/{roomId}/members/{uid} 확인
	 * - value: true → 구독 중
	 * - value: false → 구독 해제
	 * - 문서 없음 → 구독 중 (기본값)
	 */
	$effect(() => {
		if (!activeRoomId || !authStore.user?.uid || !db) {
			isNotificationSubscribed = true; // 기본값
			return;
		}

		let unsubscribe: (() => void) | undefined;

		if (isSingleChat) {
			// 1:1 채팅방: fcm-subscription 필드 구독
			const chatJoinRef = doc(db, `users/${authStore.user.uid}/chat-joins/${activeRoomId}`);

			unsubscribe = onSnapshot(chatJoinRef, (snapshot) => {
				if (!snapshot.exists()) {
					isNotificationSubscribed = true; // 기본값: 구독 중
					return;
				}

				const data = snapshot.data();
				const value = data?.['fcm-subscription'];
				isNotificationSubscribed = value !== false;
			});
		} else {
			// 그룹/오픈 채팅방: members 문서 구독
			const memberRef = doc(db, `chats/${activeRoomId}/members/${authStore.user.uid}`);

			unsubscribe = onSnapshot(memberRef, (snapshot) => {
				if (!snapshot.exists()) {
					isNotificationSubscribed = true; // 기본값: 구독 중
					return;
				}

				const data = snapshot.data();
				const value = data?.value;
				isNotificationSubscribed = value === true;
			});
		}

		return () => {
			if (unsubscribe) {
				unsubscribe();
			}
		};
	});

	// 채팅 입력 창(textarea) 직접 참조
	let composerInputRef: HTMLTextAreaElement | null = $state(null);

	/**
	 * textarea 입력 핸들러: 줄 수를 자동 계산하여 최대 4줄까지 높이 증가
	 */
	function handleComposerInput() {
		if (!composerInputRef) return;

		// 줄바꿈 개수 계산
		const lines = composerText.split('\n');
		const lineCount = lines.length;

		// 최대 4줄까지 높이 증가, 이후 스크롤바로 처리
		composerRows = Math.min(lineCount, 4);
	}

	/**
	 * textarea 키보드 이벤트 핸들러
	 * - Enter: 메시지 전송
	 * - Shift+Enter: 줄바꿈 (무제한, 단 높이는 최대 4줄)
	 */
	function handleComposerKeyDown(event: KeyboardEvent) {
		// Shift+Enter: 줄바꿈 허용 (무제한)
		if (event.key === 'Enter' && event.shiftKey) {
			// 줄바꿈 제한 없이 기본 동작 허용
			// 4줄 이상일 경우 스크롤바로 처리
			return;
		}

		// Enter만 누르면 메시지 전송
		if (event.key === 'Enter' && !event.shiftKey) {
			event.preventDefault();
			// 폼 submit 이벤트 트리거
			const target = event.currentTarget as HTMLElement;
			const form = target?.closest('form');
			if (form) {
				form.requestSubmit();
			}
		}
	}

	// 메시지 전송 처리
	async function handleSendMessage(event: SubmitEvent) {
		event.preventDefault();
		if (isSending) return;
		if (!composerText.trim() && uploadingFiles.length === 0) return;
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

		try {
			let urls: Record<number, string> = {};

			// 1. 이미 업로드된 파일 URL 수집
			if (uploadingFiles.length > 0) {
				// console.log(`📤 ${uploadingFiles.length}개 파일 정보 수집`);

				// 업로드 완료되지 않은 파일이 있는지 확인
				const incompleteFiles = uploadingFiles.filter((fs) => !fs.completed && !fs.error);
				if (incompleteFiles.length > 0) {
					sendError = `업로드 중인 파일이 ${incompleteFiles.length}개 있습니다. 업로드 완료 후 다시 시도해주세요.`;
					isSending = false;
					return;
				}

				// 업로드 실패한 파일이 있는지 확인
				const failedFiles = uploadingFiles.filter((fs) => fs.error);
				if (failedFiles.length > 0) {
					sendError = `업로드 실패한 파일이 ${failedFiles.length}개 있습니다. 삭제 후 다시 시도해주세요.`;
					isSending = false;
					return;
				}

				// 이미 업로드된 URL 수집
				uploadingFiles.forEach((fs, index) => {
					if (fs.downloadUrl) {
						urls[index] = fs.downloadUrl;
					}
				});

				// console.log(`✅ ${Object.keys(urls).length}개 파일 URL 수집 완료`);
			}

			// 2. 메시지 전송 (Firestore)
			const trimmed = composerText.trim();

			const payload = {
				roomId: activeRoomId,
				type: 'message',
				text: trimmed,
				urls,
				senderUid: authStore.user.uid,
				createdAt: Timestamp.now(),
				editedAt: null,
				deletedAt: null
			};

			const result = await addDocument(messagePath, payload);

			if (!result.success) {
				sendError = result.error ?? m.chatSendFailed();
				isSending = false;
			} else {
				// 메시지 전송 성공 시
				composerText = '';
		composerRows = 1; // textarea 줄 수 초기화
				sendError = null;
				isSending = false;

				// 업로드된 파일 목록 초기화 (이미 Storage에 업로드되어 있음)
				uploadingFiles = [];

				// 전송 소리 재생
				try {
					const sendSound = new Audio('/sound/send.mp3');
					sendSound.play().catch((error) => {
						console.warn('소리 재생 실패:', error);
					});
				} catch (error) {
					console.warn('소리 재생 실패:', error);
				}

				// DOM 업데이트 완료 후 포커스 추가
				await tick();

				// 브라우저 렌더링 완료를 확실히 기다린 후 포커스
				requestAnimationFrame(() => {
					if (composerInputRef) {
						composerInputRef.focus();
						// console.log('✅ 채팅 입력 창에 포커스 추가됨');
					}
				});
			}
		} catch (error) {
			console.error('❌ 메시지 전송 실패:', error);
			sendError = '메시지 전송에 실패했습니다. 다시 시도해주세요.';
			isSending = false;
		}
	}

	// 메시지 작성 가능 여부
	const composerDisabled = $derived.by(() => !authStore.isAuthenticated || !activeRoomId);

	// DatabaseListView 컴포넌트 참조 (스크롤 제어용)
	let databaseListView: any = $state(null);

	// 발신자 라벨
	function resolveSenderLabel(senderUid?: string | null) {
		if (!senderUid) return m.chatUnknownUser();
		if (senderUid === authStore.user?.uid) return m.chatYou();
		if (senderUid === uidParam && targetDisplayName) return targetDisplayName;
		return senderUid.slice(0, 10);
	}

	// 스크롤을 맨 위로 이동
	function handleScrollToTop() {
		databaseListView?.scrollToTop();
	}

	// 스크롤을 맨 아래로 이동
	function handleScrollToBottom() {
		databaseListView?.scrollToBottom();
	}

	// 뒤로가기 (채팅 목록으로)
	function handleGoBack() {
		void goto('/chat/list');
	}

	// 즐겨찾기 추가/제거
	// 즐겨찾기 다이얼로그를 열어서 현재 채팅방이 포함된 폴더를 강조 표시합니다.
	function handleBookmark() {
		favoritesDialogOpen = true;
	}

	// URL 복사
	async function handleCopyUrl() {
		try {
			const url = window.location.href;
			await navigator.clipboard.writeText(url);
			// console.log('URL 복사됨:', url);
			// TODO: 토스트 메시지로 알림
		} catch (error) {
			console.error('URL 복사 실패:', error);
		}
	}

	// 멤버 목록
	function handleMemberList() {
		// console.log('멤버 목록 클릭');
		// TODO: 멤버 목록 다이얼로그 표시
	}

	// 즐겨찾기에서 채팅방 선택 핸들러
	// 선택된 채팅방으로 이동합니다.
	function handleRoomSelected(event: CustomEvent<{ roomId: string }>) {
		const { roomId } = event.detail;
		void goto(`/chat/room?roomId=${roomId}`);
	}

	// 방 탈퇴하기
	async function handleLeaveRoom() {
		if (!activeRoomId || !authStore.user?.uid || !db) return;

		const confirmed = confirm('채팅방에서 나가시겠습니까?');
		if (!confirmed) return;

		try {
			await leaveChatRoom(db, activeRoomId, authStore.user.uid);
			// console.log('채팅방 탈퇴 완료');
			void goto('/chat/list');
		} catch (error) {
			console.error('채팅방 탈퇴 실패:', error);
		}
	}

	// 신고하고 탈퇴하기
	function handleReportAndLeave() {
		// console.log('신고하고 탈퇴하기 클릭');
		// TODO: 신고 다이얼로그 표시 후 탈퇴
	}

	/**
	 * 친구 초대 메뉴 클릭 핸들러
	 * UserSearchDialog를 열어서 초대할 친구를 검색합니다.
	 */
	function handleInviteFriend() {
		inviteDialogOpen = true;
	}

	/**
	 * 사용자 선택 핸들러 (초대 실행)
	 * UserSearchDialog에서 사용자를 선택하면 채팅방에 초대합니다.
	 */
	async function handleUserSelect(event: CustomEvent<{ user: any; uid: string }>) {
		const { uid } = event.detail;

		if (!activeRoomId || !authStore.user?.uid || !db) {
			console.error('채팅방 또는 사용자 정보 없음');
			return;
		}

		try {
			await inviteUserToChatRoom(db, activeRoomId, uid, authStore.user.uid);
			// console.log('✅ 초대 성공:', uid);
			alert(m.chatInvitationSent());
		} catch (error) {
			console.error('❌ 초대 실패:', error);
			alert('초대를 보내지 못했습니다.');
		}
	}

	/**
	 * 채팅방 핀 토글 핸들러
	 * 채팅방을 핀하거나 핀 해제합니다
	 */
	async function handleTogglePin() {
		if (!activeRoomId || !authStore.user?.uid || !db) {
			console.error('채팅방 또는 사용자 정보 없음');
			return;
		}

		try {
			const newPinState = await togglePinChatRoom(
				db,
				activeRoomId,
				authStore.user.uid,
				currentRoomType
			);
			// console.log(`✅ 채팅방 핀 ${newPinState ? '설정' : '해제'} 완료:`, activeRoomId);
		} catch (error) {
			console.error('채팅방 핀 토글 실패:', error);
			alert('핀 기능을 사용할 수 없습니다. 채팅방에 참여한 후 시도해주세요.');
		}
	}

	/**
	 * 채팅방 알림 구독 토글 핸들러
	 *
	 * 1:1 채팅방:
	 * - 구독 → 구독 해제: fcm-subscription: false 저장
	 * - 구독 해제 → 구독: fcm-subscription 필드 삭제
	 *
	 * 그룹/오픈 채팅방:
	 * - 구독 → 구독 해제: members/{uid}: { value: false } 저장
	 * - 구독 해제 → 구독: members/{uid}: { value: true } 저장
	 */
	async function handleToggleNotificationSubscription() {
		if (!activeRoomId || !authStore.user?.uid || !db || subscriptionLoading) {
			console.error('채팅방 또는 사용자 정보 없음');
			return;
		}

		subscriptionLoading = true;
		const newStatus = !isNotificationSubscribed;

		try {
			if (isSingleChat) {
				// 1:1 채팅방
				const chatJoinRef = doc(db, `users/${authStore.user.uid}/chat-joins/${activeRoomId}`);

				if (newStatus) {
					// 구독: 필드 삭제
					await updateDoc(chatJoinRef, {
						'fcm-subscription': deleteField()
					});
					// console.log(`📢 1:1 채팅방 알림 구독 완료: ${activeRoomId}`);
				} else {
					// 구독 해제: false 저장
					await updateDoc(chatJoinRef, {
						'fcm-subscription': false
					});
					// console.log(`🔕 1:1 채팅방 알림 구독 해제: ${activeRoomId}`);
				}
			} else {
				// 그룹/오픈 채팅방
				const memberRef = doc(db, `chats/${activeRoomId}/members/${authStore.user.uid}`);
				await setDoc(memberRef, { value: newStatus });
				// console.log(
				// 	`${newStatus ? '📢' : '🔕'} 그룹 채팅방 알림 ${newStatus ? '구독' : '구독 해제'}: ${activeRoomId}`
				// );
			}

			// 로컬 상태 업데이트 (onSnapshot 리스너가 자동으로 업데이트하지만 즉각적인 UI 반영을 위해)
			isNotificationSubscribed = newStatus;
		} catch (error) {
			console.error('알림 구독 상태 변경 실패:', error);
			alert('알림 설정을 변경할 수 없습니다. 잠시 후 다시 시도해주세요.');
		} finally {
			subscriptionLoading = false;
		}
	}

	/**
	 * 현재 채팅방의 읽지 않은 메시지 수를 0으로 초기화합니다.
	 *
	 * 사용자가 채팅방에 입장해 있는 상태에서 새 메시지를 읽었음을 표시하기 위해
	 * Firebase RTDB의 `/chat-joins/{uid}/{roomId}/newMessageCount`를 0으로 업데이트합니다.
	 *
	 * **타이밍 이슈 해결:**
	 * 새 메시지가 생성되면 다음과 같은 순서로 처리됩니다:
	 * 1. Firebase RTDB에 새 메시지 노드 생성
	 * 2. Cloud Functions의 onChatMessageCreate 트리거 실행 → newMessageCount +1 증가
	 * 3. 클라이언트의 DatabaseListView가 새 메시지 감지 → handleNewMessage 콜백 호출
	 *
	 * 문제: 클라이언트가 즉시 newMessageCount를 0으로 설정하면,
	 * Cloud Functions가 아직 실행 중이거나 완료되지 않아 값이 다시 1로 증가할 수 있습니다.
	 * 결과적으로 채팅 목록에 읽지 않은 메시지 배지(1)가 남아있게 됩니다.
	 *
	 * 해결책: 0.79초(790ms) 지연 후 newMessageCount를 0으로 설정합니다.
	 * 이렇게 하면 Cloud Functions가 먼저 +1 증가를 완료한 후,
	 * 클라이언트가 0으로 초기화하여 배지가 정확히 사라집니다.
	 *
	 * @returns {boolean} 업데이트 시도 여부 (true: 업데이트 시도함, false: 조건 미충족으로 건너뜀)
	 */
	function markCurrentRoomAsRead(): boolean {
		// 채팅방 활성화 상태 및 사용자 인증 확인
		if (!activeRoomId || !authStore.user?.uid || !db) {
			// console.log('채팅방 또는 사용자 정보 없음 - newMessageCount 업데이트 건너뜀');
			return false;
		}

		// Cloud Functions 실행 완료를 기다린 후 newMessageCount를 0으로 업데이트
		// 790ms 지연을 두어 Cloud Functions의 +1 증가가 먼저 완료되도록 보장
		setTimeout(() => {
			// 다시 한번 유효성 검사 (타이머 실행 중 사용자가 로그아웃하거나 방을 나갈 수 있음)
			if (!activeRoomId || !authStore.user?.uid || !db) {
				// console.log('타이머 실행 중 상태 변경 - newMessageCount 업데이트 취소');
				return;
			}

			const chatJoinRef = doc(db, `users/${authStore.user.uid}/chat-joins/${activeRoomId}`);
			updateDoc(chatJoinRef, {
				newMessageCount: 0
			})
				.then(() => {
					// console.log('newMessageCount 0으로 업데이트 완료 (채팅방에서 새 메시지 읽음 처리)');
				})
				.catch((error) => {
					console.error('newMessageCount 업데이트 실패:', error);
				});
		}, 790); // 0.79초 지연

		return true;
	}

	/**
	 * FirestoreListView에서 새 메시지 추가 시 호출되는 콜백
	 *
	 * 사용자가 채팅방에 입장해 있는 상태에서 새로운 메시지가 도착하면
	 * 즉시 읽음 처리를 위해 newMessageCount를 0으로 업데이트합니다.
	 *
	 * @param item - 새로 추가된 메시지 아이템 ({ id: string, data: any })
	 */
	function handleNewMessage(item: { id: string; data: any }) {
		// console.log('새 메시지 추가됨:', item);

		// 현재 채팅방을 읽음 상태로 표시
		markCurrentRoomAsRead();

		// TODO: 필요한 추가 작업 수행
		// 예: 사운드 재생, 알림 표시, 배지 업데이트 등
	}

	/**
	 * 메시지 수정 버튼 클릭 핸들러
	 *
	 * @param messageId - 메시지 ID
	 * @param text - 메시지 텍스트
	 * @param urls - 첨부파일 URL 목록
	 * @param createdAt - 메시지 생성 시각
	 */
	function handleEditMessage(
		messageId: string,
		text: string,
		urls: Record<number, string>,
		createdAt: number
	) {
		selectedMessageId = messageId;
		selectedMessageText = text ?? '';
		selectedMessageUrls = urls ?? {};
		selectedMessageCreatedAt = createdAt;
		editModalOpen = true;
	}

	/**
	 * 메시지 삭제 버튼 클릭 핸들러
	 *
	 * @param messageId - 메시지 ID
	 * @param urls - 첨부파일 URL 목록
	 */
	async function handleDeleteMessage(messageId: string, urls: Record<number, string>) {
		const confirmed = confirm('메시지를 삭제하시겠습니까?');
		if (!confirmed) return;

		if (!db) {
			alert('Firebase 연결이 없습니다.');
			return;
		}

		try {
			// 1. 첨부파일 삭제 (Storage)
			if (urls && Object.keys(urls).length > 0) {
				for (const url of Object.values(urls)) {
					try {
						await deleteChatFile(url);
					} catch (err) {
						console.error('첨부파일 삭제 실패:', err);
						// 첨부파일 삭제 실패해도 계속 진행
					}
				}
			}

			// 2. 메시지 Soft Delete (deleted: true, urls/text 필드 제거)
			const messageRef = doc(db, `chat-messages/${messageId}`);
			await updateDoc(messageRef, {
				deleted: true,
				deletedAt: Date.now(),
				text: '',
				urls: deleteField()
			});

			// console.log('메시지 삭제 완료:', messageId);
		} catch (err) {
			console.error('메시지 삭제 실패:', err);
			alert('메시지 삭제에 실패했습니다. 다시 시도해주세요.');
		}
	}

	/**
	 * 메시지 수정 가능 여부 확인
	 *
	 * 90분(5400초) 이내 메시지만 수정/삭제 가능
	 *
	 * @param createdAt - 메시지 생성 시각 (밀리초)
	 * @returns 수정 가능 여부
	 */
	function canEditMessage(createdAt: number): boolean {
		if (!createdAt) return false;

		const now = Date.now();
		const elapsed = now - createdAt;
		const ninetyMinutesInMs = 90 * 60 * 1000; // 90분 = 5,400,000ms

		return elapsed < ninetyMinutesInMs;
	}

	/**
	 * 메시지 수정 모달 닫기 핸들러
	 */
	function handleEditModalClose() {
		editModalOpen = false;
	}

	/**
	 * 메시지 수정 저장 완료 핸들러
	 */
	function handleEditModalSaved() {
		// console.log('메시지 수정 완료');
		// 모달은 자동으로 닫힘 (MessageEditModal에서 onClose 호출)
	}

	/**
	 * 파일 선택 버튼 클릭 핸들러
	 */
	function handleFileButtonClick() {
		fileInputRef?.click();
	}

	/**
	 * 파일 선택 핸들러
	 * 파일 선택 즉시 Firebase Storage에 업로드를 시작합니다.
	 */
	async function handleFileSelect(event: Event) {
		const input = event.target as HTMLInputElement;
		const files = Array.from(input.files || []);

		if (files.length === 0) return;

		// 사용자 인증 및 채팅방 확인
		if (!authStore.user?.uid || !activeRoomId) {
			alert('로그인 후 채팅방에 입장해야 파일을 업로드할 수 있습니다.');
			return;
		}

		// console.log(`📂 ${files.length}개 파일 선택됨 - 즉시 업로드 시작`);

		// v1.2.0: 공통 processFiles 함수 사용
		await processFiles(files);

		// input 초기화 (같은 파일 다시 선택 가능하도록)
		input.value = '';
	}

	/**
	 * 파일 삭제 핸들러
	 * Firebase Storage에서 실제 파일을 삭제합니다.
	 */
	async function handleRemoveFile(index: number) {
		const fileStatus = uploadingFiles[index];

		// Firebase Storage에서 파일 삭제 (업로드 완료된 경우만)
		if (fileStatus.downloadUrl) {
			try {
				// console.log(`🗑️ Firebase Storage에서 파일 삭제 시작: ${fileStatus.file.name}`);
				await deleteChatFile(fileStatus.downloadUrl);
				// console.log(`✅ 파일 삭제 완료: ${fileStatus.file.name}`);
			} catch (error) {
				console.error(`❌ 파일 삭제 실패: ${fileStatus.file.name}`, error);
				// 삭제 실패해도 로컬 목록에서는 제거
			}
		}

		// 로컬 목록에서 제거
		uploadingFiles = uploadingFiles.filter((_, i) => i !== index);
	}

	/**
	 * v1.2.0: 드래그 앤 드롭 이벤트 핸들러
	 */

	/**
	 * 드래그 진입 (dragenter)
	 * - 파일을 드래그하여 영역에 진입할 때 호출
	 */
	function handleDragEnter(event: DragEvent) {
		event.preventDefault();
		event.stopPropagation();

		dragCounter++;

		// 파일이 포함되어 있는지 확인
		if (event.dataTransfer?.types.includes('Files')) {
			isDragging = true;
		}
	}

	/**
	 * 드래그 오버 (dragover)
	 * - 파일을 드래그하여 영역 위에 있을 때 지속적으로 호출
	 */
	function handleDragOver(event: DragEvent) {
		event.preventDefault();
		event.stopPropagation();

		// dropEffect 설정 (복사 아이콘 표시)
		if (event.dataTransfer) {
			event.dataTransfer.dropEffect = 'copy';
		}
	}

	/**
	 * 드래그 이탈 (dragleave)
	 * - 파일을 드래그하여 영역을 벗어날 때 호출
	 */
	function handleDragLeave(event: DragEvent) {
		event.preventDefault();
		event.stopPropagation();

		dragCounter--;

		// 카운터가 0이 되면 드래그 상태 해제
		if (dragCounter === 0) {
			isDragging = false;
		}
	}

	/**
	 * 드롭 (drop)
	 * - 파일을 드롭할 때 호출
	 */
async function handleDrop(event: DragEvent) {
	event.preventDefault();
	event.stopPropagation();

		isDragging = false;
		dragCounter = 0;

		const files = event.dataTransfer?.files;
		if (!files || files.length === 0) {
			return;
		}

		// console.log(`📦 드롭된 파일 개수: ${files.length}`);

	// 파일 처리 (handleFileSelect와 동일한 로직)
	await processFiles(Array.from(files));
}

/**
 * 메시지 리스트 등 비드랍 영역에서 기본 드롭 동작만 차단
 */
function preventDrop(event: DragEvent) {
	event.preventDefault();
	event.stopPropagation();
}

	/**
	 * 파일 처리 공통 함수
	 * - 파일 선택 및 드래그 앤 드롭에서 공통으로 사용
	 */
	async function processFiles(files: File[]) {
		for (const file of files) {
			// 파일 크기 체크
			const isVideo = file.type === 'video/mp4' || file.name.toLowerCase().endsWith('.mp4');
			const maxSize = isVideo ? MAX_VIDEO_SIZE : MAX_FILE_SIZE;
			const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(0);

			if (file.size > maxSize) {
				alert(`파일 크기가 ${maxSizeMB}MB를 초과합니다: ${file.name}`);
				continue;
			}

			// console.log(`📎 파일 선택됨: ${file.name} (${formatFileSize(file.size)})`);

			// 파일 업로드 상태 추가 (progress: 0, completed: false)
			const fileStatus: FileUploadStatus = {
				file,
				progress: 0,
				completed: false
			};

			uploadingFiles = [...uploadingFiles, fileStatus];
			const currentIndex = uploadingFiles.length - 1;

			// Firebase Storage에 즉시 업로드 시작
			try {
				const downloadUrl = await uploadChatFile(
					file,
					authStore.user!.uid,
					activeRoomId,
					(progress) => {
						// 진행률 업데이트
						uploadingFiles[currentIndex].progress = progress;
					}
				);

				// 업로드 완료
				uploadingFiles[currentIndex].completed = true;
				uploadingFiles[currentIndex].downloadUrl = downloadUrl;
				// console.log(`✅ 파일 업로드 완료: ${file.name}`);
			} catch (error) {
				console.error('❌ 파일 업로드 실패:', error);
				uploadingFiles[currentIndex].error = '업로드 실패';
			}
		}
	}

	/**
	 * 컴포넌트 정리
	 * 더 이상 blob URL을 사용하지 않으므로 정리 작업이 필요 없습니다.
	 * (Storage URL은 Firebase에서 자동으로 관리됨)
	 */
	onDestroy(() => {
		// 정리 작업 없음
	});
</script>

<svelte:head>
	<title>{m.pageTitleChat()}</title>
</svelte:head>

<!-- 채팅방 전체 컨테이너: Flexbox로 화면 높이 최대 활용 -->
<div class="chat-room-container">
	<!-- 채팅방 상단 헤더 -->
	<header class="chat-room-header">
		<!-- 뒤로가기 버튼 -->
		<Button variant="ghost" size="icon" onclick={handleGoBack} class="shrink-0">
			<span class="text-xl">←</span>
		</Button>

		<!-- 채팅방 제목/프로필 -->
		<div class="flex flex-1 items-center gap-3 overflow-hidden">
			{#if isSingleChat && uidParam}
				<!-- 1:1 채팅: 프로필 사진 + 이름 -->
				<Avatar uid={uidParam} size={40} class="shrink-0 shadow-sm" />
				<div class="flex-1 overflow-hidden">
					<h1 class="truncate text-lg font-semibold text-gray-900">{targetDisplayName}</h1>
					{#if targetProfileLoading}
						<p class="text-xs text-gray-500">로딩 중...</p>
					{:else if targetProfileError}
						<p class="text-xs text-red-500">프로필 로드 실패</p>
					{/if}
				</div>
			{:else if roomIdParam}
				<!-- 그룹/오픈 채팅: 방 이름 -->
				<div class="flex-1 overflow-hidden">
					<h1 class="truncate text-lg font-semibold text-gray-900">
						{m.chatRoom()}
						{roomIdParam}
					</h1>
					<p class="text-xs text-gray-500">{m.chatChatRoom()}</p>
				</div>
			{:else}
				<!-- 기본 상태 -->
				<div class="flex-1 overflow-hidden">
					<h1 class="text-lg font-semibold text-gray-900">{m.chatOverview()}</h1>
					<p class="text-xs text-gray-500">{m.chatSelectConversation()}</p>
				</div>
			{/if}
		</div>

		<!-- 핀 버튼 -->
		<Button
			variant="ghost"
			size="icon"
			onclick={handleTogglePin}
			class="shrink-0"
			title={isPinned ? '핀 해제' : '핀 설정'}
		>
			<span class="text-xl">{isPinned ? '📌' : '📍'}</span>
		</Button>

		<!-- 알림 구독 버튼 -->
		<Button
			variant="ghost"
			size="icon"
			onclick={handleToggleNotificationSubscription}
			disabled={subscriptionLoading}
			class="shrink-0"
			title={isNotificationSubscribed ? '알림 구독 해제' : '알림 구독'}
		>
			{#if isNotificationSubscribed}
				<!-- 구독 중: 진한 벨 아이콘 (실선) -->
				<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
					<path
						d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
					/>
				</svg>
			{:else}
				<!-- 구독 해제: 연한 벨 아이콘 + 슬래시 -->
				<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
					/>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6" />
				</svg>
			{/if}
		</Button>

		<!-- 메뉴 드롭다운 -->
		<DropdownMenu.Root>
			<DropdownMenu.Trigger>
				<Button variant="ghost" size="icon" class="shrink-0">
					<span class="text-xl">⋮</span>
				</Button>
			</DropdownMenu.Trigger>
			<DropdownMenu.Content align="end" class="w-56">
				<DropdownMenu.Item onclick={handleBookmark} class="bg-pink-50 hover:bg-pink-100">
					<span class="mr-2">🔖</span>
					{m.chatTabBookmarks()}
				</DropdownMenu.Item>
				<DropdownMenu.Item onclick={handleCopyUrl} class="bg-gray-50 hover:bg-gray-100">
					<span class="mr-2">🔗</span>
					URL 복사
				</DropdownMenu.Item>
				<DropdownMenu.Separator />
				{#if !isSingleChat}
					<!-- 그룹/오픈 채팅방에서만 친구 초대 기능 표시 -->
					<DropdownMenu.Item onclick={handleInviteFriend} class="bg-green-50 hover:bg-green-100">
						<span class="mr-2">👤</span>
						{m.chatInviteFriend()}
					</DropdownMenu.Item>
					<DropdownMenu.Separator />

					<!-- Owner만 비밀번호 설정 메뉴 표시 -->
					{#if isRoomOwner}
						<DropdownMenu.Item
							onclick={() => (passwordSettingDialogOpen = true)}
							class="bg-purple-50 hover:bg-purple-100"
						>
							<span class="mr-2">🔒</span>
							{m.chatPasswordSettings()}
						</DropdownMenu.Item>
						<DropdownMenu.Separator />
					{/if}
				{/if}
				<DropdownMenu.Item onclick={handleMemberList} class="bg-blue-50 hover:bg-blue-100">
					<span class="mr-2">👥</span>
					멤버 목록
				</DropdownMenu.Item>
				<DropdownMenu.Separator />
				<DropdownMenu.Item
					onclick={handleLeaveRoom}
					class="bg-orange-50 text-orange-600 hover:bg-orange-100"
				>
					<span class="mr-2">🚪</span>
					방 탈퇴하기
				</DropdownMenu.Item>
				<DropdownMenu.Item
					onclick={handleReportAndLeave}
					class="bg-yellow-50 text-red-600 hover:bg-yellow-100"
				>
					<span class="mr-2">⚠️</span>
					신고하고 탈퇴하기
				</DropdownMenu.Item>
			</DropdownMenu.Content>
		</DropdownMenu.Root>
	</header>

	{#if !activeRoomId}
		<section class="chat-room-empty p-8">
			<p class="empty-title">{m.chatRoomNotReady()}</p>
			<p class="empty-subtitle">
				{m.chatAddUidOrRoomId()}
			</p>
		</section>
	{:else}
		<!-- v1.2.0: 드래그 앤 드롭 지원 메시지 목록 -->
			<div
				class="message-list-section"
				role="region"
				aria-label="채팅 메시지 영역"
				ondragover={preventDrop}
				ondrop={preventDrop}
			>
			{#if canRenderMessages}
				{#key messagePath}
					<FirestoreListView
						bind:this={databaseListView}
						path={messagePath}
						pageSize={20}
						orderByField={messageOrderField}
						orderDirection="asc"
						threshold={300}
						scrollTrigger="top"
						autoScrollToEnd={true}
						autoScrollOnNewData={true}
						onItemAdded={handleNewMessage}
					>
						{#snippet item(itemData: { id: string; data: any })}
							{@const message = itemData.data ?? {}}
							{@const mine = message.senderUid === authStore.user?.uid}
							{@const messageId = itemData.id}
							{@const isEditable = mine && canEditMessage(message.createdAt) && !message.deleted}
							<article class={`message-row ${mine ? 'message-row--mine' : 'message-row--theirs'}`}>
								{#if !mine}
									<Avatar uid={message.senderUid} size={36} class="message-avatar" />
								{/if}
								<div class={`message-bubble-wrap ${mine ? 'items-end text-right' : ''}`}>
									{#if !mine}
										<span class="message-sender-label">{resolveSenderLabel(message.senderUid)}</span
										>
									{/if}

									{#if message.deleted}
										<!-- 삭제된 메시지 표시 -->
										<div class={`message-bubble ${mine ? 'bubble-mine' : 'bubble-theirs'} deleted-message`}>
											<p class="message-text m-0 text-gray-400 italic">삭제된 메시지입니다</p>
										</div>
									{:else}
										<!-- 일반 메시지 표시 -->
										<div class={`message-bubble ${mine ? 'bubble-mine' : 'bubble-theirs'}`}>
											<!-- 텍스트 -->
											{#if message.text}
												<p class="message-text m-0">{message.text}</p>
											{/if}

											<!-- 첨부파일 목록 -->
											{#if message.urls && Object.keys(message.urls).length > 0}
												<div class="message-attachments">
													{#each Object.entries(message.urls as Record<string, string>) as [index, url]}
														<a
															href={url}
															target="_blank"
															rel="noopener noreferrer"
															class="attachment-item"
														>
															{#if isImageUrl(url)}
																<!-- 이미지 첨부파일 -->
																<img src={url} alt="첨부 이미지" class="attachment-image" />
															{:else if isVideoUrl(url)}
																<!-- 동영상 첨부파일 -->
															<video
																src={url}
																class="attachment-video"
																controls
																aria-hidden="true"
																tabindex="-1"
															/>
															{:else}
																<!-- 일반 파일 첨부파일 -->
																<div class="attachment-file">
																	<div class="attachment-file-icon">
																		<span class="attachment-file-extension"
																			>{getFileExtension(url).replace('.', '').toUpperCase()}</span
																		>
																	</div>
																	<div class="file-details">
																		<p class="file-name">{getFilenameFromUrl(url)}</p>
																	</div>
																	<svg
																		class="download-icon"
																		fill="none"
																		stroke="currentColor"
																		viewBox="0 0 24 24"
																		stroke-width="2"
																	>
																		<path
																			stroke-linecap="round"
																			stroke-linejoin="round"
																			d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
																		/>
																	</svg>
																</div>
															{/if}
														</a>
													{/each}
												</div>
											{/if}
										</div>
									{/if}

									<!-- 타임스탬프 및 설정 아이콘 -->
									<div class="message-footer">
										<span class="message-timestamp">{formatChatMessageDate(message.createdAt)}</span>

										{#if isEditable}
											<!-- 설정 드롭다운 (90분 이내 메시지만) -->
											<DropdownMenu.Root>
												<DropdownMenu.Trigger>
													<button class="message-settings-button" aria-label="메시지 설정">
														⚙
													</button>
												</DropdownMenu.Trigger>
												<DropdownMenu.Content align="end" class="w-32">
													<DropdownMenu.Item
														onclick={() =>
															handleEditMessage(
																messageId,
																message.text ?? '',
																message.urls ?? {},
																message.createdAt
															)}
														class="hover:bg-blue-50"
													>
														<span class="mr-2">✏️</span>
														수정
													</DropdownMenu.Item>
													<DropdownMenu.Item
														onclick={() => handleDeleteMessage(messageId, message.urls ?? {})}
														class="text-red-600 hover:bg-red-50"
													>
														<span class="mr-2">🗑️</span>
														삭제
													</DropdownMenu.Item>
												</DropdownMenu.Content>
											</DropdownMenu.Root>
										{/if}
									</div>
								</div>
							</article>
						{/snippet}

						{#snippet loading()}
							<div class="message-placeholder py-6">{m.chatLoadingMessages()}</div>
						{/snippet}

						{#snippet empty()}
							<div class="message-placeholder py-6">{m.chatNoMessages()}</div>
						{/snippet}

						{#snippet error(errorMessage: string | null)}
							<div class="message-error py-4">
								<p>{m.chatLoadMessagesFailed()}</p>
								<p>{errorMessage ?? m.chatUnknownError()}</p>
							</div>
						{/snippet}

						{#snippet loadingMore()}
							<div class="message-placeholder subtle py-6">{m.chatLoadingMore()}</div>
						{/snippet}

						{#snippet noMore()}
							<div class="message-placeholder subtle py-6">{m.chatNoMoreMessages()}</div>
						{/snippet}
					</FirestoreListView>
				{/key}
			{:else}
				<div class="message-placeholder py-6">{m.chatPreparingStream()}</div>
			{/if}

			<!-- 스크롤 컨트롤 버튼 -->
			{#if canRenderMessages}
				<div class="scroll-controls">
					<button
						type="button"
						class="scroll-button scroll-to-top"
						onclick={handleScrollToTop}
						title="맨 위로 이동"
					>
						↑
					</button>
					<button
						type="button"
						class="scroll-button scroll-to-bottom"
						onclick={handleScrollToBottom}
						title="맨 아래로 이동"
					>
						↓
					</button>
				</div>
			{/if}

			</div>

		<!-- 파일 미리보기 Grid -->
		{#if uploadingFiles.length > 0}
			<div class="file-preview-container">
				<div class="file-preview-grid">
					{#each uploadingFiles as fileStatus, index}
						<div class="file-preview-item">
							<!-- 이미지/동영상 미리보기 (Storage URL 사용) -->
							{#if fileStatus.file.type.startsWith('image/') || fileStatus.file.type.startsWith('video/')}
								<div class="preview-thumbnail">
									{#if fileStatus.downloadUrl}
										<!-- 업로드 완료: Storage URL로 미리보기 표시 -->
										{#if fileStatus.file.type.startsWith('image/')}
											<img src={fileStatus.downloadUrl} alt={fileStatus.file.name} />
										{:else if fileStatus.file.type.startsWith('video/')}
											<video
												src={fileStatus.downloadUrl}
												controls
												aria-hidden="true"
												tabindex="-1"
											/>
										{/if}
									{:else}
										<!-- 업로드 중: 회색 배경만 표시 -->
										<div class="preview-placeholder"></div>
									{/if}

									<!-- v1.2.0: 원형 프로그레스바와 퍼센티지 표시 -->
									{#if !fileStatus.completed && !fileStatus.error}
										<div class="upload-progress-overlay">
											<!-- SVG 원형 프로그레스바 -->
											<svg class="progress-ring" width="80" height="80">
												<!-- 배경 원 -->
												<circle
													class="progress-ring-bg"
													cx="40"
													cy="40"
													r="32"
													stroke-width="6"
												/>
												<!-- 진행률 원 -->
												<circle
													class="progress-ring-circle"
													cx="40"
													cy="40"
													r="32"
													stroke-width="6"
													stroke-dasharray="201.06"
													stroke-dashoffset={201.06 - (201.06 * fileStatus.progress) / 100}
												/>
											</svg>
											<!-- 퍼센티지 숫자 -->
											<span class="upload-percentage">{fileStatus.progress}%</span>
										</div>
									{/if}
								</div>
							{:else}
								<!-- 일반 파일 아이콘 -->
								<div class="file-icon">
									<!-- v1.1.4: 파일명에서 직접 확장자 추출 (getExtensionFromFilename 사용) -->
									<span class="file-extension"
										>{getExtensionFromFilename(fileStatus.file.name).replace('.', '').toUpperCase()}</span
									>

									<!-- v1.2.0: 원형 프로그레스바와 퍼센티지 표시 (일반 파일) -->
									{#if !fileStatus.completed && !fileStatus.error}
										<div class="upload-progress-overlay">
											<!-- SVG 원형 프로그레스바 -->
											<svg class="progress-ring" width="80" height="80">
												<!-- 배경 원 -->
												<circle
													class="progress-ring-bg"
													cx="40"
													cy="40"
													r="32"
													stroke-width="6"
												/>
												<!-- 진행률 원 -->
												<circle
													class="progress-ring-circle"
													cx="40"
													cy="40"
													r="32"
													stroke-width="6"
													stroke-dasharray="201.06"
													stroke-dashoffset={201.06 - (201.06 * fileStatus.progress) / 100}
												/>
											</svg>
											<!-- 퍼센티지 숫자 -->
											<span class="upload-percentage">{fileStatus.progress}%</span>
										</div>
									{/if}
								</div>
							{/if}

							<!-- 에러 표시 -->
							{#if fileStatus.error}
								<div class="upload-error-overlay">
									<p class="upload-error">{fileStatus.error}</p>
								</div>
							{/if}

							<!-- 삭제 버튼 (항상 표시) -->
							<button
								type="button"
								class="remove-file-button"
								onclick={() => handleRemoveFile(index)}
							>
								✕
							</button>
						</div>
					{/each}
				</div>
			</div>
		{/if}

		<!-- 입력창 폼 -->
			<form
				class="composer-form"
				onsubmit={handleSendMessage}
				ondragenter={handleDragEnter}
				ondragover={handleDragOver}
				ondragleave={handleDragLeave}
				ondrop={handleDrop}
			>
			<!-- 파일 업로드 버튼 (카메라 아이콘) -->
			<button
				type="button"
				class="file-upload-button"
				onclick={handleFileButtonClick}
				disabled={composerDisabled || isSending}
				aria-label="파일 첨부"
			>
				<!-- 카메라 아이콘 -->
				<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
					/>
					<path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
				</svg>
			</button>

			<!-- 숨겨진 파일 입력 -->
			<input
				bind:this={fileInputRef}
				type="file"
				onchange={handleFileSelect}
				multiple
				accept="image/*,video/*,.pdf,.txt,.doc,.docx,.zip,.rar"
				style="display: none;"
			/>

		<textarea
			bind:this={composerInputRef}
			name="composer"
			class="composer-input"
			placeholder={m.chatWriteMessage()}
			bind:value={composerText}
			disabled={composerDisabled || isSending}
			rows={composerRows}
			oninput={handleComposerInput}
			onkeydown={handleComposerKeyDown}
		></textarea>
				<button
					type="submit"
					class="composer-button cursor-pointer"
					disabled={composerDisabled || isSending || (!composerText.trim() && uploadingFiles.length === 0)}
					aria-label={isSending ? m.chatSending() : m.chatSend()}
				>
				<!-- 전송 아이콘 (종이비행기) -->
				<svg
					class="w-6 h-6"
					fill="none"
					stroke="currentColor"
					viewBox="0 0 24 24"
					stroke-width="2"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"
						/>
					</svg>
				</button>

				{#if isDragging}
					<div class="drag-drop-overlay" role="region" aria-label="파일 드래그 앤 드롭 안내">
						<div class="drag-drop-content">
							<!-- 파일 아이콘 애니메이션 -->
							<svg class="drag-drop-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
								/>
							</svg>
							<!-- 안내 텍스트 -->
							<p class="drag-drop-title">파일을 여기에 놓으세요</p>
							<p class="drag-drop-subtitle">이미지, 동영상, 문서 등 다양한 파일을 업로드할 수 있습니다</p>
						</div>
					</div>
				{/if}
			</form>

		{#if sendError}
			<p class="composer-error">{sendError}</p>
		{/if}
	{/if}
</div>

<!-- 즐겨찾기 다이얼로그 -->
<ChatFavoritesDialog
	bind:open={favoritesDialogOpen}
	currentRoomId={activeRoomId}
	on:roomSelected={handleRoomSelected}
/>

<!-- 친구 초대 다이얼로그 -->
<UserSearchDialog
	bind:open={inviteDialogOpen}
	title={m.chatInviteFriend()}
	description={m.chatInviteToRoom()}
	submitLabel={m.chatInviteFriend()}
	showResults={true}
	on:userSelect={handleUserSelect}
/>

<!-- 비밀번호 설정 다이얼로그 (Owner 전용) -->
{#if !isSingleChat && isRoomOwner}
	<Dialog bind:open={passwordSettingDialogOpen}>
		<DialogContent class="sm:max-w-md">
			<DialogHeader>
				<DialogTitle>{m.chatPasswordSettings()}</DialogTitle>
			</DialogHeader>
			<RoomPasswordSetting
				roomId={activeRoomId}
				currentPassword={roomPasswordValue}
				onCancel={() => (passwordSettingDialogOpen = false)}
			/>
		</DialogContent>
	</Dialog>
{/if}

<!-- 비밀번호 입력 프롬프트 모달 (비회원 입장 시) -->
{#if !isSingleChat && passwordPromptOpen}
	<RoomPasswordPrompt
		roomId={activeRoomId}
		bind:open={passwordPromptOpen}
		onSuccess={handlePasswordSuccess}
		onCancel={handlePasswordCancel}
	/>
{/if}

<!-- 메시지 수정 모달 -->
<MessageEditModal
	bind:open={editModalOpen}
	messageId={selectedMessageId}
	initialText={selectedMessageText}
	initialUrls={selectedMessageUrls}
	roomId={activeRoomId}
	onClose={handleEditModalClose}
	onSaved={handleEditModalSaved}
/>

<style>
	@import 'tailwindcss' reference;

	/**
	 * 채팅방 전체 컨테이너
	 * Flexbox column 방향으로 부모(.chat-room-main) 높이를 활용합니다.
	 * 부모 컨테이너가 이미 화면 높이를 설정했으므로 h-full 사용
	 */
	.chat-room-container {
		@apply flex flex-col;
		/* 부모 높이 100% 사용 */
		@apply h-full;
		/* 최대 너비 (데스크톱) */
		@apply mx-auto max-w-[960px];
		/* 여백 최소화: 모바일 px-2, 데스크톱 px-4 */
		@apply px-2 pb-2 md:px-4 md:pb-0;
	}

	/**
	 * 채팅방 헤더 스타일
	 * 고정 높이, shrink-0으로 축소 방지
	 */
	.chat-room-header {
		@apply flex items-center gap-3 bg-white px-4 py-3;
		/* 모바일에서 여백 최소화 */
		@apply my-1 md:my-2;
		@apply rounded-xl md:rounded-2xl;
		@apply border border-gray-200;
		@apply shadow-sm md:shadow-[0_10px_25px_rgba(15,23,42,0.06)];
		/* 축소 방지 */
		@apply shrink-0;
	}

	/* 빈 채팅방 스타일 */
	.chat-room-empty {
		@apply flex-1;
		@apply flex items-center justify-center;
		@apply rounded-2xl border border-dashed border-gray-300 bg-[#fdfdfd] text-center;
	}

	.empty-title {
		@apply mb-2 text-xl font-semibold text-gray-900;
	}

	.empty-subtitle {
		@apply text-gray-500;
	}

	/**
	 * 메시지 목록 스타일
	 * flex-1로 남은 공간 모두 차지하고 스크롤 가능
	 */
	.message-list-section {
		@apply flex-1;
		/* 스크롤 가능하도록 overflow 설정 */
		@apply overflow-auto;
		/* 배경 및 테두리 */
		@apply rounded-xl border border-gray-200 bg-white md:rounded-2xl;
		/* 패딩 */
		@apply p-3 md:p-4;
		/* 최소 높이 지정 */
		@apply min-h-0;
		/* 상대 위치 (스크롤 버튼을 위해) */
		@apply relative;
		/* 여백 */
		@apply my-1 md:my-2;
	}

	.message-row {
		@apply flex gap-3 px-2 py-3;
	}

	.message-row--mine {
		@apply justify-end;
	}

	.message-row--theirs {
		@apply justify-start;
	}

	.message-avatar {
		@apply mr-2 rounded-full bg-gray-100 shadow-sm;
	}

	.message-bubble-wrap {
		@apply flex max-w-[75%] flex-col gap-1;
	}

	.message-bubble {
		@apply rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm;
	}

	.bubble-mine {
		@apply bg-amber-300 text-gray-900 shadow-inner;
	}

	.bubble-theirs {
		@apply border border-gray-200 bg-white text-gray-900;
	}

	.message-sender-label {
		@apply text-xs text-gray-400;
	}

	.message-text {
		@apply text-[0.95rem] break-words whitespace-pre-wrap text-gray-900;
	}

	.message-timestamp {
		@apply text-[11px] text-gray-400;
	}

	/* 메시지 하단 영역 (타임스탬프 + 설정 아이콘) */
	.message-footer {
		@apply flex items-center gap-2;
	}

	/* 메시지 설정 버튼 */
	.message-settings-button {
		@apply text-sm text-gray-400 transition-colors hover:text-gray-600;
		@apply cursor-pointer bg-transparent border-0 p-0;
	}

	/* 삭제된 메시지 스타일 */
	.deleted-message {
		@apply opacity-60;
	}

	/* 메시지 플레이스홀더 스타일 */
	.message-placeholder {
		@apply text-center text-gray-500;
	}

	.message-placeholder.subtle {
		@apply text-sm text-gray-400;
	}

	.message-error {
		@apply text-center text-red-600;
	}

	/**
	 * 입력창 폼 스타일
	 * shrink-0으로 축소 방지, items-end로 하단 정렬 (textarea가 여러 줄일 때 버튼들이 하단에 정렬)
	 */
		.composer-form {
			@apply relative flex items-end gap-2 md:gap-3;
			/* 축소 방지 */
			@apply shrink-0;
		}

	/* 파일 미리보기 컨테이너 */
	.file-preview-container {
		@apply px-2 pb-2 md:px-4 md:pb-3;
	}

	.file-preview-grid {
		@apply grid grid-cols-2 gap-2 md:grid-cols-3 lg:grid-cols-4;
	}

	.file-preview-item {
		@apply relative rounded-lg border-2 overflow-hidden shadow-sm;
		@apply transition-all hover:shadow-md;
	}

	.preview-thumbnail {
		@apply relative aspect-square w-full overflow-hidden bg-gray-100;
	}

	.preview-thumbnail img,
	.preview-thumbnail video {
		@apply h-full w-full object-cover;
	}

	.preview-placeholder {
		@apply h-full w-full bg-gray-200;
	}

	.file-icon {
		@apply relative flex aspect-square w-full items-center justify-center;
		@apply bg-gray-100 text-4xl text-gray-400;
	}

	.file-extension {
		@apply text-4xl md:text-5xl font-bold uppercase text-gray-600;
	}

	/* v1.2.0: 업로드 진행률 오버레이 - 원형 프로그레스바 */
	.upload-progress-overlay {
		@apply absolute inset-0 flex items-center justify-center;
		@apply bg-black/50 backdrop-blur-sm;
	}

	/* v1.2.0: SVG 원형 프로그레스바 */
	.progress-ring {
		@apply absolute;
		transform: rotate(-90deg); /* 12시 방향부터 시작 */
	}

	/* 배경 원 (회색) */
	.progress-ring-bg {
		@apply fill-none stroke-white/30;
	}

	/* 진행률 원 (애니메이션) */
	.progress-ring-circle {
		@apply fill-none stroke-blue-400;
		transition: stroke-dashoffset 0.3s ease-in-out;
		stroke-linecap: round;
	}

	/* 퍼센티지 숫자 (원형 프로그레스바 중앙) */
	.upload-percentage {
		@apply absolute text-2xl md:text-3xl font-bold text-white;
		@apply drop-shadow-lg;
		z-index: 10;
	}

	/* 에러 오버레이 */
	.upload-error-overlay {
		@apply absolute inset-0 flex items-center justify-center;
		@apply bg-red-500/80 backdrop-blur-sm p-2;
	}

	.upload-error {
		@apply text-xs text-center text-white font-semibold;
	}

	/* 삭제 버튼 (우측 상단 고정) */
	.remove-file-button {
		@apply absolute right-2 top-2 z-10;
		@apply flex h-8 w-8 items-center justify-center;
		@apply rounded-full bg-red-500 text-sm font-bold text-white shadow-lg;
		@apply transition-all hover:bg-red-600 hover:scale-110 active:scale-95;
	}

	/* 파일 업로드 버튼 (카메라 아이콘) */
	.file-upload-button {
		@apply flex items-center justify-center;
		@apply rounded-full border-0 bg-transparent;
		@apply text-gray-700 transition-all duration-200;
		@apply p-2;
		@apply hover:bg-gray-100 active:bg-gray-200;
	}

	.file-upload-button:disabled {
		@apply cursor-not-allowed text-gray-300;
		@apply hover:bg-transparent;
	}

	/* 메시지 입력 스타일 (textarea) */
	.composer-input {
		@apply flex-1;
		@apply rounded-2xl border border-gray-300 bg-white text-base;
		@apply px-3 py-2.5 md:px-4 md:py-3.5;
		/* textarea 전용 스타일 */
		@apply resize-none; /* 사용자가 수동으로 크기 조정하지 못하게 */
		@apply leading-relaxed; /* 줄 간격 */
		@apply align-middle; /* 수직 정렬 */
		/* 최소/최대 높이 설정: 1줄~4줄 */
		min-height: 2.5rem; /* 약 1줄 */
		max-height: 10rem; /* 약 4줄 */
		overflow-y: auto; /* 4줄 초과 시 스크롤 */
	}

	.composer-input:disabled {
		@apply bg-gray-100;
	}

	.composer-button {
		@apply flex items-center justify-center;
		@apply rounded-full border-0 bg-transparent;
		@apply text-gray-700 transition-all duration-200;
		@apply p-2;
		@apply hover:bg-gray-100 active:bg-gray-200;
	}

	.composer-button:disabled {
		@apply cursor-not-allowed text-gray-300;
		@apply hover:bg-transparent;
	}

	.composer-error {
		@apply text-sm text-red-600;
		/* 축소 방지 */
		@apply shrink-0;
		/* 좌우 여백만 유지 */
		@apply mx-2 md:mx-4;
	}

	/* 메시지 첨부파일 스타일 */
	.message-attachments {
		@apply mt-2 space-y-2;
	}

	.attachment-item {
		@apply block overflow-hidden rounded-lg transition-opacity hover:opacity-90;
	}

	.attachment-image {
		@apply max-h-64 w-full rounded-lg object-cover;
	}

	.attachment-video {
		@apply max-h-64 w-full rounded-lg;
	}

	.attachment-file {
		@apply flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3;
		@apply transition-colors hover:bg-gray-100;
	}

	.attachment-file-icon {
		@apply flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-100;
	}

	.attachment-file-extension {
		@apply text-xl font-bold uppercase text-gray-600;
	}

	.attachment-file .file-icon {
		@apply h-10 w-10 shrink-0 text-gray-400;
		@apply aspect-auto bg-transparent;
	}

	.file-details {
		@apply flex-1 overflow-hidden;
	}

	.file-name {
		@apply truncate text-sm font-medium text-gray-900;
	}

	.download-icon {
		@apply h-5 w-5 shrink-0 text-blue-500;
	}

	/* 스크롤 컨트롤 버튼 스타일 */
	.scroll-controls {
		@apply absolute right-4 bottom-4 flex flex-col gap-2;
	}

	.scroll-button {
		@apply flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-0 bg-gray-900 text-lg font-bold text-white shadow-lg transition-all duration-200;
	}

	.scroll-button:hover {
		@apply scale-110 bg-gray-700;
	}

	.scroll-button:active {
		@apply scale-95 bg-gray-950;
	}

	/* v1.2.0: 드래그 앤 드롭 오버레이 스타일 */
	.drag-drop-overlay {
		@apply absolute inset-0 z-50;
		@apply flex items-center justify-center;
		@apply bg-blue-500/20 backdrop-blur-sm;
		@apply border-4 border-dashed border-blue-500;
		animation: pulse-border 1.5s ease-in-out infinite;
	}

	/* 드래그 앤 드롭 컨텐츠 */
	.drag-drop-content {
		@apply flex flex-col items-center gap-4;
		@apply rounded-2xl bg-white/95 p-8 shadow-2xl;
		@apply border-2 border-blue-400;
	}

	/* 파일 아이콘 */
	.drag-drop-icon {
		@apply h-24 w-24 text-blue-500;
		animation: bounce-gentle 1s ease-in-out infinite;
	}

	/* 안내 텍스트 */
	.drag-drop-title {
		@apply text-2xl font-bold text-gray-900;
	}

	.drag-drop-subtitle {
		@apply text-sm text-gray-600;
	}

	/* 펄스 애니메이션 (테두리) */
	@keyframes pulse-border {
		0%,
		100% {
			opacity: 1;
		}
		50% {
			opacity: 0.6;
		}
	}

	/* 부드러운 바운스 애니메이션 (아이콘) */
	@keyframes bounce-gentle {
		0%,
		100% {
			transform: translateY(0);
		}
		50% {
			transform: translateY(-10px);
		}
	}
</style>
