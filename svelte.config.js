import { mdsvex } from 'mdsvex';
import adapter from '@sveltejs/adapter-node';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	// Consult https://svelte.dev/docs/kit/integrations
	// for more information about preprocessors
	preprocess: [vitePreprocess(), mdsvex()],
	kit: {
		adapter: adapter()
	},
	extensions: ['.svelte', '.svx'],
	// Tailwind CSS v4의 @import 'tailwindcss' reference 사용 시 발생하는
	// css-unused-selector 경고를 무시합니다.
	// 이 경고는 실제 빌드나 런타임에는 영향을 주지 않습니다.
	onwarn: (warning, handler) => {
		// css-unused-selector 경고 무시
		// 둘 다 차단
		if (
			warning.code === 'css-unused-selector' ||
			warning.code === 'css_unused_selector'
		) return;
		// 다른 경고는 정상적으로 처리
		handler(warning);
	}
};

export default config;
