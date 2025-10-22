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

// NOTE: that this function is cannot be used as children of another vue.js component
function login_user_profile_photo(): void
{
    static $id = 0;
    $id++;

    ?>

    <?php if ($id == 1): ?>
        <style>
            .login-user-profile-photo {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                object-fit: cover;
            }
        </style>
    <?php endif; ?>

    <data id="login-user-profile-photo-<?= $id ?>" :title="displayName">
        <?php if (login()->photo_url) : ?>
            <img src="<?= login()->photo_url ?>" alt="Profile Photo" class="login-user-profile-photo">
        <?php else : ?>
            <i class="bi bi-person-circle fs-3"></i>
        <?php endif; ?>
    </data>
    <script>
        ready(() => {
            Vue.createApp({
                template: `
                    <img v-if="state.user.photo_url" :src="state.user?.photo_url" class="login-user-profile-photo">
                    <i v-else class="bi bi-person-circle fs-3"></i>
                `,
                data() {
                    return {
                        state: window.Store?.state || {
                            user: {},
                        },
                    };
                },
                computed: {
                    // first_name, middle_name, last_name으로 전체 이름 조합
                    displayName() {
                        if (!this.state.user) return '';
                        const parts = [
                            this.state.user.first_name,
                            this.state.user.middle_name,
                            this.state.user.last_name
                        ].filter(name => name && name.trim() !== '');
                        return parts.length > 0 ? parts.join(' ') : 'Anonymous';
                    }
                }
            }).mount('#login-user-profile-photo-<?= $id ?>');
        })
    </script>
<?php
}
