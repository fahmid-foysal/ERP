<?php
header("Access-Control-Allow-Origin: https://mondolmotors.com"); 
header("Access-Control-Allow-Credentials: true"); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");


session_start();


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
    exit;
}


if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "error", "message" => "Admin is not logged in."]);
    exit;
}


$_SESSION = array();


session_destroy();

echo json_encode(["status" => "success", "message" => "Admin logged out successfully."]);

?>