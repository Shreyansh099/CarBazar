<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { exit('Access Denied'); }
require_once '../db_connect.php';

function process_image_upload($file_key) {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]["error"] == 0) {
        $target_dir = "../images/cars/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $unique_filename = uniqid($file_key . '_', true) . '.' . strtolower(pathinfo(basename($_FILES[$file_key]["name"]), PATHINFO_EXTENSION));
        $target_file = $target_dir . $unique_filename;
        if (move_uploaded_file($_FILES[$file_key]["tmp_name"], $target_file)) { return 'images/cars/' . $unique_filename; }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $listing_id = trim($_POST['listing_id']);
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $showroom_id = !empty($_POST['showroom_id']) ? (int)$_POST['showroom_id'] : NULL;
    $year = (int)$_POST['year'];
    $fuel_type = trim($_POST['fuel_type']);
    $km_driven = !empty($_POST['km_driven']) ? (int)$_POST['km_driven'] : NULL;
    $owner_count = !empty($_POST['owner_count']) ? (int)$_POST['owner_count'] : NULL;
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    
    $front_image = process_image_upload('front_image');
    $image_1 = process_image_upload('image_1');
    $image_2 = process_image_upload('image_2');
    $image_3 = process_image_upload('image_3');
    $image_4 = process_image_upload('image_4');
    
    $sql = "INSERT INTO cars (listing_id, showroom_id, make, model, year, fuel_type, km_driven, owner_count, price, description, front_image, image_1, image_2, image_3, image_4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssiidisssss", $listing_id, $showroom_id, $make, $model, $year, $fuel_type, $km_driven, $owner_count, $price, $description, $front_image, $image_1, $image_2, $image_3, $image_4);

    if ($stmt->execute()) { header("Location: ../admin/manage_cars.php?status=add_success"); }
    else { header("Location: ../admin/manage_cars.php?error=DB_Error: " . $stmt->error); }
    $stmt->close();
    $conn->close();
}