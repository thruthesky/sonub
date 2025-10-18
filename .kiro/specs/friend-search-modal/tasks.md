# Implementation Plan

- [x] 1. Add search button to user list page
  - Add "친구 검색" button at the top of the page, above the user list
  - Use Bootstrap primary button styling with search icon
  - Wire button click event to Vue.js method
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 2. Create Bootstrap modal structure
  - [x] 2.1 Add modal HTML to page template
    - Create modal with id "friendSearchModal"
    - Add modal header with title and close button
    - Add modal body container for search UI
    - Use Bootstrap modal-lg size for better layout
    - _Requirements: 2.1, 2.2, 2.5_
  
  - [x] 2.2 Add search input and button to modal
    - Create input group with text input field
    - Add search button with loading state
    - Bind input to Vue.js data property with v-model
    - Add Enter key handler for search trigger
    - _Requirements: 2.3, 2.4_

- [x] 3. Implement Vue.js search functionality
  - [x] 3.1 Add search-related data properties
    - Add searchModalOpen, searchTerm, searchResults properties
    - Add searchPage, searchTotalPages, searchTotal properties
    - Add searchLoading and searchPerformed flags
    - Add modalInstance for Bootstrap Modal reference
    - _Requirements: 2.1, 3.1_
  
  - [x] 3.2 Implement modal control methods
    - Create openSearchModal() method to show modal
    - Create closeSearchModal() method to hide modal
    - Initialize Bootstrap Modal instance in mounted() hook
    - _Requirements: 2.1_
  
  - [x] 3.3 Implement search API call
    - Create performSearch() method with input validation
    - Create loadSearchResults() method to call list_users API
    - Pass name parameter for display_name filtering
    - Set per_page to 10 for search results
    - Handle API errors with try-catch and user alerts
    - _Requirements: 3.1, 3.2, 3.5_

- [x] 4. Display search results in modal
  - [x] 4.1 Create search results grid
    - Use same card layout as main user list (col-6)
    - Display profile photo or default avatar
    - Display display_name and created_at date
    - Make cards clickable links to profile pages
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 6.1, 6.2, 6.3_
  
  - [x] 4.2 Add no results message
    - Show "검색 결과가 없습니다" when searchResults is empty
    - Only show after search has been performed
    - Use Bootstrap alert-info styling
    - _Requirements: 3.5_

- [x] 5. Implement pagination for search results
  - [x] 5.1 Add pagination bar HTML
    - Create Bootstrap pagination component
    - Add first/previous/next/last navigation buttons
    - Add page number buttons (dynamic range)
    - Show pagination only when searchTotalPages > 1
    - _Requirements: 4.1, 4.2, 4.5_
  
  - [x] 5.2 Implement pagination logic
    - Create visiblePageNumbers computed property
    - Calculate page range (5 pages centered on current)
    - Create goToSearchPage() method to load specific page
    - Highlight current page with active class
    - Disable first/previous when on page 1
    - Disable next/last when on last page
    - _Requirements: 4.2, 4.3, 4.4_

- [x] 6. Add translation keys
  - Add Korean, English, Japanese, Chinese translations
  - Include keys: 친구_검색, 이름을_입력하세요, 검색, 검색_중
  - Include keys: 검색_결과가_없습니다, 검색어를_입력해주세요, 검색에_실패했습니다
  - Update inject_list_language() function
  - _Requirements: All_

- [ ] 7. Test search functionality
  - [ ] 7.1 Test modal behavior
    - Verify modal opens and closes correctly
    - Test close button, backdrop click, ESC key
    - _Requirements: 2.1, 2.5_
  
  - [ ] 7.2 Test search operations
    - Test search with valid terms
    - Test search with empty input (should show alert)
    - Test search with no results
    - Test search with Korean, English, numbers
    - _Requirements: 3.1, 3.2, 3.5_
  
  - [ ] 7.3 Test pagination
    - Test with result sets > 10 users
    - Test page navigation (first, previous, next, last)
    - Test page number clicks
    - Verify current page highlighting
    - _Requirements: 4.1, 4.2, 4.3, 4.4_
  
  - [ ] 7.4 Test responsive design
    - Test modal on mobile devices
    - Verify 2-column card layout on mobile
    - Test pagination readability on small screens
    - _Requirements: 5.5_
