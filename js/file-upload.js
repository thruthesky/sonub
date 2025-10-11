

(() => {
    console.log('file-upload.js loaded');
    // data-default-files 속성이 있는 모든 HTML element 를 찾아서,
    // URL 목록을 콤마로 분리해서, hidden input에 설정하고, 렌더링
    document.querySelectorAll('[data-default-files]').forEach(el => {
        const urls = el.getAttribute('data-default-files').split(',').map(u => u.trim()).filter(u => u);
        if (urls.length === 0) return;
        const displayAreaId = el.id;
        if (!displayAreaId) return;

        // hidden input 생성 및 모든 URL 설정
        const hiddenInput = attach_hidden_input_box(displayAreaId);
        if (!hiddenInput) return;

        hiddenInput.value = urls.join(',');

        // 모든 파일을 display-area에 표시
        display_uploaded_files(displayAreaId);
    });
})();

// displayArea에 hidden input을 생성하거나 찾아서 반환하는 함수
function attach_hidden_input_box(displayAreaId) {
    const displayArea = document.getElementById(displayAreaId);
    if (!displayArea) return null;

    const inputName = displayArea.getAttribute('data-input-name') || displayAreaId;
    let hiddenInput = displayArea.querySelector(`input[type="hidden"][name="${inputName}"]`);

    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = inputName;
        hiddenInput.value = '';
        displayArea.insertBefore(hiddenInput, displayArea.firstChild);
    }

    return hiddenInput;
}




function get_display_area(displayAreaId) {
    const displayArea = document.getElementById(displayAreaId);
    if (!displayArea) {
        console.error('Display area not found:', displayAreaId);
        return null;
    }
    return displayArea;
}

function get_hidden_input(displayAreaId) {
    const inputName = get_data_value(displayAreaId, 'data-input-name', displayAreaId);
    const hiddenInput = document.querySelector(`input[type="hidden"][name="${inputName}"]`);

    if (!hiddenInput) {
        console.warn('Hidden input not found for display area:', displayAreaId, 'with input name:', inputName);
        return attach_hidden_input_box(displayAreaId);
    }
    return hiddenInput || null;
}

// display-area에 연결된 hidden input의 값을 반환하는 함수
function get_hidden_input_value(displayAreaId) {
    const hiddenInput = get_hidden_input(displayAreaId);
    return hiddenInput?.value || '';
}



// display-area 요소의 data- 속성 값을 읽는 유틸리티 함수
function get_data_value(id, key, defaultValue) {
    if (!id) return defaultValue;
    const displayArea = document.getElementById(id);
    if (!displayArea) return defaultValue;

    return displayArea.getAttribute(`data-${key}`) || defaultValue;
}

async function handle_file_change(event, extra = {}) {
    const files = event.target.files;
    if (!files || files.length === 0) return;

    // extra.id는 필수
    if (!extra.id) {
        console.error('extra.id is required');
        alert('파일 업로드 설정 오류: id 속성이 필요합니다.');
        return;
    }

    const single = get_data_value(extra.id, 'single', false);
    extra.single = single === 'true' || single === true;

    for (let i = 0; i < files.length; i++) {
        await upload_file(files[i], extra);
    }
    event.target.value = ''; // 동일 파일 업로드를 위해 초기화
}


async function upload_file(file, extra = {}) {

    // console.log('Uploading file for:', file, extra);

    attach_progress_bar(extra);

    const fd = new FormData();
    fd.append('userfile', file);

    // single: true인 경우, 기존 hidden input의 값을 deleteFile로 전달
    if (extra.single) {
        const previousFile = get_hidden_input_value(extra.id);
        if (previousFile) {
            fd.append('deleteFile', previousFile);
            // console.log('Deleting previous file:', previousFile);
        }
    }

    if (extra.decodeQrCode) {
        fd.append('decodeQrCode', 'Y');
    }
    // console.log('FormData:', fd);

    try {
        // Axios를 사용한 파일 업로드 (Axios는 body 태그 상단에 이미 로드되어 있음)
        const response = await axios.post(appConfig.api.file_upload_url, fd, {
            // Axios는 FormData를 자동으로 인식하여 multipart/form-data로 전송
            // Content-Type 헤더를 자동으로 설정하므로 별도 지정 불필요
            onUploadProgress: (progressEvent) => {
                update_progress_bar(progressEvent, extra);
            }
        });

        const json = response.data;
        console.log('Upload success:', json);

        // extra.id가 있으면 hidden input 업데이트
        if (extra.id && json.url) {
            if (extra.single) {
                // single: true인 경우, 기존 값을 제거하고 새 URL로 교체
                const displayArea = document.getElementById(extra.id);
                const hiddenInput = displayArea.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.value = json.url;
                } else {
                    // hidden input이 없으면 새로 생성
                    const inputName = displayArea.getAttribute('data-input-name') || extra.id;
                    const newInput = document.createElement('input');
                    newInput.type = 'hidden';
                    newInput.name = inputName;
                    newInput.value = json.url;
                    displayArea.insertBefore(newInput, displayArea.firstChild);
                }
            } else {
                // single: false인 경우, 기존 로직대로 URL 추가
                add_url_to_hidden_input(extra.id, json.url);
            }
            // hidden input의 전체 URL 목록을 기반으로 재렌더링
            display_uploaded_files(extra.id);
        }

        data = {
            url: json.url,
            qr_code: json.qr_code || ''
        };

        if (typeof extra.on_uploaded === 'function') {
            extra.on_uploaded(data);
        }

        return data;
    } catch (e) {
        alert(get_axios_error_message(e));
        return e;
    } finally {
        detach_progress_bar(extra);
    }
}


function attach_progress_bar(extra = {}) {
    if (!extra.id) return;

    const displayArea = document.getElementById(extra.id);
    if (!displayArea) return;

    // 기존 progress 컨테이너가 있으면 재사용, 없으면 새로 생성
    let progressContainer = displayArea.querySelector('.file-upload-progress-container');
    if (!progressContainer) {
        // Bootstrap progress 구조 생성
        const container = document.createElement('div');
        container.classList.add('my-3', 'file-upload-progress-container');

        const progressWrapper = document.createElement('div');
        progressWrapper.classList.add('progress');
        progressWrapper.setAttribute('role', 'progressbar');
        progressWrapper.setAttribute('aria-label', 'File upload progress');
        progressWrapper.setAttribute('aria-valuenow', '0');
        progressWrapper.setAttribute('aria-valuemin', '0');
        progressWrapper.setAttribute('aria-valuemax', '100');

        const progressBar = document.createElement('div');
        progressBar.classList.add('progress-bar');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';

        progressWrapper.appendChild(progressBar);
        container.appendChild(progressWrapper);

        // progress bar를 항상 맨 마지막에 추가 (uploaded-files div 뒤에)
        displayArea.appendChild(container);
    }
}

function detach_progress_bar(extra = {}) {
    if (!extra.id) return;

    const displayArea = document.getElementById(extra.id);
    if (!displayArea) return;

    const progressContainer = displayArea.querySelector('.file-upload-progress-container');
    if (progressContainer) {
        setTimeout(() => {
            progressContainer.remove();
        }, 500); // 500ms 후에 progress 바 제거
    }
}

function update_progress_bar(progressEvent, extra = {}) {
    if (!extra.id || !progressEvent.lengthComputable) return;

    const displayArea = document.getElementById(extra.id);
    if (!displayArea) return;

    const progressBar = displayArea.querySelector('.file-upload-progress-container .progress-bar');
    const progressWrapper = displayArea.querySelector('.file-upload-progress-container .progress');

    if (progressBar && progressWrapper) {
        const pct = Math.round((progressEvent.loaded / progressEvent.total) * 100);
        progressBar.style.width = pct + '%';
        progressBar.textContent = pct + '%';
        progressWrapper.setAttribute('aria-valuenow', pct);
    }
}

// hidden input의 URL 목록을 기반으로 차분 업데이트하는 함수 (화면 깜빡임 최소화)
function display_uploaded_files(displayAreaId) {

    const displayArea = get_display_area(displayAreaId);
    const hiddenInput = get_hidden_input(displayAreaId);

    if (!hiddenInput || !hiddenInput.value) {
        // hidden input이 없거나 값이 비어있으면 기존 렌더링 제거
        const uploadedFilesNav = displayArea.querySelector('nav.uploaded-files');

        // console.log('Displaying uploaded files for:', displayArea, hiddenInput, hiddenInput?.value, '--- uploadedFilenavs:', uploadedFilesNav);
        if (uploadedFilesNav) uploadedFilesNav.remove();
        return;
    }


    // single 모드인지 확인
    const single = get_data_value(displayAreaId, 'single', false);

    if (single === 'true' || single === true) {
        // single 모드: 항상 이미지로 처리
        const url = hiddenInput.value.trim();




        // 기존 렌더링 찾기 또는 생성
        let uploadedFilesNav = displayArea.querySelector('.uploaded-files');
        if (!uploadedFilesNav) {
            uploadedFilesNav = document.createElement('nav');
            uploadedFilesNav.classList.add('uploaded-files', 'my-3');
            const progressContainer = displayArea.querySelector('.file-upload-progress-container');
            if (progressContainer) {
                displayArea.insertBefore(uploadedFilesNav, progressContainer);
            } else {
                displayArea.appendChild(uploadedFilesNav);
            }
        }

        // URL이 없으면 div 초기화
        if (!url) {
            uploadedFilesNav.innerHTML = '';
            return;
        }


        // 이미지 생성 (클릭 시 새 파일 업로드)
        uploadedFilesNav.innerHTML = `<img src="${url}" class="img-fluid rounded">`;

        // data-delete-icon=show 인 경우에만 삭제 버튼 추가
        if (get_data_value(displayAreaId, 'delete-icon', 'hide') === 'show') {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1';
            btn.style.zIndex = '10';
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btn.onclick = (e) => {
                e.stopPropagation();
                delete_file(url, { id: displayAreaId, alert_on_error: true });
            };
            uploadedFilesNav.appendChild(btn);
        }

        return;
    }

    // URL 목록 파싱 (콤마로 구분)
    const newUrls = hiddenInput.value.split(',').map(u => u.trim()).filter(u => u);
    if (newUrls.length === 0) {
        // URL이 없으면 기존 렌더링 제거
        const uploadedFilesNav = displayArea.querySelector('nav.uploaded-files');
        if (uploadedFilesNav) uploadedFilesNav.remove();
        return;
    }

    // nav.uploaded-files 찾기 또는 생성
    let uploadedFilesNav = displayArea.querySelector('nav.uploaded-files');
    let row;

    if (!uploadedFilesNav) {
        uploadedFilesNav = document.createElement('nav');
        uploadedFilesNav.classList.add('uploaded-files', 'my-3');

        row = document.createElement('div');
        row.classList.add('row', 'g-2');
        uploadedFilesNav.appendChild(row);

        // display-area 중간에 추가 (progress-bar 이전, hidden input 이후)
        const progressContainer = displayArea.querySelector('.file-upload-progress-container');
        if (progressContainer) {
            displayArea.insertBefore(uploadedFilesNav, progressContainer);
        } else {
            displayArea.appendChild(uploadedFilesNav);
        }
    } else {
        row = uploadedFilesNav.querySelector('.row');
        if (!row) {
            row = document.createElement('div');
            row.classList.add('row', 'g-2');
            uploadedFilesNav.appendChild(row);
        }
    }

    // 기존에 표시된 URL 목록 가져오기
    const existingCols = Array.from(row.querySelectorAll('[data-url]'));
    const existingUrls = existingCols.map(col => col.getAttribute('data-url'));

    // 삭제할 URL 찾기 (기존에는 있지만 새 목록에는 없는 URL)
    const urlsToRemove = existingUrls.filter(url => !newUrls.includes(url));

    // 추가할 URL 찾기 (새 목록에는 있지만 기존에는 없는 URL)
    const urlsToAdd = newUrls.filter(url => !existingUrls.includes(url));

    // 삭제할 URL의 요소 제거
    urlsToRemove.forEach(url => {
        const col = row.querySelector(`[data-url="${url}"]`);
        if (col) col.remove();
    });

    // 추가할 URL의 요소 생성 및 추가
    urlsToAdd.forEach(url => {
        const col = create_file_item(url, displayAreaId);
        row.appendChild(col);
    });

    // URL 순서가 변경된 경우 재정렬 (선택사항)
    // 현재는 순서 변경을 무시하고 새 파일만 뒤에 추가
}

// 개별 파일 아이템 생성 함수 (재사용 가능)
function create_file_item(url, displayAreaId) {
    const col = document.createElement('div');
    col.classList.add('col-3', 'col-md-2', 'position-relative');
    col.setAttribute('data-url', url);

    // 삭제 버튼 생성
    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'start-0', 'm-1');
    deleteBtn.style.zIndex = '10';
    deleteBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    deleteBtn.onclick = () => delete_file(url, { id: displayAreaId, alert_on_error: true });
    col.appendChild(deleteBtn);

    // 파일 타입에 따라 HTML 생성
    if (is_image_file(url)) {
        // 이미지 파일
        const img = document.createElement('img');
        img.src = url;
        img.classList.add('img-fluid', 'rounded');
        img.style.width = '100%';
        img.style.height = 'auto';
        img.style.objectFit = 'cover';
        col.appendChild(img);
    } else if (isVideoFile(url)) {
        // 비디오 파일
        const video = document.createElement('video');
        video.src = url;
        video.classList.add('img-fluid', 'rounded');
        video.style.width = '100%';
        video.style.height = 'auto';
        video.controls = true;
        col.appendChild(video);
    } else {
        // 기타 파일 (확장자 표시)
        const ext = get_file_extension(url).toUpperCase();
        const div = document.createElement('div');
        div.classList.add('border', 'rounded', 'd-flex', 'align-items-center', 'justify-content-center', 'bg-light', 'text-bg-light');
        div.style.width = '100%';
        div.style.aspectRatio = '1';
        div.style.fontSize = '1.2em';
        div.style.fontWeight = 'bold';
        div.textContent = ext || 'FILE';
        col.appendChild(div);
    }

    return col;
}

// hidden input에 URL 추가 함수
function add_url_to_hidden_input(displayAreaId, url) {
    const hiddenInput = attach_hidden_input_box(displayAreaId);
    if (!hiddenInput) return;

    // 기존 value에 새 URL 추가 (콤마로 구분)
    const currentValue = hiddenInput.value;
    if (currentValue) {
        hiddenInput.value = currentValue + ',' + url;
    } else {
        hiddenInput.value = url;
    }

    // console.log('Updated hidden input:', hiddenInput.name, '=', hiddenInput.value);
}

// hidden input에서 특정 URL 제거 함수
function remove_url_from_hidden_input(displayAreaId, url) {
    const displayArea = document.getElementById(displayAreaId);
    if (!displayArea) return;

    // display-area의 data-input-name 속성 읽기, 없으면 displayAreaId를 기본값으로 사용
    const inputName = displayArea.getAttribute('data-input-name') || displayAreaId;

    const hiddenInput = displayArea.querySelector(`input[type="hidden"][name="${inputName}"]`);
    if (!hiddenInput) return;

    // 현재 값을 콤마로 분리
    const urls = hiddenInput.value.split(',').map(u => u.trim()).filter(u => u);

    // 삭제할 URL을 제외한 나머지 URL로 업데이트
    const updatedUrls = urls.filter(u => u !== url);

    // hidden input 값 업데이트
    hiddenInput.value = updatedUrls.join(',');

    // console.log('Removed URL from hidden input:', url);
    // console.log('Updated hidden input:', inputName, '=', hiddenInput.value);
}

async function delete_file(url, extra = {}) {
    if (!confirm('정말로 이 파일을 삭제하시겠습니까?')) {
        return;
    }

    if (!extra.id) {
        console.error('extra.id is required for delete_file');
        alert('파일 삭제 설정 오류: id 속성이 필요합니다.');
        return;
    }
    const displayAreaId = extra.id;


    try {

        // Axios를 사용하여 파일 삭제 API 호출 (GET 방식)
        const response = await axios.get(appConfig.api.file_delete_url, {
            params: {
                url: url
            }
        });

        // console.log('File deleted successfully:', response.data);

        if (typeof extra.on_deleted === 'function') {
            extra.on_deleted(response.data);
        }

    } catch (e) {
        const msg = get_axios_error_message(e, extra);
        if (msg.indexOf('file-not-found') == -1) {
            throw msg;
        }
    }

    // hidden input에서 해당 URL 제거
    remove_url_from_hidden_input(displayAreaId, url);

    // 전체 파일 목록 재렌더링
    display_uploaded_files(displayAreaId);

    return;
}

// 파일 유형 확인 및 처리 함수들
function get_file_extension(url) {
    const match = url.match(/\.([^./?#]+)(?:[?#]|$)/);
    return match ? match[1].toLowerCase() : '';
}

function get_filename(url) {
    const path = url.split('?')[0];
    const segments = path.split('/');
    return segments[segments.length - 1] || 'file';
}

function is_image_file(url) {
    const ext = get_file_extension(url);
    return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext);
}

function isVideoFile(url) {
    const ext = get_file_extension(url);
    return ['mp4', 'webm', 'ogg', 'mov', 'avi', 'wmv', 'flv', 'mkv'].includes(ext);
}

function get_file_icon(url) {
    const ext = get_file_extension(url);

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
}