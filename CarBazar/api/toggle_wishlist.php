<?php
session_start();
header('Content-Type: application/json');

// Security Check: User must be logged in to use the wishlist.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

require_once '../db_connect.php';

// Get the POST data sent from the JavaScript fetch call.
$data = json_decode(file_get_contents('php://input'), true);
$car_id = isset($data['car_id']) ? (int)$data['car_id'] : 0;
$user_id = (int)$_SESSION['user_id'];

if ($car_id <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Invalid car ID provided.']);
    exit;
}

// Check if this user has already wishlisted this car.
$sql_check = "SELECT id FROM wishlist WHERE user_id = ? AND car_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $car_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // If it exists, REMOVE it from the wishlist.
    $sql_delete = "DELETE FROM wishlist WHERE user_id = ? AND car_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $user_id, $car_id);
    if ($stmt_delete->execute()) {
        echo json_encode(['status' => 'success', 'action' => 'removed']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Could not remove item from wishlist.']);
    }
    $stmt_delete->close();
} else {
    // If it does not exist, ADD it to the wishlist.
    $sql_insert = "INSERT INTO wishlist (user_id, car_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $user_id, $car_id);
    if ($stmt_insert->execute()) {
        echo json_encode(['status' => 'success', 'action' => 'added']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Could not add item to wishlist.']);
    }
    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();
?>