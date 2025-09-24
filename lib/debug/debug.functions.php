<?php


/**
 * 입력된 내용을 보기 좋게, 웹브라우저 또는 콘솔로 출력한다
 * 
 * Debug log.
 * 
 * If it has HTML, it prints the raw HTML on the screen.
 */
function d(...$data)
{
    // 데이터가 없으면 빈 데이터 메시지 표시
    if (empty($data)) {
        $output = '[[ d() : no data provided ]]';
    } else {
        // 가변 파라미터들을 처리
        $output = '';
        foreach ($data as $index => $item) {
            if ($index > 0) {
                $output .= "\n---\n";
            }
            $output .= print_r($item, true);
        }
    }

    // CLI 환경인지 확인 (STDOUT이 정의되어 있거나 php_sapi_name이 'cli'인 경우)
    if (defined('STDOUT') || php_sapi_name() === 'cli') {
        // CLI: 표준 출력으로 출력
        echo $output . "\n";
    } else {
        $output = str_replace("<", "&lt;", $output);
        $output = str_replace(">", "&gt;", $output);
        $output = str_replace(" ", "&nbsp;", $output);
        $output = nl2br($output);
        // 웹: HTML로 감싸서 출력
        echo "<pre>";
        echo $output;
        echo "</pre>";
    }
}
