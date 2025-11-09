<script lang="ts">
	/**
	 * 관리자 대시보드 페이지
	 *
	 * 관리자 대시보드의 메인 페이지입니다.
	 * 주요 통계 정보와 관리자 도구로 접근할 수 있습니다.
	 */

	import { Card } from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';

	// 대시보드 메뉴 항목들
	interface DashboardItem {
		title: string;
		description: string;
		href: string;
		icon: string;
	}

	const dashboardItems: DashboardItem[] = [
		{
			title: '테스트 사용자 생성',
			description: '테스트용 임시 사용자 100명을 일괄 생성합니다',
			href: '/admin/test/create-users',
			icon: '👥'
		},
		{
			title: '사용자 목록',
			description: '생성된 테스트 사용자 목록을 확인합니다',
			href: '/admin/users',
			icon: '📋'
		},
		{
			title: '신고 목록',
			description: '사용자 신고 내역을 확인하고 관리합니다',
			href: '/admin/reports',
			icon: '⚠️'
		},
		{
			title: '테스트',
			description: '기타 테스트 기능들을 사용합니다',
			href: '/admin/test',
			icon: '🧪'
		}
	];
</script>

<div class="space-y-6">
	<!-- 페이지 제목 -->
	<div>
		<h1 class="text-3xl font-bold text-gray-900">관리자 대시보드</h1>
		<p class="mt-2 text-gray-600">관리 도구를 선택하여 작업을 시작하세요.</p>
	</div>

	<!-- 탭 내비게이션 -->
	<nav class="dashboard-tabs" aria-label="관리자 대시보드 탭">
		{#each dashboardItems as item (item.href)}
			<Button
				href={item.href}
				variant="ghost"
				size="sm"
				class="dashboard-tab cursor-pointer"
			>
				<span class="tab-icon">{item.icon}</span>
				<span class="tab-title">{item.title}</span>
			</Button>
		{/each}
	</nav>

	<!-- 대시보드 카드들 -->
	<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
		{#each dashboardItems as item (item.href)}
			<Card>
				<div class="p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-4xl">{item.icon}</p>
							<h3 class="mt-4 text-lg font-semibold text-gray-900">{item.title}</h3>
							<p class="mt-2 text-sm text-gray-600">{item.description}</p>
						</div>
					</div>
					<div class="mt-6">
						<Button
							href={item.href}
							variant="outline"
							size="sm"
							class="w-full cursor-pointer"
						>
							이동
						</Button>
					</div>
				</div>
			</Card>
		{/each}
	</div>

	<!-- 정보 섹션 -->
	<Card>
		<div class="p-6">
			<h2 class="mb-4 text-xl font-semibold text-gray-900">정보</h2>
			<div class="space-y-2 text-sm text-gray-600">
				<p>• 현재 관리자 권한 검증은 구현되지 않았습니다.</p>
				<p>• 테스트 사용자는 `isTemporary: true` 플래그로 표시됩니다.</p>
				<p>• 테스트 데이터는 언제든지 삭제할 수 있습니다.</p>
			</div>
		</div>
	</Card>
</div>

<style>
	.dashboard-tabs {
		display: flex;
		flex-wrap: wrap;
		gap: 0.5rem;
		padding: 0.5rem;
		border: 1px solid #e5e7eb;
		border-radius: 0.75rem;
		background-color: #f9fafb;
	}

	.dashboard-tab {
		display: inline-flex;
		align-items: center;
		gap: 0.5rem;
		padding: 0.5rem 1rem;
		border: 1px solid transparent;
		color: #4b5563;
	}

	.dashboard-tab:hover {
		color: #111827;
		border-color: #d1d5db;
		background-color: #ffffff;
	}

	.tab-icon {
		font-size: 1.25rem;
	}

	.tab-title {
		font-weight: 600;
		font-size: 0.95rem;
	}

	@media (max-width: 640px) {
		.dashboard-tabs {
			flex-direction: column;
		}

		.dashboard-tab {
			justify-content: center;
		}
	}
</style>
