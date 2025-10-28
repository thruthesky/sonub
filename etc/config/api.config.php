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
            'create_comment' => 'Create a new comment under a post.',
            'create_post' => 'Create a new post.',
            'file_upload' => 'File upload',
            'file_delete' => 'File delete',
            'get_post' => 'Get a post. This is used to retrieve a single post. Additionally, the with_users: boolean and with_comments: boolean options can be used to include user information and comment information.',
            'list_posts' => 'List posts with pagination support.',
            'list_users' => 'List users with pagination support.',
            'login_with_firebase' => 'Login using Firebase UID.',
            'update_my_profile' => 'Update the profile of the currently logged-in user.',
            'update_comment' => 'Update an existing comment.',
            'update_post' => 'Update an existing post.',
        ];
    }

    public function toArray(): array
    {
        $endpoints = [
            'thumbnail_url' => $this->thumbnail_url,
        ];

        foreach ($this->endpoints as $k => $v) {
            $endpoints[$k] = '/api.php?func=' . $k;
        }

        return $endpoints;
    }

    public function allowed_functions(): array
    {
        return array_keys($this->endpoints);
    }
}
