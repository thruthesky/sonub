<!-- 개발자 빠른 로그인 패널 - 드롭다운 형태 -->
<div class="fixed-bottom" style="right: 20px; left: auto; bottom: 20px; width: auto;">
    <div class="dropdown dropup">
        <button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" type="button" id="devLoginDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 36px; height: 36px; padding: 0;">
            <i class="fa-solid fa-user-shield" style="font-size: 0.875rem;"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="devLoginDropdown">
            <li>
                <h6 class="dropdown-header"><i class="fa-solid fa-code"></i> Dev Login</h6>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#" onclick="login_as('apple'); return false;"><i class="fa-solid fa-a text-primary"></i> Apple</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('banana'); return false;"><i class="fa-solid fa-b text-success"></i> Banana</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('cherry'); return false;"><i class="fa-solid fa-c text-danger"></i> Cherry</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('date'); return false;"><i class="fa-solid fa-d text-warning"></i> Date</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('elderberry'); return false;"><i class="fa-solid fa-e text-info"></i> Elderberry</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('fig'); return false;"><i class="fa-solid fa-f text-secondary"></i> Fig</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('grape'); return false;"><i class="fa-solid fa-g text-primary"></i> Grape</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('honeydew'); return false;"><i class="fa-solid fa-h text-success"></i> Honeydew</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('jackfruit'); return false;"><i class="fa-solid fa-j text-danger"></i> Jackfruit</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('kiwi'); return false;"><i class="fa-solid fa-k text-warning"></i> Kiwi</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('lemon'); return false;"><i class="fa-solid fa-l text-info"></i> Lemon</a></li>
            <li><a class="dropdown-item" href="#" onclick="login_as('mango'); return false;"><i class="fa-solid fa-m text-secondary"></i> Mango</a></li>
        </ul>
    </div>
</div>

<script>
    async function login_as(user_id) {
        const user = await login_email_password(user_id + '@test.com', '12345a,*');
        await func('login_with_firebase', {
            firebase_uid: user.uid,
            phone_number: user.phoneNumber || '12345a,*',
            alertOnError: true,
        });
        // 로그인 성공 후 리다이렉션
        window.location.href = '<?= href()->home ?>';
    }
</script>