Sonub Coding Guideline
=======================

## General Coding Standards
- Use 4 spaces for indentation, no tabs.
- Use UTF-8 encoding without BOM.
- Write comments and documentation for complex logic.

## Framework and Library Storage Guidelines

### Complete Framework Packages
- When downloading complete framework files (e.g., Bootstrap with all its components):
  - Store in: `etc/frameworks/<framework-name>/<framework-name>-x.x.x/`
  - Example: `etc/frameworks/bootstrap/bootstrap-5.3.0/`
  - This structure maintains version control and allows multiple versions to coexist.

### Single JavaScript Libraries
- For single JavaScript file libraries (e.g., jQuery):
  - Store directly in: `/js/`
  - Example: `/js/jquery-3.7.1.min.js`
  - Use versioned filenames to track library versions.
