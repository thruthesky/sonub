<?php

/**
 * 2차 카테고리 모델
 *
 * 개별 카테고리 항목을 나타냅니다.
 */
class CategoryModel
{
    public string $category;  // 카테고리 고유 식별자 (예: 'general')
    public string $name;            // 표시 이름 (다국어 지원)

    public function __construct(string $category, string $name)
    {
        $this->category = $category;
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'category' => $this->category,
            'name' => $this->name,
        ];
    }
}

/**
 * 1차 카테고리 그룹
 *
 * 2차 카테고리들을 담는 컨테이너입니다.
 * ArrayAccess 인터페이스를 구현하여 배열처럼 접근할 수 있습니다.
 *
 * @example
 * $group = new CategoryGroup('community', '커뮤니티');
 * $group->add(new CategoryModel('general', '일반'));
 * $group->add(new CategoryModel('news', '뉴스'));
 * echo $group[0]->category; // 'general'
 */
class CategoryGroup implements ArrayAccess
{
    public string $group_name;      // 그룹 고유 식별자 (예: 'community')
    public string $display_name;    // 그룹 표시 이름 (예: '커뮤니티')
    /** @var CategoryModel[] */
    private array $categories = [];

    public function __construct(string $group_name, string $display_name)
    {
        $this->group_name = $group_name;
        $this->display_name = $display_name;
    }

    /**
     * 2차 카테고리 추가
     */
    public function add(CategoryModel $category): void
    {
        $this->categories[] = $category;
    }

    /**
     * 모든 2차 카테고리 반환
     *
     * @return CategoryModel[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * 배열로 변환
     */
    public function toArray(): array
    {
        return [
            'group_name' => $this->group_name,
            'display_name' => $this->display_name,
            'categories' => array_map(fn($cat) => $cat->toArray(), $this->categories),
        ];
    }

    // ========================================
    // ArrayAccess 인터페이스 구현
    // ========================================

    /**
     * 인덱스로 2차 카테고리 접근
     *
     * @example $categories->community[0]->category
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->categories[$offset]);
    }

    /**
     * 인덱스로 2차 카테고리 가져오기
     */
    public function offsetGet(mixed $offset): ?CategoryModel
    {
        return $this->categories[$offset] ?? null;
    }

    /**
     * 인덱스로 2차 카테고리 설정 (사용 금지)
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception('직접 설정할 수 없습니다. add() 메서드를 사용하세요.');
    }

    /**
     * 인덱스로 2차 카테고리 삭제 (사용 금지)
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new Exception('카테고리를 삭제할 수 없습니다.');
    }
}

/**
 * 전체 카테고리 목록
 *
 * 1차 카테고리(그룹)들을 담는 최상위 컨테이너입니다.
 *
 * @property CategoryGroup $community 커뮤니티
 * @property CategoryGroup $buyandsell 사고팔기
 * @property CategoryGroup $news 뉴스
 * @property CategoryGroup $travel 여행
 *
 * @example
 * $categories = new CategoryList();
 * $categories->community->add(new CategoryModel('general', '일반'));
 * echo $categories->community[0]->category; // 'general'
 */
class CategoryList
{
    /** @var CategoryGroup[] */
    private array $groups = [];

    /**
     * 1차 카테고리 그룹 추가
     */
    public function addGroup(CategoryGroup $group): void
    {
        $this->groups[$group->group_name] = $group;
    }

    /**
     * 1차 카테고리 그룹에 동적으로 접근
     *
     * @example $categories->community
     */
    public function __get(string $name): ?CategoryGroup
    {
        return $this->groups[$name] ?? null;
    }

    /**
     * 모든 1차 카테고리 그룹 반환
     *
     * @return CategoryGroup[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * 배열로 변환
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->groups as $group_name => $group) {
            $result[$group_name] = $group->toArray();
        }
        return $result;
    }
}
/**
 * 모든 카테고리 목록 반환
 *
 * 1차 카테고리(그룹)와 2차 카테고리(항목)로 구성됩니다.
 *
 * @return CategoryList
 *
 * @example 기본 사용법
 * $categories = get_all_categories();
 *
 * // 1차 카테고리의 2차 카테고리 접근
 * foreach ($categories->community->getCategories() as $cat) {
 *     echo $cat->name;
 * }
 *
 * // 인덱스로 접근
 * echo $categories->community[0]->category; // 'general'
 *
 * @example 배열로 변환
 * $array = $categories->toArray();
 */
function get_all_categories(): CategoryList
{
    static $categories = null;
    if ($categories !== null) {
        return $categories;
    }

    $categories = new CategoryList();


    // ========================================
    // 커뮤니티
    // ========================================
    $community = new CategoryGroup('community', tr(['ko' => '커뮤니티', 'en' => 'Community', 'ja' => 'コミュニティ', 'zh' => '社区']));
    $community->add(new CategoryModel('story', tr(['ko' => '이야기', 'en' => 'Story', 'ja' => 'ストーリー', 'zh' => '故事'])));
    $community->add(new CategoryModel('relationships', tr(['ko' => '관계', 'en' => 'Relationships', 'ja' => '人間関係', 'zh' => '关系'])));
    $community->add(new CategoryModel('fitness', tr(['ko' => '운동', 'en' => 'Fitness', 'ja' => 'フィットネス', 'zh' => '健身'])));
    $community->add(new CategoryModel('beauty', tr(['ko' => '뷰티', 'en' => 'Beauty', 'ja' => 'ビューティー', 'zh' => '美容'])));
    $community->add(new CategoryModel('cooking', tr(['ko' => '요리', 'en' => 'Cooking', 'ja' => '料理', 'zh' => '烹饪'])));
    $community->add(new CategoryModel('pets', tr(['ko' => '반려동물', 'en' => 'Pets', 'ja' => 'ペット', 'zh' => '宠物'])));
    $community->add(new CategoryModel('parenting', tr(['ko' => '육아', 'en' => 'Parenting', 'ja' => '育児', 'zh' => '育儿'])));
    $categories->addGroup($community);


    // ========================================
    // 장터
    // ========================================
    $buyandsell = new CategoryGroup('buyandsell', tr(['ko' => '장터', 'en' => 'Buy & Sell', 'ja' => 'マーケット', 'zh' => '市场']));
    $buyandsell->add(new CategoryModel('electronics', tr(['ko' => '전자제품', 'en' => 'Electronics', 'ja' => '電子機器', 'zh' => '电子产品'])));
    $buyandsell->add(new CategoryModel('fashion', tr(['ko' => '패션', 'en' => 'Fashion', 'ja' => 'ファッション', 'zh' => '时尚'])));
    $buyandsell->add(new CategoryModel('furniture', tr(['ko' => '가구', 'en' => 'Furniture', 'ja' => '家具', 'zh' => '家具'])));
    $buyandsell->add(new CategoryModel('books', tr(['ko' => '책', 'en' => 'Books', 'ja' => '書籍', 'zh' => '书籍'])));
    $buyandsell->add(new CategoryModel('sports-equipment', tr(['ko' => '스포츠용품', 'en' => 'Sports Equipment', 'ja' => 'スポーツ用品', 'zh' => '体育用品'])));
    $buyandsell->add(new CategoryModel('vehicles', tr(['ko' => '차량', 'en' => 'Vehicles', 'ja' => '車両', 'zh' => '车辆'])));
    $buyandsell->add(new CategoryModel('real-estate', tr(['ko' => '부동산', 'en' => 'Real Estate', 'ja' => '不動産', 'zh' => '房地产'])));
    $categories->addGroup($buyandsell);

    // ========================================
    // 뉴스
    // ========================================
    $news = new CategoryGroup('news', tr(['ko' => '뉴스', 'en' => 'News', 'ja' => 'ニュース', 'zh' => '新闻']));
    $news->add(new CategoryModel('technology', tr(['ko' => '기술', 'en' => 'Technology', 'ja' => 'テクノロジー', 'zh' => '技术'])));
    $news->add(new CategoryModel('business', tr(['ko' => '비즈니스', 'en' => 'Business', 'ja' => 'ビジネス', 'zh' => '商业'])));
    $news->add(new CategoryModel('ai', tr(['ko' => '인공지능', 'en' => 'AI', 'ja' => 'AI', 'zh' => '人工智能'])));
    $news->add(new CategoryModel('movies', tr(['ko' => '영화', 'en' => 'Movies', 'ja' => '映画', 'zh' => '电影'])));
    $news->add(new CategoryModel('drama', tr(['ko' => '드라마', 'en' => 'Drama', 'ja' => 'ドラマ', 'zh' => '电视剧'])));
    $news->add(new CategoryModel('music', tr(['ko' => '음악', 'en' => 'Music', 'ja' => '音楽', 'zh' => '音乐'])));
    $categories->addGroup($news);


    // ========================================
    // 부동산
    // ========================================
    $realestate = new CategoryGroup('realestate', tr(['ko' => '부동산', 'en' => 'Real Estate', 'ja' => '不動産', 'zh' => '房地产']));
    $realestate->add(new CategoryModel('buy', tr(['ko' => '구매', 'en' => 'Buy', 'ja' => '購入', 'zh' => '购买'])));
    $realestate->add(new CategoryModel('sell', tr(['ko' => '판매', 'en' => 'Sell', 'ja' => '売却', 'zh' => '出售'])));
    $realestate->add(new CategoryModel('rent', tr(['ko' => '임대', 'en' => 'Rent', 'ja' => '賃貸', 'zh' => '租赁'])));
    $categories->addGroup($realestate);

    // ========================================
    // 구인구직
    // ========================================
    $jobs = new CategoryGroup('jobs', tr(['ko' => '구인구직', 'en' => 'Jobs', 'ja' => '求人', 'zh' => '招聘']));
    $jobs->add(new CategoryModel('full-time', tr(['ko' => '전일제', 'en' => 'Full Time', 'ja' => 'フルタイム', 'zh' => '全职'])));
    $jobs->add(new CategoryModel('part-time', tr(['ko' => '시간제', 'en' => 'Part Time', 'ja' => 'パートタイム', 'zh' => '兼职'])));
    $jobs->add(new CategoryModel('freelance', tr(['ko' => '프리랜서', 'en' => 'Freelance', 'ja' => 'フリーランス', 'zh' => '自由职业'])));
    $categories->addGroup($jobs);

    return $categories;
}


/**
 * 전역 카테고리 인스턴스 반환
 *
 * 싱글톤 패턴으로 한 번만 생성된 카테고리 목록을 재사용합니다.
 *
 * @return CategoryList
 */
function category(): CategoryList
{
    static $instance = null;
    if ($instance === null) {
        $instance = get_all_categories();
    }
    return $instance;
}
