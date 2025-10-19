<?php

/**
 * 사용자 목록 페이지
 *
 * Vue.js를 사용하여 사용자 목록을 표시하고, infinite scroll로 추가 로드합니다.
 * 각 사용자 카드에 친구 맺기 요청 버튼이 포함되어 있습니다.
 * 친구 검색 기능은 독립적인 user-search 모듈로 분리되어 있습니다.
 */

// 초기 사용자 목록 조회 (첫 페이지, 20명)
$result = list_users(['per_page' => 20, 'page' => 1]);
$users = $result['users'] ?? [];
$total = $result['total'] ?? 0;

// 내 친구 목록은 위젯 내부에서 조회합니다.

// Vue.js hydration을 위한 데이터 준비 (친구 목록 제외)
$hydrationData = [
    'users' => $users,
    'total' => $total,
    'currentPage' => 1,
    'perPage' => 20
];

// InfiniteScroll 라이브러리 로드
load_deferred_js('infinite-scroll');
load_deferred_js('user-search');
?>

<!-- Vue.js 앱 컨테이너 -->
<div id="user-list-app" class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><?= t()->사용자_목록 ?></h1>

            <!-- Friend Search Button -->
            <button onclick="window.openFriendSearchModal && window.openFriendSearchModal()" class="btn btn-primary mb-3">
                <i class="bi bi-search me-2"></i>
                <?= t()->친구_검색 ?>
            </button>

            <!-- My Friends Section (친구 한 줄 표시 위젯) -->
            <?php
            $limit = 5;
            include __DIR__ . '/../../widgets/user/friend/friend-one-line-display.php';
            ?>

            <!-- 사용자 목록 그리드 -->
            <div class="row g-3">
                <!-- 사용자 없음 메시지 -->
                <div v-if="users.length === 0" class="col-12">
                    <div class="alert alert-info">
                        <?= t()->등록된_사용자가_없습니다 ?>
                    </div>
                </div>

                <!-- 사용자 카드 -->
                <div v-for="user in users" :key="user.id" class="col-6">
                    <div class="card h-100">
                        <div class="card-body p-2 d-flex align-items-center">
                            <!-- 프로필 사진 (클릭하면 프로필 페이지로 이동) -->
                            <a :href="`<?= href()->user->profile ?>?id=${user.id}`" class="flex-shrink-0 me-2 text-decoration-none">
                                <img v-if="user.photo_url"
                                    :src="user.photo_url"
                                    class="rounded-circle"
                                    style="width: 50px; height: 50px; object-fit: cover;"
                                    :alt="user.display_name">
                                <div v-else
                                    class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-person fs-5 text-secondary"></i>
                                </div>
                            </a>

                            <!-- 사용자 정보 (클릭하면 프로필 페이지로 이동) -->
                            <a :href="`<?= href()->user->profile ?>?id=${user.id}`" class="flex-grow-1 min-w-0 text-decoration-none">
                                <!-- 사용자 이름 -->
                                <h6 class="card-title mb-0 text-truncate text-dark">{{ user.display_name }}</h6>

                                <!-- 가입일 -->
                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem;">
                                    {{ formatDate(user.created_at) }}
                                </p>
                            </a>

                            <!-- 친구 맺기 버튼 (본인이 아닌 경우에만 표시) -->
                            <div v-if="myUserId && user.id !== myUserId" class="flex-shrink-0 ms-2">
                                <button @click="requestFriend(user)"
                                    class="btn btn-sm"
                                    :class="user.is_friend ? 'btn-success' : 'btn-primary'"
                                    :disabled="user.requesting || user.is_friend">
                                    <span v-if="user.requesting">
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        <?= t()->요청_중 ?>
                                    </span>
                                    <span v-else-if="user.is_friend">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <?= t()->친구 ?>
                                    </span>
                                    <span v-else>
                                        <i class="bi bi-person-plus me-1"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 로딩 인디케이터 -->
            <div v-if="loading" class="text-center mt-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden"><?= t()->로딩_중 ?></span>
                </div>
            </div>

            <!-- 모든 사용자 로드 완료 메시지 -->
            <div v-if="!loading && hasMore === false && users.length > 0" class="text-center mt-4 text-muted">
                <?= t()->모든_사용자를_불러왔습니다 ?>
            </div>
        </div>
    </div>
</div>

<!-- User Search Module (독립적인 Vue.js 앱) -->
<div id="user-search">
    <!-- Friend Search Modal -->
    <div class="modal fade" id="friendSearchModal" tabindex="-1" aria-labelledby="friendSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="friendSearchModalLabel">{{ translations.친구_검색 }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="search-modal-body">
                    <!-- Search Input Group -->
                    <div class="input-group mb-3">
                        <input v-model="searchTerm"
                            @keyup.enter="performSearch"
                            type="text"
                            class="form-control"
                            :placeholder="translations.이름을_입력하세요"
                            :aria-label="translations.이름을_입력하세요">
                        <button @click="performSearch"
                            class="btn btn-primary"
                            :disabled="searchLoading">
                            <span v-if="searchLoading">{{ translations.검색_중 }}</span>
                            <span v-else>{{ translations.검색 }}</span>
                        </button>
                    </div>

                    <!-- Search Results Grid -->
                    <div v-if="searchResults.length > 0" class="row g-3">
                        <div v-for="user in searchResults" :key="'search-' + user.id" class="col-6">
                            <div class="card h-100">
                                <div class="card-body p-2 d-flex align-items-center">
                                    <!-- Profile Photo (clickable link) -->
                                    <a :href="`${profileUrl}?id=${user.id}`"
                                        class="flex-shrink-0 me-2 text-decoration-none">
                                        <img v-if="user.photo_url"
                                            :src="user.photo_url"
                                            class="rounded-circle"
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                            :alt="user.display_name">
                                        <div v-else
                                            class="rounded-circle bg-secondary bg-opacity-25 d-inline-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="bi bi-person fs-5 text-secondary"></i>
                                        </div>
                                    </a>

                                    <!-- User Info (clickable link) -->
                                    <a :href="`${profileUrl}?id=${user.id}`"
                                        class="flex-grow-1 min-w-0 text-decoration-none">
                                        <h6 class="card-title mb-0 text-truncate text-dark">
                                            {{ user.display_name }}
                                        </h6>
                                        <p class="card-text text-muted mb-0" style="font-size: 0.75rem;">
                                            {{ formatDate(user.created_at) }}
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Results Message -->
                    <div v-else-if="searchPerformed && searchResults.length === 0 && !searchLoading"
                        class="alert alert-info">
                        {{ translations.검색_결과가_없습니다 }}
                    </div>

                    <!-- Pagination Bar -->
                    <nav v-if="searchTotalPages > 1" class="mt-4" aria-label="Search results pagination">
                        <ul class="pagination justify-content-center">
                            <!-- First Page -->
                            <li class="page-item" :class="{disabled: searchPage === 1}">
                                <a class="page-link" href="#" @click.prevent="goToSearchPage(1)" aria-label="First page">
                                    <i class="bi bi-chevron-double-left"></i>
                                </a>
                            </li>

                            <!-- Previous Page -->
                            <li class="page-item" :class="{disabled: searchPage === 1}">
                                <a class="page-link" href="#" @click.prevent="goToSearchPage(searchPage - 1)" aria-label="Previous page">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <!-- Page Numbers (dynamic range) -->
                            <li v-for="pageNum in visiblePageNumbers"
                                :key="pageNum"
                                class="page-item"
                                :class="{active: pageNum === searchPage}">
                                <a class="page-link" href="#" @click.prevent="goToSearchPage(pageNum)">
                                    {{ pageNum }}
                                </a>
                            </li>

                            <!-- Next Page -->
                            <li class="page-item" :class="{disabled: searchPage === searchTotalPages}">
                                <a class="page-link" href="#" @click.prevent="goToSearchPage(searchPage + 1)" aria-label="Next page">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>

                            <!-- Last Page -->
                            <li class="page-item" :class="{disabled: searchPage === searchTotalPages}">
                                <a class="page-link" href="#" @click.prevent="goToSearchPage(searchTotalPages)" aria-label="Last page">
                                    <i class="bi bi-chevron-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
/**
 * 다국어 번역 주입 함수
 *
 * 이 페이지에서 사용하는 모든 번역 텍스트를 주입합니다.
 */
function inject_list_language()
{
    t()->inject([
        '사용자_목록' => [
            'ko' => '사용자 목록',
            'en' => 'User List',
            'ja' => 'ユーザーリスト',
            'zh' => '用户列表'
        ],
        '친구_검색' => [
            'ko' => '친구 검색',
            'en' => 'Search Friends',
            'ja' => '友達検索',
            'zh' => '搜索朋友'
        ],
        '이름을_입력하세요' => [
            'ko' => '이름을 입력하세요',
            'en' => 'Enter name',
            'ja' => '名前を入力してください',
            'zh' => '请输入姓名'
        ],
        '검색' => [
            'ko' => '검색',
            'en' => 'Search',
            'ja' => '検索',
            'zh' => '搜索'
        ],
        '검색_중' => [
            'ko' => '검색 중...',
            'en' => 'Searching...',
            'ja' => '検索中...',
            'zh' => '搜索中...'
        ],
        '검색어를_입력해주세요' => [
            'ko' => '검색어를 입력해주세요',
            'en' => 'Please enter a search term',
            'ja' => '検索語を入力してください',
            'zh' => '请输入搜索词'
        ],
        '검색에_실패했습니다' => [
            'ko' => '검색에 실패했습니다',
            'en' => 'Search failed',
            'ja' => '検索に失敗しました',
            'zh' => '搜索失败'
        ],
        '검색_결과가_없습니다' => [
            'ko' => '검색 결과가 없습니다.',
            'en' => 'No results found.',
            'ja' => '検索結果がありません。',
            'zh' => '未找到结果。'
        ],
        '내_친구_목록' => [
            'ko' => '내 친구 목록',
            'en' => 'My Friends',
            'ja' => 'マイフレンド',
            'zh' => '我的朋友'
        ],
        '등록된_사용자가_없습니다' => [
            'ko' => '등록된 사용자가 없습니다.',
            'en' => 'No users registered.',
            'ja' => '登録されたユーザーがありません。',
            'zh' => '没有注册用户。'
        ],
        '요청_중' => [
            'ko' => '요청 중...',
            'en' => 'Requesting...',
            'ja' => 'リクエスト中...',
            'zh' => '请求中...'
        ],
        '친구' => [
            'ko' => '친구',
            'en' => 'Friend',
            'ja' => '友達',
            'zh' => '朋友'
        ],
        '친구_추가' => [
            'ko' => '친구 추가',
            'en' => 'Add Friend',
            'ja' => '友達追加',
            'zh' => '添加朋友'
        ],
        '로딩_중' => [
            'ko' => '로딩 중...',
            'en' => 'Loading...',
            'ja' => '読み込み中...',
            'zh' => '加载中...'
        ],
        '모든_사용자를_불러왔습니다' => [
            'ko' => '모든 사용자를 불러왔습니다.',
            'en' => 'All users loaded.',
            'ja' => 'すべてのユーザーを読み込みました。',
            'zh' => '已加载所有用户。'
        ],
        '로그인이_필요합니다' => [
            'ko' => '로그인이 필요합니다.',
            'en' => 'Login required.',
            'ja' => 'ログインが必要です。',
            'zh' => '需要登录。'
        ],
        '자기_자신에게는_친구_요청을_보낼_수_없습니다' => [
            'ko' => '자기 자신에게는 친구 요청을 보낼 수 없습니다.',
            'en' => 'You cannot send a friend request to yourself.',
            'ja' => '自分自身にはフレンドリクエストを送信できません。',
            'zh' => '您不能向自己发送好友请求。'
        ],
        '이미_친구입니다' => [
            'ko' => '이미 친구입니다.',
            'en' => 'Already friends.',
            'ja' => 'すでに友達です。',
            'zh' => '已经是朋友了。'
        ],
        '님에게_친구_맺기_신청을_하시겠습니까' => [
            'ko' => '님에게 친구 맺기 신청을 하시겠습니까?',
            'en' => ', do you want to send a friend request?',
            'ja' => 'さんにフレンドリクエストを送信しますか？',
            'zh' => '，您想发送好友请求吗？'
        ],
        '님에게_친구_요청을_보냈습니다' => [
            'ko' => '님에게 친구 요청을 보냈습니다.',
            'en' => ', friend request sent.',
            'ja' => 'さんにフレンドリクエストを送信しました。',
            'zh' => '，已发送好友请求。'
        ],
        '친구_요청에_실패했습니다' => [
            'ko' => '친구 요청에 실패했습니다.',
            'en' => 'Friend request failed.',
            'ja' => 'フレンドリクエストに失敗しました。',
            'zh' => '好友请求失败。'
        ],
        '친구_요청_실패' => [
            'ko' => '친구 요청 실패',
            'en' => 'Friend request failed',
            'ja' => 'フレンドリクエスト失敗',
            'zh' => '好友请求失败'
        ],
    ]);
}

inject_list_language();
