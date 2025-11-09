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
