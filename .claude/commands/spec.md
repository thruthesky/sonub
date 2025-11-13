당신은 SED AI SPEC 문서 생성 전문가입니다.
SED 는 https://sedai.dev/ 의 Spec-Exact Development 방법론입니다.

SED 의 개념을 잘 이해하고, ./specs/repository 폴더에 현재 프로젝트의 모든 소스 코드(Firebase cloud functions, Svelte 컴포넌트, HTML, CSS 페이지 등)를 하나의 소스 코드 별 하나의 스펙 파일으로 만들어주세요.

참고: https://sedai.dev/structure 의 "File Organization" -> "Hierarchical Folder Structure" 참고하여, 현재 프로젝트 sonub 의 모든 소스 코드를 ./specs/repository 폴더 하위에 SED 스펙으로 만들어주세요.

목적:

- Sonub 코드베이스(Svelte5, Firebase Cloud Functinos, CSS 등)의 모든 소스 코드를 통째로(완전히) 스펙 문서에 넣어서
- 스펙만으로 완전히 동일한 (또는 매우 비슷한) 웹/앱을 만들어 낼 수 있게 하기 위해서입니다.
- 즉, 소스코드 까지 포함하는 완전한 스펙을 만들어서 인공지능에게 바이브 코딩을 시켜서, 웹/앱을 만들기 위한 것입니다.

현재 프로젝트의 루트 폴더 하위의 모든 소스코드(설정파일 포함) 찾아서, ./specs/repository 폴더 아래로 SED 스펙 문서를 생성해주세요. 각 소스 코드(설정) 등의 경로를 유지하고, 파일 맨 끝에, .md 를 추가해주세요. 참고로 모든 문서의 주석이나 설명은 한국어로 작성해야 합니다. 따라서 항상 UTF-8 인코딩을 사용해야 합니다.

예:
./packages.json -> ./specs/repository/package.json.md
./src/app.css -> ./specs/repository/src/app.css.md

./specs/repository/src/**/\*.md
./specs/repository/firebase/**/\*.md

스펙으로 만들어야 할 파일 유형:

- Svelte 컴포넌트 파일: .svelte
- Firebase Cloud Functions 소스 코드 파일: .ts, .js
  - 단, 빌드된 파일(예: .js 파일 중 빌드 산출물)은 제외
- 설정 파일: .json, .ts, .js, .yaml, .yml 등
- CSS 파일: .css, .scss 등
- HTML 파일: .html
- 예외:
  - 빌드 산출물(예: .js, .css 등)이나, node_modules 등 외부 라이브러리 파일은 제외
  - 각종 \*.md 파일 제외
  - .claude, .github, .storybook, .svelte-kit, .vscode, ./tmp, .env, .gitignore, .mpmrc, .prettier\*, ./package-lock.json 등 개발 환경 설정 폴더 및 숨김 폴더/파일 제외
  - ./specs 폴더 아래에 있는 파일들을 다시 SED 스펙으로 만들지 않음
  - project.inlang,

**중요 작업 지침:**

- 개발자가 `소스 코드 스펙 업데이트`를 요구하면, 기존의 ./specs/repository 폴더를 삭제하고 완전히 새로 작성해야 합니다.
- 그리고 `./specs/index.md` 에 `## Complete Source Code Specs` 섹션을 만들고, `./specs/repository` 폴더 아래에 있는 모든 소스 코드 경로를 포함하고, 각 스펙의 간략한 설명을 요약하여, 새로 생성된 모든 소스 코드 스펙 파일들의 경로를 리스트업 해야 합니다.
- 이렇게 하므로서 최신 업데이트된 소스 코드를 스펙에 반영 할 수 있는 것입니다.
