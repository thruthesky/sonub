<?php

// etc/config/app.config.php
class AppConfigApi
{
    public string $file_upload_url;
    public string $file_delete_url;
    public function __construct(string $file_upload_url, string $file_delete_url)
    {
        $this->file_upload_url = $file_upload_url;
        $this->file_delete_url = $file_delete_url;
    }
    public function toArray(): array
    {
        return [
            'file_upload_url' => $this->file_upload_url,
            'file_delete_url' => $this->file_delete_url,
        ];
    }
}

class CategoryConfig
{
    public string $my_wall = 'my_wall';
}

class AppConfigTest
{
    public string $url;
    public function __construct(string $url)
    {
        $this->url = $url;
    }
    public function toArray(): array
    {
        return [
            'url' => $this->url,
        ];
    }
}


class AppConfig
{
    public ?UserModel $login = null;
    public ?AppConfigApi $api = null;
    public string $upload_path = '';
    public AppConfigTest $test;
    public array $pages_without_footer = [];

    public function toArray(): array
    {
        return [
            'login' => $this->login ? $this->login->toArray() : null,
            'api' => $this->api ? $this->api->toArray() : null,
            'upload_path' => $this->upload_path,
            'test' => $this->test->toArray(),
            'pages_without_footer' => [
                // exmaple: '/' 르면 보여 주지 않는다.
                '/',
                '/post/list',
                '/user/list',
            ],
        ];
    }
}


function config(): AppConfig
{
    static $config = null;
    if ($config === null) {
        $config = new AppConfig();
        $config->login = login();
        $config->api = new AppConfigApi(
            file_upload_url: '/api.php?func=file_upload',
            file_delete_url: '/api.php?func=file_delete'
        );
        $config->upload_path = get_file_upload_path();
        $config->test = new AppConfigTest(
            url: 'https://local.sonub.com'
        );
        // 개발 환경에서도 footer를 표시
        $config->pages_without_footer = [];
    }
    return $config;
}


function get_file_upload_path(): string
{
    // 로그인된 사용자가 있으면 사용자별 디렉토리, 없으면 공통 디렉토리
    $user = login();
    $userPath = $user ? $user->id . '/' : '';
    return ROOT_DIR . '/var/uploads/' . $userPath;
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


/**
 * 푸터(홈페이지 맨 하단 정보)를 보여 줄지 결정한다.
 * 
 * 참고로, 무제한 스크롤이 필요한 페이지에는 보여주지 않는다.
 * @return bool 
 * @throws ApiException 
 * @throws PDOException 
 * @throws InvalidArgumentException 
 */
function show_footer(): bool
{



    // 프로덕션 환경에서는 특정 페이지에서만 푸터를 표시
    $page = page();

    // if the page() conatins(not match) any of the config()->pages_without_footer, return false
    foreach (config()->pages_without_footer as $no_footer_page) {
        if (strpos($page, $no_footer_page) !== false) {
            return false;
        }
    }
    return true;
}
