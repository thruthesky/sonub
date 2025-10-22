# Bootstrap 5 Guidelines

## Table of Contents

- [Overview](#overview)
- [Bootstrap Utility CSS Class Usage](#bootstrap-utility-css-class-usage)
  - [Inline Design (Recommended)](#inline-design-recommended)
  - [Separate CSS File Separation](#separate-css-file-separation)
- [Page-specific CSS File Management](#page-specific-css-file-management)
  - [Page Files (page/\*\*/\*.php)](#page-files-pagephp)
  - [Widget Files (widgets/\*\*/\*.php)](#widget-files-widgetsphp)
- [Bootstrap Utility Class Guide](#bootstrap-utility-class-guide)
  - [Layout](#layout)
  - [Colors](#colors)
  - [Typography](#typography)
  - [Spacing](#spacing)
  - [Sizing](#sizing)
- [Real Examples](#real-examples)
  - [Example 1: User Card (Bootstrap Utility Only)](#example-1-user-card-bootstrap-utility-only)
  - [Example 2: Post List (Bootstrap Utility Only)](#example-2-post-list-bootstrap-utility-only)
  - [Example 3: Profile Header (Some CSS File Use)](#example-3-profile-header-some-css-file-use)
- [Anti-patterns](#anti-patterns)
  - [❌ Anti-pattern Example 1: Unnecessary CSS File Creation](#-anti-pattern-example-1-unnecessary-css-file-creation)
  - [❌ Anti-pattern Example 2: Writing Layout with CSS](#-anti-pattern-example-2-writing-layout-with-css)
  - [❌ Anti-pattern Example 3: Writing Colors and Spacing with CSS](#-anti-pattern-example-3-writing-colors-and-spacing-with-css)
- [Color Guidelines](#color-guidelines)
  - [Using Bootstrap Colors](#using-bootstrap-colors)
  - [Prohibition of Custom Colors](#prohibition-of-custom-colors)
- [Bootstrap Component Usage](#bootstrap-component-usage)
  - [Component Guidelines](#component-guidelines)
  - [Correct Usage Examples](#correct-usage-examples)
  - [Responsive Design](#responsive-design)
- [Summary](#summary)
  - [Core Principles](#core-principles)
  - [References](#references)

---

## Overview

The Sonub project **prioritizes the use of Bootstrap 5 Utility CSS classes**.

**Core Principles:**
- ✅ **Specify Bootstrap Utility classes inline in the `class=...` attribute**
- ✅ **Write all styles including layout, colors, spacing, and size using Utility classes**
- ✅ **Minimize separate CSS files** (use only when impossible to express with Bootstrap)

---

## Bootstrap Utility CSS Class Usage

### Inline Design (Recommended)

**🔥🔥🔥 Strongest Rule: In all possible cases, specify Bootstrap Utility CSS classes inline in the class="..." attribute 🔥🔥🔥**

Bootstrap Utility classes cover most design elements including layout, colors, spacing, size, and typography.

**✅ Recommended Example:**

```html
<!-- ✅ Complete design with Bootstrap Utility classes -->
<div class="container py-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <h5 class="card-title mb-3 text-primary">User Profile</h5>
                    <div class="d-flex align-items-center mb-2">
                        <img src="..." class="rounded-circle me-2" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-dark">John Doe</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">2024-01-15</p>
                        </div>
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-person-plus me-1"></i>
                            Add Friend
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

**Advantages:**
- ✅ Adheres to Bootstrap component conventions
- ✅ No separate CSS files needed (easier maintenance)
- ✅ Easy implementation of responsive design (Bootstrap-based classes)
- ✅ Maintains consistent design system (col-md-6, d-none d-md-block, etc.)

### Separate CSS File Separation

Separate **only styles that cannot be expressed with Bootstrap** into separate CSS files.

**Cases requiring separation:**
- ⚠ Animations not in Bootstrap
- ⚠ Complex gradient backgrounds
- ⚠ Special CSS transitions
- ⚠ Complex hover effects

**Example:**

```css
/* page/user/profile.css - Only styles impossible with Bootstrap */

/* Special animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-card-enter {
    animation: fadeIn 0.3s ease-out;
}

/* Complex gradient */
.profile-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

---

## Page-specific CSS File Management

### Page Files (page/**/*.php)

When separating CSS in page files, **create *.css file in the same folder** and auto-load with `load_page_css()` function.

**File structure:**
```
page/user/
├── profile.php        # Page file
└── profile.css        # CSS file (create only when needed)
```

**page/user/profile.php:**
```php
<!-- Write most design with Bootstrap Utility classes -->
<div class="container py-4">
    <div class="card shadow-sm profile-card-enter">
        <div class="card-body p-4">
            <div class="profile-header-gradient text-white p-3 rounded mb-3">
                <h3 class="mb-0"><?= htmlspecialchars($user->displayFullName()) ?></h3>
            </div>
            <!-- Other content -->
        </div>
    </div>
</div>

<?php
// Auto-load CSS file (automatically adds <link> tag if profile.css exists)
load_page_css();
?>
```

**page/user/profile.css:**
```css
/* Only styles impossible to express with Bootstrap */
.profile-card-enter {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.profile-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

**Note**: The `load_page_css()` function automatically finds and loads CSS files in the same path as the page file.

### Widget Files (widgets/**/*.php)

Widget files write CSS inside `<style>` tags.

**widgets/user/profile-card.php:**
```php
<div class="card shadow-sm profile-widget">
    <div class="card-body p-3">
        <!-- Design with Bootstrap Utility classes -->
        <div class="d-flex align-items-center">
            <img src="..." class="rounded-circle me-2" style="width: 40px; height: 40px;">
            <h6 class="mb-0">John Doe</h6>
        </div>
    </div>
</div>

<style>
/* Only styles impossible to express with Bootstrap */
.profile-widget:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
</style>
```

---

## Bootstrap Utility Class Guide

### Layout

**Container:**
```html
<div class="container">Fixed width</div>
<div class="container-fluid">Full width</div>
```

**Grid:**
```html
<div class="row">
    <div class="col-md-6">50% (medium screen and up)</div>
    <div class="col-md-6">50% (medium screen and up)</div>
</div>

<div class="row g-3">Apply spacing 3</div>
```

**Flexbox:**
```html
<div class="d-flex justify-content-between align-items-center">
    <span>Left</span>
    <span>Right</span>
</div>

<div class="d-flex flex-column">Vertical alignment</div>
```

### Colors

**Text colors:**
```html
<p class="text-primary">Blue</p>
<p class="text-success">Green</p>
<p class="text-danger">Red</p>
<p class="text-muted">Gray</p>
<p class="text-dark">Black</p>
```

**Background colors:**
```html
<div class="bg-primary text-white">Blue background</div>
<div class="bg-light">Light background</div>
<div class="bg-white">White background</div>
```

**Opacity:**
```html
<div class="bg-primary bg-opacity-25">25% opacity</div>
<div class="bg-primary bg-opacity-50">50% opacity</div>
```

### Typography

**Font size:**
```html
<p class="fs-1">Largest text</p>
<p class="fs-5">Medium text</p>
<p style="font-size: 0.875rem;">Small text (14px)</p>
```

**Font weight:**
```html
<p class="fw-bold">Bold</p>
<p class="fw-normal">Normal</p>
<p class="fw-light">Light</p>
```

**Text alignment:**
```html
<p class="text-start">Left aligned</p>
<p class="text-center">Center aligned</p>
<p class="text-end">Right aligned</p>
```

### Spacing

**Padding:**
```html
<div class="p-3">Padding all sides 3</div>
<div class="px-4">Padding left/right 4</div>
<div class="py-2">Padding top/bottom 2</div>
<div class="pt-5">Padding top 5</div>
```

**Margin:**
```html
<div class="m-3">Margin all sides 3</div>
<div class="mx-auto">Auto left/right (center align)</div>
<div class="mb-4">Margin bottom 4</div>
```

**Gap (Flexbox, Grid):**
```html
<div class="d-flex gap-2">Gap between elements 2</div>
<div class="row g-3">Grid gap 3</div>
```

### Sizing

**Width:**
```html
<div class="w-25">25% width</div>
<div class="w-50">50% width</div>
<div class="w-100">100% width</div>
```

**Height:**
```html
<div class="h-25">25% height</div>
<div class="h-100">100% height</div>
```

**Min/Max size:**
```html
<div class="min-w-0">Minimum width 0</div>
<div class="min-vh-100">Minimum height 100vh</div>
```

---

## Real Examples

### Example 1: User Card (Bootstrap Utility Only)

```html
<div class="card shadow-sm">
    <div class="card-body p-3 d-flex align-items-center">
        <!-- Profile photo -->
        <img src="photo.jpg"
             class="rounded-circle me-2"
             style="width: 50px; height: 50px; object-fit: cover;">

        <!-- User info -->
        <div class="flex-grow-1 min-w-0">
            <h6 class="mb-0 text-truncate text-dark">John Doe</h6>
            <p class="mb-0 text-muted" style="font-size: 0.75rem;">2024-01-15</p>
        </div>

        <!-- Button -->
        <button class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Add Friend
        </button>
    </div>
</div>
```

### Example 2: Post List (Bootstrap Utility Only)

```html
<div class="container py-4">
    <h1 class="mb-4">Post List</h1>

    <div class="row g-3">
        <!-- Post card -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-3">
                    <h5 class="card-title mb-2 text-primary">Post Title</h5>
                    <p class="card-text text-muted mb-3">Post content...</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted" style="font-size: 0.875rem;">2024-01-15</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Example 3: Profile Header (Some CSS File Use)

**HTML (Bootstrap Utility focused):**
```html
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="profile-header-gradient text-white p-4 rounded-top">
            <div class="d-flex align-items-center">
                <img src="photo.jpg"
                     class="rounded-circle border border-3 border-white me-3"
                     style="width: 80px; height: 80px; object-fit: cover;">
                <div>
                    <h3 class="mb-1">John Doe</h3>
                    <p class="mb-0 opacity-75">john@example.com</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Profile content -->
        </div>
    </div>
</div>
```

**CSS (Only gradient impossible with Bootstrap):**
```css
/* page/user/profile.css */
.profile-header-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

---

## Anti-patterns

### ❌ Anti-pattern Example 1: Unnecessary CSS File Creation

```html
<!-- ❌ Wrong example: Using CSS file when possible with Bootstrap -->
<div class="user-card">
    <div class="user-card-body">
        <h6 class="user-name">John Doe</h6>
    </div>
</div>

<style>
/* ❌ Writing separate CSS even though it can be solved with Bootstrap Utility */
.user-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.25rem;
}
.user-card-body {
    padding: 1rem;
}
.user-name {
    margin-bottom: 0;
    color: #212529;
}
</style>
```

**✅ Correct Example:**
```html
<!-- ✅ Use Bootstrap Utility classes -->
<div class="card shadow-sm">
    <div class="card-body p-3">
        <h6 class="mb-0 text-dark">John Doe</h6>
    </div>
</div>
```

### ❌ Anti-pattern Example 2: Writing Layout with CSS

```html
<!-- ❌ Wrong example: Writing Flexbox layout with CSS -->
<div class="profile-container">
    <div class="profile-left">Left</div>
    <div class="profile-right">Right</div>
</div>

<style>
.profile-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
```

**✅ Correct Example:**
```html
<!-- ✅ Use Bootstrap Utility classes -->
<div class="d-flex justify-content-between align-items-center">
    <div>Left</div>
    <div>Right</div>
</div>
```

### ❌ Anti-pattern Example 3: Writing Colors and Spacing with CSS

```html
<!-- ❌ Wrong example: Writing colors and spacing with CSS -->
<div class="custom-alert">
    <p class="custom-text">Alert message</p>
</div>

<style>
.custom-alert {
    background-color: #d1ecf1;
    padding: 1rem;
    margin-bottom: 1rem;
}
.custom-text {
    color: #0c5460;
    margin-bottom: 0;
}
</style>
```

**✅ Correct Example:**
```html
<!-- ✅ Use Bootstrap Utility classes -->
<div class="alert alert-info mb-3">
    <p class="mb-0 text-info-emphasis">Alert message</p>
</div>
```

---

## Summary

### Core Principles

1. **✅ Prioritize Bootstrap Utility classes**
   - Layout: `d-flex`, `row`, `col-*`, `container`
   - Colors: `text-*`, `bg-*`
   - Spacing: `p-*`, `m-*`, `gap-*`
   - Size: `w-*`, `h-*`

2. **✅ Minimize separate CSS files**
   - Separate only styles impossible with Bootstrap
   - Pages: Auto-load with `load_page_css()` function
   - Widgets: Use `<style>` tags

3. **✅ Easy implementation of responsive design**
   - Use Bootstrap-based classes
   - Easy adjustment of Bootstrap spacing (0~5)
   - Utilize Bootstrap responsive utility classes

### References

- Bootstrap 5 Official Guide: https://getbootstrap.com/docs/5.3/utilities/
- Bootstrap Icons: https://icons.getbootstrap.com/
- [docs/design-guideline.md](./design-guideline.md) - Design Guidelines

---

## Color Guidelines

### Using Bootstrap Colors

**✅ Required: Always use Bootstrap's default color classes and variables**

**Recommended Bootstrap color utilities:**
- **Background**: `bg-primary`, `bg-secondary`, `bg-success`, `bg-danger`, `bg-warning`, `bg-info`, `bg-light`, `bg-dark`, `bg-white`
- **Text**: `text-primary`, `text-secondary`, `text-success`, `text-danger`, `text-warning`, `text-info`, `text-light`, `text-dark`, `text-white`, `text-muted`
- **Border**: `border-primary`, `border-secondary`, etc.

### Prohibition of Custom Colors

**❌ Prohibited:**
- No use of HEX color codes (e.g., `#FF5733`)
- No use of CSS color names outside Bootstrap palette (e.g., `color: red`)

**⚠️ Exception:**
- Custom colors can be used only when absolutely necessary for branding requirements

---

## Bootstrap Component Usage

### Component Guidelines

**Core Principles:**
- ✅ Use Bootstrap components as-is without excessive customization
- ✅ Utilize Bootstrap's default styling for consistency
- ✅ Use Bootstrap utility classes for spacing, size, and layout

### Correct Usage Examples

**✅ Good example: Using Bootstrap color classes**
```html
<div class="bg-light text-dark p-3">
  <h1 class="text-primary">Welcome</h1>
  <button class="btn btn-success">Submit</button>
</div>
```

**❌ Bad example: Using custom colors**
```html
<div style="background-color: #f0f0f0; color: #333;">
  <h1 style="color: blue;">Welcome</h1>
  <button style="background: green;">Submit</button>
</div>
```

### Responsive Design

**Required Rules:**
- ✅ Always use Bootstrap's responsive grid system
- ✅ Test layout on various screen sizes
- ✅ Utilize Bootstrap's responsive utility classes

**Responsive grid example:**
```html
<div class="container">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <!-- Mobile: 100%, Tablet: 50%, Desktop: 33% -->
        </div>
    </div>
</div>
```

**Responsive utility example:**
```html
<!-- Show only on medium screens and up -->
<div class="d-none d-md-block">Visible only on tablet/desktop</div>

<!-- Show only on small screens -->
<div class="d-block d-md-none">Visible only on mobile</div>
```