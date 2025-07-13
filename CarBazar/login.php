<?php
session_start();
// If user is already logged in, redirect them to the homepage.
if(isset($_SESSION["loggedin"])){
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CarBazar</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header><div class="container"><a href="index.php" class="logo">CarBazar</a></div></header>
<main>
    <div class="form-container">
        <h2>Login to Your Account</h2>
        <?php 
            if(isset($_GET['status']) && $_GET['status'] == 'reg_success') echo '<p class="message success">Registration successful! Please login.</p>';
            if(isset($_GET['error'])) echo '<p class="message error">'.htmlspecialchars($_GET['error']).'</p>';
            if(isset($_GET['redirect'])) echo '<p class="message info">Please log in to continue.</p>';
        ?>
        <form action="api/login_user.php" method="POST">
            
            <?php
            // This hidden input holds the page URL we want to go back to after logging in.
            if (isset($_GET['redirect'])) {
                echo '<input type="hidden" name="redirect" value="' . htmlspecialchars($_GET['redirect']) . '">';
            }
            ?>
            
            <div class="form-group"><label for="identifier">Username or Email</label><input type="text" name="identifier" required></div>
            <div class="form-group"><label for="password">Password</label><input type="password" name="password" required></div>
            <button type="submit" class="submit-btn" style="background-color: #28a745; color: #fff;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</main>
</body>
</html>