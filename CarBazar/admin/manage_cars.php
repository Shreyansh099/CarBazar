<?php
$pageTitle = "Manage Cars";
include 'header.php';

$showroom_filter_id = isset($_GET['showroom_id']) ? (int)$_GET['showroom_id'] : 0;
$sql_where_clause = "";
if ($showroom_filter_id > 0) {
    $sql_where_clause = " WHERE c.showroom_id = " . $showroom_filter_id;
} else if (isset($_GET['showroom_id']) && $_GET['showroom_id'] === 'unassigned') {
    $sql_where_clause = " WHERE c.showroom_id IS NULL ";
}
$cars_query = "SELECT c.*, s.city AS showroom_city 
               FROM cars c 
               LEFT JOIN showrooms s ON c.showroom_id = s.id 
               {$sql_where_clause} 
               ORDER BY c.date_posted DESC";
$cars_result = $conn->query($cars_query);

$showrooms_result = $conn->query("SELECT id, city, listing_id FROM showrooms ORDER BY city");
$showroom_options = [];
if ($showrooms_result) { while($row = $showrooms_result->fetch_assoc()) { $showroom_options[] = $row; } }
?>
<div style="background:#fff; padding:1rem; margin-bottom: 2rem; border-radius: 5px; display:flex; justify-content:space-between; align-items:center;">
    <form method="GET" action="manage_cars.php"><label for="showroom_filter" style="font-weight:bold;">Filter:</label>
        <select name="showroom_id" id="showroom_filter" onchange="this.form.submit()">
            <option value="0">-- All --</option><option value="unassigned" <?php if (isset($_GET['showroom_id'])&&$_GET['showroom_id']==='unassigned') echo 'selected'; ?>>Unassigned</option>
            <?php foreach ($showroom_options as $showroom): ?><option value="<?php echo $showroom['id']; ?>" <?php if ($showroom_filter_id == $showroom['id']) echo 'selected'; ?>><?php echo htmlspecialchars($showroom['city'].' ('.$showroom['listing_id'].')'); ?></option><?php endforeach; ?>
        </select>
    </form>
    <button class="btn btn-primary" onclick="openModal('addCarModal')">Add New Car</button>
</div>
<?php if(isset($_GET['status'])) echo '<p style="color:green; font-weight:bold;">Success!</p>'; ?>
<?php if(isset($_GET['error'])) echo '<p style="color:red; font-weight:bold;">Error: '.htmlspecialchars($_GET['error']).'</p>'; ?>
<table>
    <thead><tr><th>Listing ID</th><th>Image</th><th>Make & Model</th><th>KM Driven</th><th>Location</th><th>Actions</th></tr></thead>
    <tbody>
        <?php if ($cars_result && $cars_result->num_rows > 0): while($car = $cars_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($car['listing_id']); ?></td>
            <td><img src="../<?php echo htmlspecialchars($car['front_image']?:'images/default-placeholder.png'); ?>" width="100"></td>
            <td><?php echo htmlspecialchars($car['make'].' '.$car['model']); ?><br><small><?php echo htmlspecialchars($car['year']); ?></small></td>
            <td><?php echo number_format($car['km_driven'] ?? 0); ?> km</td>
            <td><strong><?php echo htmlspecialchars($car['showroom_city']?:'Unassigned'); ?></strong></td>
            <td><button class="btn btn-warning" onclick='openEditModal(<?php echo json_encode($car); ?>)'>Edit</button><a href="../api/delete_car.php?id=<?php echo $car['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6">No cars found for the selected filter.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<!-- Add Car Modal -->
<div id="addCarModal" class="modal"><div class="modal-content"><span class="close-btn" onclick="closeModal('addCarModal')">×</span><h2>Add New Car</h2><form action="../api/add_car.php" method="POST" enctype="multipart/form-data">
    <div class="form-group"><label>Listing ID</label><input type="text" name="listing_id" required></div><div class="form-group"><label>Make</label><input type="text" name="make" required></div><div class="form-group"><label>Model</label><input type="text" name="model" required></div><div class="form-group"><label>Showroom</label><select name="showroom_id"><option value="">Unassigned</option><?php foreach($showroom_options as $showroom): ?><option value="<?php echo $showroom['id']; ?>"><?php echo htmlspecialchars($showroom['city'].' ('.$showroom['listing_id'].')'); ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Year</label><input type="number" name="year" required></div>
    <fieldset style="border:1px solid #ddd; padding:10px; margin-bottom:1rem; border-radius:5px;"><legend><strong>Specifications</strong></legend><div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;"><div class="form-group"><label>Fuel Type</label><select name="fuel_type"><option selected>Petrol</option><option>Diesel</option><option>Gas</option><option>Petrol + Gas</option><option>Electric</option></select></div><div class="form-group"><label>KM Driven</label><input type="number" name="km_driven"></div><div class="form-group"><label>No. of Owners</label><input type="number" name="owner_count"></div></div></fieldset>
    <div class="form-group"><label>Price (₹)</label><input type="number" step="0.01" name="price" required></div><div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
    <fieldset style="border:1px solid #ddd; padding:10px; margin:1rem 0; border-radius:5px;"><legend><strong>Images</strong></legend><div class="form-group"><label>Front Image (Cover)</label><input type="file" name="front_image"></div><div class="form-group"><label>Image 1</label><input type="file" name="image_1"></div><div class="form-group"><label>Image 2</label><input type="file" name="image_2"></div><div class="form-group"><label>Image 3</label><input type="file" name="image_3"></div><div class="form-group"><label>Image 4</label><input type="file" name="image_4"></div></fieldset>
    <button type="submit" class="btn btn-primary">Add Car</button></form></div></div>

<!-- Edit Car Modal -->
<div id="editCarModal" class="modal"><div class="modal-content"><span class="close-btn" onclick="closeModal('editCarModal')">×</span><h2>Edit Car</h2><form action="../api/update_car.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="car_id" id="edit-car-id"><div class="form-group"><label>Listing ID</label><input type="text" name="listing_id" id="edit-listing-id" required></div><div class="form-group"><label>Make</label><input type="text" name="make" id="edit-make" required></div><div class="form-group"><label>Model</label><input type="text" name="model" id="edit-model" required></div><div class="form-group"><label>Showroom</label><select name="showroom_id" id="edit-showroom-id-select"><option value="">Unassigned</option><?php foreach($showroom_options as $showroom): ?><option value="<?php echo $showroom['id']; ?>"><?php echo htmlspecialchars($showroom['city'].' ('.$showroom['listing_id'].')'); ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Year</label><input type="number" name="year" id="edit-year" required></div>
    <fieldset style="border:1px solid #ddd; padding:10px; margin-bottom:1rem; border-radius:5px;"><legend><strong>Specifications</strong></legend><div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;"><div class="form-group"><label>Fuel Type</label><select name="fuel_type" id="edit-fuel-type"><option>Petrol</option><option>Diesel</option><option>Gas</option><option>Petrol + Gas</option><option>Electric</option></select></div><div class="form-group"><label>KM Driven</label><input type="number" name="km_driven" id="edit-km-driven"></div><div class="form-group"><label>No. of Owners</label><input type="number" name="owner_count" id="edit-owner-count"></div></div></fieldset>
    <div class="form-group"><label>Price (₹)</label><input type="number" step="0.01" name="price" id="edit-price" required></div><div class="form-group"><label>Description</label><textarea name="description" id="edit-description"></textarea></div>
    <fieldset style="border:1px solid #ddd; padding:10px; margin:1rem 0; border-radius:5px;"><legend><strong>Images</strong></legend><p style="font-size:0.9rem; color:#555;">Only upload a file to replace the existing image.</p>
        <div class="form-group"><label>Front (Cover)</label><div style="display:flex;align-items:center;gap:10px;"><img id="edit-img-front" src="" width="80"><input type="file" name="front_image"></div></div>
        <div class="form-group"><label>Image 1</label><div style="display:flex;align-items:center;gap:10px;"><img id="edit-img-1" src="" width="80"><input type="file" name="image_1"></div></div>
        <div class="form-group"><label>Image 2</label><div style="display:flex;align-items:center;gap:10px;"><img id="edit-img-2" src="" width="80"><input type="file" name="image_2"></div></div>
        <div class="form-group"><label>Image 3</label><div style="display:flex;align-items:center;gap:10px;"><img id="edit-img-3" src="" width="80"><input type="file" name="image_3"></div></div>
        <div class="form-group"><label>Image 4</label><div style="display:flex;align-items:center;gap:10px;"><img id="edit-img-4" src="" width="80"><input type="file" name="image_4"></div></div>
    </fieldset>
    <button type="submit" class="btn btn-primary">Update Car</button></form></div></div>

<script>
function openModal(modalId) { document.getElementById(modalId).style.display = "block"; }
function closeModal(modalId) { document.getElementById(modalId).style.display = "none"; }
// THIS IS THE CORRECTED AND COMPLETE JAVASCRIPT FUNCTION
function openEditModal(car) {
    // It now sets the value for EVERY single form field.
    document.getElementById('edit-car-id').value = car.id;
    document.getElementById('edit-listing-id').value = car.listing_id;
    document.getElementById('edit-make').value = car.make;
    document.getElementById('edit-model').value = car.model;
    document.getElementById('edit-showroom-id-select').value = car.showroom_id;
    document.getElementById('edit-year').value = car.year;
    // --- THIS IS THE CRITICAL FIX ---
    document.getElementById('edit-fuel-type').value = car.fuel_type;
    document.getElementById('edit-km-driven').value = car.km_driven;
    document.getElementById('edit-owner-count').value = car.owner_count;
    // --- END OF FIX ---
    document.getElementById('edit-price').value = car.price;
    document.getElementById('edit-description').value = car.description;
    const placeholder = '../images/default-placeholder.png';
    document.getElementById('edit-img-front').src = car.front_image ? `../${car.front_image}` : placeholder;
    document.getElementById('edit-img-1').src = car.image_1 ? `../${car.image_1}` : placeholder;
    document.getElementById('edit-img-2').src = car.image_2 ? `../${car.image_2}` : placeholder;
    document.getElementById('edit-img-3').src = car.image_3 ? `../${car.image_3}` : placeholder;
    document.getElementById('edit-img-4').src = car.image_4 ? `../${car.image_4}` : placeholder;
    openModal('editCarModal');
}
window.onclick = function(event) { if (event.target.classList.contains('modal')) { closeModal(event.target.id); } }
</script>
<?php $conn->close(); ?>
</section></main></div></body></html>