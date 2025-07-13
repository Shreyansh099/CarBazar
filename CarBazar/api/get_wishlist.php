<?php
session_start(); header('Content-Type: application/json');
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) { echo json_encode(['error' => 'User not logged in.']); exit; }
require_once '../db_connect.php';
$user_id = (int)$_SESSION['user_id'];
$sql = "SELECT c.id,c.listing_id,c.make,c.model,c.year,c.price,c.front_image,c.fuel_type,c.km_driven,s.city FROM cars c JOIN wishlist w ON c.id=w.car_id LEFT JOIN showrooms s ON c.showroom_id=s.id WHERE w.user_id = ?";
$stmt = $conn->prepare($sql); $stmt->bind_param("i", $user_id); $stmt->execute();
$result = $stmt->get_result();
$wishlist_cars = []; while($row = $result->fetch_assoc()) { $wishlist_cars[] = $row; }
echo json_encode($wishlist_cars); $stmt->close(); $conn->close();