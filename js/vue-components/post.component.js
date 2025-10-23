
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
    <!-- 게시물 헤더 (사용자 정보) -->
    <header class="post-header">
        <div class="d-flex align-items-center">
            <!-- 프로필 사진 -->
            <div class="post-header-avatar">
                <img v-if="getAuthorPhoto(post)"
                        :src="getAuthorPhoto(post)"
                        :alt="getAuthorName(post)"
                        style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <i v-else class="fa-solid fa-user"></i>
            </div>

            <!-- 사용자 이름, 날짜, 공개범위 -->
            <div class="post-header-info">
                <div class="post-header-name">{{ getAuthorName(post) }}</div>
                <div class="post-header-meta">
                    {{ formatDate(post.created_at) }} ·
                    <span class="badge bg-secondary">{{ post.visibility || 'public' }}</span>
                </div>
            </div>
        </div>

        <!-- 게시물 메뉴 (본인 게시물인 경우에만 표시) -->
        <div class="dropdown">
            <button class="btn btn-sm btn-link text-muted p-1" data-bs-toggle="dropdown" data-bs-auto-close="auto">
                <i class="fa-solid fa-ellipsis"></i>
            </button>

            <!-- 드롭다운 메뉴 -->
            <ul class="dropdown-menu">
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

    <!-- 게시물 본문 -->
    <div class="post-body">
        <!-- Edit Mode -->
        {{edit.enabled ? 'Editing Post' : 'Viewing Post'}}
        <div v-if="edit.enabled">


            <!-- Edit Content Textarea -->
            <textarea
                v-model="edit.content"
                class="post-content-input form-control mb-3"
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

            <!-- File Upload Component (for adding new images) -->
            <div class="mb-3">
                <label class="form-label small text-muted">Add More Images</label>
                <file-upload-component
                    :single="false"
                    :show-uploaded-files="false"
                    :show-upload-button="true"
                    accept="image/*,video/*"
                    @uploaded="handleFileUploaded">
                </file-upload-component>
            </div>

            <!-- Edit Visibility -->
            <div class="mb-3">
                <label class="form-label small text-muted">Visibility</label>
                <div class="d-flex gap-2">
                    <div class="post-select-wrapper">
                        <i class="fa-solid fa-earth-americas" v-if="edit.visibility === 'public'" style="font-size: 12px; margin-right: 4px;"></i>
                        <i class="fa-solid fa-user-group" v-if="edit.visibility === 'friends'" style="font-size: 12px; margin-right: 4px;"></i>
                        <i class="fa-solid fa-lock" v-if="edit.visibility === 'private'" style="font-size: 12px; margin-right: 4px;"></i>
                        <select v-model="edit.visibility" class="post-select">
                            <option value="public">Public</option>
                            <option value="friends">Friends</option>
                            <option value="private">Only Me</option>
                        </select>
                        <i class="fa-solid fa-caret-down" style="font-size: 12px; margin-left: 4px;"></i>
                    </div>
                </div>
            </div>

            <!-- Edit Action Buttons -->
            <div class="d-flex gap-2 justify-content-end">
                <button @click="cancelEdit()" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-xmark me-1"></i>
                    Cancel
                </button>
                <button @click="saveEdit()" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-check me-1"></i>
                    Save
                </button>
            </div>
        </div>

        <!-- View Mode -->
        <div v-else>
            <!-- 제목 -->
            <div v-if="post.title" class="post-title">{{ post.title }}</div>

            <!-- 내용 -->
            <div v-if="post.content" class="post-content" v-html="formatContent(post.content)"></div>

            <!-- 이미지 -->
            <div class="post-images">
                <div v-if="hasPhotos(post.files)" class="row g-2">
                    <div v-for="(fileUrl, index) in getValidPhotos(post.files)" :key="index"
                            :class="getPhotoColumnClass(getValidPhotos(post.files).length)">
                        <img :src="thumbnail(fileUrl, 400, 400, 'cover', 85, 'ffffff')"
                                :alt="'Photo ' + (index + 1)"
                                style="width: 100%; height: 200px; object-fit: cover;"
                                @click="openPhotoModal(fileUrl)">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 게시물 액션 버튼 -->
    <div class="post-actions">
        <button class="post-action-btn" @click="handleLike(post)">
            <i class="fa-regular fa-thumbs-up"></i>
            <span>Like</span>
        </button>
        <button class="post-action-btn" @click="toggleCommentBox(post)">
            <i class="fa-regular fa-comment"></i>
            <span>Comment</span>
        </button>
        <button class="post-action-btn" @click="handleShare(post)">
            <i class="fa-regular fa-share-from-square"></i>
            <span>Share</span>
        </button>
    </div>

    <!-- 댓글 섹션 -->
    <div v-if="post.showComments" class="post-comments-section">
        <!-- 댓글 입력 박스 -->
        <div class="comment-input-box">
            <div class="comment-input-avatar">
                <img v-if="currentUserPhoto"
                        :src="currentUserPhoto"
                        alt="My avatar"
                        style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <i v-else class="fa-solid fa-user"></i>
            </div>
            <div class="comment-input-wrapper">
                <input
                    type="text"
                    class="comment-input"
                    placeholder="Write a comment..."
                    v-model="post.newComment"
                    @keyup.enter="submitComment(post)">
                <button
                    class="comment-submit-btn"
                    :disabled="!post.newComment || !post.newComment.trim()"
                    @click="submitComment(post)">
                    Post
                </button>
            </div>
        </div>

        <!-- 댓글 목록 -->
        <div v-if="post.comments && post.comments.length > 0" class="comments-list">
            <div v-for="comment in post.comments" :key="comment.comment_id" class="comment-item">
                <!-- 댓글 작성자 아바타 -->
                <div class="comment-avatar">
                    <img v-if="comment.author_photo_url"
                            :src="comment.author_photo_url"
                            :alt="comment.author_name"
                            style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    <i v-else class="fa-solid fa-user"></i>
                </div>

                <!-- 댓글 내용 -->
                <div class="comment-content-wrapper">
                    <div class="comment-bubble">
                        <div class="comment-author">{{ comment.author_name || 'Anonymous' }}</div>
                        <div class="comment-text">{{ comment.content }}</div>
                    </div>
                    <div class="comment-meta">
                        {{ formatDate(comment.created_at) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
`,
    data() {
        return {
            post: this.post,
            edit: {
                enabled: false,
                content: '',
                visibility: 'public',
                files: [],
            }
        };
    },
    methods: {
        thumbnail: thumbnail,
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
         * 날짜 포맷팅
         */
        formatDate(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp * 1000);
            return date.toLocaleString('ko-KR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
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
                console.log('Deleting post:', this.post.post_id);
                await func('delete_post', {
                    id: this.post.post_id,
                    auth: true
                });

                // Emit event to parent component
                // Parent component should listen for this event and update its own data
                this.$emit('post-deleted', this.post.post_id);

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
                 id: this.post.post_id,
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
            // Enable edit mode
            this.edit.enabled = true;
            this.edit.content = this.post.content || '';
            this.edit.visibility = this.post.visibility || 'public';
            // Clone the files array for editing
            this.edit.files = this.post.files ? [...this.post.files] : [];
        },

        /**
         * Cancel edit mode
         */
        cancelEdit() {
            this.edit.enabled = false;
            this.edit.content = '';
            this.edit.visibility = 'public';
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

            console.log('Saving post:', this.post.post_id);

            // Convert editFiles array to comma-separated string (if needed by API)
            const filesString = this.edit.files && this.edit.files.length > 0 ?
                this.edit.files.filter(f => f && f.trim() !== '').join(',') :
                '';

            const result = await func('update_post', {
                id: this.post.post_id,
                content: this.edit.content,
                visibility: this.edit.visibility,
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
            console.log('Like clicked:', post.post_id);
            // TODO: API 호출로 좋아요 추가/제거
        },
        /**
         * 댓글 박스 토글
         * @param {Object} post - 게시물 객체
         */
        toggleCommentBox(post) {
            post.showComments = !post.showComments;
            if (post.showComments && (!post.comments || post.comments.length === 0)) {
                this.loadComments(post);
            }
        },

        /**
         * 공유 버튼 클릭 핸들러
         * @param {Object} post - 게시물 객체
         */
        handleShare(post) {
            console.log('Share clicked:', post.post_id);
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

            try {
                // TODO: API 호출로 댓글 저장
                // const result = await func('create_comment', {
                //     post_id: post.post_id,
                //     content: post.newComment.trim(),
                //     auth: true
                // });

                // 임시: 댓글을 로컬에 추가 (실제로는 API 응답 사용)
                const newComment = {
                    content: post.newComment.trim(),
                    created_at: new Date().toISOString()
                };

                post.comments.push(newComment);
                post.newComment = '';

                console.log('Comment submitted:', newComment);
            } catch (error) {
                console.error('댓글 작성 오류:', error);
                alert('댓글 작성 중 오류가 발생했습니다.');
            }
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
         * 댓글 목록 로드
         * @param {Object} post - 게시물 객체
         */
        async loadComments(post) {
            try {
                // TODO: API 호출로 댓글 목록 가져오기
                // const comments = await func('get_comments', { post_id: post.post_id });
                // post.comments = comments;

                // 임시 데이터 (실제로는 API에서 가져옴)
                console.log('Loading comments for post:', post.post_id);
            } catch (error) {
                console.error('댓글 로드 오류:', error);
            }
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

    },
};