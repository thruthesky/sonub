<!-- 개발자 빠른 로그인 패널 -->
<div class="fixed-bottom bg-light border-top border-primary border-2 shadow-lg py-2">
    <div class="container">
        <div class="d-flex align-items-center justify-content-center gap-2">
            <span class="badge bg-primary">DEV</span>
            <button onclick="login_as('apple')" class="btn btn-outline-primary btn-sm">A</button>
            <button onclick="login_as('banana')" class="btn btn-outline-success btn-sm">B</button>
            <button onclick="login_as('cherry')" class="btn btn-outline-danger btn-sm">C</button>
            <button onclick="login_as('date')" class="btn btn-outline-warning btn-sm">D</button>
            <button onclick="login_as('elderberry')" class="btn btn-outline-info btn-sm">E</button>
            <button onclick="login_as('fig')" class="btn btn-outline-secondary btn-sm">F</button>
            <button onclick="login_as('grape')" class="btn btn-outline-primary btn-sm">G</button>
            <button onclick="login_as('honeydew')" class="btn btn-outline-success btn-sm">H</button>
            <button onclick="login_as('jackfruit')" class="btn btn-outline-danger btn-sm">J</button>
            <button onclick="login_as('kiwi')" class="btn btn-outline-warning btn-sm">K</button>
            <button onclick="login_as('lemon')" class="btn btn-outline-info btn-sm">L</button>
            <button onclick="login_as('mango')" class="btn btn-outline-secondary btn-sm">M</button>
        </div>
    </div>
</div>

<script>
    async function login_as(user_id) {
        const user = await login_email_password(user_id + '@test.com', '12345a,*');
        await func('login_with_firebase', {
            firebase_uid: user.uid,
            alertOnError: true,
        });
        // 로그인 성공 후 리다이렉션
        window.location.href = '<?= href()->home ?>';
    }
</script>