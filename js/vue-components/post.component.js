
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
    <div ref="postContainer" :class="{ 'border border-warning border-1 rounded-3': edit.enabled }" style="transition: all 0.1s ease;">
    <!-- 게시물 헤더 (사용자 정보) -->
    <header class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #e4e6eb;">
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
                    {{ getAuthorName(post) }}
                </div>
                <div class="small" style="font-size: 13px; color: #65676b; line-height: 1.3;">
                    {{ formatDate(post.created_at) }} ·
                    <span class="badge bg-secondary">{{ post.visibility || 'public' }}</span>
                    <span v-if="edit.enabled" class="badge bg-warning bg-opacity-75 text-dark ms-2">
                        <i class="fa-solid fa-pen-to-square me-1"></i>Editing
                    </span>
                </div>
            </div>
        </div>

        <!-- 게시물 메뉴 (본인 게시물인 경우에만 표시) -->
        <div v-if="isMyPost" class="dropdown">
            <button class="btn btn-sm btn-link text-muted p-1" data-bs-toggle="dropdown" data-bs-auto-close="auto">
                <i class="fa-solid fa-ellipsis"></i>
            </button>

            <!-- 드롭다운 메뉴 -->
            <ul class="dropdown-menu" style="min-width: 120px;">
                <li>
                    <button @click="handleEditPost()"
                            class="btn btn-sm w-100 text-start d-flex align-items-center gap-2 py-2 px-3 border-0">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit</span>
                    </button>
                </li>
                <li>
                    <button @click="handleDeletePost(post)"
                            class="btn btn-sm w-100 text-start text-danger d-flex align-items-center gap-2 py-2 px-3 border-0">
                        <i class="fa-solid fa-trash"></i>
                        <span>Delete</span>
                    </button>
                </li>
            </ul>
        </div>
    </header>

    <!-- 게시물 본문 (Bootstrap 패딩 사용) -->
    <div class="p-3">
        <!-- Edit Mode -->
        <div v-if="edit.enabled">
            <!-- Edit Content Textarea -->
            <textarea
                v-model="edit.content"
                class="form-control mb-3"
                placeholder="What's on your mind?"
                rows="4"
                style="border: 1px solid #ced4da; border-radius: 8px; font-size: 15px;"></textarea>

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
                        accept="image/*,video/*"
                        @uploaded="handleFileUploaded">
                    </file-upload-component>
                </div>


                <div class="d-flex gap-2 flex-column justify-content-end">

                    <div class="post-select-wrapper justify-content-between">
                        <div>
                            <i class="fa-solid fa-earth-americas" v-if="edit.visibility === 'public'" style="font-size: 12px; margin-right: 4px;"></i>
                            <i class="fa-solid fa-user-group" v-if="edit.visibility === 'friends'" style="font-size: 12px; margin-right: 4px;"></i>
                            <i class="fa-solid fa-lock" v-if="edit.visibility === 'private'" style="font-size: 12px; margin-right: 4px;"></i>
                            <select v-model="edit.visibility" class="post-select">
                                <option value="public">Public</option>
                                <option value="friends">Friends</option>
                                <option value="private">Only Me</option>
                            </select>
                        </div>
                            <i class="fa-solid fa-caret-down" style="font-size: 12px; margin-left: 4px;"></i>
                    </div>

                <!-- Edit Visibility -->
                    <div v-if="edit.visibility === 'public'" class="post-select-wrapper">
                        <i class="fa-solid fa-folder" style="font-size: 12px; margin-right: 4px;"></i>
                        <select v-model="edit.category" class="post-select">
                            <optgroup v-for="root in categories" :key="root.display_name" :label="root.display_name">
                                <option v-for="sub in root.categories" :key="sub.category" :value="sub.category">
                                    {{ sub.name }}
                                </option>
                            </optgroup>
                         </select>

                        <i class="fa-solid fa-caret-down" style="font-size: 12px; margin-left: 4px; pointer-events: none;"></i>
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
            <div v-if="post.title" class="fw-semibold mb-2" style="font-size: 18px; color: #050505; line-height: 1.4;">
                {{ post.title }}
            </div>

            <!-- 내용 (Bootstrap 타이포그래피) -->
            <div v-if="post.content" class="mb-3" style="font-size: 15px; color: #050505; line-height: 1.5; white-space: pre-wrap; word-break: break-word;" v-html="formatContent(post.content)"></div>

            <!-- 이미지 -->
            <div>
                <div v-if="hasPhotos(post.files)" class="row g-2">
                    <div v-for="(fileUrl, index) in getValidPhotos(post.files)" :key="index"
                            :class="getPhotoColumnClass(getValidPhotos(post.files).length)">
                        <img :src="thumbnail(fileUrl, 400, 400, 'cover', 85, 'ffffff')"
                                :alt="'Photo ' + (index + 1)"
                                class="rounded"
                                style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                                @click="openPhotoModal(fileUrl)">
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- 게시물 액션 버튼 (Bootstrap 버튼 그룹) -->
    <div class="d-flex border-top" style="border-color: #e4e6eb;">
        <button class="btn btn-link text-decoration-none text-secondary flex-fill py-2 border-0"
                style="font-size: 15px; font-weight: 600;"
                @click="handleLike(post)">
            <i class="fa-regular fa-thumbs-up me-2"></i>
            <span>Like</span>
        </button>
        <button class="btn btn-link text-decoration-none text-secondary flex-fill py-2 border-0"
                style="font-size: 15px; font-weight: 600;"
                @click="showMoreComments">
            <i class="fa-regular fa-comment me-2"></i>
            <span>Comment{{ post.comment_count > 0 ? ' (' + post.comment_count + ')' : '' }}</span>
        </button>
        <button class="btn btn-link text-decoration-none text-secondary flex-fill py-2 border-0"
                style="font-size: 15px; font-weight: 600;"
                @click="handleShare(post)">
            <i class="fa-regular fa-share-from-square me-2"></i>
            <span>Share</span>
        </button>
    </div>

    <!-- 댓글 섹션 (Bootstrap 패딩) -->
    <div class="border-top p-3" style="border-color: #e4e6eb; background-color: #f0f2f5;">
        <!-- 댓글 입력 박스 (Bootstrap Flexbox) -->
        <div class="d-flex align-items-center gap-2 mb-3">
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
            <!-- 댓글 입력 필드 (Bootstrap Input Group) -->
            <div class="flex-grow-1 d-flex align-items-center gap-2 bg-white rounded-pill px-3 py-1" style="border: 1px solid #ced4da;">
                <input
                    type="text"
                    class="form-control border-0 px-0"
                    style="font-size: 14px; box-shadow: none;"
                    placeholder="Write a comment..."
                    v-model="post.newComment"
                    @keyup.enter="submitComment(post)">
                <button
                    class="btn btn-link text-primary text-decoration-none p-0"
                    style="font-size: 16px;"
                    :disabled="!post.newComment || !post.newComment.trim()"
                    @click="submitComment(post)"
                    title="Send comment">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>


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
                            <span class="fw-semibold" style="font-size: 13px; color: #050505;">
                                {{ (comment.author && comment.author.first_name) || 'Anonymous' }}
                            </span>
                            <span class="text-muted" style="font-size: 12px;">
                                {{ formatDate(comment.created_at) }}
                            </span>
                        </div>

                        <!-- 두 번째 줄: 댓글 내용 (연한 회색 배경 박스) -->
                        <div class="rounded-3 px-2 py-1 mb-1" style="background-color: #f8f9fa; border: 1px solid #e4e6eb; font-size: 14px; color: #050505; line-height: 1.3; word-break: break-word;">
                            {{ comment.content }}
                        </div>

                        <!-- 세 번째 줄: 액션 버튼 (좌측: Like/Reply, 우측: Edit/Delete) -->
                        <div class="d-flex align-items-center justify-content-between">
                            <!-- 좌측: Like, Reply -->
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-link text-decoration-none text-muted p-0 fw-semibold" style="font-size: 11px;">
                                    Like
                                </button>
                                <button @click="toggleReply(comment.id)" class="btn btn-link text-decoration-none text-muted p-0 fw-semibold" style="font-size: 11px;">
                                    Reply
                                </button>
                            </div>

                            <!-- 우측: Edit, Delete -->
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-link text-decoration-none text-muted p-0" style="font-size: 13px;" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-link text-decoration-none text-danger p-0" style="font-size: 13px;" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 답글 입력창 (Reply 버튼 클릭 시 표시) -->
                        <div v-if="replyingTo === (comment.id)" class="d-flex align-items-center gap-2 mt-2">
                            <!-- 답글 작성자 아바타 -->
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle flex-shrink-0"
                                 style="width: 32px; height: 32px; background-color: #e4e6eb;">
                                <img v-if="currentUserPhoto"
                                        :src="currentUserPhoto"
                                        alt="My avatar"
                                        class="rounded-circle"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                <i v-else class="fa-solid fa-user text-secondary" style="font-size: 14px;"></i>
                            </div>
                            <!-- 답글 입력 필드 -->
                            <div class="flex-grow-1 d-flex align-items-center gap-2 bg-white rounded-pill px-3 py-1" style="border: 1px solid #ced4da;">
                                <input
                                    type="text"
                                    class="form-control border-0 px-0"
                                    style="font-size: 14px; box-shadow: none;"
                                    placeholder="Write a reply..."
                                    v-model="replyContent"
                                    @keyup.enter="submitReply(comment.id)"
                                    @keyup.esc="cancelReply()">
                                <button
                                    class="btn btn-link text-primary text-decoration-none p-0"
                                    style="font-size: 16px;"
                                    :disabled="!replyContent || !replyContent.trim()"
                                    @click="submitReply(comment.id)"
                                    title="Send reply">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    </div>
`,
    data() {
        // Safely get categories data
        const categoriesData = window.categoryData?.rootCategories;

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
            },
            replyingTo: null, // 현재 답글을 작성 중인 댓글 ID
            replyContent: '', // 답글 내용
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
        }
    },
    methods: {
        thumbnail: thumbnail,
        shortDateTime: shortDateTime,
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
            // Confirm deletion
            const confirmed = confirm('Are you sure you want to remove this image?');
            if (!confirmed) {
                return;
            }

            // Get the image URL to delete
            const imageUrl = this.edit.files[index];

            // Call API to delete the image from the post
            console.log('Deleting image from post:', imageUrl);
            const result = await func('delete_file_from_post', {
                id: this.post.id,
                url: imageUrl,
                auth: true
            });

            console.log('Image deleted successfully:', result);

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
            }
        },


        /**
         * 게시물 수정 핸들러
         */
        handleEditPost() {
            // Toggle edit mode
            this.edit.enabled = !this.edit.enabled;

            // 수정 모드로 진입하는 경우에만 데이터 초기화 및 스크롤
            if (this.edit.enabled) {
                this.edit.content = this.post.content || '';
                this.edit.visibility = this.post.visibility || 'public';

                // 카테고리 설정: post의 카테고리가 있으면 사용, 없으면 'story' 기본값
                let category = this.post.category || 'story';
                // 카테고리가 유효한지 확인 (옵션 목록에 존재하는지)
                const categoryExists = Array.isArray(this.categories) && this.categories.some(root =>
                    Array.isArray(root.categories) && root.categories.some(sub => sub.category === category)
                );
                this.edit.category = categoryExists ? category : 'story';

                // Clone the files array for editing
                this.edit.files = this.post.files ? [...this.post.files] : [];

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
        },

        /**
         * Save edited post
         * @param {Object} post - 게시물 객체
         */
        async saveEdit() {
            if (!this.edit.content || !this.edit.content.trim()) {
                alert('Please enter post content.');
                return;
            }

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


            alert('Post updated successfully!');
        },

        /**
         * 사진 개수에 따른 Bootstrap column 클래스 반환
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

        /**
         * 답글 입력창 토글
         * @param {number} commentId - 댓글 ID
         */
        toggleReply(commentId) {
            console.log('Toggle reply for comment:', commentId);
            console.log('Current replyingTo:', this.replyingTo);
            console.log('Type of commentId:', typeof commentId);
            console.log('Are they equal?', this.replyingTo === commentId);

            if (this.replyingTo === commentId) {
                // 이미 열려있으면 닫기
                this.replyingTo = null;
                this.replyContent = '';
            } else {
                // 새로 열기
                this.replyingTo = commentId;
                this.replyContent = '';
            }

            console.log('After toggle, replyingTo:', this.replyingTo);
        },

        /**
         * 답글 작성 취소
         */
        cancelReply() {
            this.replyingTo = null;
            this.replyContent = '';
        },

        /**
         * 답글 제출
         * @param {number} parentCommentId - 부모 댓글 ID
         */
        async submitReply(parentCommentId) {
            if (!this.replyContent || !this.replyContent.trim()) {
                return;
            }

            try {
                const newReply = await func('create_comment', {
                    post_id: this.post.id,
                    parent_id: parentCommentId,
                    content: this.replyContent.trim(),
                });

                console.log('Reply submitted:', newReply);

                // 부모의 형제들 중 맨 아래 위치를 찾아서 삽입
                const insertIndex = this.findInsertPositionAfterSiblings(this.post.comments, parentCommentId);
                this.post.comments.splice(insertIndex, 0, newReply);

                // 입력창 닫기
                this.replyingTo = null;
                this.replyContent = '';
            } catch (error) {
                console.error('Failed to submit reply:', error);
                alert('Failed to submit reply: ' + (error.message || 'Unknown error'));
            }
        },

    },
};