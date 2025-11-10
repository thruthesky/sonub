import { mdsvex } from 'mdsvex';
import adapter from '@sveltejs/adapter-node';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	// Consult https://svelte.dev/docs/kit/integrations
	// for more information about preprocessors
	preprocess: [vitePreprocess(), mdsvex()],
	kit: {
		adapter: adapter(),
		// Paraglide i18n 모듈이 src/paraglide에 생성되므로 alias를 설정
		alias: {
			'$lib/paraglide': './src/paraglide'
		}
	},
	extensions: ['.svelte', '.svx']
};

export default config;
