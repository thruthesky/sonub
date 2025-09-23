Sonub (sonub.com) Website Development Guidelines and Rules
- This document specifies the guidelines and rules that must be followed when developing the Sonub website.
- All developers must familiarize themselves with and comply with this document.
- The Standard Workflow must be followed without exception.



# Standard Workflow
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