<?php

// 다국어 텍스트 주입
inject_user_profile_language();

load_page_css();

// 사용자 ID 가져오기: URL 파라미터 'id' 또는 로그인한 사용자의 ID
$user_id = http_param('id') ?? login()->id ?? 0;


// 사용자 정보가 없으면 에러 표시
if (!$user_id) {
    echo '<div class="container mt-5"><div class="alert alert-danger">' . tr(['en' => 'User ID is required.', 'ko' => '사용자 ID가 필요합니다.']) . '</div></div>';
    return;
}

// 사용자 정보 로드
$user_data = get_user(['id' => $user_id]);

// 사용자를 찾을 수 없는 경우
if (isset($user_data['error_code'])) {
    return display_error(tr(['en' => 'User not found.', 'ko' => '사용자를 찾을 수 없습니다.']));
}

// UserModel 객체 생성
$user = new UserModel($user_data);

// 생년월일 포맷팅
$birthday_formatted = '';
if ($user->birthday > 0) {
    $birthday_formatted = date('Y년 m월 d일', $user->birthday);
}

// 성별 표시
$gender_text = '';
if ($user->gender === 'M') {
    $gender_text = tr(['en' => 'Male', 'ko' => '남성']);
} elseif ($user->gender === 'F') {
    $gender_text = tr(['en' => 'Female', 'ko' => '여성']);
}

// 가입일 포맷팅
$created_at_formatted = '';
if (!empty($user->created_at)) {
    $created_at_formatted = date('Y년 m월 d일', strtotime($user->created_at));
}

// 로그인한 사용자 본인인지 확인
$is_me = login() && login()->id === $user->id;

// 게시물 목록 초기 로드 (첫 페이지만 5개)
$per_page = 5;
$page = 1;

$result = get_user_posts([
    'user_id' => $user->id,
    'limit' => $per_page,
    'page' => $page
]);

$initial_posts = $result->posts ?? [];

// Create postList structure (similar to index.my-page.php)
$postList = [
    'posts' => $initial_posts,
    'page' => $page,
    'isEmpty' => empty($initial_posts),
    'isLastPage' => count($initial_posts) < $per_page
];

// Vue.js 컴포넌트 로드
load_deferred_js('vue-components/post.component');
load_deferred_js('infinite-scroll');
?>

<style>
    /* 프로필 커버 영역 */
    .profile-cover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 200px;
        border-radius: 8px;
        position: relative;
        margin-bottom: 80px;
    }

    /* 프로필 사진 래퍼 */
    .profile-photo-wrapper {
        position: absolute;
        bottom: -60px;
        left: 50%;
        transform: translateX(-50%);
    }

    /* 프로필 사진 */
    .profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* 프로필 사진 플레이스홀더 */
    .profile-photo-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: white;
        border: 4px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-photo-placeholder i {
        font-size: 60px;
        color: #e4e6eb;
    }

    /* 프로필 정보 섹션 */
    .profile-info-section {
        background-color: white;
        border-radius: 8px;
        margin-top: 1rem;
    }

    /* 프로필 이름 */
    .profile-name {
        font-size: 2rem;
        font-weight: 700;
        color: #050505;
        margin: 0;
    }

    /* 프로필 메타 정보 */
    .profile-meta {
        font-size: 0.95rem;
    }

    /* 상세 정보 항목 */
    .detail-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* 상세 정보 아이콘 */
    .detail-icon {
        font-size: 20px;
        color: var(--bs-primary);
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* 상세 정보 내용 */
    .detail-content {
        flex: 1;
    }

    /* 상세 정보 라벨 */
    .detail-label {
        font-size: 0.875rem;
        color: #65676b;
        margin-bottom: 4px;
    }

    /* 상세 정보 값 */
    .detail-value {
        font-size: 1rem;
        color: #050505;
        font-weight: 500;
    }

    /* 반응형 디자인 */
    @media (max-width: 768px) {
        .profile-cover {
            height: 150px;
            margin-bottom: 70px;
        }

        .profile-photo-wrapper {
            bottom: -50px;
        }

        .profile-photo,
        .profile-photo-placeholder {
            width: 100px;
            height: 100px;
        }

        .profile-photo-placeholder i {
            font-size: 50px;
        }

        .profile-info-section {
            padding: 1.5rem 1rem;
        }

        .profile-name {
            font-size: 1.5rem;
        }
    }
</style>

<div id="profile-component" class="container py-4">
    <!-- 커버 영역 -->
    <div class="profile-cover">
        <!-- 프로필 사진 -->
        <div class="profile-photo-wrapper">
            <?php if (!empty($user->photo_url)): ?>
                <img src="<?= $user->photo_url ?>" alt="<?= $user->displayFullName() ?>" class="profile-photo">
            <?php else: ?>
                <div class="profile-photo-placeholder">
                    <i class="fa-solid fa-user"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 프로필 정보 영역 -->
    <div class="profile-info-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <!-- 이름 및 기본 정보 -->
            <div>
                <h1 class="profile-name mb-1"><?= $user->displayFullName() ?></h1>
                <p class="profile-meta text-muted mb-0">ID: <?= $user->id ?></p>
            </div>

            <!-- 액션 버튼 -->
            <div>
                <?php if ($is_me): ?>
                    <!-- 본인인 경우: 프로필 수정 버튼 -->
                    <a href="<?= href()->user->profile_edit ?>" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square me-2"></i><?= t()->프로필_수정 ?>
                    </a>
                <?php else: ?>
                    <!-- 다른 사용자인 경우: 친구 추가 버튼 (Vue.js) -->
                    <button @click="requestFriend(<?= $user->id ?>)"
                        class="btn btn-primary"
                        :disabled="requesting || isFriend"
                        v-cloak>
                        <span v-if="requesting">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <?= t()->요청_중 ?>
                        </span>
                        <span v-else-if="isFriend">
                            <i class="fa-solid fa-check me-2"></i><?= t()->친구_요청_전송_완료 ?>
                        </span>
                        <span v-else>
                            <i class="fa-solid fa-user-plus me-2"></i><?= t()->친구_추가 ?>
                        </span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <ul class="nav nav-tabs mt-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active"
                    id="posts-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#posts-tab-pane"
                    type="button"
                    role="tab"
                    aria-controls="posts-tab-pane"
                    aria-selected="true">
                    <i class="fa-solid fa-file-lines me-2"></i><?= t()->게시물 ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link"
                    id="about-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#about-tab-pane"
                    type="button"
                    role="tab"
                    aria-controls="about-tab-pane"
                    aria-selected="false">
                    <i class="fa-solid fa-circle-info me-2"></i><?= t()->소개 ?>
                </button>
            </li>
        </ul>

        <!-- 탭 콘텐츠 -->
        <div class="tab-content mt-3" id="profileTabsContent">
            <!-- 게시물 탭 -->
            <div class="tab-pane fade show active"
                id="posts-tab-pane"
                role="tabpanel"
                aria-labelledby="posts-tab"
                tabindex="0">
                <!-- 게시물 목록 (Vue.js) -->
                <div v-if="postList.isEmpty" class="alert alert-info text-center">
                    <i class="fa-solid fa-inbox me-2"></i><?= t()->게시물_없음 ?>
                </div>

                <div v-else class="row g-3">
                    <article v-for="post in postList.posts" :key="post.id" class="col-12">
                        <post-component
                            :post="post"
                            @post-deleted="handlePostDeleted">
                        </post-component>
                    </article>
                </div>
            </div>

            <!-- 소개 탭 -->
            <div class="tab-pane fade"
                id="about-tab-pane"
                role="tabpanel"
                aria-labelledby="about-tab"
                tabindex="0">
                <div class="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong><?= t()->성별 ?>:</strong> <?= htmlspecialchars($gender_text ?: t()->정보_없음) ?>
                        </div>
                        <div class="col-md-6">
                            <strong><?= t()->생년월일 ?>:</strong> <?= htmlspecialchars($birthday_formatted ?: t()->정보_없음) ?>
                        </div>
                        <div class="col-md-6">
                            <strong><?= t()->가입일 ?>:</strong> <?= htmlspecialchars($created_at_formatted) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    ready(() => {
        // InfiniteScroll 초기화 (body에 적용)
        const scrollController = InfiniteScroll.init('body', {
            onScrolledToBottom: () => {
                console.log('하단 도달: 더 많은 데이터 로드');
                if (window.profileApp) {
                    window.profileApp.loadNextPage();
                }
            },
            threshold: 400,
            debounceDelay: 100,
            initialScrollToBottom: false
        });
    });
</script>

<script>
    ready(() => {
        // Vue.js 프로필 앱 생성
        const app = Vue.createApp({
            components: {
                'post-component': postComponent
            },
            data() {
                console.log('window.Store in profile:', window.Store);

                // 서버에서 hydrate된 게시물 목록을 Store에 저장
                if (!window.Store.state.userPostList) {
                    window.Store.state.userPostList = {};
                }
                Object.assign(window.Store.state.userPostList, <?= json_encode($postList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);

                return {
                    // 친구 요청 상태
                    requesting: false,
                    isFriend: false,
                    state: window.Store?.state || {},
                    // 서버에서 hydrate된 게시물 목록
                    postList: window.Store.state.userPostList,
                };
            },
            mounted() {
                console.log('[profile] Vue.js 프로필 페이지 초기화 완료');
                console.log('Initial posts loaded:', this.postList.posts.length);
            },
            methods: {
                async loadNextPage() {
                    if (this.postList.isLastPage) {
                        console.log('마지막 페이지에 도달했습니다.');
                        return;
                    }

                    const nextPage = this.postList.page + 1;
                    try {
                        console.log(`Loading next page: ${nextPage}`);

                        const result = await func('get_user_posts', {
                            user_id: <?= $user->id ?>,
                            limit: <?= $per_page ?>,
                            page: nextPage,
                            alertOnError: true
                        });

                        console.log('다음 페이지 데이터:', result);

                        if (result.posts && result.posts.length > 0) {
                            this.postList.posts.push(...result.posts);
                            this.postList.page = nextPage;
                            this.postList.isLastPage = result.posts.length < <?= $per_page ?>;
                            console.log(`${nextPage}번 째 페이지 로드 완료, 총 게시물 수:`, this.postList.posts.length);
                        } else {
                            this.postList.isLastPage = true;
                            console.log('더 이상 로드할 게시물이 없습니다.');
                        }
                    } catch (error) {
                        console.error('게시물 로드 중 오류 발생:', error);
                    }
                },

                /**
                 * 게시물 삭제 이벤트 핸들러
                 * @param {number} postId - 삭제된 게시물 ID
                 */
                handlePostDeleted(postId) {
                    console.log('Post deleted event received:', postId);
                    const index = this.postList.posts.findIndex(p => p.id === postId);
                    if (index !== -1) {
                        this.postList.posts.splice(index, 1);
                        console.log('Post removed from list. Remaining posts:', this.postList.posts.length);
                    }
                },
                /**
                 * 친구 추가 요청
                 * @param {number} otherUserId - 친구 요청을 보낼 사용자 ID
                 */
                async requestFriend(otherUserId) {
                    console.log(window.Store);
                    // 로그인 확인 - window.Store.user에서 로그인한 사용자 정보 가져오기
                    if (!this.state?.user?.id) {
                        alert('<?= t()->로그인이_필요합니다 ?>');
                        window.location.href = '<?= href()->user->login ?>';
                        return;
                    }

                    const myUserId = this.state.user.id;

                    // 자기 자신에게 친구 요청 방지
                    if (otherUserId === myUserId) {
                        alert('<?= t()->자기_자신에게는_친구_요청을_보낼_수_없습니다 ?>');
                        return;
                    }

                    // 이미 친구인 경우
                    if (this.isFriend) {
                        alert('<?= t()->이미_친구입니다 ?>');
                        return;
                    }

                    try {
                        // 요청 중 상태 설정
                        this.requesting = true;

                        console.log(`친구 요청 전송: 사용자 ID ${otherUserId}`);

                        // API 호출: request_friend 함수 사용
                        await func('request_friend', {
                            me: myUserId,
                            other: otherUserId,
                        });

                        // 성공: 친구 상태 업데이트
                        this.isFriend = true;
                        this.requesting = false;

                        console.log(`친구 요청 성공: 사용자 ID ${otherUserId}`);
                        alert('<?= t()->친구_요청_전송_완료 ?>');

                    } catch (error) {
                        console.error('친구 요청 실패:', error);
                        this.requesting = false;

                        // 에러 메시지 표시
                        const errorMessage = error.message || error.error_message || '<?= t()->친구_요청에_실패했습니다 ?>';
                        alert(`<?= t()->친구_요청_실패 ?>: ${errorMessage}`);
                    }
                }
            }
        });

        const vm = app.mount('#profile-component');
        console.log('Vue 인스턴스 마운트됨:', vm);

        // Vue 인스턴스를 전역 변수로 노출 (무한 스크롤에서 사용)
        window.profileApp = vm;
    });
</script>

<?php
/**
 * 사용자 프로필 페이지
 *
 * 다른 사용자의 프로필을 조회하는 페이지입니다.
 * URL 파라미터로 사용자 ID를 받아서 해당 사용자의 정보를 표시합니다.
 */

/**
 * 다국어 텍스트 주입 함수
 */
function inject_user_profile_language()
{
    t()->inject([
        '사용자 프로필' => ['ko' => '사용자 프로필', 'en' => 'User Profile', 'ja' => 'ユーザープロフィール', 'zh' => '用户资料'],
        '사용자 ID가 필요합니다.' => ['ko' => '사용자 ID가 필요합니다.', 'en' => 'User ID is required.', 'ja' => 'ユーザーIDが必要です。', 'zh' => '需要用户ID。'],
        '사용자를 찾을 수 없습니다.' => ['ko' => '사용자를 찾을 수 없습니다.', 'en' => 'User not found.', 'ja' => 'ユーザーが見つかりません。', 'zh' => '找不到用户。'],
        '성별' => ['ko' => '성별', 'en' => 'Gender', 'ja' => '性別', 'zh' => '性别'],
        '남성' => ['ko' => '남성', 'en' => 'Male', 'ja' => '男性', 'zh' => '男性'],
        '여성' => ['ko' => '여성', 'en' => 'Female', 'ja' => '女性', 'zh' => '女性'],
        '생년월일' => ['ko' => '생년월일', 'en' => 'Birthday', 'ja' => '生年月日', 'zh' => '出生日期'],
        '가입일' => ['ko' => '가입일', 'en' => 'Joined', 'ja' => '登録日', 'zh' => '注册日期'],
        '프로필_수정' => ['ko' => '프로필 수정', 'en' => 'Edit Profile', 'ja' => 'プロフィール編集', 'zh' => '编辑资料'],
        '정보 없음' => ['ko' => '정보 없음', 'en' => 'Not provided', 'ja' => '情報なし', 'zh' => '无信息'],
        '친구_추가' => ['ko' => '친구 추가', 'en' => 'Add Friend', 'ja' => '友達追加', 'zh' => '添加好友'],
        '요청_중' => ['ko' => '요청 중...', 'en' => 'Requesting...', 'ja' => 'リクエスト中...', 'zh' => '请求中...'],
        '친구_요청_전송_완료' => ['ko' => '친구 요청을 보냈습니다.', 'en' => 'Friend request sent.', 'ja' => '友達リクエストを送信しました。', 'zh' => '已发送好友请求。'],
        '오류_발생' => ['ko' => '오류가 발생했습니다.', 'en' => 'An error occurred.', 'ja' => 'エラーが発生しました。', 'zh' => '发生错误。'],
        '로그인이_필요합니다' => ['ko' => '로그인이 필요합니다.', 'en' => 'Login required.', 'ja' => 'ログインが必要です。', 'zh' => '需要登录。'],
        '자기_자신에게는_친구_요청을_보낼_수_없습니다' => ['ko' => '자기 자신에게는 친구 요청을 보낼 수 없습니다.', 'en' => 'You cannot send a friend request to yourself.', 'ja' => '自分自身にはフレンドリクエストを送信できません。', 'zh' => '您不能向自己发送好友请求。'],
        '이미_친구입니다' => ['ko' => '이미 친구입니다.', 'en' => 'Already friends.', 'ja' => 'すでに友達です。', 'zh' => '已经是朋友了。'],
        '친구_요청에_실패했습니다' => ['ko' => '친구 요청에 실패했습니다.', 'en' => 'Friend request failed.', 'ja' => 'フレンドリクエストに失敗しました。', 'zh' => '好友请求失败。'],
        '친구_요청_실패' => ['ko' => '친구 요청 실패', 'en' => 'Friend request failed', 'ja' => 'フレンドリクエスト失敗', 'zh' => '好友请求失败'],
        '게시물' => ['ko' => '게시물', 'en' => 'Posts', 'ja' => '投稿', 'zh' => '帖子'],
        '소개' => ['ko' => '소개', 'en' => 'About', 'ja' => '概要', 'zh' => '简介'],
        '게시물_없음' => ['ko' => '게시물이 없습니다.', 'en' => 'No posts yet.', 'ja' => '投稿がありません。', 'zh' => '没有帖子。'],
        '사용자_정보' => ['ko' => '사용자 정보', 'en' => 'User Information', 'ja' => 'ユーザー情報', 'zh' => '用户信息'],
    ]);
}
?>