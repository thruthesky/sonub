<?php
inject_index_language();
?>
<h1 class="display-4"><?php echo tr('Welcome to Sonub'); ?></h1>
<p class="lead"><?php echo tr('Sonub application is running!'); ?></p>
<div class="alert alert-info" role="alert">
    <?php echo tr('This is a sample application using Firebase Phone Authentication and a MySQL database.'); ?>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title"><?php echo tr('Getting Started'); ?></h5>
        <p class="card-text"><?php echo tr('Start building your application with our powerful features.'); ?></p>
        <a href="#" class="btn btn-primary"><?php echo tr('Learn More'); ?></a>
    </div>
</div>

<?php

function inject_index_language()
{
    t()->inject([
        'Welcome to Sonub' => ['en' => 'Welcome to Sonub', 'ko' => 'Sonub에 오신 것을 환영합니다', 'ja' => 'Sonubへようこそ', 'zh' => '欢迎使用Sonub'],
        'Sonub application is running!' => ['en' => 'Sonub application is running!', 'ko' => 'Sonub 애플리케이션이 실행 중입니다!', 'ja' => 'Sonubアプリケーションが実行中です！', 'zh' => 'Sonub应用程序正在运行！'],
        'This is a sample application using Firebase Phone Authentication and a MySQL database.' => ['en' => 'This is a sample application using Firebase Phone Authentication and a MySQL database.', 'ko' => '이것은 Firebase 전화 인증과 MySQL 데이터베이스를 사용하는 샘플 애플리케이션입니다.', 'ja' => 'これはFirebase電話認証とMySQLデータベースを使用したサンプルアプリケーションです。', 'zh' => '这是一个使用Firebase电话认证和MySQL数据库的示例应用程序。'],
        'Getting Started' => ['en' => 'Getting Started', 'ko' => '시작하기', 'ja' => 'はじめに', 'zh' => '入门指南'],
        'Start building your application with our powerful features.' => ['en' => 'Start building your application with our powerful features.', 'ko' => '강력한 기능으로 애플리케이션 구축을 시작하세요.', 'ja' => '強力な機能でアプリケーションの構築を開始しましょう。', 'zh' => '使用我们的强大功能开始构建您的应用程序。'],
        'Learn More' => ['en' => 'Learn More', 'ko' => '더 알아보기', 'ja' => 'もっと詳しく', 'zh' => '了解更多'],
    ]);
}
