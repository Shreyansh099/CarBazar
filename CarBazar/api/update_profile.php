<?php
session_start();
if (!isset($_SESSION["loggedin"])) { exit('Access Denied'); }
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $user_id = $_SESSION['user_id'];
    
    // Check if new username is already taken by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: ../profile.php?error=Username is already taken."); exit();
    }
    $stmt->close();
    
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ? WHERE id = ?");
    $stmt->bind_param("ssi", $full_name, $username, $user_id);
    if ($stmt->execute()) {
        $_SESSION['username'] = $username; // Update session
        header("Location: ../profile.php?status=success");
    } else {
        header("Location: ../profile.php?error=Update failed.");
    }
    $stmt->close();
    $conn->close();
}