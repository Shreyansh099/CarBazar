<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db_connect.php';
$pageTitle = $pageTitle ?? 'CarBazar - Great Deals on Wheels'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <?php if (isset($extra_styles)) { echo $extra_styles; } ?>
</head>
<body>
    <header>
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <img src="images/CarBazar-logo.png" alt="CarBazar Logo">
            </a>
            
            <!-- We give the <nav> an ID and a data attribute for our JS -->
            <nav id="main-nav" data-visible="false">
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="showrooms.php">Showrooms</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <li><a href="sell_your_car.php" style="color:#28a745; font-weight: bold;">Sell Your Car</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="wishlist.php">Wishlist</a></li>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                            <li><a href="admin/index.php" style="color: #dc3545;">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="sell_your_car.php">Sell Your Car</a></li>
                        <li><a href="login.php" class="btn-login">Login</a></li>
                        <li><a href="register.php" class="btn-register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- This is the new Hamburger Menu button -->
            <button class="mobile-nav-toggle" aria-controls="main-nav" aria-expanded="false">
                <span class="sr-only">Menu</span>
            </button>
        </div>
        <?php if (isset($header_content) && $header_content === true): ?>
            <div class="header-content"><h1>Great Deals on Wheels</h1></div>
        <?php endif; ?>
    </header>
    <main>