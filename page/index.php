<?php
inject_index_language();
?>
<h1 class="display-4"><?php echo t()->{'Welcome to Sonub'}; ?></h1>
<p class="lead"><?php echo t()->{'Sonub application is running!'}; ?></p>

<h2 class="todo">TODO:</h2>
<ul>
    <li>닉네임이 unique 이지만, nullable 하게 할 것</li>
    <li>페이스북 형태의 소셜 글 목록, 무제한 스크롤링 기능 구현</li>
</ul>

<h1 class="welcome-text"><?= t()->환영합니다 ?>, <?= login()->display_name ?? t()->손님 ?></h1>


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
        'Welcome to Sonub' => ['ko' => 'Sonub에 오신 것을 환영합니다', 'en' => 'Welcome to Sonub', 'ja' => 'Sonubへようこそ', 'zh' => '欢迎使用Sonub'],
        'Sonub application is running!' => ['ko' => 'Sonub 애플리케이션이 실행 중입니다!', 'en' => 'Sonub application is running!', 'ja' => 'Sonubアプリケーションが実行中です！', 'zh' => 'Sonub应用程序正在运行！'],
        'Browser Language' => ['ko' => '브라우저 언어', 'en' => 'Browser Language', 'ja' => 'ブラウザ言語', 'zh' => '浏览器语言'],
        'This is a sample application using Firebase Phone Authentication and a MySQL database.' => ['ko' => '이것은 Firebase 전화 인증과 MySQL 데이터베이스를 사용하는 샘플 애플리케이션입니다.', 'en' => 'This is a sample application using Firebase Phone Authentication and a MySQL database.', 'ja' => 'これはFirebase電話認証とMySQLデータベースを使用したサンプルアプリケーションです。', 'zh' => '这是一个使用Firebase电话认证和MySQL数据库的示例应用程序。'],
        'Getting Started' => ['ko' => '시작하기', 'en' => 'Getting Started', 'ja' => 'はじめに', 'zh' => '入门指南'],
        'Start building your application with our powerful features.' => ['ko' => '강력한 기능으로 애플리케이션 구축을 시작하세요.', 'en' => 'Start building your application with our powerful features.', 'ja' => '強力な機能でアプリケーションの構築を開始しましょう。', 'zh' => '使用我们的强大功能开始构建您的应用程序。'],
        'Learn More' => ['ko' => '더 알아보기', 'en' => 'Learn More', 'ja' => 'もっと詳しく', 'zh' => '了解更多'],
        '환영합니다' => ['ko' => '환영합니다', 'en' => 'Welcome', 'ja' => 'ようこそ', 'zh' => '欢迎'],
        '손님' => ['ko' => '손님', 'en' => 'Guest', 'ja' => 'ゲスト', 'zh' => '访客'],
    ]);
}
