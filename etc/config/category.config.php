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
    $categories = new CategoryList();

    // ========================================
    // 1차 카테고리: 커뮤니티
    // ========================================
    $community = new CategoryGroup('community', tr(['en' => 'Community', 'ko' => '커뮤니티']));
    $community->add(new CategoryModel('general', tr(['en' => 'General', 'ko' => '일반'])));
    $community->add(new CategoryModel('news', tr(['en' => 'News', 'ko' => '뉴스'])));
    $community->add(new CategoryModel('social-issues', tr(['en' => 'Social Issues', 'ko' => '사회 문제'])));
    $community->add(new CategoryModel('politics', tr(['en' => 'Politics', 'ko' => '정치'])));
    $community->add(new CategoryModel('culture', tr(['en' => 'Culture', 'ko' => '문화'])));
    $community->add(new CategoryModel('religion', tr(['en' => 'Religion', 'ko' => '종교'])));
    $categories->addGroup($community);

    // ========================================
    // 1차 카테고리: 사고팔기
    // ========================================
    $buyandsell = new CategoryGroup('buyandsell', tr(['en' => 'Buy & Sell', 'ko' => '사고팔기']));
    $buyandsell->add(new CategoryModel('electronics', tr(['en' => 'Electronics', 'ko' => '전자제품'])));
    $buyandsell->add(new CategoryModel('fashion', tr(['en' => 'Fashion', 'ko' => '패션'])));
    $buyandsell->add(new CategoryModel('furniture', tr(['en' => 'Furniture', 'ko' => '가구'])));
    $buyandsell->add(new CategoryModel('books', tr(['en' => 'Books', 'ko' => '책'])));
    $buyandsell->add(new CategoryModel('sports-equipment', tr(['en' => 'Sports Equipment', 'ko' => '스포츠용품'])));
    $buyandsell->add(new CategoryModel('vehicles', tr(['en' => 'Vehicles', 'ko' => '차량'])));
    $buyandsell->add(new CategoryModel('real-estate', tr(['en' => 'Real Estate', 'ko' => '부동산'])));
    $categories->addGroup($buyandsell);

    // ========================================
    // 1차 카테고리: 뉴스/미디어
    // ========================================
    $news = new CategoryGroup('news', tr(['en' => 'News & Media', 'ko' => '뉴스/미디어']));
    $news->add(new CategoryModel('sports', tr(['en' => 'Sports', 'ko' => '스포츠'])));
    $news->add(new CategoryModel('entertainment', tr(['en' => 'Entertainment', 'ko' => '엔터테인먼트'])));
    $news->add(new CategoryModel('technology', tr(['en' => 'Technology', 'ko' => '기술'])));
    $news->add(new CategoryModel('business', tr(['en' => 'Business', 'ko' => '비즈니스'])));
    $news->add(new CategoryModel('economics', tr(['en' => 'Economics', 'ko' => '경제'])));
    $news->add(new CategoryModel('movies', tr(['en' => 'Movies', 'ko' => '영화'])));
    $news->add(new CategoryModel('music', tr(['en' => 'Music', 'ko' => '음악'])));
    $categories->addGroup($news);

    // ========================================
    // 1차 카테고리: 여행
    // ========================================
    $travel = new CategoryGroup('travel', tr(['en' => 'Travel', 'ko' => '여행']));
    $travel->add(new CategoryModel('domestic', tr(['en' => 'Domestic', 'ko' => '국내여행'])));
    $travel->add(new CategoryModel('international', tr(['en' => 'International', 'ko' => '해외여행'])));
    $travel->add(new CategoryModel('food', tr(['en' => 'Food', 'ko' => '음식'])));
    $travel->add(new CategoryModel('accommodation', tr(['en' => 'Accommodation', 'ko' => '숙박'])));
    $travel->add(new CategoryModel('photography', tr(['en' => 'Photography', 'ko' => '사진'])));
    $travel->add(new CategoryModel('nature', tr(['en' => 'Nature', 'ko' => '자연'])));
    $categories->addGroup($travel);

    // ========================================
    // 1차 카테고리: 라이프스타일
    // ========================================
    $lifestyle = new CategoryGroup('lifestyle', tr(['en' => 'Lifestyle', 'ko' => '라이프스타일']));
    $lifestyle->add(new CategoryModel('health', tr(['en' => 'Health', 'ko' => '건강'])));
    $lifestyle->add(new CategoryModel('fitness', tr(['en' => 'Fitness', 'ko' => '운동'])));
    $lifestyle->add(new CategoryModel('beauty', tr(['en' => 'Beauty', 'ko' => '뷰티'])));
    $lifestyle->add(new CategoryModel('cooking', tr(['en' => 'Cooking', 'ko' => '요리'])));
    $lifestyle->add(new CategoryModel('pets', tr(['en' => 'Pets', 'ko' => '반려동물'])));
    $lifestyle->add(new CategoryModel('parenting', tr(['en' => 'Parenting', 'ko' => '육아'])));
    $lifestyle->add(new CategoryModel('relationships', tr(['en' => 'Relationships', 'ko' => '관계'])));
    $categories->addGroup($lifestyle);

    // ========================================
    // 1차 카테고리: 교육/자기계발
    // ========================================
    $education = new CategoryGroup('education', tr(['en' => 'Education', 'ko' => '교육/자기계발']));
    $education->add(new CategoryModel('school', tr(['en' => 'School', 'ko' => '학교'])));
    $education->add(new CategoryModel('language', tr(['en' => 'Language', 'ko' => '언어'])));
    $education->add(new CategoryModel('self-improvement', tr(['en' => 'Self-Improvement', 'ko' => '자기계발'])));
    $education->add(new CategoryModel('psychology', tr(['en' => 'Psychology', 'ko' => '심리학'])));
    $education->add(new CategoryModel('philosophy', tr(['en' => 'Philosophy', 'ko' => '철학'])));
    $education->add(new CategoryModel('careers', tr(['en' => 'Careers', 'ko' => '커리어'])));
    $categories->addGroup($education);

    // ========================================
    // 1차 카테고리: 취미/여가
    // ========================================
    $hobbies = new CategoryGroup('hobbies', tr(['en' => 'Hobbies', 'ko' => '취미/여가']));
    $hobbies->add(new CategoryModel('gaming', tr(['en' => 'Gaming', 'ko' => '게임'])));
    $hobbies->add(new CategoryModel('art', tr(['en' => 'Art', 'ko' => '예술'])));
    $hobbies->add(new CategoryModel('crafts', tr(['en' => 'Crafts', 'ko' => '공예'])));
    $hobbies->add(new CategoryModel('gardening', tr(['en' => 'Gardening', 'ko' => '원예'])));
    $hobbies->add(new CategoryModel('collecting', tr(['en' => 'Collecting', 'ko' => '수집'])));
    $hobbies->add(new CategoryModel('reading', tr(['en' => 'Reading', 'ko' => '독서'])));
    $categories->addGroup($hobbies);

    // ========================================
    // 1차 카테고리: 금융/투자
    // ========================================
    $finance = new CategoryGroup('finance', tr(['en' => 'Finance', 'ko' => '금융/투자']));
    $finance->add(new CategoryModel('personal-finance', tr(['en' => 'Personal Finance', 'ko' => '개인 금융'])));
    $finance->add(new CategoryModel('investing', tr(['en' => 'Investing', 'ko' => '투자'])));
    $finance->add(new CategoryModel('stocks', tr(['en' => 'Stocks', 'ko' => '주식'])));
    $finance->add(new CategoryModel('cryptocurrency', tr(['en' => 'Cryptocurrency', 'ko' => '암호화폐'])));
    $finance->add(new CategoryModel('savings', tr(['en' => 'Savings', 'ko' => '저축'])));
    $finance->add(new CategoryModel('insurance', tr(['en' => 'Insurance', 'ko' => '보험'])));
    $categories->addGroup($finance);

    // ========================================
    // 1차 카테고리: 과학/환경
    // ========================================
    $science = new CategoryGroup('science', tr(['en' => 'Science', 'ko' => '과학/환경']));
    $science->add(new CategoryModel('physics', tr(['en' => 'Physics', 'ko' => '물리'])));
    $science->add(new CategoryModel('biology', tr(['en' => 'Biology', 'ko' => '생물'])));
    $science->add(new CategoryModel('chemistry', tr(['en' => 'Chemistry', 'ko' => '화학'])));
    $science->add(new CategoryModel('astronomy', tr(['en' => 'Astronomy', 'ko' => '천문'])));
    $science->add(new CategoryModel('environment', tr(['en' => 'Environment', 'ko' => '환경'])));
    $science->add(new CategoryModel('climate', tr(['en' => 'Climate', 'ko' => '기후'])));
    $categories->addGroup($science);

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
