/**
 * Svelte 컴포넌트 파일들을 SED 형식의 스펙 문서로 변환하는 자동화 스크립트
 *
 * 사용법:
 *   node ./tmp/generate-spec-docs.mjs
 *
 * 기능:
 * - src/routes 및 src/lib/components 파일을 읽어서
 * - ./specs/repository/src/ 경로에 SED 형식의 스펙 문서(.md)를 생성
 * - UTF-8 인코딩 보장
 * - YAML 헤더 포함
 */

import fs from 'fs';
import path from 'path';
import { glob } from 'glob';

// 설정
const BASE_DIR = process.cwd();
const SOURCE_PATTERNS = [
	'src/routes/**/*.svelte',
	'src/lib/components/**/*.svelte',
	'src/stories/**/*.svelte'
];
const SPEC_OUTPUT_DIR = path.join(BASE_DIR, 'specs', 'repository');
const FORCE_OVERWRITE = process.argv.includes('--force');

/**
 * 파일 경로에서 카테고리를 결정
 */
function getCategory(filePath) {
	if (filePath.includes('src/routes')) {
		return 'route-page';
	}
	if (filePath.includes('src/lib/components/ui')) {
		return 'ui-component';
	}
	if (filePath.includes('src/lib/components')) {
		if (
			filePath.includes('top-bar') ||
			filePath.includes('left-sidebar') ||
			filePath.includes('right-sidebar')
		) {
			return 'layout-component';
		}
		return 'feature-component';
	}
	if (filePath.includes('src/stories')) {
		return 'storybook';
	}
	return 'unknown';
}

/**
 * 파일의 주요 기능을 추출 (간단한 분석)
 */
function extractFeatures(content) {
	const features = [];

	// 주석에서 TODO, Feature 등 추출
	const commentRegex = /\/\*\*([\s\S]*?)\*\//g;
	let match;
	while ((match = commentRegex.exec(content)) !== null) {
		const comment = match[1];
		if (comment.includes('TODO') || comment.includes('기능') || comment.includes('feature')) {
			// 간단히 첫 몇 줄만 추출
			const lines = comment.split('\n').slice(0, 5);
			features.push(lines.map((l) => l.trim()).join(' '));
		}
	}

	return features.length > 0 ? features : ['코드 분석 필요'];
}

/**
 * Props 추출 (간단한 분석)
 */
function extractProps(content) {
	const propsRegex = /let\s+\{\s*([^}]+)\s*\}\s*=\s*\$props\(\)/;
	const match = content.match(propsRegex);

	if (match) {
		const props = match[1]
			.split(',')
			.map((p) => p.trim())
			.filter((p) => p);
		return props.join(', ');
	}

	// $state나 $derived로 정의된 변수들
	const stateRegex = /let\s+(\w+)\s*=\s*\$(state|derived)/g;
	const states = [];
	let stateMatch;
	while ((stateMatch = stateRegex.exec(content)) !== null) {
		states.push(stateMatch[1]);
	}

	return states.length > 0 ? `State variables: ${states.join(', ')}` : '없음';
}

/**
 * SED 스펙 문서 생성
 */
function generateSpecDoc(filePath, content) {
	const fileName = path.basename(filePath);
	const category = getCategory(filePath);
	const features = extractFeatures(content);
	const props = extractProps(content);

	const tags = ['svelte5', 'sveltekit'];
	if (filePath.includes('ui')) tags.push('shadcn-ui', 'tailwindcss');
	if (filePath.includes('chat')) tags.push('realtime', 'firebase');

	// 설명 추출 (첫 번째 주석 블록)
	const descRegex = /\/\*\*\s*\n\s*\*\s*(.+?)\s*\n/;
	const descMatch = content.match(descRegex);
	const description = descMatch?.[1] || `${fileName} 컴포넌트`;

	const specDoc = `---
name: ${fileName}
description: ${description}
version: 1.0.0
type: svelte-component
category: ${category}
tags: [${tags.join(', ')}]
---

# ${fileName}

## 개요
${description}

## 소스 코드

\`\`\`svelte
${content}
\`\`\`

## 주요 기능
${features.map((f) => `- ${f}`).join('\n')}

## Props/Parameters
${props}

## 사용 예시
\`\`\`svelte
<!-- 사용 예시는 필요에 따라 추가하세요 -->
<${fileName.replace('.svelte', '')} />
\`\`\`

---

> 이 문서는 자동 생성되었습니다.
> 수정이 필요한 경우 직접 편집하세요.
`;

	return specDoc;
}

/**
 * 스펙 문서 파일 경로 생성
 */
function getSpecFilePath(sourceFilePath) {
	const relativePath = path.relative(BASE_DIR, sourceFilePath);
	return path.join(SPEC_OUTPUT_DIR, relativePath + '.md');
}

/**
 * 디렉토리 생성 (재귀적)
 */
function ensureDir(dirPath) {
	if (!fs.existsSync(dirPath)) {
		fs.mkdirSync(dirPath, { recursive: true });
	}
}

/**
 * 메인 실행 함수
 */
async function main() {
	console.log('🚀 SED 스펙 문서 생성 시작...\n');
	console.log(`📁 작업 디렉토리: ${BASE_DIR}`);
	console.log(`📝 출력 디렉토리: ${SPEC_OUTPUT_DIR}`);
	console.log(`🔄 강제 덮어쓰기: ${FORCE_OVERWRITE ? '예' : '아니오'}\n`);

	// 모든 소스 파일 찾기
	const allFiles = [];
	for (const pattern of SOURCE_PATTERNS) {
		const files = await glob(pattern, { cwd: BASE_DIR, absolute: true });
		allFiles.push(...files);
	}

	console.log(`📊 찾은 파일 개수: ${allFiles.length}개\n`);

	let successCount = 0;
	let skipCount = 0;
	let errorCount = 0;
	const errors = [];

	// 각 파일 처리
	for (const filePath of allFiles) {
		try {
			const relativePath = path.relative(BASE_DIR, filePath);
			console.log(`🔍 처리 중: ${relativePath}`);

			// 소스 파일 읽기
			const content = fs.readFileSync(filePath, 'utf-8');

			// 스펙 문서 생성
			const specDoc = generateSpecDoc(filePath, content);

			// 출력 파일 경로
			const specFilePath = getSpecFilePath(filePath);

			// 이미 존재하는지 확인
			if (fs.existsSync(specFilePath) && !FORCE_OVERWRITE) {
				console.log(`  ⏭️  건너뛰기 (이미 존재함): ${specFilePath}\n`);
				skipCount++;
				continue;
			}

			// 디렉토리 생성
			ensureDir(path.dirname(specFilePath));

			// 스펙 문서 저장 (UTF-8)
			fs.writeFileSync(specFilePath, specDoc, { encoding: 'utf-8' });

			console.log(`  ✅ 생성 완료: ${specFilePath}\n`);
			successCount++;
		} catch (error) {
			console.error(`  ❌ 에러 발생: ${filePath}`);
			console.error(`     ${error}\n`);
			errorCount++;
			errors.push({
				file: filePath,
				error: error instanceof Error ? error.message : String(error)
			});
		}
	}

	// 최종 보고
	console.log('\n' + '='.repeat(60));
	console.log('📊 작업 완료 보고서');
	console.log('='.repeat(60));
	console.log(`총 파일 수: ${allFiles.length}개`);
	console.log(`성공: ${successCount}개`);
	console.log(`건너뛰기: ${skipCount}개`);
	console.log(`실패: ${errorCount}개`);
	console.log('='.repeat(60));

	if (errors.length > 0) {
		console.log('\n❌ 실패한 파일 목록:');
		errors.forEach(({ file, error }) => {
			console.log(`  - ${file}`);
			console.log(`    에러: ${error}`);
		});
	}

	console.log('\n✨ 작업 완료!');
}

// 실행
main().catch((error) => {
	console.error('치명적 에러:', error);
	process.exit(1);
});
