<?php
inject_index_language();
?>
<h1 class="display-4"><?php echo t()->{'Welcome to Sonub'}; ?></h1>
<p class="lead"><?php echo t()->{'Sonub application is running!'}; ?></p>

<!-- Browser Language Display -->
<div class="alert alert-secondary" role="alert">
    <strong><?php echo t()->{'Browser Language'}; ?>:</strong> <?php echo get_browser_language(); ?>
</div>



<p>
    Nickname: <?php echo login()->display_name ?? 'Guest'; ?><br>
</p>


<ul>
    TODO LIST:

    <li>사진, 동영상, 파일 업로드: file/file.upload.php, file.delete.php, file.multi-upload.php</li>
    <li>프로필 사진 변경</li>
    <li>게시글: 설정 테이블없이 그냥, PostConfig() 모델을 만들고, post.config.php 에서 필요한 만큼 직접 소스 코드로 지정을 한다. post/list.php, create.php, view.php, update.php, delete.php.</li>
    <li>댓글: comment/list.php, create.php, update.php, delete.php</li>
    <li>글/댓글 기능을 바탕으로, Facebook wall 기능, 인스타 기능</li>
    <li>친구 맺기, 친구 목록, 친구에게만 자신의 글이 전달되도록.</li>
    <li>1:1 채팅, 그룹 채팅</li>
    <li>지역 기반 모임 서비스</li>
    <li>실시간 알림 서비스</li>
    <li>좋아요, 싫어요 기능</li>
</ul>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title"><?php echo t()->{'Getting Started'}; ?></h5>
        <p class="card-text"><?php echo t()->{'Start building your application with our powerful features.'}; ?></p>
        <a href="#" class="btn btn-primary"><?php echo t()->{'Learn More'}; ?></a>
    </div>
</div>

<?php

function inject_index_language()
{
    t()->inject([
        'Welcome to Sonub' => ['en' => 'Welcome to Sonub', 'ko' => 'Sonub에 오신 것을 환영합니다', 'ja' => 'Sonubへようこそ', 'zh' => '欢迎使用Sonub'],
        'Sonub application is running!' => ['en' => 'Sonub application is running!', 'ko' => 'Sonub 애플리케이션이 실행 중입니다!', 'ja' => 'Sonubアプリケーションが実行中です！', 'zh' => 'Sonub应用程序正在运行！'],
        'Browser Language' => ['en' => 'Browser Language', 'ko' => '브라우저 언어', 'ja' => 'ブラウザ言語', 'zh' => '浏览器语言'],
        'This is a sample application using Firebase Phone Authentication and a MySQL database.' => ['en' => 'This is a sample application using Firebase Phone Authentication and a MySQL database.', 'ko' => '이것은 Firebase 전화 인증과 MySQL 데이터베이스를 사용하는 샘플 애플리케이션입니다.', 'ja' => 'これはFirebase電話認証とMySQLデータベースを使用したサンプルアプリケーションです。', 'zh' => '这是一个使用Firebase电话认证和MySQL数据库的示例应用程序。'],
        'Getting Started' => ['en' => 'Getting Started', 'ko' => '시작하기', 'ja' => 'はじめに', 'zh' => '入门指南'],
        'Start building your application with our powerful features.' => ['en' => 'Start building your application with our powerful features.', 'ko' => '강력한 기능으로 애플리케이션 구축을 시작하세요.', 'ja' => '強力な機能でアプリケーションの構築を開始しましょう。', 'zh' => '使用我们的强大功能开始构建您的应用程序。'],
        'Learn More' => ['en' => 'Learn More', 'ko' => '더 알아보기', 'ja' => 'もっと詳しく', 'zh' => '了解更多'],
    ]);
}
