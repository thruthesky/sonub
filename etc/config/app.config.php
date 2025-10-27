<?php

// etc/config/app.config.php
class AppConfigApi
{
    public function __construct(
        public string $file_upload_url,
        public string $file_delete_url,
        public string $thumbnail_url = '/thumbnail.php'
    ) {}

    public function toArray(): array
    {
        return [
            'file_upload_url' => $this->file_upload_url,
            'file_delete_url' => $this->file_delete_url,
            'thumbnail_url' => $this->thumbnail_url,
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

class HrefConfig
{
    public string $user_login;

    public function __construct(
        string $user_login,
    ) {
        $this->user_login = $user_login;
    }

    public function toArray(): array
    {
        return [
            'user_login' => $this->user_login,
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
    public CategoryList $categories;
    public HrefConfig $href;

    /**
     * 테스트 계정 정보
     *
     * 개발/테스트 환경에서 사용할 수 있는 테스트 계정들의 정보를 반환합니다.
     * 모든 테스트 계정의 비밀번호는 동일하며, 이메일 형식은 {username}@test.com 입니다.
     *
     * @return array 테스트 계정 정보 (연관 배열)
     *
     * @example
     * // 테스트 계정 조회
     * $accounts = config()->testAccounts();
     * $apple = $accounts['apple'];
     * // ['first_name' => 'Apple', 'email' => 'apple@test.com', 'phone_number' => '+11234567890', 'password' => '12345a,*']
     */
    public function testAccounts(): array
    {
        return [
            'apple' => [
                'first_name' => 'Apple',
                'email' => 'apple@test.com',
                'phone_number' => '+11234567890',
                'password' => '12345a,*',
            ],
            'banana' => [
                'first_name' => 'Banana',
                'email' => 'banana@test.com',
                'phone_number' => '+11234567891',
                'password' => '12345a,*',
            ],
            'cherry' => [
                'first_name' => 'Cherry',
                'email' => 'cherry@test.com',
                'phone_number' => '+11234567892',
                'password' => '12345a,*',
            ],
            'durian' => [
                'first_name' => 'Durian',
                'email' => 'durian@test.com',
                'phone_number' => '+11234567893',
                'password' => '12345a,*',
            ],
            'elderberry' => [
                'first_name' => 'Elderberry',
                'email' => 'elderberry@test.com',
                'phone_number' => '+11234567894',
                'password' => '12345a,*',
            ],
            'fig' => [
                'first_name' => 'Fig',
                'email' => 'fig@test.com',
                'phone_number' => '+11234567895',
                'password' => '12345a,*',
            ],
            'grape' => [
                'first_name' => 'Grape',
                'email' => 'grape@test.com',
                'phone_number' => '+11234567896',
                'password' => '12345a,*',
            ],
            'honeydew' => [
                'first_name' => 'Honeydew',
                'email' => 'honeydew@test.com',
                'phone_number' => '+11234567897',
                'password' => '12345a,*',
            ],
            'jackfruit' => [
                'first_name' => 'Jackfruit',
                'email' => 'jackfruit@test.com',
                'phone_number' => '+11234567898',
                'password' => '12345a,*',
            ],
            'kiwi' => [
                'first_name' => 'Kiwi',
                'email' => 'kiwi@test.com',
                'phone_number' => '+11234567899',
                'password' => '12345a,*',
            ],
            'lemon' => [
                'first_name' => 'Lemon',
                'email' => 'lemon@test.com',
                'phone_number' => '+11234567900',
                'password' => '12345a,*',
            ],
            'mango' => [
                'first_name' => 'Mango',
                'email' => 'mango@test.com',
                'phone_number' => '+11234567901',
                'password' => '12345a,*',
            ],
        ];
    }

    public function toArray(): array
    {
        return [
            'login' => $this->login ? $this->login->toArray() : null,
            'api' => $this->api ? $this->api->toArray() : null,
            'upload_path' => $this->upload_path,
            'test' => $this->test->toArray(),
            'pages_without_footer' => $this->pages_without_footer,
            'categories' => $this->categories->toArray(),
            'href' => $this->href->toArray(),
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
        $config->pages_without_footer = [
            '/page/index.php',
            '/page/post/list.php'
        ];
        $config->categories = category();

        $config->href = new HrefConfig(
            user_login: href()->user->login,
        );
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

function get_thumbnail_url(): string
{
    return '/thumbnail.php';
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
