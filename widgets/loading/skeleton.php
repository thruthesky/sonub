<style>
    /* Skeleton Loading Animation - Reusable Components */

    /* Base skeleton shimmer effect */
    .skeleton {
        background: linear-gradient(90deg, #f0f2f5 25%, #e4e6eb 50%, #f0f2f5 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Skeleton Avatar (Profile Photo) */
    .skeleton-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 12px;
        flex-shrink: 0;
    }

    /* Skeleton Text (Lines) */
    .skeleton-text {
        border-radius: 4px;
    }

    /* Skeleton Image Placeholder */
    .skeleton-image {
        width: 100%;
        height: 200px;
        border-radius: 8px;
    }

    /* Skeleton Pulse Animation (Alternative style) */
    .skeleton-pulse {
        animation: skeleton-pulse 1.5s ease-in-out infinite;
    }

    @keyframes skeleton-pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* Bootstrap placeholder wave animation override for smoother effect */
    .placeholder-glow .placeholder {
        animation: placeholder-glow 2s ease-in-out infinite;
    }

    @keyframes placeholder-glow {

        0%,
        100% {
            opacity: 0.3;
        }

        50% {
            opacity: 0.1;
        }
    }
</style>

<!-- 로딩 스켈레톤 (Vue 마운트 전에만 표시) -->
<div id="skeleton-loader" class="d-flex flex-column gap-3">
    <!-- 스켈레톤 게시물 카드 1 -->
    <article class="post-card" style="margin-top: 0px !important;">
        <!-- 헤더 -->
        <header class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #e4e6eb;">
            <div class="d-flex align-items-center gap-2" style="flex: 1;">
                <div class="skeleton skeleton-avatar"></div>
                <div style="flex: 1;">
                    <div class="skeleton skeleton-text" style="width: 120px; height: 15px; margin-bottom: 6px;"></div>
                    <div class="skeleton skeleton-text" style="width: 180px; height: 13px;"></div>
                </div>
            </div>
        </header>

        <!-- 본문 -->
        <div class="p-3">
            <div class="skeleton skeleton-text mb-2" style="width: 100%; height: 14px;"></div>
            <div class="skeleton skeleton-text mb-3" style="width: 80%; height: 14px;"></div>
            <div class="skeleton skeleton-image"></div>
        </div>

        <!-- 액션 버튼 -->
        <div class="d-flex border-top" style="border-color: #e4e6eb;">
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 60px; height: 16px;"></div>
            </div>
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 80px; height: 16px;"></div>
            </div>
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 60px; height: 16px;"></div>
            </div>
        </div>
    </article>

    <!-- 스켈레톤 게시물 카드 2 -->
    <article class="post-card">
        <!-- 헤더 -->
        <header class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #e4e6eb;">
            <div class="d-flex align-items-center gap-2" style="flex: 1;">
                <div class="skeleton skeleton-avatar"></div>
                <div style="flex: 1;">
                    <div class="skeleton skeleton-text" style="width: 140px; height: 15px; margin-bottom: 6px;"></div>
                    <div class="skeleton skeleton-text" style="width: 160px; height: 13px;"></div>
                </div>
            </div>
        </header>

        <!-- 본문 -->
        <div class="p-3">
            <div class="skeleton skeleton-text mb-2" style="width: 95%; height: 14px;"></div>
            <div class="skeleton skeleton-text mb-2" style="width: 88%; height: 14px;"></div>
            <div class="skeleton skeleton-text mb-3" style="width: 70%; height: 14px;"></div>
            <div class="skeleton skeleton-image"></div>
        </div>

        <!-- 액션 버튼 -->
        <div class="d-flex border-top" style="border-color: #e4e6eb;">
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 60px; height: 16px;"></div>
            </div>
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 80px; height: 16px;"></div>
            </div>
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 60px; height: 16px;"></div>
            </div>
        </div>
    </article>

    <!-- 스켈레톤 게시물 카드 3 -->
    <article class="post-card">
        <!-- 헤더 -->
        <header class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: #e4e6eb;">
            <div class="d-flex align-items-center gap-2" style="flex: 1;">
                <div class="skeleton skeleton-avatar"></div>
                <div style="flex: 1;">
                    <div class="skeleton skeleton-text" style="width: 130px; height: 15px; margin-bottom: 6px;"></div>
                    <div class="skeleton skeleton-text" style="width: 170px; height: 13px;"></div>
                </div>
            </div>
        </header>

        <!-- 본문 -->
        <div class="p-3">
            <div class="skeleton skeleton-text mb-2" style="width: 100%; height: 14px;"></div>
            <div class="skeleton skeleton-text mb-3" style="width: 75%; height: 14px;"></div>
            <div class="skeleton skeleton-image"></div>
        </div>

        <!-- 액션 버튼 -->
        <div class="d-flex border-top" style="border-color: #e4e6eb;">
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 60px; height: 16px;"></div>
            </div>
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 80px; height: 16px;"></div>
            </div>
            <div class="flex-fill py-2 text-center">
                <div class="skeleton skeleton-text mx-auto" style="width: 60px; height: 16px;"></div>
            </div>
        </div>
    </article>

</div>