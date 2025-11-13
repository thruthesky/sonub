---
title: "Dockerfile"
description: "Sonub 소스 코드 저장용 자동 생성 SED 스펙"
original_path: "Dockerfile"
spec_type: "repository-source"
---

# 목적
이 문서는 `Dockerfile` 파일의 전체 내용을 기록하여 SED 스펙만으로도 Sonub 프로젝트를 재구성할 수 있도록 합니다.

## 파일 정보
- 상대 경로: `Dockerfile`
- MIME: `text/plain`
- 유형: 텍스트

# 원본 소스 코드
```````dockerfile
# Node 20 기반으로 빌드 (SvelteKit adapter-node용)
FROM node:20

# 앱 디렉토리 생성 및 설정
WORKDIR /app

# package.json과 package-lock.json 복사
COPY package*.json ./

# 의존성 설치 (npm ci가 더 빠르고 정확)
RUN npm ci

# 나머지 소스 코드 복사
COPY . .

# SvelteKit 빌드 실행
RUN npm run build

# 컨테이너가 수신할 포트 지정 (Dokploy에서 자동 감지됨)
EXPOSE 3000

# Node 서버 실행 (adapter-node에서 build 디렉토리 실행)
CMD ["node", "build"]

```````
