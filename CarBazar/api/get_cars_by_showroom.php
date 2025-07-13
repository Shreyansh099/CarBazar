<?php
session_start();
require_once '../db_connect.php';
header('Content-Type: application/json');

$showroom_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($showroom_id <= 0) {
    echo json_encode(['error' => 'Invalid showroom ID.']);
    exit;
}

$response = ['user_logged_in' => false, 'cars' => []];
$user_id = 0;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $response['user_logged_in'] = true;
    $user_id = (int)$_SESSION['user_id'];
}

// --- THIS IS THE CORRECTED SQL QUERY ---
// It now correctly selects fuel_type and km_driven for the car cards on this page.
$sql = "SELECT 
            c.id, c.listing_id, c.make, c.model, c.year, c.price, c.front_image,
            c.fuel_type, c.km_driven, 
            w.id IS NOT NULL AS in_wishlist 
        FROM cars c 
        LEFT JOIN wishlist w ON c.id = w.car_id AND w.user_id = ? 
        WHERE c.showroom_id = ?";
        
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Database query preparation failed.']);
    exit;
}

$stmt->bind_param("ii", $user_id, $showroom_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    $response['cars'][] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($response);