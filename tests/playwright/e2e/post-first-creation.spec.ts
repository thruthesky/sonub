/**
 * 첫 번째 게시글 작성 테스트
 *
 * 카테고리에 게시글이 없을 때 첫 번째 게시글을 작성하면
 * 페이지 새로고침 없이 즉시 목록에 표시되는지 테스트합니다.
 *
 * 테스트 시나리오:
 * 1. 로그인
 * 2. 특정 카테고리로 이동
 * 3. 게시글이 비어있는 상태 확인
 * 4. 첫 번째 게시글 작성
 * 5. 작성된 게시글이 즉시 목록에 표시되는지 확인
 * 6. 테스트 정리 (작성한 게시글 삭제)
 */

import { test, expect } from '@playwright/test';

// 테스트용 로그인 전화번호 (SMS 인증 없이 즉시 로그인)
const TEST_LOGIN_PHONE = 'banana@test.com:12345a,*';

// 테스트할 카테고리
const TEST_CATEGORY = 'discussion';

test.describe('첫 번째 게시글 작성 기능', () => {
    test('빈 카테고리에서 첫 게시글 작성 시 즉시 목록에 표시되어야 함', async ({ page }) => {
        // ========================================================================
        // 1단계: 로그인
        // ========================================================================
        await page.goto('https://local.sonub.com/user/login');
        await page.fill('input[type="tel"]', TEST_LOGIN_PHONE);
        await page.click('button:has-text("Send SMS Code")');
        await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

        // ========================================================================
        // 2단계: 게시글 목록 페이지로 이동
        // ========================================================================
        await page.goto(`https://local.sonub.com/post/list?category=${TEST_CATEGORY}`);
        await page.waitForLoadState('networkidle');

        // ========================================================================
        // 3단계: 현재 카테고리의 모든 게시글 삭제 (빈 상태로 만들기)
        // ========================================================================
        // 게시글이 있는지 확인
        const postCards = page.locator('article.post-card');
        const postCount = await postCards.count();

        console.log(`현재 카테고리에 ${postCount}개의 게시글이 있습니다.`);

        // 게시글이 있으면 모두 삭제 (최대 10개까지)
        for (let i = 0; i < Math.min(postCount, 10); i++) {
            // 첫 번째 게시글의 삭제 버튼 찾기 (fa-trash 아이콘)
            const deleteButton = page.locator('article.post-card').first().locator('i.fa-trash').first();

            // 삭제 버튼이 보이는지 확인
            const isVisible = await deleteButton.isVisible();
            if (!isVisible) {
                console.log('삭제 버튼이 보이지 않습니다. (본인 게시글이 아님)');
                break;
            }

            // 삭제 confirm 다이얼로그 처리
            page.once('dialog', async (dialog) => {
                console.log('Dialog message:', dialog.message());
                await dialog.accept();
            });

            // 삭제 버튼 클릭
            await deleteButton.click();

            // 삭제 완료 대기
            await page.waitForTimeout(1000);
        }

        // ========================================================================
        // 4단계: 빈 상태 확인
        // ========================================================================
        // 페이지 새로고침하여 최신 상태 확인
        await page.reload();
        await page.waitForLoadState('networkidle');

        // "아직 게시글이 없습니다" 메시지가 표시되는지 확인
        const emptyMessage = page.locator('text=아직 게시글이 없습니다');
        const isEmptyMessageVisible = await emptyMessage.isVisible();

        console.log(`빈 상태 메시지 표시: ${isEmptyMessageVisible}`);

        // 빈 상태가 아니면 테스트 건너뛰기
        if (!isEmptyMessageVisible) {
            console.log('카테고리가 비어있지 않습니다. 테스트를 건너뜁니다.');
            test.skip();
            return;
        }

        // ========================================================================
        // 5단계: 첫 번째 게시글 작성
        // ========================================================================
        // 게시글 작성 폼 확장 (클릭 가능한 버튼)
        const createTrigger = page.locator('#post-list-create .post-create-trigger');
        await expect(createTrigger).toBeVisible();
        await createTrigger.click();

        // textarea가 표시될 때까지 대기
        const textarea = page.locator('#post-list-create textarea[name="content"]');
        await expect(textarea).toBeVisible();

        // 게시글 내용 입력
        const testContent = `테스트 게시글 - ${new Date().toISOString()}`;
        await textarea.fill(testContent);

        // 게시 버튼 클릭
        const submitButton = page.locator('#post-list-create button.btn-post');
        await expect(submitButton).toBeEnabled();
        await submitButton.click();

        // ========================================================================
        // 6단계: 작성된 게시글이 즉시 목록에 표시되는지 확인
        // ========================================================================
        // "아직 게시글이 없습니다" 메시지가 사라졌는지 확인
        await expect(emptyMessage).not.toBeVisible({ timeout: 5000 });

        // 작성한 게시글이 목록에 표시되는지 확인
        const newPost = page.locator('article.post-card').filter({ hasText: testContent });
        await expect(newPost).toBeVisible({ timeout: 5000 });

        console.log('✅ 첫 번째 게시글이 즉시 목록에 표시되었습니다!');

        // ========================================================================
        // 7단계: 테스트 정리 (작성한 게시글 삭제)
        // ========================================================================
        // 삭제 confirm 다이얼로그 처리
        page.once('dialog', async (dialog) => {
            await dialog.accept();
        });

        // 방금 작성한 게시글의 삭제 버튼 클릭
        const deleteButtonFinal = newPost.locator('i.fa-trash').first();
        await deleteButtonFinal.click();

        // 삭제 완료 대기
        await page.waitForTimeout(1000);

        console.log('✅ 테스트 정리 완료 (게시글 삭제)');
    });

    test('두 번째 게시글 작성 시에도 즉시 목록에 표시되어야 함', async ({ page }) => {
        // ========================================================================
        // 1단계: 로그인
        // ========================================================================
        await page.goto('https://local.sonub.com/user/login');
        await page.fill('input[type="tel"]', TEST_LOGIN_PHONE);
        await page.click('button:has-text("Send SMS Code")');
        await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

        // ========================================================================
        // 2단계: 게시글 목록 페이지로 이동
        // ========================================================================
        await page.goto(`https://local.sonub.com/post/list?category=${TEST_CATEGORY}`);
        await page.waitForLoadState('networkidle');

        // ========================================================================
        // 3단계: 첫 번째 게시글 작성
        // ========================================================================
        const createTrigger = page.locator('#post-list-create .post-create-trigger');
        await createTrigger.click();

        const textarea = page.locator('#post-list-create textarea[name="content"]');
        const testContent1 = `첫 번째 테스트 게시글 - ${new Date().toISOString()}`;
        await textarea.fill(testContent1);

        const submitButton = page.locator('#post-list-create button.btn-post');
        await submitButton.click();

        // 첫 번째 게시글이 표시될 때까지 대기
        const firstPost = page.locator('article.post-card').filter({ hasText: testContent1 });
        await expect(firstPost).toBeVisible({ timeout: 5000 });

        // ========================================================================
        // 4단계: 두 번째 게시글 작성
        // ========================================================================
        // 작성 폼 다시 확장
        await createTrigger.click();

        const testContent2 = `두 번째 테스트 게시글 - ${new Date().toISOString()}`;
        await textarea.fill(testContent2);
        await submitButton.click();

        // ========================================================================
        // 5단계: 두 번째 게시글이 즉시 목록 상단에 표시되는지 확인
        // ========================================================================
        const secondPost = page.locator('article.post-card').filter({ hasText: testContent2 });
        await expect(secondPost).toBeVisible({ timeout: 5000 });

        // 두 번째 게시글이 첫 번째 게시글보다 위에 있는지 확인 (최신 글이 상단)
        const allPosts = page.locator('article.post-card');
        const firstPostIndex = await allPosts.filter({ hasText: testContent1 }).first().evaluate(
            (el) => Array.from(el.parentElement!.children).indexOf(el)
        );
        const secondPostIndex = await allPosts.filter({ hasText: testContent2 }).first().evaluate(
            (el) => Array.from(el.parentElement!.children).indexOf(el)
        );

        expect(secondPostIndex).toBeLessThan(firstPostIndex);
        console.log('✅ 두 번째 게시글이 첫 번째 게시글보다 위에 표시되었습니다!');

        // ========================================================================
        // 6단계: 테스트 정리 (작성한 게시글들 삭제)
        // ========================================================================
        // 두 번째 게시글 삭제
        page.once('dialog', async (dialog) => await dialog.accept());
        await secondPost.locator('i.fa-trash').first().click();
        await page.waitForTimeout(1000);

        // 첫 번째 게시글 삭제
        page.once('dialog', async (dialog) => await dialog.accept());
        await firstPost.locator('i.fa-trash').first().click();
        await page.waitForTimeout(1000);

        console.log('✅ 테스트 정리 완료 (게시글들 삭제)');
    });
});
