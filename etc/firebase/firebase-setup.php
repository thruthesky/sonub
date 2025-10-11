<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-app-compat.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-auth-compat.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging-compat.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-database-compat.js"></script>

<script>
    // Your web app's Firebase configuration
    <?php include ROOT_DIR . '/etc/config/firebase-web-config.php'; ?>
    const firebaseConfig = <?php echo json_encode(firebaseConfig); ?>;

    console.log('Firebase Config:', firebaseConfig);
    // Initialize Firebase
    firebase_ready(() => {

        firebase.initializeApp(firebaseConfig);

        console.log('Firebase initialized:', firebase.app().name);

        // Check if a user is already signed in
        firebase.auth().onAuthStateChanged((user) => {

            if (user) {

                // User is signed in.
                console.log('User is signed in:', user);

                // You can access user information here
                const uid = user.uid;
                const email = user.email;
                const displayName = user.displayName;

                console.log('User UID:', uid);
                // console.log('Email:', email);
                // console.log('Display Name:', displayName);

                // // You can also get the ID token if needed
                // user.getIdToken().then((idToken) => {
                //     console.log('ID Token:', idToken);
                //     // Send the ID token to your server for verification if needed
                // });

            } else {
                // No user is signed in.
                console.log('No user is signed in.');
            }
        });
    })
</script>