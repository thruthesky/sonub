<?php

/**
 * 첨부 파일 삭제
 * @param array $params 
 * $params['url'] - 에는 /var/uploads/[user-id]/[file-name] 과 같이 파일 경로가 들어 올 수 있다.
 * @return void 
 * 
 * 로직:
 * - 로그인 한 경우만 이 함수를 호출 할 수 있으며,
 * - 반드시 자신의 폴더 /var/uploads/[id]/xxxxx 와 같은 폴더에 저장되어져 있는 경우만 삭제를 할 수 있다.
 */
function file_delete(array $params)
{
    if (! login()) {
        return error('login-first', t()->login_first);
    }

    // check if the path is valid and contains the login user's ID
    if (
        !isset($params['url']) || !is_string($params['url']) ||
        strpos($params['url'], '/var/uploads/' . login()->id . '/') !== 0
    ) {
        return error('invalid-file-path', tr(['en' => 'Invalid file path.', 'ko' => '잘못된 파일 경로입니다.']), ['path' => $params['url'] ?? '']);
    }

    $file_path = ROOT_DIR . $params['url'];
    if (!file_exists($file_path)) {
        return error('file-not-found', tr(['en' => 'File not found.', 'ko' => '파일을 찾을 수 없습니다.']), ['path' => $file_path]);
    }

    $deleted = @unlink($file_path);
    if (! $deleted) {
        return error('file-delete-failed', tr(['en' => 'File delete failed.', 'ko' => '파일 삭제에 실패했습니다.']), ['path' => $file_path]);
    }
    return ['deleted' => true, 'path' => $file_path];
}

/**
 * 여러 파일 삭제
 * 
 * 글/코멘트의 첨부 파일들을 일괄 삭제할 때 사용한다.
 * 참고
 * - 이 함수는 배열을 입력 받을 수 있으며, 파일 경로들을 콤마(,)로 구분한 문자열을 인자로도 받는다.
 * - var/uploads 디렉토리 내부의 파일들만 삭제할 수 있다. 경로에 이 문구가 없으면 삭제하지 않는다.
 *
 * @param mixed $file_paths
 * - 삭제할 파일 경로들을 콤마(,)로 구분한 문자열
 * - 또는 파일 경로들의 배열
 * 
 * @return void
 */
function delete_files(mixed $file_paths)
{
    if (is_string($file_paths)) {
        $file_paths = array_map('trim', explode(',', $file_paths));
    }
    foreach ($file_paths as $file_url) {
        if (strpos($file_url, '/var/uploads/') === false) {
            continue;
        }
        file_delete(['url' => $file_url]);
    }
}
