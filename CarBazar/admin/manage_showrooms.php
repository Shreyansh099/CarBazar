<?php
$pageTitle = "Manage Showrooms";
include 'header.php';

$showrooms_result = $conn->query("SELECT * FROM showrooms ORDER BY city, listing_id ASC");
?>

<button class="btn btn-primary" onclick="openModal('addShowroomModal')">Add New Showroom</button>
<br><br>

<?php if(isset($_GET['status'])) echo '<p style="color:green; font-weight:bold;">Operation successful!</p>'; ?>
<?php if(isset($_GET['error'])) echo '<p style="color:red; font-weight:bold;">Error: '.htmlspecialchars($_GET['error']).'</p>'; ?>

<table>
    <thead><tr><th>Image</th><th>Listing ID</th><th>City</th><th>Address</th><th>Actions</th></tr></thead>
    <tbody>
        <?php if ($showrooms_result && $showrooms_result->num_rows > 0): while($showroom = $showrooms_result->fetch_assoc()): ?>
        <tr>
            <td><img src="../<?php echo htmlspecialchars($showroom['image_url'] ?: 'images/default-placeholder.png'); ?>" width="100"></td>
            <td><?php echo htmlspecialchars($showroom['listing_id']); ?></td>
            <td><?php echo htmlspecialchars($showroom['city']); ?></td>
            <td><?php echo htmlspecialchars($showroom['address']); ?></td>
            <td>
                <button class="btn btn-warning" onclick='openEditModal(<?php echo json_encode($showroom); ?>)'>Edit</button>
                <a href="../api/delete_showroom.php?id=<?php echo $showroom['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this showroom? This cannot be undone.')">Delete</a>
            </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="5">No showrooms found. Add one to get started.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Add Showroom Modal -->
<div id="addShowroomModal" class="modal"><div class="modal-content"><span class="close-btn" onclick="closeModal('addShowroomModal')">×</span><h2>Add New Showroom</h2><form action="../api/add_showroom.php" method="POST" enctype="multipart/form-data"><div class="form-group"><label>Listing ID (e.g., NYC-01)</label><input type="text" name="listing_id" required></div><div class="form-group"><label>City</label><input type="text" name="city" required></div><div class="form-group"><label>Address</label><input type="text" name="address" required></div><div class="form-group"><label>Phone</label><input type="text" name="phone" required></div><div class="form-group"><label>Manager Name</label><input type="text" name="manager_name"></div><div class="form-group"><label>Showroom Image</label><input type="file" name="showroom_image" accept="image/*"></div><button type="submit" class="btn btn-primary">Add Showroom</button></form></div></div>

<!-- Edit Showroom Modal -->
<div id="editShowroomModal" class="modal"><div class="modal-content"><span class="close-btn" onclick="closeModal('editShowroomModal')">×</span><h2>Edit Showroom</h2><form action="../api/update_showroom.php" method="POST" enctype="multipart/form-data"><input type="hidden" name="showroom_id" id="edit-showroom-id"><div class="form-group"><label>Listing ID</label><input type="text" id="edit-listing-id" name="listing_id" required></div><div class="form-group"><label>City</label><input type="text" id="edit-city" name="city" required></div><div class="form-group"><label>Address</label><input type="text" id="edit-address" name="address" required></div><div class="form-group"><label>Phone</label><input type="text" id="edit-phone" name="phone" required></div><div class="form-group"><label>Manager Name</label><input type="text" id="edit-manager" name="manager_name"></div><div class="form-group"><label>Current Image:</label><img id="edit-current-image" src="" width="100"></div><div class="form-group"><label>Upload New Image (optional, will replace the old one)</label><input type="file" name="showroom_image" accept="image/*"></div><button type="submit" class="btn btn-primary">Update Showroom</button></form></div></div>

<script>
function openModal(modalId) { document.getElementById(modalId).style.display = "block"; }
function closeModal(modalId) { document.getElementById(modalId).style.display = "none"; }
function openEditModal(showroom) {
    document.getElementById('edit-showroom-id').value = showroom.id;
    document.getElementById('edit-listing-id').value = showroom.listing_id;
    document.getElementById('edit-city').value = showroom.city;
    document.getElementById('edit-address').value = showroom.address;
    document.getElementById('edit-phone').value = showroom.phone;
    document.getElementById('edit-manager').value = showroom.manager_name;
    document.getElementById('edit-current-image').src = showroom.image_url ? '../' + showroom.image_url : '../images/default-placeholder.png';
    openModal('editShowroomModal');
}
window.onclick = function(event) { if (event.target.classList.contains('modal')) { closeModal(event.target.id); } }
</script>

<?php $conn->close(); ?>
            </section>
        </main>
    </div>
</body>
</html>