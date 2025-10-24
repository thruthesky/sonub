<?php

/**
 * get_comment_sort() 함수 Unit 테스트 (수정 버전)
 *
 * 댓글 순서 정렬 문자열 생성 함수 테스트
 * - 콤마(,) 구분자 사용
 * - 첫 번째 레벨: 4자리 (0000)
 * - 나머지 레벨: 3자리 (000)
 * - 최대 12단계 깊이 지원
 * - 최대 문자열 길이: 48자
 *
 * 실행 방법:
 * php tests/comment/get-comment-sort.test.php
 */

include __DIR__ . '/../../init.php';

echo "🧪 get_comment_sort() 함수 테스트 (수정 버전)\n";
echo str_repeat("=", 70) . "\n\n";

// ============================================================================
// 헬퍼 함수: 테스트 결과 출력
// ============================================================================
function test_assert(string $name, bool $condition, string $expected, string $actual): void
{
    if ($condition) {
        echo "   ✅ 통과: {$name}\n";
    } else {
        echo "   ❌ 실패: {$name}\n";
        echo "   📝 예상: {$expected}\n";
        echo "   📝 실제: {$actual}\n";
        exit(1);
    }
}

// ============================================================================
// 1단계: 첫 번째 레벨 댓글 (부모가 없는 댓글)
// ============================================================================
echo "🧪 1단계: 첫 번째 레벨 댓글 테스트\n\n";

// 테스트 1-1: 첫 번째 댓글 (order=null, depth=0, noOfComments=0)
$a = get_comment_sort(null, null, 0);
$expected_a = '0000,000,000,000,000,000,000,000,000,000,000,000';
test_assert('첫 번째 댓글 (a)', $a === $expected_a, $expected_a, $a);
echo "   📏 문자열 길이: " . strlen($a) . "자\n";

// 테스트 1-2: 두 번째 댓글 (order=null, depth=0, noOfComments=1)
$b = get_comment_sort(null, 0, 1);
$expected_b = '0001,000,000,000,000,000,000,000,000,000,000,000';
test_assert('두 번째 댓글 (b)', $b === $expected_b, $expected_b, $b);

// 테스트 1-3: 세 번째 댓글 (order='', depth=0, noOfComments=2)
$c = get_comment_sort('', 0, 2);
$expected_c = '0002,000,000,000,000,000,000,000,000,000,000,000';
test_assert('세 번째 댓글 (c)', $c === $expected_c, $expected_c, $c);

// 테스트 1-4: 네 번째 댓글 (order=null, depth=0, noOfComments=3)
$d = get_comment_sort(null, 0, 3);
$expected_d = '0003,000,000,000,000,000,000,000,000,000,000,000';
test_assert('네 번째 댓글 (d)', $d === $expected_d, $expected_d, $d);

// 테스트 1-5: 다섯 번째 댓글 (order=null, depth=0, noOfComments=4)
$e = get_comment_sort(null, 0, 4);
$expected_e = '0004,000,000,000,000,000,000,000,000,000,000,000';
test_assert('다섯 번째 댓글 (e)', $e === $expected_e, $expected_e, $e);

// 테스트 1-6: 여섯 번째 댓글 (order='', depth=0, noOfComments=5)
$f = get_comment_sort('', 0, 5);
$expected_f = '0005,000,000,000,000,000,000,000,000,000,000,000';
test_assert('여섯 번째 댓글 (f)', $f === $expected_f, $expected_f, $f);

echo "\n";

// ============================================================================
// 2단계: 두 번째 레벨 댓글 (자식 댓글)
// ============================================================================
echo "🧪 2단계: 두 번째 레벨 댓글 (자식 댓글) 테스트\n\n";

// 테스트 2-1: a의 자식 댓글 (order=a, depth=1, noOfComments=6)
$aa = get_comment_sort($a, 1, 6);
$expected_aa = '0000,006,000,000,000,000,000,000,000,000,000,000';
test_assert('a의 자식 댓글 (aa)', $aa === $expected_aa, $expected_aa, $aa);

// 테스트 2-2: b의 자식 댓글 (order=b, depth=1, noOfComments=7)
$ba = get_comment_sort($b, 1, 7);
$expected_ba = '0001,007,000,000,000,000,000,000,000,000,000,000';
test_assert('b의 첫 번째 자식 (ba)', $ba === $expected_ba, $expected_ba, $ba);

// 테스트 2-3: b의 두 번째 자식 댓글 (order=b, depth=1, noOfComments=8)
$bb = get_comment_sort($b, 1, 8);
$expected_bb = '0001,008,000,000,000,000,000,000,000,000,000,000';
test_assert('b의 두 번째 자식 (bb)', $bb === $expected_bb, $expected_bb, $bb);

// 테스트 2-4: b의 세 번째 자식 댓글 (order=b, depth=1, noOfComments=9)
$bc = get_comment_sort($b, 1, 9);
$expected_bc = '0001,009,000,000,000,000,000,000,000,000,000,000';
test_assert('b의 세 번째 자식 (bc)', $bc === $expected_bc, $expected_bc, $bc);

// 테스트 2-5: b의 네 번째 자식 댓글 (order=b, depth=1, noOfComments=10)
$bd = get_comment_sort($b, 1, 10);
$expected_bd = '0001,010,000,000,000,000,000,000,000,000,000,000';
test_assert('b의 네 번째 자식 (bd)', $bd === $expected_bd, $expected_bd, $bd);

// 테스트 2-6: b의 다섯 번째 자식 댓글 (order=b, depth=1, noOfComments=11)
$be = get_comment_sort($b, 1, 11);
$expected_be = '0001,011,000,000,000,000,000,000,000,000,000,000';
test_assert('b의 다섯 번째 자식 (be)', $be === $expected_be, $expected_be, $be);

// 테스트 2-7: f의 자식 댓글 (order=f, depth=1, noOfComments=12)
$fa = get_comment_sort($f, 1, 12);
$expected_fa = '0005,012,000,000,000,000,000,000,000,000,000,000';
test_assert('f의 자식 댓글 (fa)', $fa === $expected_fa, $expected_fa, $fa);

echo "\n";

// ============================================================================
// 3단계: 세 번째 레벨 댓글 (손자 댓글)
// ============================================================================
echo "🧪 3단계: 세 번째 레벨 댓글 (손자 댓글) 테스트\n\n";

// 테스트 3-1: fa의 자식 댓글 (order=fa, depth=2, noOfComments=13)
$faa = get_comment_sort($fa, 2, 13);
$expected_faa = '0005,012,013,000,000,000,000,000,000,000,000,000';
test_assert('fa의 자식 댓글 (faa)', $faa === $expected_faa, $expected_faa, $faa);

// 테스트 3-2: bb의 자식 댓글 (order=bb, depth=2, noOfComments=14)
$bba = get_comment_sort($bb, 2, 14);
$expected_bba = '0001,008,014,000,000,000,000,000,000,000,000,000';
test_assert('bb의 첫 번째 자식 (bba)', $bba === $expected_bba, $expected_bba, $bba);

// 테스트 3-3: bb의 두 번째 자식 댓글 (order=bb, depth=2, noOfComments=15)
$bbb = get_comment_sort($bb, 2, 15);
$expected_bbb = '0001,008,015,000,000,000,000,000,000,000,000,000';
test_assert('bb의 두 번째 자식 (bbb)', $bbb === $expected_bbb, $expected_bbb, $bbb);

// 테스트 3-4: bb의 세 번째 자식 댓글 (order=bb, depth=2, noOfComments=16)
$bbc = get_comment_sort($bb, 2, 16);
$expected_bbc = '0001,008,016,000,000,000,000,000,000,000,000,000';
test_assert('bb의 세 번째 자식 (bbc)', $bbc === $expected_bbc, $expected_bbc, $bbc);

echo "\n";

// ============================================================================
// 4단계: 네 번째 레벨 댓글 (증손자 댓글)
// ============================================================================
echo "🧪 4단계: 네 번째 레벨 댓글 (증손자 댓글) 테스트\n\n";

// 테스트 4-1: bbb의 첫 번째 자식 댓글 (order=bbb, depth=3, noOfComments=17)
$bbba = get_comment_sort($bbb, 3, 17);
$expected_bbba = '0001,008,015,017,000,000,000,000,000,000,000,000';
test_assert('bbb의 첫 번째 자식 (bbba)', $bbba === $expected_bbba, $expected_bbba, $bbba);

// 테스트 4-2: bbb의 두 번째 자식 댓글 (order=bbb, depth=3, noOfComments=18)
$bbbb = get_comment_sort($bbb, 3, 18);
$expected_bbbb = '0001,008,015,018,000,000,000,000,000,000,000,000';
test_assert('bbb의 두 번째 자식 (bbbb)', $bbbb === $expected_bbbb, $expected_bbbb, $bbbb);

// 테스트 4-3: bbb의 세 번째 자식 댓글 (order=bbb, depth=3, noOfComments=19)
$bbbc = get_comment_sort($bbb, 3, 19);
$expected_bbbc = '0001,008,015,019,000,000,000,000,000,000,000,000';
test_assert('bbb의 세 번째 자식 (bbbc)', $bbbc === $expected_bbbc, $expected_bbbc, $bbbc);

// 테스트 4-4: bbb의 네 번째 자식 댓글 (order=bbb, depth=3, noOfComments=20)
$bbbd = get_comment_sort($bbb, 3, 20);
$expected_bbbd = '0001,008,015,020,000,000,000,000,000,000,000,000';
test_assert('bbb의 네 번째 자식 (bbbd)', $bbbd === $expected_bbbd, $expected_bbbd, $bbbd);

// 테스트 4-5: faa의 첫 번째 자식 댓글 (order=faa, depth=3, noOfComments=21)
$faaa = get_comment_sort($faa, 3, 21);
$expected_faaa = '0005,012,013,021,000,000,000,000,000,000,000,000';
test_assert('faa의 첫 번째 자식 (faaa)', $faaa === $expected_faaa, $expected_faaa, $faaa);

// 테스트 4-6: faa의 두 번째 자식 댓글 (order=faa, depth=3, noOfComments=22)
$faab = get_comment_sort($faa, 3, 22);
$expected_faab = '0005,012,013,022,000,000,000,000,000,000,000,000';
test_assert('faa의 두 번째 자식 (faab)', $faab === $expected_faab, $expected_faab, $faab);

// 테스트 4-7: faa의 세 번째 자식 댓글 (order=faa, depth=3, noOfComments=23)
$faac = get_comment_sort($faa, 3, 23);
$expected_faac = '0005,012,013,023,000,000,000,000,000,000,000,000';
test_assert('faa의 세 번째 자식 (faac)', $faac === $expected_faac, $expected_faac, $faac);

// 테스트 4-8: faa의 네 번째 자식 댓글 (order=faa, depth=3, noOfComments=24)
$faad = get_comment_sort($faa, 3, 24);
$expected_faad = '0005,012,013,024,000,000,000,000,000,000,000,000';
test_assert('faa의 네 번째 자식 (faad)', $faad === $expected_faad, $expected_faad, $faad);

echo "\n";

// ============================================================================
// 추가 테스트: 깊이 제한 (12단계 이상)
// ============================================================================
echo "🧪 추가 테스트: 깊이 제한 (12단계 이상)\n\n";

// 11단계까지는 정상 작동 (depth=0부터 시작하므로 depth=11이 12번째 레벨)
$depth_11 = get_comment_sort('0000,000,000,000,000,000,000,000,000,000,000,000', 11, 100);
$expected_depth_11 = '0000,000,000,000,000,000,000,000,000,000,000,100';
test_assert('11단계 (최대 깊이)', $depth_11 === $expected_depth_11, $expected_depth_11, $depth_11);
echo "   📏 문자열 길이: " . strlen($depth_11) . "자\n";

// 12단계 이상은 변경되지 않음 (depth=12는 13번째 레벨이므로 제한 초과)
$depth_12 = get_comment_sort('0000,000,000,000,000,000,000,000,000,000,000,000', 12, 200);
$expected_depth_12 = '0000,000,000,000,000,000,000,000,000,000,000,000';
test_assert('12단계 (깊이 제한 초과)', $depth_12 === $expected_depth_12, $expected_depth_12, $depth_12);

echo "\n";

// ============================================================================
// 추가 테스트: 큰 숫자 패딩
// ============================================================================
echo "🧪 추가 테스트: 큰 숫자 패딩\n\n";

// 첫 번째 레벨: 9999 (최대값)
$large_level0 = get_comment_sort(null, 0, 9999);
$expected_large_level0 = '9999,000,000,000,000,000,000,000,000,000,000,000';
test_assert('첫 번째 레벨 큰 숫자 (9999)', $large_level0 === $expected_large_level0, $expected_large_level0, $large_level0);

// 나머지 레벨: 999 (최대값)
$large_level1 = get_comment_sort('0001,000,000,000,000,000,000,000,000,000,000,000', 1, 999);
$expected_large_level1 = '0001,999,000,000,000,000,000,000,000,000,000,000';
test_assert('두 번째 레벨 큰 숫자 (999)', $large_level1 === $expected_large_level1, $expected_large_level1, $large_level1);

// 10000을 초과하는 경우 (첫 번째 레벨) - 경고: 정렬 문제 발생 가능
$overflow_level0 = get_comment_sort(null, 0, 10000);
echo "   ⚠️  경고: 첫 번째 레벨에서 10000 이상은 5자리가 되어 정렬 문제가 발생할 수 있습니다\n";
echo "   📝 결과: {$overflow_level0}\n";
echo "   📏 문자열 길이: " . strlen($overflow_level0) . "자\n";

// 1000을 초과하는 경우 (나머지 레벨) - 경고: 정렬 문제 발생 가능
$overflow_level1 = get_comment_sort('0001,000,000,000,000,000,000,000,000,000,000,000', 1, 1000);
echo "   ⚠️  경고: 나머지 레벨에서 1000 이상은 4자리가 되어 정렬 문제가 발생할 수 있습니다\n";
echo "   📝 결과: {$overflow_level1}\n";
echo "   📏 문자열 길이: " . strlen($overflow_level1) . "자\n";

echo "\n";

// ============================================================================
// 최대 문자열 길이 계산
// ============================================================================
echo "📏 최대 문자열 길이 분석\n";
echo str_repeat("-", 70) . "\n";

// 최대 길이 시나리오: 모든 레벨이 최대값
$max_string = '9999,999,999,999,999,999,999,999,999,999,999,999';
echo "   최대 문자열: {$max_string}\n";
echo "   최대 길이: " . strlen($max_string) . "자\n";
echo "   계산: 4자리 + 11*3자리 + 11개 콤마 = 4 + 33 + 11 = 48자\n";
echo "\n";

// ============================================================================
// 테스트 완료
// ============================================================================
echo str_repeat("=", 70) . "\n";
echo "✅ 모든 테스트 통과!\n";
echo "✅ 최대 문자열 길이: 48자 (VARCHAR(64) 안전)\n";
echo str_repeat("=", 70) . "\n";
