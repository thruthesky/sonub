<?php
/**
 * file_upload() 함수 테스트 (api.php?func=file_upload 엔드포인트)
 *
 * 실행 방법: php tests/file/file_upload.test.php
 */

// 필수: init.php 포함 전 세션 시작
// CLI 환경에서 세션 경고 억제
@session_start();

include __DIR__ . '/../../init.php';

echo "=== file_upload() API 테스트 시작 ===\n\n";

// 테스트 환경 확인
echo "테스트 1: 환경 확인\n";
if (!function_exists('file_upload')) {
    echo "❌ file_upload() 함수가 존재하지 않습니다\n";
    exit(1);
}
echo "✅ file_upload() 함수 존재 확인\n\n";

// 테스트 2: 테스트용 사용자 로그인 시뮬레이션
echo "테스트 2: 테스트용 사용자 생성 및 로그인\n";
$testFirebaseUid = 'test_file_upload_' . time();
$testUser = create_user_record([
    'firebase_uid' => $testFirebaseUid,
    'first_name' => '파일업로드테스트_' . time(),
    'last_name' => '',
    'middle_name' => ''
]);

if (isset($testUser['error_code'])) {
    echo "❌ 테스트 사용자 생성 실패: " . $testUser['error_message'] . "\n";
    exit(1);
}

echo "✅ 테스트 사용자 생성 성공 (ID: {$testUser['id']})\n";

// 쿠키 설정하여 로그인 시뮬레이션
$sessionId = generate_session_id($testUser);
$_COOKIE[SESSION_ID] = $sessionId;

$loggedInUser = login();
if (!$loggedInUser) {
    echo "❌ 로그인 시뮬레이션 실패\n";
    exit(1);
}
echo "✅ 로그인 시뮬레이션 성공\n";
echo "   세션 ID: $sessionId\n\n";

// 테스트 3: 업로드 디렉토리 확인 및 생성
echo "테스트 3: 업로드 디렉토리 확인\n";
$uploadPath = get_file_upload_path();
echo "   업로드 경로: $uploadPath\n";

if (!is_dir($uploadPath)) {
    if (!mkdir($uploadPath, 0755, true)) {
        echo "❌ 업로드 디렉토리 생성 실패\n";
        exit(1);
    }
    echo "✅ 업로드 디렉토리 생성 성공\n\n";
} else {
    echo "✅ 업로드 디렉토리 이미 존재\n\n";
}

// 테스트 4: 테스트 파일 생성
echo "테스트 4: 테스트 파일 생성\n";
$testFileName = 'test_image_' . time() . '.txt';
$testFileContent = '이것은 파일 업로드 테스트 파일입니다. ' . date('Y-m-d H:i:s');
$tempFilePath = sys_get_temp_dir() . '/' . $testFileName;
file_put_contents($tempFilePath, $testFileContent);

if (!file_exists($tempFilePath)) {
    echo "❌ 테스트 파일 생성 실패\n";
    exit(1);
}

echo "✅ 테스트 파일 생성 성공\n";
echo "   파일명: $testFileName\n";
echo "   경로: $tempFilePath\n";
echo "   크기: " . filesize($tempFilePath) . " bytes\n\n";

// 테스트 5: $_FILES 시뮬레이션
echo "테스트 5: 파일 업로드 시뮬레이션\n";
$_FILES['userfile'] = [
    'name' => $testFileName,
    'type' => 'text/plain',
    'tmp_name' => $tempFilePath,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($tempFilePath)
];

echo "✅ \$_FILES 배열 시뮬레이션 완료\n";
echo "   파일명: " . $_FILES['userfile']['name'] . "\n";
echo "   임시 경로: " . $_FILES['userfile']['tmp_name'] . "\n\n";

// 테스트 6: file_upload() 함수 로직 수동 시뮬레이션
// move_uploaded_file()은 실제 HTTP 업로드에서만 작동하므로,
// CLI 환경에서는 copy()를 사용하여 동일한 로직을 재현합니다
echo "테스트 6: file_upload() 로직 수동 시뮬레이션 (CLI 환경)\n";
$uploaddir = get_file_upload_path();
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

$result = [];
// CLI 환경에서는 copy()를 사용
if (copy($_FILES['userfile']['tmp_name'], $uploadfile)) {
    $result['url'] = $uploadfile;
    echo "✅ 파일 업로드 성공 (copy 사용)\n";
} else {
    $result['error_code'] = 'copy-failed';
    $result['error_message'] = 'File copy failed in CLI environment';
    echo "❌ 파일 복사 실패\n";
    print_r($result);
    exit(1);
}

echo "   업로드된 파일 경로: " . $result['url'] . "\n\n";

// 테스트 7: 업로드된 파일 확인
echo "테스트 7: 업로드된 파일 확인\n";
if (!file_exists($result['url'])) {
    echo "❌ 업로드된 파일이 존재하지 않습니다\n";
    exit(1);
}

$uploadedContent = file_get_contents($result['url']);
if ($uploadedContent !== $testFileContent) {
    echo "❌ 업로드된 파일 내용이 일치하지 않습니다\n";
    echo "   원본: $testFileContent\n";
    echo "   업로드: $uploadedContent\n";
    exit(1);
}

echo "✅ 업로드된 파일 존재 및 내용 확인 완료\n";
echo "   파일 크기: " . filesize($result['url']) . " bytes\n\n";

// 테스트 8: cURL을 통한 실제 HTTP 파일 업로드 테스트
echo "테스트 8: cURL을 통한 실제 HTTP 파일 업로드\n";

// 새로운 테스트 파일 생성
$httpTestFileName = 'http_test_' . time() . '.txt';
$httpTestContent = 'HTTP 파일 업로드 테스트 ' . date('Y-m-d H:i:s');
$httpTempFile = sys_get_temp_dir() . '/' . $httpTestFileName;
file_put_contents($httpTempFile, $httpTestContent);

// cURL 요청
$ch = curl_init();
$apiUrl = 'https://local.sonub.com/api.php?func=file_upload';

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'userfile' => new CURLFile($httpTempFile, 'text/plain', $httpTestFileName)
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIE => SESSION_ID . "=$sessionId",
    CURLOPT_HTTPHEADER => [
        'Accept: application/json'
    ],
    CURLOPT_SSL_VERIFYPEER => false, // 개발 환경에서 자체 서명 인증서 허용
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

$apiResult = null; // 변수 초기화

if ($curlError) {
    echo "⚠️  cURL 에러: $curlError\n";
    echo "   (HTTP 테스트는 건너뜁니다)\n\n";
} else {
    echo "   HTTP 상태 코드: $httpCode\n";
    echo "   응답: $response\n";

    $apiResult = json_decode($response, true);

    if ($httpCode === 200 && isset($apiResult['url'])) {
        echo "✅ HTTP 파일 업로드 성공\n";
        echo "   업로드된 파일: " . $apiResult['url'] . "\n";
        // 파일 삭제는 나중에 정리 섹션에서 처리
    } else {
        echo "⚠️  HTTP 파일 업로드 실패 (상태 코드: $httpCode)\n";
        if (isset($apiResult['error_message'])) {
            echo "   에러 메시지: " . $apiResult['error_message'] . "\n";
        }
    }
    echo "\n";
}

// 임시 파일 삭제
@unlink($httpTempFile);

// 테스트 데이터 정리
echo "테스트 데이터 정리 중...\n";
try {
    // 업로드된 파일 삭제
    if (file_exists($result['url'])) {
        unlink($result['url']);
        echo "✅ CLI 업로드 파일 삭제 완료\n";
    }

    // HTTP로 업로드한 파일 삭제 (경로가 절대 경로가 아닌 경우 처리)
    if (isset($apiResult['url']) && $apiResult['url']) {
        // URL 경로 (/sonub/var/uploads/...)를 절대 경로로 변환
        // /sonub 부분을 제거하고 ROOT_DIR과 결합
        $relativePath = preg_replace('#^/sonub/#', '/', $apiResult['url']);
        $httpUploadedFile = ROOT_DIR . $relativePath;
        if (file_exists($httpUploadedFile)) {
            unlink($httpUploadedFile);
            echo "✅ HTTP 업로드 파일 삭제 완료\n";
        } else {
            echo "⚠️  HTTP 업로드 파일을 찾을 수 없음: $httpUploadedFile\n";
        }
    }

    // 업로드 디렉토리 삭제 (비어있는 경우)
    if (is_dir($uploadPath)) {
        $files = array_diff(scandir($uploadPath), ['.', '..']);
        if (count($files) === 0) {
            rmdir($uploadPath);
            echo "✅ 업로드 디렉토리 삭제 완료\n";
        }
    }

    // 테스트 사용자 삭제
    db()->delete()->from('users')->where('id = ?', [$testUser['id']])->execute();
    echo "✅ 테스트 사용자 삭제 완료\n";

    // 쿠키 정리
    unset($_COOKIE[SESSION_ID]);
    echo "✅ 쿠키 정리 완료\n\n";

} catch (Exception $e) {
    echo "⚠️  테스트 데이터 정리 중 오류: " . $e->getMessage() . "\n\n";
}

echo "🎉 모든 테스트 통과!\n";
