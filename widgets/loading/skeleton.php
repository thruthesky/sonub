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
<div id="skeleton-loader" class="d-flex flex-column gap-4">
    <!-- 스켈레톤 게시물 카드 1 -->
    <article class="post-card skeleton-card">
        <div class="post-header d-flex">
            <div class="skeleton skeleton-avatar"></div>
            <div class="mt-2" style="flex: 1;">
                <div class="skeleton skeleton-text" style="width: 150px; height: 16px; margin-bottom: 8px;"></div>
                <div class="skeleton skeleton-text" style="width: 100px; height: 14px;"></div>
            </div>
        </div>
        <div class="post-body mt-2">
            <div class="skeleton skeleton-image" style="margin-top: 12px;"></div>
        </div>
    </article>

    <!-- 스켈레톤 게시물 카드 1 -->
    <article class="post-card skeleton-card">
        <div class="post-header d-flex">
            <div class="skeleton skeleton-avatar"></div>
            <div class="mt-2" style="flex: 1;">
                <div class="skeleton skeleton-text" style="width: 150px; height: 16px; margin-bottom: 8px;"></div>
                <div class="skeleton skeleton-text" style="width: 100px; height: 14px;"></div>
            </div>
        </div>
        <div class="post-body mt-2">
            <div class="skeleton skeleton-image" style="margin-top: 12px;"></div>
        </div>
    </article>

    <!-- 스켈레톤 게시물 카드 1 -->
    <article class="post-card skeleton-card">
        <div class="post-header d-flex">
            <div class="skeleton skeleton-avatar"></div>
            <div class="mt-2" style="flex: 1;">
                <div class="skeleton skeleton-text" style="width: 150px; height: 16px; margin-bottom: 8px;"></div>
                <div class="skeleton skeleton-text" style="width: 100px; height: 14px;"></div>
            </div>
        </div>
        <div class="post-body mt-2">
            <div class="skeleton skeleton-image" style="margin-top: 12px;"></div>
        </div>
    </article>

</div>