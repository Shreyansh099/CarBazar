<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { exit('Access Denied.'); }
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $listing_id = trim($_POST['listing_id']);
    $city = trim($_POST['city']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $manager_name = trim($_POST['manager_name']);
    $image_path_for_db = null;

    if (isset($_FILES["showroom_image"]) && $_FILES["showroom_image"]["error"] == 0) {
        $target_dir = "../images/showrooms/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $original_filename = basename($_FILES["showroom_image"]["name"]);
        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $unique_filename = uniqid('showroom_', true) . '.' . $imageFileType;
        $target_file = $target_dir . $unique_filename;
        
        $check = getimagesize($_FILES["showroom_image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["showroom_image"]["tmp_name"], $target_file)) {
                $image_path_for_db = 'images/showrooms/' . $unique_filename;
            }
        }
    }

    $sql = "INSERT INTO showrooms (listing_id, city, address, phone, manager_name, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $listing_id, $city, $address, $phone, $manager_name, $image_path_for_db);

    if ($stmt->execute()) {
        header("Location: ../admin/manage_showrooms.php?status=add_success");
    } else {
        header("Location: ../admin/manage_showrooms.php?error=Database error: " . $conn->error);
    }
    $stmt->close();
    $conn->close();
}