<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="text-center py-5">
                <!-- 404 Error Icon -->
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="currentColor" class="bi bi-exclamation-triangle text-warning" viewBox="0 0 16 16">
                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/>
                    </svg>
                </div>

                <!-- Error Message -->
                <h1 class="display-1 fw-bold text-danger">404</h1>
                <h2 class="mb-3">Page Not Found</h2>

                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">  Oops! Something went wrong.</h4>
                    <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                    <hr>
                    <p class="mb-0">
                        <strong>Possible reasons:</strong>
                    </p>
                    <ul class="text-start mt-2">
                        <li>The URL might be mistyped</li>
                        <li>The page may have been moved or deleted</li>
                        <li>You may not have permission to access this page</li>
                        <li>The server might be experiencing technical difficulties</li>
                    </ul>
                </div>

                <!-- Helpful Actions -->
                <div class="mt-4">
                    <h5>What can you do?</h5>
                    <div class="d-grid gap-2 d-md-block mt-3">
                        <a href="/" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill me-2" viewBox="0 0 16 16">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z"/>
                                <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6Z"/>
                            </svg>
                            Go to Homepage
                        </a>
                        <button onclick="history.back()" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Go Back
                        </button>
                        <a href="/contact" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope me-2" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                            </svg>
                            Contact Support
                        </a>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-5 p-3 bg-light rounded">
                    <p class="text-muted mb-0">
                        <small>
                            <strong>Error Code:</strong> 404 |
                            <strong>Time:</strong> <?= date('Y-m-d H:i:s') ?> |
                            <strong>Request URL:</strong> <?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Unknown') ?>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>