# Requirements Document

## Introduction

This feature adds a "My Friends" section at the top of the user list page (page/user/list.php) that displays up to 5 of the logged-in user's friends. The section provides quick access to friends' profiles and enhances the user experience by showing their social connections prominently.

## Glossary

- **User List Page**: The page located at page/user/list.php that displays all registered users with infinite scroll
- **My Friends Section**: A new UI component that displays the logged-in user's friends at the top of the User List Page
- **Friend**: A user who has an accepted friendship relationship with the logged-in user
- **Friend API**: The get_friend_ids() function from lib/friend-and-feed/friend-and-feed.functions.php that returns friend IDs
- **Vue App**: The Vue.js application that manages the User List Page state and rendering
- **Hydration Data**: Server-side data passed to the Vue App for initial rendering

## Requirements

### Requirement 1

**User Story:** As a logged-in user, I want to see my friends at the top of the user list page, so that I can quickly access their profiles

#### Acceptance Criteria

1. WHEN THE User List Page loads, THE Vue App SHALL display a "My Friends" section above the main user list
2. THE My Friends Section SHALL display a maximum of 5 friends
3. WHEN the logged-in user has no friends, THE My Friends Section SHALL display a message indicating no friends exist
4. WHEN the user is not logged in, THE My Friends Section SHALL not be displayed
5. THE My Friends Section SHALL use the same card layout as the main user list for visual consistency

### Requirement 2

**User Story:** As a logged-in user, I want to see my friends' profile information in the friends section, so that I can identify them easily

#### Acceptance Criteria

1. THE My Friends Section SHALL display each friend's profile photo or default avatar
2. THE My Friends Section SHALL display each friend's display name
3. THE My Friends Section SHALL display each friend's join date in YYYY-MM-DD format
4. WHEN a user clicks on a friend's profile photo or name, THE Vue App SHALL navigate to that friend's profile page
5. THE My Friends Section SHALL NOT display friend request buttons since these users are already friends

### Requirement 3

**User Story:** As a logged-in user, I want the friends section to load efficiently, so that the page loads quickly

#### Acceptance Criteria

1. THE User List Page SHALL fetch friend IDs using the get_friend_ids() API function during server-side rendering
2. THE User List Page SHALL fetch detailed friend information using the existing list_users() function with friend IDs
3. THE Vue App SHALL receive friend data through Hydration Data to avoid additional client-side API calls on initial load
4. THE My Friends Section SHALL limit the query to 5 friends maximum to optimize performance
5. THE User List Page SHALL handle API errors gracefully without breaking the page

### Requirement 4

**User Story:** As a user viewing the page in different languages, I want the friends section labels to appear in my selected language, so that I can understand the interface

#### Acceptance Criteria

1. THE My Friends Section SHALL display a section title using the translation system
2. THE My Friends Section SHALL display the "no friends" message using the translation system
3. THE User List Page SHALL inject translations for Korean, English, Japanese, and Chinese languages
4. THE translations SHALL follow the existing naming convention using underscores (e.g., '내_친구_목록')
5. THE User List Page SHALL use the t() function to access translated strings
