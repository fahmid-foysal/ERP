<?php
session_start(); 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit;
}

$email = filter_var($data["email"], FILTER_SANITIZE_EMAIL);
$password = $data["password"];

try {
    $stmt = $pdo->prepare("SELECT id, email, password FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !password_verify($password, $admin["password"])) {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        exit;
    }

    
    $_SESSION["admin_id"] = $admin["id"];
    $_SESSION["admin_email"] = $admin["email"];

 
    file_put_contents("session_debug.log", print_r($_SESSION, true));

    echo json_encode([
        "status" => "success",
        "message" => "Login successful.",
        "admin" => [
            "email" => $admin["email"]
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}