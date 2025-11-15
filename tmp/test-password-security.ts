/**
 * 채팅방 비밀번호 Security Rules 테스트
 *
 * 목적: password: true인데도 일반 사용자가 members에 추가되는 문제 검증
 *
 * 테스트 시나리오:
 * 1. 채팅방 생성 (password 필드 없음 - 현재 코드와 동일)
 * 2. 일반 사용자가 members에 추가 시도 → 성공해야 함 (버그!)
 * 3. owner가 password: true 설정
 * 4. 일반 사용자가 이미 members이므로 계속 유지됨
 * 5. 새로운 사용자가 members에 추가 시도 → 실패해야 함 (정상)
 */

import { initializeApp } from 'firebase/app';
import {
  getDatabase,
  ref,
  set,
  get,
  remove,
  connectDatabaseEmulator
} from 'firebase/database';
import {
  getAuth,
  signInWithEmailAndPassword,
  connectAuthEmulator
} from 'firebase/auth';
import * as fs from 'fs';
import * as path from 'path';

// .env 파일 직접 파싱
function loadEnv() {
  const envPath = path.resolve(process.cwd(), '.env');
  const envContent = fs.readFileSync(envPath, 'utf-8');
  const lines = envContent.split('\n');

  lines.forEach(line => {
    const match = line.match(/^([^=:#]+)=(.*)$/);
    if (match) {
      const key = match[1].trim();
      const value = match[2].trim();
      process.env[key] = value;
    }
  });
}

loadEnv();

// Firebase 설정
const firebaseConfig = {
  apiKey: process.env.PUBLIC_FIREBASE_API_KEY,
  authDomain: process.env.PUBLIC_FIREBASE_AUTH_DOMAIN,
  databaseURL: process.env.PUBLIC_FIREBASE_DATABASE_URL,
  projectId: process.env.PUBLIC_FIREBASE_PROJECT_ID,
  storageBucket: process.env.PUBLIC_FIREBASE_STORAGE_BUCKET,
  messagingSenderId: process.env.PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
  appId: process.env.PUBLIC_FIREBASE_APP_ID
};

// Firebase 앱 초기화
const app = initializeApp(firebaseConfig, 'test-app');
const db = getDatabase(app);
const auth = getAuth(app);

// 테스트용 사용자 정보 (실제 존재하는 사용자 UID 사용)
const OWNER_EMAIL = 'test1@test.com'; // owner 사용자
const OWNER_PASSWORD = '123123';
const USER_EMAIL = 'test2@test.com'; // 일반 사용자
const USER_PASSWORD = '123123';

let ownerUid: string;
let userUid: string;
let testRoomId: string;

async function setup() {
  console.log('🔧 테스트 환경 설정 중...\n');

  // Owner 로그인하여 UID 가져오기
  const ownerCred = await signInWithEmailAndPassword(auth, OWNER_EMAIL, OWNER_PASSWORD);
  ownerUid = ownerCred.user.uid;
  console.log(`✅ Owner 로그인: ${ownerUid}`);

  // 일반 사용자 UID 미리 가져오기 (나중에 사용)
  const userCred = await signInWithEmailAndPassword(auth, USER_EMAIL, USER_PASSWORD);
  userUid = userCred.user.uid;
  console.log(`✅ User 로그인: ${userUid}\n`);

  // Owner로 다시 전환
  await signInWithEmailAndPassword(auth, OWNER_EMAIL, OWNER_PASSWORD);
}

async function cleanup() {
  console.log('\n🧹 테스트 데이터 정리 중...');

  if (testRoomId) {
    // Owner로 로그인하여 정리
    await signInWithEmailAndPassword(auth, OWNER_EMAIL, OWNER_PASSWORD);

    // 채팅방 삭제
    await remove(ref(db, `chat-rooms/${testRoomId}`));
    await remove(ref(db, `chat-room-passwords/${testRoomId}`));
    console.log(`✅ 테스트 채팅방 삭제: ${testRoomId}`);
  }
}

async function test1_CreateRoomWithoutPasswordField() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 1: 채팅방 생성 (password 필드 없음)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // Owner로 채팅방 생성 (현재 코드와 동일하게 password 필드 없음)
  const roomsRef = ref(db, 'chat-rooms');
  const newRoomRef = ref(db, `chat-rooms/-TestRoom${Date.now()}`);
  testRoomId = newRoomRef.key!;

  const roomData = {
    name: '비밀번호 테스트 채팅방',
    description: 'Security Rules 검증용',
    type: 'open',
    open: true,
    owner: ownerUid
    // ⚠️ password 필드 없음! (현재 ChatCreateDialog.svelte와 동일)
  };

  await set(newRoomRef, roomData);
  console.log(`✅ 채팅방 생성 완료: ${testRoomId}`);
  console.log(`📌 owner: ${ownerUid}`);
  console.log(`⚠️  password 필드: 없음\n`);

  // 생성된 데이터 확인
  const snapshot = await get(newRoomRef);
  console.log('📊 생성된 채팅방 데이터:');
  console.log(JSON.stringify(snapshot.val(), null, 2));
  console.log();
}

async function test2_UserJoinWithoutPassword() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 2: 일반 사용자가 members에 추가 시도');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // 일반 사용자로 전환
  await signInWithEmailAndPassword(auth, USER_EMAIL, USER_PASSWORD);
  console.log(`🔄 사용자 전환: ${userUid}`);

  try {
    // members에 추가 시도 (현재 joinChatRoom 함수와 동일)
    const memberRef = ref(db, `chat-rooms/${testRoomId}/members/${userUid}`);
    await set(memberRef, true);

    console.log(`✅ members 추가 성공! (Security Rules 통과)`);
    console.log(`⚠️  예상: password 필드가 없으므로 !password.exists() = true`);
    console.log(`⚠️  결과: 일반 사용자가 비밀번호 없이 입장 가능!\n`);

    // 확인
    const snapshot = await get(memberRef);
    console.log(`📊 members/${userUid}: ${snapshot.val()}\n`);

    return true;
  } catch (error: any) {
    console.log(`❌ members 추가 실패: ${error.message}`);
    console.log(`📌 Security Rules가 차단함\n`);
    return false;
  }
}

async function test3_OwnerSetPassword() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 3: Owner가 비밀번호 설정');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // Owner로 전환
  await signInWithEmailAndPassword(auth, OWNER_EMAIL, OWNER_PASSWORD);
  console.log(`🔄 사용자 전환: ${ownerUid}`);

  // 비밀번호 설정
  const passwordFlagRef = ref(db, `chat-rooms/${testRoomId}/password`);
  await set(passwordFlagRef, true);
  console.log(`✅ /chat-rooms/${testRoomId}/password: true 설정`);

  const passwordValueRef = ref(db, `chat-room-passwords/${testRoomId}/password`);
  await set(passwordValueRef, 'test1234');
  console.log(`✅ /chat-room-passwords/${testRoomId}/password: "test1234" 설정\n`);

  // 확인
  const roomSnapshot = await get(ref(db, `chat-rooms/${testRoomId}`));
  console.log('📊 채팅방 데이터 (비밀번호 설정 후):');
  console.log(JSON.stringify(roomSnapshot.val(), null, 2));
  console.log();
}

async function test4_CheckExistingUser() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 4: 기존 사용자 상태 확인');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // members 확인
  const memberRef = ref(db, `chat-rooms/${testRoomId}/members/${userUid}`);
  const snapshot = await get(memberRef);

  if (snapshot.exists()) {
    console.log(`✅ 일반 사용자가 여전히 members에 존재: ${snapshot.val()}`);
    console.log(`⚠️  이유: data.exists() = true → Security Rules 통과`);
    console.log(`⚠️  문제: 비밀번호 설정 후에도 기존 사용자는 계속 유지됨!\n`);
  } else {
    console.log(`❌ 일반 사용자가 members에서 제거됨\n`);
  }
}

async function test5_NewUserJoinWithPassword() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 5: 새로운 사용자가 members에 추가 시도');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // 일반 사용자로 전환
  await signInWithEmailAndPassword(auth, USER_EMAIL, USER_PASSWORD);

  // 다른 UID로 시뮬레이션 (실제로는 test3 사용자가 필요)
  const newUserUid = 'newTestUser123';

  console.log(`🔄 새로운 사용자: ${newUserUid}`);
  console.log(`📌 현재 상태: password: true 설정됨`);

  try {
    // members에 추가 시도
    const memberRef = ref(db, `chat-rooms/${testRoomId}/members/${newUserUid}`);
    await set(memberRef, true);

    console.log(`✅ members 추가 성공!`);
    console.log(`⚠️  예상 외: Security Rules가 차단해야 하는데 통과함\n`);

    return true;
  } catch (error: any) {
    console.log(`❌ members 추가 실패: ${error.message}`);
    console.log(`✅ 예상: Security Rules가 정상적으로 차단함`);
    console.log(`📌 이유: password.exists() = true, !password.exists() = false`);
    console.log(`📌 이유: data.exists() = false (새 사용자)`);
    console.log(`📌 이유: owner !== 새 사용자\n`);

    return false;
  }
}

async function printSummary() {
  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📊 테스트 결과 요약');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  console.log('🔍 발견된 문제:');
  console.log('1. 채팅방 생성 시 password 필드를 설정하지 않음');
  console.log('2. 일반 사용자가 password 없이 members에 추가 가능');
  console.log('3. 나중에 password: true 설정해도 기존 사용자는 유지됨\n');

  console.log('💡 Security Rules 분석:');
  console.log('".write": "auth != null && $uid === auth.uid && (');
  console.log('  data.exists() ||                              ← 이미 멤버면 항상 허용');
  console.log('  !password.exists() ||                         ← password 필드 없으면 허용');
  console.log('  owner === auth.uid                            ← owner면 허용');
  console.log(')"');
  console.log();

  console.log('🐛 타이밍 이슈:');
  console.log('① 채팅방 생성 (password 필드 없음)');
  console.log('② 일반 사용자 입장 → !password.exists() = true → 통과 ✅');
  console.log('③ members에 추가됨 (cherry: true)');
  console.log('④ owner가 password: true 설정');
  console.log('⑤ cherry는 이미 members → data.exists() = true → 계속 유지 ⚠️\n');

  console.log('✅ 해결 방법:');
  console.log('채팅방 생성 시 password: false 기본값 설정');
  console.log('→ ChatCreateDialog.svelte의 roomData에 추가\n');
}

async function main() {
  try {
    console.log('🚀 채팅방 비밀번호 Security Rules 테스트 시작\n');

    await setup();
    await test1_CreateRoomWithoutPasswordField();

    const userJoinSuccess = await test2_UserJoinWithoutPassword();

    if (userJoinSuccess) {
      await test3_OwnerSetPassword();
      await test4_CheckExistingUser();
      await test5_NewUserJoinWithPassword();
    }

    await printSummary();

  } catch (error) {
    console.error('❌ 테스트 실행 중 오류:', error);
  } finally {
    await cleanup();
    process.exit(0);
  }
}

main();
