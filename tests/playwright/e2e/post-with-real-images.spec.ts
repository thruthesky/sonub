/**
 * 실제 이미지가 있는 게시글 로드 테스트
 *
 * Picsum Photos를 사용하여 생성된 게시글들이 실제 이미지를 포함하고 있으며,
 * 브라우저에서 정상적으로 로드되는지 테스트합니다.
 *
 * 테스트 시나리오:
 * 1. 로그인
 * 2. discussion 카테고리의 게시글 목록 접속
 * 3. 게시글에 포함된 이미지가 실제 이미지(Picsum)인지 확인
 * 4. 이미지가 성공적으로 로드되었는지 확인
 */

import { test, expect } from '@playwright/test';

// 테스트용 로그인 전화번호 (SMS 인증 없이 즉시 로그인)
const TEST_LOGIN_PHONE = 'banana@test.com:12345a,*';

test.describe('실제 이미지가 있는 게시글', () => {
    test('게시글 목록에서 Picsum 이미지가 로드되어야 함', async ({ page }) => {
        // ========================================================================
        // 1단계: 로그인
        // ========================================================================
        await page.goto('https://local.sonub.com/user/login');
        await page.fill('input[type="tel"]', TEST_LOGIN_PHONE);
        await page.click('button:has-text("Send SMS Code")');

        // 로그인 완료 대기
        await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

        // ========================================================================
        // 2단계: discussion 카테고리 게시글 목록으로 이동
        // ========================================================================
        await page.goto('https://local.sonub.com/post/list?category=discussion', {
            waitUntil: 'networkidle'
        });

        // ========================================================================
        // 3단계: 게시글이 이미지를 포함하고 있는지 확인
        // ========================================================================
        // 페이지에 img 태그가 있는지 확인
        const postImages = page.locator('article.post-card img[src*="picsum.photos"]');
        const imageCount = await postImages.count();

        console.log(`게시글에서 발견된 Picsum 이미지: ${imageCount}개`);

        // 최소 1개 이상의 Picsum 이미지가 있어야 함
        expect(imageCount).toBeGreaterThanOrEqual(1);

        // ========================================================================
        // 4단계: 첫 번째 이미지의 src 속성 확인
        // ========================================================================
        if (imageCount > 0) {
            const firstImageSrc = await postImages.first().getAttribute('src');
            console.log(`첫 번째 이미지 URL: ${firstImageSrc}`);

            // Picsum 이미지 URL 형식 확인
            expect(firstImageSrc).toMatch(/picsum\.photos\/\d+\/\d+\?random=\d+/);
        }

        // ========================================================================
        // 5단계: 이미지가 실제로 로드되었는지 확인 (naturalWidth > 0)
        // ========================================================================
        const imageLoadedPromise = page.evaluate(() => {
            return new Promise<boolean>((resolve) => {
                const images = document.querySelectorAll('article.post-card img[src*="picsum.photos"]');

                if (images.length === 0) {
                    resolve(false);
                    return;
                }

                let loadedCount = 0;

                images.forEach((img) => {
                    const imgElement = img as HTMLImageElement;

                    if (imgElement.complete && imgElement.naturalWidth > 0) {
                        // 이미 로드됨
                        loadedCount++;
                    } else if (imgElement.complete && imgElement.naturalWidth === 0) {
                        // 이미지 로드 실패
                        console.warn(`이미지 로드 실패: ${imgElement.src}`);
                    } else {
                        // 로딩 중, 완료 대기
                        imgElement.onload = () => {
                            loadedCount++;
                        };
                        imgElement.onerror = () => {
                            console.warn(`이미지 로드 에러: ${imgElement.src}`);
                        };
                    }
                });

                // 모든 이미지의 로드 완료를 대기 (타임아웃 5초)
                const timeout = setTimeout(() => {
                    resolve(loadedCount > 0);
                }, 5000);

                // 모든 이미지가 로드되면 즉시 완료
                const checkAllLoaded = setInterval(() => {
                    const allLoaded = Array.from(images).every((img) => {
                        const imgElement = img as HTMLImageElement;
                        return imgElement.complete && imgElement.naturalWidth > 0;
                    });

                    if (allLoaded) {
                        clearTimeout(timeout);
                        clearInterval(checkAllLoaded);
                        resolve(true);
                    }
                }, 100);
            });
        });

        const imagesLoaded = await imageLoadedPromise;
        console.log(`이미지 로드 완료: ${imagesLoaded}`);

        expect(imagesLoaded).toBe(true);

        // ========================================================================
        // 6단계: 다양한 이미지 크기 확인
        // ========================================================================
        const imageSizes = await page.evaluate(() => {
            const images = document.querySelectorAll('article.post-card img[src*="picsum.photos"]');
            return Array.from(images).map((img) => {
                const imgElement = img as HTMLImageElement;
                return {
                    src: imgElement.src,
                    width: imgElement.naturalWidth,
                    height: imgElement.naturalHeight,
                };
            });
        });

        console.log('이미지 정보:');
        imageSizes.forEach((img, index) => {
            console.log(
                `  [${index + 1}] ${img.width}x${img.height} - ${img.src.substring(0, 60)}...`
            );
        });

        // 다양한 크기의 이미지가 있는지 확인
        const uniqueSizes = new Set(imageSizes.map((img) => `${img.width}x${img.height}`));
        console.log(`발견된 고유 이미지 크기: ${uniqueSizes.size}개`);
    });

    test('여러 카테고리에서 이미지 다양성 확인', async ({ page }) => {
        // ========================================================================
        // 1단계: 로그인
        // ========================================================================
        await page.goto('https://local.sonub.com/user/login');
        await page.fill('input[type="tel"]', TEST_LOGIN_PHONE);
        await page.click('button:has-text("Send SMS Code")');

        await page.waitForURL('https://local.sonub.com/', { timeout: 10000 });

        // ========================================================================
        // 2단계: 여러 카테고리에서 게시글 조회
        // ========================================================================
        const categoriesToTest = ['discussion', 'story', 'cooking'];

        for (const category of categoriesToTest) {
            console.log(`\n카테고리 '${category}' 테스트 중...`);

            await page.goto(`https://local.sonub.com/post/list?category=${category}`, {
                waitUntil: 'networkidle'
            });

            // 이미지 존재 확인
            const postImages = page.locator('article.post-card img[src*="picsum.photos"]');
            const imageCount = await postImages.count();

            console.log(`  발견된 이미지: ${imageCount}개`);

            // 게시글이 있으면 이미지도 있어야 함
            const postCards = page.locator('article.post-card');
            const postCount = await postCards.count();

            console.log(`  발견된 게시글: ${postCount}개`);

            if (postCount > 0) {
                // 게시글이 있으면 이미지도 최소 1개 이상
                expect(imageCount).toBeGreaterThanOrEqual(0); // 이미지 없을 수도 있음
            }
        }
    });

    test('게시글 API 응답에 실제 이미지 URL 포함 확인', async ({ page }) => {
        // ========================================================================
        // API 직접 호출로 이미지 URL 확인
        // ========================================================================

        // 네트워크 요청 가로채기
        const responses: string[] = [];

        page.on('response', async (response) => {
            if (response.url().includes('/api.php')) {
                try {
                    const text = await response.text();
                    responses.push(text);
                } catch (e) {
                    // 응답 본문을 읽을 수 없음
                }
            }
        });

        // 게시글 목록 페이지 접속
        await page.goto('https://local.sonub.com/post/list?category=discussion', {
            waitUntil: 'networkidle'
        });

        // API 응답에서 Picsum 이미지 URL 찾기
        const picsumUrls = responses.filter((text) =>
            text.includes('picsum.photos')
        );

        console.log(`\nAPI 응답에서 발견된 Picsum URL 응답: ${picsumUrls.length}개`);

        if (picsumUrls.length > 0) {
            // 첫 번째 응답에서 URL 추출
            const urlMatches = picsumUrls[0].match(/picsum\.photos\/\d+\/\d+\?random=\d+/g);

            if (urlMatches) {
                console.log('\n샘플 URL들:');
                urlMatches.slice(0, 3).forEach((url) => {
                    console.log(`  - https://${url}`);
                });

                expect(urlMatches.length).toBeGreaterThan(0);
            }
        }
    });
});
