<?php
// TOOD: 여기서 부터, v5 의 url.ts 를 보고 복사 할 것.




class AdvertisementHref
{
    public string $intro = '/page/adv/intro';
}

class CategoryHref
{


    public string $community = "/post/list?category=freetalk";
    public string $discussion = "/post/list?category=discussion";
}



class PostHref
{
    public CategoryHref $list;

    public string $categories = "/post/categories";

    public string $create = "/post/create";
    public string $create_submit = "/post/create-submit";

    public string $update_submit = "/post/update-submit";


    public function __construct()
    {
        $this->list = new CategoryHref();
    }

    public function list(int $page = 1, string $category = ''): string
    {
        $url = "/post/list?page=$page";
        if ($category) {
            $url .= "&category=" . urlencode($category);
        }
        return $url;
    }

    public function search(string $query, int $page = 1)
    {
        return "/post/search?query=" . urlencode($query) . "&page=$page";
    }

    public function view(int $id): string
    {
        return "/post/view?id=$id";
    }

    public function latest(): string
    {
        return "/post/latest-post-and-comment-list";
    }
}

class CommentHref
{
    // public function create(PostParams $params): string
    // {
    //     return "/post/comment/create?" . $params->buildQuery();
    // }

    public string $create_submit = "/post/comment/create-submit";
    public string $update_submit = "/post/comment/update-submit";



    public function list(int $user_id, string $display_profile = 'yes'): string
    {
        return "";
    }
    public function latest(): string
    {
        return "/post/latest-post-and-comment-list";
    }
}




class FriendHref
{
    public string $find_friend = '/friend/find-friend';
}



class UserHref
{
    public string $account_delete = '/page/help/account_removal';
    public string $profile = '/user/profile';
    public string $profile_edit = '/user/profile-edit';
    public string $profile_edit_submit = '/user/profile-edit-submit';

    public string $settings = '/user/settings';
    public string $merge_account = '/user/merge-account';
    public string $merge_account_submit = '/user/merge-account-submit';

    public string $login = '/user/login';
    public string $register = '/user/login';
    public string $login_success = '/user/login-success';
    public string $logout_submit = '/user/logout-submit?module=y';


    // TODO: implement this in the future
    public string $notifications = '/user/notifications';


    // TODO: implement this in the future
    public string $friends = '/user/friends';

    // TODO: implement this in the future
    public string $bookmarks = '/user/bookmarks';



    public string $list = '/user/list';

    // 공개 프로필 보기
    // 공개 프로필 보기는 글 목록이 된다. 그래서 특정 사용자의 글 보기를 할 때에도 쓰면 된다.
    public function public_profile(int $idx_member): string
    {
        return '/user/public-profile?idx_member=' . $idx_member;
    }
    public function my_public_profile(): string
    {
        return '/user/public-profile';
    }
    // TODO: implement this in the future
    public function comments(int $idx_member): string
    {
        return '/user/comments?idx_member=' . $idx_member;
    }
    public function photos(int $idx_member): string
    {
        return '/user/photos?idx_member=' . $idx_member;
    }
}



class HelpHref
{
    public string $howto = '/page/help/howto';
    public string $account_removal = '/page/help/account_removal';
    public string $guideline = '/page/help/guideline';
    public string $terms_and_conditions = '/page/help/terms-and-conditions';
    public string $privacy = '/page/help/privacy';
    public string $about = '/page/help/about';
}


class AdminHref
{
    public string $contact = '/page/admin/contact';
    public string $dashboard = '/page/admin/dashboard';
}


class ChatHref
{
    public string $open_chat_rooms = '/chat/rooms?type=open';
    public string $rooms = '/chat/rooms';
    public function room(string $firebase_uid): string
    {
        return "/chat/rooms?id=$firebase_uid";
    }
}



class MenuHref
{
    public string $intro = '/page/intro';
}



class Href
{
    public string $home = '/';
    // @deprecated use href()->user()->login
    public string $login = '/user/login';
    // @deprecated use href()->user()->login_success
    // public string $login_success = '/user/login_success';
    // @deprecated use href()->user()->logout
    // public string $logout = '/user/logout';

    public string $search = '/post/search';



    public string $ai = '/page/ai/index';




    public PostHref $post;
    public CommentHref $comment;
    public FriendHref $friend;
    public UserHref $user;
    public HelpHref $help;
    public AdminHref $admin;

    public ChatHref $chat;
    public AdvertisementHref $adv;
    public MenuHref $menu;


    public function __construct()
    {
        $this->post = new PostHref();
        $this->comment = new CommentHref();
        $this->friend = new FriendHref();
        $this->user = new UserHref();
        $this->help = new HelpHref();
        $this->admin = new AdminHref();
        $this->chat = new ChatHref();
        $this->adv = new AdvertisementHref();
        $this->menu = new MenuHref();
    }
}

function href(): Href
{
    static $href = null;
    if ($href === null) {
        $href = new Href();
    }
    return $href;
}
