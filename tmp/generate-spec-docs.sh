#!/bin/bash

# 생성할 파일 목록과 정보
declare -A FILES=(
  ["src/lib/stores/auth.svelte.ts"]="인증 상태 관리 스토어|store|Svelte 5 runes를 사용하여 Firebase Authentication 상태를 전역으로 관리합니다."
  ["src/lib/stores/database.svelte.ts"]="Firebase Realtime Database 유틸리티|store|데이터베이스 읽기 쓰기 업데이트 삭제 및 실시간 구독 기능을 제공합니다."
  ["src/lib/stores/user-profile.svelte.ts"]="사용자 프로필 전용 스토어|store|Firebase Realtime Database의 users 노드를 실시간으로 구독하여 사용자 프로필 데이터를 중앙에서 관리합니다."
  ["src/lib/utils.ts"]="유틸리티 함수 모음|util|shadcn-svelte와 호환되는 클래스 이름 병합 함수를 제공합니다."
  ["src/lib/utils/auth-helpers.ts"]="Firebase Authentication 헬퍼 함수|util|Google 및 Apple 로그인에 필요한 유틸리티 함수를 제공합니다."
  ["src/lib/utils/admin-service.ts"]="관리자 서비스|util|테스트 사용자 생성 사용자 목록 조회 등의 관리자 기능을 담당합니다."
  ["src/lib/utils/test-user-generator.ts"]="테스트 사용자 데이터 생성 유틸리티|util|테스트 목적으로 임시 사용자 데이터를 생성합니다."
)

echo "스펙 문서 생성 스크립트 작성 완료"
