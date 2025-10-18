/**
 * 프로필 페이지 친구 추가 테스트
 *
 * 사용자 프로필 페이지에서 친구 추가 버튼이 정상적으로 표시되고 동작하는지 테스트합니다.
 */

import { test, expect } from '@playwright/test';

// 테스트용 로그인 전화번호 (SMS 인증 없이 즉시 로그인)
const TEST_LOGIN_PHONE = 'banana@test.com:12345a,*';

test.describe('프로필 페이지 친구 추가 기능', () => {
    test('다른 사용자 프로필에서 친구 추가 버튼이 표시되어야 함', async ({ page }) => {
        // 로그인 페이지로 이동
        await page.goto('https://local.sonub.com/user/login');

        // 로그인 (SMS 인증 없이)
        await page.fill('input[type="tel"]', TEST_LOGIN_PHONE);
        await page.click('button:has-text("Send SMS Code")');

        // 로그인 완료 대기 (홈페이지로 리다이렉트)
        await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

        // 다른 사용자의 프로필 페이지로 이동 (ID 31)
        await page.goto('https://local.sonub.com/user/profile?id=31');

        // 페이지 로드 대기
        await page.waitForLoadState('networkidle');

        // 친구 추가 버튼이 표시되는지 확인 (Vue.js 버튼, class 선택자 사용)
        const addFriendButton = page.locator('.btn-add-friend');
        await expect(addFriendButton).toBeVisible();

        // 버튼 텍스트 확인 (다국어 지원)
        const buttonText = await addFriendButton.textContent();
        expect(buttonText).toMatch(/친구 추가|Add Friend/);
    });

    test('본인 프로필에서는 친구 추가 버튼이 표시되지 않아야 함', async ({ page }) => {
        // 로그인 페이지로 이동
        await page.goto('https://local.sonub.com/user/login');

        // 로그인 (SMS 인증 없이)
        await page.fill('input[type="tel"]', TEST_LOGIN_PHONE);
        await page.click('button:has-text("Send SMS Code")');

        // 로그인 완료 대기 (홈페이지로 리다이렉트)
        await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

        // 본인 프로필 페이지로 이동 (파라미터 없이)
        await page.goto('https://local.sonub.com/user/profile');

        // 페이지 로드 대기
        await page.waitForLoadState('networkidle');

        // 친구 추가 버튼이 표시되지 않는지 확인
        const addFriendButton = page.locator('.btn-add-friend');
        await expect(addFriendButton).not.toBeVisible();

        // 프로필 수정 버튼이 표시되는지 확인
        const editProfileButton = page.locator('.btn-edit-profile');
        await expect(editProfileButton).toBeVisible();
    });

    test.skip('친구 추가 버튼 클릭 시 친구 요청이 전송되어야 함', async ({ page }) => {
        // 이 테스트는 실제 친구 요청을 전송하므로 기본적으로 건너뜁니다.
        // 필요 시 test.skip을 test로 변경하여 실행

        // 로그인 페이지로 이동
        await page.goto('https://local.sonub.com/user/login');

        // 로그인 (SMS 인증 없이)
        await page.fill('input[type="tel"]', TEST_LOGIN_PHONE);
        await page.click('button:has-text("Send SMS Code")');

        // 로그인 완료 대기
        await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

        // 다른 사용자의 프로필 페이지로 이동
        await page.goto('https://local.sonub.com/user/profile?id=31');
        await page.waitForLoadState('networkidle');

        // 친구 추가 버튼 클릭
        const addFriendButton = page.locator('.btn-add-friend');

        // alert 다이얼로그 처리 (성공 메시지)
        page.on('dialog', async (dialog) => {
            const message = dialog.message();
            expect(message).toMatch(/친구 요청|Friend request/);
            await dialog.accept();
        });

        await addFriendButton.click();

        // 버튼이 비활성화되는지 확인 (요청 중 또는 완료)
        await expect(addFriendButton).toBeDisabled();

        // 버튼 텍스트가 변경되었는지 확인
        await page.waitForTimeout(1000);
        const buttonText = await addFriendButton.textContent();
        expect(buttonText).toMatch(/친구 요청을 보냈습니다|Friend request sent|요청 중|Requesting/);

    });
});
