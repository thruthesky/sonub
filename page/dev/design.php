<div class="container py-5">
    <section class="bootstrap-design-component-samples">
        <h1 class="mb-4">Bootstrap Design Component Samples</h1>
        <p class="lead mb-5">This page showcases various Bootstrap card component examples. You can access this page from the developer quick login panel dropdown menu. Explore different card patterns and use cases below.</p>

        <!-- Card with Image Top (Basic) -->
        <div class="mb-5">
            <h2 class="mb-3">1️⃣ Basic Card (card-img-top)</h2>
            <p class="text-muted mb-3">The most common card pattern with image at the top, title, text, and action button.</p>
            <div class="card shadow-sm" style="width: 18rem;">
                <img src="https://picsum.photos/286/180?random=1" class="card-img-top" alt="Sample Image">
                <div class="card-body">
                    <h5 class="card-title">Card Title</h5>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">Go somewhere</a>
                </div>
            </div>
        </div>

        <!-- Card with Image Bottom -->
        <div class="mb-5">
            <h2 class="mb-3">2️⃣ Card with Image at Bottom (card-img-bottom)</h2>
            <p class="text-muted mb-3">Image positioned at the bottom of the card - useful for content-first layouts.</p>
            <div class="card shadow-sm" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Card Title</h5>
                    <p class="card-text">Some quick example text to build on the card title. The image appears at the bottom.</p>
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">Learn More</a>
                </div>
                <img src="https://picsum.photos/286/180?random=2" class="card-img-bottom" alt="Bottom Image">
            </div>
        </div>

        <!-- Multiple Cards with card-img-top -->
        <div class="mb-5">
            <h2 class="mb-3">3️⃣ Multiple Cards with card-img-top (Responsive Grid)</h2>
            <p class="text-muted mb-3">Three cards in a responsive grid layout: 1 column on mobile, 2 on tablet, 3 on desktop.</p>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <img src="https://picsum.photos/400/200?random=3" class="card-img-top" alt="Card 1">
                        <div class="card-body">
                            <h5 class="card-title">Card Title 1</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <img src="https://picsum.photos/400/200?random=4" class="card-img-top" alt="Card 2">
                        <div class="card-body">
                            <h5 class="card-title">Card Title 2</h5>
                            <p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <img src="https://picsum.photos/400/200?random=5" class="card-img-top" alt="Card 3">
                        <div class="card-body">
                            <h5 class="card-title">Card Title 3</h5>
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card with Header and Footer -->
        <div class="mb-5">
            <h2 class="mb-3">4️⃣ Card with Header, Footer, and card-img-top</h2>
            <p class="text-muted mb-3">Card with header and footer sections for additional metadata.</p>
            <div class="card shadow-sm" style="width: 18rem;">
                <div class="card-header">
                    <i class="fa-solid fa-star text-warning"></i> Featured
                </div>
                <img src="https://picsum.photos/286/180?random=6" class="card-img-top" alt="Featured Image">
                <div class="card-body">
                    <h5 class="card-title">Special Title Treatment</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">Go somewhere</a>
                </div>
                <div class="card-footer text-body-secondary">
                    <i class="fa-regular fa-clock"></i> 2 days ago
                </div>
            </div>
        </div>

        <!-- Horizontal Card with Image -->
        <div class="mb-5">
            <h2 class="mb-3">5️⃣ Horizontal Card with Image</h2>
            <p class="text-muted mb-3">Card with image on the left and content on the right - great for list layouts.</p>
            <div class="card shadow-sm mb-3" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="https://picsum.photos/200/250?random=7" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="Horizontal Card">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Horizontal Card Title</h5>
                            <p class="card-text">This is a horizontal card layout with the image on the left side. Perfect for blog posts or product listings.</p>
                            <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colored Cards -->
        <div class="mb-5">
            <h2 class="mb-3">6️⃣ Colored Cards with card-img-top and Gradients</h2>
            <p class="text-muted mb-3">Cards with background colors, gradients for text readability, and automatic text color adjustment.</p>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card text-bg-primary shadow-sm">
                        <img src="https://picsum.photos/300/150?random=8" class="card-img-top" alt="Primary Card">
                        <div class="card-body" style="background: linear-gradient(to bottom, rgba(13, 110, 253, 0.8), rgba(13, 110, 253, 0.3));">
                            <h5 class="card-title">Primary Card</h5>
                            <p class="card-text">Gradient maintains blue theme while showing image underneath.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card text-bg-success shadow-sm">
                        <img src="https://picsum.photos/300/150?random=9" class="card-img-top" alt="Success Card">
                        <div class="card-body" style="background: linear-gradient(to bottom, rgba(25, 135, 84, 0.8), rgba(25, 135, 84, 0.3));">
                            <h5 class="card-title">Success Card</h5>
                            <p class="card-text">Gradient maintains green theme while showing image underneath.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card text-bg-danger shadow-sm">
                        <img src="https://picsum.photos/300/150?random=10" class="card-img-top" alt="Danger Card">
                        <div class="card-body" style="background: linear-gradient(to bottom, rgba(220, 53, 69, 0.8), rgba(220, 53, 69, 0.3));">
                            <h5 class="card-title">Danger Card</h5>
                            <p class="card-text">Gradient maintains red theme while showing image underneath.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card text-bg-warning shadow-sm">
                        <img src="https://picsum.photos/300/150?random=11" class="card-img-top" alt="Warning Card">
                        <div class="card-body" style="background: linear-gradient(to bottom, rgba(255, 193, 7, 0.8), rgba(255, 193, 7, 0.3));">
                            <h5 class="card-title">Warning Card</h5>
                            <p class="card-text">Gradient maintains warning color while showing image underneath.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card with Image Overlay -->
        <div class="mb-5">
            <h2 class="mb-3">7️⃣ Card with Image Overlay (Mobile Optimized)</h2>
            <p class="text-muted mb-3">Text overlay on top of a background image with gradient - perfect for hero sections. Optimized for mobile with responsive padding and font sizes.</p>
            <div class="card text-bg-dark shadow-sm" style="max-width: 540px;">
                <img src="https://picsum.photos/540/300?random=12" class="card-img" alt="Overlay Image">
                <div class="card-img-overlay d-flex flex-column justify-content-end p-3 p-md-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                    <h5 class="card-title fs-5 fs-md-4 fw-bold">Card with Image Overlay</h5>
                    <p class="card-text small">This is a text overlay on the image with gradient background for better readability. The background image remains visible through the gradient.</p>
                    <p class="card-text d-none d-md-block"><small>Last updated 3 mins ago</small></p>
                </div>
            </div>
            <div class="alert alert-info mt-3" role="alert">
                <strong>Mobile Optimizations:</strong>
                <ul class="mb-0 mt-2">
                    <li><code>p-3 p-md-4</code> - Smaller padding on mobile (12px), larger on desktop (24px)</li>
                    <li><code>fs-5 fs-md-4</code> - Smaller title font on mobile</li>
                    <li><code>small</code> - Smaller body text for better mobile readability</li>
                    <li><code>d-none d-md-block</code> - Hide less critical content on mobile</li>
                    <li>Stronger gradient (<code>rgba(0,0,0,0.8)</code>) for better text contrast on small screens</li>
                </ul>
            </div>
        </div>

        <!-- Card with Subtitle and Links -->
        <div class="mb-5">
            <h2 class="mb-3">8️⃣ Card with Subtitle, Links, and card-img-top</h2>
            <p class="text-muted mb-3">Card with subtitle and multiple actionable links.</p>
            <div class="card shadow-sm" style="width: 18rem;">
                <img src="https://picsum.photos/286/180?random=13" class="card-img-top" alt="Card with Links">
                <div class="card-body">
                    <h5 class="card-title">Card Title</h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Card Subtitle</h6>
                    <p class="card-text">Some quick example text to build on the card title and subtitle.</p>
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">Card link</a>
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">Another link</a>
                </div>
            </div>
        </div>

        <!-- Card with List Group -->
        <div class="mb-5">
            <h2 class="mb-3">9️⃣ Card with List Group and card-img-top</h2>
            <p class="text-muted mb-3">Card with a list of items - useful for menus or feature lists.</p>
            <div class="card shadow-sm" style="width: 18rem;">
                <img src="https://picsum.photos/286/180?random=14" class="card-img-top" alt="List Card">
                <div class="card-header">
                    <i class="fa-solid fa-list"></i> Featured List
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fa-solid fa-check text-success"></i> An item</li>
                    <li class="list-group-item"><i class="fa-solid fa-check text-success"></i> A second item</li>
                    <li class="list-group-item"><i class="fa-solid fa-check text-success"></i> A third item</li>
                </ul>
            </div>
        </div>

        <!-- Card Body Only -->
        <div class="mb-5">
            <h2 class="mb-3">🔟 Card with Body Only (No Image)</h2>
            <p class="text-muted mb-3">Minimal card with just text content - perfect for quotes or notifications.</p>
            <div class="card shadow-sm" style="max-width: 540px;">
                <div class="card-body">
                    <p class="card-text">This is some text within a card body. Simple and clean layout without any images.</p>
                </div>
            </div>
        </div>

        <!-- Multiple Cards with card-img-bottom -->
        <div class="mb-5">
            <h2 class="mb-3">1️⃣1️⃣ Multiple Cards with card-img-bottom</h2>
            <p class="text-muted mb-3">Three cards with images at the bottom - content-first approach.</p>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Bottom Image Card 1</h5>
                            <p class="card-text">Content comes first, then the image appears at the bottom of the card.</p>
                            <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">View Details</a>
                        </div>
                        <img src="https://picsum.photos/400/200?random=15" class="card-img-bottom" alt="Bottom 1">
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Bottom Image Card 2</h5>
                            <p class="card-text">This layout prioritizes text content and ends with a visual element.</p>
                            <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">View Details</a>
                        </div>
                        <img src="https://picsum.photos/400/200?random=16" class="card-img-bottom" alt="Bottom 2">
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Bottom Image Card 3</h5>
                            <p class="card-text">Great for emphasizing content over imagery in your design.</p>
                            <a href="#" class="card-link text-decoration-none text-secondary fw-bold small">View Details</a>
                        </div>
                        <img src="https://picsum.photos/400/200?random=17" class="card-img-bottom" alt="Bottom 3">
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Group -->
        <div class="mb-5">
            <h2 class="mb-3">1️⃣2️⃣ Card Group (Equal Width Cards with card-img-top)</h2>
            <p class="text-muted mb-3">Attached, equal-width cards using <code>.card-group</code> class.</p>
            <div class="card-group shadow-sm">
                <div class="card">
                    <img src="https://picsum.photos/400/200?random=18" class="card-img-top" alt="Group 1">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 1</h5>
                        <p class="card-text">This is a wider card with supporting text below.</p>
                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                    </div>
                </div>
                <div class="card">
                    <img src="https://picsum.photos/400/200?random=19" class="card-img-top" alt="Group 2">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 2</h5>
                        <p class="card-text">This card has supporting text below.</p>
                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                    </div>
                </div>
                <div class="card">
                    <img src="https://picsum.photos/400/200?random=20" class="card-img-top" alt="Group 3">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 3</h5>
                        <p class="card-text">This is a wider card with supporting text below.</p>
                        <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <!-- Button Component Section -->
        <h1 class="mb-4 mt-5">Bootstrap Button Component Samples</h1>
        <p class="lead mb-5">Comprehensive examples of Bootstrap button components including base variants, outlines, sizes, icons, and states.</p>

        <!-- Base Button Variants -->
        <div class="mb-5">
            <h2 class="mb-3">1️⃣ Base Button Variants</h2>
            <p class="text-muted mb-3">The eight semantic button variants - use appropriate colors for different actions.</p>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary">Primary</button>
                <button type="button" class="btn btn-secondary">Secondary</button>
                <button type="button" class="btn btn-success">Success</button>
                <button type="button" class="btn btn-danger">Danger</button>
                <button type="button" class="btn btn-warning">Warning</button>
                <button type="button" class="btn btn-info">Info</button>
                <button type="button" class="btn btn-light">Light</button>
                <button type="button" class="btn btn-dark">Dark</button>
                <button type="button" class="btn btn-link">Link</button>
            </div>
        </div>

        <!-- Outline Buttons -->
        <div class="mb-5">
            <h2 class="mb-3">2️⃣ Outline Buttons</h2>
            <p class="text-muted mb-3">Outline buttons remove background colors while maintaining borders - great for less prominent actions.</p>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-primary">Primary</button>
                <button type="button" class="btn btn-outline-secondary">Secondary</button>
                <button type="button" class="btn btn-outline-success">Success</button>
                <button type="button" class="btn btn-outline-danger">Danger</button>
                <button type="button" class="btn btn-outline-warning">Warning</button>
                <button type="button" class="btn btn-outline-info">Info</button>
                <button type="button" class="btn btn-outline-light">Light</button>
                <button type="button" class="btn btn-outline-dark">Dark</button>
            </div>
        </div>

        <!-- Button Sizes -->
        <div class="mb-5">
            <h2 class="mb-3">3️⃣ Button Sizes</h2>
            <p class="text-muted mb-3">Three size variants: large, default, and small.</p>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-primary btn-lg">Large button</button>
                <button type="button" class="btn btn-primary">Default button</button>
                <button type="button" class="btn btn-primary btn-sm">Small button</button>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary btn-lg">Large outline</button>
                <button type="button" class="btn btn-outline-secondary">Default outline</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">Small outline</button>
            </div>
        </div>

        <!-- Buttons with Icons -->
        <div class="mb-5">
            <h2 class="mb-3">4️⃣ Buttons with Icons</h2>
            <p class="text-muted mb-3">Combine Font Awesome icons with text for better visual communication.</p>

            <h5 class="mb-2">Icon + Text (Icon Left)</h5>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="button" class="btn btn-primary">
                    <i class="fa-solid fa-download"></i> Download
                </button>
                <button type="button" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Confirm
                </button>
                <button type="button" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-info">
                    <i class="fa-solid fa-circle-info"></i> Information
                </button>
            </div>

            <h5 class="mb-2">Text + Icon (Icon Right)</h5>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="button" class="btn btn-primary">
                    Next <i class="fa-solid fa-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-secondary">
                    Upload <i class="fa-solid fa-upload"></i>
                </button>
                <button type="button" class="btn btn-success">
                    Save <i class="fa-solid fa-check"></i>
                </button>
            </div>

            <h5 class="mb-2">Icon Buttons with Gap (Better Spacing)</h5>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add New</span>
                </button>
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-pen"></i>
                    <span>Edit</span>
                </button>
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    <span>Remove</span>
                </button>
            </div>
        </div>

        <!-- Icon-Only Buttons -->
        <div class="mb-5">
            <h2 class="mb-3">5️⃣ Icon-Only Buttons</h2>
            <p class="text-muted mb-3">Icon buttons without text - remember to add aria-label for accessibility!</p>

            <h5 class="mb-2">Square Icon Buttons</h5>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="button" class="btn btn-primary" aria-label="Edit">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button type="button" class="btn btn-danger" aria-label="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <button type="button" class="btn btn-success" aria-label="Check">
                    <i class="fa-solid fa-check"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" aria-label="Settings">
                    <i class="fa-solid fa-gear"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <h5 class="mb-2">Circular Icon Buttons</h5>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary rounded-circle flex-center" style="width: 40px; height: 40px;" aria-label="Add">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button type="button" class="btn btn-danger rounded-circle flex-center" style="width: 40px; height: 40px;" aria-label="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <button type="button" class="btn btn-success rounded-circle flex-center" style="width: 40px; height: 40px;" aria-label="Check">
                    <i class="fa-solid fa-check"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary rounded-circle flex-center" style="width: 40px; height: 40px;" aria-label="Settings">
                    <i class="fa-solid fa-gear"></i>
                </button>
            </div>
        </div>

        <!-- Disabled State -->
        <div class="mb-5">
            <h2 class="mb-3">6️⃣ Disabled State</h2>
            <p class="text-muted mb-3">Disable buttons to prevent interaction when actions are temporarily unavailable.</p>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary" disabled>Disabled primary</button>
                <button type="button" class="btn btn-secondary" disabled>Disabled secondary</button>
                <button type="button" class="btn btn-outline-primary" disabled>Disabled outline</button>
                <a class="btn btn-primary disabled" role="button" aria-disabled="true">Disabled link</a>
            </div>
        </div>

        <!-- Block Buttons -->
        <div class="mb-5">
            <h2 class="mb-3">7️⃣ Block Buttons (Full Width)</h2>
            <p class="text-muted mb-3">Create full-width buttons using grid utilities - great for mobile layouts.</p>

            <h5 class="mb-2">Single Full-Width Button</h5>
            <div class="d-grid mb-4" style="max-width: 400px;">
                <button class="btn btn-primary" type="button">Full width button</button>
            </div>

            <h5 class="mb-2">Stacked Full-Width Buttons</h5>
            <div class="d-grid gap-2 mb-4" style="max-width: 400px;">
                <button class="btn btn-primary" type="button">Primary action</button>
                <button class="btn btn-secondary" type="button">Secondary action</button>
                <button class="btn btn-outline-secondary" type="button">Tertiary action</button>
            </div>

            <h5 class="mb-2">Responsive: Full-Width on Mobile, Inline on Desktop</h5>
            <div class="d-grid gap-2 d-md-block" style="max-width: 600px;">
                <button class="btn btn-primary" type="button">Button 1</button>
                <button class="btn btn-secondary" type="button">Button 2</button>
                <button class="btn btn-outline-secondary" type="button">Button 3</button>
            </div>
        </div>

        <!-- Button Groups -->
        <div class="mb-5">
            <h2 class="mb-3">8️⃣ Button Groups</h2>
            <p class="text-muted mb-3">Group buttons together for related actions.</p>

            <h5 class="mb-2">Basic Button Group</h5>
            <div class="btn-group mb-4" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-primary">Left</button>
                <button type="button" class="btn btn-primary">Middle</button>
                <button type="button" class="btn btn-primary">Right</button>
            </div>

            <h5 class="mb-2">Outline Button Group</h5>
            <div class="btn-group mb-4" role="group" aria-label="Outline example">
                <button type="button" class="btn btn-outline-primary">Left</button>
                <button type="button" class="btn btn-outline-primary">Middle</button>
                <button type="button" class="btn btn-outline-primary">Right</button>
            </div>

            <h5 class="mb-2">Icon Button Group (Actions)</h5>
            <div class="btn-group" role="group" aria-label="Action buttons">
                <button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Edit">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Copy">
                    <i class="fa-solid fa-copy"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>

        <!-- Toggle Buttons -->
        <div class="mb-5">
            <h2 class="mb-3">9️⃣ Toggle Buttons</h2>
            <p class="text-muted mb-3">Buttons that toggle between active and inactive states.</p>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="button">
                    Toggle button
                </button>
                <button type="button" class="btn btn-primary active" data-bs-toggle="button" aria-pressed="true">
                    Active toggle
                </button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="button">
                    Outline toggle
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="button" disabled>
                    Disabled toggle
                </button>
            </div>
        </div>

        <!-- Borderless Icon Buttons -->
        <div class="mb-5">
            <h2 class="mb-3">🔟 Borderless Icon Buttons (Subtle Actions)</h2>
            <p class="text-muted mb-3">Icon buttons without borders - perfect for card actions with hover effects.</p>

            <h5 class="mb-2">Basic Borderless Icon Buttons</h5>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="button" class="btn btn-link text-secondary p-2" aria-label="Edit">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button type="button" class="btn btn-link text-secondary p-2" aria-label="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <button type="button" class="btn btn-link text-secondary p-2" aria-label="Settings">
                    <i class="fa-solid fa-gear"></i>
                </button>
            </div>

            <h5 class="mb-2">With Custom Hover Colors</h5>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="button" class="btn btn-link text-warning p-2 rounded"
                        style="transition: background-color 0.2s ease;"
                        onmouseover="this.style.backgroundColor='#fff3cd'"
                        onmouseout="this.style.backgroundColor='transparent'"
                        aria-label="Edit">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button type="button" class="btn btn-link text-danger p-2 rounded"
                        style="transition: background-color 0.2s ease;"
                        onmouseover="this.style.backgroundColor='#ffe5e5'"
                        onmouseout="this.style.backgroundColor='transparent'"
                        aria-label="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>

            <h5 class="mb-2">Card Example with Borderless Actions</h5>
            <div class="card shadow-sm" style="max-width: 400px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Card Title</h5>
                            <p class="card-text">Some quick example text for the card content.</p>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-link text-warning p-2 rounded"
                                    style="transition: background-color 0.2s ease;"
                                    onmouseover="this.style.backgroundColor='#fff3cd'"
                                    onmouseout="this.style.backgroundColor='transparent'"
                                    aria-label="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-link text-danger p-2 rounded"
                                    style="transition: background-color 0.2s ease;"
                                    onmouseover="this.style.backgroundColor='#ffe5e5'"
                                    onmouseout="this.style.backgroundColor='transparent'"
                                    aria-label="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Icon + Text Buttons (No Background) -->
        <div class="mb-5">
            <h2 class="mb-3">1️⃣1️⃣ Icon + Text Buttons (No Background)</h2>
            <p class="text-muted mb-3">Text with icon buttons without background - perfect for footer links and navigation. No underline on hover.</p>

            <h5 class="mb-2">Basic Icon + Text Links</h5>
            <div class="d-flex flex-wrap gap-3 mb-4">
                <a href="#" class="text-decoration-none text-secondary fw-semibold d-inline-flex align-items-center gap-2"
                   style="transition: color 0.2s ease;"
                   onmouseover="this.style.color='#0d6efd'"
                   onmouseout="this.style.color='#6c757d'">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Privacy</span>
                </a>
                <a href="#" class="text-decoration-none text-secondary fw-semibold d-inline-flex align-items-center gap-2"
                   style="transition: color 0.2s ease;"
                   onmouseover="this.style.color='#0d6efd'"
                   onmouseout="this.style.color='#6c757d'">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Terms</span>
                </a>
                <a href="#" class="text-decoration-none text-secondary fw-semibold d-inline-flex align-items-center gap-2"
                   style="transition: color 0.2s ease;"
                   onmouseover="this.style.color='#0d6efd'"
                   onmouseout="this.style.color='#6c757d'">
                    <i class="fa-solid fa-bullhorn"></i>
                    <span>Advertising</span>
                </a>
                <a href="#" class="text-decoration-none text-secondary fw-semibold d-inline-flex align-items-center gap-2"
                   style="transition: color 0.2s ease;"
                   onmouseover="this.style.color='#0d6efd'"
                   onmouseout="this.style.color='#6c757d'">
                    <i class="fa-solid fa-ellipsis"></i>
                    <span>About</span>
                </a>
            </div>

            <h5 class="mb-2">Button Version (for non-link actions)</h5>
            <div class="d-flex flex-wrap gap-3">
                <button type="button" class="btn p-0 border-0 text-decoration-none text-secondary fw-semibold d-inline-flex align-items-center gap-2"
                        style="background: none; transition: color 0.2s ease;"
                        onmouseover="this.style.color='#0d6efd'"
                        onmouseout="this.style.color='#6c757d'">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Privacy</span>
                </button>
                <button type="button" class="btn p-0 border-0 text-decoration-none text-secondary fw-semibold d-inline-flex align-items-center gap-2"
                        style="background: none; transition: color 0.2s ease;"
                        onmouseover="this.style.color='#0d6efd'"
                        onmouseout="this.style.color='#6c757d'">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Terms</span>
                </button>
            </div>
        </div>

        <!-- Expandable "See More" Button -->
        <div class="mb-5">
            <h2 class="mb-3">1️⃣2️⃣ Expandable "See More" Button</h2>
            <p class="text-muted mb-3">Dropdown-style button with chevron icon - commonly used for expanding content.</p>

            <h5 class="mb-2">Basic See More Button</h5>
            <div class="mb-4">
                <button type="button" class="btn btn-link text-decoration-none text-secondary fw-medium d-inline-flex align-items-center gap-2 p-2">
                    <span>See more</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>

            <h5 class="mb-2">Interactive Toggle (Chevron Rotates)</h5>
            <div class="mb-4">
                <button type="button"
                        class="btn btn-link text-decoration-none text-secondary fw-medium d-inline-flex align-items-center gap-2 p-2"
                        onclick="this.querySelector('i').classList.toggle('fa-chevron-down'); this.querySelector('i').classList.toggle('fa-chevron-up');">
                    <span>See more</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>

            <h5 class="mb-2">With Bootstrap Collapse</h5>
            <div>
                <button type="button"
                        class="btn btn-link text-decoration-none text-secondary fw-medium d-inline-flex align-items-center gap-2 p-2"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseExample"
                        aria-expanded="false"
                        aria-controls="collapseExample">
                    <span>See more</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="collapse mt-3" id="collapseExample">
                    <div class="card card-body">
                        This is the collapsed content that appears when you click "See more". You can put any content here including text, images, or other components.
                    </div>
                </div>
            </div>
        </div>

        <!-- Reference Links -->
        <div class="alert alert-info mt-5" role="alert">
            <h4 class="alert-heading"><i class="fa-solid fa-book"></i> Reference Materials</h4>
            <hr>
            <ul class="mb-0">
                <li><a href="https://getbootstrap.com/docs/5.3/components/card/" target="_blank" class="alert-link">Bootstrap Official Card Documentation</a></li>
                <li><a href="https://getbootstrap.com/docs/5.3/components/buttons/" target="_blank" class="alert-link">Bootstrap Official Button Documentation</a></li>
                <li><a href="https://getbootstrap.com/docs/5.3/utilities/" target="_blank" class="alert-link">Bootstrap Utility CSS Guide</a></li>
                <li><a href="/docs/design/bootstrap.md" class="alert-link">Project Bootstrap Design Guide</a></li>
            </ul>
        </div>

    </section>
</div>