<?php
// We will start the session in this file. Since this file is included
// once on every page, it guarantees the session is always started, and only started once.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// These lines remain the same.
$conn = new mysqli("localhost", "root", "", "carbazar_db");
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}
$conn->set_charset("utf8mb4");