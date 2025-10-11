<?php

/**
 * 파일 업로드 함수
 *
 * 업로드된 파일을 지정된 디렉토리로 이동합니다.
 * 디렉토리가 존재하지 않으면 자동으로 생성합니다.
 *
 * @return array 업로드 성공 시 파일 URL, 실패 시 에러 정보
 */
function file_upload()
{
    $uploaddir = get_file_upload_path();
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

    // 디렉토리가 존재하지 않으면 생성
    if (!is_dir($uploaddir)) {
        @mkdir($uploaddir, 0755, true);
    }

    $json = [];
    // @ 연산자로 PHP 경고 메시지 숨김 (에러는 error_code로 처리)
    if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        $json['url'] = get_download_url($uploadfile);
    } else {
        return error('move-uploaded-file-failed', tr(['en' => 'Failed to move temporary file. Possible file upload attack!', 'ko' => '임시 파일 이동에 실패했습니다. 파일 업로드 공격 가능성이 있습니다!']));
    }

    return $json;
}
