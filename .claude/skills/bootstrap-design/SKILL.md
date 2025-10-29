---
name: bootstrap-design
description: |
  🔥 MUST-USE SKILL FOR EVERY DESIGN REQUEST! This is an essential guide for web design using Bootstrap 5.
  Covers layouts, components, and best practices. YOU MUST USE THIS SKILL whenever modifying, enhancing, or optimizing designs.
---

# Bootstrap Design SKILL

This SKILL provides a comprehensive guide for web design using Bootstrap 5. It covers layouts, components, and best practices to help you effectively design and optimize web pages.

**⚠️ CRITICAL REQUIREMENT**: This skill MUST be consulted and applied for EVERY design-related request. Do not skip this skill for any design work.

## Card Component (Card Layout)

**🔥🔥🔥 CRITICAL RULE: The Card component is the PRIMARY UI component that MUST be used as the first choice for EVERY possible design on the Sonub website. 🔥🔥🔥**

- [Bootstrap Card Component](https://getbootstrap.com/docs/5.3/components/card/) is the fundamental building block for displaying content on every HTML page.
- ALL content—text, images, links, buttons, and more—MUST be structured and displayed within cards.
- Cards are the primary means of **visually organizing information** and **enhancing user experience** on every page.
- **Before designing any UI element, always ask: "Should this be inside a card?"** The answer is almost always YES.

### Official Bootstrap Card Examples

All examples below are from the official [Bootstrap 5.3 Card Component Documentation](https://getbootstrap.com/docs/5.3/components/card/).

#### 1️⃣ Basic Card (with Image)

The most common card pattern with image, title, text, and action button:

```html
<div class="card" style="width: 18rem;">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
  </div>
</div>
```

#### 2️⃣ Card with Body Only

Minimal card with just text content:

```html
<div class="card">
  <div class="card-body">
    This is some text within a card body.
  </div>
</div>
```

#### 3️⃣ Card with Header and Footer

Card with header and footer sections:

```html
<div class="card">
  <div class="card-header">Featured</div>
  <div class="card-body">
    <h5 class="card-title">Special title treatment</h5>
    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
  </div>
  <div class="card-footer text-body-secondary">2 days ago</div>
</div>
```

#### 4️⃣ Card with List Group

Card with a list of items:

```html
<div class="card" style="width: 18rem;">
  <div class="card-header">Featured</div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">An item</li>
    <li class="list-group-item">A second item</li>
    <li class="list-group-item">A third item</li>
  </ul>
</div>
```

#### 5️⃣ Horizontal Card

Card with image on the left and content on the right:

```html
<div class="card mb-3" style="max-width: 540px;">
  <div class="row g-0">
    <div class="col-md-4">
      <img src="..." class="img-fluid rounded-start" alt="...">
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">Supporting text content.</p>
      </div>
    </div>
  </div>
</div>
```

#### 6️⃣ Colored Card

Card with background color and text styling:

```html
<div class="card text-bg-primary mb-3" style="max-width: 18rem;">
  <div class="card-header">Header</div>
  <div class="card-body">
    <h5 class="card-title">Primary card title</h5>
    <p class="card-text">Sample text content.</p>
  </div>
</div>
```

#### 7️⃣ Card with Image at Bottom

Image positioned at the bottom of the card:

```html
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text to build on the card title.</p>
  </div>
  <img src="..." class="card-img-bottom" alt="...">
</div>
```

#### 8️⃣ Card with Image Overlay

Text overlay on top of a background image:

```html
<div class="card text-bg-dark">
  <img src="..." class="card-img" alt="...">
  <div class="card-img-overlay">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">This is a text overlay on the image.</p>
    <p class="card-text"><small>Last updated 3 mins ago</small></p>
  </div>
</div>
```

#### 9️⃣ Card with Subtitle and Links

Card with subtitle and actionable links:

```html
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <h6 class="card-subtitle mb-2 text-body-secondary">Card subtitle</h6>
    <p class="card-text">Some quick example text to build on the card title and subtitle.</p>
    <a href="#" class="card-link">Card link</a>
    <a href="#" class="card-link">Another link</a>
  </div>
</div>
```

#### 🔟 Responsive Card Grid Layout

Multiple cards in a responsive grid (1 column mobile, 2 columns tablet, 3 columns desktop):

```html
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
  <div class="col">
    <div class="card">
      <img src="..." class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in.</p>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card">
      <img src="..." class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in.</p>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card">
      <img src="..." class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in.</p>
      </div>
    </div>
  </div>
</div>
```

#### 1️⃣1️⃣ Card Group (Equal Width Cards)

Attached, equal-width cards using `.card-group`:

```html
<div class="card-group">
  <div class="card">
    <img src="..." class="card-img-top" alt="...">
    <div class="card-body">
      <h5 class="card-title">Card title</h5>
      <p class="card-text">This is a wider card with supporting text below.</p>
      <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
    </div>
  </div>
  <div class="card">
    <img src="..." class="card-img-top" alt="...">
    <div class="card-body">
      <h5 class="card-title">Card title</h5>
      <p class="card-text">This card has supporting text below.</p>
      <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
    </div>
  </div>
  <div class="card">
    <img src="..." class="card-img-top" alt="...">
    <div class="card-body">
      <h5 class="card-title">Card title</h5>
      <p class="card-text">This is a wider card with supporting text below.</p>
      <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
    </div>
  </div>
</div>
```

---

### 🔥 IMPORTANT: Card Links and Buttons Usage Rules

**CRITICAL RULES for buttons and links within cards:**

1. **✅ ALWAYS use `card-link` class** for ALL buttons and links inside cards
   - This applies to `<a>` tags AND `<button>` elements
   - Even if it's a button element, use `card-link` instead of `btn` classes

2. **❌ DO NOT use `btn` utility classes** (like `btn-primary`, `btn-success`, etc.)
   - Remove `btn`, `btn-primary`, `btn-outline-*` classes from card buttons/links
   - Replace them with `card-link` class

3. **Remove underlines** from `card-link` elements
   - Add `text-decoration-none` class to remove underlines
   - Or use inline style: `style="text-decoration: none;"`

4. **Apply dark grey color and bold font weight** to `card-link` elements
   - Use `style="color: #6c757d; font-weight: bold;"` for inline styling
   - Or use Bootstrap utility classes: `text-secondary fw-bold`
   - ❌ DO NOT use blue color - always use dark grey
   - Color should be dark grey (#6c757d or similar)
   - Font weight should always be bold

5. **Update existing HTML pages**
   - Replace all existing buttons/links in cards with `card-link`
   - Apply dark grey color and bold font weight
   - Ensure consistency across all pages

**Example - Correct Usage:**

```html
<!-- ✅ CORRECT: Using card-link with dark grey color and bold font -->
<div class="card" style="width: 18rem;">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text to build on the card title.</p>
    <a href="#" class="card-link text-decoration-none text-secondary fw-bold">Go somewhere</a>
    <a href="#" class="card-link text-decoration-none text-secondary fw-bold">Another link</a>
  </div>
</div>

<!-- ✅ CORRECT: Using inline styles for color and font-weight -->
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text.</p>
    <a href="#" class="card-link text-decoration-none" style="color: #6c757d; font-weight: bold;">Go somewhere</a>
  </div>
</div>

<!-- ✅ CORRECT: Button element with card-link -->
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text.</p>
    <button class="card-link text-decoration-none text-secondary fw-bold" onclick="doSomething()">Click me</button>
  </div>
</div>
```

**Example - WRONG Usage:**

```html
<!-- ❌ WRONG: Using btn classes -->
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text.</p>
    <a href="#" class="btn btn-primary">Go somewhere</a>  <!-- ❌ Don't use btn classes -->
    <button class="btn btn-success">Click me</button>      <!-- ❌ Don't use btn classes -->
  </div>
</div>

<!-- ❌ WRONG: Using blue color (default link color) -->
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text.</p>
    <a href="#" class="card-link text-decoration-none">Go somewhere</a>  <!-- ❌ No color specified, will be blue -->
    <a href="#" class="card-link text-decoration-none text-primary">Another link</a>  <!-- ❌ Blue color not allowed -->
  </div>
</div>

<!-- ❌ WRONG: Missing bold font-weight -->
<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <p class="card-text">Some quick example text.</p>
    <a href="#" class="card-link text-decoration-none text-secondary">Go somewhere</a>  <!-- ❌ Missing fw-bold -->
  </div>
</div>
```

**Why use `card-link` with dark grey and bold styling?**
- ✅ Maintains visual consistency across all card components
- ✅ Follows Bootstrap's card design system
- ✅ Provides proper spacing and styling within cards
- ✅ Cleaner, more minimalist design approach
- ✅ Dark grey color is more subtle and professional than blue
- ✅ Bold font weight improves clickability perception without using buttons
- ✅ Grey color blends better with card design while remaining visible

---

### 🔥 IMPORTANT: Gradient Background for Text on Images

**CRITICAL RULES for text overlays on images:**

1. **✅ ALWAYS add gradient background** when text appears on top of images
   - Improves readability and text contrast
   - Applies to card image overlays, hero sections, and any text-on-image scenario

2. **❌ DO NOT use solid background colors** that completely hide the image
   - Never use opaque backgrounds like `background: rgba(0,0,0,1)` or `background: #000`
   - Images should remain visible through the gradient

3. **✅ ALWAYS use gradient with alpha (transparency)**
   - Use `linear-gradient()` or `radial-gradient()` with rgba values
   - Alpha values typically range from 0.3 to 0.8 for optimal contrast
   - Gradient should enhance, not hide, the image

4. **Best practices for gradient overlays:**
   - Dark gradient for light images: `rgba(0, 0, 0, 0.5)` to `rgba(0, 0, 0, 0.8)`
   - Light gradient for dark images: `rgba(255, 255, 255, 0.3)` to `rgba(255, 255, 255, 0.6)`
   - Bottom-to-top gradient: `linear-gradient(to top, rgba(0,0,0,0.7), transparent)`
   - Full coverage gradient: `linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5))`

**Example - Correct Usage:**

```html
<!-- ✅ CORRECT: Using gradient overlay on card with image overlay -->
<div class="card text-bg-dark" style="max-width: 540px;">
  <img src="..." class="card-img" alt="...">
  <div class="card-img-overlay d-flex flex-column justify-content-end"
       style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
    <h5 class="card-title">Card Title with Great Readability</h5>
    <p class="card-text">This text is easily readable thanks to the gradient background.</p>
  </div>
</div>

<!-- ✅ CORRECT: Using gradient overlay with custom positioning -->
<div class="card text-white" style="max-width: 540px;">
  <img src="..." class="card-img" alt="...">
  <div class="card-img-overlay d-flex align-items-end"
       style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.8));">
    <div>
      <h5 class="card-title">Enhanced Contrast</h5>
      <p class="card-text">Gradient provides perfect contrast for readability.</p>
    </div>
  </div>
</div>

<!-- ✅ CORRECT: Light gradient for dark images -->
<div class="card text-dark" style="max-width: 540px;">
  <img src="..." class="card-img" alt="...">
  <div class="card-img-overlay d-flex flex-column justify-content-center"
       style="background: linear-gradient(rgba(255,255,255,0.6), rgba(255,255,255,0.4));">
    <h5 class="card-title">Light Gradient on Dark Image</h5>
    <p class="card-text">Perfect for dark background images.</p>
  </div>
</div>
```

**Example - WRONG Usage:**

```html
<!-- ❌ WRONG: Solid background completely hides the image -->
<div class="card text-bg-dark">
  <img src="..." class="card-img" alt="...">
  <div class="card-img-overlay"
       style="background: rgba(0,0,0,1);">  <!-- ❌ Opaque background -->
    <h5 class="card-title">Image is hidden</h5>
  </div>
</div>

<!-- ❌ WRONG: No background - poor readability -->
<div class="card text-bg-dark">
  <img src="..." class="card-img" alt="...">
  <div class="card-img-overlay">  <!-- ❌ No background gradient -->
    <h5 class="card-title">Hard to read text</h5>
  </div>
</div>

<!-- ❌ WRONG: Using solid color -->
<div class="card text-white">
  <img src="..." class="card-img" alt="...">
  <div class="card-img-overlay"
       style="background: #000000;">  <!-- ❌ Solid color hides image -->
    <h5 class="card-title">Image completely hidden</h5>
  </div>
</div>
```

**Common Gradient Patterns:**

```css
/* Bottom fade (most common for card overlays) */
background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);

/* Full coverage with transparency */
background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5));

/* Top to bottom fade */
background: linear-gradient(to bottom, rgba(0,0,0,0.6), transparent);

/* Radial gradient from center */
background: radial-gradient(circle, transparent, rgba(0,0,0,0.7));

/* Light gradient for dark images */
background: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.3));
```

**Why use gradient overlays?**
- ✅ Dramatically improves text readability on images
- ✅ Maintains image visibility while adding contrast
- ✅ Professional, modern design aesthetic
- ✅ Ensures accessibility and legibility across different image types
- ✅ Works well with various image brightness levels

---

### 🔥 IMPORTANT: Gradient Background for Colored Cards with Images

**CRITICAL RULES for colored cards (success, warning, danger, primary, etc.) with images:**

1. **✅ ALWAYS add gradient overlay** when colored cards have images with text
   - Applies to `text-bg-success`, `text-bg-warning`, `text-bg-danger`, `text-bg-primary`, etc.
   - Gradient must have transparency to show the image underneath
   - Improves text readability while maintaining the colored theme

2. **❌ DO NOT use solid opaque backgrounds** on colored cards with images
   - Never use `background: rgba(40,167,69,1)` or similar solid colors
   - Images must remain partially visible through the gradient

3. **✅ Use color-matched gradients with transparency**
   - Match gradient color to card background color
   - Use alpha values from 0.3 to 0.7 for optimal image visibility
   - Gradient should blend with card's color scheme

4. **Gradient patterns for each colored card:**

```css
/* Success Card (green) */
background: linear-gradient(to top, rgba(25, 135, 84, 0.7), transparent);
/* or full coverage */
background: linear-gradient(rgba(25, 135, 84, 0.5), rgba(25, 135, 84, 0.5));

/* Warning Card (yellow/orange) */
background: linear-gradient(to top, rgba(255, 193, 7, 0.7), transparent);
/* or full coverage */
background: linear-gradient(rgba(255, 193, 7, 0.6), rgba(255, 193, 7, 0.6));

/* Danger Card (red) */
background: linear-gradient(to top, rgba(220, 53, 69, 0.7), transparent);
/* or full coverage */
background: linear-gradient(rgba(220, 53, 69, 0.5), rgba(220, 53, 69, 0.5));

/* Primary Card (blue) */
background: linear-gradient(to top, rgba(13, 110, 253, 0.7), transparent);
/* or full coverage */
background: linear-gradient(rgba(13, 110, 253, 0.5), rgba(13, 110, 253, 0.5));
```

**Example - Correct Usage:**

```html
<!-- ✅ CORRECT: Success card with gradient overlay -->
<div class="card text-bg-success shadow-sm">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body" style="background: linear-gradient(to top, rgba(25, 135, 84, 0.7), transparent);">
    <h5 class="card-title">Success Card Title</h5>
    <p class="card-text">Text is readable and image is visible through gradient.</p>
  </div>
</div>

<!-- ✅ CORRECT: Warning card with full coverage gradient -->
<div class="card text-bg-warning shadow-sm">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body" style="background: linear-gradient(rgba(255, 193, 7, 0.6), rgba(255, 193, 7, 0.6));">
    <h5 class="card-title">Warning Card Title</h5>
    <p class="card-text">Gradient maintains warning color while showing image.</p>
  </div>
</div>

<!-- ✅ CORRECT: Danger card with gradient -->
<div class="card text-bg-danger shadow-sm">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body" style="background: linear-gradient(to top, rgba(220, 53, 69, 0.7), transparent);">
    <h5 class="card-title">Danger Card Title</h5>
    <p class="card-text">Text remains readable with danger theme preserved.</p>
  </div>
</div>

<!-- ✅ CORRECT: Primary card with gradient on card-body -->
<div class="card text-bg-primary shadow-sm">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body" style="background: linear-gradient(rgba(13, 110, 253, 0.5), rgba(13, 110, 253, 0.5));">
    <h5 class="card-title">Primary Card Title</h5>
    <p class="card-text">Gradient blends with primary blue theme.</p>
  </div>
</div>
```

**Example - WRONG Usage:**

```html
<!-- ❌ WRONG: No gradient - poor readability -->
<div class="card text-bg-success shadow-sm">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body">  <!-- ❌ No gradient overlay -->
    <h5 class="card-title">Hard to read text</h5>
    <p class="card-text">Image may interfere with text readability.</p>
  </div>
</div>

<!-- ❌ WRONG: Solid opaque background hides image -->
<div class="card text-bg-danger shadow-sm">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body" style="background: rgba(220, 53, 69, 1);">  <!-- ❌ Opaque, hides image -->
    <h5 class="card-title">Image completely hidden</h5>
    <p class="card-text">No transparency means image is not visible.</p>
  </div>
</div>

<!-- ❌ WRONG: Wrong color gradient -->
<div class="card text-bg-success shadow-sm">
  <img src="..." class="card-img-top" alt="...">
  <div class="card-body" style="background: linear-gradient(rgba(0, 0, 0, 0.7), transparent);">  <!-- ❌ Black gradient on green card -->
    <h5 class="card-title">Inconsistent colors</h5>
    <p class="card-text">Gradient color doesn't match card theme.</p>
  </div>
</div>
```

**Bootstrap Color Reference for Gradients:**

```css
/* Bootstrap 5.3 Color Values */
--bs-primary: rgb(13, 110, 253);      /* Blue */
--bs-success: rgb(25, 135, 84);       /* Green */
--bs-danger: rgb(220, 53, 69);        /* Red */
--bs-warning: rgb(255, 193, 7);       /* Yellow/Orange */
--bs-info: rgb(13, 202, 240);         /* Cyan */
--bs-secondary: rgb(108, 117, 125);   /* Grey */
```

**Why use color-matched gradients on colored cards?**
- ✅ Maintains card's visual theme and color identity
- ✅ Improves text readability without hiding the image
- ✅ Creates cohesive design that blends background, image, and text
- ✅ Provides sufficient contrast while preserving brand colors
- ✅ Ensures accessibility across different image types and lighting
- ✅ Professional appearance with proper color coordination

---

### Complete Card Classes Reference

**Image Classes:**
- `card-img-top`: Image positioned at the top of the card
- `card-img-bottom`: Image positioned at the bottom of the card
- `card-img`: Full-width background image (use with `card-img-overlay`)
- `card-img-overlay`: Text overlay on top of `card-img`

**Content Classes:**
- `card`: The main card container
- `card-header`: Header section at the top
- `card-body`: Main content area with padding
- `card-footer`: Footer section at the bottom
- `card-title`: Card title heading
- `card-subtitle`: Card subtitle (usually with gray color)
- `card-text`: Paragraph text within card
- `card-link`: Styled link within card

**Styling Classes:**
- `text-bg-primary`, `text-bg-secondary`, `text-bg-success`, `text-bg-danger`, `text-bg-warning`, `text-bg-info`, `text-bg-light`, `text-bg-dark`: Colored backgrounds with auto text colors
- `border-primary`, `border-secondary`, etc.: Colored borders
- `list-group`: List group component within a card

**Layout Classes:**
- `card-group`: Groups cards together with equal widths
- `row-cols-1`, `row-cols-md-2`, `row-cols-lg-3`: Responsive grid columns
- `g-4`: Gutter/gap spacing between cards
- `h-100`: Make card 100% of container height

### Using Bootstrap Utility Classes with Cards

**Important**: Always use Bootstrap Utility classes as much as possible.

```html
<!-- ✅ Correct: Using Bootstrap Utility classes -->
<div class="card shadow-sm mb-3">
  <div class="card-body p-4">
    <h5 class="card-title mb-3 text-primary">Title</h5>
    <p class="card-text text-muted mb-0">Content</p>
  </div>
</div>

<!-- ❌ Avoid: Unnecessary custom CSS -->
<div class="card custom-shadow">
  <div class="card-body custom-padding">
    <h5 class="custom-title">Title</h5>
  </div>
</div>
```

**Commonly Used Utility Classes:**
- `shadow-sm`: Subtle shadow
- `shadow`: Medium shadow
- `mb-3`: Bottom margin
- `p-3`: Internal padding
- `text-primary`, `text-muted`: Text colors
- `d-flex`, `align-items-center`: Flexbox layout
- `flex-grow-1`: Flexible width

### Responsive Card Layout

Layout multiple cards responsively:

```html
<div class="container py-4">
  <div class="row g-3">
    <!-- Card 1 -->
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title">Card 1</h5>
          <p class="card-text">Card content</p>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title">Card 2</h5>
          <p class="card-text">Card content</p>
        </div>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title">Card 3</h5>
          <p class="card-text">Card content</p>
        </div>
      </div>
    </div>
  </div>
</div>
```

**Responsive Classes Explained:**
- `col-12`: Mobile (100% width)
- `col-md-6`: Tablet (50% width)
- `col-lg-4`: Desktop (33% width)
- `g-3`: Gap between cards

### Card Styling Tips

1. **Maintain Consistent Height**: Use `h-100` class to keep card heights equal
2. **Shadow Effects**: Use `shadow-sm` (subtle) or `shadow` (medium)
3. **Padding Adjustment**: Use `p-3` (12px) or `p-4` (24px)
4. **Text Overflow**: Use `text-truncate` class to handle long text

---

## Reference Materials

- [Bootstrap Official Card Documentation](https://getbootstrap.com/docs/5.3/components/card/)
- [Bootstrap Utility CSS Guide](https://getbootstrap.com/docs/5.3/utilities/)
- [Project Bootstrap Design Guide](docs/design/bootstrap.md)