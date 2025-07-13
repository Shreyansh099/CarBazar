<?php session_start(); if(isset($_SESSION["loggedin"])){ header("location: index.php"); exit; } ?>
<!DOCTYPE html><html lang="en"><head><title>Register - CarBazar</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<header><div class="container"><a href="index.php" class="logo">CarBazar</a></div></header>
<main>
    <div class="form-container">
        <h2>Create Your Account</h2>
        <?php if(isset($_GET['error'])) echo '<p class="message error">'.htmlspecialchars($_GET['error']).'</p>'; ?>
        <form action="api/register_user.php" method="POST">
            <div class="form-group"><label for="full_name">Full Name</label><input type="text" name="full_name" required></div>
            <div class="form-group"><label for="username">Username</label><input type="text" name="username" required></div>
            <div class="form-group"><label for="email">Email Address</label><input type="email" name="email" required></div>
            <div class="form-group"><label for="password">Password</label><input type="password" name="password" required></div>
            <button type="submit" class="submit-btn" style="background-color: #007bff; color: #fff;">Register</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</main>
</body></html>