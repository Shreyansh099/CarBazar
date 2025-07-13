<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { exit('Access Denied'); }
require_once '../db_connect.php';

$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($car_id > 0) {
    // Before deleting the database record, get all image paths to delete the files from the server.
    $stmt_get = $conn->prepare("SELECT front_image, image_1, image_2, image_3, image_4 FROM cars WHERE id = ?");
    $stmt_get->bind_param("i", $car_id);
    $stmt_get->execute();
    $images_to_delete = $stmt_get->get_result()->fetch_assoc();
    $stmt_get->close();

    if ($images_to_delete) {
        foreach ($images_to_delete as $image_path) {
            if (!empty($image_path) && file_exists('../' . $image_path)) {
                unlink('../' . $image_path);
            }
        }
    }

    // Now, delete the database record itself.
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
header("Location: ../admin/manage_cars.php?status=deleted");
exit();