<?php
session_start();
// Security Check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { 
    exit('Access Denied.'); 
}
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get all the data from the form
    $id = (int)$_POST['showroom_id'];
    $listing_id = trim($_POST['listing_id']);
    $city = trim($_POST['city']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $manager_name = trim($_POST['manager_name']);

    if ($id <= 0) {
        header("Location: ../admin/manage_showrooms.php?error=Invalid Showroom ID.");
        exit();
    }
    
    // 2. Fetch the current showroom record from the database
    // This is crucial to get the path of the old image, if it exists.
    $stmt_get = $conn->prepare("SELECT image_url FROM showrooms WHERE id = ?");
    $stmt_get->bind_param("i", $id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $current_showroom = $result->fetch_assoc();
    $stmt_get->close();

    // Store the old image path in a variable. It might be NULL.
    $image_path_for_db = $current_showroom['image_url']; 

    // 3. Check if a NEW image file was uploaded
    if (isset($_FILES["showroom_image"]) && $_FILES["showroom_image"]["error"] == 0) {
        
        // --- Start of new image processing ---
        $target_dir = "../images/showrooms/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        
        $original_filename = basename($_FILES["showroom_image"]["name"]);
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $unique_filename = uniqid('showroom_', true) . '.' . $imageFileType;
        $target_file = $target_dir . $unique_filename;
        
        // Validate that it's a real image
        $check = getimagesize($_FILES["showroom_image"]["tmp_name"]);
        if($check !== false) {
            // Attempt to move the new file to its destination
            if (move_uploaded_file($_FILES["showroom_image"]["tmp_name"], $target_file)) {
                
                // IMPORTANT: If upload is successful, delete the OLD image from the server
                if (!empty($image_path_for_db) && file_exists('../' . $image_path_for_db)) {
                    unlink('../' . $image_path_for_db);
                }
                
                // Now, update our variable to hold the path of the NEW image
                $image_path_for_db = 'images/showrooms/' . $unique_filename;
            }
        }
        // --- End of new image processing ---
    }
    
    // 4. Update the database record with all data
    // This query is now simple and always updates every field.
    // If no new image was uploaded, $image_path_for_db still holds the old value.
    $sql = "UPDATE showrooms SET 
                listing_id = ?, 
                city = ?, 
                address = ?, 
                phone = ?, 
                manager_name = ?, 
                image_url = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssi", // 6 strings, 1 integer
        $listing_id,
        $city,
        $address,
        $phone,
        $manager_name,
        $image_path_for_db,
        $id
    );

    if ($stmt->execute()) {
        header("Location: ../admin/manage_showrooms.php?status=update_success");
    } else {
        header("Location: ../admin/manage_showrooms.php?error=Update failed: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
}