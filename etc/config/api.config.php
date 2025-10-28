<?php




// etc/config/app.config.php
class AppConfigApi
{


    public string $thumbnail_url;

    /**
     * @var array<string, string> $endpoints
     */
    public array $endpoints = [];
    public function __construct()
    {

        $this->thumbnail_url = '/thumbnail.php';

        $this->endpoints = [
            'file_upload' => '파일 업로드',
            'file_delete' => '파일 삭제',
            'get_post' => '게시물 가져오기. 게시글 하나를 가져올 때 사용합니다. 추가적으로 with_users: boolean, with_comments: boolean 옵션으로 사용자 정보와 코멘트 정보를 같이 올 수 있습니다.',
        ];
    }

    public function toArray(): array
    {
        $endpoints = [
            'thumbnail_url' => $this->thumbnail_url,
        ];

        foreach ($this->endpoints as $v) {
            $endpoints[$v] = '/api.php?func=' . $v;
        }

        return $endpoints;
    }

    public function allowed_functions(): array
    {
        return array_keys($this->endpoints);
    }
}
