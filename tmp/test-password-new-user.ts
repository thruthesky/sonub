/**
 * 채팅방 비밀번호 Security Rules 테스트 - 새로운 사용자 차단 검증
 *
 * 목적: password: true 상태에서 새로운 사용자(durian)가 members에 추가되는지 검증
 *
 * 테스트 시나리오:
 * 1. Owner가 채팅방 생성
 * 2. 즉시 password: true 및 비밀번호 설정
 * 3. 새로운 사용자(durian)가 members에 추가 시도 → 실패해야 함
 * 4. 만약 성공하면 Cloud Functions 로그 확인
 */

import { initializeApp } from 'firebase/app';
import {
  getDatabase,
  ref,
  set,
  get,
  remove,
  update
} from 'firebase/database';
import {
  getAuth,
  signInWithEmailAndPassword
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

// 테스트용 사용자 정보
const OWNER_EMAIL = 'test1@test.com';
const OWNER_PASSWORD = '123123';
const DURIAN_EMAIL = 'test2@test.com'; // durian 역할
const DURIAN_PASSWORD = '123123';

let ownerUid: string;
let durianUid: string;
let testRoomId: string;

async function setup() {
  console.log('🔧 테스트 환경 설정 중...\n');

  // Owner 로그인
  const ownerCred = await signInWithEmailAndPassword(auth, OWNER_EMAIL, OWNER_PASSWORD);
  ownerUid = ownerCred.user.uid;
  console.log(`✅ Owner 로그인: ${ownerUid}`);

  // durian 사용자 UID 가져오기
  const durianCred = await signInWithEmailAndPassword(auth, DURIAN_EMAIL, DURIAN_PASSWORD);
  durianUid = durianCred.user.uid;
  console.log(`✅ Durian 로그인: ${durianUid}\n`);

  // Owner로 다시 전환
  await signInWithEmailAndPassword(auth, OWNER_EMAIL, OWNER_PASSWORD);
}

async function cleanup() {
  console.log('\n🧹 테스트 데이터 정리 중...');

  if (testRoomId) {
    await signInWithEmailAndPassword(auth, OWNER_EMAIL, OWNER_PASSWORD);

    await remove(ref(db, `chat-rooms/${testRoomId}`));
    await remove(ref(db, `chat-room-passwords/${testRoomId}`));
    console.log(`✅ 테스트 채팅방 삭제: ${testRoomId}`);
  }
}

async function test1_CreateRoomAndSetPassword() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 1: 채팅방 생성 및 즉시 비밀번호 설정');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // Owner로 채팅방 생성
  const newRoomRef = ref(db, `chat-rooms/-TestNewUser${Date.now()}`);
  testRoomId = newRoomRef.key!;

  const roomData = {
    name: '비밀번호 신규 사용자 테스트',
    description: 'durian 차단 검증',
    type: 'open',
    open: true,
    owner: ownerUid,
    password: false // 초기에는 false로 설정
  };

  await set(newRoomRef, roomData);
  console.log(`✅ 채팅방 생성: ${testRoomId}`);
  console.log(`📌 owner: ${ownerUid}`);
  console.log(`📌 password: false (초기값)\n`);

  // 즉시 비밀번호 설정 (durian 입장 전)
  await update(ref(db, `chat-rooms/${testRoomId}`), {
    password: true
  });
  await set(ref(db, `chat-room-passwords/${testRoomId}/password`), 'secret123');

  console.log(`✅ 비밀번호 설정 완료`);
  console.log(`📌 /chat-rooms/${testRoomId}/password: true`);
  console.log(`📌 /chat-room-passwords/${testRoomId}/password: "secret123"\n`);

  // 확인
  const snapshot = await get(ref(db, `chat-rooms/${testRoomId}`));
  console.log('📊 현재 채팅방 데이터:');
  console.log(JSON.stringify(snapshot.val(), null, 2));
  console.log();
}

async function test2_DurianJoinAttempt() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 2: 새로운 사용자(durian) members 추가 시도');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // durian으로 전환
  await signInWithEmailAndPassword(auth, DURIAN_EMAIL, DURIAN_PASSWORD);
  console.log(`🔄 사용자 전환: durian (${durianUid})`);
  console.log(`📌 현재 상태: password: true 설정됨`);
  console.log(`📌 durian은 기존에 members에 없음 (새로운 사용자)\n`);

  try {
    // members에 추가 시도 (joinChatRoom 함수와 동일)
    const memberRef = ref(db, `chat-rooms/${testRoomId}/members/${durianUid}`);
    await set(memberRef, true);

    console.log(`⚠️⚠️⚠️ members 추가 성공! (Security Rules 통과) ⚠️⚠️⚠️`);
    console.log(`❌ 예상 외: password: true인데도 차단되지 않음!`);
    console.log(`❌ 심각한 보안 문제 발생!\n`);

    // 확인
    const snapshot = await get(memberRef);
    console.log(`📊 members/${durianUid}: ${snapshot.val()}\n`);

    return true;
  } catch (error: any) {
    console.log(`✅ members 추가 실패: ${error.message}`);
    console.log(`✅ 예상대로 Security Rules가 차단함`);
    console.log(`📌 password: true 상태에서 신규 사용자 차단 성공!\n`);

    return false;
  }
}

async function test3_CheckSecurityRules() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 3: Security Rules 조건 검증');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // 채팅방 정보 확인
  const roomSnapshot = await get(ref(db, `chat-rooms/${testRoomId}`));
  const roomData = roomSnapshot.val();

  const passwordExists = roomData.password !== undefined;
  const passwordValue = roomData.password;
  const ownerValue = roomData.owner;

  console.log('📊 Security Rules 조건 평가:');
  console.log(`1. auth != null: ✅ true (durian 인증됨)`);
  console.log(`2. $uid === auth.uid: ✅ true (durian === durian)`);
  console.log(`3. 다음 중 하나:`);
  console.log(`   a) data.exists(): ❌ false (durian은 members에 없음)`);
  console.log(`   b) !password.exists(): ${!passwordExists} (password 필드 존재: ${passwordExists}, 값: ${passwordValue})`);
  console.log(`   c) owner === auth.uid: ❌ false (owner: ${ownerValue}, durian: ${durianUid})\n`);

  const shouldBlock = passwordExists && passwordValue === true;
  console.log(`📌 예상 결과: ${shouldBlock ? '차단되어야 함 ✅' : '통과될 수 있음 ⚠️'}\n`);
}

async function test4_PasswordVerificationFlow() {
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📝 테스트 4: 비밀번호 검증 흐름 (Cloud Functions)');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  // durian으로 비밀번호 입력 시뮬레이션
  await signInWithEmailAndPassword(auth, DURIAN_EMAIL, DURIAN_PASSWORD);

  console.log(`🔐 비밀번호 검증 흐름:`);
  console.log(`1. durian이 비밀번호 입력: "secret123"`);
  console.log(`2. 클라이언트가 /chat-room-passwords/${testRoomId}/try/${durianUid}에 저장`);
  console.log(`3. Cloud Functions(onPasswordTry) 트리거`);
  console.log(`4. 비밀번호 검증 성공 시 members에 추가 (Admin 권한)\n`);

  try {
    // 비밀번호 입력 시뮬레이션
    const tryRef = ref(db, `chat-room-passwords/${testRoomId}/try/${durianUid}`);
    await set(tryRef, 'secret123');

    console.log(`✅ 비밀번호 입력 시뮬레이션 완료`);
    console.log(`📌 Cloud Functions가 처리 중...\n`);

    // Cloud Functions 처리 대기 (3초)
    await new Promise(resolve => setTimeout(resolve, 3000));

    // members 확인
    const memberRef = ref(db, `chat-rooms/${testRoomId}/members/${durianUid}`);
    const memberSnapshot = await get(memberRef);

    if (memberSnapshot.exists()) {
      console.log(`✅ Cloud Functions가 members에 추가함: ${memberSnapshot.val()}`);
      console.log(`📌 비밀번호 검증 흐름 정상 작동\n`);
    } else {
      console.log(`❌ members에 추가되지 않음`);
      console.log(`⚠️  비밀번호 불일치 또는 Cloud Functions 오류\n`);
    }

  } catch (error: any) {
    console.log(`❌ 비밀번호 입력 실패: ${error.message}\n`);
  }
}

async function printSummary(directJoinSuccess: boolean) {
  console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log('📊 테스트 결과 요약');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

  if (directJoinSuccess) {
    console.log('⚠️⚠️⚠️ 심각한 보안 문제 발견! ⚠️⚠️⚠️\n');
    console.log('🐛 문제:');
    console.log('- password: true 상태에서도 새로운 사용자가 members에 직접 추가 가능');
    console.log('- Security Rules가 제대로 차단하지 못함\n');

    console.log('🔍 가능한 원인:');
    console.log('1. Security Rules가 Firebase에 제대로 배포되지 않음');
    console.log('2. Security Rules 로직에 버그가 있음');
    console.log('3. 클라이언트가 다른 경로로 우회함');
    console.log('4. Cloud Functions가 자동으로 추가함\n');

    console.log('✅ 추가 조사 필요:');
    console.log('- Firebase Console에서 배포된 Rules 확인');
    console.log('- Cloud Functions 로그 확인');
    console.log('- 클라이언트 코드 흐름 재확인\n');

  } else {
    console.log('✅ Security Rules가 정상 작동!\n');
    console.log('📌 검증 결과:');
    console.log('- password: true 상태에서 새로운 사용자 차단 성공');
    console.log('- Security Rules가 제대로 배포되어 작동 중');
    console.log('- 비밀번호 검증 흐름만 사용 가능\n');

    console.log('💡 실제 문제 원인 추정:');
    console.log('- durian이 password 설정 **전**에 입장했을 가능성');
    console.log('- 또는 Cloud Functions를 통한 정상 입장');
    console.log('- 타이밍 이슈일 가능성\n');
  }

  console.log('🔐 정상적인 입장 흐름:');
  console.log('1. durian이 비밀번호 입력');
  console.log('2. /chat-room-passwords/{roomId}/try/{uid}에 저장');
  console.log('3. Cloud Functions가 검증 후 members에 추가 (Admin 권한)\n');
}

async function main() {
  try {
    console.log('🚀 채팅방 비밀번호 Security Rules 테스트 (신규 사용자)\n');

    await setup();
    await test1_CreateRoomAndSetPassword();
    await test3_CheckSecurityRules();

    const directJoinSuccess = await test2_DurianJoinAttempt();

    if (!directJoinSuccess) {
      // Security Rules가 정상 작동하면 Cloud Functions 흐름도 테스트
      await test4_PasswordVerificationFlow();
    }

    await printSummary(directJoinSuccess);

  } catch (error) {
    console.error('❌ 테스트 실행 중 오류:', error);
  } finally {
    await cleanup();
    process.exit(0);
  }
}

main();
