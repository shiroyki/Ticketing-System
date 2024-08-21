<?php
    require __DIR__.'/auth-process.php';
    include_once('csrf-verify.php');include_once('auth.php');

    session_start();

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify the CSRF token
        $nonce = $_POST['nonce'];
        if (!csrf_verifyNonce('register', $nonce)) {
            die('CSRF verification failed.');
        }

        // Get the form data
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash the password
        $salt = uniqid('', true);
        $hashed_password = hash('sha256', $salt.$password);

        // Connect to the database
        $db = new PDO('sqlite:/var/www/tickets.db');

        // Check if the email is already in use
        $stmt = $db->prepare("SELECT COUNT(*) FROM ACCOUNT WHERE EMAIL=:email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            die('An account with that email address already exists.');
        }

        // Insert the new account into the database
        $stmt = $db->prepare("INSERT INTO ACCOUNT (EMAIL, SALT, HASHED_PASSWORD, ADMIN_FLAG) VALUES (:email, :salt, :hashed_password, 0)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':salt', $salt, PDO::PARAM_STR);
        $stmt->bindParam(':hashed_password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        // Redirect to the login page
        header('Location: login.php');
        exit();
    }
?>