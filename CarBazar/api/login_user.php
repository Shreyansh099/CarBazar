<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password_hash, is_blocked, is_admin FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['is_blocked']) {
            header("Location: ../login.php?error=Your account has been suspended.");
            exit();
        }

        if (password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];

            // This is the new, smart redirect logic.
            if (!empty($_POST['redirect'])) {
                // If a redirect URL was passed, go there.
                header("Location: ../" . $_POST['redirect']);
            } else {
                // Otherwise, go to the default homepage.
                header("Location: ../index.php");
            }
            exit();
        }
    }

    // If login fails for any reason, redirect back with a generic error and the original redirect path.
    $error_url = "../login.php?error=Invalid username, email, or password.";
    if (!empty($_POST['redirect'])) {
        $error_url .= "&redirect=" . urlencode($_POST['redirect']);
    }
    header("Location: " . $error_url);
    exit();
    
    $stmt->close();
    $conn->close();
}