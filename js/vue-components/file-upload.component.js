/**
 * 파일 업로드 Vue.js 컴포넌트
 *
 * 파일 업로드 기능을 제공하는 재사용 가능한 Vue.js 컴포넌트입니다.
 * single 파일 업로드, 다중 파일 업로드, 진행률 표시, QR 코드 디코딩 등을 지원합니다.
 *
 * 사용법:
 *
 * 1. JavaScript에서 컴포넌트 등록 및 마운트:
 * ```javascript
 * Vue.createApp({
 *     components: {
 *         'file-upload': window.FileUploadComponent
 *     },
 *     data() {
 *         return {
 *             files: []
 *         };
 *     }
 * }).mount('#app');
 * ```
 *
 * 2. HTML에서 컴포넌트 사용:
 * ```html
 * <file-upload
 *     :single="true"
 *     input-name="profile_photo"
 *     :show-delete-icon="true"
 *     default-files="/uploads/photo.jpg"
 *     @uploaded="handleUpload"
 *     @deleted="handleDelete">
 * </file-upload>
 * ```
 *
 * Props:
 * - single: Boolean - 단일 파일 모드 (기존 파일 자동 교체)
 * - input-name: String - hidden input의 name 속성
 * - show-delete-icon: Boolean - 삭제 아이콘 표시 여부
 * - decode-qr-code: Boolean - QR 코드 디코딩 활성화
 * - accept: String - 파일 타입 제한 (예: "image/*")
 * - default-files: String - 기본 파일 URL (쉼표로 구분된 문자열)
 *
 * Events:
 * - @uploaded: 파일 업로드 완료 시 발생 (payload: { url, qr_code })
 * - @deleted: 파일 삭제 완료 시 발생 (payload: 삭제 응답 데이터)
 */

// 인스턴스 카운터 초기화
if (typeof window.fileUploadInstanceCounter === 'undefined') {
    window.fileUploadInstanceCounter = 0;
}

// 파일 업로드 컴포넌트 정의 (Options API)
window.FileUploadComponent = {
    template: `
        <div class="file-upload-wrapper">
            <!-- 파일 선택 버튼 -->
            <div class="file-input-wrapper" v-if="showUploadButton">
                <input
                    type="file"
                    :id="'file-input-' + instanceId"
                    :accept="accept"
                    :multiple="!single"
                    @change="handleFileChange"
                    ref="fileInput"
                    style="display: none;">

                <!-- 커스텀 업로드 버튼 (Single 모드에서 이미지가 있으면 이미지 클릭으로 대체) -->
                <label
                    v-if="!single || !currentFileUrl"
                    :for="'file-input-' + instanceId"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-upload me-2"></i>
                    {{ single ? t.파일_선택 : t.파일들_선택 }}
                </label>
            </div>

            <!-- Hidden Input (서버에 전송할 URL 값 저장) -->
            <input
                type="hidden"
                :name="computedInputName"
                :value="hiddenInputValue">

            <!-- 업로드된 파일들 표시 영역 -->
            <nav v-if="uploadedFiles.length > 0" class="uploaded-files my-3">
                <!-- Single 모드 -->
                <div v-if="single && currentFileUrl" class="single-file-display position-relative">
                    <img
                        v-if="isImageFile(currentFileUrl)"
                        :src="currentFileUrl"
                        class="img-fluid rounded cursor-pointer"
                        @click="openFileSelector"
                        style="cursor: pointer; max-width: 100%;">

                    <video
                        v-else-if="isVideoFile(currentFileUrl)"
                        :src="currentFileUrl"
                        class="img-fluid rounded"
                        controls
                        style="max-width: 100%;">
                    </video>

                    <div
                        v-else
                        class="border rounded d-flex align-items-center justify-content-center bg-light"
                        style="width: 200px; height: 200px; cursor: pointer;"
                        @click="openFileSelector">
                        <div class="text-center">
                            <i :class="getFileIcon(currentFileUrl)" class="fs-1 mb-2"></i>
                            <div class="fw-bold">{{ getFileExtension(currentFileUrl).toUpperCase() || 'FILE' }}</div>
                        </div>
                    </div>

                    <!-- 삭제 버튼 (data-delete-icon="show"인 경우만) -->
                    <button
                        v-if="showDeleteIcon"
                        type="button"
                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                        style="z-index: 10;"
                        @click.stop="deleteFile(currentFileUrl)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Multiple 모드 -->
                <div v-else class="row g-2">
                    <div
                        v-for="url in uploadedFiles"
                        :key="url"
                        class="col-3 col-md-2 position-relative">

                        <!-- 삭제 버튼 -->
                        <button
                            type="button"
                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 me-2"
                            style="z-index: 10;"
                            @click="deleteFile(url)">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                        <!-- 이미지 파일 -->
                        <img
                            v-if="isImageFile(url)"
                            :src="url"
                            class="img-fluid rounded"
                            style="width: 100%; height: auto; object-fit: cover;">

                        <!-- 비디오 파일 -->
                        <video
                            v-else-if="isVideoFile(url)"
                            :src="url"
                            class="img-fluid rounded"
                            style="width: 100%; height: auto;"
                            controls>
                        </video>

                        <!-- 기타 파일 -->
                        <div
                            v-else
                            class="border rounded d-flex align-items-center justify-content-center bg-light text-bg-light"
                            style="width: 100%; aspect-ratio: 1; font-size: 1.2em; font-weight: bold;">
                            {{ getFileExtension(url).toUpperCase() || 'FILE' }}
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Progress Bar -->
            <div v-if="uploading" class="my-3 file-upload-progress-container">
                <div class="progress" role="progressbar" aria-label="File upload progress">
                    <div
                        class="progress-bar"
                        :style="{ width: uploadProgress + '%' }"
                        :aria-valuenow="uploadProgress"
                        aria-valuemin="0"
                        aria-valuemax="100">
                        {{ uploadProgress }}%
                    </div>
                </div>
            </div>
        </div>
    `,

    props: {
        // 파일 업로드 설정
        single: {
            type: Boolean,
            default: false,
            description: '단일 파일 모드 (기존 파일 자동 교체)'
        },
        inputName: {
            type: String,
            default: '',
            description: 'hidden input의 name 속성'
        },
        showUploadButton: {
            type: Boolean,
            default: true,
            description: '업로드 버튼 표시 여부'
        },
        showDeleteIcon: {
            type: Boolean,
            default: false,
            description: '삭제 아이콘 표시 여부'
        },
        decodeQrCode: {
            type: Boolean,
            default: false,
            description: 'QR 코드 디코딩 활성화'
        },
        accept: {
            type: String,
            default: '*',
            description: '파일 타입 제한 (예: "image/*")'
        },
        defaultFiles: {
            type: String,
            default: '',
            description: '기본 파일 URL (쉼표로 구분된 문자열)'
        }
    },

    emits: ['uploaded', 'deleted', 'update:modelValue'],

    data() {
        return {
            // 인스턴스 ID
            instanceId: window.fileUploadInstanceCounter++,

            // 상태값
            uploadedFiles: [],       // 업로드된 파일 URL 목록
            uploading: false,        // 업로드 진행 중 여부
            uploadProgress: 0,       // 업로드 진행률 (0-100)
        };
    },

    computed: {
        /**
         * 현재 파일 URL (single 모드용)
         */
        currentFileUrl() {
            return this.single && this.uploadedFiles.length > 0 ? this.uploadedFiles[0] : null;
        },

        /**
         * hidden input의 value 값
         */
        hiddenInputValue() {
            return this.uploadedFiles.join(',');
        },

        /**
         * computed input name (기본값 처리)
         */
        computedInputName() {
            return this.inputName || `file-upload-${this.instanceId}`;
        },

        /**
         * 다국어 번역 텍스트
         */
        t() {
            return {
                파일_선택: tr({ ko: '파일 선택', en: 'Select File', ja: 'ファイル選택', zh: '选择文件' }),
                파일들_선택: tr({ ko: '파일들 선택', en: 'Select Files', ja: 'ファイル選택', zh: '选择문件' }),
                파일_삭제_확인: tr({ ko: '정말로 이 파일을 삭제하시겠습니까?', en: 'Are you sure you want to delete this file?', ja: 'このファイルを削除してもよろしいですか？', zh: '您确定要删除此文件吗？' }),
                업로드_설정_오류: tr({ ko: '파일 업로드 설정 오류', en: 'File upload configuration error', ja: 'ファイルアップロード設定エラー', zh: '文件上传配置错误' }),
                파일_삭제_오류: tr({ ko: '파일 삭제 오류', en: 'File deletion error', ja: 'ファイル削除エラー', zh: '文件删除错误' })
            };
        }
    },

    methods: {
        /**
         * 파일 선택 창 열기
         */
        openFileSelector() {
            this.$refs.fileInput.click();
        },

        /**
         * 파일 선택 이벤트 처리
         */
        async handleFileChange(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            // 파일들 업로드
            for (let i = 0; i < files.length; i++) {
                await this.uploadFile(files[i]);
            }

            // input 초기화 (동일 파일 재선택 가능하도록)
            event.target.value = '';
        },

        /**
         * 파일 업로드
         */
        async uploadFile(file) {
            console.log('uploadFile(file):', file.name, 'Single mode:', this.single);

            const formData = new FormData();
            formData.append('userfile', file);

            // Single 모드인 경우 기존 파일 삭제 요청
            if (this.single && this.currentFileUrl) {
                formData.append('deleteFile', this.currentFileUrl);
                console.log('Deleting previous file:', this.currentFileUrl);
            }

            // QR 코드 디코딩 옵션
            if (this.decodeQrCode) {
                formData.append('decodeQrCode', 'Y');
            }

            try {
                this.uploading = true;
                this.uploadProgress = 0;

                // Axios로 파일 업로드
                const response = await axios.post(appConfig.api.file_upload_url, formData, {
                    onUploadProgress: (progressEvent) => {
                        if (progressEvent.lengthComputable) {
                            this.uploadProgress = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                        }
                    }
                });

                const json = response.data;
                console.log('uploadFile(file) success:', json);

                if (json.url) {
                    // Single 모드: 기존 파일 교체
                    if (this.single) {
                        this.uploadedFiles = [json.url];
                    } else {
                        // Multiple 모드: 파일 추가
                        this.uploadedFiles.push(json.url);
                    }

                    // 업로드 완료 이벤트 발생
                    this.$emit('uploaded', {
                        url: json.url,
                        qr_code: json.qr_code || ''
                    });

                    // v-model 업데이트
                    this.$emit('update:modelValue', this.uploadedFiles);
                }

                return json;

            } catch (error) {
                console.error('Upload error:', error);
                alert(get_axios_error_message(error));
                throw error;

            } finally {
                // Progress bar 제거
                setTimeout(() => {
                    this.uploading = false;
                    this.uploadProgress = 0;
                }, 500);
            }
        },

        /**
         * 파일 삭제
         */
        async deleteFile(url) {
            if (!confirm(this.t.파일_삭제_확인)) {
                return;
            }


            if (!url && this.single) {
                url = this.currentFileUrl;
            }


            try {
                // Axios로 파일 삭제 API 호출
                const response = await axios.get(appConfig.api.file_delete_url, {
                    params: { url: url }
                });

                console.log('File deleted successfully:', response.data);

                // 삭제 완료 이벤트 발생
                this.$emit('deleted', response.data);

            } catch (error) {
                const msg = get_axios_error_message(error);
                if (msg.indexOf('file-not-found') === -1) {
                    console.error('Delete error:', error);
                    alert(this.t.파일_삭제_오류 + ': ' + msg);
                }
            }

            // 파일 목록에서 제거
            this.uploadedFiles = this.uploadedFiles.filter(f => f !== url);

            // v-model 업데이트
            this.$emit('update:modelValue', this.uploadedFiles);
        },

        /**
         * 파일 확장자 추출
         */
        getFileExtension(url) {
            const match = url.match(/\.([^./?#]+)(?:[?#]|$)/);
            return match ? match[1].toLowerCase() : '';
        },

        /**
         * 이미지 파일 여부 확인
         */
        isImageFile(url) {
            const ext = this.getFileExtension(url);
            return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext);
        },

        /**
         * 비디오 파일 여부 확인
         */
        isVideoFile(url) {
            const ext = this.getFileExtension(url);
            return ['mp4', 'webm', 'ogg', 'mov', 'avi', 'wmv', 'flv', 'mkv'].includes(ext);
        },

        /**
         * 파일 아이콘 클래스 반환
         */
        getFileIcon(url) {
            const ext = this.getFileExtension(url);

            // 문서 파일
            if (['pdf'].includes(ext)) return 'bi bi-file-pdf text-danger';
            if (['doc', 'docx'].includes(ext)) return 'bi bi-file-word text-primary';
            if (['xls', 'xlsx'].includes(ext)) return 'bi bi-file-excel text-success';
            if (['ppt', 'pptx'].includes(ext)) return 'bi bi-file-ppt text-warning';
            if (['txt', 'md'].includes(ext)) return 'bi bi-file-text';

            // 압축 파일
            if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) return 'bi bi-file-zip text-info';

            // 오디오 파일
            if (['mp3', 'wav', 'ogg', 'flac', 'm4a'].includes(ext)) return 'bi bi-file-music text-purple';

            // 코드 파일
            if (['js', 'ts', 'jsx', 'tsx', 'json'].includes(ext)) return 'bi bi-file-code text-warning';
            if (['html', 'htm', 'css', 'scss', 'sass'].includes(ext)) return 'bi bi-file-code text-info';
            if (['php', 'py', 'java', 'c', 'cpp', 'cs'].includes(ext)) return 'bi bi-file-code text-success';

            // 기본 아이콘
            return 'bi bi-file-earmark';
        },

        /**
         * Props로부터 초기 파일 설정
         */
        initializeFiles() {
            // 기본 파일 설정 (쉼표로 구분된 문자열을 배열로 변환)
            if (this.defaultFiles && this.defaultFiles.trim() !== '') {
                // 쉼표로 구분된 URL 문자열을 배열로 변환
                this.uploadedFiles = this.defaultFiles
                    .split(',')
                    .map(url => url.trim())
                    .filter(url => url !== '');
            }

            console.log(`[file-upload-${this.instanceId}] Initialized with settings:`, {
                single: this.single,
                inputName: this.computedInputName,
                showDeleteIcon: this.showDeleteIcon,
                decodeQrCode: this.decodeQrCode,
                defaultFiles: this.uploadedFiles.length,
                defaultFilesStr: this.defaultFiles,
                uploadedFiles: this.uploadedFiles,
                currentFileUrl: this.currentFileUrl,
                accept: this.accept
            });
        }
    },

    mounted() {
        console.log(`[file-upload-${this.instanceId}] Component mounted`);

        // Props로부터 초기 파일 설정
        this.initializeFiles();
    },

    watch: {
        // defaultFiles prop이 변경될 때 파일 목록 업데이트
        defaultFiles: {
            handler(newValue) {
                if (newValue && newValue.trim() !== '') {
                    // 쉼표로 구분된 URL 문자열을 배열로 변환
                    this.uploadedFiles = newValue
                        .split(',')
                        .map(url => url.trim())
                        .filter(url => url !== '');
                } else {
                    this.uploadedFiles = [];
                }
            },
            immediate: false
        }
    }
};

/**
 * 사용 예제
 *
 * 이 컴포넌트는 자동으로 마운트되지 않습니다.
 * 필요한 위치에서 Vue 앱을 생성할 때 컴포넌트로 등록하여 사용하세요.
 *
 * === PHP 페이지에서 사용 예제 ===
 *
 * ```php
 * <?php load_deferred_js('vue-components/file-upload.component'); ?>
 *
 * <div id="my-app">
 *     <!-- 단일 파일 업로드 -->
 *     <file-upload
 *         :single="true"
 *         input-name="profile_photo"
 *         :show-delete-icon="true"
 *         accept="image/*"
 *         :default-files="profilePhotoUrl"
 *         @uploaded="handleProfilePhotoUpload">
 *     </file-upload>
 *
 *     <!-- 다중 파일 업로드 -->
 *     <file-upload
 *         :single="false"
 *         input-name="gallery[]"
 *         :show-delete-icon="true"
 *         accept="image/*,video/*"
 *         :default-files="galleryPhotosStr"
 *         @uploaded="handleGalleryUpload"
 *         @deleted="handleGalleryDelete">
 *     </file-upload>
 * </div>
 *
 * <script>
 * ready(() => {
 *     Vue.createApp({
 *         components: {
 *             'file-upload': window.FileUploadComponent
 *         },
 *         data() {
 *             return {
 *                 profilePhotoUrl: '<?= $user->photo_url ?? '' ?>',
 *                 // 쉼표로 구분된 문자열로 변환
 *                 galleryPhotosStr: '<?= implode(",", $gallery_photos ?? []) ?>'
 *             };
 *         },
 *         methods: {
 *             handleProfilePhotoUpload(data) {
 *                 console.log('프로필 사진 업로드 완료:', data.url);
 *                 this.profilePhotoUrl = data.url;
 *             },
 *             handleGalleryUpload(data) {
 *                 console.log('갤러리 사진 추가:', data.url);
 *             },
 *             handleGalleryDelete(data) {
 *                 console.log('갤러리 사진 삭제:', data);
 *             }
 *         }
 *     }).mount('#my-app');
 * });
 * </script>
 * ```
 *
 * === v-model 사용 예제 ===
 *
 * 참고: v-model은 내부적으로 배열로 관리되지만, defaultFiles prop은 쉼표로 구분된 문자열입니다.
 *
 * ```javascript
 * Vue.createApp({
 *     components: {
 *         'file-upload': window.FileUploadComponent
 *     },
 *     data() {
 *         return {
 *             // v-model로 파일 URL 목록 관리 (배열)
 *             uploadedFiles: [],
 *             // default-files는 쉼표로 구분된 문자열
 *             initialFiles: '/uploads/file1.jpg,/uploads/file2.pdf'
 *         };
 *     },
 *     template: `
 *         <file-upload
 *             v-model="uploadedFiles"
 *             :single="false"
 *             input-name="files[]"
 *             :default-files="initialFiles"
 *             :show-delete-icon="true">
 *         </file-upload>
 *
 *         <!-- 업로드된 파일 목록 표시 -->
 *         <ul>
 *             <li v-for="url in uploadedFiles" :key="url">{{ url }}</li>
 *         </ul>
 *     `
 * }).mount('#app');
 * ```
 */