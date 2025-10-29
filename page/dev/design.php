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
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold">Go somewhere</a>
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
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold">Learn More</a>
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
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold">Go somewhere</a>
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
                        <div class="card-body" style="background: linear-gradient(rgba(13, 110, 253, 0.5), rgba(13, 110, 253, 0.5));">
                            <h5 class="card-title">Primary Card</h5>
                            <p class="card-text">Gradient maintains blue theme while showing image underneath.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card text-bg-success shadow-sm">
                        <img src="https://picsum.photos/300/150?random=9" class="card-img-top" alt="Success Card">
                        <div class="card-body" style="background: linear-gradient(rgba(25, 135, 84, 0.5), rgba(25, 135, 84, 0.5));">
                            <h5 class="card-title">Success Card</h5>
                            <p class="card-text">Gradient maintains green theme while showing image underneath.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card text-bg-danger shadow-sm">
                        <img src="https://picsum.photos/300/150?random=10" class="card-img-top" alt="Danger Card">
                        <div class="card-body" style="background: linear-gradient(rgba(220, 53, 69, 0.5), rgba(220, 53, 69, 0.5));">
                            <h5 class="card-title">Danger Card</h5>
                            <p class="card-text">Gradient maintains red theme while showing image underneath.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card text-bg-warning shadow-sm">
                        <img src="https://picsum.photos/300/150?random=11" class="card-img-top" alt="Warning Card">
                        <div class="card-body" style="background: linear-gradient(rgba(255, 193, 7, 0.6), rgba(255, 193, 7, 0.6));">
                            <h5 class="card-title">Warning Card</h5>
                            <p class="card-text">Gradient maintains warning color while showing image underneath.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card with Image Overlay -->
        <div class="mb-5">
            <h2 class="mb-3">7️⃣ Card with Image Overlay</h2>
            <p class="text-muted mb-3">Text overlay on top of a background image with gradient - perfect for hero sections.</p>
            <div class="card text-bg-dark shadow-sm" style="max-width: 540px;">
                <img src="https://picsum.photos/540/300?random=12" class="card-img" alt="Overlay Image">
                <div class="card-img-overlay d-flex flex-column justify-content-end" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                    <h5 class="card-title">Card with Image Overlay</h5>
                    <p class="card-text">This is a text overlay on the image with gradient background for better readability. The background image remains visible through the gradient.</p>
                    <p class="card-text"><small>Last updated 3 mins ago</small></p>
                </div>
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
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold">Card link</a>
                    <a href="#" class="card-link text-decoration-none text-secondary fw-bold">Another link</a>
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
                            <a href="#" class="card-link text-decoration-none text-secondary fw-bold">View Details</a>
                        </div>
                        <img src="https://picsum.photos/400/200?random=15" class="card-img-bottom" alt="Bottom 1">
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Bottom Image Card 2</h5>
                            <p class="card-text">This layout prioritizes text content and ends with a visual element.</p>
                            <a href="#" class="card-link text-decoration-none text-secondary fw-bold">View Details</a>
                        </div>
                        <img src="https://picsum.photos/400/200?random=16" class="card-img-bottom" alt="Bottom 2">
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Bottom Image Card 3</h5>
                            <p class="card-text">Great for emphasizing content over imagery in your design.</p>
                            <a href="#" class="card-link text-decoration-none text-secondary fw-bold">View Details</a>
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

        <!-- Reference Links -->
        <div class="alert alert-info mt-5" role="alert">
            <h4 class="alert-heading"><i class="fa-solid fa-book"></i> Reference Materials</h4>
            <hr>
            <ul class="mb-0">
                <li><a href="https://getbootstrap.com/docs/5.3/components/card/" target="_blank" class="alert-link">Bootstrap Official Card Documentation</a></li>
                <li><a href="https://getbootstrap.com/docs/5.3/utilities/" target="_blank" class="alert-link">Bootstrap Utility CSS Guide</a></li>
                <li><a href="/docs/design/bootstrap.md" class="alert-link">Project Bootstrap Design Guide</a></li>
            </ul>
        </div>

    </section>
</div>
