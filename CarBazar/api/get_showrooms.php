<?php
require_once '../db_connect.php';
header('Content-Type: application/json');

$city = isset($_GET['city']) ? trim($_GET['city']) : '';

// --- NEW LOGIC: If no city, fetch all showrooms ---
if (empty($city)) {
    $sql = "SELECT city, address, phone, manager_name, image_url FROM showrooms ORDER BY city, listing_id ASC";
    $stmt = $conn->prepare($sql);
} else {
    // If a city is specified, filter by it.
    $sql = "SELECT city, address, phone, manager_name, image_url FROM showrooms WHERE city = ? ORDER BY listing_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $city);
}

if ($stmt === false) { exit('DB Prepare failed.'); }
$stmt->execute();
$result = $stmt->get_result();

$showrooms = [];
// This URL logic is correct and handles subfolders.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base_path = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($base_path == '\\' || $base_path == '/') { $base_path = ''; }
$base_url = $protocol . $host . $base_path . '/';

while ($row = $result->fetch_assoc()) {
    if (!empty($row['image_url'])) {
        $row['image_url'] = $base_url . $row['image_url'];
    }
    $showrooms[] = $row;
}

$stmt->close();
$conn->close();
echo json_encode($showrooms);