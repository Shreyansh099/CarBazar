<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    exit('Access Denied.');
}
require_once '../db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM showrooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();

// Redirect back to the management page
header("Location: ../admin/manage_showrooms.php");
exit();