/**
 * SED 스펙 파일 생성 스크립트
 *
 * 이 스크립트는 프로젝트의 모든 소스 코드 파일을 읽어서
 * ./specs/repository 폴더에 SED 스펙 문서로 생성합니다.
 */

import fs from 'fs';
import path from 'path';

// 프로젝트 루트 디렉토리
const projectRoot = process.cwd();

// 소스 파일 목록 (빌드 산출물 제외)
const sourceFiles = [
  './.mcp.json',
  './components.json',
  './e2e/demo.test.ts',
  './eslint.config.js',
  './firebase/cors.json',
  './firebase/database.rules.json',
  './firebase/firebase.json',
  './firebase/functions/.eslintrc.js',
  './firebase/functions/package.json',
  './firebase/functions/scripts/generate-sample-posts.ts',
  './firebase/functions/src/handlers/chat.handler.ts',
  './firebase/functions/src/handlers/user.handler.ts',
  './firebase/functions/src/index.ts',
  './firebase/functions/src/types/index.ts',
  './firebase/functions/src/utils/comment.utils.ts',
  './firebase/functions/src/utils/like.utils.ts',
  './firebase/functions/src/utils/post.utils.ts',
  './firebase/functions/src/utils/report.utils.ts',
  './firebase/functions/test/integration/onLike.test.ts',
  './firebase/functions/test/integration/onPostCreate.test.ts',
  './firebase/functions/test/integration/test-setup.ts',
  './firebase/functions/test/unit/comment.handler.test.ts',
  './firebase/functions/test/unit/like.utils.test.ts',
  './firebase/functions/test/unit/user.handler.test.ts',
  './firebase/functions/tsconfig.dev.json',
  './firebase/functions/tsconfig.json',
  './messages/en.json',
  './messages/ja.json',
  './messages/ko.json',
  './messages/zh.json',
  './package.json',
  './playwright.config.ts',
  './shared/chat.pure-functions.ts',
  './shared/date.pure-functions.ts',
  './src/app.css',
  './src/app.d.ts',
  './src/app.html',
  './src/demo.spec.ts',
  './src/hooks.server.ts',
  './src/lib/components/DatabaseListView.svelte',
  './src/lib/components/admin-menu.svelte',
  './src/lib/components/chat/ChatCreateDialog.svelte',
  './src/lib/components/chat/ChatListMenu.svelte',
  './src/lib/components/dev/dev-icon.svelte',
  './src/lib/components/left-sidebar.svelte',
  './src/lib/components/right-sidebar.svelte',
  './src/lib/components/top-bar.svelte',
  './src/lib/components/ui/alert/alert-description.svelte',
  './src/lib/components/ui/alert/alert-title.svelte',
  './src/lib/components/ui/alert/alert.svelte',
  './src/lib/components/ui/alert/index.ts',
  './src/lib/components/ui/button/button.svelte',
  './src/lib/components/ui/button/index.ts',
  './src/lib/components/ui/card/card-content.svelte',
  './src/lib/components/ui/card/card-description.svelte',
  './src/lib/components/ui/card/card-footer.svelte',
  './src/lib/components/ui/card/card-header.svelte',
  './src/lib/components/ui/card/card-title.svelte',
  './src/lib/components/ui/card/card.svelte',
  './src/lib/components/ui/card/index.ts',
  './src/lib/components/ui/dialog/context.ts',
  './src/lib/components/ui/dialog/dialog-content.svelte',
  './src/lib/components/ui/dialog/dialog-description.svelte',
  './src/lib/components/ui/dialog/dialog-footer.svelte',
  './src/lib/components/ui/dialog/dialog-header.svelte',
  './src/lib/components/ui/dialog/dialog-title.svelte',
  './src/lib/components/ui/dialog/dialog.svelte',
  './src/lib/components/ui/dialog/index.ts',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-checkbox-group.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-checkbox-item.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-content.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-group-heading.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-group.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-item.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-label.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-radio-group.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-radio-item.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-separator.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-shortcut.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-sub-content.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-sub-trigger.svelte',
  './src/lib/components/ui/dropdown-menu/dropdown-menu-trigger.svelte',
  './src/lib/components/ui/dropdown-menu/index.ts',
  './src/lib/components/under-construction.svelte',
  './src/lib/components/user-login.svelte',
  './src/lib/components/user/UserSearchDialog.svelte',
  './src/lib/components/user/avatar.svelte',
  './src/lib/firebase.ts',
  './src/lib/functions/chat.functions.ts',
  './src/lib/functions/date.functions.ts',
  './src/lib/index.ts',
  './src/lib/stores/auth.svelte.ts',
  './src/lib/stores/database.svelte.ts',
  './src/lib/stores/user-profile.svelte.ts',
  './src/lib/utils.ts',
  './src/lib/utils/admin-service.ts',
  './src/lib/utils/auth-helpers.ts',
  './src/lib/utils/test-user-generator.ts',
  './src/lib/version.ts',
  './src/routes/+layout.svelte',
  './src/routes/+page.svelte',
  './src/routes/admin/+layout.svelte',
  './src/routes/admin/dashboard/+page.svelte',
  './src/routes/admin/reports/+page.svelte',
  './src/routes/admin/test/+page.svelte',
  './src/routes/admin/test/create-test-data/+page.svelte',
  './src/routes/admin/users/+page.svelte',
  './src/routes/chat/group-chat-list/+page.svelte',
  './src/routes/chat/list/+page.svelte',
  './src/routes/chat/open-chat-list/+page.svelte',
  './src/routes/chat/room/+page.svelte',
  './src/routes/demo/+page.svelte',
  './src/routes/demo/paraglide/+page.svelte',
  './src/routes/dev/test/database-list-view/+page.svelte',
  './src/routes/menu/+page.svelte',
  './src/routes/my/+layout.svelte',
  './src/routes/my/profile/+page.svelte',
  './src/routes/my/reports/+page.svelte',
  './src/routes/page.svelte.spec.ts',
  './src/routes/post/list/+page.svelte',
  './src/routes/stats/+page.svelte',
  './src/routes/user/list/+page.svelte',
  './src/routes/user/login/+page.svelte',
  './src/stories/Button.stories.svelte',
  './src/stories/Button.svelte',
  './src/stories/Header.stories.svelte',
  './src/stories/Header.svelte',
  './src/stories/Page.stories.svelte',
  './src/stories/Page.svelte',
  './src/stories/button.css',
  './src/stories/header.css',
  './src/stories/page.css',
  './svelte.config.js',
  './tsconfig.json',
  './vite.config.ts',
];

/**
 * SED 스펙 파일 생성
 */
function generateSpecFile(sourceFilePath: string): void {
  // 소스 파일의 절대 경로
  const absoluteSourcePath = path.join(projectRoot, sourceFilePath);

  // 스펙 파일 경로 생성 (./specs/repository/ + 소스 파일 경로 + .md)
  const specFilePath = path.join(
    projectRoot,
    'specs',
    'repository',
    sourceFilePath.replace(/^\.\//, '') + '.md'
  );

  // 스펙 파일의 디렉토리 생성
  const specDir = path.dirname(specFilePath);
  if (!fs.existsSync(specDir)) {
    fs.mkdirSync(specDir, { recursive: true });
  }

  // 소스 파일이 존재하는지 확인
  if (!fs.existsSync(absoluteSourcePath)) {
    // console.log(`⚠️  소스 파일이 존재하지 않습니다: ${sourceFilePath}`);
    return;
  }

  // 디렉토리인 경우 건너뛰기
  const stats = fs.statSync(absoluteSourcePath);
  if (stats.isDirectory()) {
    // console.log(`⚠️  디렉토리는 건너뜁니다: ${sourceFilePath}`);
    return;
  }

  // 소스 파일 읽기
  const sourceContent = fs.readFileSync(absoluteSourcePath, 'utf-8');

  // 파일 확장자 추출
  const ext = path.extname(sourceFilePath);

  // 스펙 파일이 이미 존재하는지 확인
  const specExists = fs.existsSync(specFilePath);

  if (specExists) {
    // 기존 스펙 파일이 디렉토리인 경우 건너뛰기
    const specStats = fs.statSync(specFilePath);
    if (specStats.isDirectory()) {
      // console.log(`⚠️  스펙 파일이 디렉토리입니다. 건너뜁니다: ${specFilePath}`);
      return;
    }

    // 기존 스펙 파일 업데이트
    const existingSpec = fs.readFileSync(specFilePath, 'utf-8');

    // 기존 스펙에서 YAML 헤더와 설명 부분 추출
    const yamlHeaderMatch = existingSpec.match(/^---\n([\s\S]*?)\n---/);
    const descriptionMatch = existingSpec.match(/---\n\n## 개요\n\n([\s\S]*?)\n\n## 소스 코드/);

    let yamlHeader = '';
    let description = '';

    if (yamlHeaderMatch) {
      yamlHeader = yamlHeaderMatch[1];
    }

    if (descriptionMatch) {
      description = descriptionMatch[1];
    }

    // 스펙 파일 업데이트
    const updatedSpec = generateSpecContent(
      sourceFilePath,
      sourceContent,
      ext,
      yamlHeader,
      description
    );

    fs.writeFileSync(specFilePath, updatedSpec, 'utf-8');
    // console.log(`✅ 업데이트됨: ${specFilePath}`);
  } else {
    // 새 스펙 파일 생성
    const newSpec = generateSpecContent(sourceFilePath, sourceContent, ext);
    fs.writeFileSync(specFilePath, newSpec, 'utf-8');
    // console.log(`🆕 생성됨: ${specFilePath}`);
  }
}

/**
 * 스펙 문서 내용 생성
 */
function generateSpecContent(
  sourceFilePath: string,
  sourceContent: string,
  ext: string,
  existingYamlHeader?: string,
  existingDescription?: string
): string {
  const fileName = path.basename(sourceFilePath);
  const fileType = getFileType(ext);

  // YAML 헤더
  const yamlHeader = existingYamlHeader || `title: ${fileName}
type: ${fileType}
status: active
version: 1.0.0
last_updated: ${new Date().toISOString().split('T')[0]}`;

  // 개요
  const description = existingDescription || `이 파일은 ${fileName}의 소스 코드를 포함하는 SED 스펙 문서입니다.`;

  // 코드 블록 언어
  const codeLanguage = getCodeLanguage(ext);

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

- ${new Date().toISOString().split('T')[0]}: 스펙 문서 생성/업데이트
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
    '.html': 'html',
    '.yaml': 'yaml',
    '.yml': 'yaml',
  };

  return langMap[ext] || 'text';
}

/**
 * 메인 함수
 */
function main(): void {
// console.log('🚀 SED 스펙 파일 생성 시작...\n');

  sourceFiles.forEach((sourceFile) => {
    generateSpecFile(sourceFile);
  });

// console.log('\n✨ SED 스펙 파일 생성 완료!');
}

// 스크립트 실행
main();
