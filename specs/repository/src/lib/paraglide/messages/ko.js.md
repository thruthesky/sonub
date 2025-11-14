---
name: ko.js
description: ko 파일
version: 1.0.0
type: javascript
category: other
original_path: src/lib/paraglide/messages/ko.js
---

# ko.js

## 개요

**파일 경로**: `src/lib/paraglide/messages/ko.js`
**파일 타입**: javascript
**카테고리**: other

ko 파일

## 소스 코드

```javascript
/* eslint-disable */


export const helloworld1 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Hello, ${i.name} from ko!`
};

export const commonloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `로딩 중...`
};

export const commonclose1 = /** @type {(inputs: {}) => string} */ () => {
	return `닫기`
};

export const commonsave1 = /** @type {(inputs: {}) => string} */ () => {
	return `저장`
};

export const commondelete1 = /** @type {(inputs: {}) => string} */ () => {
	return `삭제`
};

export const commoncancel1 = /** @type {(inputs: {}) => string} */ () => {
	return `취소`
};

export const commonconfirm1 = /** @type {(inputs: {}) => string} */ () => {
	return `확인`
};

export const commonrefresh1 = /** @type {(inputs: {}) => string} */ () => {
	return `새로고침`
};

export const commonretry1 = /** @type {(inputs: {}) => string} */ () => {
	return `다시 시도`
};

export const commoninfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `정보`
};

export const commonstatus1 = /** @type {(inputs: {}) => string} */ () => {
	return `상태`
};

export const commonsuccess1 = /** @type {(inputs: {}) => string} */ () => {
	return `성공`
};

export const commonerror1 = /** @type {(inputs: {}) => string} */ () => {
	return `오류`
};

export const commoncomplete1 = /** @type {(inputs: {}) => string} */ () => {
	return `완료`
};

export const commonprogress1 = /** @type {(inputs: {}) => string} */ () => {
	return `진행 상황`
};

export const commongo1 = /** @type {(inputs: {}) => string} */ () => {
	return `이동`
};

export const commonuser1 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자`
};

export const navhome1 = /** @type {(inputs: {}) => string} */ () => {
	return `홈`
};

export const navabout1 = /** @type {(inputs: {}) => string} */ () => {
	return `소개`
};

export const navproducts1 = /** @type {(inputs: {}) => string} */ () => {
	return `제품`
};

export const navcontact1 = /** @type {(inputs: {}) => string} */ () => {
	return `연락`
};

export const navboard1 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판`
};

export const navchat1 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅`
};

export const navfindusers2 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 찾기`
};

export const navlogin1 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인`
};

export const navlogout1 = /** @type {(inputs: {}) => string} */ () => {
	return `로그아웃`
};

export const navmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `메뉴`
};

export const navmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `내 프로필`
};

export const authwelcome1 = /** @type {(inputs: {}) => string} */ () => {
	return `환영합니다`
};

export const authwelcomemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Welcome to Sonub`
};

export const authintro1 = /** @type {(inputs: {}) => string} */ () => {
	return `SedAi.Dev 에 Sonub spec 으로 커뮤니티 기능을 집어 넣는다.`
};

export const authgetstarted2 = /** @type {(inputs: {}) => string} */ () => {
	return `시작하기`
};

export const authsigninguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `Google 또는 Apple 계정으로 로그인하세요`
};

export const authsigninguidestart4 = /** @type {(inputs: {}) => string} */ () => {
	return `Google 또는 Apple 계정으로 로그인하여 시작하세요`
};

export const authsigninaction3 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인하기`
};

export const authsigninwithgoogle4 = /** @type {(inputs: {}) => string} */ () => {
	return `Google로 로그인`
};

export const authsigninwithapple4 = /** @type {(inputs: {}) => string} */ () => {
	return `Apple로 로그인`
};

export const authsigningin2 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인 중...`
};

export const authsigningout2 = /** @type {(inputs: {}) => string} */ () => {
	return `로그아웃 중...`
};

export const authsigninfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인 실패`
};

export const authsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인 필요`
};

export const authsigninrequireddesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `계정에 로그인하여 더 많은 기능을 사용하세요`
};

export const authwelcomeuser2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.name}님, 로그인하셨습니다.`
};

export const profilenickname1 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임`
};

export const profilenicknameinput2 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임을 입력하세요`
};

export const profilenicknamerequired2 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임을 입력해주세요.`
};

export const profilenicknamelength2 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임은 50자 이하여야 합니다.`
};

export const profilenicknamemaxlength3 = /** @type {(inputs: {}) => string} */ () => {
	return `최대 50자`
};

export const profilegender1 = /** @type {(inputs: {}) => string} */ () => {
	return `성별`
};

export const profilegendermale2 = /** @type {(inputs: {}) => string} */ () => {
	return `남성`
};

export const profilegenderfemale2 = /** @type {(inputs: {}) => string} */ () => {
	return `여성`
};

export const profilegendernoanswer3 = /** @type {(inputs: {}) => string} */ () => {
	return `선택 안 함`
};

export const profiledateofbirth3 = /** @type {(inputs: {}) => string} */ () => {
	return `생년월일`
};

export const profileyear1 = /** @type {(inputs: {}) => string} */ () => {
	return `년도`
};

export const profilemonth1 = /** @type {(inputs: {}) => string} */ () => {
	return `월`
};

export const profileday1 = /** @type {(inputs: {}) => string} */ () => {
	return `일`
};

export const profileyearvalue2 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}년`
};

export const profilemonthvalue2 = /** @type {(inputs: { month: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.month}월`
};

export const profiledayvalue2 = /** @type {(inputs: { day: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.day}일`
};

export const profiledateofbirthpasterror5 = /** @type {(inputs: {}) => string} */ () => {
	return `생년월일은 과거여야 합니다.`
};

export const profileagerestriction2 = /** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */ (i) => {
	return `만 18세 이상만 가입할 수 있습니다 (${i.minYear}년 ~ ${i.maxYear}년생)`
};

export const profilepicture1 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 사진`
};

export const profilepictureuploadguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `클릭하여 프로필 사진 업로드 (최대 5MB)`
};

export const profilepictureuploadsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 사진이 업로드되었습니다.`
};

export const profilepictureuploadfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `사진 업로드에 실패했습니다. 다시 시도해주세요.`
};

export const profilepictureremove2 = /** @type {(inputs: {}) => string} */ () => {
	return `사진 제거`
};

export const profilepictureremovesuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 사진이 제거되었습니다.`
};

export const profilepictureremovefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `사진 제거에 실패했습니다. 다시 시도해주세요.`
};

export const profilepicturetypeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `이미지 파일만 업로드할 수 있습니다.`
};

export const profilepicturesizeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `파일 크기는 5MB 이하여야 합니다.`
};

export const profilesave1 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 저장`
};

export const profilesaving1 = /** @type {(inputs: {}) => string} */ () => {
	return `저장 중...`
};

export const profilesavesuccess2 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필이 성공적으로 업데이트되었습니다.`
};

export const profilesavefailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 저장에 실패했습니다. 다시 시도해주세요.`
};

export const profileloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 정보를 불러오는 중...`
};

export const profileloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `프로필 정보를 불러오는데 실패했습니다.`
};

export const profileinfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `회원 정보`
};

export const profileinfoguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `닉네임, 성별, 생년월일을 설정하세요`
};

export const profileinfoeditguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `회원 정보를 수정할 수 있습니다`
};

export const userlist1 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 목록`
};

export const userlistguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase Realtime Database에 등록된 모든 사용자를 확인하세요`
};

export const userjoindate2 = /** @type {(inputs: {}) => string} */ () => {
	return `가입일:`
};

export const userlastlogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `마지막 로그인:`
};

export const usernoname2 = /** @type {(inputs: {}) => string} */ () => {
	return `이름 없음`
};

export const userloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 목록을 불러오는 중...`
};

export const usernotregistered2 = /** @type {(inputs: {}) => string} */ () => {
	return `등록된 사용자가 없습니다`
};

export const usernotjoined2 = /** @type {(inputs: {}) => string} */ () => {
	return `아직 가입한 사용자가 없습니다.`
};

export const userloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 목록을 불러올 수 없습니다`
};

export const userloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `더 많은 사용자를 불러오는 중...`
};

export const userallloaded2 = /** @type {(inputs: {}) => string} */ () => {
	return `모든 사용자를 불러왔습니다`
};

export const userunknownerror2 = /** @type {(inputs: {}) => string} */ () => {
	return `알 수 없는 오류가 발생했습니다.`
};

export const userprofiledetail2 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 프로필 상세`
};

export const menutitle1 = /** @type {(inputs: {}) => string} */ () => {
	return `메뉴`
};

export const menuguide1 = /** @type {(inputs: {}) => string} */ () => {
	return `계정 및 설정 관리`
};

export const menumyaccount2 = /** @type {(inputs: {}) => string} */ () => {
	return `내 계정`
};

export const menueditprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `회원 정보 수정`
};

export const menuadminpage2 = /** @type {(inputs: {}) => string} */ () => {
	return `관리자 페이지`
};

export const menudevtest2 = /** @type {(inputs: {}) => string} */ () => {
	return `개발 테스트 (DatabaseListView)`
};

export const admindashboard1 = /** @type {(inputs: {}) => string} */ () => {
	return `관리자 대시보드`
};

export const admindashboardguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `관리 도구를 선택하여 작업을 시작하세요.`
};

export const admintestusermanagement3 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 관리`
};

export const admintestusermanagementdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `임시 사용자 생성/목록/삭제를 한 페이지에서 관리합니다`
};

export const adminuserlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 목록`
};

export const adminuserlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `생성된 테스트 사용자 목록을 확인합니다`
};

export const adminreportlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `관리자 신고 목록`
};

export const adminreportlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 신고 내역을 확인하고 관리합니다`
};

export const admintest1 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트`
};

export const admintestdesc2 = /** @type {(inputs: {}) => string} */ () => {
	return `기타 테스트 기능들을 사용합니다`
};

export const admininfopermissionnotimplemented4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 현재 관리자 권한 검증은 구현되지 않았습니다.`
};

export const admininfotestflag3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 테스트 사용자는 \`isTemporary: true\` 플래그로 표시됩니다.`
};

export const admininfodatadelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 테스트 데이터는 언제든지 삭제할 수 있습니다.`
};

export const admindashboardmenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `대시보드`
};

export const adminuserlistmenu3 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자목록`
};

export const testuserlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 목록`
};

export const testuserguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트용 임시 사용자 목록을 조회하고 관리합니다.`
};

export const testusercount2 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 수`
};

export const testusercreated2 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count}명 생성됨`
};

export const testusernotcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `아직 생성된 사용자 없음`
};

export const testusercreate2 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 생성`
};

export const testusercreateicon3 = /** @type {(inputs: {}) => string} */ () => {
	return `🚀 테스트 사용자 생성`
};

export const testusercreateguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `버튼을 클릭하면 테스트용 임시 사용자 100명이 순차적으로 생성되고 목록에 추가됩니다.`
};

export const testusercreating2 = /** @type {(inputs: {}) => string} */ () => {
	return `⏳ 생성 중...`
};

export const testusercreatecomplete3 = /** @type {(inputs: {}) => string} */ () => {
	return `✓ 생성 완료`
};

export const testusercreatecompletemessage4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `✓ 완료: ${i.count}명의 테스트 사용자가 생성되었습니다.`
};

export const testusercreateatonce4 = /** @type {(inputs: {}) => string} */ () => {
	return `한 번에 생성되는 수`
};

export const testusercurrentcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `현재 생성된 수`
};

export const testuserdeletinginprogress4 = /** @type {(inputs: {}) => string} */ () => {
	return `삭제 진행 중`
};

export const testuserdeleting2 = /** @type {(inputs: {}) => string} */ () => {
	return `삭제 중...`
};

export const testuserdeleteall3 = /** @type {(inputs: {}) => string} */ () => {
	return `모든 테스트 사용자 삭제`
};

export const testusernotcreatedguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `생성된 테스트 사용자가 없습니다. <strong><a class="text-blue-600" href="/admin/test/create-test-data">테스트 데이터 생성</a></strong> 페이지에서 테스트 사용자 100명을 만들 수 있습니다.`
};

export const testusergender2 = /** @type {(inputs: {}) => string} */ () => {
	return `성별`
};

export const testuserbirthyear3 = /** @type {(inputs: {}) => string} */ () => {
	return `생년도`
};

export const testusercreateddate3 = /** @type {(inputs: {}) => string} */ () => {
	return `생성일`
};

export const testuserstatus2 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자`
};

export const testuserinfodisplay3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 이 페이지에는 \`isTemporary: true\`로 표시된 사용자만 표시됩니다.`
};

export const testuserinfodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 각 사용자는 개별적으로 또는 일괄적으로 삭제할 수 있습니다.`
};

export const testuserinfonorecover4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 삭제된 사용자는 복구할 수 없습니다.`
};

export const testuseryeardisplay3 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}년`
};

export const constructiontitle1 = /** @type {(inputs: {}) => string} */ () => {
	return `공사중`
};

export const constructionmessage1 = /** @type {(inputs: {}) => string} */ () => {
	return `이 페이지는 현재 개발 중입니다.`
};

export const constructionbacktohome3 = /** @type {(inputs: {}) => string} */ () => {
	return `홈으로 돌아가기`
};

export const boardconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판 기능은 현재 개발 중입니다.`
};

export const chatconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅 기능은 현재 개발 중입니다.`
};

export const sidebarmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `메뉴`
};

export const sidebarrecentactivity2 = /** @type {(inputs: {}) => string} */ () => {
	return `최근 활동`
};

export const sidebarnorecentactivity3 = /** @type {(inputs: {}) => string} */ () => {
	return `최근 활동이 없습니다.`
};

export const sidebarselectlanguage2 = /** @type {(inputs: {}) => string} */ () => {
	return `언어 선택`
};

export const sidebarbuildversion2 = /** @type {(inputs: {}) => string} */ () => {
	return `빌드 버전`
};

export const sidebarmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `내 프로필`
};

export const sidebarnotifications1 = /** @type {(inputs: {}) => string} */ () => {
	return `알림`
};

export const sidebarnonotifications2 = /** @type {(inputs: {}) => string} */ () => {
	return `새로운 알림이 없습니다.`
};

export const sidebarsuggestions1 = /** @type {(inputs: {}) => string} */ () => {
	return `추천`
};

export const sidebarpopularposts2 = /** @type {(inputs: {}) => string} */ () => {
	return `인기 게시물`
};

export const sidebarnewfeatures2 = /** @type {(inputs: {}) => string} */ () => {
	return `새로운 기능`
};

export const featuresveltekit51 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 5`
};

export const featuresveltekit5desc2 = /** @type {(inputs: {}) => string} */ () => {
	return `최신 Svelte 5 runes를 사용한 모던 프레임워크`
};

export const featurefirebaseauth2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase Auth`
};

export const featurefirebaseauthdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `Google 및 Apple 소셜 로그인 지원`
};

export const featuretailwindcss2 = /** @type {(inputs: {}) => string} */ () => {
	return `TailwindCSS`
};

export const featuretailwindcssdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `shadcn-svelte와 함께하는 아름다운 UI`
};

export const linksveltekitdocs3 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 문서`
};

export const linkfirebasedocs2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase 문서`
};

export const linkshadcnsvelte2 = /** @type {(inputs: {}) => string} */ () => {
	return `shadcn-svelte`
};

export const pagetitlehome2 = /** @type {(inputs: {}) => string} */ () => {
	return `Sonub - Welcome`
};

export const pagetitlemenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `메뉴 - Sonub`
};

export const pagetitlelogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인 - Sonub`
};

export const pagetitleuserlist3 = /** @type {(inputs: {}) => string} */ () => {
	return `사용자 목록 - Sonub`
};

export const pagetitlemyprofile3 = /** @type {(inputs: {}) => string} */ () => {
	return `내 프로필 - Sonub`
};

export const pagetitleboard2 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판 - Sonub`
};

export const pagetitlechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅 - Sonub`
};

export const pagemetalogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `Sonub에 로그인하세요`
};

export const admintestmenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트`
};

export const profilepicturepreview2 = /** @type {(inputs: {}) => string} */ () => {
	return `업로드 미리보기`
};

export const testuserdeleteconfirm3 = /** @type {(inputs: {}) => string} */ () => {
	return `이 테스트 사용자를 삭제하시겠습니까?`
};

export const testusernousertodelete5 = /** @type {(inputs: {}) => string} */ () => {
	return `삭제할 테스트 사용자가 없습니다.`
};

export const testuserdeleteallconfirm4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count}명의 모든 테스트 사용자를 삭제하시겠습니까?`
};

export const testuserdeleteerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 삭제 중 오류가 발생했습니다.`
};

export const testuserlistloaderror4 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 목록 로드 중 오류:`
};

export const testusercreateerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `테스트 사용자 생성 중 오류:`
};

export const testuserdeleteallerror4 = /** @type {(inputs: {}) => string} */ () => {
	return `모든 테스트 사용자 삭제 중 오류:`
};

export const testuserprogressdisplay3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

export const testusergendermale3 = /** @type {(inputs: {}) => string} */ () => {
	return `남성`
};

export const testusergenderfemale3 = /** @type {(inputs: {}) => string} */ () => {
	return `여성`
};

export const testusergenderother3 = /** @type {(inputs: {}) => string} */ () => {
	return `기타`
};

export const testusercreatecount3 = /** @type {(inputs: {}) => string} */ () => {
	return `100`
};

export const reportreasonabuse2 = /** @type {(inputs: {}) => string} */ () => {
	return `욕설 및 비방`
};

export const reportreasonfakenews3 = /** @type {(inputs: {}) => string} */ () => {
	return `허위 정보`
};

export const reportreasonspam2 = /** @type {(inputs: {}) => string} */ () => {
	return `스팸`
};

export const reportreasoninappropriate2 = /** @type {(inputs: {}) => string} */ () => {
	return `부적절한 콘텐츠`
};

export const reportreasonother2 = /** @type {(inputs: {}) => string} */ () => {
	return `기타`
};

export const commonpost1 = /** @type {(inputs: {}) => string} */ () => {
	return `게시글`
};

export const commoncomment1 = /** @type {(inputs: {}) => string} */ () => {
	return `댓글`
};

export const reportcancelconfirm2 = /** @type {(inputs: {}) => string} */ () => {
	return `신고를 취소하시겠습니까?`
};

export const authloginrequired2 = /** @type {(inputs: {}) => string} */ () => {
	return `로그인이 필요합니다`
};

export const reportmylist2 = /** @type {(inputs: {}) => string} */ () => {
	return `내 신고 목록`
};

export const reportmylistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `내가 작성한 신고를 확인할 수 있습니다`
};

export const pagetitlemyreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `내 신고 목록 - Sonub`
};
export { adminreportlistguide3 } from "./en.js"
export { pagetitleadminreports3 } from "./en.js"

export const chatchatroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅방`
};

export const chatroom1 = /** @type {(inputs: {}) => string} */ () => {
	return `방:`
};

export const chatoverview1 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅 개요`
};

export const chatsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅을 시작하려면 로그인하세요.`
};

export const chatprovideuid2 = /** @type {(inputs: {}) => string} */ () => {
	return `단일 채팅을 열려면 uid 쿼리 매개변수를 제공하세요.`
};

export const chatloadingprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `참가자 프로필을 로딩 중...`
};

export const chatloadprofilefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `참가자 프로필 로드 실패.`
};

export const chatchattingwith2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.name}님과 채팅 중입니다.`
};

export const chatroomready2 = /** @type {(inputs: { roomId: NonNullable<unknown> }) => string} */ (i) => {
	return `방 ID ${i.roomId}가 준비되었습니다.`
};

export const chatselectconversation2 = /** @type {(inputs: {}) => string} */ () => {
	return `대화를 선택하여 시작하세요.`
};

export const chatroomnotready3 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅방이 준비되지 않았습니다.`
};

export const chatadduidorroomid5 = /** @type {(inputs: {}) => string} */ () => {
	return `대화를 열려면 URL에 ?uid=TARGET_UID 또는 ?roomId=ROOM_KEY를 추가하세요.`
};

export const chatloadingmessages2 = /** @type {(inputs: {}) => string} */ () => {
	return `메시지 로딩 중...`
};

export const chatnomessages2 = /** @type {(inputs: {}) => string} */ () => {
	return `아직 메시지가 없습니다. 인사해보세요!`
};

export const chatloadmessagesfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `메시지 로드 실패.`
};

export const chatunknownerror2 = /** @type {(inputs: {}) => string} */ () => {
	return `알 수 없는 오류.`
};

export const chatloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `더 불러오는 중...`
};

export const chatuptodate3 = /** @type {(inputs: {}) => string} */ () => {
	return `최신 상태입니다.`
};

export const chatpreparingstream2 = /** @type {(inputs: {}) => string} */ () => {
	return `메시지 스트림 준비 중...`
};

export const chatwritemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `메시지를 입력하세요...`
};

export const chatsending1 = /** @type {(inputs: {}) => string} */ () => {
	return `전송 중...`
};

export const chatsend1 = /** @type {(inputs: {}) => string} */ () => {
	return `전송`
};

export const chatsignintosend4 = /** @type {(inputs: {}) => string} */ () => {
	return `메시지를 전송하려면 로그인하세요.`
};

export const chatsendfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `메시지 전송 실패.`
};

export const chatunknownuser2 = /** @type {(inputs: {}) => string} */ () => {
	return `알 수 없는 사용자`
};

export const chatyou1 = /** @type {(inputs: {}) => string} */ () => {
	return `나`
};

export const chatpartner1 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅 상대`
};

export const chatsinglechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `1:1 채팅`
};

export const chatmyroomstitle3 = /** @type {(inputs: {}) => string} */ () => {
	return `내 대화`
};

export const chatmyroomsdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `참여한 채팅방이 시간순으로 표시됩니다.`
};

export const chatemptyrooms2 = /** @type {(inputs: {}) => string} */ () => {
	return `아직 참여한 채팅방이 없습니다.`
};

export const chatloadingrooms2 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅방 로딩 중...`
};

export const chatopenroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `대화 열기`
};

export const chatlastmessagelabel3 = /** @type {(inputs: {}) => string} */ () => {
	return `마지막 메시지`
};

export const chattabfriends2 = /** @type {(inputs: {}) => string} */ () => {
	return `친구`
};

export const chattabgroupchats3 = /** @type {(inputs: {}) => string} */ () => {
	return `그룹챗`
};

export const chattabopenchats3 = /** @type {(inputs: {}) => string} */ () => {
	return `오픈챗`
};

export const chattabbookmarks2 = /** @type {(inputs: {}) => string} */ () => {
	return `즐겨찾기`
};

export const chattabsearch2 = /** @type {(inputs: {}) => string} */ () => {
	return `검색`
};

export const chatcreateroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `방생성`
};

export const chatfindfriends2 = /** @type {(inputs: {}) => string} */ () => {
	return `친구 찾기`
};

export const chatcreategroupchat3 = /** @type {(inputs: {}) => string} */ () => {
	return `그룹챗 생성`
};

export const chatcreateopenchat3 = /** @type {(inputs: {}) => string} */ () => {
	return `오픈챗 생성`
};

export const boardtabfree2 = /** @type {(inputs: {}) => string} */ () => {
	return `자유토론`
};

export const boardtabqna2 = /** @type {(inputs: {}) => string} */ () => {
	return `질문답변`
};

export const boardtabmarket2 = /** @type {(inputs: {}) => string} */ () => {
	return `회원장터`
};

export const boardwritepost2 = /** @type {(inputs: {}) => string} */ () => {
	return `글쓰기`
};

export const sidebarupcomingtodos2 = /** @type {(inputs: {}) => string} */ () => {
	return `계획 중인 TODO`
};

export const sidebartodochatinvites3 = /** @type {(inputs: {}) => string} */ () => {
	return `채팅 초대 수락/거절`
};

export const sidebartodochatinvitesdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `초대 알림을 채팅 목록 상단에 노출하고 입장 여부를 확인할 수 있게 합니다.`
};

export const sidebartodoposttype3 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판 Post 타입 메시지`
};

export const sidebartodoposttypedesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `카테고리/제목/댓글을 갖춘 게시판형 메시지 입력 UI를 준비합니다.`
};

export const sidebartodoboardstats3 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판 통계 카드`
};

export const sidebartodoboardstatsdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `post·comment·like 카운터를 실시간으로 표시합니다.`
};

export const homesectionrecentusers3 = /** @type {(inputs: {}) => string} */ () => {
	return `최근 사용자`
};

export const homesectionrecentusersdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `방금 가입한 사용자 목록이 여기에 표시될 예정입니다.`
};

export const homesectionrecentopenchat4 = /** @type {(inputs: {}) => string} */ () => {
	return `최근 오픈 채팅 메시지`
};

export const homesectionrecentopenchatdesc5 = /** @type {(inputs: {}) => string} */ () => {
	return `실시간 오픈 채팅 메시지를 곧 확인할 수 있습니다.`
};

export const homesectionpopularopenroom4 = /** @type {(inputs: {}) => string} */ () => {
	return `인기 오픈 채팅방`
};

export const homesectionpopularopenroomdesc5 = /** @type {(inputs: {}) => string} */ () => {
	return `참여자가 많은 오픈 채팅방을 추천해 드립니다.`
};

export const homesectionrecentposts3 = /** @type {(inputs: {}) => string} */ () => {
	return `최근 글 & 댓글`
};

export const homesectionrecentpostsdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `게시판의 최신 글과 댓글을 한눈에 볼 수 있도록 준비 중입니다.`
};

export const homesectionrecentuserscount4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `최근 가입한 회원 ${i.count}명`
};

export const sidebardevhighlightattachment3 = /** @type {(inputs: {}) => string} */ () => {
	return `8. 첨부파일 업로드`
};

export const sidebardevhighlightpassword3 = /** @type {(inputs: {}) => string} */ () => {
	return `9. 채팅 비밀번호 기능`
};

export const sidebardevhighlightposttype4 = /** @type {(inputs: {}) => string} */ () => {
	return `10. Post 타입 메시지`
};

export const chataccept1 = /** @type {(inputs: {}) => string} */ () => {
	return `수락`
};

export const chatreject1 = /** @type {(inputs: {}) => string} */ () => {
	return `거절`
};

export const chatinvitefriend2 = /** @type {(inputs: {}) => string} */ () => {
	return `친구 초대`
};

export const chatinvitetoroom3 = /** @type {(inputs: {}) => string} */ () => {
	return `이 채팅방에 친구를 초대하세요`
};

export const chatsearchusertoinvite4 = /** @type {(inputs: {}) => string} */ () => {
	return `초대할 친구를 검색하세요`
};

export const chatinvitationsent2 = /** @type {(inputs: {}) => string} */ () => {
	return `초대를 보냈습니다`
};
export { chatdirectchat2 } from "./en.js"
export { loading } from "./en.js"
export { close } from "./en.js"
export { save } from "./en.js"
export { _delete } from "./en.js"
export { cancel } from "./en.js"
export { confirm } from "./en.js"
export { refresh } from "./en.js"
export { retry } from "./en.js"
export { info } from "./en.js"
export { status } from "./en.js"
export { success } from "./en.js"
export { error } from "./en.js"
export { complete } from "./en.js"
export { progress } from "./en.js"
export { go } from "./en.js"
export { user } from "./en.js"
export { home } from "./en.js"
export { about } from "./en.js"
export { products } from "./en.js"
export { contact } from "./en.js"
export { board } from "./en.js"
export { chat } from "./en.js"
export { findusers1 } from "./en.js"
export { signin1 } from "./en.js"
export { signout1 } from "./en.js"
export { menu } from "./en.js"
export { myprofile1 } from "./en.js"
export { profilenicknamelengthlimit3 } from "./en.js"
export { profilegendernotspecified3 } from "./en.js"
export { profilebirthdate1 } from "./en.js"
export { profilebirthdateyear2 } from "./en.js"
export { profilebirthdatemonth2 } from "./en.js"
export { profilebirthdateday2 } from "./en.js"
export { profileyearformat2 } from "./en.js"
export { profilemonthformat2 } from "./en.js"
export { profiledayformat2 } from "./en.js"
export { profilebirthdatefutureerror3 } from "./en.js"
export { profilebirthdateagelimit3 } from "./en.js"
export { profilephoto1 } from "./en.js"
export { profilephotouploadguide3 } from "./en.js"
export { profilephotouploadsuccess3 } from "./en.js"
export { profilephotouploadfailed3 } from "./en.js"
export { profilephotoremove2 } from "./en.js"
export { profilephotoremovesuccess3 } from "./en.js"
export { profilephotoremovefailed3 } from "./en.js"
export { profilephototypeerror3 } from "./en.js"
export { profilephotosizeerror3 } from "./en.js"
export { profilememberinfo2 } from "./en.js"
export { profilememberinfoguide3 } from "./en.js"
export { profilememberinfoeditguide4 } from "./en.js"
export { userjoineddate2 } from "./en.js"
export { usernoregistration2 } from "./en.js"
export { usernosignup2 } from "./en.js"
export { testusercreatebatchcount4 } from "./en.js"
export { testusercurrentcreatecount4 } from "./en.js"
export { testusernoneguide3 } from "./en.js"
export { testuserinfounrecoverable3 } from "./en.js"
export { underconstruction1 } from "./en.js"
export { underconstructionmessage2 } from "./en.js"
export { underconstructionbacktohome4 } from "./en.js"
export { boardunderconstruction2 } from "./en.js"
export { chatunderconstruction2 } from "./en.js"
export { sidebarlanguage1 } from "./en.js"
export { pagetitlesignin3 } from "./en.js"
export { pagemetasignin3 } from "./en.js"
export { profilephotouploadpreview3 } from "./en.js"
export { testusernodelete3 } from "./en.js"
export { testusercreateunit3 } from "./en.js"
export { authdescription1 } from "./en.js"
export { authsignin2 } from "./en.js"
export { profileuserinfo2 } from "./en.js"
export { profileuserinfoguide3 } from "./en.js"
export { usernousersregistered3 } from "./en.js"
export { usernosignedup3 } from "./en.js"
export { testusercreatedatonce4 } from "./en.js"
export { testuserdeletingprogress3 } from "./en.js"
export { testusernousersguide4 } from "./en.js"
export { testuseryear2 } from "./en.js"
export { constructionunderconstruction2 } from "./en.js"
export { featuresveltekit52 } from "./en.js"
export { featuretailwindcss4 } from "./en.js"
export { testuserdeleteconfirmation3 } from "./en.js"
export { testusernouserstodelete5 } from "./en.js"
export { testuserdeleteallconfirmation4 } from "./en.js"
export { testuserprogressindicator3 } from "./en.js"
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
