<?php

/**
 * 썸네일 생성 스크립트
 *
 * 이 스크립트는 이미지 파일을 받아서 썸네일을 생성합니다.
 * - On-demand 방식: 요청이 있을 때만 썸네일을 생성
 * - 캐싱 시스템: 한 번 생성된 썸네일은 캐시에 저장
 * - WebP 우선 정책: 모든 이미지를 WebP로 변환 (애니메이션 GIF 예외)
 * - GD 라이브러리 직접 사용: Intervention Image 등 외부 라이브러리 없이 구현
 * - PHP 7.4+ 호환
 *
 * 사용 예시:
 * https://local.sonub.com/thumbnail.php?src=/var/uploads/123/image.jpg
 * https://local.sonub.com/thumbnail.php?src=/var/uploads/123/image.jpg&w=300&h=200&fit=cover
 * https://local.sonub.com/thumbnail.php?src=/var/uploads/n/naver3.png
 * https://local.sonub.com/thumbnail.php?src=123/animation.gif&w=320&h=320
 *
 * URL 파라미터:
 * - src: 원본 이미지 경로 (필수)
 *   - 전체 경로: /var/uploads/123/image.jpg
 *   - 상대 경로: 123/image.jpg (자동으로 /var/uploads/ 추가)
 * - w: 썸네일 너비 (픽셀, 기본값: 400)
 * - h: 썸네일 높이 (픽셀, 기본값: 0 = 비율 유지)
 * - fit: 리사이즈 모드 (cover, contain, scale, 기본값: cover)
 * - q: 이미지 품질 (10-100, 기본값: 85)
 * - bg: 배경색 HEX (contain 모드용, 기본값: ffffff)
 *
 * 파일 구조:
 * /
 *   thumbnail.php (이 파일)
 *   var/
 *     uploads/
 *       {user_id}/
 *         {filename}.{ext}
 *     cache/
 *       thumbs/
 *         {hash}.webp
 *
 * @author Sonub Team
 * @version 3.0
 * @path /thumbnail.php
 */

declare(strict_types=1);

// 프로젝트 초기화 파일 포함
include_once __DIR__ . '/init.php';

// ============================================================
// PHP 7.4 호환성을 위한 폴리필 함수들
// ============================================================

if (!function_exists('str_contains')) {
    /**
     * PHP 8.0의 str_contains 함수 폴리필
     */
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * PHP 8.0의 str_starts_with 함수 폴리필
     */
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

// ============================================================
// 설정 및 초기화 함수들
// ============================================================

/**
 * 썸네일 설정 초기화
 *
 * 현재 프로젝트의 파일 구조에 맞게 업로드 및 캐시 디렉토리 경로를 설정합니다.
 *
 * @return array 설정 배열
 */
function get_thumbnail_config(): array
{
    // 업로드 디렉토리: ROOT_DIR/var/uploads/
    $uploadsDir = ROOT_DIR . '/var/uploads';

    // 캐시 디렉토리: ROOT_DIR/var/cache/thumbs/
    $cacheDir = ROOT_DIR . '/var/cache/thumbs';

    // 캐시 디렉토리 생성
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0775, true);
    }

    return [
        'uploads_dir' => $uploadsDir,
        'cache_dir' => $cacheDir,
        'default_quality' => 85,
        'allowed_fits' => ['cover', 'contain', 'scale'],
        'default_width' => 400,
        'max_quality' => 100,
        'min_quality' => 10
    ];
}

/**
 * 요청 파라미터 파싱 및 검증
 * 
 * @param array $get GET 파라미터 배열
 * @param array $config 설정 배열
 * @return array 검증된 파라미터
 */
function parse_request_params(array $get, array $config): array
{
    // 파라미터 파싱 (w가 없으면 0으로 처리)
    $width  = isset($get['w']) ? (int) $get['w'] : 0;
    $height = isset($get['h']) ? (int) $get['h'] : 0;

    // 너비와 높이가 모두 0이거나 설정되지 않은 경우 기본값 사용
    if ($width === 0 && $height === 0) {
        $width = $config['default_width'];
    }

    $params = [
        'src' => $get['src'] ?? '',
        'w' => max(1, $width),  // 최소 1픽셀
        'h' => max(0, $height), // 높이는 0 허용 (비율 계산용)
        'fit' => strtolower($get['fit'] ?? 'cover'),
        'bg' => preg_replace('/[^0-9a-f]/i', '', ($get['bg'] ?? 'ffffff')),
        'q' => min($config['max_quality'], max(
            $config['min_quality'],
            (int) ($get['q'] ?? $config['default_quality'])
        ))
    ];

    return $params;
}

/**
 * 파라미터 유효성 검증
 * 
 * @param array $params 파라미터 배열
 * @param array $config 설정 배열
 * @return array|null 에러가 있으면 [code, message] 배열 반환, 없으면 null
 */
function validate_params(array $params, array $config): ?array
{
    if (empty($params['src'])) {
        return [400, 'src is required'];
    }

    if (!in_array($params['fit'], $config['allowed_fits'], true)) {
        return [400, 'invalid fit mode: ' . $params['fit']];
    }

    return null;
}

// ============================================================
// 보안 및 경로 처리 함수들
// ============================================================

/**
 * 파일 경로 보안 검증 및 정규화
 *
 * URL 디코딩 후 thumbnail.php 기준 상대 경로로 파일을 읽습니다.
 * 클라이언트가 urlencode를 할 수도 있고 안할 수도 있으므로 자동으로 디코딩합니다.
 *
 * @param string $srcParam 원본 파라미터 (URL 인코딩 여부 무관)
 * @param string $uploadsDir 사용하지 않음 (하위 호환성 유지용)
 * @return array [성공여부, 절대경로 또는 에러메시지]
 */
function validate_file_path(string $srcParam, string $uploadsDir): array
{
    // URL 디코딩 (클라이언트가 urlencode 했을 수도 있음)
    $srcParam = urldecode($srcParam);

    // URL인 경우 /var/uploads/ 이후 경로 추출
    if (str_starts_with($srcParam, 'http://') || str_starts_with($srcParam, 'https://')) {
        // /var/uploads/ 패턴을 찾아서 그 이후 경로만 추출
        if (preg_match('/\/var\/uploads\/(.+)$/', $srcParam, $matches)) {
            $srcParam = '/var/uploads/' . $matches[1];
        } else {
            return [false, 'invalid URL format - /var/uploads/ path not found'];
        }
    }

    // 경로 정규화 (백슬래시를 슬래시로 변환)
    $srcParam = str_replace('\\', '/', $srcParam);

    // 경로 탐색 공격 방지 (.. 패턴 차단)
    if (str_contains($srcParam, '..')) {
        return [false, 'invalid src path: directory traversal detected'];
    }

    // thumbnail.php가 위치한 프로젝트 루트 기준으로 상대 경로 처리
    // srcParam: /var/uploads/123/image.jpg
    // ROOT_DIR: /path/to/sonub
    // 결과: /path/to/sonub/var/uploads/123/image.jpg
    $srcParam = ltrim($srcParam, '/'); // 맨 앞 슬래시 제거
    $srcAbs = ROOT_DIR . '/' . $srcParam;

    // 파일 존재 여부 확인
    if (!is_file($srcAbs)) {
        return [false, 'file not found: ' . $srcAbs];
    }

    return [true, $srcAbs];
}

// ============================================================
// 캐싱 관련 함수들
// ============================================================

/**
 * 캐시 키 생성
 *
 * 원본 파일 경로, 수정 시간, 썸네일 파라미터를 조합하여 고유한 캐시 키를 생성합니다.
 * 동일한 이미지와 파라미터에 대해서는 항상 같은 캐시 키를 반환합니다.
 *
 * @param string $srcPath 원본 파일 절대 경로
 * @param array $params 썸네일 파라미터 (w, h, fit, bg, q)
 * @param bool $isAnimGif 애니메이션 GIF 여부
 * @return string SHA1 해시된 캐시 키 (40자)
 */
function generate_cache_key(string $srcPath, array $params, bool $isAnimGif): string
{
    $mtime   = (int) filemtime($srcPath);
    $keyData = json_encode([
        $srcPath,
        $mtime,
        $params['w'],
        $params['h'],
        $params['fit'],
        $params['bg'],
        $params['q'],
        'force-webp',
        $isAnimGif
    ]);
    return sha1($keyData);
}

/**
 * 캐시 파일 경로 생성
 * 
 * @param string $cacheDir 캐시 디렉토리
 * @param string $cacheKey 캐시 키
 * @param string $extension 파일 확장자
 * @return string 캐시 파일 경로
 */
function get_cache_file_path(string $cacheDir, string $cacheKey, string $extension): string
{
    return $cacheDir . '/' . $cacheKey . '.' . $extension;
}

/**
 * HTTP 캐싱 헤더 처리
 * 
 * @param string $etag ETag 값
 * @param string $cacheFile 캐시 파일 경로 (optional)
 * @return bool 304 응답을 보냈으면 true
 */
function handle_http_cache_headers(string $etag, string $cacheFile = ''): bool
{
    // ETag 체크
    if (
        isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
        trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag
    ) {
        header('ETag: ' . $etag);
        header('Cache-Control: public, max-age=31536000, immutable');
        http_response_code(304);
        return true;
    }

    // Last-Modified 체크 (캐시 파일이 있는 경우)
    if ($cacheFile && is_file($cacheFile)) {
        $lastModified = gmdate('D, d M Y H:i:s', filemtime($cacheFile)) . ' GMT';
        if (
            isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
            strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= strtotime($lastModified)
        ) {
            header('ETag: ' . $etag);
            header('Last-Modified: ' . $lastModified);
            header('Cache-Control: public, max-age=31536000, immutable');
            http_response_code(304);
            return true;
        }
    }

    return false;
}

// ============================================================
// 이미지 처리 함수들
// ============================================================

/**
 * 애니메이션 GIF 파일인지 감지
 *
 * 파일 내용을 바이너리로 읽어서 그래픽 제어 확장 블록이 2개 이상 있는지 확인합니다.
 * 애니메이션 GIF는 프레임마다 제어 블록이 있으므로 이를 통해 감지할 수 있습니다.
 *
 * @param string $path 검사할 파일 절대 경로
 * @return bool 애니메이션 GIF면 true, 정적 GIF 또는 다른 형식이면 false
 */
function is_animated_gif(string $path): bool
{
    if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'gif') {
        return false;
    }

    $data = @file_get_contents($path, false, null, 0, 256 * 1024);
    if ($data === false) {
        return false;
    }

    // 그래픽 제어 확장 블록 패턴 검색
    return preg_match_all('/\x21\xF9\x04/s', $data) > 1;
}


/**
 * 메모리 제한 문자열을 바이트로 변환
 * 
 * @param string $val 메모리 값 (예: "128M", "1G")
 * @return int 바이트 단위 값
 */
function return_bytes(string $val): int
{
    $val = trim($val);
    if (empty($val)) {
        return 0;
    }

    $last = strtolower($val[strlen($val) - 1]);
    $val  = (int) $val;

    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * GD를 사용하여 이미지 로드
 *
 * Progressive JPEG 및 대용량 이미지를 안정적으로 처리합니다.
 * 파일 존재 여부, 권한, 매직 넘버 확인을 통해 실제 이미지 파일인지 검증합니다.
 * 메모리 부족 시 자동으로 memory_limit을 증가시킵니다.
 *
 * @param string $path 이미지 파일 절대 경로
 * @param string|null &$error 에러 메시지 참조 변수 (실패 시 자동으로 설정됨)
 * @return \GdImage|resource|false GD 이미지 리소스 (PHP 8.0+: GdImage, PHP 7.4: resource) 또는 false (실패 시)
 */
function load_image(string $path, ?string &$error = null): \GdImage|false
{
    // 파일 존재 및 권한 확인
    if (!file_exists($path)) {
        $error = 'File does not exist: ' . $path;
        return false;
    }
    if (!is_readable($path)) {
        $error = 'File is not readable (permission issue): ' . $path . ' (permissions: ' . substr(sprintf('%o', fileperms($path)), -4) . ')';
        return false;
    }

    // 파일 내용 시작 부분 확인 (매직 넘버)
    $handle = @fopen($path, 'rb');
    $magicNumber = '';
    $actualFileType = 'unknown';

    if ($handle) {
        $firstBytes = @fread($handle, 16);
        @fclose($handle);

        // 매직 넘버로 실제 파일 타입 확인
        if (strlen($firstBytes) >= 4) {
            $magicNumber = bin2hex(substr($firstBytes, 0, 4));

            // 매직 넘버로 파일 타입 식별
            switch (strtoupper(substr($magicNumber, 0, 8))) {
                case '89504E47': // PNG
                    $actualFileType = 'PNG';
                    break;
                case 'FFD8FFE0':
                case 'FFD8FFE1':
                case 'FFD8FFE2':
                case 'FFD8FFE8':
                    $actualFileType = 'JPEG';
                    break;
                case '47494638': // GIF
                    $actualFileType = 'GIF';
                    break;
                case '52494646': // RIFF (WebP)
                    $actualFileType = 'WebP';
                    break;
                case '424D': // BMP
                    $actualFileType = 'BMP';
                    break;
                case '00000020': // Spaces
                case '20202020': // Spaces
                    $actualFileType = 'text/spaces (not an image)';
                    break;
                case '3C68746D': // <htm
                case '3C48544D': // <HTM
                case '3C3F786D': // <?xm
                    $actualFileType = 'HTML/XML (not an image)';
                    break;
                default:
                    $actualFileType = 'unknown format';
            }
        }
    }

    $info = @getimagesize($path);
    if (!$info) {
        $error = 'NOT A VALID IMAGE FILE - The file appears to be: ' . $actualFileType;
        $error .= '. File details: size=' . filesize($path) . ' bytes';
        if ($magicNumber) {
            $error .= ', magic=0x' . strtoupper($magicNumber);
        }
        $error .= ', detected_mime=' . (function_exists('mime_content_type') ? mime_content_type($path) : 'N/A');
        $error .= '. The file may have been corrupted during upload or is not an image despite the extension.';
        return false;
    }

    // 메모리 제한 확인 및 필요시 증가
    if (isset($info[0]) && isset($info[1])) {
        // 예상 메모리 사용량 계산 (이미지 크기 * 4바이트 * 1.5 여유)
        $memoryNeeded = round($info[0] * $info[1] * 4 * 1.5);
        $memoryLimit  = @ini_get('memory_limit');

        if ($memoryLimit && $memoryLimit != -1 && $memoryLimit != '-1') {
            $memoryLimitBytes = return_bytes($memoryLimit);
            $memoryUsage      = memory_get_usage(true);

            if (($memoryUsage + $memoryNeeded) > $memoryLimitBytes) {
                $newLimitMB = ceil(($memoryUsage + $memoryNeeded) / 1048576) + 16;
                $newLimit   = strval($newLimitMB) . 'M';
                @ini_set('memory_limit', $newLimit);
            }
        }
    }

    // Progressive JPEG 경고 무시
    @ini_set('gd.jpeg_ignore_warning', '1');

    $image = false;

    switch ($info['mime']) {
        case 'image/jpeg':
            // Progressive JPEG 처리
            $image = @imagecreatefromjpeg($path);

            // 실패 시 문자열로 재시도
            if (!$image) {
                $data = @file_get_contents($path);
                if ($data !== false) {
                    $image = @imagecreatefromstring($data);
                }
                if (!$image) {
                    $error = 'imagecreatefromjpeg() failed - JPEG file may be corrupted';
                }
            }
            break;

        case 'image/png':
            $image = @imagecreatefrompng($path);
            if (!$image) {
                $error = 'imagecreatefrompng() failed - PNG file may be corrupted';
            }
            break;

        case 'image/gif':
            $image = @imagecreatefromgif($path);
            if (!$image) {
                $error = 'imagecreatefromgif() failed - GIF file may be corrupted';
            }
            break;

        case 'image/webp':
            $image = @imagecreatefromwebp($path);
            if (!$image) {
                $error = 'imagecreatefromwebp() failed - WebP file may be corrupted or WebP not supported';
            }
            break;

        case 'image/bmp':
            $image = @imagecreatefrombmp($path);
            if (!$image) {
                $error = 'imagecreatefrombmp() failed - BMP file may be corrupted or BMP not supported';
            }
            break;

        default:
            $error = 'Unsupported image format: ' . ($info['mime'] ?? 'unknown');
            return false;
    }

    // 이미지 유효성 검증
    if ($image && imagesx($image) > 0 && imagesy($image) > 0) {
        return $image;
    }

    if ($image) {
        $error = 'Invalid image dimensions: ' . imagesx($image) . 'x' . imagesy($image);
    } elseif (!isset($error)) {
        $error = 'Failed to create image resource from file';
    }

    return false;
}

/**
 * EXIF 방향 수정
 *
 * 스마트폰으로 촬영한 사진은 EXIF Orientation 값에 따라 회전이 필요할 수 있습니다.
 * 이 함수는 EXIF 정보를 읽어서 이미지를 올바른 방향으로 회전시킵니다.
 *
 * @param \GdImage|resource $image GD 이미지 리소스
 * @param string $path 원본 파일 절대 경로
 * @return \GdImage|resource 회전된 이미지 리소스 (EXIF 정보가 없거나 회전이 필요 없으면 원본 그대로 반환)
 */
function fix_orientation(\GdImage $image, string $path): \GdImage
{
    if (!function_exists('exif_read_data')) {
        return $image;
    }

    $exif = @exif_read_data($path);
    if (!$exif || !isset($exif['Orientation'])) {
        return $image;
    }

    switch ($exif['Orientation']) {
        case 3:
            return imagerotate($image, 180, 0);
        case 6:
            return imagerotate($image, -90, 0);
        case 8:
            return imagerotate($image, 90, 0);
        default:
            return $image;
    }
}

/**
 * 리사이즈 계산
 *
 * 원본 이미지 크기와 목표 크기를 비교하여 리사이즈 방식에 따른 최종 크기를 계산합니다.
 * - cover: 목표 크기를 완전히 채움 (이미지 일부가 잘릴 수 있음)
 * - contain: 목표 영역 안에 이미지 전체가 들어감 (여백이 생길 수 있음)
 * - scale: 원본 비율을 유지하면서 목표 크기를 넘지 않게 축소
 *
 * @param int $srcW 원본 이미지 너비
 * @param int $srcH 원본 이미지 높이
 * @param int $targetW 목표 너비 (0이면 높이 비율로 자동 계산)
 * @param int $targetH 목표 높이 (0이면 너비 비율로 자동 계산)
 * @param string $fit 리사이즈 모드 ('cover', 'contain', 'scale')
 * @return array 계산된 크기 정보 (width, height, resizeW, resizeH, offsetX, offsetY)
 */
function calculate_resize(int $srcW, int $srcH, int $targetW, int $targetH, string $fit): array
{
    // 원본 이미지 크기 유효성 검사
    if ($srcW <= 0 || $srcH <= 0) {
        return [
            'width' => 100,
            'height' => 100,
            'resizeW' => 100,
            'resizeH' => 100,
            'offsetX' => 0,
            'offsetY' => 0
        ];
    }

    // 0 처리
    if ($targetW === 0 && $targetH === 0) {
        $targetW = 800;
    }
    if ($targetW === 0) {
        $targetW = (int) ($srcW * $targetH / $srcH);
    }
    if ($targetH === 0) {
        $targetH = (int) ($srcH * $targetW / $srcW);
    }

    // 최종 타겟 크기 유효성 검사
    if ($targetW <= 0 || $targetH <= 0) {
        $targetW = max(1, $targetW);
        $targetH = max(1, $targetH);
    }

    $srcRatio    = $srcW / $srcH;
    $targetRatio = $targetW / $targetH;

    if ($fit === 'cover') {
        if ($srcRatio > $targetRatio) {
            $newW = (int) ($targetH * $srcRatio);
            $newH = $targetH;
        } else {
            $newW = $targetW;
            $newH = (int) ($targetW / $srcRatio);
        }
        return [
            'width' => $targetW,
            'height' => $targetH,
            'resizeW' => $newW,
            'resizeH' => $newH,
            'offsetX' => (int) (($targetW - $newW) / 2),
            'offsetY' => (int) (($targetH - $newH) / 2)
        ];
    } elseif ($fit === 'contain') {
        if ($srcRatio > $targetRatio) {
            $newW = $targetW;
            $newH = (int) ($targetW / $srcRatio);
        } else {
            $newW = (int) ($targetH * $srcRatio);
            $newH = $targetH;
        }
        return [
            'width' => $targetW,
            'height' => $targetH,
            'resizeW' => $newW,
            'resizeH' => $newH,
            'offsetX' => (int) (($targetW - $newW) / 2),
            'offsetY' => (int) (($targetH - $newH) / 2)
        ];
    } else { // scale
        $scale = min(1, min($targetW / $srcW, $targetH / $srcH));
        $newW  = (int) ($srcW * $scale);
        $newH  = (int) ($srcH * $scale);
        return [
            'width' => $newW,
            'height' => $newH,
            'resizeW' => $newW,
            'resizeH' => $newH,
            'offsetX' => 0,
            'offsetY' => 0
        ];
    }
}

/**
 * 이미지 리사이즈
 *
 * GD 라이브러리를 사용하여 이미지를 리사이즈합니다.
 * - contain 모드: 지정된 배경색으로 여백을 채웁니다.
 * - cover/scale 모드: 투명 배경을 사용합니다.
 *
 * @param \GdImage|resource $srcImage 원본 GD 이미지 리소스
 * @param int $w 목표 너비
 * @param int $h 목표 높이
 * @param string $fit 리사이즈 모드 ('cover', 'contain', 'scale')
 * @param string $bg 배경색 (HEX 6자리, 예: 'ffffff')
 * @param string|null &$error 에러 메시지 참조 변수
 * @return \GdImage|resource|false 리사이즈된 GD 이미지 리소스 또는 실패 시 false
 */
function resize_image(\GdImage $srcImage, int $w, int $h, string $fit, string $bg, ?string &$error = null): \GdImage|false
{
    $srcW = imagesx($srcImage);
    $srcH = imagesy($srcImage);

    // 원본 이미지 크기 유효성 검사
    if ($srcW <= 0 || $srcH <= 0) {
        $error = 'Invalid source image dimensions: ' . $srcW . 'x' . $srcH;
        return false;
    }

    $calc = calculate_resize($srcW, $srcH, $w, $h, $fit);

    // 계산된 크기 유효성 검사
    if ($calc['width'] <= 0 || $calc['height'] <= 0) {
        $error = 'Invalid calculated dimensions: ' . $calc['width'] . 'x' . $calc['height'];
        return false;
    }

    $newImage = imagecreatetruecolor($calc['width'], $calc['height']);

    // imagecreatetruecolor 실패 체크
    if ($newImage === false) {
        $error = 'imagecreatetruecolor() failed - not enough memory to create ' . $calc['width'] . 'x' . $calc['height'] . ' image (memory_limit: ' . ini_get('memory_limit') . ', memory_usage: ' . round(memory_get_usage(true) / 1048576) . 'MB)';
        return false;
    }

    if ($fit === 'contain') {
        $r       = hexdec(substr($bg, 0, 2));
        $g       = hexdec(substr($bg, 2, 2));
        $b       = hexdec(substr($bg, 4, 2));
        $bgColor = imagecolorallocate($newImage, $r, $g, $b);
        imagefill($newImage, 0, 0, $bgColor);
    } else {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);
        imagealphablending($newImage, true);
    }

    imagecopyresampled(
        $newImage,
        $srcImage,
        $calc['offsetX'],
        $calc['offsetY'],
        0,
        0,
        $calc['resizeW'],
        $calc['resizeH'],
        $srcW,
        $srcH
    );

    return $newImage;
}

/**
 * 애니메이션 GIF 처리
 *
 * 애니메이션 GIF의 첫 프레임만 추출하여 정적 WebP 이미지로 변환합니다.
 * Imagick이나 gifsicle 없이 GD 라이브러리만 사용합니다.
 *
 * @param string $srcPath 원본 파일 절대 경로
 * @param array $params 썸네일 파라미터 (w, h, fit, bg, q)
 * @param string $cacheFile 캐시 파일 저장 경로
 * @param string|null &$error 에러 메시지 참조 변수
 * @return bool 성공 시 true, 실패 시 false
 */
function process_animated_gif(string $srcPath, array $params, string $cacheFile, ?string &$error = null): bool
{
    // 애니메이션 GIF는 첫 프레임만 추출하여 정적 이미지로 변환
    // process_regular_image() 함수를 재사용
    return process_regular_image($srcPath, $params, $cacheFile, $error);
}

/**
 * 일반 이미지를 WebP로 변환하여 저장
 *
 * JPEG, PNG, GIF(정적) 등의 이미지를 로드하여 리사이즈한 후 WebP 형식으로 저장합니다.
 * EXIF 방향 정보를 자동으로 처리하여 올바른 방향으로 회전시킵니다.
 *
 * @param string $srcPath 원본 파일 절대 경로
 * @param array $params 썸네일 파라미터 (w, h, fit, bg, q)
 * @param string $cacheFile 캐시 파일 저장 경로
 * @param string|null &$error 에러 메시지 참조 변수
 * @return bool 성공 시 true, 실패 시 false
 */
function process_regular_image(string $srcPath, array $params, string $cacheFile, ?string &$error = null): bool
{
    $srcImage = load_image($srcPath, $error);
    if (!$srcImage) {
        if (!isset($error)) {
            $error = 'Failed to load image';
        }
        return false;
    }

    // EXIF 방향 수정
    $srcImage = fix_orientation($srcImage, $srcPath);

    // 리사이즈
    $newImage = resize_image($srcImage, $params['w'], $params['h'], $params['fit'], $params['bg'], $error);

    // resize_image가 실패한 경우 처리
    if ($newImage === false) {
        imagedestroy($srcImage);
        if (!isset($error)) {
            $error = 'Failed to resize image';
        }
        return false;
    }

    // WebP로 저장
    $result = imagewebp($newImage, $cacheFile, $params['q']);

    imagedestroy($srcImage);
    imagedestroy($newImage);

    if (!$result) {
        $error = 'imagewebp() failed - unable to save WebP file. Check disk space and permissions. Cache dir: ' . dirname($cacheFile) . ', writable: ' . (is_writable(dirname($cacheFile)) ? 'yes' : 'no');
        return false;
    }

    if (!is_file($cacheFile)) {
        $error = 'WebP file was not created. Cache dir: ' . dirname($cacheFile);
        return false;
    }

    if (filesize($cacheFile) === 0) {
        $error = 'WebP file is empty (0 bytes)';
        @unlink($cacheFile);
        return false;
    }

    return true;
}

/**
 * 썸네일 파일 전송
 *
 * 생성된 썸네일 파일을 HTTP 헤더와 함께 클라이언트에 전송합니다.
 * 캐싱 헤더를 설정하여 브라우저가 파일을 1년간 캐시하도록 합니다.
 *
 * @param string $file 썸네일 파일 절대 경로
 * @param string $mime MIME 타입 (예: 'image/webp', 'image/gif')
 * @param string $etag ETag 값 (캐시 검증용)
 * @return void
 */
function send_thumbnail_file(string $file, string $mime, string $etag): void
{
    $lastModified = gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . (string) filesize($file));
    header('ETag: ' . $etag);
    header('Last-Modified: ' . $lastModified);
    header('Cache-Control: public, max-age=31536000, immutable');

    readfile($file);
}

/**
 * 에러 응답
 *
 * HTTP 상태 코드와 에러 메시지를 클라이언트에 전송하고 스크립트를 종료합니다.
 *
 * @param int $code HTTP 상태 코드 (예: 400, 404, 500)
 * @param string $msg 에러 메시지 (평문 텍스트)
 * @return void 이 함수는 항상 exit()로 스크립트를 종료합니다
 */
function fail(int $code, string $msg): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $msg;
    exit;
}

// ============================================================
// 메인 처리 함수
// ============================================================

/**
 * 썸네일 생성 메인 처리
 *
 * GET 파라미터를 받아서 썸네일을 생성하거나 캐시된 파일을 반환합니다.
 * 전체 워크플로우:
 * 1. 파라미터 파싱 및 검증
 * 2. 파일 경로 검증 및 보안 체크
 * 3. 캐시 확인 (있으면 즉시 반환)
 * 4. 썸네일 생성 (애니메이션 GIF 또는 일반 이미지)
 * 5. 캐시 저장 및 클라이언트에 전송
 *
 * @param array $get GET 파라미터 배열 (일반적으로 $_GET)
 * @return void
 */
function process_thumbnail_request(array $get): void
{
    // 설정 초기화
    $config = get_thumbnail_config();

    // 파라미터 파싱
    $params = parse_request_params($get, $config);

    // 파라미터 검증
    $error = validate_params($params, $config);
    if ($error) {
        fail($error[0], $error[1]);
    }


    // 파일 경로 검증
    list($valid, $result) = validate_file_path($params['src'], $config['uploads_dir']);
    if (!$valid) {
        fail(404, $result);
    }
    $srcPath = $result;

    // 애니메이션 GIF 확인
    $isAnimGif = is_animated_gif($srcPath);

    // 캐시 키 생성
    $cacheKey = generate_cache_key($srcPath, $params, $isAnimGif);
    $etag     = '"' . $cacheKey . '"';

    // 출력 형식 결정: 모든 이미지를 WebP로 변환
    $targetExt = 'webp';
    $mime      = 'image/webp';

    $cacheFile = get_cache_file_path($config['cache_dir'], $cacheKey, $targetExt);

    // HTTP 캐싱 체크
    if (handle_http_cache_headers($etag, $cacheFile)) {
        exit;
    }

    // 캐시 파일이 있으면 전송
    if (is_file($cacheFile)) {
        send_thumbnail_file($cacheFile, $mime, $etag);
        exit;
    }

    // 새 썸네일 생성
    try {
        $success = false;
        $error = null;

        if ($isAnimGif) {
            // 애니메이션 GIF 처리
            $success = process_animated_gif($srcPath, $params, $cacheFile, $error);

            // 실패 시 첫 프레임만 처리
            if (!$success) {
                $animGifError = $error; // 애니메이션 GIF 에러 저장
                $error = null; // 에러 초기화

                $success = process_regular_image($srcPath, $params, $cacheFile, $error);
                if ($success) {
                    header('X-Note: animated GIF flattened to first frame (reason: ' . ($animGifError ?? 'unknown') . ')');
                } else {
                    // 둘 다 실패한 경우
                    $combinedError = "Animated GIF processing failed: " . ($animGifError ?? 'unknown') .
                        "; First frame processing also failed: " . ($error ?? 'unknown');
                    fail(500, 'Failed to create thumbnail - ' . $combinedError);
                }
            }
        } else {
            // 일반 이미지 처리
            $success = process_regular_image($srcPath, $params, $cacheFile, $error);
        }

        if (!$success) {
            $detailedError = 'Failed to create thumbnail';
            if ($error) {
                $detailedError .= ' - ' . $error;
            }

            // 추가 시스템 정보
            $detailedError .= ' | System info: PHP ' . PHP_VERSION;
            $detailedError .= ', GD ' . (function_exists('gd_info') ? gd_info()['GD Version'] : 'not available');
            $detailedError .= ', memory_limit: ' . ini_get('memory_limit');
            $detailedError .= ', memory_usage: ' . round(memory_get_usage(true) / 1048576) . 'MB';
            $detailedError .= ', src: ' . basename($srcPath);
            $detailedError .= ', size: ' . round(filesize($srcPath) / 1024) . 'KB';

            fail(500, $detailedError);
        }

        // 생성된 썸네일 전송
        send_thumbnail_file($cacheFile, $mime, $etag);
    } catch (Throwable $e) {
        $detailedError = 'Thumbnail error: ' . $e->getMessage();
        $detailedError .= ' | File: ' . $e->getFile() . ':' . $e->getLine();
        $detailedError .= ' | Trace: ' . substr($e->getTraceAsString(), 0, 500);
        fail(500, $detailedError);
    }
}

// ============================================================
// 스크립트 실행
// ============================================================

// CLI에서 실행하는 경우 테스트 모드
if (PHP_SAPI === 'cli') {
    // 테스트 목적으로 실행되는 경우
    echo "Thumbnail script loaded successfully. Run tests separately.\n";
} else {
    // 웹 요청 처리
    process_thumbnail_request($_GET);
}
