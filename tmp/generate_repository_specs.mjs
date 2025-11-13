#!/usr/bin/env node
import { execFileSync } from 'node:child_process';
import { existsSync, mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { basename, dirname, extname, join } from 'node:path';

const repoRoot = process.cwd();
const specRoot = join(repoRoot, 'specs', 'repository');

const CODE_FENCE = '```````';
const languageMap = new Map([
  ['.ts', 'typescript'],
  ['.tsx', 'tsx'],
  ['.js', 'javascript'],
  ['.jsx', 'jsx'],
  ['.svelte', 'svelte'],
  ['.css', 'css'],
  ['.scss', 'scss'],
  ['.sass', 'sass'],
  ['.less', 'less'],
  ['.json', 'json'],
  ['.md', 'markdown'],
  ['.svx', 'markdown'],
  ['.html', 'html'],
  ['.htm', 'html'],
  ['.yml', 'yaml'],
  ['.yaml', 'yaml'],
  ['.env', 'bash'],
  ['.gitignore', 'gitignore'],
  ['.gitattributes', 'text'],
  ['.txt', 'text'],
  ['.cjs', 'javascript'],
  ['.mjs', 'javascript'],
  ['.mts', 'typescript'],
  ['.cts', 'typescript'],
  ['.lock', 'json'],
  ['.svg', 'xml'],
  ['.svelte.ts', 'typescript']
]);

const binaryExtensions = new Set([
  '.png',
  '.jpg',
  '.jpeg',
  '.gif',
  '.webp',
  '.ico',
  '.pdf',
  '.zip',
  '.ttf',
  '.woff',
  '.woff2'
]);

function yamlEscape(value) {
  return value.replace(/"/g, '\\"');
}

function detectLanguage(relativePath, mime) {
  const lower = relativePath.toLowerCase();
  const ext = extname(lower);
  if (languageMap.has(ext)) {
    return languageMap.get(ext);
  }
  if (basename(lower) === 'dockerfile') {
    return 'dockerfile';
  }
  if (basename(lower) === 'makefile') {
    return 'makefile';
  }
  if (mime === 'application/json') {
    return 'json';
  }
  if (mime === 'text/html') {
    return 'html';
  }
  if (mime === 'text/markdown') {
    return 'markdown';
  }
  if (mime === 'text/x-shellscript') {
    return 'bash';
  }
  if (mime === 'text/x-python') {
    return 'python';
  }
  if (mime === 'text/plain') {
    return 'text';
  }
  return 'text';
}

function isBinaryFile(relativePath, mime) {
  const ext = extname(relativePath).toLowerCase();
  if (binaryExtensions.has(ext)) {
    return true;
  }
  if (mime.startsWith('image/') || mime === 'application/octet-stream') {
    return true;
  }
  return false;
}

const trackedFiles = execFileSync('git', ['ls-files'], { cwd: repoRoot, encoding: 'utf8' })
  .split('\n')
  .map((line) => line.trim())
  .filter(Boolean)
  .filter((relativePath) => !relativePath.startsWith('specs/repository/'));

for (const relativePath of trackedFiles) {
  const sourcePath = join(repoRoot, relativePath);
  const targetPath = join(specRoot, `${relativePath}.md`);
  mkdirSync(dirname(targetPath), { recursive: true });

  const mime = execFileSync('file', ['--brief', '--mime-type', sourcePath], { encoding: 'utf8' }).trim();
  const binary = isBinaryFile(relativePath, mime);
  const originalPathEscaped = yamlEscape(relativePath);

  let bodySection = '';
  if (binary) {
    const base64 = readFileSync(sourcePath).toString('base64');
    bodySection = `# 바이너리 자산\n이 파일은 \`${mime}\` 형식의 바이너리 데이터이며 Base64 로 인코딩된 원본을 아래에 제공합니다.\n\n## Base64 원본\n${CODE_FENCE}base64\n${base64}\n${CODE_FENCE}`;
  } else {
    const fileContent = readFileSync(sourcePath, 'utf8');
    const language = detectLanguage(relativePath, mime);
    bodySection = `# 원본 소스 코드\n${CODE_FENCE}${language}\n${fileContent}\n${CODE_FENCE}`;
  }

  const doc = `---\ntitle: "${originalPathEscaped}"\ndescription: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"\noriginal_path: "${originalPathEscaped}"\nspec_type: "repository-source"\n---\n\n# 목적\n이 문서는 \`${relativePath}\` 파일의 전체 내용을 기록하여 SED 스펙만으로도 Sonub 프로젝트를 재구성할 수 있도록 합니다.\n\n## 파일 정보\n- 상대 경로: \`${relativePath}\`\n- MIME: \`${mime}\`\n- 유형: ${binary ? '바이너리' : '텍스트'}\n\n${bodySection}\n`;

  writeFileSync(targetPath, doc, 'utf8');
}
