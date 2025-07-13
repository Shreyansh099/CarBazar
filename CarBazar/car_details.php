<?php
$pageTitle = "Car Details"; // The title will be updated by JS
ob_start(); // Capture CSS for the header
?>
<style>.image-gallery{display:flex;flex-direction:column;gap:1rem}.main-image-container{background-color:#f1f1f1;display:flex;align-items:center;justify-content:center;border-radius:8px;overflow:hidden;min-height:450px;border:1px solid #ddd}.main-image{max-width:100%;max-height:500px;display:block;object-fit:contain}.thumbnail-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(80px,1fr));gap:10px}.thumbnail{width:100%;height:80px;object-fit:cover;border:3px solid transparent;border-radius:5px;cursor:pointer;transition:all .2s;background-color:#eee}.thumbnail:hover{border-color:#007bff;transform:scale(1.05)}.thumbnail.active{border-color:#0056b3;box-shadow:0 0 8px rgba(0,86,179,.5)}</style>
<?php
$extra_styles = ob_get_clean();
include 'includes/header.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $redirect_url = "login.php";
    if(isset($_GET['id'])) { $redirect_url .= "?redirect=car_details.php?id=" . (int)$_GET['id']; }
    header("location: " . $redirect_url);
    exit;
}
?>
<!-- START: Page-specific content -->
<div class="container" id="car-detail-content" style="padding: 2rem 0;">
    <p>Loading car details...</p>
</div>
<script src="js/details.js"></script>
<!-- END: Page-specific content -->
<?php
include 'includes/footer.php';
?>