<?php




function config(): array
{
    return [
        'login' => login()?->data(),
        'api' => [
            'file_upload_url' => '/api.php?func=file_upload',
            'file_delete_url' => '/api.php?func=file_delete'
        ],
        'upload_path' => get_file_upload_path(),
        'test' => [
            'url' => 'https://local.sonub.com/',
        ]
    ];
}


function get_file_upload_path(): string
{
	        // TODO: what if the user has not loggd in?
    return ROOT_DIR . '/var/uploads/' . (login()?->id ?? 0) . '/';
}
/**
 * $file_path 에서 '/var/uploads/' 이전의 경로를 제거한 다음 리턴
 * @param string $file_path 
 * @return string 
 */
function get_download_url(string $file_path): string
{
    $file_path = str_replace(ROOT_DIR . '/var/uploads/', '', $file_path);
    return '/var/uploads/' . $file_path;
}
