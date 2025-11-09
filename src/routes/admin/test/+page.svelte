<script lang="ts">
	/**
	 * 테스트 페이지
	 *
	 * 테스트 관련 기능들의 메인 페이지입니다.
	 * 다양한 테스트 도구로 접근할 수 있습니다.
	 */

	import { Card } from '$lib/components/ui/card';
	import { Button } from '$lib/components/ui/button';

	// 테스트 메뉴 항목들
	interface TestMenuItem {
		title: string;
		description: string;
		href: string;
		icon: string;
	}

	const testMenuItems: TestMenuItem[] = [
		{
			title: '테스트 사용자 생성',
			description: '테스트용 임시 사용자 100명을 일괄 생성합니다',
			href: '/admin/test/create-users',
			icon: '👥'
		},
		{
			title: '테스트 데이터 생성',
			description: '/test/data 노드에 카테고리별 타임스탬프 데이터를 추가합니다',
			href: '/admin/test/create-test-data',
			icon: '🧪'
		}
	];
</script>

<div class="space-y-6">
	<!-- 페이지 제목 -->
	<div>
		<h1 class="text-3xl font-bold text-gray-900">테스트</h1>
		<p class="mt-2 text-gray-600">테스트 도구를 선택하여 작업을 시작하세요.</p>
	</div>

	<!-- 테스트 메뉴 카드들 -->
	<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
		{#each testMenuItems as item (item.href)}
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

	<!-- 안내 메시지 -->
	<Card>
		<div class="p-6">
			<h2 class="mb-4 text-xl font-semibold text-gray-900">정보</h2>
			<div class="space-y-2 text-sm text-gray-600">
				<p>• 이 페이지는 개발 및 테스트 목적으로 사용하는 도구 모음입니다.</p>
				<p>• 테스트 데이터는 `isTemporary: true` 플래그로 표시되어 나중에 쉽게 필터링할 수 있습니다.</p>
				<p>
					• 생성된 테스트 데이터는 `/admin/users` 페이지에서 확인하고 삭제할 수 있습니다.
				</p>
			</div>
		</div>
	</Card>
</div>

<style>
	/* 추가 스타일이 필요한 경우 작성 */
</style>
