<?php
session_start();
// Security check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { 
    exit('Access Denied'); 
}
require_once '../db_connect.php';

// Helper function to process an image upload
function process_image_upload($file_key) {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]["error"] == 0) {
        $target_dir = "../images/cars/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $unique_filename = uniqid($file_key . '_', true) . '.' . strtolower(pathinfo(basename($_FILES[$file_key]["name"]), PATHINFO_EXTENSION));
        $target_file = $target_dir . $unique_filename;
        if (move_uploaded_file($_FILES[$file_key]["tmp_name"], $target_file)) {
            return 'images/cars/' . $unique_filename;
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- STEP 1: GATHER AND SANITIZE ALL DATA ---
    $car_id = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;
    if ($car_id <= 0) { header("Location: ../admin/manage_cars.php?error=Invalid_Car_ID"); exit; }
    
    $listing_id = trim($_POST['listing_id']);
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $fuel_type = trim($_POST['fuel_type']);
    $description = trim($_POST['description']);
    
    $showroom_id = !empty($_POST['showroom_id']) ? (int)$_POST['showroom_id'] : null;
    $year = (int)$_POST['year'];
    $km_driven = !empty($_POST['km_driven']) ? (int)$_POST['km_driven'] : null;
    $owner_count = !empty($_POST['owner_count']) ? (int)$_POST['owner_count'] : null;
    $price = (float)$_POST['price'];

    // --- STEP 2: HANDLE IMAGE UPDATES ---
    $stmt_get = $conn->prepare("SELECT front_image, image_1, image_2, image_3, image_4 FROM cars WHERE id = ?");
    $stmt_get->bind_param("i", $car_id);
    $stmt_get->execute();
    $current_images = $stmt_get->get_result()->fetch_assoc();
    $stmt_get->close();

    $image_fields = ['front_image', 'image_1', 'image_2', 'image_3', 'image_4'];
    $final_image_paths = [];
    foreach ($image_fields as $field) {
        $new_path = process_image_upload($field);
        if ($new_path) {
            if (!empty($current_images[$field]) && file_exists('../' . $current_images[$field])) { unlink('../' . $current_images[$field]); }
            $final_image_paths[$field] = $new_path;
        } else {
            $final_image_paths[$field] = $current_images[$field];
        }
    }
    
    // --- STEP 3: PREPARE AND EXECUTE THE FINAL DATABASE QUERY ---
    $sql = "UPDATE cars SET 
                listing_id = ?, make = ?, model = ?, year = ?, showroom_id = ?,
                fuel_type = ?, km_driven = ?, owner_count = ?, price = ?, description = ?, 
                front_image = ?, image_1 = ?, image_2 = ?, image_3 = ?, image_4 = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    if ($stmt === false) { header("Location: ../admin/manage_cars.php?error=SQL_Prepare_Error"); exit(); }
    
    // --- THIS IS THE CORRECTED BIND_PARAM LINE ---
    // The order of variables and the types string now perfectly match the SQL query.
    // s(string), i(integer), d(double/float)
    $stmt->bind_param("sssiisiddssssssi", 
        $listing_id,
        $make,
        $model,
        $year,
        $showroom_id,
        $fuel_type,
        $km_driven,
        $owner_count,
        $price,
        $description,
        $final_image_paths['front_image'],
        $final_image_paths['image_1'],
        $final_image_paths['image_2'],
        $final_image_paths['image_3'],
        $final_image_paths['image_4'],
        $car_id
    );

    // --- STEP 4: REDIRECT BASED ON OUTCOME ---
    if ($stmt->execute()) { 
        header("Location: ../admin/manage_cars.php?status=update_success&id=" . $car_id); 
    } else {
        header("Location: ../admin/manage_cars.php?error=DB_Update_Error: ". urlencode($stmt->error)); 
    }
    
    $stmt->close();
    $conn->close();
}