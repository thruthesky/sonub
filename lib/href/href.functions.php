<?php
// TOOD: 여기서 부터, v5 의 url.ts 를 보고 복사 할 것.

use Google\ApiCore\Page;

class PostListHref
{


    public string $community = "/post/list.php?post_id=freetalk";
    public string $discussion = "/post/list.php?post_id=freetalk&category=discussion";
    public string $encyclopedia = "/post/list.php?post_id=freetalk&category=백과";
    public string $angeles = "/post/region.php?region=앙헬레스";
    public string $manila = "/post/region.php?region=마닐라";
    public string $cebu = "/post/region.php?region=세부";
    public string $business = "/post/list.php?post_id=business";
    public string $caution = "/post/list.php?post_id=caution";
    public string $greeting = "/post/list.php?post_id=greeting";
    public string $hobby = "/post/list.php?post_id=freetalk&category=취미";
    public string $job = "/post/list.php?post_id=wanted";
    public string $helper = "/post/list.php?post_id=jobs";
    public string $info = "/post/list.php?post_id=freetalk&category=info";
    public string $kophil = "/post/list.php?post_id=freetalk&category=코필커플";
    public string $kopino = "/post/list.php?post_id=freetalk&category=코피노";
    public string $immigration = "/post/list.php?post_id=freetalk&category=이민";
    public string $lifeshot = "/post/list.php?post_id=freetalk&category=사진";
    public string $life_tips = "/post/list.php?post_id=freetalk&category=생활의팁";
    public string $lookfor = "/post/list.php?post_id=lookfor";
    public string $lost = "/post/list.php?post_id=freetalk&category=행방불명";
    public string $momcafe = "/post/list.php?post_id=momcafe";
    public string $marriage = "/post/list.php?post_id=freetalk&category=국제결혼";
    public string $meetup = "/post/list.php?post_id=freetalk&category=모임";
    public string $column = "/post/list.php?post_id=freetalk&category=column";
    public string $muckbang = "/post/list.php?post_id=freetalk&category=먹방";
    public string $reports = "/post/list.php?post_id=nature";
    public string $newcomer = "/post/list.php?post_id=newcomer";
    // 기존 필고에는 뉴스 게시판이 "속보", "마닐라/세부/앙헬레스 뉴스", "플러스친구" 등 여러개로 나위어져 있는데 하나로 합친다.
    public string $news = "/post/list.php?post_id=freetalk&category=뉴스";

    // 속보게시판
    public string $headline = "/post/list.php?post_id=news";

    // 기존 필고 게시판 카테고리 형식을 우선 따르고 나중에 통합한다.
    public string $qna = "/post/list.php?post_id=qna";
    public string $passport = "/post/list.php?post_id=qna&category=여권/비자";
    public string $reminders = "/post/list.php?post_id=freetalk&category=공지사항";
    public string $school = "/post/list.php?post_id=school"; /// caution"; /&category= TODO: Confirm with MR Song
    public string $story = "/post/list.php?post_id=freetalk&category=경험담";
    public string $learn = "/post/list.php?post_id=freetalk&category=공부";
    public string $travel = "/post/list.php?post_id=travel";
    public string $travel_recommandation = "/post/list.php?post_id=travel_good";
    public string $weather = "/post/list.php?post_id=freetalk&category=태풍";
    public string $youtube = "/post/list.php?post_id=youtube";

    // buy and sell

    public string $buyandsell = '/post/list.php?post_id=buyandsell';
    public string $boarding_house = '/post/list.php?post_id=boarding_house';
    public string $business_partner = '/post/list.php?post_id=buyandsell&category=사업/동업구함';
    public string $computer = '/post/list.php?post_id=buyandsell&category=컴퓨터/인터넷';
    public string $currency_exchange = '/post/list.php?post_id=buyandsell&category=페소환전';
    public string $food_delivery = '/post/list.php?post_id=food_delivery';
    public string $handphone = '/post/list.php?post_id=buyandsell&category=핸드폰';
    public string $hotel = '/post/list.php?post_id=buyandsell&category=호텔';
    public string $gadgets = '/post/list.php?post_id=buyandsell&category=가전/생활용품';
    public string $golf = '/post/list.php?post_id=buyandsell&category=골프';
    public string $promotion = '/post/list.php?post_id=buyandsell&category=promotion';
    public string $personal_buyandsell = '/post/list.php?post_id=buyandsell&category=개인장터';
    public string $realestate = '/post/list.php?post_id=real_estate';
    public string $rent_house = '/post/list.php?post_id=buyandsell&category=주택임대';
    public string $rent_car = '/post/list.php?post_id=buyandsell&category=렌트카';
    public string $used_car = '/post/list.php?post_id=buyandsell&category=중고차';
    public string $restaurant = '/post/list.php?post_id=rest';
    public string $study = '/post/list.php?post_id=study';


    public string $blog = '/post/list.php?post_id=blog';
    public string $english_biz = '/post/list.php?post_id=english_biz';
    public string $temp = '/post/list.php?post_id=temp';
    public string $today = '/page/today.php';

    public string $massage = '/post/list.php?post_id=massage';
}

class CompanyHref
{
    public string    $home =  "/company/index.php";
    public     function category(string $category): string
    {
        return "/company/category.php?category=$category";
    }
    public function view(int $idx): string
    {
        return "/company/view.php?idx=$idx";
    }
    public string $my = "/company/my.php"; // idx is optional, if not provided; it will show all companies of the user.
    public string $register = "/company/register.php";
    public string $register_submit = "/company/register-submit.php";
    public function delete(int $idx): string
    {
        return "/company/delete.php?idx=$idx";
    }
    public function location(string $location): string
    {
        return "/company/category/all.php?location=$location";
    }
    public function admin(int|string $idx = ""): string
    {
        return "/admin/company/update.php?idx=$idx";
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
        return "/post/search.php?query=" . urlencode($query) . "&page=$page";
    }

    public function latest(): string
    {
        return "/post/latest-post-and-comment-list.php";
    }
}

class CommentHref
{
    // public function create(PostParams $params): string
    // {
    //     return "/post/comment/create.php?" . $params->buildQuery();
    // }

    public string $create_submit = "/post/comment/create-submit.php";
    public string $update_submit = "/post/comment/update-submit.php";



    public function list(int $idx_member, string $display_profile = 'yes'): string
    {
        return "/post/comment/list.php?idx_member=$idx_member&display_profile=$display_profile";
    }
    public function latest(): string
    {
        return "/post/latest-post-and-comment-list.php";
    }
}




class MenuHref
{
    public string $all = '/page/menu/all.php';
    public string $categories = '/page/menu/categories.php';
}

class PointHref
{
    public string $history = '/page/point/history.php';
    public function adv($params)
    {
        return "/page/point/adv.php?" . http_build_query($params);
    }
}

class SocialHref
{
    public string $help = '/social/help.php';
    public string $settings = '/social/settings.php';


    public function view(int $idx): string
    {
        return "/social/view.php?idx=$idx";
    }
    public function my(): string
    {
        return "/social/my.php";
    }
}



class UserHref
{
    public string $account_delete = '/page/help/account_removal.php';
    public string $profile = '/user/profile.php';
    public string $profile_edit_submit = '/user/profile-edit-submit.php';

    public string $settings = '/user/settings.php';
    public string $merge_account = '/user/merge-account.php';
    public string $merge_account_submit = '/user/merge-account-submit.php';

    public string $login = '/user/login';
    public string $login_success = '/user/login-success.php';
    public string $logout = '/user/logout.php';


    // TODO: implement this in the future
    public string $notifications = '/user/notifications.php';


    // TODO: implement this in the future
    public string $friends = '/user/friends.php';

    // TODO: implement this in the future
    public string $bookmarks = '/user/bookmarks.php';



    public string $list = '/user/list.php';

    // 공개 프로필 보기
    // 공개 프로필 보기는 글 목록이 된다. 그래서 특정 사용자의 글 보기를 할 때에도 쓰면 된다.
    public function public_profile(int $idx_member): string
    {
        return '/user/public-profile.php?idx_member=' . $idx_member;
    }
    public function my_public_profile(): string
    {
        return '/user/public-profile.php';
    }
    // TODO: implement this in the future
    public function comments(int $idx_member): string
    {
        return '/user/comments.php?idx_member=' . $idx_member;
    }
    public function photos(int $idx_member): string
    {
        return '/user/photos.php?idx_member=' . $idx_member;
    }
}



class HelpHref
{
    public string $howto = '/page/help/howto.php';
    public string $account_removal = '/page/help/account_removal.php';
    public string $guideline = '/page/help/guideline.php';
    public string $terms_and_conditions = '/page/help/terms-and-conditions.php';
    public string $privacy = '/page/help/privacy.php';
}


class AdminHref
{
    public string $index = '/page/admin/index.php';
    public string $home = '/page/admin/index.php';
    public string $advertisement = '/page/admin/advertisement.php';
    public string $advertisement_create = '/page/admin/advertisement-create.php';
    public string $advertisement_edit_banner_submit = '/page/admin/advertisement-edit-banner-submit.php';

    public string $blocked_user_list = '/page/admin/blocked-user-list.php';
    public string $post_list = '/page/admin/post-list.php';
    public string $post_config_list = '/page/admin/post-config-list.php';
    public function post_config_edit(string $post_id): string
    {
        return "/page/admin/post-config-edit.php?post_id=$post_id";
    }
    public string $post_config_edit_submit = '/page/admin/post-config-edit-submit.php';
    public string $point_history = '/page/admin/point-history.php';

    public string $family_site = '/page/admin/family-site.php';
    public function family_site_edit(int $idx_company): string
    {
        return "/page/admin/family-site-edit.php?idx_company=$idx_company";
    }
    public string $family_site_edit_submit = '/page/admin/family-site-edit-submit.php';

    public function move_post(int $idx_post, bool $block = false, string $target_post_id = ''): string
    {
        return "/page/admin/move-post.php?idx_post=$idx_post&block=$block&target_post_id=$target_post_id";
    }
    public string $move_post_submit = '/page/admin/move-post-submit.php';

    public function change_massage_post_status_submit(int $idx_post, bool $approve): string
    {
        return "/modules/massage/massage-status-submit.php?idx_post=$idx_post&approve=$approve";
    }
}


class ChatHref
{
    public string $open_chat_rooms = '/chat/rooms.php?type=open';
    public string $rooms = '/chat/rooms.php';
    public function room(string $firebase_uid): string
    {
        return "/chat/rooms.php?id=$firebase_uid";
    }
}






class Href
{
    public string $home = '/';
    // @deprecated use href()->user()->login
    public string $login = '/user/login.php';
    // @deprecated use href()->user()->login_success
    // public string $login_success = '/user/login_success.php';
    // @deprecated use href()->user()->logout
    public string $logout = '/user/logout.php';

    public string $search = '/post/search.php';



    public string $ai = '/page/ai/index.php';




    public PostHref $post;
    public CommentHref $comment;
    public SocialHref $social;
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
        $this->social = new SocialHref();
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
