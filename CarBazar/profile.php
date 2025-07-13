<?php
// Set a specific title for this page
$pageTitle = "My Profile - CarBazar";

// Include the header. No need for special CSS here anymore.
include 'includes/header.php';

// Security Check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch user data
$stmt = $conn->prepare("SELECT full_name, username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!-- START: This is the unique HTML content for the profile page -->
<div class="container">
    <div class="form-container">
        
        <h2>My Profile</h2>
        <p>Update your personal information below.</p>
        
        <?php 
        // Display any success or error messages
        if(isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<p class="message success">Profile updated successfully!</p>';
        }
        if(isset($_GET['error'])) {
            echo '<p class="message error">' . htmlspecialchars($_GET['error']) . '</p>';
        } 
        ?>

        <form action="api/update_profile.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address (cannot be changed)</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            
            <button type="submit" class="submit-btn" style="background-color:#007bff; color:#fff;">Update Profile</button>
        </form>

    </div>
</div>
<!-- END: Unique page content -->

<?php
// Include the footer
include 'includes/footer.php'; 
?>