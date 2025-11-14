#!/usr/bin/env python3
"""
Sonub 소스 코드를 SED 스펙 문서로 변환하는 스크립트

사용법:
    python3 tmp/generate-specs.py
"""

import os
import json
from pathlib import Path
from typing import Dict, List, Optional

# 프로젝트 루트 디렉토리
PROJECT_ROOT = Path(__file__).parent.parent

# 제외할 디렉토리 및 파일 패턴
EXCLUDE_DIRS = {
    'node_modules',
    '.svelte-kit',
    'build',
    '.claude',
    '.github',
    '.storybook',
    '.vscode',
    'tmp',
    'specs',  # 스펙 폴더 자체는 제외
    'project.inlang',
    '.git',
    'dist',
    '.vercel',
    '.netlify',
    'coverage',
}

EXCLUDE_FILES = {
    '.env',
    '.env.local',
    '.env.development',
    '.env.production',
    '.gitignore',
    '.npmrc',
    '.prettierrc',
    '.prettierignore',
    'package-lock.json',
    'pnpm-lock.yaml',
    'yarn.lock',
    '.DS_Store',
}

EXCLUDE_PATTERNS = {
    '.md',  # 마크다운 파일 제외
}

# 포함할 파일 확장자
INCLUDE_EXTENSIONS = {
    '.svelte',
    '.ts',
    '.js',
    '.mjs',
    '.cjs',
    '.json',
    '.css',
    '.scss',
    '.html',
    '.yaml',
    '.yml',
}


def should_exclude_path(path: Path) -> bool:
    """경로가 제외 대상인지 확인"""
    # 경로의 각 부분을 확인
    for part in path.parts:
        if part in EXCLUDE_DIRS:
            return True
        if part.startswith('.') and part not in {'.mcp.json'}:
            return True

    # 파일명 확인
    if path.name in EXCLUDE_FILES:
        return True

    # 패턴 확인
    for pattern in EXCLUDE_PATTERNS:
        if path.name.endswith(pattern):
            return True

    # 확장자 확인
    if path.suffix and path.suffix not in INCLUDE_EXTENSIONS:
        return True

    return False


def get_file_type(file_path: Path) -> str:
    """파일 타입 결정"""
    ext = file_path.suffix.lower()
    name = file_path.name.lower()

    if ext == '.svelte':
        return 'svelte-component'
    elif ext in {'.ts', '.js', '.mjs', '.cjs'}:
        if 'firebase/functions' in str(file_path):
            return 'firebase-function'
        elif file_path.stem.endswith('.spec') or file_path.stem.endswith('.test'):
            return 'test'
        else:
            return 'typescript' if ext == '.ts' else 'javascript'
    elif ext == '.json':
        return 'configuration'
    elif ext in {'.css', '.scss'}:
        return 'css'
    elif ext == '.html':
        return 'html'
    elif ext in {'.yaml', '.yml'}:
        return 'configuration'
    else:
        return 'other'


def get_category(file_path: Path) -> str:
    """파일 카테고리 결정"""
    path_str = str(file_path)

    if 'components/ui' in path_str:
        return 'ui-component'
    elif 'components' in path_str:
        return 'component'
    elif 'routes' in path_str:
        return 'route-page'
    elif 'stores' in path_str:
        return 'store'
    elif 'functions' in path_str and 'firebase/functions' not in path_str:
        return 'pure-function'
    elif 'firebase/functions' in path_str:
        return 'cloud-function'
    elif 'utils' in path_str:
        return 'utility'
    elif file_path.name in {'package.json', 'tsconfig.json', 'vite.config.ts',
                             'svelte.config.js', 'eslint.config.js', 'playwright.config.ts'}:
        return 'root-configuration'
    elif 'messages' in path_str and file_path.suffix == '.json':
        return 'i18n-message'
    else:
        return 'other'


def generate_description(file_path: Path) -> str:
    """파일 설명 생성"""
    category = get_category(file_path)
    name = file_path.stem

    descriptions = {
        'ui-component': f'{name} UI 컴포넌트',
        'component': f'{name} 컴포넌트',
        'route-page': f'{name} 페이지',
        'store': f'{name} 스토어',
        'pure-function': f'{name} 순수 함수',
        'cloud-function': f'{name} Cloud Function',
        'utility': f'{name} 유틸리티',
        'root-configuration': f'{name} 설정 파일',
        'i18n-message': f'{name} 다국어 메시지',
        'test': f'{name} 테스트 파일',
    }

    return descriptions.get(category, f'{name} 파일')


def get_language_from_extension(ext: str) -> str:
    """확장자에서 언어 결정"""
    language_map = {
        '.svelte': 'svelte',
        '.ts': 'typescript',
        '.js': 'javascript',
        '.mjs': 'javascript',
        '.cjs': 'javascript',
        '.json': 'json',
        '.css': 'css',
        '.scss': 'scss',
        '.html': 'html',
        '.yaml': 'yaml',
        '.yml': 'yaml',
    }
    return language_map.get(ext.lower(), 'text')


def create_spec_content(source_file: Path) -> str:
    """소스 코드를 SED 스펙 형식으로 변환"""
    try:
        # 파일 내용 읽기 (UTF-8)
        content = source_file.read_text(encoding='utf-8')
    except Exception as e:
        print(f"⚠️  파일 읽기 실패: {source_file}: {e}")
        return None

    # 상대 경로 계산
    try:
        relative_path = source_file.relative_to(PROJECT_ROOT)
    except ValueError:
        relative_path = source_file

    # 메타데이터 생성
    file_type = get_file_type(source_file)
    category = get_category(source_file)
    description = generate_description(source_file)
    language = get_language_from_extension(source_file.suffix)

    # YAML 헤더 생성
    yaml_header = f"""---
name: {source_file.name}
description: {description}
version: 1.0.0
type: {file_type}
category: {category}
original_path: {relative_path}
---
"""

    # 스펙 문서 본문 생성
    spec_body = f"""
# {source_file.name}

## 개요

**파일 경로**: `{relative_path}`
**파일 타입**: {file_type}
**카테고리**: {category}

{description}

## 소스 코드

```{language}
{content}
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
"""

    return yaml_header + spec_body


def collect_source_files() -> List[Path]:
    """변환할 소스 파일 목록 수집"""
    source_files = []

    for root, dirs, files in os.walk(PROJECT_ROOT):
        root_path = Path(root)

        # 제외 디렉토리 필터링
        dirs[:] = [d for d in dirs if not should_exclude_path(root_path / d)]

        for file in files:
            file_path = root_path / file

            # 제외 조건 확인
            if should_exclude_path(file_path):
                continue

            # 파일 확장자 확인
            if file_path.suffix not in INCLUDE_EXTENSIONS:
                continue

            source_files.append(file_path)

    return sorted(source_files)


def generate_all_specs():
    """모든 소스 코드의 스펙 문서 생성"""
    print("🚀 Sonub 소스 코드 스펙 생성 시작...")
    print()

    # 소스 파일 목록 수집
    source_files = collect_source_files()
    print(f"📊 총 {len(source_files)}개의 소스 파일 발견")
    print()

    # specs/repository 디렉토리 생성
    specs_dir = PROJECT_ROOT / 'specs' / 'repository'

    # 생성된 스펙 카운터
    created_count = 0
    skipped_count = 0
    error_count = 0

    for source_file in source_files:
        try:
            # 상대 경로 계산
            relative_path = source_file.relative_to(PROJECT_ROOT)

            # 스펙 파일 경로 생성 (.md 추가)
            spec_path = specs_dir / f"{relative_path}.md"

            # 디렉토리 생성
            spec_path.parent.mkdir(parents=True, exist_ok=True)

            # 스펙 내용 생성
            spec_content = create_spec_content(source_file)

            if spec_content is None:
                skipped_count += 1
                continue

            # 스펙 파일 쓰기 (UTF-8)
            spec_path.write_text(spec_content, encoding='utf-8')

            created_count += 1
            print(f"✅ {relative_path} → {spec_path.relative_to(PROJECT_ROOT)}")

        except Exception as e:
            error_count += 1
            print(f"❌ 오류 발생: {source_file}: {e}")

    print()
    print("=" * 60)
    print(f"✨ 스펙 생성 완료!")
    print(f"   - 생성됨: {created_count}개")
    print(f"   - 건너뜀: {skipped_count}개")
    print(f"   - 오류: {error_count}개")
    print("=" * 60)

    return created_count


if __name__ == '__main__':
    generate_all_specs()
