<script lang="ts">
	/**
	 * 좌측 사이드바 컴포넌트
	 *
	 * 데스크톱에서만 표시되는 좌측 네비게이션/메뉴 영역입니다.
	 * TailwindCSS를 사용하여 스타일링합니다.
	 * Paraglide-JS를 사용하여 다국어 메시지를 자동으로 처리합니다.
	 */

	import * as Card from '$lib/components/ui/card/index.js';
	import { BUILD_VERSION } from '$lib/version';
	import { getLocale, setLocale, locales } from '$lib/paraglide/runtime';
	import * as m from '$lib/paraglide/messages.js';

	// 언어 이름 매핑
	const localeNames: Record<string, string> = {
		en: 'English',
		ko: '한국어',
		ja: '日本語',
		zh: '中文'
	};

	/**
	 * 언어 변경 핸들러
	 *
	 * 사용자가 언어를 선택할 때:
	 * 1. 쿠키에 선택된 언어를 저장
	 * 2. Paraglide 런타임 로케일 업데이트 및 페이지 새로고침
	 * 3. 모든 메시지가 새로운 로케일에서 다시 렌더링됨
	 */
	async function handleLocaleChange(event: Event) {
		const target = event.target as HTMLSelectElement;
		const newLocale = target.value as (typeof locales)[number];

		// 쿠키에 선택된 언어 저장 (1년 유효)
		const expires = new Date();
		expires.setFullYear(expires.getFullYear() + 1);
		document.cookie = `PARAGLIDE_LOCALE=${newLocale}; path=/; expires=${expires.toUTCString()}`;

		// Paraglide 런타임 로케일 업데이트 (기본값으로 페이지 새로고침)
		// reload: true (기본값)를 사용하여 모든 메시지가 새로운 로케일에서 렌더링되도록 함
		await setLocale(newLocale);
	}
</script>

<aside class="hidden lg:block lg:w-64 xl:w-72">
	<div class="sticky top-20 space-y-4">
		<!-- 네비게이션 메뉴 -->
		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">{m.msg_0144()}</Card.Title>
			</Card.Header>
			<Card.Content class="space-y-2">
				<a
					href="/"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
				>
					{m.msg_0018()}
				</a>
				<a
					href="/about"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
				>
					{m.msg_0019()}
				</a>
				<a
					href="/products"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
				>
					{m.msg_0020()}
				</a>
				<a
					href="/contact"
					class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
				>
					{m.msg_0021()}
				</a>
			</Card.Content>
		</Card.Root>

		<!-- 정보 카드 -->
		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">{m.msg_0145()}</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="text-sm text-gray-600">{m.msg_0146()}</p>
			</Card.Content>
		</Card.Root>

		<!-- 언어 선택 드롭다운 -->
		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">{m.msg_0147()}</Card.Title>
			</Card.Header>
			<Card.Content>
				<select
					value={getLocale()}
					onchange={handleLocaleChange}
					class="w-full cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 transition-colors hover:border-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
					aria-label={m.msg_0147()}
				>
					{#each locales as locale}
						<option value={locale}>
							{localeNames[locale] || locale}
						</option>
					{/each}
				</select>
			</Card.Content>
		</Card.Root>

		<!-- 빌드 버전 정보 -->
		<Card.Root>
			<Card.Header>
				<Card.Title class="text-base">{m.msg_0148()}</Card.Title>
			</Card.Header>
			<Card.Content>
				<p class="text-sm text-gray-600">{BUILD_VERSION}</p>
			</Card.Content>
		</Card.Root>
	</div>
</aside>
