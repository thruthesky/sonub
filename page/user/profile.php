<?php
inject_user_profile_language();
?>
<h1><?php echo tr('Update Profile'); ?></h1>
<a href="<?= href()->user->profile_edit ?>">Edit My Profile</a>
<?php
function inject_user_profile_language()
{
    t()->inject([
        '프로필 업데이트' => 'Update Profile',
        '이름' => 'Name',
        '이메일' => 'Email',
        '저장' => 'Save',
        '프로필이 성공적으로 업데이트되었습니다.' => 'Profile updated successfully.',
        '프로필 업데이트 중 오류가 발생했습니다.' => 'An error occurred while updating the profile.',
    ]);
}
