<?php
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        header("Location: ../register.php?error=All fields are required."); exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../register.php?error=Invalid email format."); exit();
    }

    $sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header("Location: ../register.php?error=Username or Email already taken."); exit();
    }
    $stmt_check->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql_insert = "INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssss", $full_name, $username, $email, $password_hash);

    if ($stmt_insert->execute()) {
        header("Location: ../login.php?status=reg_success"); exit();
    } else {
        header("Location: ../register.php?error=Registration failed. Please try again."); exit();
    }
    $stmt_insert->close();
    $conn->close();
}