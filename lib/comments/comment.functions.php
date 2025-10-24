<?php

/**
 * 댓글 트리 구조를 표현하기 위한 정렬 문자열 생성
 *
 * 참고, `맨 처음 댓글`이란, 글에 최초로 작성되는 첫 번째 댓글. 맨 처음 댓글은 1개만 존재한다.
 * 참고, `첫번째 레벨 댓글`란, 글 바로 아래에 작성되는 댓글로, 부모 댓글가 없는 댓글이다. 여러개의 댓글가 있다.
 * 참고, `부모 댓글`란, 자식 댓글가 있는 댓글 또는 자식을 만들 댓글.
 * 참고, `자식 댓글`란, 부모 댓글 아래에 작성되는 댓글 또는 부모 댓글가 있는 댓글.
 *
 * @param string|null $order 정렬 순서 문자열
 *   - `첫 번째 레벨 댓글(부모가 없는 댓글)` 에는 빈 문자열 또는 null,
 *   - `자식 댓글`를 생성 할 때, 부모 댓글의 order 값을 그대로 전달하면 된다.
 *
 * @param int|null $depth 댓글 깊이
 *   - `첫 번째 레벨 댓글(부모가 없는 댓글)`에서는 0(또는 null),
 *   - `자식 댓글(부모가 있는 댓글)`의 경우, 부모 댓글의 depth 값을 그대로 전달하면 된다.
 *
 * @param int|null $noOfComments 댓글 개수 (항상 **글의 noOfComments 값**을 전달)
 *
 * @return string 정렬 순서 문자열
 *   - 첫 번째 레벨: 4자리 (예: "0000")
 *   - 나머지 레벨: 3자리 (예: "000")
 *   - 최대 깊이: 12단계
 *   - 최대 문자열 길이: 48자 (4 + 11*3 + 11개의 콤마 = 4 + 33 + 11 = 48)
 *
 * 참고, 이 함수는 depth 의 값을 0(또는 null)으로 입력 받지만, 실제 댓글 DB에 저장하는 값은 1부터 시작하는 것을
 * 원칙으로 한다.
 *
 * 참고, [depth] 가 12 단계 이상으로 깊어지면, 12 단계 이상의 댓글는 순서가 뒤죽 박죽이 될 수 있다.
 * 이 때, 전체가 다 뒤죽 박죽이 되는 것이 아니라, 12단계 이내에서는 잘 정렬되어 보이고, 13단계 이상의 댓글 들만
 * 정렬이 안된다.
 *
 * 참고, 첫 번째 레벨은 최대 9999개, 나머지 레벨은 최대 999개의 댓글을 지원한다.
 *
 * @example
 * $order = get_comment_sort(null, 0, 1);
 * // 결과: "0001,000,000,000,000,000,000,000,000,000,000,000"
 *
 * $order2 = get_comment_sort($order, 1, 6);
 * // 결과: "0001,006,000,000,000,000,000,000,000,000,000,000"
 */
function get_comment_sort(?string $order, ?int $depth, ?int $noOfComments): string
{
    // 기본값 설정: order가 null이거나 빈 문자열이면 초기 문자열 생성
    // 첫 번째 레벨: 4자리 (0000), 나머지 11개 레벨: 3자리 (000)
    if ($order === null || $order === '') {
        $parts = ['0000'];  // 첫 번째 레벨 (4자리)
        for ($i = 1; $i < 12; $i++) {
            $parts[] = '000';  // 나머지 레벨 (3자리)
        }
        $order = implode(',', $parts);
    }

    // depth와 noOfComments의 기본값 설정
    $depth = $depth ?? 0;
    $noOfComments = $noOfComments ?? 0;

    // depth가 12 이상이면 order를 그대로 반환 (최대 12단계까지만 지원)
    if ($depth >= 12) {
        return $order;
    }

    // noOfComments가 0이면 order를 그대로 반환
    if ($noOfComments == 0) {
        return $order;
    }

    // order 문자열을 콤마로 분리하여 배열로 변환
    $parts = explode(',', $order);

    // 현재 depth의 값을 가져와서 noOfComments를 더함
    $no = $parts[$depth];
    $computed = (int)$no + $noOfComments;

    // 첫 번째 레벨(depth=0)은 4자리, 나머지는 3자리로 패딩
    if ($depth === 0) {
        // 첫 번째 레벨: 4자리 (예: 1 → "0001")
        $parts[$depth] = str_pad((string)$computed, 4, '0', STR_PAD_LEFT);
    } else {
        // 나머지 레벨: 3자리 (예: 1 → "001")
        $parts[$depth] = str_pad((string)$computed, 3, '0', STR_PAD_LEFT);
    }

    // 배열을 다시 콤마로 연결하여 반환
    return implode(',', $parts);
}


/**
 * 댓글 작성에 필요한 정보(부모 댓글의 sort, depth, 형제 댓글 개수)를 DB 에서 직접 가져오기
 *
 * 이 함수는 댓글 작성 시, get_comment_sort() 함수에 필요한 정보를 제공합니다.
 * 입력된 파라메타를 바탕으로 DB 쿼리를 수행하여 필요한 정보를 반환합니다.
 * 
 * 댓글 작성에 필요한 정보:
 * - 부모 댓글의 sort 값
 * - 댓글 깊이
 * - 현재 depth에서 형제 댓글의 개수 + 1
 * 
 * @param int $post_id 게시물 ID
 * @param int $parent_id 부모 댓글 ID (0이면 루트 댓글)
 * @return array 댓글 작성에 필요한 정보
 *   - parent_sort (string|null): 부모 댓글의 sort 값 (부모가 없으면 null)
 *   - depth (int): 댓글 깊이 (루트 댓글은 0)
 *   - no_of_comments (int): 현재 depth에서 형제 댓글의 개수 + 1
 */
function get_comment_info(int $post_id, int $parent_id): array
{

    // 부모 댓글의 sort 정보 가져오기
    $parent_sort = null;
    $depth = 0;


    $pdo = pdo();

    if ($parent_id > 0) {
        $stmt = $pdo->prepare("SELECT sort FROM comments WHERE id = ?");
        $stmt->execute([$parent_id]);
        $parent = $stmt->fetch();

        if ($parent) {
            $parent_sort = $parent['sort'];
            // depth 계산: 부모의 depth + 1
            // 부모의 depth는 0이 아닌 마지막 부분의 인덱스
            $parts = explode(',', $parent_sort);
            $parent_depth = 0;
            for ($i = 0; $i < count($parts); $i++) {
                if ((int)$parts[$i] > 0) {
                    $parent_depth = $i;
                }
            }
            $depth = $parent_depth + 1;
        }
    }

    // 현재 depth에서 형제 댓글의 개수를 세어서 순서 번호 계산
    // noOfComments는 현재 댓글의 순서 번호 (1부터 시작)
    $no_of_comments = 1; // 기본값: 첫 번째 댓글
    if ($parent_id > 0) {
        // 같은 부모를 가진 댓글 개수 + 1
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE parent_id = ?");
        $stmt->execute([$parent_id]);
        $no_of_comments = (int)$stmt->fetchColumn() + 1;
    } else {
        // 루트 댓글 개수 + 1
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ? AND parent_id = 0");
        $stmt->execute([$post_id]);
        $no_of_comments = (int)$stmt->fetchColumn() + 1;
    }

    return [
        'parent_sort' => $parent_sort,
        'depth' => $depth,
        'no_of_comments' => $no_of_comments
    ];
}
