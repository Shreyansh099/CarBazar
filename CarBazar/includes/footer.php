<?php
// This check ensures we have a DB connection, even if footer is used alone.
if (!isset($conn) || !is_object($conn) || get_class($conn) !== 'mysqli') {
    require_once __DIR__ . '/../db_connect.php';
}

// Fetch up to 4 showrooms to display in the footer links
$footer_showrooms_result = $conn->query("SELECT id, city, listing_id FROM showrooms ORDER BY RAND() LIMIT 4");
?>
        </main> <!-- Closes the <main> tag from header.php -->

    <footer class="site-footer">
        <div class="container">
            <div class="footer-main">
                
                <!-- Column 1: Brand and About -->
                <div class="footer-column">
                    <a href="index.php" class="footer-logo">CarBazar</a>
                    <p>Your premier destination for high-quality, pre-owned vehicles. We are dedicated to making your car buying experience transparent, secure, and enjoyable.</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="footer-column">
                    <h4>Navigate</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="showrooms.php">All Showrooms</a></li>
                        <li><a href="sell_your_car.php">Sell Your Car</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Column 3: Featured Locations -->
                <div class="footer-column">
                    <h4>Featured Locations</h4>
                    <ul class="footer-links">
                        <?php if ($footer_showrooms_result && $footer_showrooms_result->num_rows > 0): ?>
                            <?php while($row = $footer_showrooms_result->fetch_assoc()): ?>
                                <li class="location-link">
                                    <a href="cars_by_showroom.php?id=<?php echo $row['id']; ?>">
                                        <i class="fa-solid fa-location-dot"></i><?php echo htmlspecialchars($row['city']); ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>No locations listed.</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Column 4: Legal -->
                <div class="footer-column">
                    <h4>Legal</h4>
                     <ul class="footer-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>

            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?php echo date("Y"); ?> CarBazar. All Rights Reserved.</p>
        </div>
    </footer>
    <!-- This ensures the navigation script is loaded on every page -->
    <script src="js/navigation.js"></script>

    <!-- This closes the body and html tags opened in header.php -->
</body>
</html>