<?php
function display_file(string $file): void
{
    $fileSrc = trim($file);

    // 파일 확장자 추출
    $extension = strtolower(pathinfo($fileSrc, PATHINFO_EXTENSION));

    // 이미지 파일 확장자 목록
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($extension, $imageExtensions)) {
?>
        <img src="<?= htmlspecialchars($fileSrc) ?>" alt="Post image" class="post-image">
    <?php
    } else {
        // 기타 파일인 경우 다운로드 링크로 출력
        $fileName = basename($fileSrc);
    ?>
        <div class="post-file">
            <a href="<?= htmlspecialchars($fileSrc) ?>" download><?= htmlspecialchars($fileName) ?></a>
        </div>
<?php
    }
}
