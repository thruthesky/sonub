<?php

/**
 * 파일 업로드 함수
 *
 * 업로드된 파일을 지정된 디렉토리로 이동합니다.
 * 디렉토리가 존재하지 않으면 자동으로 생성합니다.
 * 동일한 파일명이 존재하면 자동으로 -1, -2, -3 등을 추가합니다.
 *
 * @return array 업로드 성공 시 파일 URL, 실패 시 에러 정보
 */
function file_upload()
{
    $uploaddir = get_file_upload_path();
    $original_filename = basename($_FILES['userfile']['name']);

    // 디렉토리가 존재하지 않으면 생성
    if (!is_dir($uploaddir)) {
        @mkdir($uploaddir, 0755, true);
    }

    // 파일명 중복 체크 및 고유한 파일명 생성
    $uploadfile = get_unique_filename($uploaddir, $original_filename);

    $json = [];
    // @ 연산자로 PHP 경고 메시지 숨김 (에러는 error_code로 처리)
    if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        $json['url'] = get_download_url($uploadfile);
    } else {
        return error('move-uploaded-file-failed', tr(['en' => 'Failed to move temporary file. Possible file upload attack!', 'ko' => '임시 파일 이동에 실패했습니다. 파일 업로드 공격 가능성이 있습니다!']));
    }

    return $json;
}

/**
 * 고유한 파일명 생성 함수
 *
 * 동일한 파일명이 존재하면 -1, -2, -3 등을 파일명 끝에 추가하여
 * 고유한 파일명을 반환합니다.
 *
 * 예제:
 * - photo.jpg가 존재하면 → photo-1.jpg
 * - photo-1.jpg도 존재하면 → photo-2.jpg
 * - photo-2.jpg도 존재하면 → photo-3.jpg
 *
 * @param string $directory 파일이 저장될 디렉토리 경로 (끝에 / 포함)
 * @param string $filename 원본 파일명
 * @return string 고유한 전체 파일 경로
 */
function get_unique_filename($directory, $filename)
{
    // 파일명과 확장자 분리
    $pathinfo = pathinfo($filename);
    $basename = $pathinfo['filename'];  // 확장자를 제외한 파일명
    $extension = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';  // 확장자

    // 초기 파일 경로
    $filepath = $directory . $filename;

    // 파일이 존재하지 않으면 원본 파일명 그대로 반환
    if (!file_exists($filepath)) {
        return $filepath;
    }

    // 파일이 존재하면 -1, -2, -3... 추가
    $counter = 1;
    while (true) {
        $new_filename = $basename . '-' . $counter . $extension;
        $new_filepath = $directory . $new_filename;

        // 중복되지 않는 파일명을 찾으면 반환
        if (!file_exists($new_filepath)) {
            return $new_filepath;
        }

        $counter++;

        // 무한 루프 방지 (최대 9999개까지만 시도)
        if ($counter > 9999) {
            // 타임스탬프를 사용한 고유 파일명 생성
            $timestamp_filename = $basename . '-' . time() . $extension;
            return $directory . $timestamp_filename;
        }
    }
}
