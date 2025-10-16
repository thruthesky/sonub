<?php
/**
 * 랜덤 게시글 생성 스크립트
 *
 * 이 스크립트는 테스트용 랜덤 게시글을 대량으로 생성합니다.
 * 목적: 웹사이트가 많은 콘텐츠로 가득 찼을 때의 모습을 테스트
 *
 * 사용법:
 *   php etc/data/post/generate-random-posts.php
 *
 * 또는 개수 지정:
 *   php etc/data/post/generate-random-posts.php 100
 */

// 프로젝트 초기화
require_once __DIR__ . '/../../../init.php';

// ========================================================================
// 1단계: 명령줄 인자 처리
// ========================================================================
// 생성할 게시글 개수 (기본값: 50개)
$count = isset($argv[1]) ? (int)$argv[1] : 50;

/**
 * 랜덤 이미지를 다운로드하고 업로드하는 함수
 *
 * Picsum Photos API를 사용하여 랜덤 이미지를 다운로드한 후
 * 프로젝트의 파일 업로드 경로에 저장합니다.
 *
 * @param int $width 이미지 너비 (기본값: 800)
 * @param int $height 이미지 높이 (기본값: 600)
 * @return string|null 업로드된 이미지의 URL 또는 실패 시 null
 */
function upload_random_image($width = 800, $height = 600)
{
    // Picsum Photos API를 사용하여 랜덤 이미지 다운로드
    // 매번 다른 이미지를 가져오기 위해 random 쿼리 파라미터 추가
    $imageUrl = "https://picsum.photos/{$width}/{$height}?random=" . mt_rand(1, 100000);

    // cURL을 사용하여 이미지 다운로드
    $ch = curl_init($imageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 리다이렉트 따라가기
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL 검증 비활성화 (개발 환경)
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30초 타임아웃
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 다운로드 실패 시
    if ($httpCode !== 200 || $imageData === false || empty($imageData)) {
        echo "    ⚠️  이미지 다운로드 실패 (HTTP {$httpCode})\n";
        return null;
    }

    // 임시 파일 경로 생성
    $tmpFile = sys_get_temp_dir() . '/random-image-' . uniqid() . '.jpg';
    file_put_contents($tmpFile, $imageData);

    // 업로드 디렉토리 확인 및 생성
    $uploadDir = get_file_upload_path();
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // 고유한 파일명 생성
    $filename = 'post-image-' . uniqid() . '.jpg';
    $uploadFile = get_unique_filename($uploadDir, $filename);

    // 파일 이동
    if (rename($tmpFile, $uploadFile)) {
        // 다운로드 URL 생성
        $downloadUrl = get_download_url($uploadFile);
        return $downloadUrl;
    } else {
        // 실패 시 임시 파일 정리
        @unlink($tmpFile);
        echo "    ⚠️  파일 이동 실패\n";
        return null;
    }
}

// ========================================================================
// 2단계: 데이터베이스 연결 및 테스트 사용자 확인
// ========================================================================
// PDO 연결 가져오기 (통계 출력에 필요)
$pdo = pdo();

// 'banana' 사용자가 존재하는지 확인
$bananaUser = get_user_by_firebase_uid('banana');
if (!$bananaUser) {
    echo "❌ 오류: 'banana' 테스트 사용자가 존재하지 않습니다.\n";
    exit(1);
}

echo "✅ 테스트 사용자 확인: {$bananaUser['display_name']} (ID: {$bananaUser['id']})\n";
echo "📝 {$count}개의 랜덤 게시글을 생성합니다...\n\n";

// ========================================================================
// 3단계: 게시글 템플릿 데이터 정의
// ========================================================================

// 게시글 카테고리 목록
// 테스트 데이터는 'my-wall' 카테고리에 생성
$categories = [
    'my-wall',     // 테스트용 카테고리
];

// 랜덤 제목 템플릿 (30개)
$titleTemplates = [
    "What is most important in web development?",
    "Laravel vs Symfony: Which framework is better?",
    "PHP Programming Tips for Beginners",
    "Database Design Considerations",
    "Vue.js vs React Comparison",
    "Complete GitHub Usage Guide",
    "REST API Design Principles",
    "How to Conduct Effective Code Reviews",
    "10 Ways to Improve Programming Productivity",
    "Server Performance Optimization Guide",
    "Security Vulnerability Checklist",
    "Test-Driven Development (TDD) Examples",
    "Using Docker Containers",
    "CI/CD Pipeline Setup Experience",
    "Microservice Architecture Considerations",
    "Clean Code Principles",
    "Introduction to Functional Programming",
    "Object-Oriented Design Patterns Summary",
    "Algorithm Problem-Solving Tips",
    "Developer Career Consultation",
    "How to Work as a Freelancer",
    "Development Blog Review",
    "Open Source Contribution Experience",
    "Coding Test Preparation Methods",
    "Recommended Development Books",
    "Development Conference Review",
    "Side Project Ideas",
    "Development Tool Recommendations",
    "Bug Resolution Case Study",
    "Project Management Know-How",
];

// 랜덤 내용 템플릿 (5개)
// {tech}, {feature}, {problem}, {solution}, {topic} 키워드는 나중에 실제 값으로 치환됩니다
$contentTemplates = [
    "Hello! I had many concerns when starting a new project.\n\nEspecially in choosing the tech stack, I spent a lot of time, and finally chose {tech}.\n\nReasons for choosing:\n- Active community\n- Well documented\n- Excellent performance\n\nWhat are your thoughts?",

    "I've been studying {tech} recently, and it's really fun!\n\nIt was difficult at first, but as I continued studying, I gradually understood.\n\nI was particularly impressed with the {feature} feature.\n\nI recommend it to beginners!",

    "While working on a project, I faced the problem of {problem}.\n\nI tried several methods, but eventually solved it with {solution}.\n\nHas anyone experienced a similar problem?\n\nI'm curious about other solutions.",

    "I wrote a simple tutorial about {tech}.\n\n1. First, proceed with installation\n2. Do basic configuration\n3. Create a sample project\n4. Run and check the results\n\nPlease refer to the official documentation for details!",

    "Today I want to talk about {topic}.\n\nPersonally, I think this is important content that every developer should know.\n\nEspecially since it's a situation frequently encountered in practice, I think it would be good to study in advance.\n\nPlease share your experiences too!",
];

// 키워드 치환용 데이터
$techKeywords = ['PHP', 'Laravel', 'Vue.js', 'React', 'Docker', 'MySQL', 'Redis', 'Git', 'AWS', 'Nginx'];
$featureKeywords = ['component structure', 'state management', 'routing', 'middleware', 'caching', 'authentication', 'authorization'];
$problemKeywords = ['performance degradation', 'memory leak', 'data synchronization', 'loading speed', 'security issue'];
$solutionKeywords = ['applying caching', 'query optimization', 'adding indexes', 'load balancing', 'code refactoring'];
$topicKeywords = ['clean code', 'design patterns', 'test code', 'Git workflow', 'code review'];

// ========================================================================
// 4단계: 랜덤 게시글 생성 루프
// ========================================================================

// 성공/실패 카운트
$successCount = 0;
$failCount = 0;

for ($i = 1; $i <= $count; $i++) {
    try {
        // 카테고리 선택 (현재는 'my-wall'만 사용)
        $category = $categories[array_rand($categories)];

        // 랜덤 제목 선택
        $title = $titleTemplates[array_rand($titleTemplates)];

        // 제목에 번호 추가 (중복 방지)
        $title = $title . " #" . $i;

        // 랜덤 내용 선택 및 키워드 치환
        $content = $contentTemplates[array_rand($contentTemplates)];
        $content = str_replace('{tech}', $techKeywords[array_rand($techKeywords)], $content);
        $content = str_replace('{feature}', $featureKeywords[array_rand($featureKeywords)], $content);
        $content = str_replace('{problem}', $problemKeywords[array_rand($problemKeywords)], $content);
        $content = str_replace('{solution}', $solutionKeywords[array_rand($solutionKeywords)], $content);
        $content = str_replace('{topic}', $topicKeywords[array_rand($topicKeywords)], $content);

        // ====================================================================
        // login_as_test_user() 함수를 사용한 테스트 사용자 로그인
        // ====================================================================
        // lib/test/test.functions.php에 정의된 login_as_test_user() 함수를 사용합니다.
        // 이 함수는 테스트 환경에서 'banana' 사용자로 로그인 상태를 만듭니다.
        // 내부적으로 generate_session_id()를 호출하고 $_COOKIE[SESSION_ID]를 설정합니다.
        // 'banana'는 기본 테스트 사용자입니다.
        // 이미지 업로드 전에 먼저 로그인 상태를 만들어야 get_file_upload_path()가 작동합니다.
        login_as_test_user('banana');

        // ====================================================================
        // 랜덤 이미지 업로드 (50% 확률로 1-3개의 이미지 추가)
        // ====================================================================
        $files = [];
        // 50% 확률로 이미지 추가
        if (mt_rand(0, 1) === 1) {
            // 1-3개의 이미지를 랜덤하게 추가
            $imageCount = mt_rand(1, 3);
            echo "    📸 이미지 {$imageCount}개 업로드 중...\n";

            for ($j = 0; $j < $imageCount; $j++) {
                // 다양한 크기의 이미지 생성 (가로 400-1200, 세로 300-800)
                $width = mt_rand(400, 1200);
                $height = mt_rand(300, 800);

                $imageUrl = upload_random_image($width, $height);
                if ($imageUrl !== null) {
                    $files[] = $imageUrl;
                    echo "    ✅ 이미지 업로드 성공: " . basename($imageUrl) . "\n";
                }
            }
        }

        // 이미지 URL을 콤마로 구분된 문자열로 변환
        $filesString = !empty($files) ? implode(',', $files) : '';

        // ====================================================================
        // create_post() 함수를 사용한 게시글 생성
        // ====================================================================
        // create_post() 함수는 lib/post/post.crud.php에 정의되어 있습니다.
        // 이 함수는 다음 작업을 수행합니다:
        // 1. 로그인 확인 (login() 함수 호출)
        // 2. 입력값 검증 (category 필수)
        // 3. PDO prepared statement를 사용한 안전한 DB 삽입
        // 4. 생성된 게시글의 PostModel 객체 반환
        $postData = [
            'category' => $category,
            'title' => $title,
            'content' => $content,
        ];

        // 이미지가 있으면 files 파라미터 추가
        if (!empty($filesString)) {
            $postData['files'] = $filesString;
        }

        $post = create_post($postData);

        // 생성 성공 여부 확인
        if ($post instanceof PostModel) {
            $successCount++;

            // 진행 상황 출력 (모든 게시글 또는 10개마다)
            // 모든 게시글은 'banana' 사용자로 생성됩니다
            $imageInfo = !empty($files) ? " [이미지: " . count($files) . "개]" : "";
            if ($i % 10 == 0 || $i == $count) {
                echo "✅ {$i}/{$count} (ID: {$post->id}, Author: banana, Category: {$category}{$imageInfo})\n";
            }
        } else {
            // create_post()가 PostModel 객체를 반환하지 않으면 실패
            $failCount++;
            echo "❌ Failed ({$i}/{$count}): create_post() did not return a PostModel\n";
        }

        // 임시 세션 정리
        unset($_COOKIE[SESSION_ID]);

    } catch (Exception $e) {
        // 예외 발생 시 에러 메시지 출력 및 실패 카운트 증가
        $failCount++;
        echo "❌ Failed ({$i}/{$count}): " . $e->getMessage() . "\n";

        // 임시 세션 정리
        if (isset($_COOKIE[SESSION_ID])) {
            unset($_COOKIE[SESSION_ID]);
        }
    }
}

// ========================================================================
// 5단계: 결과 통계 출력
// ========================================================================

echo "\n";
echo "========================================\n";
echo "🎉 Random Post Generation Complete!\n";
echo "========================================\n";
echo "✅ Success: {$successCount}\n";
echo "❌ Failed: {$failCount}\n";
echo "========================================\n";

// ========================================================================
// 6단계: 카테고리별 게시글 통계 출력
// ========================================================================

echo "\n📊 Post Statistics by Category:\n";
foreach ($categories as $cat) {
    // Prepared statement로 카테고리별 게시글 수 조회
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE category = ?");
    $stmt->execute([$cat]);
    $catCount = $stmt->fetchColumn();
    echo "  - {$cat}: {$catCount}\n";
}

// ========================================================================
// 7단계: 전체 게시글 수 출력
// ========================================================================

$stmt = $pdo->query("SELECT COUNT(*) FROM posts");
$totalPosts = $stmt->fetchColumn();
echo "\n📝 Total posts: {$totalPosts}\n";
