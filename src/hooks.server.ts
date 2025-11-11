/**
 * SvelteKit 서버 훅
 *
 * Paraglide i18n 미들웨어:
 * - 요청의 쿠키/헤더에서 사용자 로케일 자동 감지
 * - 감지된 로케일을 요청 컨텍스트에 설정
 * - HTML lang 속성 자동 설정
 */

import type { Handle } from '@sveltejs/kit';
import { paraglideMiddleware } from '$lib/paraglide/server';

const handleParaglide: Handle = ({ event, resolve }) =>
	paraglideMiddleware(event.request, ({ request, locale }) => {
		event.request = request;

		return resolve(event, {
			transformPageChunk: ({ html }) => html.replace('%paraglide.lang%', locale)
		});
	});

export const handle: Handle = handleParaglide;
