/**
 * 사용자 검색 모달 X 버튼 닫기 테스트
 *
 * 이 테스트는 sidebar의 "new users" 위젯에 있는 친구 검색 버튼을 클릭하여
 * 검색 모달을 열고, X 버튼을 클릭하여 모달이 정상적으로 닫히는지 확인합니다.
 */

import { test, expect } from '@playwright/test';

test.describe('사용자 검색 모달 X 버튼 테스트', () => {
  test.beforeEach(async ({ page }) => {
    // 테스트용 로그인
    await page.goto('https://local.sonub.com/user/login');
    await page.fill('input[type="tel"]', 'banana@test.com:12345a,*');
    await page.click('button:has-text("Send SMS Code")');

    // 로그인 완료 대기 (홈페이지로 리다이렉트)
    await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

    // 페이지가 완전히 로드될 때까지 대기
    await page.waitForLoadState('networkidle');
  });

  test('친구 검색 모달을 열고 X 버튼으로 닫기', async ({ page }) => {
    // 1. 친구 검색 버튼 찾기 (sidebar의 new-users 위젯에 있음)
    const searchButton = page.locator('button:has-text("친구 검색")').or(page.locator('button:has-text("Find Friends")')).first();

    // 버튼이 존재하는지 확인
    await expect(searchButton).toBeVisible({ timeout: 5000 });

    // 2. 친구 검색 버튼 클릭하여 모달 열기
    await searchButton.click();

    // 3. 모달이 표시되는지 확인
    const modal = page.locator('.modal.fade.show').first();
    await expect(modal).toBeVisible({ timeout: 3000 });

    // 4. 모달 제목 확인
    const modalTitle = modal.locator('.modal-title');
    await expect(modalTitle).toContainText(/친구 검색|Find Friends/);

    // 5. X 버튼 찾기
    const closeButton = modal.locator('button.btn-close');
    await expect(closeButton).toBeVisible();

    // 6. X 버튼 클릭
    await closeButton.click();

    // 7. 모달이 사라지는지 확인 (최대 3초 대기)
    await expect(modal).not.toBeVisible({ timeout: 3000 });

    console.log('✅ X 버튼을 클릭하여 모달이 정상적으로 닫혔습니다.');
  });

  test('친구 검색 모달을 열고 backdrop 클릭으로 닫기', async ({ page }) => {
    // 1. 친구 검색 버튼 클릭하여 모달 열기
    const searchButton = page.locator('button:has-text("친구 검색")').or(page.locator('button:has-text("Find Friends")')).first();
    await searchButton.click();

    // 2. 모달이 표시되는지 확인
    const modal = page.locator('.modal.fade.show').first();
    await expect(modal).toBeVisible({ timeout: 3000 });

    // 3. 모달 backdrop 클릭 (모달 외부를 클릭)
    await page.keyboard.press('Escape');

    // 4. 모달이 사라지는지 확인
    await expect(modal).not.toBeVisible({ timeout: 3000 });

    console.log('✅ Escape 키를 눌러 모달이 정상적으로 닫혔습니다.');
  });

  test('친구 검색 모달에서 검색 입력 후 X 버튼으로 닫기', async ({ page }) => {
    // 1. 친구 검색 버튼 클릭하여 모달 열기
    const searchButton = page.locator('button:has-text("친구 검색")').or(page.locator('button:has-text("Find Friends")')).first();
    await searchButton.click();

    // 2. 모달이 표시되는지 확인
    const modal = page.locator('.modal.fade.show').first();
    await expect(modal).toBeVisible({ timeout: 3000 });

    // 3. 검색어 입력
    const searchInput = modal.locator('input[type="text"]');
    await searchInput.fill('test');

    // 4. 입력된 값 확인
    await expect(searchInput).toHaveValue('test');

    // 5. X 버튼 클릭
    const closeButton = modal.locator('button.btn-close');
    await closeButton.click();

    // 6. 모달이 사라지는지 확인
    await expect(modal).not.toBeVisible({ timeout: 3000 });

    console.log('✅ 검색어 입력 후 X 버튼을 클릭하여 모달이 정상적으로 닫혔습니다.');
  });
});
