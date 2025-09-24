<script src="https://www.gstatic.com/firebasejs/12.3.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/12.3.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/12.3.0/firebase-database-compat.js"></script>

<script>
    // Your web app's Firebase configuration
    <?php include etc_folder('config/firebase-web-config'); ?>
    const firebaseConfig = <?php echo json_encode(firebaseConfig); ?>;

    // Initialize Firebase

    firebase.initializeApp(firebaseConfig);

    console.log('Firebase initialized:', firebase.app().name);

    ready(() => {
        // Check if a user is already signed in
        firebase.auth().onAuthStateChanged((user) => {

            if (user) {

                console.log('User is signed in:', user.uid);

                // * 로그인 상태 유틸리티 표시
                document.querySelectorAll('[show-on-login]').forEach(el => {
                    el.style.setProperty('display', 'inline-block', 'important');
                });
                // * 로그아웃 요소를 없앤다
                document.querySelectorAll('[show-on-not-login]').forEach(el => {
                    el.style.setProperty('display', 'none', 'important');
                });

            } else {
                // * 로그아웃 상태 유틸리티 표시
                document.querySelectorAll('[show-on-not-login]').forEach(el => {
                    el.style.setProperty('display', 'inline-block', 'important');
                });

                // * 로그인 요소를 없앤다
                document.querySelectorAll('[show-on-login]').forEach(el => {
                    el.style.setProperty('display', 'none', 'important');
                });
            }
        });


    });
</script>