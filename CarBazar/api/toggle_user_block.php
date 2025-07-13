<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { exit('Access Denied'); }
require_once '../db_connect.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($user_id > 0 && ($action == 'block' || $action == 'unblock')) {
    $is_blocked = ($action == 'block') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE users SET is_blocked = ? WHERE id = ? AND is_admin = 0");
    $stmt->bind_param("ii", $is_blocked, $user_id);
    $stmt->execute();
    $stmt->close();
}
header("Location: ../admin/manage_users.php");