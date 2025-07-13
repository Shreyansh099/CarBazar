<?php
session_start();
require_once '../db_connect.php';
header('Content-Type: application/json');

$response = ['user_logged_in' => false, 'cars' => []];
$user_id = 0;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $response['user_logged_in'] = true;
    $user_id = (int)$_SESSION['user_id'];
}

// --- Gather all potential filter parameters from the URL ---
$searchTerm = $_GET['search'] ?? '';
$city = $_GET['city'] ?? '';
$brand = $_GET['brand'] ?? '';
$fuelType = $_GET['fuel_type'] ?? '';
$maxPrice = (int)($_GET['max_price'] ?? 0);
$maxKm = (int)($_GET['max_km'] ?? 0);

// Start with the base SQL query
$sql = "SELECT c.id, c.listing_id, c.make, c.model, c.year, c.price, c.front_image,
               c.fuel_type, c.km_driven, s.city, 
               w.id IS NOT NULL AS in_wishlist 
        FROM cars c 
        LEFT JOIN showrooms s ON c.showroom_id = s.id
        LEFT JOIN wishlist w ON c.id = w.car_id AND w.user_id = ? 
        WHERE 1=1"; 

$params = [$user_id];
$types = 'i';

// --- Build the query dynamically based on which filters are provided ---
if (!empty($searchTerm)) {
    $sql .= " AND (c.make LIKE ? OR c.model LIKE ? OR c.listing_id LIKE ?)";
    $searchWildcard = "%" . $searchTerm . "%";
    $params[] = $searchWildcard; $params[] = $searchWildcard; $params[] = $searchWildcard;
    $types .= 'sss';
}
if (!empty($city)) {
    $sql .= " AND s.city = ?";
    $params[] = $city;
    $types .= 's';
}
if (!empty($brand)) {
    $sql .= " AND c.make = ?";
    $params[] = $brand;
    $types .= 's';
}
if (!empty($fuelType)) {
    $sql .= " AND c.fuel_type = ?";
    $params[] = $fuelType;
    $types .= 's';
}
if ($maxPrice > 0) {
    $sql .= " AND c.price <= ?";
    $params[] = $maxPrice;
    $types .= 'i';
}
if ($maxKm > 0) {
    $sql .= " AND c.km_driven <= ?";
    $params[] = $maxKm;
    $types .= 'i';
}

$sql .= " ORDER BY c.date_posted DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) { 
    echo json_encode(['error' => "SQL Prepare Error: " . $conn->error]);
    exit();
}
// Use the spread operator '...' to pass the array of parameters to bind_param
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) { 
    $response['cars'][] = $row; 
}

$stmt->close();
$conn->close();
echo json_encode($response);