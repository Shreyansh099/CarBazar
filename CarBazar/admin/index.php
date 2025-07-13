<?php 
$pageTitle = "Dashboard";
include 'header.php';

// Fetch main stats
$totalCars = $conn->query("SELECT COUNT(*) as count FROM cars")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")->fetch_assoc()['count'];
$totalShowrooms = $conn->query("SELECT COUNT(*) as count FROM showrooms")->fetch_assoc()['count'];

// Fetch cars per showroom stat
$showroom_counts_result = $conn->query("
    SELECT s.city, s.listing_id, COUNT(c.id) as car_count 
    FROM showrooms s 
    LEFT JOIN cars c ON s.id = c.showroom_id 
    GROUP BY s.id 
    ORDER BY car_count DESC, s.city ASC
");
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; text-align:center;">
    <div style="background:#fff; padding: 2rem; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">Total Cars</h2>
        <p style="font-size: 2.5rem; font-weight:bold; margin:0; color: #007bff;"><?php echo $totalCars; ?></p>
    </div>
    <div style="background:#fff; padding: 2rem; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">Total Users</h2>
        <p style="font-size: 2.5rem; font-weight:bold; margin:0; color: #28a745;"><?php echo $totalUsers; ?></p>
    </div>
    <div style="background:#fff; padding: 2rem; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">Total Showrooms</h2>
        <p style="font-size: 2.5rem; font-weight:bold; margin:0; color: #e67e22;"><?php echo $totalShowrooms; ?></p>
    </div>
</div>

<div style="margin-top: 3rem; background: #fff; padding: 2rem; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h2 style="text-align:left; margin-top:0;">Inventory by Showroom</h2>
    <table>
        <thead>
            <tr>
                <th>Showroom City (ID)</th>
                <th>Number of Cars in Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($showroom_counts_result && $showroom_counts_result->num_rows > 0): ?>
                <?php while($row = $showroom_counts_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['city'] . ' (' . $row['listing_id'] . ')'); ?></td>
                        <td><strong><?php echo $row['car_count']; ?></strong></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="2">No showrooms found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $conn->close(); ?>
            </section> <!-- Closes .content-body -->
        </main> <!-- Closes .main-content -->
    </div> <!-- Closes .admin-wrapper -->
</body>
</html>