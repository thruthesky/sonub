<?php

include_once ROOT_DIR . '/lib/defines.php';
include_once ROOT_DIR . '/etc/app.version.php';
include_once ROOT_DIR . '/lib/ApiException.php';
include_once ROOT_DIR . '/lib/functions.php';


include_once ROOT_DIR . '/lib/app/app.info.php';
include_once ROOT_DIR . '/lib/l10n/t.php';
include_once ROOT_DIR . '/lib/l10n/texts.php';
include_once ROOT_DIR . '/lib/l10n/language.functions.php';
include_once ROOT_DIR . '/lib/api/input.functions.php';

// 데이터베이스 설정 로드
if (is_dev_computer()) {
    include_once ROOT_DIR . '/etc/config/db.dev.config.php';
} else {
    include_once ROOT_DIR . '/etc/config/db.config.php';
}

include_once ROOT_DIR . '/lib/db/db.php';
include_once ROOT_DIR . '/lib/db/entity.php';
include_once ROOT_DIR . '/lib/href/href.functions.php';
include_once ROOT_DIR . '/lib/page/page.functions.php';
include_once ROOT_DIR . '/lib/debug/debug.functions.php';
include_once ROOT_DIR . '/lib/user/user.crud.php';
include_once ROOT_DIR . '/lib/user/user.functions.php';
include_once ROOT_DIR . '/lib/user/user.model.php';
include_once ROOT_DIR . '/lib/post/post.model.php';
include_once ROOT_DIR . '/lib/post/post.crud.php';
include_once ROOT_DIR . '/lib/file/file.upload.php';
include_once ROOT_DIR . '/lib/file/file.delete.php';

include_once ROOT_DIR . '/etc/config/app.config.php';
include_once ROOT_DIR . '/lib/pagination/basic.pagination.php';
include_once ROOT_DIR . '/etc/config/category.config.php';
include_once ROOT_DIR . '/lib/error/error.functions.php';
