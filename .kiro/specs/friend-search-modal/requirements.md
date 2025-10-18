# Requirements Document

## Introduction

사용자 목록 페이지에 친구 검색 기능을 추가합니다. 사용자는 버튼을 클릭하여 모달 다이얼로그를 열고, 이름으로 친구를 검색할 수 있습니다. 검색 결과는 페이지네이션과 함께 표시됩니다.

## Glossary

- **User List Page**: 사용자 목록을 표시하는 페이지 (page/user/list.php)
- **Friend Search Modal**: Bootstrap 모달을 사용한 친구 검색 다이얼로그
- **Search Input**: 사용자가 검색어를 입력하는 텍스트 입력 필드
- **Search Results**: display_name으로 필터링된 사용자 목록
- **Pagination Bar**: 검색 결과를 10개 단위로 나누어 표시하는 페이지네이션 컨트롤
- **Vue.js App**: 사용자 목록 페이지의 Vue.js 애플리케이션
- **display_name**: users 테이블의 사용자 표시 이름 컬럼

## Requirements

### Requirement 1

**User Story:** 사용자로서, 사용자 목록 페이지에서 친구 검색 버튼을 볼 수 있어야 합니다.

#### Acceptance Criteria

1. WHEN THE User List Page loads, THE User List Page SHALL display a "친구 검색" button at the top of the page
2. THE "친구 검색" button SHALL be positioned above the user list grid
3. THE "친구 검색" button SHALL use Bootstrap styling for consistent appearance

### Requirement 2

**User Story:** 사용자로서, 친구 검색 버튼을 클릭하면 검색 모달이 열려야 합니다.

#### Acceptance Criteria

1. WHEN THE user clicks the "친구 검색" button, THE Vue.js App SHALL open the Friend Search Modal
2. THE Friend Search Modal SHALL use Bootstrap modal component
3. THE Friend Search Modal SHALL contain a Search Input field for entering a name
4. THE Friend Search Modal SHALL contain a search button to trigger the search
5. THE Friend Search Modal SHALL display a close button to dismiss the modal

### Requirement 3

**User Story:** 사용자로서, 모달에서 이름을 입력하고 검색하면 일치하는 사용자 목록을 볼 수 있어야 합니다.

#### Acceptance Criteria

1. WHEN THE user enters a search term in the Search Input and clicks the search button, THE Vue.js App SHALL call the search API with the search term
2. THE search API SHALL filter users WHERE the display_name column contains the search term
3. THE Vue.js App SHALL display the Search Results in the modal
4. THE Search Results SHALL show a maximum of 10 users per page
5. IF no users match the search term, THEN THE Vue.js App SHALL display a "검색 결과가 없습니다" message

### Requirement 4

**User Story:** 사용자로서, 검색 결과가 10개를 초과하면 페이지네이션을 사용하여 다른 결과를 볼 수 있어야 합니다.

#### Acceptance Criteria

1. WHEN THE Search Results contain more than 10 users, THE Vue.js App SHALL display a Pagination Bar below the results
2. THE Pagination Bar SHALL show page numbers for navigating between result pages
3. WHEN THE user clicks a page number, THE Vue.js App SHALL load and display the corresponding page of Search Results
4. THE Pagination Bar SHALL highlight the current page number
5. THE Pagination Bar SHALL use Bootstrap pagination component for styling

### Requirement 5

**User Story:** 사용자로서, 검색 결과에서 각 사용자의 기본 정보를 볼 수 있어야 합니다.

#### Acceptance Criteria

1. THE Search Results SHALL display each user's profile photo or default avatar
2. THE Search Results SHALL display each user's display_name
3. THE Search Results SHALL display each user's registration date
4. THE Search Results SHALL use a card layout similar to the main user list
5. THE Search Results SHALL be responsive and work on mobile devices

### Requirement 6

**User Story:** 사용자로서, 검색 결과에서 사용자를 클릭하면 해당 사용자의 프로필 페이지로 이동할 수 있어야 합니다.

#### Acceptance Criteria

1. WHEN THE user clicks on a user card in the Search Results, THE Vue.js App SHALL navigate to that user's profile page
2. THE profile photo and display_name SHALL be clickable links
3. THE links SHALL use the same href pattern as the main user list
