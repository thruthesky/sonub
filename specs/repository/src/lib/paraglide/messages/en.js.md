---
name: en.js
description: en 파일
version: 1.0.0
type: javascript
category: other
original_path: src/lib/paraglide/messages/en.js
---

# en.js

## 개요

**파일 경로**: `src/lib/paraglide/messages/en.js`
**파일 타입**: javascript
**카테고리**: other

en 파일

## 소스 코드

```javascript
/* eslint-disable */


export const helloworld1 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Hello, ${i.name} from en!`
};

export const commonloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading...`
};

export const commonclose1 = /** @type {(inputs: {}) => string} */ () => {
	return `Close`
};

export const commonsave1 = /** @type {(inputs: {}) => string} */ () => {
	return `Save`
};

export const commondelete1 = /** @type {(inputs: {}) => string} */ () => {
	return `Delete`
};

export const commoncancel1 = /** @type {(inputs: {}) => string} */ () => {
	return `Cancel`
};

export const commonconfirm1 = /** @type {(inputs: {}) => string} */ () => {
	return `Confirm`
};

export const commonrefresh1 = /** @type {(inputs: {}) => string} */ () => {
	return `Refresh`
};

export const commonretry1 = /** @type {(inputs: {}) => string} */ () => {
	return `Try Again`
};

export const commoninfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `Information`
};

export const commonstatus1 = /** @type {(inputs: {}) => string} */ () => {
	return `Status`
};

export const commonsuccess1 = /** @type {(inputs: {}) => string} */ () => {
	return `Success`
};

export const commonerror1 = /** @type {(inputs: {}) => string} */ () => {
	return `Error`
};

export const commoncomplete1 = /** @type {(inputs: {}) => string} */ () => {
	return `Complete`
};

export const commonprogress1 = /** @type {(inputs: {}) => string} */ () => {
	return `Progress`
};

export const commongo1 = /** @type {(inputs: {}) => string} */ () => {
	return `Go`
};

export const commonuser1 = /** @type {(inputs: {}) => string} */ () => {
	return `User`
};

export const navhome1 = /** @type {(inputs: {}) => string} */ () => {
	return `Home`
};

export const navabout1 = /** @type {(inputs: {}) => string} */ () => {
	return `About`
};

export const navproducts1 = /** @type {(inputs: {}) => string} */ () => {
	return `Products`
};

export const navcontact1 = /** @type {(inputs: {}) => string} */ () => {
	return `Contact`
};

export const navboard1 = /** @type {(inputs: {}) => string} */ () => {
	return `Board`
};

export const navchat1 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat`
};

export const navfindusers2 = /** @type {(inputs: {}) => string} */ () => {
	return `Find Users`
};

export const navlogin1 = /** @type {(inputs: {}) => string} */ () => {
	return `Login`
};

export const navlogout1 = /** @type {(inputs: {}) => string} */ () => {
	return `Logout`
};

export const navmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `Menu`
};

export const navmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `My Profile`
};

export const authwelcome1 = /** @type {(inputs: {}) => string} */ () => {
	return `Welcome`
};

export const authwelcomemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Welcome to Sonub`
};

export const authintro1 = /** @type {(inputs: {}) => string} */ () => {
	return `Adding community features to SedAi.Dev with Sonub spec.`
};

export const authgetstarted2 = /** @type {(inputs: {}) => string} */ () => {
	return `Get Started`
};

export const authsigninguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in with Google or Apple account`
};

export const authsigninguidestart4 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in with Google or Apple account to get started`
};

export const authsigninaction3 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign In`
};

export const authsigninwithgoogle4 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in with Google`
};

export const authsigninwithapple4 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in with Apple`
};

export const authsigningin2 = /** @type {(inputs: {}) => string} */ () => {
	return `Signing in...`
};

export const authsigningout2 = /** @type {(inputs: {}) => string} */ () => {
	return `Signing out...`
};

export const authsigninfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign In Failed`
};

export const authsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign In Required`
};

export const authsigninrequireddesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in to your account to access more features`
};

export const authwelcomeuser2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `Welcome, ${i.name}!`
};

export const profilenickname1 = /** @type {(inputs: {}) => string} */ () => {
	return `Nickname`
};

export const profilenicknameinput2 = /** @type {(inputs: {}) => string} */ () => {
	return `Enter your nickname`
};

export const profilenicknamerequired2 = /** @type {(inputs: {}) => string} */ () => {
	return `Please enter your nickname.`
};

export const profilenicknamelength2 = /** @type {(inputs: {}) => string} */ () => {
	return `Nickname must be 50 characters or less.`
};

export const profilenicknamemaxlength3 = /** @type {(inputs: {}) => string} */ () => {
	return `Max 50 characters`
};

export const profilegender1 = /** @type {(inputs: {}) => string} */ () => {
	return `Gender`
};

export const profilegendermale2 = /** @type {(inputs: {}) => string} */ () => {
	return `Male`
};

export const profilegenderfemale2 = /** @type {(inputs: {}) => string} */ () => {
	return `Female`
};

export const profilegendernoanswer3 = /** @type {(inputs: {}) => string} */ () => {
	return `Prefer not to say`
};

export const profiledateofbirth3 = /** @type {(inputs: {}) => string} */ () => {
	return `Date of Birth`
};

export const profileyear1 = /** @type {(inputs: {}) => string} */ () => {
	return `Year`
};

export const profilemonth1 = /** @type {(inputs: {}) => string} */ () => {
	return `Month`
};

export const profileday1 = /** @type {(inputs: {}) => string} */ () => {
	return `Day`
};

export const profileyearvalue2 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}`
};

export const profilemonthvalue2 = /** @type {(inputs: { month: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.month}`
};

export const profiledayvalue2 = /** @type {(inputs: { day: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.day}`
};

export const profiledateofbirthpasterror5 = /** @type {(inputs: {}) => string} */ () => {
	return `Date of birth must be in the past.`
};

export const profileagerestriction2 = /** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */ (i) => {
	return `Must be 18 years or older (${i.minYear} - ${i.maxYear})`
};

export const profilepicture1 = /** @type {(inputs: {}) => string} */ () => {
	return `Profile Picture`
};

export const profilepictureuploadguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `Click to upload profile picture (max 5MB)`
};

export const profilepictureuploadsuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `Profile picture uploaded successfully.`
};

export const profilepictureuploadfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to upload photo. Please try again.`
};

export const profilepictureremove2 = /** @type {(inputs: {}) => string} */ () => {
	return `Remove Photo`
};

export const profilepictureremovesuccess3 = /** @type {(inputs: {}) => string} */ () => {
	return `Profile picture removed successfully.`
};

export const profilepictureremovefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to remove photo. Please try again.`
};

export const profilepicturetypeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `Only image files can be uploaded.`
};

export const profilepicturesizeerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `File size must be 5MB or less.`
};

export const profilesave1 = /** @type {(inputs: {}) => string} */ () => {
	return `Save Profile`
};

export const profilesaving1 = /** @type {(inputs: {}) => string} */ () => {
	return `Saving...`
};

export const profilesavesuccess2 = /** @type {(inputs: {}) => string} */ () => {
	return `Profile updated successfully.`
};

export const profilesavefailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to save profile. Please try again.`
};

export const profileloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading profile...`
};

export const profileloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to load profile information.`
};

export const profileinfo1 = /** @type {(inputs: {}) => string} */ () => {
	return `Profile Information`
};

export const profileinfoguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `Set your nickname, gender, and date of birth`
};

export const profileinfoeditguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `You can modify your profile information`
};

export const userlist1 = /** @type {(inputs: {}) => string} */ () => {
	return `User List`
};

export const userlistguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `View all registered users in Firebase Realtime Database`
};

export const userjoindate2 = /** @type {(inputs: {}) => string} */ () => {
	return `Joined:`
};

export const userlastlogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `Last Login:`
};

export const usernoname2 = /** @type {(inputs: {}) => string} */ () => {
	return `No Name`
};

export const userloading1 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading users...`
};

export const usernotregistered2 = /** @type {(inputs: {}) => string} */ () => {
	return `No registered users`
};

export const usernotjoined2 = /** @type {(inputs: {}) => string} */ () => {
	return `No users have signed up yet.`
};

export const userloadfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to load user list`
};

export const userloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading more users...`
};

export const userallloaded2 = /** @type {(inputs: {}) => string} */ () => {
	return `All users loaded`
};

export const userunknownerror2 = /** @type {(inputs: {}) => string} */ () => {
	return `An unknown error occurred.`
};

export const userprofiledetail2 = /** @type {(inputs: {}) => string} */ () => {
	return `User Profile Details`
};

export const menutitle1 = /** @type {(inputs: {}) => string} */ () => {
	return `Menu`
};

export const menuguide1 = /** @type {(inputs: {}) => string} */ () => {
	return `Account & Settings`
};

export const menumyaccount2 = /** @type {(inputs: {}) => string} */ () => {
	return `My Account`
};

export const menueditprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `Edit Profile`
};

export const menuadminpage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Admin Page`
};

export const menudevtest2 = /** @type {(inputs: {}) => string} */ () => {
	return `Dev Test (DatabaseListView)`
};

export const admindashboard1 = /** @type {(inputs: {}) => string} */ () => {
	return `Admin Dashboard`
};

export const admindashboardguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `Select an admin tool to get started.`
};

export const admintestusermanagement3 = /** @type {(inputs: {}) => string} */ () => {
	return `Test User Management`
};

export const admintestusermanagementdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Manage temporary user creation, listing, and deletion on one page`
};

export const adminuserlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `User List`
};

export const adminuserlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `View created test user list`
};

export const adminreportlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `Report List`
};

export const adminreportlistdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `View and manage user reports`
};

export const admintest1 = /** @type {(inputs: {}) => string} */ () => {
	return `Test`
};

export const admintestdesc2 = /** @type {(inputs: {}) => string} */ () => {
	return `Use additional test features`
};

export const admininfopermissionnotimplemented4 = /** @type {(inputs: {}) => string} */ () => {
	return `• Admin permission verification is not yet implemented.`
};

export const admininfotestflag3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Test users are marked with \`isTemporary: true\` flag.`
};

export const admininfodatadelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Test data can be deleted at any time.`
};

export const admindashboardmenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `Dashboard`
};

export const adminuserlistmenu3 = /** @type {(inputs: {}) => string} */ () => {
	return `Users`
};

export const testuserlist2 = /** @type {(inputs: {}) => string} */ () => {
	return `User List`
};

export const testuserguide2 = /** @type {(inputs: {}) => string} */ () => {
	return `View and manage test temporary users.`
};

export const testusercount2 = /** @type {(inputs: {}) => string} */ () => {
	return `Test Users Count`
};

export const testusercreated2 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count} created`
};

export const testusernotcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `No users created yet`
};

export const testusercreate2 = /** @type {(inputs: {}) => string} */ () => {
	return `Create Test Users`
};

export const testusercreateicon3 = /** @type {(inputs: {}) => string} */ () => {
	return `🚀 Create Test Users`
};

export const testusercreateguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `Click the button to sequentially create 100 test temporary users and add them to the list.`
};

export const testusercreating2 = /** @type {(inputs: {}) => string} */ () => {
	return `⏳ Creating...`
};

export const testusercreatecomplete3 = /** @type {(inputs: {}) => string} */ () => {
	return `✓ Creation Complete`
};

export const testusercreatecompletemessage4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `✓ Complete: ${i.count} test users created.`
};

export const testusercreateatonce4 = /** @type {(inputs: {}) => string} */ () => {
	return `Created at once`
};

export const testusercurrentcreated3 = /** @type {(inputs: {}) => string} */ () => {
	return `Currently created`
};

export const testuserdeletinginprogress4 = /** @type {(inputs: {}) => string} */ () => {
	return `Deleting`
};

export const testuserdeleting2 = /** @type {(inputs: {}) => string} */ () => {
	return `Deleting...`
};

export const testuserdeleteall3 = /** @type {(inputs: {}) => string} */ () => {
	return `Delete All Test Users`
};

export const testusernotcreatedguide4 = /** @type {(inputs: {}) => string} */ () => {
	return `No test users have been created. Use the <strong><a class="text-blue-600" href="/admin/test/create-test-data">Create Test Data</a></strong> page to generate 100 test accounts.`
};

export const testusergender2 = /** @type {(inputs: {}) => string} */ () => {
	return `Gender`
};

export const testuserbirthyear3 = /** @type {(inputs: {}) => string} */ () => {
	return `Birth Year`
};

export const testusercreateddate3 = /** @type {(inputs: {}) => string} */ () => {
	return `Created`
};

export const testuserstatus2 = /** @type {(inputs: {}) => string} */ () => {
	return `Test User`
};

export const testuserinfodisplay3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Only users marked with \`isTemporary: true\` are displayed on this page.`
};

export const testuserinfodelete3 = /** @type {(inputs: {}) => string} */ () => {
	return `• Each user can be deleted individually or in bulk.`
};

export const testuserinfonorecover4 = /** @type {(inputs: {}) => string} */ () => {
	return `• Deleted users cannot be recovered.`
};

export const testuseryeardisplay3 = /** @type {(inputs: { year: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.year}`
};

export const constructiontitle1 = /** @type {(inputs: {}) => string} */ () => {
	return `Under Construction`
};

export const constructionmessage1 = /** @type {(inputs: {}) => string} */ () => {
	return `This page is currently under development.`
};

export const constructionbacktohome3 = /** @type {(inputs: {}) => string} */ () => {
	return `Back to Home`
};

export const boardconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `Board feature is currently under development.`
};

export const chatconstruction1 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat feature is currently under development.`
};

export const sidebarmenu1 = /** @type {(inputs: {}) => string} */ () => {
	return `Menu`
};

export const sidebarrecentactivity2 = /** @type {(inputs: {}) => string} */ () => {
	return `Recent Activity`
};

export const sidebarnorecentactivity3 = /** @type {(inputs: {}) => string} */ () => {
	return `No recent activity.`
};

export const sidebarselectlanguage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Language`
};

export const sidebarbuildversion2 = /** @type {(inputs: {}) => string} */ () => {
	return `Build Version`
};

export const sidebarmyprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `My Profile`
};

export const sidebarnotifications1 = /** @type {(inputs: {}) => string} */ () => {
	return `Notifications`
};

export const sidebarnonotifications2 = /** @type {(inputs: {}) => string} */ () => {
	return `No new notifications.`
};

export const sidebarsuggestions1 = /** @type {(inputs: {}) => string} */ () => {
	return `Suggestions`
};

export const sidebarpopularposts2 = /** @type {(inputs: {}) => string} */ () => {
	return `Popular Posts`
};

export const sidebarnewfeatures2 = /** @type {(inputs: {}) => string} */ () => {
	return `New Features`
};

export const featuresveltekit51 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit 5`
};

export const featuresveltekit5desc2 = /** @type {(inputs: {}) => string} */ () => {
	return `Modern framework using latest Svelte 5 runes`
};

export const featurefirebaseauth2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase Auth`
};

export const featurefirebaseauthdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `Google and Apple social login support`
};

export const featuretailwindcss2 = /** @type {(inputs: {}) => string} */ () => {
	return `TailwindCSS`
};

export const featuretailwindcssdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `Beautiful UI with shadcn-svelte`
};

export const linksveltekitdocs3 = /** @type {(inputs: {}) => string} */ () => {
	return `SvelteKit Docs`
};

export const linkfirebasedocs2 = /** @type {(inputs: {}) => string} */ () => {
	return `Firebase Docs`
};

export const linkshadcnsvelte2 = /** @type {(inputs: {}) => string} */ () => {
	return `shadcn-svelte`
};

export const pagetitlehome2 = /** @type {(inputs: {}) => string} */ () => {
	return `Sonub - Welcome`
};

export const pagetitlemenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `Menu - Sonub`
};

export const pagetitlelogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `Login - Sonub`
};

export const pagetitleuserlist3 = /** @type {(inputs: {}) => string} */ () => {
	return `User List - Sonub`
};

export const pagetitlemyprofile3 = /** @type {(inputs: {}) => string} */ () => {
	return `My Profile - Sonub`
};

export const pagetitleboard2 = /** @type {(inputs: {}) => string} */ () => {
	return `Board - Sonub`
};

export const pagetitlechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat - Sonub`
};

export const pagemetalogin2 = /** @type {(inputs: {}) => string} */ () => {
	return `Sign in to Sonub`
};

export const admintestmenu2 = /** @type {(inputs: {}) => string} */ () => {
	return `Test`
};

export const profilepicturepreview2 = /** @type {(inputs: {}) => string} */ () => {
	return `Upload preview`
};

export const testuserdeleteconfirm3 = /** @type {(inputs: {}) => string} */ () => {
	return `Do you want to delete this test user?`
};

export const testusernousertodelete5 = /** @type {(inputs: {}) => string} */ () => {
	return `No test users to delete.`
};

export const testuserdeleteallconfirm4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `Do you want to delete all ${i.count} test users?`
};

export const testuserdeleteerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `An error occurred while deleting the test user.`
};

export const testuserlistloaderror4 = /** @type {(inputs: {}) => string} */ () => {
	return `Error loading test user list:`
};

export const testusercreateerror3 = /** @type {(inputs: {}) => string} */ () => {
	return `Error creating test users:`
};

export const testuserdeleteallerror4 = /** @type {(inputs: {}) => string} */ () => {
	return `Error deleting all test users:`
};

export const testuserprogressdisplay3 = /** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.current} / ${i.total} (${i.percentage}%)`
};

export const testusergendermale3 = /** @type {(inputs: {}) => string} */ () => {
	return `Male`
};

export const testusergenderfemale3 = /** @type {(inputs: {}) => string} */ () => {
	return `Female`
};

export const testusergenderother3 = /** @type {(inputs: {}) => string} */ () => {
	return `Other`
};

export const testusercreatecount3 = /** @type {(inputs: {}) => string} */ () => {
	return `100`
};

export const reportreasonabuse2 = /** @type {(inputs: {}) => string} */ () => {
	return `Abuse and Harassment`
};

export const reportreasonfakenews3 = /** @type {(inputs: {}) => string} */ () => {
	return `Misinformation`
};

export const reportreasonspam2 = /** @type {(inputs: {}) => string} */ () => {
	return `Spam`
};

export const reportreasoninappropriate2 = /** @type {(inputs: {}) => string} */ () => {
	return `Inappropriate Content`
};

export const reportreasonother2 = /** @type {(inputs: {}) => string} */ () => {
	return `Other`
};

export const commonpost1 = /** @type {(inputs: {}) => string} */ () => {
	return `Post`
};

export const commoncomment1 = /** @type {(inputs: {}) => string} */ () => {
	return `Comment`
};

export const reportcancelconfirm2 = /** @type {(inputs: {}) => string} */ () => {
	return `Do you want to cancel this report?`
};

export const authloginrequired2 = /** @type {(inputs: {}) => string} */ () => {
	return `Login required`
};

export const reportmylist2 = /** @type {(inputs: {}) => string} */ () => {
	return `My Reports`
};

export const reportmylistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `View your submitted reports`
};

export const pagetitlemyreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `My Reports - Sonub`
};

export const adminreportlistguide3 = /** @type {(inputs: {}) => string} */ () => {
	return `View and manage all user reports`
};

export const pagetitleadminreports3 = /** @type {(inputs: {}) => string} */ () => {
	return `Admin Reports - Sonub`
};

export const chatchatroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat Room`
};

export const chatroom1 = /** @type {(inputs: {}) => string} */ () => {
	return `Room:`
};

export const chatoverview1 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat Overview`
};

export const chatsigninrequired3 = /** @type {(inputs: {}) => string} */ () => {
	return `Please sign in to start chatting.`
};

export const chatprovideuid2 = /** @type {(inputs: {}) => string} */ () => {
	return `Provide a uid query parameter to open a single chat.`
};

export const chatloadingprofile2 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading the participant profile...`
};

export const chatloadprofilefailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to load participant profile.`
};

export const chatchattingwith2 = /** @type {(inputs: { name: NonNullable<unknown> }) => string} */ (i) => {
	return `You are chatting with ${i.name}.`
};

export const chatroomready2 = /** @type {(inputs: { roomId: NonNullable<unknown> }) => string} */ (i) => {
	return `Room ID ${i.roomId} is ready.`
};

export const chatselectconversation2 = /** @type {(inputs: {}) => string} */ () => {
	return `Select a conversation to begin.`
};

export const chatroomnotready3 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat room is not ready.`
};

export const chatadduidorroomid5 = /** @type {(inputs: {}) => string} */ () => {
	return `Add ?uid=TARGET_UID or ?roomId=ROOM_KEY to the URL to open a conversation.`
};

export const chatloadingmessages2 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading messages...`
};

export const chatnomessages2 = /** @type {(inputs: {}) => string} */ () => {
	return `No messages yet. Say hello!`
};

export const chatloadmessagesfailed3 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to load messages.`
};

export const chatunknownerror2 = /** @type {(inputs: {}) => string} */ () => {
	return `Unknown error.`
};

export const chatloadingmore2 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading more...`
};

export const chatuptodate3 = /** @type {(inputs: {}) => string} */ () => {
	return `You are up to date.`
};

export const chatpreparingstream2 = /** @type {(inputs: {}) => string} */ () => {
	return `Preparing the message stream...`
};

export const chatwritemessage2 = /** @type {(inputs: {}) => string} */ () => {
	return `Write a message...`
};

export const chatsending1 = /** @type {(inputs: {}) => string} */ () => {
	return `Sending...`
};

export const chatsend1 = /** @type {(inputs: {}) => string} */ () => {
	return `Send`
};

export const chatsignintosend4 = /** @type {(inputs: {}) => string} */ () => {
	return `Please sign in to send messages.`
};

export const chatsendfailed2 = /** @type {(inputs: {}) => string} */ () => {
	return `Failed to send message.`
};

export const chatunknownuser2 = /** @type {(inputs: {}) => string} */ () => {
	return `Unknown user`
};

export const chatyou1 = /** @type {(inputs: {}) => string} */ () => {
	return `You`
};

export const chatpartner1 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat Partner`
};

export const chatsinglechat2 = /** @type {(inputs: {}) => string} */ () => {
	return `Single Chat`
};

export const chatmyroomstitle3 = /** @type {(inputs: {}) => string} */ () => {
	return `My conversations`
};

export const chatmyroomsdesc3 = /** @type {(inputs: {}) => string} */ () => {
	return `Chats you joined appear here in chronological order.`
};

export const chatemptyrooms2 = /** @type {(inputs: {}) => string} */ () => {
	return `You have not joined any chat rooms yet.`
};

export const chatloadingrooms2 = /** @type {(inputs: {}) => string} */ () => {
	return `Loading your chat rooms...`
};

export const chatopenroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `Open conversation`
};

export const chatlastmessagelabel3 = /** @type {(inputs: {}) => string} */ () => {
	return `Last message`
};

export const chattabfriends2 = /** @type {(inputs: {}) => string} */ () => {
	return `Friends`
};

export const chattabgroupchats3 = /** @type {(inputs: {}) => string} */ () => {
	return `Group Chats`
};

export const chattabopenchats3 = /** @type {(inputs: {}) => string} */ () => {
	return `Open Chats`
};

export const chattabbookmarks2 = /** @type {(inputs: {}) => string} */ () => {
	return `Favorites`
};

export const chattabsearch2 = /** @type {(inputs: {}) => string} */ () => {
	return `Search`
};

export const chatcreateroom2 = /** @type {(inputs: {}) => string} */ () => {
	return `Create Room`
};

export const chatfindfriends2 = /** @type {(inputs: {}) => string} */ () => {
	return `Find Friends`
};

export const chatcreategroupchat3 = /** @type {(inputs: {}) => string} */ () => {
	return `Create Group Chat`
};

export const chatcreateopenchat3 = /** @type {(inputs: {}) => string} */ () => {
	return `Create Open Chat`
};

export const boardtabfree2 = /** @type {(inputs: {}) => string} */ () => {
	return `Free Talk`
};

export const boardtabqna2 = /** @type {(inputs: {}) => string} */ () => {
	return `Q&A`
};

export const boardtabmarket2 = /** @type {(inputs: {}) => string} */ () => {
	return `Marketplace`
};

export const boardwritepost2 = /** @type {(inputs: {}) => string} */ () => {
	return `Write Post`
};

export const sidebarupcomingtodos2 = /** @type {(inputs: {}) => string} */ () => {
	return `Upcoming TODOs`
};

export const sidebartodochatinvites3 = /** @type {(inputs: {}) => string} */ () => {
	return `Chat invite accept/decline`
};

export const sidebartodochatinvitesdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Surface invite banners above the chat list and confirm before entering.`
};

export const sidebartodoposttype3 = /** @type {(inputs: {}) => string} */ () => {
	return `Board post-type messages`
};

export const sidebartodoposttypedesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Prepare the composer with category, title, and comment UI for board-style posts.`
};

export const sidebartodoboardstats3 = /** @type {(inputs: {}) => string} */ () => {
	return `Board stats cards`
};

export const sidebartodoboardstatsdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Show live post/comment/like counters for every board category.`
};

export const homesectionrecentusers3 = /** @type {(inputs: {}) => string} */ () => {
	return `Recent Users`
};

export const homesectionrecentusersdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `Newly joined members will appear here soon.`
};

export const homesectionrecentopenchat4 = /** @type {(inputs: {}) => string} */ () => {
	return `Recent Open Chat Messages`
};

export const homesectionrecentopenchatdesc5 = /** @type {(inputs: {}) => string} */ () => {
	return `Live open chat conversations will be streamed in this space.`
};

export const homesectionpopularopenroom4 = /** @type {(inputs: {}) => string} */ () => {
	return `Popular Open Chat Rooms`
};

export const homesectionpopularopenroomdesc5 = /** @type {(inputs: {}) => string} */ () => {
	return `We will highlight the most active open rooms for quick access.`
};

export const homesectionrecentposts3 = /** @type {(inputs: {}) => string} */ () => {
	return `Latest Posts & Comments`
};

export const homesectionrecentpostsdesc4 = /** @type {(inputs: {}) => string} */ () => {
	return `A snapshot of the board’s newest posts and replies is coming here.`
};

export const homesectionrecentuserscount4 = /** @type {(inputs: { count: NonNullable<unknown> }) => string} */ (i) => {
	return `${i.count} members just joined`
};

export const sidebardevhighlightattachment3 = /** @type {(inputs: {}) => string} */ () => {
	return `8. File attachments`
};

export const sidebardevhighlightpassword3 = /** @type {(inputs: {}) => string} */ () => {
	return `9. Chat password protection`
};

export const sidebardevhighlightposttype4 = /** @type {(inputs: {}) => string} */ () => {
	return `10. Post-type messages`
};
/** @type {(inputs: {}) => string} */
export const chataccept1 = () => 'chatAccept'
/** @type {(inputs: {}) => string} */
export const chatreject1 = () => 'chatReject'
/** @type {(inputs: {}) => string} */
export const chatinvitefriend2 = () => 'chatInviteFriend'
/** @type {(inputs: {}) => string} */
export const chatinvitetoroom3 = () => 'chatInviteToRoom'
/** @type {(inputs: {}) => string} */
export const chatsearchusertoinvite4 = () => 'chatSearchUserToInvite'
/** @type {(inputs: {}) => string} */
export const chatinvitationsent2 = () => 'chatInvitationSent'
/** @type {(inputs: {}) => string} */
export const chatdirectchat2 = () => 'chatDirectChat'
/** @type {(inputs: {}) => string} */
export const loading = () => 'loading'
/** @type {(inputs: {}) => string} */
export const close = () => 'close'
/** @type {(inputs: {}) => string} */
export const save = () => 'save'
/** @type {(inputs: {}) => string} */
export const _delete = () => 'delete'
/** @type {(inputs: {}) => string} */
export const cancel = () => 'cancel'
/** @type {(inputs: {}) => string} */
export const confirm = () => 'confirm'
/** @type {(inputs: {}) => string} */
export const refresh = () => 'refresh'
/** @type {(inputs: {}) => string} */
export const retry = () => 'retry'
/** @type {(inputs: {}) => string} */
export const info = () => 'info'
/** @type {(inputs: {}) => string} */
export const status = () => 'status'
/** @type {(inputs: {}) => string} */
export const success = () => 'success'
/** @type {(inputs: {}) => string} */
export const error = () => 'error'
/** @type {(inputs: {}) => string} */
export const complete = () => 'complete'
/** @type {(inputs: {}) => string} */
export const progress = () => 'progress'
/** @type {(inputs: {}) => string} */
export const go = () => 'go'
/** @type {(inputs: {}) => string} */
export const user = () => 'user'
/** @type {(inputs: {}) => string} */
export const home = () => 'home'
/** @type {(inputs: {}) => string} */
export const about = () => 'about'
/** @type {(inputs: {}) => string} */
export const products = () => 'products'
/** @type {(inputs: {}) => string} */
export const contact = () => 'contact'
/** @type {(inputs: {}) => string} */
export const board = () => 'board'
/** @type {(inputs: {}) => string} */
export const chat = () => 'chat'
/** @type {(inputs: {}) => string} */
export const findusers1 = () => 'findUsers'
/** @type {(inputs: {}) => string} */
export const signin1 = () => 'signIn'
/** @type {(inputs: {}) => string} */
export const signout1 = () => 'signOut'
/** @type {(inputs: {}) => string} */
export const menu = () => 'menu'
/** @type {(inputs: {}) => string} */
export const myprofile1 = () => 'myProfile'
/** @type {(inputs: {}) => string} */
export const profilenicknamelengthlimit3 = () => 'profileNicknameLengthLimit'
/** @type {(inputs: {}) => string} */
export const profilegendernotspecified3 = () => 'profileGenderNotSpecified'
/** @type {(inputs: {}) => string} */
export const profilebirthdate1 = () => 'profileBirthdate'
/** @type {(inputs: {}) => string} */
export const profilebirthdateyear2 = () => 'profileBirthdateYear'
/** @type {(inputs: {}) => string} */
export const profilebirthdatemonth2 = () => 'profileBirthdateMonth'
/** @type {(inputs: {}) => string} */
export const profilebirthdateday2 = () => 'profileBirthdateDay'
/** @type {(inputs: { year: NonNullable<unknown> }) => string} */
export const profileyearformat2 = () => 'profileYearFormat'
/** @type {(inputs: { month: NonNullable<unknown> }) => string} */
export const profilemonthformat2 = () => 'profileMonthFormat'
/** @type {(inputs: { day: NonNullable<unknown> }) => string} */
export const profiledayformat2 = () => 'profileDayFormat'
/** @type {(inputs: {}) => string} */
export const profilebirthdatefutureerror3 = () => 'profileBirthdateFutureError'
/** @type {(inputs: { minYear: NonNullable<unknown>, maxYear: NonNullable<unknown> }) => string} */
export const profilebirthdateagelimit3 = () => 'profileBirthdateAgeLimit'
/** @type {(inputs: {}) => string} */
export const profilephoto1 = () => 'profilePhoto'
/** @type {(inputs: {}) => string} */
export const profilephotouploadguide3 = () => 'profilePhotoUploadGuide'
/** @type {(inputs: {}) => string} */
export const profilephotouploadsuccess3 = () => 'profilePhotoUploadSuccess'
/** @type {(inputs: {}) => string} */
export const profilephotouploadfailed3 = () => 'profilePhotoUploadFailed'
/** @type {(inputs: {}) => string} */
export const profilephotoremove2 = () => 'profilePhotoRemove'
/** @type {(inputs: {}) => string} */
export const profilephotoremovesuccess3 = () => 'profilePhotoRemoveSuccess'
/** @type {(inputs: {}) => string} */
export const profilephotoremovefailed3 = () => 'profilePhotoRemoveFailed'
/** @type {(inputs: {}) => string} */
export const profilephototypeerror3 = () => 'profilePhotoTypeError'
/** @type {(inputs: {}) => string} */
export const profilephotosizeerror3 = () => 'profilePhotoSizeError'
/** @type {(inputs: {}) => string} */
export const profilememberinfo2 = () => 'profileMemberInfo'
/** @type {(inputs: {}) => string} */
export const profilememberinfoguide3 = () => 'profileMemberInfoGuide'
/** @type {(inputs: {}) => string} */
export const profilememberinfoeditguide4 = () => 'profileMemberInfoEditGuide'
/** @type {(inputs: {}) => string} */
export const userjoineddate2 = () => 'userJoinedDate'
/** @type {(inputs: {}) => string} */
export const usernoregistration2 = () => 'userNoRegistration'
/** @type {(inputs: {}) => string} */
export const usernosignup2 = () => 'userNoSignup'
/** @type {(inputs: {}) => string} */
export const testusercreatebatchcount4 = () => 'testUserCreateBatchCount'
/** @type {(inputs: {}) => string} */
export const testusercurrentcreatecount4 = () => 'testUserCurrentCreateCount'
/** @type {(inputs: {}) => string} */
export const testusernoneguide3 = () => 'testUserNoneGuide'
/** @type {(inputs: {}) => string} */
export const testuserinfounrecoverable3 = () => 'testUserInfoUnrecoverable'
/** @type {(inputs: {}) => string} */
export const underconstruction1 = () => 'underConstruction'
/** @type {(inputs: {}) => string} */
export const underconstructionmessage2 = () => 'underConstructionMessage'
/** @type {(inputs: {}) => string} */
export const underconstructionbacktohome4 = () => 'underConstructionBackToHome'
/** @type {(inputs: {}) => string} */
export const boardunderconstruction2 = () => 'boardUnderConstruction'
/** @type {(inputs: {}) => string} */
export const chatunderconstruction2 = () => 'chatUnderConstruction'
/** @type {(inputs: {}) => string} */
export const sidebarlanguage1 = () => 'sidebarLanguage'
/** @type {(inputs: {}) => string} */
export const pagetitlesignin3 = () => 'pageTitleSignIn'
/** @type {(inputs: {}) => string} */
export const pagemetasignin3 = () => 'pageMetaSignIn'
/** @type {(inputs: {}) => string} */
export const profilephotouploadpreview3 = () => 'profilePhotoUploadPreview'
/** @type {(inputs: {}) => string} */
export const testusernodelete3 = () => 'testUserNoDelete'
/** @type {(inputs: {}) => string} */
export const testusercreateunit3 = () => 'testUserCreateUnit'
/** @type {(inputs: {}) => string} */
export const authdescription1 = () => 'authDescription'
/** @type {(inputs: {}) => string} */
export const authsignin2 = () => 'authSignIn'
/** @type {(inputs: {}) => string} */
export const profileuserinfo2 = () => 'profileUserInfo'
/** @type {(inputs: {}) => string} */
export const profileuserinfoguide3 = () => 'profileUserInfoGuide'
/** @type {(inputs: {}) => string} */
export const usernousersregistered3 = () => 'userNoUsersRegistered'
/** @type {(inputs: {}) => string} */
export const usernosignedup3 = () => 'userNoSignedUp'
/** @type {(inputs: {}) => string} */
export const testusercreatedatonce4 = () => 'testUserCreatedAtOnce'
/** @type {(inputs: {}) => string} */
export const testuserdeletingprogress3 = () => 'testUserDeletingProgress'
/** @type {(inputs: {}) => string} */
export const testusernousersguide4 = () => 'testUserNoUsersGuide'
/** @type {(inputs: { year: NonNullable<unknown> }) => string} */
export const testuseryear2 = () => 'testUserYear'
/** @type {(inputs: {}) => string} */
export const constructionunderconstruction2 = () => 'constructionUnderConstruction'
/** @type {(inputs: {}) => string} */
export const featuresveltekit52 = () => 'featureSvelteKit5'
/** @type {(inputs: {}) => string} */
export const featuretailwindcss4 = () => 'featureTailwindCSS'
/** @type {(inputs: {}) => string} */
export const testuserdeleteconfirmation3 = () => 'testUserDeleteConfirmation'
/** @type {(inputs: {}) => string} */
export const testusernouserstodelete5 = () => 'testUserNoUsersToDelete'
/** @type {(inputs: { count: NonNullable<unknown> }) => string} */
export const testuserdeleteallconfirmation4 = () => 'testUserDeleteAllConfirmation'
/** @type {(inputs: { current: NonNullable<unknown>, total: NonNullable<unknown>, percentage: NonNullable<unknown> }) => string} */
export const testuserprogressindicator3 = () => 'testUserProgressIndicator'
```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
