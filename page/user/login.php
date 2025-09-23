<script defer src="/js/phone-number.js"></script>

<script>
    ready(() => {
        Alpine.store('login', {
            step: 'input-phone-number', // 'input-sms-code', // 'input-phone-number', // input-sms-code
            loading: false,
        });
    })
</script>


<h1>User </h1>

<form x-data id="login-form" action="#" class="align-content">
    <section x-show="true">
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" class="form-control p-2 xl" id="phone_number" name="phone_number" required placeholder="Please enter your phone number" autofocus>
        </div>
        <small class="d-block text-muted">
            <i class="fa-solid fa-info-circle me-1"></i>
            09XX-XXX-XXXX
        </small>

        <nav>
            <button x-show="$store.login?.loading == false" id="sign-in-button" type="button" class="btn btn-primary my-5 px-3 py-2">Send SMS Code</button>
            <button x-show="$store.login?.loading == true" type="button" class="btn btn-primary my-5 px-3 py-2" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Sending...
            </button>
        </nav>
    </section>

    <section x-show="true">
        <div class="mb-3">
            <label for="sms_code" class="form-label">SMS Code</label>
            <input type="text" id="sms_code" class="form-control p-2 xl" required>
        </div>

        <nav>
            <button id="verify-code" type="button" class="btn btn-primary my-5 px-3 py-2">Input SMS Code</button>
            <button type="button" class="btn btn-primary my-5 px-3 py-2" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Sending...
            </button>
        </nav>
    </section>
</form>