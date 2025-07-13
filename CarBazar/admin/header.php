<?php
session_start();

// Security check: This is a critical security measure.
// It verifies that a user is logged in AND that they are marked as an admin.
// If either of these conditions is false, it immediately redirects them to the public login page.
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php?error=Access Denied. Admin privileges required.");
    exit; // Always exit after a header redirect to stop script execution.
}

// Include the database connection file.
require_once '../db_connect.php';

// Get the filename of the current page (e.g., 'index.php', 'manage_cars.php').
// This is used to dynamically add the 'active' class to the correct sidebar link for styling.
$current_page = basename($_SERVER['PHP_SELF']);

// Set a default page title. This variable can be overwritten by the page that includes this header.
// For example, manage_cars.php will set $pageTitle = "Manage Cars"; before including this file.
$pageTitle = $pageTitle ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - CarBazar Admin</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>CarBazar Admin</h3>
            </div>
            <ul class="sidebar-menu">
                <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <a href="index.php">Dashboard</a>
                </li>
                <li class="<?php echo ($current_page == 'manage_cars.php') ? 'active' : ''; ?>">
                    <a href="manage_cars.php">Manage Cars</a>
                </li>
                <li class="<?php echo ($current_page == 'manage_showrooms.php') ? 'active' : ''; ?>">
                    <a href="manage_showrooms.php">Manage Showrooms</a>
                </li>
                <li class="<?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">
                    <a href="manage_users.php">Manage Users</a>
                </li>
                <li>
                    <a href="../index.php">View Public Site</a>
                </li>
                <li>
                    <a href="../logout.php">Logout</a>
                </li>
            </ul>
        </aside>
        
        <main class="main-content">
            <header class="content-header">
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            </header>
            
            <section class="content-body">
                <!--
                    The content of each specific admin page (like the tables for cars or users)
                    will start immediately after this header file is included. This file opens
                    all the main layout tags. The including page is responsible for closing them.
                -->