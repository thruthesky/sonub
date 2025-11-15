---
title: zh.js
type: javascript
path: src/lib/paraglide/messages/zh.js
status: active
version: 1.0.0
last_updated: 2025-11-15
---

## 개요

이 파일은 `src/lib/paraglide/messages/zh.js`의 소스 코드를 포함하는 SED 스펙 문서입니다.

## 소스 코드

```javascript
/* eslint-disable */


export const helloworld1 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Hello, ${i.name} from zh!`
};

export const commonloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `加载中...`
};

export const commonclose1 = /** @type {(inputs: {}) => string} */ () => {
	return `关闭`
};

export const commonsave1 = /** @type {(inputs: {}) => string} */ () => {
	return `保存`
};

export const commondelete1 = /** @type {(inputs: {}) => string} */ () => {
	return `删除`
};

export const commoncancel1 = /** @type {(inputs: {}) => string} */ () => {
	return `取消`
};

export const commonconfirm1 = /** @type {(inputs: {}) => string} */ () => {
	return `确认`
};

export const commonrefresh1 = /** @type {(inputs: {}) => string} */ () => {
	return `刷新`
};

export const commonretry1 = /** @type {(inputs: {}) => string} */ () => {
	return `重试`
};

export const commoninfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `信息`
};

export const commonstatus1 = /** @type {(inputs: {}) => string} */ () => {
	return `状态`
};

export const commonsuccess1 = /** @type {(inputs: {}) => string} */ () => {
	return `成功`
};

export const commonerror1 = /** @type {(inputs: {}) => string} */ () => {
	return `错误`
};

export const commoncomplete1 = /** @type {(inputs: {}) => string} */ () => {
	return `完成`
};

export const commonprogress1 = /** @type {(inputs: {}) => string} */ () => {
	return `进度`
};

export const commongo1 = /** @type {(inputs: {}) => string} */ () => {
	return `前往`
};

export const commonuser1 = /** @type {(inputs: {}) => string} */ () => {
	return `用户`
};

export const navhome1 = /** @type {(inputs: {}) => string} */ () => {
	return `首页`
};

export const navabout1 = /** @type {(inputs: {}) => string} */ () => {
	return `介绍`
};

export const navproducts1 = /** @type {(inputs: {}) => string} */ () => {
	return `产品`
};

export const navcontact1 = /** @type {(inputs: {}) => string} */ () => {
	return `联系`
};

export const navboard1 = /** @type {(inputs: {}) => string} */ () => {
	return `论坛`
};

export const navchat1 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天`
};

export const navfindusers2 = /** @type {(inputs: {}) => string} */ () => {
	return `查找用户`
};

export const navlogin1 = /** @type {(inputs: {}) => string} */ () => {
	return `登录`
};

export const navlogout1 = /** @type {(inputs: {}) => string} */ () => {
	return `退出登录`
};

export const navmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `菜单`
};

export const navmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `我的资料`
};

export const authwelcome1 = /** @type {(inputs: {}) => string} */ () => {
	return `欢迎`
};

export const authwelcomemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `欢迎来到 Sonub`
};

export const authintro1 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Sonub spec 为 SedAi.Dev 添加社区功能。`
};

export const authgetstarted2 = /** @type {(inputs: {}) => string} */ () => {
	return `开始使用`
};

export const authsigninguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Google 或 Apple 账号登录`
};

export const authsigninguidestart4 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Google 或 Apple 账号登录以开始使用`
};

export const authsigninaction3 = /** @type {(inputs: {}) => string} */ () => {
	return `登录`
};

export const authsigninwithgoogle4 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Google 登录`
};

export const authsigninwithapple4 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Apple 登录`
};

export const authsigningin2 = /** @type {(inputs: {}) => string} */ () => {
	return `登录中...`
};

export const authsigningout2 = /** @type {(inputs: {}) => string} */ () => {
	return `退出登录中...`
};

export const authsigninfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `登录失败`
};

export const authsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `需要登录`
};

export const authsigninrequireddesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `登录您的账号以访问更多功能`
};

export const authwelcomeuser2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `欢迎，${i.name}！`
};

export const profilenickname1 = /** @type {(inputs: {}) => string} */ () => {
	return `昵称`
};

export const profilenicknameinput2 = /** @type {(inputs: {}) => string} */ () => {
	return `请输入您的昵称`
};

export const profilenicknamerequired2 = /** @type {(inputs: {}) => string} */ () => {
	return `请输入昵称。`
};
export { profilenicknamelength2 } from "./en.js"

export const profilenicknamemaxlength3 = /** @type {(inputs: {}) => string} */ () => {
	return `最多50个字符`
};

export const profilegender1 = /** @type {(inputs: {}) => string} */ () => {
	return `性别`
};

export const profilegendermale2 = /** @type {(inputs: {}) => string} */ () => {
	return `男`
};

export const profilegenderfemale2 = /** @type {(inputs: {}) => string} */ () => {
	return `女`
};
export { profilegendernoanswer3 } from "./en.js"

export const profiledateofbirth3 = /** @type {(inputs: {}) => string} */ () => {
	return `出生日期`
};
export { profileyear1 } from "./en.js"
export { profilemonth1 } from "./en.js"
export { profileday1 } from "./en.js"
export { profileyearvalue2 } from "./en.js"
export { profilemonthvalue2 } from "./en.js"
export { profiledayvalue2 } from "./en.js"
export { profiledateofbirthpasterror5 } from "./en.js"
export { profileagerestriction2 } from "./en.js"

export const profilepicture1 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料照片`
};
export { profilepictureuploadguide3 } from "./en.js"
export { profilepictureuploadsuccess3 } from "./en.js"
export { profilepictureuploadfailed3 } from "./en.js"
export { profilepictureremove2 } from "./en.js"
export { profilepictureremovesuccess3 } from "./en.js"
export { profilepictureremovefailed3 } from "./en.js"
export { profilepicturetypeerror3 } from "./en.js"
export { profilepicturesizeerror3 } from "./en.js"

export const profilesave1 = /** @type {(inputs: {}) => string} */ () => {
	return `保存资料`
};

export const profilesaving1 = /** @type {(inputs: {}) => string} */ () => {
	return `保存中...`
};

export const profilesavesuccess2 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料更新成功。`
};

export const profilesavefailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `保存个人资料失败。请重试。`
};

export const profileloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `加载个人资料...`
};

export const profileloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `加载个人资料信息失败。`
};
export { profileinfo1 } from "./en.js"
export { profileinfoguide2 } from "./en.js"
export { profileinfoeditguide3 } from "./en.js"

export const userlist1 = /** @type {(inputs: {}) => string} */ () => {
	return `用户列表`
};

export const userlistguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `查看 Firebase Realtime Database 中注册的所有用户`
};
export { userjoindate2 } from "./en.js"

export const userlastlogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `最后登录：`
};

export const usernoname2 = /** @type {(inputs: {}) => string} */ () => {
	return `无名称`
};

export const userloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `加载用户列表...`
};
export { usernotregistered2 } from "./en.js"
export { usernotjoined2 } from "./en.js"

export const userloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `加载用户列表失败`
};

export const userloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `加载更多用户...`
};

export const userallloaded2 = /** @type {(inputs: {}) => string} */ () => {
	return `已加载所有用户`
};

export const userunknownerror2 = /** @type {(inputs: {}) => string} */ () => {
	return `发生未知错误。`
};

export const userprofiledetail2 = /** @type {(inputs: {}) => string} */ () => {
	return `用户资料详情`
};

export const menutitle1 = /** @type {(inputs: {}) => string} */ () => {
	return `菜单`
};

export const menuguide1 = /** @type {(inputs: {}) => string} */ () => {
	return `账号与设置`
};

export const menumyaccount2 = /** @type {(inputs: {}) => string} */ () => {
	return `我的账号`
};

export const menueditprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `编辑资料`
};

export const menuadminpage2 = /** @type {(inputs: {}) => string} */ () => {
	return `管理员页面`
};

export const menudevtest2 = /** @type {(inputs: {}) => string} */ () => {
	return `开发测试 (DatabaseListView)`
};

export const admindashboard1 = /** @type {(inputs: {}) => string} */ () => {
	return `管理员控制台`
};

export const admindashboardguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `选择管理工具以开始。`
};

export const admintestusermanagement3 = /** @type {(inputs: {}) => string} */ () => {
	return `测试用户管理`
};

export const admintestusermanagementdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `在一个页面上管理临时用户创建、列表和删除`
};

export const adminuserlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `用户列表`
};

export const adminuserlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `查看已创建的测试用户列表`
};

export const adminreportlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `管理员举报列表`
};

export const adminreportlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `查看和管理用户举报`
};

export const admintest1 = /** @type {(inputs: {}) => string} */ () => {
	return `测试`
};

export const admintestdesc2 = /** @type {(inputs: {}) => string} */ () => {
	return `使用其他测试功能`
};

export const admininfopermissionnotimplemented4 = /** @type {(inputs: {}) => string} */ () => {
	return `• 管理员权限验证尚未实施。`
};

export const admininfotestflag3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 测试用户标记为 \`isTemporary: true\` 标志。`
};

export const admininfodatadelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 测试数据可以随时删除。`
};

export const admindashboardmenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `控制台`
};

export const adminuserlistmenu3 = /** @type {(inputs: {}) => string} */ () => {
	return `用户`
};

export const testuserlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `用户列表`
};

export const testuserguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `查看和管理测试临时用户。`
};

export const testusercount2 = /** @type {(inputs: {}) => string} */ () => {
	return `测试用户数`
};

export const testusercreated2 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `已创建 ${i.count} 个`
};

export const testusernotcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `尚未创建用户`
};

export const testusercreate2 = /** @type {(inputs: {}) => string} */ () => {
	return `创建测试用户`
};

export const testusercreateicon3 = /** @type {(inputs: {}) => string} */ () => {
	return `🚀 创建测试用户`
};

export const testusercreateguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `点击按钮以顺序创建100个测试临时用户并将其添加到列表中。`
};

export const testusercreating2 = /** @type {(inputs: {}) => string} */ () => {
	return `⏳ 创建中...`
};

export const testusercreatecomplete3 = /** @type {(inputs: {}) => string} */ () => {
	return `✓ 创建完成`
};

export const testusercreatecompletemessage4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `✓ 完成：已创建 ${i.count} 个测试用户。`
};

export const testusercreateatonce4 = /** @type {(inputs: {}) => string} */ () => {
	return `一次创建的数量`
};

export const testusercurrentcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `当前已创建`
};

export const testuserdeletinginprogress4 = /** @type {(inputs: {}) => string} */ () => {
	return `删除中`
};

export const testuserdeleting2 = /** @type {(inputs: {}) => string} */ () => {
	return `删除中...`
};

export const testuserdeleteall3 = /** @type {(inputs: {}) => string} */ () => {
	return `删除所有测试用户`
};

export const testusernotcreatedguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `尚未创建测试用户。请前往 <strong><a class="text-blue-600" href="/admin/test/create-test-data">测试数据创建</a></strong> 页面生成 100 个测试账户。`
};

export const testusergender2 = /** @type {(inputs: {}) => string} */ () => {
	return `性别`
};

export const testuserbirthyear3 = /** @type {(inputs: {}) => string} */ () => {
	return `出生年份`
};

export const testusercreateddate3 = /** @type {(inputs: {}) => string} */ () => {
	return `创建日期`
};

export const testuserstatus2 = /** @type {(inputs: {}) => string} */ () => {
	return `测试用户`
};

export const testuserinfodisplay3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 此页面仅显示标记为 \`isTemporary: true\` 的用户。`
};

export const testuserinfodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 每个用户可以单独或批量删除。`
};
export { testuserinfonorecover4 } from "./en.js"

export const testuseryeardisplay3 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}年`
};
export { constructiontitle1 } from "./en.js"
export { constructionmessage1 } from "./en.js"
export { constructionbacktohome3 } from "./en.js"
export { boardconstruction1 } from "./en.js"

export const chatconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天功能目前正在开发中。`
};

export const sidebarmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `菜单`
};

export const sidebarrecentactivity2 = /** @type {(inputs: {}) => string} */ () => {
	return `最近活动`
};

export const sidebarnorecentactivity3 = /** @type {(inputs: {}) => string} */ () => {
	return `没有最近的活动。`
};

export const sidebarselectlanguage2 = /** @type {(inputs: {}) => string} */ () => {
	return `语言`
};

export const sidebarbuildversion2 = /** @type {(inputs: {}) => string} */ () => {
	return `构建版本`
};

export const sidebarmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `我的资料`
};

export const sidebarnotifications1 = /** @type {(inputs: {}) => string} */ () => {
	return `通知`
};

export const sidebarnonotifications2 = /** @type {(inputs: {}) => string} */ () => {
	return `没有新通知。`
};

export const sidebarsuggestions1 = /** @type {(inputs: {}) => string} */ () => {
	return `建议`
};

export const sidebarpopularposts2 = /** @type {(inputs: {}) => string} */ () => {
	return `热门帖子`
};

export const sidebarnewfeatures2 = /** @type {(inputs: {}) => string} */ () => {
	return `新功能`
};

export const featuresveltekit51 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 5`
};

export const featuresveltekit5desc2 = /** @type {(inputs: {}) => string} */ () => {
	return `使用最新 Svelte 5 runes 的现代框架`
};

export const featurefirebaseauth2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase Auth`
};

export const featurefirebaseauthdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `支持 Google 和 Apple 社交登录`
};

export const featuretailwindcss2 = /** @type {(inputs: {}) => string} */ () => {
	return `TailwindCSS`
};

export const featuretailwindcssdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `与 shadcn-svelte 一起的漂亮 UI`
};

export const linksveltekitdocs3 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 文档`
};

export const linkfirebasedocs2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase 文档`
};

export const linkshadcnsvelte2 = /** @type {(inputs: {}) => string} */ () => {
	return `shadcn-svelte`
};

export const pagetitlehome2 = /** @type {(inputs: {}) => string} */ () => {
	return `Sonub - Welcome`
};

export const pagetitlemenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `菜单 - Sonub`
};

export const pagetitlelogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `登录 - Sonub`
};

export const pagetitleuserlist3 = /** @type {(inputs: {}) => string} */ () => {
	return `用户列表 - Sonub`
};

export const pagetitlemyprofile3 = /** @type {(inputs: {}) => string} */ () => {
	return `我的资料 - Sonub`
};

export const pagetitleboard2 = /** @type {(inputs: {}) => string} */ () => {
	return `论坛 - Sonub`
};

export const pagetitlechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天 - Sonub`
};
export { pagemetalogin2 } from "./en.js"

export const admintestmenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `测试`
};
export { profilepicturepreview2 } from "./en.js"

export const testuserdeleteconfirm3 = /** @type {(inputs: {}) => string} */ () => {
	return `您要删除此测试用户吗？`
};
export { testusernousertodelete5 } from "./en.js"

export const testuserdeleteallconfirm4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `您要删除所有 ${i.count} 个测试用户吗？`
};

export const testuserdeleteerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `删除测试用户时发生错误。`
};

export const testuserlistloaderror4 = /** @type {(inputs: {}) => string} */ () => {
	return `加载测试用户列表时出错：`
};

export const testusercreateerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `创建测试用户时出错：`
};

export const testuserdeleteallerror4 = /** @type {(inputs: {}) => string} */ () => {
	return `删除所有测试用户时出错：`
};

export const testuserprogressdisplay3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

export const testusergendermale3 = /** @type {(inputs: {}) => string} */ () => {
	return `男`
};

export const testusergenderfemale3 = /** @type {(inputs: {}) => string} */ () => {
	return `女`
};

export const testusergenderother3 = /** @type {(inputs: {}) => string} */ () => {
	return `其他`
};
export { testusercreatecount3 } from "./en.js"

export const reportreasonabuse2 = /** @type {(inputs: {}) => string} */ () => {
	return `辱骂和诽谤`
};

export const reportreasonfakenews3 = /** @type {(inputs: {}) => string} */ () => {
	return `虚假信息`
};

export const reportreasonspam2 = /** @type {(inputs: {}) => string} */ () => {
	return `垃圾信息`
};

export const reportreasoninappropriate2 = /** @type {(inputs: {}) => string} */ () => {
	return `不适当内容`
};

export const reportreasonother2 = /** @type {(inputs: {}) => string} */ () => {
	return `其他`
};

export const commonpost1 = /** @type {(inputs: {}) => string} */ () => {
	return `帖子`
};

export const commoncomment1 = /** @type {(inputs: {}) => string} */ () => {
	return `评论`
};

export const reportcancelconfirm2 = /** @type {(inputs: {}) => string} */ () => {
	return `您要取消此举报吗？`
};

export const authloginrequired2 = /** @type {(inputs: {}) => string} */ () => {
	return `需要登录`
};

export const reportmylist2 = /** @type {(inputs: {}) => string} */ () => {
	return `我的举报`
};

export const reportmylistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `查看您提交的举报`
};

export const pagetitlemyreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `我的举报 - Sonub`
};

export const adminreportlistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `查看所有用户的举报`
};

export const pagetitleadminreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `管理员举报列表 - Sonub`
};

export const chatchatroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天室`
};

export const chatroom1 = /** @type {(inputs: {}) => string} */ () => {
	return `房间:`
};

export const chatoverview1 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天概览`
};

export const chatsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `请登录以开始聊天。`
};

export const chatprovideuid2 = /** @type {(inputs: {}) => string} */ () => {
	return `请提供uid查询参数以打开单聊。`
};

export const chatloadingprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `正在加载参与者资料...`
};

export const chatloadprofilefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `加载参与者资料失败。`
};

export const chatchattingwith2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `您正在与${i.name}聊天。`
};

export const chatroomready2 = /** @type {(inputs: { roomId: NonNullable<unknown> }) => string} */ (i) => {
	return `房间ID ${i.roomId}已准备就绪。`
};

export const chatselectconversation2 = /** @type {(inputs: {}) => string} */ () => {
	return `选择对话以开始。`
};

export const chatroomnotready3 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天室未准备就绪。`
};

export const chatadduidorroomid5 = /** @type {(inputs: {}) => string} */ () => {
	return `在URL中添加?uid=TARGET_UID或?roomId=ROOM_KEY以打开对话。`
};

export const chatloadingmessages2 = /** @type {(inputs: {}) => string} */ () => {
	return `正在加载消息...`
};

export const chatnomessages2 = /** @type {(inputs: {}) => string} */ () => {
	return `还没有消息。打个招呼吧!`
};

export const chatloadmessagesfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `加载消息失败。`
};

export const chatunknownerror2 = /** @type {(inputs: {}) => string} */ () => {
	return `未知错误。`
};

export const chatloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `正在加载更多...`
};

export const chatuptodate3 = /** @type {(inputs: {}) => string} */ () => {
	return `您已是最新状态。`
};

export const chatpreparingstream2 = /** @type {(inputs: {}) => string} */ () => {
	return `正在准备消息流...`
};

export const chatwritemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `输入消息...`
};

export const chatsending1 = /** @type {(inputs: {}) => string} */ () => {
	return `发送中...`
};

export const chatsend1 = /** @type {(inputs: {}) => string} */ () => {
	return `发送`
};

export const chatsignintosend4 = /** @type {(inputs: {}) => string} */ () => {
	return `请登录以发送消息。`
};

export const chatsendfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `发送消息失败。`
};

export const chatunknownuser2 = /** @type {(inputs: {}) => string} */ () => {
	return `未知用户`
};

export const chatyou1 = /** @type {(inputs: {}) => string} */ () => {
	return `您`
};

export const chatpartner1 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天对象`
};

export const chatsinglechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `单聊`
};

export const chatmyroomstitle3 = /** @type {(inputs: {}) => string} */ () => {
	return `我的对话`
};

export const chatmyroomsdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `您加入的聊天室按时间顺序显示。`
};

export const chatemptyrooms2 = /** @type {(inputs: {}) => string} */ () => {
	return `您还没有加入任何聊天室。`
};

export const chatloadingrooms2 = /** @type {(inputs: {}) => string} */ () => {
	return `正在加载聊天室...`
};

export const chatopenroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `打开对话`
};

export const chatlastmessagelabel3 = /** @type {(inputs: {}) => string} */ () => {
	return `最后消息`
};

export const chattabfriends2 = /** @type {(inputs: {}) => string} */ () => {
	return `好友`
};

export const chattabgroupchats3 = /** @type {(inputs: {}) => string} */ () => {
	return `群聊`
};

export const chattabopenchats3 = /** @type {(inputs: {}) => string} */ () => {
	return `开放聊天`
};

export const chattabbookmarks2 = /** @type {(inputs: {}) => string} */ () => {
	return `收藏`
};

export const chattabsearch2 = /** @type {(inputs: {}) => string} */ () => {
	return `搜索`
};

export const chatcreateroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `创建房间`
};

export const chatfindfriends2 = /** @type {(inputs: {}) => string} */ () => {
	return `查找好友`
};

export const chatcreategroupchat3 = /** @type {(inputs: {}) => string} */ () => {
	return `创建群聊`
};

export const chatcreateopenchat3 = /** @type {(inputs: {}) => string} */ () => {
	return `创建开放聊天`
};

export const boardtabfree2 = /** @type {(inputs: {}) => string} */ () => {
	return `自由讨论`
};

export const boardtabqna2 = /** @type {(inputs: {}) => string} */ () => {
	return `问答`
};

export const boardtabmarket2 = /** @type {(inputs: {}) => string} */ () => {
	return `会员集市`
};

export const boardwritepost2 = /** @type {(inputs: {}) => string} */ () => {
	return `写帖子`
};

export const sidebarupcomingtodos2 = /** @type {(inputs: {}) => string} */ () => {
	return `计划中的 TODO`
};

export const sidebartodochatinvites3 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天邀请处理`
};

export const sidebartodochatinvitesdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `在聊天列表顶部显示邀请并在入场前询问是否加入。`
};

export const sidebartodoposttype3 = /** @type {(inputs: {}) => string} */ () => {
	return `论坛帖子类型消息`
};

export const sidebartodoposttypedesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `准备包含分类、标题和评论 UI 的帖子式发送组件。`
};

export const sidebartodoboardstats3 = /** @type {(inputs: {}) => string} */ () => {
	return `论坛统计卡片`
};

export const sidebartodoboardstatsdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `实时展示帖子/评论/点赞的计数。`
};

export const homesectionrecentusers3 = /** @type {(inputs: {}) => string} */ () => {
	return `最近用户`
};

export const homesectionrecentusersdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `最新加入的成员将很快在这里显示。`
};

export const homesectionrecentopenchat4 = /** @type {(inputs: {}) => string} */ () => {
	return `最新开放聊天室消息`
};

export const homesectionrecentopenchatdesc5 = /** @type {(inputs: {}) => string} */ () => {
	return `实时开放聊天室对话即将展示在此区域。`
};

export const homesectionrecentopenchatempty5 = /** @type {(inputs: {}) => string} */ () => {
	return `最近没有开放聊天室消息。`
};

export const homesectionrecentopenchatlogin5 = /** @type {(inputs: {}) => string} */ () => {
	return `登录后即可查看你参与的开放聊天室最新消息。`
};

export const homesectionpopularopenroom4 = /** @type {(inputs: {}) => string} */ () => {
	return `热门开放聊天室`
};

export const homesectionpopularopenroomdesc5 = /** @type {(inputs: {}) => string} */ () => {
	return `我们会推荐参与度最高的开放聊天室。`
};

export const homesectionrecentposts3 = /** @type {(inputs: {}) => string} */ () => {
	return `最新帖子与评论`
};

export const homesectionrecentpostsdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `稍后将在此展示论坛最新的帖子和评论概览。`
};
export { homesectionrecentuserscount4 } from "./en.js"

export const homeopenchatnomessage4 = /** @type {(inputs: {}) => string} */ () => {
	return `暂无消息。`
};

export const sidebardevhighlightattachment3 = /** @type {(inputs: {}) => string} */ () => {
	return `9. 文件附件上传`
};

export const sidebardevhighlightpassword3 = /** @type {(inputs: {}) => string} */ () => {
	return `10. 聊天密码功能`
};

export const sidebardevhighlightposttype4 = /** @type {(inputs: {}) => string} */ () => {
	return `11. 帖子类型消息`
};

export const sidebardevhighlightwrapup4 = /** @type {(inputs: {}) => string} */ () => {
	return `8. 聊天室收尾功能`
};

export const chatnomoremessages3 = /** @type {(inputs: {}) => string} */ () => {
	return `没有更多消息。`
};

export const chatroomlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `我的聊天`
};

export const chatroomlistempty3 = /** @type {(inputs: {}) => string} */ () => {
	return `尚未加入任何聊天室。`
};

export const chatroomlistloading3 = /** @type {(inputs: {}) => string} */ () => {
	return `正在加载聊天室...`
};

export const chatpasswordsaving2 = /** @type {(inputs: {}) => string} */ () => {
	return `保存中...`
};

export const chatpasswordenabletoggle3 = /** @type {(inputs: {}) => string} */ () => {
	return `启用密码`
};

export const chatpasswordinputplaceholder3 = /** @type {(inputs: {}) => string} */ () => {
	return `密码（至少4个字符）`
};

export const chatpasswordconfirmplaceholder3 = /** @type {(inputs: {}) => string} */ () => {
	return `确认密码`
};

export const chatpasswordminlengtherror4 = /** @type {(inputs: {}) => string} */ () => {
	return `密码必须至少4个字符`
};

export const chatpasswordmismatcherror3 = /** @type {(inputs: {}) => string} */ () => {
	return `密码不匹配`
};

export const chatpasswordsetsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `密码已设置`
};

export const chatpasswordremovesuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `密码已移除`
};

export const chatpasswordsavefailure3 = /** @type {(inputs: {}) => string} */ () => {
	return `保存密码失败`
};

export const chatpasswordsettings2 = /** @type {(inputs: {}) => string} */ () => {
	return `密码设置`
};

export const chatpasswordrequired2 = /** @type {(inputs: {}) => string} */ () => {
	return `此聊天室需要密码。`
};

export const chatpasswordenterprompt3 = /** @type {(inputs: {}) => string} */ () => {
	return `请输入密码`
};

export const chatpasswordverifying2 = /** @type {(inputs: { countdown: NonNullable<unknown> }) => string} */ (i) => {
	return `验证中...（剩余${i.countdown}秒）`
};

export const chatpasswordverifysuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `密码已确认`
};

export const chatpasswordincorrect2 = /** @type {(inputs: {}) => string} */ () => {
	return `密码不正确`
};

export const chatpasswordverifyfailure3 = /** @type {(inputs: {}) => string} */ () => {
	return `密码验证失败`
};

export const chatpassworddelete2 = /** @type {(inputs: {}) => string} */ () => {
	return `删除密码`
};

export const chatpassworddeletesuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `密码已删除`
};
export { chataccept1 } from "./en.js"
export { chatreject1 } from "./en.js"
export { chatinvitefriend2 } from "./en.js"
export { chatinvitetoroom3 } from "./en.js"
export { chatsearchusertoinvite4 } from "./en.js"
export { chatinvitationsent2 } from "./en.js"
export { chatdirectchat2 } from "./en.js"

export const loading = /** @type {(inputs: {}) => string} */ () => {
	return `加载中...`
};

export const close = /** @type {(inputs: {}) => string} */ () => {
	return `关闭`
};

export const save = /** @type {(inputs: {}) => string} */ () => {
	return `保存`
};

export const _delete = /** @type {(inputs: {}) => string} */ () => {
	return `删除`
};

export const cancel = /** @type {(inputs: {}) => string} */ () => {
	return `取消`
};

export const confirm = /** @type {(inputs: {}) => string} */ () => {
	return `确认`
};

export const refresh = /** @type {(inputs: {}) => string} */ () => {
	return `刷新`
};

export const retry = /** @type {(inputs: {}) => string} */ () => {
	return `重试`
};

export const info = /** @type {(inputs: {}) => string} */ () => {
	return `信息`
};

export const status = /** @type {(inputs: {}) => string} */ () => {
	return `状态`
};

export const success = /** @type {(inputs: {}) => string} */ () => {
	return `成功`
};

export const error = /** @type {(inputs: {}) => string} */ () => {
	return `错误`
};

export const complete = /** @type {(inputs: {}) => string} */ () => {
	return `完成`
};

export const progress = /** @type {(inputs: {}) => string} */ () => {
	return `进度`
};

export const go = /** @type {(inputs: {}) => string} */ () => {
	return `前往`
};

export const user = /** @type {(inputs: {}) => string} */ () => {
	return `用户`
};

export const home = /** @type {(inputs: {}) => string} */ () => {
	return `首页`
};

export const about = /** @type {(inputs: {}) => string} */ () => {
	return `介绍`
};

export const products = /** @type {(inputs: {}) => string} */ () => {
	return `产品`
};

export const contact = /** @type {(inputs: {}) => string} */ () => {
	return `联系`
};

export const board = /** @type {(inputs: {}) => string} */ () => {
	return `论坛`
};

export const chat = /** @type {(inputs: {}) => string} */ () => {
	return `聊天`
};

export const findusers1 = /** @type {(inputs: {}) => string} */ () => {
	return `查找用户`
};

export const signin1 = /** @type {(inputs: {}) => string} */ () => {
	return `登录`
};

export const signout1 = /** @type {(inputs: {}) => string} */ () => {
	return `退出登录`
};

export const menu = /** @type {(inputs: {}) => string} */ () => {
	return `菜单`
};

export const myprofile1 = /** @type {(inputs: {}) => string} */ () => {
	return `我的资料`
};

export const profilenicknamelengthlimit3 = /** @type {(inputs: {}) => string} */ () => {
	return `昵称必须在50个字符以内。`
};

export const profilegendernotspecified3 = /** @type {(inputs: {}) => string} */ () => {
	return `不选择`
};

export const profilebirthdate1 = /** @type {(inputs: {}) => string} */ () => {
	return `出生日期`
};

export const profilebirthdateyear2 = /** @type {(inputs: {}) => string} */ () => {
	return `年`
};

export const profilebirthdatemonth2 = /** @type {(inputs: {}) => string} */ () => {
	return `月`
};

export const profilebirthdateday2 = /** @type {(inputs: {}) => string} */ () => {
	return `日`
};

export const profileyearformat2 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}年`
};

export const profilemonthformat2 = /** @type {(inputs: { month: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.month}月`
};

export const profiledayformat2 = /** @type {(inputs: { day: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.day}日`
};

export const profilebirthdatefutureerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `出生日期必须是过去的日期。`
};

export const profilebirthdateagelimit3 = /** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */ (i) => {
	return `必须年满18岁（${i.minYear}年 - ${i.maxYear}年）`
};

export const profilephoto1 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料照片`
};

export const profilephotouploadguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `点击上传个人资料照片（最大5MB）`
};

export const profilephotouploadsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料照片上传成功。`
};

export const profilephotouploadfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `照片上传失败。请重试。`
};

export const profilephotoremove2 = /** @type {(inputs: {}) => string} */ () => {
	return `删除照片`
};

export const profilephotoremovesuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料照片已删除。`
};

export const profilephotoremovefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `照片删除失败。请重试。`
};

export const profilephototypeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `只能上传图片文件。`
};

export const profilephotosizeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `文件大小必须在5MB以内。`
};

export const profilememberinfo2 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料信息`
};

export const profilememberinfoguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `设置您的昵称、性别和出生日期`
};

export const profilememberinfoeditguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `您可以修改您的个人资料信息`
};

export const userjoineddate2 = /** @type {(inputs: {}) => string} */ () => {
	return `注册日期：`
};

export const usernoregistration2 = /** @type {(inputs: {}) => string} */ () => {
	return `没有注册用户`
};

export const usernosignup2 = /** @type {(inputs: {}) => string} */ () => {
	return `还没有用户注册。`
};

export const testusercreatebatchcount4 = /** @type {(inputs: {}) => string} */ () => {
	return `一次创建的数量`
};

export const testusercurrentcreatecount4 = /** @type {(inputs: {}) => string} */ () => {
	return `当前已创建`
};

export const testusernoneguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `尚未创建测试用户。请前往 <strong><a class="text-blue-600" href="/admin/test/create-test-data">测试数据创建</a></strong> 页面生成 100 个测试账户。`
};

export const testuserinfounrecoverable3 = /** @type {(inputs: {}) => string} */ () => {
	return `• 已删除的用户无法恢复。`
};

export const underconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `施工中`
};

export const underconstructionmessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `此页面目前正在开发中。`
};

export const underconstructionbacktohome4 = /** @type {(inputs: {}) => string} */ () => {
	return `返回首页`
};

export const boardunderconstruction2 = /** @type {(inputs: {}) => string} */ () => {
	return `论坛功能目前正在开发中。`
};

export const chatunderconstruction2 = /** @type {(inputs: {}) => string} */ () => {
	return `聊天功能目前正在开发中。`
};

export const sidebarlanguage1 = /** @type {(inputs: {}) => string} */ () => {
	return `语言`
};

export const pagetitlesignin3 = /** @type {(inputs: {}) => string} */ () => {
	return `登录 - Sonub`
};

export const pagemetasignin3 = /** @type {(inputs: {}) => string} */ () => {
	return `登录到 Sonub`
};

export const profilephotouploadpreview3 = /** @type {(inputs: {}) => string} */ () => {
	return `上传预览`
};

export const testusernodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `没有要删除的测试用户。`
};

export const testusercreateunit3 = /** @type {(inputs: {}) => string} */ () => {
	return `100`
};

export const authdescription1 = /** @type {(inputs: {}) => string} */ () => {
	return `使用 Sonub spec 为 SedAi.Dev 添加社区功能。`
};

export const authsignin2 = /** @type {(inputs: {}) => string} */ () => {
	return `登录`
};

export const profileuserinfo2 = /** @type {(inputs: {}) => string} */ () => {
	return `个人资料信息`
};

export const profileuserinfoguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `设置您的昵称、性别和出生日期`
};

export const usernousersregistered3 = /** @type {(inputs: {}) => string} */ () => {
	return `没有注册用户`
};

export const usernosignedup3 = /** @type {(inputs: {}) => string} */ () => {
	return `还没有用户注册。`
};

export const testusercreatedatonce4 = /** @type {(inputs: {}) => string} */ () => {
	return `一次创建的数量`
};

export const testuserdeletingprogress3 = /** @type {(inputs: {}) => string} */ () => {
	return `删除中`
};

export const testusernousersguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `尚未创建测试用户。您可以使用上面的<strong>创建测试用户</strong>功能来创建100个。`
};

export const testuseryear2 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}年`
};

export const constructionunderconstruction2 = /** @type {(inputs: {}) => string} */ () => {
	return `施工中`
};

export const featuresveltekit52 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 5`
};

export const featuretailwindcss4 = /** @type {(inputs: {}) => string} */ () => {
	return `TailwindCSS`
};

export const testuserdeleteconfirmation3 = /** @type {(inputs: {}) => string} */ () => {
	return `您要删除此测试用户吗？`
};

export const testusernouserstodelete5 = /** @type {(inputs: {}) => string} */ () => {
	return `没有要删除的测试用户。`
};

export const testuserdeleteallconfirmation4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `您要删除所有 ${i.count} 个测试用户吗？`
};

export const testuserprogressindicator3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};
```

## 변경 이력

- 2025-11-15: 스펙 문서 생성
