<?php
// TOOD: 여기서 부터, v5 의 url.ts 를 보고 복사 할 것.

use Google\ApiCore\Page;

class PostListHref
{


    public string $community = "/post/list?post_id=freetalk";
    public string $discussion = "/post/list?post_id=freetalk&category=discussion";
    public string $encyclopedia = "/post/list?post_id=freetalk&category=백과";
    public string $angeles = "/post/region?region=앙헬레스";
    public string $manila = "/post/region?region=마닐라";
    public string $cebu = "/post/region?region=세부";
    public string $business = "/post/list?post_id=business";
    public string $caution = "/post/list?post_id=caution";
    public string $greeting = "/post/list?post_id=greeting";
    public string $hobby = "/post/list?post_id=freetalk&category=취미";
    public string $job = "/post/list?post_id=wanted";
    public string $helper = "/post/list?post_id=jobs";
    public string $info = "/post/list?post_id=freetalk&category=info";
    public string $kophil = "/post/list?post_id=freetalk&category=코필커플";
    public string $kopino = "/post/list?post_id=freetalk&category=코피노";
    public string $immigration = "/post/list?post_id=freetalk&category=이민";
    public string $lifeshot = "/post/list?post_id=freetalk&category=사진";
    public string $life_tips = "/post/list?post_id=freetalk&category=생활의팁";
    public string $lookfor = "/post/list?post_id=lookfor";
    public string $lost = "/post/list?post_id=freetalk&category=행방불명";
    public string $momcafe = "/post/list?post_id=momcafe";
    public string $marriage = "/post/list?post_id=freetalk&category=국제결혼";
    public string $meetup = "/post/list?post_id=freetalk&category=모임";
    public string $column = "/post/list?post_id=freetalk&category=column";
    public string $muckbang = "/post/list?post_id=freetalk&category=먹방";
    public string $reports = "/post/list?post_id=nature";
    public string $newcomer = "/post/list?post_id=newcomer";
    // 기존 필고에는 뉴스 게시판이 "속보", "마닐라/세부/앙헬레스 뉴스", "플러스친구" 등 여러개로 나위어져 있는데 하나로 합친다.
    public string $news = "/post/list?post_id=freetalk&category=뉴스";

    // 속보게시판
    public string $headline = "/post/list?post_id=news";

    // 기존 필고 게시판 카테고리 형식을 우선 따르고 나중에 통합한다.
    public string $qna = "/post/list?post_id=qna";
    public string $passport = "/post/list?post_id=qna&category=여권/비자";
    public string $reminders = "/post/list?post_id=freetalk&category=공지사항";
    public string $school = "/post/list?post_id=school"; /// caution"; /&category= TODO: Confirm with MR Song
    public string $story = "/post/list?post_id=freetalk&category=경험담";
    public string $learn = "/post/list?post_id=freetalk&category=공부";
    public string $travel = "/post/list?post_id=travel";
    public string $travel_recommandation = "/post/list?post_id=travel_good";
    public string $weather = "/post/list?post_id=freetalk&category=태풍";
    public string $youtube = "/post/list?post_id=youtube";

    // buy and sell

    public string $buyandsell = '/post/list?post_id=buyandsell';
    public string $boarding_house = '/post/list?post_id=boarding_house';
    public string $business_partner = '/post/list?post_id=buyandsell&category=사업/동업구함';
    public string $computer = '/post/list?post_id=buyandsell&category=컴퓨터/인터넷';
    public string $currency_exchange = '/post/list?post_id=buyandsell&category=페소환전';
    public string $food_delivery = '/post/list?post_id=food_delivery';
    public string $handphone = '/post/list?post_id=buyandsell&category=핸드폰';
    public string $hotel = '/post/list?post_id=buyandsell&category=호텔';
    public string $gadgets = '/post/list?post_id=buyandsell&category=가전/생활용품';
    public string $golf = '/post/list?post_id=buyandsell&category=골프';
    public string $promotion = '/post/list?post_id=buyandsell&category=promotion';
    public string $personal_buyandsell = '/post/list?post_id=buyandsell&category=개인장터';
    public string $realestate = '/post/list?post_id=real_estate';
    public string $rent_house = '/post/list?post_id=buyandsell&category=주택임대';
    public string $rent_car = '/post/list?post_id=buyandsell&category=렌트카';
    public string $used_car = '/post/list?post_id=buyandsell&category=중고차';
    public string $restaurant = '/post/list?post_id=rest';
    public string $study = '/post/list?post_id=study';


    public string $blog = '/post/list?post_id=blog';
    public string $english_biz = '/post/list?post_id=english_biz';
    public string $temp = '/post/list?post_id=temp';
    public string $today = '/page/today';

    public string $massage = '/post/list?post_id=massage';
}

class CompanyHref
{
    public string    $home =  "/company/index";
    public     function category(string $category): string
    {
        return "/company/category?category=$category";
    }
    public function view(int $idx): string
    {
        return "/company/view?idx=$idx";
    }
    public string $my = "/company/my"; // idx is optional, if not provided; it will show all companies of the user.
    public string $register = "/company/register";
    public string $register_submit = "/company/register-submit";
    public function delete(int $idx): string
    {
        return "/company/delete?idx=$idx";
    }
    public function location(string $location): string
    {
        return "/company/category/all?location=$location";
    }
    public function admin(int|string $idx = ""): string
    {
        return "/admin/company/update?idx=$idx";
    }
}

class PostHref
{
    public PostListHref $list;


    public function __construct()
    {
        $this->list = new PostListHref();
    }

    public function search(string $query, int $page = 1)
    {
        return "/post/search?query=" . urlencode($query) . "&page=$page";
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



    public function list(int $idx_member, string $display_profile = 'yes'): string
    {
        return "/post/comment/list?idx_member=$idx_member&display_profile=$display_profile";
    }
    public function latest(): string
    {
        return "/post/latest-post-and-comment-list";
    }
}




class MenuHref
{
    public string $all = '/page/menu/all';
    public string $categories = '/page/menu/categories';
}

class PointHref
{
    public string $history = '/page/point/history';
    public function adv($params)
    {
        return "/page/point/adv?" . http_build_query($params);
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
}


class AdminHref
{
    public string $index = '/page/admin/index';
    public string $home = '/page/admin/index';
    public string $advertisement = '/page/admin/advertisement';
    public string $advertisement_create = '/page/admin/advertisement-create';
    public string $advertisement_edit_banner_submit = '/page/admin/advertisement-edit-banner-submit';

    public string $blocked_user_list = '/page/admin/blocked-user-list';
    public string $post_list = '/page/admin/post-list';
    public string $post_config_list = '/page/admin/post-config-list';
    public function post_config_edit(string $post_id): string
    {
        return "/page/admin/post-config-edit?post_id=$post_id";
    }
    public string $post_config_edit_submit = '/page/admin/post-config-edit-submit';
    public string $point_history = '/page/admin/point-history';

    public string $family_site = '/page/admin/family-site';
    public function family_site_edit(int $idx_company): string
    {
        return "/page/admin/family-site-edit?idx_company=$idx_company";
    }
    public string $family_site_edit_submit = '/page/admin/family-site-edit-submit';

    public function move_post(int $idx_post, bool $block = false, string $target_post_id = ''): string
    {
        return "/page/admin/move-post?idx_post=$idx_post&block=$block&target_post_id=$target_post_id";
    }
    public string $move_post_submit = '/page/admin/move-post-submit';

    public function change_massage_post_status_submit(int $idx_post, bool $approve): string
    {
        return "/modules/massage/massage-status-submit?idx_post=$idx_post&approve=$approve";
    }
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
    public CompanyHref $company;
    public MenuHref $menu;
    public PointHref $point;
    public UserHref $user;
    public HelpHref $help;
    public AdminHref $admin;

    public ChatHref $chat;


    public function __construct()
    {
        $this->post = new PostHref();
        $this->comment = new CommentHref();
        $this->friend = new FriendHref();
        $this->company = new CompanyHref();
        $this->menu = new MenuHref();
        $this->point = new PointHref();
        $this->user = new UserHref();
        $this->help = new HelpHref();
        $this->admin = new AdminHref();
        $this->chat = new ChatHref();
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
