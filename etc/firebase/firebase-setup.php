<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-app-compat.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-auth-compat.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging-compat.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/12.3.0/firebase-database-compat.js"></script>

<script>
    // Your web app's Firebase configuration
    <?php include etc_folder('config/firebase-web-config'); ?>
    const firebaseConfig = <?php echo json_encode(firebaseConfig); ?>;

    // Initialize Firebase
    ready(() => {
        firebase.initializeApp(firebaseConfig);
    });
</script>