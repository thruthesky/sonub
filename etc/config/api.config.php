<?php




// etc/config/app.config.php
class AppConfigApi
{


    public string $thumbnail_url;

    /**
     * @var array<string, string> $endpoints
     * 
     * - Purpose: API 엔드포인트 URL 목록
     *   - This list is used to store API endpoint URLs in the layout.php that provides the available endpoints. So the Vue.js can call the endpoints as
     *      - `func(appConfig.api.list_users, {...})`.
     */
    public array $endpoints = [];
    public function __construct()
    {

        $this->thumbnail_url = '/thumbnail.php';


        $this->endpoints = [
            'delete_file_from_post' => 'Delete a uploaded file from the post',
            'get_posts_from_feed_entries' => 'Getting a post from the feed',
            'delete_comment' => 'Delete a comment',
            'get_comments' => "Get more comments",
            'create_comment' => 'Create a new comment under a post.',
            'delete_post' => 'Delete a post',
            'create_post' => 'Create a new post.',
            'file_upload' => 'File upload',
            'file_delete' => 'File delete',
            'get_post' => 'Get a post. This is used to retrieve a single post. Additionally, the with_users: boolean and with_comments: boolean options can be used to include user information and comment information.',
            'list_posts' => 'List posts with pagination support.',
            'list_users' => 'List users with pagination support.',
            'login_with_firebase' => 'Login using Firebase UID.',
            'update_user_profile' => 'Alias of update_my_profile',
            'update_my_profile' => 'Update the profile of the currently logged-in user.',
            'update_comment' => 'Update an existing comment.',
            'update_post' => 'Update an existing post.',
            'request_friend' => 'Requesting a friend to the another user',
            'reject_friend' => 'Rejecting a friend request',
            'accept_friend' => 'Accepting the friend request',
            'remove_friend' => "Removing the friendship",
            'set_language' => "Change the language of sonub website"
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
