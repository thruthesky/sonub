<?php

/**
 * 기본 페이지네이션 함수
 *
 * 페이지 네비게이션 UI를 생성합니다. Bootstrap 5 스타일을 사용하며,
 * << < [1] [2] [3] ... > >> 형태로 표시됩니다.
 *
 * @param int $page 현재 페이지 번호 (1부터 시작)
 * @param int $total_pages 전체 페이지 수
 * @param int $display_pages 네비게이션에 보여줄 페이지 수 (기본값: 5)
 *                           - 홀수만 가능 (짝수 입력 시 자동으로 +1)
 *                           - 최소값: 5
 *                           - 예: 5, 7, 9 등
 * @param array $query_params URL 쿼리 파라미터 배열 (기본값: $_GET)
 *                            - 비어있으면 자동으로 $_GET 사용
 *                            - 페이지 번호는 자동으로 'page' 키로 추가됨
 *
 * @return void
 *
 * @example 기본 사용법 (가장 간단한 방법)
 * ```php
 * // $_GET을 자동으로 사용하여 URL 쿼리 파라미터 유지
 * basic_pagination($page, $total_pages);
 * ```
 *
 * @example 페이지 표시 개수 지정
 * ```php
 * // 7개 페이지 번호 표시
 * basic_pagination($page, $total_pages, 7);
 *
 * // 9개 페이지 번호 표시
 * basic_pagination($page, $total_pages, 9);
 * ```
 *
 * @example $query_params 직접 지정
 * ```php
 * // 특정 검색 조건을 유지하면서 페이지네이션
 * $params = [
 *     'gender' => 'M',
 *     'age_start' => 25,
 *     'age_end' => 35,
 *     'name' => '김'
 * ];
 * basic_pagination($page, $total_pages, 7, $params);
 * // 생성되는 URL: ?gender=M&age_start=25&age_end=35&name=김&page=2
 * ```
 *
 * @example $_GET 사용 (권장)
 * ```php
 * // 현재 URL의 모든 쿼리 파라미터를 유지
 * // URL: ?gender=F&name=이&page=3
 * basic_pagination($page, $total_pages, 5, $_GET);
 * // 또는 빈 배열로 전달하면 자동으로 $_GET 사용
 * basic_pagination($page, $total_pages, 5);
 * ```
 *
 * @example 실전 예제 - 사용자 검색 페이지
 * ```php
 * // 사용자 목록 조회
 * $result = list_users(array_merge($_GET, ['per_page' => 10]));
 * $users = $result['users'];
 * $page = $result['page'];
 * $total_pages = $result['total_pages'];
 *
 * // 사용자 목록 표시
 * foreach ($users as $user) {
 *     echo $user['first_name'] . ' ' . $user['last_name'];
 * }
 *
 * // 페이지네이션 표시 (검색 조건 자동 유지)
 * basic_pagination($page, $total_pages, 7);
 * ```
 *
 * 네비게이션 버튼 설명:
 * - << : 맨 처음 페이지(1번)로 이동
 * - <  : 이전 블록의 마지막 페이지로 이동
 * - >  : 다음 블록의 첫 페이지로 이동
 * - >> : 맨 끝 페이지로 이동
 */
function basic_pagination(int $page, int $total_pages, int $display_pages = 5, array $query_params = []): void
{
    // 쿼리 파라미터가 비어있으면 $_GET 사용
    if (empty($query_params)) {
        $query_params = $_GET;
    }

    // display_pages는 홀수여야 함 (중앙에 현재 페이지 배치)
    if ($display_pages % 2 === 0) {
        $display_pages += 1;
    }

    // display_pages는 최소 5 이상
    $display_pages = max(5, $display_pages);

    // 페이지가 1개 이하면 표시 안 함
    if ($total_pages <= 1) {
        return;
    }

    // 현재 블록 계산 (블록은 display_pages 단위로 나눔)
    $half_display = floor($display_pages / 2);
    $start_page = max(1, $page - $half_display);
    $end_page = min($total_pages, $page + $half_display);

    // 시작 페이지와 끝 페이지 조정 (항상 display_pages 개수만큼 표시)
    if ($end_page - $start_page + 1 < $display_pages) {
        if ($start_page === 1) {
            $end_page = min($total_pages, $start_page + $display_pages - 1);
        } else {
            $start_page = max(1, $end_page - $display_pages + 1);
        }
    }

    // 이전 블록의 마지막 페이지 계산
    $prev_block_page = max(1, $start_page - 1);

    // 다음 블록의 첫 페이지 계산
    $next_block_page = min($total_pages, $end_page + 1);

    // URL 생성 헬퍼 함수
    $build_url = function ($page_num) use ($query_params) {
        $params = array_merge($query_params, ['page' => $page_num]);
        return '?' . http_build_query($params);
    };
?>

    <!-- 페이지네이션 -->
    <nav aria-label="페이지 네비게이션" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- 맨 처음 페이지 << -->
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $build_url(1) ?>" title="맨 처음">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link"><i class="bi bi-chevron-double-left"></i></span>
                </li>
            <?php endif; ?>

            <!-- 이전 블록의 마지막 페이지 < -->
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $build_url($prev_block_page) ?>" title="이전 블록">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                </li>
            <?php endif; ?>

            <!-- 페이지 번호 -->
            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <?php if ($i === $page): ?>
                        <span class="page-link"><?= $i ?></span>
                    <?php else: ?>
                        <a class="page-link" href="<?= $build_url($i) ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>

            <!-- 다음 블록의 첫 페이지 > -->
            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $build_url($next_block_page) ?>" title="다음 블록">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                </li>
            <?php endif; ?>

            <!-- 맨 끝 페이지 >> -->
            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $build_url($total_pages) ?>" title="맨 끝">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link"><i class="bi bi-chevron-double-right"></i></span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php
}
