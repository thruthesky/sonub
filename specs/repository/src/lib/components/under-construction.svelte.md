---
name: under-construction.svelte
description: under-construction 컴포넌트
version: 1.0.0
type: svelte-component
category: component
original_path: src/lib/components/under-construction.svelte
---

# under-construction.svelte

## 개요

**파일 경로**: `src/lib/components/under-construction.svelte`
**파일 타입**: svelte-component
**카테고리**: component

under-construction 컴포넌트

## 소스 코드

```svelte
<script lang="ts">
	/**
	 * 공사중 페이지 컴포넌트
	 *
	 * 아직 구현되지 않은 페이지에 표시되는 공사중 메시지를 보여줍니다.
	 */

	import * as Card from '$lib/components/ui/card/index.js';
	import { Button } from '$lib/components/ui/button/index.js';
	import { m } from '$lib/paraglide/messages';

	interface Props {
		title?: string;
		message?: string;
	}

	let { title = m.constructionTitle(), message = m.constructionMessage() }: Props = $props();
</script>

<div class="flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center py-8">
	<div class="mx-auto w-full max-w-md space-y-6">
		<Card.Root>
			<Card.Header>
				<Card.Title class="text-center text-2xl">{title}</Card.Title>
			</Card.Header>
			<Card.Content class="space-y-6">
				<!-- 공사중 아이콘 -->
				<div class="flex justify-center">
					<svg
						class="h-24 w-24 text-yellow-500"
						fill="none"
						stroke="currentColor"
						viewBox="0 0 24 24"
						stroke-width="1.5"
					>
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
						/>
					</svg>
				</div>

				<!-- 메시지 -->
				<p class="text-center text-gray-600">
					{message}
				</p>

				<!-- 홈으로 버튼 -->
				<div class="pt-4">
					<Button
						href="/"
						class="w-full cursor-pointer bg-blue-600 py-6 text-lg font-semibold text-white hover:bg-blue-700"
					>
						{m.constructionBackToHome()}
					</Button>
				</div>
			</Card.Content>
		</Card.Root>
	</div>
</div>

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
