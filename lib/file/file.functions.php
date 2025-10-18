<?php

/**
 * 썸네일 URL 생성 함수
 *
 * 주어진 이미지 파일로부터 썸네일을 생성하는 URL을 리턴합니다.
 * thumbnail.php 스크립트를 사용하여 이미지를 WebP로 변환하고 리사이즈합니다.
 *
 * **중요**: URL 인코딩을 하지 않습니다. thumbnail.php에서 자동으로 디코딩 처리합니다.
 *
 * @param string $url 원본 이미지 URL 또는 경로 (예: '/var/uploads/533/cat.jpg', '533/cat.jpg')
 * @param int|null $width 썸네일 너비 (픽셀). null이면 파라미터 생략 (기본값 400 사용)
 * @param int|null $height 썸네일 높이 (픽셀). null이면 파라미터 생략 (비율 유지)
 * @param string|null $fit 리사이즈 모드 ('cover', 'contain', 'scale'). null이면 파라미터 생략 (기본값 'cover' 사용)
 * @param int|null $quality WebP 품질 (10-100). null이면 파라미터 생략 (기본값 85 사용)
 * @param string|null $bg 배경색 HEX (contain 모드용, 예: 'ffffff'). null이면 파라미터 생략 (기본값 'ffffff' 사용)
 * @return string 썸네일 URL
 *
 * @example
 * // 기본 사용 (기본 설정: 400px 너비, cover 모드, 품질 85)
 * thumbnail('/var/uploads/533/cat.jpg');
 * // 출력: /thumbnail.php?src=/var/uploads/533/cat.jpg
 *
 * @example
 * // 너비만 지정 (높이는 비율 유지)
 * thumbnail('/var/uploads/533/cat.jpg', 300);
 * // 출력: /thumbnail.php?src=/var/uploads/533/cat.jpg&w=300
 *
 * @example
 * // 너비+높이 지정 (cover 모드)
 * thumbnail('/var/uploads/533/cat.jpg', 300, 200);
 * // 출력: /thumbnail.php?src=/var/uploads/533/cat.jpg&w=300&h=200
 *
 * @example
 * // contain 모드 + 배경색
 * thumbnail('/var/uploads/533/cat.jpg', 400, 400, 'contain', null, 'f0f0f0');
 * // 출력: /thumbnail.php?src=/var/uploads/533/cat.jpg&w=400&h=400&fit=contain&bg=f0f0f0
 *
 * @example
 * // 고품질 이미지
 * thumbnail('/var/uploads/533/artwork.jpg', 800, null, null, 95);
 * // 출력: /thumbnail.php?src=/var/uploads/533/artwork.jpg&w=800&q=95
 *
 * @example
 * // 모든 옵션 지정
 * thumbnail('/var/uploads/533/cat.jpg', 300, 200, 'cover', 90, 'ffffff');
 * // 출력: /thumbnail.php?src=/var/uploads/533/cat.jpg&w=300&h=200&fit=cover&q=90&bg=ffffff
 */
function thumbnail(
    string $url,
    ?int $width = null,
    ?int $height = null,
    ?string $fit = null,
    ?int $quality = null,
    ?string $bg = null
): string {
    // var/uploads/ 경로가 포함된 경우에만 썸네일 URL 생성
    if (!str_contains($url, 'var/uploads/')) {
        return $url;
    }

    // 썸네일 베이스 URL (URL 인코딩 안함 - thumbnail.php에서 urldecode 처리)
    $thumbnailUrl = get_thumbnail_url() . '?src=' . $url;

    // 파라미터 추가
    $params = [];

    // 너비 파라미터
    if ($width !== null && $width > 0) {
        $params['w'] = $width;
    }

    // 높이 파라미터
    if ($height !== null && $height > 0) {
        $params['h'] = $height;
    }

    // 리사이즈 모드 파라미터
    if ($fit !== null && in_array($fit, ['cover', 'contain', 'scale'], true)) {
        $params['fit'] = $fit;
    }

    // 품질 파라미터
    if ($quality !== null && $quality >= 10 && $quality <= 100) {
        $params['q'] = $quality;
    }

    // 배경색 파라미터 (contain 모드용)
    if ($bg !== null && preg_match('/^[0-9a-f]{6}$/i', $bg)) {
        $params['bg'] = $bg;
    }

    // 파라미터를 URL에 추가
    if (!empty($params)) {
        $thumbnailUrl .= '&' . http_build_query($params);
    }

    return $thumbnailUrl;
}
