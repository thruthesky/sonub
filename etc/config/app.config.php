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
            'login' => $this->login->toArray(),
            'api' => $this->api ? $this->api->toArray() : null,
            'upload_path' => $this->upload_path,
            'test' => $this->test->toArray(),
            'pages_without_footer' => $this->pages_without_footer,
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
        $config->pages_without_footer = [
            '/page/index.php',
            '/page/post/list.php',
        ];
    }
    return $config;
}


function get_file_upload_path(): string
{
    return ROOT_DIR . '/var/uploads/' . login()->id . '/';
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
