Sonub (sonub.com) Website Development Guidelines and Rules
- This document specifies the guidelines and rules that must be followed when developing the Sonub website.
- All developers must familiarize themselves with and comply with this document.
- The Standard Workflow must be followed without exception.



# Standard Workflow
- [ ] **MANDATORY DOCUMENTATION REVIEW**: When a developer requests any development work, the AI MUST:
  - [ ] **IMMEDIATELY search and identify relevant documentation in docs/**/*.md**
  - [ ] **PRESENT the list of relevant documentation files to the developer BEFORE starting any work**
  - [ ] **READ and ANALYZE these documents to understand existing patterns, conventions, and requirements**
  - [ ] **REFERENCE these documents while developing to ensure consistency**
  - [ ] The AI is STRICTLY PROHIBITED from starting development without first reviewing and presenting relevant documentation
- [ ] Issue Creation: Before starting any work, create an issue that follows git commit message conventions.
- [ ] All documentation content and source code comments must be written in English.
  - [ ] Comments in PHP, JavaScript, CSS, and all other source code must be written in English.
  - [ ] Comments in code examples within markdown documents must also be written in English.
- [ ] When designing, work must be done using Bootstrap.
  - [ ] Both Dark Mode and Light Mode must be supported, with Dark Mode taking priority during development.
  - [ ] Do not use custom colors, color HEX codes, or color word codes. Always use Bootstrap's default colors or Bootstrap's default color variables to support both Dark Mode and Light Mode.
- [ ] Develop using jQuery.
  - jQuery is already installed and loaded as deferred in the head section.
  - Use jQuery directly without installing or importing it.
- [ ] Develop using Alpine.js.
  - Alpine.js is located at `/js/alpinejs-3.15.0.min.js` and already loaded in head as deferred.
  - Use Alpine.js directly without installing or importing it.
- [ ] Documentation (*.md) and Table of Contents Management
  - [ ] When editing any *.md file or docs/**/*.md file, a Table of Contents (ToC) must be added at the top of the document.
  - [ ] The ToC must be updated whenever the document structure changes (adding, removing, or renaming sections).
  - [ ] The ToC should reflect all main headings (##) and subheadings (###) in the document.
  - [ ] Keep the ToC synchronized with the actual content at all times.
- [ ] PHP Unit Testing Guidelines
  - [ ] All PHP unit tests must be written in pure PHP without any external testing frameworks.
  - [ ] Test files should be stored in the `tests` directory following the same structure as the source code.
  - [ ] Test file names must end with `.test.php` (e.g., `db.test.php` for testing `db.php`).
  - [ ] Each test file should be independently executable with direct PHP command: `php tests/db/db.test.php`
  - [ ] Use simple assertions and clear output messages to indicate test results.