
/**
 * Post Component
 * - Display a single post with its content, author, metadata, actions and comments.
 * - Supports editing and deleting the post.
 */

const postComponent = {
    components: {
        'file-upload-component': window.FileUploadComponent
    },
    props: {
        post: {
            type: Object,
            required: true,
        },
    },
    template: /*html*/ `
    <article ref="postContainer" class="card mb-4">
    <!-- 게시물 헤더 (사용자 정보) -->
    <header class="card-header bg-white d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #e4e6eb;">
        <div class="d-flex align-items-center gap-2">
            <!-- 프로필 사진 (Bootstrap 유틸리티 클래스 사용) -->
            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                 style="width: 40px; height: 40px; background-color: #e4e6eb;">
                <img v-if="getAuthorPhoto(post)"
                        :src="getAuthorPhoto(post)"
                        :alt="getAuthorName(post)"
                        class="rounded-circle"
                        style="width: 100%; height: 100%; object-fit: cover;">
                <i v-else class="fa-solid fa-user text-secondary" style="font-size: 20px; color: #65676b;"></i>
            </div>

            <!-- 사용자 이름, 날짜, 공개범위 -->
            <div class="flex-grow-1">
                <div class="fw-semibold" style="font-size: 15px; color: #050505; line-height: 1.3;">
                <a :href="'/user/profile?id=' + post.user_id" style="color: inherit; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    {{ getAuthorName(post) }}
                </a>
                </div>
                <div class="small" style="font-size: 13px; color: #65676b; line-height: 1.3;">
                    {{ formatDate(post.created_at) }} ·
                    <span class="badge bg-secondary me-1">{{ post.visibility || 'public' }}</span>
                    <span
                      v-if="post.visibility !== 'private' && post.visibility !== 'friends'"
                      class="badge bg-secondary me-1"
                >
                      {{ post.category }}
                    </span>                
                    <span v-if="edit.enabled" class="badge bg-warning bg-opacity-75 text-dark">
                        <i class="fa-solid fa-pen-to-square me-1"></i>Editing
                    </span>
                </div>
            </div>
        </div>

        <!-- 게시물 수정/삭제 버튼 (본인 게시물인 경우에만 표시) -->
        <div v-if="isMyPost" class="d-flex align-items-center gap-1">
            <button @click="handleEditPost()"
                    class="btn btn-link text-secondary p-2 rounded"
                    style="transition: background-color 0.2s ease;"
                    @mouseover="$event.currentTarget.style.backgroundColor='#e9ecef'"
                    @mouseout="$event.currentTarget.style.backgroundColor='transparent'"
                    aria-label="Edit post">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button @click="handleDeletePost()"
                    class="btn btn-link text-secondary p-2 rounded"
                    style="transition: background-color 0.2s ease;"
                    @mouseover="$event.currentTarget.style.backgroundColor='#e9ecef'"
                    @mouseout="$event.currentTarget.style.backgroundColor='transparent'"
                    aria-label="Delete post">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </header>

    <!-- 게시물 본문 (Bootstrap 패딩 사용) -->
    <div class="card-body" :class="{ 'border border-warning border-1 rounded-3': edit.enabled }" style="transition: all 0.1s ease;">
        <!-- Edit Mode -->
        <div v-if="edit.enabled" >
            <!-- Edit Content Textarea -->
            <textarea
                v-model="edit.content"
                class="w-100 border-0 mb-3"
                style="outline: none; font-size: 15px; color: #050505; line-height: 1.5; resize: none; min-height: 80px; max-height: 200px; overflow-y: auto; padding: 0; background: transparent;"
                placeholder="What's on your mind?"
                rows="4"></textarea>

            <!-- Preview existing images (editable in edit mode) -->
            <div v-if="hasPhotos(edit.files)" class="mb-3">
                <label class="form-label small text-muted">Attached Images</label>
                <div class="row g-2">
                    <div v-for="(fileUrl, index) in getValidPhotos(edit.files)" :key="index"
                            :class="getPhotoColumnClass(getValidPhotos(edit.files).length)">
                        <div class="position-relative">
                            <img :src="thumbnail(fileUrl, 400, 400, 'cover', 85, 'ffffff')"
                                    :alt="'Photo ' + (index + 1)"
                                    style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">

                            <!-- Delete button (X) on top right -->
                            <button
                                @click="removeImageFromEdit(index)"
                                type="button"
                                class="btn btn-sm btn-danger position-absolute image-delete-btn"
                                style="top: 8px; right: 8px; width: 28px; height: 28px; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; opacity: 0.9; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"
                                title="Remove image">
                                <i class="fa-solid fa-xmark" style="font-size: 16px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
            <!-- File Upload Component (for adding new images) -->
                <div> 
                    <label class="form-label small text-muted">Add More Images</label>
                    <file-upload-component
                        :single="false"
                        :show-uploaded-files="false"
                        :show-upload-button="true"
                        :upload-button-type="'camera-icon'"
                        accept="image/*,video/*"
                        @uploaded="handleFileUploaded">
                    </file-upload-component>
                </div>


                <div class="d-flex gap-2 flex-column justify-content-end">

                    <!-- Visibility Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <i class="fa-solid fa-earth-americas" v-if="edit.visibility === 'public'" style="font-size: 12px;"></i>
                            <i class="fa-solid fa-user-group" v-if="edit.visibility === 'friends'" style="font-size: 12px;"></i>
                            <i class="fa-solid fa-lock" v-if="edit.visibility === 'private'" style="font-size: 12px;"></i>
                            <span class="ms-1">{{ getVisibilityLabel(edit.visibility) }}</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" @click.prevent="edit.visibility = 'public'">
                                    <i class="fa-solid fa-earth-americas me-2"></i>
                                    Public
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" @click.prevent="edit.visibility = 'friends'">
                                    <i class="fa-solid fa-user-group me-2"></i>
                                    Friends
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" @click.prevent="edit.visibility = 'private'">
                                    <i class="fa-solid fa-lock me-2"></i>
                                    Only Me
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Category Dropdown (only show when visibility is public) -->
                    <div v-if="edit.visibility === 'public'" class="dropdown dropup">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="min-width: 150px;">
                            <i class="fa-solid fa-folder me-1" style="font-size: 12px;"></i>
                            {{ getCategoryLabel(edit.category) }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="max-width: 300px; max-height: 400px; overflow-y: auto;">
                            <template v-for="root in categories" :key="root.display_name">
                                <li><h6 class="dropdown-header">{{ root.display_name }}</h6></li>
                                <li v-for="sub in root.categories" :key="sub.category">
                                    <a class="dropdown-item" href="#" @click.prevent="edit.category = sub.category">
                                        {{ sub.name }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Edit Action Buttons -->
            <div class="d-flex gap-2 justify-content-end">
                <button @click="cancelEdit()" class="btn btn-sm btn-link text-muted text-decoration-none">
                    <i class="fa-solid fa-xmark me-1"></i>
                    Cancel
                </button>
                <button @click="saveEdit()" class="btn btn-sm btn-link text-decoration-none">
                    <i class="fa-solid fa-check me-1"></i>
                    Save
                </button>
            </div>
        </div>

        <!-- View Mode -->
        <div v-else>
            <!-- 제목 (Bootstrap 타이포그래피) -->
            <h2 v-if="post.title" class="fw-semibold mb-2" style="font-size: 18px; color: #050505; line-height: 1.4;">
                {{ post.title }}
            </h2>

            <!-- 내용 (Bootstrap 타이포그래피) -->
            <p v-if="post.content" class="mb-2" style="font-size: 15px; color: #050505; line-height: 1.5; white-space: pre-wrap; word-break: break-word;" v-html="formatContent(displayedContent)"></p>

            <!-- See more / See less 버튼 -->
            <div v-if="isContentTooLong" class="mb-3">
                <button @click="toggleContentExpansion" class="btn btn-link text-decoration-none text-muted p-0" style="font-size: 14px; font-weight: 600;">
                    <span v-if="!contentExpanded">
                        See more
                        <i class="fa-solid fa-chevron-down ms-1" style="font-size: 12px;"></i>
                    </span>
                    <span v-else>
                        See less
                        <i class="fa-solid fa-chevron-up ms-1" style="font-size: 12px;"></i>
                    </span>
                </button>
            </div>

            <!-- 이미지 -->
            <figure v-if="hasPhotos(post.files)">
                <div class="row g-2">
                    <div v-for="(fileUrl, index) in getValidPhotos(post.files)" :key="index"
                            :class="getPhotoColumnClass(getValidPhotos(post.files).length)">
                        <img :src="thumbnail(fileUrl, 400, 400, 'cover', 85, 'ffffff')"
                                :alt="'Photo ' + (index + 1)"
                                class="rounded"
                                style="width: 100%; max-height: 512px; object-fit: cover; cursor: pointer;"
                                @click="openPhotoModal(fileUrl)">
                    </div>
                </div>
            </figure>
        </div>
    </div>




    <!-- 게시물 액션 버튼 (Bootstrap 버튼 그룹) -->
    <div v-if="!edit.enabled" class="card-footer bg-white d-flex gap-2 justify-content-around border-top py-2">
        <button class="card-link text-decoration-none text-secondary fw-bold small border-0 bg-transparent"
                @click="handleLike(post)">
            <i class="fa-regular fa-thumbs-up me-2"></i>
            <span>Like</span>
        </button>
        <button class="card-link text-decoration-none text-secondary fw-bold small border-0 bg-transparent"
                @click="showMoreComments">
            <i class="fa-regular fa-comment me-2"></i>
            <span>Comment{{ post.comment_count > 0 ? ' (' + post.comment_count + ')' : '' }}</span>
        </button>
        <button class="card-link text-decoration-none text-secondary fw-bold small border-0 bg-transparent"
                @click="handleShare(post)">
            <i class="fa-regular fa-share-from-square me-2"></i>
            <span>Share</span>
        </button>
    </div>

    <!-- 댓글 섹션 (Bootstrap 패딩) -->
    <div v-if="!edit.enabled" class="card-footer border-top">
        <!-- 가짜 댓글 입력 박스 (클릭 시 Modal 열림) -->



        <!-- 댓글 더보기 버튼 (댓글 목록 위) -->
        <div v-if="post.comment_count > post.comments.length"
             class="text-center my-3">
            <button @click="showMoreComments"
                    class="btn btn-link text-decoration-none text-muted p-2">
                <i class="fa-regular fa-comment-dots me-2"></i>
                <span class="small">{{ more_comment_count }}개의 댓글이 더 있습니다. <b>더보기</b></span>
            </button>
        </div>

        <!-- 댓글 목록 -->
        <section v-if="post.comments && post.comments.length > 0">
            <div v-for="comment in post.comments" :key="comment.id" class="mb-2" :style="getCommentIndentStyle(comment.depth)">
               <div class="d-flex align-items-start gap-2">
                    <!-- 댓글 작성자 아바타 (Bootstrap 유틸리티) -->
                    <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                         style="width: 32px; height: 32px; background-color: #e4e6eb;">
                        <img v-if="comment.author && comment.author.photo_url"
                                :src="comment.author.photo_url"
                                :alt="comment.author.first_name || 'Anonymous'"
                                class="rounded-circle"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        <i v-else class="fa-solid fa-user text-secondary" style="font-size: 14px;"></i>
                    </div>

                    <!-- 댓글 내용 -->
                    <div class="flex-grow-1">
                        <!-- 첫 번째 줄: 이름 + 날짜 -->
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <strong class="fw-semibold" style="font-size: 13px; color: #050505;">
                                {{ (comment.author && comment.author.first_name) || 'Anonymous' }}
                            </strong>
                            <time class="text-muted" style="font-size: 12px;">
                                {{ formatDate(comment.created_at) }}
                            </time>
                        </div>

                        <!-- 두 번째 줄: 댓글 내용 (연한 회색 배경 박스) -->
                        <p class="mb-1" style="background-color: #f8f9fa; font-size: 14px; color: #050505; line-height: 1.3; word-break: break-word; white-space: pre-wrap;">
                            {{ comment.content }}
                        </p>

                        <!-- 댓글 첨부 이미지 -->
                        <div v-if="hasPhotos(comment.files)" class="mb-2">
                            <div class="row g-1">
                                <div v-for="(fileUrl, index) in getValidPhotos(comment.files)" :key="index"
                                        :class="getPhotoColumnClass(getValidPhotos(comment.files).length)">
                                    <img :src="thumbnail(fileUrl, 300, 300, 'cover', 85, 'ffffff')"
                                            :alt="'Photo ' + (index + 1)"
                                            class="rounded"
                                            style="width: 100%; height: 120px; object-fit: cover; cursor: pointer;"
                                            @click="openPhotoModal(fileUrl)">
                                </div>
                            </div>
                        </div>

                        <!-- 세 번째 줄: 액션 버튼 (좌측: Like/Reply, 우측: Edit/Delete) -->
                        <div class="d-flex align-items-center justify-content-between">
                            <!-- 좌측: Like, Reply -->
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-link text-decoration-none text-muted p-0 fw-semibold" style="font-size: 11px;">
                                    Like
                                </button>
                                <button @click="openReplyModal(comment.id)" class="btn btn-link text-decoration-none text-muted p-0 fw-semibold" style="font-size: 11px;">
                                    Reply {{ comment.comment_count > 0 ? '(' + comment.comment_count + ')' : '' }}
                                </button>
                            </div>

                            <!-- 우측: Edit, Delete (본인 댓글인 경우에만 표시) -->
                            <div v-if="isMyComment(comment)" class="d-flex align-items-center gap-2">
                                <button @click="openEditCommentModal(comment)" class="btn btn-link text-decoration-none text-secondary p-0" style="font-size: 13px;" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="handleDeleteComment(comment)" class="btn btn-link text-decoration-none text-secondary p-0" style="font-size: 13px;" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="d-flex align-items-center gap-2" @click="openCommentModal()" style="cursor: pointer;">
            <!-- 댓글 작성자 아바타 (Bootstrap 유틸리티) -->
            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                 style="width: 32px; height: 32px; background-color: #e4e6eb;">
                <img v-if="currentUserPhoto"
                        :src="currentUserPhoto"
                        alt="My avatar"
                        class="rounded-circle"
                        style="width: 100%; height: 100%; object-fit: cover;">
                <i v-else class="fa-solid fa-user text-secondary" style="font-size: 14px;"></i>
            </div>
            <!-- 가짜 댓글 입력 필드 (읽기 전용) -->
            <div class="flex-grow-1 d-flex align-items-center gap-2 bg-white rounded-pill px-3 py-2" style="border: 1px solid #ced4da;">
                <span class="text-muted" style="font-size: 14px;">Write a comment...</span>
                <i class="fa-solid fa-paper-plane text-muted ms-auto" style="font-size: 16px;"></i>
            </div>
        </div>

    </div>

    <!-- 댓글/답글 작성 통합 Bootstrap Modal -->
    <!-- commentMode에 따라 최상위 댓글 또는 답글 작성 -->
    <!-- commentMode: 'comment' (최상위 댓글) | 'reply' (답글) -->
    <div class="modal fade" :id="'commentModal-' + post.id" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="commentModalLabel">
                        <i :class="modalIcon + ' me-2 text-primary'"></i>
                        {{ modalTitle }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-3">
                    <!-- 댓글 입력 텍스트 영역 -->
                    <div class="mb-3">
                        <label for="commentContentTextarea" class="form-label small text-muted mb-2">
                            <i class="fa-solid fa-comment-dots me-1"></i>
                            Comment
                        </label>
                        <textarea
                            id="commentContentTextarea"
                            ref="commentTextarea"
                            v-model="commentContent"
                            class="form-control"
                            rows="4"
                            :placeholder="modalPlaceholder"
                            style="border: 1px solid #ced4da; border-radius: 8px; font-size: 14px;"></textarea>
                    </div>

                    <!-- 하단 액션 영역: 카메라 아이콘 + 버튼들 -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- 왼쪽: 파일 업로드 카메라 아이콘 + 진행률 -->
                        <div class="d-flex align-items-center gap-2">
                            <file-upload-component
                                :single="false"
                                :show-uploaded-files="false"
                                :show-upload-button="true"
                                :show-progress-bar="false"
                                upload-button-type="camera-icon"
                                accept="image/*"
                                @uploaded="handleCommentFileUpload"
                                @uploading-progress="handleUploadProgress">
                            </file-upload-component>

                            <!-- 업로드 진행률 표시 (Bootstrap Progress Bar) -->
                            <div v-if="isUploading" class="progress" style="width: 100px; height: 20px;">
                                <div class="progress-bar"
                                     role="progressbar"
                                     :style="{ width: uploadProgress + '%' }"
                                     :aria-valuenow="uploadProgress"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    <small>{{ uploadProgress }}%</small>
                                </div>
                            </div>
                        </div>

                        <!-- 오른쪽: Cancel, Submit 버튼 -->
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark me-1"></i>
                                Cancel
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm btn-primary"
                                :disabled="!commentContent || !commentContent.trim()"
                                @click="submitComment()">
                                <i class="fa-solid fa-paper-plane me-1"></i>
                                {{ submitButtonText }}
                            </button>
                        </div>
                    </div>

                    <!-- 업로드된 파일 미리보기 (빈 박스 없음) -->
                    <div v-if="commentFiles.length > 0" class="row g-2">
                        <div v-for="(fileUrl, index) in commentFiles" :key="index" class="col-4">
                            <div class="position-relative border rounded" style="padding-bottom: 100%; background-color: #f8f9fa;">
                                <img :src="thumbnail(fileUrl, 300, 300, 'cover', 85, 'ffffff')"
                                        :alt="'Image ' + (index + 1)"
                                        class="position-absolute top-0 start-0 rounded"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                <!-- 삭제 버튼 -->
                                <button
                                    @click="removeFile(index)"
                                    type="button"
                                    class="btn btn-sm btn-danger position-absolute"
                                    style="top: 4px; right: 4px; width: 24px; height: 24px; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 10;"
                                    title="Remove image">
                                    <i class="fa-solid fa-xmark" style="font-size: 12px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </article>
`,
    data() {
        // Safely get categories data
        const categoriesData = appConfig.categories;

        // Convert object to array if needed
        let categories = [];
        if (categoriesData) {
            if (Array.isArray(categoriesData)) {
                categories = categoriesData;
            } else if (typeof categoriesData === 'object') {
                // Convert object to array of values
                categories = Object.values(categoriesData);
            }
        }

        return {
            post: this.post,
            categories: categories,
            more_comment_count: this.post.comment_count - (this.post.comments ? this.post.comments.length : 0),
            edit: {
                enabled: false,
                content: '',
                visibility: 'public',
                category: 'story',
                files: [],
                newlyUploadedFiles: [], // Track newly uploaded files in this edit session
            },
            // 댓글/답글/수정 통합 모달 관련 데이터
            commentMode: 'comment', // 댓글 모드: 'comment' (최상위 댓글) | 'reply' (답글) | 'edit' (수정)
            replyingTo: null, // 답글 작성 시 부모 댓글 ID (commentMode === 'reply'일 때만 사용)
            editingCommentId: null, // 수정 중인 댓글 ID (commentMode === 'edit'일 때만 사용)
            commentContent: '', // 댓글/답글/수정 내용
            commentFiles: [], // 댓글/답글/수정 첨부 파일 URL 배열
            uploadProgress: 0, // 파일 업로드 진행률 (0-100)
            isUploading: false, // 파일 업로드 중 여부
            // 게시물 내용 확장/축소 상태
            contentExpanded: false, // 내용이 확장되었는지 여부
            contentMaxLength: 400, // 최대 표시 길이 (문자 수)
        };
    },
    computed: {
        /**
         * 현재 로그인한 사용자가 이 게시물의 작성자인지 확인
         * @returns {boolean} 본인 게시물이면 true
         */
        isMyPost() {
            // Validate Store and user exist
            if (!window.Store || !window.Store.state || !window.Store.state.user) {
                return false;
            }

            const currentUserId = window.Store.state.user.id;
            console.log(window.Store.state);
            const postAuthorId = this.post.user_id;


            return postAuthorId === currentUserId;
        },
        /**
         * 현재 로그인한 사용자의 프로필 사진
         * @returns {string|null} 프로필 사진 URL 또는 null
         */
        currentUserPhoto() {
            if (!window.Store || !window.Store.state || !window.Store.state.user) {
                return null;
            }
            return window.Store.state.user.photo_url || null;
        },

        /**
         * 모달 제목 (댓글 모드에 따라 동적 변경)
         * @returns {string} 모달 제목
         */
        modalTitle() {
            if (this.commentMode === 'edit') return 'Edit Comment';
            if (this.commentMode === 'reply') return 'Write a Reply';
            return 'Write a Comment';
        },

        /**
         * 모달 아이콘 CSS 클래스 (댓글 모드에 따라 동적 변경)
         * @returns {string} Font Awesome 아이콘 클래스
         */
        modalIcon() {
            if (this.commentMode === 'edit') return 'fa-solid fa-pen-to-square';
            if (this.commentMode === 'reply') return 'fa-solid fa-reply';
            return 'fa-solid fa-comment';
        },

        /**
         * 제출 버튼 텍스트 (댓글 모드에 따라 동적 변경)
         * @returns {string} 버튼 텍스트
         */
        submitButtonText() {
            if (this.commentMode === 'edit') return 'Save Changes';
            if (this.commentMode === 'reply') return 'Send Reply';
            return 'Send Comment';
        },

        /**
         * Textarea placeholder 텍스트 (댓글 모드에 따라 동적 변경)
         * @returns {string} Placeholder 텍스트
         */
        modalPlaceholder() {
            if (this.commentMode === 'edit') return 'Edit your comment...';
            if (this.commentMode === 'reply') return 'Write your reply here...';
            return 'Write your comment here...';
        },

        /**
         * 게시물 내용이 길어서 축소가 필요한지 확인
         * @returns {boolean} 내용이 최대 길이를 초과하면 true
         */
        isContentTooLong() {
            if (!this.post.content) return false;
            return this.post.content.length > this.contentMaxLength;
        },

        /**
         * 표시할 게시물 내용 (축소 또는 전체)
         * @returns {string} 축소된 내용 또는 전체 내용
         */
        displayedContent() {
            if (!this.post.content) return '';

            // 확장 상태이거나 내용이 짧으면 전체 내용 반환
            if (this.contentExpanded || !this.isContentTooLong) {
                return this.post.content;
            }

            // 축소 상태: 최대 길이만큼만 반환하고 끝에 ... 추가
            return this.post.content.substring(0, this.contentMaxLength) + '...';
        }
    },
    methods: {
        thumbnail: thumbnail,
        shortDateTime: shortDateTime,
        /**
         * 게시물 내용 확장/축소 토글
         */
        toggleContentExpansion() {
            this.contentExpanded = !this.contentExpanded;
        },

        /**
         * 작성자 이름 반환 (없으면 기본값)
         * @param {Object} post - 게시물 객체
         * @returns {string} 작성자 이름
         */
        getAuthorName(post) {
            return post?.author?.first_name || 'No name';
        },

        /**
         * 작성자 프로필 사진 URL 반환
         * @param {Object} post - 게시물 객체
         * @returns {string|null} 프로필 사진 URL 또는 null
         */
        getAuthorPhoto(post) {
            return post.author?.photo_url || null;
        },

        /**
         * 날짜 포맷팅 (shortDateTime 함수 사용)
         * - 오늘 날짜: "오후 3:45" 형식
         * - 과거 날짜: "2025-10-24" 형식
         */
        formatDate(timestamp) {
            return shortDateTime(timestamp);
        },

        /**
         * 게시물 삭제 핸들러
         */
        async handleDeletePost() {
            // 하위 댓글이 있으면 삭제 불가
            if (this.post.comment_count && this.post.comment_count > 0) {
                alert('하위 댓글이 있는 경우 삭제 할 수 없습니다.');
                return;
            }

            const confirmed = confirm('이 게시물을 삭제하시겠습니까?');
            if (!confirmed) {
                return;
            }

            try {
                console.log('Deleting post:', this.post.id);
                await func('delete_post', {
                    id: this.post.id,
                    auth: true
                });

                // Emit event to parent component
                // Parent component should listen for this event and update its own data
                this.$emit('post-deleted', this.post.id);

                alert('Post deleted successfully!');
            } catch (error) {
                console.error('Failed to delete post:', error);
                alert('Failed to delete post: ' + (error.message || 'Unknown error'));
            }
        },

        /**
         * 댓글 삭제 핸들러
         * @param {Object} comment - 삭제할 댓글 객체
         */
        async handleDeleteComment(comment) {
            // 하위 댓글이 있으면 삭제 불가
            if (comment.comment_count && comment.comment_count > 0) {
                alert('하위 댓글이 있는 경우 삭제 할 수 없습니다.');
                return;
            }

            // 삭제 확인 대화상자
            const confirmed = confirm('정말 이 댓글을 삭제하시겠습니까?');
            if (!confirmed) {
                return;
            }

            try {
                console.log('Deleting comment:', comment.id);
                await func('delete_comment', {
                    comment_id: comment.id,
                    auth: true
                });

                // 댓글 목록에서 제거 (Vue reactivity)
                const index = this.post.comments.findIndex(c => c.id === comment.id);
                if (index !== -1) {
                    this.post.comments.splice(index, 1);
                }

                // comment_count 감소 (UI 즉시 반영)
                if (this.post.comment_count > 0) {
                    this.post.comment_count--;
                }

                alert('댓글이 삭제되었습니다.');
            } catch (error) {
                console.error('Failed to delete comment:', error);
                alert('댓글 삭제에 실패했습니다: ' + (error.message || 'Unknown error'));
            }
        },

        /**
         * 유효한 사진이 있는지 확인
         * @param {Array} files - 파일 URL 배열
         * @returns {boolean} 유효한 사진이 하나라도 있으면 true
         */
        hasPhotos(files) {
            if (!files || !Array.isArray(files) || files.length === 0) {
                return false;
            }
            return files.some(url => url && url.trim() !== '');
        },

        /**
         * 유효한 사진 URL만 필터링
         * @param {Array} files - 파일 URL 배열
         * @returns {Array} 빈 문자열이 제거된 URL 배열
         */
        getValidPhotos(files) {
            if (!files || !Array.isArray(files)) {
                return [];
            }
            return files.filter(url => url && url.trim() !== '');
        },

        /**
         * Remove image from edit mode
         * @param {number} index - Image index to remove
         */
        async removeImageFromEdit(index) {

            console.log('Removing image at index:', index);
            // Confirm deletion
            const confirmed = confirm('Are you sure you want to remove this image?');
            if (!confirmed) {
                return;
            }

            // Get the image URL to delete
            const imageUrl = this.edit.files[index];

            const isNewlyUploaded = this.edit.newlyUploadedFiles.includes(imageUrl);

            console.log("Image URL:", imageUrl);
            console.log("Is newly uploaded?", isNewlyUploaded);

            // Case 1: 새로 업로드된 이미지 (아직 DB에 저장 안됨)
            if (isNewlyUploaded) {
                console.log('Deleting newly uploaded image from server:', imageUrl);

                try {
                    // 서버에서 파일 삭제
                    const response = await axios.get(appConfig.api.file_delete, {
                        params: { url: imageUrl }
                    });

                    console.log('Newly uploaded file deleted successfully:', response.data);

                    // newlyUploadedFiles 배열에서 제거
                    const newlyUploadedIndex = this.edit.newlyUploadedFiles.indexOf(imageUrl);
                    if (newlyUploadedIndex !== -1) {
                        this.edit.newlyUploadedFiles.splice(newlyUploadedIndex, 1);
                    }

                    // edit.files 배열에서 제거
                    this.edit.files.splice(index, 1);

                    console.log('Image removed, remaining files:', this.edit.files);
                } catch (error) {
                    console.error('Failed to delete newly uploaded file:', error);
                    alert('Failed to delete image: ' + (error.message || 'Unknown error'));
                }

                return;
            }

            // Case 2: 기존 이미지 (DB에 저장되어 있음)
            console.log('Deleting existing image from post:', imageUrl);

            const result = await func('delete_file_from_post', {
                id: this.post.id,
                url: imageUrl,
                auth: true
            });

            console.log('Existing image deleted successfully:', result);

            // Remove the image from edit.files array (local state)
            this.edit.files.splice(index, 1);

            // Update the post object with the result
            if (result && result.files) {
                // Convert files string to array if needed
                if (typeof result.files === 'string') {
                    this.post.files = result.files.split(',').map(f => f.trim()).filter(f => f);
                } else {
                    this.post.files = result.files;
                }
            }

            console.log('Image removed, remaining files:', this.edit.files);
        },

        /**
         * 파일 업로드 완료 이벤트 핸들러
         * file-upload 컴포넌트에서 @uploaded 이벤트 발생 시 호출됨
         * @param {Object} data - { url: string, qr_code?: string }
         */
        handleFileUploaded(data) {
            console.log('File uploaded:', data.url);
            // 새로 업로드된 파일을 edit.files 배열에 추가
            if (data.url && !this.edit.files.includes(data.url)) {
                this.edit.files.push(data.url);
                // 새로 업로드된 파일로 추적 (취소 시 삭제용)
                this.edit.newlyUploadedFiles.push(data.url);
            }
        },


        /**
         * 게시물 수정 핸들러
         */
        handleEditPost() {
            // 수정 모드로 진입 시도: comment_count 검사
            if (!this.edit.enabled) {
                // 하위 댓글이 있으면 수정 불가
                if (this.post.comment_count && this.post.comment_count > 0) {
                    alert('하위 댓글이 있는 경우 수정 할 수 없습니다.');
                    return;
                }
            }

            // Toggle edit mode
            this.edit.enabled = !this.edit.enabled;

            // 수정 모드로 진입하는 경우에만 데이터 초기화 및 스크롤
            if (this.edit.enabled) {
                this.edit.content = this.post.content || '';
                this.edit.visibility = this.post.visibility || 'public';

                // 카테고리 설정: post의 카테고리가 있으면 사용, 없으면 'story' 기본값
                // 카테고리가 유효한지 확인 (옵션 목록에 존재하는지)
                // const categoryExists = Array.isArray(this.categories) && this.categories.some(root =>
                //     Array.isArray(root.categories) && root.categories.some(sub => sub.category === category)
                // );
                // this.edit.category = categoryExists ? category : 'story';

                // Clone the files array for editing
                this.edit.files = this.post.files ? [...this.post.files] : [];

                // Reset newly uploaded files tracking when entering edit mode
                this.edit.newlyUploadedFiles = [];

                // Scroll post into view with smooth animation and offset
                this.$nextTick(() => {
                    if (this.$refs.postContainer) {
                        const element = this.$refs.postContainer;
                        const elementPosition = element.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.scrollY - 100;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            }
        },

        /**
         * Cancel edit mode
         */
        cancelEdit() {
            this.edit.enabled = false;
            this.edit.content = '';
            this.edit.visibility = 'public';
            this.edit.category = 'story'; // 기본값: story
            this.edit.files = [];
            this.edit.newlyUploadedFiles = []; // Reset newly uploaded files tracking
        },

        /**
         * Save edited post
         * @param {Object} post - 게시물 객체
         */
        async saveEdit() {
            console.log('Saving post:', this.post.id);

            // Convert editFiles array to comma-separated string (if needed by API)
            const filesString = this.edit.files && this.edit.files.length > 0 ?
                this.edit.files.filter(f => f && f.trim() !== '').join(',') :
                '';

            const result = await func('update_post', {
                id: this.post.id,
                content: this.edit.content,
                visibility: this.edit.visibility,
                category: this.edit.visibility === 'public' ? this.edit.category : '', // 공개일 때만 카테고리 저장
                files: filesString
            });

            console.log('Post updated:', result);

            Object.assign(this.post, result);
            this.edit.enabled = false;

            // Clear newly uploaded files tracking after successful save
            this.edit.newlyUploadedFiles = [];

            alert('Post updated successfully!');
        },

        /**
         * 사진 개수에 따른 Bootstrap column 클래스 
         * @param {number} photoCount - 사진 개수
         * @returns {string} Bootstrap column 클래스
         */
        getPhotoColumnClass(photoCount) {
            if (photoCount === 1) return 'col-12';
            if (photoCount === 2) return 'col-6';
            if (photoCount === 3) return 'col-4';
            return 'col-6 col-md-4 col-lg-3'; // 4개 이상
        },

        /**
         * 게시물 내용 포맷팅 (줄바꿈 처리)
         */
        formatContent(content) {
            if (!content) return '';
            return content.replace(/\n/g, '<br>');
        },


        /**
         * 좋아요 버튼 클릭 핸들러
         * @param {Object} post - 게시물 객체
         */
        handleLike(post) {
            console.log('Like clicked:', post.id);
            // TODO: API 호출로 좋아요 추가/제거
        },
        /**
         * Toggle comment box
         * @param {Object} post - Post object
         */
        async showMoreComments() {
            // Comments are already loaded from list_posts(), no need to fetch
            // Just toggle visibility

            const moreComments = await func('get_comments', {
                post_id: this.post.id,
                limit: 99999, // Load all comments
            });

            console.log('Loaded more comments:', moreComments);
            this.post.comments = moreComments;
        },

        /**
         * 공유 버튼 클릭 핸들러
         * @param {Object} post - 게시물 객체
         */
        handleShare(post) {
            console.log('Share clicked:', post.id);
            // TODO: 공유 모달 표시
        },

        /**
         * 댓글 작성
         * @param {Object} post - 게시물 객체
         */
        async submitComment(post) {
            if (!post.newComment || !post.newComment.trim()) {
                return;
            }

            const newComment = await func('create_comment', {
                post_id: post.id,
                content: post.newComment.trim(),
            });

            // parent_id가 없으면 맨 아래에 추가
            if (!newComment.parent_id) {
                post.comments.push(newComment);
            } else {
                // parent_id가 있으면 부모의 형제들 중 맨 아래 위치를 찾아서 삽입
                const insertIndex = this.findInsertPositionAfterSiblings(post.comments, newComment.parent_id);
                post.comments.splice(insertIndex, 0, newComment);
            }

            post.newComment = '';

            console.log('Comment submitted:', newComment);

        },

        /**
         * 사진 모달 열기 (확대 보기)
         * @param {string} photoUrl - 사진 URL
         */
        openPhotoModal(photoUrl) {
            // Bootstrap 모달이나 라이트박스를 사용할 수 있습니다
            // 간단하게 새 창으로 열기
            window.open(photoUrl, '_blank');
        },

        /**
         * 본인 게시물인지 확인
         * @param {Object} post - 게시물 객체
         * @returns {boolean} 본인 게시물이면 true
         */
        isMyPost(post) {
            const myUserId = Store.user?.id;
            return myUserId && post.user_id === myUserId;
        },

        /**
         * 본인 댓글인지 확인
         * @param {Object} comment - 댓글 객체
         * @returns {boolean} 본인 댓글이면 true
         */
        isMyComment(comment) {
            // Validate Store and user exist
            if (!window.Store || !window.Store.state || !window.Store.state.user) {
                return false;
            }

            const currentUserId = window.Store.state.user.id;
            const commentAuthorId = comment.user_id;

            return commentAuthorId === currentUserId;
        },

        /**
         * 부모 댓글의 형제들 중 맨 아래 위치를 찾아서 삽입 인덱스 반환
         * @param {Array} comments - 전체 댓글 배열
         * @param {number} parentId - 부모 댓글 ID
         * @returns {number} 삽입할 인덱스
         */
        findInsertPositionAfterSiblings(comments, parentId) {
            // 부모 댓글 찾기
            const parentIndex = comments.findIndex(c => c.id === parentId);
            if (parentIndex === -1) {
                // 부모를 찾지 못하면 맨 아래에 추가
                return comments.length;
            }

            const parent = comments[parentIndex];
            const parentDepth = parent.depth || 1;

            // 부모 다음부터 탐색 시작
            let lastSiblingIndex = parentIndex;

            for (let i = parentIndex + 1; i < comments.length; i++) {
                const currentComment = comments[i];
                const currentDepth = currentComment.depth || 1;

                // 부모보다 depth가 작거나 같으면 형제/조상 레벨이므로 탐색 종료
                if (currentDepth <= parentDepth) {
                    break;
                }

                // 부모의 직계 자식 (depth가 parentDepth + 1)이면 형제 후보
                if (currentDepth === parentDepth + 1) {
                    lastSiblingIndex = i;
                }

                // 더 깊은 depth (손자, 증손자 등)는 건너뛰고 계속 탐색
            }

            // 마지막 형제의 다음 위치에 삽입
            return lastSiblingIndex + 1;
        },

        /**
         * 댓글 depth에 따른 들여쓰기 스타일 반환
         * @param {number} depth - 댓글 깊이 (1, 2, 3, 4, 5+)
         * @returns {object} 스타일 객체
         */
        getCommentIndentStyle(depth) {
            // depth에 따른 margin-left 값 설정
            let marginLeft = 0;
            if (depth === 1) {
                marginLeft = 32; // 32px
            } else if (depth === 2) {
                marginLeft = 64; // 64px
            } else if (depth === 3) {
                marginLeft = 80; // 80px
            } else if (depth === 4) {
                marginLeft = 96; // 96px
            } else if (depth >= 5) {
                marginLeft = 112; // 112px (최대 들여쓰기 고정)
            }

            return {
                marginLeft: `${marginLeft}px`
            };
        },

        loginCheck() {
            if (login()) return true;
            if (!login()) {
                if (confirm(tr({
                    'ko': '댓글을 작성하려면 로그인이 필요합니다. 로그인 페이지로 이동하시겠습니까?',
                    'en': 'You need to log in to write a comment. Would you like to go to the login page?',
                    'ja': 'コメントを書くにはログインが必要です。ログインページに移動しますか？',
                    'zh': '您需要登录才能发表评论。是否要前往登录页面？'
                }))) {
                    // 로그인 페이지로 리다이렉트
                    window.location.href = appConfig.href.user_login;
                    return false;
                }
                alert(tr({
                    'ko': '로그인을 하지 않아 댓글 작성을 취소합니다.',
                    'en': 'You are not logged in, cancelling comment creation.',
                    'ja': 'ログインしていないため、コメントの作成をキャンセルします。',
                    'zh': '您未登录，取消评论。'
                }));
            }
            return false;
        },


        /**
         * 최상위 댓글 작성 Modal 열기
         */
        openCommentModal() {
            console.log('Opening comment modal for post:', this.post.id);

            if (!this.loginCheck()) {
                return;
            }

            // 댓글 모드로 설정
            this.commentMode = 'comment';
            this.replyingTo = null;
            this.commentContent = '';
            this.commentFiles = [];
            this.uploadProgress = 0;
            this.isUploading = false;

            // Bootstrap Modal 열기
            const modalId = 'commentModal-' + this.post.id;
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                // Modal이 완전히 열린 후 textarea에 포커스
                modalElement.addEventListener('shown.bs.modal', () => {
                    if (this.$refs.commentTextarea) {
                        this.$refs.commentTextarea.focus();
                    }
                }, { once: true });

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Comment modal element not found:', modalId);
            }
        },


        /**
         * 답글 작성 Modal 열기
         * @param {number} commentId - 부모 댓글 ID
         */
        openReplyModal(commentId) {
            console.log('Opening reply modal for comment:', commentId);

            if (!this.loginCheck()) {
                return;
            }

            // 답글 모드로 설정
            this.commentMode = 'reply';
            this.replyingTo = commentId;
            this.commentContent = '';
            this.commentFiles = [];
            this.uploadProgress = 0;
            this.isUploading = false;

            // Bootstrap Modal 열기 (통합 모달 사용)
            const modalId = 'commentModal-' + this.post.id;
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                // Modal이 완전히 열린 후 textarea에 포커스
                modalElement.addEventListener('shown.bs.modal', () => {
                    if (this.$refs.commentTextarea) {
                        this.$refs.commentTextarea.focus();
                    }
                }, { once: true });

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Modal element not found:', modalId);
            }
        },

        /**
         * 댓글 수정 Modal 열기
         * @param {Object} comment - 수정할 댓글 객체
         */
        openEditCommentModal(comment) {
            // 하위 댓글이 있으면 수정 불가
            if (comment.comment_count && comment.comment_count > 0) {
                alert('하위 댓글이 있는 경우 수정 할 수 없습니다.');
                return;
            }

            console.log('Opening edit modal for comment:', comment.id);


            if (!this.loginCheck()) {
                return;
            }


            // 수정 모드로 설정
            this.commentMode = 'edit';
            this.editingCommentId = comment.id;
            this.replyingTo = null;
            this.commentContent = comment.content; // 기존 댓글 내용 로드

            // 기존 첨부 파일 로드 (문자열일 경우 배열로 변환, 빈 값 필터링)
            if (typeof comment.files === 'string') {
                this.commentFiles = comment.files.split(',').map(f => f.trim()).filter(f => f);
            } else if (Array.isArray(comment.files)) {
                this.commentFiles = comment.files.filter(f => f && f.trim() !== '');
            } else {
                this.commentFiles = [];
            }

            this.uploadProgress = 0;
            this.isUploading = false;

            // Bootstrap Modal 열기 (통합 모달 사용)
            const modalId = 'commentModal-' + this.post.id;
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                // Modal이 완전히 열린 후 textarea에 포커스
                modalElement.addEventListener('shown.bs.modal', () => {
                    if (this.$refs.commentTextarea) {
                        this.$refs.commentTextarea.focus();
                    }
                }, { once: true });

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Modal element not found:', modalId);
            }
        },

        /**
         * Modal에서 댓글/답글/수정 제출 (통합 메서드)
         * commentMode에 따라 최상위 댓글, 답글 생성 또는 댓글 수정
         */
        async submitComment() {
            if (!this.commentContent || !this.commentContent.trim()) {
                return;
            }

            // 답글 모드일 때 parent_id 확인
            if (this.commentMode === 'reply' && !this.replyingTo) {
                console.error('No parent comment ID for reply');
                return;
            }

            // 수정 모드일 때 댓글 ID 확인
            if (this.commentMode === 'edit' && !this.editingCommentId) {
                console.error('No comment ID for edit');
                return;
            }

            try {
                // 파일 URL을 콤마로 구분된 문자열로 변환
                const filesString = this.commentFiles.length > 0 ? this.commentFiles.join(',') : '';

                // 수정 모드
                if (this.commentMode === 'edit') {
                    const params = {
                        comment_id: this.editingCommentId,
                        content: this.commentContent.trim(),
                        files: filesString,
                    };

                    const updatedComment = await func('update_comment', params);

                    console.log('Comment updated:', updatedComment);

                    // 댓글 목록에서 기존 댓글 찾아서 업데이트
                    const index = this.post.comments.findIndex(c => c.id === this.editingCommentId);
                    if (index !== -1) {
                        // Vue reactivity를 위해 splice 사용
                        this.post.comments.splice(index, 1, updatedComment);
                    }

                    // Modal 닫기
                    this.closeModal();
                    return;
                }

                // 생성 모드 (댓글 또는 답글)
                const params = {
                    post_id: this.post.id,
                    content: this.commentContent.trim(),
                    files: filesString,
                };

                // 답글 모드일 때만 parent_id 추가
                if (this.commentMode === 'reply') {
                    params.parent_id = this.replyingTo;
                }

                const newComment = await func('create_comment', params);

                console.log(this.commentMode === 'reply' ? 'Reply submitted:' : 'Comment submitted:', newComment);

                // 댓글 목록에 추가
                if (this.commentMode === 'reply') {
                    // 답글: 부모의 형제들 중 맨 아래 위치를 찾아서 삽입
                    const insertIndex = this.findInsertPositionAfterSiblings(this.post.comments, this.replyingTo);
                    this.post.comments.splice(insertIndex, 0, newComment);
                } else {
                    // 최상위 댓글: 맨 아래에 추가
                    this.post.comments.push(newComment);
                }

                // Modal 닫기
                this.closeModal();
            } catch (error) {
                console.error('Failed to submit comment:', error);
                alert('Failed to submit comment: ' + (error.message || 'Unknown error'));
            }
        },

        /**
         * 댓글/답글 작성 Modal 닫기 (통합 메서드)
         */
        closeModal() {
            const modalId = 'commentModal-' + this.post.id;
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }

            // 상태 초기화
            this.commentMode = 'comment';
            this.replyingTo = null;
            this.editingCommentId = null;
            this.commentContent = '';
            this.commentFiles = [];
            this.uploadProgress = 0;
            this.isUploading = false;
        },

        /**
         * 파일 업로드 이벤트 핸들러 (댓글/답글 공통)
         * @param {Object} data - { url: string, qr_code?: string }
         */
        handleCommentFileUpload(data) {
            console.log('File uploaded:', data.url);
            if (data.url && !this.commentFiles.includes(data.url)) {
                this.commentFiles.push(data.url);
            }
        },

        /**
         * 업로드 진행률 이벤트 핸들러 (댓글/답글 공통)
         * @param {Object} data - { progress: number, uploading: boolean }
         */
        handleUploadProgress(data) {
            this.uploadProgress = data.progress;
            this.isUploading = data.uploading;
        },

        /**
         * 파일 삭제 (댓글/답글 공통)
         * @param {number} index - 파일 인덱스
         */
        removeFile(index) {
            this.commentFiles.splice(index, 1);
        },

        /**
         * Get visibility label for display
         * @param {string} visibility - Visibility value (public, friends, private)
         * @returns {string} Display label
         */
        getVisibilityLabel(visibility) {
            const labels = {
                'public': 'Public',
                'friends': 'Friends',
                'private': 'Only Me'
            };
            return labels[visibility] || 'Public';
        },

        /**
         * Get category label for display
         * @param {string} categoryValue - Category value
         * @returns {string} Display label
         */
        getCategoryLabel(categoryValue) {
            if (!categoryValue) return 'Select Category';

            // Find category in categories array
            for (const root of this.categories) {
                if (root.categories) {
                    const category = root.categories.find(c => c.category === categoryValue);
                    if (category) {
                        return category.name;
                    }
                }
            }
            return categoryValue;
        },

    },
};