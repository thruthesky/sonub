/**
 * 자동 SED 스펙 파일 생성 스크립트
 *
 * 프로젝트의 모든 소스 코드 파일을 자동으로 찾아서
 * ./specs/repository 폴더에 SED 스펙 문서로 생성합니다.
 */

import fs from 'fs';
import path from 'path';
import { glob } from 'glob';

// 프로젝트 루트 디렉토리
const projectRoot = process.cwd();

// 제외할 패턴
const excludePatterns = [
  '**/node_modules/**',
  '**/.svelte-kit/**',
  '**/build/**',
  '**/dist/**',
  '**/.git/**',
  '**/.github/**',
  '**/.claude/**',
  '**/.vscode/**',
  '**/.storybook/**',
  '**/tmp/**',
  '**/specs/**',
  '**/*.md',
  '**/.env',
  '**/.env.*',
  '**/.gitignore',
  '**/.npmrc',
  '**/.prettier*',
  '**/package-lock.json',
  '**/pnpm-lock.yaml',
  '**/yarn.lock',
  '**/project.inlang/**',
  '**/firebase/functions/lib/**', // 빌드된 파일 제외
];

// 포함할 파일 확장자
const includeExtensions = [
  '.svelte',
  '.ts',
  '.js',
  '.json',
  '.css',
  '.scss',
  '.html',
  '.yaml',
  '.yml',
];

/**
 * 소스 파일 찾기
 */
async function findSourceFiles(): Promise<string[]> {
  const patterns = includeExtensions.map((ext) => `**/*${ext}`);

  const files: string[] = [];

  for (const pattern of patterns) {
    const matches = await glob(pattern, {
      cwd: projectRoot,
      ignore: excludePatterns,
      nodir: true,
    });

    files.push(...matches);
  }

  // 중복 제거 및 정렬
  return Array.from(new Set(files)).sort();
}

/**
 * SED 스펙 파일 생성
 */
function generateSpecFile(sourceFilePath: string): void {
  // 소스 파일의 절대 경로
  const absoluteSourcePath = path.join(projectRoot, sourceFilePath);

  // 스펙 파일 경로 생성 (./specs/repository/ + 소스 파일 경로 + .md)
  const specFilePath = path.join(projectRoot, 'specs', 'repository', sourceFilePath + '.md');

  // 스펙 파일의 디렉토리 생성
  const specDir = path.dirname(specFilePath);
  if (!fs.existsSync(specDir)) {
    fs.mkdirSync(specDir, { recursive: true });
  }

  // 소스 파일이 존재하는지 확인
  if (!fs.existsSync(absoluteSourcePath)) {
    return;
  }

  // 디렉토리인 경우 건너뛰기
  const stats = fs.statSync(absoluteSourcePath);
  if (stats.isDirectory()) {
    return;
  }

  // 소스 파일 읽기
  const sourceContent = fs.readFileSync(absoluteSourcePath, 'utf-8');

  // 파일 확장자 추출
  const ext = path.extname(sourceFilePath);

  // 스펙 파일 생성
  const specContent = generateSpecContent(sourceFilePath, sourceContent, ext);
  fs.writeFileSync(specFilePath, specContent, 'utf-8');

  console.log(`✅ 생성됨: ${sourceFilePath}`);
}

/**
 * 스펙 문서 내용 생성
 */
function generateSpecContent(
  sourceFilePath: string,
  sourceContent: string,
  ext: string
): string {
  const fileName = path.basename(sourceFilePath);
  const fileType = getFileType(ext);
  const codeLanguage = getCodeLanguage(ext);

  // YAML 헤더
  const yamlHeader = `title: ${fileName}
type: ${fileType}
path: ${sourceFilePath}
status: active
version: 1.0.0
last_updated: ${new Date().toISOString().split('T')[0]}`;

  // 개요
  const description = `이 파일은 \`${sourceFilePath}\`의 소스 코드를 포함하는 SED 스펙 문서입니다.`;

  return `---
${yamlHeader}
---

## 개요

${description}

## 소스 코드

\`\`\`${codeLanguage}
${sourceContent}
\`\`\`

## 변경 이력

- ${new Date().toISOString().split('T')[0]}: 스펙 문서 생성
`;
}

/**
 * 파일 타입 결정
 */
function getFileType(ext: string): string {
  const typeMap: { [key: string]: string } = {
    '.svelte': 'component',
    '.ts': 'typescript',
    '.js': 'javascript',
    '.json': 'config',
    '.css': 'stylesheet',
    '.scss': 'stylesheet',
    '.html': 'template',
    '.yaml': 'config',
    '.yml': 'config',
  };

  return typeMap[ext] || 'file';
}

/**
 * 코드 언어 결정
 */
function getCodeLanguage(ext: string): string {
  const langMap: { [key: string]: string } = {
    '.svelte': 'svelte',
    '.ts': 'typescript',
    '.js': 'javascript',
    '.json': 'json',
    '.css': 'css',
    '.scss': 'scss',
    '.html': 'html',
    '.yaml': 'yaml',
    '.yml': 'yaml',
  };

  return langMap[ext] || 'text';
}

/**
 * 메인 함수
 */
async function main(): Promise<void> {
  console.log('🚀 SED 스펙 파일 자동 생성 시작...\n');

  // 소스 파일 찾기
  console.log('📂 소스 파일 검색 중...');
  const sourceFiles = await findSourceFiles();
  console.log(`📊 총 ${sourceFiles.length}개의 소스 파일을 찾았습니다.\n`);

  // 각 파일에 대해 스펙 생성
  sourceFiles.forEach((sourceFile) => {
    generateSpecFile(sourceFile);
  });

  console.log(`\n✨ SED 스펙 파일 생성 완료! (총 ${sourceFiles.length}개)`);

  // 생성된 스펙 파일 목록 저장
  const specListPath = path.join(projectRoot, 'tmp', 'generated-spec-list.json');
  fs.writeFileSync(
    specListPath,
    JSON.stringify(
      {
        generatedAt: new Date().toISOString(),
        totalFiles: sourceFiles.length,
        files: sourceFiles,
      },
      null,
      2
    ),
    'utf-8'
  );

  console.log(`\n📋 생성된 스펙 목록: ${specListPath}`);
}

// 스크립트 실행
main().catch((error) => {
  console.error('❌ 에러 발생:', error);
  process.exit(1);
});
