<?php
/**
 * 카테고리 클래스 테스트
 *
 * 실행 방법: php tests/post/category.test.php
 */

// init.php 로드
require_once __DIR__ . '/../../init.php';

// category.php 로드
require_once __DIR__ . '/../../lib/post/category.php';

// 테스트 결과 저장
$test_results = [];
$test_count = 0;
$success_count = 0;

/**
 * 테스트 헬퍼 함수
 */
function test_category(string $test_name, callable $test_fn): void
{
    global $test_results, $test_count, $success_count;

    $test_count++;
    echo "\n테스트 {$test_count}: {$test_name}\n";
    echo str_repeat("-", 60) . "\n";

    try {
        $result = $test_fn();

        if ($result === true) {
            echo "✅ 성공\n";
            $success_count++;
            $test_results[] = ['name' => $test_name, 'status' => 'success'];
        } else {
            echo "❌ 실패: {$result}\n";
            $test_results[] = ['name' => $test_name, 'status' => 'failed', 'message' => $result];
        }
    } catch (Exception $e) {
        echo "❌ 에러: " . $e->getMessage() . "\n";
        $test_results[] = ['name' => $test_name, 'status' => 'error', 'message' => $e->getMessage()];
    }
}

/**
 * 테스트 1: CategoryModel 기본 생성
 */
test_category(
    'CategoryModel 기본 생성',
    function () {
        $category = new CategoryModel('general', '일반');

        if ($category->category_name !== 'general') {
            return "category_name이 'general'이 아닙니다: {$category->category_name}";
        }

        if ($category->name !== '일반') {
            return "name이 '일반'이 아닙니다: {$category->name}";
        }

        $array = $category->toArray();
        if ($array['category_name'] !== 'general' || $array['name'] !== '일반') {
            return "toArray() 결과가 올바르지 않습니다";
        }

        echo "  category_name: {$category->category_name}\n";
        echo "  name: {$category->name}\n";

        return true;
    }
);

/**
 * 테스트 2: CategoryGroup 기본 생성 및 add()
 */
test_category(
    'CategoryGroup 기본 생성 및 add()',
    function () {
        $group = new CategoryGroup('community', '커뮤니티');

        if ($group->group_name !== 'community') {
            return "group_name이 'community'가 아닙니다";
        }

        if ($group->display_name !== '커뮤니티') {
            return "display_name이 '커뮤니티'가 아닙니다";
        }

        // 카테고리 추가
        $group->add(new CategoryModel('general', '일반'));
        $group->add(new CategoryModel('news', '뉴스'));

        $categories = $group->getCategories();
        if (count($categories) !== 2) {
            return "카테고리 개수가 2개가 아닙니다: " . count($categories);
        }

        echo "  그룹명: {$group->group_name}\n";
        echo "  표시명: {$group->display_name}\n";
        echo "  카테고리 수: " . count($categories) . "\n";

        return true;
    }
);

/**
 * 테스트 3: CategoryGroup 배열 접근 (ArrayAccess)
 */
test_category(
    'CategoryGroup 배열 접근 [0]',
    function () {
        $group = new CategoryGroup('community', '커뮤니티');
        $group->add(new CategoryModel('general', '일반'));
        $group->add(new CategoryModel('news', '뉴스'));
        $group->add(new CategoryModel('sports', '스포츠'));

        // 배열처럼 접근
        $first = $group[0];
        if ($first === null) {
            return "group[0]이 null입니다";
        }

        if ($first->category_name !== 'general') {
            return "group[0]->category_name이 'general'이 아닙니다: {$first->category_name}";
        }

        $second = $group[1];
        if ($second->category_name !== 'news') {
            return "group[1]->category_name이 'news'가 아닙니다";
        }

        $third = $group[2];
        if ($third->category_name !== 'sports') {
            return "group[2]->category_name이 'sports'가 아닙니다";
        }

        // 존재하지 않는 인덱스
        $none = $group[999];
        if ($none !== null) {
            return "group[999]가 null이 아닙니다";
        }

        echo "  group[0]: {$first->category_name} - {$first->name}\n";
        echo "  group[1]: {$second->category_name} - {$second->name}\n";
        echo "  group[2]: {$third->category_name} - {$third->name}\n";

        return true;
    }
);

/**
 * 테스트 4: CategoryList 기본 생성 및 addGroup()
 */
test_category(
    'CategoryList 기본 생성 및 addGroup()',
    function () {
        $categories = new CategoryList();

        $community = new CategoryGroup('community', '커뮤니티');
        $community->add(new CategoryModel('general', '일반'));

        $buyandsell = new CategoryGroup('buyandsell', '사고팔기');
        $buyandsell->add(new CategoryModel('electronics', '전자제품'));

        $categories->addGroup($community);
        $categories->addGroup($buyandsell);

        $groups = $categories->getGroups();
        if (count($groups) !== 2) {
            return "그룹 개수가 2개가 아닙니다: " . count($groups);
        }

        echo "  총 그룹 수: " . count($groups) . "\n";

        return true;
    }
);

/**
 * 테스트 5: CategoryList 동적 접근 ($categories->community)
 */
test_category(
    'CategoryList 동적 접근 ->community',
    function () {
        $categories = new CategoryList();

        $community = new CategoryGroup('community', '커뮤니티');
        $community->add(new CategoryModel('general', '일반'));
        $community->add(new CategoryModel('news', '뉴스'));

        $categories->addGroup($community);

        // 동적 접근
        $group = $categories->community;
        if ($group === null) {
            return "categories->community가 null입니다";
        }

        if ($group->group_name !== 'community') {
            return "group_name이 'community'가 아닙니다";
        }

        $cats = $group->getCategories();
        if (count($cats) !== 2) {
            return "카테고리 개수가 2개가 아닙니다";
        }

        echo "  categories->community->group_name: {$group->group_name}\n";
        echo "  categories->community 카테고리 수: " . count($cats) . "\n";

        return true;
    }
);

/**
 * 테스트 6: 완전한 체이닝 접근 (categories->community[0]->category_name)
 */
test_category(
    '완전한 체이닝 접근 ->community[0]->category_name',
    function () {
        $categories = new CategoryList();

        $community = new CategoryGroup('community', '커뮤니티');
        $community->add(new CategoryModel('general', '일반'));
        $community->add(new CategoryModel('news', '뉴스'));

        $categories->addGroup($community);

        // 완전한 체이닝 접근
        $categoryName = $categories->community[0]->category_name;
        if ($categoryName !== 'general') {
            return "categories->community[0]->category_name이 'general'이 아닙니다: {$categoryName}";
        }

        $name = $categories->community[0]->name;
        if ($name !== '일반') {
            return "categories->community[0]->name이 '일반'이 아닙니다: {$name}";
        }

        $secondCategoryName = $categories->community[1]->category_name;
        if ($secondCategoryName !== 'news') {
            return "categories->community[1]->category_name이 'news'가 아닙니다";
        }

        echo "  categories->community[0]->category_name: {$categoryName}\n";
        echo "  categories->community[0]->name: {$name}\n";
        echo "  categories->community[1]->category_name: {$secondCategoryName}\n";

        return true;
    }
);

/**
 * 테스트 7: get_all_categories() 함수 테스트
 */
test_category(
    'get_all_categories() 함수 테스트',
    function () {
        $categories = get_all_categories();

        // 1차 카테고리 확인
        $groups = $categories->getGroups();
        if (count($groups) < 5) {
            return "그룹 개수가 5개 미만입니다: " . count($groups);
        }

        // community 그룹 확인
        if ($categories->community === null) {
            return "community 그룹이 없습니다";
        }

        $communityCategories = $categories->community->getCategories();
        if (count($communityCategories) === 0) {
            return "community 그룹에 카테고리가 없습니다";
        }

        // buyandsell 그룹 확인
        if ($categories->buyandsell === null) {
            return "buyandsell 그룹이 없습니다";
        }

        // news 그룹 확인
        if ($categories->news === null) {
            return "news 그룹이 없습니다";
        }

        // travel 그룹 확인
        if ($categories->travel === null) {
            return "travel 그룹이 없습니다";
        }

        echo "  총 그룹 수: " . count($groups) . "\n";
        echo "  community 카테고리 수: " . count($communityCategories) . "\n";
        echo "  community[0]: {$categories->community[0]->category_name}\n";

        return true;
    }
);

/**
 * 테스트 8: toArray() 메서드 테스트
 */
test_category(
    'toArray() 메서드 테스트',
    function () {
        $categories = new CategoryList();

        $community = new CategoryGroup('community', '커뮤니티');
        $community->add(new CategoryModel('general', '일반'));
        $community->add(new CategoryModel('news', '뉴스'));

        $categories->addGroup($community);

        $array = $categories->toArray();

        if (!isset($array['community'])) {
            return "array에 'community' 키가 없습니다";
        }

        if (!isset($array['community']['group_name'])) {
            return "array['community']에 'group_name' 키가 없습니다";
        }

        if ($array['community']['group_name'] !== 'community') {
            return "group_name이 'community'가 아닙니다";
        }

        if (!isset($array['community']['categories'])) {
            return "array['community']에 'categories' 키가 없습니다";
        }

        if (count($array['community']['categories']) !== 2) {
            return "categories 배열 크기가 2가 아닙니다";
        }

        echo "  array['community']['group_name']: {$array['community']['group_name']}\n";
        echo "  array['community']['categories'] 크기: " . count($array['community']['categories']) . "\n";

        return true;
    }
);

/**
 * 테스트 9: 모든 1차 카테고리 순회
 */
test_category(
    '모든 1차 카테고리 순회',
    function () {
        $categories = get_all_categories();

        $groupNames = [];
        foreach ($categories->getGroups() as $group) {
            $groupNames[] = $group->group_name;
        }

        // 기대되는 그룹들이 있는지 확인
        $expectedGroups = ['community', 'buyandsell', 'news', 'travel'];
        foreach ($expectedGroups as $expected) {
            if (!in_array($expected, $groupNames)) {
                return "'{$expected}' 그룹이 없습니다";
            }
        }

        echo "  발견된 그룹: " . implode(', ', $groupNames) . "\n";

        return true;
    }
);

/**
 * 테스트 10: 특정 1차 카테고리의 모든 2차 카테고리 순회
 */
test_category(
    '특정 1차 카테고리의 모든 2차 카테고리 순회',
    function () {
        $categories = get_all_categories();

        $categoryNames = [];
        foreach ($categories->community->getCategories() as $cat) {
            $categoryNames[] = $cat->category_name;
        }

        if (count($categoryNames) === 0) {
            return "community 그룹에 카테고리가 없습니다";
        }

        // 'general' 카테고리가 있는지 확인
        if (!in_array('general', $categoryNames)) {
            return "'general' 카테고리가 없습니다";
        }

        echo "  community 카테고리: " . implode(', ', $categoryNames) . "\n";

        return true;
    }
);

// 테스트 결과 요약
echo "\n" . str_repeat("=", 60) . "\n";
echo "테스트 결과 요약\n";
echo str_repeat("=", 60) . "\n";

foreach ($test_results as $result) {
    $status_icon = $result['status'] === 'success' ? '✅' : '❌';
    echo "{$status_icon} {$result['name']} - {$result['status']}\n";

    if (isset($result['message'])) {
        echo "   메시지: {$result['message']}\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "총 테스트: {$test_count}개\n";
echo "✅ 성공: {$success_count}개\n";
echo "❌ 실패: " . ($test_count - $success_count) . "개\n";
echo "📈 성공률: " . round(($success_count / $test_count) * 100, 1) . "%\n";
echo str_repeat("=", 60) . "\n";

// 모든 테스트가 성공하면 0, 하나라도 실패하면 1 반환
exit($success_count === $test_count ? 0 : 1);
