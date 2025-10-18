# Implementation Plan

- [x] 1. Add server-side friend data fetching logic
  - Add PHP code at the top of page/user/list.php to fetch friend IDs and details
  - Implement try-catch error handling for graceful degradation
  - Limit friend results to 5 users maximum
  - Add myFriends data to the hydration object
  - _Requirements: 1.4, 3.1, 3.2, 3.4, 3.5_

- [x] 2. Add My Friends section to Vue.js template
  - Insert new HTML section after the page title and before the main user list
  - Implement conditional rendering with v-if for logged-in users with friends
  - Reuse existing user card layout for visual consistency
  - Add friend badge instead of friend request button
  - Add horizontal divider between My Friends and All Users sections
  - _Requirements: 1.1, 1.2, 1.3, 1.5, 2.1, 2.2, 2.3, 2.4, 2.5_

- [x] 3. Update Vue.js data model
  - Add myFriends property to Vue data object
  - Initialize myFriends with hydration data from server
  - Ensure formatDate method works for friend cards
  - _Requirements: 3.3_

- [x] 4. Add translation strings
  - Add '내_친구_목록' translation key to inject_list_language function
  - Provide translations for Korean, English, Japanese, and Chinese
  - Follow existing translation naming conventions
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
