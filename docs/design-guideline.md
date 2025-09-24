Design Guideline for Sonub Website Development
================================================

## Table of Contents
- [Overview](#overview)
- [Design Philosophy](#design-philosophy)
- [Color Guidelines](#color-guidelines)
- [Bootstrap Usage](#bootstrap-usage)

## Overview
- This document specifies the design guidelines and rules for developing the Sonub website.
- All developers must familiarize themselves with and comply with this document.

## Design Philosophy

### Light Mode Only
- **Important**: Sonub website supports **Light Mode only**
- **Never** implement Dark Mode features or Dark Mode-specific styles
- All design decisions should be optimized for Light Mode appearance

## Color Guidelines

### Use Bootstrap Colors
- **Always** use Bootstrap's default color classes and variables
- **Preferred** Bootstrap color utilities:
  - Background: `bg-primary`, `bg-secondary`, `bg-success`, `bg-danger`, `bg-warning`, `bg-info`, `bg-light`, `bg-dark`, `bg-white`
  - Text: `text-primary`, `text-secondary`, `text-success`, `text-danger`, `text-warning`, `text-info`, `text-light`, `text-dark`, `text-white`, `text-muted`
  - Borders: `border-primary`, `border-secondary`, etc.

### Avoid Custom Colors
- **Do not** use custom HEX color codes (e.g., `#FF5733`)
- **Do not** use CSS color names outside Bootstrap's palette (e.g., `color: red`)
- **Exception**: Only use custom colors when absolutely necessary for branding requirements

## Bootstrap Usage

### Component Guidelines
- Use Bootstrap components as-is without heavy customization
- Rely on Bootstrap's default styling for consistency
- Use Bootstrap utility classes for spacing, sizing, and layout

### Examples of Correct Usage
```html
<!-- Good: Using Bootstrap color classes -->
<div class="bg-light text-dark p-3">
  <h1 class="text-primary">Welcome</h1>
  <button class="btn btn-success">Submit</button>
</div>

<!-- Bad: Using custom colors -->
<div style="background-color: #f0f0f0; color: #333;">
  <h1 style="color: blue;">Welcome</h1>
  <button style="background: green;">Submit</button>
</div>
```

### Responsive Design
- Always use Bootstrap's responsive grid system
- Test layouts on different screen sizes
- Use Bootstrap's responsive utility classes