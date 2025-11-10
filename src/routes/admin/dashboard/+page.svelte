<script lang="ts">
	/**
	 * 관리자 대시보드 페이지
	 *
	 * 관리자 대시보드의 메인 페이지입니다.
	 * 주요 통계 정보와 관리자 도구로 접근할 수 있습니다.
	 */

	import { Card } from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';
	import { m } from '$lib/paraglide/messages-proxy';

	// 대시보드 메뉴 항목들
	interface DashboardItem {
		titleKey: () => string;
		descriptionKey: () => string;
		href: string;
		icon: string;
	}

	const dashboardItems: DashboardItem[] = [
		{
			titleKey: m.adminTestUserManagement,
			descriptionKey: m.adminTestUserManagementDesc,
			href: '/admin/users',
			icon: '👥'
		},
		{
			titleKey: m.adminUserList,
			descriptionKey: m.adminUserListDesc,
			href: '/admin/users',
			icon: '📋'
		},
		{
			titleKey: m.adminReportList,
			descriptionKey: m.adminReportListDesc,
			href: '/admin/reports',
			icon: '⚠️'
		},
		{
			titleKey: m.adminTest,
			descriptionKey: m.adminTestDesc,
			href: '/admin/test',
			icon: '🧪'
		}
	];
</script>

<div class="space-y-6">
	<!-- 페이지 제목 -->
	<div>
		<h1 class="text-3xl font-bold text-gray-900">{m.adminDashboard()}</h1>
		<p class="mt-2 text-gray-600">{m.adminDashboardGuide()}</p>
	</div>

	<!-- 탭 내비게이션 -->
	<nav class="dashboard-tabs" aria-label={m.adminDashboard()}>
		{#each dashboardItems as item (item.href)}
			<a
				href={item.href}
				class="dashboard-tab"
			>
				<span class="tab-icon">{item.icon}</span>
				<span class="tab-title">{item.titleKey()}</span>
			</a>
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
							<h3 class="mt-4 text-lg font-semibold text-gray-900">{item.titleKey()}</h3>
							<p class="mt-2 text-sm text-gray-600">{item.descriptionKey()}</p>
						</div>
					</div>
					<div class="mt-6">
						<Button
							href={item.href}
							variant="outline"
							size="sm"
							class="w-full cursor-pointer"
						>
							{m.go()}
						</Button>
					</div>
				</div>
			</Card>
		{/each}
	</div>

	<!-- 정보 섹션 -->
	<Card>
		<div class="p-6">
			<h2 class="mb-4 text-xl font-semibold text-gray-900">{m.commonInfo()}</h2>
			<div class="space-y-2 text-sm text-gray-600">
				<p>{m.adminInfoPermissionNotImplemented()}</p>
				<p>{m.adminInfoTestFlag()}</p>
				<p>{m.adminInfoDataDelete()}</p>
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
