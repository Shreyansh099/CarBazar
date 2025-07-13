<?php
session_start();
require_once '../db_connect.php';
header('Content-Type: application/json');

$response = ['user_logged_in' => false, 'car' => null, 'error' => null];
if (isset($_SESSION['loggedin'])) { $response['user_logged_in'] = true; }
$car_id = (int)($_GET['id'] ?? 0);

if ($car_id <= 0) { 
    $response['error'] = 'Invalid car ID.';
    echo json_encode($response);
    exit();
}

// This query explicitly selects ALL columns needed for the details page.
$sql = "SELECT 
            c.id, c.listing_id, c.make, c.model, c.year, c.price, c.description,
            c.fuel_type, c.km_driven, c.owner_count,
            c.showroom_id, c.front_image, c.image_1, c.image_2, c.image_3, c.image_4,
            s.city, s.address AS showroom_address, s.phone AS showroom_phone 
        FROM cars c 
        LEFT JOIN showrooms s ON c.showroom_id = s.id 
        WHERE c.id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) { 
    $response['car'] = $result->fetch_assoc(); 
} else { 
    http_response_code(404);
    $response['error'] = 'Car not found.'; 
}

$stmt->close();
$conn->close();
echo json_encode($response);